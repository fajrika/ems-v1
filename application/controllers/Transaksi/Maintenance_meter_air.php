<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Maintenance_meter_air extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('m_login');
        if (!$this->m_login->status_login()) redirect(site_url());

        $this->load->model('m_core');
        global $jabatan;
        $jabatan = $this->m_core->jabatan();
        global $project;
        $project = $this->m_core->project();
        global $menu;
        $menu = $this->m_core->menu();

        ini_set('memory_limit', '256M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
        ini_set('sqlsrv.ClientBufferMaxKBSize', '524288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size', '524288');
    }

    public function request_data_json()
    {
        $this->load->library("Ssp_custom");
        // $project_id = $GLOBALS['project']->id;
        $unit_id = $this->input->get('unit_id');
        $table = "
        (
            SELECT
                ROW_NUMBER() OVER(ORDER BY log_unit_air.id) nomor,
                log_unit_air.id,
                m_meter_air.no_seri_meter,
                m_meter_air.nama_meter_air,
                sub_golongan.code,
                CONCAT(sub_golongan.name, ' - ',range_air.code) AS sub_gol_name,
                CASE
                    WHEN jenis_transaksi = 1 THEN 'Pengaktifan Baru'
                    WHEN jenis_transaksi = 2 THEN 'Pengaktifan Kembali'
                    WHEN jenis_transaksi = 3 THEN 'Rusak'
                ELSE 
                    'Pemutusan / Tidak Aktif' 
                END AS jenis_transaksi,
                log_unit_air.nilai_penyambungan,
                log_unit_air.biaya_admin,
                log_unit_air.total_biaya,
                FORMAT(log_unit_air.tgl_pasang,'dd/MM/yyyy') AS tgl_pasang,
                FORMAT(log_unit_air.tgl_aktif,'dd/MM/yyyy') AS tgl_aktif,
                FORMAT(log_unit_air.tgl_putus,'dd/MM/yyyy') AS tgl_putus,
                CASE
                    WHEN jenis_transaksi = 1 THEN log_unit_air.angka_meter_sekarang
                    WHEN jenis_transaksi = 2 THEN log_unit_air.angka_meter_sekarang
                ELSE 0
                END AS meter_awal,
                CASE
                    WHEN jenis_transaksi = 1 THEN 0
                    WHEN jenis_transaksi = 2 THEN 0
                    WHEN jenis_transaksi = 3 THEN log_unit_air.angka_meter_sekarang
                    WHEN jenis_transaksi = 4 THEN log_unit_air.angka_meter_sekarang
                ELSE 
                    log_unit_air.angka_meter_sekarang
                END AS meter_akhir
            FROM 
                log_unit_air
                LEFT JOIN sub_golongan ON sub_golongan.id = log_unit_air.sub_gol_id
                LEFT JOIN range_air ON range_air.id = sub_golongan.range_id
                LEFT JOIN m_meter_air ON log_unit_air.m_meter_air_id = m_meter_air.id
            WHERE 1=1
                AND log_unit_air.unit_id = '$unit_id'
        ) temp
        ";

        $primaryKey = 'id';
        $columns = array(
            array('db' => 'nomor', 'dt' => 0),
            array('db' => 'jenis_transaksi', 'dt' => 1),
            array('db' => 'sub_gol_name', 'dt' => 2),
            array('db' => 'no_seri_meter', 'dt' => 3),
            array('db' => 'tgl_pasang', 'dt' => 4),
            array('db' => 'tgl_aktif', 'dt' => 5),
            array('db' => 'tgl_putus', 'dt' => 6),
            array('db' => 'nilai_penyambungan', 'dt' => 7),
            array('db' => 'biaya_admin', 'dt' => 8),
            array('db' => 'meter_awal', 'dt' => 9),
            array('db' => 'meter_akhir', 'dt' => 10),
        );
        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db'   => $this->db->database,
            'host' => $this->db->hostname
        );
        $table = SSP_Custom::simple( $_GET, $sql_details, $table, $primaryKey, $columns );

        foreach ($table['data'] as $key => $value) 
        {
            $table['data'][$key][7] = "<div style='text-align:right;'>".number_format($table['data'][$key][7], 0, ",", ",")."</div>";
            $table['data'][$key][8] = "<div style='text-align:right;'>".number_format($table['data'][$key][8], 0, ",", ",")."</div>";
        }
        echo(json_encode($table));
    }

    public function add_modal($unit_id = 0)
    {
        if ($_POST)
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('m_meter_air_id','No Seri Meter Air','trim|required');
            $this->form_validation->set_rules('jenis_transaksi','Jenis Transaksi','trim|required');
            $this->form_validation->set_rules('tgl_aktif','Tgl Pasang','trim|required');
            $this->form_validation->set_rules('tgl_pasang','Tgl Pasang','trim|required');
            if ($this->input->post('tipe_transaksi')=='aktif_baru') {
                $this->form_validation->set_rules('nilai_penyambungan','Nilai Sambungan','trim|required');
                $this->form_validation->set_rules('biaya_admin','Biaya Admin','trim|required');
                $this->form_validation->set_rules('meter_awal','Meter Awal','trim|required');
            } else {
                $this->form_validation->set_rules('meter_akhir','Meter Akhir','trim|required');
                $this->form_validation->set_rules('tgl_pemutusan','Tanggal Pemutusan','trim|required');
            }

            $this->form_validation->set_message('exist_username','%s sudah ada !');
            $this->form_validation->set_message('required','%s Masih Kosong !');
            if ($this->form_validation->run() == TRUE)
            {
                $create_user_id = $this->db->select("id")->where("username", $this->session->userdata["username"])->get('user')->row()->id;
                $nilai_penyambungan = str_replace(",", "", $this->input->post('nilai_penyambungan'));
                $biaya_admin = str_replace(",", "", $this->input->post('biaya_admin'));
                $total_biaya = str_replace(",", "", $this->input->post('total_biaya'));

                // Upload dokumen untuk pemasangan kembali atau pemutusan
                $upload_path             = './files/meter_air/';
                $name_field              = 'dokumen';
                $allowed_types           = '*'; 
                $max_size                = '5000'; //5 MB
                $nama_file               = str_replace(' ', '_', strtolower($_FILES[$name_field]['name']));
                $config['file_name']     = $nama_file;
                $config['upload_path']   = $upload_path;
                $config['allowed_types'] = $allowed_types;
                $config['max_size']      = $max_size;
                $this->load->library('upload', $config);

                $diupload  = $this->upload->do_upload($name_field);
                if ($diupload)
                {
                    $data = $this->upload->data();
                    $nama_file = str_replace(' ', '_', strtolower($data['file_name']));
                }

                ## ###################################################################
                ## I. insert if not exist / update if exist to `unit_air`
                ## ###################################################################
                $arr_unit_air = [
                    'meter_id'        => 0,
                    'barcode_meter'   => $this->input->post('barcode_meter'),
                    'no_seri_meter'   => $this->input->post('no_seri_meter'),
                    'm_meter_air_id'  => $this->input->post('m_meter_air_id'),
                    'jenis_transaksi' => $this->input->post('jenis_transaksi'),
                    'nilai_penyambungan' => $nilai_penyambungan,
                    'biaya_admin'     => $biaya_admin,
                    'total_biaya'     => $total_biaya,
                    'dukumen'         => $nama_file,
                    'keterangan'      => preg_replace("/\r\n|\r|\n/",'<br>', $this->input->post('keterangan')),
                    'total_pemakaian'=> '',
                    'petugas' => $this->input->post('petugas')
                ];
                $m_status = 1;
                $cek_unit = $this->db->limit(1)->where('unit_id', $unit_id)->get('unit_air');
                if ($cek_unit->num_rows() > 0)
                {
                    $rows = $cek_unit->row();
                    //if transaksi pengaktifan baru / pengaktifan kembali
                    if ($this->input->post('jenis_transaksi')=='1' OR $this->input->post('jenis_transaksi')=='2') 
                    {
                        $m_status = 2;
                        $update_unit_air = $this->db
                            ->where('unit_id', $unit_id)
                            ->update('unit_air', array_merge($arr_unit_air, [
                                'tgl_putus'  => NULL,
                                'tgl_pasang' => $this->input->post('tgl_pasang'),
                                'tgl_aktif'  => $this->input->post('tgl_aktif'),
                                'sub_gol_id' => $this->input->post('sub_gol_id'),
                                'angka_meter_sekarang' => str_replace(",", "", $this->input->post('meter_awal')),
                                'petugas' => $this->input->post('petugas'),
                                'created_date' => date('Y-m-d H:i:s'),
                                'created_by' => $create_user_id
                            ])
                        );

                        ## ####################################################
                        ## II. insert into `t_pencatatan_meter_air`
                        ## ####################################################
                        $insert_pencatatan_meter = $this->db->insert('t_pencatatan_meter_air', [
                                'unit_id'    => $unit_id,
                                'periode'    => substr($this->input->post('tgl_aktif'),0,7).'-01',
                                'meter_awal' => str_replace(",", "", $this->input->post('meter_awal')),
                                'keterangan' => preg_replace("/\r\n|\r|\n/",'<br>', $this->input->post('keterangan')),
                                'create_user_id' => $create_user_id
                            ]
                        );

                        ## ###############################################
                        ## III. insert to table `log_unit_air`
                        ## ###############################################
                        $insert_log_unit_air = $this->db->insert('log_unit_air', [
                            'unit_id'         => $unit_id,
                            'aktif'           => '1',
                            'meter_id'        => 0,
                            'tgl_putus'       => NULL,
                            'tgl_pasang'      => $this->input->post('tgl_pasang'),
                            'tgl_aktif'       => $this->input->post('tgl_aktif'),
                            'sub_gol_id'      => $this->input->post('sub_gol_id'),
                            'angka_meter_sekarang' => str_replace(",", "", $this->input->post('meter_awal')),
                            'barcode_meter'   => $this->input->post('barcode_meter'),
                            'no_seri_meter'   => $this->input->post('no_seri_meter'),
                            'm_meter_air_id'  => $this->input->post('m_meter_air_id'),
                            'jenis_transaksi' => $this->input->post('jenis_transaksi'),
                            'nilai_penyambungan' => $nilai_penyambungan,
                            'biaya_admin'     => $biaya_admin,
                            'total_biaya'     => $total_biaya,
                            'dukumen'         => $nama_file,
                            'keterangan'      => preg_replace("/\r\n|\r|\n/",'<br>', $this->input->post('keterangan')),
                            'total_pemakaian' => '',
                            'petugas'         => $this->input->post('petugas'),
                            'created_date'    => date('Y-m-d H:i:s'),
                            'created_by'      => $create_user_id
                        ]);
                    }

                    //if transaksi rusak
                    if ($this->input->post('jenis_transaksi')=='3' OR $this->input->post('jenis_transaksi')=='4') 
                    {
                        $m_status = 3;
                        $update_unit_air = $this->db
                            ->where('unit_id', $unit_id)
                            ->update('unit_air', array_merge($arr_unit_air, [
                                'tgl_putus'  => $this->input->post('tgl_pemutusan'),
                                'sub_gol_id' => $rows->sub_gol_id,
                                'angka_meter_sekarang' => str_replace(",", "", $this->input->post('meter_akhir')),
                                'petugas' => $this->input->post('petugas')
                            ])
                        );

                        $cek_pencatatan = $this->db->query("SELECT TOP 1 * FROM t_pencatatan_meter_air WHERE unit_id='".$unit_id."' AND periode LIKE '".substr($this->input->post('tgl_pemutusan'),0,7)."%'");
                        if($cek_pencatatan->num_rows() > 0){
                            $update_pencatatan_meter = $this->db
                                ->where('periode', substr($this->input->post('tgl_pemutusan'),0,7).'-01')
                                ->where('unit_id', $unit_id)
                                ->update('t_pencatatan_meter_air', [
                                    'meter_akhir'=> str_replace(",", "", $this->input->post('meter_akhir')),
                                    'keterangan' => preg_replace("/\r\n|\r|\n/",'<br>', $this->input->post('keterangan')),
                                    'create_user_id' => $create_user_id
                                ]
                            );
                        }else{
                            $insert_pencatatan_meter = $this->db->insert('t_pencatatan_meter_air', [
                                    'unit_id'    => $unit_id,
                                    'periode'    => substr($this->input->post('tgl_pemutusan'),0,7).'-01',
                                    'meter_akhir'=> str_replace(",", "", $this->input->post('meter_akhir')),
                                    'keterangan' => preg_replace("/\r\n|\r|\n/",'<br>', $this->input->post('keterangan')),
                                    'create_user_id' => $create_user_id
                                ]
                            );
                        }

                        $insert_log_unit_air = $this->db->insert('log_unit_air', [
                            'unit_id'         => $unit_id,
                            'aktif'           => '1',
                            'meter_id'        => 0,
                            'tgl_putus'       => $this->input->post('tgl_pemutusan'),
                            'tgl_pasang'      => $rows->tgl_pasang,
                            'tgl_aktif'       => $rows->tgl_aktif,
                            'sub_gol_id'      => $rows->sub_gol_id,
                            'angka_meter_sekarang' => str_replace(",", "", $this->input->post('meter_akhir')),
                            'barcode_meter'   => $this->input->post('barcode_meter'),
                            'no_seri_meter'   => $this->input->post('no_seri_meter'),
                            'm_meter_air_id'  => $this->input->post('m_meter_air_id'),
                            'jenis_transaksi' => $this->input->post('jenis_transaksi'),
                            'nilai_penyambungan' => $nilai_penyambungan,
                            'biaya_admin'     => $biaya_admin,
                            'total_biaya'     => $total_biaya,
                            'dukumen'         => $nama_file,
                            'keterangan'      => preg_replace("/\r\n|\r|\n/",'<br>', $this->input->post('keterangan')),
                            'total_pemakaian' => '',
                            'petugas'         => $this->input->post('petugas'),
                            'created_date'    => date('Y-m-d H:i:s'),
                            'created_by'      => $create_user_id
                        ]);
                    }
                }
                else
                {
                    $this->db->insert('unit_air', [
                        'unit_id' => $unit_id,
                        'aktif' => '1',
                        'tgl_putus' => NULL,
                        'meter_id' => 0,
                        'sub_gol_id' => $this->input->post('sub_gol_id'),
                        'tgl_pasang' => $this->input->post('tgl_pasang'),
                        'tgl_aktif' => $this->input->post('tgl_aktif'),
                        'angka_meter_sekarang' => str_replace(",", "", $this->input->post('meter_awal')),
                        'petugas' => $this->input->post('petugas'),
                        'created_date' => date('Y-m-d H:i:s'),
                        'created_by' => $create_user_id
                    ]);
                }

                ## ###############################################
                ## IV. update status to `m_meter_air`
                ## ###############################################
                $status_meter = $this->db
                    ->where('id', $this->input->post('m_meter_air_id'))
                    ->update('m_meter_air', [
                        'status_meter' => $m_status,
                        'updated_date' => date('Y-m-d H:i:s'),
                        'updated_by' => $this->session->userdata('name')
                    ]
                );

                echo json_encode([
                    'status'=>1,'pesan'=>'Data Berhasil Ditambahkan'
                ]);
            }
            else
            {
                echo json_encode(array(
                    'status' => 0,
                    'pesan' => str_replace('<p>', '- ', str_replace('</p>', '', validation_errors()))
                ));
            }
        }
        else
        {
            $project = $this->m_core->project();
            $unit = (object) [];
            if ($unit_id != 0) {
                $unit = $this->db
                    ->select("unit.id, unit_air.sub_gol_id, CONCAT(kawasan.name,'-',blok.name,'/',unit.no_unit,'-',customer.name) as text")
                    ->from('unit')
                    ->join('unit_air', 'unit_air.unit_id = unit.id', 'left')
                    ->join('blok', 'blok.id = unit.blok_id')
                    ->join('kawasan', 'kawasan.id = blok.kawasan_id')
                    ->join('customer', 'customer.id = unit.pemilik_customer_id')
                    ->where('unit.project_id', $GLOBALS['project']->id)
                    ->where("unit.id", $unit_id)
                    ->get()->row();
            } else {
                $unit->id = 0;
            }

            $sub_gol = "
                SELECT 
                    sub_golongan.id,
                    CONCAT(sub_golongan.name, ' - ',range_air.code) AS name
                FROM 
                    sub_golongan 
                    INNER JOIN jenis_golongan ON jenis_golongan.id = sub_golongan.jenis_golongan_id
                    LEFT JOIN range_air ON range_air.id = sub_golongan.range_id
                    INNER JOIN service ON sub_golongan.service_id = service.id AND service.service_jenis_id = 2
                WHERE 
                    sub_golongan.active = 1 
                    AND jenis_golongan.project_id = '".$GLOBALS['project']->id."'
                ORDER BY 
                    sub_golongan.id ASC
                ";
            $sub_gol = $this->db->query($sub_gol);
            $aktif_baru = $this->db->query("SELECT id FROM log_unit_air WHERE unit_id='".$unit->id."' AND jenis_transaksi IN('1','2') AND tgl_pasang LIKE '".date('Y-m')."%'");
            $data = [
                'unit' => $unit, 
                'sub_gol' => $sub_gol,
                'aktif_baru' => $aktif_baru
            ];
            $this->load->view('Proyek/Transaksi/maintenance_meter_air/view', $data);
        }
    }

    function biaya_sambungan()
    {
        $id = $this->input->post('id');
        $q = "
            SELECT
                ISNULL(pemeliharaan_air.nilai_pemasangan + nilai_ppn_pemasangan, 0) AS biaya_sambungan 
            FROM 
                sub_golongan
                INNER JOIN pemeliharaan_air ON sub_golongan.pemeliharaan_air_id = pemeliharaan_air.id
            WHERE
                sub_golongan.id = '".$id."'
        ";
        $q = $this->db->query($q);
        $biaya_sambungan = 0;
        if ($q->num_rows() > 0) {
            $biaya_sambungan = $q->row()->biaya_sambungan;
        }
        echo json_encode(['biaya_sambungan'=>$biaya_sambungan]);
    }

    // get data seri meter air
    public function get_seri_meter()
    {
        $project_id   = $GLOBALS['project']->id;
        $unit_id      = $this->input->post('unit_id');
        $id_transaksi = $this->input->post('id');
        $tipe         = $this->input->post('tipe');
        if ($tipe == 'jenis_transaksi')
        {
            $where = '';
            $barcode = '';
            if($id_transaksi==1 OR $id_transaksi==2)
            {
                $where = "AND status_meter = '1' ";

                $sql = "SELECT * FROM m_meter_air WHERE 1=1 $where AND project_id = '$project_id' ";
                $sql = $this->db->query($sql);
                $option = "<option value=''>Pilih Seri Meter</option>";
                if ($sql->num_rows() > 0) {
                    foreach ($sql->result() as $q) {
                        $option .= "<option value='".$q->id."'>".$q->no_seri_meter."</option>";
                    }
                }
            }
            if($id_transaksi==3 OR $id_transaksi==4)
            {
                $where = "AND status_meter = '2' ";

                // get barcode and no.meter
                $get_meter = $this->db->where('unit_id', $unit_id)->limit(1)->get('unit_air');
                if ($get_meter->num_rows() > 0) {
                    $row = $get_meter->row();
                    $barcode = $row->barcode_meter;
                    $option  = "<option value='".$row->m_meter_air_id."'>".$row->no_seri_meter."</option>";
                }
            }
            echo json_encode([
                'option' => $option,
                'barcode' => $barcode
            ]);
        }

        if ($tipe == 'm_meter_air_id') 
        {
            $id  = $this->input->post('id');
            $sql = "SELECT barcode FROM m_meter_air WHERE id = '".$id."'";
            $sql = $this->db->query($sql);
            $barcode = '';
            if ($sql->num_rows() > 0) {
                $barcode = $sql->row()->barcode;
            }
            echo json_encode($barcode);
        }
    }
}
