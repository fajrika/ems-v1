<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/phpspreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
class History_meter_air extends CI_Controller {
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
        $is_service = $this->db->where("service.project_id", $GLOBALS['project']->id)->where("service.delete", 0)->get('service');
        $is_pt = "
            SELECT DISTINCT
                unit.pt_id,
                m_pt.name
            FROM 
                unit 
                INNER JOIN dbmaster.dbo.m_pt ON unit.pt_id = m_pt.pt_id
            WHERE 1=1
                AND unit.pt_id IS NOT NULL
                AND unit.project_id = '".$GLOBALS['project']->id."'
                AND m_pt.name NOT IN('PT. Kosong')
                AND m_pt.active = 1
            ";
        $is_pt = $this->db->query($is_pt);
        $this->load->view('core/header');
        $this->load->view('core/side_bar',['menu' => $GLOBALS['menu']]);
        $this->load->view('core/top_bar',['jabatan' => $GLOBALS['jabatan'],'project' => $GLOBALS['project']]);
        $this->load->view('core/body_header',['title' => 'Report > History Meter Air','subTitle' => 'Meter Air']);
        $this->load->view('proyek/report/history_meter_air/view', [
            'kawasan'=>$kawasan,'cara_bayar'=>$cara_bayar,'service_jenis'=>$service_jenis,'is_service'=>$is_service,'is_pt'=>$is_pt
        ]);
        $this->load->view('core/body_footer');
        $this->load->view('core/footer');
    }

    public function generate(){
        ini_set('memory_limit', '-1'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
        ini_set('sqlsrv.ClientBufferMaxKBSize', '524288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size', '524288'); // Setting to 512M - for pdo_sqlsrv
        ini_set('max_execution_time','-1'); // Setting to 512M - for pdo_sqlsrv
        $variable  = "?kawasan=".$this->input->post("kawasan");
        $variable .= "&blok=".$this->input->post("blok");
        $variable .= "&periode_awal=".$this->input->post("periode_awal");
        $variable .= "&periode_akhir=".$this->input->post("periode_akhir");
        $variable .= "&jenis_service=".$this->input->post("jenis_service");

        echo json_encode(array(
            'status'=>1,
            'link_data'=>site_url('report/history_meter_air/load_table'.$variable)
        ));
    }

    public function load_table()
    {
        $this->load->library("Ssp_custom");
        $kawasan       = $this->input->get('kawasan');
        $blok          = $this->input->get('blok');
        $periode_awal  = $this->input->get('periode_awal');
        $periode_akhir = $this->input->get('periode_akhir');
        $jenis_transaksi= $this->input->get('jenis_transaksi');

        $sql = $this->sql_meterair($kawasan, $blok, $jenis_transaksi, $periode_awal, $periode_akhir);
        $table = "
        (
            $sql
        ) temp
        ";

        $primaryKey = 'id_log_unit_air';
        $columns = array(
            array('db' => 'nomor', 'dt' => 0),
            array('db' => 'nama_kawasan', 'dt' => 1),
            array('db' => 'blok_unit', 'dt' => 2),
            array('db' => 'jenis_transaksi', 'dt' => 3),
            array('db' => 'sub_gol_name', 'dt' => 4),
            array('db' => 'no_seri_meter', 'dt' => 5),
            array('db' => 'tgl_pasang', 'dt' => 6),
            array('db' => 'tgl_aktif', 'dt' => 7),
            array('db' => 'tgl_putus', 'dt' => 8),
            array('db' => 'tgl_transaksi', 'dt' => 9),
            array('db' => 'meter_awal', 'dt' => 10),
            array('db' => 'meter_akhir', 'dt' => 11),
        );
        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db'   => $this->db->database,
            'host' => $this->db->hostname
        );
        $table = SSP_Custom::simple( $_GET, $sql_details, $table, $primaryKey, $columns );

        foreach ($table['data'] as $key => $value) 
        {
            // $table['data'][$key][8] = number_format($table['data'][$key][8], 0, ",", ",");
        }
        echo(json_encode($table));
    }

    public function sql_meterair($kawasan, $blok, $jenis_transaksi, $periode_awal, $periode_akhir)
    {
        $project_id = $GLOBALS['project']->id;
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

        if ($jenis_transaksi=='all') {
            $where_transaksi = "";
        } else {
            $where_transaksi = "AND log_unit_air.jenis_transaksi = '".$jenis_transaksi."'";
        }
        $head = "
            SELECT
                ROW_NUMBER() OVER(ORDER BY log_unit_air.id) nomor,
                log_unit_air.id AS id_log_unit_air,
                kawasan.project_id,
                unit.id AS unit_id,
                kawasan.name AS nama_kawasan,
                CONCAT(blok.name,'/',unit.no_unit) AS blok_unit,
                customer.name,
                m_meter_air.no_seri_meter,
                m_meter_air.nama_meter_air,
                sub_golongan.code,
                CONCAT(sub_golongan.name, ' - ',range_air.code) AS sub_gol_name,
                log_unit_air.jenis_transaksi AS id_jenis_transaksi,
                CASE
                    WHEN log_unit_air.jenis_transaksi = 1 THEN 'Pengaktifan Baru'
                    WHEN log_unit_air.jenis_transaksi = 2 THEN 'Pengaktifan Kembali'
                    WHEN log_unit_air.jenis_transaksi = 3 THEN 'Rusak'
                ELSE 
                    'Pemutusan / Tidak Aktif' 
                END AS jenis_transaksi,
                log_unit_air.nilai_penyambungan,
                log_unit_air.biaya_admin,
                log_unit_air.total_biaya,
                FORMAT(log_unit_air.tgl_pasang,'dd/MM/yyyy') AS tgl_pasang,
                FORMAT(log_unit_air.tgl_aktif,'dd/MM/yyyy') AS tgl_aktif,
                ISNULL(FORMAT(log_unit_air.tgl_putus,'dd/MM/yyyy'), '-') AS tgl_putus,
                FORMAT(log_unit_air.created_date,'dd/MM/yyyy') AS tgl_transaksi,
                CASE
                    WHEN log_unit_air.jenis_transaksi = 1 THEN log_unit_air.angka_meter_sekarang
                    WHEN log_unit_air.jenis_transaksi = 2 THEN log_unit_air.angka_meter_sekarang
                ELSE 0
                END AS meter_awal,
                CASE
                    WHEN log_unit_air.jenis_transaksi = 1 THEN 0
                    WHEN log_unit_air.jenis_transaksi = 2 THEN 0
                    WHEN log_unit_air.jenis_transaksi = 3 THEN log_unit_air.angka_meter_sekarang
                    WHEN log_unit_air.jenis_transaksi = 4 THEN log_unit_air.angka_meter_sekarang
                ELSE 
                    log_unit_air.angka_meter_sekarang
                END AS meter_akhir
            FROM 
                log_unit_air
                INNER JOIN unit ON log_unit_air.unit_id = unit.id
                INNER JOIN customer ON customer.id = unit.pemilik_customer_id 
                INNER JOIN blok ON unit.blok_id = blok.id
                INNER JOIN kawasan ON blok.kawasan_id = kawasan.id
                LEFT JOIN sub_golongan ON sub_golongan.id = log_unit_air.sub_gol_id
                LEFT JOIN range_air ON range_air.id = sub_golongan.range_id
                LEFT JOIN m_meter_air ON log_unit_air.m_meter_air_id = m_meter_air.id
            WHERE 1=1
                AND FORMAT(log_unit_air.created_date,'yyyy-MM-dd') BETWEEN '".$periode_awal."' AND '".$periode_akhir."'
                AND kawasan.project_id = '".$project_id."'
                $where_kawasan
                $where_blok
                $where_transaksi
            ";

        return $head;
    }

    // function for export to excel
    public function export_excel()
    {
        $spreadsheet   = new Spreadsheet();
        $sheet         = $spreadsheet->getActiveSheet();
        $filename      = $GLOBALS['project']->code . '_history_meter_air_' . date('YmdHis');
        $kawasan       = $this->input->get('id_kawasan');
        $blok          = $this->input->get('id_blok');
        $periode_awal  = $this->input->get('periode_awal');
        $periode_akhir = $this->input->get('periode_akhir');
        $jenis_transaksi= $this->input->get('jenis_transaksi');

        $styleJudul  = [
            'font' => [
                'color' => [
                    'rgb' => 'FFFFFF'
                ],
                'bold'=>true,
                'size'=>11
            ],
            'fill'=>[
                'fillType' =>  fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '31869B'
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ];

        $styleBorder = [
            'borders' => [
                'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,],
                'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,],
                'right' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,],
                'left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,],
            ],
        ];

        // STYLE judul table
        // $spreadsheet->getActiveSheet()->getStyle('A1:L4')->applyFromArray($styleJudul);
        $totalColumn = [
            'A'=>8, 'B'=>20, 'C'=>12, 'D'=>20, 'E'=>27, 'F'=>17, 'G'=>18, 'H'=>13, 'I'=>17, 'J'=>15, 'K'=>15, 'L'=>15
        ];
        foreach ($totalColumn as $alfabet => $size) {
            $spreadsheet->getActiveSheet()->getColumnDimension($alfabet)->setWidth($size);
        }

        //Style Judul table
        $spreadsheet->getActiveSheet()->setCellValue('A1', "LAPORAN HISTORY METER AIR")->mergeCells("A1:L1")->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('A2', $periode_awal.' / '.$periode_akhir)->mergeCells("A2:L2")->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $judulSheet = [
            'A'=>'No.', 'B'=>'Kawasan', 'C'=>'Blok/Unit', 'D'=>'Jenis Transaksi', 
            'E'=>'Sub. Golongan', 'F'=>'No. Seri Meter', 'G'=>'Tgl. Pasang', 'H'=>'Tgl. Aktif', 
            'I'=>'Tgl. Pemutusan', 'J'=>'Tgl. Transaksi', 'K'=>'Meter Awal', 'L'=>'Meter Akhir'
        ];
        foreach ($judulSheet as $alfabet => $title) {
            $spreadsheet->getActiveSheet()->setCellValue($alfabet.'4', $title)->getStyle($alfabet.'4')->applyFromArray($styleBorder);
        }


        $sql = $this->sql_meterair($kawasan, $blok, $jenis_transaksi, $periode_awal, $periode_akhir);
        $sql = $this->db->query($sql);
        if ($sql->num_rows() > 0) 
        {
            $start = 5;
            $nomor = 1;
            $totalColumn = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
            foreach ($sql->result() as $key=>$d) 
            {
                // $periode_penggunaan  = ( $d->code == 'Biaya Admin' ? '' : date('m/Y', strtotime($d->periode_penggunaan)) );
                // $periode_penagihan   = ( $d->code == 'Biaya Admin' ? '' : date('m/Y', strtotime($d->periode_penagihan)) );
                $spreadsheet->getActiveSheet()->setCellValue('A'.$start, $nomor)->getStyle('A'.$start)->applyFromArray($styleBorder);
                $spreadsheet->getActiveSheet()->setCellValue('B'.$start, $d->nama_kawasan)->getStyle('B'.$start)->applyFromArray($styleBorder);
                $spreadsheet->getActiveSheet()->setCellValue('C'.$start, $d->blok_unit)->getStyle('C'.$start)->applyFromArray($styleBorder);
                $spreadsheet->getActiveSheet()->setCellValue('D'.$start, $d->jenis_transaksi)->getStyle('D'.$start)->applyFromArray($styleBorder);
                $spreadsheet->getActiveSheet()->setCellValue('E'.$start, $d->sub_gol_name)->getStyle('E'.$start)->applyFromArray($styleBorder);
                $spreadsheet->getActiveSheet()->setCellValue('F'.$start, $d->no_seri_meter)->getStyle('F'.$start)->applyFromArray($styleBorder);
                $spreadsheet->getActiveSheet()->setCellValue('G'.$start, $d->tgl_pasang)->getStyle('G'.$start)->applyFromArray($styleBorder);
                $spreadsheet->getActiveSheet()->setCellValue('H'.$start, $d->tgl_aktif)->getStyle('H'.$start)->applyFromArray($styleBorder);
                $spreadsheet->getActiveSheet()->setCellValue('I'.$start, $d->tgl_putus)->getStyle('I'.$start)->applyFromArray($styleBorder);
                $spreadsheet->getActiveSheet()->setCellValue('J'.$start, $d->tgl_transaksi)->getStyle('J'.$start)->applyFromArray($styleBorder);
                $spreadsheet->getActiveSheet()->setCellValue('K'.$start, $d->meter_awal)->getStyle('K'.$start)->applyFromArray($styleBorder);
                $spreadsheet->getActiveSheet()->setCellValue('L'.$start, $d->meter_akhir)->getStyle('L'.$start)->applyFromArray($styleBorder);
                $start++;
                $nomor++;

                // $spreadsheet->getActiveSheet()->setCellValue('H'.$nomor, ($d->code == 'Biaya Admin' ? '' : $nilai_ppn))
                //             ->getStyle('H'.$nomor)->applyFromArray($styleBorder)->applyFromArray($styleBorder);
                // $spreadsheet->getActiveSheet()->setCellValue('I'.$nomor, ($d->code == 'Biaya Admin' ? '' : $total_tagihan))
                //             ->getStyle('I'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
                // $spreadsheet->getActiveSheet()->setCellValue('J'.$nomor, ($d->code == 'Biaya Admin' ? '' : $d->nilai_denda))
                //             ->getStyle('J'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
                // $spreadsheet->getActiveSheet()->setCellValue('K'.$nomor, ($d->code == 'Biaya Admin' ? '' : $total_tagihan_denda))
                //             ->getStyle('K'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function get_range_date()
    {
        $start_date = $this->input->post('start_date');
        $end_date   = date('Y-m-d', strtotime($start_date . '+7 days'));
        echo json_encode([
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
    }
}