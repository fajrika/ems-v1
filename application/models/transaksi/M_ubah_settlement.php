<?php
defined('BASEPATH') or exit('No direct script access allowed');

class m_ubah_settlement extends CI_Model
{
    public function read_file_bca($path)
    {
        $fh = fopen($path, 'r');
        $i = 0;
        $text = [];
        while ($line = fgets($fh)) {
            array_push($text, $line);
        }
        $data = (object)[];
        $data->tgl_rekening_koran = substr($text[1], -10, 8);
        $data->pembayaran = [];
        // $data->raw = $text;
        $rn = [];
        for ($i = 0; $i < count($text) - 6; $i++) {
            if (strpos($text[$i], "LAPORAN"))
                $i += 9;
            if (array_key_exists(substr($text[$i], 8, 11), $rn))
                $rn[substr($text[$i], 8, 11)]++;
            else
                $rn[substr($text[$i], 8, 11)] = 1;

            array_push($data->pembayaran, (object)[
                'va' => substr($text[$i], 8, 11),
                'rn' => $rn[substr($text[$i], 8, 11)],
                'tgl_bayar' =>  substr($text[$i], 73, 8),
                'total_bayar' =>  (int)str_replace(['.', ',', ' '], '', substr($text[$i], 53, 16))
            ]);
        }
        return $data;
    }
    public function uploud_file($path, $cara_pembayaran_id)
    {
        $datas = $this->read_file_bca($path);
        $datas->status = 1;
        foreach ($datas->pembayaran as $index => $data) {
            $data->index = $index + 1;
            $data->va_ems = substr($data->va, -7);
            $data->tgl_bayar .= '20';
            $db = $this->db
                ->select('
                    row_number() over(ORDER BY t_pembayaran.id) as rn,
                    unit.id,
                    kawasan.name as kawasan,
                    blok.name as blok,
                    unit.no_unit,
                    customer.name as pemilik,
                    t_pembayaran.tgl_bayar,
                    sum(isnull(t_pembayaran_detail.bayar,0)+isnull(t_pembayaran_detail.bayar_deposit,0)) as total')
                ->from('unit')
                ->join('blok', 'blok.id = unit.blok_id')
                ->join('kawasan', 'kawasan.id = blok.kawasan_id')
                ->join('customer', 'customer.id = unit.pemilik_customer_id')
                ->join(
                    't_pembayaran',
                    "t_pembayaran.unit_id = unit.id
                    AND CONVERT(varchar(10),tgl_bayar,103) = '$data->tgl_bayar'
                    AND t_pembayaran.cara_pembayaran_id = $cara_pembayaran_id",
                    'left'
                )
                ->join('t_pembayaran_detail', 't_pembayaran_detail.t_pembayaran_id = t_pembayaran.id', 'left')
                ->like('unit.virtual_account', $data->va_ems)
                ->group_by(
                    't_pembayaran.id,
                    unit.id,
                    kawasan.name,
                    blok.name,
                    unit.no_unit,
                    customer.name,
                    t_pembayaran.tgl_bayar'
                )
                ->where('unit.project_id', $GLOBALS['project']->id)
                ->get_compiled_select();
            // var_dump($db);
            // die;
            $db = $this->db
                ->select('*')
                ->from("($db) as db")
                ->where('rn', $data->rn)
                ->get()->row();
            // var_dump($this->db->last_query());
            $data->kawasan = $db->kawasan ?? '';
            $data->blok = $db->blok ?? '';
            $data->no_unit = $db->no_unit ?? '';
            $data->pemilik = $db->pemilik ?? '';
            $data->tgl_bayar_ems = $db->tgl_bayar ?? '';
            $data->total_bayar_ems = $db->total ?? '';
            if ($data->no_unit == '') {
                $data->status = 'Unit Tidak di Temukan';
                $datas->status = 0;
            } elseif ($data->total_bayar_ems != $data->total_bayar) {
                $data->status = 'Total Bayar Berbeda';
                $datas->status = 0;
            } else {
                $data->status = 'Data valid';
            }
        }
        return $datas;
    }
}
