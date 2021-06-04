<?php
defined('BASEPATH') or exit('No direct script access allowed');
class P_customer extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('m_core');
        global $jabatan;
        $jabatan = $this->m_core->jabatan();
        global $project;
        $project = $this->m_core->project();
        global $menu;
        $menu = $this->m_core->menu();
        global $unit_id;
        $unit_id = $this->m_core->unit_id();
        ini_set('memory_limit', '256M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
        ini_set('sqlsrv.ClientBufferMaxKBSize', '524288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size', '524288');
    }

    public function index()
    {
        $kawasan = $this->db->select("id, code, name")->from("kawasan")->where("project_id", $GLOBALS['project']->id)->order_by('id ASC')->get()->result();

        $this->load->view('core/header');
        $this->load->view('core/side_bar', ['menu' => $GLOBALS['menu']]);
        $this->load->view('core/top_bar', ['jabatan' => $GLOBALS['jabatan'], 'project' => $GLOBALS['project']]);
        $this->load->view('core/body_header', ['title' => 'Report Customer', 'subTitle' => 'List']);
        $this->load->view('proyek/report/rpt_customer/view', ['kawasan' => $kawasan]);
        $this->load->view('core/body_footer');
        $this->load->view('core/footer');
    }


    public function request_cust_json()
    {
        ini_set('memory_limit', '1024M'); // or you could use 1G
        $requestData    = $_REQUEST;
        $like_value     = $requestData['search']['value'];
        $column_order   = $requestData['order'][0]['column'];
        $column_dir     = $requestData['order'][0]['dir'];
        $limit_start    = $requestData['start'];
        $limit_length   = $requestData['length'];
        $id_kawasan     = $this->input->post('id_kawasan');
        $blok           = $this->input->post('id_blok');

        $sql  = $this->query_customer($id_kawasan, $blok);
        $sql .= "
            AND (
                dbo.kawasan.name LIKE '%" . $this->db->escape_like_str($like_value) . "%'
                OR dbo.blok.name LIKE '%" . $this->db->escape_like_str($like_value) . "%'
                OR dbo.unit.no_unit LIKE '%" . $this->db->escape_like_str($like_value) . "%'
                OR pemilik.name LIKE '%" . $this->db->escape_like_str($like_value) . "%'
            )";
        $data_sql['totalFiltered']  = $this->db->query($sql)->num_rows();
        $data_sql['totalData']      = $this->db->query($sql)->num_rows();
        $columns_order_by = array(
            0 => 'nomor',
            1 => 'dbo.kawasan.name',
            2 => 'dbo.blok.name',
            3 => 'dbo.unit.no_unit',
            4 => 'pemilik.name',
            5 => 'pemilik.mobilephone1'
        );

        $sql  .= " ORDER BY " . $columns_order_by[$column_order] . " " . $column_dir . " ";
        $sql  .= " OFFSET " . $limit_start . " ROWS FETCH NEXT " . $limit_length . " ROWS ONLY ";

        $data_sql['query'] = $this->db->query($sql);
        $totalData       = $data_sql['totalData'];
        $totalFiltered   = $data_sql['totalFiltered'];
        $query           = $data_sql['query'];

        $data   = array();
        $urut1  = 1;
        $urut2  = 0;
        foreach ($query->result_array() as $row) {
            $nestedData  = array();
            $total_data  = $totalData;
            $start_dari  = $requestData['start'];
            $perhalaman  = $requestData['length'];
            $asc_desc    = $requestData['order'][0]['dir'];
            if ($asc_desc == 'asc') {
                $nomor = $urut1 + $start_dari;
            }
            if ($asc_desc == 'desc') {
                $nomor = ($total_data - $start_dari) - $urut2;
            }

            $nestedData[] = $nomor;
            $nestedData[] = $row['kawasan'];
            $nestedData[] = $row['blok'];
            $nestedData[] = $row['no_unit'];
            $nestedData[] = strtoupper($row['pemilik']);
            $nestedData[] = $row['mobilephone1'];
            $data[] = $nestedData;
            $urut1++;
            $urut2++;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($json_data));
        // echo json_encode($json_data);
    }

    public function ajax_get_blok()
    {
        $id      = $this->input->get('id');
        $project = $this->m_core->project();
        $result  = $this->db
            ->select("blok.id, blok.code, blok.name")
            ->from("blok")
            ->join("kawasan", "kawasan.id = blok.kawasan_id")
            ->where("kawasan.project_id", $project->id);

        if ($id !== 'all') {
            $result = $result->where("kawasan.id", $id);
        }
        $result = $result->get()->result();
        echo json_encode($result);
    }

    /*
    * --------------------------------------------------------------------
    * View Report Dalam Bentuk Excel
    * --------------------------------------------------------------------
    */
    function report_excel()
    {
        $id_kawasan = $this->input->get('id_kawasan');
        $blok = $this->input->get('id_blok');

        $filename = $GLOBALS['project']->code . '_report_customer_' . date('YmdH');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment; filename=" . $filename . ".xls");
        header("Content-Transfer-Encoding: binary ");

        $this->xlsBOF();
        $this->xlsWriteLabel(0, 0, "No");
        $this->xlsWriteLabel(0, 1, "Unit ID");
        $this->xlsWriteLabel(0, 2, "Nama PT");
        $this->xlsWriteLabel(0, 3, "Kode Kawasan");
        $this->xlsWriteLabel(0, 4, "Kawasan");
        $this->xlsWriteLabel(0, 5, "Blok/Unit");
        $this->xlsWriteLabel(0, 6, "Pemilik");
        $this->xlsWriteLabel(0, 7, "NIK/KTP");
        $this->xlsWriteLabel(0, 8, "Address");
        $this->xlsWriteLabel(0, 9, "NPWP");
        $this->xlsWriteLabel(0, 10, "ALAMAT NPWP");
        $this->xlsWriteLabel(0, 11, "No. HP");
        $this->xlsWriteLabel(0, 12, "No. HP 2");
        $this->xlsWriteLabel(0, 13, "E-Mail");
        $this->xlsWriteLabel(0, 14, "Tgl Serah Terima");
        $this->xlsWriteLabel(0, 15, "Keterangan");
        $this->xlsWriteLabel(0, 16, "Virtual Account");
        $this->xlsWriteLabel(0, 17, "Luas Tanah");
        $this->xlsWriteLabel(0, 18, "Luas Bangunan");
        $this->xlsWriteLabel(0, 19, "Golongan");
        $this->xlsWriteLabel(0, 20, "Status Diskon Unit");
        $this->xlsWriteLabel(0, 21, "Sub Golongan IPL");
        $this->xlsWriteLabel(0, 22, "Biaya Admin Sub Golongan IPL");
        $this->xlsWriteLabel(0, 23, "Kede Tarif IPL");
        $this->xlsWriteLabel(0, 24, "Tgl Aktif IPL");
        $this->xlsWriteLabel(0, 25, "Sub Golongan Air");
        $this->xlsWriteLabel(0, 26, "Biaya Admin Sub Golongan Air");
        $this->xlsWriteLabel(0, 27, "Kede Tarif Air");
        $this->xlsWriteLabel(0, 28, "Tgl Aktif Air");
        $this->xlsWriteLabel(0, 29, "No Meter Air");

        $no   = 1;
        $sql  = $this->query_customer($id_kawasan, $blok);
        $sql .= " ORDER BY nama_pt, dbo.kawasan.name, dbo.blok.name, dbo.unit.no_unit ";
        $reporting = $this->db->query($sql);
        if ($reporting->num_rows() > 0) {
            foreach ($reporting->result() as $e) {
                $this->xlsWriteNumber($no, 0, $no);
                $this->xlsWriteLabel($no, 1, $e->id_unit);
                $this->xlsWriteLabel($no, 2, empty($e->nama_pt) ? "-" : $e->nama_pt);
                $this->xlsWriteLabel($no, 3, $e->code_kawasan);
                $this->xlsWriteLabel($no, 4, $e->kawasan);
                $this->xlsWriteLabel($no, 5, $e->blok.'/'.$e->no_unit);
                $this->xlsWriteLabel($no, 6, $e->pemilik);
                $this->xlsWriteLabel($no, 7, $e->NIK_pemilik);
                $this->xlsWriteLabel($no, 8, $e->address);
                $this->xlsWriteLabel($no, 9, $e->NPWP);
                $this->xlsWriteLabel($no, 10, $e->NPWP_alamat);
                $this->xlsWriteLabel($no, 11, $e->mobilephone1);
                $this->xlsWriteLabel($no, 12, $e->mobilephone2);
                $this->xlsWriteLabel($no, 13, $e->email);
                $this->xlsWriteLabel($no, 14, date('d-m-Y', strtotime($e->tgl_serah_terima)));
                $this->xlsWriteLabel($no, 15, $e->description);
                $this->xlsWriteLabel($no, 16, $e->virtual_account);
                $this->xlsWriteLabel($no, 17, $e->luas_tanah);
                $this->xlsWriteLabel($no, 18, $e->luas_bangunan);
                $this->xlsWriteLabel($no, 19, $e->jenis_golongan);
                $this->xlsWriteLabel($no, 20, ($e->diskon_flag?'Aktif':'Tidak Aktif'));
                $this->xlsWriteLabel($no, 21, $e->sub_golongan_lingkungan_name);
                $this->xlsWriteLabel($no, 22, $e->sub_golongan_lingkungan_administrasi);
                $this->xlsWriteLabel($no, 23, $e->sub_golongan_lingkungan);
                $this->xlsWriteLabel($no, 24, $e->unit_lingkungan_tgl_aktif);
                $this->xlsWriteLabel($no, 25, $e->sub_golongan_air_name);
                $this->xlsWriteLabel($no, 26, $e->sub_golongan_air_administrasi);
                $this->xlsWriteLabel($no, 27, $e->sub_golongan_air);
                $this->xlsWriteLabel($no, 28, $e->unit_air_tgl_aktif);
                $this->xlsWriteLabel($no, 29, $e->no_seri_meter);
                $no++;
            }
            $this->xlsEOF();
        }
    }

    function query_customer($kawasan, $blok)
    {
        $where_blok     = '';
        if ($blok !== 'all') {
            $where_blok = "AND dbo.blok.id  = '" . $blok . "'";
        }

        if ($kawasan == 'all') {
            $where_kawasan = "AND dbo.project.id = '" . $GLOBALS['project']->id . "'";
        } else {
            $where_kawasan = "AND dbo.kawasan.id = '" . $kawasan . "'";
        }

        $sql = " 
            SELECT 
                ROW_NUMBER() OVER (ORDER BY dbo.unit.id) AS nomor,
                dbo.unit.id AS id_unit,
                dbo.unit.diskon_flag,
                dbo.project.id AS project_id,
                dbo.project.name AS project,
                dbo.kawasan.id AS id_kawasan,
                dbo.kawasan.code AS code_kawasan,
                dbo.kawasan.name AS kawasan,
                dbo.blok.id AS id_blok,
                dbo.blok.name AS blok,
                dbo.unit.no_unit,
                pemilik.name AS pemilik,
                pemilik.ktp AS NIK_pemilik,
                pemilik.address,
                pemilik.mobilephone1,
                pemilik.mobilephone2,
                pemilik.homephone,
                pemilik.officephone,
                pemilik.email,
                pemilik.npwp_no AS NPWP,
                pemilik.npwp_address AS NPWP_alamat,
                dbo.unit.tgl_st AS tgl_serah_terima,
                pemilik.description,
                dbo.unit.virtual_account,
                dbo.unit.luas_tanah,
                dbo.unit.luas_bangunan,
                dbo.jenis_golongan.code AS jenis_golongan,
                sub_golongan_air.code AS sub_golongan_air,
                sub_golongan_air.name AS sub_golongan_air_name,
                sub_golongan_air.administrasi AS sub_golongan_air_administrasi,
                sub_golongan_lingkungan.code AS sub_golongan_lingkungan,
                sub_golongan_lingkungan.name AS sub_golongan_lingkungan_name,
                sub_golongan_lingkungan.administrasi AS sub_golongan_lingkungan_administrasi,
                dbo.unit_lingkungan.tgl_aktif AS unit_lingkungan_tgl_aktif,
                dbo.unit_air.tgl_aktif AS unit_air_tgl_aktif,
                dbo.unit_air.no_seri_meter,
                dbmaster.dbo.m_pt.name AS nama_pt 
            FROM
                dbo.unit
                INNER JOIN dbo.project ON dbo.project.id = dbo.unit.project_id
                INNER JOIN dbo.blok ON dbo.blok.id = dbo.unit.blok_id
                INNER JOIN dbo.kawasan ON dbo.kawasan.id = dbo.blok.kawasan_id
                INNER JOIN dbo.customer AS pemilik ON pemilik.id = dbo.unit.pemilik_customer_id
                LEFT JOIN dbo.customer AS penghuni ON penghuni.id = dbo.unit.penghuni_customer_id
                LEFT JOIN dbo.jenis_golongan ON jenis_golongan.id = dbo.unit.gol_id
                LEFT JOIN dbo.unit_air ON unit_air.unit_id = dbo.unit.id
                LEFT JOIN dbo.unit_lingkungan ON unit_lingkungan.unit_id = dbo.unit.id
                LEFT JOIN dbo.sub_golongan AS sub_golongan_air ON sub_golongan_air.id = dbo.unit_air.sub_gol_id
                LEFT JOIN dbo.sub_golongan AS sub_golongan_lingkungan ON sub_golongan_lingkungan.id = dbo.unit_lingkungan.sub_gol_id
                LEFT JOIN dbmaster.dbo.m_pt ON dbo.unit.pt_id = dbmaster.dbo.m_pt.pt_id
            WHERE 1=1
                $where_kawasan 
                $where_blok 
            ";
        return $sql;
    }

    // Function penanda awal file (Begin Of File) Excel
    function xlsBOF()
    {
        echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
        return;
    }

    // Function penanda akhir file (End Of File) Excel
    function xlsEOF()
    {
        echo pack("ss", 0x0A, 0x00);
        return;
    }

    // Function untuk menulis data (angka) ke cell excel
    function xlsWriteNumber($Row, $Col, $Value)
    {
        echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
        echo pack("d", $Value);
        return;
    }

    // Function untuk menulis data (text) ke cell excel
    function xlsWriteLabel($Row, $Col, $Value)
    {
        $L = strlen($Value);
        echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
        echo $Value;
        return;
    }
}
