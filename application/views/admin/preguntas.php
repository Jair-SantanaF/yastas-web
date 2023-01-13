<style>
    * {
        /* box-sizing: content-box!important; */
    }

    .usuarios,
    .grupos {
        max-height: 400px;
        overflow-y: auto;
    }
    table {
        table-layout:fixed;
    }
    table td {
    word-wrap: break-word;
    max-width: 400px;
    }
    #preguntas_tabla td {
    white-space:inherit;
    }
    #catalogo_preguntas td {
    white-space:inherit;
    }
    #tabla_respuestas td {
    white-space:inherit;
    }
</style>

<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Cuestionarios</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-11 alert alert-success" role="alert">
                En esta sección se pueden crear cuestionarios para diferentes propositos, dependiendo de la categoría se crean preguntas que se deben contestar al finalizar un archivo de biblioteca, un curso o dentro de la seccion especifica para cuestionarios en la app.
                La herramienta cuenta con soporte para varios tipos de respuesta (opción multiple, opción única, like caras, like numeros, abierta, etc).
            </div>
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="EditarCatalogo('')"><i class="fa fa-plus"></i> Agregar</a>
                    </div>
                </div>
                <input type="hidden" id="quiz_id" value="">
                <table id="catalogo_preguntas" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Fecha limite</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_preguntas_detalle" style="overflow-y: scroll;" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Preguntas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                        <div class="row">
                            <div class="col-12 pt-2 pb-2 text-right">
                                <a id="agregar_pregunta" class="btn btn-success btn-xs editar mr-2 lead_0_8 display_none" href="javascript:void(0)" onclick="EditarPregunta('')"><i class="fa fa-plus"></i> Agregar</a>
                            </div>
                        </div>
                        <input type="hidden" id="question_id" value="">
                        <input type="hidden" id="type_id_answers" value="">
                        <table id="preguntas_tabla" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Pregunta</th>
                                    <th>Tipo pregunta</th>
                                    <th>Puntos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_respuestas_detalle" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Respuestas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                        <div class="row">
                            <div class="col-12 pt-2 pb-2 text-right">
                                <a id="agregar_respuesta" class="btn btn-success btn-xs editar mr-2 lead_0_8 display_none" href="javascript:void(0)" onclick="EditarRespuesta('')"><i class="fa fa-plus"></i> Agregar</a>
                            </div>
                        </div>
                        <table id="tabla_respuestas" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Respuesta</th>
                                    <th>Correcta</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="preguntas_detalle" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Preguntas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_catalogo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Agregar cuestionario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!--  -->
                <div class="row align-items-start">
                    <div class="col-md-4">
                        <!-- <form id="form_catalogo"> -->
                        
                        <div class="form-group">
                            <label for="nombre_catalogo">Nombre cuestionario</label>
                            <input type="text" name="name" class="form-control" id="nombre_catalogo" placeholder="Nombre cuestionario">
                        </div>
                        <div class="form-group">
                            <label for="fecha_limite">Fecha Limite</label>
                            <input type="date" name="fecha_limite" class="form-control" id="fecha_limite" placeholder="Fecha limite">
                        </div>
                        <?php

                        foreach ($configuration as $index => $value) {

                            if ($value['name'] === 'category_id' && $value['active'] == 1) {
                                $options = '';
                                foreach ($categories as $index_categories => $value_categories) {
                                    $options .= '<option value="' . $value_categories['id'] . '">' . $value_categories['name'] . '</option>';
                                }
                                echo '
                                <div class="form-group">
                                    <label for="category_id">Categoría</label>
                                    <select name="category_id" class="form-control" id="category_id">
                                        <option value="">Seleccionar...</option>
                                        ' . $options . '
                                    </select>
                                </div>
                            ';
                            }
                            if ($value['name'] === 'job_id' && $value['active'] == 1) {
                                $options_puestos = '';
                                foreach ($jobs as $index_jobs => $value_jobs) {
                                    $options_puestos .= '<option value="' . $value_jobs['id'] . '">' . $value_jobs['name'] . '</option>';
                                }
                                echo '
                                <div class="form-group">
                                    <label for="job_id">Puestos</label>
                                    <select name="job_id" class="form-control" id="job_id">
                                        <option value="">Seleccionar...</option>
                                        ' . $options_puestos . '
                                    </select>
                                </div>
                            ';
                            }
                        }
                        ?>
                        <div class=""></div>
                        <div class="form-group" id="contenedor_capacitacion_obligatoria">
                            <label>Tipo de elementos</label>
                            <select id="capacitacion_obligatoria" class="form-control">
                                <option value="-1">Seleccionar ...</option>
                                <option value="0">Normal</option>
                                <option value="1">Capacitación obligatoria</option>
                            </select>
                            <article style="font-size: 10px;">Los elementos que se guarden dentro de la opción capacitacion oblogatoria no aparecen en el listado de biblioteca en la app, solo se pueden usar dentro de las capacitaciones obligatorias</article>
                        </div>
                        <!-- opcion de trivia (first_question_is_correct) -->
                       <!--  <div class="form-group" id="contenedor_trivia">
                            <label>Tipo de cuestionario</label>
                            <select id="first_question_is_correct" class="form-control">
                                <option value="-1">Seleccionar ...</option>
                                <option value="0">Normal</option>
                                <option value="1">Trivia</option>
                            </select>
                            <article style="font-size: 10px;">Al seleccionar trivia significa que si el usuario falla la primer pregunta, termina el cuestionario</article>
                        </div> -->
                        <!-- </form> -->
                    </div>
                    <div class="col-md-8 row align-items-start">
                        <div class="col-md-12 row align-items-start">
                            <div class="col-md-12 row align-items-start">
                                <div class="col-md-6">
                                    <button class="col-md-12 btn btn-primary" onclick="mostrarTablas(0)">Agregar Usuarios</button>
                                </div>
                                <div class="col-md-6">
                                    <button class="col-md-12 btn btn-primary" onclick="mostrarTablas(1)">Agregar Grupos</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 row usuarios align-items-start">
                            <input class="form-control col-8" id="buscador" placeholder="Buscar por id o nombre">
                            <button class="btn btn-success col-4" onclick="mostrarUsuariosFiltrados()" style="height: 47px!important;">Filtrar</button>
                            <table class="table">
                                <tr>
                                    <th>Nombre</th>
                                    <th></th>
                                </tr>
                                <tbody id="contenedor_usuarios"></tbody>
                            </table>
                            <!-- <select id="grupos" class="form-control col-12" onchange="obtenerPorGrupo()"></select>
                            <table class="">
                                <tr>
                                    <td>Agregar Usuarios</td>
                                    <td><button class="btn btn-success" onclick="agregarTodos()">Agregar Todos</button></td>
                                </tr>
                                <tbody id="contenedor_usuarios"></tbody>
                            </table> -->
                        </div>
                        <div class="col-md-6 usuarios">
                            <table>
                                <tr>
                                    <td>Nombre</td>
                                    <td></td>
                                </tr>
                                <tbody id="contenedor_usuarios_cuestionarios"></tbody>
                            </table>
                        </div>
                        <div class="col-md-6 grupos">
                            <table>
                                <tr>
                                    <td>Agregar Grupos</td>
                                    <td><button class="btn btn-success" onclick="agregarTodosGrupos()">Agregar Todos</button></td>
                                </tr>
                                <tbody id="contenedor_grupos"></tbody>
                            </table>
                        </div>
                        <div class="col-md-6 grupos">
                            <table>
                                <tr>
                                    <td>Nombre</td>
                                    <td></td>
                                </tr>
                                <tbody id="contenedor_grupos_cuestionarios"></tbody>
                            </table>
                        </div>
                    </div>
                    <button class="btn btn-primary" onclick="GuardarCatalogo()">Guardar</button>
                </div>
                <!-- </form> -->
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modal_preguntas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Preguntas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_preguntas">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="form-group">
                                <label for="question">Pregunta</label>
                                <div class="col-12">
                                    <div id="question"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="type_id">Tipo pregunta</label>
                                <select name="type_id" class="form-control" id="type_id">
                                    <option value="">Seleccionar...</option>
                                    <?php
                                    foreach ($types_question as $index => $value) {
                                        echo '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="points">Puntos</label>
                                <input type="number" min="0" value="0" name="points" class="form-control" id="points" placeholder="Puntos">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modal_respuestas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Respuesta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_respuestas">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="form-group">
                                <label for="answer">Respuesta</label>
                                <div class="col-12">
                                    <div id="answer">
                                    </div>
                                </div>
                                <!--<input type="text" name="answer" class="form-control" id="answer"  placeholder="Respuesta">-->
                                <input type="file" name="answer" class="form-control display_none" id="answer_file">
                                <a href="" id="view_response" class="display_none pt-3" target="_blank">Ver respuesta</a>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="correct">Correcta</label>
                                <select name="correct" class="form-control" id="correct">
                                    <option value="">Seleccionar...</option>
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modal_detalles_ux" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Respuestas por usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="input-group mb-3">
                    <select name="user_quiz_list" class="form-control" id="user_quiz_list">
                        <option value="">Seleccionar...</option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="button" onClick="detallarCuestionario()">Ver respuestas</button>
                    </div>
                </div>

                <div class="alert alert-danger" role="alert" style="display:none;" id="display_error">
                    Nadie ha contestado este cuestionario
                </div>

                <div style="display:none;" id="loading_q">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;background:#fff;display:block;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                        <g transform="translate(20 50)">
                            <circle cx="0" cy="0" r="6" fill="#e15b64">
                                <animateTransform attributeName="transform" type="scale" begin="-0.375s" calcMode="spline" keySplines="0.3 0 0.7 1;0.3 0 0.7 1" values="0;1;0" keyTimes="0;0.5;1" dur="1s" repeatCount="indefinite"></animateTransform>
                            </circle>
                        </g>
                        <g transform="translate(40 50)">
                            <circle cx="0" cy="0" r="6" fill="#f8b26a">
                                <animateTransform attributeName="transform" type="scale" begin="-0.25s" calcMode="spline" keySplines="0.3 0 0.7 1;0.3 0 0.7 1" values="0;1;0" keyTimes="0;0.5;1" dur="1s" repeatCount="indefinite"></animateTransform>
                            </circle>
                        </g>
                        <g transform="translate(60 50)">
                            <circle cx="0" cy="0" r="6" fill="#abbd81">
                                <animateTransform attributeName="transform" type="scale" begin="-0.125s" calcMode="spline" keySplines="0.3 0 0.7 1;0.3 0 0.7 1" values="0;1;0" keyTimes="0;0.5;1" dur="1s" repeatCount="indefinite"></animateTransform>
                            </circle>
                        </g>
                        <g transform="translate(80 50)">
                            <circle cx="0" cy="0" r="6" fill="#81a3bd">
                                <animateTransform attributeName="transform" type="scale" begin="0s" calcMode="spline" keySplines="0.3 0 0.7 1;0.3 0 0.7 1" values="0;1;0" keyTimes="0;0.5;1" dur="1s" repeatCount="indefinite"></animateTransform>
                            </circle>
                        </g>
                    </svg>
                </div>


                <div id="cuestionarios_dinamicos">

                </div>


            </div>

        </div>
    </div>
</div>


<script src="<?php echo base_url() ?>assets/js/preguntas.js?v=<?php echo time(); ?>"></script>