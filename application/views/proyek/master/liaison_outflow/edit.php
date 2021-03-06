<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<link href="<?=base_url(); ?>vendors/switchery/dist/switchery.min.css" rel="stylesheet">
<script src="<?=base_url(); ?>vendors/switchery/dist/switchery.min.js"></script>

<link href="<?=base_url(); ?>vendors/select2/dist/css/select2.min.css" rel="stylesheet">
<script src="<?=base_url(); ?>vendors/select2/dist/js/select2.min.js"></script>

<!-- modals -->
<!-- Large modal -->
<div id="modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Detail Log</h4>
			</div>
			<div class="modal-body">
				<table class="table table-striped jambo_table bulk_action">
					<thead>
						<tr>
							<th>Point Detail</th>
							<th>Before</th>
							<th>After</th>
						</tr>
					</thead>
					<tbody id="dataModal">
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>

		</div>
	</div>
</div>

<div style="float:right">
	<h2>
		<button class="btn btn-warning" onClick="window.history.back()">
			<i class="fa fa-arrow-left"></i>
			Back
		</button>
		<button class="btn btn-success" onClick="window.open(window.location.href,'_self')">
			<i class="fa fa-repeat"></i>
			Refresh
		</button>
	</h2>
</div>
<div class="clearfix"></div>
</div>

