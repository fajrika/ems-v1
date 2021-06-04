<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<?=$load_css;?>
<?=$load_js;?>
<div style="float:right">
	<h2>
		<button class="btn btn-warning btn-action-header" onClick="window.location.href = '<?=substr(current_url(),0,strrpos(current_url(),"/"))?>'">
			<i class="fa fa-arrow-left"></i>
			Back
		</button>
		<button class="btn btn-success btn-action-header" onClick="window.location.href='<?=site_url()?>/P_master_isp/add'">
			<i class="fa fa-repeat"></i>
			Refresh
		</button>
	</h2>
</div>
<div class="clearfix"></div>
</div>
<div class="x_content">
	<br>
	<form id="form" class="form-horizontal form-label-left" method="post" action="<?=site_url();?>/P_master_isp/save">
		<div class="col-md-6 col-xs-12">
			<div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Nama ISP *</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" name="nama_isp" class="form-control" placeholder="Masukkan Nama ISP" required>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Bandwidth (MB) *</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="number" name="bandwidth" class="form-control" placeholder="Masukkan Bandwidth (MB)" min="0" required>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Persen Mitra *</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="number" name="persen_mitra" class="form-control" placeholder="Masukkan Persen Mitra" min="0" max="100" required>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Nilai Kabel *</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" name="nilai_kabel" class="form-control nominal_mata_uang" placeholder="Masukkan Nilai Kabel" required>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Nilai Pemasangan *</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" name="nilai_pemasangan" class="form-control nominal_mata_uang" placeholder="Masukkan Nilai Pemasangan" required>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Nilai Lain-lain *</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" name="nilai_lain_lain" class="form-control nominal_mata_uang" placeholder="Masukkan Nilai Lain-lain" required>
				</div>
			</div>
		</div>
		<div class="col-md-6 col-xs-12">
			<div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Keterangan *</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<textarea name="keterangan" class="form-control" rows="3" placeholder='Masukkan Keterangan' required></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Status</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<select name="active" class="form-control select2" required>
						<option value="" selected disabled>--Pilih Status--</option>
						<option value="1">Aktif</option>
						<option value="0">Tidak Aktif</option>
					</select>
				</div>
			</div>
		</div>
		<div class="clear-fix"></div>

		<div class="col-md-12 col-xs-12">
			<div class="center-margin">
				<button class="btn btn-primary btn-action-form" type="reset">Reset</button>
				<button type="submit" class="btn btn-success btn-action-form">Submit</button>
			</div>
		</div>
	</form>
</div>

<!-- jQuery -->

<script type="text/javascript">
	$(".select2").select2();

    $(".nominal_mata_uang").inputmask({
        prefix : 'Rp ',
        radixPoint: ',',
        groupSeparator: ".",
        alias: "numeric",
        autoGroup: true,
        digits: 0
    });
</script>
