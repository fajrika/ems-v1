<?php

defined('BASEPATH') or exit('No direct script access allowed');

class m_paket_service extends CI_Model
{
    public function get()
    {
        $project = $this->m_core->project();
        $query = $this->db->query("
            SELECT 
                * 
            FROM paket_service 
            WHERE [delete] = 0
            AND project_id = $project->id
            ORDER By id DESC
        ");

        return $query->result();
    }

    public function get_jenis_services()
    {
        $this->load->model('m_core');
        $project = $this->m_core->project();
        $query = $this->db->query("
        SELECT 
            id,
            name			
        FROM service
             WHERE service_jenis_id = 6 and project_id = $project->id
        ");

        return $query->result_array();
    }

    public function getSelect($id)
    {
        $this->load->model('m_core');
        $project = $this->m_core->project();

        $query = $this->db->query("

            SELECT 
                * 
            FROM paket_service 
			WHERE id = $id
            AND project_id = $project->id
			
        ");
        $row = $query->row();

        return $row;
    }

    public function cek($id)
    {
        $this->load->model('m_core');
        $project = $this->m_core->project();

        $query = $this->db->query("
            SELECT * FROM paket_service 
            WHERE id = $id and project_id = $project->id
        ");
        $row = $query->row();

        return isset($row) ? 1 : 0;
    }

    public function get_log($id)
    {
        $this->load->model('m_core');
        $project = $this->m_core->project();

        $query = $this->db->query("
            SELECT
            paket_service.project_id,
            paket_service.code,
            paket_service.name,
            paket_service.service_id AS [service id],
            service.name AS [service name],
            paket_service.satuan,
            REPLACE( CONVERT ( VARCHAR, CAST ( paket_service.biaya_satuan_langganan AS money ), 1 ), '.00', '' ) AS [biaya_satuan_langganan],
            REPLACE( CONVERT ( VARCHAR, CAST ( paket_service.biaya_satuan_tanpa_langganan AS money ), 1 ), '.00', '' ) AS [biaya_satuan_tanpa_langganan],
            CASE
                WHEN paket_service.biaya_registrasi_aktif = 1 THEN
                'Aktif' ELSE 'Non Aktif' 
            END AS biaya_registrasi_aktif,
            CASE
                WHEN paket_service.biaya_pemasangan_aktif = 1 THEN
                'Aktif' ELSE 'Non Aktif' 
            END AS biaya_pemasangan_aktif,
            CASE
                WHEN paket_service.active = 1 THEN
                'Aktif' ELSE 'Non Aktif' 
            END AS active,
            CASE
                tipe_periode 
                WHEN 1 THEN
                'Hari' 
                WHEN 2 THEN
                'Bulan' 
                WHEN 3 THEN
                'Tahun' 
            END AS [Tipe Periode],
            paket_service.[delete],
            paket_service.minimal_langganan AS [Minimal Langganan],
            paket_service.biaya_registrasi AS [Biaya Registrasi],
            paket_service.biaya_pemasangan AS [Biaya Pemasangan] 
        FROM
            paket_service
            JOIN service ON service.id = paket_service.service_id 
        WHERE
            paket_service.project_id = $project->id 
            AND paket_service.id = $id
        ");
        $row = $query->row();

        return $row;
    }

    public function save($dataTmp)
    {
        $this->load->model('m_core');
        $this->load->model('m_log');

        $project = $this->m_core->project();
        $data =
        [
            'service_id' => $dataTmp['jenis_service'],
            'code' => $dataTmp['kode_paket'],
            'project_id' => $project->id,
            'name' => $dataTmp['nama_pekerjaan'],
            'satuan' => $dataTmp['satuan'],
            'biaya_satuan_langganan' => $this->m_core->currency_to_number($dataTmp['biaya_satuan_langganan']),
            'biaya_satuan_tanpa_langganan' => $this->m_core->currency_to_number($dataTmp['biaya_satuan_tanpa_langganan']),
            'biaya_registrasi_aktif' => $dataTmp['biaya_registrasi_aktif'],
            'biaya_registrasi' => $this->m_core->currency_to_number($dataTmp['biaya_registrasi']),
            'biaya_pemasangan_aktif' => $dataTmp['biaya_pemasangan_aktif'],
            'biaya_pemasangan' => $this->m_core->currency_to_number($dataTmp['biaya_pemasangan']),
            'minimal_langganan' => $this->m_core->currency_to_number($dataTmp['minimal_langganan']),
            'tipe_periode' => $dataTmp['tipe_periode'],
            'active' => 1,
            'delete' => 0,
        ];

        $this->db->where('code', $data['code']);
        $this->db->where('project_id', $project->id);
        $this->db->from('paket_service');

        // validasi double
        if ($this->db->count_all_results() == 0) {
            $this->db->insert('paket_service', $data);
            $idTMP = $this->db->insert_id();
            $dataLog = $this->get_log($idTMP);
            $this->m_log->log_save('paket_service', $idTMP, 'Tambah', $dataLog);

            return 'success';
        } else {
            return 'double';
        }
    }

    public function edit($dataTmp)
    {
        $this->load->model('m_core');
        $this->load->model('m_log');
        $project = $this->m_core->project();
        $user_id = $this->m_core->user_id();

        $this->db->where('project_id', $project->id);
        $this->db->from('paket_service');
        $data =
        [
            'service_id' => $dataTmp['jenis_service'],
            'code' => $dataTmp['kode_paket'],
            'name' => $dataTmp['nama_pekerjaan'],
            'satuan' => $dataTmp['satuan'],
            'biaya_satuan_langganan' => $this->m_core->currency_to_number($dataTmp['biaya_satuan_langganan']),
            'biaya_satuan_tanpa_langganan' => $this->m_core->currency_to_number($dataTmp['biaya_satuan_tanpa_langganan']),
            'biaya_registrasi_aktif' => $dataTmp['biaya_registrasi_aktif'],
            'biaya_registrasi' => $this->m_core->currency_to_number($dataTmp['biaya_registrasi']),
            'biaya_pemasangan_aktif' => $dataTmp['biaya_pemasangan_aktif'],
            'biaya_pemasangan' => $this->m_core->currency_to_number($dataTmp['biaya_pemasangan']),
            'minimal_langganan' => $this->m_core->currency_to_number($dataTmp['minimal_langganan']),
            'tipe_periode'      => $dataTmp['tipe_periode'],
            'active' => $dataTmp['active'] ? 1 : 0,
        ];
        // validasi apakah user dengan project $project boleh edit data ini
        if ($this->db->count_all_results() != 0) {
            $this->db->where('code', $data['code'])->where('id !=', $dataTmp['id']);
            $this->db->from('paket_service');
            // validasi double
            if ($this->db->count_all_results() == 0) {
                $before = $this->get_log($dataTmp['id']);
                                
                $this->db->where('id', $dataTmp['id']);
                $this->db->update('paket_service', $data);
                $after = $this->get_log($dataTmp['id']);
                
                $diff = (object) (array_diff_assoc((array) $after, (array) $before));
                $tmpDiff = (array) $diff;
                
                if ($tmpDiff) {
                    $this->m_log->log_save('paket_service', $dataTmp['id'], 'Edit', $diff);

                    return 'success';
                } else {
                    return 'Tidak Ada Perubahan';
                }
            } else {
                return 'double';
            }
        }
    }

    public function delete($dataTmp)
    {
        $this->load->model('m_core');
        $this->load->model('m_log');
        $project = $this->m_core->project();
        $user_id = $this->m_core->user_id();

        $this->db->where('project_id', $project->id);
        $this->db->from('paket_service');

        // validasi apakah user dengan project $project boleh edit data ini
        if ($this->db->count_all_results() != 0) {
            $before = $this->get_log($dataTmp['id']);
            $this->db->where('id', $dataTmp['id']);
            $this->db->update('paket_service', ['delete' => 1]);
            $after = $this->get_log($dataTmp['id']);

            $diff = (object) (array_diff_assoc((array) $after, (array) $before));
            $tmpDiff = (array) $diff;

            if ($tmpDiff) {
                $this->m_log->log_save('paket_service', $dataTmp['id'], 'Edit', $diff);

                return 'success';
            } else {
                return 'Tidak Ada Perubahan';
            }
        }
    }
}
