<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/phpspreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
class Kubikasi_air extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->model('m_login');
		if(!$this->m_login->status_login()) redirect(site_url());
		$this->load->model('report/m_history_pembayaran','m_history');
		$this->load->model('m_core');
		global $jabatan;
		$jabatan = $this->m_core->jabatan();
		global $project;
		$project = $this->m_core->project();
		global $menu;
		$menu = $this->m_core->menu();
	}
	public function index()
	{
        $this->load->model('transaksi/m_meter_air');
		$kawasan = $this->m_meter_air->getKawasan();
		$cara_bayar = $this->m_history->getCaraPembayaran();
		$service_jenis = $this->m_history->getService();
		$this->load->view('core/header');
		$this->load->view('core/side_bar',['menu' => $GLOBALS['menu']]);
		$this->load->view('core/top_bar',['jabatan' => $GLOBALS['jabatan'],'project' => $GLOBALS['project']]);
		$this->load->view('core/body_header',['title' => 'Report > Kubikasi Air ','subTitle' => 'Report Tagihan Meter Air']);
		$this->load->view('proyek/report/kubikasi_air/view', ['kawasan'=>$kawasan,'cara_bayar'=>$cara_bayar,'service_jenis'=>$service_jenis]);
		$this->load->view('core/body_footer');
		$this->load->view('core/footer');
	}

    public function request_data_air()
    {
        $requestData    = $_REQUEST;
        $like_value     = $requestData['search']['value'];
        $column_order   = $requestData['order'][0]['column'];
        $column_dir     = $requestData['order'][0]['dir'];
        $limit_start    = $requestData['start'];
        $limit_length   = $requestData['length'];
        $kawasan        = $this->input->post('kawasan');
        $blok           = $this->input->post('blok');
        $periode_awal   = $this->input->post('periode_awal');
        $periode_akhir  = $this->input->post('periode_akhir');

        $where_global = "
            AND (
                kawasan.name LIKE '%" . $this->db->escape_like_str($like_value) . "%'
                OR CONCAT(blok.name,'/',unit.no_unit) LIKE '%" . $this->db->escape_like_str($like_value) . "%'
                OR pemilik.name LIKE '%" . $this->db->escape_like_str($like_value) . "%'
                OR FORMAT(t_pencatatan_meter_air.periode, 'MM/yyyy') LIKE '%" . $this->db->escape_like_str($like_value) . "%'
            )
        ";
        $sql = $this->sql_meterair($kawasan, $blok, $periode_awal, $periode_akhir, $where_global);
        $data_sql['totalFiltered'] = $this->db->query($sql)->num_rows();
        $data_sql['totalData']     = $this->db->query($sql)->num_rows();
        $columns_order_by = array(
            0 => 'kawasan',
            1 => 'blok_unit',
            2 => 'pemilik',
            3 => 'periode_tagihan',
            4 => 'periode_pemakaian',
            5 => 'meter_awal',
            6 => 'meter_akhir',
            7 => 'meter_pakai',
            8 => 'nilai'
        );

        $sql  .= " ORDER BY " . $columns_order_by[$column_order] . " " . $column_dir . " ";
        $sql  .= " OFFSET " . $limit_start . " ROWS FETCH NEXT " . $limit_length . " ROWS ONLY ";

        $data_sql['query'] = $this->db->query($sql);
        $totalData       = $data_sql['totalData'];
        $totalFiltered   = $data_sql['totalFiltered'];
        $query           = $data_sql['query'];

        $data   = array();
        $urut1  = 1;
        $urut2  = 0;
        foreach ($query->result_array() as $row) {
            $nestedData  = array();
            $total_data  = $totalData;
            $start_dari  = $requestData['start'];
            $perhalaman  = $requestData['length'];
            $asc_desc    = $requestData['order'][0]['dir'];
            if ($asc_desc == 'desc') {
                $nomor = $urut1 + $start_dari;
            }
            if ($asc_desc == 'asc') {
                $nomor = ($total_data - $start_dari) - $urut2;
            }

            $nestedData[] = $row['kawasan'];
            $nestedData[] = $row['blok_unit'];
            $nestedData[] = $row['pemilik'];
            $nestedData[] = $row['periode_tagihan'];
            $nestedData[] = $row['periode_pemakaian'];
            $nestedData[] = $row['meter_awal'];
            $nestedData[] = $row['meter_akhir'];
            $nestedData[] = $row['meter_pakai'];
            $nestedData[] = number_format($row['nilai'], 0, ",", ",");
            $data[] = $nestedData;
            $urut1++;
            $urut2++;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    function sql_meterair($kawasan, $blok, $periode_awal, $periode_akhir, $where_global=NULL)
    {
        $project_id    = $GLOBALS['project']->id;
        $periode_awal  = $periode_awal."-01";
        $periode_akhir = $periode_akhir."-01";
        if ($kawasan=='all') {
            $where_kawasan = "";
        } else {
            $where_kawasan = "AND kawasan.id = '".$kawasan."'";
        }

        if ($blok=='all') {
            $where_blok = "";
        } else {
            $where_blok = "AND blok.id = '".$blok."'";
        }

        $sql = "
            SELECT DISTINCT
                unit.id AS unit_id,
                kawasan.name AS kawasan,
                CONCAT(blok.name,'/',unit.no_unit) AS blok_unit,
                pemilik.name AS pemilik,
                FORMAT(t_pencatatan_meter_air.periode, 'MM/yyyy') AS periode_tagihan,
                FORMAT(DATEADD(MONTH,-1, t_pencatatan_meter_air.periode), 'MM/yyyy') AS periode_pemakaian,
                REPLACE(CONVERT(VARCHAR, CAST(ISNULL(t_pencatatan_meter_air.meter_awal, ISNULL(cek_sebelum.meter_akhir, 0)) AS money), 1), '.00', '') AS meter_awal,
                REPLACE(CONVERT(VARCHAR, CAST(ISNULL(t_pencatatan_meter_air.meter_akhir, 0 ) AS money), 1), '.00', '') AS meter_akhir,
                CASE
                    WHEN ISNULL(t_pencatatan_meter_air.meter_akhir, 0) <= ISNULL( t_pencatatan_meter_air.meter_awal, ISNULL(cek_sebelum.meter_akhir,0)) THEN '0'
                ELSE  
                    REPLACE(CONVERT(VARCHAR, CAST((ISNULL(t_pencatatan_meter_air.meter_akhir , 0)-ISNULL(t_pencatatan_meter_air.meter_awal, ISNULL(cek_sebelum.meter_akhir,0))) AS money), 1), '.00', '')
                END AS meter_pakai,
                tagihan.nilai 
            FROM 
                unit
                INNER JOIN unit_air ON unit_air.unit_id = unit.id AND unit_air.aktif = '1'
                INNER JOIN t_pencatatan_meter_air ON t_pencatatan_meter_air.unit_id = unit.id
                INNER JOIN t_pencatatan_meter_air AS cek_sebelum ON cek_sebelum.unit_id = unit.id
                INNER JOIN blok ON blok.id = unit.blok_id
                INNER JOIN kawasan ON  kawasan.id = blok.kawasan_id
                INNER JOIN customer AS pemilik ON pemilik.id = unit.pemilik_customer_id
                INNER JOIN
                ( 
                    SELECT 
                        t_tagihan_air.unit_id,
                        t_tagihan_air.periode,
                        t_tagihan_air_detail.nilai
                    FROM 
                        t_tagihan_air 
                        INNER JOIN t_tagihan_air_detail ON 
                            t_tagihan_air_detail.t_tagihan_air_id = t_tagihan_air.id 
                            AND t_tagihan_air.periode BETWEEN '$periode_awal' AND '$periode_akhir'
                            AND t_tagihan_air.proyek_id = '$project_id'
                ) AS tagihan ON tagihan.unit_id = t_pencatatan_meter_air.unit_id AND tagihan.periode = t_pencatatan_meter_air.periode
            WHERE 1=1
                AND t_pencatatan_meter_air.periode BETWEEN '$periode_awal' AND '$periode_akhir'
                AND kawasan.project_id = '$project_id'
                $where_blok
                $where_kawasan
                $where_global 
            ";
        // print_r($sql);
        return $sql;
    }

    // function for export to excel
    public function export_excel()
    {
        $spreadsheet   = new Spreadsheet();
        $sheet         = $spreadsheet->getActiveSheet();
        $filename      = $GLOBALS['project']->code . '_Kubikasi_Air_' . date('YmdHis');
        $project_id    = $GLOBALS['project']->id;
        $kawasan       = $this->input->get("id_kawasan");
        $blok          = $this->input->get("id_blok");
        $periode_awal  = $this->input->get("periode_awal");
        $periode_akhir = $this->input->get("periode_akhir");

        $styleJudul = [
            'font' => [
                'color' => [
                    'rgb' => '000000'
                ],
                // 'bold'=>true,
                'size'=>11
            ],
            'fill'=>[
                'fillType' =>  fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'DDDDDD'
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ];

        // STYLE judul table
        $spreadsheet->getActiveSheet()->getStyle('A3:I4')->applyFromArray($styleJudul);

        // style lebar kolom
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(14);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(40);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(17);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(17);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(12);

        //Style Judul table
        $spreadsheet->getActiveSheet()->setCellValue('A1', "LAPORAN TAGIHAN METER AIR")->mergeCells("A1:I1");
        $spreadsheet->getActiveSheet()->setCellValue('A2', "PERIODE PAKAI: ".$periode_awal.' / '.$periode_akhir)->mergeCells("A2:I2");
        // $spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // SET judul table
        $spreadsheet->getActiveSheet()->setCellValue('A3', "KAWASAN")->mergeCells("A3:A4")->getStyle('A3')->getAlignment();
        $spreadsheet->getActiveSheet()->setCellValue('B3', "BLOK/UNIT")->mergeCells("B3:B4")->getStyle('B3')->getAlignment();
        $spreadsheet->getActiveSheet()->setCellValue('C3', "PEMILIK")->mergeCells("C3:C4")->getStyle('C3')->getAlignment();
        $spreadsheet->getActiveSheet()->setCellValue('D3', "PERIODE TAGIHAN")->mergeCells("D3:D4")->getStyle('D3')->getAlignment();
        $spreadsheet->getActiveSheet()->setCellValue('E3', "PERIODE PEMAKAIAN")->mergeCells("E3:E4")->getStyle('E3')->getAlignment();
        $spreadsheet->getActiveSheet()->setCellValue('F3', "METER")->mergeCells("F3:H3")->getStyle('F3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);;
        $spreadsheet->getActiveSheet()->setCellValue('F4', "AWAL");
        $spreadsheet->getActiveSheet()->setCellValue('G4', "AKHIR");
        $spreadsheet->getActiveSheet()->setCellValue('H4', "PAKAI");
        $spreadsheet->getActiveSheet()->setCellValue('I3', "NILAI PAKAI (RP.)")->mergeCells("I3:I4");

        $sql = $this->sql_meterair($kawasan, $blok, $periode_awal, $periode_akhir);
        $sql.= "
            ORDER BY
                kawasan.name,
                CONCAT(blok.name,'/',unit.no_unit),
                FORMAT(t_pencatatan_meter_air.periode, 'MM/yyyy')
            ";
        $sql = $this->db->query($sql);
        $nomor = 5;
        if ($sql->num_rows() > 0) 
        {
            foreach ($sql->result() as $d) 
            {
                $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, $d->kawasan);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$nomor, $d->blok_unit);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$nomor, $d->pemilik);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$nomor, $d->periode_tagihan);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$nomor, $d->periode_pemakaian);
                $spreadsheet->getActiveSheet()->setCellValue('F'.$nomor, $d->meter_awal)->getStyle('F'.$nomor)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->setCellValue('G'.$nomor, $d->meter_akhir)->getStyle('G'.$nomor)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->setCellValue('H'.$nomor, $d->meter_pakai)->getStyle('H'.$nomor)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->setCellValue('I'.$nomor, number_format($d->nilai, 0, ",", ","))->getStyle('I'.$nomor)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $nomor++;
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
}