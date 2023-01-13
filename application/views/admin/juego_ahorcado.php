<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Juego Ahorcado</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-11 alert alert-success" role="alert">
                Aquí podrás agregar, editar, eliminar frases para el juego Ahorcado.
            </div>
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row py-4">
                    <div class="col-12">

                    </div>
                </div>
                <table id="tabla_roulette_quiz" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Frase</th>
                            <th>Palabras oculats</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tr>
                        <td>
                            <input id="frase" class="form-control" placeholder="Frase">
                        </td>
                        <td>
                            <input id="ocultas" class="form-control" placeholder="Palabras ocultas">
                        </td>
                        <td>
                            <button class="btn btn-success" onclick="guardar()">Agregar</button>
                        </td>
                    </tr>
                    <tbody id="contenido_ahorcado">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Actualizar frase</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-12">
                    <br>
                    <input class="form-control" id="frase_edit">
                </div>
                <div class="col-12">
                    <br>
                    <input class="form-control" id="ocultas_edit">
                </div>
                <div>
                    <button class="btn btn-success" onclick="actualizar()">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
    <script src="<?php echo base_url() ?>/assets/js/ahorcado.js"></script>
</div>