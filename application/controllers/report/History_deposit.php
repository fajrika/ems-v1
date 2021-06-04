<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/phpspreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
class History_deposit extends CI_Controller {
	function __construct() {
        parent::__construct();

        $this->load->model('m_login');
        if(!$this->m_login->status_login()) redirect(site_url());

        $this->load->model('m_core');
        global $jabatan,$project,$menu;
        $jabatan = $this->m_core->jabatan();
        $project = $this->m_core->project();
        $menu = $this->m_core->menu();
	}
	public function index()
	{
        $this->load->model('report/m_history_deposit','m_history_deposit');

        $isi = array();
        $isi['load_css'] = load_css(['select2','datetimepicker']);
        $isi['load_js'] = load_js(['select2','moment','datetimepicker']);
        $isi['cara_pembayaran'] = $this->m_history_deposit->list_cara_pembayaran();
        $this->view('normal', 'Report > History Deposit', 'Report History Deposit', 'proyek/report/history_deposit/view', $isi);
        $this->load->view('core/css_custom_master');
	}

    public function get_data_ajax()
    {
        $this->load->model('report/m_history_deposit','m_history_deposit');

        $data = array();
        $no = (isset($_POST['start']) ? $_POST['start'] : 0);

        if (!$_POST['reset'])
        {
            $res = $this->m_history_deposit->get_datatables();
            foreach ($res as $r) {
                $no++;
                $row = array();
                // $row[] = $no;
                $row[] = $r->kwitansi_referensi_id;
                $row[] = $r->customer_name;
                $row[] = nominal($r->nilai,"",0, ".");
                $row[] = $r->cara_pembayaran_code . ' - ' . $r->cara_pembayaran_name;
                $row[] = $r->description;
                $row[] = date('d/M/Y', strtotime($r->tgl_tambah));
                $row[] = $r->user_name;
     
                $data[] = $row;
            }
        }

        $output = array(
                        "draw" => (isset($_POST['draw']) ? $_POST['draw'] : 0),
                        "recordsTotal" => $this->m_history_deposit->count_all(),
                        "recordsFiltered" => $this->m_history_deposit->count_filtered(),
                        "deposit_saldo" => $this->m_history_deposit->get_deposit_saldo(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }

    public function get_customer_pemilik_ajax()
    {
        $this->load->model('report/m_history_deposit','m_history_deposit');

        $data = (object)[];
        $data->result = [];
        $data->result[0] = (object)[];
        // $data->result[0]->text = 'Project';
        $data->result[0]->children = $this->m_history_deposit->list_customer($this->input->get('data'));

        echo json_encode($data->result);
    }
}