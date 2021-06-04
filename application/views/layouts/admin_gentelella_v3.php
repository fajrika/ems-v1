<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Ciputra EMS</title>
    
    
    <link href="http://localhost/emsdev/vendors/switchery/dist/switchery.min.css" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="http://localhost/emsdev/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="http://localhost/emsdev/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="http://localhost/emsdev/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="http://localhost/emsdev/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
	
    <!-- bootstrap-progressbar -->
    <link href="http://localhost/emsdev/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">

    <!-- bootstrap-daterangepicker -->
    <link href="http://localhost/emsdev/vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="http://localhost/emsdev/css/custom.min.css" rel="stylesheet">
    
    <!-- dataTables -->
    <link href="http://localhost/emsdev/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="http://localhost/emsdev/vendors/jquery/dist/jquery.min.js"></script>
    <!-- untuk dragable -->
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <!-- untuk reziable -->
    <link href="https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css" rel="stylesheet"/>

    <!-- switch  -->
</head>
<body class="nav-md" style="display: grid;">
    <div class="container body">
        <div class="main_container">
            <div class="col-md-3 left_col">
                <div class="left_col scroll-view">
                    <div class="clearfix"></div>
                    <!-- menu profile quick info -->
                    <div class="profile clearfix">
                        <div class="profile_pic">
                            <img src="http://localhost/emsdev//images/user.png" alt="..." class="img-circle profile_img">
                        </div>
                        <div class="profile_info">
                            <span>Admin</span>
                            <h2>R Fajrika Hadnis Putra</h2>
                        </div>
                    </div>
                    <!-- /menu profile quick info -->

                    <br>

                    <!-- sidebar menu -->
                    
                    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class='menu_section'><h3 class='menu_section'>Proyek</h3><ul class='nav side-menu'><li><a><i class='fa fa-database'></i>Master<span class='fa fa-chevron-down'></span></a><ul class='nav child_menu'><li><a>Accounting<span class='fa fa-chevron-down'></a><ul class='nav child_menu'><li class='sub_menu'><a href='http://localhost/emsdev/index.php/p_master_pt'>PT</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/p_master_coa'>COA</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_bank'>Bank</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_cara_pembayaran'>Cara Pembayaran</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_metode_penagihan'>Metode Penagihan</></li></ul></li><li><a href='http://localhost/emsdev/index.php/P_master_service' >Service</a><ul class='nav child_menu'></ul></li><li><a href='http://localhost/emsdev/index.php/P_master_paket_service' >Layanan Lain</a><ul class='nav child_menu'></ul></li><li><a>Service LOi<span class='fa fa-chevron-down'></a><ul class='nav child_menu'><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_item_loi'>Item</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_paket_loi'>Paket</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_item_survei_loi'>Item Survei</></li></ul></li><li><a>Service TVI<span class='fa fa-chevron-down'></a><ul class='nav child_menu'><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_group_tvi'>Grup</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_channel'>Channel</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_item_tvi'>Item</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_paket_tvi'>Paket</></li></ul></li><li><a>Tarif<span class='fa fa-chevron-down'></a><ul class='nav child_menu'><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_range_air'>Air</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_range_lingkungan'>Lingkungan</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_golongan'>Golongan</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_sub_golongan'>Sub Golongan</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_pemeliharaan_air'>Pemeliharaan Air</></li></ul></li><li><a href='http://localhost/emsdev/index.php/P_master_customer' >Customers</a><ul class='nav child_menu'></ul></li><li><a href='http://localhost/emsdev/index.php/P_master_purpose_use' >Purpose Use</a><ul class='nav child_menu'></ul></li><li><a>Town Planning<span class='fa fa-chevron-down'></a><ul class='nav child_menu'><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_proyek'>Proyek</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_kawasan'>Kawasan</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_blok'>Blok</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_unit'>Unit</></li></ul></li><li><a>Diskon<span class='fa fa-chevron-down'></a><ul class='nav child_menu'><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_gol_diskon'>Golongan</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/P_master_diskon'>Nilai</></li></ul></li></ul></li></ul><ul class='nav side-menu'><li><a><i class='fa fa-database'></i>Transaksi Service<span class='fa fa-chevron-down'></span></a><ul class='nav child_menu'><li><a href='http://localhost/emsdev/index.php/Transaksi/P_unit' >Dashboard Unit</a><ul class='nav child_menu'></ul></li><li><a href='http://localhost/emsdev/index.php/Transaksi/P_Generate/Lingkungan' >Generate IPL</a><ul class='nav child_menu'></ul></li><li><a href='http://localhost/emsdev/index.php/Transaksi/P_transaksi_meter_air/add' >Pencatatan Meter Air</a><ul class='nav child_menu'></ul></li><li><a href='http://localhost/emsdev/index.php/Transaksi/P_kirim_konfirmasi_tagihan' >Kirim Konfirmasi Tagihan</a><ul class='nav child_menu'></ul></li><li><a href='http://localhost/emsdev/index.php/Transaksi/P_pemutihan/add' >Pemutihan</a><ul class='nav child_menu'></ul></li><li><a>Voucher<span class='fa fa-chevron-down'></a><ul class='nav child_menu'><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Transaksi/P_voucher_tagihan'>Buat Voucher</></li></ul></li></ul></li></ul><ul class='nav side-menu'><li><a><i class='fa fa-database'></i>Transaksi Service Lain<span class='fa fa-chevron-down'></span></a><ul class='nav child_menu'><li><a>TVI<span class='fa fa-chevron-down'></a><ul class='nav child_menu'><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Transaksi_lain/P_registrasi_tvi'>Registrasi</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Transaksi_lain/P_survei_tvi'>Survei</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Transaksi_lain/P_instalasi_tvi'>Instalasi</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Transaksi_lain/P_aktifasi_tvi'>Aktifasi</></li></ul></li><li><a>Sewa Properti<span class='fa fa-chevron-down'></a><ul class='nav child_menu'></ul></li><li><a>LOi<span class='fa fa-chevron-down'></a><ul class='nav child_menu'><li class='sub_menu'><a href='http://localhost/emsdev/index.php/transaksi_lain/P_registrasi_liaison_officer'>Registrasi</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/transaksi_lain/P_survei_liaison_officer'>Survei</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/transaksi_lain/P_pelaksanaan_liaison_officer'>Pelaksanaan</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/transaksi_lain/P_st_liaison_officer'>Serah Terima</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/transaksi_lain/P_monitoring_liaison_officer'>Monitoring</></li></ul></li><li><a>Layanan Lain<span class='fa fa-chevron-down'></a><ul class='nav child_menu'><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Transaksi_lain/P_registrasi_layanan_lain'>Registrasi</></li></ul></li></ul></li></ul><ul class='nav side-menu'><li><a><i class='fa fa-database'></i>Report<span class='fa fa-chevron-down'></span></a><ul class='nav child_menu'><li><a>Tagihan<span class='fa fa-chevron-down'></a><ul class='nav child_menu'><li class='sub_menu'><a href='http://localhost/emsdev/index.php/report/P_history_pembayaran/'>History Pembayaran</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Transaksi/P_tagihan/kawasan'>Total Rencana</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Transaksi/P_belum_tertagih/kawasan'>Belum Tertagih</></li></ul></li><li><a href='http://localhost/emsdev/index.php/Transaksi/P_aging' >Aging</a><ul class='nav child_menu'></ul></li><li><a href='http://localhost/emsdev/index.php/Report/P_Exam' >Exam</a><ul class='nav child_menu'></ul></li></ul></li></ul><ul class='nav side-menu'><li><a><i class='fa fa-database'></i>Approval<span class='fa fa-chevron-down'></span></a><ul class='nav child_menu'><li><a href='http://localhost/emsdev/index.php/P_approval' >Approval Dokumen</a><ul class='nav child_menu'></ul></li></ul></li></ul><ul class='nav side-menu'><li><a><i class='fa fa-database'></i>Setting<span class='fa fa-chevron-down'></span></a><ul class='nav child_menu'><li><a href='http://localhost/emsdev/index.php/Setting/P_parameter_project' >Parameter Project</a><ul class='nav child_menu'></ul></li><li><a>Permission Dokumen<span class='fa fa-chevron-down'></a><ul class='nav child_menu'></ul></li><li><a href='http://localhost/emsdev/index.php/Setting/P_setting_approval' >Setting Approval</a><ul class='nav child_menu'></ul></li></ul></li></ul></div><div class='menu_section'><h3 class='menu_section'>Global</h3><ul class='nav side-menu'><li><a><i class='fa fa-database'></i>Migrasi dari Desktop<span class='fa fa-chevron-down'></span></a><ul class='nav child_menu'><li><a>Core<span class='fa fa-chevron-down'></a><ul class='nav child_menu'><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Sync/Desktop_Unit'>Unit</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Sync/Desktop_tagihan_air/add'>Tagihan Air</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Sync/Desktop_tagihan_lingkungan/add'>Tagihan Lingkungan</></li></ul></li><li><a>Service Pack<span class='fa fa-chevron-down'></a><ul class='nav child_menu'></ul></li></ul></li></ul><ul class='nav side-menu'><li><a><i class='fa fa-database'></i>Setting<span class='fa fa-chevron-down'></span></a><ul class='nav child_menu'><li><a>Akun<span class='fa fa-chevron-down'></a><ul class='nav child_menu'><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Setting/Akun/user'>User</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Setting/Akun/Jabatan'>Jabatan</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Setting/Akun/Group'>Jabatan User</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Setting/Akun/Level'>Level</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Setting/Akun/Group_level'>Group Level</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Setting/Akun/Permission_menu'>Permission Menu</></li><li class='sub_menu'><a href='http://localhost/emsdev/index.php/Setting/Akun/P_permission_dokumen'>Permission Dokumen</></li></ul></li></ul></li></ul></div>                        </div>
                    </div>
                </h3>
                <!-- /sidebar menu -->

            </div>
            <!-- /menu footer buttons -->            <!-- top nav -->
            <div class="top_nav">
                <div class="nav_menu">
                    <nav>
                        <div class="nav toggle">
                            <a id="menu_toggle">
                                <i class="fa fa-bars"></i>
                            </a>
                        </div>
                        <div class="nav toggle col-md-12" style="height:45px;width:70%">
                            <form action="http://localhost/emsdev/index.php/Core/changeJP" method="post">
                                                                <select class="col-md-3 btn btn-default" name="jabatan" id="jabatan" style="font-weight:bold" disabled>
                                    <option value="1">ADMIN</option>
                                </select>

                                <select class="col-md-3 btn btn-default" name="project" id="project" style="font-weight:bold" disabled>
                                    <option value="17">CITRAGRAND CBD CIBUBUR</option>
                                </select>
                                <a id="changeJP" class="btn btn-default col-md-3">Change</a>
                                <input id="saveJP" class="btn btn-default col-md-3" type="submit" value="Save" style="display:none">
                                                            </form>    
                        
                        </div>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="">
                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <img src="http://localhost/emsdev/images/user.png" alt="">R Fajrika Hadnis Putra                                    <span class=" fa fa-angle-down"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-usermenu pull-right">
                                    <li>
                                        <a href="javascript:;"> Profile</a>
                                    </li>
                                    <li>
                                        <a href="javascript:;">
                                            <span class="badge bg-red pull-right">50%</span>
                                            <span>Settings</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;">Help</a>
                                    </li>
                                    <li>
                                        <a href="http://localhost/emsdev/index.php/login/logout">
                                            <i class="fa fa-sign-out pull-right"></i> Log Out
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li role="presentation" class="dropdown">
                                <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-envelope-o"></i>
                                    <!-- <span class="badge bg-green">6</span> -->
                                </a>
                                <!-- <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                                    <li>
                                        <a>
                                            <span class="image"><img src="http://localhost/emsdev/images/user.png" alt="Profile Image"></span>
                                            <span>
                                                <span>John Smith</span>
                                                <span class="time">3 mins ago</span>
                                            </span>
                                            <span class="message">
                                                Film festivals used to be do-or-die moments for movie makers. They were where...
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a>
                                            <span class="image"><img src="http://localhost/emsdev/images/user.png" alt="Profile Image"></span>
                                            <span>
                                                <span>John Smith</span>
                                                <span class="time">3 mins ago</span>
                                            </span>
                                            <span class="message">
                                                Film festivals used to be do-or-die moments for movie makers. They were where...
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                    <a>
                                            <span class="image"><img src="http://localhost/emsdev/images/user.png" alt="Profile Image"></span>
                                            <span>
                                                <span>John Smith</span>
                                                <span class="time">3 mins ago</span>
                                            </span>
                                            <span class="message">
                                                Film festivals used to be do-or-die moments for movie makers. They were where...
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a>
                                            <span class="image"><img src="http://localhost/emsdev/images/user.png" alt="Profile Image"></span>
                                            <span>
                                                <span>John Smith</span>
                                                <span class="time">3 mins ago</span>
                                            </span>
                                            <span class="message">
                                                Film festivals used to be do-or-die moments for movie makers. They were where...
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="text-center">
                                            <a>
                                                <strong>See All Alerts</strong>
                                                <i class="fa fa-angle-right"></i>
                                            </a>
                                        </div>
                                    </li>
                                </ul> -->
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- /top nav -->
        </div>
        
