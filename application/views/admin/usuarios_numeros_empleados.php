<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Numeros de empleados</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row">
                    <div class="col-12">
                        Instrucciones: Aquí podras visualizar todos los usuarios que cuentan con numero de empleado que aun no han sido registrados en la plataforma, cuando un numero de empleado se ha registros aparecera en el apartado de usuarios y se borrara de este listado.
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarNumeroEmpleado()"><i class="fa fa-user"></i> Agregar</a>
                    </div>
                </div>
                <table id="tabla_numeros" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Email</th>
                        <th>No. empleado</th>
                        <th>Grupo</th>
                        <th>Puesto</th>
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
<div class="modal fade" id="modal_numero_empleado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Numeros de empleados</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_numero_empleado" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="number_employee_id">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" id="email"  placeholder="correo@mail.com">
                    </div>
                    <div class="form-group">
                        <label for="number">Numero de empleado</label>
                        <input type="text" name="number" class="form-control" id="number"  placeholder="Empleado123">
                    </div>
                    <div class="form-group">
                        <label for="job_id">Puesto</label>
                        <select name="job_id" class="form-control" id="job_id">

                        </select>
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
<script src="<?php echo base_url() ?>assets/js/usuarios_numeros_empleados.js"></script>
