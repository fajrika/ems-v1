<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/phpspreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Master_meter_air extends CI_Controller 
{
    function __construct() 
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('m_login');
        if(!$this->m_login->status_login()) redirect(site_url());
        $this->load->model('m_customer');
        $this->load->model('m_core');
        $this->load->model('transaksi/m_meter_air');
        $this->load->model('m_code_country_telp');
        global $jabatan;
        global $project;
        global $menu;
        $jabatan = $this->m_core->jabatan();
        $project = $this->m_core->project();
        $menu    = $this->m_core->menu();
    }

    public function index()
    {
        ini_set('memory_limit', '256M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
        ini_set('sqlsrv.ClientBufferMaxKBSize', '524288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size', '524288'); // Setting to 512M - for pdo_sqlsrv
        ini_set('max_execution_time','-1'); // Setting to 512M - for pdo_sqlsrv

        $sql = "
            SELECT
                m_meter_air.*,
                CASE
                    WHEN unit.no_unit IS NULL THEN '-'
                ELSE
                    CONCAT(kawasan.name, ' - ', blok.name, '/', unit.no_unit)
                END AS nama_unit
            FROM
                m_meter_air
                LEFT JOIN unit ON m_meter_air.unit_id = unit.id
                LEFT JOIN blok ON unit.blok_id = blok.id
                LEFT JOIN kawasan ON blok.kawasan_id = kawasan.id
            WHERE 
                m_meter_air.project_id = '".$GLOBALS['project']->id."'
            ORDER BY 
                m_meter_air.id DESC
            ";
        $data['meter_air'] = $this->db->query($sql);
        $this->load->view('core/header');
        $this->load->view('core/side_bar', ['menu' => $GLOBALS['menu']]);
        $this->load->view('core/top_bar', ['jabatan' => $GLOBALS['jabatan'],'project' => $GLOBALS['project']]);
        $this->load->view('core/body_header', ['title' => 'Master > Meter Air','subTitle' => 'List']);
        $this->load->view('proyek/master/meter_air/view', $data);
        $this->load->view('core/body_footer');
        $this->load->view('core/footer');
    }

    public function add()
    {
        if ($_POST)
        {
            $this->load->library('form_validation');
            // $this->form_validation->set_rules('kawasan','Kawasan','trim|required');
            // $this->form_validation->set_rules('blok','Blok','trim|required');
            // $this->form_validation->set_rules('unit_id','unit_id','trim|required');
            $this->form_validation->set_rules('kode','Kode','trim|required');
            $this->form_validation->set_rules('nama_meteran','Nama Meteran Air','trim|required');
            $this->form_validation->set_rules('no_seri_meter','No. Meter Air','trim|required');
            $this->form_validation->set_rules('ukuran_meter_air','Ukuran Meter Air','trim|required');
            $this->form_validation->set_rules('tgl_awal','Tanggal Awal','trim|required');
            $this->form_validation->set_rules('tgl_akhir','Tanggal Akhir','trim|required');
            $this->form_validation->set_message('exist_unit','%s sudah ada !');
            $this->form_validation->set_message('required','%s Masih Kosong!');
            if ($this->form_validation->run() == TRUE)
            {
                $insert = [
                    'project_id' => $GLOBALS['project']->id,
                    // 'unit_id' => $this->input->post('unit_id'),
                    'kode' => $this->input->post('kode'),
                    'nama_meter_air' => $this->input->post('nama_meteran'),
                    'no_seri_meter' => $this->input->post('no_seri_meter'),
                    'barcode' => $this->input->post('id_barcode'),
                    'ukuran_meter_air' => $this->input->post('ukuran_meter_air'),
                    'size_pipa' => 0,
                    'tgl_meter_awal' => $this->input->post('tgl_awal'),
                    'tgl_meter_akhir' => $this->input->post('tgl_akhir'),
                    'status_meter' => $this->input->post('status_meteran'),
                    'created_date' => date('Y-m-d H:i:s'),
                    'created_by' => $this->session->userdata('name')
                ];
                if ($insert)
                {
                    $insert = $this->db->insert('m_meter_air', $insert);
                    echo json_encode([
                        'status'=>1,'pesan'=>'Data Berhasil Ditambahkan',
                        'link_href'=>site_url('master-meter-air')
                    ]);
                }
            }
            else
            {
                echo json_encode(array(
                    'status' => 0,
                    'pesan' => validation_errors("<i class='fa fa-times'></i> ")
                ));
            }
        }
        else
        {
            $project = $this->m_core->project();
            @$kode_cust = "CUST/".$project->code."/".date("Y")."/".str_pad(($this->m_customer->last_id()+1), 4, "0", STR_PAD_LEFT);
            $dataPT = $this->m_customer->getPT();
            $dataCodeTelp = $this->m_code_country_telp->get();
            $kawasan = $this->m_meter_air->getKawasan();

            $this->load->view('core/header');
            $this->load->view('core/side_bar',['menu' => $GLOBALS['menu']]);
            $this->load->view('core/top_bar');
            $this->load->view('core/body_header',['title' => 'Master > Meter Air', 'subTitle' => 'Tambah Data']);
            $this->load->view('proyek/master/meter_air/add', [
                'kawasan'=>$kawasan, 'dataCodeTelp'=>$dataCodeTelp, 'dataPT' => $dataPT, 'kode_cust'=>$kode_cust
            ]);
            $this->load->view('core/body_footer');
            $this->load->view('core/footer');
        }
    }

    public function edit($id)
    {
        if ($_POST)
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('kode','Kode','trim|required');
            $this->form_validation->set_rules('nama_meteran','Nama Meteran Air','trim|required');
            $this->form_validation->set_rules('no_seri_meter','No. Meter Air','trim|required');
            $this->form_validation->set_rules('ukuran_meter_air','Ukuran Meter Air','trim|required');
            $this->form_validation->set_rules('tgl_awal','Tanggal Awal','trim|required');
            $this->form_validation->set_rules('tgl_akhir','Tanggal Akhir','trim|required');
            $this->form_validation->set_message('exist_username','%s sudah ada !');
            $this->form_validation->set_message('required','%s Masih Kosong!');
            if ($this->form_validation->run() == TRUE)
            {
                $update = [
                    'kode' => $this->input->post('kode'),
                    'nama_meter_air' => $this->input->post('nama_meteran'),
                    'no_seri_meter' => $this->input->post('no_seri_meter'),
                    'barcode' => $this->input->post('id_barcode'),
                    'ukuran_meter_air' => $this->input->post('ukuran_meter_air'),
                    'size_pipa' => 0,
                    'tgl_meter_awal' => $this->input->post('tgl_awal'),
                    'tgl_meter_akhir' => $this->input->post('tgl_akhir'),
                    // 'status_meter' => $this->input->post('status_meteran'),
                    'updated_date' => date('Y-m-d H:i:s'),
                    'updated_by' => $this->session->userdata('name')
                ];
                if ($update)
                {
                    $update = $this->db->where('id', $id)->update('m_meter_air', $update);
                    echo json_encode([
                        'status'=>1,'pesan'=>'Data Berhasil Diubah',
                        'link_href'=>site_url('master-meter-air')
                    ]);
                }
            }
            else
            {
                echo json_encode(array(
                    'status' => 0,
                    'pesan' => validation_errors("<i class='fa fa-times'></i> ")
                ));
            }
        }
        else
        {
            $data['id'] = $id;
            $data['meteran'] = $this->db->where('id', $id)->limit(1)->get('m_meter_air');
            $this->load->view('proyek/master/meter_air/edit', $data);
        }
    }

    public function load_data()
    {
        if ($_FILES['dokumen']['name'] !== '')
        {
            if($_FILES['dokumen']['error'] == 0)
            {
                $allowed_extension = array('xls', 'xlsx');
                $file_array        = explode(".", $_FILES['dokumen']['name']);
                $file_extension    = end($file_array);
                $inputFileName     = $_FILES['dokumen']['name'];
                $inputFileType     = pathinfo($inputFileName, PATHINFO_EXTENSION);
                if (in_array($file_extension, $allowed_extension))
                {
                    $meter_air = [];
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    $spreadsheet = $reader->load($_FILES['dokumen']['tmp_name']);
                    $sheet = $spreadsheet->getActiveSheet()->toArray();
                    $no = 0;
                    $double = 0;
                    foreach ($sheet as $cell) {
                        if ($no > 0) {
                            $no_seri_meter = $cell[2];
                            $validasi = $this->db->where('LOWER(no_seri_meter)', strtolower($no_seri_meter))->where('project_id', $GLOBALS['project']->id)->get('m_meter_air');
                            if ($validasi->num_rows() > 0) {
                                $double++;
                                $no_seri_meter = "<span style='color: red;'>".$no_seri_meter."</span>";
                            }

                            $meter_air[] = [
                                'project_id'       => $GLOBALS['project']->id,
                                'kode'             => $cell[1],
                                'nama_meter_air'   => $cell[1],
                                'no_seri_meter'    => $no_seri_meter,
                                'barcode'          => $cell[3],
                                'ukuran_meter_air' => $cell[4],
                                'tgl_meter_awal'   => date('Y-m-d'),
                                'tgl_meter_akhir'  => date('Y-m-d'),
                                'status_meter'     => 1,
                                'created_date'     => date('Y-m-d H:i:s'),
                                'created_by'       => $this->session->userdata('name')
                            ];
                        }
                        $no++;
                    }

                    $double_msg = '';
                    if ($double > 0) {
                        $double_msg = "$double data sudah ada di database";
                    }
                    echo json_encode([
                        'status' => 1,
                        'msg' => 'Data successfully uploaded',
                        'uploaded' => $meter_air,
                        'double_data' => $double_msg
                    ]);
                }
                else 
                {
                    echo json_encode([
                        'status' => 0,
                        'msg' => 'Unable to upload a file, This file type is not supported'
                    ]);
                }
            }
        }
        else
        {
            echo json_encode([
                'status' => 0,
                'msg' => 'Please select file'
            ]);
        }
    }

    // save the data
    public function upload()
    {
        if ($_FILES['dokumen']['name'] !== '')
        {
            if($_FILES['dokumen']['error'] == 0)
            {
                $allowed_extension = array('xls', 'xlsx');
                $file_array        = explode(".", $_FILES['dokumen']['name']);
                $file_extension    = end($file_array);
                $inputFileName     = $_FILES['dokumen']['name'];
                $inputFileType     = pathinfo($inputFileName, PATHINFO_EXTENSION);
                if (in_array($file_extension, $allowed_extension))
                {
                    $no = 0;
                    $double = 0;
                    $msg = '';
                    $meter_air = [];
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    $spreadsheet = $reader->load($_FILES['dokumen']['tmp_name']);
                    $sheet = $spreadsheet->getActiveSheet()->toArray();
                    foreach ($sheet as $cell) {
                        if ($no > 0) {
                            $no_seri_meter = $cell[2];
                            $validasi = $this->db->where('LOWER(no_seri_meter)', strtolower($no_seri_meter))->where('project_id', $GLOBALS['project']->id)->get('m_meter_air');
                            if ($validasi->num_rows() > 0) {
                                $no_seri_meter = "<span style='color: red;'>".$no_seri_meter."</span>";
                            } else {
                                $double++;
                                $meter_air[] = [
                                    'project_id'       => $GLOBALS['project']->id,
                                    'kode'             => $cell[1],
                                    'nama_meter_air'   => $cell[1],
                                    'no_seri_meter'    => $no_seri_meter,
                                    'barcode'          => $cell[3],
                                    'ukuran_meter_air' => $cell[4],
                                    'tgl_meter_awal'   => date('Y-m-d'),
                                    'tgl_meter_akhir'  => date('Y-m-d'),
                                    'status_meter'     => 1,
                                    'created_date'     => date('Y-m-d H:i:s'),
                                    'created_by'       => $this->session->userdata('name')
                                ];
                            }
                        }
                        $no++;
                    }

                    $double_msg = '';
                    if ($double > 0) {
                        $this->db->insert_batch('m_meter_air', $meter_air);
                        $response = [
                            'status' => 1,
                            'msg' => "Data berhasil disimpan",
                            'double_data' => "$double data tersimpan"
                        ];
                    } else {
                        $msg = '';
                        $response = [
                            'status' => 0,
                            'msg' => "Tidak ada data yang tersimpan",
                            'double_data' => "$double data tersimpan"
                        ];
                    }
                    echo json_encode($response);
                }
                else 
                {
                    echo json_encode([
                        'status' => 0,
                        'msg' => 'Unable to upload a file, This file type is not supported'
                    ]);
                }
            }
        }
        else
        {
            echo json_encode([
                'status' => 0,
                'msg' => 'Please select file'
            ]);
        }
    }

    public function get_data_upload()
    {
        $this->load->library("Ssp_custom");
        $project_id = $GLOBALS['project']->id;

        $table = "
        (
            SELECT
                ROW_NUMBER() OVER(ORDER BY m_meter_air.id ASC) AS nomor,
                m_meter_air.*
            FROM
                m_meter_air
            WHERE 
                m_meter_air.project_id = '".$GLOBALS['project']->id."'
        ) temp
        ";

        $primaryKey = 'id';
        $columns = array(
            array('db' => 'nomor', 'dt' => 0),
            array('db' => 'kode', 'dt' => 1),
            array('db' => 'nama_meter_air', 'dt' => 2),
            array('db' => 'no_seri_meter', 'dt' => 3),
            array('db' => 'ukuran_meter_air', 'dt' => 4),
            array('db' => 'tgl_meter_awal', 'dt' => 5),
            array('db' => 'tgl_meter_akhir', 'dt' => 6)
        );
        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db'   => $this->db->database,
            'host' => $this->db->hostname
        );
        $table = SSP_Custom::simple( $_GET, $sql_details, $table, $primaryKey, $columns );

        foreach ($table['data'] as $key => $value) {
            $table['data'][$key][5] = date("d-m-Y", strtotime($table['data'][$key][5]));
            $table['data'][$key][6] = date("d-m-Y", strtotime($table['data'][$key][6]));
        }
        echo(json_encode($table));
    }

    public function delete($id)
    {
        $hapus = $this->db->where('id', $id)->limit(1)->delete('m_meter_air');
        if ($hapus) {
            echo json_encode([
                'pesan' => 'Data Berhasil Dihapus',
                'link_href' => site_url('master-meter-air')
            ]);
        }
    }

    public function ajax_get_blok()
    {
        echo json_encode($this->m_meter_air->ajax_get_blok($this->input->get('id')));
    }

    public function ajax_get_unit()
    {
        $id  = $this->input->get('id');
        $sql = "
            SELECT 
                id,
                no_unit
            FROM unit" . ($id != 'all' ? " WHERE blok_id = $id" : "") . " ORDER BY id ASC
        ";
        $sql = $this->db->query($sql);
        if ($sql->num_rows() > 0) {
            echo json_encode($sql->result());
        }
    }

    function exist_unit($unit_id)
    {
        $cek = $this->db->where('unit_id', $unit_id)->get('m_meter_air');
        if($cek->num_rows() > 0){
            return FALSE;
        }else{
            return TRUE;
        }
    }
}
