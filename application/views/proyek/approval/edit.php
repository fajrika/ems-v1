
<link href="<?= base_url() ?>vendors/switchery/dist/switchery.min.css" rel="stylesheet">
<script src="<?= base_url() ?>vendors/switchery/dist/switchery.min.js"></script>
<link href="<?= base_url() ?>vendors/select2/dist/css/select2.min.css" rel="stylesheet">
<script src="<?= base_url() ?>vendors/select2/dist/js/select2.min.js"></script>
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
    <div class="row">
        <div class="col-xs-4">
            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="text-align:center">Approve Percentage</label>
            <div style="text-align: center">
                <input class="knob" data-width="100" data-height="120" data-min="0" data-max="100" data-displayPrevious=true data-fgColor="#26B99A" value="<?= ($data->jumlah->approve / $data->jumlah->total) * 100 ?>" readonly>
            </div>
            <div class="clearfix"></div>
            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="text-align:center"><?= $data->jumlah->approve . "/" . $data->jumlah->total ?> Approve</label>
        </div>
        <div class="col-xs-4">
            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="text-align:center">Reject Percentage</label>
            <div style="text-align: center">
                <input class="knob" data-width="100" data-height="120" data-min="0" data-max="100" data-displayPrevious=true data-fgColor="#26B99A" value="<?= ($data->jumlah->reject / $data->jumlah->total) * 100 ?>" readonly>
            </div>
            <div class="clearfix"></div>
            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="text-align:center"><?= $data->jumlah->reject . "/" . $data->jumlah->total ?> Reject</label>
        </div>
        <div class="col-xs-4">
            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="text-align:center">Total Percentage</label>
            <div style="text-align: center">
                <input class="knob" data-width="100" data-height="120" data-min="0" data-max="100" data-displayPrevious=true data-fgColor="#26B99A" value="<?= (($data->jumlah->approve + $data->jumlah->reject) / $data->jumlah->total) * 100 ?>" readonly>
            </div>
            <div class="clearfix"></div>
            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="text-align:center"><?= ($data->jumlah->approve + $data->jumlah->reject) . "/" . $data->jumlah->total ?> Total</label>
        </div>
        <div class="clearfix"></div>
    </div>
    <br>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <ul class="list-unstyled timeline">
            <li>
                <div class="block">
                    <div class="tags">
                        <a class='tag' style='background:rgba(38,185,154,.88)'>
                            Mengetahui
                        </a>
                    </div>
                    <div class="block_content">
                        <h2 class="title">
                            <?php foreach ($data->mengetahui as $k => $v) : ?>
                                <a><?=$k==0?$v->daftar_user:", ".$v->daftar_user?></a>
                            <?php endforeach; ?>
                        </h2>
                    </div>
                </div>
            </li>
            <?php $is_pending = 0; 
                foreach ($data->wewenang as $k => $v) : 
            ?>
            <li>
                <div class="block">
                    <div class="tags">
                        <a class='tag' 
                            <?php $max = 0;
                            if ($v->approval_status_id == 0)
                                echo ("style='background:rgba(243,156,18,.88)'");
                            if ($v->approval_status_id == 1)
                                echo ("style='background:rgba(38,185,154,.88)'");
                            if ($v->approval_status_id == 2)
                                echo ("style='background:rgba(231,76,60,.88)'");
                            if ($v->approval_status_id == 3)
                                echo ("style='background:rgba(52,152,219,.88)'");
                            ?>
                        >
                            <?php
                            echo ("<span>Wewenang</span>");
                            ?>
                        </a>
                    </div>
                    <div class="block_content">
                        <h2 class="title">
                            <?php if($is_pending == 0):?>
                                <a><?="$v->status : $v->daftar_user"?></a>                            
                            <?php else: ?>
                                <a><?="Pending : $v->daftar_user"?></a>
                            <?php endif; ?>
                            <?php if($v->approval_status_id == 0) $is_pending = 1;?>
                        </h2>
                        <div class="byline">
                           <span> Status</span> Telah dikirim Email
                        </div>
                        <p class="excerpt">Deskripsi : <?= $v->description ?> </a>
                        </p>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>

            <li>
                <div class="block">
                    <div class="tags">

                        <a class='tag' 
                            <?php $max = 0;
                            if ($data->dokumen->status_dokumen_id == 0)
                                echo ("style='background:rgba(243,156,18,.88)'");
                            if ($data->dokumen->status_dokumen_id == 1)
                                echo ("style='background:rgba(38,185,154,.88)'");
                            if ($data->dokumen->status_dokumen_id == 2)
                                echo ("style='background:rgba(231,76,60,.88)'");
                            ?>
                            >
                            <?php
                            echo ("<span>Status Akhir</span>");
                            ?>
                        </a>
                    </div>
                    <div class="block_content">
                        <h2 class="title">
                            <?php if($data->dokumen->status_dokumen_id == 0):?>
                            Open :
                            <?php else:?>
                            Close :
                            <?php endif;?>
                            <?=$data->dokumen->status_dokumen?>
                        </h2>
                        </p>
                    </div>
                </div>
            </li>
        </ul>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="form-horizontal form-label-left">
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pt_id">Dokumen</label>
                <div class="col-md-7 col-sm-7 col-xs-10">
                    <input type="text" class="form-control col-md-7 col-xs-12" readonly value="<?= $data->dokumen->dokumen ?>">
                </div>
                <div class="col-md-2 col-sm-2 col-xs-2">
                    <button class="btn btn-primary col-md-12 col-xs-12"> Lampiran</button>
                </div>

            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pt_id">Kode Dokumen</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="text" class="form-control col-md-7 col-xs-12" readonly value="<?= $data->dokumen->dokumen_code ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pt_id">Request</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="text" class="form-control col-md-7 col-xs-12" readonly value="<?= $data->dokumen->request ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pt_id">Unit</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <textarea type="text" class="form-control col-md-7 col-xs-12" readonly><?= str_replace("|", "<br/>", $data->dokumen->units); ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pt_id">Nilai Dokumen</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="text" class="form-control col-md-7 col-xs-12" readonly value="<?= "Rp. " . number_format($data->dokumen->dokumen_nilai) ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pt_id">Tanggal Request</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="text" class="form-control col-md-7 col-xs-12" readonly value="<?= $data->dokumen->tgl_tambah ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pt_id">Tanggal Closed</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input type="text" class="form-control col-md-7 col-xs-12" readonly value="<?= $data->dokumen->tgl_closed ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pt_id">Keterangan Pengajuan</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <textarea type="text" class="form-control col-md-7 col-xs-12" readonly><?= $data->dokumen->description ?></textarea>
                </div>
            </div>
            <?php if ($data->izin == 2) : ?>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pt_id">Keterangan Approval</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <textarea id="deskripsi" type="text" class="form-control col-md-7 col-xs-12" name="description"></textarea>
                    </div>
                </div>
            <?php endif; ?>

            <div id="btn-action" class="col-lg-9 col-md-9 col-sm-9 col-lg-offset-3 col-md-offset-3 col-sm-offset-3">
                <div class="form-group" style="margin-top:20px">
                    <div class="center-margin">
                        <?php
                        $app_id = $this->input->get("id");
                        $dokumen_id = $this->db->select('dokumen_id')->where('id', $app_id)->get('approval');
                        if ($dokumen_id->num_rows() > 0) {
                            $status_pemutihan = $this->db
                                ->join('pemutihan_unit', 'pemutihan.id = pemutihan_unit.pemutihan_id')
                                ->join(
                                    't_tagihan_air', 
                                    'pemutihan_unit.unit_id = t_tagihan_air.unit_id AND pemutihan_unit.periode = t_tagihan_air.periode', 
                                    'left'
                                )
                                ->join(
                                    't_tagihan_lingkungan', 
                                    'pemutihan_unit.unit_id = t_tagihan_lingkungan.unit_id AND pemutihan_unit.periode = t_tagihan_lingkungan.periode', 
                                    'left'
                                )
                                ->where('pemutihan.id', $dokumen_id->row()->dokumen_id)
                                ->where('pemutihan.status', 1)
                                ->where('(t_tagihan_air.status_tagihan=0 OR t_tagihan_lingkungan.status_tagihan=0)')
                                ->get('pemutihan');
                            if ($status_pemutihan->num_rows() > 0) {
                            ?>
                                <a data-toggle="modal" data-target="#modal_delete" class="btn btn-danger"><i class="fa fa-remove"></i> Delete</a>
                            <?php
                            }
                        }
                        ?>
                        <?php if ($data->izin == 2) : ?>
                            <a data-toggle="modal" data-target="#modal2" class="btn btn-warning"><i class="fa fa-refresh"></i> Reject</a>
                            <a data-toggle="modal" data-target="#modal" class="btn btn-success"><i class="fa fa-check"></i> Approve</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="x_content">
    <div class="modal fade" id="modal" data-backdrop="static" data-keyboard="false">
        <div class="" style="width:35%;margin:auto">
            <div class="modal-content" style="margin-top:100px;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" style="text-align:center;" id="header-modal">
                        Approve
                        <span class="grt"></span>
                    </h4>
                </div>
                <div class="modal-body">
                    Apakah anda yakin untuk
                    <a id="body-modal">
                        Approve
                    </a>
                    Dokumen ini ?
                </div>
                <div class="modal-footer" style="margin:0px; border-top:0px; text-align:center;">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="delete_cancel_link">Close</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal"  id="btn-approve">Approve</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="x_content">
    <div class="modal fade" id="modal2" data-backdrop="static" data-keyboard="false">
        <div class="" style="width:35%;margin:auto">
            <div class="modal-content" style="margin-top:100px;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" style="text-align:center;" id="header-modal">
                        Reject
                        <span class="grt"></span>
                    </h4>
                </div>
                <div class="modal-body">
                    Apakah anda yakin untuk
                    <a id="body-modal">
                        Reject
                    </a>
                    Dokumen ini ?
                </div>
                <div class="modal-footer" style="margin:0px; border-top:0px; text-align:center;">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="delete_cancel_link">Close</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal" id="btn-reject">Reject</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="x_content">
    <div class="modal" id="modal_delete" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" id="modal-dialog" style="width: 20%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Konfirmasi</h4>
                </div>
                <div class="modal-body" id="modal-body">Data akan dihapus permanen!</div>
                <div class="modal-footer" id="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" id="delete_app">Yes Delete</button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<script src="<?= base_url() ?>vendors/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>
