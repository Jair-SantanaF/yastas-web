<?php
defined('BASEPATH') or exit('No direct script access allowed');
/***********************************************************************
 *    Autor: Mario Adrián Martínez Fernández
 *           mario.martinez.f@hotmail.es
 *    Fecha: 08 mar 2018
 *    Nota: Se valida que exista session en caso de que no haya conexion
 *          sacara a la ventana de inicio para que vuelva iniciar
 *          sesion.
 ***********************************************************************/
$session = $this->session->userdata('id_user');

if (!isset($session)) {
    //$this->session->sess_destroy();
    header("Location:" . base_url() . '/admin');
    exit;
}
/***********************************************************************
 *    Autor: Mario Adrián Martínez Fernández
 *           mario.martinez.f@hotmail.es
 *    Fecha: 11 mar 2018
 *    Nota: Se instancia el model para obtener los datos de la funcion,
 *          ubicada en la rama de models.
 ***********************************************************************/
$KCO = &get_instance();
$KCO->load->model('admin_mdl');
?>
<link rel="stylesheet" href="<?php echo base_url() ?>assets/css/admin.css">
<div class="wrapper">
    <!-- Sidebar  -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <img class="w-100" src="<?php echo base_url() ?>assets/img/logo_blanco.png">
        </div>
        <?php if ($this->session->userdata('rol_id') == 1) { ?>
            <div class="form-group px-3">
                <label for="session_company_id">Empresa</label>
                <select name="session_company_id" class="form-control" id="session_company_id"></select>
            </div>
        <?php } ?>
        <?php if ($this->session->userdata('rol_id') == 5) { ?>
            <div class="form-group px-3">
                <label for="select_regiones">Region</label>
                <select style="height: 40px!important;" name="select_regiones" class="form-control" id="select_regiones" onchange="cambiar_region()"></select>
            </div>
        <?php } ?>
        <div class="font-weight-bold mt-4 pl-3 lead">Menú</div>
        <?php
        $validate__ = 'nuup';
        if ($validate__ !== 'nuup') {
        ?>
            <div class="row justify-content-center">
                <div class="col-10">
                    <div class="form-group">
                        <select id="select_type_user" class="form-control" aria-describedby="select_">
                            <?php if ($this->session->userdata('empresa_id') == EMPRESA_INTERNOS) {
                            ?>
                                <option value="internal">Interno</option>
                                <option value="external">Externo</option>
                            <?php
                            } else {
                            ?>
                                <option value="external">Externo</option>
                                <option value="internal">Interno</option>
                            <?php
                            } ?>
                        </select>
                        <small id="select_" style="font-size: 12px" class="form-text text-white">Selecciona el tipo de usuario que deseas visualizar</small>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
        <ul class="list-unstyled components pl-2 pr-2">
            <!-- <li><a onclick="cargarHtml('admin/Analiticos')" class="nav-link" href="javascript:void(0)"><i class="far fa-chart-bar" style="font-size:13px;margin-right:5px"></i>Gráficas</a></li>
            <li><a onclick="cargarHtml('admin/ReporteUso')" class="nav-link" href="javascript:void(0)"><i class="far fa-file" style="font-size:13px;margin-right:5px"></i>Reporte de uso</a></li> -->
            <?php
            if (isset($session)) {
                /***********************************************************************
                 *    Autor: Mario Adrián Martínez Fernández
                 *           mario.martinez.f@hotmail.es
                 *    Fecha: 23 mar 2018
                 *    Nota: Se hace peticion a model para crear Arbol de modulos segun
                 *          los permisos del usuario tenga asignados en BD.
                 ***********************************************************************/
                $html = $KCO->admin_mdl->CrearArbolMenu($this->session->userdata('rol_id'));
                echo $html;
            }
            ?>
            <li id="li_contacto"><a onclick="cargarHtml('admin/MensajesContacto')" class="nav-link" href="javascript:void(0)"><img class="pr-1 pb-1" src="">Mensajes de contacto</a></li>
            <li id="li_reset"><a onclick="cargarHtml('admin/ResetPassword')" class="nav-link" href="javascript:void(0)"><img class="pr-1 pb-1" src="">Reset Password</a></li>
            <li id="li_reset_invitacion"><a onclick="cargarHtml('admin/ResetInvitacion')" class="nav-link" href="javascript:void(0)"><img class="pr-1 pb-1" src="">Restablecer Invitación</a></li>
            <li id="li_desbloqueo"><a onclick="cargarHtml('admin/Desbloqueo')" class="nav-link" href="javascript:void(0)"><img class="pr-1 pb-1" src="">Desbloqueo de usuarios</a></li>
             <li id="li_capturas"><a onclick="cargarHtml('admin/SeccionesCapturas')" class="nav-link" href="javascript:void(0)"><img class="pr-1 pb-1" src="">Capturas de pantalla</a></li> 
        </ul>
    </nav>

    <!-- Page Content  -->
    <div id="content">
        <div class="container-fluid">
            <div class="row fondo_gris">
                <div class="col-6 text-left text-white h3 pt-3">
                    <i id="sidebarCollapse" class="fas fa-bars cursor_pointer"></i>
                </div>
                <div class="col-6 text-right pt-2 pb-2">
                    <div class="dropdown" style="background: #000!important">
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-rigth button_transparente" data-toggle="dropdown">
                            <span>Hola,</span><span class="font-weight-bold"><?php echo $this->session->userdata('nombre'); ?>!</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" style="background:#000!important">
                            <a class="dropdown-item" onclick="CerrarSesion()" style="background:#000!important;color:#fff!important" href="#">Cerrar session</a>
                        </div>
                        <img class="imagen_redonda imagen_header" src="<?php echo ($this->session->userdata('foto') === 'default') ? base_url() . 'assets/img/persona_1.png' : $this->session->userdata('foto'); ?>">
                    </div>
                </div>
            </div>
            <div id="contenedor_detalle">
            </div>
        </div>
        <!--<div>
        <h1>AQUI CÓDIGO PARA ADMIN</h1>
        <button class="btn btn-warning">Siler</button>
        <select id="select_idioma" class="form-control">
            <option value="">Seleccionar...</option>
            <option value="es_ES">Español</option>
            <option value="en_US">Ingles</option>
            <option value="fr_FR">Frances</option>
        </select>
    </div>-->
        <script src="<?php echo base_url() ?>assets/js/admin.js"></script>
    </div>
</div>
<!--<nav class="navbar navbar-inverse bg-inverse fixed-top navbar-toggleable-md bg-faded">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#nav_admin" aria-controls="nav_admin" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav_admin">
        <ul class="navbar-nav">
            <?php
            if (isset($session)) {
                /***********************************************************************
                 *    Autor: Mario Adrián Martínez Fernández
                 *           mario.martinez.f@hotmail.es
                 *    Fecha: 23 mar 2018
                 *    Nota: Se hace peticion a model para crear Arbol de modulos segun
                 *          los permisos del usuario tenga asignados en BD.
                 ***********************************************************************/
                $html = ''; //$KCO->admin_mdl->CrearArbolMenu($this->session->userdata('rol_id'));
                echo $html;
            }
            ?>
        </ul>
    </div>
</nav>-->
<script>
    jQuery(document).ready(function() {
        if (rol_id == 4 || rol_id == 7) {
            console.log("si entra pero no lo esta ocultando")
            var v = "";
            v = document.getElementById("li_desbloqueo")
            if(v){
                v.style.display = "none"
            }
            v = document.getElementById("li_capturas")
            if(v){
                v.style.display = "none"
            }
            v = document.getElementById("li_contacto")
            if(v){
                v.style.display = "none"
            }
            v = document.getElementById("li_reset")
            if(v){
                v.style.display = "none"
            }
            v = document.getElementById("li_reset_invitacion")
            if(v){
                v.style.display = "none"
            }
        }
    })
</script>