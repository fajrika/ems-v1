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
        <button class="btn btn-success btn-action-header" onClick="window.location.href='<?=site_url()?>/master/range/master_range_internet/edit?id=<?=$this->input->get('id')?>'">
            <i class="fa fa-repeat"></i>
            Refresh
        </button>
    </h2>
</div>
<div class="clearfix"></div>
</div>

<div class="x_content">
    <br>
    <form id="form" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post" action="<?=site_url(); ?>/master/range/master_range_internet/edit?id=<?=$this->input->get('id')?>">
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Jenis Service *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <select name="service_jenis_id" class="form-control can_edit select2" disabled>
                        <option value="" disabled="">--Pilih Jenis Service--</option>
                        <?php
                            foreach ($select_jenis_service as $r) {
                                echo '<option value="'.$r['id'].'"'.($dataSelect->service_jenis_id==$r['id'] ? ' selected' : '').'>'.$r['jenis_service'].'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Nama Paket *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="text" name="nama_paket" value="<?=$dataSelect->nama_paket?>" class="form-control can_edit" disabled placeholder="Masukkan nama paket" required>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">ISP *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <select name="isp_id" class="form-control can_edit select2" disabled>
                        <option value="" disabled="">--Pilih ISP--</option>
                        <?php
                            foreach ($select_isp as $r) {
                                echo '<option value="'.$r['id'].'"'.($dataSelect->isp_id==$r['id'] ? ' selected' : '').'>'.$r['nama_isp'].'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Kapasitas (Mbps) *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="number" name="kapasitas" value="<?=$dataSelect->kapasitas?>" class="form-control can_edit" disabled placeholder="Masukkan kapasitas (Mbps)" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Kuota (MB) *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="number" name="kuota" value="<?=$dataSelect->kuota?>" class="form-control can_edit" disabled placeholder="Masukkan kuota (MB)" min="0" required>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Max Device *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="number" name="up_device" value="<?=$dataSelect->up_device?>" class="form-control can_edit" disabled placeholder="Masukkan max device (MB)" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Biaya Langganan *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="text" name="nilai_langganan" value="<?=$dataSelect->nilai_langganan?>" class="form-control can_edit nominal_mata_uang" disabled placeholder="Masukkan biaya langganan" required>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Keterangan *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <textarea name="keterangan" class="form-control can_edit" disabled rows="3" placeholder='Masukkan Keterangan' required><?=$dataSelect->keterangan?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Status</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <select name="active" class="form-control can_edit select2" disabled required>
                        <option value="" disabled>--Pilih Status--</option>
                        <option value="1" <?=$dataSelect->active==1?'selected':''?>>Aktif</option>
                        <option value="0" <?=$dataSelect->active==0?'selected':''?>>Tidak Aktif</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="clear-fix"></div>
        
		<div class="col-md-12 col-xs-12">
			<div class="center-margin">
                <input id="btn-update" class="btn btn-success btn-action-form" value="Edit">
                <input id="btn-cancel" class="btn btn-danger btn-action-form" value="Cancel" style="display:none">
            </div>
        </div>

        <div id="isi_tabel" class="col-md-12">
        </div>

    </form>
</div>
<!-- modals -->
    <!-- Large modal -->
    <div id="modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Detail Log</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-striped jambo_table bulk_action">
                        <thead>
                            <tr>
                                <th>Point Detail</th>
                                <th>Before</th>
                                <th>After</th>
                            </tr>
                        </thead>
                        <tbody id="dataModal">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="x_panel">
    <div class="x_title">
        <h2>Log</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <br>
        <table class="table table-striped jambo_table bulk_action">
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu</th>
                <th>User</th>
                <th>Status</th>
                <th>Detail</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $i = 0;
            foreach ($data as $key => $v){
                $i++;
                echo('<tr>');
                    echo("<td>$i</td>");
                    echo("<td>$v[date]</td>");
                    echo("<td>$v[name]</td>");
                    echo("<td>");
                        if($v['status']==1)
                            echo("Tambah");
                        elseif($v['status']==2)
                            echo("Edit");
                        else
                            echo("Hapus");
                    echo("</td>");
                    echo("
                    <td class='col-md-1'>
                        <a class='btn-modal btn btn-sm btn-primary col-md-12' data-toggle='modal' data-target='#modal' data-transfer='$v[id]' data-type='$v[status]'>
                            <i class='fa fa-pencil'></i>
                        </a>
                    </td>
                ");
                echo('</td></tr>');                
            }
        ?>
        </tbody>
    </table>
    </div>
</div>
<!-- jQuery -->
<script type="text/javascript">
    $(".select2").select2();
    
    var service_jenis_id = "<?=$dataSelect->service_jenis_id?>";
    var nama_paket = "<?=$dataSelect->nama_paket?>";
    var isp_id = "<?=$dataSelect->isp_id?>";
    var kapasitas = "<?=$dataSelect->kapasitas?>";
    var kuota = "<?=$dataSelect->kuota?>";
    var up_device = "<?=$dataSelect->up_device?>";
    var nilai_langganan = "<?=$dataSelect->nilai_langganan?>";
    var keterangan = "<?=$dataSelect->keterangan?>";
    var active = "<?=$dataSelect->active?>";

    $(function() {
        $("#btn-update").click(function () {
            disableForm = 0;
            $(".disabled-form").removeAttr("disabled");

            $("#btn-cancel").removeAttr("style");

            $("#btn-update").val("Update");
            setTimeout(function(){ 
                $('.can_edit').each(function()
                {
                    $(this).removeAttr("disabled");
                });

                $("#btn-update").attr("type", "submit"); 
            }, 100);
        });
        $("#btn-cancel").click(function () {
            disableForm = 1;
            $(".disabled-form").attr("disabled", "");

            $("#btn-cancel").attr("style", "display:none");

            $("#btn-update").val("Edit")

            $('.can_edit').each(function()
            {
                $(this).attr("disabled", "");

                $('#form input[name="nama_paket"]').val(nama_paket);
                $('#form input[name="kapasitas"]').val(kapasitas);
                $('#form input[name="kuota"]').val(kuota);
                $('#form input[name="up_device"]').val(up_device);
                $('#form input[name="nilai_langganan"]').val(nilai_langganan);
                
                $('#form textarea[name="keterangan"]').val(keterangan);

                $('#form select[name="service_jenis_id"]').val(service_jenis_id).trigger('change');
                $('#form select[name="isp_id"]').val(isp_id).trigger('change');
                $('#form select[name="active"]').val(active).trigger('change');
            });
            $("#btn-update").removeAttr("type");
        });
        $(".btn-modal").click(function(){
            url = '<?=site_url()?>/core/get_log_detail';
            console.log($(this).attr('data-transfer'));
            console.log($(this).attr('data-type'));
            $.ajax({
                type: "POST",
                data: {id:$(this).attr('data-transfer'),type:$(this).attr('data-type')},
                url: url,
                dataType: "json",
                success: function(data){
                    $("#dataModal").html("");
                    if(data[data.length-1] == 2){
                        console.log(data[0]);
                        for (i = 0; i < data[0].length; i++) { 
                            $.each(data[1], function(key, val){
                                if(val.name == data[0][i].name){
                                    console.log(val.name);
                                    $("#dataModal").append("<tr><th>"+data[0][i].name+"</th><td>"+val.value+"</td><td>"+data[0][i].value+"</td></tr>");        
                                }
                            }); 
                        }
                    }else{
                        $.each(data, function(key, val){
                            if(data[data.length-1] == 1){
                                console.log(data);
                                if(val.name)
                                    $("#dataModal").append("<tr><th>"+val.name.toUpperCase()+"</th><td></td><td>"+val.value+"</td></tr>");
                            }else if(data[data.length-1] == 2){
                                
                            }else if(data[data.length-1] == 3){
                                console.log(data);
                                if(val.name)
                                    $("#dataModal").append("<tr><th>"+val.name.toUpperCase()+"</th><td>"+val.value+"</td><td></td></tr>");
                            }
                        });
                    }
                    
                }
            });
        });
    });

    $(".nominal_mata_uang").inputmask({
        prefix : 'Rp ',
        radixPoint: ',',
        groupSeparator: ".",
        alias: "numeric",
        autoGroup: true,
        digits: 0
    });
</script>
