<link href="<?= base_url(); ?>vendors/select2/dist/css/select2.min.css" rel="stylesheet">
<link href="<?= base_url(); ?>vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<script src="<?= base_url(); ?>vendors/select2/dist/js/select2.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>vendors/moment/min/moment.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<style>
	.invalid { background-color: lightpink; }
	.has-error { border: 1px solid rgb(185, 74, 72) !important; }
    #select2-kawasan-results { font-size: 12px; }
</style>
<div style="float:right">
	<h2>
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
</div>
    <div class="x_content">
        <br>
        <form id="demo-form2" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
            <div class="item form-group">
                <label class="col-form-label col-md-3 col-sm-3 label-align" style="text-align: right;margin-top: 8px;">Kawasan<span class="required">*</span></label>
                <div class="col-md-4">
                    <select name="kawasan" required="" id="kawasan" class="form-control select2" placeholder="....">
                        <option value="" disabled selected>....</option>
                        <option value="all">--Semua Kawasan--</option>
                        <?php
                        foreach ($kawasan as $v) {
                            echo ("<option value='$v->id'>$v->code - $v->name</option>");
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="item form-group">
                <label class="col-form-label col-md-3 col-sm-3 label-align" style="text-align: right;margin-top: 8px;">Blok</label>
                <div class="col-md-4">
                    <select class="form-control select2" name="blok" id="blok" required="" placeholder="-- Pilih Kawasan Dahulu --" disabled="">
                        <option value="" disabled="" selected="">-- Pilih Kawasan Dahulu --</option>
                        <option value="all">-- Semua Blok --</option>
                    </select>
                </div>
            </div>

            <div class="item form-group">
                <label class="col-form-label col-md-3 col-sm-3 label-align">&nbsp;</label>
                <div class="col-md-6 col-sm-6" style="margin-top: 6px;">
                    <button type="button" class="btn btn-primary" id="btn-generate"><i class="fa fa-refresh"></i> Generate</button>
                    <button type="button" class="btn btn-success" id="export-excel"><i class="fa fa-print"></i> Expot Excel</button>
                </div>
            </div>
        </form>
        <div class="ln_solid"></div>
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <table class="table table-striped jambo_table bulk_action" id="tb-customer" width="100%">
                    <thead>
                        <tr>
                            <th width="20">#</th>
                            <th>Kawasan</th>
                            <th>Blok</th>
                            <th>Unit</th>
                            <th>Pemilik</th>
                            <th>No. Hp</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_customer"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
$(function(){
    $(".select2").select2();

    $("#kawasan").change(function() {
        if ($("#kawasan").val() == null) {
            $('#kawasan').next().find('.select2-selection').addClass('has-error');
        } else {
            $('#kawasan').next().find('.select2-selection').removeClass('has-error');
        }
        $.ajax({
            url: "<?=site_url('report/p_customer/ajax_get_blok');?>",
            type: "GET",
            cache: false,
            data: {
                id: $(this).val()
            },
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

    $(document).on('click', '#btn-generate', function(e){
        e.preventDefault();
        kawasan = $('#kawasan').val();
        blok = $('#blok').val();

        if (kawasan == null || kawasan == '') {
            alert('Kawasan masih kosong.');
        } else if (blok == null || blok == '') {
            alert('Blok masih kosong.');
        } else {
            table_customer(kawasan, blok);
        }
    });

    $(document).on('click', '#export-excel', function(e){
        e.preventDefault();
        kawasan = $('#kawasan').val();
        blok = $('#blok').val();

        if (kawasan == null || kawasan == '') {
            alert('Kawasan masih kosong.');
        } else if (blok == null || blok == '') {
            alert('Blok masih kosong.');
        } else {
            variable = '?id_kawasan='+kawasan+'&id_blok='+blok;
            window.open("<?=site_url('report/p_customer/report_excel/');?>"+variable);
        }
    });
});

function table_customer(kawasan=null, blok=null)
{
    $('#tb-customer').DataTable({
        "serverSide": true,
        "stateSave" : false,
        "bAutoWidth": true,
        "bDestroy" : true,
        // "bPaginate": false,
        // "bFilter": false,
        // "bInfo": true,
        "oLanguage": {
            "sSearch": "Live Search : ",
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
        "aaSorting": [[ 0, "asc" ]],
        "columnDefs": [
            {"aTargets":[0], "sClass" : "column-hide"},
            {"aTargets":[1], "sClass" : "column-hide"},
            {"targets": 'no-sort', "orderable": false}
        ],
        "sPaginationType": "simple_numbers",
        "iDisplayLength": 10,
        "aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
        "ajax": {
            url : "<?=site_url('report/p_customer/request_cust_json'); ?>",
            cache: false,
            type: "post",
            data: {
                id_kawasan: kawasan,
                id_blok: blok,
            },
            error: function() {
                $(".tb-customer-error").html("");
                $("#tb-customer").append('<tbody class="tb-customer-error"><tr><th colspan="9"><center>No data found in the server</center></th></tr></tbody>');
                $("#tb-customer_processing").css("display","none");
            }
        }
    });
}
</script>