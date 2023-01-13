<style>
    .dias{
        height: 40px;
        width: 40px;
        border:solid 1px #eee;
        text-align: center;
        margin: unset;
    }
    .dias.seleccionado{
        background: rgba(0,165,255,0.2);
    }
    input{
        display: none;
    }
    td{
        padding: 10px !important;
    }
</style>
<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Preguntas de ambiente laboral</div>
    <!-- <iframe class="col-12" src="http://localhost/nuup_respaldo/assets/js/admin_angular/"></iframe> -->
    <div class="col-md-12 col-12 row">
        <br>
        <label class="col-12">Rango de fechas habilitadas</label>
        <div class="col-4">
            <label>Fecha inicio</label>
            <input type="date" class="form-control" id="fecha_inicio">
        </div>
        <div class="col-4">
            <label>Fecha fin</label>
            <input type="date" class="form-control" id="fecha_fin">
        </div>
        <div class="col-4">
            <button onclick="guardar_fechas()" class="btn btn-primary col-12">Guardar</button>
            <br>
            <br>
        </div>
        <br>
        <br>
        <div class="col-12">
            <br>
            <table class="table">
                <tr>
                    <td style="max-width: 400px!important;width: 400px;">Pregunta</td>
                    <td>
                        <button onclick="guardar_dias()" class="btn btn-success">Guardar dias habilitados</button>
                    </td>
                    <td>
                        <button onclick="nueva_pregunta()" class="btn btn-success">Agregar pregunta</button>
                    </td>
                </tr>
                <tbody id="contenedor_preguntas"></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_edicion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Editar pregunta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-12">
                    <input class="form-control" id="pregunta">
                </div>
                <div class="col-12">
                    <button class="btn btn-primary" onclick="actualizar()">Guardar</button>
                </div>
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
                <div id="form_preguntas">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="form-group">
                                <label for="question">Pregunta</label>
                                <div class="col-12">
                                    <input id="question" class="form-control">
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
                    <button class="btn btn-primary" onclick="GuardarPregunta()">Guardar</button>
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
<input type="hidden" id="question_id" value="">
                        <input type="hidden" id="type_id_answers" value="">

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

<script src="<?php echo base_url() ?>assets/js/ambiente_laboral.js?v=<?php echo time(); ?>"></script>