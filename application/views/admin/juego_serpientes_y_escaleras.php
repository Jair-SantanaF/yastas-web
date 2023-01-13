<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Juego Serpientes y Escaleras</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-11 alert alert-success" role="alert">
                Aquí podrás agregar, editar, eliminar preguntas y respuestas para el juego de Serpientes y escaleras.
            </div>
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row py-4">
                    <div class="col-12">
                        <button class="btn btn-success" onclick="agregar()">Agregar</button>
                    </div>
                </div>
                <table id="tabla_roulette_quiz" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Pregunta</th>
                            <th>Respuestas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenido_tabla_serpientes_escaleras">

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="nueva_pregunta" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pregunta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <div class="col-12">
                                <label>Pregunta</label>
                                <input class="form-control" id="pregunta" placeholder="Pregunta">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="col-12">
                                <label>Respuesta</label>
                                <input class="form-control" id="respuesta" placeholder="Respuesta">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="col-12">
                                <label>Correcta</label>
                                <select class="form-control" id="es_correcta">
                                    <option value="1">Si</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="row col-12">
                            <div class="col-12">
                                <button class="btn btn-success" onclick="agregarRespuesta()">Agregar Respuesta</button>
                            </div>
                        </div>
                        <div class="row col-12">
                            <div class="col-12">
                                <table class="table ">
                                    <tr>
                                        <th>Respuesta</th>
                                        <th>Correcta</th>
                                        <th></th>
                                    </tr>
                                    <tbody id="contenedor_respuestas">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-right">
                        <button class="btn btn-primary" onclick="guardar()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url() ?>/assets/js/serpientes_escaleras.js"></script>
</div>