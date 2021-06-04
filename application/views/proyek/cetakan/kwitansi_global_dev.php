<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset='UTF-8'>
    <title>Konfirmasi Tagihan</title>

</head>
<style>
    html {
        font-family: sans-serif;
        -webkit-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%;
    }

    body {
        margin: 0;
    }

    strong {
        font-weight: 700;
    }

    img {
        border: 0;
    }

    table {
        border-spacing: 0;
        border-collapse: collapse;
    }

    td {
        padding: 0;
    }

    @media print {

        *,
        :after,
        :before {
            color: #000 !important;
            text-shadow: none !important;
            background: 0 0 !important;
            -webkit-box-shadow: none !important;
            box-shadow: none !important;
        }

        img,
        tr {
            page-break-inside: avoid;
        }

        img {
            max-width: 100% !important;
        }

        p {
            orphans: 3;
            widows: 3;
        }

        .table {
            border-collapse: collapse !important;
        }

        .table td {
            background-color: #fff !important;
        }

        .table-bordered td {
            border: 1px solid #ddd !important;
        }
    }

    * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    :after,
    :before {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    html {
        font-size: 10px;
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
    }

    body {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 14px;
        line-height: 1.42857143;
        color: #333;
        background-color: #fff;
    }

    img {
        vertical-align: middle;
    }

    p {
        margin: 0 0 10px;
    }

    ul {
        margin-top: 0;
        margin-bottom: 10px;
    }

    table {
        background-color: transparent;
    }

    .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 20px;
    }

    .table>tbody>tr>td {
        padding: 8px;
        line-height: 1.42857143;
        vertical-align: middle;
        border-top: 1px solid #ddd;
    }

    .table-bordered {
        border: 1px solid #ddd;
    }

    .table-bordered>tbody>tr>td {
        border: 1px solid #ddd;
    }

    .hidden {
        display: none !important;
    }

    html {
        margin: 5px 15px;
        padding: 0px;
    }

    table tbody tr td {
        font-size: 12px;
    }

    body {
        font-weight: bold;
        padding-top: -5px;
    }

    .table>tbody>tr>td {
        padding-top: 3px;
        padding-bottom: 0px;
    }

    table.table-bordered>tbody>tr>td {
        border: 2px solid black;
    }
</style>

