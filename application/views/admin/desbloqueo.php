<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Desbloqueo de usuarios</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-11 alert alert-success" role="alert">
                Desbloquea a los usuarios que fallaron mas de 3 veces al iniciar sesion.
            </div>
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="desbloquear_todos()">Desbloquear Todos</a>
                    </div>
                </div>
                <table id="tabla_invitados" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>ID</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="contenedor_bloqueados">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/desbloqueo.js?v=<?php echo time();?>"></script>