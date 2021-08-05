<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<link rel="stylesheet" href="<?= base_url(); ?>vendors/select2/dist/css/select2.min.css">
<script src="<?= base_url(); ?>vendors/select2/dist/js/select2.min.js"></script>
<div style="float:right">
	<h2>
        <button id='print-data' class='btn btn-success' type="button">
            <i class="fa fa-file-excel-o"></i> 
            <!-- <i class="fa fa-print"></i>  -->
            Print Excel
        </button>
        <button id='print-doc' class='btn btn-danger'>
            <img src='<?=base_url('images/extension/icon_pdf.png');?>' style='margin-top: -3px;'/> Print Document
        </button>
		<button id="btn-kirim-email" class="btn btn-primary">
			<i class="fa fa-plus"></i>
			Kirim Email
		</button>
		<button id="btn-kirim-sms" class="btn btn-primary">
			<i class="fa fa-plus"></i>
			Kirim SMS
		</button>

		<button class="btn btn-warning" onClick="window.history.back()" disabled>
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


<div class="x_content" style="padding: 10px;">
	<form>
		<table id="tableDTServerSite" class="table table-striped jambo_table bulk_action">
            <tfoot id="tfoot" style="display: table-header-group;">
                <tr>
                    <th class="hide-search">Check</th>
                    <th>Kawasan</th>
                    <th>Blok</th>
                    <th>No. Unit</th>
                    <th>Tujuan</th>
                    <th>Pemilik</th>
                    <th>Email</th>
                    <th>SMS</th>
                    <th>Surat</th>
                    <th class="hidden">Dokumen Downloaded</th>
                    <th>Total Tagihan</th>
                </tr>
            </tfoot>
			<thead>
				<tr>
					<th class="col-md-1 col-sm-1 col-lg-1 col-xs-1 no-sort" id="di_bayar_dengan_table" width="50">
						<center><input id="check-all" type='checkbox' class='flat'></center>
					</th>
					<th>Kawasan</th>
					<th>Blok</th>
					<th>No. Unit</th>
					<th>Tujuan</th>
					<th>Pemilik</th>
					<th>Email</th>
					<th>SMS</th>
					<th>Surat</th>
					<th class="no-sort">Dokumen Downloaded</th>
                    <th>Total Tagihan</th>
				</tr>
			</thead>
			<tbody id="load_data"></tbody>
		</table>
	</form>

	<!-- (Normal Modal)-->
	<div class="modal fade" id="modal_delete_m_n" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content" style="margin-top:100px;">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" style="text-align:center;">Apakah anda yakin untuk mendelete data ini ?<span class="grt"></span> ?</h4>
				</div>

				<div class="modal-footer" style="margin:0px; border-top:0px; text-align:center;">
					<span id="preloader-delete"></span>
					<br>
					<a class="btn btn-danger" id="delete_link_m_n" href="">Delete</a>
					<button type="button" class="btn btn-info" data-dismiss="modal" id="delete_cancel_link">Cancel</button>
				</div>
			</div>
		</div>
	</div>
