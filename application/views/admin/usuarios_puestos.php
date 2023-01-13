<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Puestos</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row">
                    <div class="col-12 text-center h4 pt-2">Registros</div>
                </div>
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarPuesto()"><i class="fa fa-plus"></i> Agregar</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <!--a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarUsuario()"><i class="fa fa-user"></i> Agregar</a-->
                    </div>
                </div>
                <table id="tabla_puestos" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Puesto</th>
                        <th>Editar/Borrar</th>
                    </tr>
                    </thead>
                    <tbody id="contenido_tabla_puestos">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_puesto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Catálogo puestos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_job" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="job_id">
                    <div class="form-group">
                        <label for="job_name">Puesto</label>
                        <input type="text" class="form-control" id="job_name">
                    </div>
                    <!--<div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" name="password" class="form-control" id="password"  placeholder="Contreaseña">
                    </div>-->
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/puestos.js"></script>