<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Konfirmasi Tagihan</title>
</head>
<style>
    html {
        font-family: sans-serif;
        -webkit-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%
    }
    body { margin: 0; font-weight: 700; }
    strong { font-weight: 700; }
    img { border: 0; }
    table { border-spacing: 0; border-collapse: collapse; }
    td { padding: 0 }
    @media print {
        *,
        :after,
        :before {
            color: #000 !important;
            text-shadow: none !important;
            background: 0 0 !important;
            -webkit-box-shadow: none !important;
            box-shadow: none !important
        }
        img,
        tr {
            page-break-inside: avoid
        }
        img {
            max-width: 100% !important
        }
        p {
            orphans: 3;
            widows: 3
        }
        .table {
            border-collapse: collapse !important
        }
        .table td {
            background-color: #fff !important
        }
        .table-bordered td {
            border: 1px solid #ddd !important
        }
    }

    * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box
    }

    :after,
    :before {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box
    }

    html {
        font-size: 10px;
        -webkit-tap-highlight-color: transparent
    }

    body {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 14px;
        line-height: 1.42857143;
        color: #333;
        background-color: #fff
    }
    img { vertical-align: middle; }
    p { margin: 0 0 10px; }
    ul {
        margin-top: 0;
        margin-bottom: 10px
    }
    table { background-color: transparent; }
    .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 20px
    }

    .table>tbody>tr>td {
        padding: 8px;
        line-height: 1.42857143;
        vertical-align: middle;
        border-top: 1px solid #ddd
    }

    .table-bordered {
        border: 1px solid #ddd
    }

    .table-bordered>tbody>tr>td {
        border: 1px solid #ddd
    }

    .hidden {
        display: none !important
    }

    html {
        margin: 5px 15px;
        padding: 0
    }

    table tbody tr td {
        font-size: 12px
    }

    .table>tbody>tr>td {
        padding-top: 3px;
        padding-bottom: 0
    }

    table.table-bordered>tbody>tr>td {
        border: 2px solid #000
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
                    <p style="text-align: left; margin-left: 10px; font-size: 16px; margin-top: 20px;"> <strong>
                            <strong><?=$project->name?></strong> </strong> </p>
                </td>
                <td style="width: 30%;">
                    <p style="text-align: right; margin-right: 10px;"> <strong> <strong><?=$unit->pt_name?></strong>
                        </strong> </p>
                    <p style="text-align: right; margin-right: 10px;"> 
                        <strong> <strong>No Kwitansi: <?=$no_kwitansi?></strong> </strong>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%; margin-bottom: 0;" class="table table-bordered">
        <tbody>
            <tr>
                <td>
                    <p style="text-align: center;"> <strong><strong>Kwitansi</strong></strong> </p>
                </td>
            </tr>
        </tbody>
    </table>
    <table style="width: 50%;">
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td><?=$unit->pemilik?></td>
        </tr>
        <tr>
            <td>Unit</td>
            <td>:</td>
            <td><?=empty($unit->no_unit) ? $unit->uid : "$unit->kawasan $unit->blok/$unit->no_unit"?></td>
        </tr>
        <tr>
            <td>Unit ID</td>
            <td>:</td>
            <td><?=$unit->uid?></td>
        </tr>
        <tr>
            <td>No. Meter</td>
            <td>:</td>
            <td><?=$unit->no_meter?></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td><?=empty($unit->no_unit) ? $unit->kawasan : "$unit->kawasan $unit->blok/$unit->no_unit"?></td>
        </tr>
    </table>
    <?php if($pembayaran_air->total_tagihan): ?> 
        <table style="width: 100%;" class="table table-bordered jambo_table">
        <tbody>
            <tr>
                <td colspan="8">
                    <p style="text-align: center;"> <strong><strong>Perincian Biaya Air Bersih</strong></strong> </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p> <strong><strong>Periode</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong><strong>Meter Awal</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong><strong>Meter Akhir</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong><strong>Tagihan (Rp.)</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong><strong>Diskon (Rp.)</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong><strong>Denda (Rp.)</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong><strong>Terbayar Sebelum (Rp.)</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong><strong>Terbayar Saat ini (Rp.)</strong></strong> </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><?php if($pembayaran_air_periode_awal==$pembayaran_air_periode_akhir): ?><?=$pembayaran_air_periode_awal?><?php else: ?><?="$pembayaran_air_periode_awal - $pembayaran_air_periode_akhir"?><?php endif; ?>
                    </p>
                </td>
                <td>
                    <p style="text-align: right;"><?=$meter->meter_awal?>m<sup>3</sup></p>
                </td>
                <td style="text-align: right;">
                    <p><?=$meter->meter_akhir?>m<sup>3</sup></p>
                </td>
                <td>
                    <p style="text-align: right;"><?= number_format($pembayaran_air->total_tagihan, 0,'.','.'); ?></p>
                </td>
                <td style="text-align: right;">
                    <p><?= number_format($pembayaran_air->total_diskon, 0,'.','.'); ?></p>
                </td>
                <td style="text-align: right;">
                    <p><?= number_format($pembayaran_air->total_denda, 0,'.','.'); ?></p>
                </td>
                <td style="text-align: right;">
                    <p><?= number_format($pembayaran_air->total_terbayar_sebelum, 0,'.','.'); ?></p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <?php 
                        if ($pembayaran_air->total_diskon > 0)  {
                            echo number_format(($pembayaran_air->total_terbayar_saat_ini - $pembayaran_air->total_diskon), 0,'.','.');
                        } else {
                            echo number_format($pembayaran_air->total_terbayar_saat_ini, 0,'.','.');
                        }
                        ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    <?php endif; ?>
    <?php if($pembayaran_lingkungan->total_tagihan): ?> 
        <table style="width: 100%; margin-bottom: 0;" class="table table-bordered">
        <tbody>
            <tr>
                <td colspan="8">
                    <p style="text-align: center;"> <strong><strong>Perincian Iuran Pengelolaan Lingkungan (I.P.L)</strong></strong> </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p> <strong><strong>Periode</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong><strong>Total Pokok (Rp.)</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong><strong>Total PPN (Rp.)</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong><strong>Total Tagihan (Rp.)</strong></strong> </p>
                </td>                
                <td style="text-align: right;">
                    <p> <strong><strong>Diskon (Rp.)</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong><strong>Denda (Rp.)</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong><strong>Terbayar Sebelum (Rp.)</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong><strong>Terbayar Saat ini (Rp.)</strong></strong> </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p> <?php if($pembayaran_lingkungan_periode_awal==$pembayaran_lingkungan_periode_akhir): ?><?=$pembayaran_lingkungan_periode_awal?><?php else: ?><?="$pembayaran_lingkungan_periode_awal - $pembayaran_lingkungan_periode_akhir"?><?php endif; ?>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p><?=number_format($pembayaran_lingkungan->total_pokok, 0,',','.');?></p>
                </td>
                <td style="text-align: right;">
                    <p><?=number_format($pembayaran_lingkungan->total_ppn, 0,',','.'); ?></p>
                </td>
                <td style="text-align: right;">
                    <p><?=number_format($pembayaran_lingkungan->total_tagihan, 0,',','.'); ?></p>
                </td>
                <td style="text-align: right;">
                    <p><?=number_format($pembayaran_lingkungan->total_diskon, 0,',','.'); ?></p>
                </td>
                <td style="text-align: right;">
                    <p><?= number_format($pembayaran_lingkungan->total_denda, 0,'.','.');?></p>
                </td>
                <td style="text-align: right;">
                    <p><?= number_format($pembayaran_lingkungan->total_terbayar_sebelum, 0,'.','.'); ?></p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <?php 
                        if ($pembayaran_lingkungan->total_diskon > 0)  {
                            echo number_format(($pembayaran_lingkungan->total_terbayar_saat_ini - $pembayaran_lingkungan->total_diskon), 0,'.','.');
                        } else {
                            echo number_format($pembayaran_lingkungan->total_terbayar_saat_ini, 0,'.','.');
                        }
                        ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    <?php endif; ?>
    <?php if($pembayaran_ll): ?>
    <p></p>
    <table style="width: 100%; margin-bottom: 0;" class="table table-bordered">
        <tbody>
            <tr>
                <td colspan="6">
                    <p style="text-align: center;"> <strong><strong>Perincian Service Lain</strong></strong> </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p> <strong><strong>Service</strong></strong> </p>
                </td>
                <td>
                    <p> <strong><strong>Periode</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong><strong>Tagihan (Rp.)</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong><strong>PPN (Rp.)</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong><strong>Denda (Rp.)</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong><strong>Diskon (Rp.)</strong></strong> </p>
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
                    <p><?= number_format($pembayaran_ll->nilai_tagihan, 0,'.','.');?></p>
                </td>
                <td style="text-align: right;">
                    <p><?= number_format($pembayaran_ll->nilai_ppn_lainnya, 0,'.','.');?></p>
                </td>
                <td style="text-align: right;">
                    <p><?= number_format($pembayaran_ll->nilai_denda, 0,'.','.');?></p>
                </td>
                <td style="text-align: right;">
                    <p><?=$pembayaran_ll->nilai_diskon; ?></p>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?> <p></p>

    <table style="width: 100%; padding-top: 20px;margin-bottom: 0;" class="table table-bordered">
        <tbody>
            <tr>
                <td>
                    <p> <strong><strong></strong>Biaya Admin</strong> </p>
                </td>
                <td>
                    <p> <strong><strong>: Rp.</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong> <strong><?=number_format($pembayaran->nilai_biaya_admin_cara_pembayaran, 0,'.','.')?></strong> </strong> </p>
                </td>
                <td colspan="2">
                    <p> <strong><strong>TERBILANG</strong></strong> </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p> <strong><strong></strong>Total Bayar</strong> </p>
                </td>
                <td>
                    <p> <strong><strong>: Rp.</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p>
                        <strong>
                            <?= number_format($grand_total, 0,'.','.'); ?>
                        </strong>
                    </p>
                </td>
                <td colspan="2">
                    <p> <strong> <strong><?=$terbilang?></strong> </strong> </p>
                </td>
            </tr>
            <tr>
                <td colspan="2">Tanggal Bayar</td>
                <td style="text-align: right;"><?=date('d-m-Y', strtotime($tgl_bayar));?></td>
                <td>
                    <p> <strong><strong></strong></strong> </p>
                </td>
                <td>
                    <p> <strong> <strong><?=$city?>,<?=date("d - m - Y")?></strong> </strong> </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p> <strong><strong>Pemakaian Deposit</strong></strong> </p>
                </td>
                <td>
                    <p> <strong><strong>: Rp.</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong> <strong><?=$pemakaian_deposit?></strong> </strong> </p>
                </td>
                <td>
                    <p> <strong><strong></strong></strong> </p>
                </td>
                <td>
                    <p> <strong><strong></strong></strong> </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p> <strong><strong>Sisa Deposit</strong></strong> </p>
                </td>
                <td>
                    <p> <strong><strong>: Rp.</strong></strong> </p>
                </td>
                <td style="text-align: right;">
                    <p> <strong> <strong><?=$sisa_deposit?></strong> </strong> </p>
                </td>
                <td>
                    <p> <strong><strong></strong></strong> </p>
                </td>
                <td>
                    <p> <strong></strong> <strong> <strong><?=$user?></strong> </strong> </p>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>