<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!-- select2 -->
<link href="<?= base_url() ?>vendors/select2/dist/css/select2.min.css" rel="stylesheet">
<script src="<?= base_url() ?>vendors/select2/dist/js/select2.min.js"></script>
<!-- flat -->
<link href="<?= base_url() ?>vendors/iCheck/skins/flat/green.css" rel="stylesheet">

<!DOCTYPE html>
<div style="float:right">
    <h2>
        <button class="btn btn-warning" onClick="window.location.href = '<?= substr(current_url(), 0, strrpos(current_url(), "/")) ?>'">
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
    <form id="form" class="form-horizontal form-label-left">
        <div class="com-lg-6 col-md-6 col-xs-6">
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Nama</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="text" class="form-control" name="name" placeholder="Masukkan Nama Level">
                </div>
            </div>
        </div>
        <div class="com-lg-6 col-md-6 col-xs-6">
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Keterangan</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <textarea class="form-control" rows="6" name="description" placeholder='Masukkan Keterangan'></textarea>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="form-group">
            <div class="center-margin">
                <button class="btn btn-primary" type="reset">Reset</button>
                <a id="submit" type="submit" class="btn btn-success">Submit</a>
            </div>
        </div>
    </form>


    <script>
        function notif(title, text, type) {
			new PNotify({
				title: title,
				text: text,
				type: type,
				styling: 'bootstrap3'
			});
		}
        $(function() {

            $('.select2').select2();

            $("#submit").click(function(){
                $.ajax({
                    type: "GET",
                    data: $("#form").serialize(),
                    url: "<?= site_url() ?>/Setting/Akun/Level/ajax_save",
                    dataType: "json",
                    success: function(data) {
                        if (data.status)
                            notif('Sukses', data.message, 'success');
                        else
                            notif('Gagal', data.message, 'danger');
                    }
                });
            });
        });
    </script>