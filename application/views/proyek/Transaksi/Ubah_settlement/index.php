<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<!-- select2 -->
<link href="<?= base_url(); ?>vendor/select2/dist/css/select2.min.css" rel="stylesheet">
<script src="<?= base_url(); ?>vendor/select2/dist/js/select2.min.js"></script>

<div class="clearfix"></div>
<div class="x_content">
    <br>
    <form id="form_ubah_tgl_settlement" class="form-horizontal form-label-left" method="post">
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Cara Pembayaran</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <select id="cara_pembayaran" type="text" name="cara_pembayaran" class="select2 form-control" style="width: 100%" data-placeholder="Pilih Cara Pembayaran" required>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Uploud File</label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                    <input id="file_rekening_koran" type="file" name="file_rekening_koran" class="form-control" require>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-xs-12" style="margin:0 auto">
            <div class="form-group">
                <div class="center-margin">
                    <button type="submit" class="btn btn-primary">Load File</button>
                    <button id="proses" type="button" class="btn btn-success" disabled>Proses</button>
                </div>
            </div>
        </div>
    </form>
    <div>
        <table id="table_settlement" class="table table-striped jambo_table bulk_action table_settlement">
            <tfoot id="tfoot" style="display: table-header-group">
                <tr>
                    <th>No</th>
                    <th>Kawasan</th>
                    <th>Blok</th>
                    <th>No Unit</th>
                    <th>Pemilik</th>
                    <th>Virtual Account EMS</th>
                    <th>Virtual Account RK</th>
                    <th>Tgl Bayar EMS</th>
                    <th>Tgl Bayar RK</th>
                    <th>Total Bayar EMS</th>
                    <th>Total Bayar RK</th>
                    <th>Status</th>
                </tr>
            </tfoot>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kawasan</th>
                    <th>Blok</th>
                    <th>No Unit</th>
                    <th>Pemilik</th>
                    <th>Virtual Account EMS</th>
                    <th>Virtual Account RK</th>
                    <th>Tgl Bayar EMS</th>
                    <th>Tgl Bayar RK</th>
                    <th>Total Bayar EMS</th>
                    <th>Total Bayar RK</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(function() {
        var formData = new FormData();
        $('.table_settlement tfoot th').each(function() {
            var title = $(this).text();
            $(this).html('<input type="text" placeholder="Filter ' + title + '" />');
        });

        // DataTable

        // Apply the search
        var table = $('.table_settlement').DataTable({
            paging: false,
            columns: [{
                    data: 'index'
                },
                {
                    data: "kawasan"
                },
                {
                    data: "blok"
                },
                {
                    data: "no_unit"
                },
                {
                    data: "pemilik"
                },
                {
                    data: "va_ems"
                },
                {
                    data: "va"
                },
                {
                    data: "tgl_bayar_ems"
                },
                {
                    data: "tgl_bayar"
                },
                {
                    data: "total_bayar_ems"
                },
                {
                    data: "total_bayar"
                },
                {
                    data: "status"
                }
            ]
        });
        $("#cara_pembayaran").select2({
            ajax: {
                dataType: "json",
                url: "<?= site_url() ?>Transaksi/Payment/ajax_get_cara_pembayaran",
                data: params => ({
                    data: params.term
                }),
                processResults: data => ({
                    results: data
                })
            }
        });
        $("#form_ubah_tgl_settlement").submit(e => {
            e.preventDefault();
            formData.delete('cara_pembayaran');
            formData.delete('file_rekening_koran');
            formData.append('cara_pembayaran', $("#cara_pembayaran").val());
            formData.append('file_rekening_koran', $("#file_rekening_koran")[0].files[0]);
            $.ajax({
                url: `<?= site_url() ?>Transaksi/Ubah_settlement/ajax_uploud_file/${$('#cara_pembayaran').val()}`,
                // dataType: "json",
                processData: false,
                contentType: false,
                type: 'POST',
                data: formData,
                success: function(result) {
                    if (result) {
                        table.clear().draw();
                        table.rows.add(result.pembayaran).draw();
                    }
                }
            });
        })
        $("#proses").click(e => {
            e.preventDefault();

        })
    });
</script>