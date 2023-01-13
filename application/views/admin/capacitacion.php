<style>
    #menu_items {
        border: solid #ccc 1px;
        border-radius: 10px;
    }

    #menu_items div,
    #menu_items_edicion div,
    #elementos_agregados_edicion,
    #elementos_agregados {
        height: 250px;
        overflow-y: auto;
    }

    /* #usuarios,
    #usuarios_edicion {
         display: none; 
    } */

    #usuarios {
        background-color: #ccc;
        margin-left: 20px;
        margin-right: 20px;
        width: calc(100% - 40px);
    }

    #elementos {
        background-color: #eee;
    }

    .pendiente {
        color: yellow;
    }

    .completo {
        color: green;
    }

    .grupos {
        height: auto;
        margin-bottom: 25px;
        overflow-y: auto;
    }

    .usuarios {
        max-height: 400px;
        overflow-y: auto;
    }

    .eliminado {
        background: #b7070f !important;
        /* height: 100px;
        width: 100%; */
        color: #fff;
    }
</style>

<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Capacitaciones</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-11 alert alert-success" role="alert">
                En esta sección podrás agregar, editar, eliminar capacitaciones con archivos de biblioteca y/o podcast, juegos, cuestionarios y/o chats de comunidad de aprendizaje, en cada capacitación puedes agregar a los usuarios que deban ver los recursos.
            </div>
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row">
                    <div class="col-4">
                        <label>Filtrar por habilitados/deshabilitados</label>
                        <select id="tipo_listado" class="form-control" onchange="cambiarTipoListado()">
                            <option value="-1">Todos</option>
                            <option value="1">Habilitados</option>
                            <option value="0">Deshabilitados</option>
                        </select>
                        <br>
                    </div>
                    <div class="col-8 pt-2 pb-2 text-right">
                        <a id="agregar_elemento" class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="agregarCapacitacion()"><i class="fa fa-plus"></i> Agregar Capacitación</a>
                    </div>
                </div>
                <table id="tabla_elementos" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Fecha limite</th>
                            <th>Fecha programada</th>
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
<!-- <form method="post"> -->
<div class="modal fade" id="modal_new_capacitacion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nueva Capacitación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- <form id="form_products_quiz" method="post" action="javascript:void(0)" novalidate="novalidate"> -->
                <div>
                    <div class="col-md-12 row">

                        <div class="col-md-12">
                            <label for="nombre">Nombre de la capacitación</label>
                            <input type="text" name="nombre" class="form-control" id="txt_nombre" placeholder="Nombre" onkeyup="escribiendoNombre()">
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_limite">Fecha limite</label>
                            <input type="date" name="fecha_limite" class="form-control" id="fecha_limite" placeholder="Fecha limite">
                        </div>
                        <div class="col-md-6 row">
                            <label for="fecha_programada">Programar fecha de incio</label>
                            <div class="col-8">
                                <input type="date" name="fecha_programada" class="form-control" id="fecha_programada" placeholder="Fecha Programada">
                            </div>
                            <div class="col-4">
                                <button class="btn btn-danger" onclick="eliminar_fecha()">Eliminar fecha</button>
                            </div>
                            <small>Fecha en la que estará disponible la capacitación, para los usuarios que se den de alta en la fecha o despues de la fecha</small>
                        </div>
                        <div class="col-md-12">
                            <label for="descripcion">Descripción</label>
                            <textarea name="descripcion" class="form-control" id="txt_descripcion" placeholder="Descripcion" onkeyup="escribiendoDescripcion()"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label>Imagen</label>
                            <div class="input-group">
                                <input id="imagen" type="file" accept="image/jpeg, image/jpg, image/png" onchange="loadImagen(event)" style="display: none;">
                                <input id="nombre_imagen" type="text" class="form-control " placeholder="" disabled>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="subir_imagen">Subir</button>
                                </div>
                            </div>
                            <small id="imagen" class="form-text text-muted">Recomendado: Resolución max: 500x500, Peso max:3mb</small>
                        </div>

                    </div>
                    <br>
                    <!-- <div class="col-md-12">
                        <button class="btn btn-success" onclick="mostrar('elementos')">Agregar elementos</button>
                        <button class="btn btn-success" onclick="mostrar('usuarios')">Agregar usuarios</button>
                    </div> -->

                    <div class="col-md-4">
                        <br>
                        <select class="form-control" id="tipo" onchange="generarMenuHTML()">
                            <option value="-1">Seleccionar...</option>
                            <option value="0">Capacitación Normal</option>
                            <option value="1">Capacitacion Obligatoria</option>
                            <option value="2">Capacitacion Presencial</option>
                        </select>
                        <br>
                    </div>

                    <div class="col-md-12 row extras" id="elementos">
                        <br>
                        <div class="col-md-12">
                            <h1>Agregar elementos a la capacitación</h1>
                        </div>
                        <br>
                        <div class="row col-md-12">
                            <div class="col-md-6">
                                <div class="col-md-12" id="menu_items">

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="col-md-12" id="elementos_agregados">

                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="col-md-12 row extras" id="">
                        <div class="col-md-12">
                            <h1>Agregar grupos al capacitación</h1>
                        </div>
                        <br>
                        <div class="col-md-12 row">
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
                    </div>
                    <br>
                    <div class="col-md-12 row extras" id="usuarios">
                        <br>
                        <div class="col-md-12">
                            <h1>Agregar usuarios a la capacitación</h1>
                        </div>
                        <br>
                        <div class="col-md-12 row">
                            <div class="col-md-6 row usuarios" style="overflow-y: auto;">
                                <input class="form-control col-8" id="buscador" placeholder="Buscar por id o nombre">
                                <button class="btn btn-success col-4" onclick="mostrarUsuariosFiltrados()" style="height: 47px!important;">Filtrar</button>
                                <table class="table">
                                    <tr>
                                        <th>Nombre</th>
                                        <th></th>
                                    </tr>
                                    <tbody id="contenedor_usuarios"></tbody>
                                </table>
                                <!-- <label for="grupos">Filtrar por grupos :</label>
                                <select name="grupos" id="grupos" onchange="obtenerPorGrupo()"></select>
                                <table class="table">
                                    <tr>
                                        <th>Nombre</th>
                                        <th><button class="btn btn-success" onclick="agregarTodos()">Agregar Todos</button></th>
                                    </tr>
                                    <tbody id="contenedor_usuarios"></tbody>
                                </table> -->
                            </div>
                            <div class="col-md-6" style="max-height: 400px;overflow-y: auto;">
                                <table class="table">
                                    <tr>
                                        <th>Nombre</th>
                                        <th><button class="btn btn-danger" onclick="eliminarTodos()">Eliminar todos</button></th>
                                    </tr>
                                    <tbody id="contenedor_usuarios_capacitacion"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>

                    <br>


                </div>
            </div>
            <div class="modal-footer">
                <!-- <button class="btn btn-primary" onclick="guardarCapacitacion()">Guardar</button> -->
                <input class="btn btn-primary" type="submit" value="Guardar" id="submit">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- </form> -->
