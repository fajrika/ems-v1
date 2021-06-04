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
        <button class="btn btn-success btn-action-header" onClick="window.location.href='<?=site_url()?>/P_master_isp/edit?id=<?=$this->input->get('id')?>'">
            <i class="fa fa-repeat"></i>
            Refresh
        </button>
    </h2>
</div>
<div class="clearfix"></div>
</div>

<div class="x_content">
    <br>
    <form id="form" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post" action="<?=site_url(); ?>/P_master_isp/save_edit?id=<?=$this->input->get('id')?>">
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Nama ISP *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="text" name="nama_isp" class="form-control can_edit" disabled placeholder="Masukkan Nama ISP" value="<?=$dataSelect->nama_isp?>" required>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Bandwidth (MB) *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="number" name="bandwidth" class="form-control can_edit" disabled placeholder="Masukkan Bandwidth (MB)" min="0" value="<?=$dataSelect->bandwidth?>" required>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Persen Mitra *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="number" name="persen_mitra" class="form-control can_edit" disabled placeholder="Masukkan Persen Mitra" min="0" max="100" value="<?=$dataSelect->persen_mitra?>" required>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Nilai Kabel *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="text" name="nilai_kabel" class="form-control can_edit nominal_mata_uang" disabled placeholder="Masukkan Nilai Kabel" value="<?=$dataSelect->nilai_kabel?>" required>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Nilai Pemasangan *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="text" name="nilai_pemasangan" class="form-control can_edit nominal_mata_uang" disabled placeholder="Masukkan Nilai Pemasangan" value="<?=$dataSelect->nilai_pemasangan?>" required>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Nilai Lain-lain *</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="text" name="nilai_lain_lain" class="form-control can_edit nominal_mata_uang" disabled placeholder="Masukkan Nilai Lain-lain" value="<?=$dataSelect->nilai_lain_lain?>" required>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
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
                        <option value="" selected disabled>--Pilih Status--</option>
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
<!-- jQuery -->
<script type="text/javascript">
    $(".select2").select2();
    
    var nama_isp = "<?=$dataSelect->nama_isp?>";
    var bandwidth = "<?=$dataSelect->bandwidth?>";
    var persen_mitra = "<?=$dataSelect->persen_mitra?>";
    var nilai_kabel = "<?=$dataSelect->nilai_kabel?>";
    var nilai_pemasangan = "<?=$dataSelect->nilai_pemasangan?>";
    var nilai_lain_lain = "<?=$dataSelect->nilai_lain_lain?>";
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

                $('#form input[name="nama_isp"]').val(nama_isp);
                $('#form input[name="bandwidth"]').val(bandwidth);
                $('#form input[name="persen_mitra"]').val(persen_mitra);
                $('#form input[name="nilai_kabel"]').val(nilai_kabel);
                $('#form input[name="nilai_pemasangan"]').val(nilai_pemasangan);
                $('#form input[name="nilai_lain_lain"]').val(nilai_lain_lain);
                
                $('#form textarea[name="keterangan"]').val(keterangan);

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
