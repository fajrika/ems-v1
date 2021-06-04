<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<link href="<?= base_url(); ?>vendors/select2/dist/css/select2.min.css" rel="stylesheet">
<script src="<?= base_url(); ?>vendors/select2/dist/js/select2.min.js"></script>
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
				<label class="control-label col-lg-offset-2 col-md-offset-2 col-lg-1 col-md-1 col-sm-12 col-xs-12" 
                    data-toggle="tooltip" 
                    data-placement="top" 
                    title="Kawasan - Blok - Unit - Customer">Cari Unit</label>
				<div class="col-lg-8 col-md-9 col-sm-12 col-xs-12">
					<select name="unit" required="" id="unit" class="form-control select2" placeholder="-- Pilih Kawasan - Blok - Unit - Pemilik --">
						<?php if(isset($unit->id)):?>
						<option value="<?=$unit->id?>" selected="selected"><?=$unit->text?></option>
						<?php endif;?>
					</select>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-top:20px">
			<div class="form-group">
				<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Total Outstanding</label>
				<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
					<input type="text" class="total-outstanding form-control" readonly>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-top:20px">
			<div class="form-group">
				<label 
                    class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12"
                    data-toggle="tooltip" 
                    data-placement="top" 
                    title="Total Pemutihan ialah hasil penjumlahan Total Pemutihan Tagihan dengan Total Pemutihan Denda">Total Pemutihan</label>
				<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
					<input type="text" class="total-pemutihan form-control" readonly>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-top:20px">
			<div class="form-group">
				<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Total Outstanding Setelah Pemutihan</label>
				<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
					<input type="text" class="total-outstanding-setelah-pemutihan form-control" readonly>
				</div>
			</div>
		</div>
		<div class="col-lg-offset-4 col-md-offset-4 col-lg-2 col-md-2 col-sm-12" style="margin-top:20px">
			<div class="form-group">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="float:none;margin:0 auto">
					<button type="button" class="form-control btn btn-primary" data-toggle="modal" data-target="#modal-input-helper">Input Helper</button>
				</div>
			</div>
		</div>
        <div class="col-lg-2 col-md-2 col-sm-12" style="margin-top:20px">
			<div class="form-group">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="float:none;margin:0 auto">
					<button type="button" id="btn-ajukan" class="form-control btn btn-success" data-toggle="modal">Ajukan</button>
				</div>
			</div>
		</div>
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
                            <th class='text-center' 
                                rowspan=2 
                                style="width:10%"
                                data-toggle="tooltip" 
								data-html="true" 
								data-placement="top" 
								title="Terbayar ialah nilai yang sudah di lakukan pembayaran<br>tapi tidak 100% (bayar sebagian)<br>sehingga masih ada di list">Terbayar</th>
                            <th class='text-center' 
                                rowspan=2 
                                style="width:10%"
                                data-toggle="tooltip" 
								data-html="true" 
								data-placement="top" 
								title="Outstanding ialah hasil kalkulasi dari<br>Nilai Pokok + Nilai Denda - Nilai Pemutihan Tagihan - Nilai Pemuithan Denda - Nilai Diskon Tagihan - Nilai Terbayar">Outstanding</th>
                        </tr>
                        <tr>
                            <th class='text-center' style="width:10%">Pokok + PPN</th>
                            <th class='text-center' 
                                style="width:10%"
                                data-toggle="tooltip" 
								data-html="true" 
								data-placement="top" 
                                delay= "{show: 1000, hide: 500}}"
								title="Tagihan ialah Nilai Pokok + Nilai PPN">Tagihan</th>
                            <th class='text-center' style="width:10%">Denda</th>
                            <th class='text-center' 
                                style="width:10%;border-right:1px solid rgb(221, 221, 221)!important"
                                data-toggle="tooltip" 
								data-html="true" 
								data-placement="top" 
								title="Tagihan ialah Nilai Pokok + Nilai PPN"
                            >Tagihan</th>
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
    </form>