<div class="modal fade" id="modal_detalles" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nueva Capacitación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 row">
                    <div class="col-md-7">
                        <h3 id="nombre_capacitacion"></h3>
                    </div>
                    <div class="col-md-7">
                        <p id="descripcion_capacitacion"></p>
                    </div>
                    <div class="col-md-5">
                        <img height="200" width="300" id="imagen_capacitacion">
                    </div>
                </div>
                <div class="col-md-12 row">
                    <div class="col-md-6" id="elementos_detalles">
                        <h4>Elementos</h4>
                        <table class="table">
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Orden</th>
                                <th></th>
                            </tr>
                            <tbody id="contenedor_elementos_detalle"></tbody>
                        </table>
                    </div>
                    <div class="col-md-6" id="usuarios_detalles">
                        <h4>Usuarios</h4>
                        <table class="table">
                            <tr>
                                <th>Nombre</th>
                                <th></th>
                            </tr>
                            <tbody id="contenedor_usuarios_detalle"></tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4>Grupos</h4>
                        <table class="table">
                            <tr>
                                <th>Grupo</th>
                                <th></th>
                            </tr>
                            <tbody id="contenedor_grupos_detalle"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_edit_capacitacion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Editar Capacitación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- <form id="form_products_quiz" method="post" action="javascript:void(0)" novalidate="novalidate"> -->
                <div>
                    <div class="col-md-12 row">

                        <div class="col-md-12">
                            <label for="nombre">Nombre de la capacitación</label>
                            <input type="text" name="nombre" class="form-control" id="txt_nombre_edicion" placeholder="Nombre" onkeyup="escribiendoNombre()">
                        </div>
                        <div class="col-6">
                            <label for="fecha_limite">Fecha limite</label>
                            <input type="date" name="fecha_limite" class="form-control" id="fecha_limite_edicion" placeholder="Fecha limite">
                        </div>
                        <div class="col-md-6 row">
                            <label for="fecha_programada_edicion">Programar fecha de incio</label>
                            <div class="col-8">
                                <input type="date" name="fecha_programada_edicion" class="form-control" id="fecha_programada_edicion" placeholder="Fecha Programada">
                            </div>
                            <div class="col-4">
                                <button class="btn btn-danger" onclick="eliminar_fecha()">Eliminar fecha</button>
                            </div>
                            <small>Fecha en la que estará disponible la capacitación, para los usuarios que se den de alta en la fecha o despues de la fecha</small>
                        </div>
                        <div class="col-md-12">
                            <label for="descripcion">Descripción</label>
                            <textarea name="descripcion" class="form-control" id="txt_descripcion_edicion" placeholder="Descripcion" onkeyup="escribiendoDescripcion()"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label>Imagen</label>
                            <div class="input-group">
                                <input id="imagen_edicion" type="file" accept="image/jpeg, image/jpg" onchange="loadImagenEdicion(event)" style="display: none;">
                                <input id="nombre_imagen_edicion" type="text" class="form-control " placeholder="" disabled>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="subir_imagen_edicion">Subir</button>
                                </div>
                            </div>
                            <small id="imagen" class="form-text text-muted">Recomendado: Resolución max: 500x500, Peso max:3mb</small>
                        </div>

                    </div>
                    <br>
                    <!-- <div class="col-md-12">
                        <button class="btn btn-success" onclick="mostrar('elementos_edicion')">Agregar elementos</button>
                        <button class="btn btn-success" onclick="mostrar('usuarios_edicion')">Agregar usuarios</button>
                    </div> -->
                    <br>
                    <div class="col-md-12 row extras" id="elementos_edicion">
                        <br>
                        <h1>Agregar elementos a la capacitación</h1>
                        <br>
                        <div class="row col-md-12">
                            <div class="col-md-6">
                                <div class="col-md-12" id="menu_items_edicion">

                                </div>
                            </div>
                            <div class="col-md-6">
                                <br>
                                <div class="col-md-12" id="elementos_agregados_edicion">

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 row extras" id="" style="margin-top:25px">
                        <br>
                        <div class="col-md-12">
                            <h1>Agregar grupos al capacitación</h1>
                        </div>
                        <br>
                        <div class="col-md-12 row">
                            <div class="col-md-6 grupos">
                                <table>
                                    <tr>
                                        <td>Agregar Grupos</td>
                                        <td><button class="btn btn-success" onclick="agregarTodosGrupos()">Agregar Todos</button></td>
                                    </tr>
                                    <tbody id="contenedor_grupos_edicion"></tbody>
                                </table>
                            </div>
                            <div class="col-md-6 grupos">
                                <table>
                                    <tr>
                                        <td>Nombre</td>
                                        <td></td>
                                    </tr>
                                    <tbody id="contenedor_grupos_cuestionarios_edicion"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 row extras" id="usuarios_edicion">

                        <h1 class="col-md-12">Agregar usuarios a la capacitación</h1>
                        <br>
                        <div class="col-md-12 row">

                            <div class="col-md-6 row usuarios">

                                <input class="form-control col-8" id="buscador_edicion" placeholder="Buscar por id o nombre">
                                <button class="btn btn-success col-4" onclick="mostrarUsuariosFiltrados()" style="height: 47px!important;">Filtrar</button>
                                <table class="table">
                                    <tr>
                                        <th>Nombre</th>
                                        <th></th>
                                    </tr>
                                    <tbody id="contenedor_usuarios_edicion"></tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <th>Nombre</th>
                                        <th></th>
                                    </tr>
                                    <tbody id="contenedor_usuarios_capacitacion_edicion"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="actualizarCapacitacion()">Guardar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="modal_detalles_usuario" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de capacitación de usuarios</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-12 row">
                    <div><b id="nombre_usuario_detalles"></b></div>
                    <table class="table">
                        <tr>
                            <th>Nombre Elemento</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                        <tbody id="contenedor_detalles_usuario">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script>
    var id_usuario_actual = <?php echo $usuario_actual; ?>;
    console.log(id_usuario_actual)
