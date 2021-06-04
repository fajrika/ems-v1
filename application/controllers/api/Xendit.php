<?php

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Xendit extends REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->load->database();
        $this->db->database = "ems.dbo";
    }

    public function index_post($api_key = null)
    {
        $xendit_id    = $this->post("callback_virtual_account_id");

        $bank_code    = $this->post("bank_code") ?? '';

        $checkout_salesforce = $this->db->select("*")
            ->from("checkout_salesforce")
            ->where("external_id", $this->post("external_id"))
            ->where("bank_code", $bank_code)
            ->where("xendit_id", $xendit_id)
            ->order_by("id", "DESC")
            ->get()->row();


        $external_id_string    = $this->post("external_id");
        $external_id_array  = explode(";", $external_id_string);

        $cara_pembayaran =
            $this->db->select("cara_pembayaran.*")
            ->from("t_tagihan")
            ->join(
                "cara_pembayaran",
                "cara_pembayaran.jenis_cara_pembayaran_id = 3
                AND cara_pembayaran.project_id = t_tagihan.proyek_id",
                "LEFT"
            )
            ->join(
                "bank",
                "bank.id = cara_pembayaran.bank_id",
                "LEFT"
            )
            ->join(
                "bank_jenis",
                "bank_jenis.id = bank.bank_jenis_id", //bca
                "LEFT"
            )
            ->where("bank_jenis.code", $bank_code)
            ->where_in("t_tagihan.id", $external_id_array) //6927692
            ->order_by("bank_jenis.id", "DESC")
            ->get()->row();


        if (isset($cara_pembayaran->id)) {
            $unit_id =
                $this->db->select('unit_id')
                ->from('t_tagihan')
                ->where_in('id', $external_id_array)
                ->get()->row()->unit_id ?? 0;


            $this->load->model("core/m_tagihan");
            $this->load->model('transaksi/m_payment');

            $service_id = 1;
            $data = (object)[];

            $data->pembayaran = (object)[
                "unit_id"                   => $unit_id,
                "cara_pembayaran_id"        => $cara_pembayaran->id,
                "jenis_cara_pembayaran_id"  => $cara_pembayaran->jenis_cara_pembayaran_id,
                "code_pembayaran"           => 'pembayaran_aplikasi',
                "tgl_document"              => date('Y-m-d H:i:s.000'),
                "keterangan"                => '',
                "tgl_tambah"                => date('Y-m-d H:i:s.000'),
                "user_id"                   => 0,
                "delete"                    => 0,
                "tgl_bayar"                 => date('Y-m-d H:i:s.000'),
                "flag_trf_keuangan"         => 0,
                "no_kwitansi"               => $this->m_payment->generate_kwitansi($cara_pembayaran->project_id),
                "is_void"                   => 0,
                "nilai_biaya_admin_cara_pembayaran"    => 0,
                "count_print_kwitansi"      => 0
            ];
            $this->db->insert('t_pembayaran', $data->pembayaran);
            $data->pembayaran->id = $this->db->insert_id();


            $data->pembayaran_detail = [];
            $param = (object)[
                'date' => date('Y-m-d'),
                'unit_id' => $unit_id,
                'status_tagihan' => [3]
            ];
            foreach ($this->m_tagihan->get_lingkungan($param) as $tagihan_ipl) {
                array_push($data->pembayaran_detail, [
                    "t_pembayaran_id"           => $data->pembayaran->id,
                    "nilai_tagihan"             => $tagihan_ipl->final_nilai_tagihan,
                    "nilai_penalti"             => 0,
                    "bayar"                     => $tagihan_ipl->final_total,
                    "bayar_deposit"             => 0,
                    "service_id"                => $tagihan_ipl->service_id,
                    "tagihan_service_id"        => $tagihan_ipl->id,
                    "nilai_denda"               => $tagihan_ipl->nilai_denda,
                    "kwitansi_referensi_id"     => 0,
                    "diskon_id"                 => 0,
                    "nilai_diskon"              => 0,
                    "nilai_ppn"                 => $tagihan_ipl->ppn > 0 ? 10 : 0,
                    "nilai_tagihan_pemutihan"   => 0,
                    "nilai_denda_pemutihan"     => 0,
                    "nilai_biaya_admin_cara_pembayaran" => 0,
                    "sisa_tagihan"              => 0,
                    "is_tunggakan"              => 0,
                    "service_jenis_id"          => 1,
                    "nilai_pokok"               => $tagihan_ipl->final_nilai_tagihan_tanpa_ppn,
                    "nilai_pemutihan_pokok"     => 0,
                    "nilai_pemutihan_tagihan"   => 0,
                    "nilai_pemutihan_denda"     => 0,
                    "nilai_diskon_pokok"        => 0,
                    "nilai_diskon_tagihan"      => 0,
                    "nilai_ppn_persen"          => $tagihan_ipl->nilai_ppn,
                    "nilai_terbayar"            => $tagihan_ipl->dibayar ?? 0,
                    "nilai_outstanding"         => $tagihan_ipl->final_total
                ]);
            }
            foreach ($this->m_tagihan->get_air($param) as $tagihan_air) {
                array_push($data->pembayaran_detail, [
                    "t_pembayaran_id"           => $data->pembayaran->id,
                    "nilai_tagihan"             => $tagihan_air->final_nilai_tagihan,
                    "nilai_penalti"             => 0,
                    "bayar"                     => $tagihan_air->final_total,
                    "bayar_deposit"             => 0,
                    "service_id"                => $tagihan_air->service_id,
                    "tagihan_service_id"        => $tagihan_air->id,
                    "nilai_denda"               => $tagihan_air->nilai_denda,
                    "kwitansi_referensi_id"     => 0,
                    "diskon_id"                 => 0,
                    "nilai_diskon"              => 0,
                    "nilai_ppn"                 => $tagihan_air->ppn,
                    "nilai_tagihan_pemutihan"   => 0,
                    "nilai_denda_pemutihan"     => 0,
                    "nilai_biaya_admin_cara_pembayaran" => 0,
                    "sisa_tagihan"              => 0,
                    "is_tunggakan"              => 0,
                    "service_jenis_id"          => 2,
                    "nilai_pokok"               => $tagihan_air->final_nilai_tagihan_tanpa_ppn,
                    "nilai_pemutihan_pokok"     => 0,
                    "nilai_pemutihan_tagihan"   => 0,
                    "nilai_pemutihan_denda"     => 0,
                    "nilai_diskon_pokok"        => 0,
                    "nilai_diskon_tagihan"      => 0,
                    "nilai_ppn_persen"          => $tagihan_air->nilai_ppn,
                    "nilai_terbayar"            => $tagihan_air->dibayar ?? 0,
                    "nilai_outstanding"         => $tagihan_air->final_total
                ]);
            }
            $this->db->insert_batch('t_pembayaran_detail', $data->pembayaran_detail);

            $this->db->update("t_tagihan_lingkungan", ['status_tagihan' => 1], ['unit_id' => $unit_id, 'status_tagihan' => 3]);
            $this->db->update("t_tagihan_air", ['status_tagihan' => 1], ['unit_id' => $unit_id, 'status_tagihan' => 3]);
            $message = "$external_id_string - Success ";
        } else {
            $message = "$external_id_string - Failed - Cara Bayar ";
        }

        $response = ['message' => $message];

        $this->db->insert(
            'log_tp',
            [
                'url' => 'https://ems.ciputragroup.com:11443/index.php/api/xendit/xendit_permission',
                'type' => 1, //
                'method' => 'POST',
                'status_code' => '200',
                'vendor_name' => 'Xendit',
                'request'   => json_encode($this->post()),
                'response'    => json_encode($response),
                'created_at'    => date('Y-m-d H:i:s.000'),
                'description' => 'Controller/API/Xendit/index_post'
            ]
        );



        $this->load->library('curl');
        $dataApi = [
            "grant_type"    => "password",
            "client_id"     => "3MVG9G9pzCUSkzZvdA4eKm_jdVZVXQ5U2c0L6gyvdnC.WqwoLIqfj2j8vfExyjTTvNQBdogzzrCLuPpkdmgbt",
            "client_secret" => "C823D7F2BFC8688FF1ADF9A0B8873EFECD9D2F22E85C2538127458A2FCB16F88",
            "username"      => "ciputrapropertyy-cisv@force.com",
            "password"      => "Salesforce2019ZSq9OdbkPDFtmqNvOqDIdBhZ8"
        ];
        $keySF = json_decode($this->curl->simple_post("https://login.salesforce.com/services/oauth2/token", $dataApi));

        if ($checkout_salesforce->salesforce_id) {
            $dateZ = new DateTime(date("Y-m-d H:i:s"), new DateTimeZone("Asia/Jakarta"));
            $dateZ->setTimezone(new DateTimeZone('UTC'));
            $WaktuZulu = $dateZ->format("Y-m-d") . "T" . $dateZ->format("H:i:s.000") . "Z";
            $data = [
                "Status__c"     => "Paid",
                "Paid_Date__c"  => $WaktuZulu
            ];
            $data_string = json_encode($data);
            // write_file("./log/" . date("y-m-d") . '_log_midtrans_to_salesforce.txt', "\n" . date("y-m-d h:i:s") . " = POST !" . json_encode($data_string) . " !", 'a+');
            $ch = curl_init("https://ciputra-sh1.my.salesforce.com/services/data/v46.0/sobjects/Checkout_Invoice__c/$checkout_salesforce->salesforce_id?_HttpMethod=PATCH");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string),
                    "Authorization: Bearer $keySF->access_token"
                )
            );
            $result = curl_exec($ch);

            $this->db->insert(
                'log_tp',
                [
                    'url' => "https://ciputra-sh1.my.salesforce.com/services/data/v46.0/sobjects/Checkout_Invoice__c/$checkout_salesforce->salesforce_id?_HttpMethod=PATCH",
                    'type' => 0, //
                    'method' => 'POST',
                    'status_code' => '200',
                    'vendor_name' => 'TRIMITRASIS',
                    'request'   => json_encode($data_string),
                    'response'    => json_encode($result),
                    'created_at'    => date('Y-m-d H:i:s.000'),
                    'description' => 'Controller/API/Midtrans/index_post'
                ]
            );
        }
        $this->set_response($response, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
    }
    public function index_post_bu($api_key = null)
    {
        $this->load->helper('file');
        write_file("./log/" . date("y-m-d") . '_log_xendit.txt', "\n" . date("y-m-d h:i:s") . " = POST !" . json_encode($this->post()) . " !", 'a+');

        $from = "ems.dbo.";
        $this->load->model('transaksi/m_pembayaran_xendit');
        // $dataPost = json_decode($this->post()[0]);
        // $external_id    = $dataPost->external_id;
        $external_id    = $this->post("external_id");
        $bank_code    = $this->post("bank_code");
        $xendit_id    = $this->post("callback_virtual_account_id");
        // var_dump($this->post());

        $external_id    = explode(";", $external_id);
        // echo("test");
        // var_dump($external_id);
        $cara_pembayaran    = $this->db
            ->select("cara_pembayaran.id,cara_pembayaran.project_id")
            ->from($from . "t_tagihan")
            ->join(
                $from . "cara_pembayaran",
                "cara_pembayaran.jenis_cara_pembayaran_id = 3
                                            AND cara_pembayaran.project_id = t_tagihan.proyek_id",
                "LEFT"
            )
            ->join(
                "bank",
                "bank.id = cara_pembayaran.bank_id",
                "LEFT"
            )
            ->join(
                "bank_jenis",
                "bank_jenis.id = bank.bank_jenis_id",
                "LEFT"
            )
            ->where("bank_jenis.code", $bank_code)
            ->where_in("t_tagihan.id", $external_id)
            ->order_by("bank_jenis.id", "DESC")
            ->get()->row();
        //sebelumnya where bank_jenis.code ada di join bank, sehingga menimbulkan bug 
        // var_dump($cara_pembayaran);
        if (isset($cara_pembayaran->project_id)) {
            $project_id = $cara_pembayaran->project_id;
            $cara_pembayaran = $cara_pembayaran->id;
            $periode = date("Y-m-01");
            $tagihan_air = $this->db->select("
                                CONCAT(service.id,'|',service.service_jenis_id,'|',v_tagihan_air.tagihan_id,'|',isnull(v_tagihan_air.total,0),'|',
								isnull(CASE
								WHEN service.denda_flag = 0 THEN 0
								WHEN v_tagihan_air.nilai_denda_flag = 1 THEN v_tagihan_air.nilai_denda 
								WHEN v_tagihan_air.periode > '$periode' THEN 0
									ELSE
									CASE					
										WHEN v_tagihan_air.denda_jenis_service = 1 
											THEN v_tagihan_air.denda_nilai_service 
										WHEN v_tagihan_air.denda_jenis_service = 2 
											THEN v_tagihan_air.denda_nilai_service *
												(DateDiff
													( MONTH, DATEADD(month,service.denda_selisih_bulan,v_tagihan_air.periode), '$periode' ) 
													+ IIF(" . date("d") . ">=service.denda_tanggal_jt,1,0) 
												)
										WHEN v_tagihan_air.denda_jenis_service = 3 
											THEN 
												(v_tagihan_air.denda_nilai_service* v_tagihan_air.total/ 100 ) 
												* (DateDiff( MONTH, v_tagihan_air.periode, '$periode' ) 
												+ IIF(" . date("d") . ">=service.denda_tanggal_jt,1,0) )
									END 
                                END,0),'|',0,'|',
                                0,'|',
								0) as tagihan")
                ->from($from . "v_tagihan_air")
                // ->where("v_tagihan_air.periode <= '$periode_now'")
                ->join(
                    $from . "service",
                    "service.project_id = $project_id
				AND service.service_jenis_id = 2
				AND service.active = 1
				AND service.delete = 0"
                )
                // ->join($from."v_pemutihan",
                // 		"v_pemutihan.masa_akhir >= GETDATE()
                // 		AND v_pemutihan.masa_awal <= GETDATE()
                // 		AND v_pemutihan.periode_akhir >= v_tagihan_air.periode 
                // 		AND v_pemutihan.periode_awal <= v_tagihan_air.periode 
                // 		AND v_pemutihan.service_jenis_id = 2
                // 		AND v_pemutihan.unit_id  = v_tagihan_air.unit_id",
                // 		"LEFT")
                ->where("v_tagihan_air.status_tagihan = 3")
                ->where_in("v_tagihan_air.t_tagihan_id", $external_id)
                // ->order_by("v_pemutihan.tgl_tambah,periode")
                ->get()->result();
            // var_dump($this->db->last_query());
            $tagihan_lingkungan = $this->db->select("
                                CONCAT(service.id,'|',service.service_jenis_id,'|',v_tagihan_lingkungan.tagihan_id,'|',isnull(v_tagihan_lingkungan.total,0),'|',
								isnull(CASE
									WHEN v_tagihan_lingkungan.periode <= unit_lingkungan.tgl_mulai_denda THEN
										CASE
											WHEN v_tagihan_lingkungan.denda_jenis_service = 1 
												THEN v_tagihan_lingkungan.denda_nilai_service 
											WHEN v_tagihan_lingkungan.denda_jenis_service = 2 
												THEN 
													v_tagihan_lingkungan.denda_nilai_service * 
														(DateDiff
															( MONTH, unit_lingkungan.tgl_mulai_denda, '$periode' ) 
															+ IIF(" . date("d") . ">=service.denda_tanggal_jt,1,0) 
														)
											WHEN v_tagihan_lingkungan.denda_jenis_service = 3 
												THEN 
													( v_tagihan_lingkungan.denda_nilai_service * v_tagihan_lingkungan.total/ 100 ) 
													* (DateDiff( MONTH, unit_lingkungan.tgl_mulai_denda, '$periode' ) 
													+ IIF(" . date("d") . ">=service.denda_tanggal_jt,1,0) )
										END 	
									WHEN v_tagihan_lingkungan.nilai_denda_flag = 1 THEN v_tagihan_lingkungan.nilai_denda 
									WHEN v_tagihan_lingkungan.periode > '$periode' THEN 0
									ELSE
										CASE
											WHEN v_tagihan_lingkungan.denda_jenis_service = 1 
												THEN v_tagihan_lingkungan.denda_nilai_service 
											WHEN v_tagihan_lingkungan.denda_jenis_service = 2 
												THEN v_tagihan_lingkungan.denda_nilai_service * 
													(DateDiff
														( MONTH, DATEADD(MONTH, service.denda_selisih_bulan, v_tagihan_lingkungan.periode), '$periode' ) 
														+ IIF(" . date("d") . ">=service.denda_tanggal_jt,1,0) 
													)
											WHEN v_tagihan_lingkungan.denda_jenis_service = 3 
												THEN 
													( v_tagihan_lingkungan.denda_nilai_service * v_tagihan_lingkungan.total/ 100 ) 
													* (DateDiff( MONTH, v_tagihan_lingkungan.periode, '$periode' ) 
													+ IIF(" . date("d") . ">=service.denda_tanggal_jt,1,0) )
										END 
									END,0),'|',0,'|',
                                0,'|',
								0) as tagihan")
                ->from($from . "v_tagihan_lingkungan")
                // ->where("v_tagihan_lingkungan.periode <= '$periode_now'")
                ->join(
                    $from . "service",
                    "service.project_id = $project_id
				AND service.service_jenis_id = 1
				AND service.active = 1
				AND service.delete = 0"
                )
                // ->join($from."v_pemutihan",
                // 		"v_pemutihan.masa_akhir >= GETDATE()
                // 		AND v_pemutihan.masa_awal <= GETDATE()
                // 		AND v_pemutihan.periode_akhir >= v_tagihan_lingkungan.periode 
                // 		AND v_pemutihan.periode_awal <= v_tagihan_lingkungan.periode 
                // 		AND v_pemutihan.service_jenis_id = 1
                // 		AND v_pemutihan.unit_id  = v_tagihan_lingkungan.unit_id",
                // 		"LEFT")
                ->join(
                    $from . "unit_lingkungan",
                    "unit_lingkungan.unit_id = v_tagihan_lingkungan.unit_id"
                )
                ->where("v_tagihan_lingkungan.status_tagihan = 3")
                ->where_in("v_tagihan_lingkungan.t_tagihan_id", $external_id)
                // ->order_by("v_pemutihan.tgl_tambah,periode")

                ->get()->result();
            // echo("tagihan_lingkungan<pre>");
            //     print_r($tagihan_lingkungan);
            // echo("</pre>");
            // echo("tagihan_air<pre>");
            //     print_r($tagihan_air);
            // echo("</pre>");
            $tagihan = [];
            foreach ($tagihan_air as $k => $v) {
                array_push($tagihan, $v->tagihan);
            }
            foreach ($tagihan_lingkungan as $k => $v) {
                array_push($tagihan, $v->tagihan);
            }
            if (!$tagihan) {
                $message = [
                    'message' => implode(",", $external_id) . "- Failed"
                ];
                $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
                return;
            }
            $success = 0;

            $unit_id = $this->db->select("*")
                ->from($from . "t_tagihan")
                ->where_in("id", $external_id)
                ->get()->row()->unit_id;
            // echo("unit_id<pre>");
            //     print_r($unit_id);
            // echo("</pre>");

            // var_dump($tagihan);
            // var_dump($cara_pembayaran);

            if ($this->m_pembayaran_xendit->save($tagihan, null, $unit_id, $cara_pembayaran, $project_id, 0, 0, date("d/m/Y")))
                $success++;
            if (!$api_key) {
                $this->response(null, 401);
            } else {
                if ($api_key != 'xendit_permission')
                    $this->response(null, 401);
            }

            // $this->some_model->update_user( ... );
            $this->load->library('curl');
            // client_id 3MVG9G9pzCUSkzZvdA4eKm_jdVZVXQ5U2c0L6gyvdnC.WqwoLIqfj2j8vfExyjTTvNQBdogzzrCLuPpkdmgbt
            // client_secret C823D7F2BFC8688FF1ADF9A0B8873EFECD9D2F22E85C2538127458A2FCB16F88
            // username ciputrapropertyy-cisv@force.com
            // password Salesforce12e2wQmbTThC7qXrr20Jm3cvmK
            // url https://ciputra-sh1.my.salesforce.com/services/data/v46.0/sobjects/Checkout_Invoice__c/a036D000002P9yAQAS?_HttpMethod=PATCH

            // client_id 3MVG9G9pzCUSkzZvdA4eKm_jdVZVXQ5U2c0L6gyvdnC.WqwoLIqfj2j8vfExyjTTvNQBdogzzrCLuPpkdmgbt
            // client_secret C823D7F2BFC8688FF1ADF9A0B8873EFECD9D2F22E85C2538127458A2FCB16F88
            // username ciputrapropertyy-cisv@force.com
            // password Salesforce12e2wQmbTThC7qXrr20Jm3cvmK
            // url https://ciputra-sh1.my.salesforce.com/services/data/v46.0/sobjects/Checkout_Invoice__c/a036D000002P9yAQAS?_HttpMethod=PATCH

            // $dataApi = [
            //     "grant_type"    => "password",
            //     "client_id"     => "3MVG9G9pzCUSkzZvdA4eKm_jdVZVXQ5U2c0L6gyvdnC.WqwoLIqfj2j8vfExyjTTvNQBdogzzrCLuPpkdmgbt",
            //     "client_secret" => "C823D7F2BFC8688FF1ADF9A0B8873EFECD9D2F22E85C2538127458A2FCB16F88",
            //     "username"      => "ciputrapropertyy-cisv@force.com",
            //     "password"      => "Salesforce12IIrpCWdsKQAjOQq4VUqpyGTp"
            // ];

            $dataApi = [
                "grant_type"    => "password",
                "client_id"     => "3MVG9G9pzCUSkzZvdA4eKm_jdVZVXQ5U2c0L6gyvdnC.WqwoLIqfj2j8vfExyjTTvNQBdogzzrCLuPpkdmgbt",
                "client_secret" => "C823D7F2BFC8688FF1ADF9A0B8873EFECD9D2F22E85C2538127458A2FCB16F88",
                "username"      => "ciputrapropertyy-cisv@force.com",
                "password"      => "Salesforce2019ZSq9OdbkPDFtmqNvOqDIdBhZ8"
            ];
            $this->load->helper('file');
            write_file("./log/" . date("y-m-d") . '_log_xendit_to_salesforce.txt', "\n" . date("y-m-d h:i:s") . " = POST (Get Key) !" . json_encode($dataApi) . " !", 'a+');
            $keySF = json_decode($this->curl->simple_post("https://login.salesforce.com/services/oauth2/token", $dataApi));
            // echo("key");
            // var_dump($keySF);

            // $external_id    = $this->post("external_id");
            // $bank_code    = $this->post("bank_code");
            // $xendit_id    = $this->post("callback_virtual_account_id");

            $checkout_data = $this->db->select("*")
                ->from($from . "checkout_salesforce")
                ->where("external_id", $this->post("external_id"))
                ->where("bank_code", $bank_code)
                ->where("xendit_id", $xendit_id)
                ->order_by("id", "DESC")
                ->get()->row();
            if ($checkout_data->salesforce_id) {
                $dateZ = new DateTime(date("Y-m-d H:i:s"), new DateTimeZone("Asia/Jakarta"));
                $dateZ->setTimezone(new DateTimeZone('UTC'));
                $WaktuZulu = $dateZ->format("Y-m-d") . "T" . $dateZ->format("H:i:s.000") . "Z";
                $data = [
                    "Status__c"     => "Paid",
                    "Paid_Date__c"  => $WaktuZulu
                ];
                // "2019-09-18T14:02:40.123Z"

                // string(30) "2019-10-07CEST09:25:38.0007200"

                // var_dump($data);
                // $data = array("name" => "Hagrid", "age" => "36");                                                                    
                $data_string = json_encode($data);
                // var_dump($data_string);
                $this->load->helper('file');
                write_file("./log/" . date("y-m-d") . '_log_xendit_to_salesforce.txt', "\n" . date("y-m-d h:i:s") . " = POST !" . json_encode($data_string) . " !", 'a+');
                $ch = curl_init("https://ciputra-sh1.my.salesforce.com/services/data/v46.0/sobjects/Checkout_Invoice__c/$checkout_data->salesforce_id?_HttpMethod=PATCH");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data_string),
                        "Authorization: Bearer $keySF->access_token"
                    )
                );
                $result = curl_exec($ch);
                // echo("notif");
                // var_dump($result);
                // $this->curl->option([]); 
                // $this->curl->create("https://ciputra-sh1--appsdev.my.salesforce.com/services/data/v46.0/sobjects/Checkout_Invoice__c/$checkout_data->salesforce_id?_HttpMethod=PATCH");

                // $this->curl->http_header("Authorization","Bearer abc$keySF->access_token");
                // $this->curl->options(array(CURLOPT_HTTPHEADER => "Authorization : Bearer abc$keySF->access_token"));

                // $this->curl->post($dataApi);
                // var_dump($this->curl);
                // var_dump($this->curl->execute());
                // var_dump($this->curl->error_code); // int
                // var_dump($this->curl->error_string);

                // // Information
                // var_dump($this->curl->info); // array


                // $result = $this->curl
                //                     // ->http_header("Authorization","Bearer abc".$keySF->access_token)
                //                     ->simple_post(,$dataApi);
                // var_dump($dataApi);
                // var_dump($result);


                if ($success > 0)
                    $message = [
                        'message' => implode(",", $external_id) . "- Success"
                    ];
                else
                    $message = [
                        'message' => implode(",", $external_id) . "- Failed "
                    ];
            } else {
                $message = [
                    'message' => implode(",", $external_id) . "- Failed : Data Check Out tidak ada"
                ];
            }
        } else {
            $message = [
                'message' => implode(",", $external_id) . "- Failed : Cara Pembayaran"
            ];
        }
        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
    }
}
