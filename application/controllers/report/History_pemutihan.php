<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/phpspreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
class History_pemutihan extends CI_Controller {
	function __construct() {
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
        $this->load->model('transaksi/m_pemutihan');

        $isi = array();
        $isi['kawasan'] = $this->m_pemutihan->get_kawasan();
        $isi['jenis'] = $this->m_pemutihan->get_service();
        $isi['load_css'] = load_css(['select2','datetimepicker']);
        $isi['load_js'] = load_js(['select2','moment','datetimepicker']);
        $this->view('normal', 'Report > History Pemutihan', 'Report History Pemutihan', 'proyek/report/history_pemutihan/view', $isi);
        $this->load->view('core/css_custom_master');
	}

    public function request_data_json()
    {
        $kawasan = $this->input->get('kawasan');
        $blok = $this->input->get('blok');
        $jenis_service = $this->input->get('jenis_service');
        $periode_awal = $this->input->get('periode_awal');
        $periode_akhir = $this->input->get('periode_akhir');
        $masa_awal = $this->input->get('ed_pemutihan_awal');
        $masa_akhir = $this->input->get('ed_pemutihan_akhir');

        $sql = "
        		SELECT
                    pemutihan.id,
                    pemutihan_nilai.nilai_tagihan,
                    pemutihan_nilai.nilai_denda,
                    (pemutihan_nilai.nilai_tagihan+pemutihan_nilai.nilai_denda) AS nilai_total,
                    pemutihan_unit.service_jenis_id,
                    service_jenis.jenis_service,
                    pemutihan_nilai.nilai_tagihan_type,
                    (
                        CASE pemutihan_nilai.nilai_tagihan_type 
                            WHEN 0 THEN 'Rupiah' 
                            ELSE 'Persentase' 
                        END
                     ) AS nilai_tagihan_type_desc,
                    SUM(pemutihan_unit.pemutihan_nilai_tagihan) AS sum_pemutihan_nilai_tagihan,
                    pemutihan_nilai.nilai_denda_type,
                    (
                        CASE pemutihan_nilai.nilai_denda_type 
                            WHEN 0 THEN 'Rupiah' 
                            ELSE 'Persentase' 
                        END
                     ) AS nilai_denda_type_desc,
                    SUM(pemutihan_unit.pemutihan_nilai_denda) AS sum_pemutihan_nilai_denda,
                    pemutihan_unit.unit_id,
                    blok.id AS blok_id,
                    blok.name AS blok_name,
                    kawasan.id AS kawasan_id,
                    kawasan.name AS kawasan_name,
                    (
                        SELECT TOP 1 
                            approval_status.status_approval
                        FROM
                            approval
                            INNER JOIN
                            approval_wewenang AS aw
                            ON 
                                aw.approval_id = approval.id
                            INNER JOIN
                            approval_status
                            ON 
                                approval_status.id = aw.approval_status_id
                        WHERE
                            approval.dokumen_id = pemutihan.id
                        ORDER BY 
                            aw.id DESC
                     ) AS status_approval
                FROM
                    pemutihan
                    INNER JOIN
                    pemutihan_nilai
                    ON 
                        pemutihan.id = pemutihan_nilai.pemutihan_id
                    INNER JOIN
                    pemutihan_unit
                    ON 
                        pemutihan.id = pemutihan_unit.pemutihan_id
                     INNER JOIN
                     unit
                     ON 
                            pemutihan_unit.unit_id = unit.id
                     INNER JOIN
                     blok
                     ON 
                            blok.id = unit.blok_id
                     INNER JOIN
                     kawasan
                     ON 
                            kawasan.id = blok.kawasan_id
                    INNER JOIN
                    service_jenis
                    ON 
                        service_jenis.id = pemutihan_unit.service_jenis_id

        ";

        $where = array();
    	if ($kawasan!="all"){ array_push($where, "kawasan.id = '".$kawasan."'"); }
    	if ($blok!="all"){ array_push($where, "blok.id = '".$blok."'"); }
        if ($jenis_service!="all"){ array_push($where, "pemutihan_unit.service_jenis_id = '".$jenis_service."'"); }
    	if ($periode_awal!=""){ array_push($where, "pemutihan.periode_awal >= '".$periode_awal."-01'"); }
    	if ($periode_akhir!=""){ array_push($where, "pemutihan.periode_akhir <= '".date("Y-m-d", strtotime($periode_akhir . "-01 +1 Month"))."'"); }
    	if ($masa_awal!=""){ array_push($where, "pemutihan.masa_awal >= '".$masa_awal."-01'"); }
    	if ($masa_akhir!=""){ array_push($where, "pemutihan.masa_akhir <= '".date("Y-m-d", strtotime($masa_akhir . "-01 +1 Month"))."'"); }

		$sql .= ' 
                    WHERE ' . implode(' AND ', $where) . ' 

                    GROUP BY 
                        pemutihan.id,
                        pemutihan_nilai.nilai_tagihan,
                        pemutihan_nilai.nilai_denda,
                        pemutihan_unit.service_jenis_id,
                        service_jenis.jenis_service,
                        pemutihan_nilai.nilai_tagihan_type,
                        pemutihan_nilai.nilai_denda_type,
                        pemutihan_unit.unit_id,
                        blok.id,
                        blok.name,
                        kawasan.id,
                        kawasan.name
                ';

        $table = "
        (
            $sql
        ) temp
        ";
        $primaryKey = 'id';
        $columns = array(
            array('db' => 'id', 'dt' => 0),
            array('db' => 'kawasan_name', 'dt' => 1),
            array('db' => 'blok_name', 'dt' => 2),
            // array('db' => 'jenis_pemutihan', 'dt' => 17),
            array('db' => 'nilai_tagihan', 'dt' => 3),
            array('db' => 'nilai_denda', 'dt' => 4),
            array('db' => 'nilai_total', 'dt' => 5),
            array('db' => 'nilai_tagihan_type_desc', 'dt' => 6),
            array('db' => 'sum_pemutihan_nilai_tagihan', 'dt' => 7),
            array('db' => 'nilai_denda_type_desc', 'dt' => 8),
            array('db' => 'sum_pemutihan_nilai_denda', 'dt' => 9),
            array('db' => 'status_approval', 'dt' => 10),
            array('db' => 'blok_id', 'dt' => 11),
            array('db' => 'kawasan_id', 'dt' => 12)
        );

        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db'   => $this->db->database,
            'host' => $this->db->hostname
        );
        $this->load->library("Ssp_custom");
        $table = SSP_Custom::simple( $_GET, $sql_details, $table, $primaryKey, $columns );

