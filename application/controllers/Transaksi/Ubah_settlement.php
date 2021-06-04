<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ubah_settlement extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('m_login');
        $this->load->model('transaksi/m_ubah_settlement');
        if (!$this->m_login->status_login()) redirect(site_url());
        $this->load->model('m_core');
        global $jabatan;
        $jabatan = $this->m_core->jabatan();
        global $project;
        $project = $this->m_core->project();
        global $menu;
        $menu = $this->m_core->menu();
    }
    public function index()
    {
        $this->view('normal', 'Transaksi > Voucher > Ubah Tgl Settlement', 'Change', 'proyek/Transaksi/Ubah_settlement/index', ["project" => $GLOBALS['project']]);
    }

    public function ajax_uploud_file($cara_pembayaran_id)
    {
        // var_dump($_FILES['file_rekening_koran']);
        $file = $_FILES['file_rekening_koran'];

        if ($file['size'] <= 1240000/* 1 mb */) {
            if (in_array($file['type'], ['text/plain'])) {
                $result = $this->m_ubah_settlement->uploud_file($file['tmp_name'], $cara_pembayaran_id);
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($result))
                    ->set_status_header(200);
            } else return ['message' => 'ekstensi tidak di dukung'];
        } else return ['message' => 'ukuran file terlalu besar, maksimum 1 mb'];
    }
}
