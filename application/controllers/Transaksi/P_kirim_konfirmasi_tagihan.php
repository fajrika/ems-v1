<?php

defined('BASEPATH') or exit('No direct script access allowed');

class P_kirim_konfirmasi_tagihan  extends CI_Controller
{
    public function __construct()
    {
        // die('Under Construction'); //=== die by Arif

        parent::__construct();
        $this->load->database();
        $this->load->model('m_core');
        $this->load->model('m_login');
        if (!$this->m_login->status_login()) {
            redirect(site_url());
        }
        global $jabatan;
        $jabatan = $this->m_core->jabatan();
        global $project;
        $project = $this->m_core->project();
        global $menu;
        $menu = $this->m_core->menu();
        $this->load->model('Setting/m_parameter_project');

        ini_set('memory_limit', '256M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
        ini_set('sqlsrv.ClientBufferMaxKBSize', '524288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size', '524288');
    }

    public function index()
    {
        // die;
        $project = $this->m_core->project();
        $periode = date('Y-m');

        $this->load->view('core/header');
        $this->load->view('core/side_bar', ['menu' => $GLOBALS['menu']]);
        $this->load->view('core/top_bar', ['jabatan' => $GLOBALS['jabatan'], 'project' => $GLOBALS['project']]);
        $this->load->view('core/body_header', ['title' => 'Transaksi Service > Kirim Konfirmasi Tagihan', 'subTitle' => 'List']);
        $this->load->view('proyek/transaksi/kirim_konfirmasi_tagihan/view');
        $this->load->view('core/body_footer');
        $this->load->view('core/footer');
    }
    public function ajax_get_view()
    {
        $project = $this->m_core->project();
        $periode = date('Y-m');

        $table =    "v_kirim_konfirmasi_tagihan
                    WHERE project_id = $project->id
                    ";
        $primaryKey = 'unit_id';
        $columns = array(
            array('db' => 'unit_id as unit_id', 'dt' => 0),
            array('db' => 'kawasan as kawasan',  'dt' => 1),
            array('db' => 'blok as blok', 'dt' => 2),
            array('db' => 'no_unit as no_unit', 'dt' => 3),
            array('db' => "tujuan as tujuan", 'dt' => 4),
            array('db' => "pemilik as pemilik", 'dt' => 5),
            array('db' => "'Belum di kirim' as send_email", 'dt' => 6),
            array('db' => "send_sms as send_sms", 'dt' => 7),
            array('db' => "send_surat as send_surat", 'dt' => 8)
        );
        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db'   => $this->db->database,
            'host' => $this->db->hostname
        );
        $this->load->library("SSP");


        $table = SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns);
        $this->load->helper('directory');

        $map = directory_map('./application/pdf');

