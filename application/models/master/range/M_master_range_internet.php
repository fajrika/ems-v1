<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_master_range_internet extends CI_Model
{
    private $table = 'm_range_int';
	private $field = array('id','project_id','nama_paket','service_jenis_id','isp_id','kapasitas','kuota','up_device','nilai_langganan','keterangan','active');

    public function get()
    {
        $project = $this->m_core->project();

        $query = $this->db->select("*")
                            ->from($this->table)
                            // ->where('delete','0')
                            ->where("project_id",$project->id)
                            ->get();
        return $query->result_array();
    }
    public function get_selected($id)
    {
        $project = $this->m_core->project();

        return $this->db->from($this->table)
                        ->where('id',$id)
                        ->where("project_id",$project->id)
                        ->get()->row();
    }

    public function get_isp()
    {
        $project = $this->m_core->project();

        $query = $this->db->select("isp.*")
                            ->from('dbo.m_isp_int AS isp')
                            ->where('isp.active','1')
                            ->where("isp.project_id",$project->id)
                            ->get();
        return $query->result_array();
    }
    public function get_jenis_servis()
    {
        $query = $this->db->select("sj.*")
                            ->from('dbo.service_jenis AS sj')
                            ->where('sj.active','1')
                            ->get();
        return $query->result_array();
    }

    public function edit($dataTmp)
    {
        $this->load->model('m_core');
        $this->load->model('m_log');
        $user_id = $this->m_core->user_id();

        $data =
        [
            'nama_paket'        => fix_whitespace($dataTmp['nama_paket']),
            'service_jenis_id'  => only_number($dataTmp['service_jenis_id']),
            'isp_id'            => only_number($dataTmp['isp_id']),
            'kapasitas'         => only_number($dataTmp['kapasitas']),
            'kuota'             => only_number($dataTmp['kuota']),
            'up_device'         => only_number($dataTmp['up_device']),
            'nilai_langganan'   => only_number($dataTmp['nilai_langganan']),
            'keterangan'        => fix_whitespace($dataTmp['keterangan']),
            'active'            => only_number($dataTmp['active'])
        ];

        // validasi double
        $cek = array(
            'id <>' => $dataTmp['id'],
            'nama_paket' => $dataTmp['nama_paket']
        );
            // 'delete' => 0
        if (!$this->cek($cek)) {
            //proses edit
            $before = $this->get_selected($dataTmp['id']);
                $this->db->where('id', $dataTmp['id']);
                $this->db->update($this->table, $data);
            $after = $this->get_selected($dataTmp['id']);

            $diff = (object) (array_diff_assoc((array) $after, (array) $before));
            $tmpDiff = (array) $diff;
            if ($tmpDiff) {
                $this->m_log->log_save($this->table, $dataTmp['id'], 'Edit', $diff);

                return 'success';
            } else {
                return 'failed';
            }
        } else {
            return 'double';
        }
    }

    public function save($dataTmp)
    {
        $this->load->model('m_core');
        $this->load->model('m_log');
        $data = [
            'project_id'        => $dataTmp['project_id'],
            'nama_paket'        => fix_whitespace($dataTmp['nama_paket']),
            'service_jenis_id'  => only_number($dataTmp['service_jenis_id']),
            'isp_id'            => only_number($dataTmp['isp_id']),
            'kapasitas'         => only_number($dataTmp['kapasitas']),
            'kuota'             => only_number($dataTmp['kuota']),
            'up_device'         => only_number($dataTmp['up_device']),
            'nilai_langganan'   => only_number($dataTmp['nilai_langganan']),
            'keterangan'        => fix_whitespace($dataTmp['keterangan']),
            'active'            => only_number($dataTmp['active'])
        ];

        // validasi double
        $cek = array(
            'nama_paket' => $dataTmp['nama_paket']
        );
            // 'delete' => 0
        if (!$this->cek($cek)) {
            $res = $this->db->insert($this->table, $data);

            if ($res) {
                $id = $this->db->insert_id();

                $dataLog = $this->get_selected($id);
                $this->m_log->log_save($this->table, $id, 'Tambah', $dataLog);

                return 'success';
            } else {
                return 'failed';
            }
        } else {
            return 'double';
        }
    }

    // public function delete($dataTmp)
    // {
    //     $this->load->model('m_core');
    //     $this->load->model('m_log');
    //     $user_id = $this->m_core->user_id();

    //     $cek = array(
    //         'id' => $dataTmp['id'],
    //         'delete' => 0
    //     );
    //     if ($this->cek($cek)) {

    //         $before = $this->get_selected($dataTmp['id']);
    //             $this->db->where('id', $dataTmp['id']);
    //             $this->db->set("delete", '1');
    //             $this->db->update($this->table);
    //         $after = $this->get_selected($dataTmp['id']);

    //         $diff = (object) (array_diff_assoc((array) $after, (array) $before));
    //         $tmpDiff = (array) $diff;
    //         if ($tmpDiff) {
    //             $diff = (object) (array_diff((array) $after, (array) $before));
    //             $this->m_log->log_save($this->table, $dataTmp['id'], 'Delete', $diff);

    //             return 'success';
    //         } else {
    //             return 'failed';
    //         }
    //     } else{
    //         return 'not found';
    //     }
    // }

    public function cek($dataTmp)
    {
        $project = $this->m_core->project();

        $row = $this->db->from($this->table)
                        ->where($dataTmp)
                        ->where("project_id",$project->id)
                        ->get()->row();

        return isset($row) ? 1 : 0;
    }
}