<body class="container2">
    <p></p>
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="width: 10%;">
                    <p><img src="images/logo-ciputra-min.jpeg" width="5000%" /></p>
                </td>
                <td style="width: 40%;">
                    <p style="text-align: left; margin-left: 10px; font-size: 16px; margin-top: 20px;">
                        <strong>
                            <strong><?=$project->name?></strong>
                        </strong>
                    </p>
                </td>
                <td style="width: 30%;">
                    <p style="text-align: right; margin-right: 10px;">
                        <strong>
                            <strong><?=$unit->pt_name?></strong>
                        </strong>
                    </p>
                    <p style="text-align: right; margin-right: 10px;">
                        <strong>
                            <strong>No Kwitansi :<?=$project->code?>-<?=$unit->tgl_bayar?><?=$no_kwitansi?></strong>
                        </strong>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%; margin-bottom: 0;" class="table table-bordered">
        <tbody>
            <tr>
                <td>
                    <p style="text-align: center;">
                        <strong><strong>Kwitansi</strong></strong>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    <table style="width: 50%;">
        <tr>
            <td style="width: 20%;">Nama</td>
            <td>:</td>
            <td><?=$unit->pemilik?></td>
        </tr>
        <tr>
            <td style="width: 20%;">Unit</td>
            <td>:</td>
            <td><?="$unit->kawasan $unit->blok/$unit->no_unit"?></td>
        </tr>
        <tr>
            <td style="width: 20%;">Unit ID</td>
            <td>:</td>
            <td><?=$unit_id?></td>
        </tr>
        <tr>
            <td style="width: 20%;">No. Meter</td>
            <td>:</td>
            <td><?=$unit->no_meter?></td>
        </tr>
        <tr>
            <td style="width: 20%;">Alamat</td>
            <td>:</td>
            <td><?="$unit->kawasan $unit->blok/$unit->no_unit"?></td>
        </tr>
    </table>
    <?php if($pembayaran_air->tagihan): ?>
    <table style="width: 100%;" class="table table-bordered jambo_table">
        <tbody>
            <tr>
                <td colspan="6">
                    <p style="text-align: center;">
                        <strong><strong>Perincian Biaya Air Bersih</strong></strong>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        <strong><strong>Periode</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong><strong>Meter Awal</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong><strong>Meter Akhir</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong><strong>Tagihan (Rp.)</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong><strong>Diskon (Rp.)</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong><strong>Denda (Rp.)</strong></strong>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><?php if($pembayaran_air_periode_awal==$pembayaran_air_periode_akhir): ?><?=$pembayaran_air_periode_awal?><?php else: ?><?="$pembayaran_air_periode_awal - $pembayaran_air_periode_akhir"?><?php endif; ?></p>
                </td>
                <td>
                    <p style="text-align: right;"><?=$meter->meter_awal?>m<sup>3</sup></p>
                </td>
                <td style="text-align: right;">
                    <p><?=$meter->meter_akhir?>m<sup>3</sup></p>
                </td>
                <td>
                    <p style="text-align: right;"><?=$pembayaran_air->tagihan?></p>
                </td>
                <td style="text-align: right;">
                    <p><?=$pembayaran_air->diskon?></p>
                </td>
                <td style="text-align: right;">
                    <p><?=$pembayaran_air->denda?></p>
                </td>
            </tr>
        </tbody>
    </table>
    <?php endif; ?><?php if($pembayaran_lingkungan->tagihan): ?>
    <table style="width: 100%; margin-bottom: 0;" class="table table-bordered">
        <tbody>
            <tr>
                <td colspan="6">
                    <p style="text-align: center;">
                        <strong><strong>Perincian Iuran Pengelolaan Lingkungan (I.P.L)</strong></strong>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        <strong><strong>Periode</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong><strong>Total Tagihan (Rp.)</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong><strong>Diskon (Rp.)</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong><strong>DPP (Rp.)</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong><strong>PPN (Rp.)</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong><strong>Denda (Rp.)</strong></strong>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        <?php if($pembayaran_lingkungan_periode_awal==$pembayaran_lingkungan_periode_akhir): ?><?=$pembayaran_lingkungan_periode_awal?><?php else: ?><?="$pembayaran_lingkungan_periode_awal - $pembayaran_lingkungan_periode_akhir"?><?php endif; ?>
                    </p>
                </td>
                <td style="text-align: right;">
                    <!-- <p><?=$pembayaran_lingkungan->tagihan_tanpa_ppn?></p> -->
                    <p><?=number_format($pembayaran_lingkungan->tagihan, 0,',','.');?></p>
                </td>
                <td style="text-align: right;">
                    <p><?=$pembayaran_lingkungan->diskon_tanpa_ppn?></p>
                </td>
                <td style="text-align: right;">
                    <p><?=$pembayaran_lingkungan->dpp?></p>
                </td>
                <td style="text-align: right;">
                    <p><?=$pembayaran_lingkungan->ppn_rupiah?></p>
                </td>
                <td style="text-align: right;">
                    <p><?=$pembayaran_lingkungan->denda?></p>
                </td>
            </tr>
        </tbody>
    </table>
    <?php endif; ?><?php if($pembayaran_ll): ?>
    <p></p>
    <table style="width: 100%; margin-bottom: 0;" class="table table-bordered">
        <tbody>
            <tr>
                <td colspan="6">
                    <p style="text-align: center;">
                        <strong><strong>Perincian Service Lain</strong></strong>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        <strong><strong>Service</strong></strong>
                    </p>
                </td>
                <td>
                    <p>
                        <strong><strong>Periode</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong><strong>Tagihan (Rp.)</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong><strong>PPN (Rp.)</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong><strong>Denda (Rp.)</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong><strong>Diskon (Rp.)</strong></strong>
                    </p>
                </td>
            </tr>
            <?php foreach($pembayaran_ll as $key=>$pembayaran_ll): ?>
            <tr>
                <td>
                    <p><?=$pembayaran_ll->name?></p>
                </td>
                <td>
                    <p><?="$pembayaran_ll->periode_awal - $pembayaran_ll->periode_akhir"?></p>
                </td>
                <td style="text-align: right;">
                    <p><?=$pembayaran_ll->tagihan_tanpa_ppn?></p>
                </td>
                <td style="text-align: right;">
                    <p><?=$pembayaran_ll->ppn_rupiah?></p>
                </td>
                <td style="text-align: right;">
                    <p><?=$pembayaran_ll->denda?></p>
                </td>
                <td style="text-align: right;">
                    <p><?=$pembayaran_ll->diskon?></p>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <p></p>
    <table style="width: 100%; margin-bottom: 0;" class="table table-bordered">
        <tbody>
            <tr>
                <td>
                    <p>
                        <strong><strong></strong>Biaya Admin</strong>
                    </p>
                </td>
                <td>
                    <p>
                        <strong><strong>: Rp.</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong>
                            <strong><?=$biaya_admin?></strong>
                        </strong>
                    </p>
                </td>
                <td colspan="2">
                    <p>
                        <strong><strong>TERBILANG</strong></strong>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        <strong><strong></strong>Total Bayar</strong>
                    </p>
                </td>
                <td>
                    <p>
                        <strong><strong>: Rp.</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong>
                            <strong><?=$grand_total?></strong>
                        </strong>
                    </p>
                </td>
                <td colspan="2">
                    <p>
                        <strong>
                            <strong><?=$terbilang?></strong>
                        </strong>
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td>
                    <p>
                        <strong><strong></strong></strong>
                    </p>
                </td>
                <td>
                    <p>
                        <strong>
                            <strong><?=$city?>,<?=date("d - m - Y")?></strong>
                        </strong>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        <strong><strong>Pemakaian Deposit</strong></strong>
                    </p>
                </td>
                <td>
                    <p>
                        <strong><strong>: Rp.</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong>
                            <strong><?=$pemakaian_deposit?></strong>
                        </strong>
                    </p>
                </td>
                <td>
                    <p>
                        <strong><strong></strong></strong>
                    </p>
                </td>
                <td>
                    <p>
                        <strong><strong></strong></strong>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        <strong><strong>Sisa Deposit</strong></strong>
                    </p>
                </td>
                <td>
                    <p>
                        <strong><strong>: Rp.</strong></strong>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong>
                            <strong><?=$sisa_deposit?></strong>
                        </strong>
                    </p>
                </td>
                <td>
                    <p>
                        <strong><strong></strong></strong>
                    </p>
                </td>
                <td>
                    <p>
                        <strong></strong>
                        <strong>
                            <strong><?=$user?></strong>
                        </strong>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>