<style type="text/css">
	@keyframes lds-double-ring {
		0% {
			-webkit-transform: rotate(0);
			transform: rotate(0);
		}

		100% {
			-webkit-transform: rotate(360deg);
			transform: rotate(360deg);
		}
	}

	@-webkit-keyframes lds-double-ring {
		0% {
			-webkit-transform: rotate(0);
			transform: rotate(0);
		}

		100% {
			-webkit-transform: rotate(360deg);
			transform: rotate(360deg);
		}
	}

	@keyframes lds-double-ring_reverse {
		0% {
			-webkit-transform: rotate(0);
			transform: rotate(0);
		}

		100% {
			-webkit-transform: rotate(-360deg);
			transform: rotate(-360deg);
		}
	}

	@-webkit-keyframes lds-double-ring_reverse {
		0% {
			-webkit-transform: rotate(0);
			transform: rotate(0);
		}

		100% {
			-webkit-transform: rotate(-360deg);
			transform: rotate(-360deg);
		}
	}

	.lds-double-ring {
		position: absolute;
		z-index: 99;
        margin-top: 20%;
	}

	.lds-double-ring div {
		position: absolute;
		width: 160px;
		height: 160px;
		top: 20px;
		left: 20px;
		border-radius: 50%;
		border: 8px solid #000;
		border-color: #1d3f72 transparent #1d3f72 transparent;
		-webkit-animation: lds-double-ring 2s linear infinite;
		animation: lds-double-ring 2s linear infinite;
	}

	.lds-double-ring div:nth-child(2) {
		width: 140px;
		height: 140px;
		top: 30px;
		left: 30px;
		border-color: transparent #5699d2 transparent #5699d2;
		-webkit-animation: lds-double-ring_reverse 2s linear infinite;
		animation: lds-double-ring_reverse 2s linear infinite;
	}

	.lds-double-ring {
		width: 200px !important;
		height: 200px !important;
		-webkit-transform: translate(-100px, -100px) scale(1) translate(100px, 100px);
		transform: translate(-100px, -100px) scale(1) translate(100px, 100px);
	}

