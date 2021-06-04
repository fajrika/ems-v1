<link href="<?= base_url('vendors/select2/dist/css/select2.min.css');?>" rel="stylesheet">
<link href="<?= base_url('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css');?>" rel="stylesheet">
<script src="<?=base_url('vendors/select2/dist/js/select2.min.js'); ?>"></script>
<script src="<?=base_url('vendors/moment/min/moment.min.js');?>"></script>
<script src="<?=base_url('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js');?>"></script>
<script src="<?=base_url('vendors/validator/multifield.js');?>"></script>
<script src="<?=base_url('vendors/validator/validator.js');?>"></script>
<div style="float:right">
    <h2>
        <button class="btn btn-warning" onClick="window.location.href='<?=site_url('Master_meter_air/');?>'">
            <i class="fa fa-arrow-left"></i> Back
        </button>
        <button class="btn btn-success" onClick="window.location.href='<?=site_url('Master_meter_air/add');?>'">
            <i class="fa fa-repeat"></i> Refresh
        </button>
    </h2>
</div>
<div class="clearfix"></div>
</div>
<div class="x_content">
    <br>
    <form action="<?=site_url('master_meter_air/add');?>" id="form-meterair" class="form-horizontal form-label-left" method="post" novalidate>
        <div class="field item form-group">
            <label class="control-label col-md-3 col-xs-12">Kode<span class="required">*</span></label>
            <div class="col-md-6 col-xs-12">
                <input type="text" class="form-control" name="kode" id="kode" placeholder="" readonly />
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-3 col-xs-12">Nama Meteran Air<span class="required">*</span></label>
            <div class="col-md-6 col-xs-12">
                <input type="text" class="form-control" name="nama_meteran" id="nama_meteran" placeholder="Nama Meteran Air" required>
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-3 col-xs-12">No. Seri Meter<span class="required">*</span></label>
            <div class="col-md-6 col-xs-12">
                <input type="text" name="no_seri_meter" class="form-control" placeholder="No. Seri Meter" required>
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-3 col-xs-12">ID Barcode</label>
            <div class="col-md-6 col-xs-12">
                <input type="text" name="id_barcode" class="form-control" placeholder='Barcode'>
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-3 col-xs-12">Ukuran Meter Air<span class="required">*</span></label>
            <div class="col-md-6 col-xs-12">
                <input type="text" name="ukuran_meter_air" class="form-control" placeholder="0.5" required>
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-3 col-xs-12">Status Meteran</label>
            <div class="col-md-6 col-xs-12">
                <select name="status_meteran" class="form-control select2">
                    <option value="1">Belum Terpasang</option>
                </select>
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-3 col-xs-12">Tgl. Awal/Akhir Pakai</label>
            <div class="col-md-3">
                <input type="text" class="form-control" id="tgl_awal" name="tgl_awal" placeholder="....">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="tgl_akhir" id="tgl_akhir" placeholder="...."> 
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-3 col-xs-12">&nbsp;</label>
            <div class="col-md-6 col-xs-12 center-margin">
                <button type="reset" id="btn-reset" class="btn btn-primary"><i class="fa fa-refresh"></i> Reset</button>
                <button type="button" id="btn-process" class="btn btn-success"><i class="fa fa-edit"></i> Submit</button>
            </div>
        </div>
    </form>
</div>

