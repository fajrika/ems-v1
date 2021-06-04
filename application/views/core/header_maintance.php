<!DOCTYPE html>
<html lang="en">

<head>
    <title>Maintance</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="<?= base_url() ?>images/icons/favicon.ico" />
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>vendor/animate/animate.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>vendor/select2/select2.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/util.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/main.css">
    <!--===============================================================================================-->
</head>

<body>

    <!--  -->
    <div class="simpleslide100">
        <div class="simpleslide100-item bg-img1" style="background-image: url('<?= base_url() ?>images/bg01.jpg');"></div>
        <div class="simpleslide100-item bg-img1" style="background-image: url('<?= base_url() ?>images/bg02.jpg');"></div>
        <div class="simpleslide100-item bg-img1" style="background-image: url('<?= base_url() ?>images/bg03.jpg');"></div>
    </div>

    <div class="size1 overlay1">
        <!--  -->
        <div class="size1 flex-col-c-m p-l-15 p-r-15 p-t-50 p-b-50">
            <h3 class="l1-txt1 txt-center p-b-25">
                Maintance
            </h3>

            <p class="m2-txt1 txt-center p-b-48">
                Website EMS dalam masa perbaikan, mohon di tunggu sampai countdown habis
            </p>

            <div class="flex-w flex-c-m cd100 p-b-33">
                <div class="flex-col-c-m size2 bor1 m-l-15 m-r-15 m-b-20">
                    <span class="l2-txt1 p-b-9 days">0</span>
                    <span class="s2-txt1">Days</span>
                </div>

                <div class="flex-col-c-m size2 bor1 m-l-15 m-r-15 m-b-20">
                    <span class="l2-txt1 p-b-9 hours">0</span>
                    <span class="s2-txt1">Hours</span>
                </div>

                <div class="flex-col-c-m size2 bor1 m-l-15 m-r-15 m-b-20">
                    <span class="l2-txt1 p-b-9 minutes">0</span>
                    <span class="s2-txt1">Minutes</span>
                </div>

                <div class="flex-col-c-m size2 bor1 m-l-15 m-r-15 m-b-20">
                    <span class="l2-txt1 p-b-9 seconds">0</span>
                    <span class="s2-txt1">Seconds</span>
                </div>
            </div>
            <p class="m2-txt1 txt-center p-b-48">
                <h5>
                    <small>Jika contdown sudah habis dan masih tidak bisa di akses, segera hubungi IT SH2-KP</small>
                </h5>
            </p>

        </div>
    </div>
    <script src="<?= base_url() ?>vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="<?= base_url() ?>vendor/bootstrap/js/popper.js"></script>
    <script src="<?= base_url() ?>vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?= base_url() ?>vendor/select2/select2.min.js"></script>
    <script src="<?= base_url() ?>vendor/countdowntime/moment.min.js"></script>
    <script src="<?= base_url() ?>vendor/countdowntime/moment-timezone.min.js"></script>
    <script src="<?= base_url() ?>vendor/countdowntime/moment-timezone-with-data.min.js"></script>
    <script src="<?= base_url() ?>vendor/countdowntime/countdowntime.js"></script>
    <script>
        var msec = (new Date('2020-11-04 14:00:00')) - (new Date());
        var hh = Math.floor(msec / 1000 / 60 / 60);
        msec -= hh * 1000 * 60 * 60;
        var mm = Math.floor(msec / 1000 / 60);
        msec -= mm * 1000 * 60;
        var ss = Math.floor(msec / 1000);
        msec -= ss * 1000;
        $(function() {
            $(".hours").html(hh);
            $(".minutes").html(mm);
            $(".seconds").html(ss);
            si = setInterval(() => {
                ss -= 1;
                if (ss == -1) {
                    ss = 59;
                    mm -= 1;
                }
                if (mm == -1) {
                    mm = 59;
                    hh -= 1;
                }
                if (hh == -1) {
                    hh = mm = ss = 0;
                }
                if (hh == 0 && mm == 0 && ss == 0) clearInterval(si);
                $(".hours").html(hh);
                $(".minutes").html(mm);
                $(".seconds").html(ss);
            }, 1000)
        })
    </script>
    <script src="<?= base_url() ?>vendor/tilt/tilt.jquery.min.js"></script>
    <script>
        $('.js-tilt').tilt({
            scale: 1.1
        })
    </script>
</body>

</html>

<?php die; ?>

<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ciputra EMS</title>
    <link rel="icon" href="https://img.icons8.com/cotton/2x/home--v3.png" type="image/gif" sizes="16x16">
    <link href="<?= base_url() ?>vendors/switchery/dist/switchery.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>vendors/nprogress/nprogress.css" rel="stylesheet">
    <link href="<?= base_url() ?>vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <link href="<?= base_url() ?>vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="<?= base_url() ?>css/custom.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <script src="<?= base_url() ?>vendors/jquery/dist/jquery.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <link href="https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css" rel="stylesheet" />
</head>

<body class="nav-md" style="display:grid">
    <div class="container body">
        <div class="main_container">