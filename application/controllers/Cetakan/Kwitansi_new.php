<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kwitansi_new extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('m_login');
        if (!$this->m_login->status_login()) {
            redirect(site_url());
        }
        $this->load->model('Cetakan/m_konfirmasi_tagihan');
        $this->load->model('Setting/m_parameter_project');
        $this->load->model('m_core');
        global $jabatan;
        $jabatan = $this->m_core->jabatan();
        global $project;
        $project = $this->m_core->project();
        global $menu;
        $menu = $this->m_core->menu();
    }

    //tambah cetak ke-
    public function kwitansi_request()
    {
        $description   = $this->input->post('description');
        $pembayaran_id = $this->input->post('pembayaran_id');
        $code_service  = $this->input->post('code_service');
        $variables     = '?pembayaran_id=' . $pembayaran_id;
        $variables    .= '&service=' . $code_service;

        $update_count = $this->db
            ->set('count_print_kwitansi', 'count_print_kwitansi+1', FALSE)
            ->where('id', $this->input->post('pembayaran_id'))
            ->update('t_pembayaran');
        if ($update_count) {
            $data = [
                't_pembayaran_id' => $pembayaran_id,
                'description' => $description,
                'created_by'  => $this->session->userdata('name'),
                'created_at'  => date('Y-m-d H:i:s')
            ];
            $this->db->insert('log_kwitansi', $data);
        }

        echo json_encode(array(
            'status' => 1,
            'link_print' => site_url("cetakan/Kwitansi_new/all/$pembayaran_id")
        ));
    }
    
    public function all($pembayaran_id)
    {
        $pembayaran = $this->db->from("t_pembayaran")->where('id', $pembayaran_id)->get()->row();
        $grand_total = $this->db->select('sum(bayar+bayar_deposit) as grand_total')->from("t_pembayaran_detail")->where('t_pembayaran_id', $pembayaran_id)->get()->row()->grand_total;

        //uid
        $unit = $this->db->select("
                CONCAT(project.source_id,kawasan.code,blok.code,'/',unit.no_unit) as uid,
                m_pt.name as pt_name,
                customer.name as pemilik,
                kawasan.name as kawasan,
                blok.name as blok,
                unit.no_unit,
                unit.pemilik_customer_id,
                unit_air.no_seri_meter as no_meter,
            ")
            ->from('unit')
            ->join('blok', 'blok.id = unit.blok_id')
            ->join('kawasan', 'kawasan.id = blok.kawasan_id')
            ->join('project', 'project.id = unit.project_id')
            ->join("dbmaster.dbo.m_pt", "m_pt.pt_id = unit.pt_id", "LEFT")
            ->join('customer', 'customer.id = unit.pemilik_customer_id')
            ->join("unit_air", "unit_air.unit_id = unit.id", "LEFT")
            ->where('unit.id', $pembayaran->unit_id)
            ->get();

        if ($unit->num_rows() > 0) {
            $unit = $unit->row();
        } else {
            $unit = $this->db->query("
                SELECT
                    IIF(kawasan.code IS NULL, unit_virtual.unit, CONCAT( project.source_id, kawasan.code, blok.code, '/', unit.no_unit )) AS uid,
                    m_pt.name AS pt_name,
                    customer.name AS pemilik,
                    ISNULL(kawasan.name, unit_virtual.alamat) AS kawasan,
                    blok.name AS blok,
                    unit.no_unit,
                    unit.pemilik_customer_id,
                    '0' AS no_meter
                FROM
                    unit_virtual
                    INNER JOIN project ON project.id = unit_virtual.project_id
                    INNER JOIN customer ON customer.id = unit_virtual.customer_id
                    LEFT JOIN unit ON unit.pemilik_customer_id = customer.id
                    LEFT JOIN blok ON blok.id = unit.blok_id
                    LEFT JOIN kawasan ON kawasan.id = blok.kawasan_id
                    LEFT JOIN dbmaster.dbo.m_pt ON m_pt.pt_id = customer.pt_id
                WHERE
                    unit_virtual.id = '".$pembayaran->unit_id."'
            ")->row();
        }

        //lingkungan
        $pembayaran_detail_ipl = (object)[];

        $pembayaran_detail_ipl->raw = $this->db
            ->select('
                t_tagihan_lingkungan.id,
                t_tagihan_lingkungan.periode,
                t_pembayaran_detail.is_tunggakan,
                t_pembayaran_detail.nilai_diskon,
                CASE t_pembayaran_detail.is_tunggakan
                    WHEN 1 THEN (
                        SELECT t_pembayaran_detail_tunggakan.sisa_tagihan
                        FROM t_pembayaran_detail AS t_pembayaran_detail_tunggakan
                        WHERE t_pembayaran_detail.tagihan_service_id = t_pembayaran_detail_tunggakan.tagihan_service_id
                            AND t_pembayaran_detail.service_id = t_pembayaran_detail_tunggakan.service_id
                        ORDER BY t_pembayaran_detail_tunggakan.id DESC
                        OFFSET 1 ROWS
                        FETCH NEXT 1 ROWS ONLY
                    )
                    ELSE 0
                END AS nilai_tunggakan,
                ISNULL(t_pembayaran_detail.nilai_terbayar, 0) AS nilai_terbayar,
                t_pembayaran_detail.bayar,
                t_pembayaran_detail.bayar_deposit,
                ( t_tagihan_lingkungan_detail.nilai_bangunan 
                    + t_tagihan_lingkungan_detail.nilai_kavling 
                    + t_tagihan_lingkungan_detail.nilai_administrasi 
                    + t_tagihan_lingkungan_detail.nilai_keamanan 
                    + t_tagihan_lingkungan_detail.nilai_kebersihan 
                ) AS nilai_pokok,
                ISNULL( t_tagihan_lingkungan_detail.nilai_ppn, 0 ) AS nilai_ppn,
                ISNULL( t_tagihan_lingkungan_detail.ppn_flag, 0 ) AS ppn_flag,
                t_pembayaran_detail.nilai_denda AS nilai_denda_ipl 
            ')
            ->from("t_pembayaran_detail")
            ->join(
                't_tagihan_lingkungan',
                't_tagihan_lingkungan.id = t_pembayaran_detail.tagihan_service_id'
            )
            ->join(
                't_tagihan_lingkungan_detail',
                't_tagihan_lingkungan_detail.t_tagihan_lingkungan_id = t_tagihan_lingkungan.id'
            )
            ->join(
                'service',
                'service.id = t_pembayaran_detail.service_id AND service.delete = 0 AND service.active = 1'
            )
            ->where('service.service_jenis_id', 1)
            ->where('t_pembayaran_detail.t_pembayaran_id', $pembayaran_id)
            ->order_by('t_tagihan_lingkungan.periode')
            ->get()->result();

        $tmp = (object)['total_pokok' => 0, 'total_ppn' => 0, 'total_tagihan' => 0, 'total_denda' => 0, 'total_diskon' => 0, 'total_terbayar_sebelum' => 0, 'total_terbayar_saat_ini' => 0];
        foreach ($pembayaran_detail_ipl->raw as $ind => $el) {
            if ($el->is_tunggakan) {
                $tmp->total_pokok += ($el->nilai_tunggakan / ( 1 + ($el->ppn_flag * ($el->nilai_ppn/100))));
                $tmp->total_tagihan += $el->nilai_tunggakan;
            } else {
                $tmp->total_pokok += $el->nilai_pokok;
                $tmp->total_tagihan += round($el->nilai_pokok + (($el->nilai_pokok * $el->nilai_ppn / 100) * $el->ppn_flag));
            }
            $tmp->total_denda += $el->nilai_denda_ipl;
            $tmp->total_diskon += $el->nilai_diskon;
            $tmp->total_terbayar_sebelum += $el->nilai_terbayar;
            $tmp->total_terbayar_saat_ini += $el->bayar + $el->bayar_deposit;
        }
        $tmp->total_ppn = $tmp->total_tagihan - $tmp->total_pokok;
        $pembayaran_detail_ipl->summary = $tmp;
        //air
        $pembayaran_detail_air = (object)[];
        $pembayaran_detail_air->raw = $this->db
            ->select('
                t_tagihan_air.id,
                t_tagihan_air.periode,
                t_pembayaran_detail.is_tunggakan,
                t_pembayaran_detail.nilai_diskon,
                CASE t_pembayaran_detail.is_tunggakan
                        WHEN 1 THEN (
                                SELECT t_pembayaran_detail_tunggakan.sisa_tagihan
                                FROM t_pembayaran_detail AS t_pembayaran_detail_tunggakan
                                WHERE t_pembayaran_detail.tagihan_service_id = t_pembayaran_detail_tunggakan.tagihan_service_id
                                        AND t_pembayaran_detail.service_id = t_pembayaran_detail_tunggakan.service_id
                                ORDER BY t_pembayaran_detail_tunggakan.id DESC
                                OFFSET 1 ROWS
                                FETCH NEXT 1 ROWS ONLY
                        )
                        ELSE 0
                END AS nilai_tunggakan,
                ISNULL(t_pembayaran_detail.nilai_terbayar, 0) AS nilai_terbayar,
                t_pembayaran_detail.bayar,
                t_pembayaran_detail.bayar_deposit,
                ( t_tagihan_air_detail.nilai 
                        + t_tagihan_air_detail.nilai_administrasi 
                        + t_tagihan_air_detail.nilai_pemeliharaan
                ) AS nilai_pokok,
                ISNULL( t_tagihan_air_detail.nilai_ppn, 0 ) AS nilai_ppn,
                ISNULL( t_tagihan_air_detail.ppn_flag, 0 ) AS ppn_flag,
                t_pembayaran_detail.nilai_denda AS nilai_denda_air,
                t_pencatatan_meter_air.meter_awal,
                t_pencatatan_meter_air.meter_akhir
            ')
            ->from("t_pembayaran_detail")
            ->join(
                't_tagihan_air',
                't_tagihan_air.id = t_pembayaran_detail.tagihan_service_id'
            )
            ->join(
                't_tagihan_air_detail',
                't_tagihan_air_detail.t_tagihan_air_id = t_tagihan_air.id'
            )
            ->join(
                'service',
                'service.id = t_pembayaran_detail.service_id AND service.delete = 0 AND service.active = 1'
            )
            ->where('service.service_jenis_id', 2)
            ->where('t_pembayaran_detail.t_pembayaran_id', $pembayaran_id)
            ->join(
                "t_pencatatan_meter_air",
                "t_pencatatan_meter_air.periode = t_tagihan_air.periode 
                    AND t_pencatatan_meter_air.unit_id = t_tagihan_air.unit_id"
            )
            ->order_by('t_tagihan_air.periode')
            ->get()->result();


        $tmp = (object)['total_pokok' => 0, 'total_ppn' => 0, 'total_tagihan' => 0, 'total_denda' => 0, 'total_diskon' => 0, 'total_terbayar_sebelum' => 0, 'total_terbayar_saat_ini' => 0];
        foreach ($pembayaran_detail_air->raw as $ind => $el) {
            if ($el->is_tunggakan) {
                $tmp->total_pokok += ($el->nilai_tunggakan / ( 1 + ($el->ppn_flag * ($el->nilai_ppn/100))));
                $tmp->total_tagihan += $el->nilai_tunggakan;
            } else {
                $tmp->total_pokok += $el->nilai_pokok;
                $tmp->total_tagihan += round($el->nilai_pokok + (($el->nilai_pokok * $el->nilai_ppn / 100) * $el->ppn_flag));
            }
            $tmp->total_denda += $el->nilai_denda_air;
            $tmp->total_diskon += $el->nilai_diskon;
            $tmp->total_terbayar_sebelum += $el->nilai_terbayar;
            $tmp->total_terbayar_saat_ini += $el->bayar;
        }
        $tmp->total_ppn = $tmp->total_tagihan - $tmp->total_pokok;
        $pembayaran_detail_air->summary = $tmp;
        // layanan lain
        // $pembayaran_detail_ll = $this->db
        //     ->from("t_pembayaran_detail")
        //     ->join( 't_tagihan_layanan_lain', 't_tagihan_layanan_lain.id = t_pembayaran_detail.tagihan_service_id')
        //     ->where('service_jenis_id', 6)
        //     ->where('t_pembayaran_id', $pembayaran_id)
        //     // ->order_by('t_tagihan_lingkungan.periode')
        //     ->get()
        //     ->result();

        $pembayaran_detail_ll = $this->db
            ->select('
                t_pembayaran_detail.id,
                t_pembayaran_detail.nilai_tagihan,
                t_pembayaran_detail.nilai_denda,
                t_pembayaran_detail.nilai_diskon,
                CONVERT ( INT, ROUND( ( t_pembayaran_detail.nilai_tagihan ) * ( t_pembayaran_detail.nilai_ppn / 100.0 ), 0 ) ) AS nilai_ppn_lainnya,
                service.name,
                t_layanan_lain_registrasi_detail.periode_awal,
                t_layanan_lain_registrasi_detail.periode_akhir 
            ')
            ->from("t_pembayaran_detail")
            ->join(
                't_tagihan_layanan_lain',
                't_tagihan_layanan_lain.id = t_pembayaran_detail.tagihan_service_id'
            )
            ->join(
                't_layanan_lain_registrasi_detail',
                't_tagihan_layanan_lain.t_layanan_lain_registrasi_id = t_layanan_lain_registrasi_detail.t_layanan_lain_registrasi_id'
            )
            ->join(
                'service',
                'service.id = t_pembayaran_detail.service_id AND service.active = 1'
            )
            ->where('service.service_jenis_id', 6)
            ->where('t_pembayaran_detail.t_pembayaran_id', $pembayaran_id)
            ->get()->result();

        $user = $this->m_parameter_project->get($GLOBALS['project']->id, "pj_kwitansi");
        $user = str_replace("{{user_login}}", $this->session->userdata('name'), $user);
        $user = $user == "" ? $this->session->userdata('name') : $user;
        $city = $this->db->from('project')->where('id', $GLOBALS['project']->id)->get()->row()->city;

        $this->load->library('pdf');

        $sql_saldo_deposit = $this->db
            ->select('
                SUM(t_deposit_detail.nilai) AS nilai
            ')
            ->from("t_pembayaran")
            ->join(
                'unit',
                't_pembayaran.unit_id = unit.id'
            )
            ->join(
                't_deposit',
                'unit.pemilik_customer_id = t_deposit.customer_id'
            )
            ->join(
                't_deposit_detail',
                't_deposit_detail.t_deposit_id = t_deposit.id'
            )
            ->where('t_pembayaran.id', $pembayaran_id)
            ->where('t_deposit.customer_id', $unit->pemilik_customer_id)
            ->where('t_deposit.project_id', $GLOBALS['project']->id)
            ->get();
        if ($sql_saldo_deposit->num_rows() > 0) {
            $sisa_deposit = number_format($sql_saldo_deposit->row()->nilai, 0, ",", ".");
        } else {
            $sisa_deposit = 0;
        }

        $pemakaian_deposit = $this->db
            ->select('
                SUM(t_pembayaran_detail.bayar_deposit) AS nilai
            ')
            ->from("t_pembayaran_detail")
            ->where('t_pembayaran_detail.t_pembayaran_id', $pembayaran_id)
            ->get();
        if ($pemakaian_deposit->num_rows() > 0) {
            $pemakaian_deposit = number_format($pemakaian_deposit->row()->nilai, 0, ",", ".");
        } else {
            $pemakaian_deposit = 0;
        }

        if ($pembayaran_detail_air->summary->total_diskon > 0 OR $pembayaran_detail_ipl->summary->total_diskon > 0)  {
            $grand_total = ($grand_total - $pembayaran_detail_air->summary->total_diskon) - $pembayaran_detail_ipl->summary->total_diskon;
        }
        $grand_total = $grand_total + @$pembayaran->nilai_biaya_admin_cara_pembayaran;

        $this->pdf->load_view(
            "proyek/cetakan/kwitansi_global",
            [
                "pembayaran"         => $pembayaran,
                "pembayaran_lingkungan_periode_awal"  => isset($pembayaran_detail_ipl->raw[0]) ? $pembayaran_detail_ipl->raw[0]->periode : null,
                "pembayaran_lingkungan_periode_akhir" => isset($pembayaran_detail_ipl->raw[0]) ? end($pembayaran_detail_ipl->raw)->periode : null,
                "pembayaran_air_periode_awal"  => isset($pembayaran_detail_air->raw[0]) ? $pembayaran_detail_air->raw[0]->periode : null,
                "pembayaran_air_periode_akhir" => isset($pembayaran_detail_air->raw[0]) ? end($pembayaran_detail_air->raw)->periode : null,
                "no_kwitansi"           => $pembayaran->no_kwitansi,
                "project"               => $GLOBALS['project'],
                "unit"                  => $unit,
                "meter"                 => isset($pembayaran_detail_air->raw[0]) ? (object)['meter_awal' => $pembayaran_detail_air->raw[0]->meter_awal, 'meter_akhir' => end($pembayaran_detail_air->raw)->meter_akhir] : null,
                "pembayaran_lingkungan" => $pembayaran_detail_ipl->summary,
                "pembayaran_air"        => $pembayaran_detail_air->summary,
                "grand_total"           => $grand_total,
                "terbilang"             => strtoupper($this->terbilang($grand_total)),
                "pemakaian_deposit"     => $pemakaian_deposit,
                "sisa_deposit"          => $sisa_deposit,
                "date"                  => date("Y-m-d"),
                "user"                  => $user,
                "city"                  => $city,
                "pembayaran_ll"         => $pembayaran_detail_ll,
                "tgl_bayar"             => $pembayaran->tgl_bayar
            ]
        );
    }

    public function all_test($pembayaran_id)
    {
        $pembayaran = $this->db->from("t_pembayaran")->where('id', $pembayaran_id)->get()->row();
        $grand_total = $this->db->select('sum(bayar) as grand_total')->from("t_pembayaran_detail")->where('t_pembayaran_id', $pembayaran_id)->get()->row()->grand_total;

        //uid
        $unit = $this->db->select("
                            CONCAT(project.source_id,kawasan.code,blok.code,'/',unit.no_unit) as uid,
                            m_pt.name as pt_name,
                            customer.name as pemilik,
                            kawasan.name as kawasan,
                            blok.name as blok,
                            unit.no_unit,
                            unit_air.no_seri_meter as no_meter,
                        ")
            ->from('unit')
            ->join('blok', 'blok.id = unit.blok_id')
            ->join('kawasan', 'kawasan.id = blok.kawasan_id')
            ->join('project', 'project.id = unit.project_id')
            ->join("dbmaster.dbo.m_pt", "m_pt.pt_id = unit.pt_id")
            ->join('customer', 'customer.id = unit.pemilik_customer_id')
            ->join("unit_air", "unit_air.unit_id = unit.id", "LEFT")
            ->where('unit.id', $pembayaran->unit_id)
            ->get()->row();
        //lingkungan
        $pembayaran_detail_ipl = (object)[];

        $pembayaran_detail_ipl->raw = $this->db
            ->select('
                    *, 
                    (t_tagihan_lingkungan_detail.nilai_bangunan 
                    + t_tagihan_lingkungan_detail.nilai_kavling 
                    + t_tagihan_lingkungan_detail.nilai_administrasi 
                    + t_tagihan_lingkungan_detail.nilai_keamanan 
                    + t_tagihan_lingkungan_detail.nilai_kebersihan) as nilai_pokok,
                    isnull(t_tagihan_lingkungan_detail.nilai_ppn,0) as nilai_ppn,
                    isnull(t_tagihan_lingkungan_detail.ppn_flag,0) as ppn_flag')
            ->from("t_pembayaran_detail")
            ->join(
                't_tagihan_lingkungan',
                't_tagihan_lingkungan.id = t_pembayaran_detail.tagihan_service_id'
            )
            ->join(
                't_tagihan_lingkungan_detail',
                't_tagihan_lingkungan_detail.t_tagihan_lingkungan_id = t_tagihan_lingkungan.id'
            )
            ->join(
                'service',
                'service.id = t_pembayaran_detail.service_id AND service.delete = 0 AND service.active = 1'
            )
            ->where('service.service_jenis_id', 1)
            ->where('t_pembayaran_detail.t_pembayaran_id', $pembayaran_id)
            ->order_by('t_tagihan_lingkungan.periode')
            ->get()->result();

        $tmp = (object)['total_pokok' => 0, 'total_ppn' => 0, 'total_tagihan' => 0, 'total_denda' => 0, 'total_diskon' => 0, 'total_terbayar_sebelum' => 0, 'total_terbayar_saat_ini' => 0];
        foreach ($pembayaran_detail_ipl->raw as $ind => $el) {
            $tmp->total_pokok += $el->nilai_pokok;
            $tmp->total_ppn += $el->nilai_ppn;
            $tmp->total_tagihan += round($el->nilai_pokok + (($el->nilai_pokok * $el->nilai_ppn / 100) * $el->ppn_flag));
            $tmp->total_denda += $el->nilai_denda;
            $tmp->total_diskon += $el->nilai_diskon;
            $tmp->total_terbayar_sebelum += $el->nilai_terbayar;
            $tmp->total_terbayar_saat_ini += $el->bayar;
        }
        $pembayaran_detail_ipl->summary = $tmp;
        //air
        $pembayaran_detail_air = (object)[];
        $pembayaran_detail_air->raw = $this->db
            ->select('
                *, 
                (t_tagihan_air_detail.nilai 
                + t_tagihan_air_detail.nilai_administrasi 
                + t_tagihan_air_detail.nilai_pemeliharaan) as nilai_pokok,
                isnull(t_tagihan_air_detail.nilai_ppn,0) as nilai_ppn,
                isnull(t_tagihan_air_detail.ppn_flag,0) as ppn_flag')
            ->from("t_pembayaran_detail")
            ->join(
                't_tagihan_air',
                't_tagihan_air.id = t_pembayaran_detail.tagihan_service_id'
            )
            ->join(
                't_tagihan_air_detail',
                't_tagihan_air_detail.t_tagihan_air_id = t_tagihan_air.id'
            )
            ->join(
                'service',
                'service.id = t_pembayaran_detail.service_id AND service.delete = 0 AND service.active = 1'
            )
            ->where('service.service_jenis_id', 2)
            ->where('t_pembayaran_detail.t_pembayaran_id', $pembayaran_id)
            ->order_by('t_tagihan_air.periode')
            ->join(
                "t_pencatatan_meter_air",
                "t_pencatatan_meter_air.periode = t_tagihan_air.periode 
                    AND t_pencatatan_meter_air.unit_id = t_tagihan_air.unit_id"
            )
            ->get()->result();


        $tmp = (object)['total_pokok' => 0, 'total_ppn' => 0, 'total_tagihan' => 0, 'total_denda' => 0, 'total_diskon' => 0, 'total_terbayar_sebelum' => 0, 'total_terbayar_saat_ini' => 0];
        foreach ($pembayaran_detail_air->raw as $ind => $el) {
            $tmp->total_pokok += $el->nilai_pokok;
            $tmp->total_ppn += $el->nilai_ppn;
            $tmp->total_tagihan += round($el->nilai_pokok + (($el->nilai_pokok * $el->nilai_ppn / 100) * $el->ppn_flag));
            $tmp->total_denda += $el->nilai_denda;
            $tmp->total_diskon += $el->nilai_diskon;
            $tmp->total_terbayar_sebelum += $el->nilai_terbayar;
            $tmp->total_terbayar_saat_ini += $el->bayar;
        }
        $pembayaran_detail_air->summary = $tmp;
        //layanan lain
        $pembayaran_detail_ll = $this->db->from("t_pembayaran_detail")
            ->join(
                't_tagihan_layanan_lain',
                't_tagihan_layanan_lain.id = t_pembayaran_detail.tagihan_service_id'
            )
            ->where('service_jenis_id', 6)
            ->where('t_pembayaran_id', $pembayaran_id)
            // ->order_by('t_tagihan_lingkungan.periode')
            ->get()->result();

        $user = $this->m_parameter_project->get($GLOBALS['project']->id, "pj_kwitansi");
        $user = str_replace("{{user_login}}", $this->session->userdata('name'), $user);
        $user = $user == "" ? $this->session->userdata('name') : $user;
        $city = $this->db->from('project')->where('id', $GLOBALS['project']->id)->get()->row()->city;

        $this->load->library('pdf');
        echo ('<pre>');
        print_r([
            "pembayaran"         => $pembayaran,
            "pembayaran_lingkungan_periode_awal"  => isset($pembayaran_detail_ipl->raw[0]) ? $pembayaran_detail_ipl->raw[0]->periode : null,
            "pembayaran_lingkungan_periode_akhir" => isset($pembayaran_detail_ipl->raw[0]) ? end($pembayaran_detail_ipl->raw)->periode : null,
            "pembayaran_air_periode_awal"  => isset($pembayaran_detail_air->raw[0]) ? $pembayaran_detail_air->raw[0]->periode : null,
            "pembayaran_air_periode_akhir" => isset($pembayaran_detail_air->raw[0]) ? end($pembayaran_detail_air->raw)->periode : null,
            "no_kwitansi"           => $pembayaran->no_kwitansi,
            "project"               => $GLOBALS['project'],
            "unit"                  => $unit,
            "meter"                 => isset($pembayaran_detail_air->raw[0]) ? (object)['meter_awal' => $pembayaran_detail_air->raw[0]->meter_awal, 'meter_akhir' => end($pembayaran_detail_air->raw)->meter_akhir] : null,
            "pembayaran_lingkungan" => $pembayaran_detail_ipl->summary,
            "pembayaran_air"        => $pembayaran_detail_air->summary,
            "grand_total"           => $grand_total,
            "terbilang"             => strtoupper($this->terbilang($grand_total)),
            "pemakaian_deposit"     => 0,
            "sisa_deposit"          => 0,
            "date"                  => date("Y-m-d"),
            "user"                  => $user,
            "city"                  => $city,
            "pembayaran_ll"         => $pembayaran_detail_ll,
            "tgl_bayar"             => $pembayaran->tgl_bayar
        ]);
        die;
        $this->pdf->load_view(
            "proyek/cetakan/kwitansi_global",
            [
                "pembayaran"         => $pembayaran,
                "pembayaran_lingkungan_periode_awal"  => isset($pembayaran_detail_ipl->raw[0]) ? $pembayaran_detail_ipl->raw[0]->periode : null,
                "pembayaran_lingkungan_periode_akhir" => isset($pembayaran_detail_ipl->raw[0]) ? end($pembayaran_detail_ipl->raw)->periode : null,
                "pembayaran_air_periode_awal"  => isset($pembayaran_detail_air->raw[0]) ? $pembayaran_detail_air->raw[0]->periode : null,
                "pembayaran_air_periode_akhir" => isset($pembayaran_detail_air->raw[0]) ? end($pembayaran_detail_air->raw)->periode : null,
                "no_kwitansi"           => $pembayaran->no_kwitansi,
                "project"               => $GLOBALS['project'],
                "unit"                  => $unit,
                "meter"                 => isset($pembayaran_detail_air->raw[0]) ? (object)['meter_awal' => $pembayaran_detail_air->raw[0]->meter_awal, 'meter_akhir' => end($pembayaran_detail_air->raw)->meter_akhir] : null,
                "pembayaran_lingkungan" => $pembayaran_detail_ipl->summary,
                "pembayaran_air"        => $pembayaran_detail_air->summary,
                "grand_total"           => $grand_total,
                "terbilang"             => strtoupper($this->terbilang($grand_total)),
                "pemakaian_deposit"     => 0,
                "sisa_deposit"          => 0,
                "date"                  => date("Y-m-d"),
                "user"                  => $user,
                "city"                  => $city,
                "pembayaran_ll"         => $pembayaran_detail_ll,
                "tgl_bayar"             => $pembayaran->tgl_bayar
            ]
        );
    }
    public function gabungan()
    {
        if (!$this->input->get("pembayaran_id")) {
            exit('Pembayaran ID Kosong.');
        } else {
            $project = $this->m_core->project();
            $this->load->library('pdf');
            $pembayaran_id_tmp      = $this->input->get("pembayaran_id");
            $code_service           = '1,2';
            $code_service           = explode(",", $code_service);

            $pembayaran_id          = (object)[];
            $pembayaran_id->{1}     = [];
            $pembayaran_id->{2}     = [];
            $pembayaran_id->{3}     = [];
            $pembayaran_id->{4}     = [];
            $pembayaran_id->{5}     = [];
            $pembayaran_id->{6}     = [];
            $pembayaran_id_gabungan = $pembayaran_id_tmp;
            $tmp_no_kwitansi        = $pembayaran_id_tmp;

            $this->load->model('Setting/m_parameter_project');
            $user = $this->m_parameter_project->get($project->id, "pj_kwitansi");
            $user = str_replace("{{user_login}}", $this->session->userdata('name'), $user);
            $user = $user == "" ? $this->session->userdata('name') : $user;

            foreach ($code_service as $tmp) {
                array_push($pembayaran_id->{"$tmp[0]"}, $pembayaran_id_gabungan);
            }

            $unit = $this->db
                ->select("
                    unit.project_id,
                    kawasan.code AS kawasan_code,
                    blok.code AS blok_code,
                    pemilik.id AS pemilik_id,
                    kawasan.name AS kawasan,
                    blok.name AS blok,
                    unit.no_unit,
                    pemilik.name AS pemilik,
                    unit_air.no_seri_meter AS no_meter,
                    unit.virtual_account,
                    FORMAT (t_pembayaran.tgl_bayar, 'ddMMyyyy') AS tgl_bayar,
                    m_pt.name AS pt_name
                ")
                ->from("t_pembayaran")
                ->join("unit", "unit.id = t_pembayaran.unit_id")
                ->join("unit_air", "unit_air.unit_id = unit.id", "LEFT")
                ->join("blok", "blok.id = unit.blok_id")
                ->join("kawasan", "kawasan.id = blok.kawasan_id")
                ->join("customer as pemilik", "pemilik.id = unit.pemilik_customer_id")
                ->join("dbmaster.dbo.m_pt", "m_pt.pt_id = unit.pt_id")
                ->where_in("t_pembayaran.id", $pembayaran_id_gabungan)
                ->get()
                ->row();

            $meter = $this->db
                ->select("
                    isnull(min(t_pencatatan_meter_air.meter_awal),0) as meter_awal,
                    isnull(max(t_pencatatan_meter_air.meter_akhir),0) as meter_akhir,
                    isnull(max(t_pencatatan_meter_air.meter_akhir) - min(t_pencatatan_meter_air.meter_awal),0) as meter_pakai
                ")
                ->from("t_pembayaran")
                ->join("t_pembayaran_detail", "t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id")
                ->join("service", "service.id = t_pembayaran_detail.service_id")
                ->join(
                    "v_tagihan_air",
                    "v_tagihan_air.tagihan_id = t_pembayaran_detail.tagihan_service_id
                    AND t_pembayaran_detail.service_id = service.id
                    AND service.service_jenis_id = 2"
                )
                ->join("t_pencatatan_meter_air", "t_pencatatan_meter_air.periode = v_tagihan_air.periode AND t_pencatatan_meter_air.unit_id = t_pembayaran.unit_id");

            if ($pembayaran_id->{2}) {
                $meter = $meter->where_in("t_pembayaran.id", $pembayaran_id->{2});
            } else {
                $meter = $meter->where("t_pembayaran.id", null);
            }
            $meter = $meter->get()->row();

            $periode = $this->db
                ->select("
                    min(isnull(v_tagihan_lingkungan.periode,v_tagihan_air.periode)) as periode_awal,
                    max(isnull(v_tagihan_lingkungan.periode,v_tagihan_air.periode)) as periode_akhir
                ")
                ->from("t_pembayaran")
                ->join("t_pembayaran_detail", "t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id")
                ->join("service", "service.id = t_pembayaran_detail.service_id")
                ->join(
                    "v_tagihan_lingkungan",
                    "v_tagihan_lingkungan.tagihan_id = t_pembayaran_detail.tagihan_service_id
                        AND t_pembayaran_detail.service_id = service.id
                        AND service.service_jenis_id = 1",
                    "LEFT"
                )
                ->join(
                    "v_tagihan_air",
                    "v_tagihan_air.tagihan_id = t_pembayaran_detail.tagihan_service_id
                        AND t_pembayaran_detail.service_id = service.id
                        AND service.service_jenis_id = 2",
                    "LEFT"
                )
                ->where_in("t_pembayaran.id", $pembayaran_id_gabungan)
                ->order_by("periode_awal")
                ->get()
                ->result();

            $periode_first_v2   = substr($periode[0]->periode_awal, 5, 2) . "/" . substr($periode[0]->periode_awal, 0, 4);
            $periode_last_v2    = substr(end($periode)->periode_akhir, 5, 2) . "/" . substr(end($periode)->periode_akhir, 0, 4);

            $periode_awal       = substr($periode[0]->periode_awal, 0, 4) . "/" . substr($periode[0]->periode_awal, 5, 2) . "/01";
            $periode_akhir      = substr(end($periode)->periode_akhir, 0, 4) . "/" . substr(end($periode)->periode_akhir, 5, 2) . "/01";

            if ($periode_awal != "//01") {
            }

            $pembayaran_lingkungan = (object)[];
            if ($periode_awal != "//01") {
                $pembayaran_lingkungan  = $this->db
                    ->select("
                        sum(t_pembayaran_detail.nilai_tagihan) as tagihan,
                        AVG( t_pembayaran_detail.nilai_ppn ) as ppn,
                        sum(t_pembayaran_detail.nilai_denda) as denda,
                        sum(t_pembayaran_detail.nilai_diskon) as diskon,
                        sum(t_pembayaran_detail.bayar) as total
                    ")
                    ->from("t_pembayaran")
                    ->join("t_pembayaran_detail", "t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id")
                    ->join("service", "service.id = t_pembayaran_detail.service_id AND service.service_jenis_id = 1");

                if ($pembayaran_id->{1}) {
                    $pembayaran_lingkungan = $pembayaran_lingkungan->where_in("t_pembayaran.id", $pembayaran_id->{1});
                } else {
                    $pembayaran_lingkungan = $pembayaran_lingkungan->where("t_pembayaran.id", null);
                }
                $pembayaran_lingkungan = $pembayaran_lingkungan->get()->row();

                if ($pembayaran_lingkungan->tagihan) {
                    $pembayaran_lingkungan_periode = $this->db
                        ->select("FORMAT (t_tagihan_lingkungan.periode, 'MM/yyyy ') as periode")
                        ->from("t_pembayaran")
                        ->join("t_pembayaran_detail", "t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id")
                        ->join("t_tagihan_lingkungan", "t_tagihan_lingkungan.id = t_pembayaran_detail.tagihan_service_id")
                        ->join("service", "service.id = t_pembayaran_detail.service_id AND service.service_jenis_id = 1");

                    if ($pembayaran_id->{1}) {
                        $pembayaran_lingkungan_periode = $pembayaran_lingkungan_periode->where_in("t_pembayaran.id", $pembayaran_id->{1});
                    } else {
                        $pembayaran_lingkungan_periode = $pembayaran_lingkungan_periode->where("t_pembayaran.id", null);
                    }

                    $pembayaran_lingkungan_periode       = $pembayaran_lingkungan_periode->order_by('t_tagihan_lingkungan.periode')->get()->result();
                    $pembayaran_lingkungan_periode_awal  = $pembayaran_lingkungan_periode[0]->periode;
                    $pembayaran_lingkungan_periode_akhir = end($pembayaran_lingkungan_periode)->periode;
                } else {
                    $pembayaran_lingkungan_periode_awal = 0;
                    $pembayaran_lingkungan_periode_akhir = 0;
                }
            } else {
                $pembayaran_lingkungan->ppn = 0;
                $pembayaran_lingkungan->tagihan = 0;
                $pembayaran_lingkungan->tagihan_tanpa_ppn = 0;
                $pembayaran_lingkungan->ppn_rupiah = 0;
                $pembayaran_lingkungan->denda = 0;
                $pembayaran_lingkungan->total = 0;
                $pembayaran_lingkungan->diskon = 0;
                $pembayaran_lingkungan_periode_awal = 0;
                $pembayaran_lingkungan_periode_akhir = 0;
            }
            $pembayaran_air = (object)[];
            if ($periode_awal != "//01") {
                $pembayaran_air  = $this->db
                    ->select("
                        sum(t_pembayaran_detail.nilai_tagihan) as tagihan,
                        AVG( t_pembayaran_detail.nilai_ppn ) AS ppn,
                        sum(t_pembayaran_detail.nilai_denda) as denda,
                        sum(t_pembayaran_detail.nilai_diskon) as diskon,
                        sum(t_pembayaran_detail.bayar) as total
                    ")
                    ->from("t_pembayaran")
                    ->join("t_pembayaran_detail", "t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id")
                    ->join("service", "service.id = t_pembayaran_detail.service_id AND service.service_jenis_id = 2");

                if ($pembayaran_id->{2}) {
                    $pembayaran_air = $pembayaran_air->where_in("t_pembayaran.id", $pembayaran_id->{2});
                } else {
                    $pembayaran_air = $pembayaran_air->where("t_pembayaran.id", null);
                }
                $pembayaran_air = $pembayaran_air->get()->row();
                if ($pembayaran_air->tagihan) {
                    $pembayaran_air_periode  = $this->db
                        ->select("FORMAT (t_tagihan_air.periode, 'MM/yyyy ') as periode")
                        ->from("t_pembayaran")
                        ->join("t_pembayaran_detail", "t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id")
                        ->join("t_tagihan_air", "t_tagihan_air.id = t_pembayaran_detail.tagihan_service_id")
                        ->join("service", "service.id = t_pembayaran_detail.service_id AND service.service_jenis_id = 2");
                    if ($pembayaran_id->{2}) {
                        $pembayaran_air_periode = $pembayaran_air_periode->where_in("t_pembayaran.id", $pembayaran_id->{2});
                    } else {
                        $pembayaran_air_periode = $pembayaran_air_periode->where("t_pembayaran.id", null);
                    }
                    $pembayaran_air_periode = $pembayaran_air_periode->order_by('t_tagihan_air.periode')->get()->result();

                    $pembayaran_air_periode_awal = $pembayaran_air_periode[0]->periode;
                    $pembayaran_air_periode_akhir = end($pembayaran_air_periode)->periode;
                } else {
                    $pembayaran_air_periode_awal = 0;
                    $pembayaran_air_periode_akhir = 0;
                }
            } else {
                $pembayaran_air->ppn = 0;
                $pembayaran_air->tagihan = 0;
                $pembayaran_air->tagihan_tanpa_ppn = 0;
                $pembayaran_air->ppn_rupiah = 0;
                $pembayaran_air->denda = 0;
                $pembayaran_air->total = 0;
                $pembayaran_air->diskon = 0;
                $pembayaran_air_periode_awal = 0;
                $pembayaran_air_periode_akhir = 0;
            }
            if ($pembayaran_id->{6} != null) {
                $pembayaran_ll  = $this->db
                    ->select("
                        sum(t_pembayaran_detail.nilai_tagihan) AS tagihan,
                        sum(t_pembayaran_detail.nilai_ppn) AS ppn,
                        sum(t_pembayaran_detail.nilai_denda) AS denda,
                        sum(t_pembayaran_detail.nilai_diskon) AS diskon,
                        sum(t_pembayaran_detail.bayar) AS total,
                        paket_service.name,
                        FORMAT(t_layanan_lain_registrasi_detail.periode_awal, 'dd/MM/yyyy ') AS periode_awal,
                        FORMAT(t_layanan_lain_registrasi_detail.periode_akhir, 'dd/MM/yyyy ') AS periode_akhir
                    ")
                    ->from("t_pembayaran")
                    ->join("t_pembayaran_detail", "t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id")
                    ->join("t_tagihan_layanan_lain", "t_tagihan_layanan_lain.id = t_pembayaran_detail.tagihan_service_id")
                    ->join("service", "service.id = t_pembayaran_detail.service_id AND service.service_jenis_id = 6")
                    ->join("t_layanan_lain_registrasi", "t_layanan_lain_registrasi.id = t_tagihan_layanan_lain.t_layanan_lain_registrasi_id")
                    ->join("t_layanan_lain_registrasi_detail", "t_layanan_lain_registrasi_detail.t_layanan_lain_registrasi_id = t_layanan_lain_registrasi.id")
                    ->join("paket_service", "paket_service.id = t_layanan_lain_registrasi_detail.paket_service_id");

                if ($pembayaran_id->{6}) {
                    $pembayaran_ll = $pembayaran_ll->where_in("t_pembayaran.id", $pembayaran_id->{6});
                } else {
                    $pembayaran_ll = $pembayaran_ll->where("t_pembayaran.id", null);
                }
                $pembayaran_ll = $pembayaran_ll->group_by("
                    paket_service.name,
                    FORMAT(t_layanan_lain_registrasi_detail.periode_awal, 'dd/MM/yyyy '),
                    FORMAT(t_layanan_lain_registrasi_detail.periode_akhir, 'dd/MM/yyyy ')
                ");
                $pembayaran_ll = $pembayaran_ll->order_by('periode_awal')->get()->result();

                $pembayaran_ll_awal  = $pembayaran_ll[0]->periode_awal;
                $pembayaran_ll_akhir = end($pembayaran_ll)->periode_akhir;
                $pembayaran_ll_total = 0;
                foreach ($pembayaran_ll as $key => $tmp) {
                    $pembayaran_ll[$key]->tagihan_tanpa_ppn = number_format(($tmp->tagihan) / ((100 + $tmp->ppn) / 100), 0, ",", ".");
                    $pembayaran_ll[$key]->ppn_rupiah = number_format($tmp->tagihan - ($tmp->tagihan) / ((100 + $tmp->ppn) / 100), 0, ",", ".");
                    $pembayaran_ll_total += ($tmp->total - $tmp->diskon);
                }
            } else {
                $pembayaran_ll = null;
            }

            $saldo_deposit_tmp = $this->db
                ->select("min(t_deposit_detail.tgl_tambah) as tgl_tambah")
                ->from("t_pembayaran")
                ->join("unit", "unit.id = t_pembayaran.unit_id")
                ->join("t_deposit", "t_deposit.customer_id = unit.pemilik_customer_id")
                ->join("t_deposit_detail", "t_deposit_detail.t_deposit_id = t_deposit.id AND t_deposit_detail.tgl_tambah = t_pembayaran.tgl_tambah")
                ->where_in("t_pembayaran.id", $pembayaran_id_gabungan)
                ->get()
                ->row();

            $pemakaian_deposit = $this->db
                ->select("sum(t_pembayaran_detail.bayar_deposit) as deposit")
                ->from("t_pembayaran")
                ->join("t_pembayaran_detail", "t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id")
                ->where_in("t_pembayaran.id", $pembayaran_id_gabungan)
                ->get()
                ->row();

            $saldo_deposit_tmp = $saldo_deposit_tmp ? $saldo_deposit_tmp->tgl_tambah : null;
            $pemakaian_deposit = $pemakaian_deposit ? $pemakaian_deposit->deposit : null;

            $saldo_deposit = $this->db
                ->select("sum(t_deposit_detail.nilai) as nilai")
                ->from("t_deposit")
                ->join("t_deposit_detail", "t_deposit_detail.t_deposit_id = t_deposit.id AND t_deposit_detail.tgl_tambah < '$saldo_deposit_tmp'")
                ->where("t_deposit.customer_id", $unit->pemilik_id)
                // ->where("t_deposit_detail.nilai > 0")
                ->get()
                ->row();

            $biaya_admin = $this->db
                ->select("sum(nilai_biaya_admin_cara_pembayaran) as biaya_admin")
                ->from("t_pembayaran")
                ->where_in("t_pembayaran.id", $pembayaran_id_gabungan)
                ->get()
                ->row()
                ->biaya_admin;

            $saldo_deposit = $saldo_deposit ? $saldo_deposit->nilai : null;
            $sisa_deposit = $saldo_deposit - $pemakaian_deposit;

            $saldo_deposit = number_format($saldo_deposit, 0, ",", ".");
            $pemakaian_deposit = number_format($pemakaian_deposit, 0, ",", ".");
            $sisa_deposit = number_format($sisa_deposit, 0, ",", ".");
            $total_ppn = ($pembayaran_lingkungan->ppn / 100) * ((100 + $pembayaran_lingkungan->ppn) / 100);

            $grand_total = ($pembayaran_lingkungan->total - $pembayaran_lingkungan->diskon) + ($pembayaran_air->total - $pembayaran_air->diskon) + $biaya_admin;
            if ($pembayaran_ll)
                $grand_total += $pembayaran_ll_total;
            $terbilang = strtoupper($this->terbilang($grand_total));

            $grand_total = number_format($grand_total, 0, ",", ".");
            $biaya_admin = number_format($biaya_admin, 0, ",", ".");

            $pembayaran_lingkungan->tagihan_tanpa_ppn = ($pembayaran_lingkungan->tagihan) / ((100 + $pembayaran_lingkungan->ppn) / 100);
            $pembayaran_lingkungan->diskon_tanpa_ppn = ($pembayaran_lingkungan->diskon) / ((100 + $pembayaran_lingkungan->ppn) / 100);
            $pembayaran_lingkungan->dpp = $pembayaran_lingkungan->tagihan_tanpa_ppn - $pembayaran_lingkungan->diskon_tanpa_ppn;

            $pembayaran_lingkungan->ppn_rupiah = $pembayaran_lingkungan->dpp * $pembayaran_lingkungan->ppn / 100;

            $pembayaran_air->tagihan = number_format($pembayaran_air->tagihan, 0, ",", ".");
            $pembayaran_lingkungan->tagihan_tanpa_ppn = number_format($pembayaran_lingkungan->tagihan_tanpa_ppn, 0, ",", ".");
            $pembayaran_lingkungan->dpp = number_format($pembayaran_lingkungan->dpp, 0, ",", ".");
            $pembayaran_lingkungan->ppn_rupiah = number_format($pembayaran_lingkungan->ppn_rupiah, 0, ",", ".");
            $pembayaran_lingkungan->denda = number_format($pembayaran_lingkungan->denda, 0, ",", ".");
            $pembayaran_lingkungan->total = number_format($pembayaran_lingkungan->total - $pembayaran_lingkungan->diskon, 0, ",", ".");
            $pembayaran_lingkungan->diskon_tanpa_ppn = number_format($pembayaran_lingkungan->diskon_tanpa_ppn, 0, ",", ".");
            $pembayaran_air->denda  = number_format($pembayaran_air->denda, 0, ",", ".");
            $pembayaran_air->total  = number_format($pembayaran_air->total - $pembayaran_air->diskon, 0, ",", ".");
            $pembayaran_air->diskon = number_format($pembayaran_air->diskon, 0, ",", ".");

            $no_referensi = "";
            $unit_id = $unit->project_id . $unit->kawasan_code . $unit->blok_code . '/' . $unit->no_unit;
            $city = $this->db->from('project')->where('id', $project->id)->get()->row()->city;

            $this->pdf->load_view(
                "proyek/cetakan/kwitansi_global",
                [
                    "pembayaran_lingkungan_periode_awal"  => $pembayaran_lingkungan_periode_awal,
                    "pembayaran_lingkungan_periode_akhir" => $pembayaran_lingkungan_periode_akhir,
                    "pembayaran_air_periode_awal"  => $pembayaran_air_periode_awal,
                    "pembayaran_air_periode_akhir" => $pembayaran_air_periode_akhir,
                    "no_kwitansi"           => $tmp_no_kwitansi,
                    "project"               => $project,
                    "unit_id"               => $unit_id,
                    "unit"                  => $unit,
                    "meter"                 => $meter,
                    "periode_first"         => $periode_first_v2,
                    "periode_last"          => $periode_last_v2,
                    "pembayaran_lingkungan" => $pembayaran_lingkungan,
                    "pembayaran_air"        => $pembayaran_air,
                    "grand_total"           => $grand_total,
                    "terbilang"             => $terbilang,
                    "saldo_deposit"         => $saldo_deposit,
                    "pemakaian_deposit"     => $pemakaian_deposit,
                    "sisa_deposit"          => $sisa_deposit,
                    "total_ppn"             => $total_ppn,
                    "date"                  => date("Y-m-d"),
                    "user"                  => $user,
                    "no_referensi"          => $no_referensi,
                    "city"                  => $city,
                    "pembayaran_ll"         => $pembayaran_ll,
                    "biaya_admin"           => $biaya_admin
                ]
            );
        }
    }

    /*
    | -------------------------------------------------------------------------------
    | function for request data kwitansi customer
    | 2020-08-28
    |
    */
    public function request_data_kwitansi()
    {
        $unit_id = $this->input->post('unit_id');
        $query   = $this->db
            ->query("SELECT
                    t_pembayaran.id AS pembayaran_id,
                    FORMAT(t_pembayaran.tgl_bayar, 'dd-MM-yyyy hh:mm:ss') AS tgl_bayar,
                    service_jenis.id AS service_jenis_id,
                    service_jenis.code_default AS code_service,
                    service_jenis.name_default AS name_service,
                    CASE    
                        WHEN ttl.id IS NOT NULL THEN
                        ttl.periode 
                        WHEN tta.id IS NOT NULL THEN
                        tta.periode 
                        ELSE tta.periode 
                    END AS periode_service,
                    SUM(ISNULL(t_pembayaran_detail.bayar, t_pembayaran_detail.bayar_deposit)) AS bayar,
                    ISNULL(t_pembayaran.no_kwitansi, '') AS no_kwitansi,
                    t_pembayaran.count_print_kwitansi 
                FROM t_pembayaran
                INNER JOIN t_pembayaran_detail 
                    ON t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id
                INNER JOIN service 
                    ON service.id = t_pembayaran_detail.service_id
                INNER JOIN service_jenis 
                    ON service_jenis.id = service.service_jenis_id

                LEFT JOIN t_tagihan_lingkungan ttl 
                    ON ttl.id = t_pembayaran_detail.tagihan_service_id AND service_jenis.id = 1
                LEFT JOIN t_tagihan_air tta 
                    ON tta.id = t_pembayaran_detail.tagihan_service_id AND service_jenis.id = 2
                LEFT JOIN t_tagihan_lainnya ttla 
                    ON ttla.id = t_pembayaran_detail.tagihan_service_id AND service_jenis.id = 6
                WHERE 1=1
                    AND t_pembayaran.unit_id = '" . $unit_id . "'
                    AND ISNULL(t_pembayaran.is_void, 0) = 0
                GROUP BY
                    t_pembayaran.id,
                    t_pembayaran.tgl_bayar,
                    service_jenis.id,
                    service_jenis.code_default, 
                    service_jenis.name_default,
                    t_pembayaran.no_kwitansi,
                    t_pembayaran.count_print_kwitansi,
                    ttl.id,
                    tta.id,
                    ttl.periode,
                    tta.periode,
                    ttla.periode
                ORDER BY t_pembayaran.id
            ");
        $query = $query->result();

        $pembayaran_id = [];
        $kwitansi_all_service = [];
        foreach ($query as $q) {
            if (!in_array($q->pembayaran_id,$pembayaran_id)) {
                array_push($pembayaran_id, $q->pembayaran_id);
                array_push($kwitansi_all_service, $q);
            }
        }
        $data = [];
        foreach ($kwitansi_all_service as $k) {
            $service_jenis_id = [];
            $code_service = [];
            $name_service = [];
            $periode_tmp = [];
            $periode_service = [];
            $bayar = 0;
            foreach ($query as $q) {
                if ($q->pembayaran_id == $k->pembayaran_id) {
                    if (!in_array($q->service_jenis_id, $service_jenis_id)) {
                        array_push($service_jenis_id, $q->service_jenis_id);
                        array_push($code_service, $q->code_service);
                        array_push($name_service, $q->name_service);
                    }

                    $index = array_search($q->service_jenis_id, $service_jenis_id);
                    if (!isset($periode_tmp[$index])) {
                        $periode_tmp[$index] = [];
                    }
                    if (!in_array($q->periode_service, $periode_tmp[$index])) {
                        array_push($periode_tmp[$index],$q->periode_service);
                    }

                    $bayar += $q->bayar; 
                }
            }
            for ($i=0; $i < count($periode_tmp); $i++) {
                $periode_tmp_count = count($periode_tmp[$i]);
                sort($periode_tmp[$i]); 


                $periode_service[$i] = $name_service[$i].' ('.($periode_tmp_count>1 ? (date('m/Y',strtotime($periode_tmp[$i][0])).' s/d '.date('m/Y',strtotime($periode_tmp[$i][$periode_tmp_count-1]))) : date('m/Y',strtotime($periode_tmp[$i][0]))).')';
            }

            $k->service_jenis_id = implode(',', $service_jenis_id);
            $k->code_service = implode(',', $code_service);
            $k->name_service = implode(',', $name_service);
            $k->periode_service = implode(',', $periode_service);
            $k->bayar = $bayar;

            array_push($data, $k);
        }

        echo json_encode(array('data' => $data));
    }

    public function request_history_kwitansi()
    {
        $requestData    = $_REQUEST;
        $like_value     = $requestData['search']['value'];
        $column_order   = $requestData['order'][0]['column'];
        $column_dir     = $requestData['order'][0]['dir'];
        $limit_start    = $requestData['start'];
        $limit_length   = $requestData['length'];
        $unit_id        = $this->input->post('unit_id');

        $sql = " 
            SELECT
                ROW_NUMBER() OVER (ORDER BY log_kwitansi.id) AS nomor,
                log_kwitansi.id,
                FORMAT(log_kwitansi.created_at, 'dd-MM-yyyy hh:mm:ss') AS create_date,
                CAST(log_kwitansi.description AS VARCHAR(MAX)) AS description,
                log_kwitansi.created_by,
                SUM(ISNULL(t_pembayaran_detail.bayar, t_pembayaran_detail.bayar_deposit)) AS bayar,
                t_pembayaran.no_kwitansi,
                (
                    SUBSTRING((
                        SELECT DISTINCT 
                            ','+LEFT(service_jenis.name_default, LEN(service_jenis.name_default))
                        FROM 
                            t_pembayaran AS pembayaran
                            INNER JOIN t_pembayaran_detail ON t_pembayaran_detail.t_pembayaran_id = pembayaran.id
                            INNER JOIN service ON service.id = t_pembayaran_detail.service_id
                            INNER JOIN service_jenis ON service_jenis.id = service.service_jenis_id
                        WHERE 1=1
                            AND pembayaran.unit_id = t_pembayaran.unit_id
                            AND ISNULL(pembayaran.is_void, 0) = 0
                            AND pembayaran.id = t_pembayaran.id
                        FOR XML PATH ('')
                    ), 2, 1000)
                ) AS name_default 
            FROM 
                log_kwitansi 
                INNER JOIN t_pembayaran ON log_kwitansi.t_pembayaran_id = t_pembayaran.id
                INNER JOIN t_pembayaran_detail ON t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id
                INNER JOIN service ON service.id = t_pembayaran_detail.service_id
                INNER JOIN service_jenis ON service_jenis.id = service.service_jenis_id
            WHERE 1=1
                AND t_pembayaran.unit_id = '" . $unit_id . "'
                AND ISNULL(t_pembayaran.is_void, 0) = 0
                AND (
                    log_kwitansi.description LIKE '%" . $this->db->escape_like_str($like_value) . "%'
                    OR log_kwitansi.created_by LIKE '%" . $this->db->escape_like_str($like_value) . "%'
                    OR FORMAT(log_kwitansi.created_at, 'dd-MM-yyyy hh:mm:ss') LIKE '%" . $this->db->escape_like_str($like_value) . "%'
                    OR (
                        SUBSTRING((
                            SELECT DISTINCT 
                                ','+LEFT(service_jenis.name_default, LEN(service_jenis.name_default))
                            FROM 
                                t_pembayaran AS pembayaran
                                INNER JOIN t_pembayaran_detail ON t_pembayaran_detail.t_pembayaran_id = pembayaran.id
                                INNER JOIN service ON service.id = t_pembayaran_detail.service_id
                                INNER JOIN service_jenis ON service_jenis.id = service.service_jenis_id
                            WHERE 1=1
                                AND pembayaran.unit_id = t_pembayaran.unit_id
                                AND ISNULL(pembayaran.is_void, 0) = 0
                                AND pembayaran.id = t_pembayaran.id
                            FOR XML PATH ('')
                        ), 2, 1000)
                    ) LIKE '%" . $this->db->escape_like_str($like_value) . "%'
                )
            GROUP BY
                t_pembayaran.id,
                t_pembayaran.unit_id,
                log_kwitansi.id,
                FORMAT(log_kwitansi.created_at, 'dd-MM-yyyy hh:mm:ss'),
                CAST(log_kwitansi.description AS VARCHAR(MAX)),
                log_kwitansi.created_by,
                t_pembayaran.no_kwitansi
            ";
        // print_r($sql);
        $data_sql['totalFiltered']  = $this->db->query($sql)->num_rows();
        $data_sql['totalData']      = $this->db->query($sql)->num_rows();
        $columns_order_by = array(
            0 => 'nomor',
            1 => 'name_default',
            2 => 'bayar',
            3 => 't_pembayaran.no_kwitansi',
            4 => 'create_date',
            5 => 'description',
            6 => 'log_kwitansi.created_by'
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

            $nestedData[] = $nomor;
            $nestedData[] = $row['name_default'];
            $nestedData[] = number_format($row['bayar'], 0, ",", ",");
            $nestedData[] = $row['no_kwitansi'];
            $nestedData[] = $row['create_date'];
            $nestedData[] = $row['description'];
            $nestedData[] = $row['created_by'];
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

    function penyebut($nilai)
    {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->penyebut($nilai - 10) . " belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebut($nilai / 10) . " puluh" . $this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . $this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebut($nilai / 100) . " ratus" . $this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . $this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebut($nilai / 1000) . " ribu" . $this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebut($nilai / 1000000) . " juta" . $this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->penyebut($nilai / 1000000000) . " milyar" . $this->penyebut(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $this->penyebut($nilai / 1000000000000) . " trilyun" . $this->penyebut(fmod($nilai, 1000000000000));
        }
        return $temp;
    }
    function terbilang($nilai)
    {
        if ($nilai < 0)    $hasil = "minus " . trim($this->penyebut($nilai));
        else            $hasil = trim($this->penyebut($nilai));
        return $hasil . " Rupiah";
    }
    function bln_indo($tmp)
    {
        $bulan = array(
            1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        return $bulan[(int)$tmp];
    }
}
