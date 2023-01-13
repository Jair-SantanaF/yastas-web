<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>yastas</title>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/client/home.css">
</head>

<style>
    body {
        background-image: url("<?php echo base_url('/assets/img/yastas/back y header') ?>/img_background.png");
        background-repeat: no-repeat;
        background-size: cover;
    }

    .content-1 {
        background-image: url("<?php echo base_url('/assets/img/yastas/Home') ?>/img_home_btn_biblioteca.png");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        border-radius: 10px;
    }

    .content-2 {
        background-image: url("<?php echo base_url('/assets/img') ?>/img_home_cuestionarios.png");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
    }

    .content-3 {
        background-image: url("<?php echo base_url('/assets/img/yastas/Home') ?>/img_home_btn_mi_aprendizaje.png");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        border-radius: 10px;
    }

    .content-4 {
        background-image: url("<?php echo base_url('/assets/img') ?>/img_home_ranking.png");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
    }

    .content-5 {
        background-image: url("<?php echo base_url('/assets/img') ?>/img_home_podcast.png");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
    }

    .content-6 {
        background-image: url("<?php echo base_url('/assets/img') ?>/img_home_comunidades.png");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
    }
</style>

<nav class="navbar navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="text_center">
            <img src="<?php echo base_url('/assets/img/yastas/back y header') ?>/img_header_logo.png" alt="logo" width="100px" />
        </div>
        <div class="user_notifications">
            <a href="<?php echo base_url('client/notifications') ?>" class="btn btn-outline-light" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
                    <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z" />
                </svg>
            </a>
            <a href="<?php echo base_url('client/profile') ?>" class="btn btn-outline-light" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
                </svg>
            </a>
        </div>
        <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
            <div class="offcanvas-header">
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Mis logros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contactanos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Tour Nupi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Ayuda</a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url('client/logout') ?>">Eliminar Cuenta</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url('client/logout') ?>">Aviso de privacidad</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url('client/logout') ?>">Terminos y condiciones</a>
                    </li>
                    <li class="nav-item">
                        <a onclick="cerrarSesion();" class="nav-link">Cerrar sesión</a>
                    </li>
                </ul>

            </div>
        </div>
    </div>
</nav>

<body>
    <script>
        window.base_url = "<?php echo base_url() ?>";
    </script>

    <div class="container">
        <div class="row">
            <div class="col-12 noOverflow">
                <div class="msg_especial">
                    <p>¡Extra! ¡Extra!</p>
                </div>
                <div class="news">
                    <img src="<?php echo base_url('/assets/img') ?>/img_home_miniatura_noticias.png" alt="logo" width="100px" style="margin-left: 10px; margin-right: 20px;" />
                    <div id="news">
                        <p>Cargando noticias...</p>
                    </div>
                    <div class="ver_mas">
                        <p onclick="ShowNews();">Ver más...</p>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <br>
        <div class="row">
            <div class="col-2"></div>
            <div class="mb-xs-4 col-md-4">
                <button onclick="ShowLibrary();" class="content content-1">
                </button>
            </div>
            <!-- <div class="mb-sm-4 mb-xs-4 col-md-4">
                <button onclick="ShowTest();" class="content content-2">
                </button>
            </div> -->
            <div class="mb-xs-4 col-md-4">
                <button onclick="ShowTraining();" class="content content-3">
                </button>
            </div>
            <div class="col-2"></div>
        </div>
        <!-- <div class="row">
            <div class="mb-sm-4 mb-xs-4 col-md-4">
                <button class="content content-4">
                </button>
            </div>
            <div class="mb-sm-4 mb-xs-4 col-md-4">
                <button class="content content-5">
                </button>
            </div>
            <div class="mb-sm-4 mb-xs-4 col-md-4">
                <button class="content content-6">
                </button>
            </div>
        </div> -->
    </div>

    <script src="<?php echo base_url() ?>assets/plugins/jquery/js/jquery-3.3.1.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/client.js"></script>

</body>

</html>

<script>
    $(document).ready(function() {
        const localUser = JSON.parse(localStorage.getItem('token'))[0];
        const business_id = localUser.business_id;
        const user_id = localUser.user_id;
        getNews(business_id, user_id);
    });
</script>