<div class="modal" id="ModalShow" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Konfirmasi</h4>
            </div>
            <div class="modal-body" id="modal-body"></div>
            <div class="modal-footer" id="modal-footer"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(".select2").select2();

        // Apply the search
        var table = $('#tableDTServerSite').DataTable({
            "serverSide": true,
            "stateSave" : false,
            "bAutoWidth": true,
            "oLanguage": {
                "sSearch": "Search :",
                "sLengthMenu": "Show _MENU_ entries",
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
                [ 0, "desc" ],
                [ 1, "desc" ]
            ],
            "columnDefs": [
                {"targets": 'no-sort', "orderable": false}
            ],
            "ajax": {
                url : "<?=site_url("master_meter_air/get_data_upload")?>",
                cache: false,
                type: "get",
            }
        });

        // change kawasan
        $(document).on('change', '#kawasan', function(e){
            e.preventDefault();
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
                url: "<?=site_url('Master_meter_air/ajax_get_blok');?>",
                dataType: "json",
                success: function(data) {
                    $("#blok").html("");
                    $("#blok").attr("disabled", false);
                    $("#blok").append("<option value='' disabled selected>-- Pilih Blok --</option>");
                    for (var i = 0; i < data.length; i++) {
                        $("#blok").append("<option value='" + data[i].id + "'>" + data[i].name + "</option>");
                    }
                }
            });
        });

        // change blok
        $(document).on('change', '#blok', function(e){
            e.preventDefault();
            if ($("#blok").val() == null) {
                $('#blok').next().find('.select2-selection').addClass('has-error');
            } else {
                $('#blok').next().find('.select2-selection').removeClass('has-error');
            }
            $.ajax({
                type: "GET",
                data: {
                    id: $(this).val()
                },
                url: "<?=site_url('Master_meter_air/ajax_get_unit');?>",
                dataType: "json",
                success: function(data) {
                    $("#unit_id").html("");
                    $("#unit_id").attr("disabled", false);
                    $("#unit_id").append("<option value='' disabled selected>-- Pilih Unit --</option>");
                    for (var i = 0; i < data.length; i++) {
                        $("#unit_id").append("<option value='" + data[i].id + "'>" + data[i].no_unit + "</option>");
                    }
                }
            });
        });

        $(document).on('click', '#btn-process', function(e){
            e.preventDefault();
            msg  = '<p>Yakin ingin menyimpan data ini ?</p>';
            btn_save = '<button type="button" id="Yes" class="btn btn-primary">Simpan Data</button>';
            show_modal(msg, btn_save);
        });

        $(document).on('click', '#Yes', function(e){
            e.preventDefault();
            $.ajax({
                url: $('#form-meterair').attr('action'),
                cache: false,
                type: 'POST',
                data: $('#form-meterair').serialize(),
                dataType: 'json',
                success: function(data) {
                    if(data.status==1){
                        show_modal(data.pesan);
                        setTimeout(function(){
                            window.location.href=data.link_href;
                        }, 1000);
                    } else {
                        show_modal(data.pesan);
                    }
                }
            });
        });

        $("#nama_meteran").keyup(function(){
            $("#kode").val($("#nama_meteran").val().toLowerCase().replace(/ /g,'_'));
        });

        $('#tgl_awal, #tgl_akhir').datetimepicker({
            format: 'YYYY-MM-DD'
        });

    });

    function show_modal(msg, btn_save=null) {
        close = '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
        if (btn_save==null) {
            button = close;
        } else {
            button = btn_save + close;
        }

        $('#modal-footer').html(button);
        $('#modal-body').html(msg);
        $('#ModalShow').modal('show');
    }
</script>
<style type="text/css">
    .btn:focus { outline: none;}
    .table thead th{ border: 1px solid #556c81 !important; }
    table.dataTable thead .sorting_asc:after { font-size: 11px; }
    div.dataTables_wrapper { width: 99%; height: 100%; margin: 0 auto; }
    .table>thead>tr>th { border-bottom: 1px solid #556c81 !important; padding: 10px 9px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .table>tbody>tr>td { border: 1px solid #ddd; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { padding-top: 2px; font-size: 12px; }
    .form-control, .select2-container--default .select2-selection--single { font-size: 12px; box-shadow: none; border-radius: 0px !important; }
    .dataTables_length select,
    .dataTables_filter input { padding: 6px 12px; border: 1px solid #ccc; border-radius: 0px; }
    tfoot input::placeholder { font-size:11px; }
</style>