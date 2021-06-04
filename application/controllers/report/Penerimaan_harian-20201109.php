<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/phpspreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
class Penerimaan_harian extends CI_Controller {
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
        $this->load->view('core/body_header',['title' => 'Report > Penerimaan Harian ','subTitle' => 'Report Harian']);
        $this->load->view('proyek/report/penerimaan_harian/view', ['kawasan'=>$kawasan,'cara_bayar'=>$cara_bayar,'service_jenis'=>$service_jenis]);
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
        // $this->m_history->getRetribusi($kawasan,$blok,$periode_awal,$periode_akhir,$cara_bayar);
        echo json_encode(array(
            'status'=>1,
            'link_data'=>site_url('report/penerimaan_harian/load_table'.$variable)
        ));
    }

    public function load_table()
    {
        $project_id    = $GLOBALS['project']->id;
        $kawasan       = $this->input->get("kawasan");
        $blok          = $this->input->get("blok");
        $periode_awal  = $this->input->get("periode_awal");
        $periode_akhir = $this->input->get("periode_akhir");

        $head = $this->sql_header($kawasan, $blok, $periode_awal, $periode_akhir);
        if ($head->num_rows() > 0) 
        {
            $query = $head->result();
            $join_unit_id = [];
            $j = 0;
            $count = count($query);
            for ($i = 0; $i < $count; $i++) 
            {
                if (isset($query[$i])) 
                {
                    array_push($join_unit_id, $query[$i]);
                    unset($query[$i]);
                    $tmp = [];
                    foreach ($query as $key => $v) {
                        if ($v->cara_pembayaran_id == $join_unit_id[$j]->cara_pembayaran_id) {
                            if (! in_array($v->unit_id, $tmp)) {
                                array_push($tmp, $v->unit_id);
                                $join_unit_id[$j]->unit_id = $join_unit_id[$j]->unit_id.','.$v->unit_id;
                            }
                            unset($query[$key]);
                        }
                    }
                    $j++;
                }
            }

            $distinct_pt = [];
            foreach ($join_unit_id as $h) 
            {
                $nama_pt = $h->nama_pt;
                if (! in_array($nama_pt, $distinct_pt))
                {
                    echo "<tr><td colspan='12' style='background-color: #f9f9f9; font-weight: 600;'>".$h->nama_pt."</td></tr>";
                }
                array_push($distinct_pt, $nama_pt);

                $explode = explode(',', $h->unit_id);
                $unit_id = '';
                foreach ($explode as $u) { $unit_id .= "'".$u."',"; }
                $unit_id = rtrim($unit_id, ',');

                ?>
                <tr><td colspan="12" style="background-color: #f9f9f9; font-weight: 600;"><?=$h->cara_bayar;?></td></tr>
                <?php
                $detail = $this->sql_detail($kawasan, $blok, $periode_awal, $periode_akhir, $unit_id, $h->cara_pembayaran_id);
                $grand_total = 0;
                foreach (array_reverse($detail->result()) as $d) 
                {
                    $tagihan             = $d->tagihan;
                    $nilai_ppn           = $d->nilai_ppn;
                    $total_tagihan       = $d->tagihan + $d->nilai_ppn;
                    $denda               = $d->nilai_denda;
                    $total_tagihan_denda = $total_tagihan + $denda;
                    $periode             = ( $d->code == 'Biaya Admin' ? '' : date('m/Y', strtotime($d->periode)) );
                    $bayar               = $d->code == 'Biaya Admin' ? $tagihan : $d->bayar;

                    if (!empty($d->nilai_diskon)) {
                        $bayar = $bayar - $d->nilai_diskon;
                    }

                    if ($tagihan!==0)
                    {
                        ?>
                        <tr style="background-color: #ffffff;">
                            <td><?= $d->nama_unit; ?></td>
                            <td><?= $d->tgl_bayar; ?></td>
                            <td style="white-space: nowrap;"><?= $d->code; ?></td>
                            <td><?= $periode; ?></td>
                            <td align="right"><?= number_format($tagihan, 0,',','.'); ?></td>
                            <td align="right"><?= ($d->code == 'Biaya Admin' ? '' : number_format($nilai_ppn, 0,',','.')); ?></td>
                            <td align="right"><?= ($d->code == 'Biaya Admin' ? '' : number_format($total_tagihan, 0,',','.')); ?></td>
                            <td align="right"><?= ($d->code == 'Biaya Admin' ? '' : number_format($d->nilai_denda, 0,',','.')); ?></td>
                            <td align="right"><?= ($d->code == 'Biaya Admin' ? '' : number_format($total_tagihan_denda, 0,',','.')); ?></td>
                            <td align="right"><?= ($d->code == 'Biaya Admin' ? '' : number_format($d->nilai_diskon, 0,',','.')); ?></td>
                            <td align="right"><?= ($d->code == 'Biaya Admin' ? '' : number_format($d->nilai_tagihan_pemutihan, 0,',','.')); ?></td>
                            <td align="right"><?= number_format($bayar, 0,',','.'); ?></td>
                        </tr>
                        <?php
                        $grand_total += $bayar;
                    }
                }

                echo "
                <tr style='background-color: #ffffff;'>
                    <td colspan='11' align='right'>TOTAL BAYAR (Rp.)</td>
                    <td align='right'>".number_format($grand_total, 0, ',','.')."</td>
                </tr>";
            }
        }
        else
        {
            echo "<tr><td colspan='12'>No data available in table</td></tr>";
        }
    }

    // query penerimaan header
    public function sql_header($kawasan, $blok, $periode_awal, $periode_akhir)
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
        $head = "
            SELECT DISTINCT
                unit.id AS unit_id,
                unit.project_id,
                CONVERT(DATE, t_pembayaran.tgl_bayar) AS tgl_bayar,
                cara_pembayaran.id AS cara_pembayaran_id,
                CASE ISNULL(bank.id, 0) WHEN 0 THEN cara_pembayaran.name ELSE CONCAT (cara_pembayaran.name, '-', bank.name) END AS cara_bayar,
                ISNULL(m_pt.name, '-') AS nama_pt 
            FROM 
                t_pembayaran_detail
                INNER JOIN t_pembayaran ON t_pembayaran.id = t_pembayaran_detail.t_pembayaran_id
                INNER JOIN unit ON t_pembayaran.unit_id = unit.id
                INNER JOIN blok ON unit.blok_id = blok.id
                INNER JOIN kawasan ON blok.kawasan_id = kawasan.id
                LEFT JOIN cara_pembayaran ON t_pembayaran.cara_pembayaran_id = cara_pembayaran.id
                LEFT JOIN bank ON cara_pembayaran.bank_id = bank.id
                LEFT JOIN dbmaster.dbo.m_pt ON unit.pt_id = m_pt.pt_id 
            WHERE 1=1
                AND ISNULL(t_pembayaran.is_void, 0) = 0 
                AND unit.project_id = '$project_id' 
                $where_blok
                $where_kawasan
                AND CONVERT ( DATE, t_pembayaran.tgl_bayar ) >= '$periode_awal' 
                AND CONVERT ( DATE, t_pembayaran.tgl_bayar ) <= '$periode_akhir' 
            GROUP BY
                unit.id,
                unit.project_id,
                tgl_bayar,
                cara_pembayaran.id,
                CASE ISNULL(bank.id, 0) WHEN 0 THEN cara_pembayaran.name ELSE CONCAT (cara_pembayaran.name, '-', bank.name) END,
                ISNULL(m_pt.name, '-')
            ORDER BY
                CONVERT(DATE, t_pembayaran.tgl_bayar)
            ";
        $head = $this->db->query($head);

        return $head;
    }

    public function sql_detail($kawasan, $blok, $periode_awal, $periode_akhir, $unit_id, $cara_pembayaran_id)
    {
        $project_id = $GLOBALS['project']->id;
        $detail = "
            SELECT
                t_pembayaran.unit_id,
                CONCAT(kawasan.name, ' - ', blok.name, '/', unit.no_unit) AS nama_unit,
                service.code,
                FORMAT(t_pembayaran.tgl_bayar,'dd/MM/yyyy') AS tgl_bayar,
                t_tagihan_lingkungan.periode,
                CONVERT (INT, ROUND((
                t_tagihan_lingkungan_detail.nilai_kavling
                + t_tagihan_lingkungan_detail.nilai_bangunan
                + t_tagihan_lingkungan_detail.nilai_administrasi
                + t_tagihan_lingkungan_detail.nilai_keamanan
                + t_tagihan_lingkungan_detail.nilai_kebersihan ), 0 
                )) AS tagihan,
                CONVERT(INT,ROUND((
                t_tagihan_lingkungan_detail.nilai_kavling
                + t_tagihan_lingkungan_detail.nilai_bangunan
                + t_tagihan_lingkungan_detail.nilai_keamanan
                + t_tagihan_lingkungan_detail.nilai_kebersihan)
                *(t_tagihan_lingkungan_detail.nilai_ppn / 100.0), 0
                )) AS nilai_ppn,
                t_pembayaran_detail.nilai_denda,
                ISNULL(t_pembayaran_detail.nilai_diskon,0) AS nilai_diskon,
                t_pembayaran_detail.nilai_tagihan_pemutihan,
                t_pembayaran_detail.bayar 
            FROM
                t_pembayaran_detail
                INNER JOIN t_pembayaran ON t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id
                INNER JOIN unit ON t_pembayaran.unit_id = unit.id
                INNER JOIN blok ON unit.blok_id = blok.id
                INNER JOIN kawasan ON blok.kawasan_id = kawasan.id
                INNER JOIN service ON t_pembayaran_detail.service_id = service.id AND service.service_jenis_id = 1
                INNER JOIN t_tagihan_lingkungan ON t_pembayaran_detail.tagihan_service_id = t_tagihan_lingkungan.id 
                INNER JOIN t_tagihan_lingkungan_detail ON t_tagihan_lingkungan.id = t_tagihan_lingkungan_detail.t_tagihan_lingkungan_id 
            WHERE 1=1 
                AND unit.project_id = '$project_id' 
                AND unit.id IN ( $unit_id )
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) >= '$periode_awal'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) <= '$periode_akhir'

            UNION

            SELECT 
                t_pembayaran.unit_id,
                CONCAT(kawasan.name, ' - ', blok.name, '/', unit.no_unit) AS nama_unit,
                service.code,
                FORMAT(t_pembayaran.tgl_bayar,'dd/MM/yyyy') AS tgl_bayar,
                t_tagihan_air.periode,
                (t_tagihan_air_detail.nilai + t_tagihan_air_detail.nilai_administrasi) as tagihan,
                '' AS nilai_ppn,
                t_pembayaran_detail.nilai_denda,
                '0' AS nilai_diskon,
                t_pembayaran_detail.nilai_tagihan_pemutihan,
                t_pembayaran_detail.bayar
            FROM
                t_pembayaran_detail
                INNER JOIN t_pembayaran ON t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id
                INNER JOIN unit ON t_pembayaran.unit_id = unit.id
                INNER JOIN blok ON unit.blok_id = blok.id
                INNER JOIN kawasan ON blok.kawasan_id = kawasan.id
                INNER JOIN service ON t_pembayaran_detail.service_id = service.id AND service.service_jenis_id = 2
                INNER JOIN t_tagihan_air ON t_pembayaran_detail.tagihan_service_id = t_tagihan_air.id
                INNER JOIN t_tagihan_air_detail ON t_tagihan_air.id = t_tagihan_air_detail.t_tagihan_air_id
            WHERE 1=1 
                AND unit.project_id = '$project_id' 
                AND unit.id IN ( $unit_id )
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) >= '$periode_awal'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) <= '$periode_akhir'

            UNION

            SELECT 
                t_pembayaran.unit_id,
                CONCAT(kawasan.name, ' - ', blok.name, '/', unit.no_unit) AS nama_unit,
                service.code,
                FORMAT(t_pembayaran.tgl_bayar,'dd/MM/yyyy') AS tgl_bayar,
                CASE
                    WHEN t_tagihan_layanan_lain_detail.periode_awal = t_tagihan_layanan_lain_detail.periode_akhir 
                    THEN CONVERT(VARCHAR, t_tagihan_layanan_lain_detail.periode_awal) 
                ELSE CONCAT(t_tagihan_layanan_lain_detail.periode_awal, '<br>', t_tagihan_layanan_lain_detail.periode_akhir) 
                END AS periode,
                t_tagihan_layanan_lain.total_nilai AS tagihan,
                '' AS nilai_ppn,
                t_pembayaran_detail.nilai_denda,
                '0' AS nilai_diskon,
                t_pembayaran_detail.nilai_tagihan_pemutihan,
                t_pembayaran_detail.bayar
            FROM
                t_pembayaran_detail
                INNER JOIN t_pembayaran ON t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id
                INNER JOIN unit ON t_pembayaran.unit_id = unit.id
                INNER JOIN blok ON unit.blok_id = blok.id
                INNER JOIN kawasan ON blok.kawasan_id = kawasan.id
                INNER JOIN service ON t_pembayaran_detail.service_id = service.id
                INNER JOIN t_tagihan_layanan_lain ON t_pembayaran_detail.tagihan_service_id = t_tagihan_layanan_lain.id
                INNER JOIN t_tagihan_layanan_lain_detail ON t_tagihan_layanan_lain.id = t_tagihan_layanan_lain_detail.t_layanan_lain_tagihan_id
            WHERE 1=1 
                AND unit.project_id = '$project_id' 
                AND unit.id IN ( $unit_id )
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) >= '$periode_awal'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) <= '$periode_akhir'

            UNION

            SELECT DISTINCT
                t_pembayaran.unit_id,
                CONCAT(kawasan.name, ' - ', blok.name, '/', unit.no_unit) AS nama_unit,
                'Biaya Admin' AS code,
                '' AS tgl_bayar,
                '' AS periode,
                t_pembayaran.nilai_biaya_admin_cara_pembayaran AS tagihan,
                '' AS nilai_ppn,
                '' nilai_denda,
                '' AS nilai_diskon,
                '' AS nilai_tagihan_pemutihan,
                '' AS bayar 
            FROM
                t_pembayaran
                INNER JOIN t_pembayaran_detail ON t_pembayaran.id = t_pembayaran_detail.t_pembayaran_id
                INNER JOIN unit ON t_pembayaran.unit_id = unit.id
                INNER JOIN blok ON unit.blok_id = blok.id
                INNER JOIN kawasan ON blok.kawasan_id = kawasan.id 
            WHERE 1=1
                AND unit.project_id = '$project_id' 
                AND unit.id IN ( $unit_id )
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) >= '$periode_awal'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) <= '$periode_akhir'
            ";
        $detail = $this->db->query($detail);
        return $detail;
    }

    // function for export to excel
    public function export_excel()
    {
        $spreadsheet   = new Spreadsheet();
        $sheet         = $spreadsheet->getActiveSheet();
        $filename      = $GLOBALS['project']->code . '_Penerimaan_Harian_' . date('YmdHis');
        $project_id    = $GLOBALS['project']->id;
        $kawasan       = $this->input->get("id_kawasan");
        $blok          = $this->input->get("id_blok");
        $periode_awal  = $this->input->get("periode_awal");
        $periode_akhir = $this->input->get("periode_akhir");
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

        // STYLE judul table
        // $spreadsheet->getActiveSheet()->getStyle('A1:L4')->applyFromArray($styleJudul);

        // style lebar kolom
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(14);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(17);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(17);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(12);

        //Style Judul table
        $spreadsheet->getActiveSheet()->setCellValue('A1', "Penerimaan Harian");
        $spreadsheet->getActiveSheet()->mergeCells("A1:L1");
        $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(13);
        $spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // SET judul table
        $spreadsheet->getActiveSheet()->setCellValue('A2', "Unit")
                    ->mergeCells("A2:A4")
                    ->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('B2', "Tgl. Bayar")
                    ->mergeCells("B2:B4")
                    ->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('C2', "Service")
                    ->mergeCells("C2:C4")
                    ->getStyle('C2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('D2', "Periode")
                    ->mergeCells("D2:D4")
                    ->getStyle('D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('E2', "Nilai (Rp.)")
                    ->mergeCells("E2:L2")
                    ->getStyle('E2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('E3', "Pokok")
                    ->mergeCells("E3:E4")
                    ->getStyle('E3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('F3', "PPN")
                    ->mergeCells("F3:F4")
                    ->getStyle('F3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('G3', "Tagihan")
                    ->getStyle('G3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('G4', "Pokok + PPN")
                    ->getStyle('G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('H3', "Denda")
                    ->mergeCells("H3:H4")
                    ->getStyle('H3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('I3', "Total")
                    ->getStyle('I3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('I4', "Tagihan + Denda")
                    ->getStyle('I4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('J3', "Diskon")
                    ->mergeCells("J3:J4")
                    ->getStyle('J3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('K3', "Pemutihan")
                    ->mergeCells("K3:K4")
                    ->getStyle('K3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->setCellValue('L3', "Bayar")
                    ->mergeCells("L3:L4")
                    ->getStyle('L3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $head = $this->sql_header($kawasan, $blok, $periode_awal, $periode_akhir);
        if ($head->num_rows() > 0) 
        {
            $query = $head->result();
            $join_unit_id = [];
            $j = 0;
            $count = count($query);
            for ($i = 0; $i < $count; $i++) 
            {
                if (isset($query[$i])) 
                {
                    array_push($join_unit_id, $query[$i]);
                    unset($query[$i]);
                    $tmp = [];
                    foreach ($query as $key => $v) {
                        if ($v->cara_pembayaran_id == $join_unit_id[$j]->cara_pembayaran_id) {
                            if (! in_array($v->unit_id, $tmp)) {
                                array_push($tmp, $v->unit_id);
                                $join_unit_id[$j]->unit_id = $join_unit_id[$j]->unit_id.','.$v->unit_id;
                            }
                            unset($query[$key]);
                        }
                    }
                    $j++;
                }
            }

            $index_pt = 5;
            $nomor = 5;
            $distinct_pt = [];
            foreach ($join_unit_id as $h) 
            {
                $nama_pt = $h->nama_pt;
                if (! in_array($nama_pt, $distinct_pt))
                {
                    $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, $h->nama_pt)->mergeCells("A".$nomor.":L".$nomor."");
                }
                array_push($distinct_pt, $nama_pt);

                $explode = explode(',', $h->unit_id);
                $unit_id = '';
                foreach ($explode as $u) { $unit_id .= "'".$u."',"; }
                $unit_id = rtrim($unit_id, ',');

                $nomor++;
                $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, $h->cara_bayar)->mergeCells("A".$nomor.":L".$nomor."");

                $detail = $this->sql_detail($kawasan, $blok, $periode_awal, $periode_akhir, $unit_id, $h->cara_pembayaran_id);
                $grand_total = 0;
                foreach (array_reverse($detail->result()) as $d) 
                {
                    $tagihan             = $d->tagihan;
                    $nilai_ppn           = $d->nilai_ppn;
                    $total_tagihan       = $d->tagihan + $d->nilai_ppn;
                    $denda               = $d->nilai_denda;
                    $total_tagihan_denda = $total_tagihan + $denda;
                    $periode             = ( $d->code == 'Biaya Admin' ? '' : date('m/Y', strtotime($d->periode)) );
                    $bayar               = $d->code == 'Biaya Admin' ? $tagihan : $d->bayar;

                    if (!empty($d->nilai_diskon)) {
                        $bayar = $bayar - $d->nilai_diskon;
                    }

                    if ($tagihan!==0)
                    {
                        $nomor++;
                        $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, $d->nama_unit);
                        $spreadsheet->getActiveSheet()->setCellValue('B'.$nomor, $d->tgl_bayar);
                        $spreadsheet->getActiveSheet()->setCellValue('C'.$nomor, $d->code);
                        $spreadsheet->getActiveSheet()->setCellValue('D'.$nomor, $periode);
                        $spreadsheet->getActiveSheet()->setCellValue('E'.$nomor, $tagihan, 0);
                        $spreadsheet->getActiveSheet()->setCellValue('F'.$nomor, ($d->code == 'Biaya Admin' ? '' : $nilai_ppn));
                        $spreadsheet->getActiveSheet()->setCellValue('G'.$nomor, ($d->code == 'Biaya Admin' ? '' : $total_tagihan));
                        $spreadsheet->getActiveSheet()->setCellValue('H'.$nomor, ($d->code == 'Biaya Admin' ? '' : $d->nilai_denda));
                        $spreadsheet->getActiveSheet()->setCellValue('I'.$nomor, ($d->code == 'Biaya Admin' ? '' : $total_tagihan_denda));
                        $spreadsheet->getActiveSheet()->setCellValue('J'.$nomor, ($d->code == 'Biaya Admin' ? '' : $d->nilai_diskon));
                        $spreadsheet->getActiveSheet()->setCellValue('K'.$nomor, ($d->code == 'Biaya Admin' ? '' : $d->nilai_tagihan_pemutihan));
                        $spreadsheet->getActiveSheet()->setCellValue('L'.$nomor, $bayar)->getStyle('L'.$nomor)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                        $grand_total += $bayar;
                    }
                }

                $nomor++;
                $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, $grand_total)->mergeCells("A".$nomor.":L".$nomor."");
            }
            $nomor++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
}