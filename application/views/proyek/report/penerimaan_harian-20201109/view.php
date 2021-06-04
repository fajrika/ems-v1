
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
    .table th{ border: 1px solid #556c81 !important; }
    a.disabled { pointer-events: none; cursor: default; }
    .has-error { border: 1px solid rgb(185, 74, 72) !important; }
    div.dataTables_wrapper { width: 99%; height: 100%; margin: 0 auto; }
    .select2-container--default .select2-selection--single {min-height: 34px;}
    .table>thead>tr>th { border-bottom: 1px solid #556c81 !important; padding: 6px; }
    .table>tbody>tr>td { border: 1px solid #ddd; }
    .DTFC_LeftBodyWrapper>.DTFC_LeftBodyLiner>.table { margin-top: -2.2px !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { padding-top: 2px; font-size: 12px; }
    .form-control, .select2-container--default .select2-selection--single { font-size: 12px; box-shadow: none; border-radius: 0px !important; }
    .popover { color: #7a7777; }
</style>
<div style="float:right">
    <!-- <h2>
        <button class="btn btn-warning" onClick="window.location.href='<?=site_url();?>'"> <i class="fa fa-arrow-left"></i> Back</button>
        <button class="btn btn-success" onClick="window.location.href=''"><i class="fa fa-repeat"></i> Refresh</button>
    </h2> -->
</div>
<div class="clearfix"></div>
</div>
<div class="x_conte nt">
    <!-- <div class="col-md-8" style="margin-top:20px"> -->
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
                <label>Start Date (Tgl. Bayar)</label>
                <div class='input-group date datetimepicker_month'>
                    <input type="text" id="periode-awal" class="form-control datetimepicker_month" placeholder="Periode Awal" value="<?=date('Y-m-d');?>">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
            <div class="form-group row col-md-3">
                <label>End Date (Tgl. Bayar)</label>
                <div class='input-group date datetimepicker_month'>
                    <input type="text" id="periode-akhir" class="form-control datetimepicker_month" placeholder="Periode Akhir" value="<?=date('Y-m-d');?>">
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
    <!-- </div> -->
    <br>
    <div class="clearfix"></div>
    <br>
    <br>
    <div class="table-responsive">
    <div class="col-lg-12 col-md-12 col-sm-12" style="margin-top:20px">
        <table class="table table-striped jambo_table" id="tb-penerimaan" style="width:100%">
            <thead>
                <tr>
                    <th colspan="12" style="text-align: center;">Penerimaan Harian</th>
                </tr>
                <tr>
                    <th rowspan="3" style="vertical-align: middle;">Unit</th>
                    <th rowspan="3" style="vertical-align: middle;">Tgl. Bayar</th>
                    <th rowspan="3" style="vertical-align: middle;">Service</th>
                    <th rowspan="3" style="vertical-align: middle;">Periode</th>
                    <th colspan="8" style="text-align: center;">Nilai (Rp.)</th>
                </tr>
                <tr>
                    <th rowspan="2" style="vertical-align: middle;">Pokok</th>
                    <th rowspan="2" style="vertical-align: middle;">PPN</th>
                    <th style="text-align: center;">Tagihan</th>
                    <th rowspan="2" style="vertical-align: middle;">Denda</th>
                    <th style="text-align: center;">Total</th>
                    <th rowspan="2" style="vertical-align: middle;">Diskon</th>
                    <th rowspan="2" style="vertical-align: middle;">Pemutihan</th>
                    <th rowspan="2" style="vertical-align: middle; text-align: center;">Bayar</th>
                </tr>
                <tr>
                    <th style="text-align: center;">Pokok + PPN</th>
                    <th style="text-align: center;" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Pokok + PPN">Tagihan + Denda</th>
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
        // $('[data-toggle="popover"]').popover();
        $('.datetimepicker_month').datetimepicker({
            viewMode: 'years',
            format: 'YYYY-MM-DD'
        });

        // $('#tb-penerimaan').DataTable();

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
            periode_awal  = $("#periode-awal").val();
            periode_akhir = $("#periode-akhir").val();
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
                i = 0;
                $.ajax({
                    url: "<?=site_url('report/penerimaan-harian/generate');?>",
                    cache: false,
                    type: "POST",
                    data: {
                        kawasan: kawasan,
                        blok: blok,
                        periode_awal: periode_awal,
                        periode_akhir: periode_akhir,
                    },
                    dataType: "json",
                    beforeSend: function() {
                        $('#loading').show();
                    },
                    complete: function(){
                        $('#loading').hide();
                    },
                    success: function(data) {
                        i++;
                        $('#load_data').load(data.link_data);
                    },
                });
            }
        });

        $(document).on('click', '#export-excel', function(e){
            e.preventDefault();

            kawasan = $("#kawasan").val();
            blok = $("#blok").val();
            periode_awal  = $("#periode-awal").val();
            periode_akhir = $("#periode-akhir").val();
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
                variable = '?id_kawasan='+kawasan+'&id_blok='+blok+'&periode_awal='+periode_awal+'&periode_akhir='+periode_akhir;
                window.open("<?=site_url('report/penerimaan-harian/export-excel/');?>"+variable);
            }
        });
    });
</script>