        foreach ($table["data"] as $k => $v) {

            $table["data"][$k][9] =
                "<button class='btn btn-primary' onClick=\"window.open('" . site_url() . "/Cetakan/konfirmasi_tagihan/unit/" . $table["data"][$k][0] . "')\">View Dokumen</button>";

            $unit_id_periode = $table["data"][$k][0] . "_" . date("Y-m-");
            $result = preg_grep("/^$unit_id_periode/i", $map);
            $table["data"][$k][10] = end($result)
                ? "<button class='btn btn-primary' onClick=\"window.location.href='" . base_url() . "pdf/" . end($result) . "'\">View Dokumen</button>"
                : "";

            $table["data"][$k][0] =
                "<input name='unit_id[]' type='checkbox' class='flat table-check' val='$v[0]'>";
        }
        echo (json_encode($table));
    }
    public function test()
    {
        // response post
        // 90026993                      = Harus Cek Report
        // Invalid MSISDN                = Failed / Nomor Salah
        // Sorry anda tidak punya akses  = Salah Username/Password

        // report
        // 22,                          = Sukses Terikirim
        // 50,Failed                    = Gagal
        // 51                           = Periode Habis
        // 52                           = Kesalahan Format Nomor Tujuan
        // 20                           = Pesan Pending Terkirim, nomor tujuan tidak aktif dalam waktu tertentu
    }
    public function total_outstanding($unit_id)
    {
        $this->load->model("core/m_tagihan");
        $project = $this->m_core->project();
        $lingkungans = $this->m_tagihan->lingkungan($project->id, ['unit_id' => $unit_id, 'status_tagihan' => [0, 4]]);
        $airs = $this->m_tagihan->air($project->id, ['unit_id' => $unit_id, 'status_tagihan' => [0, 4]]);
        $total = 0;
        foreach ($lingkungans as $lingkungan) {
            $total = $total + ($lingkungan->belum_bayar != 0 ? $lingkungan->belum_bayar : $lingkungan->total);
        }
        foreach ($airs as $air) {
            $total = $total + ($air->belum_bayar != 0 ? $air->belum_bayar : $air->total);
        }
        return $total;
    }
    public function kirim_sms()
    {
        $this->load->model('m_unit');
        $this->load->model('m_customer');

        $unit_id_array = $this->input->post("unit_id[]");
        $project = $this->m_core->project();
        $this->load->library('curl');
        $template_sms = $this->m_parameter_project->get($project->id, "template_sms_konfirmasi_tagihan");
        foreach ($unit_id_array as $k => $unit_id) {
            $data_unit = $this->m_unit->getSelect($unit_id);

            $message = $template_sms;
            $blok = $this->db->select("name")->from("blok")->where("id", $data_unit->blok)->get()->row()->name;
            $kawasan = $this->db->select("name")->from("kawasan")->where("id", $data_unit->kawasan)->get()->row()->name;

            // $total_tagihan = $this->db->select("sum(tagihan_air + tagihan_lingkungan + total_denda) as total_tagihan")
            //     ->from("v_sales_force_bill")
            //     ->where("unit_id", $unit_id)
            //     ->get()->row()->total_tagihan; 17.59 detik
            $total_tagihan = $this->total_outstanding($unit_id); // 2.22 detik
            $message = str_replace("{{Blok}}", $blok . "/" . $data_unit->no_unit, $message);
            $message = str_replace("{{Kawasan}}", $kawasan, $message);
            $message = str_replace("{{Total_tagihan}}", number_format($total_tagihan, 0, ",", "."), $message);
            $uid =  $this->db->select("concat(project.source_id,kawasan.code,blok.code,'/',unit.no_unit) as uid")
                ->from("unit")
                ->join(
                    "project",
                    "project.id = unit.project_id"
                )
                ->join(
                    "blok",
                    "blok.id = unit.blok_id"
                )
                ->join(
                    "kawasan",
                    "kawasan.id = blok.kawasan_id"
                )
                ->where("unit.id", $unit_id)
                ->get()->row();
            $uid = $uid ? $uid->uid : 0;
            $message = str_replace("{{no_iplk}}", $uid, $message);
            $data_customer = $this->m_customer->getSelect($data_unit->pemilik);
            $data_customer->mobilephone1 = preg_replace("/[^0-9]/", "", $data_customer->mobilephone1);
            // echo("<pre>");
            //     print_r($data_unit);
            // echo("</pre>");
            // echo("<pre>");
            //     print_r($data_customer);
            // echo("</pre>");
            // echo("<pre>");
            //     print_r($message);
            // echo("</pre>");
            // die;
            // $url = "http://103.16.199.187/masking/send_post.php";
            $url = $this->m_parameter_project->get($project->id, "sms_gateway_host");
            $rows = array(
                'username' => $this->m_parameter_project->get($project->id, "sms_gateway_user"),
                'password' => $this->m_parameter_project->get($project->id, "sms_gateway_pass"),
                'hp' => $data_customer->mobilephone1,
                'message' => $message
                // 'message' => 'testing SMS'
            );
            $source_id = $this->curl->simple_post($url, $rows);
            $i = 0;
            do {
                $result = $this->curl->simple_get("http://103.16.199.187/masking/report.php?rpt=$source_id");
                $i++;
            } while ($result == "Success Send" && $i < 100);
            $send_sms = [
                "unit_id"       => $unit_id,
                "no"            => $data_customer->mobilephone1,
                "source_id"     => $source_id,
                "status_full"   => $result,
                "create_date"   => date("Y-m-d"),
                "message"       => $message,
                "jenis_id"      => 1,
                "status_flag"   => (int) $result
            ];
            $this->db->insert("send_sms", $send_sms);
            echo ("<pre>");
            print_r($rows);
            echo ("</pre>");
            echo ("<pre>");
            print_r($result);
            echo ("</pre>");
            echo ("Success ");
        }
    }
    public function test2()
    {
        // $data = "{\"isi\":\"Kepata Yth.\r\nBapak\/Ibu JUHARIAH\r\nProject CitraLand Cibubur\r\nKawasan MONTEVERDE APHANDRA\r\nBlok A.02\/08\r\n\r\nDengan Hormat,\r\nTerlampir detail tagihan IPLK & AIR\r\nBulan JANUARI JAN  sampai AGUSTUS 2019 Tahun 2019\r\n\r\nTerimakasih atas kesetiaan dan\r\nkepercayaan Anda bersama \r\nCitraLand Cibubur\r\n\r\nSalam,\r\nCitraLand Cibubur\",\"name_file\":\"19_2019-08-19_15-46-55.pdf\"}";
        $data = '{"isi":"Kepata Yth.\r\nBapak\/Ibu JUHARIAH\r\nProject CitraLand Cibubur\r\nKawasan MONTEVERDE APHANDRA\r\nBlok A.02\/08\r\n\r\nDengan Hormat,\r\nTerlampir detail tagihan IPLK & AIR\r\nBulan JANUARI JAN  sampai AGUSTUS 2019 Tahun 2019\r\n\r\nTerimakasih atas kesetiaan dan\r\nkepercayaan Anda bersama \r\nCitraLand Cibubur\r\n\r\nSalam,\r\nCitraLand Cibubur","name_file":"19_2019-08-19_15-46-55.pdf"}';

        echo ("<pre>");
        print_r($data);
        echo ("</pre>");
        $data = json_decode($data);
        echo (json_encode($data->isi));
    }
    public function kirim_email()
    {
        $unit_id_array = $this->input->post("unit_id[]");
        // echo("<pre>");
        //     print_r($unit_id_array);
        // echo("</pre>");
        $project = $this->m_core->project();
        $email_success = 0;
        foreach ($unit_id_array as $k => $unit_id) {

            $this->load->library('curl');
            $isi_konfirmasi_tagihan = [
                "project_id"  => $project->id,
                "isi" => $this->m_parameter_project->get($project->id, "isi_konfirmasi_tagihan")
            ];

            echo ("test1<pre>");
            print_r($unit_id);
            echo ("</pre>");
            echo ("test2<pre>");
            print_r($isi_konfirmasi_tagihan);
            echo ("</pre>");
            var_dump(site_url() . "/Cetakan/konfirmasi_tagihan_api/send/" . $unit_id);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, site_url() . "/Cetakan/konfirmasi_tagihan_api/send/" . $unit_id);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, false); 
            curl_setopt($ch, CURLOPT_POST, count($isi_konfirmasi_tagihan));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $isi_konfirmasi_tagihan);    
            $result=curl_exec($ch);
            curl_close($ch);
            
            echo ("result1<pre>");
            print_r($result);
            echo ("</pre>");
            $result = json_decode($result);
            echo ("result2<pre>");
            print_r($result);
            echo ("</pre>");

            if ($result) {
                echo ("test123");
                // var_dump($result);
                // var_dump($result->name_file);

                $result->name_file = str_replace("\"", "", $result->name_file);
                $config = [
                    'mailtype'  => 'html',
                    'charset'   => 'utf-8',
                    'protocol'  => 'smtp',
                    'smtp_host' => $this->m_parameter_project->get($project->id, "smtp_host"),
                    'smtp_user' => $this->m_parameter_project->get($project->id, "smtp_user"),
                    'smtp_pass' => $this->m_parameter_project->get($project->id, "smtp_pass"),
                    'smtp_port' => $this->m_parameter_project->get($project->id, "smtp_port"),
                    // 'smtp_crypto' => 'tls',
                    'crlf'      => "\r\n",
                    'newline'   => "\r\n",
                    'smtp_crypto'   => $this->m_parameter_project->get($project->id, "smtp_secure")??"ssl"
                ];
                echo ("config<pre>");
                print_r($config);
                echo ("</pre>");
                $this->load->library('email', $config);
                // print_r($config);
                // $this->db->selec
                $this->email->from($this->m_parameter_project->get($project->id, "smtp_user"), 'EMS Ciputra');

                $email = $this->db
                    ->select("
                        CASE
                            WHEN unit.kirim_tagihan = 1 THEN pemilik.email
                            WHEN unit.kirim_tagihan = 2 THEN penghuni.email
                            WHEN unit.kirim_tagihan = 3 THEN CONCAT(pemilik.email,';',penghuni.email)
                        END as email")
                    ->from("unit")
                    ->join(
                        "customer as pemilik",
                        "pemilik.id = unit.pemilik_customer_id",
                        "LEFT"
                    )
                    ->join(
                        "customer as penghuni",
                        "penghuni.id = unit.penghuni_customer_id 
                            AND penghuni.id != pemilik.id",
                        "LEFT"
                    )

                    ->where("unit.id", $unit_id)->get()->row()->email;
                $email = explode(";", $email);
                $parameter_delay = explode(";", $this->m_parameter_project->get($project->id, "delay_email"));
                $uid =  $this->db->select("concat(project.source_id,kawasan.code,blok.code,'/',unit.no_unit) as uid")
                    ->from("unit")
                    ->join(
                        "project",
                        "project.id = unit.project_id"
                    )
                    ->join(
                        "blok",
                        "blok.id = unit.blok_id"
                    )
                    ->join(
                        "kawasan",
                        "kawasan.id = blok.kawasan_id"
                    )
                    ->where("unit.id", $unit_id)
                    ->get()->row();
                $uid = $uid ? $uid->uid : 0;
                $result->isi = str_replace("{{no_iplk}}", $uid, $result->isi);
                foreach ($email as $k => $v) {
                    if ($k != 0 && ($k + 1) % $parameter_delay[0] == 0) {
                        sleep($parameter_delay[1]);
                    }


                    $this->email->clear(TRUE);
                    $this->email->from($this->m_parameter_project->get($project->id, "smtp_user"), 'EMS Ciputra');
                    $this->email->subject($this->m_parameter_project->get($project->id, "subjek_konfirmasi_tagihan"));
                    $this->email->message(($result->isi));
                    $this->email->to($v);
                    $this->email->attach("application/pdf/$result->name_file");
                    var_dump($this->m_parameter_project->get($project->id, "smtp_user"));
                    var_dump($this->m_parameter_project->get($project->id, "subjek_konfirmasi_tagihan"));
                    var_dump($result->isi);

                    $status = $this->email->send();
                    if ($status) {
                        echo ("Success " . $result->name_file);
                        $email_success++;
                    } else {
                        echo ("Gagal  " . $result->name_file);
                    }
                    var_dump($v . "->" . $status);
                }
            }
        }
    }

    /**
    | function for send whatsapp blast to customer
    | --------------------------------------------------------------------
    | july 16, 2020
     */
    public function send_whatsapp()
    {
        $json_data['status'] = 1;
        $json_data['pesan']  = "WhatsApp Successfully Send";
        $json_data['redirect_page'] = "YES";
        $json_data['redirect_page_URL'] = site_url('transaksi/p_kirim_konfirmasi_tagihan');

        //Start OB & put json output-------------------------//
        ob_end_clean();
        ignore_user_abort();
        ob_start();
        header("Connection: close");
        echo json_encode($json_data);
        header("Content-Length: " . ob_get_length());
        ob_end_flush();
        flush();
        //Run Process Here----------------------------------//
        set_time_limit(0);

        $project   = $this->m_core->project();
        $unit_id   = $this->input->post('unit_id');
        $join_unit = '';
        $join_unit_comma = '';
        foreach ($unit_id as $value) {
            $join_unit .= "'" . $value . "',";
            $join_unit_comma .= $value . ",";
        }
        $join_unit = rtrim($join_unit, ',');
        $join_unit_comma = rtrim($join_unit_comma, ',');
        $sql = "
            SELECT
                unit.id,
                unit.no_unit,
                pemilik.name AS pemilik_name,
                penghuni.name AS penghuni_name,
                CASE 
                    unit.kirim_tagihan
                WHEN 2 THEN '0'
                ELSE 
                    pemilik.mobilephone1 
                END AS pemilik_no,
                penghuni.name AS penghuni_name,
                CASE 
                    WHEN unit.kirim_tagihan = 3 AND unit.pemilik_customer_id = unit.penghuni_customer_id THEN '0'
                    WHEN (unit.kirim_tagihan = 3 AND unit.pemilik_customer_id != unit.penghuni_customer_id) OR unit.kirim_tagihan = 2 THEN penghuni.mobilephone1
                ELSE '0'
                END AS penghuni_no
            FROM 
                unit
                LEFT JOIN customer AS pemilik ON pemilik.id = unit.pemilik_customer_id
                LEFT JOIN customer AS penghuni ON penghuni.id = unit.penghuni_customer_id
            WHERE 1=1
                AND unit.project_id = '" . $project->id . "'
                AND unit.id IN (" . $join_unit . ")
        ";
        $sql = $this->db->query($sql);

        $api_key = '';
        $get_apikey = $this->db->where('project_id', $project->id)->where('code', 'whatsapp_api_key')->limit(1)->get('parameter_project');
        if ($get_apikey->num_rows() > 0) {
            $api_key = $get_apikey->row()->value;
        }

        // $dummy_no_hp = array('08567159231', '081585810669');
        $dummy_no_hp = array('08567159231');
        $key_tsel_me = "170821cc33b400304660a940afeb51463e9958e699544189";
        if ($sql->num_rows() > 0) {
            foreach ($sql->result() as $d) {
                // if ($d->pemilik_no > 7) 
                // {
                $no_pemilik  = preg_replace('/[^A-Za-z0-9\-]/', '', trim($d->pemilik_no));
                $no_penghuni = preg_replace('/[^A-Za-z0-9\-]/', '', trim($d->penghuni_no));
                // print_r($no.' '.$no_pemilik.' '.$no_penghuni);echo "<br>";
                foreach ($dummy_no_hp as $phone_no) {
                    $call     = $this->print_pdf('send_wa', $d->id);
                    // $file_url = "https://ces-ems.ciputragroup.com:11443/pdf/".$call['nama_file'];
                    $file_url = "https://ces-ems.ciputragroup.com:11443/pdf/" . $call;
                    $message  = "*Informasi Tagihan Retribusi Estate*\n\n";
                    $message .= "Kepada Yth,\n" . $d->pemilik_name . "\n\n";
                    $message .= "Dengan ini kami sampaikan informasi total tagihan dari bulan september 2018 sampai maret 2020, dengan perincian sebagai berikut :";
                    // $message .= "Dengan ini kami sampaikan informasi total tagihan";
                    // if($call['periode_first'] == $call['periode_last']){
                    //     $message .= (" bulan " . strtolower($call['periode_first']));
                    // }else{
                    //     $message .= (" dari bulan ".strtolower($call['periode_first'])." sampai ".strtolower($call['periode_last']));
                    // }
                    // $message .= ", dengan perincian sebagai berikut :";

                    // print_r($call);exit();

                    // Send WA Text
                    $data = array("key" => $key_tsel_me, "phone_no" => $phone_no, "message" => $message);
                    $data_string = json_encode($data);
                    $ch = curl_init('http://116.203.92.59/api/send_message');
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_VERBOSE, 0);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string)));
                    curl_exec($ch);

                    // Send WA Attachment
                    $data = array("phone_no" => $phone_no, "key" => $key_tsel_me, "url" => $file_url);
                    $data_string = json_encode($data);
                    $ch = curl_init('http://116.203.92.59/api/send_file_url');
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_VERBOSE, 0);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string)));
                    curl_exec($ch);

                    // $log = $this->db->insert('send_sms', [
                    //     'unit_id' => $d->id,
                    //     'no' => $phone_no,
                    //     'status_full' => $res,
                    //     'create_date' => date('Y-m-d'),
                    //     'message' => $message,
                    //     'send_by' => 1
                    // ]);
                }
                // }
            }
        }
    }


    /**
    | -----------------------------------------------------------------------
    | Process request query kirim konfirmasi tagihan
    | Jakarta, 2020-07-08
    |
     */
    public function request_tagihan_json()
    {
        $project = $this->m_core->project();       
        $this->load->library("Ssp_custom");
        $table = "
        (
            SELECT 
                DISTINCT sub.*
            FROM 
                (
                    SELECT
                        unit.project_id,
                        customer.name as pemilik,
                        unit.id AS unit_id,
                        kawasan.name AS kawasan,
                        blok.name AS blok,
                        unit.no_unit AS no_unit,
                        CASE unit.kirim_tagihan 
                            WHEN 1 THEN 'Pemilik' 
                            WHEN 2 THEN 'Penghuni' 
                            WHEN 3 THEN 'Keduanya' 
                            ELSE '' 
                        END AS tujuan,
                        'Belum di kirim' AS send_email,
                        CASE COUNT ( send_sms.id ) 
                            WHEN 0 THEN 'Belum di Kirim' ELSE 'Sudah di kirim' 
                        END AS send_sms,
                        'Belum di kirim' AS send_surat 
                    FROM 
                        unit
                        JOIN customer ON customer.id = unit.pemilik_customer_id
                        JOIN blok ON blok.id = unit.blok_id
                        JOIN kawasan ON kawasan.id = blok.kawasan_id
                        INNER JOIN t_tagihan_lingkungan ON t_tagihan_lingkungan.unit_id = unit.id AND t_tagihan_lingkungan.status_tagihan != 1
                        INNER JOIN unit_lingkungan ON t_tagihan_lingkungan.unit_id = unit_lingkungan.unit_id AND unit_lingkungan.tgl_mandiri IS NULL
                        LEFT JOIN send_sms ON send_sms.unit_id = unit.id AND FORMAT( send_sms.create_date, 'yyyy-MM' ) = FORMAT( GETDATE(), 'yyyy-MM' )
                    WHERE 
                        unit.project_id = '".$project->id."' AND 
                        t_tagihan_lingkungan.status_tagihan IS NOT NULL
                    GROUP BY
                        unit.project_id,
                        customer.name,
                        unit.id,
                        kawasan.name,
                        blok.name,
                        unit.no_unit,
                        CASE unit.kirim_tagihan 
                            WHEN 1 THEN 'Pemilik' 
                            WHEN 2 THEN 'Penghuni' 
                            WHEN 3 THEN 'Keduanya' 
                            ELSE '' 
                        END 

                    UNION

                    SELECT
                        unit.project_id,
                        customer.name as pemilik,
                        unit.id AS unit_id,
                        kawasan.name AS kawasan,
                        blok.name AS blok,
                        unit.no_unit AS no_unit,
                        CASE unit.kirim_tagihan 
                            WHEN 1 THEN 'Pemilik' 
                            WHEN 2 THEN 'Penghuni' 
                            WHEN 3 THEN 'Keduanya' 
                            ELSE '' 
                        END AS tujuan,
                        'Belum di kirim' AS send_email,
                        CASE COUNT ( send_sms.id ) 
                            WHEN 0 THEN 'Belum di Kirim' ELSE 'Sudah di kirim' 
                        END AS send_sms,
                        'Belum di kirim' AS send_surat 
                    FROM 
                        unit
                        JOIN customer ON customer.id = unit.pemilik_customer_id
                        JOIN blok ON blok.id = unit.blok_id
                        JOIN kawasan ON kawasan.id = blok.kawasan_id
                        INNER JOIN t_tagihan_air ON t_tagihan_air.unit_id = unit.id AND t_tagihan_air.status_tagihan != 1
                        LEFT JOIN send_sms ON send_sms.unit_id = unit.id AND FORMAT( send_sms.create_date, 'yyyy-MM' ) = FORMAT( GETDATE(), 'yyyy-MM' )
                    WHERE 
                        unit.project_id = '".$project->id."' AND 
                        t_tagihan_air.status_tagihan IS NOT NULL 
                    GROUP BY
                        unit.project_id,
                        customer.name,
                        unit.id,
                        kawasan.name,
                        blok.name,
                        unit.no_unit,
                        CASE unit.kirim_tagihan 
                            WHEN 1 THEN 'Pemilik' 
                            WHEN 2 THEN 'Penghuni' 
                            WHEN 3 THEN 'Keduanya' 
                            ELSE '' 
                        END 
                ) AS sub
        ) temp
        ";

        $primaryKey = 'unit_id';
        $columns = array(
            array('db' => 'unit_id',    'dt' => 0),
            array('db' => 'kawasan',    'dt' => 1),
            array('db' => 'blok',       'dt' => 2),
            array('db' => 'no_unit',    'dt' => 3),
            array('db' => 'tujuan',     'dt' => 4),
            array('db' => 'pemilik',    'dt' => 5),
            array('db' => 'send_email', 'dt' => 6),
            array('db' => 'send_sms',   'dt' => 7),
            array('db' => 'send_surat', 'dt' => 8),
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
            $table["data"][$key][0]  = "<center><input name='unit_id[]' type='checkbox' class='flat table-check' value='$value[0]' style='cursor: pointer;'></center>";
            $table['data'][$key][9]  = '';
        }

        echo(json_encode($table));
    }

    /**
    | -----------------------------------------------------------------------
    | Process print pdf by mpdf
    | Jakarta, 2020-07-08
    |
     */
    public function print_pdf($type = NULL, $params = NULL)
    {
        require_once 'vendor/MPDF/vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
        // $mpdf = new \Mpdf\Mpdf(['mode'=>'utf-8', 'format'=>'A4', 'orientation' => 'L']);

        ini_set("pcre.backtrack_limit", "9000000");
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <title>Kirim Konfirmasi Tagihan</title>
            <style type="text/css">
                html {
                    margin-top: 200px;
                    padding: 0px;
                }

                body {
                    font-size: 13.5px;
                }

                table {
                    width: 100%;
                }

                .casabanti {
                    font-family: 'casbanti';
                }

                .f-20 {
                    font-size: 20px;
                    font-weight: 700;
                    line-height: 15px;
                }

                .f-14,
                .f-15 {
                    font-size: 14px;
                }

                .lh-15 {
                    line-height: 15px;
                }

                .align-center,
                .text-center {
                    text-align: center;
                }

                .text-right {
                    text-align: right;
                }

                .font-normal {
                    font-weight: 500;
                }

                .table-striped tr:nth-child(even) {
                    background-color: #f2f2f2;
                }

                .table-striped th {
                    font-size: 12px;
                }
            </style>
        </head>

        <body>
            <?php
            if (!empty($_GET['unit_id']) or !empty($params)) {
                ### jika type send whatsapp
                if (!empty($type) and $type == 'send_wa') {
                    $unit_ids = $params;
                } else {
                    $unit_ids = $this->input->get('unit_id');
                }

                $unit_ids = explode(",", $unit_ids);
                $nomor    = 1;
                $jml_data = count($unit_ids) - 1;
                $project  = $this->m_core->project();
                $ttd      = $this->m_parameter_project->get($project->id, "ttd_konfirmasi_tagihan");
                $service_air = $this->db->select("jarak_periode_penggunaan")
                    ->from("service")
                    ->where("project_id", $project->id)
                    ->where("service_jenis_id", 2)
                    ->get()
                    ->row();
                $service_air = $service_air ? $service_air->jarak_periode_penggunaan : 0;
                $service_lingkungan = $this->db->select("jarak_periode_penggunaan")
                    ->from("service")
                    ->where("project_id", $project->id)
                    ->where("service_jenis_id", 1)
                    ->get()
                    ->row();
                $service_lingkungan = $service_lingkungan ? $service_lingkungan->jarak_periode_penggunaan : 0;

                foreach ($unit_ids as $unit_id) {
                    $this->load->model('Cetakan/m_konfirmasi_tagihan');
                    $unit                 = $this->m_konfirmasi_tagihan->get_unit($unit_id);
                    $status_saldo_deposit = $this->m_konfirmasi_tagihan->get_status_saldo_deposit($unit_id);
                    $saldo_deposit        = $this->m_konfirmasi_tagihan->get_saldo_deposit($unit_id);

                    $catatan = $unit->catatan;
                    $catatan = str_replace("{{va_unit}}", $unit->virtual_account, $catatan);
                    $uid = $this->db
                        ->select("concat(project.source_id,kawasan.code,blok.code,'/',unit.no_unit) as uid")
                        ->from("unit")
                        ->join("project", "project.id = unit.project_id")
                        ->join("blok", "blok.id = unit.blok_id")
                        ->join("kawasan", "kawasan.id = blok.kawasan_id")
                        ->where("unit.id", $unit_id)
                        ->get()
                        ->row();
                    $uid = $uid ? $uid->uid : 0;
                    $catatan = str_replace("{{no_iplk}}", $uid, $catatan);

                    //Data Tagihan Without Sorting
                    $dataTagihanWoS = $this->ajax_get_tagihan($unit_id);

                    //After sort
                    $dataTagihanWS = [];

                    $min_tagihan_air        = isset($dataTagihanWoS->tagihan_air[0]) ? $dataTagihanWoS->tagihan_air[0]->periode : null;
                    $max_tagihan_air        = isset($dataTagihanWoS->tagihan_air[0]) ? end($dataTagihanWoS->tagihan_air)->periode : null;
                    $min_tagihan_lingkungan = isset($dataTagihanWoS->tagihan_lingkungan[0]) ? $dataTagihanWoS->tagihan_lingkungan[0]->periode : null;
                    $max_tagihan_lingkungan = isset($dataTagihanWoS->tagihan_lingkungan[0]) ? end($dataTagihanWoS->tagihan_lingkungan)->periode : null;

                    if ($min_tagihan_air == null) {
                        $min_tagihan_air = $min_tagihan_lingkungan;
                    }
                    if ($min_tagihan_lingkungan == null) {
                        $min_tagihan_lingkungan = $min_tagihan_air;
                    }
                    if ($max_tagihan_air == null) {
                        $max_tagihan_air = $max_tagihan_lingkungan;
                    }
                    if ($max_tagihan_lingkungan == null) {
                        $max_tagihan_lingkungan = $max_tagihan_air;
                    }
                    $min_tagihan = new DateTime($min_tagihan_air > $min_tagihan_lingkungan ? $min_tagihan_lingkungan : $min_tagihan_air);
                    $max_tagihan = new DateTime($max_tagihan_air > $max_tagihan_lingkungan ? $max_tagihan_air : $max_tagihan_lingkungan);

                    $iterasi = 0;
                    $total_tagihan = (object)[];
                    $total_tagihan->pakai   = null;
                    $total_tagihan->air     = null;
                    $total_tagihan->ipl     = null;
                    $total_tagihan->ppn     = null;
                    $total_tagihan->denda   = null;
                    $total_tagihan->tunggakan = null;
                    $total_tagihan->total   = null;
                    $total_tagihan->lain    = null;
                    $periode_first = $this->bln_indo(substr($min_tagihan->format("Y-m-01"), 5, 2)) . " " . substr($min_tagihan->format("Y-m-01"), 0, 4);
                    $periode_last  = $this->bln_indo(substr($max_tagihan->format("Y-m-01"), 5, 2)) . " " . substr($max_tagihan->format("Y-m-01"), 0, 4);


                    if ($service_air == $service_lingkungan) {
                        $jarak_periode_penggunaan = $service_air;
                    } else {
                        $jarak_periode_penggunaan = -1;
                    }
                    for ($i = $min_tagihan; $i <= $max_tagihan; $i->modify('+1 month')) {
                        $periode = $i->format("Y-m-01");
                        $periode_1 = $periode;
                        if ($jarak_periode_penggunaan != -1) {
                            $tmp = $periode;
                            $tmp = strtotime(date("Y-m-d", strtotime($tmp)) . " -$jarak_periode_penggunaan month");
                            $tmp = date("Y-m-d", $tmp);
                            $periode_1 = $tmp;
                        }
                        $dataTagihanWS[$iterasi] = (object)[];
                        $dataTagihanWS[$iterasi]->periode = substr($this->bln_indo(substr($periode, 5, 2)), 0, 3) . " " . substr($periode, 0, 4);
                        $dataTagihanWS[$iterasi]->periode_penggunaan = substr($this->bln_indo(substr($periode_1, 5, 2)), 0, 3) . " " . substr($periode_1, 0, 4);
                        $dataTagihanWS[$iterasi]->meter_awal    = null;
                        $dataTagihanWS[$iterasi]->meter_akhir   = null;
                        $dataTagihanWS[$iterasi]->pakai         = null;
                        $dataTagihanWS[$iterasi]->air           = 0;
                        $dataTagihanWS[$iterasi]->ipl           = null;
                        $dataTagihanWS[$iterasi]->ppn           = null;
                        $dataTagihanWS[$iterasi]->denda         = 0;
                        $dataTagihanWS[$iterasi]->tunggakan     = 0;
                        $dataTagihanWS[$iterasi]->total         = null;

                        foreach ($dataTagihanWoS->tagihan_air as $k => $v) {
                            if ($v->periode == $periode) {
                                $tmp_tagihan_air = $v;
                                $dataTagihanWS[$iterasi]->meter_awal    = $v->meter_awal;
                                $dataTagihanWS[$iterasi]->meter_akhir   = $v->meter_akhir;
                                $dataTagihanWS[$iterasi]->pakai         = $v->meter_akhir - $v->meter_awal;
                                if ($v->belum_bayar > 0) {
                                    $dataTagihanWS[$iterasi]->tunggakan = $v->belum_bayar;
                                    $dataTagihanWS[$iterasi]->total    += $v->belum_bayar;
                                } else {
                                    $dataTagihanWS[$iterasi]->air       = $v->nilai_tagihan;
                                    $dataTagihanWS[$iterasi]->denda    += $v->nilai_denda;
                                    $dataTagihanWS[$iterasi]->total    += $v->total;
                                }
                                break;
                            }
                        }
                        // var_dump($tmp_tagihan_air);
                        foreach ($dataTagihanWoS->tagihan_lingkungan as $k => $v) {
                            if ($v->periode == $periode) {
                                if ($v->belum_bayar > 0) {
                                    $dataTagihanWS[$iterasi]->tunggakan += $v->belum_bayar;
                                    $dataTagihanWS[$iterasi]->total  += $v->belum_bayar;
                                } else {
                                    $dataTagihanWS[$iterasi]->ipl    = $v->total_tanpa_ppn;
                                    $dataTagihanWS[$iterasi]->ppn    = $v->nilai_tagihan - $v->total_tanpa_ppn;
                                    $dataTagihanWS[$iterasi]->denda += $v->nilai_denda;
                                    $dataTagihanWS[$iterasi]->total += $v->total;
                                }
                                break;
                            }
                        }
                        $total_tagihan->pakai   += $dataTagihanWS[$iterasi]->pakai;
                        $total_tagihan->air     += $dataTagihanWS[$iterasi]->air;
                        $total_tagihan->ipl     += $dataTagihanWS[$iterasi]->ipl;
                        $total_tagihan->ppn     += $dataTagihanWS[$iterasi]->ppn;
                        $total_tagihan->denda   += $dataTagihanWS[$iterasi]->denda;
                        $total_tagihan->total   += $dataTagihanWS[$iterasi]->total;
                        $total_tagihan->tunggakan += $dataTagihanWS[$iterasi]->tunggakan;
                        $iterasi++;
                    }

                    if ($jarak_periode_penggunaan != -1) {
                        $data = [
                            "unit" => $unit,
                            "catatan" => $catatan,
                            "tagihan" => $dataTagihanWS,
                            "total_tagihan" => $total_tagihan,
                            "periode_first" => $periode_first,
                            "periode_last" => $periode_last,
                            "saldo_deposit" => $saldo_deposit,
                            "status_saldo_deposit" => $status_saldo_deposit,
                            "ttd" => $ttd,
                            "nomor" => $nomor++,
                            "jml_data" => $jml_data,
                            "project_id" => $project->id
                        ];
                        $this->load->view('proyek/transaksi/kirim_konfirmasi_tagihan/print_pdf_second', $data);
                    } else {
                        $data = [
                            "unit" => $unit,
                            "catatan" => $catatan,
                            "tagihan" => $dataTagihanWS,
                            "total_tagihan" => $total_tagihan,
                            "periode_first" => $periode_first,
                            "periode_last" => $periode_last,
                            "saldo_deposit" => $saldo_deposit,
                            "status_saldo_deposit" => $status_saldo_deposit,
                            "ttd" => $ttd,
                            "nomor" => $nomor++,
                            "jml_data" => $jml_data
                        ];
                        $this->load->view('proyek/transaksi/kirim_konfirmasi_tagihan/print_pdf', $data);
                    }
                }
            }
            ?>
        </body>

        </html>
        <?php
        $nama_file = "konf_tagihan_" . $project->id . "_" . date("Ymd") . ".pdf";
        $html = ob_get_contents(); //Proses untuk mengambil data
        ob_end_clean();
        $mpdf->WriteHTML(utf8_encode($html));
        $mpdf->WriteHTML($html, 1);

        ### jika type send whatsapp
        if (!empty($type) and $type == 'send_wa') {
            $mpdf->Output("pdf/" . $nama_file, \Mpdf\Output\Destination::FILE);
            // return array('nama_file'=>$nama_file, 'periode_first'=>$periode_first, 'periode_last'=>$periode_last);
            return $nama_file;
        } else {
            $mpdf->Output($nama_file . "_" . date("YmdHis") . ".pdf", 'I');
        }
    }

    function bln_indo($tmp)
    {
        $bulan = array(
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        return $bulan[(int)$tmp];
    }


    public function ajax_get_tagihan($unit_id)
    {
        $project  = $this->m_core->project();
        $dateForm = $this->input->post("date");

        if ($dateForm) {
            $periode_now = substr($dateForm, 6, 4) . "-" . substr($dateForm, 3, 2) . "-01";
        } else {
            $periode_now = date("Y-m-01");
        }

        $periode_pemakaian = date("Y-m-01", strtotime("-1 Months"));

        $this->load->model("core/m_tagihan");
        $tagihan_air = $this->m_tagihan->air_tanpa_potong_pemutihan($project->id, ['status_tagihan' => [0, 2, 3, 4], 'unit_id' => [$unit_id], 'periode' => $periode_now]);
        $tagihan_lingkungan = $this->m_tagihan->lingkungan_tanpa_potong_pemutihan($project->id, ['status_tagihan' => [0, 2, 3, 4], 'unit_id' => [$unit_id], 'periode' => $periode_now]);

        $jumlah_tunggakan_bulan = 0;
        $jumlah_tunggakan = 0;
        $jumlah_denda     = 0;
        $jumlah_penalti   = 0;
        $jumlah_tagihan   = 0;

        $jumlah_nilai_pokok             = 0;
        $jumlah_nilai_ppn               = 0;
        $jumlah_nilai_denda             = 0;
        $jumlah_nilai_penalti           = 0;
        $jumlah_nilai_pemutihan_pokok   = 0;
        $jumlah_nilai_pemutihan_denda   = 0;
        $jumlah_total                   = 0;
        foreach ($tagihan_lingkungan as $v) {
            $jumlah_nilai_pokok             += $v->total_tanpa_ppn;
            $jumlah_nilai_ppn               += $v->ppn;
            $jumlah_nilai_denda             += $v->nilai_denda;
            $jumlah_nilai_penalti           += $v->nilai_penalti;
            $jumlah_nilai_pemutihan_pokok   += $v->view_pemutihan_nilai_tagihan;
            $jumlah_nilai_pemutihan_denda   += $v->view_pemutihan_nilai_denda;
            $jumlah_total                   += $v->total;
        }
        foreach ($tagihan_air as $v) {
            $jumlah_nilai_pokok             += $v->total_tanpa_ppn;
            $jumlah_nilai_ppn               += $v->ppn;
            $jumlah_nilai_denda             += $v->nilai_denda;
            $jumlah_nilai_penalti           += $v->nilai_penalti;
            $jumlah_nilai_pemutihan_pokok   += $v->view_pemutihan_nilai_tagihan;
            $jumlah_nilai_pemutihan_denda   += $v->view_pemutihan_nilai_denda;
            $jumlah_total                   += $v->total;
        }
        $unit = (object) [];
        $unit->tagihan_air = $tagihan_air;
        $unit->tagihan_lingkungan = $tagihan_lingkungan;

        return ($unit);
    }
}
