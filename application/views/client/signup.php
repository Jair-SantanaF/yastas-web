<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrarme</title>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/bootstrap_4/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/login.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/client/signin.css">
</head>

<style>
    .container_form {
        background-image: url("<?php echo base_url('/assets/img/yastas/login-signin') ?>/img_bg_degradado.png");
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
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
        <div class="col-md-4 container_form nopadding">
            <div class="fadeInDown text-center">
                <div class="fadeIn first logo_top">
                    <img src="<?php echo base_url('/assets/img/yastas/login-signin') ?>/img_logo.png" id="logo_animated" alt="User Icon" />
                </div>
                <div class="login-form">
                    <form>
                        <p>Ingresa tu ID de operador para iniciar tu registro</p>
                        <input type="number" id="number_employee" class="fadeIn third" name="number_employee" placeholder="ID operador*">
                        <input type="text" id="name" class="fadeIn second" name="name" placeholder="Nombre*">
                        <input type="text" id="lastname" class="fadeIn second" name="lastname" placeholder="Apellido*">
                        <input type="text" id="segundo_apellido" class="fadeIn second" name="segundo_apellido" placeholder="Segundo apellido">
                        <input type="number" id="phone" class="fadeIn second" name="phone" placeholder="Teléfono">
                        <input type="email" id="email" class="fadeIn second" name="email" placeholder="Correo electrónico*">
                        <input type="password" id="password" class="fadeIn third" name="password" placeholder="Contraseña*">
                        <select class="form-control" id="job" name="job">
                            <option value="0">Selecciona un perfil</option>
                            <option value="Comisionista">Comisionista</option>
                            <option value="Operador">Operador</option>
                        </select>
                        <br>
                        <checkbox>
                            <input type="checkbox" id="terminos_aceptados" name="terminos_aceptados" value="1">
                            <label for="terminos_aceptados">Acepto los términos y condiciones</label>
                        </checkbox>
                        <br>
                        <checkbox>
                            <input type="checkbox" id="aviso_aceptado" name="aviso_aceptado" value="1">
                            <label for="aviso_aceptado">Acepto la política de privacidad</label>
                        </checkbox>
                        <button type="button" onclick="signin();" class="btn">Registrarse</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="loader_background">
        <div id="loader" class="text-center">
            <br>
            <br>
            <br>
            <h1 style="color:#000!important">Registrando...</h1>
            <br>
            <img src="http://kreativeco.com/nuup/assets/img/circl.gif">
        </div>
    </div>
    <script src="<?php echo base_url() ?>assets/plugins/jquery/js/jquery-3.3.1.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/client.js"></script>
</body>

</html>