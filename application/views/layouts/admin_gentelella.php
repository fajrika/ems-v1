<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= base_url() ?>/assets/gentelella/build/images-production/favicon.ico" type="image/ico" />

    <title>EMS</title>

    <!-- Bootstrap -->
    <link href="<?= base_url("assets/gentelella/bootstrap/dist/css/bootstrap.min.css") ?>" rel="stylesheet">
    <!-- <link href="<?= base_url("vendors/bootstrap/dist/css/bootstrap.min.css") ?>" rel="stylesheet"> -->
    <!-- Font Awesome -->
    <link href="<?= base_url("assets/gentelella/font-awesome/css/font-awesome.min.css") ?>" rel="stylesheet">
    <!-- NProgress -->
    <link href="<?= base_url("assets/gentelella/nprogress/nprogress.css") ?>" rel="stylesheet">
    <!-- iCheck -->
    <link href="<?= base_url("assets/gentelella/iCheck/skins/flat/green.css") ?>" rel="stylesheet">

    <!-- bootstrap-progressbar -->
    <link href="<?= base_url("assets/gentelella/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css") ?>" rel="stylesheet">
    <!-- JQVMap -->
    <link href="<?= base_url("assets/gentelella/jqvmap/dist/jqvmap.min.css") ?>" rel="stylesheet" />
    <!-- bootstrap-daterangepicker -->
    <link href="<?= base_url("assets/gentelella/bootstrap-daterangepicker/daterangepicker.css") ?>" rel=" stylesheet">

    <?= isset($css) ? $css : '' ?>

    <!-- Custom Theme Style -->
    <!-- <link href="<?= base_url("css/custom.min.css") ?>" rel="stylesheet"> -->
    <link href="<?= base_url("assets/gentelella/build/css/custom.min.css") ?>" rel="stylesheet">
    <link href="<?= base_url("assets/gentelella/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css") ?>" rel="stylesheet">

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

        /* .dropdown-menu>li>a {
            padding: 12px 20px;
            width: 100%;
            display: block;
        } */

        /* .dropdown-menu>li>a:hover {
            color: #262626;
            text-decoration: none;
            background-color: #f5f5f5;
        } */

        #jabatan,
        #project {
            /* background-color: #fff;
            color: #333; */
            -webkit-appearance: button-arrow-down;
            -moz-appearance: button-arrow-down;
            font-size: 14px
        }

        #jabatan,
        #project,
        #changeJP {
            font-size: 14px
        }

        #jabatan:disabled,
        #project:disabled {
            /* background-color: #fff;
            color: #333; */

            -webkit-appearance: none;
            -moz-appearance: none;

        }

        /* nav>.toggle {
            float: left;
            margin: 0;
            padding-top: 16px;
            width: 70px;
        } */
        .nav.toggle.form {
            float: left;
            margin: 0;
            padding-top: 16px;
            width: 70px;
        }

        .nav.navbar-nav.navbar-right {
            margin: 5px;
        }
    </style>
    <!-- <style>
        tfoot .hide input {
            pointer-events: none;
            background-color: rgba(239, 239, 239, 0.3);
            border: rgba(118, 118, 118, 0.3) double 1px;
        }

        .dataTables_length {
            float: left;
        }

        .dt-buttons.btn-group {
            float: right;

        }

        .buttons-columnVisibility {
            padding: 5px;
            background-color: #26b99a;
        }

        .buttons-columnVisibility:hover {
            background-color: #169f85;
        }

        .buttons-columnVisibility.active {
            background-color: #118972;
        }

        .buttons-columnVisibility.active:hover {
            background-color: #26b99a;
        }

        .dropdown-menu>.buttons-columnVisibility>a {
            color: white;
        }

        table thead {
            background: rgba(52, 73, 94, .94);
            color: #ECF0F1;
        }

        .pagination>.paginate_button>a {
            margin: 0 3px;
            border-radius: 5px;
            background-color: rgba(52, 73, 94, .94) !important;
            color: white;
        }

        .pagination>.active>a {
            margin: 0 3px;
            border-radius: 5px;
            background-color: #337ab7 !important;
            color: white;
        }

        div#datatable-responsive_processing {
            height: 50px;
            background-color: rgb(38, 185, 154);
            color: white;
            display: none;
        }
    </style> -->
</head>

