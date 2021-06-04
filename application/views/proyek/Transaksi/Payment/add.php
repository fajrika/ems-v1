<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<link href="<?= base_url(); ?>vendors/select2/dist/css/select2.min.css" rel="stylesheet">
<script src="<?= base_url(); ?>vendors/select2/dist/js/select2.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>vendors/moment/min/moment.min.js"></script>
<link href="<?= base_url(); ?>vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<script type="text/javascript" src="<?= base_url(); ?>vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>vendor/numeral/min/numeral.min.js"></script>

<style>
	.invalid{background-color:lightpink}.has-error{border:1px solid rgb(185,74,72)!important}.text-right{text-align:right}.table>thead>tr>th{vertical-align:middle}.select2.select2-container{width:100%!important}.dt-body-right{text-align:right}
</style>
<div style="float:right">
	<h2><button class="btn btn-success" onClick="location.reload()"><i class="fa fa-repeat"></i> Refresh</button></h2>
</div>
<div class="clearfix"></div>
</div>
<div class="x_content">
	<br>
	<form id="form" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post" action="<?= site_url(); ?>/Transaksi/P_transaksi_generate_bill/save" autocomplete="off">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top:20px">
			<div class="form-group">
				<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Cari Unit</label>
				<div class="col-lg-8 col-md-9 col-sm-12 col-xs-12">
					<select name="unit" required="" id="unit" class="form-control select2" placeholder="-- Pilih Kawasan - Blok - Unit - Pemilik --">
						<?php if(isset($unit->id)):?>
						<option value="<?=$unit->id?>" selected="selected"><?=$unit->text?></option>
						<?php endif;?>
					</select>
				</div>
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12" style="margin-top:20px">
			<div class="form-group">
				<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Tgl Pembayaran</label>
				<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
					<input type="text" class="form-control tgl_pembayaran">
				</div>
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12" style="margin-top:20px">
			<div class="form-group">
				<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Total Outstanding (Rp.)</label>
				<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
					<input type="text" class="total_outstanding form-control" readonly>
				</div>
			</div>
		</div>
		<div class="col-lg-12 col-md-12 col-sm-12" style="margin-top:20px">
			<div class="form-group">
				<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="float:none;margin:0 auto">
					<button type="button" class="form-control btn btn-success" data-toggle="modal" data-target="#modal-checkout">Checkout</button>
				</div>
			</div>
		</div>
	</form>
	<div class="col-md-12" id="dataisi">
		<div id="div_table" class="col-md-12 card-box table-responsive">
			<table id="table-unit" class="table table-striped table-bordered" style="width:100%">
				<thead>
					<tr>
						<th class='text-center' colspan=2 style="width:20%">Periode</th>
						<th class='text-center' rowspan=3 style="width:10%">Service</th>
						<th class='text-center' colspan=9 style="width:70%">Nilai (Rp.)</th>
					</tr>
					<tr>
						<th class='text-center' rowspan=2 style="width:10%">Penggunaan</th>
						<th class='text-center' rowspan=2 style="width:10%">Tagihan</th>
						<th class='text-center' rowspan=2 style="width:10%">Pokok</th>
						<th class='text-center' rowspan=2 style="width:10%">PPN</th>
						<th class='text-center' style="width:10%">Tagihan</th>
						<th class='text-center' rowspan=2 style="width:10%">Denda</th>
						<th class='text-center' colspan=2 style="width:10%">Pemutihan</th>
						<th class='text-center' style="width:10%">Diskon</th>
						<th class='text-center' rowspan=2 style="width:10%">Terbayar</th>
						<th class='text-center' rowspan=2 style="width:10%">Outstanding</th>
					</tr>
					<tr>
						<th class='text-center' style="width:10%">Pokok + PPN</th>
						<th class='text-center' style="width:10%">Tagihan</th>
						<th class='text-center' style="width:10%">Denda</th>
						<th class='text-center' style="width:10%;border-right:1px solid rgb(221, 221, 221)!important">Tagihan</th>
					</tr>
				</thead>
				<tbody id="tbody-unit">
				</tbody>
				<tfoot id="tfood-unit">
					<th colspan=3>Total</th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tfoot>
			</table>
		</div>
	</div>
</div>

