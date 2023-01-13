<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar de sesión</title>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/bootstrap_4/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/login.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/client/login.css">
</head>

<style>
    .container_form {
        background-image: url("<?php echo base_url('/assets/img/yastas/login-signin') ?>/img_bg_degradado.png");
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }
</style>

<body>
    <script>
        window.base_url = "<?php echo base_url() ?>";
    </script>
    <div class="row">
        <div class="col-md-8 nopadding">
            <div class="logo_animation">
                <img src="<?php echo base_url('/assets/img/yastas/login-signin') ?>/img_logo.png" id="logo_animated" alt="User Icon" />
            </div>
        </div>
        <div class="col-md-4 container_form">
            <div class="wrapper fadeInDown text-center">
                <div class="fadeIn first pt-5 pb-5">
                    <img src="<?php echo base_url('/assets/img/yastas/login-signin') ?>/img_logo.png" id="logo_form" alt="User Icon" />
                </div>
                <form class="login-form">
                    <input type="text" id="email" class="fadeIn third" name="email" placeholder="ID de operador">
                    <input type="password" id="password" class="fadeIn third" name="password" placeholder="Contraseña">
                    <br>
                    <a href="#"> Olvidaste tu contraseña </a>
                    <div class="row">
                        <div class="col-12">
                            <button type="button" onclick="Login();" class="btn">Iniciar sesión</button>
                        </div>
                        <div class="col-12">
                            <button type="button" onclick="ShowSignUp();" class="btn">Registrarme</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="loader_background">
        <div id="loader" class="text-center">
            <br>
            <br>
            <br>
            <h1 style="color:#000!important">Iniciando sesión...</h1>
            <br>
            <img src="http://kreativeco.com/nuup/assets/img/circl.gif">
        </div>
    </div>
    <script src="<?php echo base_url() ?>assets/plugins/jquery/js/jquery-3.3.1.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/client.js"></script>
</body>

</html>