</script>

<script src="<?php echo base_url() ?>assets/js/capacitaciones.js?v=<?php echo time(); ?>"></script>

<!-- <div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Podcast</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <a id="agregar_elemento" class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarElemento()"><i class="fa fa-plus"></i> Agregar Podcast</a>
                    </div>
                </div>

                <table id="tabla_elementos" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Tipo</th>
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


<div class="modal fade" id="modal_elemento" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nueva capacitación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_elemento" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="id_elemento">

                    <div class="form-group">
                        <label for="titulo">Titulo</label>
                        <input type="text" class="form-control" id="titulo" aria-describedby="emailHelp" name="titulo">
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea style="min-height: 200px" type="text" class="form-control text-left" id="descripcion" name="descripcion"></textarea>
                    </div>

                    <div class="form-group" id="div_preview">
                        <label>Preview</label>
                        <div class="input-group">
                            <input id="preview" type="file" accept="image/jpeg, image/jpg, image/png" onchange="loadPreview(event)" style="display: none;">
                            <input id="nombre_preview" type="text" class="form-control " placeholder="" disabled>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" id="subir_preview">Subir</button>
                            </div>
                        </div>
                        <small id="preview" class="form-text text-muted">Recomendado: Resolución max:  500x500, Peso max:3mb</small>
                    </div>


                    <div class="form-group" id="div_audio">
                        <label>Audio</label>
                        <div class="input-group">
                            <input id="audio" type="file" accept="audio/mpeg3" onchange="loadAudio(event)" style="display: none;">
                            <input id="nombre_audio" type="text" class="form-control " placeholder="" disabled>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" id="subir_audio">Subir</button>
                            </div>
                        </div>
                        <small id="audio" class="form-text text-muted">Recomendado: mp3</small>
                    </div>

                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>

        </div>
    </div>
</div>

 -->