        foreach ($table["data"] as $k => $v) {
            // $table["data"][$k][3] = ($table["data"][$k][3]==1 ? "Lingkungan" : "Air");
            $table["data"][$k][6] = ($table["data"][$k][7]<1 ? "-" : $table["data"][$k][6]);
            $table["data"][$k][8] = ($table["data"][$k][9]<1 ? "-" : $table["data"][$k][8]);

            $table["data"][$k][3] = nominal($table["data"][$k][3], "", 0, ".");
            $table["data"][$k][4] = nominal($table["data"][$k][4], "", 0, ".");
            $table["data"][$k][5] = nominal($table["data"][$k][5], "", 0, ".");
            $table["data"][$k][7] = nominal($table["data"][$k][7], "", 0, ".");
            $table["data"][$k][9] = nominal($table["data"][$k][9], "", 0, ".");

            $table["data"][$k][11] = 
                "
                	<a href='javascript:void(0)' alt='".$table["data"][$k][0].'|'.$table["data"][$k][12].'|'.$table["data"][$k][11]."' class='btn btn-primary detail-data'>
	                    <i class='fa fa-eye'></i>
	                </a>";
        }
        echo(json_encode($table));
    }

    public function get_detail_pemutihan()
    {
        $this->load->model('report/m_history_pemutihan');

        $id = $this->input->post('id');

        $kawasan = $this->input->post('kawasan');
        $blok = $this->input->post('blok');
        $jenis_service = $this->input->post('jenis_service');
        $periode_awal = $this->input->post('periode_awal');
        $periode_akhir = $this->input->post('periode_akhir');
        $masa_awal = $this->input->post('ed_pemutihan_awal');
        $masa_akhir = $this->input->post('ed_pemutihan_akhir');

        $where = array();
    	if ($id!=""){ array_push($where, "pemutihan.id = '".$id."'"); }

    	if ($kawasan!="all"){ array_push($where, "kawasan.id = '".$kawasan."'"); }
    	if ($blok!="all"){ array_push($where, "blok.id = '".$blok."'"); }
        if ($jenis_service!="all"){ array_push($where, "pemutihan_unit.service_jenis_id = '".$jenis_service."'"); }
    	if ($periode_awal!=""){ array_push($where, "pemutihan.periode_awal >= '".$periode_awal."-01'"); }
    	if ($periode_akhir!=""){ array_push($where, "pemutihan.periode_akhir <= '".date("Y-m-d", strtotime($periode_akhir . "-01 +1 Month"))."'"); }
    	if ($masa_awal!=""){ array_push($where, "pemutihan.masa_awal >= '".$masa_awal."-01'"); }
    	if ($masa_akhir!=""){ array_push($where, "pemutihan.masa_akhir <= '".date("Y-m-d", strtotime($masa_akhir . "-01 +1 Month"))."'"); }

		$data_air = array();
		$data_lingkungan = array();
        if ($jenis_service==1) 
        {
            $data_lingkungan = $this->m_history_pemutihan->get_pemutihan_lingkungan((count($where)>0 ? (' AND ' . implode(' AND ', $where)) : ''))->result_array();
        } 
        else if ($jenis_service==2) 
        {
            $data_air = $this->m_history_pemutihan->get_pemutihan_air((count($where)>0 ? (' AND ' . implode(' AND ', $where)) : ''))->result_array();
        } 
        else 
        {
            $data_air = $this->m_history_pemutihan->get_pemutihan_air((count($where)>0 ? (' AND ' . implode(' AND ', $where)) : ''))->result_array();
            $data_lingkungan = $this->m_history_pemutihan->get_pemutihan_lingkungan((count($where)>0 ? (' AND ' . implode(' AND ', $where)) : ''))->result_array();
        }

        $log_approval = array();
        foreach ($this->m_history_pemutihan->get_approval($id)->result_array() as $r)
        {
            array_push($log_approval, [
                'aw_id' => $r['aw_id']
                ,'description' => $r['description']
                ,'u_aw_name' => $r['u_aw_name']
                ,'status' => $r['status']
                ,'tgl' => date('Y-m-d H:i:s', strtotime($r['tgl']))
            ]);
        }

        $this->load->model('m_log');
        $output = array(
        	'detail' => array_merge($data_air, $data_lingkungan),
        	'log' => $this->m_log->get('pemutihan', $id),
            'log_approval' => $log_approval
        );

		echo json_encode($output);
		exit();
    }
}