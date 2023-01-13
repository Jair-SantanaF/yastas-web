<style>
    .circle-dot-justificante { 
        width: 15px;
        height: 15px;
        background: #498dda; 
        -moz-border-radius: 70px; 
        -webkit-border-radius: 70px; 
        border-radius: 70px;
        display: inline-block;
    }
    .circle-dot-permiso { 
        width: 15px;
        height: 15px;
        background: #01cc01; 
        -moz-border-radius: 70px; 
        -webkit-border-radius: 70px; 
        border-radius: 70px;
        display: inline-block;
    }
    .circle-dot-sin-justificante { 
        width: 15px;
        height: 15px;
        background: #cf1e33;
        -moz-border-radius: 70px; 
        -webkit-border-radius: 70px; 
        border-radius: 70px;
        display: inline-block;
    }
    .circle-dot-accidente { 
        width: 15px;
        height: 15px;
        background: #fbcd02;
        -moz-border-radius: 70px; 
        -webkit-border-radius: 70px; 
        border-radius: 70px;
        display: inline-block;
    }
</style>
<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Incidencias</div>
    <div class="col-md-12 col-12 mt-4">
        <div class="row justify-content-center">
            <div class="col-xl-11 col-md-11 col-sm-11 col-12" id="incidencias">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="region_id">Región</label>
                            <select class="form-control" name="region_id" id="region_id">
                                <option hidden selected value="">-- Seleccionar --</option>
                                <option value="null">Todos</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="start-date">Desde</label>
                            <input type="date" class="form-control" id="start-date" name="start-date">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="end-date">Hasta</label>
                            <input type="date" class="form-control" id="end-date" name="end-date">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <p class="m-0 p-0">&nbsp;</p>
                        <button class="btn btn-primary" onclick="filtrarResultados()">Filtrar</button>
                        <button class="btn btn-secondary" onclick="limpiarResultados()">Limpiar</button>

                    </div>

                    <div class="col-md-3 text-right">
                        <button class="btn btn-primary" onclick="descargarCsv()">Descargar CSV</button>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-12">
                        <p class="font-weight-bold">Tipos de incidencias:</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <ul style="list-style: none;">
                            <li><span class="circle-dot-justificante"></span> Enfermedad con justificante</li>
                            <!-- <li><span class="circle-dot-permiso"></span> Permiso</li> -->
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <ul style="list-style: none;">
                            <li><span class="circle-dot-sin-justificante"></span> Enfermedad sin justificante</li>
                            <!-- <li><span class="circle-dot-accidente"></span> Accidente</li> -->
                        </ul>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <!-- tabla para incidencias -->
                        <div class="table-responsive">
                            <table id="tabla_incidencias" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Clave</th>
                                        <th>Asistencias</th>
                                        <th>Retardos</th>
                                        <th>Faltas</th>
                                        <th><div class="circle-dot-justificante"></div></th>
                                        <th><div class="circle-dot-sin-justificante"></div></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_agregar_reto" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar reto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre_reto">Nombre del reto</label>
                                <input class="form-control" id="nombre_reto" placeholder="Nombre del reto">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mes_curso">Mes en curso</label>
                                <select class="form-control" name="mes_curso" id="mes_curso">
                                    <option hidden selected value="">-- Seleccionar --</option>
                                    <option value="01">Enero</option>
                                    <option value="02">Febrero</option>
                                    <option value="03">Marzo</option>
                                    <option value="04">Abril</option>
                                    <option value="05">Mayo</option>
                                    <option value="06">Junio</option>
                                    <option value="07">Julio</option>
                                    <option value="08">Agosto</option>
                                    <option value="09">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="descripcion_reto">Descripción del reto</label>
                                <textarea class="form-control" name="descripcion_reto" id="descripcion_reto" cols="30" rows="10"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="region_id">Seleccionar región</label>
                                        <select class="form-control" name="region_id" id="region_id">
                                            <option hidden selected value="">-- Seleccionar --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="region">Seleccionar tutor</label>
                                        <select class="form-control" name="tutor_id" id="tutor_id" disabled>
                                            <option hidden selected value="">-- Seleccionar --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="contenedor_alumnos">

                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-right">
                        <div class="col-md-12">
                            <button class="btn btn-primary" onclick="guardarReto()">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_editar_reto" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <input type="hidden" id="reto_id_editar">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar reto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nombre_reto_editar">Nombre del reto</label>
                                <input class="form-control" id="nombre_reto_editar" placeholder="Nombre del reto">
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion_reto_editar">Descripción del reto</label>
                                <textarea class="form-control" name="descripcion_reto_editar" id="descripcion_reto_editar" cols="30" rows="10"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-right">
                        <div class="col-md-12">
                            <button class="btn btn-primary" onclick="actualizarReto()">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url() ?>/assets/js/incidencias.js?v=<?php echo time(); ?>"></script>
</div>