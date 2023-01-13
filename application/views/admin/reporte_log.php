<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Reporte de Logs</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-md-10 col-sm-10 col-12">
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                    </div>
                </div>
                <div class="row">
                    <div class="col-3">Fecha de Reporte</div>
                    <div class="col-3"><input class="col-12" type="date" id="fecha"></div>
                    <div class="col-3"><button class="col-12 btn btn-info" onclick="buscar_reporte()">Buscar</button></div>
                </div>
                <!-- <table id="" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
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
                </table> -->
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/reporte_log.js"></script>