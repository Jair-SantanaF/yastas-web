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
</style>

<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Juego Culebra</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-11 alert alert-success" role="alert">
                Aquí podrás agregar, editar, eliminar temas para el juego de culebra, cada tema debe tener palabras correctas para reforzar el tema y palabras incorrectas que son obstaculos en el juego y quitan puntos.
            </div>
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row py-4">
                    <div class="col-12">
                        <button class="btn btn-success" onclick="agregar()">Agregar</button>
                    </div>
                </div>
                <table id="tabla_roulette_quiz" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Palabras correctas</th>
                            <th>Palabras incorrectas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenido_tabla_juego_culebra">

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="nuevo_tema" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar Tema</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Nombre del tema</label>
                            <input class="form-control" id="nombre" placeholder="Nombre del tema">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label>Palabras buenas</label>
                                    <input class="form-control" id="txt_palabras_buenas" placeholder="Palabras buenas">
                                </div>
                                <div class="col-md-3">
                                    <p class="m-0 p-1">&nbsp;</p>
                                    <button class="btn btn-primary" onclick="agregarPalabra(1)">Agregar</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="contenedor_palabras" id="palabras_buenas"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label>Palabras malas</label>
                                    <input class="form-control" id="txt_palabras_malas" placeholder="Palabras malas">
                                </div>
                                <div class="col-md-3">
                                    <p class="m-0 p-1">&nbsp;</p>
                                    <button class="btn btn-primary" onclick="agregarPalabra(0)">Agregar</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="contenedor_palabras" id="palabras_malas"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button class="btn btn-success" onclick="guardar()">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url() ?>/assets/js/juego_culebra.js"></script>
</div>