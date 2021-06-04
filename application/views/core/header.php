<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
        $title = [];
        $controller = explode("_", $this->router->fetch_class());
        foreach ($controller as $c => $value) {
            if (strlen($value)>1) {
                array_push($title, $value);
            }
        }
        echo('<title>'.ucwords(implode(" ", $title)).'</title>');
    ?>
    <link rel="icon" href="<?=base_url()?>assets/images/logos/Ciputra-circle.png" type="image/x-icon" sizes="16x16">
    
    <link href="<?=base_url()?>vendors/switchery/dist/switchery.min.css" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="<?=base_url()?>vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?=base_url()?>vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="<?=base_url()?>vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="<?=base_url()?>vendors/iCheck/skins/flat/green.css" rel="stylesheet">
	
    <!-- bootstrap-progressbar -->
    <link href="<?=base_url()?>vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">

    <!-- bootstrap-daterangepicker -->
    <link href="<?=base_url()?>vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?=base_url()?>css/custom.min.css?v=1.1.2" rel="stylesheet">
    
    <!-- dataTables -->
    <link href="<?=base_url()?>vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="<?=base_url()?>vendors/jquery/dist/jquery.min.js"></script>
    <!-- untuk dragable -->
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <!-- untuk reziable -->
    <link href="https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css" rel="stylesheet"/>

    <style type="text/css">
        .new_preloader {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          z-index: 9999;
          background-color: #ffffff5e;
        }
        .new_preloader .new_loading {
          position: absolute;
          left: 45%;
          top: 42%;
          transform: translate(-50%,-50%);
          font: 14px arial;
        }
        .new_preloader .new_loading .item_loading_1 {
            position: absolute;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            border: 8px solid #000;
            border-color: #183a6d transparent #183a6d transparent;
            -webkit-animation: lds-double-ring 2s linear infinite;
            animation: lds-double-ring 2s linear infinite;
        }
        .new_preloader .new_loading .item_loading_2 {
            position: absolute;
            border-radius: 50%;
            border: 8px solid #000;
            width: 140px;
            height: 140px;
            top: 10px;
            left: 10px;
            border-color: transparent #0d7c5c transparent #0d7c5c;
            -webkit-animation: lds-double-ring_reverse 2s linear infinite;
            animation: lds-double-ring_reverse 2s linear infinite;
        }
        .new_preloader .new_loading .item_loading_3 {
            position: absolute;
            width: 120px;
            height: 120px;
            top: 20px;
            left: 14px;
        }
        .new_preloader .new_loading .item_loading_3 img {
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body class="nav-md" style="display:grid">
    <div class="new_preloader">
      <div class="new_loading">
        <div class="item_loading_1"></div>
        <div class="item_loading_2"></div>
        <div class="item_loading_3">
            <img src="<?=base_url();?>assets/images/logos/Ciputra.png">
        </div>
      </div>
    </div>
    <div class="container body">
        <div class="main_container">

            
                
            
        

        
        
        
        
    
