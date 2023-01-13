<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Usuarios Administradores</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-md-10 col-sm-10 col-12">
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarAdmin()"><i class="fa fa-plus"></i> Agregar</a>
                    </div>
                </div>
                <table id="" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Fecha de registro</th>
                            <th>Email</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla_usuarios">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="nuevo_usuario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nueva Usuario Administrador</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_wall_post" method="post" action="javascript:void(0)" enctype="multipart/form-data" novalidate="novalidate">
                    <input type="hidden" id="wall_post_id">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" name="nombre" class="form-control" id="nombre" placeholder="Nombre">
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" name="apellido" class="form-control" id="apellido" placeholder="Apellido">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" name="email" class="form-control" id="email" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <label>Debe contener al menos 12 caracteres</label>
                        <label>Un numero</label>
                        <label>Una minuscula</label>
                        <label>Una mayuscula</label>
                        <label>Un caracter especial</label>
                        <label for="password">Contraseña</label>
                        <input type="password" onkeyup="change()" name="password" class="form-control" id="password" placeholder="Contraseña">
                    </div>
                    <input type="text" id="tipo" style="display:none">
                    <button class="btn btn-primary" onclick="guardarAdmin()">Guardar</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/user_admin.js"></script>