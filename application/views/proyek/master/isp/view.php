<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<?=$load_css;?>
<?=$load_js;?>
<div style="float:right">
    <h2>
        <button class="btn btn-primary btn-action-header" onClick="window.location.href='<?= site_url() ?>/P_master_isp/add'">
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
                <table id="tableDTServerSite" class="table table-striped jambo_table bulk_action">
                    <tfoot id="tfoot" style="display: table-header-group">
                        <tr>
                            <th>Nama ISP</th>
                            <th>Bandwidth (MB)</th>
                            <th>Persen Mitra</th>
                            <th>Nilai Kabel</th>
                            <th>Nilai Pemasangan</th>
                            <th>Nilai lain-lain</th>
                            <th>Keterangan</th>
                            <th hidden>Status</th>
                            <th hidden>Action</th>
                        </tr>
                    </tfoot>
                    <thead>
                        <tr>
                            <th>Nama ISP</th>
                            <th>Bandwidth (MB)</th>
                            <th>Persen Mitra</th>
                            <th>Nilai Kabel</th>
                            <th>Nilai Pemasangan</th>
                            <th>Nilai lain-lain</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th>Action</th>
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
            "ajax": "<?=site_url("P_master_isp/ajax_get_view")?>"
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