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
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Polizas</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-11 alert alert-success" role="alert">
                En esta sección pordrás agregar, editar o eliminar polizas de usuarios
            </div>
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <a id="agregar_elemento" class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="agregarPoliza()"><i class="fa fa-plus"></i> Agregar Poliza</a>
                    </div>
                </div>
               <!--  <div class="row col-12" id="selectFiltro">
                    Filtro 
                    <select id="select_filtro">
                        <option value="todo">Ver Todo</option>
                        <option value="activo">Habilitado</option>
                        <option value="noactivo">Deshabilitado</option>
                    </select>
                </div> -->
                <div class="row"><br>
                </div>
                <table id="tabla_elementos" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Usuario</th>
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
<div class="modal fade" id="modal_preguntas_detalle" style="overflow-y: scroll;" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Polizas</h5>
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
                                    <th>Link</th>
                                    <th>Fecha</th>
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
<div class="modal fade" id="modal_elemento" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nueva poliza para usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-12 row">
                    <div class="col-6">

                        <div class="form-group">
                            <!-- <small id="preview" class="form-text text-muted">Recomendado: Resolución max: 500x500, Peso max:3mb</small> -->
                        </div>
                        <div class="form-group">
                            <label>PDF</label>
                            <div class="input-group">
                                <input id="documento" type="file" accept="application/pdf" onchange="loadDocumento(event)">
                                <input id="nombre_documento" type="text" class="form-control " placeholder="">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" id="subir_documento">Subir</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row col-12">
                        <div class="col-12">
                            <h4>Agregar usuarios</h4>
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
                    <button class="btn btn-primary" onclick="guardarPoliza()">Guardar</button>
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
<script src="<?php echo base_url() ?>assets/js/poliza.js?v=<?php echo time(); ?>"></script>