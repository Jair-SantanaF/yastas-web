<div class="row contenedor_detalle">
    <div class="col-12 text-right h4 text-white my-auto titulo_pantallas">Eventos</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="alert alert-success" role="alert">
                En agenda podras programar eventos en una fecha y hora determinados
            </div>
            <div class="col-xl-11 col-lg-12 col-md-12 col-sm-12 col-12">
                <div id="calendar"></div>
            </div>
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row">
                    <div class="col-12 pt-2 pb-2 text-right">
                        <a class="btn btn-info btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="ObtenerTodosEventos()"><i class="fa fa-eye"></i> Ver todos</a>
                        <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarEvent()"><i class="fa fa-plus"></i> Agregar</a>
                    </div>
                </div>
                <input type="hidden" id="date_select" value="">
                <table id="tabla_events" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th>Nota</th>
                            <th>Usuario</th>
                            <th>Fecha</th>
                            <th>Inicio</th>
                            <th>Fin</th>
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
                <h5 class="modal-title" id="exampleModalLabel">Detalle miembros</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-10">Instrucciones:Agrega a usuarios de tu empresa al evento.</div>
                </div>
                <div id="seccion_miembros" class="row justify-content-center d-none">
                    <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                        <div class="row">
                            <div class="col-12 pt-2 pb-2 text-right">
                                <a class="btn btn-success btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarMember()"><i class="fa fa-plus"></i> Agregar</a>
                            </div>
                        </div>
                        <input type="hidden" id="selected_event_id">
                        <table id="tabla_members" class="datatable table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
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
<div class="modal fade" id="modal_event" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nuevo Evento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_event" method="post" action="javascript:void(0)" novalidate="novalidate">
                    <input type="hidden" id="event_id">
                    <div class="form-group">
                        <label for="event_description">Descripción</label>
                        <input type="text" name="event_description" class="form-control" id="event_description" placeholder="Descripción">
                    </div>
                    <div class="form-group">
                        <label for="event_note">Nota</label>
                        <input type="text" name="event_note" class="form-control" id="event_note" placeholder="Nota">
                    </div>
                    <div class="form-group">
                        <label for="event_date">Fecha</label>
                        <input type="date" name="event_date" class="form-control" id="event_date">
                    </div>
                    <div class="form-group">
                        <label for="event_time_start">Hora inicio</label>
                        <input type="time" name="event_time_start" class="form-control" id="event_time_start">
                    </div>
                    <div class="form-group">
                        <label for="event_time_end">Hora fin</label>
                        <input type="time" name="event_time_end" class="form-control" id="event_time_end">
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

<script src="<?php echo base_url() ?>assets/js/eventos.js"></script>