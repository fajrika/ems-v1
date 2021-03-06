<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class m_range_air extends CI_Model {

    public function get()
    {
	    $project = $this->m_core->project();
        $query = $this->db->query("
            SELECT * FROM range_air where project_id = $project->id and [delete] = 0 order by id desc
        ");
        return $query->result_array();
    }
	
	public function getSelect($id){
        $this->load->model('m_core');
        $project = $this->m_core->project();
        
        $query = $this->db->query("
		    SELECT * FROM range_air 
			WHERE id = $id
            AND project_id = $project->id
        ");
        $row = $query->row();
        return $row; 
    }
	
	public function get_range_air_detail($id)
    {
        $this->load->model('m_core');
        $project = $this->m_core->project();
        $query = $this->db->query("
            SELECT  *  FROM range_air_detail
            WHERE range_air_id = $id 
            order by  id asc
        ");
		return $query->result_array();
	}
	
	public function cek($id){
        $this->load->model('m_core');
        $project = $this->m_core->project();

        $query = $this->db->query("
            SELECT * FROM range_air WHERE id = $id and project_id = $project->id
        ");
        $row = $query->row();
        return isset($row)?1:0;
    }
	
	public function get_log($id)
    {
        $this->load->model('m_core');
        $project = $this->m_core->project();
        $query = $this->db->query("
            SELECT
                range_air.code AS code,
                range_air.name AS name,
                range_air.formula AS formula,
                range_air.description AS description,
                CASE
                    WHEN range_air.active = 0 THEN
                    'Tidak Aktif' ELSE 'Aktif' 
                END AS aktif,
                CASE
                    WHEN range_air.[delete] = 0 THEN
                    'Tidak di Hapus' ELSE 'Terhapus' 
                END AS [delete],
                CASE
                    WHEN range_air.lock = 0 THEN
                    'Tidak Aktif' ELSE 'Aktif' 
                END AS lock,
                range_air_detail.id AS id_range_air_detail,
                range_air_detail.range_awal AS range_awal,
                range_air_detail.range_akhir AS range_akhir,
                range_air_detail.harga_hpp AS harga_hpp,
                range_air_detail.harga AS harga_range 
            FROM
                range_air
                JOIN range_air_detail ON range_air.id = range_air_detail.range_air_id 
            WHERE
                range_air.id = $id 
                AND range_air.project_id = $project->id 
            ORDER BY
                range_air_detail.id ASC
        ");
        $row = $query->result_array();
        $hasil = [];
        $i = 1;
        foreach ($row as $v) {
            if (!array_key_exists('code', $hasil)) {
                $hasil['code'] = $v['code'];
                $hasil['name'] = $v['name'];
                $hasil['formula'] = $v['formula'];
                $hasil['description'] = $v['description'];
                $hasil['aktif'] = $v['aktif'];
                $hasil['delete'] = $v['delete'];
                $hasil['lock'] = $v['lock'];
            }
            $hasil[$i.' id_range_air_detail'] = $v['id_range_air_detail'];
            $hasil[$i.' range_awal'] = $v['range_awal'];
            $hasil[$i.' range_akhir'] = $v['range_akhir'];
            $hasil[$i.' harga_hpp'] = $v['harga_hpp'];
            $hasil[$i.' harga'] = $v['harga_range'];
           
            ++$i;
        }
        return $hasil;
    }
	
	public function save($dataTmp)
    {
        $this->load->model('m_core');
        $this->load->model('m_log');
        $project = $this->m_core->project();
        $data = [
            'code'              => $dataTmp['kode'],
            'project_id'        => $project->id,
            'name'              => $dataTmp['nama'],
            'description'       => $dataTmp['keterangan'],
            'formula'           => $dataTmp['formula'],
            'active'            => 1,
            'delete'            => 0
        ];
		
		$data_range_detail = [
            'range_awal'       =>  $dataTmp['range_awal'],  
            'range_akhir'      =>  $dataTmp['range_akhir'],  
            'harga_hpp'        =>  $dataTmp['harga_hpp'],  
			'harga'            =>  $dataTmp['harga_range'],  
            'delete'           => 0
        ];
		//var_dump($data_range_detail);
	
        $this->db->where('code', $data['code']);
        $this->db->from('range_air');

        // validasi double
        if($this->db->count_all_results()==0){ 
            $this->db->insert('range_air', $data);
            $id = $this->db->insert_id();

            if (isset($dataTmp['range_awal']))
            {
				for($i= 0;$i<count($dataTmp['range_awal']);$i++) {
					
                    $range_awal[$i]  =  $this->m_core->currency_to_number($dataTmp['range_awal'][$i]);  
                    $range_akhir[$i] =  $this->m_core->currency_to_number($dataTmp['range_akhir'][$i]); 
                    $harga_hpp[$i]   =  $this->m_core->currency_to_number($dataTmp['harga_hpp'][$i]);  
					$harga_range[$i] =  $this->m_core->currency_to_number($dataTmp['harga_range'][$i]); 
                    
                    
                    $data_range_detail2 = [
                        'range_air_id'      => $id, 
                        'range_awal'        => $range_awal[$i],
                        'range_akhir'       => $range_akhir[$i],
                        'harga_hpp'         => $harga_hpp[$i],
						'harga'             => $harga_range[$i],
                        'delete'            => 0
                    ];
                        
                    $this->db->insert('range_air_detail', $data_range_detail2);
                    $dataLog = $this->get_log($this->db->insert_id());
                    $this->m_log->log_save('range_air_detail',$this->db->insert_id(),'Tambah',$dataLog);

				}
            }
            $dataLog = $this->get_log($id);
            $this->m_log->log_save('range_air',$id,'Tambah',$dataLog);
					
            return 'success';
        } else {
            return 'double';
        }
    }
	
	public function edit($dataTmp)
    {
        // echo '<pre>';
        // print_r($dataTmp);
        // echo '</pre>';

        $before = $this->get_log($dataTmp['id']);

        $this->db->where('range_air_id', $dataTmp['id']);
        $this->db->update('range_air_detail', ['delete' => 1]);

        //$this->save($dateTmp);

        $this->load->model('m_core');
        $this->load->model('m_log');
        $project = $this->m_core->project();
        $user_id = $this->m_core->user_id();

        // $this->db->where('project_id', $project->id);
        // $this->db->from('bank');
        $data =
        [
            'code' => $dataTmp['kode_range'],
            'name' => $dataTmp['name'],
            'formula'  => $dataTmp['formula'],
            'description' => $dataTmp['keterangan'],
            'active' => $dataTmp['active'] ? 1 : 0,
            'delete' => 0,
        ];
        
        // echo '<pre>';
        // print_r($before);
        // echo '</pre>';
        
        //proses edit
        $beforeRange = $this->get_log($dataTmp['id']);

        $this->db->where('id', $dataTmp['id']);
        $this->db->update('range_air', $data);
        $afterRange = $this->get_log($dataTmp['id']);
        $diff = (object) (array_diff_assoc((array) $afterRange, (array) $beforeRange));
        $tmpDiff = (array) $diff;
        if(true){
            $i = 0;
            $jumlahRangeDetailBaru = 0;

            if (isset($dataTmp['id_range_air_detail'] )) {
                foreach ($dataTmp['id_range_air_detail'] as $v) {
                    // echo '<pre>';
                    // print_r($v);
                    // echo '</pre>';
                    // var_dump($dataTmp['id_rekening'][$i]);
                    $dataRangeAirDetailTmp = [];
                    $dataRangeAirDetailTmp =
                    [
                        'range_air_id' => $dataTmp['id'],
                        'range_awal' =>  $this->m_core->currency_to_number($dataTmp['range_awal'][$i]),  
                        'range_akhir' =>  $this->m_core->currency_to_number($dataTmp['range_akhir'][$i]), 
                        'harga_hpp' =>  $this->m_core->currency_to_number($dataTmp['harga_hpp'][$i]),  
                        'harga' =>  $this->m_core->currency_to_number($dataTmp['harga_range'][$i]),                    
                        'delete' => 0,
                    ];
                    if ($v != 0) {
                        $jumlahRangeDetailBaru++;
                        // $dataRekeningTmp['id'] = $dataTmp['id_rekening'][$i];
                        // edit rekening
                        $this->db->where('id', $dataTmp['id_range_air_detail'][$i]);
                        $this->db->update('range_air_detail', $dataRangeAirDetailTmp);
                    }else{
                        // add rekening
                        $this->db->insert('range_air_detail', $dataRangeAirDetailTmp);  

                    }

                    // echo '<pre>';
                    // print_r($dataRekeningTmp);
                    // echo '</pre>';
                    $i++;

                }
            }
            // echo '<pre>';
            //     print_r($jumlahRekeningBaru);
            // echo '</pre>';
            
            $after = $this->get_log($dataTmp['id']);
            $diff = (object) (array_diff_assoc((array) $after, (array) $before));
            $tmpDiff = (array) $diff;
            // echo '<pre>';
            //     print_r($before);
            // echo '</pre>';
            // echo '<pre>';
            //     print_r($after);
            // echo '</pre>';
            // echo '<pre>';
            //     print_r($tmpDiff);
            // echo '</pre>';
            if ($tmpDiff) {
                $this->m_log->log_save('range_air', $dataTmp['id'], 'Edit', $diff);

                return 'success';
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
        $this->db->from('range_air');

        // validasi apakah user dengan project $project boleh edit data ini
        if ($this->db->count_all_results() != 0) {
            $before = $this->get_log($dataTmp['id']);
            $this->db->where('id', $dataTmp['id']);
            $this->db->update('range_air', ['delete' => 1]);
            $after = $this->get_log($dataTmp['id']);

            $diff = (object) (array_diff((array) $after, (array) $before));
            $tmpDiff = (array) $diff;

            if ($tmpDiff) {
                $this->m_log->log_save('range_air', $dataTmp['id'], 'Edit', $diff);

                return 'success';
            } else {
                return 'Tidak Ada Perubahan';
            }
        }
    }
}