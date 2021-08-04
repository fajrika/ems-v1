
<form action="<?=site_url('master_meter_air/add');?>" id="form_master_meter" class="form-label-left" method="post" novalidate>
    <div class="field item form-group">
        <label class="label-text">Kode<span class="required">*</span></label>
        <input type="text" class="form-control" name="kode" id="kode" placeholder="" readonly />
    </div>
    <div class="field item form-group">
        <label class="label-text">Nama Meteran Air<span class="required">*</span></label>
        <input type="text" class="form-control" name="nama_meteran" id="nama_meteran" placeholder="Nama Meteran Air" required>
    </div>
    <div class="field item form-group">
        <label class="label-text">No. Seri Meter<span class="required">*</span></label>
        <input type="text" name="no_seri_meter" class="form-control" placeholder="No. Seri Meter" required>
    </div>
    <div class="field item form-group">
        <label class="label-text">ID Barcode</label>
        <input type="text" name="id_barcode" class="form-control" placeholder='Barcode'>
    </div>
    <div class="field item form-group">
        <label class="label-text">Ukuran Meter Air<span class="required">*</span></label>
        <input type="text" name="ukuran_meter_air" class="form-control" onkeypress="onlyinteger(event)" maxlength="4" placeholder="0.5" required>
    </div>
    <div class="field item form-group hidden">
        <label class="label-text">Status Meteran</label>
        <select name="status_meteran" class="form-control select2">
            <option value="1">Belum Terpasang</option>
        </select>
    </div>
    <div class="field item form-group hidden">
        <label class="label-text">Tgl. Awal Pakai</label>
        <input type="text" class="form-control" id="tgl_awal" name="tgl_awal" value="<?=date('Y-m-d');?>">
    </div>
    <div class="field item form-group hidden">
        <label class="label-text">Tgl. Akhir Pakai</label>
        <input type="text" class="form-control" name="tgl_akhir" id="tgl_akhir" value="<?=date('Y-m-d', strtotime('+5 years'));?>"> 
    </div>
    <div class="field item form-group">
        <button type="button" id="save-meter-air" class="btn btn-sm btn-success"><i class="fa fa-edit"></i> Submit</button>
        <button type="button" id="close-meter-air" class="btn btn-sm btn-primary"><i class="fa fa-remove"></i> Close</button>
    </div>
</form>

<hr>
<div id="notif_master_meter"></div>

<script type="text/javascript">
    $(document).ready(function(){
        // datepicker fomr meter air
        $('#tgl_awal, #tgl_akhir').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    });
</script>
<style type="text/css">
    .label-text { font-size: 12px; }
    input[placeholder] { font-size: 12px; }
</style>