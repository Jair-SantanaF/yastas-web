<style>
    #autocomplete_list {
        background: #fff;
        box-shadow: 10px 10px 5px #666;
        height: auto;
    }

    #lista_autocomplete,
    #lista_autocomplete_edicion {
        list-style: none;
        padding: 0;
    }

    #lista_autocomplete li,
    #lista_autocomplete_edicion li {
        padding-left: 10px;
        height: 35px;
        border-bottom: solid 1px #ccc;
        cursor: pointer;
    }

    .requerido {
        border: red solid 2px;
    }

    .usuarios,
    .grupos {
        height: 400px;
        overflow-y: auto;
    }

    .eliminado {
        background: #b7070f!important;
        
        color: #fff;
    }
</style>

<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Chat</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-11 alert alert-success" role="alert">
                En esta sección podras crear, editar, eliminar temas y agregar usuarios a ellos para crear conversaciones entre los integrantes.
            </div>
            <div class="row col-xl-12 col-md-12 col-sm-12 col-12">
                <div class="row py-4">
                    <div class="col-12 pb-2 text-right">
                        <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="agregarTopic()"><i class="fa fa-plus"></i> Agregar</a>
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
                <table id="tabla_topics" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Número de usuarios</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenido_tabla_topics">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_mensaje" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nuevo Mensaje</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="col-md-12 row">
                        <div class="col-md-12">
                            <label for="mensaje">Mensaje</label>
                            <input type="text" name="mensaje" class="form-control" id="mensaje" placeholder="Mensaje">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="guardarMensaje()">Guardar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_new_topic" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nuevo Topic</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- <form id="form_products_quiz" method="post" action="javascript:void(0)" novalidate="novalidate"> -->
                <div>
                    <div class="col-md-12 row">
                        <div class="col-md-12">
                            <label for="nombre">Nombre del topic</label>
                            <input type="text" name="nombre" class="form-control" id="txt_nombre" onkeyup="escribiendoNombre()" placeholder="Nombre">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <br>
                        <div class="col-md-12" id="contenedor_capacitacion_obligatoria">
                            <label>Tipo de elemento</label>
                            <select id="capacitacion_obligatoria" class="form-control">
                                <option value="-1">Seleccionar ...</option>
                                <option value="0">Normal</option>
                                <option value="1">Capacitacion obligatoria</option>
                            </select>
                            <article style="font-size: 10px;">Los elementos que se guarden dentro de la opción capacitacion oblogatoria no aparecen en el listado de biblioteca en la app, solo se pueden usar dentro de las capacitaciones obligatorias</article>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-6" id="regiones">
                            <label>Seleccionar region:</label>
                            <select class="form-control" id="select_regiones_com" onchange="obtener_asesores()"></select>
                        </div>
                        <div class="col-6" id="asesores">
                            <label>Seleccionar asesor</label>
                            <select class="form-control" id="select_asesores"></select>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-12">
                            <br>
                            <h2>Agregar usuarios</h2>
                            <br>
                        </div>
                        <div class="col-md-6 row usuarios align-items-start">
                            <select id="grupos" class="form-control col-12" onchange="obtenerPorGrupo()"></select>
                            <table class="">
                                <tr>
                                    <td>Agregar Usuarios</td>
                                    <td><button class="btn btn-success" onclick="agregarTodos()">Agregar Todos</button></td>
                                </tr>
                                <tbody id="contenedor_usuarios"></tbody>
                            </table>
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
                        <div class="col-12">
                            <br>
                            <h2>Agregar grupos</h2>
                            <br>
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
                        <!-- 
                        <br>
                        <div class="col-md-8">
                            <label for="usuarios">Agregar usuarios al topic</label>
                            <input type="text" name="usuarios" class="form-control" id="txt_usuarios" placeholder="Agregar usuarios al topic" onkeyup="llenar_autocomplete_list()">
                            <div id="autocomplete_list">
                                <ul id="lista_autocomplete">

                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-success" style="margin-top: 38px" onclick="agregarUsuario()">Agregar</button>
                        </div>
                        <div class="col-md-12">
                            <br>
                            <table id="tabla_topics" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="contenido_tabla_usuarios">

                                </tbody>
                            </table>
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="guardarTopic()">Guardar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_detalles" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detalles</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- <form id="form_products_quiz" method="post" action="javascript:void(0)" novalidate="novalidate"> -->
                <div>
                    <div class="col-md-12 row">
                        <div class="col-md-12">
                            <h3 id="lbl_nombre_topic"></h3>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-12">
                            <br>
                            <br>
                            <label>Usuarios en el topic</label>
                            <br>
                            <table id="tabla_topics" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                    </tr>
                                </thead>
                                <tbody id="contenido_tabla_usuarios_detalles">

                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-12">
                            <br>
                            <br>
                            <label>Grupos en el topic</label>
                            <br>
                            <table id="tabla_topics" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                    </tr>
                                </thead>
                                <tbody id="contenido_tabla_grupos_detalles">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_edicion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Editar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- <form id="form_products_quiz" method="post" action="javascript:void(0)" novalidate="novalidate"> -->
                <div>
                    <div class="col-md-12 row">
                        <div class="col-md-12">
                            <label for="nombre">Nombre del topic</label>
                            <input type="text" name="nombre" class="form-control" id="txt_nombre_edicion" onkeyup="escribiendoNombre()" placeholder="Nombre">
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-6" id="regiones_edicion">
                            <label>Seleccionar region:</label>
                            <select class="form-control" id="select_regiones_com_edicion" onchange="obtener_asesores()"></select>
                        </div>
                        <div class="col-6" id="asesores_edicion">
                            <label>Seleccionar asesor</label>
                            <select class="form-control" id="select_asesores_edicion"></select>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-12 row">
                            <div class="col-12">
                                <br>
                                <h2>Agregar usuarios</h2>
                                <br>
                            </div>
                            <div class="col-md-6 row usuarios align-items-start">
                                <select id="grupos" class="form-control col-12" onchange="obtenerPorGrupo()"></select>
                                <table class="">
                                    <tr>
                                        <td>Agregar Usuarios</td>
                                        <td><button class="btn btn-success" onclick="agregarTodos()">Agregar Todos</button></td>
                                    </tr>
                                    <tbody id="contenedor_usuarios_edicion"></tbody>
                                </table>
                            </div>
                            <div class="col-md-6 usuarios">
                                <table>
                                    <tr>
                                        <td>Nombre</td>
                                        <td></td>
                                    </tr>
                                    <tbody id="contenedor_usuarios_cuestionarios_edicion"></tbody>
                                </table>
                            </div>
                            <div class="col-12">
                                <br>
                                <h2>Agregar grupos</h2>
                                <br>
                            </div>
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
                        <!-- <br>
                        <div class="col-md-8">
                            <label for="usuarios">Agregar usuarios al topic</label>
                            <input type="text" name="usuarios" class="form-control" id="txt_usuarios_edicion" placeholder="Agregar usuarios al topic" onkeyup="llenar_autocomplete_list_edicion()">
                            <div id="autocomplete_list">
                                <ul id="lista_autocomplete_edicion">

                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-success" style="margin-top: 38px" onclick="agregarUsuarioEdicion()">Agregar</button>
                        </div>
                        <div class="col-md-12">
                            <br>
                            <table id="tabla_topics" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="contenido_tabla_usuarios_edicion">

                                </tbody>
                            </table>
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="editarTopicAjax()">Editar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script>
    var id_usuario_actual = <?php echo $usuario_actual ?>
</script>
<script src="<?php echo base_url() ?>assets/js/chat.js?v=<?php echo time(); ?>"></script>