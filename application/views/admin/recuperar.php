<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar contraseña</title>
    <link href="<?php echo base_url() ?>/assets/plugins/dataTables_1_10_16/datatables.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/bootstrap_4/css/bootstrap.min.css">

    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/fontawesome_5_0_8/web-fonts-with-css/css/fontawesome-all.min.css">
    <link href="<?php echo base_url() ?>assets/plugins/loadmask/css/jquery.loadmask.spin.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url() ?>assets/css/style.css" rel="stylesheet" type="text/css">
</head>

<body>
    
    <div class="container-fluid h-100" id="body" style="height:100vh!important;padding: 0 !important;background: url(https://kreativeco.com/nuup/assets/img/info_bg.png) ;">
    <div class="col-12" style="background: transparent;">
        <img class="" src="<?php echo  base_url() ?>assets/img/info_logo.png" style="width: 300px;left : calc(50% - 150px);    position: relative;">
    </div>
        <?php
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández
         *		   mario.martinez.f@hotmail.es
         *	Fecha: 01 may 2018
         *	Nota: Si el token no es valido, retornara un id_usuario falso si
         *          retorna falso enviara el mensaje de TOKEN NO VALIDO, en
         *          caso de ser valido enviara el formulario para setear el
         *          password.
         ***********************************************************************/
        $html = '';
        if (!$id_user) {
            $html .= '<div class="align-self-center w-100" style="margin: 0 !important;">
                <div class="col-12 padding_bottom_10">
                    <div class="font-weight-bold text-center display-1">TOKEN NO VALIDO</div>      
                </div>
            </div>';
        } else {
            $html .= '
            <script>var id_usuario = ' . $id_user . ',
                        token = "' . $token . '",
                        base_url = "' . base_url() . '";</script>
            <div class="align-self-center w-100" style="margin: 0 !important;">
                <div class="col-12 padding_bottom_10">
                    <div class="h2 font-weight-bold text-center" style="color:#fff">Recuperar contraseña</div>      
                </div>
                <div class="col-12 col-md-8 mx-auto">
                    <div class="text-center fondo_formulario_recover" >
                        <form class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="password" class="text-left w-100 font-weight-bold">Nuevo password:</label>
                                    <input type="password" id="password" class="form-control"  placeholder="Password">
                                </div>
                                <div class="form-group">
                                    <label for="confirmar_password" class="text-left w-100 font-weight-bold">Confirmar password:</label>
                                    <input type="password" id="confirmar_password" class="form-control"  placeholder="Password">
                                </div>
                            </div>
                        </form>
                        <div class="row justify-content-md-center">
                        <div class="col-md-6 col-12">
                            <button style="background-color: #593085;" onclick="Enviar()" style="background: black;" class="btn btn-lg text-white">Confirmar contraseña</button>                        
                        </div>
                    </div>
                    </div>
                </div>
            </div>';
        }
        echo $html;
        ?>
    </div>
    <nav class="navbar navbar-expand-md navbar-dark fixed-bottom cabecera_pie_recover" style="background: transparent;">
        <div class="font-weight-normal text-right lead text-white">© 2021 Kreativeco. Todos los derechos reservados</div>
    </nav>
    <script src="<?php echo base_url() ?>assets/plugins/jquery/js/jquery-3.3.1.min.js"></script>
    <script src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/bootstrap_4/js/bootstrap.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/loadmask/js/spin.js" type="text/javascript"></script>
    <script src="<?php echo base_url() ?>assets/plugins/form_validation/jquery.validate.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url() ?>assets/plugins/dataTables_1_10_16/datatables.js"></script>
    <script src="<?php echo base_url() ?>assets/js/recuperar_password.js"></script>
</body>

</html>