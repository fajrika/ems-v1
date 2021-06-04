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
        <button class="btn btn-success btn-action-header" onClick="window.location.href='<?=site_url()?>/master/range/master_range_internet/add'">
            <i class="fa fa-repeat"></i>
            Refresh
        </button>
    </h2>
</div>
<div class="clearfix"></div>
</div>
<div class="x_content">
    <br>
    <form id="form" class="form-horizontal form-label-left" method="post" action="<?=site_url();?>/master/range/master_range_internet/save">
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Jenis Service *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <select name="service_jenis_id" class="form-control select2">
                        <option value="" selected="" disabled="">--Pilih Jenis Service--</option>
                        <?php
                            foreach ($select_jenis_service as $r) {
                                echo '<option value="'.$r['id'].'">'.$r['jenis_service'].'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Nama Paket *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="text" name="nama_paket" class="form-control" placeholder="Masukkan nama paket" required>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">ISP *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <select name="isp_id" class="form-control select2">
                        <option value="" selected="" disabled="">--Pilih ISP--</option>
                        <?php
                            foreach ($select_isp as $r) {
                                echo '<option value="'.$r['id'].'">'.$r['nama_isp'].'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Kapasitas (Mbps) *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="number" name="kapasitas" class="form-control" placeholder="Masukkan kapasitas (Mbps)" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Kuota (MB) *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="number" name="kuota" class="form-control" placeholder="Masukkan kuota (MB)" min="0" required>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Max Device *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="number" name="up_device" class="form-control" placeholder="Masukkan max device (MB)" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Biaya Langganan *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="text" name="nilai_langganan" class="form-control nominal_mata_uang" placeholder="Masukkan biaya langganan" required>
                </div>
            </div>
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