<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class P_master_isp extends CI_Controller {
    public function __construct()
    {
        parent::__construct();

        $this->load->model('m_login');
        if(!$this->m_login->status_login()) redirect(site_url());

        $this->load->model('m_isp');
        
        $this->load->model('m_core');
        global $jabatan,$project,$menu;
        $jabatan = $this->m_core->jabatan();
        $project = $this->m_core->project();
        $menu = $this->m_core->menu();
    }
    public function index()
    {
        ini_set('memory_limit', '256M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
        ini_set('sqlsrv.ClientBufferMaxKBSize', '524288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size', '524288'); // Setting to 512M - for pdo_sqlsrv
        ini_set('max_execution_time','-1'); // Setting to 512M - for pdo_sqlsrv

        $isi = array();
        $isi['load_css'] = load_css();
        $isi['load_js'] = load_js();
        $this->view('normal', 'Master > ISP', 'List', 'proyek/master/isp/view', $isi);
        $this->load->view('core/css_custom_master');
    }
    public function add()
    {   
        $isi = array();
        $isi['load_css'] = load_css(['select2']);
        $isi['load_js'] = load_js(['select2','inputmask']);
        $this->view('normal', 'Master > ISP', 'Add', 'proyek/master/isp/add', $isi);
        $this->load->view('core/css_custom_master');
    }
    
    public function save()
    {   
        if (!empty($this->input->post()))
        {
            $status = $this->m_isp->save([
                'project_id'       => $GLOBALS['project']->id,
                'nama_isp'         => $this->input->post('nama_isp'),
                'bandwidth'        => $this->input->post('bandwidth'),
                'persen_mitra'     => $this->input->post('persen_mitra'),
                'nilai_kabel'      => $this->input->post('nilai_kabel'),
                'nilai_pemasangan' => $this->input->post('nilai_pemasangan'),
                'nilai_lain_lain'  => $this->input->post('nilai_lain_lain'),
                'keterangan'       => $this->input->post('keterangan'),
                'active'           => $this->input->post('active')
            ]);
        }

        if (!empty($this->input->post()))
        {
            if ($status == 'success') {
                $this->session->set_flashdata('msg', ['title' => 'Berhasil', 'text' => 'Data Berhasil diTambah', 'type' => 'success']);
            } elseif ($status == 'double') {
                $this->session->set_flashdata('msg', ['title' => 'Gagal', 'text' => 'Nama ISP sudah ada sebelumnya', 'type' => 'danger']);
            } elseif ($status == 'failed') {
                $this->session->set_flashdata('msg', ['title' => 'Gagal', 'text' => 'Data gagal diTambah', 'type' => 'danger']);
            } 
        }

        redirect('P_master_isp/add');
    }
    public function edit()
    {
        if ($this->input->get('id')!="")
        {
            if ($this->m_isp->cek(['id' => $this->input->get('id')]))
            {
                $this->load->model('m_log');

                $isi = array();
                $isi['data'] = $this->m_log->get('m_isp_int', $this->input->get('id'));
                $isi['dataSelect'] = $this->m_isp->get_selected($this->input->get('id'));
                $isi['load_css'] = load_css(['select2']);
                $isi['load_js'] = load_js(['select2','inputmask']);
                $this->view('normal', 'Master > ISP', 'Edit', 'proyek/master/isp/edit', $isi);
                $this->load->view('core/css_custom_master');
            }
            else
            {
                $this->session->set_flashdata('msg', ['title' => 'Gagal', 'text' => 'Data tidak ditemukan', 'type' => 'error']);
                redirect('P_master_isp');
            }
        }
        else 
        {
            redirect('P_master_isp');
        }
    }

    public function save_edit()
    {
        if (!empty($this->input->post())) {
            $status = $this->m_isp->edit([
                'id'             => $this->input->get('id'),
                'nama_isp'         => $this->input->post('nama_isp'),
                'bandwidth'        => $this->input->post('bandwidth'),
                'persen_mitra'     => $this->input->post('persen_mitra'),
                'nilai_kabel'      => $this->input->post('nilai_kabel'),
                'nilai_pemasangan' => $this->input->post('nilai_pemasangan'),
                'nilai_lain_lain'  => $this->input->post('nilai_lain_lain'),
                'keterangan'       => $this->input->post('keterangan'),
                'active'           => $this->input->post('active')
            ]);

            if ($status == 'success') {
                $this->session->set_flashdata('msg', ['title' => 'Berhasil', 'text' => 'Data Berhasil diupdate', 'type' => 'success']);
            } elseif ($status == 'double') {
                $this->session->set_flashdata('msg', ['title' => 'Gagal', 'text' => 'Nama ISP sudah ada sebelumnya', 'type' => 'danger']);
            } elseif ($status == 'failed') {
                $this->session->set_flashdata('msg', ['title' => 'Gagal', 'text' => 'Data tidak diupdate', 'type' => 'danger']);
            } 
        }

        if ($this->input->get('id')!="")
        {
            redirect('P_master_isp/edit?id='.$this->input->get('id'));
        }
        else
        {
            redirect('P_master_isp');
        }
    }
    
    public function ajax_get_view(){
        $sql = "
                    SELECT
                        m_isp.*
                    FROM
                        dbo.m_isp_int AS m_isp
                    WHERE
                        m_isp.project_id = '".$GLOBALS['project']->id."'
            ";
        $table = "
        (
            $sql
        ) temp
        ";
        
        $primaryKey = 'id';

        $columns = array(
            array( 'db' => 'nama_isp', 'dt' => 0 ),
            array( 'db' => 'bandwidth', 'dt' => 1 ),
            array( 'db' => 'persen_mitra', 'dt' => 2 ),
            array( 'db' => 'nilai_kabel', 'dt' => 3 ),
            array( 'db' => 'nilai_pemasangan', 'dt' => 4 ),
            array( 'db' => 'nilai_lain_lain', 'dt' => 5 ),
            array( 'db' => 'keterangan', 'dt' => 6 ),
            array( 'db' => 'active', 'dt' => 7 ),
            array( 'db' => 'id', 'dt' => 8 )
        );

        // SQL server connection information

        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db'   => $this->db->database,
            'host' => $this->db->hostname
        );
        $this->load->library("Ssp_custom");
        $table = SSP_custom::simple( $_GET, $sql_details, $table, $primaryKey, $columns );
        foreach ($table["data"] as $k => $v) {
            $table["data"][$k][7] = ($table["data"][$k][7]=='1' ? "Aktif" : "Tidak Aktif");
            $table["data"][$k][8] = 
                "<a href='" . site_url() . "/p_master_isp/edit?id=".$table["data"][$k][8]."' class='btn btn-primary col-md-10'>
                    <i class='fa fa-pencil'></i>
                </a>";
        }
        echo(json_encode($table));      
    }
}
