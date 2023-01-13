<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Juego Perfilador</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row py-4">
                    <div class="col-12">
                        Instrucciones: Registra tus categorías con las cuales los usuarios contesar jugando en perfilador.
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 pb-2 text-right">
                        <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarProfilerQuiz()"><i class="fa fa-plus"></i> Agregar</a>
                    </div>
                </div>
                <table id="tabla_profiler_quiz" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Categoría</th>
                        <th>Puntaje</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody id="contenido_tabla_profiler_quiz">

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
                        <input type="hidden" id="selected_profiler_quiz_id">
                        <table id="tabla_profiler_questions" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
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
                        <table id="tabla_profiler_answers" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
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
<div class="modal fade" id="modal_profiler_quiz" tabindex="-1" role="dialog" aria-labelledby="title_profiler_quiz" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title_profiler_quiz">Agregar categoría</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_profiler_quiz" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="profiler_quiz_id">
                    <div class="form-group">
                        <label for="profiler_quiz_history">Nombre</label>
                        <input type="text" name="profiler_quiz_history" class="form-control" id="profiler_quiz_history"  placeholder="Nombre">
                    </div>
                    <div class="form-group">
                        <label for="profiler_quiz_points">Puntaje</label>
                        <input type="number" name="profiler_quiz_points" class="form-control" id="profiler_quiz_points"  placeholder="Puntaje">
                    </div>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="modal_profiler_question" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Catálogo Preguntas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_profiler_question" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="profiler_question_id">
                    <div class="form-group">
                        <label for="profiler_question">Pregunta</label>
                        <input type="text" name="profiler_question" class="form-control" id="profiler_question"  placeholder="Pregunta">
                    </div>
                    <div class="form-group">
                        <input type="file" id="profiler_question_image" accept="image/*" onchange="loadProfilerQuestionImage(event)" class="d-none">
                        <div class="w-100 d-flex justify-content-center">
                            <img id="profiler_question_image_preview" style="width: 150px" class="cursor-pointer" src="<?php echo base_url()?>/assets/img/Camara.png">
                        </div>
                        <div class="small text-gray text-center">
                            Cambiar imagen
                        </div>
                    </div>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="modal_profiler_answer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Catálogo Respuestas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_profiler_answer" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="profiler_answer_id">
                    <div class="form-group">
                        <label for="profiler_answer">Respuesta</label>
                        <input type="text" name="profiler_answer" class="form-control" id="profiler_answer"  placeholder="Respuesta">
                    </div>
                    <div class="form-group">
                        <label for="profiler_correct">Correcta</label>
                        <select name="profiler_correct" class="form-control" id="profiler_correct">
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

<script src="<?php echo base_url() ?>assets/js/juego_perfilador.js"></script>
