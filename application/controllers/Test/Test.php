<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Test extends CI_Controller
{
	public function index()
	{
		$this->load->model('m_core');

		$this->load->model('Transaksi/m_pemutihan');
		$kawasan = 211;
		$blok = 1880;
		$periode_awal = "01/2019";
		$periode_akhir = "01/2019";
		$metode_tagihan = [1];

		echo json_encode($this->m_pemutihan->get_unit_test($blok, $kawasan, $periode_awal, $periode_akhir, $metode_tagihan));
	}
	public function get_gabungan()
	{
		$this->load->model('core/m_tagihan');
		$param = (object)[
			// 'unit_id' => [255616],
			'kawasan_id' => [7621],
			'blok_id' => [34152],
			'periode_awal' => "2019-01-01",
			'periode_akhir' => "2020-12-01",
			'service_jenis_id' => [2],
			'status_tagihan' => [0]

		];
		// $tagihans =  ?? $param;
		$tagihan = [];
		echo ('123<pre>');
		print_r($this->m_tagihan->get_tagihan_gabungan($param, 'unit'));
		echo ('</pre>');
		die;
		foreach ($tagihans as $iterasi => $tagihanTmp) {
			echo ("iterasi : $iterasi <pre>");
			print_r($tagihanTmp);
			echo ('</pre>');
			$tmp = (object)[
				"unit_id" => 0,
				"lingkungan_nilai_pokok" => 0,
				"lingkungan_nilai_pokok_ppn" => 0,
				"lingkungan_nilai_denda" => 0,
				"air_nilai_pokok" => 0,
				"air_nilai_denda" => 0,
				"periode" => null
			];
			if (isset($tagihanTmp->lingkungan->id)) {
				$tmp->unit_id = $tagihanTmp->lingkungan->unit_id;
				$tmp->lingkungan_nilai_pokok = $tagihanTmp->lingkungan->nilai_tagihan_tanpa_ppn;
				$tmp->lingkungan_nilai_pokok_ppn = $tagihanTmp->lingkungan->nilai_tagihan;
				$tmp->lingkungan_nilai_denda = $tagihanTmp->lingkungan->nilai_denda;
				$tmp->periode = $tagihanTmp->lingkungan->periode;
			}
			if (isset($tagihanTmp->air->id)) {
				$tmp->unit_id = $tagihanTmp->air->unit_id;
				$tmp->air_nilai_pokok = $tagihanTmp->air->nilai_tagihan;
				$tmp->air_nilai_denda = $tagihanTmp->air->nilai_denda;
				$tmp->periode = $tagihanTmp->air->periode;
			}
			array_push($tagihan, $tmp);
		}
		echo ('tagihan<pre>');
		print_r($tagihan);
		echo ('</pre>');
	}
	public function get_gabungan_tagihan($unit_id="")
	{
		$this->load->model('core/m_tagihan');
		$param = (object)[
			'unit_id' => $unit_id,
			'periode_awal' => "2020-01-01",
			'periode_akhir' => "2020-09-01"
		];
		$tagihans = $this->m_tagihan->get_tagihan_gabungan($param, 'periode');
		$tagihan = [];
		echo ('tagihans<pre>');
		print_r($tagihans);
		echo ('</pre>');
		foreach ($tagihans as $iterasi => $tagihanTmp) {
			echo ("iterasi : $iterasi <pre>");
			print_r($tagihanTmp);
			echo ('</pre>');
			$tmp = (object)[
				"unit_id" => 0,
				"lingkungan_nilai_pokok" => 0,
				"lingkungan_nilai_pokok_ppn" => 0,
				"lingkungan_nilai_denda" => 0,
				"air_nilai_pokok" => 0,
				"air_nilai_denda" => 0,
				"periode" => null
			];
			if (isset($tagihanTmp->lingkungan->id)) {
				$tmp->unit_id = $tagihanTmp->lingkungan->unit_id;
				$tmp->lingkungan_nilai_pokok = $tagihanTmp->lingkungan->nilai_tagihan_tanpa_ppn;
				$tmp->lingkungan_nilai_pokok_ppn = $tagihanTmp->lingkungan->nilai_tagihan;
				$tmp->lingkungan_nilai_denda = $tagihanTmp->lingkungan->nilai_denda;
				$tmp->periode = $tagihanTmp->lingkungan->periode;
			}
			if (isset($tagihanTmp->air->id)) {
				$tmp->unit_id = $tagihanTmp->air->unit_id;
				$tmp->air_nilai_pokok = $tagihanTmp->air->nilai_tagihan;
				$tmp->air_nilai_denda = $tagihanTmp->air->nilai_denda;
				$tmp->periode = $tagihanTmp->air->periode;
			}
			array_push($tagihan, $tmp);
		}
		echo ('tagihan<pre>');
		print_r($tagihan);
		echo ('</pre>');
	}
}
