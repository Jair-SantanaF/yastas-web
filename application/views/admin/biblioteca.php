<style>
    #loader_background {
        background: rgba(0, 0, 0, 0.4);
        height: 100vh;
        width: 150vw;
        position: fixed;
        z-index: 9999999 !important;
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

    table tr td{
        padding: 0!important;
        
    }

    .eliminado {
        background: #b7070f;
        height: 100px;
        width: 100%;
        color: #fff;
    }
</style>

<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Biblioteca</div>
    <!-- <iframe class="col-12" src="http://localhost/nuup_respaldo/assets/js/admin_angular/"></iframe> -->
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-11 alert alert-success" role="alert">
                En esta sección pordrás agregar, editar o eliminar archivos(imagenes, videos, pdf, likns) para el equipo, los archivos pueden estar dentro de catgorias y subcategorias, y segmentados por grupos para que solo una parte del equipo pueda verlos
            </div>
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <button class="btn btn-primary btn-xs editar mr-2 lead_0_8" data-toggle="modal" data-target="#modal_categorias"><i class="fa fa-list-ol"></i> Categorías</button>
                        <a id="agregar_elemento" class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarElemento()"><i class="fa fa-plus"></i> Agregar Elemento</a>
                    </div>
                </div>
                <div class="row col-12" id="selectFiltro">
                    Filtro 
                    <select id="select_filtro">
                        <option value="todo">Ver Todo</option>
                        <option value="activo">Habilitado</option>
                        <option value="noactivo">Deshabilitado</option>
                    </select>
                </div>
                <div class="row"><br>
                </div>
                <table id="tabla_elementos" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Etiquetas</th>
                            <th>Subcategoría</th>
                            <th>Grupos</th>
                            <th>Documento</th>
                            <th>Fecha límite</th>
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
                <h5 class="modal-title" id="exampleModalLabel">Catálogo subcategorías</h5>
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
<div class="modal fade" id="modal_elemento" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nuevo elemento biblioteca</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- <form id="form_elemento" method="post" action="javascript:void(0)" novalidate="novalidate"> -->
                <div class="col-12 row">
                    <div class="col-6">
                        <input type="hidden" id="id_elemento">

                        <div class="form-group">
                            <label for="titulo">Titulo</label>
                            <input type="text" class="form-control" id="titulo" aria-describedby="emailHelp" name="titulo">
                        </div>
                        <div class="form-group">
                            <label for="texto">Texto</label>
                            <textarea style="min-height: 200px" type="text" class="form-control text-left" id="texto" name="texto"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="etiquetas">Etiquetas</label>
                            <textarea style="min-height: 200px" type="text" class="form-control text-left" id="etiquetas" name="etiquetas"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="id_categoria_elemento">Categoría</label>
                            <select id="id_categoria_elemento" class="form-control" name="id_categoria_elemento">
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_subcategory_elemento">Subcategoría</label>
                            <select id="id_subcategory_elemento" class="form-control" name="id_subcategory_elemento">
                            </select>
                        </div>
                    </div>
                    <div class="col-6">

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
                        <div class="form-group">
                            <label for="Tipo">Tipo de elemento</label>
                            <select id="tipo" class="form-control" name="tipo">
                                <option value="">Seleccionar</option>
                                <option value="video">Video</option>
                                <option value="documento">PDF</option>
                                <option value="imagen">Imagen</option>
                                <option value="link">Link</option>
                            </select>
                        </div>
                        <!--div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupFileAddon01">Upload</span>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="inputGroupFile01" aria-describedby="inputGroupFileAddon01">
                                <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                            </div>
                        </div>
                    </div-->
                        <div class="form-group d-none" id="div_tipo_video">
                            <label for="tipo_video">Tipo Video</label>
                            <select id="tipo_video" class="form-control" name="tipo_video">
                                <option value="">Seleccionar</option>
                                <option value="servidor">Subir</option>
                                <option value="youtube">Youtube</option>
                                <option value="vimeo">Vimeo</option>
                            </select>
                        </div>
                        <div class="form-group d-none" id="div_video">
                            <label>Video</label>
                            <div class="input-group">
                                <input id="video" type="file" accept="video/mp4" onchange="loadVideo(event)">
                                <input id="nombre_video" type="text" class="form-control " placeholder="" disabled>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="subir_video">Subir</button>
                                </div>
                            </div>
                            <small id="video" class="form-text text-muted">Recomendado: Resolución max: 1920x1080, Peso max:30mb, Solo archivos .mp4</small>
                        </div>
                        <div class="form-group d-none" id="div_documento">
                            <label>PDF</label>
                            <div class="input-group">
                                <input id="documento" type="file" accept="application/pdf" onchange="loadDocumento(event)">
                                <input id="nombre_documento" type="text" class="form-control " placeholder="" disabled>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="subir_documento">Subir</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group d-none" id="div_imagen">
                            <label>Imagen</label>
                            <div class="input-group">
                                <input id="imagen" type="file" accept="image/jpeg, image/jpg" onchange="loadImagen(event)">
                                <input id="nombre_imagen" type="text" class="form-control " placeholder="" disabled>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="subir_imagen">Subir</button>
                                </div>
                            </div>
                            <small id="imagen" class="form-text text-muted">Recomendado: Resolución max: 500x500, Peso max:3mb</small>
                        </div>
                        <div class="form-group d-none" id="div_video_id">
                            <label for="video_id">Video</label>
                            <input type="text" class="form-control" id="video_id" aria-describedby="emailHelp" name="video_id">
                        </div>
                        <div class="form-group d-none" id="div_link">
                            <label for="link">Link</label>
                            <input type="text" class="form-control" id="link" aria-describedby="emailHelp" name="link">
                        </div>
                        <div class="form-group">
                            <label for="quiz_library">Asignar preguntas</label>
                            <select id="quiz_library" class="form-control" name="quiz_library">
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fecha_limite">Fecha limite</label>
                            <input type="date" class="form-control" id="fecha_limite" name="fecha_limite">
                        </div>
                    </div>
                    <div class="col-6" id="regiones">
                        <label>Seleccionar region:</label>
                        <select class="form-control" id="select_regiones_library" onchange="obtener_asesores()"></select>
                    </div>
                    <div class="col-6" id="asesores">
                        <label>Seleccionar asesor</label>
                        <select class="form-control" id="select_asesores"></select>
                    </div>
                    <div class="col-6" id="contenedor_capacitacion_obligatoria">
                        <label>Tipo de elemento</label>
                        <select class="form-control" id="capacitacion_obligatoria">
                            <option value="-1">Seleccionar ...</option>
                            <option value="0">Normal</option>
                            <option value="1">Capacitación obligatoria</option>
                        </select>
                        <article style="font-size: 10px;">Los elementos que se guarden dentro de la opción capacitacion oblogatoria no aparecen en el listado de biblioteca en la app, solo se pueden usar dentro de las capacitaciones obligatorias</article>
                    </div>
                    <div class="row col-12">
                        <div class="col-12">
                            <div class="col-12">
                                <h4>Agregar usuarios</h4>
                                <!-- <label>Filtrar por grupos</label>
                                    <select id="grupos" class="form-control" style="width: auto;display:inline-block;margin-bottom:15px" onchange="obtenerPorGrupo()"></select>
                                    <br> -->
                            </div>
                        </div>
                        <div class="col-6 row">
                            <input class="form-control col-8" id="buscador" placeholder="Buscar por id o nombre">
                            <button class="btn btn-success col-4" onclick="mostrarUsuariosFiltrados()" style="height: 47px!important;">Filtrar</button>
                            <table class="table">
                                <tr>
                                    <th>Nombre</th>
                                    <th></th>
                                </tr>
                                <tbody id="contenedor_usuarios"></tbody>
                            </table>
                            <!-- <div class=" col-12 contenedor-tabla">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Nombre</th>
                                            <th><button class="btn btn-success" onclick="agregarTodos()">Agregar todos</button></th>
                                        </tr>
                                        <tbody id="contenedor_usuarios"></tbody>
                                    </table>
                                </div> -->
                        </div>
                        <div class="col-6">
                            <div class=" col-12 contenedor-tabla">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Nombre</th>
                                        <th><button class="btn btn-success" onclick="eliminarTodos()">Eliminar todos</button></th>
                                    </tr>
                                    <tbody id="contenedor_usuarios_library"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" onclick="GuardarElemento()">Guardar</button>
                </div>
                <!-- </form> -->
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
<script src="<?php echo base_url() ?>assets/js/biblioteca.js?v=<?php echo time(); ?>"></script>