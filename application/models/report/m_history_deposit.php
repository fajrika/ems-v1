<?php

defined("BASEPATH") or exit("No direct script access allowed");

class m_history_deposit extends CI_Model
{
    // $project = $this->m_core->project();
    // AND kawasan.project_id = $project->id

    private function _get_datatables_query()
    {
        $column_order = array(null,'dd.kwitansi_referensi_id','c.name','dd.nilai','cp.name','dd.description','dd.tgl_tambah','u.name'); //set column field database for datatable orderable
        $column_search = array('dd.kwitansi_referensi_id','c.name','dd.nilai','cp.name','dd.description','dd.tgl_tambah','u.name'); //set column field database for datatable searchable 
   
        $this->db->select('
                            d.id,
                            dd.id AS deposit_detail_id,
                            dd.kwitansi_referensi_id,
                            c.name AS customer_name,
                            dd.nilai,
                            cp.code AS cara_pembayaran_code,
                            cp.name AS cara_pembayaran_name,
                            dd.description,
                            dd.tgl_tambah,
                            u.name AS user_name
                        ');
        $this->db->from('t_deposit d');
        $this->db->join('t_deposit_detail dd','dd.t_deposit_id = d.id','INNER');
        $this->db->join('customer c','c.id = d.customer_id','INNER');
        $this->db->join('user u','u.id = dd.user_id','INNER');
        $this->db->join('cara_pembayaran cp','cp.id = dd.cara_pembayaran_id','LEFT');

        //custom filter here
            if($this->input->post('pemilik'))
            {
                $this->db->where('d.customer_id', $this->input->post('pemilik'));
            }
            if($this->input->post('cara_pembayaran')!="all" && $this->input->post('cara_pembayaran')!="")
            {
                $this->db->where('dd.cara_pembayaran_id', $this->input->post('cara_pembayaran'));
            }
            if($this->input->post('tanggal_bayar')!="")
            {
                $this->db->group_start();
                    $this->db->where('dd.tgl_tambah >=', date("Y-m-d H:i:s", strtotime($this->input->post('tanggal_bayar'))));
                    $this->db->where('dd.tgl_tambah <', date("Y-m-d H:i:s", strtotime($this->input->post('tanggal_bayar') . " +1 Days")));
                $this->db->group_end();
            }

        // Search Each Column
            $i = 0;
            foreach ($_POST['columns'] as $c)
            {
                if($c['search']['value']!="")
                {
                    $this->db->like($column_search[$i], $c['search']['value']);
                }
                $i++;
            }

        $i = 0;
        foreach ($column_search as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                 
                if($i===0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
 
                if(count($column_search) - 1 == $i) //last loop
                {
                    $this->db->group_end(); //close bracket
                }
            }
            $i++;
        }
        
        $project = $this->m_core->project();
        $this->db->where('d.project_id', $project->id);

        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else
        {
            $this->db->order_by('d.customer_id', 'ASC');
        }

        // $this->db->group_by(
        //                         array(
        //                             "d.customer_id", 
        //                             "d.project_id"
        //                         )
        //                     );
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        }

        $query = $this->db->get();
        return $query->result();
    }
 
    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all()
    {
        $this->db->select('
                            d.customer_id,
                            d.project_id,
                            sum(dd.nilai) as saldo
                        ');
        $this->db->from('t_deposit d');
        $this->db->join('t_deposit_detail dd','dd.t_deposit_id = d.id','INNER');

        $this->db->group_by(
                                array(
                                    "d.customer_id", 
                                    "d.project_id"
                                )
                            );

        return $this->db->count_all_results();
    }

    public function list_customer($search="")
    {
        $project = $this->m_core->project();

        $this->db
            ->select("
                        c.id, 
                        c.name AS text
                    ")
            ->from("customer c")
            ->join("unit u","u.pemilik_customer_id = c.id","INNER")
            ->where("u.project_id", $project->id);
        if ($search!="")
        {
            $this->db->like('c.name', $search);
        }

        $this->db->limit(10,0); //Limit per 10 data agar tidak berat
        
        return $this->db->get()->result();
    }
    public function list_cara_pembayaran()
    {
        $project = $this->m_core->project();

        return $this->db
            ->select("
                        cp.id, 
                        cp.code, 
                        cp.name
                    ")
            ->from("cara_pembayaran cp")
            ->where("cp.project_id", $project->id)
            ->get()->result();
    }
    public function get_deposit_saldo()
    {
        if($this->input->post('pemilik'))
        {
            $project = $this->m_core->project();

            $this->db->select('
                                sum(dd.nilai) as saldo
                            ');
            $this->db->from('t_deposit d');
            $this->db->join('t_deposit_detail dd','dd.t_deposit_id = d.id','INNER');

            $this->db->where('d.customer_id', $this->input->post('pemilik'));

            $project = $this->m_core->project();
            $this->db->where('d.project_id', $project->id);
            $saldo = $this->db->get()->row()->saldo;
            if (!$saldo)
            {
                return 0;
            }
            else
            {
                return nominal($saldo,"",0,".");
            }
        }
        else
        {
            return 0;
        }
    }
}