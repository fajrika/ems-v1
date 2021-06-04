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
        $this->load->view('core/body_header',['title' => 'Report > Penerimaan Harian ','subTitle' => 'Report Harian']);
        $this->load->view('proyek/report/penerimaan_harian/view', [
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

        echo json_encode(array(
            'status'=>1,
            'content_body'=>$this->load_table(array(
                'kawasan' => $this->input->post("kawasan"),
                'blok' => $this->input->post("blok"),
                'periode_awal' => $this->input->post("periode_awal"),
                'periode_akhir' => $this->input->post("periode_akhir"),
                'pt_id' => $this->input->post("pt_id"),
                'jenis_service' => $this->input->post("jenis_service"),
                'cara_bayar' => $this->input->post("cara_bayar"),
                'unit_virtual' => $this->input->post('unit_virtual')
            ))
        ));
    }
    public function generate_summary(){
        ini_set('memory_limit', '-1'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
        ini_set('sqlsrv.ClientBufferMaxKBSize', '524288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size', '524288'); // Setting to 512M - for pdo_sqlsrv
        ini_set('max_execution_time','-1'); // Setting to 512M - for pdo_sqlsrv
        $variable  = "?kawasan=".$this->input->post("kawasan");
        $variable .= "&blok=".$this->input->post("blok");
        $variable .= "&periode_awal=".$this->input->post("periode_awal");
        $variable .= "&periode_akhir=".$this->input->post("periode_akhir");
        $variable .= "&pt_id=".$this->input->post("pt_id");
        $variable .= "&jenis_service=".$this->input->post("jenis_service");
        $variable .= "&cara_bayar=".$this->input->post("cara_bayar");

        echo json_encode(array(
            'status'=>1,
            'link_data'=>site_url('report/penerimaan_harian/load_table_summary'.$variable)
        ));
    }
    public function get_data_summary_ajax()
    {
        $project_id    = $GLOBALS['project']->id;
        $kawasan       = $this->input->post("kawasan");
        $blok          = $this->input->post("blok");
        $periode_awal  = $this->input->post("periode_awal");
        $periode_akhir = $this->input->post("periode_akhir");
        $pt_id         = $this->input->post("pt_id");
        $jenis_service = $this->input->post("jenis_service");
        $cara_bayar    = $this->input->post("cara_bayar");

        $output = array(
            'status' => 1,
            'gt_pokok' => 0,
            'gt_ppn' => 0,
            'gt_denda' => 0,
            'gt_biaya_admin' => 0,
            'gt_diskon' => 0,
            'gt_pemutihan' => 0,
            'gt_bayar' => 0,
            'table' => array()
        );

        $head = $this->sql_header($kawasan, $blok, $periode_awal, $periode_akhir, $pt_id, $cara_bayar);
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

            $gt_pokok = 0;
            $gt_ppn = 0;
            $gt_denda = 0;
            $gt_biaya_admin = 0;
            $gt_diskon = 0;
            $gt_pemutihan = 0;
            $gt_bayar = 0;
            $kawasan_name_array = array();
            $kawasan_code_array = array();
            $nama_unit_array = array();
            $code_array = array();
            $tagihan_array = array();
            $nilai_ppn_array = array();
            $total_tagihan_array = array();
            $nilai_denda_array = array();
            $total_tagihan_denda_array = array();
            $nilai_diskon_array = array();
            $nilai_tagihan_pemutihan_array = array();
            $bayar_array = array();
            $biaya_admin_array = array();
            foreach ($join_unit_id as $h) 
            {
                $nama_pt = $h->nama_pt;

                $explode = explode(',', $h->unit_id);
                $unit_id = '';
                foreach ($explode as $u) { $unit_id .= "'".$u."',"; }
                $unit_id = rtrim($unit_id, ',');

                ?>
                <?php
                $detail = $this->sql_detail_new($kawasan, $blok, $periode_awal, $periode_akhir, $unit_id, $jenis_service, $h->cara_pembayaran_id, $h->cara_pembayaran_jenis_id, $h->pt_id);

                $grand_total = 0;
                foreach (array_reverse($detail->result()) as $d) 
                {
                    $tagihan                  = ($d->tagihan ? $d->tagihan : 0);
                    $nilai_ppn                = ($d->nilai_ppn ? $d->nilai_ppn : 0);
                    $total_tagihan            = $tagihan + $nilai_ppn;
                    $denda                    = ($d->nilai_denda ? $d->nilai_denda : 0);
                    $biaya_admin              = ($d->biaya_admin ? $d->biaya_admin : 0);
                    $total_tagihan_denda      = $total_tagihan + $denda + $biaya_admin;
                    $bayar                    = ($d->bayar ? $d->bayar : 0) + $biaya_admin;
                    $nilai_diskon             = ($d->nilai_diskon ? $d->nilai_diskon : 0);
                    $nilai_tagihan_pemutihan  = ($d->nilai_tagihan_pemutihan ? $d->nilai_tagihan_pemutihan : 0);

                    if (!empty($nilai_diskon)) {
                        $bayar = $bayar - $nilai_diskon;
                    }

                    if (!empty($tagihan))
                    {
                        if (!in_array($d->kawasan_name.'|'.$d->code, $kawasan_name_array))
                        {
                            array_push($kawasan_name_array, $d->kawasan_name.'|'.$d->code);
                            array_push($kawasan_code_array, $d->kawasan_code);

                            array_push($nama_unit_array, $d->kawasan_name);

                            array_push($code_array, $d->code);

                            array_push($tagihan_array, $tagihan);
                            array_push($nilai_ppn_array, $nilai_ppn);
                            array_push($total_tagihan_array, $total_tagihan);
                            array_push($nilai_denda_array, $denda);
                            array_push($biaya_admin_array, $biaya_admin);
                            array_push($total_tagihan_denda_array, $total_tagihan_denda);
                            array_push($nilai_diskon_array, $nilai_diskon);
                            array_push($nilai_tagihan_pemutihan_array, $nilai_tagihan_pemutihan);

                            $gt_pokok += $tagihan;
                            $gt_ppn += $nilai_ppn;
                            $gt_denda += $denda;
                            $gt_biaya_admin += $biaya_admin; 
                            $gt_diskon += $nilai_diskon;
                            $gt_pemutihan += $nilai_tagihan_pemutihan;

                            array_push($bayar_array, $bayar);

                            $gt_bayar += $bayar;
                        }
                        else
                        {
                            $index_array = array_search($d->kawasan_name.'|'.$d->code, $kawasan_name_array);

                            $tagihan_array[$index_array] += $tagihan;
                            $nilai_ppn_array[$index_array] += $nilai_ppn;
                            $total_tagihan_array[$index_array] += $total_tagihan;
                            $nilai_denda_array[$index_array] += $denda;
                            $biaya_admin_array[$index_array] += $biaya_admin;
                            $total_tagihan_denda_array[$index_array] += $total_tagihan_denda;
                            $nilai_diskon_array[$index_array] += $nilai_diskon;
                            $nilai_tagihan_pemutihan_array[$index_array] += $nilai_tagihan_pemutihan;

                            $gt_pokok += $tagihan;
                            $gt_ppn += $nilai_ppn;
                            $gt_denda += $denda;
                            $gt_biaya_admin += $biaya_admin; 
                            $gt_diskon += $nilai_diskon;
                            $gt_pemutihan += $nilai_tagihan_pemutihan;

                            $bayar_array[$index_array] += $bayar;

                            $gt_bayar += $bayar;
                        }
                        $grand_total += $bayar;
                    }
                }
            }
            for ($i=0; $i < count($kawasan_name_array); $i++)
            { 
                array_push($output['table'], array(
                    $kawasan_code_array[$i],
                    $nama_unit_array[$i],
                    $code_array[$i],
                    nominal($tagihan_array[$i],"",0, "."),
                    nominal($nilai_ppn_array[$i],"",0, "."),
                    nominal($total_tagihan_array[$i],"",0, "."),
                    nominal($nilai_denda_array[$i],"",0, "."),
                    nominal($biaya_admin_array[$i],"",0, "."),
                    nominal($total_tagihan_denda_array[$i],"",0, "."),
                    nominal($nilai_diskon_array[$i],"",0, "."),
                    nominal($nilai_tagihan_pemutihan_array[$i],"",0, "."),
                    nominal($bayar_array[$i],"",0, ".")
                ));
            }
            $output['gt_pokok'] = $gt_pokok;
            $output['gt_ppn'] = $gt_ppn;
            $output['gt_denda'] = $gt_denda;
            $output['gt_biaya_admin'] = $gt_biaya_admin;
            $output['gt_diskon'] = $gt_diskon;
            $output['gt_pemutihan'] = $gt_pemutihan;
            $output['gt_bayar'] = $gt_bayar;
        }
        echo json_encode($output);
    }

    public function load_table($data)
    {
        $project_id    = $GLOBALS['project']->id;
        $kawasan       = $data["kawasan"];
        $blok          = $data["blok"];
        $periode_awal  = $data["periode_awal"];
        $periode_akhir = $data["periode_akhir"];
        $pt_id         = $data["pt_id"];
        $jenis_service = $data["jenis_service"];
        $cara_bayar    = $data["cara_bayar"];
        $unit_virtual  = $data["unit_virtual"];

        $content_body = "";

        $head = $this->sql_header($kawasan, $blok, $periode_awal, $periode_akhir, $pt_id, $cara_bayar, $unit_virtual);
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
                    $content_body .= "<tr><td colspan='17' style='background-color: #f9f9f9; font-weight: 600;'>".$h->nama_pt."</td></tr>";
                }
                array_push($distinct_pt, $nama_pt);

                $explode = explode(',', $h->unit_id);
                $unit_id = '';
                foreach ($explode as $u) { $unit_id .= "'".$u."',"; }
                $unit_id = rtrim($unit_id, ',');

                $content_body .= '<tr><td colspan="17" style="background-color: #c1ffe9; font-weight: 600;">'.$h->cara_bayar.'</td></tr>';
                $detail = $this->sql_detail($kawasan, $blok, $periode_awal, $periode_akhir, $unit_id, $jenis_service, $h->cara_pembayaran_id, $h->cara_pembayaran_jenis_id, $h->pt_id, $unit_virtual);
                $grand_total = 0;
                foreach (array_reverse($detail->result()) as $d) 
                {
                    $tagihan                  = ($d->tagihan ? $d->tagihan : 0);
                    $nilai_ppn                = ($d->nilai_ppn ? $d->nilai_ppn : 0);
                    $total_tagihan            = $tagihan + $nilai_ppn;
                    $denda                    = ($d->nilai_denda ? $d->nilai_denda : 0);
                    $total_tagihan_denda      = $total_tagihan + $denda;
                    $periode_penggunaan       = ( $d->code == 'Biaya Admin' ? '' : date('m/Y', strtotime($d->periode_penggunaan)) );
                    $periode_penagihan        = ( $d->code == 'Biaya Admin' ? '' : date('m/Y', strtotime($d->periode_penagihan)) );
                    $bayar                    = $d->code == 'Biaya Admin' ? $tagihan : ($d->bayar ? $d->bayar : 0);
                    $nilai_diskon             = ($d->nilai_diskon ? $d->nilai_diskon : 0);
                    $nilai_tagihan_pemutihan  = ($d->nilai_tagihan_pemutihan ? $d->nilai_tagihan_pemutihan : 0);

                    if (!empty($nilai_diskon)) {
                        $bayar = $bayar - $nilai_diskon;
                    }

                    if (!empty($tagihan))
                    {
                        $content_body .= '
                            <tr style="background-color: #ffffff;">
                                <td>'.$d->nama_unit.'</td>
                                <td>'.$d->name.'</td>
                                <td>'.$d->tgl_bayar.'</td>
                                <td>'.$d->jam_bayar.'</td>
                                <td style="white-space: nowrap;">'.$d->code.'</td>
                                <td>'.$periode_penggunaan.'</td>
                                <td>'.$periode_penagihan.'</td>
                                <td align="right">'.nominal($tagihan,"",0, ".").'</td>
                                <td align="right">'.($d->code == 'Biaya Admin' ? '' : nominal($nilai_ppn,"",0, ".")).'</td>
                                <td align="right">'.($d->code == 'Biaya Admin' ? '' : nominal($total_tagihan,"",0, ".")).'</td>
                                <td align="right">'.($d->code == 'Biaya Admin' ? '' : nominal($denda,"",0, ".")).'</td>
                                <td align="right">'.($d->code == 'Biaya Admin' ? '' : nominal($total_tagihan_denda,"",0, ".")).'</td>
                                <td align="right">'.($d->code == 'Biaya Admin' ? '' : nominal($nilai_diskon,"",0, ".")).'</td>
                                <td align="right">'.($d->code == 'Biaya Admin' ? '' : nominal($nilai_tagihan_pemutihan,"",0, ".")).'</td>
                                <td align="right">'.nominal($bayar,"",0, ".").'</td>
                                <td align="left">'.$d->no_kwitansi.'</td>
                                <td align="left">'.$d->virtual_account.'</td>
                            </tr>
                        ';
                        $grand_total += $bayar;
                    }
                }

                $content_body .= "
                <tr style='background-color: #ffffff;'>
                    <td colspan='14' align='right'>TOTAL BAYAR (Rp.)</td>
                    <td align='right'>".number_format($grand_total, 0, ',','.')."</td>
                    <td colspan='2' align='right'></td>
                </tr>";
            }
        }
        else
        {
            $content_body.= "<tr><td colspan='17'>No data available in table</td></tr>";
        }
        return $content_body;
    }
    public function load_table_summary()
    {
        $project_id    = $GLOBALS['project']->id;
        $kawasan       = $this->input->get("kawasan");
        $blok          = $this->input->get("blok");
        $periode_awal  = $this->input->get("periode_awal");
        $periode_akhir = $this->input->get("periode_akhir");
        $pt_id         = $this->input->get("pt_id");
        $jenis_service = $this->input->get("jenis_service");
        $cara_bayar = $this->input->get("cara_bayar");

        $head = $this->sql_header($kawasan, $blok, $periode_awal, $periode_akhir, $pt_id, $cara_bayar);
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
            $gt_pokok = 0;
            $gt_ppn = 0;
            $gt_denda = 0;
            $gt_biaya_admin = 0;
            $gt_diskon = 0;
            $gt_pemutihan = 0;
            $gt_bayar = 0;
            $nama_pt_array = array();
            $kawasan_name_array = array();
            $kawasan_code_array = array();
            $nama_unit_array = array();
            $code_array = array();
            $tagihan_array = array();
            $nilai_ppn_array = array();
            $total_tagihan_array = array();
            $nilai_denda_array = array();
            $total_tagihan_denda_array = array();
            $nilai_diskon_array = array();
            $nilai_tagihan_pemutihan_array = array();
            $bayar_array = array();
            $biaya_admin_array = array();
            foreach ($join_unit_id as $h) 
            {
                $nama_pt = $h->nama_pt;
                if (! in_array($nama_pt, $distinct_pt))
                {
                    // echo "<tr><td colspan='13' style='background-color: #f9f9f9; font-weight: 600;'>".$h->nama_pt."</td></tr>";
                }
                array_push($distinct_pt, $nama_pt);

                $explode = explode(',', $h->unit_id);
                $unit_id = '';
                foreach ($explode as $u) { $unit_id .= "'".$u."',"; }
                $unit_id = rtrim($unit_id, ',');

                ?>
                <!-- <tr><td colspan="13" style="background-color: #c1ffe9; font-weight: 600;"><?=$h->cara_bayar;?></td></tr> -->
                <?php
                $detail = $this->sql_detail_new($kawasan, $blok, $periode_awal, $periode_akhir, $unit_id, $jenis_service, $h->cara_pembayaran_id, $h->cara_pembayaran_jenis_id, $h->pt_id);

                $grand_total = 0;
                foreach (array_reverse($detail->result()) as $d) 
                {
                    $tagihan                  = ($d->tagihan ? $d->tagihan : 0);
                    $nilai_ppn                = ($d->nilai_ppn ? $d->nilai_ppn : 0);
                    $total_tagihan            = $tagihan + $nilai_ppn;
                    $denda                    = ($d->nilai_denda ? $d->nilai_denda : 0);
                    $biaya_admin              = ($d->biaya_admin ? $d->biaya_admin : 0);
                    $total_tagihan_denda      = $total_tagihan + $denda + $biaya_admin;
                    $bayar                    = ($d->bayar ? $d->bayar : 0) + $biaya_admin;
                    $nilai_diskon             = ($d->nilai_diskon ? $d->nilai_diskon : 0);
                    $nilai_tagihan_pemutihan  = ($d->nilai_tagihan_pemutihan ? $d->nilai_tagihan_pemutihan : 0);

                    if (!empty($nilai_diskon)) {
                        $bayar = $bayar - $nilai_diskon;
                    }

                    if (!empty($tagihan))
                    {
                        if (!in_array($d->kawasan_name.'|'.$d->code, $kawasan_name_array))
                        {
                            array_push($kawasan_name_array, $d->kawasan_name.'|'.$d->code);
                            array_push($nama_pt_array, $nama_pt);
                            array_push($kawasan_code_array, $d->kawasan_code);

                            array_push($nama_unit_array, $d->kawasan_name);

                            array_push($code_array, $d->code);

                            array_push($tagihan_array, $tagihan);
                            array_push($nilai_ppn_array, $nilai_ppn);
                            array_push($total_tagihan_array, $total_tagihan);
                            array_push($nilai_denda_array, $denda);
                            array_push($biaya_admin_array, $biaya_admin);
                            array_push($total_tagihan_denda_array, $total_tagihan_denda);
                            array_push($nilai_diskon_array, $nilai_diskon);
                            array_push($nilai_tagihan_pemutihan_array, $nilai_tagihan_pemutihan);

                            $gt_pokok += $tagihan;
                            $gt_ppn += $nilai_ppn;
                            $gt_denda += $denda;
                            $gt_biaya_admin += $biaya_admin; 
                            $gt_diskon += $nilai_diskon;
                            $gt_pemutihan += $nilai_tagihan_pemutihan;

                            array_push($bayar_array, $bayar);

                            $gt_bayar += $bayar;
                        }
                        else
                        {
                            $index_array = array_search($d->kawasan_name.'|'.$d->code, $kawasan_name_array);

                            // $code_array[$index_array] .= ','.$d->code;

                            $tagihan_array[$index_array] += $tagihan;
                            $nilai_ppn_array[$index_array] += $nilai_ppn;
                            $total_tagihan_array[$index_array] += $total_tagihan;
                            $nilai_denda_array[$index_array] += $denda;
                            $biaya_admin_array[$index_array] += $biaya_admin;
                            $total_tagihan_denda_array[$index_array] += $total_tagihan_denda;
                            $nilai_diskon_array[$index_array] += $nilai_diskon;
                            $nilai_tagihan_pemutihan_array[$index_array] += $nilai_tagihan_pemutihan;

                            $gt_pokok += $tagihan;
                            $gt_ppn += $nilai_ppn;
                            $gt_denda += $denda;
                            $gt_biaya_admin += $biaya_admin; 
                            $gt_diskon += $nilai_diskon;
                            $gt_pemutihan += $nilai_tagihan_pemutihan;

                            $bayar_array[$index_array] += $bayar;

                            $gt_bayar += $bayar;
                        }
                        $grand_total += $bayar;
                    }
                }

                // echo "
                // <tr style='background-color: #ffffff;'>
                //     <td colspan='10' align='right'>TOTAL BAYAR (Rp.)</td>
                //     <td align='right'>".nominal($grand_total,"",0, ".")."</td>
                // </tr>";
            }
            for ($i=0; $i < count($kawasan_name_array); $i++)
            { 
                // $code_array[$i] = ((empty($code_array[$i]) ? '-' : implode(', ', array_values(array_unique(array_values(array_filter(explode(',', $code_array[$i]))))))));
                ?>
                <tr style="background-color: #ffffff;">
                    <td><?= $nama_pt_array[$i]; ?></td>
                    <td><?= $kawasan_code_array[$i]; ?></td>
                    <td><?= $nama_unit_array[$i]; ?></td>
                    <td style="white-space: nowrap;"><?= $code_array[$i]; ?></td>
                    <td align="right"><?= nominal($tagihan_array[$i],"",0, "."); ?></td>
                    <td align="right"><?= nominal($nilai_ppn_array[$i],"",0, "."); ?></td>
                    <td align="right"><?= nominal($total_tagihan_array[$i],"",0, "."); ?></td>
                    <td align="right"><?= nominal($nilai_denda_array[$i],"",0, "."); ?></td>
                    <td align="right"><?= nominal($biaya_admin_array[$i],"",0, "."); ?></td>
                    <td align="right"><?= nominal($total_tagihan_denda_array[$i],"",0, "."); ?></td>
                    <td align="right"><?= nominal($nilai_diskon_array[$i],"",0, "."); ?></td>
                    <td align="right"><?= nominal($nilai_tagihan_pemutihan_array[$i],"",0, "."); ?></td>
                    <td align="right"><?= nominal($bayar_array[$i],"",0, "."); ?></td>
                </tr>
                <?php
            }
            echo "
            <tr style='background-color: #ffffff;'>
                <td colspan='4' align='right'>Grand Total (Rp.)</td>
                <td align='right'>".nominal($gt_pokok,"",0, ".")."</td>
                <td align='right'>".nominal($gt_ppn,"",0, ".")."</td>
                <td align='right'>".nominal($gt_pokok + $gt_ppn,"",0, ".")."</td>
                <td align='right'>".nominal($gt_denda,"",0, ".")."</td>
                <td align='right'>".nominal($gt_biaya_admin,"",0, ".")."</td>
                <td align='right'>".nominal($gt_pokok + $gt_ppn + $gt_denda + $gt_biaya_admin,"",0, ".")."</td>
                <td align='right'>".nominal($gt_diskon,"",0, ".")."</td>
                <td align='right'>".nominal($gt_pemutihan,"",0, ".")."</td>
                <td align='right'>".nominal($gt_bayar,"",0, ".")."</td>
            </tr>";
        }
        else
        {
            echo "<tr><td colspan='13'>No data available in table</td></tr>";
        }
    }

    // query penerimaan header
    public function sql_header($kawasan, $blok, $periode_awal, $periode_akhir, $pt_id, $cara_bayar, $unit_virtual=NULL)
    {
        $project_id = $GLOBALS['project']->id;
        if ($unit_virtual) 
        {
            $head = $this->db->query("
                SELECT DISTINCT
                    unit_virtual.id AS unit_id,
                    unit_virtual.project_id,
                    customer.name,
                    CONVERT(DATE, t_pembayaran.tgl_bayar) AS tgl_bayar,
                    cara_pembayaran.id AS cara_pembayaran_id,
                    cara_pembayaran_jenis.id AS cara_pembayaran_jenis_id,
                    CASE ISNULL(bank.id, 0) WHEN 0 THEN cara_pembayaran.name ELSE CONCAT (cara_pembayaran.name, '-', bank.name) END AS cara_bayar,
                    ISNULL(m_pt.name, '-') AS nama_pt,
                    customer.pt_id
                FROM 
                    t_pembayaran_detail
                    INNER JOIN t_pembayaran ON t_pembayaran.id = t_pembayaran_detail.t_pembayaran_id
                    INNER JOIN unit_virtual ON t_pembayaran.unit_id = unit_virtual.id
                    INNER JOIN customer ON customer.id = unit_virtual.customer_id 
                    LEFT JOIN cara_pembayaran ON t_pembayaran.cara_pembayaran_id = cara_pembayaran.id
                    LEFT JOIN cara_pembayaran_jenis ON t_pembayaran.jenis_cara_pembayaran_id = cara_pembayaran_jenis.id
                    LEFT JOIN bank ON cara_pembayaran.bank_id = bank.id
                    LEFT JOIN dbmaster.dbo.m_pt ON customer.pt_id = m_pt.pt_id 
                WHERE 1=1
                    AND ISNULL(t_pembayaran.is_void, 0) = 0 
                    AND unit_virtual.project_id = '$project_id' 
                    AND CONVERT ( DATE, t_pembayaran.tgl_bayar ) >= '$periode_awal' 
                    AND CONVERT ( DATE, t_pembayaran.tgl_bayar ) <= '$periode_akhir' 
                    AND customer.pt_id = '".$pt_id."'
                GROUP BY
                    unit_virtual.id,
                    customer.name,
                    unit_virtual.project_id,
                    tgl_bayar,
                    cara_pembayaran.id,
                    cara_pembayaran_jenis.id,
                    CASE ISNULL(bank.id, 0) WHEN 0 THEN cara_pembayaran.name ELSE CONCAT (cara_pembayaran.name, '-', bank.name) END,
                    ISNULL(m_pt.name, '-'),
                    customer.pt_id
                ORDER BY
                    CONVERT(DATE, t_pembayaran.tgl_bayar)
            ");
        }
        else
        {
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
            if ($cara_bayar=='all') {
                $where_cara_bayar = "";
            } else {
                $where_cara_bayar = "AND cara_pembayaran.id = '".$cara_bayar."'";
            }
            $head = "
                SELECT DISTINCT
                    unit.id AS unit_id,
                    unit.project_id,
                    customer.name,
                    CONVERT(DATE, t_pembayaran.tgl_bayar) AS tgl_bayar,
                    cara_pembayaran.id AS cara_pembayaran_id,
                    cara_pembayaran_jenis.id AS cara_pembayaran_jenis_id,
                    CASE ISNULL(bank.id, 0) WHEN 0 THEN cara_pembayaran.name ELSE CONCAT (cara_pembayaran.name, '-', bank.name) END AS cara_bayar,
                    ISNULL(m_pt.name, '-') AS nama_pt,
                    unit.pt_id
                FROM 
                    t_pembayaran_detail
                    INNER JOIN t_pembayaran ON t_pembayaran.id = t_pembayaran_detail.t_pembayaran_id
                    INNER JOIN unit ON t_pembayaran.unit_id = unit.id
                    INNER JOIN customer ON customer.id = unit.pemilik_customer_id 
                    INNER JOIN blok ON unit.blok_id = blok.id
                    INNER JOIN kawasan ON blok.kawasan_id = kawasan.id
                    LEFT JOIN cara_pembayaran ON t_pembayaran.cara_pembayaran_id = cara_pembayaran.id
                    LEFT JOIN cara_pembayaran_jenis ON t_pembayaran.jenis_cara_pembayaran_id = cara_pembayaran_jenis.id
                    LEFT JOIN bank ON cara_pembayaran.bank_id = bank.id
                    LEFT JOIN dbmaster.dbo.m_pt ON unit.pt_id = m_pt.pt_id 
                WHERE 1=1
                    AND ISNULL(t_pembayaran.is_void, 0) = 0 
                    AND unit.project_id = '$project_id' 
                    $where_blok
                    $where_kawasan
                    $where_cara_bayar
                    AND CONVERT ( DATE, t_pembayaran.tgl_bayar ) >= '$periode_awal' 
                    AND CONVERT ( DATE, t_pembayaran.tgl_bayar ) <= '$periode_akhir' 
                    AND unit.pt_id = '".$pt_id."'
                GROUP BY
                    unit.id,
                    customer.name,
                    unit.project_id,
                    tgl_bayar,
                    cara_pembayaran.id,
                    cara_pembayaran_jenis.id,
                    CASE ISNULL(bank.id, 0) WHEN 0 THEN cara_pembayaran.name ELSE CONCAT (cara_pembayaran.name, '-', bank.name) END,
                    ISNULL(m_pt.name, '-'),
                    unit.pt_id
                ORDER BY
                    CONVERT(DATE, t_pembayaran.tgl_bayar)
                ";
            $head = $this->db->query($head);
        }

        return $head;
    }

    public function sql_detail($kawasan, $blok, $periode_awal, $periode_akhir, $unit_id, $jenis_service, $cara_pembayaran_id, $cara_pembayaran_jenis_id, $pt_id, $unit_virtual=NULL)
    {
        $project_id = $GLOBALS['project']->id;
        $where_unit = "AND unit.id IN ( $unit_id )";
        if($kawasan == 'all' AND $blok == 'all'){
            $where_unit = '';
        }
        $sql_lingkungan = "
            SELECT
                t_pembayaran.unit_id,
                customer.name,
                kawasan.name AS kawasan_name,
                kawasan.code AS kawasan_code,
                CONCAT(kawasan.name, ' - ', blok.name, '/', unit.no_unit) AS nama_unit,
                service.code,
                FORMAT(t_pembayaran.tgl_bayar,'dd/MM/yyyy') AS tgl_bayar,
                FORMAT(t_pembayaran.tgl_bayar,'HH:mm:ss') AS jam_bayar,
                CASE
                    WHEN ISNULL(service.jarak_periode_penggunaan, 0) > 0 THEN DATEADD(MONTH, - ISNULL(service.jarak_periode_penggunaan, 0), t_tagihan_lingkungan.periode)
                ELSE
                    t_tagihan_lingkungan.periode
                END AS periode_penggunaan,
                t_tagihan_lingkungan.periode AS periode_penagihan,
                CONVERT (INT, ROUND((
                    t_tagihan_lingkungan_detail.nilai_kavling
                    + t_tagihan_lingkungan_detail.nilai_bangunan
                    + t_tagihan_lingkungan_detail.nilai_administrasi
                    + t_tagihan_lingkungan_detail.nilai_keamanan
                    + t_tagihan_lingkungan_detail.nilai_kebersihan ), 0 
                )) AS tagihan,
                CONVERT(INT,
                    ROUND
                    (
                        (
                            t_tagihan_lingkungan_detail.nilai_kavling
                            + t_tagihan_lingkungan_detail.nilai_bangunan
                            + t_tagihan_lingkungan_detail.nilai_administrasi
                            + t_tagihan_lingkungan_detail.nilai_keamanan
                            + t_tagihan_lingkungan_detail.nilai_kebersihan
                        ) * (t_tagihan_lingkungan_detail.nilai_ppn / 100.0), 0
                    ) * t_tagihan_lingkungan_detail.ppn_flag
                ) AS nilai_ppn,
                t_pembayaran_detail.nilai_denda,
                ISNULL(t_pembayaran_detail.nilai_diskon,0) AS nilai_diskon,
                t_pembayaran_detail.nilai_tagihan_pemutihan,
                t_pembayaran_detail.bayar,
                t_pembayaran.no_kwitansi,
                t_pembayaran.count_print_kwitansi,
                RIGHT(CONCAT('00000000', unit.virtual_account), 8) AS virtual_account 
            FROM
                t_pembayaran_detail
                INNER JOIN t_pembayaran ON t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id
                INNER JOIN unit ON t_pembayaran.unit_id = unit.id
                INNER JOIN blok ON unit.blok_id = blok.id
                INNER JOIN kawasan ON blok.kawasan_id = kawasan.id
                INNER JOIN service ON t_pembayaran_detail.service_id = service.id AND service.service_jenis_id = 1
                INNER JOIN t_tagihan_lingkungan ON t_pembayaran_detail.tagihan_service_id = t_tagihan_lingkungan.id 
                INNER JOIN t_tagihan_lingkungan_detail ON t_tagihan_lingkungan.id = t_tagihan_lingkungan_detail.t_tagihan_lingkungan_id 
                INNER JOIN customer ON customer.id = unit.pemilik_customer_id 
            WHERE 1=1 
                AND unit.project_id = '$project_id' 
                AND unit.pt_id = '$pt_id'
                $where_unit
                AND ISNULL(t_pembayaran.is_void, 0) = 0 
                AND t_pembayaran.cara_pembayaran_id = '$cara_pembayaran_id'
                AND t_pembayaran.jenis_cara_pembayaran_id = '$cara_pembayaran_jenis_id'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) >= '$periode_awal'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) <= '$periode_akhir'
        ";

        $sql_air = "
            SELECT 
                t_pembayaran.unit_id,
                customer.name,
                kawasan.name AS kawasan_name,
                kawasan.code AS kawasan_code,
                CONCAT(kawasan.name, ' - ', blok.name, '/', unit.no_unit) AS nama_unit,
                service.code,
                FORMAT(t_pembayaran.tgl_bayar,'dd/MM/yyyy') AS tgl_bayar,
                FORMAT(t_pembayaran.tgl_bayar,'HH:mm:ss') AS jam_bayar,
                CASE
                    WHEN ISNULL(service.jarak_periode_penggunaan, 0) > 0 THEN DATEADD(MONTH, - ISNULL(service.jarak_periode_penggunaan, 0), t_tagihan_air.periode)
                ELSE
                    t_tagihan_air.periode
                END AS periode_penggunaan,
                t_tagihan_air.periode AS periode_penagihan,
                (t_tagihan_air_detail.nilai + t_tagihan_air_detail.nilai_administrasi + t_tagihan_air_detail.nilai_pemeliharaan) AS tagihan,
                CONVERT(INT,
                    ROUND
                    (
                        (
                            t_tagihan_air_detail.nilai + 
                            t_tagihan_air_detail.nilai_administrasi + 
                            t_tagihan_air_detail.nilai_pemeliharaan
                        ) * (t_tagihan_air_detail.nilai_ppn / 100.0), 0
                    ) * t_tagihan_air_detail.ppn_flag
                ) AS nilai_ppn,
                t_pembayaran_detail.nilai_denda,
                '0' AS nilai_diskon,
                t_pembayaran_detail.nilai_tagihan_pemutihan,
                t_pembayaran_detail.bayar,
                t_pembayaran.no_kwitansi,
                t_pembayaran.count_print_kwitansi,
                RIGHT(CONCAT('00000000', unit.virtual_account), 8) AS virtual_account 
            FROM
                t_pembayaran_detail
                INNER JOIN t_pembayaran ON t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id
                INNER JOIN unit ON t_pembayaran.unit_id = unit.id
                INNER JOIN blok ON unit.blok_id = blok.id
                INNER JOIN kawasan ON blok.kawasan_id = kawasan.id
                INNER JOIN service ON t_pembayaran_detail.service_id = service.id AND service.service_jenis_id = 2
                INNER JOIN t_tagihan_air ON t_pembayaran_detail.tagihan_service_id = t_tagihan_air.id
                INNER JOIN t_tagihan_air_detail ON t_tagihan_air.id = t_tagihan_air_detail.t_tagihan_air_id
                INNER JOIN customer ON customer.id = unit.pemilik_customer_id 
            WHERE 1=1
                AND unit.project_id = '$project_id' 
                AND unit.pt_id = '$pt_id'
                $where_unit
                AND ISNULL(t_pembayaran.is_void, 0) = 0 
                AND t_pembayaran.cara_pembayaran_id = '$cara_pembayaran_id'
                AND t_pembayaran.jenis_cara_pembayaran_id = '$cara_pembayaran_jenis_id'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) >= '$periode_awal'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) <= '$periode_akhir'
        ";

        $sql_lainnya = "
            SELECT 
                t_pembayaran.unit_id,
                customer.name,
                kawasan.name AS kawasan_name,
                kawasan.code AS kawasan_code,
                CONCAT(kawasan.name, ' - ', blok.name, '/', unit.no_unit) AS nama_unit,
                service.code,
                FORMAT(t_pembayaran.tgl_bayar,'dd/MM/yyyy') AS tgl_bayar,
                FORMAT(t_pembayaran.tgl_bayar,'HH:mm:ss') AS jam_bayar,
                CASE
                    WHEN t_tagihan_layanan_lain_detail.periode_awal = t_tagihan_layanan_lain_detail.periode_akhir 
                    THEN CONVERT(VARCHAR, t_tagihan_layanan_lain_detail.periode_awal) 
                ELSE CONCAT(t_tagihan_layanan_lain_detail.periode_awal, '<br>', t_tagihan_layanan_lain_detail.periode_akhir) 
                END AS periode_penggunaan,
                CASE
                    WHEN t_tagihan_layanan_lain_detail.periode_awal = t_tagihan_layanan_lain_detail.periode_akhir 
                    THEN CONVERT(VARCHAR, t_tagihan_layanan_lain_detail.periode_awal) 
                ELSE CONCAT(t_tagihan_layanan_lain_detail.periode_awal, '<br>', t_tagihan_layanan_lain_detail.periode_akhir) 
                END AS periode_penagihan,
                t_tagihan_layanan_lain.total_nilai AS tagihan,
                '' AS nilai_ppn,
                t_pembayaran_detail.nilai_denda,
                '0' AS nilai_diskon,
                t_pembayaran_detail.nilai_tagihan_pemutihan,
                t_pembayaran_detail.bayar,
                t_pembayaran.no_kwitansi,
                t_pembayaran.count_print_kwitansi,
                RIGHT(CONCAT('00000000', unit.virtual_account), 8) AS virtual_account 
            FROM
                t_pembayaran_detail
                INNER JOIN t_pembayaran ON t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id
                INNER JOIN unit ON t_pembayaran.unit_id = unit.id
                INNER JOIN blok ON unit.blok_id = blok.id
                INNER JOIN kawasan ON blok.kawasan_id = kawasan.id
                INNER JOIN service ON t_pembayaran_detail.service_id = service.id
                INNER JOIN t_tagihan_layanan_lain ON t_pembayaran_detail.tagihan_service_id = t_tagihan_layanan_lain.id
                INNER JOIN t_tagihan_layanan_lain_detail ON t_tagihan_layanan_lain.id = t_tagihan_layanan_lain_detail.t_layanan_lain_tagihan_id
                INNER JOIN customer ON customer.id = unit.pemilik_customer_id 
            WHERE 1=1 
                AND unit.project_id = '$project_id' 
                AND unit.pt_id = '$pt_id'
                $where_unit
                AND ISNULL(t_pembayaran.is_void, 0) = 0 
                AND t_pembayaran.cara_pembayaran_id = '$cara_pembayaran_id'
                AND t_pembayaran.jenis_cara_pembayaran_id = '$cara_pembayaran_jenis_id'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) >= '$periode_awal'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) <= '$periode_akhir'
        ";

        $sql_admin = "
            SELECT DISTINCT
                t_pembayaran.unit_id,
                customer.name,
                kawasan.name AS kawasan_name,
                kawasan.code AS kawasan_code,
                CONCAT(kawasan.name, ' - ', blok.name, '/', unit.no_unit) AS nama_unit,
                'Biaya Admin' AS code,
                '' AS tgl_bayar,
                '' AS jam_bayar,
                '' AS periode_penggunaan,
                '' AS periode_penagihan,
                t_pembayaran.nilai_biaya_admin_cara_pembayaran AS tagihan,
                '' AS nilai_ppn,
                '' nilai_denda,
                '' AS nilai_diskon,
                '' AS nilai_tagihan_pemutihan,
                '' AS bayar,
                t_pembayaran.no_kwitansi AS no_kwitansi,
                '' AS count_print_kwitansi,
                '' AS virtual_account 
            FROM
                t_pembayaran
                INNER JOIN t_pembayaran_detail ON t_pembayaran.id = t_pembayaran_detail.t_pembayaran_id
                INNER JOIN unit ON t_pembayaran.unit_id = unit.id
                INNER JOIN blok ON unit.blok_id = blok.id
                INNER JOIN kawasan ON blok.kawasan_id = kawasan.id 
                INNER JOIN customer ON customer.id = unit.pemilik_customer_id 
            WHERE 1=1
                AND unit.project_id = '$project_id' 
                AND unit.pt_id = '$pt_id'
                $where_unit
                AND ISNULL(t_pembayaran.is_void, 0) = 0 
                AND t_pembayaran.cara_pembayaran_id = '$cara_pembayaran_id'
                AND t_pembayaran.jenis_cara_pembayaran_id = '$cara_pembayaran_jenis_id'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) >= '$periode_awal'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) <= '$periode_akhir'
            ";

        $sql_unit_virtual = "
            SELECT 
                t_pembayaran.unit_id,
                customer.name,
                unit_virtual.unit AS kawasan_name,
                'Unit Virtual' AS kawasan_code,
                unit_virtual.unit AS nama_unit,
                service.code,
                FORMAT(t_pembayaran.tgl_bayar,'dd/MM/yyyy') AS tgl_bayar,
                FORMAT(t_pembayaran.tgl_bayar,'HH:mm:ss') AS jam_bayar,
                CASE
                    WHEN t_tagihan_layanan_lain_detail.periode_awal = t_tagihan_layanan_lain_detail.periode_akhir 
                    THEN CONVERT(VARCHAR, t_tagihan_layanan_lain_detail.periode_awal) 
                ELSE CONCAT(t_tagihan_layanan_lain_detail.periode_awal, '<br>', t_tagihan_layanan_lain_detail.periode_akhir) 
                END AS periode_penggunaan,
                CASE
                    WHEN t_tagihan_layanan_lain_detail.periode_awal = t_tagihan_layanan_lain_detail.periode_akhir 
                    THEN CONVERT(VARCHAR, t_tagihan_layanan_lain_detail.periode_awal) 
                ELSE CONCAT(t_tagihan_layanan_lain_detail.periode_awal, '<br>', t_tagihan_layanan_lain_detail.periode_akhir) 
                END AS periode_penagihan,
                t_tagihan_layanan_lain.total_nilai AS tagihan,
                '' AS nilai_ppn,
                t_pembayaran_detail.nilai_denda,
                '0' AS nilai_diskon,
                t_pembayaran_detail.nilai_tagihan_pemutihan,
                t_pembayaran_detail.bayar,
                t_pembayaran.no_kwitansi,
                t_pembayaran.count_print_kwitansi,
                '0' AS virtual_account 
            FROM
                t_pembayaran_detail
                INNER JOIN t_pembayaran ON t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id
                LEFT JOIN unit_virtual ON t_pembayaran.unit_id = unit_virtual.id
                LEFT JOIN service ON t_pembayaran_detail.service_id = service.id
                LEFT JOIN t_tagihan_layanan_lain ON t_pembayaran_detail.tagihan_service_id = t_tagihan_layanan_lain.id
                LEFT JOIN t_tagihan_layanan_lain_detail ON t_tagihan_layanan_lain.id = t_tagihan_layanan_lain_detail.t_layanan_lain_tagihan_id
                LEFT JOIN customer ON customer.id = unit_virtual.customer_id 
            WHERE 1=1 
                AND unit_virtual.project_id = '$project_id' 
                AND customer.pt_id = '$pt_id'
                AND ISNULL(t_pembayaran.is_void, 0) = 0 
                AND t_pembayaran.cara_pembayaran_id = '$cara_pembayaran_id'
                AND t_pembayaran.jenis_cara_pembayaran_id = '$cara_pembayaran_jenis_id'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) >= '$periode_awal'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) <= '$periode_akhir'

            UNION

            SELECT DISTINCT
                t_pembayaran.unit_id,
                customer.name,
                unit_virtual.unit AS kawasan_name,
                'Unit Virtual' AS kawasan_code,
                unit_virtual.unit AS nama_unit,
                'Biaya Admin' AS code,
                '' AS tgl_bayar,
                '' AS jam_bayar,
                '' AS periode_penggunaan,
                '' AS periode_penagihan,
                t_pembayaran.nilai_biaya_admin_cara_pembayaran AS tagihan,
                '' AS nilai_ppn,
                '' nilai_denda,
                '' AS nilai_diskon,
                '' AS nilai_tagihan_pemutihan,
                '' AS bayar,
                t_pembayaran.no_kwitansi AS no_kwitansi,
                '' AS count_print_kwitansi,
                '' AS virtual_account 
            FROM
                t_pembayaran
                INNER JOIN t_pembayaran_detail ON t_pembayaran.id = t_pembayaran_detail.t_pembayaran_id
                INNER JOIN unit_virtual ON t_pembayaran.unit_id = unit_virtual.id
                INNER JOIN customer ON customer.id = unit_virtual.customer_id 
            WHERE 1=1
                AND unit_virtual.project_id = '$project_id' 
                AND customer.pt_id = '$pt_id'
                AND ISNULL(t_pembayaran.is_void, 0) = 0 
                AND t_pembayaran.cara_pembayaran_id = '$cara_pembayaran_id'
                AND t_pembayaran.jenis_cara_pembayaran_id = '$cara_pembayaran_jenis_id'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) >= '$periode_awal'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) <= '$periode_akhir'
            ";

        $show_service = "";
        if ($unit_virtual)
        {
            $show_service = $sql_unit_virtual;
        }
        else
        {
            if ($jenis_service == '1') {
                $show_service .= $sql_lingkungan;
            } elseif ($jenis_service == '2') {
                $show_service .= $sql_air;
            } elseif ($jenis_service == '6') {
                $show_service .= $sql_lainnya;
            } else {
                $show_service = implode(' UNION ',[$sql_lingkungan,$sql_air,$sql_lainnya,$sql_admin]);
                // $show_service .= $sql_lingkungan;
                // $show_service .= $sql_air;
                // $show_service .= $sql_lainnya;
                // $show_service .= $sql_admin;
            }
        }

        $detail = $show_service;
        // print_r($detail);
        $detail = $this->db->query($detail);
        return $detail;
    }

    public function sql_detail_new($kawasan, $blok, $periode_awal, $periode_akhir, $unit_id, $jenis_service, $cara_pembayaran_id, $cara_pembayaran_jenis_id, $pt_id)
    {
        $project_id = $GLOBALS['project']->id;
        $where_unit = "AND unit.id IN ( $unit_id )";
        if($kawasan == 'all' AND $blok == 'all'){
            $where_unit = '';
        }
        $sql_lingkungan = "
            SELECT
                t_pembayaran.unit_id,
                customer.name,
                kawasan.name AS kawasan_name,
                kawasan.code AS kawasan_code,
                CONCAT(kawasan.name, ' - ', blok.name, '/', unit.no_unit) AS nama_unit,
                service.code,
                FORMAT(t_pembayaran.tgl_bayar,'dd/MM/yyyy') AS tgl_bayar,
                CASE
                    WHEN ISNULL(service.jarak_periode_penggunaan, 0) > 0 THEN DATEADD(MONTH, - ISNULL(service.jarak_periode_penggunaan, 0), t_tagihan_lingkungan.periode)
                ELSE
                    t_tagihan_lingkungan.periode
                END AS periode_penggunaan,
                t_tagihan_lingkungan.periode AS periode_penagihan,
                CONVERT (INT, ROUND((
                    t_tagihan_lingkungan_detail.nilai_kavling
                    + t_tagihan_lingkungan_detail.nilai_bangunan
                    + t_tagihan_lingkungan_detail.nilai_administrasi
                    + t_tagihan_lingkungan_detail.nilai_keamanan
                    + t_tagihan_lingkungan_detail.nilai_kebersihan ), 0 
                )) AS tagihan,
                CONVERT(INT,
                    ROUND
                    (
                        (
                            t_tagihan_lingkungan_detail.nilai_kavling
                            + t_tagihan_lingkungan_detail.nilai_bangunan
                            + t_tagihan_lingkungan_detail.nilai_administrasi
                            + t_tagihan_lingkungan_detail.nilai_keamanan
                            + t_tagihan_lingkungan_detail.nilai_kebersihan
                        ) * (t_tagihan_lingkungan_detail.nilai_ppn / 100.0), 0
                    ) * t_tagihan_lingkungan_detail.ppn_flag
                ) AS nilai_ppn,
                t_pembayaran_detail.nilai_denda,
                t_pembayaran.nilai_biaya_admin_cara_pembayaran AS biaya_admin,
                ISNULL(t_pembayaran_detail.nilai_diskon,0) AS nilai_diskon,
                t_pembayaran_detail.nilai_tagihan_pemutihan,
                t_pembayaran_detail.bayar,
                t_pembayaran.no_kwitansi,
                t_pembayaran.count_print_kwitansi,
                RIGHT(CONCAT('00000000', unit.virtual_account), 8) AS virtual_account 
            FROM
                t_pembayaran_detail
                INNER JOIN t_pembayaran ON t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id
                INNER JOIN unit ON t_pembayaran.unit_id = unit.id
                INNER JOIN blok ON unit.blok_id = blok.id
                INNER JOIN kawasan ON blok.kawasan_id = kawasan.id
                INNER JOIN service ON t_pembayaran_detail.service_id = service.id AND service.service_jenis_id = 1
                INNER JOIN t_tagihan_lingkungan ON t_pembayaran_detail.tagihan_service_id = t_tagihan_lingkungan.id 
                INNER JOIN t_tagihan_lingkungan_detail ON t_tagihan_lingkungan.id = t_tagihan_lingkungan_detail.t_tagihan_lingkungan_id 
                INNER JOIN customer ON customer.id = unit.pemilik_customer_id 
            WHERE 1=1 
                AND unit.project_id = '$project_id' 
                AND unit.pt_id = '$pt_id'
                $where_unit
                AND ISNULL(t_pembayaran.is_void, 0) = 0 
                AND t_pembayaran.cara_pembayaran_id = '$cara_pembayaran_id'
                AND t_pembayaran.jenis_cara_pembayaran_id = '$cara_pembayaran_jenis_id'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) >= '$periode_awal'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) <= '$periode_akhir'
        ";

        $sql_air = "
            SELECT 
                t_pembayaran.unit_id,
                customer.name,
                kawasan.name AS kawasan_name,
                kawasan.code AS kawasan_code,
                CONCAT(kawasan.name, ' - ', blok.name, '/', unit.no_unit) AS nama_unit,
                service.code,
                FORMAT(t_pembayaran.tgl_bayar,'dd/MM/yyyy') AS tgl_bayar,
                CASE
                    WHEN ISNULL(service.jarak_periode_penggunaan, 0) > 0 THEN DATEADD(MONTH, - ISNULL(service.jarak_periode_penggunaan, 0), t_tagihan_air.periode)
                ELSE
                    t_tagihan_air.periode
                END AS periode_penggunaan,
                t_tagihan_air.periode AS periode_penagihan,
                (t_tagihan_air_detail.nilai + t_tagihan_air_detail.nilai_administrasi + t_tagihan_air_detail.nilai_pemeliharaan) AS tagihan,
                CONVERT(INT,
                    ROUND
                    (
                        (
                            t_tagihan_air_detail.nilai + 
                            t_tagihan_air_detail.nilai_administrasi + 
                            t_tagihan_air_detail.nilai_pemeliharaan
                        ) * (t_tagihan_air_detail.nilai_ppn / 100.0), 0
                    ) * t_tagihan_air_detail.ppn_flag
                ) AS nilai_ppn,
                t_pembayaran_detail.nilai_denda,
                t_pembayaran.nilai_biaya_admin_cara_pembayaran AS biaya_admin,
                '0' AS nilai_diskon,
                t_pembayaran_detail.nilai_tagihan_pemutihan,
                t_pembayaran_detail.bayar,
                t_pembayaran.no_kwitansi,
                t_pembayaran.count_print_kwitansi,
                RIGHT(CONCAT('00000000', unit.virtual_account), 8) AS virtual_account 
            FROM
                t_pembayaran_detail
                INNER JOIN t_pembayaran ON t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id
                INNER JOIN unit ON t_pembayaran.unit_id = unit.id
                INNER JOIN blok ON unit.blok_id = blok.id
                INNER JOIN kawasan ON blok.kawasan_id = kawasan.id
                INNER JOIN service ON t_pembayaran_detail.service_id = service.id AND service.service_jenis_id = 2
                INNER JOIN t_tagihan_air ON t_pembayaran_detail.tagihan_service_id = t_tagihan_air.id
                INNER JOIN t_tagihan_air_detail ON t_tagihan_air.id = t_tagihan_air_detail.t_tagihan_air_id
                INNER JOIN customer ON customer.id = unit.pemilik_customer_id 
            WHERE 1=1
                AND unit.project_id = '$project_id' 
                AND unit.pt_id = '$pt_id'
                $where_unit
                AND ISNULL(t_pembayaran.is_void, 0) = 0 
                AND t_pembayaran.cara_pembayaran_id = '$cara_pembayaran_id'
                AND t_pembayaran.jenis_cara_pembayaran_id = '$cara_pembayaran_jenis_id'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) >= '$periode_awal'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) <= '$periode_akhir'
        ";

        $sql_lainnya = "
            SELECT 
                t_pembayaran.unit_id,
                customer.name,
                kawasan.name AS kawasan_name,
                kawasan.code AS kawasan_code,
                CONCAT(kawasan.name, ' - ', blok.name, '/', unit.no_unit) AS nama_unit,
                service.code,
                FORMAT(t_pembayaran.tgl_bayar,'dd/MM/yyyy') AS tgl_bayar,
                CASE
                    WHEN t_tagihan_layanan_lain_detail.periode_awal = t_tagihan_layanan_lain_detail.periode_akhir 
                    THEN CONVERT(VARCHAR, t_tagihan_layanan_lain_detail.periode_awal) 
                ELSE CONCAT(t_tagihan_layanan_lain_detail.periode_awal, '<br>', t_tagihan_layanan_lain_detail.periode_akhir) 
                END AS periode_penggunaan,
                CASE
                    WHEN t_tagihan_layanan_lain_detail.periode_awal = t_tagihan_layanan_lain_detail.periode_akhir 
                    THEN CONVERT(VARCHAR, t_tagihan_layanan_lain_detail.periode_awal) 
                ELSE CONCAT(t_tagihan_layanan_lain_detail.periode_awal, '<br>', t_tagihan_layanan_lain_detail.periode_akhir) 
                END AS periode_penagihan,
                t_tagihan_layanan_lain.total_nilai AS tagihan,
                '' AS nilai_ppn,
                t_pembayaran_detail.nilai_denda,
                t_pembayaran.nilai_biaya_admin_cara_pembayaran AS biaya_admin,
                '0' AS nilai_diskon,
                t_pembayaran_detail.nilai_tagihan_pemutihan,
                t_pembayaran_detail.bayar,
                t_pembayaran.no_kwitansi,
                t_pembayaran.count_print_kwitansi,
                RIGHT(CONCAT('00000000', unit.virtual_account), 8) AS virtual_account 
            FROM
                t_pembayaran_detail
                INNER JOIN t_pembayaran ON t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id
                INNER JOIN unit ON t_pembayaran.unit_id = unit.id
                INNER JOIN blok ON unit.blok_id = blok.id
                INNER JOIN kawasan ON blok.kawasan_id = kawasan.id
                INNER JOIN service ON t_pembayaran_detail.service_id = service.id
                INNER JOIN t_tagihan_layanan_lain ON t_pembayaran_detail.tagihan_service_id = t_tagihan_layanan_lain.id
                INNER JOIN t_tagihan_layanan_lain_detail ON t_tagihan_layanan_lain.id = t_tagihan_layanan_lain_detail.t_layanan_lain_tagihan_id
                INNER JOIN customer ON customer.id = unit.pemilik_customer_id 
            WHERE 1=1 
                AND unit.project_id = '$project_id' 
                AND unit.pt_id = '$pt_id'
                $where_unit
                AND ISNULL(t_pembayaran.is_void, 0) = 0 
                AND t_pembayaran.cara_pembayaran_id = '$cara_pembayaran_id'
                AND t_pembayaran.jenis_cara_pembayaran_id = '$cara_pembayaran_jenis_id'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) >= '$periode_awal'
                AND CONVERT(DATE, t_pembayaran.tgl_bayar) <= '$periode_akhir'
        ";

        if ($jenis_service == '1') {
            $show_service = $sql_lingkungan;
        } elseif ($jenis_service == '2') {
            $show_service = $sql_air;
        } elseif ($jenis_service == '6') {
            $show_service = $sql_lainnya;
        } else {
            $show_service = $sql_lingkungan.' UNION '.$sql_air.' UNION '.$sql_lainnya;
        }

        $detail = $this->db->query($show_service);
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
        $jenis_service = $this->input->get("jenis_service");
        $cara_bayar    = $this->input->get("cara_bayar");
        $pt_id         = $this->input->get("pt_id");
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

        // style lebar kolom
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(17);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(13);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(17);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(17);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(17);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(17);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(17);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(22);
        $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(17);

        //Style Judul table
        $spreadsheet->getActiveSheet()->setCellValue('A1', "Penerimaan Harian")->mergeCells("A1:N1")
                    ->getStyle('A1:N1')->applyFromArray($styleBorder);
        $spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // SET judul table
        $spreadsheet->getActiveSheet()->setCellValue('A2', "Unit")->mergeCells("A2:A4")
                    ->getStyle('A2:A4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('B2', "Nama Pemilik")->mergeCells("B2:B4")
                    ->getStyle('B2:B4')->applyFromArray($styleBorder);

        $spreadsheet->getActiveSheet()->setCellValue('C2', "Tgl. Bayar")->mergeCells("C2:C4")
                    ->getStyle('C2:C4')->applyFromArray($styleBorder);
        $spreadsheet->getActiveSheet()->setCellValue('D2', "Jam Bayar")->mergeCells("D2:D4")
                    ->getStyle('D2:D4')->applyFromArray($styleBorder);

        $spreadsheet->getActiveSheet()->setCellValue('E2', "Service")->mergeCells("E2:E4")
                    ->getStyle('E2:E4')->applyFromArray($styleBorder);

        $spreadsheet->getActiveSheet()->setCellValue('F2', "Periode Penggunaan")->mergeCells("F2:F4")
                    ->getStyle('F2:F4')->applyFromArray($styleBorder);

        $spreadsheet->getActiveSheet()->setCellValue('G2', "Periode Penagihan")->mergeCells("G2:G4")
                    ->getStyle('G2:G4')->applyFromArray($styleBorder);

        $spreadsheet->getActiveSheet()->setCellValue('H2', "Nilai (Rp.)")->mergeCells("H2:O2")
                    ->getStyle('H2:O2')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('H3', "Pokok")->mergeCells("H3:H4")
                    ->getStyle('H3:H4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('I3', "PPN")->mergeCells("I3:I4")
                    ->getStyle('I3:I4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('J3', "Tagihan")
                    ->getStyle('J3')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('J4', "Pokok + PPN")
                    ->getStyle('J4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('K3', "Denda")->mergeCells("K3:K4")
                    ->getStyle('K3:K4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('L3', "Total")
                    ->getStyle('L3')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('L4', "Tagihan + Denda")
                    ->getStyle('L4')->applyFromArray($styleBorder)->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('M3', "Diskon")->mergeCells("M3:M4")
                    ->getStyle('M3:M4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('N3', "Pemutihan")->mergeCells("N3:N4")
                    ->getStyle('N3:N4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('O3', "Bayar")->mergeCells("O3:O4")
                    ->getStyle('O3:O4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('P2', "No Kwitansi")->mergeCells("P2:P4")
                    ->getStyle('P2:P4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('Q2', "Cetak Ke-")->mergeCells("Q2:Q4")
                    ->getStyle('Q2:Q4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValue('R2', "Virtual Account")->mergeCells("R2:R4")
                    ->getStyle('R2:R4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $head = $this->sql_header($kawasan, $blok, $periode_awal, $periode_akhir, $pt_id, $cara_bayar);
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
                    $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, $h->nama_pt)->mergeCells("A".$nomor.":O".$nomor)->getStyle("A".$nomor.":O".$nomor)->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }
                array_push($distinct_pt, $nama_pt);

                $explode = explode(',', $h->unit_id);
                $unit_id = '';
                foreach ($explode as $u) { $unit_id .= "'".$u."',"; }
                $unit_id = rtrim($unit_id, ',');

                $nomor++;
                $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, $h->cara_bayar)->mergeCells("A".$nomor.":O".$nomor)->getStyle("A".$nomor.":O".$nomor)->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $detail = $this->sql_detail($kawasan, $blok, $periode_awal, $periode_akhir, $unit_id, $jenis_service, $h->cara_pembayaran_id, $h->cara_pembayaran_jenis_id, $h->pt_id);
                $grand_total = 0;
                foreach (array_reverse($detail->result()) as $d) 
                {
                    $tagihan                  = ($d->tagihan ? $d->tagihan : 0);
                    $nilai_ppn                = ($d->nilai_ppn ? $d->nilai_ppn : 0);
                    $total_tagihan            = $tagihan + $nilai_ppn;
                    $denda                    = ($d->nilai_denda ? $d->nilai_denda : 0);
                    $total_tagihan_denda      = $total_tagihan + $denda;
                    $periode_penggunaan       = ( $d->code == 'Biaya Admin' ? '' : date('m/Y', strtotime($d->periode_penggunaan)) );
                    $periode_penagihan        = ( $d->code == 'Biaya Admin' ? '' : date('m/Y', strtotime($d->periode_penagihan)) );
                    $bayar                    = $d->code == 'Biaya Admin' ? $tagihan : ($d->bayar ? $d->bayar : 0);
                    $nilai_diskon             = ($d->nilai_diskon ? $d->nilai_diskon : 0);
                    $nilai_tagihan_pemutihan  = ($d->nilai_tagihan_pemutihan ? $d->nilai_tagihan_pemutihan : 0);

                    if (!empty($d->nilai_diskon)) {
                        $bayar = $bayar - $d->nilai_diskon;
                    }

                    if (!empty($tagihan))
                    {
                        $nomor++;
                        $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, $d->nama_unit)->getStyle('A'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('B'.$nomor, $d->name)->getStyle('B'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('C'.$nomor, $d->tgl_bayar)->getStyle('C'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('D'.$nomor, $d->jam_bayar)->getStyle('D'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('E'.$nomor, $d->code)->getStyle('E'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('F'.$nomor, $periode_penggunaan)->getStyle('F'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('G'.$nomor, $periode_penagihan)->getStyle('G'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('H'.$nomor, $tagihan, 0)->getStyle('H'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('I'.$nomor, ($d->code == 'Biaya Admin' ? '' : $nilai_ppn))
                                    ->getStyle('I'.$nomor)->applyFromArray($styleBorder)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('J'.$nomor, ($d->code == 'Biaya Admin' ? '' : $total_tagihan))
                                    ->getStyle('J'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
                        $spreadsheet->getActiveSheet()->setCellValue('K'.$nomor, ($d->code == 'Biaya Admin' ? '' : $denda))
                                    ->getStyle('K'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
                        $spreadsheet->getActiveSheet()->setCellValue('L'.$nomor, ($d->code == 'Biaya Admin' ? '' : $total_tagihan_denda))
                                    ->getStyle('L'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');

                        if(empty($nilai_diskon)){
                            $spreadsheet->getActiveSheet()->setCellValue('M'.$nomor, ($d->code == 'Biaya Admin' ? '' : $nilai_diskon))
                                    ->getStyle('M'.$nomor)->applyFromArray($styleBorder);
                        }else{
                            $spreadsheet->getActiveSheet()->setCellValue('M'.$nomor, ($d->code == 'Biaya Admin' ? '' : $nilai_diskon))
                                    ->getStyle('M'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
                        }

                        if(empty($nilai_tagihan_pemutihan)){
                            $spreadsheet->getActiveSheet()->setCellValue('N'.$nomor, ($d->code == 'Biaya Admin' ? '' : $nilai_tagihan_pemutihan))
                                    ->getStyle('N'.$nomor)->applyFromArray($styleBorder);
                        }else{
                            $spreadsheet->getActiveSheet()->setCellValue('N'.$nomor, ($d->code == 'Biaya Admin' ? '' : $nilai_tagihan_pemutihan))
                                    ->getStyle('N'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
                        }

                        $spreadsheet->getActiveSheet()->setCellValue('O'.$nomor, $bayar)->getStyle('O'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
                        $spreadsheet->getActiveSheet()->setCellValue('P'.$nomor, $d->no_kwitansi)->getStyle('P'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
                        $spreadsheet->getActiveSheet()->setCellValue('Q'.$nomor, $d->count_print_kwitansi)
                                    ->getStyle('Q'.$nomor)->applyFromArray($styleBorder)->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $spreadsheet->getActiveSheet()->setCellValue('R'.$nomor, $d->virtual_account)
                                    ->getStyle('R'.$nomor)->applyFromArray($styleBorder)->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $grand_total += $bayar;
                    }
                }

                $nomor++;
                $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, 'TOTAL BAYAR (Rp.)')->mergeCells("A".$nomor.":N".$nomor)->getStyle("A".$nomor.":N".$nomor)->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->setCellValue('O'.$nomor, $grand_total)->mergeCells("O".$nomor.":O".$nomor)->getStyle("O".$nomor.":O".$nomor)->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $nomor++;
        }
        $nomor+=2;
        $text = "Printed By: [".ucwords($this->m_core->user()->name)."] | Print Date: ".date("[d/m/Y] [H:i:s]");
        $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, $text)->mergeCells("A".$nomor.":S".$nomor)->getStyle("A".$nomor.":S".$nomor);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
    public function export_excel_summary()
    {
        $spreadsheet   = new Spreadsheet();
        $sheet         = $spreadsheet->getActiveSheet();
        $filename      = $GLOBALS['project']->code . '_Penerimaan_Harian_Summary_' . date('YmdHis');
        $project_id    = $GLOBALS['project']->id;
        $kawasan       = $this->input->get("id_kawasan");
        $blok          = $this->input->get("id_blok");
        $periode_awal  = $this->input->get("periode_awal");
        $periode_akhir = $this->input->get("periode_akhir");
        $jenis_service = $this->input->get("jenis_service");
        $cara_bayar = $this->input->get("cara_bayar");
        $pt_id         = $this->input->get("pt_id");
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

        // style lebar kolom
            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(40);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(13);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(17);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(12);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(17);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(32);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(17);
            $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(17);
            $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(17);
            $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(22);
            $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(17);

        //Style Judul table
            $spreadsheet->getActiveSheet()->setCellValue('A1', "Penerimaan Harian Summary")->mergeCells("A1:L1")
                        ->getStyle('A1:L1')->applyFromArray($styleBorder);
            $spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('A2', "Kode")->mergeCells("A2:A4")
                        ->getStyle('A2:A4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('B2', "Unit")->mergeCells("B2:B4")
                        ->getStyle('B2:B4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('C2', "Service")->mergeCells("C2:C4")
                        ->getStyle('C2:C4')->applyFromArray($styleBorder);

            $spreadsheet->getActiveSheet()->setCellValue('D2', "Nilai (Rp.)")->mergeCells("D2:L2")
                        ->getStyle('D2:L2')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('D3', "Pokok")->mergeCells("D3:D4")
                        ->getStyle('D3:D4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('E3', "PPN")->mergeCells("E3:E4")
                        ->getStyle('E3:E4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('F3', "Tagihan")
                        ->getStyle('F3')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('F4', "Pokok + PPN")
                        ->getStyle('F4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('G3', "Denda")->mergeCells("G3:G4")
                        ->getStyle('G3:G4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('H3', "Biaya Admin")->mergeCells("H3:H4")
                        ->getStyle('H3:H4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('I3', "Total")
                        ->getStyle('I3')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('I4', "Tagihan + Denda + Biaya Admin")
                        ->getStyle('I4')->applyFromArray($styleBorder)->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('J3', "Diskon")->mergeCells("J3:J4")
                        ->getStyle('J3:J4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('K3', "Pemutihan")->mergeCells("K3:K4")
                        ->getStyle('K3:K4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('L3', "Bayar")->mergeCells("L3:L4")
                        ->getStyle('L3:L4')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $head = $this->sql_header($kawasan, $blok, $periode_awal, $periode_akhir, $pt_id, $cara_bayar);
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
            $gt_pokok = 0;
            $gt_ppn = 0;
            $gt_denda = 0;
            $gt_biaya_admin = 0;
            $gt_diskon = 0;
            $gt_pemutihan = 0;
            $gt_bayar = 0;
            $kawasan_name_array = array();
            $kawasan_code_array = array();
            $nama_unit_array = array();
            $code_array = array();
            $tagihan_array = array();
            $nilai_ppn_array = array();
            $total_tagihan_array = array();
            $nilai_denda_array = array();
            $total_tagihan_denda_array = array();
            $nilai_diskon_array = array();
            $nilai_tagihan_pemutihan_array = array();
            $bayar_array = array();
            $biaya_admin_array = array();
            foreach ($join_unit_id as $h) 
            {
                $nama_pt = $h->nama_pt;
                if (! in_array($nama_pt, $distinct_pt))
                {
                    $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, $h->nama_pt)->mergeCells("A".$nomor.":K".$nomor)->getStyle("A".$nomor.":K".$nomor)->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }
                array_push($distinct_pt, $nama_pt);

                $explode = explode(',', $h->unit_id);
                $unit_id = '';
                foreach ($explode as $u) { $unit_id .= "'".$u."',"; }
                $unit_id = rtrim($unit_id, ',');

                // $nomor++;
                // $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, $h->cara_bayar)->mergeCells("A".$nomor.":K".$nomor)->getStyle("A".$nomor.":K".$nomor)->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $detail = $this->sql_detail_new($kawasan, $blok, $periode_awal, $periode_akhir, $unit_id, $jenis_service, $h->cara_pembayaran_id, $h->cara_pembayaran_jenis_id, $h->pt_id);
                $grand_total = 0;
                foreach (array_reverse($detail->result()) as $d) 
                {
                    $tagihan                  = ($d->tagihan ? $d->tagihan : 0);
                    $nilai_ppn                = ($d->nilai_ppn ? $d->nilai_ppn : 0);
                    $total_tagihan            = $tagihan + $nilai_ppn;
                    $denda                    = ($d->nilai_denda ? $d->nilai_denda : 0);
                    $biaya_admin              = ($d->biaya_admin ? $d->biaya_admin : 0);
                    $total_tagihan_denda      = $total_tagihan + $denda + $biaya_admin;
                    $bayar                    = ($d->bayar ? $d->bayar : 0) + $biaya_admin;
                    $nilai_diskon             = ($d->nilai_diskon ? $d->nilai_diskon : 0);
                    $nilai_tagihan_pemutihan  = ($d->nilai_tagihan_pemutihan ? $d->nilai_tagihan_pemutihan : 0);

                    if (!empty($nilai_diskon)) {
                        $bayar = $bayar - $nilai_diskon;
                    }

                    if (!empty($tagihan))
                    {
                        if (!in_array($d->kawasan_name.'|'.$d->code, $kawasan_name_array))
                        {
                            array_push($kawasan_name_array, $d->kawasan_name.'|'.$d->code);
                            array_push($kawasan_code_array, $d->kawasan_code);

                            array_push($nama_unit_array, $d->kawasan_name);

                            array_push($code_array, $d->code);

                            array_push($tagihan_array, $tagihan);
                            array_push($nilai_ppn_array, $nilai_ppn);
                            array_push($total_tagihan_array, $total_tagihan);
                            array_push($nilai_denda_array, $denda);
                            array_push($biaya_admin_array, $biaya_admin);
                            array_push($total_tagihan_denda_array, $total_tagihan_denda);
                            array_push($nilai_diskon_array, $nilai_diskon);
                            array_push($nilai_tagihan_pemutihan_array, $nilai_tagihan_pemutihan);

                            $gt_pokok += $tagihan;
                            $gt_ppn += $nilai_ppn;
                            $gt_denda += $denda;
                            $gt_biaya_admin += $biaya_admin; 
                            $gt_diskon += $nilai_diskon;
                            $gt_pemutihan += $nilai_tagihan_pemutihan;

                            array_push($bayar_array, $bayar);

                            $gt_bayar += $bayar;
                        }
                        else
                        {
                            $index_array = array_search($d->kawasan_name.'|'.$d->code, $kawasan_name_array);

                            // $code_array[$index_array] .= ','.$d->code;

                            $tagihan_array[$index_array] += $tagihan;
                            $nilai_ppn_array[$index_array] += $nilai_ppn;
                            $total_tagihan_array[$index_array] += $total_tagihan;
                            $nilai_denda_array[$index_array] += $denda;
                            $biaya_admin_array[$index_array] += $biaya_admin;
                            $total_tagihan_denda_array[$index_array] += $total_tagihan_denda;
                            $nilai_diskon_array[$index_array] += $nilai_diskon;
                            $nilai_tagihan_pemutihan_array[$index_array] += $nilai_tagihan_pemutihan;

                            $gt_pokok += $tagihan;
                            $gt_ppn += $nilai_ppn;
                            $gt_denda += $denda;
                            $gt_biaya_admin += $biaya_admin; 
                            $gt_diskon += $nilai_diskon;
                            $gt_pemutihan += $nilai_tagihan_pemutihan;

                            $bayar_array[$index_array] += $bayar;

                            $gt_bayar += $bayar;
                        }
                        $grand_total += $bayar;
                    }
                }

                // $nomor++;
                // $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, 'TOTAL BAYAR (Rp.)')->mergeCells("A".$nomor.":J".$nomor)
                //             ->getStyle("A".$nomor.":J".$nomor)->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                // $spreadsheet->getActiveSheet()->setCellValue('K'.$nomor, $grand_total)->mergeCells("K".$nomor.":K".$nomor)
                //             ->getStyle("K".$nomor.":K".$nomor)->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $nomor++;
        }
        for ($i=0; $i < count($kawasan_name_array); $i++)
        { 
            $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, $kawasan_code_array[$i])
                        ->getStyle('A'.$nomor)->applyFromArray($styleBorder);

            $spreadsheet->getActiveSheet()->setCellValue('B'.$nomor, $nama_unit_array[$i])
                        ->getStyle('B'.$nomor)->applyFromArray($styleBorder);

            $code_array[$i] = ((empty($code_array[$i]) ? '-' : implode(', ', array_values(array_unique(array_values(array_filter(explode(',', $code_array[$i]))))))));
            $spreadsheet->getActiveSheet()->setCellValue('C'.$nomor, $code_array[$i])
                        ->getStyle('C'.$nomor)->applyFromArray($styleBorder);

            if(empty($tagihan_array[$i])){
                $spreadsheet->getActiveSheet()->setCellValue('D'.$nomor, $tagihan_array[$i], 0)
                            ->getStyle('D'.$nomor)->applyFromArray($styleBorder);
            }else{
                $spreadsheet->getActiveSheet()->setCellValue('D'.$nomor, $tagihan_array[$i], 0)
                            ->getStyle('D'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
            }
            if(empty($nilai_ppn_array[$i])){
                $spreadsheet->getActiveSheet()->setCellValue('E'.$nomor, $nilai_ppn_array[$i], 0)
                            ->getStyle('E'.$nomor)->applyFromArray($styleBorder);
            }else{
                $spreadsheet->getActiveSheet()->setCellValue('E'.$nomor, $nilai_ppn_array[$i], 0)
                            ->getStyle('E'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
            }
            if(empty($total_tagihan_array[$i])){
                $spreadsheet->getActiveSheet()->setCellValue('F'.$nomor, $total_tagihan_array[$i], 0)
                            ->getStyle('F'.$nomor)->applyFromArray($styleBorder);
            }else{
                $spreadsheet->getActiveSheet()->setCellValue('F'.$nomor, $total_tagihan_array[$i], 0)
                            ->getStyle('F'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
            }
            if(empty($nilai_denda_array[$i])){
                $spreadsheet->getActiveSheet()->setCellValue('G'.$nomor, $nilai_denda_array[$i], 0)
                            ->getStyle('G'.$nomor)->applyFromArray($styleBorder);
            }else{
                $spreadsheet->getActiveSheet()->setCellValue('G'.$nomor, $nilai_denda_array[$i], 0)
                            ->getStyle('G'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
            }
            if(empty($biaya_admin_array[$i])){
                $spreadsheet->getActiveSheet()->setCellValue('H'.$nomor, $biaya_admin_array[$i], 0)
                            ->getStyle('H'.$nomor)->applyFromArray($styleBorder);
            }else{
                $spreadsheet->getActiveSheet()->setCellValue('H'.$nomor, $biaya_admin_array[$i], 0)
                            ->getStyle('H'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
            }
            if(empty($total_tagihan_denda_array[$i])){
                $spreadsheet->getActiveSheet()->setCellValue('I'.$nomor, $total_tagihan_denda_array[$i], 0)
                            ->getStyle('I'.$nomor)->applyFromArray($styleBorder);
            }else{
                $spreadsheet->getActiveSheet()->setCellValue('I'.$nomor, $total_tagihan_denda_array[$i], 0)
                            ->getStyle('I'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
            }
            if(empty($nilai_diskon_array[$i])){
                $spreadsheet->getActiveSheet()->setCellValue('J'.$nomor, $nilai_diskon_array[$i], 0)
                            ->getStyle('J'.$nomor)->applyFromArray($styleBorder);
            }else{
                $spreadsheet->getActiveSheet()->setCellValue('J'.$nomor, $nilai_diskon_array[$i], 0)
                            ->getStyle('J'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
            }
            if(empty($nilai_tagihan_pemutihan_array[$i])){
                $spreadsheet->getActiveSheet()->setCellValue('K'.$nomor, $nilai_tagihan_pemutihan_array[$i], 0)
                            ->getStyle('K'.$nomor)->applyFromArray($styleBorder);
            }else{
                $spreadsheet->getActiveSheet()->setCellValue('K'.$nomor, $nilai_tagihan_pemutihan_array[$i], 0)
                            ->getStyle('K'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
            }
            if(empty($bayar_array[$i])){
                $spreadsheet->getActiveSheet()->setCellValue('L'.$nomor, $bayar_array[$i], 0)
                            ->getStyle('L'.$nomor)->applyFromArray($styleBorder);
            }else{
                $spreadsheet->getActiveSheet()->setCellValue('L'.$nomor, $bayar_array[$i], 0)
                            ->getStyle('L'.$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
            }
            $nomor++;
        }

        $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, 'Grand Total (Rp.)')->mergeCells("A".$nomor.":C".$nomor)
                    ->getStyle("A".$nomor.":C".$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
        if (($gt_pokok)>0) {
            $spreadsheet->getActiveSheet()->setCellValue('D'.$nomor, $gt_pokok)->mergeCells("D".$nomor.":D".$nomor)
                        ->getStyle("D".$nomor.":D".$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
        } else {
            $spreadsheet->getActiveSheet()->setCellValue('D'.$nomor, $gt_pokok)->mergeCells("D".$nomor.":D".$nomor)
                        ->getStyle("D".$nomor.":D".$nomor)->applyFromArray($styleBorder);
        }
        if (($gt_ppn)>0) {
            $spreadsheet->getActiveSheet()->setCellValue('E'.$nomor, $gt_ppn)->mergeCells("E".$nomor.":E".$nomor)
                        ->getStyle("E".$nomor.":E".$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
        } else {
            $spreadsheet->getActiveSheet()->setCellValue('E'.$nomor, $gt_ppn)->mergeCells("E".$nomor.":E".$nomor)
                        ->getStyle("E".$nomor.":E".$nomor)->applyFromArray($styleBorder);
        }
        if (($gt_pokok + $gt_ppn)>0) {
            $spreadsheet->getActiveSheet()->setCellValue('F'.$nomor, $gt_pokok + $gt_ppn)->mergeCells("F".$nomor.":F".$nomor)
                        ->getStyle("F".$nomor.":F".$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
        } else {
            $spreadsheet->getActiveSheet()->setCellValue('F'.$nomor, $gt_pokok + $gt_ppn)->mergeCells("F".$nomor.":F".$nomor)
                        ->getStyle("F".$nomor.":F".$nomor)->applyFromArray($styleBorder);
        }
        if (($gt_denda)>0) {
            $spreadsheet->getActiveSheet()->setCellValue('G'.$nomor, $gt_denda)->mergeCells("G".$nomor.":G".$nomor)
                        ->getStyle("G".$nomor.":G".$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
        } else {
            $spreadsheet->getActiveSheet()->setCellValue('G'.$nomor, $gt_denda)->mergeCells("G".$nomor.":G".$nomor)
                        ->getStyle("G".$nomor.":G".$nomor)->applyFromArray($styleBorder);
        }
        if (($gt_biaya_admin)>0) {
            $spreadsheet->getActiveSheet()->setCellValue('H'.$nomor, $gt_biaya_admin)->mergeCells("H".$nomor.":H".$nomor)
                        ->getStyle("H".$nomor.":H".$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
        } else {
            $spreadsheet->getActiveSheet()->setCellValue('H'.$nomor, $gt_biaya_admin)->mergeCells("H".$nomor.":H".$nomor)
                        ->getStyle("H".$nomor.":H".$nomor)->applyFromArray($styleBorder);
        }
        if (($gt_pokok + $gt_ppn + $gt_denda + $gt_biaya_admin)>0) {
            $spreadsheet->getActiveSheet()->setCellValue('I'.$nomor, $gt_pokok + $gt_ppn + $gt_denda + $gt_biaya_admin)->mergeCells("I".$nomor.":I".$nomor)
                        ->getStyle("I".$nomor.":I".$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
        } else {
            $spreadsheet->getActiveSheet()->setCellValue('I'.$nomor, $gt_pokok + $gt_ppn + $gt_denda + $gt_biaya_admin)->mergeCells("I".$nomor.":I".$nomor)
                        ->getStyle("I".$nomor.":I".$nomor)->applyFromArray($styleBorder);
        }
        if (($gt_diskon)>0) {
            $spreadsheet->getActiveSheet()->setCellValue('J'.$nomor, $gt_diskon)->mergeCells("J".$nomor.":J".$nomor)
                        ->getStyle("J".$nomor.":J".$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
        } else {
            $spreadsheet->getActiveSheet()->setCellValue('J'.$nomor, $gt_diskon)->mergeCells("J".$nomor.":J".$nomor)
                        ->getStyle("J".$nomor.":J".$nomor)->applyFromArray($styleBorder);
        }
        if (($gt_pemutihan)>0) {
            $spreadsheet->getActiveSheet()->setCellValue('K'.$nomor, $gt_pemutihan)->mergeCells("K".$nomor.":K".$nomor)
                        ->getStyle("K".$nomor.":K".$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
        } else {
            $spreadsheet->getActiveSheet()->setCellValue('K'.$nomor, $gt_pemutihan)->mergeCells("K".$nomor.":K".$nomor)
                        ->getStyle("K".$nomor.":K".$nomor)->applyFromArray($styleBorder);
        }
        if (($gt_bayar)>0) {
            $spreadsheet->getActiveSheet()->setCellValue('L'.$nomor, $gt_bayar)->mergeCells("L".$nomor.":L".$nomor)
                        ->getStyle("L".$nomor.":L".$nomor)->applyFromArray($styleBorder)->getNumberFormat()->setFormatCode('#,##');
        } else {
            $spreadsheet->getActiveSheet()->setCellValue('L'.$nomor, $gt_bayar)->mergeCells("L".$nomor.":L".$nomor)
                        ->getStyle("L".$nomor.":L".$nomor)->applyFromArray($styleBorder);
        }
        $nomor+=2;
        $text = "Printed By: [".ucwords($this->m_core->user()->name)."] | Print Date: ".date("[d/m/Y] [H:i:s]");
        $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, $text)->mergeCells("A".$nomor.":L".$nomor)->getStyle("A".$nomor.":L".$nomor);

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