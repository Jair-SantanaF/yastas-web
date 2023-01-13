<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Juego Ruleta</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row py-4">
                    <div class="alert alert-success" role="alert">
                        Aquí se mostrarán las 8 categorias que podrás configurar para el juego de ruleta.
                    </div>
                </div>
                <table id="tabla_roulette_quiz" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th>Puntaje</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenido_tabla_roulette_quiz">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_preguntas" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Crear preguntas de la categoría</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="seccion_preguntas" class="row justify-content-center d-none">
                    <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                        <div class="row">
                            <div class="col-12 pt-2 pb-2 text-right">
                                <a id="agregar_pregunta" class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarPregunta()"><i class="fa fa-plus"></i> Agregar</a>
                            </div>
                        </div>
                        <input type="hidden" id="selected_quiz_id">
                        <table id="tabla_roulette_questions" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Pregunta</th>
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
<div class="modal fade" id="modal_respuestas" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detalle respuestas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="seccion_respuestas" class="row justify-content-center d-none">
                    <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                        <div class="row">
                            <div class="col-12 pt-2 pb-2 text-right">
                                <a id="agregar_respuesta" class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarRespuesta()"><i class="fa fa-plus"></i> Agregar</a>
                            </div>
                        </div>
                        <input type="hidden" id="selected_question_id">
                        <table id="tabla_roulette_answers" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Respuesta</th>
                                    <th>Correcta</th>
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
<div class="modal fade" id="modal_roulette_quiz" tabindex="-1" role="dialog" aria-labelledby="title_roulette_quiz" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title_roulette_quiz">Editar categoría</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_roulette_quiz" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="roulette_quiz_id">
                    <div class="form-group">
                        <label for="roulette_quiz_name">Nombre</label>
                        <input type="text" name="roulette_quiz_name" class="form-control" id="roulette_quiz_name" placeholder="Nombre categoría">
                    </div>
                    <div class="form-group">
                        <label for="roulette_quiz_points">Puntaje</label>
                        <input type="number" name="roulette_quiz_points" class="form-control" id="roulette_quiz_points" placeholder="Puntaje">
                    </div>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="modal_roulette_question" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Catálogo Preguntas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_roulette_question" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="roulette_question_id">
                    <div class="form-group">
                        <label for="roulette_question">Pregunta</label>
                        <input type="text" name="roulette_question" class="form-control" id="roulette_question" placeholder="Pregunta">
                    </div>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_roulette_answer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Catálogo Respuestas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_roulette_answer" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="roulette_answer_id">
                    <div class="form-group">
                        <label for="roulette_answer">Respuesta</label>
                        <input type="text" name="roulette_answer" class="form-control" id="roulette_answer" placeholder="Respuesta">
                    </div>
                    <div class="form-group">
                        <label for="roulette_correct">Correcta</label>
                        <select name="roulette_correct" class="form-control" id="roulette_correct">
                            <option value="">Seleccionar...</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/juego_ruleta.js"></script>