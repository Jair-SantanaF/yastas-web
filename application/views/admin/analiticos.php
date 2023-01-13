<style>
    .contenedor-grafica {
        /* background: rgba(250, 250, 250, 1); */
        margin-top: 15px;
        padding-top: 20px;
        padding-bottom: 20px;
        border: solid #ccc 1px;
    }

    .contenedor-grafica-body {
        height: 200px;
    }

    .contenedor-grafica:nth-child(1) {
        margin-top: 20px;
    }

    .header-grafica {
        padding: 0;
        border-bottom: 1px #ccc solid;
        margin-bottom: 20px;
    }

    .header-grafica label,
    select {
        font-size: 14px;
    }

    select {
        float: right;
        right: 0;
        background: none;
        border: none;
    }

</style>

<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Gráficas</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
        <button onclick="prueba()">Prueba</button>
            <div class="row col-xl-12 col-md-12 col-sm-12 col-12">
                <div class="col-md-2 col-sm-12">
                    <div class="col-md-12 contenedor-grafica">
                        <div class="col-md-12 header-grafica">
                            <label>Usuarios</label>
                        </div>
                        <div style="height: 150px;">
                            <canvas id="chart-area" class="chartjs-render-monitor"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 col-sm-12">
                    <div class="col-md-12 contenedor-grafica">
                        <div class="col-md-12 header-grafica">
                            <label>Post mas comentados</label>
                            <select onchange="actualizarGrafica(this.value,2)">
                                <option value="1">Hoy</option>
                                <option value="2">Esta semana</option>
                                <option value="3">Este mes</option>
                                <option value="4">Siempre</option>
                            </select>
                        </div>
                        <div class="contenedor-grafica-body">
                            <canvas id="post_comments" class="chartjs-render-monitor"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 col-sm-12">
                    <div class="col-md-12 contenedor-grafica">
                        <div class="col-md-12 header-grafica">
                            <label>Post mas gustados</label>
                        </div>
                        <div class="contenedor-grafica-body">
                            <canvas id="post_likes" class="chartjs-render-monitor"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="col-md-12 contenedor-grafica">
                        <div class="col-md-12 header-grafica">
                            <label>Podcast mas comentados</label>
                            <select onchange="actualizarGrafica(this.value,4)">
                                <option value="1">Hoy</option>
                                <option value="2">Esta semana</option>
                                <option value="3">Este mes</option>
                                <option value="4">Siempre</option>
                            </select>
                        </div>
                        <div class="contenedor-grafica-body">
                            <canvas id="podcast_comments" style="display: block; width: 533px; height: 266px;"
                                width="533" height="266" class="chartjs-render-monitor"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 col-sm-12">
                    <div class="col-md-12 contenedor-grafica">
                        <div class="col-md-12 header-grafica">
                            <label>Usuarios mas activos</label>
                            <select onchange="actualizarGrafica(this.value,5)">
                                <option value="1">Hoy</option>
                                <option value="2">Esta semana</option>
                                <option value="3">Este mes</option>
                                <option value="4">Siempre</option>
                            </select>
                        </div>
                        <div style="height: 250px;">
                            <canvas id="usuarios_activos" style="display: block; width: 533px; height: 266px;"
                                width="533" height="266" class="chartjs-render-monitor"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/analiticos.js"></script>
