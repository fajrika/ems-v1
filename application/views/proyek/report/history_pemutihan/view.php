<?=$load_css;?>
<?=$load_js;?>
<div class="clearfix"></div>
</div>
<div class="x_conte nt">
	<form id="form" class="form-horizontal form-label-left" method="post" action="<?=site_url();?>/P_master_isp/save">
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
				<label class="label-align col-lg-3 col-md-3 col-sm-12">Periode Pemutihan</label>
				<div class="col-lg-4 col-md-4 col-sm-5">
					<div class='input-group'>
						<input id="periode-awal" type="text" class="form-control datetimepicker_month" placeholder="Periode Awal" value="">
						<span class="fa fa-calendar form-control-feedback right" aria-hidden="true"></span>
					</div>
				</div>
				<label class="label-align col-lg-1 col-md-1 col-sm-2" style="text-align:center">-</label>
				<div class="col-lg-4 col-md-4 col-sm-5">
					<div class='input-group'>
						<input id="periode-akhir" type="text" class="form-control datetimepicker_month" placeholder="Periode Akhir" value="">
						<span class="fa fa-calendar form-control-feedback right" aria-hidden="true"></span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="label-align col-lg-3 col-md-3 col-sm-12">Lama Berlakunya Pemutihan</label>
				<div class="col-lg-4 col-md-4 col-sm-5">
					<div class='input-group'>
						<input id="ed_pemutihan_awal" type="text" class="form-control datetimepicker_month" placeholder="Lama Berlakunya Awal" value="">
						<span class="fa fa-calendar form-control-feedback right" aria-hidden="true"></span>
					</div>
				</div>
				<label class="label-align col-lg-1 col-md-1 col-sm-2" style="text-align:center">-</label>
				<div class="col-lg-4 col-md-4 col-sm-5">
					<div class='input-group'>
						<input id="ed_pemutihan_akhir" type="text" class="form-control datetimepicker_month" placeholder="Lama Berlakunya Akhir" value="">
						<span class="fa fa-calendar form-control-feedback right" aria-hidden="true"></span>
					</div>
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
				<button id="btn-load-data" class="btn btn-primary btn-action-form" type="button">Load Data</button>
			</div>
		</div>
	</form>
    <div class="clearfix"></div>
    <div class="table-responsive">
	    <div class="col-lg-12 col-md-12 col-sm-12" style="margin-top:20px">
	        <table class="table table-striped jambo_table" id="tb-target-realisasi" style="width:100%">
	            <thead>
	                <tr>
	                    <th>No. Pemutihan</th>
	                    <th>Kawasan</th>
	                    <th>Blok</th>
                        <!-- <th>Jenis Service</th> -->
	                    <th>Nilai</th>
	                    <th>Denda</th>
	                    <th>Total</th>
	                    <th>Jenis</th>
	                    <th>Nilai Pemutihan</th>
	                    <th>Jenis</th>
	                    <th>Nilai Pemutihan Denda</th>
	                    <th>Status</th>
                        <th>Action</th>
	                </tr>
	            </thead>
	        </table>
	    </div>
    </div>
</div>

