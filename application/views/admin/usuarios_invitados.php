<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Usuarios invitados</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-11 alert alert-success" role="alert">
                En esta sección puedes invitar a mas usuarios, les llegara una invitacion a su correo con un link para registrase y descargar la app
            </div>
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
            <div class="row">
                    <div class="col-6">
                    </div>
                    <div class="col-6 row">
                        <a href="<?php echo base_url(); ?>uploads/alta_masiva.xlsx">Descarga la plantilla de alta masiva</a>
                        <form id="form_masivo_form" class="row" action="<?php echo base_url(); ?>Ws/alta_masiva" method="POST" enctype="multipart/form-data">
                            <div class="col-7 form-group">
                                <input class="form-control" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" name="uploadFile" id="uploadFile" type="file">
                            </div>
                            <div class="col-5">
                                <button class="btn btn-success">Subir archivo</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarInvitado()"><i class="fa fa-user"></i> Agregar</a>
                    </div>
                </div>
                <table id="tabla_invitados" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>ID</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_invitado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Invitados</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_invitado" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="invited_id">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="member_mail" class="form-control" id="email" placeholder="correo@mail.com">
                    </div>
                    <div class="form-group">
                        <label for="number_employee">Id</label>
                        <input type="number_employee" name="number_employee" class="form-control" id="number_employee" placeholder="ID comisionista / Operador">
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="nombre" name="nombre" class="form-control" id="nombre" placeholder="Nombre">
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="apellido" name="apellido" class="form-control" id="apellido" placeholder="Apellido">
                    </div>
                    <div class="form-group">
                        <label for="group_id">Grupo</label>
                        <select name="group_id" class="form-control" id="group_id">

                        </select>
                    </div>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>

        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/usuarios_invitados.js?v=<?php echo time(); ?>"></script>