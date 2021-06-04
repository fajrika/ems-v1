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
class Midtrans extends REST_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->database();
        // $this->db->database = "ems.dbo";
    }
    private function _store_log($url, $type, $method, $status_code, $vendor_name, $request, $response, $created_at, $description)
    {
        // var_dump('store_log');
        $this->db->insert(
            'log_tp',
            [
                'url'           => $url,
                'type'          => $type,
                'method'        => $method,
                'status_code'   => $status_code,
                'vendor_name'   => $vendor_name,
                'request'       => $request,
                'response'      => $response,
                'created_at'    => $created_at,
                'description'   => $description
            ]
        );
        return true;
    }
    public function index_post($api_key = null)
    {
        $url = "https://ems.ciputragroup.com:11443/index.php/api/midtrans/$api_key";
        $path = 'Controller/API/Midtrans/index_post';
        $response = ['status' => "sukses"];
        if (!$api_key || $api_key != 'midtrans_permission') {
            $response = ['status' => 'Failed Api Key'];
            $this->_store_log($url, 1, 'POST', '200', 'MIDTRANS', json_encode($this->post()), json_encode($response), date('Y-m-d H:i:s.000'), $path);
            $this->response($response, 401);
        } else {

            $salesforce_id  = $this->post("order_id") ?? null;
            $bank_code    = $this->post("va_numbers")[0]['bank'] ?? 'bca';
            $checkout_salesforce = $this->db
                ->select("*")
                ->from("checkout_salesforce")
                ->where("salesforce_id", $salesforce_id)
                ->get()->row() ?? (object)['salesforce_id' => null];

            if ($checkout_salesforce->salesforce_id) {
                $external_id = $checkout_salesforce->external_id ?? null;
                $cara_pembayaran = $this->db
                    ->select("cara_pembayaran.*")
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
                    ->where_in("t_tagihan.id", explode(';', $external_id)) //6927692
                    ->order_by("bank_jenis.id", "DESC")
                    ->get()->row();

                if (in_array($this->post('transaction_status'), ['settlement', 'capture'])) {
                    $parse_status = [
                        "Status__c" => "Paid",
                        "Paid_Date__c" => substr(gmdate("Y-m-d\TH:i:s.U"), 0, 23) . 'Z'
                    ];
                    $unit_id =
                        $this->db->select('unit_id')
                        ->from('t_tagihan')
                        ->where_in('id', explode(';', $external_id))
                        ->get()->row()->unit_id ?? 0;

                    $this->load->model("core/m_tagihan");
                    $this->load->model('transaksi/m_payment');

                    $data = (object)[
                        "pembayaran" => (object)[
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
                        ]
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
                    $message = "$external_id - Success ";

                    $this->load->library('curl');
                    $keySF = json_decode($this->curl->simple_post("https://login.salesforce.com/services/oauth2/token", [
                        "grant_type"    => "password",
                        "client_id"     => "3MVG9G9pzCUSkzZvdA4eKm_jdVZVXQ5U2c0L6gyvdnC.WqwoLIqfj2j8vfExyjTTvNQBdogzzrCLuPpkdmgbt",
                        "client_secret" => "C823D7F2BFC8688FF1ADF9A0B8873EFECD9D2F22E85C2538127458A2FCB16F88",
                        "username"      => "ciputrapropertyy-cisv@force.com",
                        "password"      => "Salesforce2019ZSq9OdbkPDFtmqNvOqDIdBhZ8"
                    ]));


                    $ch = curl_init("https://ciputra-sh1.my.salesforce.com/services/data/v46.0/sobjects/Checkout_Invoice__c/$checkout_salesforce->salesforce_id?_HttpMethod=PATCH");
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parse_status));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt(
                        $ch,
                        CURLOPT_HTTPHEADER,
                        array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen(json_encode($parse_status)),
                            "Authorization: Bearer $keySF->access_token"
                        )
                    );
                    $response = curl_exec($ch);

                    $this->_store_log(
                        "https://ciputra-sh1.my.salesforce.com/services/data/v46.0/sobjects/Checkout_Invoice__c/$checkout_salesforce->salesforce_id?_HttpMethod=PATCH",
                        0,
                        'POST',
                        '200',
                        'TROMITRASIS',
                        json_encode($this->post()),
                        json_encode($response),
                        date('Y-m-d H:i:s.000'),
                        $path
                    );
                    $this->set_response(['status' => "Sukses Settlement/Capture"], REST_Controller::HTTP_OK);
                } else {
                    $this->_store_log($url, 1, 'POST', '200', 'MIDTRANS', json_encode($this->post()), json_encode($response), date('Y-m-d H:i:s.000'), $path);
                    $this->set_response(['status' => "Sukses Expired/Pending"], REST_Controller::HTTP_OK);
                }
            }else{

                $this->_store_log($url, 1, 'POST', '200', 'MIDTRANS', json_encode($this->post()), json_encode($response), date('Y-m-d H:i:s.000'), $path);

                $this->set_response(['status' => "Salesforce_id Tidak Di Temukan"], REST_Controller::HTTP_OK);
            }
        }
    }
    public function index_bu_post($api_key = null)
    {
        // pembayaran melalui midtrans
        $this->load->helper('file');
        write_file("./log/" . date("y-m-d") . '_log_xendit.txt', "\n" . date("y-m-d h:i:s") . " = POST !" . json_encode($this->post()) . " !", 'a+');

        // transaction_status = capture/settlement/pending/deny/cancel/expire/refund

        if ($this->post("transaction_status") == 'settlement') {
            $from = "ems.dbo.";
            $this->load->model('transaksi/m_pembayaran_xendit');
            $external_id    = $this->post("order_id") ? $this->post("order_id") : null; //a022u0000017gPoAAI
            $external_id = $this->db->select("external_id")
                ->from($from . "checkout_salesforce")
                ->where("salesforce_id", $external_id)
                ->get()->row(); // 7942744
            $external_id = $external_id ? $external_id->external_id : 0;
            $external_id_full = $external_id;
            $bank_code    = $this->post("va_numbers")[0]['bank']; //bca

            $external_id    = explode(";", $external_id);
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
                    "bank_jenis.id = bank.bank_jenis_id", //bca
                    "LEFT"
                )
                ->where("bank_jenis.code", $bank_code)
                ->where_in("t_tagihan.id", $external_id) //6927692
                ->order_by("bank_jenis.id", "DESC")
                ->get()->row();
            //sebelumnya where bank_jenis.code ada di join bank, sehingga menimbulkan bug 

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
                    ->join(
                        $from . "service",
                        "service.project_id = $project_id
                    AND service.service_jenis_id = 2
                    AND service.active = 1
                    AND service.delete = 0"
                    )
                    ->where("v_tagihan_air.status_tagihan = 3")
                    ->where_in("v_tagihan_air.t_tagihan_id", $external_id)
                    ->get()->result();
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
                    ->join(
                        $from . "service",
                        "service.project_id = $project_id
                    AND service.service_jenis_id = 1
                    AND service.active = 1
                    AND service.delete = 0"
                    )
                    ->join(
                        $from . "unit_lingkungan",
                        "unit_lingkungan.unit_id = v_tagihan_lingkungan.unit_id"
                    )
                    ->where("v_tagihan_lingkungan.status_tagihan = 3")
                    ->where_in("v_tagihan_lingkungan.t_tagihan_id", $external_id)

                    ->get()->result();
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

                if ($this->m_pembayaran_xendit->save($tagihan, null, $unit_id, $cara_pembayaran, $project_id, 0, 0, date("d/m/Y")))
                    $success++;
                if (!$api_key) {
                    $this->response(null, 401);
                } else {
                    if ($api_key != 'midtrans_permission')
                        $this->response(null, 401);
                }
                $this->load->library('curl');
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
                $checkout_data = $this->db->select("*")
                    ->from($from . "checkout_salesforce")
                    ->where("external_id", $external_id_full)
                    ->where("bank_code", $bank_code)
                    // ->where("xendit_id",$midtrans_id)
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
                    $data_string = json_encode($data);
                    $this->load->helper('file');
                    write_file("./log/" . date("y-m-d") . '_log_midtrans_to_salesforce.txt', "\n" . date("y-m-d h:i:s") . " = POST !" . json_encode($data_string) . " !", 'a+');
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
}