<!-- modals -->
<div class="x_content">
    <div id="modal" class="modal fade bs-example-modal-lg" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Detail History Pemutihan</h4>
                </div>
                <div class="modal-body">
                    <h2 class="modal-title text-center" style="margin-bottom: 2.5rem;">History Pemutihan</h4>
                    <div class="overflow-y">
                        <table id="tbl_detail_history_pemutihan" class="table table-striped jambo_table">
                            <tfoot style="display: table-header-group">
                                <tr>
                                    <th>Kawasan</th>
                                    <th>Blok</th>
                                    <th>Jenis Pemutihan</th>
                                    <th>Unit</th>
                                    <th>Pemilik</th>
                                    <th>Periode</th>
                                    <th>Nilai Pokok</th>
                                    <th>Denda</th>
                                    <th>Total</th>
                                    <th>Jenis</th>
                                    <th>Nilai Pemutihan Pokok</th>
                                    <th>Jenis</th>
                                    <th>Nilai Pemutihan Denda</th>
                                    <!-- <th>Status Pemutihan</th>
                                    <th>Approver</th>
                                    <th>Keterangan</th> -->
                                </tr>
                            </tfoot>
                            <thead>
                                <tr>
                                    <th>Kawasan</th>
                                    <th>Blok</th>
                                    <th>Jenis Pemutihan</th>
                                    <th>Unit</th>
                                    <th>Pemilik</th>
                                    <th>Periode</th>
                                    <th>Nilai Pokok</th>
                                    <th>Denda</th>
                                    <th>Total</th>
                                    <th>Jenis</th>
                                    <th>Nilai Pemutihan Pokok</th>
                                    <th>Jenis</th>
                                    <th>Nilai Pemutihan Denda</th>
                                    <!-- <th>Status Pemutihan</th>
                                    <th>Approver</th>
                                    <th>Keterangan</th> -->
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <hr/>
                    <!-- <button id="btn-back" class="btn btn-danger right" style="position: absolute; right: 0; margin-right: 2rem; width: 100px;">Back</button>
                    <h2 id="title-switch" class="modal-title text-center" style="margin-bottom: 2.5rem;">Log</h4>
                    <div id="area-log" class="overflow-y">
                        <table id="tbl_log_detail_history_pemutihan" class="table table-striped jambo_table">
                            <tfoot style="display: table-header-group">
                                <tr>
                                    <th>No</th>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th hidden>Detail</th>
                                </tr>
                            </tfoot>
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
                            </tbody>
                        </table>
                    </div>
                    <div id="area-log-detail" class="overflow-y">
                        <table id="tbl_log_detail_detail_history_pemutihan" class="table table-striped jambo_table">
                            <tfoot style="display: table-header-group">
                                <tr>
                                    <th>Point Detail</th>
                                    <th>Before</th>
                                    <th>After</th>
                                </tr>
                            </tfoot>
                            <thead>
                                <tr>
                                    <th>Point Detail</th>
                                    <th>Before</th>
                                    <th>After</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <hr/> -->
                    <h2 class="modal-title text-center" style="margin-bottom: 2.5rem;">Log Approval</h4>
                    <div class="overflow-y">
                        <table id="tbl_log_approval" class="table table-striped jambo_table">
                            <tfoot style="display: table-header-group">
                                <tr>
                                    <th>No.</th>
                                    <th>Approval Status</th>
                                    <th>User</th>
                                    <th>Deskripsi</th>
                                    <th>Approval Date</th>
                                </tr>
                            </tfoot>
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Approval Status</th>
                                    <th>User</th>
                                    <th>Deskripsi</th>
                                    <th>Approval Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
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

        $("#jenis_service").change(function() {
            if ($("#jenis_service").val() == null) {
                $('#jenis_service').next().find('.select2-selection').addClass('has-error');
            } else {
                $('#jenis_service').next().find('.select2-selection').removeClass('has-error');
            }
        });

        var kawasan, blok, jenis_service, periode_awal, periode_akhir, ed_pemutihan_awal, ed_pemutihan_akhir;
        $(document).on('click', '#btn-load-data', function(e){
            e.preventDefault();
            kawasan = $("#kawasan").val();
            blok = $("#blok").val();
            jenis_service = $("#jenis_service").val();
            periode_awal  = $("#periode-awal").val();
            periode_akhir = $("#periode-akhir").val();
            ed_pemutihan_awal  = $("#ed_pemutihan_awal").val();
            ed_pemutihan_akhir = $("#ed_pemutihan_akhir").val();
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
	                ErrMsg += '<li>Periode Pemutihan Awal <b>Lebih Lama Daripada</b> Periode Pemutihan Akhir</li>';
	                ErrCount++;
            	}
            }
            if ((ed_pemutihan_awal!='' && ed_pemutihan_awal!=null) && (ed_pemutihan_akhir!='' && ed_pemutihan_akhir!=null))
            {
            	if ((ed_pemutihan_awal+'-01')>ed_pemutihan_akhir+'-01')
            	{
	                ErrMsg += '<li>Lama Berlakunya Pemutihan Awal <b>Lebih Lama Daripada</b> Lama Berlakunya Pemutihan Akhir</li>';
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
                $('#load_data').html('<tr><td colspan="14" align="center">Mohon Tunggu...</td></tr>');
                load_table(kawasan, blok, jenis_service, periode_awal, periode_akhir, ed_pemutihan_awal, ed_pemutihan_akhir);
            }
        });
        $('#tbl_detail_history_pemutihan tfoot th').each( function () {
            var title = $(this).text();
            $(this).html( '<input type="text" placeholder="'+title+'" />' );
        } );
        $('#tbl_log_detail_history_pemutihan tfoot th').each( function () {
            var title = $(this).text();
            $(this).html( '<input type="text" placeholder="'+title+'" />' );
        } );
        $('#tbl_log_detail_detail_history_pemutihan tfoot th').each( function () {
            var title = $(this).text();
            $(this).html( '<input type="text" placeholder="'+title+'" />' );
        } );
        $('#tbl_log_approval tfoot th').each( function () {
            var title = $(this).text();
            $(this).html( '<input type="text" placeholder="'+title+'" />' );
        } );
        var table_detail = $('#tbl_detail_history_pemutihan').DataTable({
                                "preDraw": function( settings ) { 
                                    $(".new_preloader").show(); 
                                }, 
                                "drawCallback": function( settings ) { 
                                    $(".new_preloader").fadeOut(); 
                                },
                                "initComplete": function () {
                                    // Apply the search
                                    this.api().columns().every( function () {
                                        var that = this;
                                        $( 'input', this.footer() ).on( 'keyup change clear', function () {
                                            if ( that.search() !== this.value ) {
                                                that
                                                    .search( this.value )
                                                    .draw();
                                            }
                                        } );
                                    } );
                                }
                                // "scrollX": true
                            });
        var table_log = $('#tbl_log_detail_history_pemutihan').DataTable({
                                "preDraw": function( settings ) { 
                                    $(".new_preloader").show(); 
                                }, 
                                "drawCallback": function( settings ) { 
                                    $(".new_preloader").fadeOut(); 
                                },
                                "initComplete": function () {
                                    // Apply the search
                                    this.api().columns().every( function () {
                                        var that = this;
                                        $( 'input', this.footer() ).on( 'keyup change clear', function () {
                                            if ( that.search() !== this.value ) {
                                                that
                                                    .search( this.value )
                                                    .draw();
                                            }
                                        } );
                                    } );
                                }
                                // "scrollX": true
                            });
        var table_log_detail = $('#tbl_log_detail_detail_history_pemutihan').DataTable({
                                "preDraw": function( settings ) { 
                                    $(".new_preloader").show(); 
                                }, 
                                "drawCallback": function( settings ) { 
                                    $(".new_preloader").fadeOut(); 
                                },
                                "initComplete": function () {
                                    // Apply the search
                                    this.api().columns().every( function () {
                                        var that = this;
                                        $( 'input', this.footer() ).on( 'keyup change clear', function () {
                                            if ( that.search() !== this.value ) {
                                                that
                                                    .search( this.value )
                                                    .draw();
                                            }
                                        } );
                                    } );
                                }
                            });
        var table_log_approval = $('#tbl_log_approval').DataTable({
                                "preDraw": function( settings ) { 
                                    $(".new_preloader").show(); 
                                }, 
                                "drawCallback": function( settings ) { 
                                    $(".new_preloader").fadeOut(); 
                                },
                                "initComplete": function () {
                                    // Apply the search
                                    this.api().columns().every( function () {
                                        var that = this;
                                        $( 'input', this.footer() ).on( 'keyup change clear', function () {
                                            if ( that.search() !== this.value ) {
                                                that
                                                    .search( this.value )
                                                    .draw();
                                            }
                                        } );
                                    } );
                                }
                            });
        var form_data, no;
        $(document).on('click', '.detail-data', function(e){
        	id = $(this).attr('alt');
            if (id!=""&&id!=null)
            {
                id = id.split('|');
                form_data = new FormData();
                form_data.append('id', id[0]);
                form_data.append('kawasan', id[1]);
                form_data.append('blok', id[2]);
                form_data.append('jenis_service', jenis_service);
                form_data.append('periode_awal', periode_awal);
                form_data.append('periode_akhir', periode_akhir);
                form_data.append('ed_pemutihan_awal', ed_pemutihan_awal);
                form_data.append('ed_pemutihan_akhir', ed_pemutihan_akhir);
	            $.ajax({
                  url: "<?=site_url('report/history_pemutihan/get_detail_pemutihan');?>"
                  ,type: "POST"
                  ,contentType: false
                  ,processData: false
                  ,dataType:"json"
                  ,data: form_data
                  ,beforeSend: function()
                  {
                    $(".new_preloader").show();
                  }
                  ,success: function(data)
                  {
                    $("#modal").modal("show");

                    $("#title-switch").html('Log');
                    $("#area-log").show();
                    $("#btn-back").hide();
                    $("#area-log-detail").hide();

                    if (data.detail.length<1 && data.log.length<1)
                    {
                        $(".new_preloader").fadeOut();
                    }

                    table_detail.clear().draw();
                    for(var i=0; i<data.detail.length; i++)
                    {
                        table_detail.row.add( [
                            data.detail[i].kawasan_name,
                            data.detail[i].blok_name,
                            data.detail[i].jenis_service,
                            data.detail[i].no_unit,
                            data.detail[i].pemilik_name,
                            data.detail[i].periode,
                            (data.detail[i].nilai_pokok>0 ? format_nominal(data.detail[i].nilai_pokok) : 0),
                            (data.detail[i].nilai_denda>0 ? format_nominal(data.detail[i].nilai_denda) : 0),
                            (data.detail[i].nilai_total>0 ? format_nominal(data.detail[i].nilai_total) : 0),
                            data.detail[i].nilai_tagihan_type_desc,
                            (data.detail[i].pemutihan_nilai_tagihan>0 ? format_nominal(data.detail[i].pemutihan_nilai_tagihan) : 0),
                            data.detail[i].nilai_denda_type_desc,
                            (data.detail[i].pemutihan_nilai_denda>0 ? format_nominal(data.detail[i].pemutihan_nilai_denda) : 0)
                            // ,
                            // data.detail[i].status,
                            // data.detail[i].name,
                            // data.detail[i].description
                        ] ).draw( false );

                        if (i==(data.detail.length-1) && data.log.length<1 && data.log_approval.length<1)
                        {
                            $(".new_preloader").fadeOut();
                        }
                    }

                    table_log.clear().draw();
                    no = 1;
                    for(var i=0; i<data.log.length; i++)
                    {
                        if(data.log[i].status==1){
                            status = "Tambah";
                        }
                        else if(data.log[i].status==2){
                            status = "Edit";
                        }
                        else{
                            status = "Hapus";
                        }

                        table_log.row.add( [
                            no,
                            data.log[i].date,
                            data.log[i].name,
                            status,
                            '<a class="btn-modal btn btn-sm btn-primary col-md-12" data-transfer="'+data.log[i].id+'" data-type="'+data.log[i].status+'">\
                                <i class="fa fa-pencil"></i>\
                            </a>'
                        ] ).draw( false );

                        if (i==(data.log.length-1) && data.log_approval.length<1)
                        {
                            $(".new_preloader").fadeOut();
                        }
                        no++;
                    }

                    table_log_approval.clear().draw();
                    no = 1;
                    for(var i=0; i<data.log_approval.length; i++)
                    {
                        table_log_approval.row.add( [
                            no,
                            data.log_approval[i].status,
                            data.log_approval[i].u_aw_name,
                            data.log_approval[i].description,
                            data.log_approval[i].tgl
                        ] ).draw( false );

                        if (i==(data.log_approval.length-1))
                        {
                            $(".new_preloader").fadeOut();
                        }
                        no++;
                    }
                  }
                  ,error: function(data)
                  {
                    alert("Something Wrong!");
                  }
                });
            }
        });
        $(document).on('click', '.btn-modal', function(e){
            url = '<?=site_url()?>/core/get_log_detail';
            console.log($(this).attr('data-transfer'));
            console.log($(this).attr('data-type'));
            $.ajax({
                type: "POST",
                data: {id:$(this).attr('data-transfer'),type:$(this).attr('data-type')},
                url: url,
                dataType: "json",
                success: function(data){
                    $("#modal-detail").modal("show");

                    $("#title-switch").html('Log Detail');
                    $("#area-log").hide();
                    $("#btn-back").show();
                    $("#area-log-detail").show();

                    $("#dataModal").html("");

                    table_log_detail.clear().draw();
                    if(data[data.length-1] == 2){
                        console.log(data[0]);
                        for (i = 0; i < data[0].length; i++) { 
                            $.each(data[1], function(key, val){
                                if(val.name == data[0][i].name){
                                    console.log(val.name);
                                    table_log_detail.row.add( [
                                        data[0][i].name,
                                        val.value,
                                        data[0][i].value
                                    ] ).draw( false );
                                }
                            }); 
                        }
                    }else{
                        $.each(data, function(key, val){
                            if(data[data.length-1] == 1){
                                console.log(data);
                                if(val.name)
                                    table_log_detail.row.add( [
                                        val.name.toUpperCase(),
                                        val.value,
                                        ''
                                    ] ).draw( false );
                            }else if(data[data.length-1] == 2){
                                
                            }else if(data[data.length-1] == 3){
                                console.log(data);
                                if(val.name)
                                    table_log_detail.row.add( [
                                        val.name.toUpperCase(),
                                        val.value,
                                        ''
                                    ] ).draw( false );
                            }
                        });
                    }
                }
            });
        });
        $(document).on('click', '#btn-back', function(e){
            $("#title-switch").html('Log');
            $("#area-log").show();
            $("#btn-back").hide();
            $("#area-log-detail").hide();
        });
    });

    function format_nominal(nominal="")
    {
        if (nominal!="")
        {
            var number_string = nominal.toString(),
                sisa    = number_string.length % 3,
                rupiah  = number_string.substr(0, sisa),
                ribuan  = number_string.substr(sisa).match(/\d{3}/g);
                    
            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            nominal = rupiah;
        }
        return nominal;
    }
    
    function load_table(kawasan=null, blok=null, jenis_service=null, periode_awal=null, periode_akhir=null, ed_pemutihan_awal=null, ed_pemutihan_akhir=null)
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
            
            "ajax": {
                url: "<?= site_url('report/history_pemutihan/request_data_json'); ?>",
                type: "get",
                data: {
                    kawasan: kawasan,
                    blok: blok,
                    jenis_service: jenis_service,
                    periode_awal: periode_awal,
                    periode_akhir: periode_akhir,
                    ed_pemutihan_awal: ed_pemutihan_awal,
                    ed_pemutihan_akhir: ed_pemutihan_akhir,
                },
                cache: false,
                error: function() {
                    $(".tb-meterair-error").html("");
                    $("#tb-meterair").append('<tbody class="my-grid-error"><tr><th colspan="14"><center>No data found in the server</center></th></tr></tbody>');
                    $("#tb-meterair_processing").css("display", "none");
                }
            },
            "scrollX": true,
            "preDraw": function( settings ) {
                $(".new_preloader").show();
            },
            "drawCallback": function( settings ) {
                $(".new_preloader").fadeOut();
            }
        });
        // Apply the search
    }
</script>
<style>
    #load_data td:nth-child(1) { white-space: nowrap; }
</style>