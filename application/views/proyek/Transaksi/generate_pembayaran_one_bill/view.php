<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
    <div style="float:right">
        <h2>
            <button class="btn btn-primary" onClick="window.location.href='<?=site_url()?>/P_transaksi_meter_air/add'">
                <i class="fa fa-plus"></i>                
                Tambah
            </button>
            <button class="btn btn-warning" onClick="window.history.back()" disabled>
                <i class="fa fa-arrow-left"></i>
                Back
            </button>
            <button class="btn btn-success" onClick="window.location.href='<?=site_url()?>/P_transaksi_meter_air'">
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
            <table class="table table-striped jambo_table tableDT">
              <thead>
              <th>Kode Blok</th>
              <th>Nama</th>
              <th>Nomor</th>
              <th>Pembayaran</th>
              <th>Tanggal Bayar</th>
              <th>Catatan</th>
              <th>Periode</th>
              <th>Nilai Total</th>
              </thead>
              <tbody>
               <?php
                //$i = 0;
                //foreach ($data as $key => $v){
                //$i++;
               // echo('<tr>');
                //    echo("<td>$i</td>");
				//	echo("<td>$v[code]</td>");
                //    echo("<td>$v[name]</td>");
				//	echo("<td>$v[description]</td>");
                 //  	echo("<td>");
				//	echo($v['active']?'Aktif':'Tidak Aktif');
				//	echo("</td>");
                //   echo("
                 //       <td>
                 //       <a href='".site_url()."/P_transaksi_meter_air/edit?id=$v[id]' class='btn btn-primary col-md-10'>
                 //           <i class='fa fa-pencil'></i>
                 //       </a>
                 //       </td>
                 //   ");
                 //   echo('</td></tr>');                
               // }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>