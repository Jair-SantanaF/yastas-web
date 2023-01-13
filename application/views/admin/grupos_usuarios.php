<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Grupos</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="alert alert-success" role="alert">
                Podras crear, editar, eliminar grupos y agregarles integrantes, los grupos sirven para segmentar recursos
            </div>
            <div class="col-xl-11 col-md-111 col-sm-11 col-12">
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarGrupo()"><i class="fa fa-plus"></i> Agregar</a>
                    </div>
                </div>
                <table id="table_groups" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenido_tabla_events">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_miembros_detalle" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detalle usuarios</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="seccion_miembros" class="row justify-content-center d-none">
                    <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                        <div class="row">
                            <div class="col-12 pt-2 pb-2 text-right">
                                <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarMember()"><i class="fa fa-plus"></i> Agregar</a>
                            </div>
                        </div>
                        <input type="hidden" id="selected_group_id">
                        <table id="tabla_members" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Número empleado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_grupo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nuevo Grupo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_group" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="group_id">
                    <div class="form-group">
                        <label for="name">Nombre grupo</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Descripción">
                    </div>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="modal_member" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Miembros</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="">
                    <table id="tabla_users" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><input name="select_all" value="1" id="example-select-all" type="checkbox" /></th>
                                <th>Usuario</th>
                                <th>Número empleado</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <button class="btn btn-primary" onclick="AgregarUsuarios()">Guardar</button>
            </div>

        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/groups.js"></script>