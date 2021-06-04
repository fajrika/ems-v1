<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pemutihan_unit extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('m_login');
		if (!$this->m_login->status_login()) redirect(site_url());
		$this->load->model('Transaksi/m_payment');
		$this->load->model('Transaksi/m_pemutihan');
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
	public function add($jenis_unit = 1,$unit_id = 0)
	{
		if($unit_id != 0)
			if($jenis_unit == 1)
				$unit = $this->db
					->select("concat('1.',unit.id) as id, CONCAT(kawasan.name,'-',blok.name,'/',unit.no_unit,'-',customer.name) as text")
					->from('unit')
					->join('blok','blok.id = unit.blok_id')
					->join('kawasan','kawasan.id = blok.kawasan_id')
					->join('customer','customer.id = unit.pemilik_customer_id')
					->where('unit.project_id', $GLOBALS['project']->id)
					->where("unit.id", $unit_id);
			else
				$unit = $this->db
					->select("unit_virtual.id, CONCAT(unit_virtual.unit,'-',customer.name) as text")
					->from('unit_virtual')
					->join('customer','customer.id = unit_virtual.customer_id')
					->where('unit_virtual.project_id', $GLOBALS['project']->id)
					->where("unit_virtual.id", $unit_id);
		$unit = isset($unit) ? $unit->get()->row(): (object)[];		
		$this->load->view('core/header');
		$this->load->model('alert');
		$this->alert->css();

		$this->load->view('core/top_bar_modal', ['jabatan' => $GLOBALS['jabatan'], 'project' => $GLOBALS['project']]);
		$this->load->view('core/body_header_modal', ['title' => 'Transaksi Service > Pembayaran Tagihan', 'subTitle' => 'List']);
		$this->load->view('Proyek/Transaksi/pemutihan_unit/add',compact('unit'));
		$this->load->view('core/body_footer_modal');
		$this->load->view('core/footer_modal');
	}
}
