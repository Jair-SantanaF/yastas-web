<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <base href="http://localhost:81/basf_backend/">
    <!--<base href="https://kreativeco.com/basf/">-->

    <title> <?php echo $title; ?> </title>
    <link href="<?php echo base_url()?>assets/plugins/dataTables_1_10_21/datatables.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/bootstrap_4/css/bootstrap.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/fontawesome_5_0_8/web-fonts-with-css/css/fontawesome-all.min.css">    <!-- Font Awesome -->
    <link href="<?php echo base_url()?>assets/plugins/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo base_url()?>assets/css/style_web.css" type="text/css">
    <link href="<?php echo base_url()?>assets/plugins/roundSlider/dist2/roundslider.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/fullcalendar-5.3.2/lib/main.css">
    <link href="<?php echo base_url()?>/assets/plugins/dataTables_1_10_21/datatables.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <!-- Prueba ruleta -->
    <script src="<?php echo base_url()?>assets/js/app/Winwheel.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/latest/TweenMax.min.js"></script>
    <!-- Prueba ruleta -->

</head>

<body>
    <script src="<?php echo base_url()?>assets/plugins/jquery/js/jquery-3.3.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>
    <script src="<?php echo base_url()?>assets/plugins/bootstrap_4/js/bootstrap.js"></script>
    <script src="<?php echo base_url()?>assets/plugins/loadmask/js/spin.js" type="text/javascript"></script>
    <script src="<?php echo base_url()?>assets/plugins/form_validation/jquery.validate.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url()?>assets/plugins/dataTables_1_10_21/datatables.js"></script>
    <script src="<?php echo base_url()?>assets/plugins/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="<?php echo base_url()?>assets/plugins/roundSlider/dist2/roundslider.min.js"></script>
    <script src="<?php echo base_url()?>assets/plugins/fullcalendar-5.3.2/lib/main.js"></script>
    <script src="<?php echo base_url()?>assets/plugins/fullcalendar-5.3.2/lib/locales-all.js"></script>
    <script src="<?php echo base_url()?>assets/plugins/dataTables_1_10_21/datatables.js"></script>
    <script>
        jQuery.extend(jQuery.validator.messages, {
            required: "Este campo es requerido",
            remote: "Please fix this field.",
            email: "Por favor, introduzca un email valido.",
            url: "Por favor, introduzca una URL valida",
            date: "Por favor, introduzca una fecha valida",
            dateISO: "Please enter a valid date (ISO).",
            number: "Please enter a valid number.",
            digits: "Please enter only digits.",
            creditcard: "Please enter a valid credit card number.",
            equalTo: "Por favor, introduzca el mismo valor.",
            accept: "Please enter a value with a valid extension.",
            maxlength: jQuery.validator.format("Ingrese no más de {0} caracteres."),
            minlength: jQuery.validator.format("Ingrese al menos {0} caracteres."),
            rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
            range: jQuery.validator.format("Please enter a value between {0} and {1}."),
            max: jQuery.validator.format("Por favor ingrese un valor menor o igual a {0}."),
            min: jQuery.validator.format("Por favor ingrese un valor mayor o igual a {0}.")
        });
    </script>
    <script>
        window.base_url = "<?php echo base_url() ?>";
        window.photo = "<?php echo $this->session->userdata('profile_photo') ?>";
        window.name = "<?php echo $this->session->userdata('name') ?>";
        window.job_name = "<?php echo $this->session->userdata('job_name') ?>";
    </script>
    <?php if($session==TRUE){ ?>
    <?php if(!$b_elearning || $b_elearning==FALSE){ ?>

    <!-- Navegacion -->
    <nav class="navbar navbar-light navbar-expand-md bg-faded justify-content-center">

        <?php echo anchor('app/home', img($logo), array('class'=>'navbar-brand d-flex w-30 mr-auto')); ?>

        <!-- <div class="navbar">
            <ul class="navbar-nav w-100 justify-content-center">
                <li class="nav-item">
                    <?php echo anchor(base_url().'index.php/app/switchLang/spanish', 'ES', array('class'=>'nav-link')); ?>
                </li>
                <li class="nav-item">
                    <?php echo anchor(base_url().'index.php/app/switchLang/english', 'EN', array('class'=>'nav-link')); ?>
                </li>
            </ul>
        </div> -->

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsingNavbar3">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-collapse collapse w-50" id="collapsingNavbar3">

            <ul class="navbar-nav w-100 justify-content-center">
                <li class="nav-item">
                    <?php echo anchor('app/home','Inicio',array('class'=>'nav-link')); ?>
                </li>
                <li class="nav-item">
                    <!-- <?php echo anchor('app/services','Servicios',array('class'=>'nav-link')); ?> -->
                </li>
                <li class="nav-item">
                    <!-- <?php echo anchor('app/feedback','Feedback',array('class'=>'nav-link')); ?> -->
                </li>
                <li class="nav-item">
                    <?php echo anchor('app/contact','Contacto',array('class'=>'nav-link')); ?>
                </li>
                <li class="nav-item">
                    <!-- <?php echo anchor('app/pricing','Precios',array('class'=>'nav-link')); ?> -->
                </li>
                <li class="nav-item">
                    <!-- Button trigger modal -->
                    <a href="javascript:void()" class="nav-link" onclick="CerrarSesion();" data-backdrop="false">
                      Cerrar Sesión
                    </a>
                </li>
            </ul>

        </div>

        <div class="navbar-collapse collapse w-10" id="collapsingNavbar3">

            <?php if($services==TRUE){ ?>

            <ul class="navbar-nav w-100 justify-content-center">
                <li class="nav-item dropdown notifications-dropdown">
                    <a class="nav-link" href="<?php echo base_url() ?>index.php/app/cart">
                        <i class="fas fa-shopping-cart"></i>
                    </a>
                </li>
            </ul>

        <?php } else { ?>

            <ul class="navbar-nav w-100 justify-content-center">
                <li class="nav-item dropdown notifications-dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <i class="fas fa-bell"></i>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="left: unset; right:0">
                            <div class="dropdown-menu-header" id="new_notification">
                            </div>

                            <div id="notification">
                            </div>
                            
                            <div class="notifications-dropdown-footer">
                                <?php echo anchor('app/notification', 'Ver todas las notificaciones', array()); ?>
                            </div>
                    </div>
                </li>
            </ul>

        <?php } ?>

        </div>

        <div class="navbar-collapse collapse w-10" id="collapsingNavbar3">

            <?php echo anchor('app/profile', img($user_img_nav)); ?>

        </div>

    </nav>
    <!-- Navegacion -->

    <script src="<?php echo base_url()?>assets/js/app/notifications.js" type="text/javascript"></script>

    <?php

    }
    else
    {

    ?>

    <nav class="navbar navbar-dark bg-dark navbar-expand-md justify-content-center">

        <?php echo anchor(base_url().'index.php/app/home', img($logo), array('class'=>'navbar-brand d-flex w-30 mr-auto')); ?>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsingNavbar3">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-collapse collapse w-70" id="collapsingNavbar3">
            <ul class="navbar-nav w-100 justify-content-center">
                <li class="nav-item active">

                </li>
                <li class="nav-item">

                </li>
                <li class="nav-item">

                </li>
            </ul>
            <ul class="nav navbar-nav ml-auto w-100 justify-content-end">
                <li class="nav-item">

                </li>
                <li class="nav-item">

                </li>
                <li class="nav-item">
                    <?php echo anchor(base_url().'index.php/app/home','<i class="fas fa-arrow-left"></i> Regresar', array("class"=>"nav-link text-white")); ?>
                </li>
            </ul>
        </div>

    </nav>

    <?php }?>
<?php } ?>