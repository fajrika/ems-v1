<html lang="en">
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

        <!-- Bootstrap -->
        <link href="<?=base_url()?>vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="<?=base_url()?>vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <!-- NProgress -->
        <link href="<?=base_url()?>vendors/nprogress/nprogress.css" rel="stylesheet">
        <!-- Animate.css -->
        <link href="<?=base_url()?>vendors/animate.css/animate.min.css" rel="stylesheet">

        <!-- Custom Theme Style -->
        <link href="<?=base_url()?>css/custom.min.css" rel="stylesheet">
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-S7EZK4R5QY"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-S7EZK4R5QY');
        </script>
    </head>

    <body class="login">
        <div>
            <a class="hiddenanchor" id="signup"></a>
            <a class="hiddenanchor" id="signin"></a>
            <div class="login_wrapper">
                <div class="animate form login_form">
                    <section class="login_content">
                        <form action ="<?=site_url()?>/login/proses" method="post">
                            <h1>Login EMS</h1>
                            <div>
                                <input type="text" class="form-control" name="username" placeholder="Username" required>
                            </div>
                            <div>
                                <input type="password" class="form-control" name="password" placeholder="Password" required>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-default submit">Login</button>
                            </div>

                            <div class="clearfix"></div>
                            <br>
                            <div class="separator">
                                <p class="change_link">
                                    <a href="#signup" class="to_register"> Lupa Password ? </a>
                                </p>

                                <div class="clearfix"></div>
                                <br>

                                <div>
                                    <h1>
                                        <!-- <i class="fa fa-paw"></i>  -->
                                        Ciputra
                                    </h1>
                                    <!-- <p>©2016 All Rights Reserved. Gentelella Alela! is a Bootstrap 3 template. Privacy and Terms</p> -->
                                    <p>@2021 PT. Ciputra Nusantara</p>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
                <div id="register" class="animate form registration_form">
                    <section class="login_content">
                        <h1>Lupa Password</h1>
                        <div>
                            Jika anda tidak bisa login, ada kemungkinan kalau anda salah memasukkan password untuk beberapa periode atau ada kesalahan dari sistem ini,
                            <h2>Hubungi SuperAdmin EMS sesegera mungkin !</h2>
                        </div>

                        <div class="clearfix"></div>

                        <div class="separator">
                            <p class="change_link">Jika sudah silahkan
                                <b><a href="#signin" class="to_register"> Log in</a></b>
                            </p>

                            <div class="clearfix"></div>
                            <br>

                            <div>
                                <h1>
                                    <!-- <i class="fa fa-paw"></i>  -->
                                    Ciputra
                                </h1>
                                <p>@2021 PT. Ciputra Nusantara</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </body>
</html>