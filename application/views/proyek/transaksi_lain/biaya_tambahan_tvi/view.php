<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
    <div style="float:right">
        <h2>
            <button class="btn btn-primary" onClick="window.location.href='<?=site_url(); ?>/transaksi_lain/p_biaya_tambahan_tvi/add'">
                <i class="fa fa-plus"></i>                
                Tambah
            </button>
            <button class="btn btn-warning" onClick="window.history.back()" disabled>
                <i class="fa fa-arrow-left"></i>
                Back
            </button>
            <button class="btn btn-success" onClick="window.location.href='<?=site_url()?>/transaksi_lain/p_biaya_tamabahan_tvi'">>
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
                  <tr>
                  <th>No</th>
                  <th>Kawasan</th>
                  <th>Blok</th>
                  <th>Unit No</th>
                  <th>Customer Name</th>
                  <th>Nomor Tagihan</th>
                  <th>Tanggal Tagihan</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Action</th>
                  <th></th>
                  <th></th>
                 </tr>
                </thead>
                <tbody>
                <?php
                     $i = 0;
                     foreach ($data as $key => $v) {
                         ++$i;
                         echo'<tr>';
                         echo "<td>$i</td>";
                         echo "<td>$v[kawasan_name]</td>";
                         echo "<td>$v[blok_name]</td>";
                         echo "<td>$v[unit_no]</td>";
                         echo "<td>$v[customer_name]</td>";
                         echo "<td>$v[nomor_tagihan]</td>";
                         echo "<td>$v[tanggal_tagihan]</td>";
                         echo '<td>'.number_format($v['total_tagihan']).'</td>';
                         echo '<td>';
                         echo $v['active'] ? 'Aktif' : 'Tidak Aktif';
                         echo '</td>';
                         if ($v['status_bayar'] == 0 )
                         { 
                         echo"
                         <td class='col-md-1'>
                         <a href='".site_url()."/transaksi_lain/P_pembayaran_tvi/pembayaran_biaya_tambahan?id=$v[id]' class='btn btn-sm btn-warning col-md-12'>
                             <i class='fa fa-money'></i>
                         </a>
                         </td>
                          ";
                         }
                         else
                         {
                         echo"
                            <td class='col-md-1'>
                            <a href='#' class='btn btn-sm btn-warning col-md-12' disabled>
                            <i class='fa fa-money'></i>
                          </a>
                         </td>
                         ";


                         }
                         
                        echo"
                             <td class='col-md-1'>
                             <a href='".site_url()."/transaksi_lain/P_biaya_tambahan_tvi/edit?id=$v[id]' class='btn btn-sm btn-primary col-md-12'>
                                 <i class='fa fa-pencil'></i>
                             </a>
                             </td>
                         ";
                         echo "
                         <td class='col-md-1'>
						          	<a href='#'  class='btn btn-md btn-danger col-md-12' data-toggle='modal' 
						           	onclick='confirm_modal(
						       		$v[id]
						         	)'";
                         echo " data-target='#myModal'> ";
                        echo "<i class='fa fa-trash'></i>
						        	</a>
						          </td> ";

                        echo '</tr>';
                     }
                ?>
                </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<!-- (Normal Modal)-->
	<div class="modal fade" id="modal_delete_m_n" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content" style="margin-top:100px;">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" style="text-align:center;">Apakah anda yakin untuk mendelete data ini ?<span class="grt"></span> ?</h4>
				</div>

				<div class="modal-footer" style="margin:0px; border-top:0px; text-align:center;">
					<span id="preloader-delete"></span>
					</br>
					<a class="btn btn-danger" id="delete_link_m_n" href="">Delete</a>
					<button type="button" class="btn btn-info" data-dismiss="modal" id="delete_cancel_link">Cancel</button>

				</div>
			</div>
		</div>
	</div>
	<script>
		function confirm_modal(id) {
			jQuery('#modal_delete_m_n').modal('show', {
				backdrop: 'static',
				keyboard: false
			});
			document.getElementById('delete_link_m_n').setAttribute("href", "<?= site_url('P_master_cara_pembayaran/delete?id="+id+"'); ?>");
			document.getElementById('delete_link_m_n').focus();
		}

	</script>
