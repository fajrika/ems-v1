
<link href="<?= base_url('vendors/select2/dist/css/select2.min.css');?>" rel="stylesheet">
<link href="<?= base_url('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css');?>" rel="stylesheet">
<script src="<?=base_url('vendors/select2/dist/js/select2.min.js'); ?>"></script>
<script src="<?=base_url('vendors/moment/min/moment.min.js');?>"></script>
<script src="<?=base_url('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js');?>"></script>
<link href="<?= base_url(); ?>vendors/switchery/dist/switchery.min.css" rel="stylesheet">
<script src="<?= base_url(); ?>vendors/switchery/dist/switchery.min.js"></script>
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
    #load_data tr td:nth-child(1) { white-space: nowrap; }
    #load_data_summary tr td:nth-child(1) { white-space: nowrap; }
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

        <form action="" class="form-horizontal form-label-left">
            <div class="form-group row col-md-3">
                
            </div>
            <div class="form-group row col-md-3">
                
            </div>
            <div class="form-group row col-md-2">
                
            </div>
            <div class="form-group row col-md-2">
                
            </div>
            <div class="form-group row col-md-2">
                
            </div>
            <div class="col-md-8 center-margin">
            </div>
        </form>

    <br>

    <form action="" id="form-meterair" class="form-horizontal form-label-left" method="post" novalidate>
        <div class="field item form-group">
            <label class="control-label col-md-3 col-xs-12">PT</label>
            <div class="col-md-6 col-xs-12">
                <select class="form-control select2" name="pt_id" id="pt_id" placeholder="-- Pilih PT --" required="" style="width: 100%;">
                    <?php
                    if($is_pt->num_rows() > 0){
                        foreach($is_pt->result() as $p){
                            echo "<option value='".$p->pt_id."'>".$p->name."</option>";
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-3 col-xs-12">Unit Virtual</label>
            <div class="col-md-6 col-xs-12">
                <input type="checkbox" id="unit_virtual" name="unit_virtual" class="js-switch form-control" value="1" /> Yes
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-3 col-xs-12">Kawasan / Blok</label>
            <div class="col-md-3 col-xs-12">
                <select name="kawasan" required="" id="kawasan" class="form-control select2" placeholder="-- Pilih Kawasan --" style="width: 100%;">
                    <option value="" disabled selected>-- Pilih Kawasan --</option>
                    <option value="all">Semua Kawasan</option>
                    <?php
                    foreach ($kawasan as $v) {
                        echo ("<option value='$v->id'>$v->code - $v->name</option>");
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3 col-xs-12">
                <select name="blok" required="" id="blok" class="form-control select2" placeholder="-- Pilih Blok --" disabled style="width: 100%;">
                    <option value="" disabled selected>-- Pilih Blok --</option>
                    <option value="all">-- Semua Blok --</option>
                </select>
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-3 col-xs-12">Service</label>
            <div class="col-md-6 col-xs-12">
                <select name="jenis_service" id="jenis_service" class="form-control select2" placeholder="-- Pilih Service --" style="width: 100%;">
                    <option value="all">-- Semua Service --</option>
                    <?php
                    if ($is_service->num_rows() > 0) {
                        foreach ($is_service->result() as $d) {
                            echo "<option value='".$d->service_jenis_id."'>".$d->name."</option>";
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-3 col-xs-12">Cara Bayar</label>
            <div class="col-md-6 col-xs-12">
                <select name="cara_bayar" id="cara_bayar" class="form-control select2" placeholder="-- Pilih Cara Bayar --" style="width: 100%;">
                    <option value="all">-- Semua Cara Bayar --</option>
                    <?php
                        foreach ($cara_bayar as $d) {
                            echo "<option value='".$d->id."'>".$d->cara.($d->bank_name?(" - ".$d->bank_name):'')."</option>";
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-3 col-xs-12">Tgl.Bayar Awal/Akhir</label>
            <div class="col-md-3">
                <div class='input-group'>
                    <input type="text" id="periode-awal" class="form-control" placeholder="Periode Awal" value="<?=date('Y-m-d');?>">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
            <div class="col-md-3">
                <div class='input-group'>
                    <input type="text" id="periode-akhir" class="form-control" placeholder="Periode Akhir" value="<?=date('Y-m-d');?>">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-2 col-xs-12">&nbsp;</label>
            <div class="col-md-2 col-xs-12">
                <button type="button" id="btn-load-unit" class="btn btn-primary btn-block"><i class="fa fa-repeat"></i> Generate</button>
            </div>
            <div class="col-md-2 col-xs-12">
                <button type="button" id="export-excel" class="btn btn-success btn-block"><i class="fa fa-file-excel-o"></i>&nbsp; Export</button>
            </div>
            <div class="col-md-2 col-xs-12">
                <button type="button" id="btn-load-unit-summary" class="btn btn-primary btn-block"><i class="fa fa-repeat"></i>&nbsp; Summary Generate</button>
            </div>
            <div class="col-md-2 col-xs-12">
                <button type="button" id="print-data" class="btn btn-success btn-block"><i class="fa fa-file-excel-o"></i>&nbsp; Print</button>
            </div>
        </div>
    </form>
    <div class="clearfix"></div>
    <br>
    <br>
    <div class="table-responsive">
        <div id="area-tb-penerimaan" class="col-lg-12 col-md-12 col-sm-12" style="margin-top:20px">
            <table class="table table-striped jambo_table" id="tb-penerimaan" style="width:100%">
                <thead>
                    <tr>
                        <th colspan="17" style="text-align: center;">Penerimaan Harian</th>
                    </tr>
                    <tr>
                        <th rowspan="3" style="vertical-align: middle;">Unit</th>
                        <th rowspan="3" style="vertical-align: middle;">Nama Pemilik</th>
                        <th rowspan="3" style="vertical-align: middle;">Tgl. Bayar</th>
                        <th rowspan="3" style="vertical-align: middle;">Jam Bayar</th>
                        <th rowspan="3" style="vertical-align: middle;">Service</th>
                        <th rowspan="3" style="vertical-align: middle; text-align: center;">Periode<br>Penggunaan</th>
                        <th rowspan="3" style="vertical-align: middle; text-align: center;">Periode<br>Penagihan</th>
                        <th colspan="8" style="text-align: center;">Nilai (Rp.)</th>
                        <th rowspan="3" style="text-align: center;">No Kwitansi</th>
                        <th rowspan="3" style="text-align: center;">No Va</th>
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
                <tbody id="load_data"><tr><td colspan="17" align="center">No data available in table</td></tr></tbody>
            </table>
        </div>
        <div id="area-tb-penerimaan-summary" class="col-lg-12 col-md-12 col-sm-12" style="margin-top:20px; display: none;">
            <table class="table table-striped jambo_table" id="tb-penerimaan-summary" style="width:100%;">
                <thead>
                    <tr>
                        <th colspan="12" style="text-align: center;">Penerimaan Harian Summary</th>
                    </tr>
                    <tr>
                        <th rowspan="3" style="vertical-align: bottom;">Kode</th>
                        <th rowspan="3" style="vertical-align: bottom;">Kawasan</th>
                        <th rowspan="3" style="vertical-align: bottom;">Service</th>
                        <th colspan="9" style="text-align: center;">Nilai (Rp.)</th>
                    </tr>
                    <tr>
                        <th rowspan="2" style="vertical-align: bottom;">Pokok</th>
                        <th rowspan="2" style="vertical-align: bottom;">PPN</th>
                        <th style="text-align: center;">Tagihan</th>
                        <th rowspan="2" style="vertical-align: bottom;">Denda</th>
                        <th rowspan="2" style="vertical-align: bottom;">Biaya Admin</th>
                        <th style="text-align: center;">Total</th>
                        <th rowspan="2" style="vertical-align: bottom;">Diskon</th>
                        <th rowspan="2" style="vertical-align: bottom;">Pemutihan</th>
                        <th rowspan="2" style="vertical-align: bottom; text-align: center;">Bayar</th>
                    </tr>
                    <tr>
                        <th style="text-align: center;">Pokok + PPN</th>
                        <th style="text-align: center;" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Pokok + PPN">Tagihan + Denda + Biaya Admin</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th colspan="3"> Grand Total</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
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

        // if unit virtual
        $("#unit_virtual").change(function() {
            if ($("#unit_virtual").is(':checked')) {
                $("#kawasan").attr('disabled', true);
                $('select[name="jenis_service"]').first().val("6").trigger('change');
                $('select[name="jenis_service"]').first().val("6").attr('disabled', true);
            } else {
                $("#kawasan").attr('disabled', false);
                $('select[name="jenis_service"]').first().val("all").trigger('change').attr('disabled', false);;
            }
        })

        $('#periode-awal').datetimepicker({
            viewMode: 'years',
            format: 'YYYY-MM-DD'
        });
        $('#periode-akhir').datetimepicker({
            viewMode: 'years',
            format: 'YYYY-MM-DD'
        });

        $(document).on('input', '#periode-awal', function(e){
            check_date($('#periode-awal').val(),'month');
        });

        var d,d2,month,day,year;
        function format_date()
        {
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

            if (month.length < 2) 
                month = '0' + month;
            if (day.length < 2) 
                day = '0' + day;

            return [year, month, day].join('-');
        }
        function check_date(date,type) {
            d = new Date(date);
            d1 = new Date($('#periode-akhir').val());
            d_cek = new Date(date);

            if(d1>d)
            {
                if (type == 'date')
                {
                    d_cek.setDate(d_cek.getDate() + 7);
                    if (d_cek<d1)
                    {
                        d.setDate(d.getDate() + 7);
                    }
                    else
                    {
                        d = d1;
                    }
                }
                else
                {
                    d_cek.setMonth(d_cek.getMonth() + 1);
                    if (d_cek<d1)
                    {
                        d.setMonth(d.getMonth() + 1);
                    }
                    else
                    {
                        d = d1;
                    }
                }
            }

            $('#periode-akhir').val(format_date());
        }

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

            if (kawasan == 'all') {
                check_date(periode_awal, 'date');
            } else {
                check_date(periode_awal, 'month');
            }

            periode_akhir = $("#periode-akhir").val();
            jenis_service = $("#jenis_service").val();
            cara_bayar = $("#cara_bayar").val();
            pt_id = $("#pt_id").val();
            unit_virtual = $("#unit_virtual").is(':checked') ? 1 : 0;

            ErrCount = 0;
            ErrMsg = '<ul>';

            if (!unit_virtual) 
            {
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
            }
           
            if (ErrCount < 1)
            {
                $('#area-tb-penerimaan').show();
                $('#area-tb-penerimaan-summary').hide();
                $('#load_data').html('<tr><td colspan="16" align="center">Mohon Tunggu...</td></tr>');
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
                        jenis_service: jenis_service,
                        cara_bayar: cara_bayar,
                        pt_id: pt_id,
                        unit_virtual: unit_virtual,
                    },
                    dataType: "json",
                    // beforeSend: function() {
                    //     $('#loading').show();
                    // },
                    // complete: function(){
                    //     $('#loading').hide();
                    // },
                    success: function(data) {
                        i++;
                        print_content = '\
                            <table class="table table-striped jambo_table" id="tb-penerimaan" style="width:100%" border="1">\
                                <thead>\
                                    <tr>\
                                        <th colspan="17" style="text-align: center;">Penerimaan Harian</th>\
                                    </tr>\
                                    <tr>\
                                        <th rowspan="3" style="vertical-align: middle;">Unit</th>\
                                        <th rowspan="3" style="vertical-align: middle;">Nama Pemilik</th>\
                                        <th rowspan="3" style="vertical-align: middle;">Tgl. Bayar</th>\
                                        <th rowspan="3" style="vertical-align: middle;">Jam Bayar</th>\
                                        <th rowspan="3" style="vertical-align: middle;">Service</th>\
                                        <th rowspan="3" style="vertical-align: middle; text-align: center;">Periode<br>Penggunaan</th>\
                                        <th rowspan="3" style="vertical-align: middle; text-align: center;">Periode<br>Penagihan</th>\
                                        <th colspan="8" style="text-align: center;">Nilai (Rp.)</th>\
                                        <th rowspan="3" style="text-align: center;">No Kwitansi</th>\
                                        <th rowspan="3" style="text-align: center;">No Va</th>\
                                    </tr>\
                                    <tr>\
                                        <th rowspan="2" style="vertical-align: middle;">Pokok</th>\
                                        <th rowspan="2" style="vertical-align: middle;">PPN</th>\
                                        <th style="text-align: center;">Tagihan</th>\
                                        <th rowspan="2" style="vertical-align: middle;">Denda</th>\
                                        <th style="text-align: center;">Total</th>\
                                        <th rowspan="2" style="vertical-align: middle;">Diskon</th>\
                                        <th rowspan="2" style="vertical-align: middle;">Pemutihan</th>\
                                        <th rowspan="2" style="vertical-align: middle; text-align: center;">Bayar</th>\
                                    </tr>\
                                    <tr>\
                                        <th style="text-align: center;">Pokok + PPN</th>\
                                        <th style="text-align: center;" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Pokok + PPN">Tagihan + Denda</th>\
                                    </tr>\
                                </thead>\
                                <tbody id="load_data"><tr><td colspan="17" align="center">\
                        ';
                        $('#load_data').html(data.content_body);
                        
                        print_content += data.content_body+'</tbody></table>';
                    },
                });
            }
        });

        var table_generate_summary = $('#tb-penerimaan-summary').DataTable({
            "preDraw": function( settings ) { 
                $(".new_preloader").show(); 
            }, 
            "drawCallback": function( settings ) { 
                $(".new_preloader").fadeOut(); 
            },
            "initComplete": function () {
                // Apply the search
                // this.api().columns().every( function () {
                //     var that = this;
                //     $( 'input', this.footer() ).on( 'keyup change clear', function () {
                //         if ( that.search() !== this.value ) {
                //             that
                //                 .search( this.value )
                //                 .draw();
                //         }
                //     } );
                // } );
            },
            "scrollX": true
        });
        $(document).on('click', '#btn-load-unit-summary', function(e){
            e.preventDefault();
            kawasan = $("#kawasan").val();
            blok = $("#blok").val();

            periode_awal  = $("#periode-awal").val();
            check_date(periode_awal,'month');

            periode_akhir = $("#periode-akhir").val();
            jenis_service = $("#jenis_service").val();
            cara_bayar = $("#cara_bayar").val();
            pt_id = $("#pt_id").val();
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
                $('#area-tb-penerimaan-summary').show();
                $('#area-tb-penerimaan').hide();
                table_generate_summary.clear().draw();
                $.ajax({
                    url: "<?=site_url('report/penerimaan-harian/get_data_summary_ajax');?>",
                    cache: false,
                    type: "POST",
                    data: {
                        kawasan: kawasan,
                        blok: blok,
                        periode_awal: periode_awal,
                        periode_akhir: periode_akhir,
                        jenis_service: jenis_service,
                        cara_bayar: cara_bayar,
                        pt_id: pt_id,
                    },
                    dataType: "json",
                    // beforeSend: function() {
                    //     $('#loading').show();
                    // },
                    // complete: function(){
                    //     $('#loading').hide();
                    // },
                    success: function(data) {
                        // $('#load_data_summary').html(data.status);
                        print_content = '\
                            <table class="table table-striped jambo_table" id="tb-penerimaan-summary" style="width:100%;" border="1">\
                                <thead>\
                                    <tr>\
                                        <th colspan="12" style="text-align: center;">Penerimaan Harian Summary</th>\
                                    </tr>\
                                    <tr>\
                                        <th rowspan="3" style="vertical-align: bottom;">Kode</th>\
                                        <th rowspan="3" style="vertical-align: bottom;">Kawasan</th>\
                                        <th rowspan="3" style="vertical-align: bottom;">Service</th>\
                                        <th colspan="9" style="text-align: center;">Nilai (Rp.)</th>\
                                    </tr>\
                                    <tr>\
                                        <th rowspan="2" style="vertical-align: bottom;">Pokok</th>\
                                        <th rowspan="2" style="vertical-align: bottom;">PPN</th>\
                                        <th style="text-align: center;">Tagihan</th>\
                                        <th rowspan="2" style="vertical-align: bottom;">Denda</th>\
                                        <th rowspan="2" style="vertical-align: bottom;">Biaya Admin</th>\
                                        <th style="text-align: center;">Total</th>\
                                        <th rowspan="2" style="vertical-align: bottom;">Diskon</th>\
                                        <th rowspan="2" style="vertical-align: bottom;">Pemutihan</th>\
                                        <th rowspan="2" style="vertical-align: bottom; text-align: center;">Bayar</th>\
                                    </tr>\
                                    <tr>\
                                        <th style="text-align: center;">Pokok + PPN</th>\
                                        <th style="text-align: center;" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Pokok + PPN">\Tagihan + Denda + Biaya Admin</th>\
                                    </tr>\
                                </thead>\
                                <tbody>\
                        ';
                        for(var i=0; i<data.table.length; i++)
                        {
                            table_generate_summary.row.add( [
                                data.table[i][0],
                                data.table[i][1],
                                data.table[i][2],
                                data.table[i][3],
                                data.table[i][4],
                                data.table[i][5],
                                data.table[i][6],
                                data.table[i][7],
                                data.table[i][8],
                                data.table[i][9],
                                data.table[i][10],
                                data.table[i][11]
                            ] ).draw( false );

                            print_content += '\
                                <tr>\
                                    <td>'+data.table[i][0]+'</td>\
                                    <td>'+data.table[i][1]+'</td>\
                                    <td>'+data.table[i][2]+'</td>\
                                    <td>'+data.table[i][3]+'</td>\
                                    <td>'+data.table[i][4]+'</td>\
                                    <td>'+data.table[i][5]+'</td>\
                                    <td>'+data.table[i][6]+'</td>\
                                    <td>'+data.table[i][7]+'</td>\
                                    <td>'+data.table[i][8]+'</td>\
                                    <td>'+data.table[i][9]+'</td>\
                                    <td>'+data.table[i][10]+'</td>\
                                    <td>'+data.table[i][11]+'</td>\
                                </tr>\
                            ';
                        }

                        $( table_generate_summary.column( 3 ).footer() ).text( format_nominal(data.gt_pokok) );
                        $( table_generate_summary.column( 4 ).footer() ).text( format_nominal(data.gt_ppn) );
                        $( table_generate_summary.column( 5 ).footer() ).text( format_nominal(data.gt_pokok + data.gt_ppn) );
                        $( table_generate_summary.column( 6 ).footer() ).text( format_nominal(data.gt_denda) );
                        $( table_generate_summary.column( 7 ).footer() ).text( format_nominal(data.gt_biaya_admin) );
                        $( table_generate_summary.column( 8 ).footer() ).text( format_nominal(data.gt_pokok + data.gt_ppn + data.gt_denda + data.gt_biaya_admin) );
                        $( table_generate_summary.column( 9 ).footer() ).text( format_nominal(data.gt_diskon) );
                        $( table_generate_summary.column( 10 ).footer() ).text( format_nominal(data.gt_pemutihan) );
                        $( table_generate_summary.column( 11 ).footer() ).text( format_nominal(data.gt_bayar) );
                        
                        print_content += '\
                            <tr>\
                                <td colspan="3">Grand Total</td>\
                                <td>'+format_nominal(data.gt_pokok)+'</td>\
                                <td>'+format_nominal(data.gt_ppn)+'</td>\
                                <td>'+format_nominal(data.gt_pokok + data.gt_ppn)+'</td>\
                                <td>'+format_nominal(data.gt_denda)+'</td>\
                                <td>'+format_nominal(data.gt_biaya_admin)+'</td>\
                                <td>'+format_nominal(data.gt_pokok + data.gt_ppn + data.gt_denda + data.gt_biaya_admin)+'</td>\
                                <td>'+format_nominal(data.gt_diskon)+'</td>\
                                <td>'+format_nominal(data.gt_pemutihan)+'</td>\
                                <td>'+format_nominal(data.gt_bayar)+'</td>\
                            </tr>\
                        ';
                        print_content += '</tbody>\
                            </table>\
                        ';
                    },
                });
            }
        });
        function format_nominal(nominal="")
        {
            if (nominal!="")
            {
                var number_string = nominal.toString(),
                    sisa    = number_string.length % 3,
                    rupiah  = number_string.substr(0, sisa),
                    ribuan  = number_string.substr(sisa).match(/\d{3}/g);
                        
                    separator = sisa ? '.' : '';
                if (ribuan) {
                    rupiah += separator + ribuan.join('.');
                }
                nominal = rupiah;
            }
            return nominal;
        }

        $(document).on('click', '#export-excel', function(e){
            e.preventDefault();

            kawasan = $("#kawasan").val();
            blok = $("#blok").val();
            periode_awal  = $("#periode-awal").val();
            periode_akhir = $("#periode-akhir").val();
            jenis_service = $("#jenis_service").val();
            cara_bayar = $("#cara_bayar").val();
            pt_id = $("#pt_id").val();
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
                variable = '?id_kawasan='+kawasan+'&id_blok='+blok+'&periode_awal='+periode_awal+'&periode_akhir='+periode_akhir+'&jenis_service='+jenis_service+'&cara_bayar='+cara_bayar+'&pt_id='+pt_id;
                if ($('#area-tb-penerimaan-summary').is(":hidden"))
                {
                    window.open("<?=site_url('report/penerimaan-harian/export-excel/');?>"+variable);
                }
                else
                {
                    window.open("<?=site_url('report/penerimaan-harian/export-excel-summary/');?>"+variable);
                }
            }
        });

        $(document).on('click', '#print-data', function(e){
            if (kawasan=='' || kawasan==null)
            {
                notif("Data inputan belum lengkap!", "Kawasan masih kosong", 'warning');
                $("#kawasan").select2('open');
                return false;
            }
            if (blok=='' || blok==null) {
                notif("Data inputan belum lengkap!", "Blok masih kosong", 'warning');
                $("#blok").select2('open');
                return false;
            }
            
            // variable = '?kawasan='+kawasan+'&blok='+blok+'&periode_awal='+periode_awal+'&periode_akhir='+periode_akhir+'&jenis_service='+jenis_service+'&cara_bayar='+cara_bayar+'&pt_id='+pt_id;
            var winPrint = window.open('', '', 'left=0,top=0,width=800,height=600,toolbar=0,scrollbars=0,status=0');
            winPrint.document.write(print_content);
            winPrint.document.close();
            winPrint.focus();
            winPrint.print();
            winPrint.close(); 
        });
    });
</script>