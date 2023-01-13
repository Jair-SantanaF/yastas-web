<style>
    #loader_background {
        background: rgba(0, 0, 0, 0.4);
        height: 100vh;
        width: 150vw;
        position: fixed;
        z-index: 9999;
        top: 0;
        display: none;
    }

    #loader {
        background: #fff;
        height: 300px;
        border-radius: 10px;
        width: 500px;
        margin-left: calc(50% - 350px);
        top: 100px;
        z-index: 9999;
        position: fixed;
        display: none;
    }
</style>

<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Faqs</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <!-- <div class="alert alert-success" role="alert">
                Se pueden agregar, editar, eliminar faqs, se pueden segmentar por grupos
            </div> -->
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <a id="agregar_elemento" class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarElemento()"><i class="fa fa-plus"></i> Agregar Pregunta</a>
                    </div>
                </div>

                <table id="tabla_elementos" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Pregunta</th>
                            <th>Respuesta</th>
                            <th>Categoria</th>
                            <th>Imagen</th>
                            <th>Etiquetas</th>
                          <!--   <th>Fecha limite</th> -->
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenido_elementos">

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<!-- Modal agregar nuevo podcast -->
<div class="modal fade" id="modal_elemento" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nueva pregunta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- <form id="form_elemento" method="post" action="javascript:void(0)" novalidate="novalidate"> -->
                    <input type="hidden" id="id_elemento">

                    <div class="form-group">
                        <label for="pregunta">Pregunta</label>
                        <input type="text" class="form-control" id="pregunta" aria-describedby="emailHelp" name="titulo">
                    </div>

                    <div class="form-group">
                        <label for="respuesta">Respuesta</label>
                        <textarea style="min-height: 200px" type="text" class="form-control text-left" id="respuesta" name="descripcion"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="etiquetas">Etiquetas</label>
                        <textarea style="min-height: 200px" type="text" class="form-control text-left" id="etiquetas" name="etiquetas"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Preview</label>
                        <div class="input-group">
                            <input id="preview" type="file" accept="image/jpeg, image/jpg, image/png" onchange="loadPreview(event)">
                            <input id="nombre_preview" type="text" class="form-control " placeholder="" disabled>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" id="subir_preview">Subir</button>
                            </div>
                        </div>
                        <small id="preview" class="form-text text-muted">Recomendado: Resolución max: 500x500, Peso max:3mb</small>
                    </div>

                    <div class="row col-12">
                        <div class="col-6">
                            <div class=" col-12 contenedor-tabla">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Categoria</th>
                                        <!-- <th><button class="btn btn-success" onclick="agregarTodos()">Agregar todas</button></th> -->
                                    </tr>
                                    <tbody id="contenedor_categorias"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class=" col-12 contenedor-tabla">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Categoria</th>
                                        <th><button class="btn btn-success" onclick="eliminarTodos()">Eliminar todas</button></th>
                                    </tr>
                                    <tbody id="contenedor_categorias_faqs"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary" onclick="GuardarElemento()">Guardar</button>
                <!-- </form> -->
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="modal_categorias" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detalle categorías</h5>
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
                        <table id="tabla_categorias" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
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

<div class="modal fade" id="modal_subcategorys" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detalle subcategorías</h5>
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
                        <table id="tabla_subcategorys" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Categoria</th>
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

<div class="modal fade" id="modal_subcategory" tabindex="1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Preguntas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_subcategory" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="id_subcategory">
                    <input id="category_id" type="hidden" value="">
                    <div class="form-group">
                        <label for="nombre_subcategory">Nombre subcategoría</label>
                        <input type="text" name="nombre_subcategory" class="form-control" id="nombre_subcategory" placeholder="Nombre subcategoría">
                    </div>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modal_categoria" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Catálogo categorías</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_categoria" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="id_categoria">
                    <div class="form-group">
                        <label for="nombre_categoria">Nombre categoría</label>
                        <input type="text" name="nombre_categoria" class="form-control" id="nombre_categoria" placeholder="Nombre categoría">
                    </div>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>

        </div>
    </div>
</div>
<div id="loader_background">
    <div id="loader" class="text-center">
        <br>
        <br>
        <br>
        <h1>Guardando...</h1>
        <br>
        <img src="http://kreativeco.com/nuup/assets/img/circl.gif">
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/faqs.js"></script>