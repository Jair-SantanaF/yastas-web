<style>
    th.rotate {
        height: 170px;
        white-space: nowrap;
    }

    th.rotate>div {
        transform: translate(7px, 78px) rotate(270deg);
        width: 12px;
    }

    th.rotate>div>span {
        padding: 5px 0px;
        width: 170px !important;
        font-size: 13px;
        font-weight: normal;
    }

    .separador {
        border-top: solid #ccc 1px;
        height: 20px !important;
        width: 100%;
        margin-top: 20px;
    }

    .separador1 {
        height: 20px;
    }

    .fondo_negro {
        background: #000;
        color: #fff;
        border: solid #fff 1px;
    }

    label,
    p {
        margin-bottom: 0px;
        font-size: 14px;
    }

    .contenedor {
        border: solid 1px #ccc;
        padding-bottom: 10px;
    }

    .form-control {
        height: 30px !important;
        font-size: 14px !important;
    }

    table tr td {
        font-size: 14px;
        padding: 5px !important;
    }

    #tbl_ranking tr:nth-child(2n) {
        background: #000;
        color: #fff;
    }

    #tbl_ranking tr:nth-child(2n +1) {
        background: #eee;
        color: #000;
    }

    #retroalimentacion {
        background: red;
        color: #fff;
        font-size: 60px;
        border-radius: 70%;
        height: 100px;
        width: 100px;
        margin-left: calc(50% - 50px);
    }

    .reproducciones,
    .comentarios,
    .calificacion {
        height: 90px;
        width: 90px;
        border-radius: 70%;
        position: absolute;
        padding-top: 30px;
    }

    .reproducciones {
        background: #999;
        color: #fff;
        left: 0px;
        float: left;
    }

    .comentarios {
        background: #eee;
        right: 10px;
        float: right;
    }

    .calificacion {
        background: #000;
        color: #fff;
        top: 110px;
        left: 55px;
    }

    td img {
        height: 20px;
        width: 20px;
    }

    i {
        border: solid 1px #ccc;
        height: 16px;
        width: 16px;
    }

    .visible {
        display: flex !important;
    }

    .detalles {
        display: none;
    }

    .modal-body div {

        justify-content: center !important;
    }

    .btn-dark {
        border: none !important;
        background: none !important;
        font-size: 14px !important;
        text-decoration: underline !important;
        color: #000;
    }

    .border {
        border: solid #000 2px !important;
        padding: 20px;
    }
</style>
<style>
    #loader_background {
        background: rgba(0, 0, 0, 0.4);
        height: 100vh;
        width: 150vw;
        position: fixed;
        z-index: 9999;
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

    .h0 {
        font-size: 70px;
        font-weight: bolder;
    }

    .g_a_report {
        border: dashed 1px #000;
        margin-bottom: 10px;
        padding: 20px;
    }

    .android {
        color: green;
    }

    .ios {
        color: yellow;
    }

    .web {
        color: red;
    }

    .info {
        float: right;
        border-radius: 70%;
        border: 1px solid #ccc;
        height: 20px;
        width: 20px;
        text-align: center;
    }

    .mytooltip {
        position: relative;
        display: inline-block;
        border-bottom: 1px dotted black;
        /* If you want dots under the hoverable text */
    }

    /* Tooltip text */
    .mytooltip .mytooltiptext {
        visibility: hidden;
        width: 120px;
        background-color: #555;
        color: #fff;
        text-align: center;
        padding: 5px 0;
        border-radius: 6px;

        /* Position the tooltip text */
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -60px;

        /* Fade in tooltip */
        opacity: 0;
        transition: opacity 0.3s;
    }

    /* Tooltip arrow */
    .mytooltip .mytooltiptext::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #555 transparent transparent transparent;
    }

    /* Show the tooltip text when you mouse over the tooltip container */
    .mytooltip:hover .mytooltiptext {
        visibility: visible;
        opacity: 1;
    }

    #sesiones_activas,
    #sesiones_promedio,
    #duracion_sesion {
        color: blue;
    }
</style>

<link rel="stylesheet" href="<?php echo base_url() ?>assets/css/dashboard.css">


