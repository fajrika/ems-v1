<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<div style="float:right">
	<h2>

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
	<div class="row">
		<div class="col-sm-12">
			<div class="card-box table-responsive">
				<table class="table table-striped jambo_table tableDT">
					<thead>
						<tr>
							<th>No</th>
							<th>Nama Kawasan</th>
							<th>Nama Blok</th>
							<th>Deskripsi</th>
							<th>Unit</th>
						</tr>
					</thead>
					<tfoot id="tfoot" style="display: table-header-group">
						<tr>
							<th>No</th>
							<th>Nama Kawasan</th>
							<th>Nama Blok</th>
							<th>Deskripsi</th>
							<th hidden>Unit</th>
						</tr>
					</tfoot>
					<tbody>
						<?php 
                            $i = 0;
                            foreach ($data as $key => $v) {
                                ++$i;
                                echo '<tr>';
                                echo "<td>$i</td>";
                                echo "<td>$v[kawasan_name]</td>";
                                echo "<td>$v[blok_name]</td>";
                                echo "<td>$v[blok_desc]</td>";
                                echo "
                                    <td>
                                    <a href='".site_url()."/P_master_unit/index' class='btn btn-primary col-md-10'>
                                        <i class='fa fa-pencil'></i>
                                    </a>
                                    </td>
                                ";
                                echo '</td></tr>';
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
