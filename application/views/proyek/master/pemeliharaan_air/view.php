
<div style="float:right">
    <h2>
        <button class="btn btn-dark" onClick="window.location.href='<?=site_url('P_master_pemeliharaan_air/add');?>'">
            <i class="fa fa-plus"></i> Tambah
        </button>
        <button class="btn btn-dark" onClick="window.location.href='<?=site_url('P_master_pemeliharaan_air');?>'">
            <i class="fa fa-repeat"></i> Refresh
        </button>
    </h2>
</div>
<div class="clearfix"></div>
</div>
<div class="x_content">
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <table class="table table-striped jambo_table" id="tableDTServerSite">
                    <tfoot id="tfoot" style="display: table-header-group">
                        <tr>
                            <th class="hide-search">No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Ukuran Pipa</th>
                            <th class="text-right">Pemeliharaan</th>
                            <th class="text-right">Pemasangan</th>
                            <th>PPN Pemasangan</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th hidden>Action</th>
                            <th hidden>Delete</th>
                        </tr>
                    </tfoot>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Ukuran Pipa</th>
                            <th class="text-right"> Biaya Pemeliharaan</th>
                            <th class="text-right"> Biaya Pemasangan</th>
                            <th class="text-right">PPN Pemasangan</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                            <th class="no-sort">Delete</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<!-- (Normal Modal)-->
<div class="modal fade" id="modal_delete" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content" style="margin-top:100px;">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" style="text-align:center;">Apakah anda yakin untuk mendelete data ini ?</h4>
            </div>

            <div class="modal-footer" style="margin:0px; border-top:0px; text-align:center;">
                <span id="preloader-delete"></span>
                </br>
                <button class="btn btn-danger" id="btn-delete">Delete</button>
                <button type="button" class="btn btn-info" data-dismiss="modal" id="delete_cancel_link">Cancel</button>

            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#tableDTServerSite tfoot th').each( function () {
            var title = $(this).text();
            $(this).html("<input type='text' class='form-control form-control-sm' placeholder='"+title+"' />");
        });

        table = $('#tableDTServerSite').DataTable({
            "serverSide": true,
            "stateSave": false,
            "bAutoWidth": true,
            "bDestroy" : true,
            "oLanguage": {
                "sSearch": "Search : ",
                "sLengthMenu": "_MENU_ ",
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
            "aaSorting": [
                [0, "desc"]
            ],
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false
            }],
            "sPaginationType": "simple_numbers",
            "iDisplayLength": 10,
            "aLengthMenu": [
                [10, 20, 50, 100, 150, 200], [10, 20, 50, 100, 150, 200]
            ],
            "ajax": {
                url: "<?= site_url('p-master-pemeliharaan-air/request-data-json'); ?>",
                type: "get",
                cache: false,
                error: function() {
                    $(".tableDTServerSite-error").html("");
                    $("#tableDTServerSite").append('<tbody class="my-grid-error"><tr><th colspan="10"><center>No data found in the server</center></th></tr></tbody>');
                    $("#tableDTServerSite_processing").css("display", "none");
                }
            }
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

        // delete row
        $(document).on('click', '#btn-hapus', function(e){
            e.preventDefault();
            href = $(this).attr('href');
            msg  = '<p>Yakin ingin menghapus data ini ?</p>';
            btn_save = '<a href="'+href+'" id="YesDelete" class="btn btn-sm btn-primary">Hapus</a>';
            show_modal(msg, btn_save, 'modal-sm');
        });
    });

    $(function(){
        function notif(title, text, type) {
            new PNotify({
                title: title,
                text: text,
                type: type,
                styling: 'bootstrap3'
            });
        }
        $(".btn-delete").click(function(){
            $("#btn-delete").attr('item_id',$(this).attr('item_id'));
        });
        $("#btn-delete").click(function(){
            $.ajax({
                type: "POST",
                data:{
                    id: $('.btn-delete').data('item_id')
                },
                url: "<?= site_url('P_master_pemeliharaan_air/ajax_delete') ?>",
                dataType: "json",
                success: function(data) {
                    if (data.status == 1) {
                        notif('Sukses', data.message, 'success')
                        setTimeout(function () { 
                            if(!alert('Halaman akan di refresh otomatis untuk update data!')){
                                window.open(window.location.href,'_self');
                            }
                        }, 2 * 1000);
                    } else
                        notif('Gagal', data.message, 'danger')
                }
            });
        });
    });
</script>
<style type="text/css">
    .btn { font-size: 13px;}
    .btn-sm { padding: 5px 7px; font-size: 11px;}
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
    .hide-search input {display:none;}
    #tableDTServerSite tbody { font-size: 13px; }
</style>