<!-- jQuery Knob -->
<script src="<?= base_url() ?>vendors/jquery-knob/dist/jquery.knob.min.js"></script>
<script>
    $(function() {
        var id_approval = <?= $this->input->get("id") ?>;
        //selected
        function notif(title, text, type) {
            new PNotify({
                title: title,
                text: text,
                type: type,
                styling: 'bootstrap3'
            });
        }
        $("#btn-approve").click(function() {
            $.ajax({
                type: "POST",
                data: {
                    id: id_approval,
                    deskripsi: $("#deskripsi").val()
                },
                url: '<?= site_url() ?>/P_approval/ajax_approve',
                dataType: "json",
                success: function(data) {
                    if (data.status == 1) {
                        $("#btn-action").hide();
                        notif('Sukses', data.message, 'success');
                    }else{
                        notif('Gagal', data.message, 'alert');
                    }
                    setTimeout(function () { 
                        if(!alert('Halaman akan di refresh otomatis untuk update data!')){
                            window.open(window.location.href,'_self');
                        }
                    }, 5 * 1000);
                }
            });
        });
        $("#btn-reject").click(function() {
            $.ajax({
                type: "POST",
                data: {
                    id: id_approval,
                    deskripsi: $("#deskripsi").val()
                },
                url: '<?= site_url() ?>/P_approval/ajax_reject',
                dataType: "json",
                success: function(data) {
                    if (data.status == 1) {
                        $("#btn-action").hide();
                        notif('Sukses', data.message, 'success');
                    }else{
                        notif('Gagal', data.message, 'alert');
                    }
                    setTimeout(function () { 
                        if(!alert('Halaman akan di refresh otomatis untuk update data!')){
                            window.open(window.location.href,'_self');
                        }
                    }, 5 * 1000);
                }
            });
        });
        $('.select2').select2();

        // delete approval
        $(document).on('click', '#delete_app', function(e){
            e.preventDefault();
            $.ajax({
                url: "<?=site_url('p_approval/delete_approval');?>",
                cache: false,
                type: "POST",
                data: {id: id_approval},
                dataType: 'json',
                success: function(data) {
                    if (data.status == 1) {
                        $("#btn-action").hide();
                        notif('Sukses', data.message, 'success');
                        $('#modal_delete').modal('hide');
                    }else{
                        notif('Gagal', data.message, 'alert');
                    }

                    setTimeout(function(){
                        if(!alert('Halaman akan di refresh otomatis untuk update data!')){
                            window.location.href="<?=site_url('P_approval');?>";
                        }
                    }, 2000);
                }
            });
        });
    });
</script>