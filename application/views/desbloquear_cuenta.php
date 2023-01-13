<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Desbloqueo de cuenta</title>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/bootstrap_4/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/login.css">
</head>

<body class="h-100">
    <script>
        window.base_url = "<?php echo base_url() ?>";
        var email = "<?php echo $email ?>";
    </script>
    <div class="h-100">
        <div class="wrapper fadeInDown">
            <div id="formContent">
                <!-- Tabs Titles -->
                <!-- Icon -->
                <div class="fadeIn first pt-5 pb-5" style="background-color: #343a40; background-image: none; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <img class="margin_top_20 margin_bottom_20" src="<?php echo base_url('/assets/img') ?>/header_logo_kreativeco.png" id="icon_" alt="User Icon" />
                </div>
                <button type="button" onclick="desbloquearCuenta();" class="btn btn-dark margin_bottom_10 margin_top_5 mt-3">Desbloquear Cuenta</button>
                <p>Da click en el botón para desbloquear tu cuenta</p>
                <!-- Login Form -->
                <!-- <form class="pt-3 pb-3">
				<input type="text" id="email" class="fadeIn third" name="email" placeholder="email">
				<input type="password" id="password" class="fadeIn third" name="password" placeholder="password">
				<button type="button" onclick="Login();" class="btn btn-dark margin_bottom_10 margin_top_5 mt-3">Iniciar sesión</button>
			</form> -->
            </div>
        </div>
    </div>
    <script src="<?php echo base_url() ?>assets/plugins/jquery/js/jquery-3.3.1.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/desbloquear.js"></script>
</body>

</html>