<?=$load_css;?>
<?=$load_js;?>
<div class="clearfix"></div>
</div>
<div class="x_conte nt">
	<form id="form" class="form-horizontal form-label-left" method="post" action="<?=site_url();?>/P_master_isp/save">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label class="label-align col-lg-5 col-md-5 col-sm-12">Pemilik <span class="required">*</span></label>
				<div class="col-lg-5 col-md-5 col-sm-12">
					<select name="pemilik" required="" class="form-control" placeholder="-- Pilih Pemilik --">
					</select>
				</div>
			</div>
            <div class="form-group">
                <label class="label-align col-lg-5 col-md-5 col-sm-12">Cara Pembayaran</label>
                <div class="col-lg-5 col-md-5 col-sm-12">
                    <select name="cara_pembayaran" required="" class="form-control select2" placeholder="-- Pilih Cara Pembayaran --">
                        <option value="all" selected>Semua Cara Pembayaran</option>
                        <?php
                            foreach ($cara_pembayaran as $v) {
                                echo ("<option value='$v->id'>$v->code - $v->name</option>");
                            }
                        ?>
                    </select>
                </div>
            </div>
			<div class="form-group">
				<label class="label-align col-lg-5 col-md-5 col-sm-12">Tanggal Bayar</label>
				<div class="col-lg-5 col-md-5 col-sm-12">
					<div class='input-group' style="display: block;">
						<input name="tanggal_bayar" type="text" class="form-control datetimepicker_date" placeholder="Tanggal Bayar" value="">
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
                <!-- <button id="btn-reset-filter" class="btn btn-warning btn-action-form" type="button"></i> Reset</button> -->
			</div>
		</div>
	</form>
    <div class="clearfix"></div>
    <div class="table-responsive">
	    <div class="col-lg-12 col-md-12 col-sm-12" style="margin-top:20px">
	        <table class="table table-striped jambo_table" id="tb-history-deposit" style="width:100%">
                <tfoot style="display: table-header-group">
                    <tr>
                        <th>No. Deposit</th>
                        <th>Pemilik</th>
                        <th>Nilai Deposit</th>
                        <th>Cara Pembayaran</th>
                        <th>Deskripsi</th>
                        <th>Tanggal Bayar</th>
                        <th>User</th>
                    </tr>
                </tfoot>
	            <thead>
	                <tr>
                        <th>No. Deposit</th>
                        <th>Pemilik</th>
                        <th>Nilai Deposit</th>
                        <th>Cara Pembayaran</th>
                        <th>Deskripsi</th>
                        <th>Tanggal Bayar</th>
                        <th>User</th>
	                </tr>
	            </thead>
                <tbody></tbody>
	        </table>
	    </div>
    </div>
    <div class="clearfix"></div>
    <div class="table-responsive">
        <div class="col-lg-12 col-md-12 col-sm-12" style="margin-top:20px">
            Total deposit customer saat ini : <font id="deposit_user" style="font-weight: bold;"></font>
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
        $('#form select[name="pemilik"]').select2({
            width: 'resolve',
            // resize:true,
            // minimumInputLength: 1,
            placeholder: '-- Pilih Pemilik --',
            ajax: {
                type: "GET",
                dataType: "json",
                url: "<?= site_url() ?>/report/history_deposit/get_customer_pemilik_ajax",
                data: params => {
                    return {
                        data: params.term
                    }
                },
                processResults: data => {
                    return {
                        results: data
                    };
                }
            }
        });

        $('.datetimepicker_date').datetimepicker({
            viewMode: 'years',
            format: 'YYYY-MM-DD'
        });

        $('#tb-history-deposit tfoot th').each(function(){
            var title = $(this).text();
            if ($(this).text()!="")
            {
                $(this).html("<input type='text' class='form-control form-control-sm' placeholder='"+title+"' />");
            }
        });

        //datatables
        var reset_table = 1;
        var table = $('#tb-history-deposit').DataTable({ 
            "preDraw": function( settings ) { 
                $(".new_preloader").show(); 
            }, 
            "drawCallback": function( settings ) { 
                $(".new_preloader").fadeOut();
                $("#deposit_user").html(settings.json.deposit_saldo);
            },
            "initComplete": function() {
              this.api().columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if (cek_requirement())
                    {
                      if (that.search() !== this.value) {
                        that
                          .search(this.value)
                          .draw();
                      }
                    }
                    else
                    {
                        $(this).val("");
                    }
                });
              });
            },
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
     
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo site_url('report/history_deposit/get_data_ajax')?>",
                "type": "POST",
                "data": function ( data ) {
                    data.reset = reset_table;

                    data.pemilik = $('#form select[name="pemilik"]').val();
                    data.cara_pembayaran = $('#form select[name="cara_pembayaran"]').val();
                    data.tanggal_bayar = $('#form input[name="tanggal_bayar"]').val();
                }
            },
     
            //Set column definition initialisation properties.
            "columnDefs": [
                { 
                    "targets": [ 0 ], //first column / numbering column
                    "orderable": false, //set not orderable
                },
            ],
        });
     
        var success,ErrMsg;
        function cek_requirement()
        {
            success = 1;
            ErrMsg = '<ul>';

            if ($('#form select[name="pemilik"]').val() == "" || $('#form select[name="pemilik"]').val() == null) {
                ErrMsg += '<li>Pemilik Masih Belum dipilih</li>';
                success = 0;
            }

            if (!success)
            {
                ErrMsg += '</ul>';
                var btn_close = "<button type='button' class='btn btn-info' data-dismiss='modal'>Close</button>";
                $('#modal-body').html(ErrMsg);
                $('#modal-footer-desc').html(btn_close);
                $('#modal-notify').modal('show');
            }

            return success;
        }
        var cek;
        $('#btn-load-data').click(function(){ //button filter event click
            if (cek_requirement())
            {
                reset_table = 0;
                table.ajax.reload();  //just reload table
            }
        });
        $('#btn-reset-filter').click(function(){ //button filter event click
            $('#form select[name="pemilik"]').val();

            $('#form select[name="pemilik"]').val("").trigger('change');
            $('#form select[name="cara_pembayaran"]').val("").trigger('change');
            $('#form input[name="tanggal_bayar"]').val("");

            $('#tb-history-deposit input[type="text"]').each(function(){
                $(this).val("");
            });

            $('#tb-history-deposit').dataTable().fnFilter('');

            // table.ajax.reload();  //just reload table
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
</script>
<style>
    #load_data td:nth-child(1) { white-space: nowrap; }
</style>