<script>
    var es_seccion_capturas = 1;
</script>

<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Chat</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-11 alert alert-success" role="alert">
                Aquí verás las capturas de pantalla que hacen los usuarios en la app.
            </div>
            <div class="row contenedor_detalle p-4">
                <div class="col-md-12 col-12">
                    <table id="seccion_capturas" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Sección</th>
                                <th>Nombre usuario</th>
                                <th>Imagen</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Latitud</th>
                                <th>Longitud</th>
                                <th>Ir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($secciones_captura == false) {
                                $secciones_captura = [];
                            }
                            foreach ($secciones_captura as $index => $value) {
                            ?>
                                <tr id="<?php echo $value['id'] ?>" class="fila">
                                    <td><?php echo $value['seccion'] ?></td>
                                    <td><?php echo $value['nombre_usuario'] ?></td>
                                    <td><a target="_blank" href="<?php echo $value['imagen'] ?>">Ver imagen</a></td>
                                    <td><?php echo $value['fecha'] ?></td>
                                    <td><?php echo $value['hora'] ?></td>
                                    <td><?php echo $value['latitud'] ?></td>
                                    <td><?php echo $value['longitud'] ?></td>
                                    <td><a href="https://www.google.com/maps/search/?api=1&query=<?php echo $value['latitud'] ?>,<?php echo $value['longitud'] ?>" target="_blanc">Ir al mapa</a></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="row hidden" style="display:none">
                    <div id="token"></div>
                    <div id="msg"></div>
                    <div id="notis"></div>
                    <div id="err"></div>
                </div>
                <script src="<?php echo base_url() ?>/assets/js/categorias.js"></script>
                <script src="<?php echo base_url() ?>/assets/js/app.js"></script>
            </div>
        </div>
    </div>
</div>