<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Dashboard</div>
    <div class="col-md-12 col-12" >
        
        <div class="row g-0 justify-content-center">
            
            <div class="row g-0 col-12 mt-3 justify-content-center">

                <div class="row col-12 g-0 p-3 container_style">
                    <div class="col-4 contenedor">
                        <h4 class="fondo_negro text-center">Filtros</h4>
                        <div class="col-12">
                            <label>Fecha de incio</label>
                            <input id="fecha_inicio" type="date" class="form-control">
                            <label>Fecha fin</label>
                            <input id="fecha_fin" type="date" class="form-control">
                            <div class="separador"></div>
                            <label>Buscar a un usuario por nombre</label>
                            <input id="nombre_usuario" type="text" class="form-control">
                            <div class="separador"></div>
                            <label>Buscar por grupo (biblioteca)</label>
                            <select id="grupos" class="form-control py-0 ps-2" style="cursor: pointer;"></select>

                            <button class="col-12 mt-3 btn btn-primary" onclick="filtrar()">Filtrar</button>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="col-12">
                            <p class="text-center mt-1"><strong>Ranking de usuarios</strong></p>
                            <a onclick="descargarReporteRanking()" href="#">Descargar Reporte</a>
                            <br>
                            <table class="table mt-1" id="tbl_ranking"></table>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="col-12 text-center">
                            <p class="text-center"><strong>Retroalimentación</strong>
                                <a class="" target="_blank" onclick="abrirModal(1)" style="cursor: pointer;">Detalles</a></p>

                            <div id="retroalimentacion" class="text-center mt-3"></div>
                        </div>
                    </div>
                </div>

                <div class="row g-0 col-12 mt-3 container_style">
                    <div class="col-6 mt-3">
                        <div class="col-md-12 text-center" style="padding: 0px">
                            <div class="col-md-12">
                                <p><strong>Ambiente laboral</strong></p>
                                <p class="mt-4"><strong>Promedio de respuestas (mes)</strong></p>
                                <div class="col-md-12">
                                    <img id="carita_mes">
                                </div>
                                <div class="col-md-12 row border">
                                    <p class="col-12 d-flex justify-content-start" id="num_respuestas_mes"></p>
                                    <p class="col-md-6" id="fecha_mes"></p>
                                </div>

                            </div>
                            <div class="col-md-12 mt-3">
                                <p><strong>Promedio de respuestas (semana)</strong></p>
                                <div class="col-md-12">
                                    <img id="carita_semana">
                                </div>
                                <div class="col-md-12 row border">
                                    <p class="col-12 d-flex justify-content-start" id="num_respuestas_semana"></p>
                                    <p class="col-md-4" id="numero_semana"></p>
                                    <p class="col-md-4" id="fecha_semana"></p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-6 my-3">
                        <div class="col-12 text-center" style="padding: 0px;">
                            <div class="col-md-12">
                                <p><strong>Uso de podcast</strong>
                                    <a onclick="descargarReportePodcast()" href="#">Descargar Reporte</a>
                                </p>
                                <br>
                                <canvas id="podcast_chart" class="chartjs-render-monitor"></canvas>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row col-12 g-0 my-3 container_style">
                    <div class="col-12 mt-3">
                        <div class="row g-0">
                            <div class="col-6">
                                <p class="text-center"><strong>Uso de biblioteca</strong></p>
                                <div class="col-12 d-flex justify-content-center">
                                    <canvas id="library_chart" class="chartjs-render-monitor"></canvas>
                                </div>
                            </div>
        
                            <div class="col-6">
                                <p class="text-center">
                                    <strong>Interacciones con muro</strong>
                                    <a class="ms-3" target="_blank" onclick="descargarReporteMuro()">Descargar reporte</a>
                                </p>
                                <div class="col-12 d-flex justify-content-center">
                                    <canvas id="wall_chart" class="chartjs-render-monitor"></canvas>
                                </div>
                            </div>
                        </div>    
                    </div>
                </div>

                <div class="row col-12 g-0 mb-3 container_style">
                    <div class="col-6 mt-3">
                        <div class="col-md-12">
                            <p class="text-center"><strong>Participación en cuestionarios</strong></p>
                            <div class="col-12 d-flex justify-content-center">
                                <canvas id="cuestionarios_chart" class="chartjs-render-monitor"></canvas>
                            </div>
                        </div>
                        <div class="co-md-12">
                            <p class="text-center">
                                <strong>Juegos</strong>
                                <a class="" onclick="descargarReporteJuegos()" style="cursor: pointer;">Descargar Reporte</a>
                            </p>
                            <div class="col-12 d-flex justify-content-center">
                                <canvas id="juegos_chart" class="chartjs-render-monitor"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mt-3">
                        <!-- <p class="text-center"><strong>Participación en comunidad de aprendizaje</strong><button class="btn btn-dark" onclick="descargarReporteComunidad()">Descargar Reporte</button></p>
                        <br>
                        <div class="col-md-12" style="display: flex; justify-content: center;">
                            <table id="tabla_chat_general"></table>
                        </div>
                        <br> -->

                        <p class="text-center">
                            <strong>Participación en comunidad de aprendizaje</strong>
                            <a class="" onclick="descargarReporteComunidad()" style="cursor: pointer;">Descargar Reporte</a>
                        </p>
                        <div class="col-12 d-flex justify-content-center">
                            <canvas id="aprendizaje_chart" class="chartjs-render-monitor"></canvas>
                        </div>

                    </div>
                </div>
            

                
                <div class="row col-12 g-0 mb-3 container_style">
                    <div class="col-12">
                        <div class="d-flex flex-column justify-content-center">
                            <h1 class="col-12 mt-3">Reporte de usuarios y sesiones</h1>

                            <div class="d-flex justify-content-center mt-3">
                                <div class="row g-0 col-12">
                                    <div class="col-4">
                                        <div class="g_a_report" style="height: 150px;">
                                            <h6>Usuarios totales <span class="info mytooltip">?<span class="mytooltiptext">Total de usuarios registrados y vigentes</span></span></h6>
                                            <h2 class="text-center h0" id="total_usuarios"></h2>
                                        </div>
                                        <div class="g_a_report" style="height: 350px;">
                                            <h6>Sesiones activas <span class="info mytooltip">?<span class="mytooltiptext">Sesiones activas actualmente, usuarios que no han cerrado sesión.</span></span></h6>
                                            <h2 id="sesiones_activas"></h2>
                                            <table class="table">
                                                <tr class="android">
                                                    <td>Android</td>
                                                    <td id="sesiones_android"></td>
                                                </tr>
                                                <tr class="ios">
                                                    <td>IOS</td>
                                                    <td id="sesiones_ios"></td>
                                                </tr>
                                                <tr class="web">
                                                    <td>Web</td>
                                                    <td id="sesiones_web"></td>
                                                </tr>
                                            </table>
                                            <canvas id="sesiones_activas_chart" class="chartjs-render-monitor"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="g_a_report" style="height: 150px;">
                                            <h6>Usuarios Nuevos<span class="info mytooltip">?<span class="mytooltiptext">Usuarios que iniciaron sesión pro primera vez.</span></span></h6>
                                            <h2 class="text-center h0" id="usuarios_nuevos"></h2>
                                        </div>
                                        <div class="g_a_report" style="height: 350px;">
                                            <h6>Sesiones por usuario (%)<span class="info mytooltip">?<span class="mytooltiptext">Número de sesiones que ha tenido un usuario dentro del periodo.</span></span></h6>
                                            <h2 id="sesiones_promedio"></h2>
                                            <table class="table">
                                                <tr class="android">
                                                    <td>Android</td>
                                                    <td id="promedio_android"></td>
                                                </tr>
                                                <tr class="ios">
                                                    <td>IOS</td>
                                                    <td id="promedio_ios"></td>
                                                </tr>
                                                <tr class="web">
                                                    <td>Web</td>
                                                    <td id="promedio_web"></td>
                                                </tr>
                                            </table>
                                            <canvas id="sesiones_promedio_chart" class="chartjs-render-monitor"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="g_a_report" style="height: 150px;">
                                            <h6>Visitantes de regreso<span class="info mytooltip">?<span class="mytooltiptext">Usuarios que han iniciado sesion antes del periodo actual y que han iniciado sesion dentro de este periodo</span></span></h6>
                                            <h2 class="text-center h0" id="usuarios_regreso"></h2>
                                        </div>
                                        <div class="g_a_report" style="height: 350px;">
                                            <h6>Duracion de la sesión (%)<span class="info mytooltip">?<span class="mytooltiptext">Promedio de duración de las sesiones de los usuarioss</span></span></h6>
                                            <h2 id="duracion_sesion"></h2>
                                            <table class="table">
                                                <tr class="android">
                                                    <td>Android</td>
                                                    <td id="duracion_android"></td>
                                                </tr>
                                                <tr class="ios">
                                                    <td>IOS</td>
                                                    <td id="duracion_ios"></td>
                                                </tr>
                                                <tr class="web">
                                                    <td>Web</td>
                                                    <td id="duracion_web"></td>
                                                </tr>
                                            </table>
                                            <canvas id="duracion_sesion_chart" class="chartjs-render-monitor"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            
                        
                        </div>
                       
                    </div>
                </div>


                <div class="modal" id="modal_detalles" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Información detallada de <strong id="modal_title"></strong></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="col-md-12 detalles" id="bibioteca_container">
                                    <br>
                                    <table id="tbl_biblioteca">

                                    </table>
                                </div>
                                <div class="col-md-12 detalles" id="podcast_container">
                                    <br>
                                    <table id="tbl_podcast"></table>
                                </div>
                                <div class="col-md-12 detalles" id="wall_container">
                                    <br>
                                    <table id="tbl_wall"></table>
                                </div>

                                <div class="col-md-12 detalles" id="cuestionarios_container">
                                    <br>
                                    <table id="tbl_cuestionarios"></table>
                                </div>

                                <div class="col-md-12 detalles" id="juegos_container">
                                    <br>
                                    <table id="tbl_juegos"></table>
                                </div>
                                <div class="col-md-12 detalles" id="comunidad_container">
                                    <br>
                                    <table id="tbl_comunidad"></table>
                                </div>
                                <div class="col-md-12 detalles" id="retroalimentacion_container">
                                    <br>
                                    <table id="tbl_feedback"></table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="loader_background">
    <div id="loader" class="text-center">
        <br>
        <br>
        <br>
        <h1>Obteniendo datos...</h1>
        <br>
        <img src="http://kreativeco.com/nuup/assets/img/circl.gif">
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/reporte_de_uso.js?v=<?php echo time(); ?>"></script>