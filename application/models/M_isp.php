<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_isp extends CI_Model
{
    private $table = 'm_isp_int';
	private $field = array('id','project_id','nama_isp','bandwidth','persen_mitra','nilai_kabel','nilai_pemasangan','nilai_lain_lain','keterangan','active');

    public function get()
    {
        $project = $this->m_core->project();

        $query = $this->db->select("*")
                            ->from($this->table)
                            ->where("project_id",$project->id)
                            ->get();
        return $query->result_array();
    }
    public function get_selected($id)
    {
        $project = $this->m_core->project();

        return $this->db->from($this->table)
                        ->where("id",$id)
                        ->where("project_id",$project->id)
                        ->get()->row();
    }

    public function edit($dataTmp)
    {
        $this->load->model('m_core');
        $this->load->model('m_log');
        $user_id = $this->m_core->user_id();

        $data =
        [
			'nama_isp'			=> fix_whitespace($dataTmp['nama_isp']),
			'bandwidth'			=> only_number($dataTmp['bandwidth']),
			'persen_mitra'		=> range_percent($dataTmp['persen_mitra']),
			'nilai_kabel'		=> only_number($dataTmp['nilai_kabel']),
			'nilai_pemasangan'	=> only_number($dataTmp['nilai_pemasangan']),
			'nilai_lain_lain'	=> only_number($dataTmp['nilai_lain_lain']),
			'keterangan'		=> fix_whitespace($dataTmp['keterangan']),
			'active'			=> only_number($dataTmp['active'])
        ];
        

        // validasi double
        $cek = array(
            'id <>' => $dataTmp['id'],
            'nama_isp' => $dataTmp['nama_isp']
        );
        if (!$this->cek($cek)) {
            //proses edit
            $before = $this->get_selected($dataTmp['id']);
                $this->db->where('id', $dataTmp['id']);
                $this->db->update($this->table, $data);
            $after = $this->get_selected($dataTmp['id']);

            $diff = (object) (array_diff_assoc((array) $after, (array) $before));
            $tmpDiff = (array) $diff;
            if ($tmpDiff) {
                $this->m_log->log_save($this->table, $dataTmp['id'], 'Edit', $diff);

                return 'success';
            } else {
                return 'failed';
            }
        } else {
            return 'double';
        }
    }

    public function save($dataTmp)
    {
        $this->load->model('m_core');
        $this->load->model('m_log');
        $data = [
			'project_id'		=> $dataTmp['project_id'],
            'nama_isp'          => fix_whitespace($dataTmp['nama_isp']),
			'bandwidth'			=> only_number($dataTmp['bandwidth']),
			'persen_mitra'		=> range_percent($dataTmp['persen_mitra']),
			'nilai_kabel'		=> only_number($dataTmp['nilai_kabel']),
			'nilai_pemasangan'	=> only_number($dataTmp['nilai_pemasangan']),
			'nilai_lain_lain'	=> only_number($dataTmp['nilai_lain_lain']),
			'keterangan'		=> fix_whitespace($dataTmp['keterangan']),
			'active'			=> only_number($dataTmp['active'])
        ];

        $cek = array(
            'nama_isp' => $dataTmp['nama_isp']
        );
        if (!$this->cek($cek)) {
            $res = $this->db->insert($this->table, $data);

            if ($res) {
                $id = $this->db->insert_id();

                $dataLog = $this->get_selected($id);
                $this->m_log->log_save($this->table, $id, 'Tambah', $dataLog);

                return 'success';
            } else {
                return 'failed';
            }
        } else {
            return 'double';
        }
    }

    public function cek($dataTmp)
    {
        $project = $this->m_core->project();

        $row = $this->db->from($this->table)
                        ->where($dataTmp)
                        ->where("project_id",$project->id)
                        ->get()->row();

        return isset($row) ? 1 : 0;
    }
}