</div>
<div class="x_content">
	<div class="modal fade modal-move" id="modal-input-helper" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" style="width:40%;margin:auto">
			<div class="modal-content" style="margin-top:100px;">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Input Helper<span class="grt"></span></h4>
				</div>
				<div class="modal-body">
				<form id="form2" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post" action="<?= site_url(); ?>/Transaksi/P_transaksi_generate_bill/save" autocomplete="off">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top:20px">
						<div class="form-group">
							<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Nilai Pemutihan Tagihan</label>
							<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
								<input type="text" id="input-helper-inp-tagihan" class="form-control" data-toggle="tooltip" data-placement="top" title="Double Click untuk isi nilai dengan maksimum">
							</div>
							<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                <select id="input-helper-type-tagihan" class="form-control" onchange="$('#input-helper-inp-tagihan').val(0)">
                                    <option value="1">Rp.</option>
                                    <option value="2">%</option>
                                </select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Nilai Pemutihan Denda</label>
							<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
								<input type="text" id="input-helper-inp-denda" class="form-control" data-toggle="tooltip" data-placement="top" title="Double Click untuk isi nilai dengan maksimum">
							</div>
							<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                <select id="input-helper-type-denda" class="form-control" onchange="$('#input-helper-inp-denda').val(0)">
                                    <option value="1">Rp.</option>
                                    <option value="2">%</option>
                                </select>
							</div>
						</div>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12" style="margin-top:20px">
						<div class="form-group">
							<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" style="float:none;margin:0 auto">
								<button type="button" id="input-helper-btn" class="form-control btn btn-success" data-toggle="modal" data-target="#modal-checkout">Terapkan</button>
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
	var bill = [];
	$(function(){
        $('input').tooltip({trigger:'hover',delay: {show: 1000, hide: 500},});
		var table_unit = $("#table-unit").DataTable(
			{
				paging: false,
				order: [[1,'asc']],
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
							'nilai_pemutihan_tagihan':`<input class='form-control numeral-input inp-pemutihan-tagihan col-md-2' name='nilai_pemutihan_tagihan[${e.service_jenis_id+'.'+e.id}]' value='${numeral(e.nilai_pemutihan_tagihan).format()}' style='text-align:right;width:150px;' style="width:10%" data-toggle="tooltip" data-placement="top" title="Double Click untuk isi nilai dengan maksimum">`,
							'nilai_pemutihan_denda':`<input class='form-control numeral-input inp-pemutihan-denda col-md-2' name='nilai_pemutihan_denda[${e.service_jenis_id+'.'+e.id}]' value='${numeral(e.nilai_pemutihan_denda).format()}' style='text-align:right;width:150px;' data-toggle="tooltip" data-placement="top" title="Double Click untuk isi nilai dengan maksimum">`,
							'nilai_diskon_tagihan':numeral(e.nilai_diskon_tagihan).format(),
							'nilai_terbayar':numeral(e.nilai_terbayar).format(),
							'nilai_outstanding':numeral(e.nilai_outstanding).format()}							
						}
					)
					$(".total-outstanding").val(numeral(result.total_outstanding).format('$0,0'));
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
                    numeral.nullFormat(0)
                    $('input').tooltip({trigger:'hover',delay: {show: 1000, hide: 500}});

                },
				error: result =>(alert('Connection atau system error, refresh atau segera hubungi IT SH2 KP'))
			});
        })
        // START : pemutihan input dan recalculate
        recalculate_outstanding = () => {
            $("#tbody-unit").children().each((i,el) => {
                $(el).children().eq(11).html(
                    numeral(
                        numeral($(el).children().eq(5).html()).value() 
                        + numeral($(el).children().eq(6).html()).value()
                        - numeral($(el).find('.inp-pemutihan-tagihan').val()).value()
                        - numeral($(el).find('.inp-pemutihan-denda').val()).value()
                    ).format()
                )
            })
            $(table_unit.column(11).footer()).html(numeral(numeral($('.total-outstanding').val()).value()-numeral($('.total-pemutihan').val()).value()).format()) 
            $('.total-outstanding-setelah-pemutihan').val(numeral(numeral($('.total-outstanding').val()).value()-numeral($('.total-pemutihan').val()).value()).format('$0,0'))            
        }
        recalculate_pemutihan_total = () => {
            $(".total-pemutihan").val(numeral(numeral($(table_unit.column(7).footer()).html()).value() + numeral($(table_unit.column(8).footer()).html()).value()).format('$0,0'))
            recalculate_outstanding();
        }
        recalculate_pemutihan_tagihan = () => {
            let total_pemutihan_tagihan = 0;
            $("#tbody-unit").children().each((i,el) => {total_pemutihan_tagihan += numeral($(el).find('.inp-pemutihan-tagihan').val()).value()})
            $(table_unit.column(7).footer()).html(numeral(total_pemutihan_tagihan).format())
            recalculate_pemutihan_total();
        }
        recalculate_pemutihan_denda = () => {
            let total_pemutihan_denda = 0;
            $("#tbody-unit").children().each((i,el) => {total_pemutihan_denda += numeral($(el).find('.inp-pemutihan-denda').val()).value()})
            $(table_unit.column(8).footer()).html(numeral(total_pemutihan_denda).format())
            recalculate_pemutihan_total();
        }
        $("body").on('keyup','.inp-pemutihan-tagihan',e=>{
            $(e.target).val(numeral($(e.target).val()).format())
            if(numeral($(e.target).val()).value() < 0)
                $(e.target).val(numeral(0).format())
            else if(numeral($(e.target).val()).value() > numeral($(e.target).parents('tr').children().eq(5).html()).value())
                $(e.target).val($(e.target).parents('tr').children().eq(5).html())
            recalculate_pemutihan_tagihan();
        })
        $("body").on('keyup','.inp-pemutihan-denda',e=>{
            $(e.target).val(numeral($(e.target).val()).format())
            if(numeral($(e.target).val()).value() < 0)
                $(e.target).val(numeral(0).format())
            else if(numeral($(e.target).val()).value() > numeral($(e.target).parents('tr').children().eq(6).html()).value())
                $(e.target).val($(e.target).parents('tr').children().eq(6).html())
            recalculate_pemutihan_denda();
        })
        $("body").on('dblclick','.inp-pemutihan-tagihan',e=>{
            $(e.target).val(numeral($(e.target).parents('tr').children().eq(5).html()).format());
            recalculate_pemutihan_tagihan();
        })
        $("body").on('dblclick','.inp-pemutihan-denda',e=>{
            $(e.target).val(numeral($(e.target).parents('tr').children().eq(6).html()).format())
            recalculate_pemutihan_denda();
        })
        $("#input-helper-inp-tagihan, #input-helper-inp-denda").keyup(e=>{
            $(e.target).val(numeral($(e.target).val()).format())

        })
        $("#input-helper-inp-tagihan").dblclick(e=>{
            console.log(bill)
            if($("#input-helper-type-tagihan").val() == 1)
                $(e.target).val(numeral(
                    Math.max(...bill.map(el=>numeral(el.nilai_tagihan).value()))
                ).format())
            else
                $(e.target).val(100)
            
        })
        $("body").on('change','#input-helper-type-tagihan',e=>{
            $('#input-helper-inp-tagihan').val(0)
        })
        $("#input-helper-inp-denda").dblclick(e=>{
            if($("#input-helper-type-denda").val() == 1)
                $(e.target).val(numeral(
                    Math.max(...bill.map(el=>numeral(el.nilai_denda).value()))
                ).format())
            else
                $(e.target).val(100)
        })
        $("#input-helper-btn").click(e =>{
            let type_tagihan = $("#input-helper-type-tagihan").val()
            let type_denda = $("#input-helper-type-denda").val()
            let nilai_tagihan = $("#input-helper-inp-tagihan").val()
            let nilai_denda = $("#input-helper-inp-denda").val()
            
            $("#tbody-unit").children().each((i,el) => {
                if(numeral($("#input-helper-type-tagihan").val()).value() == 1){
                    $(el).find('.inp-pemutihan-tagihan').val(numeral($("#input-helper-inp-tagihan").val()).format()).trigger('keyup')
                }else{
                    $(el).find('.inp-pemutihan-tagihan').val(
                        numeral(
                            Math.round(numeral($("#input-helper-inp-tagihan").val()).value() * numeral($(el).children().eq(5).html()).value() / 100,0)
                        ).format()
                    ).trigger('keyup')
                }
                if(numeral($("#input-helper-type-denda").val()).value() == 1){
                    $(el).find('.inp-pemutihan-denda').val(numeral($("#input-helper-inp-denda").val()).format()).trigger('keyup')
                }else{
                    $(el).find('.inp-pemutihan-denda').val(
                        numeral(
                            Math.round(numeral($("#input-helper-inp-denda").val()).value() * numeral($(el).children().eq(6).html()).value() / 100,0)
                        ).format()
                    ).trigger('keyup')
                }


            })
        })
        // END : pemutihan input dan recalculate
        $("#btn-ajukan").click(e=>{
            swal({
                title: "Yakin pemutihannya mau di ajukan ?",
                text: "Kalau jadi, approval akan langsung otomatis terkirim ke email atasan loh !",
                icon: "warning",
                buttons: ['Nggak jadi deh', 'Jadi'],
                dangerMode: true,
            }).then((ajukan) => {
                if (ajukan) {
                    swal("Ok, pemutihan berhasil di ajukan :)", {
                        icon: "success",buttons: 'tutup'
                    });
                } else {
                    swal("Ok, pemutihan tidak jadi di ajukan :)",{
                        icon: "info",buttons: 'tutup'
                    });
                    
                }
            });
        })
		window.location.pathname.length>40?$("#unit").trigger('change'):'';
	})
</script>