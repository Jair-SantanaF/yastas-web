<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Retos</div>
    <div class="col-md-12 col-12 mt-4">
        <div class="row justify-content-center">
            <div class="col-xl-11 col-md-11 col-sm-11 col-12" id="retos">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="mes_curso_filtro">Mes</label>
                            <select class="form-control" name="mes_curso_filtro" id="mes_curso_filtro">
                                <option hidden selected value="">-- Seleccionar --</option>
                                <option value="null">Ninguno</option>
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

                    <div class="col-md-8 text-right">
                        <button class="btn btn-primary" onclick="descargarCsv()">Descargar CSV</button>
                        <button class="btn btn-secondary" onclick="agregarRetoTodos()">Agregar a todos</button>
                        <button class="btn btn-success" onclick="agregarReto()">Agregar</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item mr-3" role="presentation">
                                <button class="nav-link active border-0" id="pills-home-tab" data-toggle="pill" data-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Retos cargados</button>
                            </li>
                            <li class="nav-item mr-3" role="presentation">
                                <button class="nav-link border-0" id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Retos realizados</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                <!-- tabla para retos cargados -->
                                <div class="table-responsive">
                                    <table id="tabla_retos_cargados" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Reto</th>
                                                <th>Asignado por</th>
                                                <th>Asignado a</th>
                                                <th>Estatus</th>
                                                <th>Fecha de carga</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- tabla para retos realizados -->
                            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                <div class="table-responsive">
                                    <table id="tabla_retos_realizados" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Reto</th>
                                                <th>Asignado por</th>
                                                <th>Asignado a</th>
                                                <th>Estatus</th>
                                                <th>Fecha de carga</th>
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


    <!-- modales para el apartado de agregar retos a todos -->
    <div class="modal fade" id="modal_agregar_reto_todos" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar reto a todos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre_reto_todos">Nombre del reto</label>
                                <input class="form-control" id="nombre_reto_todos" placeholder="Nombre del reto">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mes_curso">Mes en curso</label>
                                <select class="form-control" name="mes_curso_todos" id="mes_curso_todos">
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

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion_reto">Descripción del reto</label>
                                <textarea class="form-control" name="descripcion_reto_todos" id="descripcion_reto_todos" cols="30" rows="10"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="region_id">Seleccionar región</label>
                                <select class="form-control" name="region_id_todos" id="region_id_todos">
                                    <option hidden selected value="">-- Seleccionar --</option>
                                    <option value="todos">Todos</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-right">
                        <div class="col-md-12">
                            <button class="btn btn-primary" onclick="guardarRetoTodos()">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="<?php echo base_url() ?>/assets/js/challenges.js?v=<?php echo time(); ?>"></script>
</div>