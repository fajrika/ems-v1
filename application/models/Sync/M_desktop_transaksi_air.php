<?php
defined('BASEPATH') or exit('No direct script access allowed');

class m_desktop_transaksi_air extends CI_Model
{
    public function telahDiMigrasi($source)
    {
        $data =
            $this->db
            ->select("count(*) as c")
            ->from("ems_temp.$source.td_air")
            ->join(
                "t_tagihan_air_detail",
                "t_tagihan_air_detail.source_table = '$source'
                    AND t_tagihan_air_detail.source_id = td_air.td_air_id"
            )
            ->where("td_air.nilai_total != 0")
            ->or_where("(td_air.meter_akhir - td_air.meter_awal) > 0")
            ->get()->row();
        return $data->c ?? '0';
    }
    public function belumDiMigrasi($source)
    {
        $data =
            $this->db
            ->select("count(*) as c")
            ->from("ems_temp.$source.td_air")
            ->join(
                "t_tagihan_air_detail",
                "t_tagihan_air_detail.source_table = '$source'
                    AND t_tagihan_air_detail.source_id = td_air.td_air_id",
                "LEFT"
            )
            ->where("t_tagihan_air_detail.id is null")
            ->where("(td_air.nilai_total != 0 OR (td_air.meter_akhir - td_air.meter_awal) > 0)")
            ->get()->row();
        return $data->c ?? '0';
    }
    // public function progress($project_id)
    // {
    //     $data = $this->db->select("count(*) as c")
    //         ->from("t_tagihan_air")
    //         ->join(
    //             "t_tagihan_air_info",
    //             "t_tagihan_air_info.t_tagihan_air_id = t_tagihan_air.id"
    //         )
    //         ->where("proyek_id", $project_id)
    //         ->get()->row();
    //     $data = $data->c;
    //     echo (json_encode($data ?? '-1'));
    // }
    public function getDataBeforeMigrate($source, $jarak_periode = 0)
    {
        $data =
            $this->db
            ->select("td_air.td_air_id")
            ->select("td_air.th_trans_id")
            ->select("DATEADD(MONTH,$jarak_periode,FORMAT(td_air.periode,'yyyy-MM-01')) as periode")
            ->select("td_air.range_id")
            ->select("td_air.bayar_id")
            ->select("td_air.tanggal_bayar")
            ->select("td_air.Meter_awal")
            ->select("td_air.Meter_akhir")
            ->select("td_air.nilai_admin")
            ->select("td_air.nilai_denda")
            ->select("td_air.nilai_pakai")
            ->select("td_air.nilai_pipa")
            ->select("td_air.nomor")
            ->select("td_air.nilai_ppnair")
            ->select("unit.id as unit_id")
            ->select("sub_golongan.id as sub_gol_id")
            ->select("sub_golongan.code as sub_gol_code")
            ->select("range_air.id as range_id")
            ->select("range_air.code as range_code")
            ->select("t_tagihan_air_detail.t_tagihan_air_id as tagihan_id")
            ->from("ems_temp.$source.td_air")
            ->join(
                "ems_temp.$source.th_trans",
                "th_trans.th_trans_id = td_air.th_trans_id"
            )
            ->join(
                "unit",
                "unit.source_table = '$source'
                AND unit.source_id = th_trans.cust_id"
            )
            ->join(
                "unit_air",
                "unit_air.unit_id = unit.id"
            )
            ->join(
                "sub_golongan",
                "sub_golongan.id = unit_air.sub_gol_id"
            )
            ->join(
                "range_air",
                "range_air.id = sub_golongan.range_id"
            )
            ->join(
                "t_tagihan_air_detail",
                "t_tagihan_air_detail.source_table = '$source'
                 AND t_tagihan_air_detail.source_id = td_air.td_air_id",
                "LEFT"
            )
            ->where("t_tagihan_air_detail.t_tagihan_air_id is null")
            ->where("(td_air.nilai_total != 0 OR (td_air.Meter_akhir - td_air.Meter_awal) > 0)")
            ->order_by("tagihan_id")
            ->limit("1000")
            ->get()->result();
        // var_dump($this->db->last_query());
        return $data;
        // echo (json_encode($data));
    }
    public function save($project_id, $source, $denda_jenis_service, $denda_nilai_service, $data)
    {
        $username = $this->session->userdata('username');
        $password = $this->session->userdata('password');
        $user_id = $this->db->SELECT("id")
            ->from("user")
            ->where("username", $username)
            ->where("password", $password)
            ->get()->row();
        $user_id = $user_id ? $user_id->id : 0;

        $tagihan_air         = (object)[];
        $tagihan_air_detail  = (object)[];
        $tagihan_air_info    = (object)[];
        $meter_air           = (object)[];
        $data_tagihan        = (object)[];

        $tagihan_air->proyek_id             = $project_id;
        $tagihan_air_detail->source_table   = $source;
        $tagihan_air_detail->active         = 1;
        $tagihan_air_detail->user_id        = $user_id;
        $tagihan_air_detail->nilai_ppn      = 10;
        $tagihan_air_detail->nilai_flag     = 0;
        $tagihan_air_detail->nilai_denda_flag = 0;
        $data_tagihan->proyek_id    = $project_id;
        $i = 0;
        foreach ($data as $k => $v) {
            if (!$v->tagihan_id) {


                $this->db->where('proyek_id', $project_id);
                $this->db->where('unit_id', $v->unit_id);
                $this->db->where('periode', $v->periode);

                $tagihan_sudah_ada = $this->db->get('t_tagihan');

                if (!$tagihan_sudah_ada->num_rows()) {
                    $data_tagihan->unit_id      = $v->unit_id;
                    $data_tagihan->periode      = $v->periode;

                    $this->db->insert('t_tagihan', $data_tagihan);
                    $tagihan_air->t_tagihan_id = $this->db->insert_id();
                } else {
                    $tagihan_air->t_tagihan_id = $tagihan_sudah_ada->row()->id;
                }


                $tagihan_air->unit_id = $v->unit_id;
                $tagihan_air->kode_tagihan = $v->nomor;
                $tagihan_air->periode = $v->periode;
                $tagihan_air->status_tagihan = $v->tanggal_bayar ? 1 : 0;
                $this->db->insert("t_tagihan_air", $tagihan_air);
                $i++;
                $tagihan_air_detail->t_tagihan_air_id   = $this->db->insert_id();;
                $tagihan_air_detail->nilai              = $v->nilai_pakai;
                $tagihan_air_detail->nilai_administrasi = $v->nilai_admin;
                $tagihan_air_detail->nilai_pemeliharaan = $v->nilai_pipa;
                $tagihan_air_detail->nilai_denda        = $v->nilai_denda;
                $tagihan_air_detail->ppn_flag           = $v->nilai_ppnair ? 1 : 0;
                $tagihan_air_detail->source_id          = $v->td_air_id;

                $tagihan_air_info->t_tagihan_air_id     = $tagihan_air_detail->t_tagihan_air_id;
                $tagihan_air_info->range_id             = $v->range_id;
                $tagihan_air_info->range_code           = $v->range_code;
                $tagihan_air_info->sub_golongan_id      = $v->sub_gol_id;
                $tagihan_air_info->sub_golongan_code    = $v->sub_gol_code;
                $tagihan_air_info->pemakaian            = ($v->Meter_akhir - $v->Meter_awal);
                $tagihan_air_info->denda_jenis_service  = $denda_jenis_service;
                $tagihan_air_info->denda_nilai_service  = $denda_nilai_service;

                $meter_air->unit_id     = $tagihan_air->unit_id;
                $meter_air->periode     = $tagihan_air->periode;
                $meter_air->meter_awal  = $v->Meter_awal;
                $meter_air->meter_akhir = $v->Meter_akhir;
                $meter_air->keterangan  = "Data Migrasi dari $source";

                $this->db->insert("t_pencatatan_meter_air", $meter_air);
                $this->db->insert("t_tagihan_air_detail", $tagihan_air_detail);
                $this->db->insert("t_tagihan_air_info", $tagihan_air_info);
            }
        }
        $this->db->trans_commit();
        return $i > 0 ? $i : -1;
    }
}
