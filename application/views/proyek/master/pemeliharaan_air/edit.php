<link href="<?= base_url() ?>vendors/select2/dist/css/select2.min.css" rel="stylesheet">
<link href="<?=base_url(); ?>vendors/switchery/dist/switchery.min.css" rel="stylesheet">
<script src="<?= base_url() ?>vendors/select2/dist/js/select2.min.js"></script>
<script src="<?=base_url(); ?>vendors/switchery/dist/switchery.min.js"></script>
<div id="modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Detail Log</h4>
			</div>
			<div class="modal-body">
				<table class="table table-striped jambo_table">
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
<div style="float:right">
    <h2>
        <button class="btn btn-dark" onClick="window.location.href = '<?= substr(current_url(), 0, strrpos(current_url(), "/")) ?>'">
            <i class="fa fa-arrow-left"></i> Back
        </button>
        <button class="btn btn-dark" onClick="window.location.href='<?= site_url('P_master_pemeliharaan_air/edit?id='.$this->input->get('id')) ?>'">
            <i class="fa fa-repeat"></i> Refresh
        </button>
    </h2>
</div>
<div class="clearfix"></div>
</div>
<div class="x_content">
    <br>
    <form id="form-cara-bayar" autocomplete="off" class="form-horizontal form-label-left">
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="code">Kode<span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" id="code" name="code" required class="form-control col-md-7 col-xs-12" placeholder="Masukkan Nama" value='<?=$data2->code?>' readonly>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Nama<span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" id="name" name="name" required class="form-disabled form-control col-md-7 col-xs-12" placeholder="Masukkan Nama" value='<?=$data2->name?>' disabled>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ukuran_pipa">Ukuran Pipa<span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" id="ukuran_pipa" name="ukuran_pipa" required class="form-disabled form-control col-md-7 col-xs-12" placeholder="Masukkan Ukuran Pipa Beserta Satuan" value='<?=$data2->ukuran_pipa?>' disabled>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nilai">Biaya Pemeliharaan<span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <span class="form-control-feedback left" aria-hidden="true">Rp.</span>
                <input type="text" id="nilai" name="nilai" required class="form-disabled text-right form-control col-md-7 col-xs-12 currency" style="padding-left: 50px;" value='<?=number_format($data2->nilai)?>' disabled>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nilai_pemasangan">Biaya Pemasangan<span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <span class="form-control-feedback left" aria-hidden="true">Rp.</span>
                <input type="text" id="nilai_pemasangan" name="nilai_pemasangan" required class="form-disabled text-right form-control col-md-7 col-xs-12 currency_pemasangan" style="padding-left: 50px;" value='<?=number_format($data2->nilai_pemasangan)?>' disabled>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">PPN Pemasangan?</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="checkbox" 
                    class="form-control js-switch form-disabled col-md-7 col-xs-12" 
                    id="nilai_ppn_pemasangan" 
                    name="nilai_ppn_pemasangan" 
                    <?= $data2->nilai_ppn_pemasangan !==0 ? 'checked' : ''; ?> 
                    /> Yes
            </div>
        </div>

        <div class="form-group">
            <label for="description" class="control-label col-md-3 col-sm-3 col-xs-12">Keterangan</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <textarea id="description" class="form-disabled form-control col-md-7 col-xs-12" type="text" name="description" placeholder="Masukkan Keterangan jika diperlukan" disabled><?=$data2->description?></textarea>
            </div>
        </div>
		<div class="col-md-12">
        	<input id="btn-edit" class="btn btn-primary col-md-1 col-md-offset-5" value="Edit">
        	<button id="btn-update" class="btn btn-success col-md-1 col-md-offset-5">Update</button>
			<input id="btn-cancel"class="btn btn-danger col-md-1" value="cancel">
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
        <table class="table table-striped jambo_table tableDT">
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
    function formatNumber(data) {
        data = data + '';
        data = data.replace(/,/g, "");
        data = parseInt(data) ? parseInt(data) : 0;
        data = data.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
        return data;
    }

    function notif(title, text, type) {
        new PNotify({
            title: title,
            text: text,
            type: type,
            styling: 'bootstrap3'
        });
    }

    function unformatNumber(data) {
        data = data + '';
        return data.replace(/,/g, "");
    }
    $(function() {
		$("#btn-update").hide();
		$("#btn-cancel").hide();
		$("#btn-edit").click(function(){
			$("#btn-update").show();
			$("#btn-cancel").show();
			$("#btn-edit").hide();
			$(".form-disabled").attr('disabled',false);
		});
		$("#btn-cancel").click(function(){
			$("#btn-update").hide();
			$("#btn-cancel").hide();
			$("#btn-edit").show();
			$(".form-disabled").attr('disabled',true );
		});

        $("#name").keyup(function() {
            $("#code").val($("#name").val().toLowerCase().replace(/ /g, '_'));
        });
        $(".currency").keyup(function() {
            $(this).val(formatNumber($(this).val()));
        });
        $(".currency_pemasangan").keyup(function() {
            $(this).val(formatNumber($(this).val()));
        });
        $("form").submit(function(e) {
            e.preventDefault();
            $('.currency').val(unformatNumber($(".currency").val()));
            $('.currency_pemasangan').val(unformatNumber($(".currency_pemasangan").val()));

            if($('#nilai_ppn_pemasangan').prop('checked')){
                nilai_ppn_pemasangan = 1;
            }else{
                nilai_ppn_pemasangan = 0;
            }

            $.ajax({
                url: "<?= site_url('P_master_pemeliharaan_air/ajax_edit') ?>",
                cache: false,
                type: "POST",
                data: $("form").serialize() + '&nilai_ppn_pemasangan='+nilai_ppn_pemasangan+"&id=<?=$this->input->get('id')?>",
                dataType: "json",
                success: function(data) {
                    if (data.status == 1) {
                        notif('Sukses', data.message, 'success');
                        window.location.href="<?= site_url('P_master_pemeliharaan_air/edit?id='.$this->input->get('id')) ?>";
                    } else {
                        notif('Gagal', data.message, 'danger');
                    }
                }
            });
            $('.currency').val(formatNumber($(".currency").val()));
        })
		$(".btn-modal").click(function(){
            url = '<?=site_url('core/get_log_detail');?>';
            console.log($(this).attr('data-transfer'));
            console.log($(this).attr('data-type'));
            $.ajax({
                type: "POST",
                data: {id:$(this).attr('data-transfer'),type:$(this).attr('data-type')},
                url: url,
                dataType: "json",
                success: function(data){
                    console.log(data);
                    // var items = []; 
                    // $("#changeJP").attr("style","display:none");
                    // $("#saveJP").removeAttr('style');
                    // $("#jabatan").removeAttr('disabled');
                    // $("#jabatan")[0].innerHTML = "";
                    // $("#project")[0].innerHTML = "";
                    // $("#jabatan").append("<option value='' selected disabled>Pilih Jabatan</option>");
                    console.log($(this).attr('data-type'));
                    $("#dataModal").html("");
                    if (data[data.length-1] == 2) {
                        console.log(data[0]);
                        for (i = 0; i < data[0].length; i++) { 
                            $.each(data[1], function(key, val){
                                if(val.name == data[0][i].name){
                                    console.log(val.name);
                                    $("#dataModal").append("<tr><th>"+data[0][i].name+"</th><td>"+val.value+"</td><td>"+data[0][i].value+"</td></tr>");        
                                }
                            }); 
                        }
                    } else {
                        $.each(data, function(key, val) {
                            if (data[data.length-1] == 1) {
                                console.log(data);
                                if(val.name)
                                    $("#dataModal").append("<tr><th>"+val.name.toUpperCase()+"</th><td></td><td>"+val.value+"</td></tr>");
                            } else if (data[data.length-1] == 2) {
                                
                            } else if (data[data.length-1] == 3) {
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
</script>
<style type="text/css">
    .btn { padding: 7px 12px; font-size: 13px;}
    .btn:focus { outline: none;}
    .table thead th{ border: 1px solid #556c81 !important; }
    table.dataTable thead .sorting_asc:after { font-size: 11px; }
    div.dataTables_wrapper { width: 99%; height: 100%; margin: 0 auto; }
    .table>thead>tr>th { border-bottom: 1px solid #556c81 !important; padding: 10px 9px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .table>tbody>tr>td { border: 1px solid #ddd; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { padding-top: 2px; font-size: 12px; }
    .form-control, .select2-container--default .select2-selection--single { font-size: 12px; box-shadow: none; border-radius: 0px !important; }
    .dataTables_length select,
    .dataTables_filter input { padding: 6px 12px; border: 1px solid #ccc; border-radius: 0px; }
    tfoot input::placeholder { font-size:11px; }
    textarea { resize: vertical; }
</style>