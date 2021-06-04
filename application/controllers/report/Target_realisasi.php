<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/phpspreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
class Target_realisasi extends CI_Controller {
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
		$this->load->view('core/body_header',['title' => 'Report > Target Vs Realisasi','subTitle' => 'Report Harian']);
		$this->load->view('proyek/report/target_realisasi/view', ['kawasan'=>$kawasan,'cara_bayar'=>$cara_bayar,'service_jenis'=>$service_jenis]);
		$this->load->view('core/body_footer');
		$this->load->view('core/footer');
	}

    public function request_data_json()
    {
        $this->load->library("Ssp_custom");
        $project_id = $GLOBALS['project']->id;
        $kawasan = $this->input->get('kawasan');
        $blok = $this->input->get('blok');
        $periode_tagihan = $this->input->get('periode_tagihan');

        $sqlsrv = $this->sql_show_data($kawasan, $blok, $periode_tagihan);
        $table = "
        (
            $sqlsrv
        ) temp
        ";

        $primaryKey = 'unit_id';
        $columns = array(
            array('db' => 'nama_kawasan', 'dt' => 0),
            array('db' => 'blok_unit', 'dt' => 1),
            array('db' => 'pemilik', 'dt' => 2),
            array('db' => 'periode_tagihan', 'dt' => 3),
            array('db' => 'nilai_air', 'dt' => 4),
            array('db' => 'nilai_lingkungan', 'dt' => 5),
            array('db' => 'unit_id', 'dt' => 6),
            array('db' => 'bayar_air', 'dt' => 7),
            array('db' => 'bayar_ipl', 'dt' => 8),
            array('db' => 'unit_id', 'dt' => 9),
            array('db' => 'unit_id', 'dt' => 10)
        );
        $sql_details = array('user'=>$this->db->username, 'pass'=>$this->db->password, 'db'=>$this->db->database, 'host'=>$this->db->hostname);
        $table = SSP_Custom::simple( $_GET, $sql_details, $table, $primaryKey, $columns );

        foreach ($table['data'] as $key => $value) 
        {
            $nilai_air          = $table['data'][$key][4];
            $nilai_ipl          = $table['data'][$key][5];
            $total_nilai        = $table['data'][$key][4] + $table['data'][$key][5];
            $bayar_air          = $table['data'][$key][7];
            $bayar_ipl          = $table['data'][$key][8];
            $total_realisasi    = $table['data'][$key][7] + $table['data'][$key][8];

            if ($total_nilai!==0 OR $total_realisasi!==0) {
                $percentage = round(($total_realisasi / $total_nilai) * 100, 2);
            } else {
                $percentage = 0;
            }

            $table['data'][$key][4]  = "<div style='text-align: right;'>".number_format($nilai_air, 0, ",", ",")."</div>";
            $table['data'][$key][5]  = "<div style='text-align: right;'>".number_format($nilai_ipl, 0, ",", ",")."</div>";
            $table['data'][$key][6]  = "<div style='text-align: right;'>".number_format($total_nilai, 0, ",", ",")."</div>";
            $table['data'][$key][7]  = "<div style='text-align: right;'>".number_format($bayar_air, 0, ",", ",")."</div>";
            $table['data'][$key][8]  = "<div style='text-align: right;'>".number_format($bayar_ipl, 0, ",", ",")."</div>";
            $table['data'][$key][9]  = "<div style='text-align: right;'>".number_format($total_realisasi, 0, ",", ",")."</div>";
            $table['data'][$key][10] = "<div style='text-align: right;'>".$percentage."</div>";
        }
        echo(json_encode($table));
    }

    // query target realisasi
    public function sql_show_data($kawasan, $blok, $periode_tagihan)
    {
        $project_id = $GLOBALS['project']->id;
        $periode_tagihan = $periode_tagihan.'-01';
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

        $sqlsrv = "
            SELECT DISTINCT
                unit.id AS unit_id,
                unit_lingkungan.tgl_mandiri,
                kawasan.name AS nama_kawasan,
                CONCAT(blok.name,'/',unit.no_unit) AS blok_unit,
                pemilik.name AS pemilik,
                -- CONVERT(DATE,t_tagihan.periode) AS periode_tagihan,
                CONVERT(VARCHAR(7), t_tagihan.periode) AS periode_tagihan,
                ISNULL(CONVERT(INT, 
                    ROUND((
                        t_tagihan_lingkungan_detail.nilai_kavling
                        + t_tagihan_lingkungan_detail.nilai_bangunan
                        + t_tagihan_lingkungan_detail.nilai_administrasi
                        + t_tagihan_lingkungan_detail.nilai_keamanan
                        + t_tagihan_lingkungan_detail.nilai_kebersihan 
                    ),0)), 0
                ) AS nilai_lingkungan,
                ISNULL(CONVERT(INT,
                    ROUND((
                        t_tagihan_air_detail.nilai + t_tagihan_air_detail.nilai_administrasi + t_tagihan_air_detail.nilai_pemeliharaan 
                    ), 0)), 0
                ) AS nilai_air,
                CASE
                    WHEN 
                        t_tagihan_lingkungan.status_tagihan = '1' OR t_tagihan_lingkungan.status_tagihan = '4' 
                    THEN 
                        ISNULL(CONVERT(INT, 
                            ROUND((
                                t_tagihan_lingkungan_detail.nilai_kavling
                                + t_tagihan_lingkungan_detail.nilai_bangunan
                                + t_tagihan_lingkungan_detail.nilai_administrasi
                                + t_tagihan_lingkungan_detail.nilai_keamanan
                                + t_tagihan_lingkungan_detail.nilai_kebersihan 
                            ),0)), 0
                        )
                    ELSE 0
                END  AS bayar_ipl,
                CASE
                    WHEN 
                        t_tagihan_air.status_tagihan = '1' OR t_tagihan_air.status_tagihan = '4'
                    THEN 
                        ISNULL(CONVERT(INT,
                            ROUND((
                                t_tagihan_air_detail.nilai + t_tagihan_air_detail.nilai_administrasi + t_tagihan_air_detail.nilai_pemeliharaan 
                            ), 0)), 0
                        )
                    ELSE 0
                END AS bayar_air 
            FROM 
                t_tagihan
                LEFT JOIN t_tagihan_lingkungan ON 
                    t_tagihan.unit_id = t_tagihan_lingkungan.unit_id AND t_tagihan.periode = t_tagihan_lingkungan.periode
                LEFT JOIN t_tagihan_lingkungan_detail ON t_tagihan_lingkungan.id = t_tagihan_lingkungan_detail.t_tagihan_lingkungan_id
                LEFT JOIN t_tagihan_air ON 
                    t_tagihan.unit_id = t_tagihan_air.unit_id AND t_tagihan.periode = t_tagihan_air.periode
                LEFT JOIN t_tagihan_air_detail ON t_tagihan_air.id = t_tagihan_air_detail.t_tagihan_air_id
                INNER JOIN unit ON t_tagihan.unit_id = unit.id
                LEFT JOIN unit_lingkungan ON unit_lingkungan.unit_id = unit.id
                INNER JOIN blok ON unit.blok_id = blok.id
                INNER JOIN kawasan ON blok.kawasan_id = kawasan.id
                INNER JOIN customer AS pemilik ON pemilik.id = unit.pemilik_customer_id
            WHERE 1=1
                AND CONVERT(DATE, t_tagihan.periode) = '$periode_tagihan'
                AND unit.project_id = '$project_id' 
                $where_blok
                $where_kawasan
                AND (
                    unit_lingkungan.tgl_mandiri IS NULL 
                    OR (
                        '$periode_tagihan' < unit_lingkungan.tgl_mandiri
                    )
                )
        ";
        // print_r($sqlsrv);
        return $sqlsrv;
    }

    // function for export to excel
    public function export_excel()
    {
        $spreadsheet   = new Spreadsheet();
        $sheet         = $spreadsheet->getActiveSheet();
        $filename      = $GLOBALS['project']->code . '_Target_Realisasi_' . date('YmdHis');
        $project_id    = $GLOBALS['project']->id;
        $kawasan       = $this->input->get('kawasan');
        $blok          = $this->input->get('blok');
        $periode_tagihan = $this->input->get('periode_tagihan');

        $styleJudul  = [
            'font' => [
                'color' => [
                    'rgb' => '000000'
                ],
                'bold'=>true,
                'size'=>11
            ]
        ];

        // style lebar kolom
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(45);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(14);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(17);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(17);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(12);

        //Style Judul table
        $spreadsheet->getActiveSheet()->setCellValue('A1', "JENIS LAPORAN")->getStyle('A1')->applyFromArray($styleJudul);
        $spreadsheet->getActiveSheet()->setCellValue('B1', "TARGET VS REALISASI")->getStyle('B1')->applyFromArray($styleJudul);
        $spreadsheet->getActiveSheet()->setCellValue('A2', "PERIODE TAGIHAN")->getStyle('A2')->applyFromArray($styleJudul);
        $spreadsheet->getActiveSheet()->setCellValue('B2', $periode_tagihan)->getStyle('B2')->applyFromArray($styleJudul);

        // SET judul table
        $spreadsheet->getActiveSheet()->setCellValue('A4', "Kawasan")->mergeCells("A4:A5")->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('B4', "Blok/Unit")->mergeCells("B4:B5")->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('C4', "Pemilik")->mergeCells("C4:C5")->getStyle('C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('D4', "Periode Tagihan")->mergeCells("D4:D5")->getStyle('D4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('E4', "Target")->mergeCells("E4:G4")->getStyle('E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('E5', "Nilai Air");
        $spreadsheet->getActiveSheet()->setCellValue('F5', "Nilai IPL");
        $spreadsheet->getActiveSheet()->setCellValue('G5', "Total Target");
        $spreadsheet->getActiveSheet()->setCellValue('H4', "Realisasi")->mergeCells("H4:J4")->getStyle('H4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('H5', "Bayar AIR");
        $spreadsheet->getActiveSheet()->setCellValue('I5', "Bayar IPL");
        $spreadsheet->getActiveSheet()->setCellValue('J5', "Total Realisasi");
        $spreadsheet->getActiveSheet()->setCellValue('K5', "%")->getStyle('K5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);;

        $sqlsrv = $this->sql_show_data($kawasan, $blok, $periode_tagihan);
        $sqlsrv.= "ORDER BY kawasan.name, CONCAT(blok.name,'/',unit.no_unit), pemilik.name ";
        $sqlsrv = $this->db->query($sqlsrv);
        $query  = $sqlsrv->result();
        $count  = count($query);
        $distinct_kawasan = [];
        $j = 0;
        for ($i=0; $i<$count; $i++) {
            if (isset($query[$i])) {
                array_push($distinct_kawasan, $query[$i]);
                unset($query[$i]);
                $tmp = [];
                foreach ($query as $key => $v) {
                    if ($v->nama_kawasan == $distinct_kawasan[$j]->nama_kawasan) {
                        if (! in_array($v->nama_kawasan, $tmp)) {
                            array_push($tmp, $v->nama_kawasan);
                            $distinct_kawasan[$j]->nama_kawasan = $distinct_kawasan[$j]->nama_kawasan;
                        }
                        unset($query[$key]);
                    }
                }
                $j++;
            }
        }

        $nomor = 5;
        foreach ($distinct_kawasan as $p)
        {
            $total_target = 0;
            $total_realisasi = 0;
            foreach ($sqlsrv->result() as $key=>$value)
            {
                if ($p->nama_kawasan == $value->nama_kawasan)
                {
                    $periode       = date('m/Y', strtotime($value->periode_tagihan));
                    $bayar_ipl     = $value->bayar_ipl;
                    $sum_target    = $value->nilai_air + $value->nilai_lingkungan;
                    $sum_realisasi = $value->bayar_air + $bayar_ipl;

                    if ($sum_target!==0 OR $sum_realisasi!==0) {
                        $sum_percentage = round(($sum_realisasi / $sum_target) * 100, 2);
                    } else {
                        $sum_percentage = 0;
                    }

                    $nomor++;
                    $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, $value->nama_kawasan);
                    $spreadsheet->getActiveSheet()->setCellValue('B'.$nomor, $value->blok_unit);
                    $spreadsheet->getActiveSheet()->setCellValue('C'.$nomor, $value->pemilik);
                    $spreadsheet->getActiveSheet()->setCellValue('D'.$nomor, $periode);
                    $spreadsheet->getActiveSheet()->setCellValue('E'.$nomor, $value->nilai_air)->getStyle('E'.$nomor)->getNumberFormat()->setFormatCode('#,##');
                    $spreadsheet->getActiveSheet()->setCellValue('F'.$nomor, $value->nilai_lingkungan)->getStyle('F'.$nomor)->getNumberFormat()->setFormatCode('#,##');
                    $spreadsheet->getActiveSheet()->setCellValue('G'.$nomor, $sum_target)->getStyle('G'.$nomor)->getNumberFormat()->setFormatCode('#,##');

                    if (empty($value->bayar_air)) {
                        $spreadsheet->getActiveSheet()->setCellValue('H'.$nomor, $value->bayar_air);
                    } else {
                        $spreadsheet->getActiveSheet()->setCellValue('H'.$nomor, $value->bayar_air)->getStyle('H'.$nomor)->getNumberFormat()->setFormatCode('#,##');
                    }

                    if (empty($bayar_ipl)) {
                        $spreadsheet->getActiveSheet()->setCellValue('I'.$nomor, $bayar_ipl);
                    } else {
                        $spreadsheet->getActiveSheet()->setCellValue('I'.$nomor, $bayar_ipl)->getStyle('I'.$nomor)->getNumberFormat()->setFormatCode('#,##');
                    }

                    if (empty($sum_realisasi)) {
                        $spreadsheet->getActiveSheet()->setCellValue('J'.$nomor, $sum_realisasi);
                    } else {
                        $spreadsheet->getActiveSheet()->setCellValue('J'.$nomor, $sum_realisasi)->getStyle('J'.$nomor)->getNumberFormat()->setFormatCode('#,##');
                    }
                    $spreadsheet->getActiveSheet()->setCellValue('K'.$nomor, $sum_percentage)->getStyle('K'.$nomor)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    $total_target    += $sum_target;
                    $total_realisasi += $sum_realisasi;
                }
            }

            $nomor++;
            $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, 'TOTAL '.$p->nama_kawasan);
            if (empty($total_target)) {
                $spreadsheet->getActiveSheet()->setCellValue('B'.$nomor, $total_target)->mergeCells("B".$nomor.":G".$nomor);
            } else {
                $spreadsheet->getActiveSheet()->setCellValue('B'.$nomor, $total_target)->mergeCells("B".$nomor.":G".$nomor)->getStyle('B'.$nomor)->getNumberFormat()->setFormatCode('#,##');
            }

            if (empty($total_realisasi)) {
                $spreadsheet->getActiveSheet()->setCellValue('H'.$nomor, $total_realisasi)->mergeCells("H".$nomor.":J".$nomor);
            } else {
                $spreadsheet->getActiveSheet()->setCellValue('H'.$nomor, $total_realisasi)->mergeCells("H".$nomor.":J".$nomor)->getStyle('H'.$nomor)->getNumberFormat()->setFormatCode('#,##');
            }


            if ($total_target!==0 OR $total_realisasi!==0) {
                $total_percentage = round(($total_realisasi / $total_target) * 100, 2);
            } else {
                $total_percentage = 0;
            }
            $spreadsheet->getActiveSheet()->setCellValue('K'.$nomor, $total_percentage)->getStyle('K'.$nomor)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $nomor++;

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }


    /*public function generate(){
        ini_set('memory_limit', '-1'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
        ini_set('sqlsrv.ClientBufferMaxKBSize', '524288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size', '524288'); // Setting to 512M - for pdo_sqlsrv
        ini_set('max_execution_time','-1'); // Setting to 512M - for pdo_sqlsrv
        $variable  = "?kawasan=".$this->input->post("kawasan");
        $variable .= "&blok=".$this->input->post("blok");
        $variable .= "&periode_tagihan=".$this->input->post("periode_tagihan");
        echo json_encode(array(
            'status'=>1,
            'link_data'=>site_url('report/target_realisasi/load_table'.$variable)
        ));
    }*/


    /*public function load_table()
    {
        $kawasan       = $this->input->get("kawasan");
        $blok          = $this->input->get("blok");
        $periode_tagihan  = $this->input->get("periode_tagihan");

        $sqlsrv = $this->sql_show_data($kawasan, $blok, $periode_tagihan);
        $sqlsrv = $this->db->query($sqlsrv);
        if ($sqlsrv->num_rows() > 0) 
        {
            $query = $sqlsrv->result();
            $count = count($query);
            $distinct_kawasan = [];
            $j = 0;
            for ($i=0; $i<$count; $i++) {
                if (isset($query[$i])) {
                    array_push($distinct_kawasan, $query[$i]);
                    unset($query[$i]);
                    $tmp = [];
                    foreach ($query as $key => $v) {
                        if ($v->nama_kawasan == $distinct_kawasan[$j]->nama_kawasan) {
                            if (! in_array($v->nama_kawasan, $tmp)) {
                                array_push($tmp, $v->nama_kawasan);
                                $distinct_kawasan[$j]->nama_kawasan = $distinct_kawasan[$j]->nama_kawasan;
                            }
                            unset($query[$key]);
                        }
                    }
                    $j++;
                }
            }

            foreach ($distinct_kawasan as $p)
            {
                $total_target = 0;
                $total_realisasi = 0;
                foreach ($sqlsrv->result() as $key=>$value) {
                    if ($p->nama_kawasan == $value->nama_kawasan) {
                        $sum_target     = $value->nilai_air + $value->nilai_lingkungan;
                        $sum_realisasi  = $value->total_realisasi;
                        echo "
                            <tr>
                                <td>".$value->nama_kawasan."</td>
                                <td>".$value->blok_unit."</td>
                                <td>".$value->pemilik."</td>
                                <td>".$value->periode_tagihan."</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        ";
                        $total_target += $sum_target;
                        $total_realisasi += $sum_realisasi;
                    }
                }

                echo "
                <tr>
                    <td>TOTAL ".$p->nama_kawasan."</td>
                    <td colspan='6' align='right'>".$total_target."</td>
                    <td colspan='4' align='right'>".$total_realisasi."</td>
                </tr>";
            }
        }
        else
        {
            echo "<tr><td colspan='12'>No data available in table</td></tr>";
        }
    }*/
}