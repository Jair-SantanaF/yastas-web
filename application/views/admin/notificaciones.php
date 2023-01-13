<style>
    .contenedor_tabla {
        height: 500px;
        overflow-y: auto;
    }
</style>
<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Notificaciones</div>
    <!-- <form class="col-12" id="form_notificacion"> -->
    <iframe class="col-12" style="height: 500px;" src="https://appy.com.mx/nuup/assets/js/notificaciones_angular/"></iframe>
        <!-- <div class="row justify-content-center pb-5 pt-4">
            <div class="alert alert-success" role="alert">
                En el siguiente formulario podrás enviar a una notificación a toda tu empresa, dicha notificación les llegara a su celular.
            </div>

        </div> -->
        <!-- <div class="row justify-content-center">
            <div class="col-6 form-group">
                <label for="titulo">Titulo</label>
                <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Titulo de la notificación">
            </div>
            <div class="col-6 form-group">
                <label for="titulo">Sección:</label>
                <select class="form-control" id="service_id" name="service_id">
                    <option value="0">General</option>
                    <option value="3">Muro</option>
                    <option value="4">Biblioteca</option>
                    <option value="5">Juegos</option>
                    <option value="8">Retroalimentacion</option>
                    <option value="9">Ranking</option>
                    <option value="10">Agenda</option>
                    <option value="11">Cuestionarios</option>
                    <option value="12">Capacitación</option>
                    <option value="13">Podcast</option>
                </select>
            </div>
            
            <div class="col-4 form-group">
                <?php if ($this->session->userdata('rol_id') == 5 ) { ?>
                    <div class="form-group px-3">
                        <label for="select_region">Región</label>
                        <select name="select_region" class="form-control" id="select_region" multiple="multiple"></select>
                        <label style="cursor:pointer" onclick="selectAllRegiones()">Seleccionar Todas las Regiones</label>
                    </div>
                    
                <?php } ?>
            </div>
            
            <div class="col-4 form-group">
                <?php if ($this->session->userdata('rol_id') == 5 ) { ?>
                    <div class="form-group px-3">
                        <label for="select_asesor">Asesor</label>
                        <select class="form-control" id="select_asesor" multiple="multiple"></select>
                    </div>
                <?php } ?>
            </div>
            <div class="col-6 form-group">
                <label for="notificacion">Descripción</label>
                <textarea maxlength="280" type="text" style="height: 150px; resize: none;" class="form-control" id="notificacion" name="notificacion" placeholder="Descripción"></textarea>
                <small class="form-text text-muted">Maximo de caracteres 280.</small>
            </div>
        </div>
        
        <div class="row pt-3 pb-3 justify-content-center">
            <div class="col-4 text-center">
                <button class="btn btn-success btn-lg">Enviar notificación</button>
            </div>
        </div>
    </form>
    <div class="row col-12 justify-content-center">
        <h3>Notificaciones enviadas</h3>
        <button class="btn btn-info" onclick="descargar()">Descargar</button>
        <div class="col-12 contenedor_tabla">
            <table class="table">
                <tr>
                    <th>Notificacion</th>
                    <th>Sección</th>
                    <th style="width: 140px;">Fecha</th>
                    <th>Grupo</th>
                </tr>
                <tbody id="contenedor_notificaciones"></tbody>
            </table>
        </div>
    </div> -->

</div>
<!-- <script src="<?php echo base_url() ?>/assets/js/notificaciones.js"></script> -->