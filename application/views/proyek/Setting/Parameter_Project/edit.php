<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!-- select2 -->
<link href="<?= base_url() ?>vendors/select2/dist/css/select2.min.css" rel="stylesheet">
<script src="<?= base_url() ?>vendors/select2/dist/js/select2.min.js"></script>
<!-- summernote-0.8.18-dist -->
<link href="<?= base_url() ?>assets/summernote-0.8.18-dist/summernote.min.css" rel="stylesheet">
<script src="<?= base_url() ?>assets/summernote-0.8.18-dist/summernote.min.js"></script>
<!-- flat -->
<link href="<?= base_url() ?>vendors/iCheck/skins/flat/green.css" rel="stylesheet">

<!DOCTYPE html>

<style>
    .range_akhir,
    .range_awal {
        text-align: right;
    }

    .range_akhir {
        color: transparent;
        text-shadow: 0 0 0 black;

        &:focus {
            outline: none;
        }
    }
</style>

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

        <div class="com-lg-6 col-md-6 col-xs-12">
            <div class="form-group">

                <label class="control-label col-md-3 col-sm-3 col-xs-12">Nama</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" value="<?= $data->name ?>" required readonly>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Kode</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" value="<?= $data->code ?>" required readonly>
                </div>
            </div>
        </div>
        <div class="com-lg-6 col-md-6 col-xs-12">
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Keterangan</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <textarea name="description" class="form-control" rows="4"><?= $data->description ?></textarea>
                </div>
            </div>
        </div>


        <div class="com-lg-12 col-md-12 col-xs-12">

            <div class="form-group">
                <label class="control-label" style="width:12%; float:left">Value</label>
                <?php if (stripos($data->code, 'ttd') !== FALSE) :?>
                <div class="col-md-3 col-sm-3">
                    <input id="file" type="file" class="form-control" name="file">
                </div>
                <?php elseif($data->code == 'user_approve_void_pembayaran'):?>
                    <select id="file" type="file" class="form-control" name="file">
                    
                <?php else : ?>
                <div class="col-md-11 col-sm-11 col-xs-11" style="width:88%">
                    <?php
                        if ($data->type=='2') {
                            echo '<textarea id="descr" name="value">'.$data->value.'</textarea>';
                        } else if ($data->type=='1') {
                            echo '<input name="value" type="number" class="form-control" min=0 value="'.$data->value.'" required>';
                        } else { // $data->type=='0'
                            echo '<input name="value" type="text" class="form-control" value="'.$data->value.'" required>';
                        }
                    ?>
                </div>
                <?php
                endif;
                ?>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                <div class="center-margin">
                    <button class="btn btn-primary" type="reset">Reset</button>
                    <a id="submit" type="submit" class="btn btn-success">Submit</a>
                </div>
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

            $("#submit").click(function() {
                console.log($("#form").serialize());
                if(<?=(stripos($data->code, 'ttd') !== FALSE)?>+0!=0){
                    var data2 = new FormData();

                    $('input[type="file"]').each(function($i) {
                        data2.append($(this).prop("id"), $(this)[0].files[0]);
                    });
                    data = $("#form").serialize();
                    alert(JSON.stringify(data));
                    return false;
                    url = "<?= site_url() ?>Setting/P_parameter_project/ajax_save_img?id=<?= $this->input->get("id") ?>"+"&"+data;
                    $.ajax({
                        type: "POST",
                        data: data2,
                        url: url,
                        dataType: "json",
                        cache: false,
                        contentType: false,
                        processData: false,
                        
                        success: function(data) {
                            console.log($("#form").serialize());
                            if (data.status)
                                notif('Sukses', data.message, 'success');
                            else
                                notif('Gagal', data.message, 'danger');
                        }
                    });
                }else{
                    url = "<?= site_url() ?>Setting/P_parameter_project/ajax_save?id=<?= $this->input->get("id") ?>";

                    $.ajax({
                        type: "POST",
                        data: $("#form").serialize(),
                        url: url,
                        dataType: "json",
                        success: function(data) {
                            console.log($("#form").serialize());
                            if (data.status)
                                notif('Sukses', data.message, 'success');
                            else
                                notif('Gagal', data.message, 'danger');
                        }
                    });
                }
            });

            $('#descr').summernote({
                placeholder: 'Hello stand alone ui',
                tabsize: 5,
                height: 500,
                toolbar: [
                  ['font', ['bold', 'italic', 'underline', 'clear']],
                  // ['para', ['ul', 'ol', 'paragraph']],
                  // ['table', ['table']],
                  // ['insert', ['picture']],
                  // ['view', ['fullscreen']]
                ]
            });
        });
    </script>
<!-- <script src="<?= base_url() ?>assets/wysiwng/bootstrap-wysiwng.min.js"></script> -->
<!-- <script src="<?= base_url() ?>assets/wysiwng/prettify.js"></script> -->