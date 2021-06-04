
<link href="<?= base_url() ?>vendors/select2/dist/css/select2.min.css" rel="stylesheet">
<link href="<?=base_url(); ?>vendors/switchery/dist/switchery.min.css" rel="stylesheet">
<script src="<?= base_url() ?>vendors/select2/dist/js/select2.min.js"></script>
<script src="<?=base_url(); ?>vendors/switchery/dist/switchery.min.js"></script>
<!DOCTYPE html>
<div style="float:right">
    <h2>
        <button class="btn btn-dark" onClick="window.location.href = '<?= substr(current_url(), 0, strrpos(current_url(), "/")) ?>'">
            <i class="fa fa-arrow-left"></i> Back
        </button>
        <button class="btn btn-dark" onClick="window.location.href='<?=site_url('p-master-pemeliharaan-air/add');?>'">
            <i class="fa fa-repeat"></i> Refresh
        </button>
    </h2>
</div>
<div class="clearfix"></div>
</div>
<div class="x_content">
    <br>
    <form id="form-cara-bayar" autocomplete="off" class="form-horizontal form-label-left">
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="code">Kode<span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" id="code" name="code" required class="form-control col-md-7 col-xs-12" placeholder="Masukkan Nama" readonly>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Nama<span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" id="name" name="name" required class="form-control col-md-7 col-xs-12" placeholder="Masukkan Nama">
            </div>
        </div>
        <!-- <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ukuran_pipa">Ukuran Pipa<span class="required">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" id="ukuran_pipa" name="ukuran_pipa" class="form-control col-md-7 col-xs-12" placeholder="0.5" required>
            </div>
        </div> -->
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nilai">Biaya Pemeliharaan<span class="required">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <span class="form-control-feedback left" aria-hidden="true">Rp.</span>
                <input type="text" id="nilai" name="nilai" required class="form-control col-md-7 col-xs-12 currency" style="padding-left: 50px;">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nilai">Biaya Pemasangan<span class="required">*</span></label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <span class="form-control-feedback left" aria-hidden="true">Rp.</span>
                <input type="text" id="nilai_pemasangan" name="nilai_pemasangan" class="form-control col-md-7 col-xs-12 currency_pemasangan" required style="padding-left: 50px;">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">PPN Pemasangan?</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="checkbox" class="form-control js-switch disabled-form col-md-7 col-xs-12" id="nilai_ppn_pemasangan" name="nilai_ppn_pemasangan" /> Yes
            </div>
        </div>

        <div class="form-group">
            <label for="description" class="control-label col-md-3 col-sm-3 col-xs-12">Keterangan</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <textarea class="form-control col-md-7 col-xs-12" id="description" name="description" rows="6" placeholder="Keterangan jika diperlukan"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="description" class="control-label col-md-3 col-sm-3 col-xs-12">&nbsp;</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <button type="submit" class="btn-submit btn btn-primary btn-block"><i class="fa fa-edit"></i> Submit</button>
            </div>
        </div>
    </form>
</div>

<!-- jQuery -->
<script type="text/javascript">
    function formatNumber(data) {
        data = data + '';
        data = data.replace(/,/g, "");
        data = parseInt(data) ? parseInt(data) : 0;
        data = data.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
        return data;
    }
    function unformatNumber(data) {
        data = data + '';
        return data.replace(/,/g, "");
    }
    function notif(title, text, type) {
        new PNotify({
            title: title,
            text: text,
            type: type,
            styling: 'bootstrap3'
        });
    }
    
    $(function() {
        $("#name").keyup(function() {
            $("#code").val($("#name").val().toLowerCase().replace(/ /g, '_'));
        });
        $(".currency").keyup(function() {
            $(this).val(formatNumber($(this).val()));
        });
        $(".currency_pemasangan").keyup(function() {
            $(this).val(formatNumber($(this).val()));
        });
        $("form").submit(function(e) {
            e.preventDefault();
            $('.currency').val(unformatNumber($(".currency").val()));
            $('.currency_pemasangan').val(unformatNumber($(".currency_pemasangan").val()));

            if($('#nilai_ppn_pemasangan').prop('checked')){
                nilai_ppn_pemasangan = 1;
            }else{
                nilai_ppn_pemasangan = 0;
            }

            $.ajax({
                url: "<?=site_url('P_master_pemeliharaan_air/ajax_save'); ?>",
                type: "POST",
                cache: false,
                data: $("#form-cara-bayar").serialize() + '&nilai_ppn_pemasangan='+nilai_ppn_pemasangan,
                dataType: "json",
                success: function(data) {
                    if (data.status == 1) {
                        notif('Sukses', data.message, 'success')
                        setTimeout(function() {
                            window.location.href = '<?=site_url('P_master_pemeliharaan_air')?>'
                        }, 1000);
                    } else
                        notif('Gagal', data.message, 'danger')
                }
            });
            $('.currency').val(formatNumber($(".currency").val()));
        })
    });
</script>
<style type="text/css">
    .btn { padding: 7px 12px; font-size: 13px;}
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
    textarea { resize: vertical; }
</style>