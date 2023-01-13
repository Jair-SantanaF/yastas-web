<style>
    .contenedor_palabras {
        height: 200px;
        width: 100%;
        border: solid 1px #ccc;
        padding: 10px;
    }

    .btn_quitar {
        height: 20px;
        width: 20px;
        background-color: transparent;
        border: none;
        text-align: center;
        color: red;
    }

    .boton {
        position: relative;
        height: 30px;
        width: 30px;
        background-color: red;
        border-radius: 70%;
        color: white;
        border: none;
        top: -20px;
        right: 20px;
    }

    .enlace {
        color: blue;
        text-decoration: underline;
        cursor: pointer;
    }
</style>

<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Retos</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-12 row">
                <div class="col-3">
                    <button class="btn btn-primary col-12" onclick="mostrarRetos(1)">Retos</button>
                </div>
                <div class="col-3">
                    <button class="btn btn-primary col-12" onclick="mostrarRetos(2)">Retos para calificar</button>
                </div>
            </div>
            <div class="col-xl-11 col-md-11 col-sm-11 col-12" id="retos">
                <div class="row py-4">
                    <div class="col-12">
                        <button class="btn btn-success" onclick="agregar()">Agregar</button>
                    </div>
                </div>
                <table id="tabla_roulette_quiz" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Objetivo</th>
                            <th>Descripcion</th>
                            <th>Usuario</th>
                            <th>Imagenes</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenido_tabla_retos">

                    </tbody>
                </table>
            </div>
            <div class="col-11" id="retos_calificar">
                <br>
                <br>
                <table id="" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Objetivo</th>
                            <th>Descripcion</th>
                            <th>Retado</th>
                            <th>Reporte</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenido_calificar">

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="nuevo_reto" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar Tema</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <div class="col-12">
                                <label>Nombre del reto</label>
                                <input class="form-control" id="nombre" placeholder="Nombre del tema">
                            </div>
                            <div class="col-12">
                                <label>Objeto</label>
                                <input class="form-control" id="objetivo" placeholder="Objeto">
                            </div>
                            <div class="col-12">
                                <label>Descripción</label>
                                <input class="form-control" id="descripcion" placeholder="Descripción">
                            </div>
                            <div class="col-12 ocultos">
                                <label>Imagenes</label>
                                <input type="file" class="form-control" id="imagen">
                                <br>
                                <div class="col-12 row" id="contenedor_preview">

                                </div>
                            </div>
                            <div class="col-12 ocultos">
                                <div class="col-12">
                                    <div class="col-12">
                                        <div class="col-12">
                                            <h4>Agregar usuarios</h4>
                                            <label>Filtrar por grupos</label>
                                            <select id="grupos" class="form-control" style="width: auto;display:inline-block;margin-bottom:15px" onchange="obtenerPorGrupo()"></select>
                                            <br>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class=" col-12 contenedor-tabla" style="height: 300px; overflow-y: auto;">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th></th>
                                                </tr>
                                                <tbody id="contenedor_usuarios"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-right">
                        <div class="col-12">
                            <div class="col-12">
                                <button class="btn btn-primary" onclick="guardar()">Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_imagenes" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Imagenes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="imagenes" class="col-12 row"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_calificar" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Calificar Reto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-12">
                        <div class="col-12 row">
                            <div class="col-6">
                                <div class="col-12">
                                    <label>Desempeño</label>
                                    <input type="number" class="form-control" id="desempeno_1">
                                </div>
                                <div class="col-12">
                                    <label>Desempeño</label>
                                    <input type="number" class="form-control" id="desempeno_2">
                                </div>
                                <div class="col-12">
                                    <label>Desempeño</label>
                                    <input type="number" class="form-control" id="desempeno_3">
                                </div>
                                <div class="col-12">
                                    <label>Desempeño</label>
                                    <input type="number" class="form-control" id="desempeno_4">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="col-12">
                                    <label>Actitud</label>
                                    <input type="number" class="form-control" id="actitud_1">
                                </div>
                                <div class="col-12">
                                    <label>Actitud</label>
                                    <input type="number" class="form-control" id="actitud_2">
                                </div>
                                <div class="col-12">
                                    <label>Actitud</label>
                                    <input type="number" class="form-control" id="actitud_3">
                                </div>
                                <div class="col-12">
                                    <label>Actitud</label>
                                    <input type="number" class="form-control" id="actitud_4">
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" onclick="guardar_calificacion()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url() ?>/assets/js/retos.js"></script>
</div>