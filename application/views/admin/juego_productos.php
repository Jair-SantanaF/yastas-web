<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Juego Productos</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row py-4">
                    <div class="col-12 pt-2">
                        Instrucciones: Registra los productos con los cuales los usuarios contesarán jugando en productos.
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 pb-2 text-right">
                        <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarProductsQuiz()"><i class="fa fa-plus"></i> Agregar</a>
                    </div>
                </div>
                <table id="tabla_products_quiz" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Puntaje</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody id="contenido_tabla_products_quiz">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_pasos" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detalle pasos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="seccion_pasos" class="row justify-content-center d-none">
                    <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                        <div class="row">
                            <div class="col-12 pt-2 pb-2 text-right">
                                <!--a id="agregar_pregunta" class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarPregunta()"><i class="fa fa-user"></i> Agregar</a-->
                            </div>
                        </div>
                        <input type="hidden" id="selected_products_quiz_id">
                        <table id="tabla_products_steps" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>Paso</th>
                                <th>Descripción</th>
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
<div class="modal fade" id="modal_products_quiz" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Catálogo Quiz</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_products_quiz" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="products_quiz_id">
                    <div class="form-group">
                        <label for="products_quiz_description">Descripcion</label>
                        <input type="text" name="products_quiz_description" class="form-control" id="products_quiz_description"  placeholder="Descripcion">
                    </div>
                    <div class="form-group">
                        <label for="products_quiz_points">Puntaje</label>
                        <input type="number" name="products_quiz_points" class="form-control" id="products_quiz_points"  placeholder="Puntaje">
                    </div>
                    <div class="form-group">
                        <input type="file" id="products_quiz_image" accept="image/*" onchange="loadProductsQuizImage(event)" class="d-none">
                        <div class="w-100 d-flex justify-content-center">
                            <img id="products_quiz_image_preview" style="width: 150px" class="cursor-pointer" src="<?php echo base_url()?>/assets/img/Camara.png">
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
<div class="modal fade" id="modal_products_step" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Catálogo Preguntas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_products_step" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="products_step_id">
                    <div class="form-group">
                        <label for="products_step_num_step">Paso</label>
                        <input type="text" name="products_step_num_step" class="form-control" id="products_step_num_step"  placeholder="Paso" disabled>
                    </div>
                    <div class="form-group">
                        <label for="products_step_option_description">Descripción</label>
                        <input type="text" name="products_step_option_description" class="form-control" id="products_step_option_description"  placeholder="Descripción">
                    </div>
                    <div class="form-group">
                        <input type="file" id="products_step_image" accept="image/*" onchange="loadProductsStepImage(event)" class="d-none">
                        <div class="w-100 d-flex justify-content-center">
                            <img id="products_step_image_preview" style="width: 150px" class="cursor-pointer" src="<?php echo base_url()?>/assets/img/Camara.png">
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

<script src="<?php echo base_url() ?>assets/js/juego_productos.js"></script>
