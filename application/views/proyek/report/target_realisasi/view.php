
<link href="<?= base_url('vendors/select2/dist/css/select2.min.css');?>" rel="stylesheet">
<link href="<?= base_url('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css');?>" rel="stylesheet">
<script src="<?=base_url('vendors/select2/dist/js/select2.min.js'); ?>"></script>
<script src="<?=base_url('vendors/moment/min/moment.min.js');?>"></script>
<script src="<?=base_url('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js');?>"></script>
<style type="text/css">
    .btn { border-radius: 0px !important; padding: 7px 12px; font-size: 13px;}
    .btn:focus { outline: none;}
    .nav-md {display: block !important;}
    .invalid { background-color: lightpink; }
    .table thead th{ border: 1px solid #556c81 !important; }
    table.dataTable thead .sorting_asc:after { font-size: 11px; }
    a.disabled { pointer-events: none; cursor: default; }
    .has-error { border: 1px solid rgb(185, 74, 72) !important; }
    div.dataTables_wrapper { width: 99%; height: 100%; margin: 0 auto; }
    .select2-container--default .select2-selection--single {min-height: 34px;}
    .table>thead>tr>th { border-bottom: 1px solid #556c81 !important; padding: 6px; }
    .table>tbody>tr>td { border: 1px solid #ddd; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { padding-top: 2px; font-size: 12px; }
    .form-control, .select2-container--default .select2-selection--single { font-size: 12px; box-shadow: none; border-radius: 0px !important; }
    #load_data td:nth-child(1), td:nth-child(3), td:nth-child(8), td:nth-child(10) { white-space: nowrap; }
    .dataTables_length select,
    .dataTables_filter input { padding: 6px 12px; border: 1px solid #ccc; border-radius: 0px; }
    tfoot input::placeholder { font-size:11px; }
    #tb-target-realisasi thead th i { font-size: 15px; margin-left: 3px; cursor: pointer; }
    .hide-search input {display:none;}
</style>

<div class="clearfix"></div>
</div>
<div class="x_conte nt">
    <form action="" class="form-horizontal form-label-left">
        <div class="form-group row col-md-3">
            <label>Kawasan</label>
            <select name="kawasan" required="" id="kawasan" class="form-control select2" placeholder="-- Pilih Kawasan --">
                <option value="" disabled selected>-- Pilih Kawasan --</option>
                <option value="all">Semua Kawasan</option>
                <?php
                foreach ($kawasan as $v) {
                    echo ("<option value='$v->id'>$v->code - $v->name</option>");
                }
                ?>
            </select>
        </div>
        <div class="form-group row col-md-3">
            <label>Blok</label>
            <select name="blok" required="" id="blok" class="form-control select2" placeholder="-- Pilih Blok --" disabled>
                <option value="" disabled selected>-- Pilih Blok --</option>
                <option value="all">-- Semua Blok --</option>
            </select>
        </div>
        <div class="form-group row col-md-3">
            <label>Periode Tagihan</label>
            <div class='input-group date datetimepicker_month'>
                <input type="text" id="periode-tagihan" class="form-control datetimepicker_month" placeholder="Periode Tagihan" value="<?=date('Y-m');?>">
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
        <div class="col-md-8 center-margin">
            <a id="btn-load-unit" class="btn btn-primary"><i class="fa fa-repeat"></i> Generate</a>
            <a id="export-excel" class="btn btn-success"><i class="fa fa-file-excel-o"></i>&nbsp; Export </a>
        </div>
    </form>
    <br>
    <div class="clearfix"></div>
    <br>
    <br>
    <div class="table-responsive">
    <div class="col-lg-12 col-md-12 col-sm-12" style="margin-top:20px">
        <table class="table table-striped jambo_table" id="tb-target-realisasi" style="width:100%">
            <tfoot id="tfoot" style="display: table-header-group;">
                <tr>
                    <th>Kawasan</th>
                    <th>Blok/Unit</th>
                    <th>Pemilik</th>
                    <th>Periode Tagihan</th>
                    <th>Nilai Air</th>
                    <th>Nilai IPL</th>
                    <th>Nilai Total</th>
                    <th>Bayar Air</th>
                    <th>Bayar IPL</th>
                    <th>Total Realisasi</th>
                    <th>%</th>
                </tr>
            </tfoot>
            <thead>
                <tr>
                    <th rowspan="2" style="vertical-align: middle;">Kawasan</th>
                    <th rowspan="2" style="vertical-align: middle;">Blok/Unit</th>
                    <th rowspan="2" style="vertical-align: middle;">Pemilik</th>
                    <th rowspan="2" style="vertical-align: middle;">Periode Tagihan</th>
                    <th colspan="3" style="text-align: center;">Target</th>
                    <th colspan="3" style="text-align: center;">Realisasi</th>
                    <th rowspan="2" class="no-sort" style="vertical-align: middle; text-align: center; width: 55px;">%</th>
                </tr>
                <tr>
                    <th class="no-sort" style="white-space: nowrap;">Nilai Air <i class="fa fa-info-circle" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Nilai Tagihan + Nilai Administrasi +Nilai Pemeliharaan"></i></th>
                    <th class="no-sort">Nilai IPL</th>
                    <th class="no-sort">Total Target</th>
                    <th class="no-sort">Bayar Air</th>
                    <th class="no-sort">Bayar IPL</th>
                    <th class="no-sort">Total Realisasi</th>
                </tr>
            </thead>
            <tbody id="load_data"><tr><td colspan="12" align="center">No data available in table</td></tr></tbody>
        </table>
    </div>
    </div>
</div>

<div class="x_content">
    <div class="modal" id="modal-notify" data-backdrop="static" data-keyboard="false" style="width:100vw">
        <div class="modal-dialog" style="width: 20vw;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Notifikasi<span class="grt"></span></h4>
                </div>
                <div class="modal-body" id="modal-body"></div>
                <div class="modal-footer" id="modal-footer-desc" style="margin:0px; border-top:0px;"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(".select2").select2();
        $('.datetimepicker_month').datetimepicker({
            viewMode: 'years',
            format: 'YYYY-MM'
        });

        $('#tb-target-realisasi tfoot th').each(function(){
            var title = $(this).text();
            $(this).html("<input type='text' class='form-control form-control-sm' placeholder='"+title+"' />");
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
                url: "<?=site_url('Transaksi/P_transaksi_meter_air/ajax_get_blok');?>",
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    $("#blok").html("");
                    $("#blok").attr("disabled", false);
                    $("#blok").append("<option value='' disabled selected>-- Pilih Blok --</option>");
                    $("#blok").append("<option value='all'>Semua Blok</option>");
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

        $(document).on('click', '#btn-load-unit', function(e){
            e.preventDefault();
            kawasan = $("#kawasan").val();
            blok = $("#blok").val();
            periode_tagihan  = $("#periode-tagihan").val();
            ErrCount = 0;
            ErrMsg = '<ul>';

            if (kawasan=='' || kawasan==null) {
                ErrMsg += '<li>Kawasan masih kosong</li>';
                ErrCount++;
            }
            if (blok=='' || blok==null) {
                ErrMsg += '<li>Blok masih kosong</li>';
                ErrCount++;
            }
            if (ErrCount > 0)
            {
                ErrMsg += '</ul>';
                var btn_close = "<button type='button' class='btn btn-info' data-dismiss='modal'>Close</button>";
                $('#modal-body').html(ErrMsg);
                $('#modal-footer-desc').html(btn_close);
                $('#modal-notify').modal('show');
            }
            else
            {
                $('#load_data').html('<tr><td colspan="12" align="center">Mohon Tunggu...</td></tr>');
                load_table(kawasan, blok, periode_tagihan);
                /*$.ajax({
                    url: "<?=site_url('report/target-realisasi/generate');?>",
                    cache: false,
                    type: "POST",
                    data: {
                        kawasan: kawasan,
                        blok: blok,
                        periode_tagihan: periode_tagihan,
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#load_data').load(data.link_data);
                    },
                });*/
            }
        });

        $(document).on('click', '#export-excel', function(e){
            e.preventDefault();

            kawasan = $("#kawasan").val();
            blok = $("#blok").val();
            periode_tagihan  = $("#periode-tagihan").val();
            ErrCount = 0;
            ErrMsg = '<ul>';

            if (kawasan=='' || kawasan==null) {
                ErrMsg += '<li>Kawasan masih kosong</li>';
                ErrCount++;
            }
            if (blok=='' || blok==null) {
                ErrMsg += '<li>Blok masih kosong</li>';
                ErrCount++;
            }
            if (ErrCount > 0)
            {
                ErrMsg += '</ul>';
                var btn_close = "<button type='button' class='btn btn-info' data-dismiss='modal'>Close</button>";
                $('#modal-body').html(ErrMsg);
                $('#modal-footer-desc').html(btn_close);
                $('#modal-notify').modal('show');
            }
            else
            {
                variable = '?kawasan='+kawasan+'&blok='+blok+'&periode_tagihan='+periode_tagihan;
                window.open("<?=site_url('report/target-realisasi/export-excel/');?>"+variable);
            }
        });
    });

    function load_table(kawasan=null, blok=null, periode_tagihan=null)
    {
        dataTable = $('#tb-target-realisasi').DataTable({
            "serverSide": true,
            "stateSave": false,
            "bAutoWidth": true,
            "bDestroy" : true,
            "oLanguage": {
                "sSearch": "Search : ",
                "sLengthMenu": "_MENU_ ",
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
            "aaSorting": [
                [0, "asc"],
                [1, "asc"]
            ],
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false
            }],
            "sPaginationType": "simple_numbers",
            "iDisplayLength": 10,
            "aLengthMenu": [
                [10, 20, 50, 100, 150, 200], [10, 20, 50, 100, 150, 200]
            ],
            "ajax": {
                url: "<?= site_url('report/target-realisasi/request_data_json'); ?>",
                type: "get",
                data: {
                    kawasan: kawasan,
                    blok: blok,
                    periode_tagihan: periode_tagihan,
                },
                cache: false,
                error: function() {
                    $(".tb-meterair-error").html("");
                    $("#tb-meterair").append('<tbody class="my-grid-error"><tr><th colspan="9"><center>No data found in the server</center></th></tr></tbody>');
                    $("#tb-meterair_processing").css("display", "none");
                }
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
    }
</script>
<style>
    #load_data td:nth-child(1) { white-space: nowrap; }
</style>