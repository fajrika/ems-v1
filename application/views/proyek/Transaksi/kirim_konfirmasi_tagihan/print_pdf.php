
<span>
    <table style="padding-top: -30px;">
        <tr>
            <th style="width: fit-content;text-align: center; margin-top:20px">
                <img src="images/ciputra-logo-min.png" width="15%" style="align-content:center">
            </th>
        </tr>
        <tr>
            <th>
                <p class="align-center f-20"><u>Informasi Tagihan Pemeliharaan Estate</u></p>
            </th>
        </tr>
        <tr>
            <th>
                <p class="align-center f-20 casabanti"><?= $unit->project ?></p>
            </th>
        </tr>
        <tr>
            <th>
                <p class="align-center f-14"><?= $unit->project_address ?></p>
            </th>
        </tr>
        <tr><th><br></th></tr>
        <tr><th><br></th></tr>
    </table>

    <table>
        <tr>
            <th align="left" class="font-normal f-14">Kepada Yth,</th>
        </tr>
        <tr>
            <th align="left" class="font-normal f-14">Bpk/ibu <?= $unit->customer_name ?></th>
        </tr>
        <tr>
            <th align="left" class="font-normal f-14"><?= $unit->alamat ?></th>
        </tr>
        <tr>
            <th align="left" class="font-normal f-14">Perumahan <?= $unit->project ?></th>
        </tr>
        <tr><th><br></th></tr>
        <tr>
            <th align="left" class="font-normal f-14">
                <div>
                    <p class="f-15" style="margin-bottom: 20px;">Dengan Hormat,</p><br>
                    <p class="f-15 lh-15">
                        Dengan ini kami sampaikan informasi total tagihan
                        <?php
                        if ($periode_first == $periode_last) {
                            echo (" bulan " . strtolower($periode_first));
                        } else {
                            echo (" dari bulan " . strtolower($periode_first) . " sampai " . strtolower($periode_last));
                        }
                        ?>, dengan perincian sebagai berikut :
                    </p>
                </div>
            </th>
        </tr>
    </table>
    <br>
    <table class="table table-striped" cellspacing="0" style="margin-bottom:0;">
        <thead>
            <tr>
                <th class="text-right" <?php if ($total_tagihan->air){ echo "rowspan='2'";}else{ ""; } ?> style="vertical-align: middle; border: 1px solid #ccc; padding: 10px;">No</th>
                <th class="text-center" <?php if ($total_tagihan->air){ echo "rowspan='2'";}else{ ""; } ?> style="vertical-align: middle; border: 1px solid #ccc;">Periode</th>
                <?php if ($total_tagihan->air) : ?>
                    <th class="text-center" colspan="3" style="padding-bottom:0px; border: 1px solid #ccc;">Meter</th>
                <?php endif; ?>
                <?php if ($total_tagihan->lain) : ?>
                    <th class="text-right" <?php if ($total_tagihan->air){ echo "rowspan='2'";}else{ ""; } ?> style="vertical-align: middle; border: 1px solid #ccc;">LAIN(Rp.)</th>
                <?php endif; ?>
                <?php if ($total_tagihan->air) : ?>
                    <th class="text-right" <?php if ($total_tagihan->air){ echo "rowspan='2'";}else{ ""; } ?> style="vertical-align: middle; border: 1px solid #ccc;">AIR(Rp.)</th>
                <?php endif; ?>
                <?php if ($total_tagihan->ipl) : ?>
                    <th class="text-right" <?php if ($total_tagihan->air){ echo "rowspan='2'";}else{ ""; } ?> style="vertical-align: middle; border: 1px solid #ccc;">IPL(Rp.)</th>
                    <th class="text-right" <?php if ($total_tagihan->air){ echo "rowspan='2'";}else{ ""; } ?> style="vertical-align: middle; border: 1px solid #ccc;">PPN(Rp.)</th>
                <?php endif; ?>
                <th class="text-right" <?php if ($total_tagihan->air){ echo "rowspan='2'";}else{ ""; } ?> style="vertical-align: middle; border: 1px solid #ccc;">Denda(Rp.)</th>
                <?php if ($total_tagihan->tunggakan) : ?>
                    <th class="text-right" <?php if ($total_tagihan->air){ echo "rowspan='2'";}else{ ""; } ?> style="vertical-align: middle; border: 1px solid #ccc;">Tunggakan(Rp.)</th>
                <?php endif; ?>
                <th class="text-right" <?php if ($total_tagihan->air){ echo "rowspan='2'";}else{ ""; } ?> style="vertical-align: middle; border: 1px solid #ccc;">Total(Rp.)</th>
            </tr>
            <tr>
                <?php if ($total_tagihan->air) : ?>
                    <th class="text-right" style="padding: 5px; border: 1px solid #ccc;">Awal</th>
                    <th class="text-right" style="padding: 5px; border: 1px solid #ccc;">Akhir</th>
                    <th class="text-right" style="padding: 5px; border: 1px solid #ccc;">Pakai</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($tagihan as $i => $v) :
            ?>
                <tr>
                    <td class="text-right" style="padding: 5px; border: 1px solid #ccc;"><?= $i + 1 ?></td>
                    <td class="text-center" style="border: 1px solid #ccc;"><?= $v->periode ?></td>
                    <?php if ($total_tagihan->air) : ?>
                        <td class="text-right" style="padding: 5px; border: 1px solid #ccc;"><?= $v->meter_awal !== null ? number_format($v->meter_awal) : '' ?></td>
                        <td class="text-right" style="padding: 5px; border: 1px solid #ccc;"><?= $v->meter_akhir !== null ? number_format($v->meter_akhir) : '' ?></td>
                        <td class="text-right" style="padding: 5px; border: 1px solid #ccc;"><?= $v->pakai !== null ? number_format($v->pakai) : '' ?></td>
                    <?php endif; ?>
                    <?php if ($total_tagihan->lain) : ?>
                        <td class="text-right" style="padding: 5px; border: 1px solid #ccc;"><?= number_format($v->tagihan_lain) ?></td>
                    <?php endif; ?>
                    <?php if ($total_tagihan->air) : ?>
                        <td class="text-right" style="padding: 5px; border: 1px solid #ccc;"><?= number_format($v->air) ?></td>
                    <?php endif; ?>
                    <?php if ($total_tagihan->ipl) : ?>
                        <td class="text-right" style="padding: 5px; border: 1px solid #ccc;"><?= number_format($v->ipl) ?></td>
                        <td class="text-right" style="padding: 5px; border: 1px solid #ccc;"><?= number_format($v->ppn) ?></td>
                    <?php endif; ?>
                    <td class="text-right" style="padding: 5px; border: 1px solid #ccc;"><?= number_format($v->denda) ?></td>
                    <?php if ($total_tagihan->tunggakan) : ?>
                        <td class="text-right" style="padding: 5px; border: 1px solid #ccc;"><?= number_format($v->tunggakan) ?></td>
                    <?php endif; ?>
                    <td class="text-right" style="padding: 5px; border: 1px solid #ccc;"><?= number_format($v->total) ?></td>
                </tr>
            <?php
            endforeach;
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="<?php if ($total_tagihan->air) { echo (4); } else { echo (2); } ?>" style="padding: 5px; border: 1px solid #ccc;"><b>Grand Total :</b></td>
                <?php if ($total_tagihan->air) : ?>
                    <td class="text-right" style="border: 1px solid #ccc;"><?= number_format($total_tagihan->pakai) ?></td>
                <?php endif; ?>
                <?php if ($total_tagihan->lain) : ?>
                    <td class="text-right" style="border: 1px solid #ccc;"><?= number_format($total_tagihan->lain) ?></td>
                <?php endif; ?>
                <?php if ($total_tagihan->air) : ?>
                    <td class="text-right" style="border: 1px solid #ccc;"><?= number_format($total_tagihan->air) ?></td>
                <?php endif; ?>
                <?php if ($total_tagihan->ipl) : ?>
                    <td class="text-right" style="border: 1px solid #ccc;"><?= number_format($total_tagihan->ipl) ?></td>
                    <td class="text-right" style="border: 1px solid #ccc;"><?= number_format($total_tagihan->ppn) ?></td>
                <?php endif; ?>
                <td class="text-right" style="border: 1px solid #ccc;"><?= number_format($total_tagihan->denda) ?></td>
                <?php if ($total_tagihan->tunggakan) : ?>
                    <td class="text-right" style="border: 1px solid #ccc;"><?= number_format($total_tagihan->tunggakan) ?></td>
                <?php endif; ?>
                <td class="text-right" style="border: 1px solid #ccc;"><?= number_format($total_tagihan->total) ?></td>
            </tr>
        </tfoot>
    </table>

    <div <?php if (($i + 1 >= 13 && $i + 1 <= 20) || (((($i + 1) - 20) % 23 >= 20) && ((($i + 1) - 21) % 23 <= 23))) echo ("style='page-break-before: always;'"); ?>>
        <?php if ($status_saldo_deposit == 1) : ?>
            <p class="lh-18 f-15" style="margin-bottom:6px;font-weight:bold;">
                Saldo deposit sebesar : Rp.<?= $saldo_deposit ? $saldo_deposit : 0 ?>
            </p>
        <?php endif; ?>

        <?= $text_konfirmasi_tagihan ?>

        <br>
        <table style="margin-top: 10px;">
            <tr>
                <td>
                    <table border="0">
                        <tr>
                            <td><p class="lh-5 f-15">Hormat Kami,</p></td>
                        </tr>
                        <tr>
                            <td><p class="lh-5 f-15"><?= $unit->pt ?></p></td>
                        </tr>
                        <tr>
                            <td>
                                <?php if ($ttd) : ?>
                                    <img src="files/ttd/konfirmasi_tagihan/<?= $ttd ?>" width="120px" height="120px" style="margin-top:10px" />
                                <?php else : ?>
                                    <br><br><br><br>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p class="lh-5 f-15"><u><?= $unit->pp_value ?></u></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p class="lh-5 f-15"><?= $unit->pp_name ?></p>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table border="0">
                        <tr>
                            <td style="border: 1px solid #000; width: 180px; padding: 10px;"><?= $catatan ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</span>

<?php if ($nomor <= ($jml_data)): ?>
<pagebreak>
<?php endif; ?>