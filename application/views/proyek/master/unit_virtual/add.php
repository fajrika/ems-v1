<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<link rel="stylesheet" href="<?= base_url(); ?>vendors/select2/dist/css/select2.min.css">
<script src="<?= base_url(); ?>vendors/select2/dist/js/select2.min.js"></script>
<!DOCTYPE html>
<div style="float:right">
    <h2>
        <button class="btn btn-warning" onClick="window.history.back()">
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
    <form id="form" class="form-horizontal form-label-left" method="post" action="<?= site_url() ?>/P_master_unit_virtual/save">
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="code">Unit <span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" id="unit" name="unit" required="required" class="form-control col-md-7 col-xs-12" value="">
            </div>
        </div>


        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pilih pt">Customer <span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <select type="text" id="customer_id" required="required" class="form-control col-md-7 col-xs-12" name="customer_id">
                    <option value="" selected="" disabled="">--Pilih Customer--</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Alamat</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <textarea id="alamat" class="form-control col-md-7 col-xs-12" type="text" name="alamat"></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="code"> Jenis Unit<span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" id="va" name="jenis_usaha" required="required" class="form-control col-md-7 col-xs-12" maxlength=8>
            </div>
        </div>




        <div class="form-group">
            <div class="center-margin">
                <button class="btn btn-primary" type="reset">Reset</button>
                <button type="submit" class="btn btn-success">Submit</button>
            </div>
        </div>
    </form>
    
    <script>
        $("#customer_id").select2({
            width: 'resolve',
            // resize:true,
            minimumInputLength: 1,
            placeholder: 'Customer Name',
            ajax: {
                type: "GET",
                dataType: "json",
                url: "<?= site_url() ?>/P_master_unit_virtual/get_ajax_customer",
                data: function(params) {
                    return {
                        data: params.term
                    }
                },
                processResults: function(data) {
                    console.log(data);
                    // Tranforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: data
                    };
                }
            }
        });
    </script>
</div>
</div>
</form>