<body class="nav-md" onload="startTime()" style="display: none;">
    <div class="container body">
        <div class="main_container">
            <div class="col-md-3 left_col">
                <div class="left_col scroll-view">
                    <!-- <div class="navbar nav_title" style="border: 0;">
                        <a href="index.html" class="site_title"><i class="fa fa-paw"></i> <span>EMS | SH2</span></a>
                    </div> -->

                    <div class="clearfix"></div>
                    <!-- menu profile quick info -->
                    <div class="profile clearfix">
                        <div class="profile_pic">
                            <img src="<?= base_url("/images/user.png") ?>" alt="..." class="img-circle profile_img">
                        </div>
                        <div class="profile_info">

                            <span>Admin</span>
                            <h2> <?= $this->session->userdata('name') ?> </h2>
                        </div>
                    </div>
                    <!-- /menu profile quick info -->

                    <br />

                    <!-- sidebar menu -->
                    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                        <?php
                        foreach ($GLOBALS['menu']['level1'] as $level1) {
                            echo ("<div class='menu_section'>");
                            echo ("<h3 class='menu_section'>");
                            echo ($level1['name1']);
                            echo ("</h3>");

                            foreach ($GLOBALS['menu']['level2'] as $level2) {
                                if ($level2['id2'] == $level1['id1']) {
                                    echo ("<ul class='nav side-menu'>");
                                    echo ("<li>");
                                    echo ("<a>");
                                    echo ("<i class='fa fa-database'></i>");
                                    echo ($level2['name1']);
                                    echo ("<span class='fa fa-chevron-down'></span>");
                                    echo ("</a>");
                                    echo ("<ul class='nav child_menu'>");
                                    foreach ($GLOBALS['menu']['level3'] as $level3) {
                                        if ($level3['id2'] == $level2['id1']) {
                                            echo ("<li>");
                                            echo ("<a");
                                            if ($level3['url']) {
                                                echo (" href='");
                                                echo (site_url());
                                                echo ("/$level3[url]");
                                                echo ("' >");
                                                echo ($level3['name1']);
                                            } else {
                                                echo (">");
                                                echo ($level3['name1']);
                                                echo ("<span class='fa fa-chevron-down'>");
                                            }
                                            echo ("</a>");
                                            if ($GLOBALS['menu']['level4']) {
                                                echo ("<ul class='nav child_menu'>");
                                                foreach ($GLOBALS['menu']['level4'] as $level4) {
                                                    if ($level4['id2'] == $level3['id1'] && $level4['akses'] == 1) {
                                                        echo ("<li class='sub_menu'>");
                                                        echo ("<a href='");
                                                        echo (site_url());
                                                        echo ("/$level4[url]");
                                                        echo ("'>");
                                                        echo ($level4['name1']);
                                                        echo ("</a>");
                                                        echo ("</li>");
                                                    }
                                                }
                                                echo ("</ul>");
                                            }
                                            echo ("</li>");
                                        }
                                    }
                                    echo ("</ul>");
                                    echo ("</li>");
                                    echo ("</ul>");
                                }
                            }
                            echo ("</div>");
                        }
                        ?>

                    </div>
                    <!-- /sidebar menu -->

                    <!-- /menu footer buttons -->
                    <div class="sidebar-footer hidden-small">
                        <a id="clock">

                        </a>
                        <a id="toggleFullScreen" data-toggle="tooltip" data-placement="top" title="FullScreen">
                            <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
                        </a>
                        <a id='blur' data-toggle="tooltip" data-placement="top" title="Lock">
                            <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                        </a>
                        <a data-toggle="tooltip" data-placement="top" title="Logout" href="<?= site_url() ?>/login/logout">
                            <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                        </a>
                    </div>
                    <!-- /menu footer buttons -->
                </div>
            </div>

            <!-- top navigation -->
            <div class="top_nav">
                <div class="nav_menu" style="border-radius: 0px 0px 15px 15px;">
                    <nav>
                        <div class="nav toggle">
                            <a id="menu_toggle">
                                <i class="fa fa-bars"></i>
                            </a>
                        </div>
                        <div class="nav toggle form col-md-12" style="height:45px;width:70%;">
                            <form action="<?= site_url() ?>/Core/changeJP" method="post" style="width: 100%;">
                                <?php
                                if ($GLOBALS['jabatan']) :
                                ?>
                                    <select class="col-md-3 btn btn-light" name="jabatan" id="jabatan" style="font-weight:bold" disabled>
                                        <option value="<?= $GLOBALS['jabatan']->id ?>"><?= strtoupper($GLOBALS['jabatan']->name) ?></option>
                                    </select>

                                    <select class="col-md-3 btn btn-light" name="project" id="project" style="font-weight:bold" disabled>
                                        <option value="<?= $GLOBALS['project']->id ?>"><?= strtoupper($GLOBALS['project']->name) ?></option>
                                    </select>
                                    <a id="changeJP" class="btn btn-light col-md-3" style="padding: 6px 12px;">Change</a>
                                    <input id="saveJP" class="btn btn-light col-md-3" type="submit" value="Save" style="padding: 6px 12px;display:none">
                                <?php
                                else :
                                ?>
                                    <select class="col-md-3 btn btn-light" name="jabatan" id="jabatan" disabled>
                                        <option>SuperAdmin</option>
                                    </select>
                                <?php endif; ?>
                            </form>

                        </div>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="">
                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">

                                    <img src="<?= base_url() ?>images/user.png" alt=""><?= ucwords($this->session->userdata('name')) ?>
                                    <span class=" fa fa-angle-down"></span>
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
                                        <a href="<?= site_url() ?>/login/logout">
                                            <i class="fa fa-sign-out pull-right"></i> Log Out
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li role="presentation" class="dropdown">
                                <!-- <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false"> -->
                                <!-- <i class="fa fa-envelope-o"></i> -->
                                <!-- <span class="badge bg-green">6</span> -->
                                <!-- </a> -->
                                <!-- <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                                    <li>
                                        <a>
                                            <span class="image"><img src="<?= base_url() ?>images/user.png" alt="Profile Image"></span>
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
                                            <span class="image"><img src="<?= base_url() ?>images/user.png" alt="Profile Image"></span>
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
                                            <span class="image"><img src="<?= base_url() ?>images/user.png" alt="Profile Image"></span>
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
                                            <span class="image"><img src="<?= base_url() ?>images/user.png" alt="Profile Image"></span>
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
            <!-- /top navigation -->

            <!-- page content -->
            <div class="right_col" role="main">
                <div class="">
                    <div class="page-title">

                        <div class="title_left d-block d-sm-block d-md-none">
                            <h3><?= substr($title_submenu, strpos($title_submenu, '>', strpos($title_submenu, '>') + 2) + 2) ?></h3>
                        </div>
                        <div class="title_left d-none d-sm-none d-md-block d-lg-none">
                            <h3><?= substr($title_submenu, strpos($title_submenu, '>') + 2) ?></h3>
                        </div>
                        <div class="title_left d-none d-sm-none d-md-none d-lg-block d-xl-block">
                            <h3><?= $title_submenu ?></h3>
                        </div>

                        <div class="title_right">
                            <div class="col-md-5 col-sm-5  form-group pull-right top_search" hidden>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search for...">
                                    <span class="input-group-btn">
                                        <button class="btn btn-light" type="button">Go!</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div id="content" class="row">
                        <?= isset($content) ? $content : '' ?>
                    </div>
                </div>
            </div>
            <!-- /page content -->

            <!-- footer content -->
            <footer>
                <div class="pull-right">
                    Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>
                </div>
                <div class="clearfix"></div>
            </footer>
            <!-- /footer content -->
        </div>
    </div>

    <!-- jQuery -->
    <script src="<?= base_url("assets/gentelella/jquery/dist/jquery.min.js") ?>"></script>
    <script src="<?= base_url("assets/gentelella/moment/moment.js") ?>"></script>

    <!-- Bootstrap -->
    <script src="<?= base_url("assets/gentelella/bootstrap/dist/js/bootstrap.bundle.min.js") ?>"></script>
    <!-- FastClick -->
    <script src="<?= base_url("assets/gentelella/fastclick/lib/fastclick.js") ?>"></script>
    <!-- NProgress -->
    <script src="<?= base_url("assets/gentelella/nprogress/nprogress.js") ?>"></script>
    <!-- Chart.js -->
    <script src="<?= base_url("assets/gentelella/Chart.js/dist/Chart.min.js") ?>"></script>
    <!-- gauge.js -->
    <script src="<?= base_url("assets/gentelella/gauge.js/dist/gauge.min.js") ?>"></script>
    <!-- bootstrap-progressbar -->
    <script src="<?= base_url("assets/gentelella/bootstrap-progressbar/bootstrap-progressbar.min.js") ?>"></script>
    <!-- iCheck -->
    <script src="<?= base_url("assets/gentelella/iCheck/icheck.min.js") ?>"></script>
    <!-- Skycons -->
    <script src="<?= base_url("assets/gentelella/skycons/skycons.js") ?>"></script>
    <!-- Flot -->
    <script src="<?= base_url("assets/gentelella/Flot/jquery.flot.js") ?>"></script>
    <script src="<?= base_url("assets/gentelella/Flot/jquery.flot.pie.js") ?>"></script>
    <script src="<?= base_url("assets/gentelella/Flot/jquery.flot.time.js") ?>"></script>
    <script src="<?= base_url("assets/gentelella/Flot/jquery.flot.stack.js") ?>"></script>
    <script src="<?= base_url("assets/gentelella/Flot/jquery.flot.resize.js") ?>"></script>
    <!-- Flot plugins -->
    <script src="<?= base_url("assets/gentelella/flot.orderbars/js/jquery.flot.orderBars.js") ?>"></script>
    <script src="<?= base_url("assets/gentelella/flot-spline/js/jquery.flot.spline.min.js") ?>"></script>
    <script src="<?= base_url("assets/gentelella/flot.curvedlines/curvedLines.js") ?>"></script>
    <!-- DateJS -->
    <script src="<?= base_url("assets/gentelella/DateJS/build/date.js") ?>"></script>
    <!-- JQVMap -->
    <script src="<?= base_url("assets/gentelella/jqvmap/dist/jquery.vmap.js") ?>"></script>
    <script src="<?= base_url("assets/gentelella/jqvmap/dist/maps/jquery.vmap.world.js") ?>"></script>
    <script src="<?= base_url("assets/gentelella/jqvmap/examples/js/jquery.vmap.sampledata.js") ?>"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="<?= base_url("assets/gentelella/moment/min/moment.min.js") ?>"></script>
    <script src="<?= base_url("assets/gentelella/bootstrap-daterangepicker/daterangepicker.js") ?>"></script>

    <?= isset($js) ? $js : '' ?>

    <!-- Custom Theme Scripts -->
    <script src="<?= base_url("assets/gentelella/build/js/custom.min.js") ?>">
    </script>

    <!-- FullScreenToggle -->
    <script>
        $(function() {
            $('body').show();
        });


        function ToggleFullScreen() {
            // UniversalXPConnect privilege is required in Firefox
            try {
                if (window.netscape && netscape.security) { // Firefox
                    netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
                }
            } catch (e) {
                alert("UniversalXPConnect privilege is required for this operation!");
                return;
            }

            if ('fullScreen' in window) {
                window.fullScreen = !window.fullScreen;
            } else {
                alert("Your browser does not support this example!");
            }
        }
        $("#toggleFullScreen").click(function() {
            // ToggleFullScreen();
            if (!document.fullscreenElement && // alternative standard method
                !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) { // current working methods
                alert('Full Screen Effect is Temporary, Click F11 for Better Conditions :)');
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.msRequestFullscreen) {
                    document.documentElement.msRequestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                }
                $(this).children().removeClass('glyphicon-fullscreen');
                $(this).children().addClass('glyphicon-resize-small');
                // sessionStorage.setItem("fullscreen", 1);
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                }
                $(this).children().removeClass('glyphicon-resize-small');
                $(this).children().addClass('glyphicon-fullscreen');
                // sessionStorage.setItem("fullscreen", 0);

            }
        });
        $("body").click(function() {
            if ($("body").css('filter') != 'none')
                $("body").css('filter', '')
        })
        $("#blur").click(function() {
            if ($("body").css('filter') == 'none') {
                setTimeout(function() {
                    $("body").css('filter', 'blur(40px)')
                }, 100)
            }
        })

        function startTime() {
            var today = new Date();
            var h = today.getHours();
            var m = today.getMinutes();
            var s = today.getSeconds();
            m = checkTime(m);
            s = checkTime(s);
            document.getElementById('clock').innerHTML =
                h + ":" + m;
            var t = setTimeout(startTime, 500);
        }

        function checkTime(i) {
            if (i < 10) {
                i = "0" + i
            }; // add zero in front of numbers < 10
            return i;
        }
    </script>
    <script>
        $('.modal').modal({
            keyboard: false,
            show: false
        });
        $(function() {


            // Setup - add a text input to each footer cell


            $(".right_col").css('height', document.getElementById('content').clientHeight + 170);

            $('#content').resize(function() {
                $(".right_col").css('height', document.getElementById('content').clientHeight + 170);
            });
        });

        $("#changeJP").click(function() {
            url = 'http://localhost/emsdev/index.php/core/get_jabatan';
            $.ajax({
                url: url,
                dataType: "json",
                success: function(data) {
                    var items = [];
                    $("#changeJP").attr("style", "display:none");
                    $("#saveJP").removeAttr('style');
                    $("#jabatan").removeAttr('disabled');
                    $("#jabatan")[0].innerHTML = "";
                    $("#project")[0].innerHTML = "";
                    $("#jabatan").append("<option value='' selected disabled>Pilih Jabatan</option>");
                    $.each(data, function(key, val) {
                        $("#jabatan").append("<option value='" + val.id + "'>" + val.name.toUpperCase() + "</option>");
                    });
                }
            });

        });
        $("#jabatan").change(function() {
            url = 'http://localhost/emsdev/index.php/core/get_project';
            console.log(this.value);
            $.ajax({
                type: "post",
                url: url,
                data: {
                    jabatan: this.value
                },
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    $("#project").removeAttr('disabled');
                    $("#project")[0].innerHTML = "";
                    $("#project").append("<option value='' selected disabled>Pilih Project</option>");
                    $.each(data, function(key, val) {
                        $("#project").append("<option value='" + val.id + "'>" + val.name.toUpperCase() + "</option>");
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

        $(document).ajaxStart(function() {
            $("#loading").show();
        });

        $(document).ajaxComplete(function() {
            $("#loading").hide();
        });
    </script>
</body>

</html>