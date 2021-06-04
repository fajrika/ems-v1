<?php
defined('BASEPATH') or exit('No direct script access allowed');
class m_payment extends CI_Model
{
    public function get_bill($jenis_unit,$unit_id,$project_id,$date){
        $result = (object)[];
        if($date == date('Y-m-d') || $date == null)
            $result->bill = $this->db
                    ->query("SELECT *, id,'IPL' as service, 1 as service_jenis_id
                            FROM t_tagihan_lingkungan
                            WHERE unit_id = '$unit_id'
                            AND project_id = '$project_id'
                            AND status_tagihan != 1
                            UNION ALL
                            SELECT *, id,'Air' as service, 2 as service_jenis_id
                            FROM t_tagihan_air
                            WHERE unit_id = '$unit_id'
                            AND project_id = '$project_id'
                            AND status_tagihan != 1
                            order by periode_tagihan,service_jenis_id");
        else
            $result->bill = $this->db
                    ->query("SELECT t_tagihan_lingkungan.*, t_tagihan_lingkungan.id,'IPL' as service,
                                CASE 
                                    WHEN '$date' >= tgl_mulai_denda THEN 
                                        case denda_jenis_service
                                            WHEN 1 THEN denda_nilai_service
                                            WHEN 2 THEN denda_nilai_service * (DATEDIFF(MONTH, tgl_mulai_denda, '$date')+IIF(day(tgl_mulai_denda) <= day('$date'),1,0))
                                            WHEN 3 THEN CAST(ROUND(nilai_pokok * (POWER(cast(1+(denda_nilai_service*0.01) as float),DATEDIFF(MONTH, tgl_mulai_denda, '$date')+IIF(day(tgl_mulai_denda) <= day('$date'),1,0))-1),0) as INT)
                                            ELSE 0
                                        END
                                    ELSE 0
                                END as nilai_denda,
                                nilai_tagihan + 
                                CASE 
                                    WHEN '$date' >= tgl_mulai_denda THEN 
                                        case denda_jenis_service
                                            WHEN 1 THEN denda_nilai_service
                                            WHEN 2 THEN denda_nilai_service * (DATEDIFF(MONTH, tgl_mulai_denda, '$date')+IIF(day(tgl_mulai_denda) <= day('$date'),1,0))
                                            WHEN 3 THEN CAST(ROUND(nilai_pokok * (POWER(cast(1+(denda_nilai_service*0.01) as float),DATEDIFF(MONTH, tgl_mulai_denda, '$date')+IIF(day(tgl_mulai_denda) <= day('$date'),1,0))-1),0) as INT)
                                            ELSE 0
                                        END
                                    ELSE 0
                                END +
                                - (nilai_pemutihan_tagihan+nilai_pemutihan_denda+nilai_diskon_tagihan+nilai_terbayar) as nilai_outstanding,
                                1 as service_jenis_id
                            FROM t_tagihan_lingkungan
                            JOIN t_tagihan_lingkungan_info ON t_tagihan_lingkungan_info.t_tagihan_lingkungan_id = t_tagihan_lingkungan.id
                            WHERE unit_id = '$unit_id'
                            AND project_id = '$project_id'
                            AND status_tagihan != 1
                            UNION ALL
                            SELECT t_tagihan_air.*, t_tagihan_air.id,'Air' as service,
                                CASE 
                                    WHEN '$date' >= tgl_mulai_denda THEN 
                                        case denda_jenis_service
                                            WHEN 1 THEN denda_nilai_service
                                            WHEN 2 THEN denda_nilai_service * (DATEDIFF(MONTH, tgl_mulai_denda, '$date')+IIF(day(tgl_mulai_denda) <= day('$date'),1,0))
                                            WHEN 3 THEN CAST(ROUND(nilai_pokok * (POWER(cast(1+(denda_nilai_service*0.01) as float),DATEDIFF(MONTH, tgl_mulai_denda, '$date')+IIF(day(tgl_mulai_denda) <= day('$date'),1,0))-1),0) as INT)
                                            ELSE 0
                                        END
                                    ELSE 0
                                END as nilai_denda,
                                nilai_tagihan + 
                                CASE 
                                    WHEN '$date' >= tgl_mulai_denda THEN 
                                        case denda_jenis_service
                                            WHEN 1 THEN denda_nilai_service
                                            WHEN 2 THEN denda_nilai_service * (DATEDIFF(MONTH, tgl_mulai_denda, '$date')+IIF(day(tgl_mulai_denda) <= day('$date'),1,0))
                                            WHEN 3 THEN CAST(ROUND(nilai_pokok * (POWER(cast(1+(denda_nilai_service*0.01) as float),DATEDIFF(MONTH, tgl_mulai_denda, '$date')+IIF(day(tgl_mulai_denda) <= day('$date'),1,0))-1),0) as INT)
                                            ELSE 0
                                        END
                                    ELSE 0
                                END +
                                - (nilai_pemutihan_tagihan+nilai_pemutihan_denda+nilai_diskon_tagihan+nilai_terbayar) as nilai_outstanding,
                                2 as service_jenis_id
                            FROM t_tagihan_air
                            JOIN t_tagihan_air_info ON t_tagihan_air_info.t_tagihan_air_id = t_tagihan_air.id
                            WHERE unit_id = '$unit_id'
                            AND project_id = '$project_id'
                            AND status_tagihan != 1
                            order by periode_tagihan,service_jenis_id");
        $result->bill = $result->bill->result();
        $result->total_pokok = $result->total_ppn = $result->total_tagihan = $result->total_denda = $result->total_pemutihan_tagihan = $result->total_pemutihan_denda = $result->total_diskon_tagihan = $result->total_terbayar = $result->total_outstanding = 0;
        foreach ($result->bill as $bill) {
            $result->total_pokok += $bill->nilai_pokok;
            $result->total_ppn += $bill->nilai_ppn;
            $result->total_tagihan += $bill->nilai_tagihan;
            $result->total_denda += $bill->nilai_denda;
            $result->total_pemutihan_tagihan += $bill->nilai_pemutihan_tagihan;
            $result->total_pemutihan_denda += $bill->nilai_pemutihan_denda;
            $result->total_diskon_tagihan += $bill->nilai_diskon_tagihan;
            $result->total_terbayar += $bill->nilai_terbayar;
            $result->total_outstanding += $bill->nilai_outstanding;
        }
        return $result;
    }
    public function get_cara_pembayaran($limit = 0){
        // sleep(10);
        $result = $this->db
			->select("cara_pembayaran.id,
						concat(cara_pembayaran_jenis.code,' - ',cara_pembayaran.name,IIF(bank.name is null,'',concat(' - ',bank.name)),' | Biaya Admin: ',cara_pembayaran.biaya_admin) as text,
						cara_pembayaran.biaya_admin")
			->from("cara_pembayaran")
			->join('cara_pembayaran_jenis',
					"cara_pembayaran_jenis.id = cara_pembayaran.jenis_cara_pembayaran_id")
			->join('bank',
					"bank.id = cara_pembayaran.bank_id",
					"LEFT")
			->where("cara_pembayaran.project_id", $GLOBALS['project']->id)
			->where("cara_pembayaran.delete", 0)
            ->where("cara_pembayaran.active", 1)
            ->order_by("cara_pembayaran.jenis_cara_pembayaran_id");
        $limit = (int)$limit;
        if($limit != 0)     $result->limit($limit);
        return $result->get()->result();
    }
    public function generate_kwitansi($project_id){
		$project_code = $this->db->select("code")->from("project")->where('id',$project_id)->get()->row()->code;
		$no = $project_code.date("dmY");
		$tmp = $this->db	->select('no_kwitansi')
							->from("t_pembayaran")
							->where("no_kwitansi like '".$no."%'")	
							->order_by("id DESC")
							->get()->row();
		if($tmp)    return $no.str_pad(substr($tmp->no_kwitansi,strpos($tmp->no_kwitansi,date('dmY'))+8)+1, 5, "0", STR_PAD_LEFT);
		else        return $no.'00001';
    }

    public function update_bill($bills,$service_id){
        if($service_id == 1) $table = 't_tagihan_lingkungan';
        elseif($service_id == 2) $table = 't_tagihan_air';
        else {return 0;}
        foreach ($bills as $ind => $bill){
            $this->db->where('id',$bill->id);
            unset($bill->service,$bill->service_jenis_id,$bill->id);
            $this->db->update($table, $bill);
        }
        return 1;
    }
    
    public function save($jenis_unit, $unit_id, $project_id, $nilai_bayar = 0,$cara_pembayaran_id,$tgl_pembayaran,$user_id){       
        // var_dump(isset($tgl_pembayaran)); 

        if($nilai_bayar > 0 && isset($cara_pembayaran_id) && isset($tgl_pembayaran)){
            $this->db->trans_start();

            $bills = $this->get_bill($jenis_unit,$unit_id,$project_id,$tgl_pembayaran);
            $cara_pembayaran = $this->db->select('id, code, jenis_cara_pembayaran_id, biaya_admin')->from('cara_pembayaran')->where('id',$cara_pembayaran_id)->get()->row();
            if($cara_pembayaran->jenis_cara_pembayaran_id == 0){ // deposit //cek saldo
            }
            $tagihans = [[],[],[],[],[],[],[]];
            $pembayaran = (object)[
                'unit_id' => $unit_id,
                'cara_pembayaran_id' => $cara_pembayaran_id,
                'jenis_cara_pembayaran_id' => $cara_pembayaran->jenis_cara_pembayaran_id,
                'code_pembayaran' => 'Web App',
                'tgl_document' => $tgl_pembayaran." 00:00:00.000",
                'keterangan' => 'Rekap',
                'tgl_tambah' => date("Y-m-d H:i:s.000"),
                'user_id' => $user_id,
                'delete' => 0,
                'tgl_bayar' => $tgl_pembayaran." 00:00:00.000",
                'flag_trf_keuangan' => 0,
                'no_kwitansi' => $this->generate_kwitansi($project_id),
                'is_void' => 0,
                'nilai_biaya_admin_cara_pembayaran' => $cara_pembayaran->biaya_admin,
                'count_print_kwitansi' => 0
            ];
            $pembayaran_details = [];
            $service = [];
            $service[1] = $this->db->from('service')->where('service_jenis_id',1)->where('project_id',$project_id)->where('delete',0)->where('active',1)->get()->row();
            $service[2] = $this->db->from('service')->where('service_jenis_id',2)->where('project_id',$project_id)->where('delete',0)->where('active',1)->get()->row();
            $service[3] = $this->db->from('service')->where('service_jenis_id',3)->where('project_id',$project_id)->where('delete',0)->where('active',1)->get()->row();
            $service[4] = $this->db->from('service')->where('service_jenis_id',4)->where('project_id',$project_id)->where('delete',0)->where('active',1)->get()->row();
            $service[5] = $this->db->from('service')->where('service_jenis_id',5)->where('project_id',$project_id)->where('delete',0)->where('active',1)->get()->row();
            $service[6] = $this->db->from('service')->where('service_jenis_id',6)->where('project_id',$project_id)->where('delete',0)->where('active',1)->get()->row();
            $this->db->insert('t_pembayaran',$pembayaran);
            $pembayaran->id = $this->db->insert_id();
            $nilai_bayar -= $cara_pembayaran->biaya_admin;
            foreach ($bills->bill as $ind => $bill) {
                $pembayaran_detail = (object)[
                    't_pembayaran_id' => $pembayaran->id,
                ];
                $tagihan = $bill;
                if($nilai_bayar == 0)
                    break;
                else{
                    $pembayaran_detail->nilai_tagihan           = $bill->nilai_tagihan;
                    $pembayaran_detail->bayar                   = $nilai_bayar;
                    $pembayaran_detail->service_id              = $service[$bill->service_jenis_id]->id;
                    $pembayaran_detail->tagihan_service_id      = $bill->id;
                    $pembayaran_detail->nilai_denda             = $bill->nilai_denda;
                    $pembayaran_detail->nilai_diskon            = $bill->nilai_diskon_tagihan;
                    $pembayaran_detail->nilai_ppn               = $bill->nilai_ppn;
                    $pembayaran_detail->nilai_tagihan_pemutihan = $bill->nilai_pemutihan_tagihan;
                    $pembayaran_detail->nilai_denda_pemutihan   = $bill->nilai_pemutihan_denda;
                    $pembayaran_detail->service_jenis_id        = $bill->service_jenis_id;
                    $pembayaran_detail->nilai_pokok             = $bill->nilai_pokok;
                    $pembayaran_detail->nilai_pemutihan_pokok   = $bill->nilai_pemutihan_pokok;
                    $pembayaran_detail->nilai_pemutihan_tagihan = $bill->nilai_pemutihan_tagihan;
                    $pembayaran_detail->nilai_pemutihan_denda   = $bill->nilai_pemutihan_denda;
                    $pembayaran_detail->nilai_diskon_pokok      = $bill->nilai_diskon_pokok;
                    $pembayaran_detail->nilai_diskon_tagihan    = $bill->nilai_diskon_tagihan;
                    $pembayaran_detail->nilai_terbayar          = $bill->nilai_terbayar;
                    $pembayaran_detail->nilai_outstanding       = $bill->nilai_outstanding;
                    
                    if($bill->nilai_outstanding > $nilai_bayar){
                        $pembayaran_detail->bayar          = $nilai_bayar;
                        $bill->nilai_terbayar += $nilai_bayar;
                        $bill->nilai_outstanding -= $nilai_bayar;
                        $bill->status_tagihan = 4;
                        $nilai_bayar = 0;                        
                    }
                    else if($bill->nilai_outstanding <= $nilai_bayar){
                        $pembayaran_detail->bayar          = $bill->nilai_outstanding;
                        $nilai_bayar -= $bill->nilai_outstanding;
                        $bill->nilai_terbayar += $bill->nilai_outstanding;
                        $bill->nilai_outstanding = 0;
                        $bill->status_tagihan = 1;
                    }                    
                    array_push($pembayaran_details,$pembayaran_detail);
                    array_push($tagihans[$bill->service_jenis_id],$tagihan);
                }
            }
            $this->db->insert_batch('t_pembayaran_detail',$pembayaran_details);

            $this->update_bill($tagihans[1],1);
            $this->update_bill($tagihans[2],2);
            $this->update_bill($tagihans[6],6);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return false;
            } else {
                $this->db->trans_commit();
                return $pembayaran->id;
            }
    
        }
        return false;

    }
}
