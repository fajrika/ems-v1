<?=$load_css;?>
<?=$load_js;?>
<div style="float:right">
    <!-- <h2>
        <button class="btn btn-warning" onClick="window.location.href='<?=site_url();?>'"> <i class="fa fa-arrow-left"></i> Back</button>
        <button class="btn btn-success" onClick="window.location.href=''"><i class="fa fa-repeat"></i> Refresh</button>
    </h2> -->
</div>
<div class="clearfix"></div>
</div>
<div class="x_conte nt">
    <form id="form" class="form-horizontal form-label-left" method="post" action="#" novalidate>
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="form-group">
                <label class="label-align col-lg-5 col-md-5 col-sm-12 col-xs-12">Kawasan <span class="required">*</span></label>
                <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
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
            </div>
            <div class="form-group">
                <label class="label-align col-lg-5 col-md-5 col-sm-12 col-xs-12">Blok <span class="required">*</span></label>
                <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
                    <select name="blok" required="" id="blok" class="form-control select2" placeholder="-- Pilih Kawasan Dahulu --" disabled>
                        <option value="" disabled selected>-- Pilih Kawasan Dahulu --</option>
                        <option value="all">Semua Blok</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="label-align col-lg-5 col-md-5 col-sm-12 col-xs-12">Jenis Service <span class="required">*</span></label>
                <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
                    <select name="jenis_service" required="" id="jenis_service" class="form-control select2" placeholder="-- Pilih Blok Dahulu --">
                        <option value="" disabled selected>-- Pilih Jenis Service --</option>
                        <option value="all">Semua Jenis Service</option>
                        <?php
                        foreach ($jenis as $v) {
                            echo ("<option value='$v->id'>$v->name</option>");
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="form-group">
                <label class="label-align col-lg-5 col-md-5 col-sm-12 col-xs-12">Periode</label>
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class='input-group'>
                        <input name="periode_awal" type="text" class="form-control datetimepicker_month" placeholder="Lama Berlakunya Awal" value="<?=date('Y-m');?>">
                        <span class="fa fa-calendar form-control-feedback right" aria-hidden="true"></span>
                    </div>
                </div>
                <label class="label-align col-lg-1 col-md-1 col-sm-1" style="text-align:center">-</label>
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class='input-group'>
                        <input name="periode_akhir" type="text" class="form-control datetimepicker_month" placeholder="Lama Berlakunya Akhir" value="<?=date('Y-m');?>">
                        <span class="fa fa-calendar form-control-feedback right" aria-hidden="true"></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="label-align col-lg-5 col-md-5 col-sm-12 col-xs-12">Status Tagihan</label>
                <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
                    <select name="status_tagihan" required="" id="status-tagihan" class="form-control select2" placeholder="-- Pilih Status Tagihan --">
                        <option value="all" selected>Semua Status</option>
                        <option value="1">Terbayar</option>
                        <option value="4">Belum Lunas</option>
                        <option value="0">Belum Terbayar</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="clear-fix"></div>
        <style type="text/css">
            .styled-select select {
            -moz-appearance:none; /* Firefox */
            -webkit-appearance:none; /* Safari and Chrome */
            appearance:none;
        }
        </style>
        <div class="col-md-12 col-xs-12">
            <div class="center-margin">
                <br/>
                <button type="button" id="btn-load-unit" class="btn btn-primary btn-action-form"><i class="fa fa-repeat"></i> Generate</button>
                <button type="button" id="export-excel" class="btn btn-success btn-action-form"><i class="fa fa-file-excel-o"></i> Export</button>
            </div>
        </div>
    </form>
    <div class="clearfix"></div>
    <br>
    <br>
    <div class="table-responsive">
        <div class="col-lg-12 col-md-12 col-sm-12" style="margin-top:20px">
            <table class="table table-striped jambo_table" id="tb-status-tagihan" style="width:100%">
                <thead>
                    <tr>
                        <th colspan="14" style="text-align: center;">Status Tagihan</th>
                    </tr>
                    <tr>
                        <th>Kawasan</th>
                        <th>Blok</th>
                        <th>Unit</th>
                        <th>Periode</th>
                        <th>Jenis Service</th>
                        <th>Luas Tanah</th>
                        <th>Luas Bangunan</th>
                        <th>Nama Pemilik</th>
                        <th>Alamat</th>
                        <th>Tlp. 1</th>
                        <th>Tlp. 2</th>
                        <th>Handphone</th>
                        <th>Status Huni</th>
                        <th>Status Bayar</th>
                    </tr>
                </thead>
                <tbody></tbody>
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

        var table_generate = $('#tb-status-tagihan').DataTable({
                                "processing": true,
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
        $(document).on('click', '#btn-load-unit', function(e){
            e.preventDefault();
            kawasan = $('#form select[name="kawasan"]').val();
            blok = $('#form select[name="blok"]').val();
            jenis_service = $('#form select[name="jenis_service"]').val();
            periode_awal = $('#form input[name="periode_awal"]').val();
            periode_akhir = $('#form input[name="periode_akhir"]').val();
            status_tagihan = $('#form select[name="status_tagihan"]').val();
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
            if (jenis_service=='' || jenis_service==null) {
                ErrMsg += '<li>Jenis service masih kosong</li>';
                ErrCount++;
            }
            if ((periode_awal!='' && periode_awal!=null) && (periode_akhir!='' && periode_akhir!=null))
            {
                if ((periode_awal+'-01')>periode_akhir+'-01')
                {
                    ErrMsg += '<li>Periode Tagihan Awal <b>Lebih Baru Daripada</b> Periode Tagihan Akhir</li>';
                    ErrCount++;
                }
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
                table_generate.clear().draw();
                $.ajax({
                    url: "<?=site_url('report/status-tagihan/get_data_ajax');?>",
                    cache: false,
                    type: "POST",
                    data: {
                        kawasan: kawasan,
                        blok: blok,
                        jenis_service: jenis_service,
                        periode_awal: periode_awal,
                        periode_akhir: periode_akhir,
                        status_tagihan: status_tagihan,
                    },
                    dataType: "json",
                    // beforeSend: function() {
                    //     $('#loading').show();
                    // },
                    // complete: function(){
                    //     $('#loading').hide();
                    // },
                    success: function(data) {
                        for(var i=0; i<data.table.length; i++)
                        {
                            table_generate.row.add( [
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
                                data.table[i][11],
                                (data.table[i][12]),
                                (data.table[i][13])
                            ] ).draw( false );
                        }
                    },
                });
            }
        });

        $(document).on('click', '#export-excel', function(e){
            e.preventDefault();

            kawasan = $('#form select[name="kawasan"]').val();
            blok = $('#form select[name="blok"]').val();
            jenis_service = $('#form select[name="jenis_service"]').val();
            periode_awal = $('#form input[name="periode_awal"]').val();
            periode_akhir = $('#form input[name="periode_akhir"]').val();
            status_tagihan = $('#form select[name="status_tagihan"]').val();
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
            if (jenis_service=='' || jenis_service==null) {
                ErrMsg += '<li>Jenis service masih kosong</li>';
                ErrCount++;
            }
            if ((periode_awal!='' && periode_awal!=null) && (periode_akhir!='' && periode_akhir!=null))
            {
                if ((periode_awal+'-01')>periode_akhir+'-01')
                {
                    ErrMsg += '<li>Periode Tagihan Awal <b>Lebih Baru Daripada</b> Periode Tagihan Akhir</li>';
                    ErrCount++;
                }
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
                variable = '?kawasan='+kawasan+'&blok='+blok+'&jenis_service='+jenis_service+'&periode_awal='+periode_awal+'&periode_akhir='+periode_akhir+'&status_tagihan='+status_tagihan;
                window.open("<?=site_url('report/status-tagihan/export-excel/');?>"+variable);
            }
        });
    });
</script>