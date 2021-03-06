<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!DOCTYPE html>

<link href="<?= base_url() ?>vendors/select2/dist/css/select2.min.css" rel="stylesheet">
<script src="<?= base_url() ?>vendors/select2/dist/js/select2.min.js"></script>

<div style="float:right">
    <h2>
        <!-- <button class="btn btn-primary" onClick="window.location.href='<?= site_url() ?>/P_master_purpose_use/add'">
            <i class="fa fa-plus"></i>
            Tambah
        </button> -->
        <button class="btn btn-warning" onClick="window.history.back()" disabled>
            <i class="fa fa-arrow-left"></i>
            Back
        </button>
        <button class="btn btn-success" onClick="window.open(window.location.href,'_self')">
            <i class="fa fa-repeat"></i>
            Refresh
        </button>
    </h2>
</div>

<div class="clearfix"></div>
</div>

<div class="x_content">
    <table class="table table-striped jambo_table tableDT">
        <tfoot id="tfoot" style="display: table-header-group">
            <tr>
                <th>No</th>
                <th>Nama Unit Type</th>
                <th>Keterangan</th>
                <th>Status</th>
                <!-- <th hidden>Action</th>
                <th hidden>Delete</th> -->
            </tr>
        </tfoot>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Unit Type</th>
                <th>Keterangan</th>
                <th>Status</th>
                <!-- <th>Action</th>
                <th>Delete</th> -->
            </tr>
        </thead>

        <tbody>
            <?php
            $i = 0;
            foreach ($data as $key => $v) {
                $i++;
                echo "<tr>";
                echo "  <td>$i</td>";
                echo "  <td>$v->name</td>";
                echo "  <td>$v->description</td>";
                echo "  <td>";
                echo $v->active ? 'Aktif' : 'Tidak Aktif';
                echo "  </td>";
                // echo "  <td class='col-md-1'>
                //             <a href='" . site_url() . "/P_master_purpose_use/edit?id=$v->id' class='btn btn-sm btn-primary col-md-12'>
                //                 <i class='fa fa-pencil'></i>
                //             </a>
                //         </td>";
                // echo "
                //         <td class='col-md-1'>
                //             <a href='#'  class='btn btn-md btn-danger col-md-12' data-toggle='modal' 
                //             onclick='confirm_modal(
                //                 $v->id
                //             )'";
                // echo " data-target='#myModal'> ";
                // echo "<i class='fa fa-trash'></i>
                //             </a>
                //         </td> ";
                echo '</tr>';
            }
            ?>
        </tbody>

    </table>
</div>

<!-- (Normal Modal)-->
<!-- <div class="modal fade" id="modal_delete_m_n" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content" style="margin-top:100px;">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" style="text-align:center;">Apakah anda yakin untuk mendelete data ini ?<span class="grt"></span>
                    ?</h4>
            </div>

            <div class="modal-footer" style="margin:0px; border-top:0px; text-align:center;">
                <span id="preloader-delete"></span>
                <a class="btn btn-danger" id="delete_link_m_n" href="">Delete</a>
                <button type="button" class="btn btn-info" data-dismiss="modal" id="delete_cancel_link">Cancel</button>

            </div>
        </div>
    </div>
</div> -->


<script>
    function confirm_modal(id) {
        jQuery('#modal_delete_m_n').modal('show', {
            backdrop: 'static',
            keyboard: false
        });
        document.getElementById('delete_link_m_n').setAttribute("href",
            "<?= site_url('P_master_purpose_use/delete?id=" + id + "'); ?>");
        document.getElementById('delete_link_m_n').focus();
    }
</script>