<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_range_internet extends CI_Controller {
    public function __construct()
    {
        parent::__construct();

        $this->load->model('m_login');
        if(!$this->m_login->status_login()) redirect(site_url());

        $this->load->model('master/range/M_master_range_internet', 'm_mri');
        
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
        $this->view('normal', 'Master > Range > Range Internet', 'List', 'proyek/master/range_internet/view', $isi);
        $this->load->view('core/css_custom_master');
    }
    public function add()
    {
        $isi = array();
        $isi['select_jenis_service'] = $this->m_mri->get_jenis_servis();
        $isi['select_isp'] = $this->m_mri->get_isp();
        $isi['load_css'] = load_css(['select2']);
        $isi['load_js'] = load_js(['select2','inputmask']);
        $this->view('normal', 'Master > Range > Range Internet', 'Add', 'proyek/master/range_internet/add', $isi);
        $this->load->view('core/css_custom_master');
    }
    
    public function save()
    {   	
        if (!empty($this->input->post()))
        {
        $status = $this->m_mri->save([
            'project_id'        => $GLOBALS['project']->id,
            'nama_paket'        => $this->input->post('nama_paket'),
            'service_jenis_id'  => $this->input->post('service_jenis_id'),
            'isp_id'            => $this->input->post('isp_id'),
            'kapasitas'         => $this->input->post('kapasitas'),
            'kuota'             => $this->input->post('kuota'),
            'up_device'         => $this->input->post('up_device'),
            'nilai_langganan'   => $this->input->post('nilai_langganan'),
            'keterangan'        => $this->input->post('keterangan'),
            'active'            => $this->input->post('active')
        ]);
	}

        $this->load->model('alert');

        $this->alert->css();

        $isi = array();
        $isi['select_jenis_service'] = $this->m_mri->get_jenis_servis();
        $isi['select_isp'] = $this->m_mri->get_isp();
        $isi['load_css'] = load_css(['select2']);
        $isi['load_js'] = load_js(['select2','inputmask']);
        $this->view('normal', 'Master > Range > Range Internet', 'Add', 'proyek/master/range_internet/add', $isi);
	
        if (!empty($this->input->post()))
        {
        if ($status == 'success') {
            $this->load->view('core/alert', ['title' => 'Berhasil', 'text' => 'Data Berhasil diTambah', 'type' => 'success']);
        } elseif ($status == 'double') {
            $this->load->view('core/alert', ['title' => 'Gagal', 'text' => 'Nama paket sudah ada sebelumnya', 'type' => 'danger']);
        } elseif ($status == 'failed') {
            $this->load->view('core/alert', ['title' => 'Gagal', 'text' => 'Data gagal diTambah', 'type' => 'danger']);
        }
	}
    }
    public function edit()
    {
        $status = 0;
        if ($this->input->post('nama_paket')) {
            $this->load->model('alert');

            $status = $this->m_mri->edit([
                'id'                => $this->input->get('id'),
                'nama_paket'        => $this->input->post('nama_paket'),
                'service_jenis_id'  => $this->input->post('service_jenis_id'),
                'isp_id'            => $this->input->post('isp_id'),
                'kapasitas'         => $this->input->post('kapasitas'),
                'kuota'             => $this->input->post('kuota'),
                'up_device'         => $this->input->post('up_device'),
                'nilai_langganan'   => $this->input->post('nilai_langganan'),
                'keterangan'        => $this->input->post('keterangan'),
                'active'            => $this->input->post('active')
            ]);
            $this->alert->css();
        }

        if ($this->m_mri->cek(['id' => $this->input->get('id')])) {
            $this->load->model('m_log');

            $isi = array();
            $isi['select_jenis_service'] = $this->m_mri->get_jenis_servis();
            $isi['select_isp'] = $this->m_mri->get_isp();
            $isi['data'] = $this->m_log->get('m_range_int', $this->input->get('id'));
            $isi['dataSelect'] = $this->m_mri->get_selected($this->input->get('id'));
            $isi['load_css'] = load_css(['select2']);
            $isi['load_js'] = load_js(['select2','inputmask']);

            $this->view('normal', 'Master > Range > Range Internet', 'Edit', 'proyek/master/range_internet/edit', $isi);
            $this->load->view('core/css_custom_master');
        } else {
            redirect(site_url().'/master/range/master_range_internet');
        }

        if ($status == 'success') {
            $this->load->view('core/alert', ['title' => 'Berhasil', 'text' => 'Data Berhasil diupdate', 'type' => 'success']);
        } elseif ($status == 'double') {
            $this->load->view('core/alert', ['title' => 'Gagal', 'text' => 'Nama paket sudah ada sebelumnya', 'type' => 'danger']);
        } elseif ($status == 'failed') {
            $this->load->view('core/alert', ['title' => 'Gagal', 'text' => 'Data tidak diupdate', 'type' => 'danger']);
        } 
    }
    
    // public function delete()
    // {
    //     $this->load->model('alert');

    //     $status = $this->m_mri->delete([
    //         'id' => $this->input->get('id'),
    //     ]);
    //     $this->alert->css();
        
    //     ini_set('memory_limit', '256M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    //     ini_set('sqlsrv.ClientBufferMaxKBSize', '524288'); // Setting to 512M
    //     ini_set('pdo_sqlsrv.client_buffer_max_kb_size', '524288'); // Setting to 512M - for pdo_sqlsrv
    //     ini_set('max_execution_time','-1'); // Setting to 512M - for pdo_sqlsrv

        // $isi['load_css'] = load_css(['select2']);
        // $isi['load_js'] = load_js(['select2','inputmask']);
    //     $this->view('normal', 'Master > Range > Range Internet', 'List', 'proyek/master/range_internet/view', []);

    //     if ($status == 'success') {
    //         $this->load->view('core/alert', ['title' => 'Berhasil', 'text' => 'Data Berhasil dihapus', 'type' => 'success']);
    //     } elseif ($status == 'failed') {
    //         $this->load->view('core/alert', ['title' => 'Gagal', 'text' => 'Data Gagal dihapus', 'type' => 'danger']);
    //     } else {
    //         $this->load->view('core/alert', ['title' => 'Gagal', 'text' => $status, 'type' => 'danger']);
    //     }
    // }
    
    public function ajax_get_view(){
        $sql = "
                    SELECT
                        m_r_isp.*, 
                        m_isp.nama_isp, 
                        sj.jenis_service
                    FROM
                        dbo.m_range_int AS m_r_isp
                        INNER JOIN
                        dbo.m_isp_int AS m_isp
                        ON 
                            m_r_isp.isp_id = m_isp.id
                        INNER JOIN
                        dbo.service_jenis AS sj
                        ON 
                            m_r_isp.service_jenis_id = sj.id
                    WHERE
                        m_isp.project_id = '".$GLOBALS['project']->id."'
            ";
                    // WHERE
                    //     m_r_isp.[delete] = 0
        $table = "
        (
            $sql
        ) temp
        ";
 
        $primaryKey = 'id';
        
        $columns = array(
            array( 'db' => 'nama_paket', 'dt' => 0 ),
            array( 'db' => 'jenis_service', 'dt' => 1 ),
            array( 'db' => 'nama_isp', 'dt' => 2 ),
            array( 'db' => 'kapasitas', 'dt' => 3 ),
            array( 'db' => 'kuota', 'dt' => 4 ),
            array( 'db' => 'up_device', 'dt' => 5 ),
            array( 'db' => 'nilai_langganan', 'dt' => 6 ),
            array( 'db' => 'keterangan', 'dt' => 7 ),
            array( 'db' => 'active', 'dt' => 8 ),
            array( 'db' => 'id', 'dt' => 9 ),
        );
            // array( 'db' => 'id', 'dt' => 10 ),

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
            $table["data"][$k][8] = ($table["data"][$k][8]=='1' ? "Aktif" : "Tidak Aktif");
            $table["data"][$k][9] = 
                "<a href='" . site_url() . "/master/range/master_range_internet/edit?id=".$table["data"][$k][9]."' class='btn btn-primary col-md-9'>
                    <i class='fa fa-pencil'></i>
                </a>";
            // $table["data"][$k][10] = 
            //     "<a href='#'  class='btn btn-md btn-danger col-md-12' data-toggle='modal' onclick='confirm_modal(".$table["data"][$k][10].")' data-target='#myModal'> 
            //         <i class='fa fa-trash'></i>
            //     </a>";
        }
        echo(json_encode($table));      
    }
}
