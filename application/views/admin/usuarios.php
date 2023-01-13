<div class="row contenedor_detalle" style="width: 78%;">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Usuarios</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center" style="width: 100%;overflow: auto;">
            <div class="alert alert-success" role="alert">
                Listado de todos los usuarios registrados, se pueden eliminar y editar algunos campos
            </div>
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row">
                    <div class="col-12 text-center h4 pt-2">Registros</div>
                </div>
                <div class="row col-12 justify-content-left">
                    <!-- <select id="grupos" onchange="obtenerUsuarios()"></select> -->
                    <button class="btn btn-primary" onclick="descargarCsv()">Descargar Csv</button>
                </div>
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <!--a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarUsuario()"><i class="fa fa-user"></i> Agregar</a-->
                    </div>
                </div>
                <table id="tabla_usuarios" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Fecha de registro</th>
                            <th>Email</th>
                            <th>Solicitud</th>
                            <th>Puesto</th>
                            <th>Teléfono</th>
                            <th>Score</th>
                            <th>Grupos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenido_tabla_usuarios">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_usuario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Catálogo usuarios</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_usuario" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="id_usuario">
                    <div class="form-group">
                        <label for="nombre_usuario">Nombre</label>
                        <input type="text" name="nombre_usuario" class="form-control" id="nombre_usuario" placeholder="Nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido_usuario">Apellido</label>
                        <input type="text" name="apellido_usuario" class="form-control" id="apellido_usuario" placeholder="Apellido" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" name="email" class="form-control" id="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <label for="id_puesto">Puesto</label>
                        <select name="id_puesto" class="form-control" id="id_puesto"></select>
                    </div>
                    <!--<div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" name="password" class="form-control" id="password"  placeholder="Contreaseña">
                    </div>-->
                    <button class="btn btn-primary" onclick="GuardarUsuario()">Guardar</button>
                </form>
            </div>

        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/usuarios.js"></script>