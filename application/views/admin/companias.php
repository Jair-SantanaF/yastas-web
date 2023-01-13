<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Empresas</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row">
                    <div class="col-12 text-center h4 pt-2">Registros</div>
                </div>
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarCompania()"><i class="fa fa-user"></i> Agregar</a>
                    </div>
                </div>
                <table id="tabla_companias" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Empresa</th>
                        <th>Plan</th>
                        <th>Número de Usuarios</th>
                        <th>Espacio Utilizado</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody id="contenido_tabla_companias">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_compania" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Catálogo empresas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_compania" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="business_id">
                    <div class="form-group">
                        <label for="business_name">Nombre</label>
                        <input type="text" name="business_name" class="form-control" id="business_name"  placeholder="Nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="plan_id">Plan</label>
                        <select name="plan_id" class="form-control" id="plan_id"></select>
                    </div>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="modal_servicios" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Servicios contratados</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="services_business_id" value="0">
                <table id="tabla_servicios" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Servicio</th>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody id="contenido_tabla_servicios">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/companias.js"></script>