</style>
<!-- body -->
<div class="right_col" role="main" style="min-height: 100vh;">
	<div id="loading" class="lds-css ng-scope" hidden style="position: absolute;
    z-index: 9999999;
    left: 50%;
    top: 45%;">
		<div style="width:100%;height:100%" class="col-md-offset-4 lds-double-ring">
			<div></div>
			<div></div>
		</div>
	</div>

	<div class>
		<div class="page-title">
			<div class="title_left" style="margin-bottom: 10px">
				<h3>
					Master > PT				</h3>
			</div>
			<div class="clearfix"></div>
			<div id='content' class="row">
				<div class="col-md-12 col-sm-12">
					<div class="x_panel">
						<div class="x_title">
							<div class="col-md-6">
								<h2>
									List								</h2>
							</div><!DOCTYPE html>
<div style="float:right">
    <h2>
        <button class="btn btn-success" onClick="window.open(window.location.href,'_self')">Refresh</button>
    </h2>
</div>
<div class="clearfix"></div>
</div>
<div class="x_content">
    <table id="tableDT" class="table table-striped jambo_table tableDT">
        <tfoot id="tfoot" style="display: table-header-group">
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama PT</th>
                <th>API Key</th>
                <th>Alamat</th>
                <th>Telepon</th>
                <th>NPWP</th>
                <th>Kode Pos</th>
                <th>Edit</th>
            </tr>
        </tfoot>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama PT</th>
                <th>API Key</th>
                <th>Alamat</th>
                <th>Telepon</th>
                <th>NPWP</th>
                <th>Kode Pos</th>
                <th>Edit Api Key</th>
            </tr>
        </thead>
        <tbody>
                            <tr>
                    <td>1</td>
                    <td>XXX</td>
                    <td>PT. Kosong</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <a class="btn btn-primary" href='http://localhost/emsdev/index.php/p_master_pt/edit?id=2095'>
                            Edit
                        </a>
                    </td>
                    </td>
                </tr>
                            <tr>
                    <td>2</td>
                    <td>A02</td>
                    <td>PT. CIPUTRA NUGRAHA INTERNASIONAL</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <a class="btn btn-primary" href='http://localhost/emsdev/index.php/p_master_pt/edit?id=2097'>
                            Edit
                        </a>
                    </td>
                    </td>
                </tr>
                    </tbody>
    </table>                                
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>    </div>

    <!-- Bootstrap -->
    <script src="http://localhost/emsdev/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="http://localhost/emsdev/vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="http://localhost/emsdev/vendors/nprogress/nprogress.js"></script>
    <!-- Chart.js -->
    <script src="http://localhost/emsdev/vendors/Chart.js/dist/Chart.min.js"></script>
    <!-- gauge.js -->
    <script src="http://localhost/emsdev/vendors/gauge.js/dist/gauge.min.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="http://localhost/emsdev/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="http://localhost/emsdev/vendors/iCheck/icheck.min.js"></script>
    <!-- Skycons -->
    <script src="http://localhost/emsdev/vendors/skycons/skycons.js"></script>
    <!-- Flot -->
    <script src="http://localhost/emsdev/vendors/Flot/jquery.flot.js"></script>
    <script src="http://localhost/emsdev/vendors/Flot/jquery.flot.pie.js"></script>
    <script src="http://localhost/emsdev/vendors/Flot/jquery.flot.time.js"></script>
    <script src="http://localhost/emsdev/vendors/Flot/jquery.flot.stack.js"></script>
    <script src="http://localhost/emsdev/vendors/Flot/jquery.flot.resize.js"></script>
    <!-- Flot plugins -->
    <script src="http://localhost/emsdev/vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
    <script src="http://localhost/emsdev/vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
    <script src="http://localhost/emsdev/vendors/flot.curvedlines/curvedLines.js"></script>
    <!-- DateJS -->
    <script src="http://localhost/emsdev/vendors/DateJS/build/date.js"></script>
    <!-- JQVMap -->
    <script src="http://localhost/emsdev/vendors/jqvmap/dist/jquery.vmap.js"></script>
    <script src="http://localhost/emsdev/vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
    <script src="http://localhost/emsdev/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="http://localhost/emsdev/vendors/moment/min/moment.min.js"></script>
    <script src="http://localhost/emsdev/vendors/bootstrap-daterangepicker/daterangepicker.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="http://localhost/emsdev/js/custom.js"></script>
    <!-- dataTables -->
    <script type="text/javascript" src="http://localhost/emsdev/vendors/datatables.net/js/jquery.dataTables.min.js"></script>

    <!-- PNotify -->
    <script src="http://localhost/emsdev/vendors/pnotify/dist/pnotify.js"></script>
    <script src="http://localhost/emsdev/vendors/pnotify/dist/pnotify.buttons.js"></script>
    <script src="http://localhost/emsdev/vendors/pnotify/dist/pnotify.nonblock.js"></script>
    <!-- <pre>stdClass Object