<div class="x_content">
	<div class="modal fade modal-move" id="modal-checkout" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" style="width:80%;margin:auto">
			<div class="modal-content" style="margin-top:100px;">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Checkout<span class="grt"></span></h4>
				</div>
				<div class="modal-body">
				<form id="form2" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post" action="<?= site_url(); ?>/Transaksi/P_transaksi_generate_bill/save" autocomplete="off">
					
					<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12" style="margin-top:20px">
						<div class="form-group">
							<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Total Outstanding (Rp.)</label>
							<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
								<input type="text" class="total_outstanding form-control" readonly>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Cara Bayar</label>
							<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
								<select id="cara_pembayaran" type="text" class="form-control">
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Tgl Pembayaran</label>
							<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
								<input type="text" class="tgl_pembayaran form-control" readonly>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12" style="margin-top:20px">
						<div class="form-group">
							<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Saldo Deposit (Rp.)</label>
							<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
								<input id="saldo_deposit" type="text" class="form-control" readonly>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Deposit Digunakan (Rp.)</label>
							<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
								<input id="deposit_digunakan" type="text" class="form-control" readonly>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Saldo Deposit Akhir (Rp.)</label>
							<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
								<input id="saldo_deposit_akhir" type="text" class="form-control" readonly>
							</div>
						</div>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top:20px">
						<div class="form-group">
							<label 
								class="control-label col-md-offset-1 col-lg-offset-1 col-md-2 col-lg-2 col-sm-12 col-xs-12" 
								data-toggle="tooltip" 
								data-html="true" 
								data-placement="top" 
								title="Nilai sudah include dengan Biaya Admin Cara Pembayaran<br>Double click untuk nilai maksimum">
								Mau Bayar Berapa ?
							</label>
							<div class="col-lg-8 col-md-9 col-sm-12 col-xs-12">
								<input 
									id="nilai_bayar" 
									type="text" 
									class="form-control" 
									onkeyup="value=numeral(value).format()" 
									ondblclick="value=numeral( numeral($('.total_outstanding').val()).value() + parseInt($('#cara_pembayaran option:selected').attr('biaya_admin')) ).format()" 
									value=0>
							</div>
						</div>
						<div class="form-group">
							<label 
								class="control-label col-md-offset-2 col-lg-offset-2 col-md-1 col-lg-1 col-sm-12 col-xs-12" 
								data-toggle="tooltip" 
								data-html="true" 
								data-placement="top" 
								title="Rekap ini berdasarkan periode tagihan dan service yang di prioritaskan">
								Rekap
							</label>
							<div class="col-lg-8 col-md-9 col-sm-12 col-xs-12">
								<textarea id="rekap" type="text" class="form-control" rows=10 disabled></textarea>
							</div>
						</div>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12" style="margin-top:20px">
						<div class="form-group">
							<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="float:none;margin:0 auto">
								<button id="bayar" type="button" class="form-control btn btn-success" data-toggle="modal">Proses</button>
							</div>
						</div>
					</div>
				</form>
				</div>
				<div class="modal-footer" style="margin:0px; border-top:0px; text-align:center;">
					<button type="button" class="btn btn-primary col-md-12" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
	$('.tgl_pembayaran').datetimepicker({viewMode: 'years',format: 'DD/MM/YYYY',maxDate: "<?= date("Y-m-d") ?>"});
	var cara_pembayaran_first = true;
	var bill = [];
	$(function(){
		var table_unit = $("#table-unit").DataTable(
			{
				paging: false,
				order: [[1,'asc']],
				// ordering: false,
				columns: [
					{ "data": "periode_penggunaan", "targets": 0},
					{ "data": "periode_tagihan", "targets": 1},
					{ "data": "service", "targets": 2},
					{ "data": "nilai_pokok", "targets": 3, className: 'dt-body-right'},
					{ "data": "nilai_ppn", "targets": 4, className: 'dt-body-right'},
					{ "data": "nilai_tagihan", "targets": 5, className: 'dt-body-right'},
					{ "data": "nilai_denda", "targets": 6, className: 'dt-body-right'},
					{ "data": "nilai_pemutihan_tagihan", "targets": 7, className: 'dt-body-right'},
					{ "data": "nilai_pemutihan_denda", "targets": 8, className: 'dt-body-right'},
					{ "data": "nilai_diskon_tagihan", "targets": 9, className: 'dt-body-right'},
					{ "data": "nilai_terbayar", "targets": 10, className: 'dt-body-right'},
					{ "data": "nilai_outstanding", "targets": 11, className: 'dt-body-right'}
				]
			}
		);		
		$("#unit").select2({
			
			ajax: {
				dataType: "json",
				url: "<?= site_url() ?>/Transaksi/P_unit/get_ajax_unit",
				data: params => ({data: params.term}),
				processResults: data => ({results: data})
			}
		});
		$("#cara_pembayaran").select2({
			width: 'resolve',
			resize:true,
			placeholder: 'Pilih Cara Bayar'
		})
		$("#unit").change(e=>{
			table_unit.clear().draw();
			$.ajax({
				type: "POST",
				url: `<?= site_url() ?>/Transaksi/Payment/ajax_get_bill/${$('#unit').val().replace('.','/')}`,
				data: {tgl_pembayaran: $(".tgl_pembayaran").val().split('/').reverse().join('-')},
				dataType: "json",
				success: result => {
					bill = result.bill.map(function(e){
						return {...e,
							'periode_tagihan': Date.parse(e.periode_tagihan).format('m-Y'),
							'periode_penggunaan': Date.parse(e.periode_penggunaan).format('m-Y'),
							'nilai_pokok':numeral(e.nilai_pokok).format(),
							'nilai_ppn':numeral(e.nilai_ppn).format(),
							'nilai_tagihan':numeral(e.nilai_tagihan).format(),
							'nilai_denda':numeral(e.nilai_denda).format(),
							'nilai_pemutihan_tagihan':numeral(e.nilai_pemutihan_tagihan).format(),
							'nilai_pemutihan_denda':numeral(e.nilai_pemutihan_denda).format(),
							'nilai_diskon_tagihan':numeral(e.nilai_diskon_tagihan).format(),
							'nilai_terbayar':numeral(e.nilai_terbayar).format(),
							'nilai_outstanding':numeral(e.nilai_outstanding).format()}							
						}
					)
					$("#total_tagihan").val(numeral(result.total_tagihan).format());
					$(".total_outstanding").val(numeral(result.total_outstanding).format());
					table_unit.rows.add(bill).draw()					
					$(table_unit.column(3).footer()).html(numeral(result.total_pokok).format())
					$(table_unit.column(4).footer()).html(numeral(result.total_ppn).format())
					$(table_unit.column(5).footer()).html(numeral(result.total_tagihan).format())
					$(table_unit.column(6).footer()).html(numeral(result.total_denda).format())
					$(table_unit.column(7).footer()).html(numeral(result.total_pemutihan_tagihan).format())
					$(table_unit.column(8).footer()).html(numeral(result.total_pemutihan_denda).format())
					$(table_unit.column(9).footer()).html(numeral(result.total_diskon_tagihan).format())
					$(table_unit.column(10).footer()).html(numeral(result.total_terbayar).format())
					$(table_unit.column(11).footer()).html(numeral(result.total_outstanding).format())
					if(cara_pembayaran_first){
						$.ajax({//auto tanpa load animasi, karena ajax dalam ajax...
							url: `<?= site_url() ?>/Transaksi/Payment/ajax_get_cara_pembayaran/`,
							dataType: "json",
							success: result => {
								result.forEach((el,i) => {
									$("#cara_pembayaran").append(`<option value='${el.id}' biaya_admin='${el.biaya_admin}'>${el.text}</option>`);
								});
								console.log(result);
								cara_pembayaran_first = false;
							}}
						)

					}
				},
				error: result =>(alert('Connection atau system error, lakukan ulang atau segera hubungi IT SH2 KP'))
			});
		})
		$("#nilai_bayar").on('keyup dblclick',e => {
			let nilai_bayar = numeral($("#nilai_bayar").val()).value();
			let biaya_admin = numeral($('#cara_pembayaran option:selected').attr('biaya_admin')).value();

			if(nilai_bayar <= 0)
				text="Nilai Bayar kurang dari sama dengan 0 atau tidak valid"

			else if(nilai_bayar < biaya_admin)
				text="Nilai Bayar kurang dari biaya admin"
			
			else{
				text = `Biaya Admin = ${numeral(biaya_admin).format('$0,0')}`;
				nilai_bayar = numeral(nilai_bayar).value() - biaya_admin;
				var service = ['',{},{},{},{},{},{}]; // 1 ipl, 2 air, 6 lainnya
				
				bill.forEach(el => {
					if(nilai_bayar > 0){
						if(service[el.service_jenis_id].service_name == null){
							service[el.service_jenis_id].service_name = el.service;
							service[el.service_jenis_id].periode_awal = el.periode_tagihan;
							service[el.service_jenis_id].value = 0;
						}
						if(numeral(el.nilai_outstanding).value() <= numeral(nilai_bayar).value()){
							service[el.service_jenis_id].value += numeral(el.nilai_outstanding).value();
							service[el.service_jenis_id].periode_akhir = el.periode_tagihan;
						}
						else if (numeral(nilai_bayar).value() > 0){
							if(service[el.service_jenis_id].value>0){
								service[el.service_jenis_id].value2 = numeral(nilai_bayar).value();
								service[el.service_jenis_id].periode_akhir2 = el.periode_tagihan;
							}
							else{
								service[el.service_jenis_id].value += (numeral(nilai_bayar).value());
								service[el.service_jenis_id].periode_akhir = el.periode_tagihan;
							}
						}
					}
					nilai_bayar -= numeral(el.nilai_outstanding).value();
				});
				service.forEach(el=>{
					if(el.service_name){
						text += `\nNilai ${el.service_name}`
						text += `\n  ${el.periode_awal} sampai ${el.periode_akhir} = ${numeral(el.value).format('$0,0')}`;
						if(el.value2)
							text += `\n  dan ${el.periode_akhir2} = ${numeral(el.value2).format('$0,0')}`;
					}
				})
				if(nilai_bayar > 0)
					text += `\n\n**Sisa = ${numeral(nilai_bayar).format('$0,0')} akan dimasukkan ke deposit`; 
			}
			let sisa_outstanding = (numeral($('.total_outstanding').val()).value() + numeral($('#cara_pembayaran option:selected').attr('biaya_admin')).value()) - numeral($("#nilai_bayar").val()).value();
			if(sisa_outstanding > 0)
				text+=`\n\n**Sisa Outstanding mu ialah = ${numeral(sisa_outstanding).format('$0,0')}`
			$("#rekap").val(text);
		})
		$("#bayar").click(e=>{
            swal({
                title: "Yakin mau melakukan pembayaran ?",
                text: "Kalau yakin, pembayaran akan segera dilakukan nih !",
                icon: "warning",
                buttons: ['Nggak yakin', 'yakin'],
                dangerMode: true,
            }).then((yakin) => {
                if (yakin) {
					$.ajax({
						type: "POST",
						url: `<?= site_url() ?>/Transaksi/Payment/ajax_save/${$('#unit').val().replace('.','/')}`,
						data: {
							cara_pembayaran_id: $("#cara_pembayaran").val(),
							nilai_bayar: numeral($("#nilai_bayar").val()).value(),
							tgl_pembayaran: $(".tgl_pembayaran").val().split('/').reverse().join('-')
						},
						dataType: "json",
						success: result => {
							swal("Ok, Pembayaran berhasil di lakukan :),\nMau cetak kwitansi enggak nih ?", {
								icon: "success",
								buttons: ['Nanti Aja', 'Cetak Kwitansi'],

							}).then((cetak_kwitansi)=>{
								$("#modal-checkout").modal('hide')
								if(cetak_kwitansi){
									window.open(`<?= site_url() ?>/cetakan/Kwitansi_new/all/${result}`);
								}
								swal("Ayo tekan refresh untuk menu pembayaranya ini!!", {
									icon: "success",
									buttons: 'Refresh',
								}).then(e=>{
									location.reload();
								})

							});
						},
						error: result =>(alert('Connection atau system error, lakukan ulang atau hubungi IT SH2 KP'))
					});
                    
                } else {
                    swal("Ok, Pembayaran tidak jadi di lakukan :)",{
                        icon: "info",buttons: 'tutup'
                    });
                    
                }
            });
        })
		$('.tgl_pembayaran').on('dp.change',()=>{
			$('.tgl_pembayaran').eq(1).val($('.tgl_pembayaran').val());
			$("#unit").trigger('change')
		})
		window.location.pathname.length>33?$("#unit").trigger('change'):'';
	})
</script>