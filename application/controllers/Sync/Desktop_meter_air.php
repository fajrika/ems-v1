<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Desktop_meter_air extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('m_login');
        if (!$this->m_login->status_login())
            redirect(site_url());
        $this->load->model('Sync/m_desktop_transaksi_lingkungan');
        $this->load->model('m_core');
        global $jabatan;
        $jabatan = $this->m_core->jabatan();
        global $project;
        $project = $this->m_core->project();
        global $menu;
        $menu = $this->m_core->menu();
    }
    
    public function CSV(){
        $this->load->view('core/header');
        $this->load->model('alert');
        $this->alert->css();

        $this->load->view('core/side_bar', ['menu' => $GLOBALS['menu']]);
        $this->load->view('core/top_bar', ['jabatan' => $GLOBALS['jabatan'], 'project' => $GLOBALS['project']]);
        $this->load->view('core/body_header', ['title' => 'Sync > Desktop Tagihan Lingkungan', 'subTitle' => 'Add']);
        $this->load->view('sync/desktop_meter_air',[
            'project'   => $this->db->from("project")->get()->result()
        ]);
        $this->load->view('core/body_footer');
        $this->load->view('core/footer');
    }
    public function get_header(){
        $nameFile = $this->input->get('nameFile');
        $file = fopen("files/meter_air/".$nameFile,"r");
        $header = explode(';',fgetcsv($file)[0]);
        echo(json_encode($header));
        
    }
    public function proses(){
        $this->load->model('transaksi/m_meter_air');
        $this->load->model('m_core');        
        set_time_limit(500);
        $nameFile = $this->input->get('nameFile');
        $file = fopen("files/meter_air/".$nameFile,"r");
        $i = 0;
        $data = (object)[
            'r_unit' => $this->input->get('unit'),
            'r_meter_awal' => $this->input->get('meter_awal'),
            'r_meter_akhir' => $this->input->get('meter_akhir'),
            'periode' => $this->input->get('periode'),
            'project_id' => $this->input->get('project_id'),            
            'header' => [],
            'body' => []
        ];
        // $data->periode = $this->input->get('periode');
        // $data->project_id = $this->input->get('project_id');
        $save = (object)[
            'meter' => 0,
            'meter_tagihan' => 0
        ];
        while(! feof($file)){
            if($i == 0){
                $data->header = explode(';',fgetcsv($file)[0]);
            }else{
                $temp = explode(';',fgetcsv($file)[0]);

                if(isset($temp[$data->r_meter_akhir])){
                    array_push($data->body,(object)[]);
                    $row = count($data->body)-1;
                    
                    $data->body[$row]->unit = $temp[$data->r_unit];
                    $data->body[$row]->meter_awal = $temp[$data->r_meter_awal];
                    $data->body[$row]->meter_akhir = $temp[$data->r_meter_akhir];
                    
                    $unit = $this->db->select("unit.id")
                                ->from("unit")
                                ->join("blok",
                                        "blok.id = unit.blok_id")
                                ->where("unit.project_id",$data->project_id)
                                ->where("concat(blok.code,'/',unit.no_unit) = '" . $data->body[$row]->unit . "'")
                                ->get()->row();
                    
                    $data->body[$row]->unit_id = isset($unit->id)?$unit->id:0;         

                    // validasi
                    if( $data->body[$row]->unit_id != 0 
                        && !($data->body[$row]->meter_awal == 0 && $data->body[$row]->meter_akhir)){  
                            echo("<pre>");
                            print_r([
                                "meter_awal"    => $data->body[$row]->meter_awal,
                                "meter_akhir"   => $data->body[$row]->meter_akhir,
                                "periode"       => $data->periode,
                                "unit_id"       => $data->body[$row]->unit_id 
                            ]);
                            echo("</pre>");
                             
                        if($data->body[$row]->meter_akhir < $data->body[$row]->meter_awal){
                            var_dump("ajax_save_meter_api_4");
                            $this->m_meter_air->ajax_save_meter_api_4(
                                $data->body[$row]->meter_awal,
                                $data->body[$row]->meter_akhir,
                                $data->periode,
                                $data->body[$row]->unit_id
                            );
                            $save->meter_tagihan++;
                        }else{
                            var_dump("ajax_save_meter_api_3");
                            $this->m_meter_air->ajax_save_meter_api_3(
                                $data->body[$row]->meter_awal,
                                $data->body[$row]->meter_akhir,
                                $data->periode,
                                $data->body[$row]->unit_id
                            );
                            $save->meter++;
                        }
                    }
                }
            }
            
            if($i == 100)
                break;
            
            $i++;            
        }
        
        // echo("<pre>");
        //     print_r($data); 
        // echo("</pre>");        
        
        echo(json_encode($save));
        // $dataPeriode = $this->input->GET('periode');

        // $this->load->library('upload');
        // $data = array(
        //                 'upload_data' => $this->upload->data(),
        //                 'test' => $this->upload->do_upload('file'),
        //                 'test2' => $this->input->post('fileCSV'),
        //                 'test3' => $_FILES
        //             );

        // echo(json_encode($dataPeriode));
        // echo("<pre>");
        //     print_r($data);
        // echo("</pre>");        
        
    }

}