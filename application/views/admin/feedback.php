<style>
    .contenedor_tabla {
        height: 500px;
        overflow-y: auto;
    }
</style>

<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Feedback</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-11 alert alert-success" role="alert">
                Listado de retroalimentacion de los usuarios
            </div>
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <button class="btn btn-info btn-xs editar mr-2 lead_0_8" data-toggle="modal" data-target="#modal_categorias_detalle">+ Categorías</button>
                    </div>
                </div>
                <div class="row">
                    <br>
                    <div class="col-3">
                        <label>Filtrar por nombre</label>
                        <input class="form-control" id="filtro_usuario">
                        <label>Filtrar por categoria</label>
                        <select class="form-control" id="select_categorias"></select>
                        <br>
                    </div>
                    <div class="col-3">
                        <label>Filtrar por grupo</label>
                        <select class="form-control" id="select_grupos"></select>
                    </div>
                    <div class="col-3">
                        <label>Fecha inicio</label>
                        <input class="form-control" id="fecha_inicio" type="date">
                        <label>Fecha fin</label>
                        <input class="form-control" id="fecha_fin" type="date">
                    </div>
                    <div class="col-3">
                        <br>
                        <button class="btn btn-primary col-12" onclick="filtrar()">Filtrar</button>
                    </div>
                    <br>
                </div>
                <div class="contenedor_tabla">
                    <table id="tabla_feedback" class="datatable table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>¿Qui&eacute;n dio?</th>
                                <th>¿Qui&eacute;n recibe?</th>
                                <th style="max-width: 200px;">Descripción</th>
                                <th>Archivo</th>
                                <th>Categoría</th>
                                <th>Likes</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="contenedor_feedback">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_categorias_detalle" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detalle Categorias</h5>
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
<script src="<?php echo base_url() ?>assets/js/feedback.js"></script>