(
    [url] => p_master_pt
    [read] => 1
    [create] => 1
    [update] => 1
    [delete] => 1
)
</pre> -->
    <script>
        $('.modal').modal({ 
            keyboard: false,
            show:false
        });
        // Jquery draggable
        $('.modal-dialog').draggable({
            // handle: ".modal-header"
        });
        $('.modal-dialog').resizable({
            handle: ".modal-header"
        });
        // $('#modal-iframe-large').on('show.bs.modal', function () {
        //     $(this).find('.modal-body').css({
        //         'max-height':'100%'
        //     });
        // });
        $(function() {

            
            // Setup - add a text input to each footer cell
            
            $('.tableDT tfoot th').each( function () {
                var title = $(this).text();
                $(this).html( '<input type="text" placeholder="Filter '+title+'" />' );
            } );
        
            // DataTable

            // Apply the search
            var table = $('.tableDT').DataTable();
            var table2 = $('.bulk_action').dataTable();
            if(0){
                var index = 0;
                $.each($("button"),function(k,v){
                    if ($("button").eq(k).html() == 'Tambah')
                        index = k;
                })
                console.log(index);
                $("button").eq(index).hide()
            }
            if(0){
                var index = 0;
                $.each($("table").children('thead').children().children(),function(k,v){
                    if ($("table").children('thead').children().children().eq(k).html() == 'Delete')
                        index = k;
                })
                console.log(index);
                table2.fnSetColumnVis(index,false);
            }
            if(0){
                var index = 0;
                $.each($("table").children('thead').children().children(),function(k,v){
                    if ($("table").children('thead').children().children().eq(k).html() == 'Action')
                        index = k;
                })
                console.log(index);
                table2.fnSetColumnVis(index,false);
            }
            
            table.columns().every( function () {
                var that = this;
                $( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                } );
            } );

            $(".right_col").css('height', document.getElementById('content').clientHeight+170);

            $('#content').resize(function() {
                $(".right_col").css('height', document.getElementById('content').clientHeight+170);
            });
        });
        
        $("#changeJP").click(function(){
            url = 'http://localhost/emsdev/index.php/core/get_jabatan';
            $.ajax({
                url: url,
                dataType: "json",
                success: function(data){
                    var items = []; 
                    $("#changeJP").attr("style","display:none");
                    $("#saveJP").removeAttr('style');
                    $("#jabatan").removeAttr('disabled');
                    $("#jabatan")[0].innerHTML = "";
                    $("#project")[0].innerHTML = "";
                    $("#jabatan").append("<option value='' selected disabled>Pilih Jabatan</option>");
                    $.each(data, function(key, val){
                        $("#jabatan").append("<option value='" + val.id + "'>" + val.name.toUpperCase()+ "</option>");   
                    });
                }
            });

        });
        $("#jabatan").change(function(){
            url = 'http://localhost/emsdev/index.php/core/get_project';
            console.log(this.value);
            $.ajax({
                type: "post",
                url: url,
                data: {jabatan:this.value},
                dataType: "json",
                success: function(data){
                    console.log(data);
                    $("#project").removeAttr('disabled');
                    $("#project")[0].innerHTML = "";
                    $("#project").append("<option value='' selected disabled>Pilih Project</option>");
                    $.each(data, function(key, val){
                        $("#project").append("<option value='" + val.id + "'>" + val.name.toUpperCase()+ "</option>");   
                    });
                    // $("#project").removeAttr('disabled');
                    // var items = []; 
                    // $("#changeJP").attr("style","display:none");
                    // $("#saveJP").removeAttr('style');
                    // $("#jabatan").removeAttr('disabled');
                    // $("#project").removeAttr('disabled');
                    // $("#jabatan")[0].innerHTML = "";
                    // $("#project")[0].innerHTML = "";
                    // $("#jabatan").append("<option value='' selected disabled>Pilih Jabatan</option>");
                    // $.each(data, function(key, val){
                    //     $("#jabatan").append("<option value='" + val.id + "'>" + val.name.toUpperCase()+ "</option>");   
                    // });
                }
            });
        });

        $(document).ajaxStart(function(){
            $("#loading").show();
        });

        $(document).ajaxComplete(function(){
            $("#loading").hide();
        });
    </script>
</body>
</html>