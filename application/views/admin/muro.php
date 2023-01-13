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
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Muro</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="alert alert-success col-11" role="alert">
                Muro permite hacer publicaciones que los integrantes pueden ver en su aplicación,se muestra un listado con el resumen de likes y comentarios obtenidos por la publicación
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-xl-10 col-md-10 col-sm-10 col-12">
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarWallPost()"><i class="fa fa-plus"></i> Agregar</a>
                    </div>
                </div>
                <table id="tabla_wall_posts" class="datatable table-striped table-bordered nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Descripción</th>
                            <th>Imagen</th>
                            <th>Likes</th>
                            <th>Comentarios</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenido_tabla_wall_posts">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_comentarios_detalle" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Comentarios</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="seccion_comentarios" class="row justify-content-center d-none">
                    <div class="col-xl-10 col-md-10 col-sm-10 col-12">
                        <div class="row">
                            <div class="col-12 pt-2 pb-2 text-right">
                                <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarWallComment()"><i class="fa fa-plus"></i> Agregar</a>
                            </div>
                        </div>
                        <input type="hidden" id="selected_wall_post_id">
                        <table id="tabla_wall_comments" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Comentario</th>
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
<div class="modal fade" id="modal_wall_post" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nueva Publicación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_wall_post" method="post" action="javascript:void(0)" enctype="multipart/form-data" novalidate="novalidate">
                    <input type="hidden" id="wall_post_id">
                    <div class="form-group">
                        <label for="wall_post_description">Escribe tu mensaje</label>
                        <input type="text" name="wall_post_description" class="form-control" id="wall_post_description" placeholder="Mensaje">
                    </div>
                    <!-- <div class="form-group">
                        <label for="redirect">Redirigir a muro en App</label>
                        <select name="redirect" class="form-control" id="redirect">
                            <option value="0">No</option>
                            <option value="1">Si</option>
                        </select>
                    </div> -->
                    <div class="form-group">

                        <small id="video" class="form-text text-muted">Recomendado para video: Resolución max: 1920x1080, Peso max:30mb, Solo archivos .mp4</small>
                        <input type="file" id="wall_post_image" accept="image/*, video/mp4" onchange="loadWallPostImage(event)" class="d-none">

                        <div class="row col-12 d-flex justify-content-center">
                            <div class="row col-12 justify-content-center">
                                <img id="wall_post_image_preview" style="width: 150px" class="cursor-pointer" src="<?php echo base_url() ?>/assets/img/Camara.png">
                                <div class="small row col-12 text-gray text-center">
                                    Cambiar imagen
                                </div>
                                <br>
                            </div>
                            <input class="row col-12" type="file" id="wall_post_preview" accept="image/*, video/mp4" style="display:none">
                            <small>Selecciona una imagen para vista previa</small>
                        </div>

                        <!-- <input type="file" id="wall_post_video" accept="video/mp4" onchange="loadWallPostImage(event)"> -->
                    </div>
                    <input type="text" id="tipo" style="display:none">
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="modal_wall_comment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nuevo comentario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_wall_comment" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="wall_comment_id">
                    <div class="form-group">
                        <label for="wall_comment">Comentario</label>
                        <input type="text" name="wall_comment" class="form-control" id="wall_comment" placeholder="Comentario">
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

<script src="<?php echo base_url() ?>assets/js/muro.js"></script>