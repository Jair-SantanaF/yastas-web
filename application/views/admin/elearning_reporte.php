<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">
        Elearning reporte
    </div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="alert alert-success" role="alert">
                Reporte de interacción con elearnings, listado de usuarios con resultados y fechas en las que vieron el curso
            </div>
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row">
                    <div class="col-3 pt-2 pb-2">
                        <div class="form-group">
                            <select id="select_elearning" class="form-control" aria-describedby="select_">
                                <option value="">Seleccionar...</option>
                            </select>
                            <small id="select_" class="form-text text-muted">Selecciona un curso para ver su reporte</small>
                        </div>
                    </div>
                </div>
                <table id="tabla_cursos_detalle" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr class="lead_0_9">
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Score</th>
                            <th>Fecha inicio</th>
                            <th>Fecha salida</th>
                            <th>Tiempo</th>
                            <th>Intento final</th>
                            <th>Max intentos</th>
                            <th>Estatus</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/elearning_reporte.js"></script>