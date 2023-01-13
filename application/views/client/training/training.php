<!DOCTYPE html>
<html lang="es" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/generalClient.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/client/training.css">
    <title>Mi aprendizaje</title>
</head>

<style>
    body {
        background-image: url("<?php echo base_url('/assets/img/yastas/back y header') ?>/img_background.png");
        background-repeat: no-repeat;
        background-size: cover;
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
    <br><br><br>
    <div class="row" style="display: flex; justify-content: space-around; align-items: center; color: white; padding-bottom: 20px;">
        <div onclick="GoBack();" class="col-md-2" style="display: flex; justify-content: space-around;  align-items: center; cursor: pointer;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
            </svg>
            Regresar
        </div>
        <div class="col-md-10">
            <p style="font-weight: bold; font-size: large; margin-left: 35%;">Mi aprendizaje</p>
        </div>
    </div>
    <div class="container">
        <div class="row gx-5" id="capacitaciones" style="margin: 0 auto; max-width: 800px;">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Cargando...</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.base_url = "<?php echo base_url() ?>";
    </script>
    <script src="<?php echo base_url() ?>assets/plugins/jquery/js/jquery-3.3.1.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/client.js"></script>
</body>

</html>

<script>
    $(document).ready(function() {
        getTrainingDetails();
    });

    function getTrainingDetails() {
        const localUser = JSON.parse(localStorage.getItem('token'));
        const capacitacion_id = localStorage.getItem('training_id');

        $.ajax({
            url: window.base_url + "capacitacion/getDetail",
            type: "POST",
            dataType: "json",
            data: {
                capacitacion_id: capacitacion_id,
                token: localUser[0].token
            },
            success: function(data) {
                let trainings = $("#capacitaciones");
                let html = "";

                trainings.empty();

                html += `
                <div class="row">
                    <div class="col-2">
                        <img src="${data.data.image}" class="card-img-top" alt="...">
                    </div>
                    <div class="col-10 prin_text">
                        <h5 class="card-title">${data.data.name}</h5>
                        <p class="card-text">${data.data.description}</p>
                    </div>
                </div>
                <div class="separador"></div>
                `;

                data.data.elementos.forEach(element => {
                    html += `
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                    `;

                    switch (element.categoria) {
                        case 'Biblioteca':
                            html += `
                                <div class="row library_container">
                                    <div class="col-1 img_library">
                                        <img src="<?php echo base_url('/assets/img') ?>/img_notificaciones_icono_biblioteca.png" alt="logo"/>
                                    </div>
                                    <div class="col-11">
                                        <h5 class="card-title">${element.categoria}</h5>
                                    </div>
                                </div>
                            `;
                            break;
                        case 'Preguntas':
                            html += `
                                <div class="row library_container">
                                    <div class="col-1 img_library">
                                        <img src="<?php echo base_url('/assets/img') ?>/img_notificaciones_icono_cuestionarios.png" alt="logo"/>
                                    </div>
                                    <div class="col-11">
                                        <h5 class="card-title">${element.categoria}</h5>
                                    </div>
                                </div>
                            `;
                            break;
                    }

                    element.items.forEach(item => {
                        html += `
                            <div class="row">
                                <div class="col-1">
                                    <div id="lineas"></div>
                                </div>
                        `;

                        if (!item.ejecutado) {
                            html += `
                                <div class="col-1 circulo_container">
                                    <div class="circulo"></div>
                                </div>
                                <div class="col-8">
                            `;
                        } else {
                            html += `
                                <div class="col-1 circulo_container">
                                    <div class="circulo">
                                        <div class="circulo_ejecutado"></div>
                                    </div>
                                </div>
                                <div class="col-8">
                            `;
                        }

                        if (item.title) {
                            html += `<p>${item.title}</p> </div> <div class="col-2">`;
                            switch (item.type) {
                                case 'video':
                                    item.video = JSON.stringify(item.video);
                                    html += '<button onclick=selectVideo(' + item.video + ',' + item.id + ') type="button" class="btn btn-danger btn_ir">Ir</button> </div> </div>';
                                    break;
                                case 'imagen':
                                    item.image = JSON.stringify(item.image);
                                    html += '<button onclick=selectImage(' + item.image + ',' + item.id + ') type="button" class="btn btn-danger btn_ir">Ir</button> </div> </div>';
                                    break;
                                case 'documento':
                                    item.file = JSON.stringify(item.file);
                                    html += '<button onclick=selectPdf(' + item.file + ',' + item.id + ') type="button" class="btn btn-danger btn_ir">Ir</button> </div> </div>';
                                    break;
                            }
                        } else {
                            html += `<p>${item.name}</p> </div> <div class="col-2">`;
                            html += '<button onclick=select(' + item.id + ',' + item.id + ') type="button" class="btn btn-danger btn_ir">Ir</button> </div> </div>';
                        }
                    });
                    html += `
                            </div>
                        </div>
                    </div>
                    `;
                });

                trainings.append(html);

            },
            error: function(data) {
                quesionnaries.empty();
                questionnaires.append("<tr><td colspan='3'>No hay detalles disponibles</td></tr>");
            }
        });
    }

    function select(id) {
        SetVisto(id);
        selectQuestionnarie(id);
    }

    function selectVideo(url, id) {
        localStorage.setItem('video', url);
        SetVisto(id);
        ShowVideo();
    }

    function selectImage(url, id) {
        localStorage.setItem('img', url);
        SetVisto(id);
        ShowImage();
    }

    function selectPdf(url, id) {
        localStorage.setItem('pdf', url);
        SetVisto(id);
        ShowPdf();
    }

    function getOnlyText(element) {
        return element.split(">")[1].split("<")[0];
    }
</script>