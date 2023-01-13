<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Inicio de sesión</title>
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
	<link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/bootstrap_4/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/css/login.css">
	<style>
    #loader_background {
        background: rgba(0, 0, 0, 0.4);
        height: 100vh;
        width: 150vw;
        position: fixed;
        z-index: 9999;
        top: 0;
        display: none;
    }

    #loader {
        background: #fff;
        height: 300px;
        border-radius: 10px;
        width: 500px;
        margin-left: calc(50% - 250px);
        top: 100px;
        z-index: 9999;
        position: fixed;
        display: none;
		color :#000!important;

    }
</style>
</head>
<body class="h-100">
<script>
	window.base_url = "<?php echo base_url() ?>";
</script>
<div class="h-100">
	<!--<div class="fondo_ventajas h-100 overflow-y">
		<div class="col-12">
			<div class="row justify-content-center">
				<form @submit.prevent>
					<h1>Inicia sesión</h1>

					<label for="email1">Email</label>
					<input v-model.trim="loginForm.email" type="text" placeholder="you@email.com" id="email1" />

					<label for="password1">Password</label>
					<input v-model.trim="loginForm.password" type="password" placeholder="******" id="password1" />

					<button @click="login" class="button">Iniciar sesión</button>
				</form>
			</div>
		</div>
	</div>-->
	<div class="wrapper fadeInDown">
		<div id="formContent">
			<!-- Tabs Titles -->
			<!-- Icon -->
			<div class="fadeIn first pt-5 pb-5" style="background-color: #343a40; background-image: none; border-top-left-radius: 10px; border-top-right-radius: 10px;">
				<img class="margin_top_20 margin_bottom_20" src="<?php echo base_url('/assets/img') ?>/header_logo_kreativeco.png" id="icon_" alt="User Icon" />
			</div>
			<!-- Login Form -->
			<form class="pt-3 pb-3">
				<input type="text" id="email" class="fadeIn third" name="email" placeholder="email">
				<input type="password" id="password" class="fadeIn third" name="password" placeholder="password">
				<button type="button" onclick="Login();" class="btn btn-dark margin_bottom_10 margin_top_5 mt-3">Iniciar sesión</button>
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
<script src="<?php echo base_url()?>assets/plugins/jquery/js/jquery-3.3.1.min.js"></script>
<script  src="<?php echo base_url() ?>assets/js/login.js"></script>
</body>
</html>
