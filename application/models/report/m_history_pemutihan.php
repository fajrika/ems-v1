<?php

defined("BASEPATH") or exit("No direct script access allowed");

class m_history_pemutihan extends CI_Model
{
    public function get_pemutihan_air($where="")
    {
        $project = $this->m_core->project();

        return $this->db->query("
                                    SELECT
                                        pemutihan_unit.id,
                                        kawasan.id AS kawasan_id,
                                        kawasan.name AS kawasan_name,
                                        blok.id AS blok_id,
                                        blok.name AS blok_name,
                                        service_jenis.jenis_service,
                                        unit.no_unit,
                                        customer.name AS pemilik_name,
                                        t_tagihan.periode,
                                        t_tagihan_air.nilai_pokok,
                                        t_tagihan_air.nilai_denda,
                                        (t_tagihan_air.nilai_pokok+t_tagihan_air.nilai_denda) AS nilai_total,
                                        pemutihan_nilai.nilai_tagihan_type,
                                        (
                                            CASE pemutihan_nilai.nilai_tagihan_type 
                                                WHEN 0 THEN 'Rupiah' 
                                                ELSE 'Persentase' 
                                            END
                                         ) AS nilai_tagihan_type_desc,
                                        pemutihan_unit.pemutihan_nilai_tagihan,
                                        pemutihan_nilai.nilai_denda_type,
                                        (
                                            CASE pemutihan_nilai.nilai_denda_type 
                                                WHEN 0 THEN 'Rupiah' 
                                                ELSE 'Persentase' 
                                            END
                                         ) AS nilai_denda_type_desc,
                                        pemutihan_unit.pemutihan_nilai_denda
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
                                        t_tagihan
                                        ON 
                                            t_tagihan.unit_id = unit.id AND 
                                            t_tagihan.periode = pemutihan_unit.periode
                                        INNER JOIN
                                        t_tagihan_air
                                        ON 
                                            t_tagihan_air.t_tagihan_id = t_tagihan.id
                                        INNER JOIN
                                        customer
                                        ON 
                                            unit.pemilik_customer_id = customer.id
                                        INNER JOIN
                                        blok
                                        ON 
                                            blok.id = unit.blok_id
                                        INNER JOIN
                                        kawasan
                                        ON 
                                            kawasan.id = blok.kawasan_id
                                            AND kawasan.project_id = $project->id
                                        INNER JOIN
                                        service_jenis
                                        ON 
                                            service_jenis.id = pemutihan_unit.service_jenis_id
                                    WHERE
                                        pemutihan_unit.service_jenis_id = 2
                                ".$where);
    }
    public function get_pemutihan_lingkungan($where="")
    {
        $project = $this->m_core->project();

        return $this->db->query("
                                    
                                    SELECT
                                        pemutihan_unit.id,
                                        kawasan.id AS kawasan_id,
                                        kawasan.name AS kawasan_name,
                                        blok.id AS blok_id,
                                        blok.name AS blok_name,
                                        service_jenis.jenis_service,
                                        unit.no_unit,
                                        customer.name AS pemilik_name,
                                        t_tagihan.periode,
                                        t_tagihan_lingkungan.nilai_pokok,
                                        t_tagihan_lingkungan.nilai_denda,
                                        (t_tagihan_lingkungan.nilai_pokok+t_tagihan_lingkungan.nilai_denda) AS nilai_total,
                                        pemutihan_nilai.nilai_tagihan_type,
                                        (
                                            CASE pemutihan_nilai.nilai_tagihan_type 
                                                WHEN 0 THEN 'Rupiah' 
                                                ELSE 'Persentase' 
                                            END
                                         ) AS nilai_tagihan_type_desc,
                                        pemutihan_unit.pemutihan_nilai_tagihan,
                                        pemutihan_nilai.nilai_denda_type,
                                        (
                                            CASE pemutihan_nilai.nilai_denda_type 
                                                WHEN 0 THEN 'Rupiah' 
                                                ELSE 'Persentase' 
                                            END
                                         ) AS nilai_denda_type_desc,
                                        pemutihan_unit.pemutihan_nilai_denda
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
                                        t_tagihan
                                        ON 
                                            t_tagihan.unit_id = unit.id AND 
                                            t_tagihan.periode = pemutihan_unit.periode
                                        INNER JOIN
                                        t_tagihan_lingkungan
                                        ON 
                                            t_tagihan_lingkungan.t_tagihan_id = t_tagihan.id
                                        INNER JOIN
                                        customer
                                        ON 
                                            unit.pemilik_customer_id = customer.id
                                        INNER JOIN
                                        blok
                                        ON 
                                            blok.id = unit.blok_id
                                        INNER JOIN
                                        kawasan
                                        ON 
                                            kawasan.id = blok.kawasan_id
                                            AND kawasan.project_id = $project->id
                                        INNER JOIN
                                        service_jenis
                                        ON 
                                            service_jenis.id = pemutihan_unit.service_jenis_id
                                    WHERE
                                        pemutihan_unit.service_jenis_id = 1
                                ".$where);
    }
    public function get_approval($id_pemutihan="")
    {
        return $this->db->query("
                                    SELECT 
                                        aw.id AS aw_id, 
                                        aw.description, 
                                        u_aw.name AS u_aw_name, 
                                        [as].status, 
                                        aw.tgl_approve AS tgl  
                                    FROM approval_wewenang AS aw 
                                    INNER JOIN [user] AS u_aw 
                                        ON u_aw.id = aw.[user_id]
                                    INNER JOIN approval_wewenang_user AS awu 
                                        ON awu.approval_wewenang_id = aw.id
                                    INNER JOIN [user] AS u_awu 
                                        ON u_awu.id = awu.[user_id]
                                    INNER JOIN approval_status AS [as] 
                                        ON [as].id = aw.approval_status_id
                                    INNER JOIN approval AS a 
                                        ON a.id = aw.approval_id 
                                        AND a.dokumen_id = '".$id_pemutihan."'
                                    GROUP BY aw.id,aw.description,u_aw.name,[as].status,aw.tgl_approve 
                                ");
    }
}