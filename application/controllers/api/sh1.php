<?php

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class sh1 extends REST_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->db->database = "ems";
    }
    private function _store_log($url, $type, $method, $status_code, $vendor_name, $request, $response, $description)
    {
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
                'created_at'    => date('Y-m-d H:i:s.000'),
                'description'   => $description
            ]
        );
        return true;
    }
  
    public function index_get($api_key = null, $type = null)
    {
        $url = "https://ems.ciputragroup.com:11443/index.php/api/sh1/$api_key/$type";
        $path = 'Controller/API/sh1/index_get';
        $vendor_name = "SH1";
        $method = "GET";
        $request = json_encode($this->get());

        $this->load->helper('file');
        write_file("./log/" . date("y-m-d") . '_log_salesforce.txt', "\n" . date("y-m-d h:i:s") . " = GET !" . json_encode($this->input->get("uid")) . " !", 'a+');

        $this->load->database();

        $from = "ems.dbo";
        $this->load->model("core/m_tagihan");

        $result = (object)[];

        $uid = $this->input->get("uid");
        $response = (object)['status' => 200];
        if (!$api_key) {
            $response->status = 401;
            $response->message = "Api Key tidak diterima";
            $this->_store_log($url, 1, $method, $response->status, $vendor_name, $request, json_encode($response), $path);
            $this->response($response,$response->status);
            return 1;
        } else {
            if ($api_key != '12hrbhfildksvnhjfrvqehjw'){
                $response->status = 401;
                $response->message = "Api Key salah";
                $this->_store_log($url, 1, $method, $response->status, $vendor_name, $request, json_encode($response), $path);
                $this->response($response,$response->status);
                return 1;
            }
        }
        if(!$uid){
            $response->status = 400;
            $response->message = "UID tidak diterima";
            $this->_store_log($url, 1, $method, $response->status, $vendor_name, $request, json_encode($response), $path);
            $this->response($response,$response->status);
            return 1;
        }
        if(!$type){
            $response->status = 400;
            $response->message = "Type tidak diterima";
            $this->_store_log($url, 1, $method, $response->status, $vendor_name, $request, json_encode($response), $path);
            $this->response($response,$response->status);
            return 1;
        }

        $resultUnit = $this->db->select("
                                        unit.id as unit_id,
                                        kawasan.name as kawasan,
                                        blok.name as blok,
                                        project.name as project,
                                        unit.no_unit,
                                        xendit_sub_account.sub_account as xendit,
                                        pt_apikey.apikey as midtrans
                                        ")
            ->from("unit")
            ->join(
                'pt_apikey',
                'pt_apikey.pt_id = unit.pt_id',
                "LEFT"
            )
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
            ->join(
                "xendit_sub_account",
                "xendit_sub_account.project_id = unit.project_id
                                        AND xendit_sub_account.pt_id = unit.pt_id",
                "LEFT"
            )
            ->where("CONCAT(project.source_id,kawasan.code,blok.code,'/',unit.no_unit)", "$uid")
            ->get()->row();

        if(!$resultUnit){
            $response->status = 400;
            $response->message = "Unit tidak ada di sistem EMS, segera Hubungi Kantor Marketing";
            $this->_store_log($url, 1, $method, $response->status, $vendor_name, $request, json_encode($response), $path);
            $this->response($response,$response->status);
            return 1;
        }

        $total = (object)[];

        if ($type == "bill") {
            $param = [
                'unit_id' => $resultUnit->unit_id,
                'status_tagihan' => 0,
                'date' => date("Y-m-d")
            ];
            $tagihans = $this->m_tagihan->get_tagihan_gabungan($param);

            $total->tagihan_air = 0;
            $total->tagihan_lingkungan = 0;
            $total->tagihan_lain = 0;
            $total->total_denda = 0;
            $total->total = 0;

            $result->va = $this->m_tagihan->get_va($resultUnit->unit_id);
            $result->tagihan = [];
            foreach ($tagihans as $key => $tagihan) {
                if (isset($tagihan->air->final_nilai_tagihan))
                    $total->tagihan_air += $tagihan->air->final_nilai_tagihan;
                if (isset($tagihan->lingkungan->final_nilai_tagihan))
                    $total->tagihan_lingkungan += $tagihan->lingkungan->final_nilai_tagihan;

                $total->tagihan_lain         += 0;

                if (isset($tagihan->air->nilai_denda))
                    $total->total_denda += $tagihan->air->nilai_denda;
                if (isset($tagihan->lingkungan->nilai_denda))
                    $total->total_denda += $tagihan->lingkungan->nilai_denda;

                if (isset($tagihan->lingkungan->id)) {
                    $tagihan_id = $tagihan->lingkungan->external_id;
                    $periode = $tagihan->lingkungan->periode;
                } elseif (isset($tagihan->air->id)) {
                    $tagihan_id = $tagihan->air->external_id;
                    $periode = $tagihan->air->periode;
                }
                $tagihan_air = isset($tagihan->air->final_nilai_tagihan) ? $tagihan->air->final_nilai_tagihan : 0;
                $tagihan_lingkungan = isset($tagihan->lingkungan->final_nilai_tagihan) ? $tagihan->lingkungan->final_nilai_tagihan : 0;
                $total_denda = (isset($tagihan->air->final_nilai_denda) ? $tagihan->air->final_nilai_denda : 0)
                    + (isset($tagihan->lingkungan->final_nilai_denda) ? $tagihan->lingkungan->final_nilai_denda : 0);
                array_push($result->tagihan, (object)[
                    "uid" => $uid,
                    "tagihan_id" => $tagihan_id,
                    "periode" => $periode,
                    "tagihan_air" => $tagihan_air,
                    "tagihan_lingkungan" => $tagihan_lingkungan,
                    "tagihan_lain" => 0,
                    "total_denda" => $total_denda,
                    "total" => $tagihan_air + $tagihan_lingkungan + $total_denda
                ]);
            }
            $result->tagihan = array_reverse($result->tagihan);
            $total->total = $total->tagihan_air + $total->tagihan_lingkungan + $total->tagihan_lain + $total->total_denda;
        } else if ($type == "history") {
            $param = [
                'unit_id' => $resultUnit->unit_id,
                'status_tagihan' => 1,
                'date'  => date("Y-m-d")
            ];
            $tagihans = $this->m_tagihan->get_tagihan_gabungan($param);

            $total->tagihan_air = 0;
            $total->tagihan_lingkungan = 0;
            $total->tagihan_lain = 0;
            $total->total_denda = 0;
            $total->total = 0;

            $result->va = $this->m_tagihan->get_va($resultUnit->unit_id);
            $result->tagihan = [];
            $iterasi = 0;
            $tagihans = array_reverse($tagihans);
            foreach ($tagihans as $key => $tagihan) {
                if (isset($tagihan->air->final_nilai_tagihan))
                    $total->tagihan_air += $tagihan->air->final_nilai_tagihan;
                if (isset($tagihan->lingkungan->final_nilai_tagihan))
                    $total->tagihan_lingkungan += $tagihan->lingkungan->final_nilai_tagihan;

                $total->tagihan_lain         += 0;

                if (isset($tagihan->air->nilai_denda))
                    $total->total_denda += $tagihan->air->nilai_denda;
                if (isset($tagihan->lingkungan->nilai_denda))
                    $total->total_denda += $tagihan->lingkungan->nilai_denda;

                if (isset($tagihan->lingkungan->id)) {
                    $tagihan_id = $tagihan->lingkungan->external_id;
                    $periode = $tagihan->lingkungan->periode;
                } elseif (isset($tagihan->air->id)) {
                    $tagihan_id = $tagihan->air->external_id;
                    $periode = $tagihan->air->periode;
                }
                $tagihan_air = isset($tagihan->air->final_nilai_tagihan) ? $tagihan->air->final_nilai_tagihan : 0;
                $tagihan_lingkungan = isset($tagihan->lingkungan->final_nilai_tagihan) ? $tagihan->lingkungan->final_nilai_tagihan : 0;
                $total_denda = (isset($tagihan->air->final_nilai_denda) ? $tagihan->air->final_nilai_denda : 0)
                    + (isset($tagihan->lingkungan->final_nilai_denda) ? $tagihan->lingkungan->final_nilai_denda : 0);

                $status_tagihan = "Telah Lunas di Bayar";
                if (isset($tagihan->air))
                    if (isset($tagihan->air->status_tagihan))
                        if ($tagihan->air->status_tagihan == 4)
                            $status_tagihan = "Telah di Bayar Sebagian";
                if (isset($tagihan->lingkungan))
                    if (isset($tagihan->lingkungan->status_tagihan))
                        if ($tagihan->lingkungan->status_tagihan == 4)
                            $status_tagihan = "Telah di Bayar Sebagian";

                array_push($result->tagihan, (object)[
                    "uid" => $uid,
                    "tagihan_id" => $tagihan_id,
                    "periode" => $periode,
                    "tagihan_air" => $tagihan_air,
                    "tagihan_lingkungan" => $tagihan_lingkungan,
                    "tagihan_lain" => 0,
                    "total_denda" => $total_denda,
                    "total" => $tagihan_air + $tagihan_lingkungan + $total_denda,
                    "status_tagihan" => $status_tagihan
                ]);
                $iterasi++;
                if ($iterasi == 12)
                    break;
            }
            $total->total = $total->tagihan_air + $total->tagihan_lingkungan + $total->tagihan_lain + $total->total_denda;
        } else
            $this->response(null, 400);

        $result->info = (object)[];
        $result->info->project  = $resultUnit->project;
        $result->info->kawasan  = $resultUnit->kawasan;
        $result->info->blok     = $resultUnit->blok;
        $result->info->no_unit  = $resultUnit->no_unit;
        $result->info->xendit = $resultUnit->xendit;
        $result->info->midtrans = $resultUnit->midtrans;

        $result->summary = $total;
        $this->db->insert(
            'log_tp',
            [
                'url' => $url,
                'type' => 1,
                'method' => 'GET',
                'status_code' => '200',
                'vendor_name' => 'TRIMITRASIS',
                'request'   => json_encode($this->get()),
                'response'    => json_encode($result),
                'created_at'    => date('Y-m-d H:i:s.000'),
                'description' => 'Controller/API/sh1/index_get'
            ]
        );
        $response = $result;
        $response->status = 200;
        $response->message = 'Berhasil mengambil data tagihan';

        $this->_store_log($url, 1, $method, $response->status, $vendor_name, $request, json_encode($response), $path);
        $this->response($response,$response->status);
        return 1;
    }
}
