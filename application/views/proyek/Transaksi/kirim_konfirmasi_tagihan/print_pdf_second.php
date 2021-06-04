
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
        <?php
            if ($unit->alamat_pemilik) {
                echo '<tr><th align="left" class="font-normal f-14">'.$unit->alamat_pemilik.'</th></tr>';
            }
        ?>
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
                <th class="f-table text-right" rowspan="2" style="vertical-align: middle; border: 1px solid #ccc;">No</th>
                <th class="f-table text-center" colspan="2" style="vertical-align: middle; border: 1px solid #ccc;">Periode</th>
                <?php if ($total_tagihan->air) : ?>
                <th class="f-table text-center" colspan="3" style="padding-bottom:0px; border: 1px solid #ccc;">Meter</th>
                <?php endif; ?>
                <?php if ($total_tagihan->lain) : ?>
                <th class="f-table text-right" rowspan="2" style="vertical-align: middle; border: 1px solid #ccc;">LAIN(Rp.)</th>
                <?php endif; ?>
                <?php if ($total_tagihan->air) : ?>
                <th class="f-table text-right" rowspan="2" style="vertical-align: middle; border: 1px solid #ccc;">AIR(Rp.)</th>
                <?php endif; ?>
                <?php if ($total_tagihan->ipl) : ?>
                <th class="f-table text-right" rowspan="2" style="vertical-align: middle; border: 1px solid #ccc;">IPL(Rp.)</th>
                <th class="f-table text-right" rowspan="2" style="vertical-align: middle; border: 1px solid #ccc;">PPN(Rp.)</th>
                <?php endif; ?>
                <th class="f-table text-right" rowspan="2" style="vertical-align: middle; border: 1px solid #ccc;">Denda(Rp.)</th>
                <?php if ($total_tagihan->tunggakan) : ?>
                <th class="f-table text-right" rowspan="2" style="vertical-align: middle; border: 1px solid #ccc;">Tunggakan(Rp.)</th>
                <?php endif; ?>
                <th class="f-table text-right" rowspan="2" style="vertical-align: middle; border: 1px solid #ccc;">Total(Rp.)</th>
            </tr>
            <tr>
                <th class="f-table text-center" style="padding: 5px; border: 1px solid #ccc;">Penggunaan</th>
                <th class="f-table text-center" style="padding: 5px; border: 1px solid #ccc;">Tagihan</th>
                <?php if ($total_tagihan->air) : ?>
                <th class="f-table text-right" style="padding: 5px; border: 1px solid #ccc;">Awal</th>
                <th class="f-table text-right" style="padding: 5px; border: 1px solid #ccc;">Akhir</th>
                <th class="f-table text-right" style="padding: 5px; border: 1px solid #ccc;">Pakai</th>
                <?php endif; ?>

            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0 ;
            foreach ($tagihan as $i => $v) :
            ?>
            <tr>
                <td class="f-table text-right" style="padding: 5px; border: 1px solid #ccc;"><?= $i + 1 ?></td>                        
                <td class="f-table text-center" style="padding: 5px; border: 1px solid #ccc;"><?= $v->periode_penggunaan ?></td>
                <td class="f-table text-center" style="padding: 5px; border: 1px solid #ccc;"><?= $v->periode ?></td>
                <?php if ($total_tagihan->air) : ?>
                <td class="f-table text-right" style="padding: 5px; border: 1px solid #ccc;"><?= $v->meter_awal!==null?number_format($v->meter_awal):'' ?></td>
                <td class="f-table text-right" style="padding: 5px; border: 1px solid #ccc;"><?= $v->meter_akhir!==null?number_format($v->meter_akhir):'' ?></td>
                <td class="f-table text-right" style="padding: 5px; border: 1px solid #ccc;"><?= $v->pakai!==null?number_format($v->pakai):'' ?></td>
                <?php endif; ?>
                <?php if ($total_tagihan->lain) : ?>
                <td class="f-table text-right" style="padding: 5px; border: 1px solid #ccc;"><?= number_format($v->tagihan_lain) ?></td>
                <?php endif; ?>
                <?php if ($total_tagihan->air) : ?>
                <td class="f-table text-right" style="padding: 5px; border: 1px solid #ccc;"><?= number_format($v->air) ?></td>
                <?php endif; ?>
                <?php if ($total_tagihan->ipl) : ?>
                <td class="f-table text-right" style="padding: 5px; border: 1px solid #ccc;"><?= number_format($v->ipl) ?></td>
                <td class="f-table text-right" style="padding: 5px; border: 1px solid #ccc;"><?= number_format($v->ppn) ?></td>
                <?php endif; ?>
                <td class="f-table text-right" style="padding: 5px; border: 1px solid #ccc;"><?= number_format($v->denda) ?></td>
                <?php if ($total_tagihan->tunggakan) : ?>
                <td class="f-table text-right" style="padding: 5px; border: 1px solid #ccc;"><?= number_format($v->tunggakan) ?></td>
                <?php endif; ?>
                <td class="f-table text-right" style="padding: 5px; border: 1px solid #ccc;"><?= number_format($v->total) ?></td>
            </tr>
            <?php
            endforeach;
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td class='f-table' colspan="<?php if ($total_tagihan->air) { echo(5); } else { echo(3); } ?>" style="padding: 5px; border: 1px solid #ccc;"><b>Grand Total :</b></td>
                <?php if ($total_tagihan->air) : ?>
                <td class="f-table text-right" style="border: 1px solid #ccc;"><?= number_format($total_tagihan->pakai) ?></td>
                <?php endif; ?>
                <?php if ($total_tagihan->lain) : ?>
                <td class="f-table text-right" style="border: 1px solid #ccc;"><?= number_format($total_tagihan->lain) ?></td>
                <?php endif; ?>
                <?php if ($total_tagihan->air) : ?>
                <td class="f-table text-right" style="border: 1px solid #ccc;"><?= number_format($total_tagihan->air) ?></td>
                <?php endif; ?>
                <?php if ($total_tagihan->ipl) : ?>
                <td class="f-table text-right" style="border: 1px solid #ccc;"><?= number_format($total_tagihan->ipl) ?></td>
                <td class="f-table text-right" style="border: 1px solid #ccc;"><?= number_format($total_tagihan->ppn) ?></td>
                <?php endif; ?>
                <td class="f-table text-right" style="border: 1px solid #ccc;"><?= number_format($total_tagihan->denda) ?></td>
                <?php if ($total_tagihan->tunggakan) : ?>
                <td class="f-table text-right" style="border: 1px solid #ccc;"><?= number_format($total_tagihan->tunggakan) ?></td>
                <?php endif; ?>

                <td class="f-table text-right" style="border: 1px solid #ccc;"><?= number_format($total_tagihan->total) ?></td>
            </tr>
        </tfoot>
    </table>

    <div <?php if (($i + 1 >= 13 && $i + 1 <= 20) || (((($i + 1) - 20) % 23 >= 20) && ((($i + 1) - 21) % 23 <= 23))) echo ("style='page-break-before: always;'"); ?>>
        <?php if($status_saldo_deposit==1):?>
            <p class="lh-18 f-15" style="margin-bottom:6px;font-weight:bold;">
                Saldo deposit sebesar : Rp.<?=$saldo_deposit?$saldo_deposit:0?>
            </p>
        <?php endif;?>

            <?php if ($project_id == 2): ?>

            <?php else:?>
                
            <?php endif;?>

            Jika Pembayaran dilakukan setelah tanggal 20 bulan berjalan akan dikenakan denda
            kumulatif/penalti. Untuk Informasi lebih lanjut dapat menghubungi Customer Service di
            kantor Estate Office
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
            <br>
            Demikian Informasi yang dapat kami sampaikan, Atas kerjasamanya yang baik kami ucapkan terima
            kasih.
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