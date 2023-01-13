<script>
    var ranking = <?php echo json_encode($detail) ?>
</script>
<style>
    .contenedor_tabla {
        height: 400px;
        max-height: 400px;
        overflow-y: auto;
    }
</style>
<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Ranking</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-11 alert alert-success" role="alert">
                Ranking muestra los usuarios que mas han usado la aplicación y con ello ganado puntos
            </div>
            <div class="col-10">
                <div class="row pt-4 justify-content-center">
                    <?php
                    foreach ($detail as $index => $value) {
                        $html = '';
                        if ($index == 0 || $index == 1) {
                            $html .= '
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                    <div class="row justify-content-center">
                                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-4">
                                            <img style="width: 120px; height: 120px;" class="imagen_redonda" src="' . $value['profile_photo'] . '">
                                        </div>
                                        <div class="col-xl-5 col-lg-5 col-md-5 col-sm-5 col-5">
                                            <h2 class="font-weight-light"><img style="display:inline-block; width: 30px;"  src="' . base_url('assets/img/ranking.png') . '"/>
                                            ' . ($index + 1) . '</h2>
                                            <div class="lead_0_9">
                                                <strong class="lead_0_9">' . $value['name'] . ' ' . $value['last_name'] . '</strong><br>
                                                <strong class="lead_0_9">Score: ' . $value['score'] . 'pts</strong><br>
                                                ' . $value['job_name'] . '
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ';
                        } else {
                            if ($index < 10)
                                $html .= '
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12 pt-4">
                                    <div class="row">
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 text-center">
                                            <img style="width: 100px; height: 100px;" class="imagen_redonda" src="' . $value['profile_photo'] . '">
                                        </div>
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 text-center">
                                            <h2 class="font-weight-light"><img style="display:inline-block; width: 30px;"  src="' . base_url('assets/img/ranking.png') . '"/>
                                            ' . ($index + 1) . '</h2>
                                            <div class="lead_0_9">
                                                <strong class="lead_0_9">' . $value['name'] . ' ' . $value['last_name'] . '</strong><br>
                                                <strong class="lead_0_9">Score: ' . $value['score'] . 'pts</strong><br>
                                                ' . $value['job_name'] . '
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ';
                        }
                        echo $html;
                    }

                    ?>
                    <br>
                    <br>
                    <div class="col-12 justify-content-center row">
                        <br>
                        <br>
                        <div class="col-6">
                            <input class="form-control" id="filtro">
                        </div>
                        <div class="col-2">
                            <button class="btn btn-primary" onclick="filtrar()">Filtrar</button>
                        </div>
                        <br>
                        <br>
                    </div>
                    <!-- <div class="col-4"></div> -->
                    <div class="col-12 justify-content-center row">
                        <div class="col-8 contenedor_tabla">
                            <table class="table col-12 table-striped table-dark">
                                <tr>
                                    <th>Puesto</th>
                                    <th>Nombre</th>
                                    <th>Area</th>
                                    <th>Puntos</th>
                                </tr>
                                <tbody id="contenedor_ranking">

                                </tbody>
                                <!-- <?php
                                        $html = '';
                                        foreach ($detail as $index => $value) {
                                            if ($index >= 10) {
                                                $html .= '<tr>';
                                                $html .= '<td>' . ($index + 1) . '</td>';
                                                $html .= '<td>' . $value["name"] . " " . $value["last_name"] . '</td>';
                                                $html .= '<td>' . $value["job_name"] . '</td>';
                                                $html .= '<td>' . $value["score"] . '</td>';
                                                $html .= '</tr>';
                                            }
                                        }
                                        echo $html;
                                        ?> -->
                            </table>
                        </div>
                    </div>
                    <br>
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/ranking.js"></script>