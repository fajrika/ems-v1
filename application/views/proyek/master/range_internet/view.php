<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<?=$load_css;?>
<?=$load_js;?>
<div style="float:right">
    <h2>
        <button class="btn btn-primary btn-action-header" onClick="window.location.href='<?= site_url() ?>/master/range/master_range_internet/add'">
            <i class="fa fa-plus"></i>
            Tambah
        </button>
        <button id="reload_data" class="btn btn-success btn-action-header">
            <i class="fa fa-repeat"></i>
            Refresh
        </button>
    </h2>
</div>
<div class="clearfix"></div>
</div>
<div class="x_content">
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <table id="tableDTServerSite" class="table table-striped jambo_table">
                    <tfoot id="tfoot" style="display: table-header-group">
                        <tr>
                            <th>Nama Paket</th>
                            <th>Jenis Service</th>
                            <th>ISP</th>
                            <th>Kapasitas</th>
                            <th>Kuota</th>
                            <th>Up Device</th>
                            <th>Nilai Langganan</th>
                            <th>Keterangan</th>
                            <th hidden>Status</th>
                            <th hidden>Action</th>
                            <!-- <th hidden>Delete</th> -->
                        </tr>
                    </tfoot>
                    <thead>
                        <tr>
                            <th>Nama Paket</th>
                            <th>Jenis Service</th>
                            <th>ISP</th>
                            <th>Kapasitas</th>
                            <th>Kuota</th>
                            <th>Up Device</th>
                            <th>Nilai Langganan</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                            <!-- <th>Delete</th> -->
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<!-- (Normal Modal)-->
<!-- <div class="modal fade" id="modal_delete_m_n" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content" style="margin-top:100px;">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" style="text-align:center;">Apakah anda yakin untuk mendelete data ini<span class="grt"></span>
                    ?</h4>
            </div>

            <div class="modal-footer" style="margin:0px; border-top:0px; text-align:center;">
                <span id="preloader-delete"></span>
                </br>
                <a class="btn btn-danger" id="delete_link_m_n" href="">Delete</a>
                <button type="button" class="btn btn-info" data-dismiss="modal" id="delete_cancel_link">Cancel</button>

            </div>
        </div>
    </div>
</div> -->

<!-- <script>
    function confirm_modal(id) {
        jQuery('#modal_delete_m_n').modal('show', {
            backdrop: 'static',
            keyboard: false
        });
        $('#delete_link_m_n').attr("href","<?= site_url('master/range/master_range_internet/delete?id=" + id + "'); ?>");
        $('#delete_link_m_n').focus();
    }
</script> -->

<script>
    $(document).ready(function() {
        $('#tableDTServerSite tfoot th').each( function () {
            var title = $(this).text();
            $(this).html( '<input type="text" placeholder="Filter '+title+'" />' );
        } );

        var table = 
        $('#tableDTServerSite').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": "<?=site_url("master/range/master_range_internet/ajax_get_view")?>"
        });

        // Reload data
        $('#reload_data').on( 'click', function () {
          table.ajax.reload();
        });

        // Apply the search
        table.columns().every( function () {
            var that = this;
            $( 'input', this.footer() ).on( 'keyup change', function () {
                if ( that.search() !== this.value ) {
                    that
                        .search( this.value )
                        .draw();
                }
            } );
        } );


    });
</script>