<div class="x_content">
	<br>
	<form id="form" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post" action="<?=site_url(); ?>/proyek/master/liaison_outflow/edit?id=<?=$this->input->get('id'); ?>">


		<div class="form-group">
			<label class="control-label col-md-3 col-sm-3 col-xs-12" for="code">Kode <span class="required">*</span>
			</label>
			<div class="col-md-6 col-sm-6 col-xs-12">
				<input type="text" id="code" name="code" required="required" class="form-control col-md-7 col-xs-12 disabled-form"
				 value="<?=$data_select->code; ?>" disabled>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3 col-sm-3 col-xs-12" for="nama_transaksi">Nama Item Transaksi <span class="required">*</span>
			</label>
			<div class="col-md-6 col-sm-6 col-xs-12">
				<input type="text" id="nama_transaksi" name="nama_transaksi" required="required" class="form-control col-md-7 col-xs-12 disabled-form"
				 value="<?=$data_select->name; ?>" disabled>
			</div>
		</div>
		<div class="form-group">
			<label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Keterangan</label>
			<div class="col-md-6 col-sm-6 col-xs-12">
				<textarea id="keterangan" class="form-control col-md-7 col-xs-12 disabled-form" type="text" name="keterangan"
				 disabled><?=$data_select->description; ?></textarea>
			</div>
		</div>
		<div class="col-md-6 col-xs-12">
			<label class="control-label col-md-6 col-sm-6 col-xs-12">Status</label>
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="">
					<label>
						<input id="active" type="checkbox" class="js-switch disabled-form" name="active" value='1' <?=$data_select->active
						== 1 ? 'checked' : ''; ?> disabled/> Aktif
					</label>
				</div>
			</div>
		</div>


        <div class="clearfix"></div>
		
		<div id="outflow">
			<div idFromItem="1" class="table_outflow col-md-12" style="margin-top:20px">
				<div class="col-md-12 x_title">
					<h2>Outflow Transaksi</h2>
					<div class="clearfix"></div>
				</div>
				<table class="table table-responsive">
					<thead>
						<tr>
							<th>No</th>
							<th>No Log</th>
							<th>Kode</th>
							<th>Nama</th>
							<th>Harga</th>
							<th>Keterangan</th>
							<th>Hapus</th>
						</tr>
					</thead>
					<tbody id="tbody_outflow_transaksi">
						<!--<tr id="srow2">-->
							<!-- <td><input type="text" class="form-control" value="" name="kode[]" placeholder="Masukkan Kode"></td>
							<td><input type="text" class="form-control" value="" placeholder="Masukkan Nama" name="nama[]"></td>
							<td><input type="text" name="harga[]" value="" placeholder="Masukkan Harga" onkeydown="return numbersonly(this, event);"
									onkeyup="javascript:tandaPemisahTitik(this);" class="form-control"></td>
							<td><input type="text" class="form-control" value="" name="keterangan_outflow[]" placeholder="Masukkan Keterangan"></td>
							<td class="delete" onclick="deleteRow($(this))"> <a class="btn btn-danger" style="color:#3399FD;"><i class="fa fa-trash"></i>
								</a></td> -->
                           

                        <?php
							$i = 0;
							$j = 0;
							//var_dump($dataRangeAirDetail);
                            foreach ($dataTransaksiLOOutflow as $v) {
								++$j;
								if($v['delete'] == 0){
									++$i;
									echo "<tr id='srow".$i."'>";
									echo "<td hidden><input name='id_transaksi_liaison_outflow[]' value='$v[id]'> </td>";
									echo "<td class='no2' >".$i.'</td>';
                                    echo "<td class='nolog' >".$j.'</td>';
                                    echo "<td><input type='text' class='form-control disabled-form' name='kode2[]' placeholder='Masukkan Kode' required value ='$v[code]'disabled ></td>" ;
									echo "<td><input type='text' class='form-control disabled-form' name='nama2[]' placeholder='Masukkan Nama'  required value ='$v[name]' disabled></td>";
									echo "<td><input type='text' class='form-control disabled-form' name='harga2[]' placeholder='Masukkan Harga' required value ='$v[harga]' disabled></td>";
									echo "<td><input type='text' class='form-control disabled-form' name='keterangan2[]' placeholder='Masukkan Keterangan' required value ='$v[description]' disabled></td>";
									echo"<td class='delete' onclick='deleteRow($(this))'> <a class='btn btn-danger disabled-form' style=\"color:#3399FD;\" disabled><i class='fa fa-trash'></i> </a></td>" ;
									echo '</tr>';
								}
                            }
                        ?>
                        	

                        <td><input id="idf2" value="1" type="hidden" /></td>

                     
						
					</tbody>
				</table>

				<button id='button_outflow_transaksi' type="button" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>
					Add Outflow Transaksi</button>
			</div>
		</div>
		






		<div class="col-md-12">
			<input id="btn-update" class="btn btn-success col-md-1 col-md-offset-5" value="Edit">
			<input id="btn-cancel" class="btn btn-danger col-md-1" value="Cancel" style="display:none">
		</div>
	</form>

	<div class="x_panel">
		<div class="x_title">
			<h2>Log</h2>
			<div class="clearfix"></div>
		</div>
		<div class="x_content">
			<br>
			<table class="table table-striped jambo_table tableDT">
				<thead>
					<tr>
						<th>No</th>
						<th>Waktu</th>
						<th>User</th>
						<th>Status</th>
						<th>Detail</th>
					</tr>
				</thead>
				<tbody>
					<?php
            $i = 0;
            foreach ($data as $key => $v) {
                ++$i;
                echo '<tr>';
                echo "<td>$i</td>";
                echo "<td>$v[date]</td>";
                echo "<td>$v[name]</td>";
                echo '<td>';
                if ($v['status'] == 1) {
                    echo 'Tambah';
                } elseif ($v['status'] == 2) {
                    echo 'Edit';
                } else {
                    echo 'Hapus';
                }
                echo '</td>';
                echo "
                    <td class='col-md-1'>
                        <a class='btn-modal btn btn-sm btn-primary col-md-12' data-toggle='modal' data-target='#modal' data-transfer='$v[id]' data-type='$v[status]'>
                            <i class='fa fa-pencil'></i>
                        </a>
                    </td>
                ";
                echo '</td></tr>';
            }
        ?>
				</tbody>
			</table>
		</div>
	</div>

	<!-- Modal -->


    <script type="text/javascript">
	const outflow = $('.table_outflow');
	function deleteRow(row) {
		row.parent().remove();
	};
	function createOT(row){
		$(".createOT").parent().parent().removeClass('active')
		row.parent().parent().addClass('active');
	}
	$(function () {
		$("#button_outflow_transaksi").click(function () {
			if ($(".no2").html()) {
				idf2 = parseInt($(".no2").last().html()) + 1;
			} else {
				idf2 = 1;
			}
			var str = "<tr id='srow2" + idf2 + "'>" +
			    "<td hidden><input name='id_transaksi_liaison_outflow[]' value='0'> </td>" +
				"<td class='no2'>" + idf2 + "</td>" +
				"<td class='nolog' ></td>" +
				"<td><input type='text' class='form-control' value='' name='kode2[]' placeholder='Masukkan Kode' /></td>" +
				"<td><input type='text' class='form-control' value='' placeholder='Masukkan Nama' name='nama2[]' placeholder='' /></td>" +
				"<td><input type='text' name='harga2[]' value='' placeholder='Masukkan Harga' onkeydown='return numbersonly(this, event);' onkeyup='javascript:tandaPemisahTitik(this);' class='form-control'/></td>" +
				"<td><input type='text' class='form-control' value='' name='keterangan2[]' placeholder='Masukkan Keterangan' /></td>" +
				"<td class='delete' onclick='deleteRow($(this))' > <a class='btn btn-danger' style=\"color:#3399FD;\"><i class='fa fa-trash'></i> </a></td>" +
				"</tr>";
			$("#tbody_outflow_transaksi").append(str);
		});

	});

