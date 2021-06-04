<?php

use custom\Libraries\Element;

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/Element.php';
class Desktop_Tagihan_Air extends Element
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('m_login');
        if (!$this->m_login->status_login())
            redirect(site_url());
        $this->load->model('Sync/m_desktop_transaksi_air');
        $this->load->model('m_core');
        global $jabatan;
        $jabatan = $this->m_core->jabatan();
        global $project;
        $project = $this->m_core->project();
        global $menu;
        $menu = $this->m_core->menu();
        // ini_set('memory_limit', '256M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
        // ini_set('sqlsrv.ClientBufferMaxKBSize', '524288'); // Setting to 512M
        // ini_set('pdo_sqlsrv.client_buffer_max_kb_size', '524288'); // Setting to 512M - for pdo_sqlsrv
        // ini_set('max_execution_time', '-1'); // Setting to 512M - for pdo_sqlsrv
    }

    public function add()
    {
        $project = $this->m_core->project();

        $this->theme("layouts/admin_gentelella");
        $this->title_submenu("Builder > Migrate Tagihan Air");
        $this->css("sync/desktop_tagihan_air/indexCSS");
        $this->js("sync/desktop_tagihan_air/indexJS");

        $this->content("sync/desktop_tagihan_air/index", [
            'project'       => $this->db->from("project")->get()->result(),
            'project_id'    => $project->id,
            'schemas'       => $this->db->select("name")->from("ems_temp.sys.schemas")->get()->result()
        ]);

        $this->render([
            'css' => [base_url("/assets/select2/select2.min.css")],
            'js' => [
                base_url("/assets/select2/select2.min.js"),
                base_url("/assets/jquery-ui/jquery-ui.min.js")
            ]
        ]);
    }
    public function telahDiMigrasi()
    {
        header('Content-Type: application/json');
        $result = $this->m_desktop_transaksi_air->telahDiMigrasi($this->input->post("source"));
        echo (json_encode($result));
    }
    public function belumDiMigrasi()
    {
        header('Content-Type: application/json');
        $result = $this->m_desktop_transaksi_air->belumDiMigrasi($this->input->post("source"));
        echo (json_encode($result));
    }
    public function save()
    {
        header('Content-Type: application/json');
        $result = $this->m_desktop_transaksi_air->save(
            $this->input->post("project_id"),
            $this->input->post("source"),
            $this->input->post("denda_jenis_service"),
            $this->input->post("denda_nilai_service"),
            json_decode($this->input->post("data"))
        );
        echo (json_encode($result));
    }
    public function getDataBeforeMigrate()
    {
        header('Content-Type: application/json');
        $result = $this->m_desktop_transaksi_air->getDataBeforeMigrate(
            $this->input->post("source"),
            $this->input->post("jarak_periode")
        );
        echo (json_encode($result));
    }
}
