<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset='UTF-8'>
    <title>Konfirmasi Tagihan</title>

</head>
<style>
    /*! CSS Used from: http://localhost/emsdev-master/vendors/bootstrap/dist/css/bootstrap.min.css */
    html {
        font-family: sans-serif;
        -webkit-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%
    }

    body {
        margin: 0
    }

    b {
        font-weight: 700
    }

    img {
        border: 0
    }

    table {
        border-spacing: 0;
        border-collapse: collapse
    }

    td,
    th {
        padding: 0
    }

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

        thead {
            display: table-header-group
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

        .table td,
        .table th {
            background-color: #fff !important
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
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0)
    }

    body {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 14px;
        line-height: 1.42857143;
        color: #333;
        background-color: #fff
    }

    img {
        vertical-align: middle
    }

    p {
        margin: 0 0 10px
    }

    .text-right {
        text-align: right
    }

    .text-center {
        text-align: center
    }

    ul {
        margin-top: 0;
        margin-bottom: 10px
    }

    .row {
        margin-right: -15px;
        margin-left: -15px
    }

    .col-lg-12,
    .col-lg-6,
    .col-md-12,
    .col-md-6,
    .col-sm-12,
    .col-sm-6,
    .col-xs-12,
    .col-xs-6 {
        position: relative;
        min-height: 1px;
        padding-right: 15px;
        padding-left: 15px
    }

    .col-xs-12,
    .col-xs-6 {
        float: left
    }

    .col-xs-12 {
        width: 100%
    }

    .col-xs-6 {
        width: 50%
    }

    @media (min-width:768px) {

        .col-sm-12,
        .col-sm-6 {
            float: left
        }

        .col-sm-12 {
            width: 100%
        }

        .col-sm-6 {
            width: 50%
        }
    }

    @media (min-width:992px) {

        .col-md-12,
        .col-md-6 {
            float: left
        }

        .col-md-12 {
            width: 100%
        }

        .col-md-6 {
            width: 50%
        }
    }

    @media (min-width:1200px) {

        .col-lg-12,
        .col-lg-6 {
            float: left
        }

        .col-lg-12 {
            width: 100%
        }

        .col-lg-6 {
            width: 50%
        }
    }

    table {
        background-color: transparent
    }

    th {
        text-align: left
    }

    .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 20px
    }

    .table>tbody>tr>td,
    .table>tfoot>tr>td,
    .table>thead>tr>th {
        padding: 8px;
        line-height: 1.42857143;
        vertical-align: middle;
        border-top: 1px solid #ddd
    }

    .table>thead>tr>th {
        vertical-align: bottom;
        border-bottom: 0.5px solid #ddd
    }

    .table>thead:first-child>tr:first-child>th {
        border-top: 0
    }

    .table-striped>tbody>tr:nth-of-type(odd) {
        background-color: #f9f9f9
    }

    table td[class*=col-] {
        position: static;
        display: table-cell;
        float: none
    }

    .row:after,
    .row:before {
        display: table;
        content: " "
    }

    .row:after {
        clear: both
    }

    .hidden {
        display: none !important
    }

    /*! CSS Used from: Embedded */
    .casabanti {
        font-family: 'casbanti'
    }

    .f-20 {
        font-size: 20px;
        font-weight: 700;
        line-height: 15px
    }

    .f-14 {
        font-size: 14px;
        line-height: 10px
    }

    html {
        margin-top: 200px;
        padding: 0px
    }

    .f-15 {
        font-size: 14px;
        line-height: 10px
    }

    table thead th,
    table tbody tr td,
    table tfoot tr td {
        font-size: 12px
    }

    .lh-18 {
        line-height: 18px
    }

    .lh-15 {
        line-height: 15px
    }

    .lh-5 {
        line-height: 5px
    }

    #header {
        position: fixed;
        top: -200px
    }

    <?php if ($total_tagihan->tunggakan != 0) : ?>.f-table {
        font-size: 10px;
        line-height: 10px;
    }

    <?php else : ?>.f-table {
        font-size: 12px;
        line-height: 10px;
    }

    <?php endif; ?>
</style>