</script>


	




	<script type="text/javascript">



		$("#btn-update").click(function () {
			disableForm = 0;
			$(".disabled-form").removeAttr("disabled");
			$("#btn-cancel").removeAttr("style");
			$("#btn-update").val("Update");
			setTimeout(function(){ $("#btn-update").attr("type", "submit"); }, 100);
		});
		$("#btn-cancel").click(function () {
			disableForm = 1;
			$(".disabled-form").attr("disabled", "")
			$("#btn-cancel").attr("style", "display:none");
			$("#btn-update").val("Edit")
			$("#btn-update").removeAttr("type");
		});


		$(".btn-modal").click(function () {
			url = '<?=site_url(); ?>/core/get_log_detail';
			console.log($(this).attr('data-transfer'));
			console.log($(this).attr('data-type'));
			$.ajax({
				type: "POST",
				data: {
					id: $(this).attr('data-transfer'),
					type: $(this).attr('data-type')
				},
				url: url,
				dataType: "json",
				success: function (data) {
					console.log(data);
					// var items = []; 
					// $("#changeJP").attr("style","display:none");
					// $("#saveJP").removeAttr('style');
					// $("#jabatan").removeAttr('disabled');
					// $("#jabatan")[0].innerHTML = "";
					// $("#project")[0].innerHTML = "";
					// $("#jabatan").append("<option value='' selected disabled>Pilih Jabatan</option>");
					console.log($(this).attr('data-type'));
					$("#dataModal").html("");
					if (data[data.length - 1] == 2) {
						console.log(data[0]);
						for (i = 0; i < data[0].length; i++) {
							var tmpj = 0;
							for (j = 0; j < data[0].length; j++) {
								if (data[1][j] != null) {
									if (data[1][j].name == data[0][i].name) {
										$("#dataModal").append("<tr><th>" + data[0][i].name + "</th><td>" + data[1][j].value + "</td><td>" + data[0]
										[i].value + "</td></tr>");
										tmpj++;
									}

								}
							}
							if (tmpj == 0) {
								$("#dataModal").append("<tr><th>" + data[0][i].name + "</th><td></td><td>" + data[0]
								[i].value + "</td></tr>");
							}
						}

						// 	if(data[1].length > data[0].length){
						// 		$.each(data[1], function (key, val) {
						// 			if (val.name == data[0][i].name) {
						// 				console.log(val.name);
						// 				$("#dataModal").append("<tr><th>" + data[0][i].name + "</th><td>" + val.value + "</td><td>" + data[0]
						// 					[i].value + "</td></tr>");
						// 			}
						// 		});
						// 	}else{
						// 		$.each(data[0], function (key, val) {
						// 			if (val.name == data[1][i].name) {
						// 				console.log(val.name);
						// 				$("#dataModal").append("<tr><th>" + data[1][i].name + "</th><td>" + val.value + "</td><td>" + data[1]
						// 					[i].value + "</td></tr>");
						// 			}
						// 		});
						// 	}
						// }
					} else {
						$.each(data, function (key, val) {
							if (data[data.length - 1] == 1) {
								console.log(data);
								if (val.name)
									$("#dataModal").append("<tr><th>" + val.name.toUpperCase() + "</th><td></td><td>" + val.value +
										"</td></tr>");
							} else if (data[data.length - 1] == 2) {

							} else if (data[data.length - 1] == 3) {
								console.log(data);
								if (val.name)
									$("#dataModal").append("<tr><th>" + val.name.toUpperCase() + "</th><td>" + val.value +
										"</td><td></td></tr>");
							}
						});
					}

				}
			});

		});
		$('.select2').select2({
			width: 'resolve'
		});

		$(document).keydown(function (e) {
			return (e.which || e.keyCode) != 116;
		});

		$(document).keydown(function (e) {
			if (e.ctrlKey) {
				return (e.which || e.keyCode) != 82;
			}
		});




	</script>