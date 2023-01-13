<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Mensajes contacto</div>
    <div class="col-12">
        <div class="col-12">
            <div class="alert alert-success col-11" role="alert">
                En esta seccion se muestran los mensajes que los usuarios mandan a Contacto
            </div>
        </div>
        <div class="col-12">
            <table class="table">
                <tr>
                    <th>Nombre</th>
                    <th>Id</th>
                    <th>Mensaje</th>
                    <!-- <th>Email</th> -->
                    <th>Acciones</th>
                </tr>
                <tbody id="contenedor_mensajes"></tbody>
            </table>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/mensajes_contacto.js?v=<?php echo time(); ?>"></script>