<body>
    <div id="header">
        <div style="width: fit-content;text-align: center; margin-top:20px">
            <img src="images/logoCiputra.png" width="15%" style="align-content:center">
        </div>

        <div>
            <div class="" style="width: fit-content;text-align: center; margin-top:5px; margin-bottom:200px">
                <p class="align-center f-20"><u>Informasi Tagihan Retribusi Estate</u></p>
                <p class="align-center f-20 casabanti"><?= $unit->project ?></p>
                <p class="align-center f-14"><?= $unit->project_address ?></p>
            </div>
        </div>
    </div>

    <div id="container">
        <div id="body">
            <div>
                <p class="f-15">Kepada Yth,</p>
                <p class="f-15 lh-15">Bpk/ibu <?= $unit->customer_name ?></p>
                <p class="f-15 lh-15"><?= $unit->alamat ?></p>
                <p class="f-15">Perumahan <?= $unit->project ?></p>
            </div>
            <br>
            <div>
                <p class="f-15">Dengan Hormat,</p>
                <p class="f-15 lh-15">Dengan ini kami sampaikan informasi total tagihan
                    <?php
                    if ($periode_first == $periode_last) {
                        echo (" bulan " . strtolower($periode_first));
                    } else {
                        echo (" dari bulan " . strtolower($periode_first) . " sampai " . strtolower($periode_last));
                    }
                    ?>
                    , dengan perincian sebagai
                    berikut :</p>
            </div>
            <table class="table table-striped" style="margin-bottom:0">
                <thead>
                    <tr>
                        <th class="text-right" rowspan="2" style="vertical-align: middle">No</th>
                        <th class="text-center" rowspan="2" style="vertical-align: middle">Periode</th>
                        <?php if ($total_tagihan->air) : ?>
                            <th class="text-center" colspan="3" style="padding-bottom:0px">Meter</th>
                        <?php endif; ?>
                        <?php if ($total_tagihan->lain) : ?>
                            <th class="text-right" rowspan="2" style="vertical-align: middle">LAIN(Rp.)</th>
                        <?php endif; ?>
                        <?php if ($total_tagihan->air) : ?>
                            <th class="text-right" rowspan="2" style="vertical-align: middle">AIR(Rp.)</th>
                        <?php endif; ?>
                        <?php if ($total_tagihan->ipl) : ?>
                            <th class="text-right" rowspan="2" style="vertical-align: middle">IPL(Rp.)</th>
                            <th class="text-right" rowspan="2" style="vertical-align: middle">PPN(Rp.)</th>
                        <?php endif; ?>
                        <th class="text-right" rowspan="2" style="vertical-align: middle">Denda(Rp.)</th>
                        <?php if ($total_tagihan->tunggakan) : ?>
                            <th class="text-right" rowspan="2" style="vertical-align: middle">Tunggakan(Rp.)</th>
                        <?php endif; ?>
                        <th class="text-right" rowspan="2" style="vertical-align: middle">Total(Rp.)</th>
                    </tr>
                    <tr>
                        <?php if ($total_tagihan->air) : ?>
                            <th class="text-right">Awal</th>
                            <th class="text-right">Akhir</th>
                            <th class="text-right">Pakai</th>
                        <?php endif; ?>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    foreach ($tagihan as $i => $v) :
                    ?>
                        <tr>
                            <td class="text-right"><?= $i + 1 ?></td>
                            <td class="text-center"><?= $v->periode ?></td>
                            <?php if ($total_tagihan->air) : ?>
                                <td class="text-right"><?= $v->meter_awal !== null ? number_format($v->meter_awal) : '' ?></td>
                                <td class="text-right"><?= $v->meter_akhir !== null ? number_format($v->meter_akhir) : '' ?></td>
                                <td class="text-right"><?= $v->pakai !== null ? number_format($v->pakai) : '' ?></td>
                            <?php endif; ?>
                            <?php if ($total_tagihan->lain) : ?>
                                <td class="text-right"><?= number_format($v->tagihan_lain) ?></td>
                            <?php endif; ?>
                            <?php if ($total_tagihan->air) : ?>
                                <td class="text-right"><?= number_format($v->air) ?></td>
                            <?php endif; ?>
                            <?php if ($total_tagihan->ipl) : ?>
                                <td class="text-right"><?= number_format($v->ipl) ?></td>
                                <td class="text-right"><?= number_format($v->ppn) ?></td>
                            <?php endif; ?>
                            <td class="text-right"><?= number_format($v->denda) ?></td>
                            <?php if ($total_tagihan->tunggakan) : ?>
                                <td class="text-right"><?= number_format($v->tunggakan) ?></td>
                            <?php endif; ?>
                            <td class="text-right"><?= number_format($v->total) ?></td>
                        </tr>
                    <?php
                    endforeach;
                    ?>

                <tfoot>
                    <tr>

                        <td colspan="
                        <?php
                        if ($total_tagihan->air) {
                            echo (4);
                        } else {
                            echo (2);
                        }
                        ?>
                        "><b>Grand Total :</b></td>
                        <?php if ($total_tagihan->air) : ?>
                            <td class="text-right"><?= number_format($total_tagihan->pakai) ?></td>
                        <?php endif; ?>
                        <?php if ($total_tagihan->lain) : ?>
                            <td class="text-right"><?= number_format($total_tagihan->lain) ?></td>
                        <?php endif; ?>
                        <?php if ($total_tagihan->air) : ?>
                            <td class="text-right"><?= number_format($total_tagihan->air) ?></td>
                        <?php endif; ?>
                        <?php if ($total_tagihan->ipl) : ?>
                            <td class="text-right"><?= number_format($total_tagihan->ipl) ?></td>
                            <td class="text-right"><?= number_format($total_tagihan->ppn) ?></td>
                        <?php endif; ?>
                        <td class="text-right"><?= number_format($total_tagihan->denda) ?></td>
                        <?php if ($total_tagihan->tunggakan) : ?>
                            <td class="text-right"><?= number_format($total_tagihan->tunggakan) ?></td>
                        <?php endif; ?>

                        <td class="text-right"><?= number_format($total_tagihan->total) ?></td>
                    </tr>
                </tfoot>

            </table>
            <div <?php
                    if (($i + 1 >= 13 && $i + 1 <= 20) || (((($i + 1) - 20) % 23 >= 20) && ((($i + 1) - 21) % 23 <= 23)))
                        echo ("style='page-break-before: always;'");

                    ?>>
                <?php if ($status_saldo_deposit == 1) : ?>
                    <p class="lh-18 f-15" style="margin-bottom:6px;font-weight:bold;">
                        Saldo deposit sebesar : Rp.<?= $saldo_deposit ? $saldo_deposit : 0 ?>
                    </p>
                <?php endif; ?>

                <p class="lh-18 f-15">
                    Jika Pembayaran dilakukan setelah tanggal <?= $tgl_jatuh_tempo ?> bulan berjalan akan dikenakan denda
                    kumulatif/penalti. Untuk Informasi lebih lanjut dapat menghubungi Customer Service di
                    <?= $nama_pusat_informasi ?>
                    <?php
                    if ($unit->contactperson || $unit->phone) {
                        echo (" di ");
                        if ($unit->contactperson && $unit->phone) {
                            echo ("$unit->contactperson dan $unit->phone.");
                        } else if ($unit->contactperson) {
                            echo ("$unit->contactperson.");
                        } else if ($unit->phone) {
                            echo ("$unit->phone.");
                        }
                    } else {
                        echo (".");
                    }

                    ?>
                </p>
                <p class="lh-5">
                    Demikian Informasi yang dapat kami sampaikan, Atas kerjasamanya yang baik kami ucapkan terima
                    kasih.
                </p>
                <br>
                <div style="margin-top: 15px;margin-bottom:-100px;">
                    <table class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row">
                        <tr>
                            <td class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                <p class="lh-5 f-15">Hormat Kami,</p>
                                <p class="lh-5 f-15"><?= $unit->pt ?></p>
                                <?php if ($ttd) : ?>
                                    <img src="files/ttd/konfirmasi_tagihan/<?= $ttd ?>" width="150px" height="150px" style="margin-top:10px" />
                                <?php else : ?>
                                    <div style="height:150px;margin-top:10px">
                                    </div>
                                <?php endif; ?>
                                <p class="lh-5 f-15"><u><?= $unit->pp_value ?></u></p>
                                <p class="lh-5 f-15"><?= $unit->pp_name ?></p>
                            </td>
                            <td>
                                <div style="border: 2px solid black; padding:10px">
                                    <?= $catatan ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>