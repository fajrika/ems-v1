<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payment extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('m_login');
		if (!$this->m_login->status_login()) redirect(site_url());
		$this->load->model('transaksi/m_payment');
		$this->load->model('m_core');
		global $jabatan;
		$jabatan = $this->m_core->jabatan();
		global $project;
		$project = $this->m_core->project();
		global $menu;
		$menu = $this->m_core->menu();
		ini_set('memory_limit', '256M');
		ini_set('sqlsrv.ClientBufferMaxKBSize', '524288');
		ini_set('pdo_sqlsrv.client_buffer_max_kb_size', '524288');
	}
	public function test($unit_id)
	{
		$this->load->model("core/m_tagihan");
		$service_id = 1;
		$data = (object)[];
		$data->pembayaran = (object)[
			"id"							=> 0,
			"unit_id" 						=> $unit_id,
			"cara_pembayaran_id"			=> 1,
			"jenis_cara_pembayaran_id"		=> 1,
			"code_pembayaran"				=> 'pembayaran_aplikasi',
			"tgl_document"					=> date('Y-m-d'),
			"keterangan"					=> '',
			"tgl_tambah"					=> date('Y-m-d'),
			"user_id"						=> 0,
			"delete"						=> 0,
			"tgl_bayar"						=> date('Y-m-d'),
			"flag_trf_keuangan"				=> 0,
			"no_kwitansi"					=> 0,
			"is_void"						=> 0,
			"nilai_biaya_admin_cara_pembayaran"	=> 0,
			"count_print_kwitansi"			=> 0
		];
		$data->pembayaran_detail = [];

		foreach ($this->m_tagihan->get_lingkungan((object)[
			'date' => date('Y-m-d'),
			'unit_id' => $unit_id,
			'status_tagihan' => [3]
		]) as $index => $tagihan_ipl) {
			array_push($data->pembayaran_detail, [
				"t_pembayaran_id" => $data->pembayaran->id,
				"nilai_tagihan" => $tagihan_ipl->final_nilai_tagihan,
				"nilai_penalti" => 0,
				"bayar" => $tagihan_ipl->final_total,
				"bayar_deposit" => 0,
				"service_id" => $tagihan_ipl->service_id,
				"tagihan_service_id" => $tagihan_ipl->id,
				"nilai_denda" => $tagihan_ipl->nilai_denda,
				"kwitansi_referensi_id" => 0,
				"diskon_id" => 0,
				"nilai_diskon" => 0,
				"nilai_ppn" => $tagihan_ipl->ppn,
				"nilai_tagihan_pemutihan" => 0,
				"nilai_denda_pemutihan" => 0,
				"nilai_biaya_admin_cara_pembayaran" => 0,
				"sisa_tagihan" => 0,
				"is_tunggakan" => 0,
				"service_jenis_id" => 1,
				"nilai_pokok" => $tagihan_ipl->final_nilai_tagihan_tanpa_ppn,
				"nilai_pemutihan_pokok" => 0,
				"nilai_pemutihan_tagihan" => 0,
				"nilai_pemutihan_denda" => 0,
				"nilai_diskon_pokok" => 0,
				"nilai_diskon_tagihan" => 0,
				"nilai_ppn_persen" => $tagihan_ipl->nilai_ppn,
				"nilai_terbayar" => $tagihan_ipl->dibayar ?? 0,
				"nilai_outstanding" => $tagihan_ipl->final_total

			]);
		}
		foreach ($this->m_tagihan->get_air((object)[
			'date' => date('Y-m-d'),
			'unit_id' => $unit_id,
			'status_tagihan' => [3]
		]) as $index => $tagihan_air) {
			array_push($data->pembayaran_detail, [
				"t_pembayaran_id" => $data->pembayaran->id,
				"nilai_tagihan" => $tagihan_air->final_nilai_tagihan,
				"nilai_penalti" => 0,
				"bayar" => $tagihan_air->final_total,
				"bayar_deposit" => 0,
				"service_id" => $tagihan_air->service_id,
				"tagihan_service_id" => $tagihan_air->id,
				"nilai_denda" => $tagihan_air->nilai_denda,
				"kwitansi_referensi_id" => 0,
				"diskon_id" => 0,
				"nilai_diskon" => 0,
				"nilai_ppn" => $tagihan_air->ppn,
				"nilai_tagihan_pemutihan" => 0,
				"nilai_denda_pemutihan" => 0,
				"nilai_biaya_admin_cara_pembayaran" => 0,
				"sisa_tagihan" => 0,
				"is_tunggakan" => 0,
				"service_jenis_id" => 2,
				"nilai_pokok" => $tagihan_air->final_nilai_tagihan_tanpa_ppn,
				"nilai_pemutihan_pokok" => 0,
				"nilai_pemutihan_tagihan" => 0,
				"nilai_pemutihan_denda" => 0,
				"nilai_diskon_pokok" => 0,
				"nilai_diskon_tagihan" => 0,
				"nilai_ppn_persen" => $tagihan_air->nilai_ppn,
				"nilai_terbayar" => $tagihan_air->dibayar ?? 0,
				"nilai_outstanding" => $tagihan_air->final_total
			]);
		}
		echo json_encode($data);
	}
	public function add($unit_jenis = 1, $unit_id = 0)
	{
		if ($unit_id != 0)
			if ($unit_jenis == 1)
				$unit = $this->db
					->select("concat('1.',unit.id) as id, CONCAT(kawasan.name,'-',blok.name,'/',unit.no_unit,'-',customer.name) as text")
					->from('unit')
					->join('blok', 'blok.id = unit.blok_id')
					->join('kawasan', 'kawasan.id = blok.kawasan_id')
					->join('customer', 'customer.id = unit.pemilik_customer_id')
					->where('unit.project_id', $GLOBALS['project']->id)
					->where("unit.id", $unit_id);
			else
				$unit = $this->db
					->select("unit_virtual.id, CONCAT(unit_virtual.unit,'-',customer.name) as text")
					->from('unit_virtual')
					->join('customer', 'customer.id = unit_virtual.customer_id')
					->where('unit_virtual.project_id', $GLOBALS['project']->id)
					->where("unit_virtual.id", $unit_id);
		$unit = isset($unit) ? $unit->get()->row() : (object)[];
		$this->load->view('core/header');
		$this->load->view('core/top_bar_modal', ['jabatan' => $GLOBALS['jabatan'], 'project' => $GLOBALS['project']]);
		$this->load->view('core/body_header_modal', ['title' => 'Transaksi Service > Pembayaran Tagihan', 'subTitle' => 'List']);
		$this->load->view('Proyek/Transaksi/Payment/add', compact('unit'));
		$this->load->view('core/body_footer_modal');
		$this->load->view('core/footer_modal');
	}
	public function ajax_get_bill($unit_jenis = 1, $unit_id = 0)
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->m_payment->get_bill($unit_jenis, $unit_id, $GLOBALS['project']->id, $this->input->post('tgl_pembayaran'))))
			->set_status_header(200);
	}
	public function ajax_get_cara_pembayaran($limit = 0)
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->m_payment->get_cara_pembayaran($limit)))
			->set_status_header(200);
	}
	public function ajax_save($unit_jenis = 1, $unit_id = 0)
	{
		$user_id = $this->db->select("id")
			->from("user")
			->where("username", $this->session->userdata["username"])
			->get()->row()->id;
		if ($this->input->post('nilai_bayar'))
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($this->m_payment->save(
					$unit_jenis,
					$unit_id,
					$GLOBALS['project']->id,
					$this->input->post('nilai_bayar'),
					$this->input->post('cara_pembayaran_id'),
					$this->input->post('tgl_pembayaran'),
					$user_id
				)))
				->set_status_header(200);
		else
			$this->output
				->set_content_type('application/json')
				->set_status_header(400);
	}
}