<script>
	$(document).ready(function() {
		$("#a").html('');
		$('.select2').select2();
	});

	$(document).ready(function(){
		$('#tableDTServerSite tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input type="text" placeholder="..." />' );
		});
        $('#load_data').html('<tr><td colspan="10" align="center">Mohon Tunggu...</td></tr>');
        var dataTable = $('#tableDTServerSite').DataTable({
            "serverSide": true,
            "stateSave" : false,
            "bAutoWidth": true,
            "responsive": true,
            "oLanguage": {
                "sSearch": " ",
                "sLengthMenu": "_MENU_",
                "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
                "sInfoFiltered": "(filtered from _MAX_ total entries)",
                "sZeroRecords": "<center>Data tidak ditemukan</center>",
                "sEmptyTable": "No data available in table",
                "sLoadingRecords": "Please wait - loading...",
                "oPaginate": {
                    "sPrevious": "Prev",
                    "sNext": "Next"
                }
            },
            "aaSorting": [[ 1, "desc" ]],
            "columnDefs": [
                // {"aTargets":[0], "sClass" : "column-hide"},
                {"targets": 'no-sort', "orderable": false}
            ],
            "sPaginationType": "simple_numbers",
            "iDisplayLength": 10,
            "ajax": {
                url : "<?=site_url('Transaksi/P_kirim_konfirmasi_tagihan/request_tagihan_json');?>",
                type: "get",
                cache: false,
            }
        });

		
        // Apply the search
        dataTable.columns().every(function() {
            var pencarian = this;
            $('input', this.footer()).on('keyup change', function() {
                if (pencarian.search() !== this.value) {
                    pencarian.search(this.value).draw();
                }
            });
        });
	});

	$("#btn-kirim-email").click(function() {
		// var unit_id = $("input[name='unit_id[]']").map(function() {
		// 	if ($(this).is(":checked")) {
		// 		return $(this).attr("val");
		// 	}
		// }).get();
        var unit_id = $('#tableDTServerSite tbody input:checkbox:checked').map(function(){
            return this.value;
        }).get();
		$.ajax({
			type: "POST",
			data: {
				unit_id: unit_id
			},
			url: "<?= site_url() ?>/Transaksi/P_kirim_konfirmasi_tagihan/kirim_email",
			dataType: "json",
			success: function(data) {
				if (data)
					notif('Sukses', 'Pengiriman Email Sukses', 'success');
				else
					notif('Gagal', 'Pengiriman Email Gagal', 'danger');
			}

		});
	})
	$("#btn-kirim-sms").click(function() {
		// var unit_id = $("input[name='unit_id[]']").map(function() {
		// 	if ($(this).is(":checked")) {
		// 		return $(this).attr("val");
		// 	}
		// }).get();
        var unit_id = $('#tableDTServerSite tbody input:checkbox:checked').map(function(){
            return this.value;
        }).get();
		$.ajax({
			type: "POST",
			data: {
				unit_id: unit_id
			},
			url: "<?= site_url() ?>/Transaksi/P_kirim_konfirmasi_tagihan/kirim_sms",
			dataType: "json",
			success: function(data) {
				if (data)
					notif('Sukses', 'Pengiriman SMS Sukses', 'success');
				else
					notif('Gagal', 'Pengiriman SMS Gagal', 'danger');
			}

		});
	})
	$(".delete_data").click(function() {
		var r = confirm('Are You Sure Want To Delete This Data ?');
		if (r == true) {

			url = '<?= site_url(); ?>/P_master_mappingCoa/delete';
			var id = $(this).attr('id');

			$.ajax({
				url: url,
				method: "POST",
				data: {
					id: id
				},
				dataType: "text",
				success: function(data) {
					alert('Data berhasil dihapus...');
				}
			});
		}
	});

    $("table").on("ifChanged", "#check-all", function() {
        if ($("#check-all").is(":checked")) {
            $(".table-check").iCheck("check");
        }else{
            $(".table-check").iCheck("uncheck");
        }
    });

    // modify dr
    $(document).on("click", "#tableDTServerSite tbody td", function(){
        if($(this).index() == 0)
        {
            if ($(this).find('input').prop("checked")) {
                // $("#print-doc").show();
            } else {
                if ($("#tableDTServerSite tbody input:checked").length < 1) {
                    // $("#print-doc").hide();
                }
            }
        }

        if ($(this).index() > 0)
        {
            var Is_checked = $(this).parent().find('td:nth-child(1) input');
            if(Is_checked.prop("checked")) {
                Is_checked.prop("checked", false);
                if($("#tableDTServerSite tbody input:checked").length < 1) {
                    // $("#print-doc").hide();
                }
            }  else {
                Is_checked.prop("checked", true);
                if($("#tableDTServerSite tbody input:checked").length > 0) {
                    // $("#print-doc").show();
                }
            }
        }
    });


    //*********************************************************************************************
    // PROSES PRINT DOCUMENT MANY
    //*********************************************************************************************
    $(document).on('click', '#print-doc', function(){
        var Links = "<?= site_url('transaksi/p_kirim_konfirmasi_tagihan/print_pdf/'); ?>";
        var checkedValues = $('#tableDTServerSite tbody input:checkbox:checked').map(function(){
            return this.value;
        }).get();

        const jml_row = $("#tableDTServerSite tbody input:checked").length;
        if (jml_row < 1) {
            alert('Mohon checklist salah satu data.');
        } else {
            window.open(Links+"?unit_id="+checkedValues);
            /*$.ajax({
                url: Links,
                cache: false,
                type: "POST",
                dataType: "json",
                data: {unit_id:checkedValues},
                success: function(data) {
                    if (data.status == 1) {
                        window.open(data.LinkURL);
                    }
                }
            });*/
        }
    });
    function formatRupiah(angka, prefix){
		var number_string = angka.toString(),
		split   		= number_string.split(','),
		sisa     		= split[0].length % 3,
		rupiah     		= split[0].substr(0, sisa),
		ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);

		// tambahkan titik jika yang di input sudah menjadi angka ribuan
		if(ribuan){
			separator = sisa ? '.' : '';
			rupiah += separator + ribuan.join('.');
		}

		rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
		return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
	}

    $(document).on('click', '#print-data', function(e){
		var checkedValues = $('#tableDTServerSite tbody input:checkbox:checked').map(function(){
		    return this.value;
		}).get();

		const jml_row = $("#tableDTServerSite tbody input:checked").length;
		if (jml_row < 1) {
		    alert('Mohon checklist salah satu data.');
		} else {
		    $.ajax({
				url: "<?=base_url();?>transaksi/p_kirim_konfirmasi_tagihan/print_excel",
				method: "POST",
				data: {
					list_unit_id: checkedValues
				},
                dataType: "json",
				success: function(data) {
					if (data.length) {
						var print_content = '\
							<table class="table table-striped jambo_table" id="tb-penerimaan" style="width:100%" border="1">\
                                <thead>\
                                    <tr>\
                                        <th style="text-align: left;">No.</th>\
                                        <th style="text-align: center;">Kawasan</th>\
                                        <th style="text-align: center;">Blok</th>\
                                        <th style="text-align: center;">No. Unit</th>\
                                        <th style="text-align: center;">Tujuan</th>\
                                        <th style="text-align: center;">Pemilik</th>\
                                        <th style="text-align: center;">Email</th>\
                                        <th style="text-align: center;">SMS</th>\
                                        <th style="text-align: center;">Surat</th>\
                                        <th style="text-align: center;">Total Tagihan</th>\
                                    </tr>\
                                </thead>\
                                <tbody>\
						';
						var no = 1;
						for (var i = 0; i < data.length; i++) {
							print_content += '<tr>\
								<td>'+(no+i)+'</td>\
								<td>'+data[i]['kawasan']+'</td>\
								<td>'+data[i]['blok']+'</td>\
								<td>'+data[i]['no_unit']+'</td>\
								<td>'+data[i]['tujuan']+'</td>\
								<td>'+data[i]['pemilik']+'</td>\
								<td>'+data[i]['send_email']+'</td>\
								<td>'+data[i]['send_sms']+'</td>\
								<td>'+data[i]['send_surat']+'</td>\
								<td>'+data[i]['total_tagihan']+'</td>\
							</tr>';
						}
						print_content += '</tbody></table>';
						
				        var winPrint = window.open('', '', 'left=0,top=0,width=800,height=600,toolbar=0,scrollbars=0,status=0');
				        winPrint.document.write(print_content);
				        winPrint.document.close();
				        winPrint.focus();
				        winPrint.print();
				        winPrint.close(); 
					}
				}
			});
	    }
    });
</script>
<script>
	function confirm_modal(id) {
		jQuery('#modal_delete_m_n').modal('show', {
			backdrop: 'static',
			keyboard: false
		});
		document.getElementById('delete_link_m_n').setAttribute("href", "<?= site_url('P_master_mappingCoa/delete?id="+id+"'); ?>");
		document.getElementById('delete_link_m_n').focus();
	}
</script>

<style type="text/css">
    div.dataTables_wrapper div.dataTables_length select {
        height: 32px;
    }
    .dataTables_length {
        height: 35px;
    }
</style>