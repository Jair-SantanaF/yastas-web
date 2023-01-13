<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Asignar equipo a usuario</div>
    <div class="col-md-12 col-12 mt-4">
        <div class="row justify-content-center">
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row mb-3">
                    <div class="col-md-12 text-right">
                        <button class="btn btn-success" onclick="agregarEquipo()">Agregar</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item mr-3" role="presentation">
                                <button class="nav-link active border-0" id="pills-tutores-tab" data-toggle="pill" data-target="#pills-tutores" type="button" role="tab" aria-controls="pills-profile" aria-selected="true">Tutores</button>
                            </li>
                            <li class="nav-item mr-3" role="presentation">
                                <button class="nav-link border-0" id="pills-becarios-tab" data-toggle="pill" data-target="#pills-becarios" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Becarios</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-tutores" role="tabpanel" aria-labelledby="pills-tutores-tab">
                                <!-- tabla para mostrar los tutores -->
                                <div class="table-responsive">
                                    <table id="tabla_tutores" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Tutor</th>
                                                <th>Asignado a</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- tabla para mostrar los becarios -->
                            <div class="tab-pane fade" id="pills-becarios" role="tabpanel" aria-labelledby="pills-becarios-tab">
                                <div class="table-responsive">
                                    <table id="tabla_becarios" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Becario</th>
                                                <th>Asignado a</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_agregar_equipo" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Asignar equipo a usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="tipo_usuario">Tipo de usuario</label>
                            <select class="form-control" name="tipo_usuario" id="tipo_usuario">
                                <option hidden selected value="">-- Seleccionar --</option>
                                <option value="1">Tutor</option>
                                <option value="2">Becario</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="region_equipo_id">Seleccionar región</label>
                            <select class="form-control" name="region_equipo_id" id="region_equipo_id">
                                <option hidden selected value="">-- Seleccionar --</option>
                            </select>
                        </div>
                    </div>

                    <div id="containerTutor">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="asesor_equipo_id">Seleccionar asesor</label>
                                <select class="form-control" name="asesor_equipo_id" id="asesor_equipo_id" disabled>
                                    <option hidden selected value="">-- Seleccionar --</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3" id="contenedor_tutores"></div>
                    </div>

                    <div id="containerBecario">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="tutor_equipo_id">Seleccionar tutor</label>
                                <select class="form-control" name="tutor_equipo_id" id="tutor_equipo_id">
                                    <option hidden selected value="">-- Seleccionar --</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3" id="contenedor_becarios"></div>
                    </div>


                    <div class="row justify-content-right">
                        <div class="col-md-12">
                            <button class="btn btn-primary" onclick="guardarEquipo()" id="btnGuardarEquipo">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_editar_tutor" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <input type="hidden" id="tutor_id_editar">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar equipo a usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nombre_tutor_editar">Nombre del tutor</label>
                                <input class="form-control" id="nombre_tutor_editar" placeholder="Nombre del tutor" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="region_tutor_id">Seleccionar región</label>
                                <select class="form-control" name="region_tutor_id" id="region_tutor_id">
                                    <option hidden selected value="">-- Seleccionar --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="asesor_id">Seleccionar asesor</label>
                                <select class="form-control" name="asesor_id" id="asesor_id">
                                    <option hidden selected value="">-- Seleccionar --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-right">
                        <div class="col-md-12">
                            <button class="btn btn-primary" onclick="actualizarDatosTutor()">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_editar_becario" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <input type="hidden" id="becario_id_editar">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar equipo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nombre_becario_editar">Nombre del becario</label>
                                <input class="form-control" id="nombre_becario_editar" placeholder="Nombre del becario" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="region_becario_id">Seleccionar región</label>
                                <select class="form-control" name="region_becario_id" id="region_becario_id">
                                    <option hidden selected value="">-- Seleccionar --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="tutor_becario_id">Seleccionar tutor</label>
                                <select class="form-control" name="tutor_becario_id" id="tutor_becario_id">
                                    <option hidden selected value="">-- Seleccionar --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-right">
                        <div class="col-md-12">
                            <button class="btn btn-primary" onclick="actualizarDatosBecario()">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url() ?>/assets/js/teams.js?v=<?php echo time(); ?>"></script>
</div>