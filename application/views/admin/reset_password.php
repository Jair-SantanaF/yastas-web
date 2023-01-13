<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Reset Passwprd</div>
    <div class="col-12">
        <div class="col-12">
            <div class="alert alert-success col-11" role="alert">
                Resetea la contraseña de los usuarios usando la tabla siguiente, La contraseña por predeterminada es Ab12345@
            </div>
        </div>
        <div class="col-12 row">
            <label>Filtrar por nombre o Id</label>
            <div class="col-12 row">
                <div class="col-8">
                    <input class="form-control" id="filtro" placeholder="Filtrar por nombre o ID">
                </div>
                <div class="col-4">
                    <button class="btn btn-primary" onclick="filtrar()">Buscar</button>
                </div>
            </div>
            <br>
            <table class="table">
                <tr>
                    <th>Nombre</th>
                    <th>Id</th>
                    <!-- <th>Email</th> -->
                    <th>Acciones</th>
                </tr>
                <tbody id="contenedor_usuarios"></tbody>
            </table>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/reset_password.js?v=<?php echo time(); ?>"></script>