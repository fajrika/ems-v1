<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_tagihan extends CI_Model
{
	public function get()
	{
		$project = $this->m_core->project();
		$query = $this->db->query("
            SELECT * FROM kawasan where project_id = $project->id and [delete] = 0 order by id desc
        ");

		return $query->result_array();
	}
	public function lingkungan($project_id, $data = null)
	{
		$data = (object)$data;
		$periode = isset($data->periode) ? substr($data->periode, 0, 4) . '-' . substr($data->periode, 5, 2) . '-01' : date('Y-m-01');
		$date = isset($data->periode) ? substr($data->periode, 0, 4) . '-' . substr($data->periode, 5, 2) . '-' . substr($data->periode, 8, 2) : date('Y-m-d');
		$day = isset($data->periode) ? substr($data->periode, 8, 2) : date('d');
		$tagihan_lingkungan = $this->db->select("
								'Lingkungan' as service,
								CASE
									WHEN v_tagihan_lingkungan.periode >= '$periode' THEN 1
									ELSE 0
								END as is_tagihan,
								CASE
									WHEN v_tagihan_lingkungan.status_tagihan = 0 
										or v_tagihan_lingkungan.status_tagihan = 2 
										or v_tagihan_lingkungan.status_tagihan = 3 
										or v_tagihan_lingkungan.status_tagihan = 4 THEN
										isnull(CASE
											WHEN DATEADD(MONTH,service.penalti_selisih_bulan,v_tagihan_lingkungan.periode) > '$date' THEN 0
											ELSE
												CASE
													WHEN service.penalti_jenis = 1 
														THEN v_tagihan_lingkungan.denda_nilai_service 
													WHEN service.penalti_jenis = 3 
														THEN service.penalti_nilai * v_tagihan_lingkungan.total_tanpa_ppn / 100
												END 
											END,0) 
										ELSE 0
								END as nilai_penalti,
								v_tagihan_lingkungan.total_tanpa_ppn,
								v_tagihan_lingkungan.tagihan_id,
								service.id as service_id,
								service.service_jenis_id as service_jenis_id,
								v_tagihan_lingkungan.periode,
								DATEADD(MONTH, - (service.jarak_periode_penggunaan), v_tagihan_lingkungan.periode) as periode_pemakaian,
								unit_lingkungan.tgl_mulai_denda,
								v_tagihan_lingkungan.status_tagihan,
								isnull(CASE 
									WHEN v_tagihan_lingkungan.status_tagihan = 0 or v_tagihan_lingkungan.status_tagihan = 2 or v_tagihan_lingkungan.status_tagihan = 3  THEN isnull(v_tagihan_lingkungan.ppn,0)
									ELSE 0
								END,0) as ppn,
								CASE RIGHT(isnull(v_tagihan_lingkungan.total,0),2)
									WHEN 99 THEN 1
									WHEN 01 THEN -1
									ELSE 0
								END
								+
								CASE 
									WHEN v_tagihan_lingkungan.status_tagihan = 4 THEN 
										isnull(v_tagihan_lingkungan.total,0) - iif(t_pembayaran.is_void=1,0,t_pembayaran_detail.nilai_tagihan)
									else isnull(v_tagihan_lingkungan.total,0)
								END as nilai_tagihan,
								CASE
									WHEN v_tagihan_lingkungan.status_tagihan = 0 or v_tagihan_lingkungan.status_tagihan = 2 or v_tagihan_lingkungan.status_tagihan = 3 or v_tagihan_lingkungan.status_tagihan = 4 THEN
										isnull(CASE
											WHEN v_tagihan_lingkungan.periode <= unit_lingkungan.tgl_mulai_denda THEN
												0
											WHEN v_tagihan_lingkungan.nilai_denda_flag = 1 THEN v_tagihan_lingkungan.nilai_denda 
											WHEN DATEADD(MONTH,service.denda_selisih_bulan,v_tagihan_lingkungan.periode) > '$date' THEN 0
											WHEN CONCAT(
														SUBSTRING(CONVERT(varchar, v_tagihan_lingkungan.periode), 1, 8),
														(
															CASE 
																WHEN (service.denda_tanggal_jt) > 9 
																	THEN CAST(service.denda_tanggal_jt AS VARCHAR) 
																ELSE CONCAT(0,(service.denda_tanggal_jt)) 
															END
														)
													) > '$date' THEN 0
											ELSE
												CASE
													WHEN v_tagihan_lingkungan.denda_jenis_service = 1 
														THEN v_tagihan_lingkungan.denda_nilai_service 
													WHEN v_tagihan_lingkungan.denda_jenis_service = 2 
														THEN v_tagihan_lingkungan.denda_nilai_service * 
															(DateDiff
																( MONTH, DATEADD(MONTH, service.denda_selisih_bulan, v_tagihan_lingkungan.periode), '$date' ) 
																+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) 
															)
													WHEN v_tagihan_lingkungan.denda_jenis_service = 3 
														THEN 
															( v_tagihan_lingkungan.denda_nilai_service * v_tagihan_lingkungan.total_tanpa_ppn/ 100 ) 
															* (DateDiff( MONTH, DATEADD(MONTH, service.denda_selisih_bulan, v_tagihan_lingkungan.periode), '$date' ) 
															+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) )
												END 
											END,0) 
										ELSE 0
								END 
								-
								CASE 
									WHEN v_tagihan_lingkungan.status_tagihan = 4 THEN isnull(iif(t_pembayaran.is_void=1,0,t_pembayaran_detail.nilai_denda),0)
									else 0
								END
								AS nilai_denda,
								isnull(v_tagihan_lingkungan.total,0)+
								isnull(CASE
								WHEN v_tagihan_lingkungan.status_tagihan = 0 or v_tagihan_lingkungan.status_tagihan = 2 or v_tagihan_lingkungan.status_tagihan = 3 THEN
									isnull(CASE
										WHEN v_tagihan_lingkungan.periode <= unit_lingkungan.tgl_mulai_denda THEN
											0
										WHEN v_tagihan_lingkungan.nilai_denda_flag = 1 THEN v_tagihan_lingkungan.nilai_denda 
										WHEN DATEADD(MONTH,service.denda_selisih_bulan,v_tagihan_lingkungan.periode) > '$date' THEN 0
										WHEN CONCAT(
													SUBSTRING(CONVERT(varchar, v_tagihan_lingkungan.periode), 1, 8),
													(
														CASE 
															WHEN (service.denda_tanggal_jt) > 9 
																THEN CAST(service.denda_tanggal_jt AS VARCHAR) 
															ELSE CONCAT(0,(service.denda_tanggal_jt)) 
														END
													)
												) > '$date' THEN 0
										ELSE
											CASE
												WHEN v_tagihan_lingkungan.denda_jenis_service = 1 
													THEN v_tagihan_lingkungan.denda_nilai_service 
												WHEN v_tagihan_lingkungan.denda_jenis_service = 2 
													THEN v_tagihan_lingkungan.denda_nilai_service * 
														(DateDiff
															( MONTH, DATEADD(MONTH, service.denda_selisih_bulan, v_tagihan_lingkungan.periode), '$date' ) 
															+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) 
														)
												WHEN v_tagihan_lingkungan.denda_jenis_service = 3 
													THEN 
														( v_tagihan_lingkungan.denda_nilai_service * v_tagihan_lingkungan.total_tanpa_ppn/ 100 ) 
														* (DateDiff( MONTH, DATEADD(MONTH, service.denda_selisih_bulan, v_tagihan_lingkungan.periode), '$date' ) 
														+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) )
											END 
										END,0) 
									ELSE 0
								END,0) 
									- isnull(v_pemutihan.pemutihan_nilai_tagihan,0)
									- isnull(v_pemutihan.pemutihan_nilai_denda,0)
									AS total,
								v_tagihan_lingkungan.ppn_flag,
								CONVERT(INT,
									CASE v_tagihan_lingkungan.ppn_flag
										WHEN 1 THEN 
											isnull(
												round(v_pemutihan.pemutihan_nilai_tagihan*(1.0+(v_tagihan_lingkungan.nilai_ppn/100.0)),0)
												,0)
										ELSE isnull(v_pemutihan.pemutihan_nilai_tagihan,0)
									END
								) as view_pemutihan_nilai_tagihan,
								CONVERT(INT,
									-- CASE v_tagihan_lingkungan.ppn_flag
									--	WHEN 1 THEN 
									--	isnull(
									--		round(v_pemutihan.pemutihan_nilai_denda*(1.0+(v_tagihan_lingkungan.nilai_ppn/100.0)),0)
									--		,0)
									--	ELSE
										isnull(v_pemutihan.pemutihan_nilai_denda,0)
									--END
								) as view_pemutihan_nilai_denda,								
								isnull(iif(t_pembayaran.is_void=1,0,t_pembayaran_detail.sisa_tagihan),0) as belum_bayar")
			->from("v_tagihan_lingkungan")
			// ->distinct()
			// ->where("v_tagihan_lingkungan.periode <= '$periode_now'")
			->join(
				"service",
				"service.project_id = $project_id
				AND service.service_jenis_id = 1
				AND service.active = 1
				AND service.delete = 0"
			)
			->join(
				"v_pemutihan",
				"v_pemutihan.masa_akhir >= '$date'
					AND v_pemutihan.masa_awal <= '$date'
					AND v_pemutihan.periode = v_tagihan_lingkungan.periode 
					AND v_pemutihan.service_jenis_id = 1
					AND v_pemutihan.unit_id  = v_tagihan_lingkungan.unit_id",
				"LEFT"
			)
			->join(
				"t_tagihan_lingkungan",
				"t_tagihan_lingkungan.t_tagihan_id = v_tagihan_lingkungan.t_tagihan_id
					AND t_tagihan_lingkungan.unit_id =  v_tagihan_lingkungan.unit_id
					-- AND t_tagihan_lingkungan.periode =  v_tagihan_lingkungan.periode",
				"LEFT"
			)
			// AND t_tagihan_lingkungan.periode = dateadd(MONTH,-1,v_tagihan_lingkungan.periode)",
			->join(
				"t_pembayaran_detail",
				"t_pembayaran_detail.tagihan_service_id = t_tagihan_lingkungan.id
					AND t_pembayaran_detail.service_id = service.id",
				"LEFT"
			)
			->join(
				"t_pembayaran",
				"t_pembayaran.id = t_pembayaran_detail.t_pembayaran_id
					AND (
						(t_pembayaran.is_void = 0 and v_tagihan_lingkungan.status_tagihan in (0,4))
						or (t_pembayaran.is_void = 1 and v_tagihan_lingkungan.status_tagihan = 0)
						)",
				"INNER"
			)
			->join(
				"unit_lingkungan",
				"unit_lingkungan.unit_id = v_tagihan_lingkungan.unit_id"
			);
		// ->where("v_tagihan_lingkungan.unit_id = $unit_id")
		if (isset($data->unit_id))
			$tagihan_lingkungan = $tagihan_lingkungan->where_in("v_tagihan_lingkungan.unit_id", $data->unit_id);
		if (isset($data->status_tagihan))
			$tagihan_lingkungan = $tagihan_lingkungan->where_in("v_tagihan_lingkungan.status_tagihan", $data->status_tagihan);
		else
			$tagihan_lingkungan = $tagihan_lingkungan->where("(v_tagihan_lingkungan.status_tagihan = 0 or v_tagihan_lingkungan.status_tagihan = 4  or v_tagihan_lingkungan.status_tagihan = 2 or v_tagihan_lingkungan.status_tagihan = 3)");

		$tagihan_lingkungan = $tagihan_lingkungan
			->distinct()
			->get();
		// var_dump($this->db->last_query());

		// var_dump($this->db->last_query());
		$tagihan_lingkungan = $this->db->from("(" . $this->db->last_query() . ") as a")
			->order_by("periode")
			->get()->result();
		// echo("<pre>");
		// 	print_r($tagihan_lingkungan);
		// echo("</pre>");
		// echo("<pre>");
		// 	print_r($this->db->last_query());
		// echo("</pre>");


		$tagihan_lingkungan_tmp = $tagihan_lingkungan;
		$tagihan_lingkungan = [];
		$view_pemutihan_nilai_tagihan = 0;
		$view_pemutihan_nilai_denda = 0;
		$sisa_nilai_tagihan = 0;
		$sisa_nilai_denda = 0;
		foreach ($tagihan_lingkungan_tmp as $k => $v) {
			// if($v->view_pemutihan_nilai_tagihan > 0 ){
			// 	$v->nilai_tagihan = ($v->total_tanpa_ppn - $v->view_pemutihan_nilai_tagihan) + (($v->total_tanpa_ppn - $v->view_pemutihan_nilai_tagihan) * 10 / 100);
			// }
			if (substr($v->view_pemutihan_nilai_tagihan, -2) == '99')
				$tagihan_lingkungan_tmp[$k]->view_pemutihan_nilai_tagihan++;
			elseif (substr($v->view_pemutihan_nilai_tagihan, -2) == '01')
				$tagihan_lingkungan_tmp[$k]->view_pemutihan_nilai_tagihan--;
			$v->sisa_nilai_tagihan = $v->nilai_tagihan - $v->view_pemutihan_nilai_tagihan;

			// if($v->view_pemutihan_nilai_denda > 0 ){
			// 	$v->nilai_denda = ($v->nilai_denda - $v->view_pemutihan_nilai_denda);
			// }
			$v->sisa_nilai_denda = $v->nilai_denda - $v->view_pemutihan_nilai_denda;

			array_push($tagihan_lingkungan, $v);
		}
		return $tagihan_lingkungan;
	}
	public function lingkungan_tanpa_potong_pemutihan($project_id, $data = null)
	{
		$data = (object)$data;
		$periode = isset($data->periode) ? substr($data->periode, 0, 4) . '-' . substr($data->periode, 5, 2) . '-01' : date('Y-m-01');
		$date = isset($data->periode) ? substr($data->periode, 0, 4) . '-' . substr($data->periode, 5, 2) . '-' . substr($data->periode, 8, 2) : date('Y-m-d');
		$day = isset($data->periode) ? substr($data->periode, 8, 2) : date('d');
		$tagihan_lingkungan = $this->db->select("
								'Lingkungan' as service,
								CASE
									WHEN v_tagihan_lingkungan.periode >= '$periode' THEN 1
									ELSE 0
								END as is_tagihan,
								CASE
									WHEN v_tagihan_lingkungan.status_tagihan = 0 
										or v_tagihan_lingkungan.status_tagihan = 2 
										or v_tagihan_lingkungan.status_tagihan = 3 
										or v_tagihan_lingkungan.status_tagihan = 4 THEN
										isnull(CASE
											WHEN DATEADD(MONTH,service.penalti_selisih_bulan,v_tagihan_lingkungan.periode) > '$date' THEN 0
											ELSE
												CASE
													WHEN service.penalti_jenis = 1 
														THEN v_tagihan_lingkungan.denda_nilai_service 
													WHEN service.penalti_jenis = 3 
														THEN service.penalti_nilai * v_tagihan_lingkungan.total_tanpa_ppn / 100
												END 
											END,0) 
										ELSE 0
								END as nilai_penalti,
								v_tagihan_lingkungan.total_tanpa_ppn,
								v_tagihan_lingkungan.tagihan_id,
								service.id as service_id,
								service.service_jenis_id as service_jenis_id,
								v_tagihan_lingkungan.periode,
								DATEADD(MONTH, - (service.jarak_periode_penggunaan), v_tagihan_lingkungan.periode) as periode_pemakaian,
								unit_lingkungan.tgl_mulai_denda,
								v_tagihan_lingkungan.status_tagihan,
								isnull(CASE 
									WHEN v_tagihan_lingkungan.status_tagihan = 0 or v_tagihan_lingkungan.status_tagihan = 2 or v_tagihan_lingkungan.status_tagihan = 3  THEN isnull(v_tagihan_lingkungan.ppn,0)
									ELSE 0
								END,0) as ppn,
								CASE RIGHT(isnull(v_tagihan_lingkungan.total,0),2)
									WHEN 99 THEN 1
									WHEN 01 THEN -1
									ELSE 0
								END
								+
								CASE 
									WHEN v_tagihan_lingkungan.status_tagihan = 4 THEN 
										isnull(v_tagihan_lingkungan.total,0) - iif(t_pembayaran.is_void=1,0,t_pembayaran_detail.nilai_tagihan)
									else isnull(v_tagihan_lingkungan.total,0)
								END as nilai_tagihan,
								CASE
									WHEN v_tagihan_lingkungan.status_tagihan = 0 or v_tagihan_lingkungan.status_tagihan = 2 or v_tagihan_lingkungan.status_tagihan = 3 or v_tagihan_lingkungan.status_tagihan = 4 THEN
										isnull(CASE
											WHEN v_tagihan_lingkungan.periode <= unit_lingkungan.tgl_mulai_denda THEN
												0
											WHEN v_tagihan_lingkungan.nilai_denda_flag = 1 THEN v_tagihan_lingkungan.nilai_denda 
											WHEN DATEADD(MONTH,service.denda_selisih_bulan,v_tagihan_lingkungan.periode) > '$date' THEN 0
											WHEN CONCAT(
														SUBSTRING(CONVERT(varchar, v_tagihan_lingkungan.periode), 1, 8),
														(
															CASE 
																WHEN (service.denda_tanggal_jt) > 9 
																	THEN CAST(service.denda_tanggal_jt AS VARCHAR) 
																ELSE CONCAT(0,(service.denda_tanggal_jt)) 
															END
														)
													) > '$date' THEN 0
											ELSE
												CASE
													WHEN v_tagihan_lingkungan.denda_jenis_service = 1 
														THEN v_tagihan_lingkungan.denda_nilai_service 
													WHEN v_tagihan_lingkungan.denda_jenis_service = 2 
														THEN v_tagihan_lingkungan.denda_nilai_service * 
															(DateDiff
																( MONTH, DATEADD(MONTH, service.denda_selisih_bulan, v_tagihan_lingkungan.periode), '$date' ) 
																+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) 
															)
													WHEN v_tagihan_lingkungan.denda_jenis_service = 3 
														THEN 
															( v_tagihan_lingkungan.denda_nilai_service * v_tagihan_lingkungan.total_tanpa_ppn/ 100 ) 
															* (DateDiff( MONTH, DATEADD(MONTH, service.denda_selisih_bulan, v_tagihan_lingkungan.periode), '$date' ) 
															+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) )
												END 
											END,0) 
										ELSE 0
								END 
								-
								CASE 
									WHEN v_tagihan_lingkungan.status_tagihan = 4 THEN isnull(iif(t_pembayaran.is_void=1,0,t_pembayaran_detail.nilai_denda),0)
									else 0
								END
								AS nilai_denda,
								isnull(v_tagihan_lingkungan.total,0)+
								isnull(CASE
								WHEN v_tagihan_lingkungan.status_tagihan = 0 or v_tagihan_lingkungan.status_tagihan = 2 or v_tagihan_lingkungan.status_tagihan = 3 THEN
									isnull(CASE
										WHEN v_tagihan_lingkungan.periode <= unit_lingkungan.tgl_mulai_denda THEN
											0
										WHEN v_tagihan_lingkungan.nilai_denda_flag = 1 THEN v_tagihan_lingkungan.nilai_denda 
										WHEN DATEADD(MONTH,service.denda_selisih_bulan,v_tagihan_lingkungan.periode) > '$date' THEN 0
										WHEN CONCAT(
													SUBSTRING(CONVERT(varchar, v_tagihan_lingkungan.periode), 1, 8),
													(
														CASE 
															WHEN (service.denda_tanggal_jt) > 9 
																THEN CAST(service.denda_tanggal_jt AS VARCHAR) 
															ELSE CONCAT(0,(service.denda_tanggal_jt)) 
														END
													)
												) > '$date' THEN 0
										ELSE
											CASE
												WHEN v_tagihan_lingkungan.denda_jenis_service = 1 
													THEN v_tagihan_lingkungan.denda_nilai_service 
												WHEN v_tagihan_lingkungan.denda_jenis_service = 2 
													THEN v_tagihan_lingkungan.denda_nilai_service * 
														(DateDiff
															( MONTH, DATEADD(MONTH, service.denda_selisih_bulan, v_tagihan_lingkungan.periode), '$date' ) 
															+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) 
														)
												WHEN v_tagihan_lingkungan.denda_jenis_service = 3 
													THEN 
														( v_tagihan_lingkungan.denda_nilai_service * v_tagihan_lingkungan.total_tanpa_ppn/ 100 ) 
														* (DateDiff( MONTH, DATEADD(MONTH, service.denda_selisih_bulan, v_tagihan_lingkungan.periode), '$date' ) 
														+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) )
											END 
										END,0) 
									ELSE 0
								END,0) 
									AS total,
								v_tagihan_lingkungan.ppn_flag,
								CONVERT(INT,
									CASE v_tagihan_lingkungan.ppn_flag
										WHEN 1 THEN 
											0
										ELSE 
											0
									END
								) as view_pemutihan_nilai_tagihan,
								CONVERT(INT,0) as view_pemutihan_nilai_denda,								
								isnull(iif(t_pembayaran.is_void=1,0,t_pembayaran_detail.sisa_tagihan),0) as belum_bayar")
			->from("v_tagihan_lingkungan")
			// ->distinct()
			// ->where("v_tagihan_lingkungan.periode <= '$periode_now'")
			->join(
				"service",
				"service.project_id = $project_id
				AND service.service_jenis_id = 1
				AND service.active = 1
				AND service.delete = 0"
			)
			->join(
				"t_tagihan_lingkungan",
				"t_tagihan_lingkungan.t_tagihan_id = v_tagihan_lingkungan.t_tagihan_id
					AND t_tagihan_lingkungan.unit_id =  v_tagihan_lingkungan.unit_id
					-- AND t_tagihan_lingkungan.periode =  v_tagihan_lingkungan.periode",
				"LEFT"
			)
			// AND t_tagihan_lingkungan.periode = dateadd(MONTH,-1,v_tagihan_lingkungan.periode)",
			->join(
				"t_pembayaran_detail",
				"t_pembayaran_detail.tagihan_service_id = t_tagihan_lingkungan.id
					AND t_pembayaran_detail.service_id = service.id",
				"LEFT"
			)
			->join(
				"t_pembayaran",
				"t_pembayaran.id = t_pembayaran_detail.t_pembayaran_id
					AND (
						(t_pembayaran.is_void = 0 and v_tagihan_lingkungan.status_tagihan in (0,4))
						or (t_pembayaran.is_void = 1 and v_tagihan_lingkungan.status_tagihan = 0)
						)",
				"LEFT"
			)
			->join(
				"unit_lingkungan",
				"unit_lingkungan.unit_id = v_tagihan_lingkungan.unit_id"
			);
		// ->where("v_tagihan_lingkungan.unit_id = $unit_id")
		if (isset($data->unit_id))
			$tagihan_lingkungan = $tagihan_lingkungan->where_in("v_tagihan_lingkungan.unit_id", $data->unit_id);
		if (isset($data->status_tagihan))
			$tagihan_lingkungan = $tagihan_lingkungan->where_in("v_tagihan_lingkungan.status_tagihan", $data->status_tagihan);
		else
			$tagihan_lingkungan = $tagihan_lingkungan->where("(v_tagihan_lingkungan.status_tagihan = 0 or v_tagihan_lingkungan.status_tagihan = 4  or v_tagihan_lingkungan.status_tagihan = 2 or v_tagihan_lingkungan.status_tagihan = 3)");

		$tagihan_lingkungan = $tagihan_lingkungan
			->distinct()
			->get();
		// var_dump($this->db->last_query());

		// var_dump($this->db->last_query());
		$tagihan_lingkungan = $this->db->from("(" . $this->db->last_query() . ") as a")
			->order_by("periode")
			->get()->result();
		// echo("<pre>");
		// 	print_r($tagihan_lingkungan);
		// echo("</pre>");
		// echo("<pre>");
		// 	print_r($this->db->last_query());
		// echo("</pre>");


		$tagihan_lingkungan_tmp = $tagihan_lingkungan;
		$tagihan_lingkungan = [];
		$view_pemutihan_nilai_tagihan = 0;
		$view_pemutihan_nilai_denda = 0;
		$sisa_nilai_tagihan = 0;
		$sisa_nilai_denda = 0;
		foreach ($tagihan_lingkungan_tmp as $k => $v) {
			// if($v->view_pemutihan_nilai_tagihan > 0 ){
			// 	$v->nilai_tagihan = ($v->total_tanpa_ppn - $v->view_pemutihan_nilai_tagihan) + (($v->total_tanpa_ppn - $v->view_pemutihan_nilai_tagihan) * 10 / 100);
			// }
			if (substr($v->view_pemutihan_nilai_tagihan, -2) == '99')
				$tagihan_lingkungan_tmp[$k]->view_pemutihan_nilai_tagihan++;
			elseif (substr($v->view_pemutihan_nilai_tagihan, -2) == '01')
				$tagihan_lingkungan_tmp[$k]->view_pemutihan_nilai_tagihan--;
			$v->sisa_nilai_tagihan = $v->nilai_tagihan - $v->view_pemutihan_nilai_tagihan;

			// if($v->view_pemutihan_nilai_denda > 0 ){
			// 	$v->nilai_denda = ($v->nilai_denda - $v->view_pemutihan_nilai_denda);
			// }
			$v->sisa_nilai_denda = $v->nilai_denda - $v->view_pemutihan_nilai_denda;

			array_push($tagihan_lingkungan, $v);
		}
		return $tagihan_lingkungan;
	}
	public function layanan_lain($project_id, $data = null)
	{
		$data = (object) $data;
		$tagihan_layanan_lain = $this->db->select("
													t_tagihan_layanan_lain.id as tagihan_id,
													CONCAT('Layanan Lain - <br>',paket_service.name) as service,
													case
														WHEN periode_awal = periode_akhir THEN convert(varchar,periode_awal) 
														ELSE CONCAT(periode_awal,'<br>',periode_akhir) 
													END as periode,
													case
														WHEN periode_awal = periode_akhir THEN convert(varchar,periode_awal) 
														ELSE CONCAT(periode_awal,'<br>',periode_akhir) 
													END as periode_pemakaian,
													1 as is_tagihan,
													0 as nilai_penalti,
													CASE 
														WHEN t_tagihan_layanan_lain.status_tagihan = 0 THEN t_tagihan_layanan_lain.total_nilai - isnull(t_pembayaran_detail.nilai_tagihan,0)
														WHEN t_tagihan_layanan_lain.status_tagihan = 4 THEN 
														0
													END as nilai_tagihan,
													t_tagihan_layanan_lain.total_nilai - isnull(t_pembayaran_detail.nilai_tagihan,0) as total,
													service.id as service_id,
													service.service_jenis_id as service_jenis_id,
													isnull(t_pembayaran_detail.sisa_tagihan,0) as belum_bayar

												", false)
			// t_tagihan_layanan_lain.total_nilai - isnull(t_pembayaran_detail.nilai_tagihan,0) as belum_bayar

			->from('t_tagihan_layanan_lain')
			->join(
				't_layanan_lain_registrasi',
				't_layanan_lain_registrasi.id = t_tagihan_layanan_lain.t_layanan_lain_registrasi_id'
			)
			->join(
				't_layanan_lain_registrasi_detail',
				't_layanan_lain_registrasi_detail.t_layanan_lain_registrasi_id = t_layanan_lain_registrasi.id'
			)
			->join(
				'service',
				'service.id = t_layanan_lain_registrasi_detail.service_id'
			)
			->join(
				'paket_service',
				'paket_service.id = t_layanan_lain_registrasi_detail.paket_service_id'
			)
			->join(
				"t_pembayaran_detail",
				"t_pembayaran_detail.tagihan_service_id = t_tagihan_layanan_lain.id
														AND t_pembayaran_detail.service_id = service.id",
				"LEFT"
			)
			->where_in('t_tagihan_layanan_lain.status_tagihan', [0, 4]);
		if (isset($data->unit_id)) {
			$tagihan_layanan_lain = $tagihan_layanan_lain
				->where_in('t_tagihan_layanan_lain.unit_id', $data->unit_id);
		} else {
			$tagihan_layanan_lain = $tagihan_layanan_lain
				->where_in('t_tagihan_layanan_lain.unit_virtual_id', $data->unit_virtual_id);
		}
		$tagihan_layanan_lain = $tagihan_layanan_lain->get()->result();
		// echo("<pre>");
		// print_r($this->db->last_query());
		// echo("</pre>");
		return $tagihan_layanan_lain;
	}
	public function air($project_id, $data = null)
	{
		$data = (object)$data;
		$periode = isset($data->periode) ? substr($data->periode, 0, 4) . '-' . substr($data->periode, 5, 2) . '-01' : date('Y-m-01');
		$date = isset($data->periode) ? substr($data->periode, 0, 4) . '-' . substr($data->periode, 5, 2) . '-' . substr($data->periode, 8, 2) : date('Y-m-d');
		$day = isset($data->periode) ? substr($data->periode, 8, 2) : date('d');
		$tagihan_air = $this->db->select("
								'Air' as service,
								DATEADD(MONTH, -(service.jarak_periode_penggunaan), v_tagihan_air.periode) as periode_pemakaian,

								CASE
									WHEN v_tagihan_air.periode >= '$periode' THEN 1
									ELSE 0
								END as is_tagihan,
								CASE
									WHEN v_tagihan_air.status_tagihan = 0 
										or v_tagihan_air.status_tagihan = 2 
										or v_tagihan_air.status_tagihan = 3 
										or v_tagihan_air.status_tagihan = 4 THEN
										isnull(CASE
											WHEN DATEADD(MONTH,service.penalti_selisih_bulan,v_tagihan_air.periode) > '$date' THEN 0
											ELSE
												CASE
													WHEN service.penalti_jenis = 1 
														THEN v_tagihan_air.denda_nilai_service 
													WHEN service.penalti_jenis = 3 
														THEN service.penalti_nilai * v_tagihan_air.total_tanpa_ppn / 100
												END 
											END,0) 
										ELSE 0
								END as nilai_penalti,
								v_tagihan_air.total_tanpa_ppn,
								v_tagihan_air.tagihan_id,
								service.id as service_id,
								service.service_jenis_id as service_jenis_id,
								v_tagihan_air.periode,
								v_tagihan_air.status_tagihan,
								CASE 
									WHEN v_tagihan_air.status_tagihan = 4 THEN 
										isnull(v_tagihan_air.total,0) - iif(t_pembayaran.is_void=1,0,t_pembayaran_detail.nilai_tagihan)
									else isnull(v_tagihan_air.total,0)
								END as nilai_tagihan,
								CASE 
									WHEN v_tagihan_air.status_tagihan = 0 or v_tagihan_air.status_tagihan = 2 or v_tagihan_air.status_tagihan = 3 THEN isnull(v_tagihan_air.total,0) 
									ELSE 0 
								END - v_tagihan_air.total_tanpa_ppn
								as ppn,
								CASE 
									WHEN v_tagihan_air.status_tagihan = 0 or v_tagihan_air.status_tagihan = 2 or v_tagihan_air.status_tagihan = 3 THEN
										isnull(CASE
										WHEN service.denda_flag = 0 THEN 0
										WHEN v_tagihan_air.nilai_denda_flag = 1 THEN v_tagihan_air.nilai_denda 
										WHEN DATEADD(MONTH,service.denda_selisih_bulan,v_tagihan_air.periode) > '$date' THEN 0
										WHEN CONCAT(
														SUBSTRING(CONVERT(varchar, v_tagihan_air.periode), 1, 8),
														(
															CASE 
																WHEN (service.denda_tanggal_jt) > 9 
																	THEN CAST(service.denda_tanggal_jt AS VARCHAR) 
																ELSE CONCAT(0,(service.denda_tanggal_jt)) 
															END
														)
													) > '$date' THEN 0
										
											ELSE
											CASE					
												WHEN v_tagihan_air.denda_jenis_service = 1 
													THEN v_tagihan_air.denda_nilai_service *
													CASE (DateDiff
																( MONTH, DATEADD(month,service.denda_selisih_bulan,v_tagihan_air.periode), '$date' ) 
																+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) 
															)
														WHEN 0 THEN 0
														ELSE 1
												END WHEN v_tagihan_air.denda_jenis_service = 2 
													THEN v_tagihan_air.denda_nilai_service
														* (DateDiff( MONTH, DATEADD(month,service.denda_selisih_bulan,v_tagihan_air.periode), '$date' ) 
														+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) )
												WHEN v_tagihan_air.denda_jenis_service = 3 
													THEN 
														(v_tagihan_air.denda_nilai_service* v_tagihan_air.total_tanpa_ppn/ 100 ) 
														* (DateDiff( MONTH, DATEADD(month,service.denda_selisih_bulan,v_tagihan_air.periode), '$date' ) 
														+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) )
											END 
										END,0) 
									ELSE 0
								END
								-
								CASE 
									WHEN v_tagihan_air.status_tagihan = 4 THEN isnull(iif(t_pembayaran.is_void=1,0,t_pembayaran_detail.nilai_denda),0)
									else 0
								END
								AS nilai_denda,
								isnull(v_tagihan_air.total,0)+
								isnull(CASE
									WHEN service.denda_flag = 0 THEN 0
									WHEN v_tagihan_air.nilai_denda_flag = 1 THEN v_tagihan_air.nilai_denda 
									WHEN DATEADD(MONTH,service.denda_selisih_bulan,v_tagihan_air.periode) > '$date' THEN 0
									WHEN CONCAT(
													SUBSTRING(CONVERT(varchar, v_tagihan_air.periode), 1, 8),
													(
														CASE 
															WHEN (service.denda_tanggal_jt) > 9 
																THEN CAST(service.denda_tanggal_jt AS VARCHAR) 
															ELSE CONCAT(0,(service.denda_tanggal_jt)) 
														END
													)
												) > '$date' THEN 0
									ELSE
									CASE
										WHEN v_tagihan_air.denda_jenis_service = 1 
											THEN v_tagihan_air.denda_nilai_service *
												CASE (DateDiff
															( MONTH, DATEADD(month,service.denda_selisih_bulan,v_tagihan_air.periode), '$date' ) 
															+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) 
														)
													WHEN 0 THEN 0
													ELSE 1
												END
										WHEN v_tagihan_air.denda_jenis_service = 2 
											THEN v_tagihan_air.denda_nilai_service *
												(DateDiff
													( MONTH, DATEADD(month,service.denda_selisih_bulan,v_tagihan_air.periode), '$date' ) 
													+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) 
												)
										WHEN v_tagihan_air.denda_jenis_service = 3 
											THEN (v_tagihan_air.denda_nilai_service * v_tagihan_air.total_tanpa_ppn/ 100 ) 
											* (DateDiff( MONTH, v_tagihan_air.periode, '$date' ) 
											+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) )
									END 
								END,0) 
								- isnull(v_pemutihan.pemutihan_nilai_tagihan,0)
								- isnull(v_pemutihan.pemutihan_nilai_denda,0)
								AS total,
								CONVERT(INT,
									CASE v_tagihan_air.ppn_flag
										WHEN 1 THEN 
											isnull(
												round(v_pemutihan.pemutihan_nilai_tagihan*(1.0+(v_tagihan_air.nilai_ppn/100.0)),0)
												,0)
										ELSE isnull(v_pemutihan.pemutihan_nilai_tagihan,0)
									END
								) as view_pemutihan_nilai_tagihan,
								isnull(v_pemutihan.pemutihan_nilai_denda,0) as view_pemutihan_nilai_denda,
								isnull(iif(t_pembayaran.is_void=1,0,t_pembayaran_detail.sisa_tagihan),0) as belum_bayar,
								meter_awal,
								meter_akhir")
			->from("v_tagihan_air")
			// ->where("v_tagihan_air.periode <= '$periode_now'")
			->join(
				"service",
				"service.project_id = $project_id
				AND service.service_jenis_id = 2
				AND service.active = 1
				AND service.delete = 0"
			)
			->join(
				"v_pemutihan",
				"v_pemutihan.masa_akhir >= '$date'
					AND v_pemutihan.masa_awal <= '$date'
					AND v_pemutihan.periode = v_tagihan_air.periode 
					AND v_pemutihan.service_jenis_id = 2
					AND v_pemutihan.unit_id  = v_tagihan_air.unit_id",
				"LEFT"
			)
			->join(
				"t_tagihan_air",
				"t_tagihan_air.t_tagihan_id = v_tagihan_air.t_tagihan_id
					AND t_tagihan_air.unit_id =  v_tagihan_air.unit_id
					-- AND t_tagihan_air.periode =  v_tagihan_air.periode",
				"LEFT"
			)
			->join(
				"t_pencatatan_meter_air",
				"t_pencatatan_meter_air.unit_id = v_tagihan_air.unit_id
					AND t_pencatatan_meter_air.periode = v_tagihan_air.periode"
			)
			->join(
				"t_pembayaran_detail",
				"t_pembayaran_detail.tagihan_service_id = t_tagihan_air.id
					AND t_pembayaran_detail.service_id = service.id",
				"LEFT"
			)
			->join(
				"t_pembayaran",
				"t_pembayaran.id = t_pembayaran_detail.t_pembayaran_id
					AND (
						(t_pembayaran.is_void = 0 and v_tagihan_air.status_tagihan in (0,4))
						or (t_pembayaran.is_void = 1 and v_tagihan_air.status_tagihan = 0)
						)",
				"LEFT"
			);
		if (isset($data->unit_id))
			$tagihan_air = $tagihan_air->where_in("v_tagihan_air.unit_id", $data->unit_id);
		if (isset($data->status_tagihan))
			$tagihan_air = $tagihan_air->where_in("v_tagihan_air.status_tagihan", $data->status_tagihan);
		else
			$tagihan_air = $tagihan_air->where("(v_tagihan_air.status_tagihan = 0 or v_tagihan_air.status_tagihan = 4  or v_tagihan_air.status_tagihan = 2 or v_tagihan_air.status_tagihan = 3)");

		$tagihan_air = $tagihan_air
			->distinct()
			->get();

		// var_dump($this->db->last_query());
		$tagihan_air = $this->db->from("(" . $this->db->last_query() . ") as a")
			->order_by("periode")
			->get()->result();

		$tagihan_air_tmp = $tagihan_air;
		$tagihan_air = [];
		$view_pemutihan_nilai_tagihan = 0;
		$view_pemutihan_nilai_denda = 0;
		$sisa_nilai_tagihan = 0;
		$sisa_nilai_denda = 0;
		foreach ($tagihan_air_tmp as $k => $v) {
			// if($v->view_pemutihan_nilai_tagihan > 0 ){
			// 	$v->nilai_tagihan = ($v->total_tanpa_ppn - $v->view_pemutihan_nilai_tagihan) + (($v->total_tanpa_ppn - $v->view_pemutihan_nilai_tagihan) * 10 / 100);
			// }
			$v->sisa_nilai_tagihan = $v->nilai_tagihan - $v->view_pemutihan_nilai_tagihan;

			// if($v->view_pemutihan_nilai_denda > 0 ){
			// 	$v->nilai_denda = ($v->nilai_denda - $v->view_pemutihan_nilai_denda);
			// }
			$v->sisa_nilai_denda = $v->nilai_denda - $v->view_pemutihan_nilai_denda;

			array_push($tagihan_air, $v);
		}
		return $tagihan_air;
	}
	public function air_tanpa_potong_pemutihan($project_id, $data = null)
	{
		$data = (object)$data;
		$periode = isset($data->periode) ? substr($data->periode, 0, 4) . '-' . substr($data->periode, 5, 2) . '-01' : date('Y-m-01');
		$date = isset($data->periode) ? substr($data->periode, 0, 4) . '-' . substr($data->periode, 5, 2) . '-' . substr($data->periode, 8, 2) : date('Y-m-d');
		$day = isset($data->periode) ? substr($data->periode, 8, 2) : date('d');
		$tagihan_air = $this->db->select("
								'Air' as service,
								DATEADD(MONTH, -(service.jarak_periode_penggunaan), v_tagihan_air.periode) as periode_pemakaian,

								CASE
									WHEN v_tagihan_air.periode >= '$periode' THEN 1
									ELSE 0
								END as is_tagihan,
								CASE
									WHEN v_tagihan_air.status_tagihan = 0 
										or v_tagihan_air.status_tagihan = 2 
										or v_tagihan_air.status_tagihan = 3 
										or v_tagihan_air.status_tagihan = 4 THEN
										isnull(CASE
											WHEN DATEADD(MONTH,service.penalti_selisih_bulan,v_tagihan_air.periode) > '$date' THEN 0
											ELSE
												CASE
													WHEN service.penalti_jenis = 1 
														THEN v_tagihan_air.denda_nilai_service 
													WHEN service.penalti_jenis = 3 
														THEN service.penalti_nilai * v_tagihan_air.total_tanpa_ppn / 100
												END 
											END,0) 
										ELSE 0
								END as nilai_penalti,
								v_tagihan_air.total_tanpa_ppn,
								v_tagihan_air.tagihan_id,
								service.id as service_id,
								service.service_jenis_id as service_jenis_id,
								v_tagihan_air.periode,
								v_tagihan_air.status_tagihan,
								CASE 
									WHEN v_tagihan_air.status_tagihan = 4 THEN 
										isnull(v_tagihan_air.total,0) - iif(t_pembayaran.is_void=1,0,t_pembayaran_detail.nilai_tagihan)
									else isnull(v_tagihan_air.total,0)
								END as nilai_tagihan,
								CASE 
									WHEN v_tagihan_air.status_tagihan = 0 or v_tagihan_air.status_tagihan = 2 or v_tagihan_air.status_tagihan = 3 THEN isnull(v_tagihan_air.total,0) 
									ELSE 0 
								END - v_tagihan_air.total_tanpa_ppn
								as ppn,
								CASE 
									WHEN v_tagihan_air.status_tagihan = 0 or v_tagihan_air.status_tagihan = 2 or v_tagihan_air.status_tagihan = 3 THEN
										isnull(CASE
										WHEN service.denda_flag = 0 THEN 0
										WHEN v_tagihan_air.nilai_denda_flag = 1 THEN v_tagihan_air.nilai_denda 
										WHEN DATEADD(MONTH,service.denda_selisih_bulan,v_tagihan_air.periode) > '$date' THEN 0
										WHEN CONCAT(
														SUBSTRING(CONVERT(varchar, v_tagihan_air.periode), 1, 8),
														(
															CASE 
																WHEN (service.denda_tanggal_jt) > 9 
																	THEN CAST(service.denda_tanggal_jt AS VARCHAR) 
																ELSE CONCAT(0,(service.denda_tanggal_jt)) 
															END
														)
													) > '$date' THEN 0
										
											ELSE
											CASE					
												WHEN v_tagihan_air.denda_jenis_service = 1 
													THEN v_tagihan_air.denda_nilai_service *
													CASE (DateDiff
																( MONTH, DATEADD(month,service.denda_selisih_bulan,v_tagihan_air.periode), '$date' ) 
																+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) 
															)
														WHEN 0 THEN 0
														ELSE 1
												END WHEN v_tagihan_air.denda_jenis_service = 2 
													THEN v_tagihan_air.denda_nilai_service
														* (DateDiff( MONTH, DATEADD(month,service.denda_selisih_bulan,v_tagihan_air.periode), '$date' ) 
														+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) )
												WHEN v_tagihan_air.denda_jenis_service = 3 
													THEN 
														(v_tagihan_air.denda_nilai_service* v_tagihan_air.total_tanpa_ppn/ 100 ) 
														* (DateDiff( MONTH, DATEADD(month,service.denda_selisih_bulan,v_tagihan_air.periode), '$date' ) 
														+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) )
											END 
										END,0) 
									ELSE 0
								END
								-
								CASE 
									WHEN v_tagihan_air.status_tagihan = 4 THEN isnull(iif(t_pembayaran.is_void=1,0,t_pembayaran_detail.nilai_denda),0)
									else 0
								END
								AS nilai_denda,
								isnull(v_tagihan_air.total,0)+
								isnull(CASE
									WHEN service.denda_flag = 0 THEN 0
									WHEN v_tagihan_air.nilai_denda_flag = 1 THEN v_tagihan_air.nilai_denda 
									WHEN DATEADD(MONTH,service.denda_selisih_bulan,v_tagihan_air.periode) > '$date' THEN 0
									WHEN CONCAT(
													SUBSTRING(CONVERT(varchar, v_tagihan_air.periode), 1, 8),
													(
														CASE 
															WHEN (service.denda_tanggal_jt) > 9 
																THEN CAST(service.denda_tanggal_jt AS VARCHAR) 
															ELSE CONCAT(0,(service.denda_tanggal_jt)) 
														END
													)
												) > '$date' THEN 0
									ELSE
									CASE
										WHEN v_tagihan_air.denda_jenis_service = 1 
											THEN v_tagihan_air.denda_nilai_service *
												CASE (DateDiff
															( MONTH, DATEADD(month,service.denda_selisih_bulan,v_tagihan_air.periode), '$date' ) 
															+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) 
														)
													WHEN 0 THEN 0
													ELSE 1
												END
										WHEN v_tagihan_air.denda_jenis_service = 2 
											THEN v_tagihan_air.denda_nilai_service *
												(DateDiff
													( MONTH, DATEADD(month,service.denda_selisih_bulan,v_tagihan_air.periode), '$date' ) 
													+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) 
												)
										WHEN v_tagihan_air.denda_jenis_service = 3 
											THEN (v_tagihan_air.denda_nilai_service * v_tagihan_air.total_tanpa_ppn/ 100 ) 
											* (DateDiff( MONTH, v_tagihan_air.periode, '$date' ) 
											+ IIF(" . $day . ">=service.denda_tanggal_jt,1,0) )
									END 
								END,0) 
								AS total,
								CONVERT(INT,0) as view_pemutihan_nilai_tagihan,
								isnull(0,0) as view_pemutihan_nilai_denda,
								isnull(iif(t_pembayaran.is_void=1,0,t_pembayaran_detail.sisa_tagihan),0) as belum_bayar,
								meter_awal,
								meter_akhir")
			->from("v_tagihan_air")
			// ->where("v_tagihan_air.periode <= '$periode_now'")
			->join(
				"service",
				"service.project_id = $project_id
				AND service.service_jenis_id = 2
				AND service.active = 1
				AND service.delete = 0"
			)
			->join(
				"t_tagihan_air",
				"t_tagihan_air.t_tagihan_id = v_tagihan_air.t_tagihan_id
					AND t_tagihan_air.unit_id =  v_tagihan_air.unit_id
					-- AND t_tagihan_air.periode =  v_tagihan_air.periode",
				"LEFT"
			)
			->join(
				"t_pencatatan_meter_air",
				"t_pencatatan_meter_air.unit_id = v_tagihan_air.unit_id
					AND t_pencatatan_meter_air.periode = v_tagihan_air.periode"
			)
			->join(
				"t_pembayaran_detail",
				"t_pembayaran_detail.tagihan_service_id = t_tagihan_air.id
					AND t_pembayaran_detail.service_id = service.id",
				"LEFT"
			)
			->join(
				"t_pembayaran",
				"t_pembayaran.id = t_pembayaran_detail.t_pembayaran_id
					AND (
						(t_pembayaran.is_void = 0 and v_tagihan_air.status_tagihan in (0,4))
						or (t_pembayaran.is_void = 1 and v_tagihan_air.status_tagihan = 0)
						)",
				"LEFT"
			);
		if (isset($data->unit_id))
			$tagihan_air = $tagihan_air->where_in("v_tagihan_air.unit_id", $data->unit_id);
		if (isset($data->status_tagihan))
			$tagihan_air = $tagihan_air->where_in("v_tagihan_air.status_tagihan", $data->status_tagihan);
		else
			$tagihan_air = $tagihan_air->where("(v_tagihan_air.status_tagihan = 0 or v_tagihan_air.status_tagihan = 4  or v_tagihan_air.status_tagihan = 2 or v_tagihan_air.status_tagihan = 3)");

		$tagihan_air = $tagihan_air
			->distinct()
			->get();

		// var_dump($this->db->last_query());
		$tagihan_air = $this->db->from("(" . $this->db->last_query() . ") as a")
			->order_by("periode")
			->get()->result();

		$tagihan_air_tmp = $tagihan_air;
		$tagihan_air = [];
		$view_pemutihan_nilai_tagihan = 0;
		$view_pemutihan_nilai_denda = 0;
		$sisa_nilai_tagihan = 0;
		$sisa_nilai_denda = 0;
		foreach ($tagihan_air_tmp as $k => $v) {
			// if($v->view_pemutihan_nilai_tagihan > 0 ){
			// 	$v->nilai_tagihan = ($v->total_tanpa_ppn - $v->view_pemutihan_nilai_tagihan) + (($v->total_tanpa_ppn - $v->view_pemutihan_nilai_tagihan) * 10 / 100);
			// }
			$v->sisa_nilai_tagihan = $v->nilai_tagihan - $v->view_pemutihan_nilai_tagihan;

			// if($v->view_pemutihan_nilai_denda > 0 ){
			// 	$v->nilai_denda = ($v->nilai_denda - $v->view_pemutihan_nilai_denda);
			// }
			$v->sisa_nilai_denda = $v->nilai_denda - $v->view_pemutihan_nilai_denda;

			array_push($tagihan_air, $v);
		}
		return $tagihan_air;
	}
	public function get_lingkungan($param = [])
	{
		// echo ('<pre>');
		// print_r($param);
		// echo ('</pre>');
		$param = (object)$param;
		// $periode = isset($periode) ? substr($periode, 0, 4) . '-' . substr($periode, 5, 2) . '-01' : date('Y-m-01');
		$date = $param->date ?? date("Y-m-d");

		// $date = isset($periode) ? substr($periode, 0, 4) . '-' . substr($periode, 5, 2) . '-' . substr($periode, 8, 2) : date('Y-m-d');
		$day = isset($date) ? substr($date, 8, 2) : date('d');

		// echo ('<pre>');
		// print_r($param);
		// echo ('</pre>');
		$tagihans = $this->db->select("
									t_tagihan_lingkungan.id,
									'Lingkungan' as service,
									t_tagihan_lingkungan.periode,
									DATEADD(MONTH, - (service.jarak_periode_penggunaan), t_tagihan_lingkungan.periode) as periode_pemakaian,
									unit.project_id,
									unit.id as unit_id,
									unit.no_unit,
									blok.name AS blok,
									kawasan.name AS kawasan,
									pemilik.name AS pemilik,
									penghuni.name AS penghuni,
									t_tagihan_lingkungan_detail.nilai_bangunan,
									t_tagihan_lingkungan_detail.nilai_kavling,
									t_tagihan_lingkungan_detail.nilai_administrasi,
									t_tagihan_lingkungan_detail.nilai_keamanan,
									t_tagihan_lingkungan_detail.nilai_kebersihan,
									t_tagihan_lingkungan_detail.nilai_denda,
									t_tagihan_lingkungan_detail.nilai_ppn,
									t_tagihan_lingkungan_detail.nilai_bangunan_flag,
									t_tagihan_lingkungan_detail.nilai_kavling_flag,
									t_tagihan_lingkungan_detail.nilai_denda_flag,
									t_tagihan_lingkungan_detail.ppn_flag,
									t_tagihan_lingkungan_detail.ppn_sc_flag,
									t_tagihan_lingkungan_info.denda_nilai_service,
									t_tagihan_lingkungan_info.denda_jenis_service,
									t_tagihan_lingkungan_info.penalti_nilai_service,
									t_tagihan_lingkungan_info.penalti_jenis_service,
									count(pemutihan_unit.pemutihan_nilai_tagihan) AS pemutihan_nilai_tagihan,
									count(pemutihan_unit.pemutihan_nilai_denda) AS pemutihan_nilai_denda,
									unit_lingkungan.tgl_mulai_denda,
									DATEADD(DAY,service.denda_tanggal_jt-1,DATEADD(MONTH,service.denda_selisih_bulan,t_tagihan_lingkungan.periode)) as duedate,
									t_tagihan_lingkungan.status_tagihan,
									service.penalti_selisih_bulan,
									DATEADD(MONTH,service.penalti_selisih_bulan,t_tagihan_lingkungan.periode) as duedatepenalti,
									service.penalti_nilai,
									service.id as service_id,
									t_tagihan_lingkungan.t_tagihan_id as external_id
								", false)
			->from("dbo.t_tagihan_lingkungan")
			// ->distinct()
			->JOIN(
				"dbo.t_tagihan_lingkungan_detail",
				"t_tagihan_lingkungan.id = t_tagihan_lingkungan_detail.t_tagihan_lingkungan_id"
			)
			->JOIN(
				"dbo.t_tagihan_lingkungan_info",
				"t_tagihan_lingkungan.id = t_tagihan_lingkungan_info.t_tagihan_lingkungan_id"
			)
			->JOIN(
				"dbo.unit",
				"t_tagihan_lingkungan.unit_id = unit.id"
			)
			->JOIN(
				"dbo.unit_lingkungan",
				"unit_lingkungan.unit_id = unit.id"
			)
			->JOIN(
				"dbo.service",
				"service.project_id = unit.project_id 
								AND service.service_jenis_id = 1
								AND service.delete = 0
								AND service.active = 1
								"
			)
			->JOIN(
				"dbo.blok",
				"unit.blok_id = blok.id"
			)
			->JOIN(
				"dbo.kawasan",
				"blok.kawasan_id = kawasan.id"
			)
			->JOIN(
				"dbo.customer AS pemilik",
				"unit.pemilik_customer_id = pemilik.id",
				"LEFT"
			)
			->JOIN(
				"dbo.customer AS penghuni",
				"unit.penghuni_customer_id = penghuni.id",
				"LEFT"
			)
			->JOIN(
				"dbo.pemutihan_unit",
				"unit.id = pemutihan_unit.unit_id 
								AND pemutihan_unit.service_jenis_id = 1
								AND pemutihan_unit.periode = t_tagihan_lingkungan.periode",
				"LEFT"
			)
			->JOIN(
				"dbo.pemutihan",
				"pemutihan_unit.pemutihan_id = pemutihan.id
								AND pemutihan.masa_awal >= '$date'
								AND pemutihan.masa_akhir <= '$date'",
				"LEFT"
			)
			->JOIN(
				"dbo.approval",
				"approval.dokumen_id = pemutihan.id
								AND approval.dokumen_jenis_id = 1
								AND approval.approval_status_id = 1",
				"LEFT"
			)
			->WHERE("dbo.unit.active",1);

		if (isset($param->unit_id)) {
			$tagihans = $tagihans->WHERE_IN("dbo.unit.id", $param->unit_id);
		}
		if (isset($param->blok_id)) {
			$tagihans = $tagihans->WHERE_IN("dbo.blok.id", $param->blok_id);
		}
		if (isset($param->kawasan_id)) {
			$tagihans = $tagihans->WHERE_IN("dbo.kawasan.id", $param->kawasan_id);
		}
		if (isset($param->project_id)) {
			$tagihans = $tagihans->WHERE_IN("dbo.unit.project_id", $param->project_id);
		}
		if (isset($param->status_tagihan)) {
			$tagihans = $tagihans->WHERE_IN("dbo.t_tagihan_lingkungan.status_tagihan", $param->status_tagihan);
		} else {
			$tagihans = $tagihans->WHERE_IN("dbo.t_tagihan_lingkungan.status_tagihan", [0, 2, 3, 4]);
		}
		if (isset($param->periode_awal) && isset($param->periode_akhir)) {

			$tagihans = $tagihans->where("dbo.t_tagihan_lingkungan.periode >= '$param->periode_awal'");
			$tagihans = $tagihans->where("dbo.t_tagihan_lingkungan.periode <= '$param->periode_akhir'");
		}

		$tagihans = $tagihans->group_by("
				t_tagihan_lingkungan.id,
				t_tagihan_lingkungan.periode,
				unit.project_id,
				unit.id,
				unit.no_unit,
				blok.name,
				kawasan.name,
				pemilik.name,
				penghuni.name,
				t_tagihan_lingkungan_detail.nilai_bangunan,
				t_tagihan_lingkungan_detail.nilai_kavling,
				t_tagihan_lingkungan_detail.nilai_administrasi,
				t_tagihan_lingkungan_detail.nilai_keamanan,
				t_tagihan_lingkungan_detail.nilai_kebersihan,
				t_tagihan_lingkungan_detail.nilai_denda,
				t_tagihan_lingkungan_detail.nilai_ppn,
				t_tagihan_lingkungan_detail.nilai_bangunan_flag,
				t_tagihan_lingkungan_detail.nilai_kavling_flag,
				t_tagihan_lingkungan_detail.nilai_denda_flag,
				t_tagihan_lingkungan_detail.ppn_flag,
				t_tagihan_lingkungan_detail.ppn_sc_flag,
				t_tagihan_lingkungan_info.denda_nilai_service,
				t_tagihan_lingkungan_info.denda_jenis_service,
				t_tagihan_lingkungan_info.penalti_nilai_service,
				t_tagihan_lingkungan_info.penalti_jenis_service,
				unit_lingkungan.tgl_mulai_denda,
				t_tagihan_lingkungan.status_tagihan,
				service.penalti_selisih_bulan,
				service.penalti_nilai,
				service.id,
				t_tagihan_lingkungan.t_tagihan_id,
				service.jarak_periode_penggunaan,
				service.denda_tanggal_jt,
				service.denda_selisih_bulan
			");
		$tagihans = $tagihans->ORDER_BY("periode")->get()->result();
		// print_r($this->db->last_query());
		// echo ("lingkungans<pre>");
		// print_r($tagihans);
		// echo ("</pre>");

		foreach ($tagihans as $iterasi => $tagihan) {
			// total tanpa ppn
			$tagihan->nilai_tagihan_tanpa_ppn = $tagihan->nilai_bangunan + $tagihan->nilai_kavling + $tagihan->nilai_keamanan + $tagihan->nilai_kebersihan;

			// ppn (ppn adalah ppn dalam bentuk rupiah, sedangkan nilai ppn adalah ppn dalam bentuk %)
			$tagihan->ppn = 0;
			$tagihan->ppn = round($tagihan->nilai_tagihan_tanpa_ppn * ($tagihan->ppn_flag * $tagihan->nilai_ppn) / 100);

			//nilai_tagihan (dengan ppn)
			$tagihan->nilai_tagihan = $tagihan->nilai_tagihan_tanpa_ppn + $tagihan->ppn + $tagihan->nilai_administrasi;

			//nilai denda
			$tagihan->nilai_denda = 0;
			if ($tagihan->status_tagihan != 1) {
				if ($tagihan->periode <= ($tagihan->tgl_mulai_denda == null ? '0000-00-00' : $tagihan->tgl_mulai_denda) || $date < $tagihan->duedate) {
					// print_r('1');
					$tagihan->nilai_denda = 0;
				} elseif ($tagihan->nilai_denda_flag == 1) {
					// print_r('2');
					$tagihan->nilai_denda = $tagihan->nilai_denda;
				} else {
					// print_r('3');
					if ($tagihan->denda_jenis_service == 1) {
						// print_r('4');
						$tagihan->nilai_denda = $tagihan->denda_nilai_service;
					} elseif ($tagihan->denda_jenis_service == 2 || $tagihan->denda_jenis_service == 3) {
						// print_r('5');
						// $tmp_bulan_telat = $this->dateDifference($tagihan->duedate, $date, '%y,%m,%d');
						$tmp_bulan_telat =
							$this->dateDifference(
								$tagihan->duedate,
								date('Y-m-d', strtotime('+1 day', strtotime($date))),
								'%y,%m,%d'
							);
						$tmp_bulan_telat = explode(',', $tmp_bulan_telat);

						$bulan_telat = ($tmp_bulan_telat[0] * 12) + ($tmp_bulan_telat[1]) + ($tmp_bulan_telat[2] > 0 ? 1 : 0);

						if ($tagihan->denda_jenis_service == 2) {
							// print_r('8');
							$tagihan->nilai_denda = $tagihan->denda_nilai_service * $bulan_telat;
						} elseif ($tagihan->denda_jenis_service == 3) {
							// print_r('9');
							$tagihan->nilai_denda = round($bulan_telat * ($tagihan->nilai_tagihan_tanpa_ppn * $tagihan->denda_nilai_service / 100));
						}
					}
				}
			}

			//nilai penalti
			$tagihan->nilai_penalti = 0;
			if ($tagihan->status_tagihan != 1) {
				if ($date < $tagihan->duedatepenalti) {
					$tagihan->nilai_penalti = 0;
				} else {
					if ($tagihan->denda_jenis_service == 1) {
						$tagihan->nilai_penalti = $tagihan->penalti_nilai;
					} elseif ($tagihan->denda_jenis_service == 3) {
						$tagihan->nilai_penalti = $tagihan->penalti_nilai * $tagihan->nilai_tagihan_tanpa_ppn / 100;
					}
				}
			}
			$tagihan->total =
				$tagihan->nilai_tagihan + $tagihan->nilai_denda + $tagihan->nilai_penalti - $tagihan->pemutihan_nilai_tagihan - $tagihan->pemutihan_nilai_denda;
			if ($tagihan->status_tagihan == 4) {
				$pembayaran = (object)['dibayar' => 0];
				$pembayaran = $this->db->select("isnull(t_pembayaran_detail.bayar,t_pembayaran_detail.bayar_deposit) as dibayar")
					->from("t_pembayaran")
					->join(
						"t_pembayaran_detail",
						"t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id"
					)
					->WHERE("t_pembayaran.unit_id", $tagihan->unit_id)
					->WHERE("t_pembayaran_detail.tagihan_service_id", $tagihan->id)
					->WHERE("t_pembayaran_detail.service_id", $tagihan->service_id)
					->get()->row();
				if (isset($pembayaran->dibayar)) {
					if ($pembayaran->dibayar > 0) {
						$dibayar = $pembayaran->dibayar;
						$tagihan->dibayar = $dibayar;
						do {
							$berkurang = 0;
							$berkurang = $tagihan->nilai_penalti;
							if ($tagihan->nilai_penalti >= $dibayar) {
								$tagihan->final_nilai_penalti = $tagihan->nilai_penalti - $dibayar;
								$dibayar = 0;
							} else {
								$dibayar = $dibayar - $tagihan->nilai_penalti;
								$tagihan->final_nilai_penalti = 0;
							}
							if ($tagihan->nilai_denda >= $dibayar) {
								$tagihan->final_nilai_denda = $tagihan->nilai_denda - $dibayar;
								$dibayar = 0;
							} else {
								$dibayar = $dibayar - $tagihan->nilai_denda;
								$tagihan->final_nilai_denda = 0;
							}
							if ($tagihan->nilai_tagihan >= $dibayar) {
								$tagihan->final_nilai_tagihan = $tagihan->nilai_tagihan - $dibayar;
								$dibayar = 0;
							} else {
								$dibayar = $dibayar - $tagihan->nilai_tagihan;
								$tagihan->final_nilai_tagihan = 0;
							}
						} while ($dibayar > 0);
					}
				}
			} else {
				$tagihan->dibayar = 0;
				$tagihan->final_nilai_tagihan = $tagihan->nilai_tagihan;
				$tagihan->final_nilai_denda = $tagihan->nilai_denda;
				$tagihan->final_nilai_penalti = $tagihan->nilai_penalti;
				$tagihan->final_nilai_tagihan_tanpa_ppn = $tagihan->nilai_tagihan_tanpa_ppn;
				$tagihan->final_ppn = $tagihan->ppn;
			}
			$tagihan->final_nilai_tagihan_tanpa_ppn = round($tagihan->final_nilai_tagihan / (1 + ($tagihan->nilai_ppn / 100)));
			$tagihan->final_ppn = $tagihan->final_nilai_tagihan - $tagihan->final_nilai_tagihan_tanpa_ppn;

			$tagihan->final_total =
				$tagihan->final_nilai_tagihan + $tagihan->final_nilai_denda + $tagihan->final_nilai_penalti - $tagihan->pemutihan_nilai_tagihan - $tagihan->pemutihan_nilai_denda;
		}
		return $tagihans;
	}
	public function get_air($param = [])
	{
		$param = (object)$param;

		$date = $param->date ? $param->date : date("Y-m-d");

		$day = isset($date) ? substr($date, 8, 2) : date('d');

		$tagihans = $this->db->select("
									t_tagihan_air.id,
									'air' AS service,
									t_tagihan_air.periode,
									DATEADD( MONTH, - ( service.jarak_periode_penggunaan ), t_tagihan_air.periode ) AS periode_pemakaian,
									unit.project_id,
									unit.id AS unit_id,
									unit.no_unit,
									blok.name AS blok,
									kawasan.name AS kawasan,
									pemilik.name AS pemilik,
									penghuni.name AS penghuni,
									count(pemutihan_unit.pemutihan_nilai_tagihan) AS pemutihan_nilai_tagihan,
									count(pemutihan_unit.pemutihan_nilai_denda) AS pemutihan_nilai_denda,
									DATEADD( DAY, service.denda_tanggal_jt- 1, DATEADD( MONTH, service.denda_selisih_bulan, t_tagihan_air.periode ) ) AS duedate,
									t_tagihan_air.status_tagihan,
									service.penalti_selisih_bulan,
									DATEADD( MONTH, service.penalti_selisih_bulan, t_tagihan_air.periode ) AS duedatepenalti,
									service.penalti_nilai,
									service.id AS service_id,
									t_tagihan_air_detail.nilai_denda,
									t_tagihan_air_detail.nilai_ppn,
									t_tagihan_air_detail.nilai_denda_flag,
									t_tagihan_air_detail.nilai_flag,
									t_tagihan_air_detail.ppn_flag,
									t_tagihan_air_detail.nilai_administrasi,
									t_tagihan_air_detail.nilai_pemeliharaan,
									t_tagihan_air_detail.nilai,
									t_tagihan_air_info.denda_nilai_service,
									t_tagihan_air_info.denda_jenis_service,
									t_tagihan_air.t_tagihan_id as external_id
								", false)
			->from("dbo.t_tagihan_air")
			// ->distinct()

			->JOIN(
				"dbo.t_tagihan_air_detail",
				"t_tagihan_air.id = t_tagihan_air_detail.t_tagihan_air_id"
			)
			->JOIN(
				"dbo.t_tagihan_air_info",
				"t_tagihan_air.id = t_tagihan_air_info.t_tagihan_air_id"
			)
			->JOIN(
				"dbo.unit",
				"t_tagihan_air.unit_id = unit.id"
			)
			// ->JOIN(
			// 	"dbo.unit_air",
			// 	"unit_air.unit_id = unit.id"
			// )
			->JOIN(
				"dbo.blok",
				"unit.blok_id = blok.id"
			)
			->JOIN(
				"dbo.kawasan",
				"blok.kawasan_id = kawasan.id"
			)
			->JOIN(
				"dbo.customer AS pemilik",
				"unit.pemilik_customer_id = pemilik.id",
				"LEFT"
			)
			->JOIN(
				"dbo.customer AS penghuni",
				"unit.penghuni_customer_id = penghuni.id",
				"LEFT"
			)
			->JOIN(
				"dbo.pemutihan_unit",
				"unit.id = pemutihan_unit.unit_id 
								AND pemutihan_unit.service_jenis_id = 2
								AND pemutihan_unit.periode = t_tagihan_air.periode",
				"LEFT"
			)
			->JOIN(
				"dbo.pemutihan",
				"pemutihan_unit.pemutihan_id = pemutihan.id
								AND pemutihan.masa_awal >= '$date'
								AND pemutihan.masa_akhir <= '$date'",
				"LEFT"
			)
			->JOIN(
				"dbo.service",
				"service.project_id = unit.project_id 
								AND service.service_jenis_id = 2
								AND service.delete = 0
								AND service.active = 1",
				"LEFT"
			)
			->JOIN(
				"dbo.approval",
				"approval.dokumen_id = pemutihan.id
								AND approval.dokumen_jenis_id = 1
								AND approval.approval_status_id = 1",
				"LEFT"
			)
			->WHERE("dbo.unit.active",1);
		if (isset($param->unit_id)) {
			$tagihans = $tagihans->WHERE_IN("dbo.unit.id", $param->unit_id);
		}
		if (isset($param->blok_id)) {
			$tagihans = $tagihans->WHERE_IN("dbo.blok.id", $param->blok_id);
		}
		if (isset($param->kawasan_id)) {
			$tagihans = $tagihans->WHERE_IN("dbo.kawasan.id", $param->kawasan_id);
		}
		if (isset($param->project_id)) {
			$tagihans = $tagihans->WHERE_IN("dbo.unit.project_id", $param->project_id);
		}
		if (isset($param->status_tagihan)) {
			$tagihans = $tagihans->WHERE_IN("dbo.t_tagihan_air.status_tagihan", $param->status_tagihan);
		} else {
			$tagihans = $tagihans->WHERE_IN("dbo.t_tagihan_air.status_tagihan", [0, 2, 3, 4]);
		}
		if (isset($param->periode_awal) && isset($param->periode_akhir)) {

			$tagihans = $tagihans->where("dbo.t_tagihan_air.periode >= '$param->periode_awal'");
			$tagihans = $tagihans->where("dbo.t_tagihan_air.periode <= '$param->periode_akhir'");
		}

		$tagihans = $tagihans->group_by("
				t_tagihan_air.id,
				t_tagihan_air.periode,
				DATEADD( MONTH, - ( service.jarak_periode_penggunaan ), t_tagihan_air.periode ),
				unit.project_id,
				unit.id,
				unit.no_unit,
				blok.name,
				kawasan.name,
				pemilik.name,
				penghuni.name,
				pemutihan_unit.pemutihan_nilai_tagihan,
				pemutihan_unit.pemutihan_nilai_denda,
				service.denda_tanggal_jt,
				service.denda_selisih_bulan,
				t_tagihan_air.periode,
				t_tagihan_air.status_tagihan,
				service.penalti_selisih_bulan,
				service.penalti_nilai,
				service.id,
				t_tagihan_air_detail.nilai_denda,
				t_tagihan_air_detail.nilai_ppn,
				t_tagihan_air_detail.nilai_denda_flag,
				t_tagihan_air_detail.nilai_flag,
				t_tagihan_air_detail.ppn_flag,
				t_tagihan_air_detail.nilai_administrasi,
				t_tagihan_air_detail.nilai_pemeliharaan,
				t_tagihan_air_detail.nilai,
				t_tagihan_air_info.denda_nilai_service,
				t_tagihan_air_info.denda_jenis_service,
				t_tagihan_air.t_tagihan_id
			");
		$tagihans = $tagihans->ORDER_BY("periode")->get()->result();
		// print_r($this->db->last_query());

		foreach ($tagihans as $iterasi => $tagihan) {
			// total tanpa ppn
			$tagihan->nilai_tagihan_tanpa_ppn = $tagihan->nilai;

			// ppn (ppn adalah ppn dalam bentuk rupiah, sedangkan nilai ppn adalah ppn dalam bentuk %)
			$tagihan->ppn = 0;
			$tagihan->ppn = round($tagihan->nilai_tagihan_tanpa_ppn * ($tagihan->ppn_flag * $tagihan->nilai_ppn) / 100);

			//nilai_tagihan (dengan ppn)
			$tagihan->nilai_tagihan = $tagihan->nilai_tagihan_tanpa_ppn + $tagihan->ppn + $tagihan->nilai_pemeliharaan + $tagihan->nilai_administrasi;

			//nilai denda
			$tagihan->nilai_denda = 0;
			if ($tagihan->status_tagihan != 1) {
				if ($date < $tagihan->duedate) {
					$tagihan->nilai_denda = 0;
				} elseif ($tagihan->nilai_denda_flag == 1) {
					$tagihan->nilai_denda = $tagihan->nilai_denda;
				} else {
					if ($tagihan->denda_jenis_service == 1) {
						$tagihan->nilai_denda = $tagihan->denda_nilai_service;
					} elseif ($tagihan->denda_jenis_service == 2 || $tagihan->denda_jenis_service == 3) {

						$tmp_bulan_telat =
							$this->dateDifference(
								$tagihan->duedate,
								date('Y-m-d', strtotime('+1 day', strtotime($date))),
								'%y,%m,%d'
							);
						$tmp_bulan_telat = explode(',', $tmp_bulan_telat);

						$bulan_telat = ($tmp_bulan_telat[0] * 12) + ($tmp_bulan_telat[1]) + ($tmp_bulan_telat[2] > 0 ? 1 : 0);

						if ($tagihan->denda_jenis_service == 2) {
							$tagihan->nilai_denda = $tagihan->denda_nilai_service * $bulan_telat;
						} elseif ($tagihan->denda_jenis_service == 3) {
							$tagihan->nilai_denda = $bulan_telat * ($tagihan->nilai_tagihan_tanpa_ppn * $tagihan->denda_nilai_service / 100);
						}
					}
				}
			}

			//nilai penalti
			$tagihan->nilai_penalti = 0;
			if ($tagihan->status_tagihan != 1) {
				if ($date < $tagihan->duedatepenalti) {
					$tagihan->nilai_penalti = 0;
				} else {
					if ($tagihan->denda_jenis_service == 1) {
						$tagihan->nilai_penalti = $tagihan->penalti_nilai;
					} elseif ($tagihan->denda_jenis_service == 3) {
						$tagihan->nilai_penalti = $tagihan->penalti_nilai * $tagihan->nilai_tagihan_tanpa_ppn / 100;
					}
				}
			}
			$tagihan->total =
				$tagihan->nilai_tagihan + $tagihan->nilai_denda + $tagihan->nilai_penalti - $tagihan->pemutihan_nilai_tagihan - $tagihan->pemutihan_nilai_denda;
			$tagihan->final_nilai_tagihan =  0;
			$tagihan->final_nilai_denda =  0;
			$tagihan->final_nilai_penalti =  0;
			$tagihan->final_nilai_tagihan_tanpa_ppn =  0;
			$tagihan->final_ppn =  0;
			if ($tagihan->status_tagihan == 4) {
				$pembayaran = (object)['dibayar' => 0];
				$pembayaran = $this->db
					->select("
						isnull(t_pembayaran_detail.bayar,t_pembayaran_detail.bayar_deposit) as dibayar,
						t_pembayaran.tgl_bayar
					")
					->from("t_pembayaran")
					->join(
						"t_pembayaran_detail",
						"t_pembayaran_detail.t_pembayaran_id = t_pembayaran.id"
					)
					->WHERE("t_pembayaran.unit_id", $tagihan->unit_id)
					->WHERE("t_pembayaran_detail.tagihan_service_id", $tagihan->id)
					->WHERE("t_pembayaran_detail.service_id", $tagihan->service_id)
					->get()->row();
				// echo("<pre>");
				// 	print_r($pembayaran);
				// echo("</pre>");

				if (isset($pembayaran->dibayar)) {
					if ($pembayaran->dibayar > 0) {
						$dibayar = $pembayaran->dibayar;
						$tagihan->dibayar = $dibayar;
						do {
							$berkurang = 0;
							$berkurang = $tagihan->nilai_penalti;
							if ($tagihan->nilai_penalti >= $dibayar) {
								$tagihan->final_nilai_penalti = $tagihan->nilai_penalti - $dibayar;
								$dibayar = 0;
							} else {
								$dibayar = $dibayar - $tagihan->nilai_penalti;
								$tagihan->final_nilai_penalti = 0;
							}
							if ($tagihan->nilai_denda >= $dibayar) {
								$tagihan->final_nilai_denda = $tagihan->nilai_denda - $dibayar;
								$dibayar = 0;
							} else {
								$dibayar = $dibayar - $tagihan->nilai_denda;
								$tagihan->final_nilai_denda = 0;
							}
							if ($tagihan->nilai_tagihan >= $dibayar) {
								$tagihan->final_nilai_tagihan = $tagihan->nilai_tagihan - $dibayar;
								$dibayar = 0;
							} else {
								$dibayar = $dibayar - $tagihan->nilai_tagihan;
								$tagihan->final_nilai_tagihan = 0;
							}
						} while ($dibayar > 0);
					}
				}
			} else {
				$tagihan->final_nilai_tagihan = $tagihan->nilai_tagihan;
				$tagihan->final_nilai_denda = $tagihan->nilai_denda;
				$tagihan->final_nilai_penalti = $tagihan->nilai_penalti;
				$tagihan->final_nilai_tagihan_tanpa_ppn = $tagihan->nilai_tagihan_tanpa_ppn;
				$tagihan->final_ppn = $tagihan->ppn;
			}
			$tagihan->final_nilai_tagihan_tanpa_ppn = round($tagihan->final_nilai_tagihan / (1 + ($tagihan->nilai_ppn / 100)));
			$tagihan->final_ppn = $tagihan->final_nilai_tagihan - $tagihan->final_nilai_tagihan_tanpa_ppn;

			$tagihan->final_total =
				$tagihan->final_nilai_tagihan + $tagihan->final_nilai_denda + $tagihan->final_nilai_penalti - $tagihan->pemutihan_nilai_tagihan - $tagihan->pemutihan_nilai_denda;
		}

		return $tagihans;
	}
	//$periode == untuk perhitungan denda dan penalti
	function get_tagihan_gabungan($param, $group_by = 'periode')
	{
		$tagihan = (object)[];
		$param = (object)$param;
		$param->date = isset($param->date) ? $param->date : date("Y-m-d");

		if (!isset($param->service_jenis_id))
			$param->service_jenis_id = [1, 2];

		if (in_array(1, $param->service_jenis_id))
			$tagihan->lingkungans = $this->get_lingkungan($param);
		else
			$tagihan->lingkungans = [];

		if (in_array(2, $param->service_jenis_id))
			$tagihan->airs = $this->get_air($param);
		else
			$tagihan->airs = [];



		$tagihan->gabungans = [];
		$periode_lingkungan = (object)[];
		$periode_lingkungan->min = isset($tagihan->lingkungans[0]) ? $tagihan->lingkungans[0]->periode : '9999-99-99';
		$periode_lingkungan->max = isset($tagihan->lingkungans[0]) ? end($tagihan->lingkungans)->periode : '0';

		$periode_air = (object)[];
		$periode_air->min = isset($tagihan->airs[0]) ? $tagihan->airs[0]->periode : '9999-99-99';
		$periode_air->max = isset($tagihan->airs[0]) ? end($tagihan->airs)->periode : '0';


		$periode = $periode_lingkungan;
		if ($periode_lingkungan->min > $periode_air->min)
			$periode->min = $periode_air->min;
		if ($periode_lingkungan->max < $periode_air->max)
			$periode->max = $periode_air->max;


		$now = $periode->min;

		if ($group_by == 'periode') {
			while ($now <= $periode->max) {
				$tmpLingkungan = (object)[];
				$tmpAir = (object)[];
				foreach ($tagihan->lingkungans as $lingkungan) {
					if ($lingkungan->periode == $now)
						$tmpLingkungan = $lingkungan;
				}
				foreach ($tagihan->airs as $air) {
					if ($air->periode == $now)
						$tmpAir = $air;
				}
				if (isset($tmpLingkungan->id) || isset($tmpAir->id)) {
					array_push($tagihan->gabungans, (object)[
						'lingkungan' => $tmpLingkungan,
						'air' => $tmpAir
					]);
				}
				$now = date("Y-m-d", strtotime("+1 month", strtotime($now)));
			}
		}
		if ($group_by == 'unit') {
			$unit_ids = [];
			foreach ($tagihan->lingkungans as $lingkungan) {
				if (!in_array($lingkungan->unit_id, $unit_ids))
					array_push($unit_ids, $lingkungan->unit_id);
			}
			foreach ($tagihan->airs as $air) {
				if (!in_array($air->unit_id, $unit_ids))
					array_push($unit_ids, $air->unit_id);
			}
			$iterasi = [0, 0, 0, 0];
			foreach ($unit_ids as $unit_id) {
				$tmp = [];

				foreach ($tagihan->lingkungans as $lingkungan) {
					if ($lingkungan->unit_id == $unit_id) {
						array_push($tmp, $lingkungan);
						$iterasi[3]++;
					}
					$iterasi[1]++;
				}
				foreach ($tagihan->airs as $air) {
					if ($air->unit_id == $unit_id)
						array_push($tmp, $air);
					$iterasi[2]++;
				}
				if (isset($tmp)) {
					array_push($tagihan->gabungans, $tmp);
				}
				$iterasi[0]++;

				// $now = date("Y-m-d", strtotime("+1 month", strtotime($now)));
			}
		}

		return $tagihan->gabungans;
	}
	function get_va($unit_id)
	{
		return 	$this->db->SELECT("
					bank.code as bank,
					CASE bank.code
						WHEN 'bca' THEN 'midtrans'
						ELSE 'xendit'
					END as tipe,
					

					CONCAT (
						cara_pembayaran.va_merchant,
					RIGHT(CONCAT('0000000000000000',unit.virtual_account),16 - len(cara_pembayaran.va_bank) - len(cara_pembayaran.va_merchant))) as va_number,

					concat('" . base_url() . "images/my_ciputra/',bank.code,'Logo.png') as logo")
			->FROM("unit")
			->JOIN(
				"cara_pembayaran",
				"cara_pembayaran.project_id = unit.project_id
				AND cara_pembayaran.pt_id = unit.pt_id"
			)
			->JOIN(
				"bank",
				"bank.id = cara_pembayaran.bank_id"
			)
			->WHERE("unit.id", $unit_id)
			->WHERE("cara_pembayaran.jenis_cara_pembayaran_id", 3)
			->WHERE("isnull(CONCAT(cara_pembayaran.va_bank,cara_pembayaran.va_merchant,RIGHT(CONCAT('0000000000',unit.virtual_account),cara_pembayaran.max_digit-LEN(CONCAT(cara_pembayaran.va_bank,cara_pembayaran.va_merchant)))),'0') is not null")
			->get()->result();
	}
	function dateDifference($date_1, $date_2, $differenceFormat = '%a')
	{
		//////////////////////////////////////////////////////////////////////
		//https://www.php.net/manual/en/function.date-diff.php
		//PARA: Date Should In YYYY-MM-DD Format
		//RESULT FORMAT:
		// '%y Year %m Month %d Day %h Hours %i Minute %s Seconds'        =>  1 Year 3 Month 14 Day 11 Hours 49 Minute 36 Seconds
		// '%y Year %m Month %d Day'                                    =>  1 Year 3 Month 14 Days
		// '%m Month %d Day'                                            =>  3 Month 14 Day
		// '%d Day %h Hours'                                            =>  14 Day 11 Hours
		// '%d Day'                                                        =>  14 Days
		// '%h Hours %i Minute %s Seconds'                                =>  11 Hours 49 Minute 36 Seconds
		// '%i Minute %s Seconds'                                        =>  49 Minute 36 Seconds
		// '%h Hours                                                    =>  11 Hours
		// '%a Days                                                        =>  468 Days
		//////////////////////////////////////////////////////////////////////
		$datetime1 = date_create($date_1);
		$datetime2 = date_create($date_2);

		$interval = date_diff($datetime1, $datetime2);

		return $interval->format($differenceFormat);
	}
}
