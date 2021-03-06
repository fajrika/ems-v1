<link href="<?= base_url(); ?>vendors/select2/dist/css/select2.min.css" rel="stylesheet">
<script src="<?= base_url(); ?>vendors/select2/dist/js/select2.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>vendors/moment/min/moment.min.js"></script>
<link href="<?= base_url(); ?>vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<script type="text/javascript" src="<?= base_url(); ?>vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<style type="text/css">
	.invalid {
		background-color: lightpink;
	}
	.has-error {
		border: 1px solid rgb(185, 74, 72) !important;
	}
	a.disabled {
		pointer-events: none;
		cursor: default;
	}

    table.dataTable { font-size: 13px !important; }
    div.dataTables_wrapper { width: 99%; height: 100%; margin: 0 auto; }
    .dataTables_length select,
    .dataTables_filter input { padding: 6px 12px; border: 1px solid #ccc; }
    tfoot input::placeholder { font-size:11px; }
</style>

<div style="float:right">
	<h2>
		<button class="btn btn-warning" onClick="window.location.href='<?= site_url() ?>/P_transaksi_meter_air'">
			<i class="fa fa-arrow-left"></i>
			Back
		</button>
		<button class="btn btn-success" onClick="window.location.href='<?= site_url() ?>/P_transaksi_meter_air/add'">
			<i class="fa fa-repeat"></i>
			Refresh
		</button>
	</h2>
</div>
<div class="clearfix"></div>
</div>
<div class="x_conte	nt">
	<br>
	<form id="form" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post" action="<?= site_url(); ?>/Transaksi/P_transaksi_generate_bill/save" autocomplete="off">
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12" style="margin-top:20px">
			<div class="form-group">
				<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Kawasan</label>
				<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
					<select name="kawasan" required="" id="kawasan" class="form-control select2" placeholder="-- Pilih Kawasan --">
						<option value="" disabled selected>-- Pilih Kawasan --</option>
						<option value="all">-- Semua Kawasan --</option>
						<?php
						foreach ($kawasan as $v) {
							echo ("<option value='$v->id'>$v->code - $v->name</option>");
						}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Blok</label>
				<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
					<select name="blok" required="" id="blok" class="form-control select2" placeholder="-- Pilih Kawasan Dahulu --" disabled>
						<option value="" disabled selected>-- Pilih Kawasan Dahulu --</option>
						<option value="all">-- Semua Blok --</option>
					</select>
				</div>
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-sm-12" style="margin-top:20px">
			<div class="form-group">
				<label class="control-label col-lg-3 col-md-3 col-sm-12">Periode Penggunaan</label>
				<div class="col-lg-4 col-md-4 col-sm-5">
					<div class='input-group date datetimepicker_month'>
						<input id="periode-penggunaan-awal" type="text" class="form-control datetimepicker_month" placeholder="Periode Awal" disabled>
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
				</div>
				<label class="control-label col-lg-1 col-md-1 col-sm-2" style="text-align:center">-</label>
				<div class="col-lg-4 col-md-4 col-sm-5">
					<div class='input-group date datetimepicker_month'>
						<input id="periode-penggunaan-akhir" type="text" class="form-control datetimepicker_month" placeholder="Periode Akhir" disabled>
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-6 col-md-12 col-sm-12">
			<div class="form-group">
				<label class="control-label col-lg-3 col-md-3 col-sm-12">Periode Tagihan</label>
				<div class="col-lg-9 col-md-9 col-sm-12">
					<div class='input-group date datetimepicker_month'>
						<input id="periode" type="text" class="form-control datetimepicker_month" name="bulan" placeholder="Bulan">
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-12 col-md-12 col-sm-12">

			<div class="form-group" style="margin-top:20px">
				<div class="center-margin">
					<!-- <button class="btn btn-primary" type="reset" style="margin-left: 110px">Reset</button> -->
					<a id="btn-load-unit" class="btn btn-primary">Load Unit</a>
                    <a href="<?=site_url('transaksi/p_transaksi_meter_air/sync_unit');?>" id="sync_unit" class="btn btn-success">Sync Unit</a>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<br>
		<br>
		<div class="table-responsive">

		</div>
		<div class="col-md-12" id="dataisi">
			<div class="card-box table-responsive">
			</div>
			<div id="div_table" class="col-md-12 card-box table-responsive">
				<table id="table_unit" class="table table-striped jambo_table" style="width:100%; border-collapse: collapse !important;">
					<tfoot id="tfoot" style="display: table-header-group">
						<tr>
							<th class='table-kawasan'>Kawasan</th>
							<th class='table-blok'>Blok</th>
							<th>No. Unit</th>
							<th>Customer</th>
							<th>Meter Awal</th>
							<th>Meter Akhir</th>
							<th>Pemakaian</th>
							<th>Tagihan</th>
							<?php if (permission() ? permission()->create : 0) : ?>
								<th class="no-sort" hidden>Submit</th>
							<?php endif; ?>
							<th class="no-sort" hidden>Foto</th>
						</tr>
					</tfoot>
					<thead>
						<tr>
							<th class='table-kawasan'>Kawasan</th>
							<th class='table-blok'>Blok</th>
							<th>No. Unit</th>
							<th>Customer</th>
							<th>Meter Awal</th>
							<th>Meter Akhir</th>
							<th class="text-center">Pemakaian</th>
							<th style="white-space: nowrap;">Status Tagihan</th>
							<?php if (permission() ? permission()->create : 0) : ?>
								<th class="no-sort">Submit</th>
							<?php endif; ?>
							<th class="no-sort">Foto</th>
						</tr>
					</thead>
					<tbody id="tbody_unit">

					</tbody>
				</table>
			</div>
		</div>
	</form>
</div>

<!-- jQuery -->
<script type="text/javascript" src="<?= base_url(); ?>vendors/datatables.net/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
	function create_dataTable() {
		$('#table_unit tfoot th').each(function() {
			var title = $(this).text();
			$(this).html('<input type="text" class="form-control form-control-sm" placeholder="Search ' + title + '" />');
		});
	}

	function apply_search_dataTable() {
		var table_unit = $("#table_unit").DataTable();
		table_unit.columns().every(function() {
			var that = this;
			$('input', this.footer()).on('keyup change', function() {
				if (that.search() !== this.value) {
					that
						.search(this.value)
						.draw();
				}
			});
		});
	}
	create_dataTable();
	var table_unit = $("#table_unit");
	var table_unit_dt = table_unit.dataTable({
		order: [
			[1, "asc"]
		],
		columnDefs: [{
			orderable: !1,
			targets: [1]
		},
        {
            "targets": 'no-sort', 
            "orderable": false
        }],
	});
	table_unit.on("draw.dt", function() {
		$("checkbox input").iCheck({
			checkboxClass: "icheckbox_flat-green"
		})
	})

	function currency(input) {
		var input = input.toString().replace(/[\D\s\._\-]+/g, "");
		input = input ? parseInt(input, 10) : 0;
		return (input === 0) ? "" : input.toLocaleString("en-US");
	}

	function tableICheck() {
		$("input.flat").iCheck({
			checkboxClass: "icheckbox_flat-green",
			radioClass: "iradio_flat-green"
		})
	}

	function periode(e) {
		var tmp = e.val();
		console.log(tmp);
		tmp = new Date(tmp.substr(3, 4), tmp.substr(0, 2) - 1, 1);
		console.log(tmp);
		tmp.setMonth(tmp.getMonth() - 1);
		console.log(tmp);
		$("#periode-penggunaan-akhir").val(e.val());
		$("#periode-penggunaan-awal").val(("0" + (parseInt(tmp.getMonth()) + 1)).slice(-2) + "/" + tmp.getFullYear());
		console.log(tmp);
	}

	function formatNumber(data) {
		data = data + '';
		data = data.replace(/,/g, "");

		data = parseInt(data) ? parseInt(data) : 0;
		data = data.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
		return data;
	}

	function unformatNumber(data) {
		data = data + '';
		return data.replace(/,/g, "");
	}
	$(".select2").select2();
	$(function() {
		function notif(title, text, type) {
			new PNotify({
				title: title,
				text: text,
				type: type,
				styling: 'bootstrap3'
			});
		}
		var date = new Date();

		$("#periode").val(date.getMonth() + 1 + "/" + date.getFullYear());
		$("#periode").trigger("change");

		$("#periode").on('dp.change', function() {
			periode($("#periode"));
		});
		$("body").on("keyup", ".meter-akhir", function() {
			awal = unformatNumber($(this).parent().parent().children('.meter-awal').html());
			akhir = unformatNumber($(this).val());
			pakai = formatNumber(akhir - awal);
			$(this).parent().parent().children('.meter-pakai').html(pakai);

			if (akhir - awal < 0) {
				$(this).parent().parent().children().children('.save-row').addClass("disabled");
			} else {
				$(this).parent().parent().children().children('.save-row').removeClass("disabled");
				$(this).parent().parent().children('.meter-pakai').html(pakai);
			}
		});

		$("#btn-load-unit").click(function() {
            var project_id = $('#project').val();
			if ($("#kawasan").val() == null) {
				$('#kawasan').next().find('.select2-selection').addClass('has-error');
			} else {
				$('#kawasan').next().find('.select2-selection').removeClass('has-error');
			}
			if ($("#blok").val() == null) {
				$('#blok').next().find('.select2-selection').addClass('has-error');
			} else {
				$('#blok').next().find('.select2-selection').removeClass('has-error');
			}
			if ($("#periode").val() == "") {
				$('#periode').addClass('has-error');
			} else {
				$('#periode').removeClass('has-error');
			}
			if ($("#kawasan").val() != null && $("#blok").val() != null && $("#periode").val() != null) {
				$.ajax({
					type: "GET",
					data: {
						kawasan: $("#kawasan").val(),
						blok: $("#blok").val(),
						periode: $("#periode").val()
					},
					url: "<?= site_url() ?>/Transaksi/P_transaksi_meter_air/ajax_get_unit",
					dataType: "json",
					success: function(data) {
						console.log(data);
						table_unit_dt.fnDestroy();
						$("#tbody_unit").html("");

						for (var i = 0; i < data.length; i++) {

                            display_meter_akhir = '';
                            if (project_id == '1021' || project_id == '18' || project_id == '4032' || project_id == '4037')
                            {
                                display_meter_akhir = '';
                            }
                            else
                            {
                                // if (data[i].meter_akhir < 1) {
                                //     display_meter_akhir = "readonly";
                                // } else {
                                //     display_meter_akhir = '';
                                // }

                                // jika backdate, status tagihan yg sdh terbayar dia disable, selain itu bisa diubah,
                                // jika dia current date makan disabled semua yg meter air nya 0 & yg sdh terbayar
                                const today = new Date();
                                const year = today.getFullYear();
                                const month = `${today.getMonth() + 1}`.padStart(2, "0");
                                const month_year = `${month+"/"+year}`;
                                if ($("#periode").val() <= month_year) 
                                {
                                    if (data[i].status_tagihan > 0) {
                                        display_meter_akhir = "readonly";
                                    } else {
                                        display_meter_akhir = "";
                                    }
                                }
                                else if ($("#periode").val() > month_year)
                                {
                                    if (data[i].status_tagihan > 0) {
                                        display_meter_akhir = "readonly";
                                    } else {
                                        // display_meter_akhir = "";
                                        if (data[i].meter_akhir < 1) {
                                            display_meter_akhir = "readonly";
                                        }
                                    }
                                }
                            }

							<?php if (permission() ? permission()->create : 0) : ?>
								$("#tbody_unit").append(
									"<tr class='even pointer'>" +
									"<td class='table-kawasan'>" + data[i].kawasan + "</td>" +
									"<td class='table-blok'>" + data[i].blok + "</td>" +
									"<td>" + data[i].no_unit + "</td>" +
									"<td>" + data[i].pemilik + "</td>" +
									"<td class='meter-awal'>" + data[i].meter_awal + "</td>" +
	                                "<td class='a-right a-right'> <input class='meter-akhir form-control currency' value='" + data[i].meter_akhir + "' "+display_meter_akhir+">" +
	                                // "<td class='a-right a-right'> <input class='meter-akhir form-control currency' value='" + data[i].meter_akhir + "' " + data[i].data_setelah + ">" +
									"<td class='meter-pakai text-center'>" + data[i].meter_pakai + "</td>" +
									"</input></td>" +
									"<td>"+((data[i].status_tagihan > 0) ? 'Terbayar' : 'Belum Terbayar')+"</td>" +
									"<td class='col-md-1'><a class='save-row btn btn-success' unit_id='" + data[i].id + "' periode='" + $("#periode").val() + "'>Save</a></td>" +
									"<td class='col-md-1'><a class='btn-success'>View Foto</a></td>" +
									"</tr>");
							<?php else: ?>
								$("#tbody_unit").append(
									"<tr class='even pointer'>" +
									"<td class='table-kawasan'>" + data[i].kawasan + "</td>" +
									"<td class='table-blok'>" + data[i].blok + "</td>" +
									"<td>" + data[i].no_unit + "</td>" +
									"<td>" + data[i].pemilik + "</td>" +
									"<td class='meter-awal'>" + data[i].meter_awal + "</td>" +
	                                "<td class='a-right a-right'> <input class='meter-akhir form-control currency' value='" + data[i].meter_akhir + "' "+display_meter_akhir+">" +
	                                // "<td class='a-right a-right'> <input class='meter-akhir form-control currency' value='" + data[i].meter_akhir + "' " + data[i].data_setelah + ">" +
									"<td class='meter-pakai text-center'>" + data[i].meter_pakai + "</td>" +
									"</input></td>" +
									"<td>"+((data[i].status_tagihan > 0) ? 'Terbayar' : 'Belum Terbayar')+"</td>" +
									"<td class='col-md-1'><a class='btn-success'>View Foto</a></td>" +
									"</tr>");
							<?php endif; ?>
						}
						tableICheck();
						create_dataTable();
						table_unit.dataTable({
							order: [
								[2, "asc"]
							]
						});
						apply_search_dataTable();

						for (var i = 0; i < $(".paginate_button")[$(".paginate_button").length - 2].innerHTML; i++) {
							table_unit_dt.fnPageChange(i);
							if ($("#kawasan").val() != 'all')
								$(".table-kawasan").hide()
							else
								$(".table-kawasan").show()

							if ($("#blok").val() != 'all')
								$(".table-blok").hide()
							else
								$(".table-blok").show()
						}
						table_unit_dt.fnPageChange("first");
					}
				});
			}
		});

        // Sync Unit
        $(document).on('click', '#sync_unit', function(e){
            e.preventDefault();
            if ($("#kawasan").val() == null) {
                $('#kawasan').next().find('.select2-selection').addClass('has-error');
            } else {
                $('#kawasan').next().find('.select2-selection').removeClass('has-error');
            }
            if ($("#blok").val() == null) {
                $('#blok').next().find('.select2-selection').addClass('has-error');
            } else {
                $('#blok').next().find('.select2-selection').removeClass('has-error');
            }
            if ($("#periode").val() == "") {
                $('#periode').addClass('has-error');
            } else {
                $('#periode').removeClass('has-error');
            }
            if ($("#kawasan").val() != null && $("#blok").val() != null && $("#periode").val() != null)
            {
                $('#modal-body').html('<img src="../../../images/loading.gif" width="50" /> Mohon Tunggu...');
                $('#modal-footer-desc').html("<button type='button' class='btn btn-info' data-dismiss='modal'>Close</button>");
                $('#modal-notify').modal('show');
                $.ajax({
                    url: $(this).attr('href'),
                    cache: false,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if(data.status=='1'){
                            setTimeout(function(){
                                $('#modal-notify').modal('hide');
                                alert(data.message);
                            },1500);
                        }else{
                            $('#modal-notify').modal('hide');
                            alert(data.message);
                        }
                    }
                });
            }
        });

		$("body").on("click", ".save-row", function() {
			console.log($(this));
			var meter = $(this).parent().parent().find('.meter-akhir').val();
			var periode = $(this).attr('periode');
			var unit_id = $(this).attr('unit_id');

			$.ajax({
				type: "GET",
				data: {
					meter: meter,
					periode: periode,
					unit_id: unit_id
				},
				url: "<?= site_url() ?>/Transaksi/P_transaksi_meter_air/ajax_save_meter",
				dataType: "json",
				success: function(data) {
					if (data.status)
						notif('Sukses', data.message, 'success');
					else
						notif('Gagal', data.message, 'danger');
				}
			});
		})

		$('.datetimepicker_month').datetimepicker({
			viewMode: 'years',
			format: 'MM/YYYY'
		});
		$('.datetimepicker_year').datetimepicker({
			format: 'YYYY'
		});
		$("#kawasan").change(function() {
			if ($("#kawasan").val() == null) {
				$('#kawasan').next().find('.select2-selection').addClass('has-error');
			} else {
				$('#kawasan').next().find('.select2-selection').removeClass('has-error');
			}
			$.ajax({
				type: "GET",
				data: {
					id: $(this).val()
				},
				url: "<?= site_url() ?>/Transaksi/P_transaksi_meter_air/ajax_get_blok",
				dataType: "json",
				success: function(data) {
					console.log(data);
					$("#blok").html("");
					$("#blok").attr("disabled", false);
					$("#blok").append("<option value='' disabled selected>-- Pilih Kawasan Dahulu --</option>");
					$("#blok").append("<option value='all'>-- Semua Blok --</option>");
					for (var i = 0; i < data.length; i++) {
						$("#blok").append("<option value='" + data[i].id + "'>" + data[i].name + "</option>");
					}
				}
			});
		});
		$("#blok").change(function() {
			if ($("#blok").val() == null) {
				$('#blok').next().find('.select2-selection').addClass('has-error');
			} else {
				$('#blok').next().find('.select2-selection').removeClass('has-error');
			}
		});
		$("#periode").on('dp.change', function(e) {
			console.log(e);
			if ($("#periode").val() == "") {
				$('#periode').addClass('has-error');
			} else {
				$('#periode').removeClass('has-error');
			}
		});
		$("#unit").change(function() {
			url = '<?= site_url(); ?>/P_transaksi_meter_air/getInfoUnit';
			var id = $("#unit").val();
			//console.log(this.value);
			$.ajax({
				type: "get",
				url: url,
				data: {
					id: id
				},
				dataType: "json",
				success: function(data) {
					$("#customer").val(data.customer);
					$("#barcode").val(data.barcode);
					$("#meter_awal").val(currency(data.meter));
					$("#meter_akhir").attr('disabled', false);
					$("#meter_akhir").attr('placeholder', '-- Masukkan Meter Akhir --');
				}
			});
		});
		$("#meter_akhir").keyup(function() {
			$("#pemakaian").val($("#meter_akhir").val().replace(/,/g, '') - $("#meter_awal").val().replace(/,/g, ''));
		});
	});
</script>