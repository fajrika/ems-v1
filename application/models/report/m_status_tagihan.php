<?php

defined("BASEPATH") or exit("No direct script access allowed");

class m_status_tagihan extends CI_Model
{
    public function get_kawasan()
    {
        $project = $this->m_core->project();

        return $this->db
            ->select("
                        id,
                        code,
                        name")
            ->from("kawasan")
            ->where("project_id", $project->id)
            ->get()->result();
    }

    public function get_service()
    {
        return $this->db
            ->select("id,jenis_service as name")
            ->from("service_jenis")
            ->where_in("id", [1, 2])
            ->get()->result();
    }

    public function get_tagihan_air($req)
    {
        $project = $this->m_core->project();

        $this->db->select("
                            t.*,
                            k.name AS kawasan_name,
                            b.name AS blok_name,
                            u.no_unit,
                            u.luas_tanah,
                            u.luas_bangunan,
                            c.name AS pemilik_name,
                            c.address AS pemilik_address,
                            c.mobilephone1 AS pemilik_mobilephone1,
                            c.mobilephone2 AS pemilik_mobilephone2,
                            c.homephone AS pemilik_homephone,
                            u.penghuni_customer_id AS penghuni_id,
                            ts.status_tagihan,
                            'Air' AS service_jenis
                        ");
        $this->db->from('t_tagihan t');
        $this->db->join('unit u', 't.unit_id = u.id', 'INNER');
        $this->db->join('blok b', 'u.blok_id = b.id', 'INNER');
        $this->db->join('kawasan k', 'b.kawasan_id = k.id', 'INNER');
        $this->db->join('customer c', 'u.pemilik_customer_id = c.id', 'INNER');
        $this->db->join('t_tagihan_air ts', 't.id = ts.t_tagihan_id', 'INNER');

        $this->db->where('t.proyek_id', $project->id);

        if ($req['kawasan']!="")
        {
            $this->db->where('k.id', $req['kawasan']);
        }
        if ($req['blok']!="")
        {
            $this->db->where('b.id', $req['blok']);
        }

        if ($req['periode_awal']!="" && $req['periode_akhir']!="")
        {
            $this->db->group_start();
                $this->db->where('t.periode >=', $req['periode_awal']);
                $this->db->where('t.periode <', $req['periode_akhir']);
            $this->db->group_end();
        }
        else
        {
            if ($req['periode_awal']!="")
            {
                $this->db->where('t.periode >=', $req['periode_awal']);
            }
            if ($req['periode_akhir']!="")
            {
                $this->db->where('t.periode <', $req['periode_akhir']);
            }
        }

        if ($req['status_tagihan']!="")
        {
            $this->db->where('ts.status_tagihan', $req['status_tagihan']);
        }
        $this->db->order_by('t.periode', 'ASC');

        return $this->db->get();
    }
    public function get_tagihan_lingkungan($req)
    {
        $project = $this->m_core->project();

        $this->db->select("
                            t.*,
                            k.name AS kawasan_name,
                            b.name AS blok_name,
                            u.no_unit,
                            u.luas_tanah,
                            u.luas_bangunan,
                            c.name AS pemilik_name,
                            c.address AS pemilik_address,
                            c.mobilephone1 AS pemilik_mobilephone1,
                            c.mobilephone2 AS pemilik_mobilephone2,
                            c.homephone AS pemilik_homephone,
                            u.penghuni_customer_id AS penghuni_id,
                            ts.status_tagihan,
                            'Lingkungan' AS service_jenis
                        ");
        $this->db->from('t_tagihan t');
        $this->db->join('unit u', 't.unit_id = u.id', 'INNER');
        $this->db->join('unit_lingkungan ul', 'u.id = ul.unit_id', 'INNER');
        $this->db->join('blok b', 'u.blok_id = b.id', 'INNER');
        $this->db->join('kawasan k', 'b.kawasan_id = k.id', 'INNER');
        $this->db->join('customer c', 'u.pemilik_customer_id = c.id', 'INNER');
        $this->db->join('t_tagihan_lingkungan ts', 't.id = ts.t_tagihan_id', 'INNER');

        $this->db->where('t.proyek_id', $project->id);

        $this->db->where('ul.tgl_mandiri IS NULL'); // Filter untuk unit yang belum mandiri saja

        if ($req['kawasan']!="")
        {
            $this->db->where('k.id', $req['kawasan']);
        }
        if ($req['blok']!="")
        {
            $this->db->where('b.id', $req['blok']);
        }

        if ($req['periode_awal']!="" && $req['periode_akhir']!="")
        {
            $this->db->group_start();
                $this->db->where('t.periode >=', $req['periode_awal']);
                $this->db->where('t.periode <', $req['periode_akhir']);
            $this->db->group_end();
        }
        else
        {
            if ($req['periode_awal']!="")
            {
                $this->db->where('t.periode >=', $req['periode_awal']);
            }
            if ($req['periode_akhir']!="")
            {
                $this->db->where('t.periode <', $req['periode_akhir']);
            }
        }

        if ($req['status_tagihan']!="")
        {
            $this->db->where('ts.status_tagihan', $req['status_tagihan']);
        }
        $this->db->order_by('t.periode', 'ASC');

        return $this->db->get();
    }
}