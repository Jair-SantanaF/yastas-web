<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Capacitación</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-11 alert alert-success" role="alert">
                En este módulo podrás agregar elearnings y asignarle cuestionarios
            </div>
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <button class="btn btn-primary btn-xs editar mr-2 lead_0_8" data-toggle="modal" data-target="#modal_categorias" onclick="ObtenerTablaCategorias();"><i class="fa fa-list-ol"></i> Categorías</button>
                        <a id="agregar_curso" class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarCurso()"><i class="fa fa-plus"></i> Agregar Capacitación</a>
                    </div>
                </div>
                <table id="tabla_cursos" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Titulo</th>
                            <th>Descripción</th>
                            <th>Url</th>
                            <th>Max intentos</th>
                            <th>Min score</th>
                            <th>Cuestionario satisfacción</th>
                            <th>Cuestionario evaluación</th>
                            <th>Categoría</th>
                            <th>Subcategoría</th>
                            <th>Fecha limite</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_categorias" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detalle categorias</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                        <div class="row">
                            <div class="col-12 pt-2 pb-2 text-right">
                                <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarCategoria()"><i class="fa fa-plus"></i> Agregar</a>
                            </div>
                        </div>
                        <table id="tabla_categorias" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="contenido_tabla_categorias">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_categoria" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nueva categoría</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_category" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="category_id_edicion">
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="category">Nombre categoria</label>
                            <input type="text" class="form-control" id="category" aria-describedby="emailHelp" name="category">
                        </div>
                    </div>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_subcategorias" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detalle subcategorias</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                        <div class="row">
                            <div class="col-12 pt-2 pb-2 text-right">
                                <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarSubcategoria()"><i class="fa fa-plus"></i> Agregar</a>
                            </div>
                        </div>
                        <table id="tabla_subcategorias" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
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
<div class="modal fade" id="modal_subcategoria" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nueva subcategoría</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_subcategory" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="subcategory_id_edicion">
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="subcategory">Nombre subcategoría</label>
                            <input type="text" class="form-control" id="subcategory" aria-describedby="emailHelp" name="subcategory">
                        </div>
                    </div>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_curso" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nuevo capacitación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_curso" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="id_curso">
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="title">Titulo</label>
                            <input type="text" class="form-control" id="title" aria-describedby="emailHelp" id="title" name="title">
                        </div>
                        <div class="form-group col-12">
                            <label for="description">Descripción</label>
                            <textarea style="min-height: 150px" type="text" class="form-control text-left" id="description" name="description"></textarea>
                        </div>
                        <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">
                            <label for="max_try">Max intento</label>
                            <input type="number" min="0" class="form-control" id="max_try" name="max_try">
                        </div>
                        <div class="form-group col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">
                            <label for="min_score">Min score</label>
                            <input type="number" min="0" max="100" class="form-control" id="min_score" name="min_score">
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div>
                                <label for="trail_url">Ingrese un Url de elearning</label>
                                <input type="url" class="form-control" id="trail_url" name="trail_url">
                            </div>
                            <div>
                                <label for="zip">O suba un Paquete scorm (.zip)</label>
                                <input type="file" accept=".zip" class="form-control" id="zip" name="zip">
                            </div>
                        </div>
                        
                        <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                            <label for="quiz_satisfaction_id">Cuestionario de satisfacción</label>
                            <select id="quiz_satisfaction_id" class="form-control" name="quiz_satisfaction_id">
                            </select>
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                            <label for="quiz_final_evaluation_id">Cuestionario de evaluación</label>
                            <select id="quiz_final_evaluation_id" class="form-control" name="quiz_final_evaluation_id">
                            </select>
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                            <label for="category_id">Categoría</label>
                            <select id="category_id" class="form-control" name="category_id">
                            </select>
                        </div>
                        <div class="form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                            <label for="subcategory_id">Subcategoría</label>
                            <select id="subcategory_id" class="form-control" name="subcategory_id">
                            </select>
                        </div>
                        <div class="form group col-6">
                            <label for="fecha_limite">Fecha límite</label>
                            <input type="date" class="form-control" id="fecha_limite">
                        </div>
                    </div>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/elearning.js"></script>