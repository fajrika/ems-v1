<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class P_transaksi_meter_air extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->model('m_login');
		if(!$this->m_login->status_login()) redirect(site_url());
		$this->load->model('transaksi/m_meter_air');
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
		$data = $this->m_meter_air->get();
		$this->load->view('core/header');
		$this->load->view('core/side_bar',['menu' => $GLOBALS['menu']]);
		$this->load->view('core/top_bar',['jabatan' => $GLOBALS['jabatan'],'project' => $GLOBALS['project']]);
		$this->load->view('core/body_header',['title' => 'Transaksi Service > Pencatatan Meter Air > Meter Air','subTitle' => 'List']);
		$this->load->view('proyek/transaksi/meter_air/view',['data' => $data]);
		$this->load->view('core/body_footer');
		$this->load->view('core/footer');
	}
	public function add()
	{
		$kode_cust = $this->m_core->project()->code."/CUST/".date("Y")."/".$this->m_core->numberToRomanRepresentation($this->m_core->project()->id)."/".($this->m_meter_air->last_id()+1);
		$dataPT = $this->m_meter_air->getPT();
		$kawasan = $this->m_meter_air->getKawasan();
		
	    $this->load->view('core/header');
		$this->load->model('alert');
		$this->alert->css();
		$this->load->view('core/side_bar',['menu' => $GLOBALS['menu']]);
		$this->load->view('core/top_bar');
		$this->load->view('core/body_header',['title' => 'Transaksi Service > Pencatatan Meter Air > Meter Air', 'subTitle' => 'Add']);
		$this->load->view('proyek/transaksi/meter_air/add',['dataPT' => $dataPT,'kode_cust'=>$kode_cust,'kawasan'=>$kawasan]);
		$this->load->view('core/body_footer');
		$this->load->view('core/footer');
	}
	public function save()
	{		
    	if (!empty($this->input->post()))
    	{
    		$status = $this->m_meter_air->save([
    			'code' 			=> $this->input->post('code'),
    			'pt_id' 		=> $this->input->post('pt_id'),
    			'unit' 			=> $this->input->post('unit'),
    			'name' 			=> $this->input->post('name'),
    			'address' 		=> $this->input->post('alamat_domisili'),
    			'email' 		=> $this->input->post('email'),
    			'ktp' 			=> $this->input->post('nik'),
    			'ktp_address' 	=> $this->input->post('alamat_ktp'),
    			'mobilephone1' 	=> $this->input->post('mobile_phone_1'),
    			'mobilephone2' 	=> $this->input->post('mobile_phone_2'),
    			'homephone' 	=> $this->input->post('home_phone'),
    			'officephone' 	=> $this->input->post('office_phone'),
    			'npwp_no' 		=> $this->input->post('nomor_npwp'),
    			'npwp_name' 	=> $this->input->post('nama_npwp'),
    			'npwp_address' 	=> $this->input->post('alamat_npwp'),
    			'description' 	=> $this->input->post('keterangan'),
    			'active' 		=> 1,
    			'delete' 		=> 0
    		]);
		}

		var_dump($this->input->post('nik'));
		$this->load->model('alert');
		$this->load->model('m_golongan');

		$this->load->view('core/header');
		$this->alert->css();
		
		$kode_cust = "CG/CUST/".date("Y")."/".$this->m_core->numberToRomanRepresentation($this->m_core->project()->id)."/".($this->m_meter_air->last_id()+1);
		$dataPT = $this->m_meter_air->getPT();
		$this->load->view('core/side_bar',['menu' => $GLOBALS['menu']]);
		$this->load->view('core/top_bar');
		$this->load->view('core/body_header',['title' => 'Transaksi Service > Pencatatan Meter Air > Meter Air', 'subTitle' => 'Add']);
		$this->load->view('proyek/transaksi/meter_air//add',['dataPT' => $dataPT,'kode_cust'=>$kode_cust]);
		$this->load->view('core/body_footer');
		$this->load->view('core/footer');
	
        	if (!empty($this->input->post()))
	        {
		if($status == 'success')
			$this->load->view('core/alert',['title' => 'Berhasil','text'=>'Data Berhasil di Tambah','type'=>'success']);
		elseif($status == 'double')
			$this->load->view('core/alert',['title' => 'Gagal','text'=>'Data Inputan suda Ada','type'=>'danger']);
		}			
	}
	public function edit()
    {
        $status = 0;
        if ($this->input->post('pt_id')) {
            $this->load->model('alert');

            $status = $this->m_meter_air->edit([
				'id' 			=> $this->input->get('id'),
				'pt_id' 		=> $this->input->post('pt_id'),
				'unit' 			=> $this->input->post('unit'),
				'name' 			=> $this->input->post('name'),
				'address' 		=> $this->input->post('alamat_domisili'),
				'email' 		=> $this->input->post('email'),
				'ktp' 			=> $this->input->post('nik'),
				'ktp_address' 	=> $this->input->post('alamat_ktp'),
				'mobilephone1' 	=> $this->input->post('mobile_phone_1'),
				'mobilephone2' 	=> $this->input->post('mobile_phone_2'),
				'homephone' 	=> $this->input->post('home_phone'),
				'officephone' 	=> $this->input->post('office_phone'),
				'npwp_no' 		=> $this->input->post('nomor_npwp'),
				'npwp_name' 	=> $this->input->post('nama_npwp'),
				'npwp_address' 	=> $this->input->post('alamat_npwp'),
				'description' 	=> $this->input->post('keterangan'),
				'active' 		=> $this->input->post('status'),
				'delete' 		=> 0
            ]);
            $this->alert->css();
        }

        if ($this->m_meter_air->cek($this->input->get('id'))) {
            $this->load->model('m_log');
            $data = $this->m_log->get('customer', $this->input->get('id'));
			$this->load->view('core/header');
			
            $kode_cust = "CG/CUST/".date("Y")."/".$this->m_core->numberToRomanRepresentation($this->m_core->project()->id)."/".($this->m_meter_air->last_id()+1);
			$dataPT = $this->m_meter_air->getPT();
			$dataSelect = $this->m_meter_air->getSelect($this->input->get('id'));
			$this->load->view('core/side_bar',['menu' => $GLOBALS['menu']]);
			$this->load->view('core/top_bar');
			$this->load->view('core/body_header',['title' => 'Transaksi Service > Pencatatan Meter Air > Meter Air', 'subTitle' => 'Edit']);
			$this->load->view('proyek/transaksi/meter_air//edit',['data'=>$data,'dataPT' => $dataPT,'dataSelect'=> $dataSelect]);
			$this->load->view('core/body_footer');
			$this->load->view('core/footer');
        } else {
            redirect(site_url().'/P_master_mappingCoa');
        }

        if ($status == 'success') {
            $this->load->view('core/alert', ['title' => 'Berhasil', 'text' => 'Data Berhasil di Tambah', 'type' => 'success']);
        } elseif ($status == 'double') {
            $this->load->view('core/alert', ['title' => 'Gagal | Double', 'text' => 'Data Inputan suda Ada', 'type' => 'danger']);
        } 
	}
	public function ajax_get_blok(){
		echo json_encode($this->m_meter_air->ajax_get_blok($this->input->get('id')));
	}
	public function ajax_get_unit(){
		
        ini_set('memory_limit', '-1');
        ini_set('sqlsrv.ClientBufferMaxKBSize','5242880'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size','5242880'); // Setting to 512M - for pdo_sqlsrv
        ini_set('max_execution_time','-1'); 
		echo json_encode($this->m_meter_air->ajax_get_unit($this->input->get('kawasan'),$this->input->get('blok'),$this->input->get('periode')));
	}
	public function ajax_save_meter(){
		echo json_encode($this->m_meter_air->ajax_save_meter($this->input->get('meter'),$this->input->get('periode'),$this->input->get('unit_id')));
	}
	public function auto_generate($project_id,$periode){
		$t_pencatatan_meter_air = $this->db
            ->select('t_pencatatan_meter_air.*')
			->from('t_pencatatan_meter_air')
			->join('unit','unit.id = t_pencatatan_meter_air.unit_id')
			->join('t_tagihan_air', 't_tagihan_air.periode=t_pencatatan_meter_air.periode AND t_tagihan_air.unit_id=t_pencatatan_meter_air.unit_id', 'LEFT')
			->where("unit.project_id = $project_id")
			->where("t_pencatatan_meter_air.periode", $periode)
			->where("t_tagihan_air.id is null")
			->get()
            ->result();

		$periode = explode('-',$periode);
		$periode = $periode[1].'-'.$periode[0];
		foreach ($t_pencatatan_meter_air as $ind => $el) {
			echo("<pre>");
				print_r($el);
			echo("</pre>");
			echo json_encode($this->m_meter_air->ajax_save_meter($el->meter_akhir,$periode,$el->unit_id));
		}
	}

    public function sync_unit()
    {
        $project_id = $this->m_core->project()->id;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://watermeter-api-ems.ciputragroup.com:11442/pull-request',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS =>'{
                "id": "'.$project_id.'",
                "module": "project"
            }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: bt76VoSGwwLy8XWpas1lSl0g1c8l0iu6S8seSG3DkYs8LvgWym8vmVHApMxo',
                'Accept: application/json',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $result = json_decode($response);

        if ($httpCode == '200')
        {
            if ($result->data->message == 'trigger pull data') {
                echo json_encode(['status'=>1, 'message'=>'Data Successfully Synchronized', 'result'=>$response]);
            } else {
                echo json_encode(['status'=>2, 'message'=>'Fail Synchronized']);
            }
        }
    }
}
