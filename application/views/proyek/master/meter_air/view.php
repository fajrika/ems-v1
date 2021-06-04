
<div style="float:right">
    <h2>
        <a href="<?=site_url('Master_meter_air/add');?>" class="btn btn-primary">
            <i class="fa fa-pencil"></i> Tambah Data
        </a>
        <a href="<?=site_url('Master_meter_air');?>" class="btn btn-success">
            <i class="fa fa-repeat"></i> Refresh
        </a>
    </h2>
</div>
<div class="clearfix"></div>
</div>
<div class="x_content">
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <table class="table table-striped jambo_table" id="tableDTServerSite" style="width:100%; border-collapse: collapse !important;">
                    <tfoot id="tfoot" style="display: table-header-group">
                        <tr>
                            <th class="hide-search">#</th>
                            <th>Unit</th>
                            <th>Kode</th>
                            <th>Nama Meteran Air</th>
                            <th>No. Seri Meter</th>
                            <th>Ukuran Meteran</th>
                            <th>Status</th>
                            <th class="hide-search">Option</th>
                        </tr>
                    </tfoot>
                    <thead>
                        <tr>
                            <th class="no-sort">#</th>
                            <th>Unit</th>
                            <th>Kode</th>
                            <th>Nama Meteran Air</th>
                            <th>No. Seri Meter</th>
                            <th>Ukuran Meteran</th>
                            <th class="no-sort">Status</th>
                            <th class="no-sort">Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($meter_air->num_rows() > 0) {
                            foreach ($meter_air->result() as $d) {

                                if ($d->status_meter==1) {
                                    $sts = 'Belum Terpasang';
                                } else if ($d->status_meter==2) {
                                    $sts = 'Terpasang';
                                } else if ($d->status_meter==3) {
                                    $sts = 'Rusak';
                                } else {
                                    $sts = 'Tidak Aktif';
                                }
                                echo "
                                <tr>
                                    <td></td>
                                    <td style='white-space: nowrap;'>".$d->nama_unit."</td>
                                    <td>".$d->kode."</td>
                                    <td>".$d->nama_meter_air."</td>
                                    <td>".$d->no_seri_meter."</td>
                                    <td>".$d->ukuran_meter_air."</td>
                                    <td>".$sts."</td>
                                    <td>
                                        <a href='".site_url('master-meter-air/edit/'.$d->id)."' class='btn btn-sm btn-dark' id='btn_edit'>
                                            <i class='fa fa-edit'></i> Edit
                                        </a>
                                        <a href='".site_url('master-meter-air/delete/'.$d->id)."' class='btn btn-sm btn-danger' id='btn-hapus'>
                                            <i class='fa fa-trash'></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                                ";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<div class="modal" id="ModalShow" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" id="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modal-title">Konfirmasi</h4>
            </div>
            <div class="modal-body" id="modal-body"></div>
            <div class="modal-footer" id="modal-footer"></div>
        </div>
    </div>
</div>

<div class="modal fade modal-upload" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Upload Master Meter Air</h4>
            </div>
            <div class="modal-body">
                <p>
                    <form action="<?=site_url('master_meter_air/load_data');?>" id="FormUploadMeter" class="form-horizontal form-label-left" enctype="multipart/form-data" novalidate>
                        <div class="field item form-group">
                            <label class="control-label col-md-4 col-xs-12">Pilih Dokumen<span class="required">*</span></label>
                            <div class="col-md-6 col-xs-12">
                                <input type="file" name="dokumen" id="dokumen" class="form-control" data-container="body" data-toggle="popover" data-placement="top" data-content="Allowed Size : 5MB per file" data-trigger="hover" data-original-title="" title="">
                                <small>Pilih .xls/.xlsx File</small>
                            </div>
                        </div>
                        <div class="field item form-group text-center">
                            <button type="button" class="btn btn-success" id="btnLoad">Load Data</button>
                            <button type="button" class="btn btn-primary" id="btnSave">Simpan Data</button>
                        </div>
                    </form>

                    <div id="ShowNotify" style="display: none;">
                        <div class="x_content bs-example-popovers">
                            <div class="alert alert-danger alert-dismissible " role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <div id="text-alert"></div>
                            </div>
                        </div>
                    </div>
                </p>

                <br>
                <p>
                    <table class="table table-striped jambo_table" id="tb-upload" style="width:100%; border-collapse: collapse !important;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kode</th>
                                <th>Nama Meteran Air</th>
                                <th>No. Seri Meter</th>
                                <th>Ukuran Meteran</th>
                                <th>Tgl. Awal Pakai</th>
                                <th>Tgl. Akhir Pakai</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </p>
                <br>
                <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php
$download_template = "<a href='../files/meter_air/template_master_air.xlsx' class='btn btn btn-success'>Download Template</a>";
$button_upload = "<button type='button' class='btn btn-danger' data-toggle='modal' data-target='.modal-upload'><i class='fa fa-upload'></i> Upload Master Meter</button>";
?>
<script>
    function confirm_modal(id) {
        jQuery('#modal_delete_m_n').modal('show', {
            backdrop: 'static',
            keyboard: false
        });
        document.getElementById('delete_link_m_n').setAttribute("href", "<?= site_url('P_master_customer/delete?id=" + id + "'); ?>");
        document.getElementById('delete_link_m_n').focus();
    }

    $(document).ready(function(){
        // Apply the search
        var table = $('#tableDTServerSite').DataTable({
            // "serverSide": true,
            "stateSave" : false,
            "bAutoWidth": true,
            "bDestroy" : true,
            "oLanguage": {
                "sSearch": "Search : ",
                "sLengthMenu": "Show _MENU_ &nbsp;&nbsp;<?= $button_upload.' '.$download_template; ?>",
                "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
                "sInfoFiltered": "(filtered from _MAX_ total entries)",
                "sZeroRecords": "<center>Data tidak ditemukan</center>",
                "sEmptyTable": "No data available in table",
                "sLoadingRecords": "Please wait - loading...",
                "oPaginate": {
                    "sPrevious": "Prev",
                    "sNext": "Next"
                }
            },
            "aaSorting": [[ 0, "desc" ]],
            "columnDefs": [
                {"targets": 'no-sort', "orderable": false}
            ],
            // "ajax": {
            //     url : "<?=site_url("P_master_customer/ajax_get_view")?>",
            //     cache: false,
            //     type: "post",
            // }
        });

        table.columns().every(function(){
            var that = this;
            $('input', this.footer()).on('keyup change', function(){
                if ( that.search() !== this.value ) {
                    that
                        .search( this.value )
                        .draw();
                }
            });
        });

        table.on('order.dt search.dt', function(){
            table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            });
        }).draw();

        // table upload
        var tblupload = $('#tb-upload').DataTable({
            "oLanguage": {
                "sSearch": "Search : ",
                "sLengthMenu": "_MENU_ &nbsp;&nbsp; <span id='doubleData' style='color: red; font-weight: 500;'></span>",
                "sInfo": "_START_ to _END_ of _TOTAL_",
                "sInfoFiltered": "(filtered from _MAX_ total entries)",
                "sZeroRecords": "<center>Data tidak ditemukan</center>",
                "sEmptyTable": "No data available in table",
                "sLoadingRecords": "Please wait - loading...",
                "oPaginate": {
                    "sPrevious": "Prev",
                    "sNext": "Next"
                }
            },
        });

        // process upload
        $(document).on('click', '#btnLoad', function(e){
            e.preventDefault();
            var formData = new FormData($('#FormUploadMeter')[0]);
            // $('#btnLoad').html("Please Wait...").attr('disabled', true);
            $.ajax({
                url: $('#FormUploadMeter').attr('action'),
                type: "POST",
                data: formData,
                cache: false,
                dataType: 'json',
                processData: false, 
                contentType: false, 
                success: a => {
                    if(a.status == 1) {
                        // $('.modal-upload').modal('hide');
                        // alert(a.msg);
                        tblupload.clear().draw();
                        let no = 1;
                        upload = a.uploaded.map(b => {
                            tblupload.row.add([
                                no++,
                                b.kode,
                                b.nama_meter_air,
                                b.no_seri_meter,
                                b.ukuran_meter_air,
                                b.tgl_meter_awal,
                                b.tgl_meter_akhir,
                            ]).draw(false);
                        });
                        $('#doubleData').html(a.double_data);
                    } else {
                        $('#ShowNotify').show();
                        $('#text-alert').html(a.msg);
                        $('#doubleData').html(a.double_data);
                        setTimeout(function(){
                            $('#ShowNotify').hide();
                            $('#text-alert').html('');
                        }, 2500);
                    }
                }
            });
        });

        // save data
        $(document).on('click', '#btnSave', function(e){
            e.preventDefault();
            if( document.getElementById("dokumen").files.length == 0 ){
                alert("No files selected");
            } else {
                var formData = new FormData($('#FormUploadMeter')[0]);
                $.ajax({
                    url: "<?=site_url('master_meter_air/upload');?>",
                    type: "POST",
                    data: formData,
                    cache: false,
                    dataType: 'json',
                    processData: false, 
                    contentType: false, 
                    success: a => {
                        if(a.status == 1) {
                            alert(a.msg);
                            tblupload.clear().draw();
                            $('#doubleData').attr('style', 'color: green;').html(a.double_data);
                            setTimeout(() => {
                                $('#doubleData').html('');
                            }, 4000);
                            $('#FormUploadMeter').each(function(){
                                this.reset();
                            });
                        } else {
                            alert(a.msg);
                            $('#doubleData').attr('style', 'color: red;').html(a.double_data);
                            setTimeout(() => {
                                $('#doubleData').html('');
                            }, 4000);
                        }
                    }
                });
            }
        });

        // reload on close modal
        $('.modal-upload').on('hidden.bs.modal', function () {
            location.reload();
            $('#doubleData').html('');
        });

        $('#tableDTServerSite tfoot th').each( function () {
            var title = $(this).text();
            $(this).html("<input type='text' class='form-control form-control-sm' placeholder='"+title+"' />");
        });

        // delete row
        $(document).on('click', '#btn-hapus', function(e){
            e.preventDefault();
            href = $(this).attr('href');
            msg  = '<p>Yakin ingin menghapus data ini ?</p>';
            btn_save = '<a href="'+href+'" id="YesDelete" class="btn btn-sm btn-primary">Hapus</a>';
            show_modal(msg, btn_save, 'modal-sm');
        });
    });

    function show_modal(msg, btn_save=null, modal_size) {
        close = '<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>';
        if (btn_save==null) {
            button = close;
        } else {
            button = btn_save + close;
        }

        $('#modal-dialog').addClass(modal_size);
        $('#modal-footer').html(button);
        $('#modal-body').html(msg);
        $('#ModalShow').modal('show');
    }

    $(document).on('click', '#YesDelete', function(e){
        e.preventDefault();
        $.ajax({
            url: $(this).attr('href'),
            cache: false,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                show_modal(data.pesan);
                window.location.href=data.link_href;
            }
        });
    });

    // modal show edit
    $(document).on('click', '#btn_edit', function(e){
        e.preventDefault();
        save = '<a href="'+$(this).attr('href')+'" id="save_edit" class="btn btn-sm btn-primary">Simpan Data</a>';
        close = '<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>';

        $('#modal-title').html('Edit Data');
        $('#modal-dialog').addClass('modal-md');
        $('#modal-footer').html(save + close);
        $('#modal-body').load($(this).attr('href'));
        $('#ModalShow').modal('show');
    });
    // proses save edit
    $(document).on('click', '#save_edit', function(e){
        e.preventDefault();
        $.ajax({
            url: $(this).attr('href'),
            cache: false,
            type: 'POST',
            data: $('#form-meterair').serialize(),
            dataType: 'json',
            success: function(data) {
                show_modal(data.pesan, '', 'modal-sm');
                window.location.href=data.link_href;
            }
        });
    });
</script>
<style type="text/css">
    .btn { font-size: 13px;}
    .btn-sm { padding: 5px 7px; font-size: 11px;}
    .btn:focus { outline: none;}
    table.dataTable thead .sorting_asc:after { font-size: 11px; }
    div.dataTables_wrapper { width: 99%; height: 100%; margin: 0 auto; }
    .dataTables_length select,
    .dataTables_filter input { padding: 6px 12px; border: 1px solid #ccc; }
    tfoot input::placeholder { font-size:11px; }
    .hide-search input {display:none;}
    #tableDTServerSite tbody td:nth-child(8) { white-space: nowrap; }
</style>