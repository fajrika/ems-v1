<?php
defined('BASEPATH') or exit('No direct script access allowed');
class P_master_pemeliharaan_air  extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('m_pemeliharaan_air');
        $this->load->model('m_bank');
        $this->load->model('m_core');
        $this->load->model('m_login');
        if (!$this->m_login->status_login()) {
            redirect(site_url());
        }
        global $jabatan;
        $jabatan = $this->m_core->jabatan();
        global $project;
        $project = $this->m_core->project();
        global $menu;
        $menu = $this->m_core->menu();
    }

    public function index()
    {
        $data = $this->m_pemeliharaan_air->get();
        $this->load->model('alert');
        $this->load->view('core/header');
        $this->alert->css();
        $this->load->view('core/side_bar', ['menu' => $GLOBALS['menu']]);
        $this->load->view('core/top_bar', ['jabatan' => $GLOBALS['jabatan'], 'project' => $GLOBALS['project']]);
        $this->load->view('core/body_header', ['title' => 'Master > Tarif > Pemeliharaan Air', 'subTitle' => 'List']);
        $this->load->view('proyek/master/pemeliharaan_air/view', ['data' => $data]);
        $this->load->view('core/body_footer');
        $this->load->view('core/footer');
    }

    public function request_data_json()
    {
        $this->load->library("Ssp_custom");
        $project_id = $GLOBALS['project']->id;

        $sql = "
            SELECT
                ROW_NUMBER() OVER(ORDER BY pemeliharaan_air.id) nomor,
                pemeliharaan_air.* 
            FROM 
                pemeliharaan_air
            WHERE 1=1
                AND pemeliharaan_air.project_id = '$project_id'
                AND pemeliharaan_air.[delete] = '0'
            ";
        $table = "
        (
            $sql
        ) temp
        ";

        $primaryKey = 'id';
        $columns = array(
            array('db' => 'nomor', 'dt' => 0),
            array('db' => 'code', 'dt' => 1),
            array('db' => 'name', 'dt' => 2),
            array('db' => 'ukuran_pipa', 'dt' => 3),
            array('db' => 'nilai', 'dt' => 4),
            array('db' => 'nilai_pemasangan', 'dt' => 5),
            array('db' => 'nilai_ppn_pemasangan', 'dt' => 6),
            array('db' => 'description', 'dt' => 7),
            array('db' => 'active', 'dt' => 8),
            array('db' => 'id', 'dt' => 9),
            array('db' => 'id', 'dt' => 10),
        );
        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db'   => $this->db->database,
            'host' => $this->db->hostname
        );
        $table = SSP_Custom::simple( $_GET, $sql_details, $table, $primaryKey, $columns );
        $nomor = 1;
        foreach ($table['data'] as $key => $value) 
        {
            $table['data'][$key][4] = "<div class='text-right'>".number_format($table['data'][$key][4])."</div>";
            $table['data'][$key][5] = "<div class='text-right'>".number_format($table['data'][$key][5])."</div>";
            $table['data'][$key][6] = "<div class='text-right'>".number_format($table['data'][$key][6])."</div>";
            $table['data'][$key][8] = $table['data'][$key][8] == 1 ? 'Aktif' : 'Tidak Aktif';
            $table['data'][$key][9] = "
                <a href='".site_url('P_master_pemeliharaan_air/edit?id='.$table['data'][$key][9])."' class='btn btn-sm btn-primary col-md-12'>
                    <i class='fa fa-edit'></i> Edit
                </a>
            ";
            $table['data'][$key][10] = "
                <a href='#' class='btn-delete btn btn-sm btn-danger col-md-12' data-toggle='modal' data-target='#modal_delete' data-item_id='".$table['data'][$key][10]."'> 
                    <i class='fa fa-trash'></i> Hapus
                </a>
            ";
        }
        echo(json_encode($table));
    }

    public function add()
    {
        $this->load->model('alert');
        $this->load->view('core/header');
        $this->alert->css();
        $this->load->view('core/side_bar', ['menu' => $GLOBALS['menu']]);
        $this->load->view('core/top_bar');
        $this->load->view('core/body_header', ['title' => 'Master > Tarif > Pemeliharaan Air', 'subTitle' => 'Add']);
        $this->load->view('proyek/master/pemeliharaan_air/add');
        $this->load->view('core/body_footer');
        $this->load->view('core/footer');
    }

    public function ajax_save(){
        echo(json_encode($this->m_pemeliharaan_air->save($this->input->post())));
    }

    public function ajax_edit(){
        echo(json_encode($this->m_pemeliharaan_air->edit($this->input->post())));
    }
    public function ajax_delete(){
        echo(json_encode($this->m_pemeliharaan_air->delete($this->input->post())));
    }
    public function edit()
    {
        $this->load->model('m_log');
        $data = $this->m_log->get('pemeliharaan_air', $this->input->get('id'));
        $data2 = $this->m_pemeliharaan_air->get($this->input->get('id'))[0];
        $this->load->model('alert');
        $this->load->view('core/header');
        $this->alert->css();
        $this->load->view('core/side_bar', ['menu' => $GLOBALS['menu']]);
        $this->load->view('core/top_bar');
        $this->load->view('core/body_header', ['title' => 'Master > Tarif > Pemeliharaan Air', 'subTitle' => 'Add']);
        $this->load->view('proyek/master/pemeliharaan_air/edit',['data'=>$data,'data2'=>$data2]);
        $this->load->view('core/body_footer');
        $this->load->view('core/footer');
    }

    public function delete()
    {
        $this->load->model('alert');
        $status = $this->m_pemeliharaan_air->delete([
            'id' => $this->input->get('id'),
        ]);

        $this->alert->css();
        $data = $this->m_pemeliharaan_air->getAll();
        $this->load->view('core/header');
        $this->load->view('core/side_bar', ['menu' => $GLOBALS['menu']]);
        $this->load->view('core/top_bar', ['jabatan' => $GLOBALS['jabatan'], 'project' => $GLOBALS['project']]);
        $this->load->view('core/body_header', ['title' => 'Master > Pemeliharaan Air', 'subTitle' => 'List']);
        $this->load->view('proyek/master/pemeliharaan_air/view', ['data' => $data]);
        $this->load->view('core/body_footer');
        $this->load->view('core/footer');
        if ($status == 'success') {
            $this->load->view('core/alert', ['title' => 'Berhasil', 'text' => 'Data Berhasil di Delete', 'type' => 'success']);
        } elseif ($status == 'cara_pembayaran') {
            $this->load->view('core/alert', ['title' => 'Gagal', 'text' => 'Data COA digunakan di Pemeliharaan Air', 'type' => 'danger']);
        } elseif ($status == 'metode_penagihan') {
            $this->load->view('core/alert', ['title' => 'Gagal', 'text' => 'Data COA digunakan di Metode Penagihan', 'type' => 'danger']);
        } elseif ($status == 'service') {
            $this->load->view('core/alert', ['title' => 'Gagal', 'text' => 'Data COA digunakan di Service', 'type' => 'danger']);
        }
    }
}
