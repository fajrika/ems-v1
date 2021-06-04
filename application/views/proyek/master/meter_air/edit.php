<link href="<?= base_url('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css');?>" rel="stylesheet">
<script src="<?=base_url('vendors/moment/min/moment.min.js');?>"></script>
<script src="<?=base_url('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js');?>"></script>

<?php
if ($meteran->num_rows()) :
$row = $meteran->row();
?>
    <form action="<?=site_url('master-meter-air/edit/'.$row->id);?>" id="form-meterair" class="form-horizontal form-label-left" method="post" novalidate>
        <div class="field item form-group">
            <label class="control-label col-md-4 col-xs-12">Kode<span class="required">*</span></label>
            <div class="col-md-7 col-xs-12">
                <input type="text" class="form-control" name="kode" id="kode" placeholder="...." readonly value="<?=$row->kode;?>" />
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-4 col-xs-12">Nama Meteran Air<span class="required">*</span></label>
            <div class="col-md-7 col-xs-12">
                <input type="text" class="form-control" name="nama_meteran" id="nama_meteran" placeholder="...." required value="<?=$row->nama_meter_air;?>">
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-4 col-xs-12">No. Seri Meter<span class="required">*</span></label>
            <div class="col-md-7 col-xs-12">
                <input type="text" name="no_seri_meter" class="form-control" placeholder="...." required value="<?=$row->no_seri_meter;?>" readonly>
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-4 col-xs-12">ID Barcode</label>
            <div class="col-md-7 col-xs-12">
                <input type="text" name="id_barcode" class="form-control" placeholder='....' value="<?=$row->barcode;?>" readonly>
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-4 col-xs-12">Ukuran Meter Air<span class="required">*</span></label>
            <div class="col-md-7 col-xs-12">
                <input type="text" name="ukuran_meter_air" class="form-control" placeholder="...." required value="<?=$row->ukuran_meter_air;?>">
            </div>
        </div>
        <!-- <div class="field item form-group">
            <label class="control-label col-md-4 col-xs-12">Size Pipa<span class="required">*</span></label>
            <div class="col-md-7 col-xs-12">
                <input type="text" name="size_pipa" class="form-control" placeholder="...." required value="<?=$row->size_pipa;?>"> 
            </div>
        </div> -->
        <div class="field item form-group">
            <label class="control-label col-md-4 col-xs-12">Status Meteran</label>
            <div class="col-md-7 col-xs-12">
                <select name="status_meteran" class="form-control select2" placeholder="...." disabled="">
                    <option value="1" <?=($row->status_meter=='1') ? 'selected' : '';?>>Belum Terpasang</option>
                    <option value="2" <?=($row->status_meter=='2') ? 'selected' : '';?>>Terpasang</option>
                    <option value="3" <?=($row->status_meter=='3') ? 'selected' : '';?>>Rusak </option>
                    <option value="4" <?=($row->status_meter=='4') ? 'selected' : '';?>>Tidak Aktif</option>
                </select>
            </div>
        </div>
        <div class="field item form-group">
            <label class="control-label col-md-4 col-xs-12">Tgl. Awal/Akhir Pakai</label>
            <div class="col-md-3">
                <input type="text" class="form-control" name="tgl_awal" id="tgl_awal" placeholder="...." value="<?=$row->tgl_meter_awal;?>">
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" name="tgl_akhir" id="tgl_akhir" placeholder="...." value="<?=$row->tgl_meter_akhir;?>"> 
            </div>
        </div>
    </form>
<?php endif; ?>
<script>
    $(document).ready(function(){
        $("#nama_meteran").keyup(function(){
            $("#kode").val($("#nama_meteran").val().toLowerCase().replace(/ /g,'_'));
        });
        // datepicker
        $('#tgl_awal, #tgl_akhir').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    });
</script>