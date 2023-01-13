
<div class="container-fluid h-100">
   <div class="row">
       <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 p-4">
           <div class="row justify-content-center">
               <div class="col-xl-11 col-lg-12 col-md-12 col-sm-12 col-12">
                   <div id="calendar"></div>
               </div>
           </div>
           <div class="row justify-content-center border-top- mt-4 pt-2">
               <div class="col-xl-11 col-lg-12 col-md-12 col-sm-12 col-12">
                   <div class="font-weight-bold lead mb-2">Eventos</div>
                   <table class="table">
                       <tbody id="tbody_events"></tbody>
                   </table>
               </div>
           </div>
       </div>
       <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 pt-4">
           <div id="view_event" class="display_none content_events">
               <input type="hidden" id="date_select">
               <div class="row justify-content-center">
                   <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12 rosa_nuup_dark text-center">
                       <span id="day_select" class="display-3 font-weight-bold" style="vertical-align: middle;"></span>
                       <span id="title_event" class="lead font-weight-bold" style="vertical-align: middle;"></span>
                   </div>
                   <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12 rosa_nuup_dark text-center mt-2">
                       <div class="row">
                           <div class="col-10">
                               <div id="detail_time" class="rosa_nuup_dark_back text-white text-center lead font-weight-bold pt-2 pb-2"></div>
                           </div>
                           <div class="col-2 my-auto text-left">
                              <button class="button cursor-pointer" onclick="NewEvent();"><i class="fas fa-plus"></i></button>
                              <!-- <img onclick="NewEvent();" class="cursor-pointer" src="<?php echo base_url('assets/img/add_button.png'); ?>"> -->
                           </div>
                       </div>
                   </div>
                   <div id="description_event" class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12 text-left mt-2 lead_0_9"></div>
                   <div id="members_select" class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12 text-center mt-2 lead_0_9">
                       <div class="lead font-weight-bold">Usuarios del evento</div>
                       <div id="members_register">
                       </div>
                   </div>
               </div>
           </div>
           <form id="new_event" class="display_none content_events">
               <div class="row justify-content-center">
                   <div class="col-3">
                       <img src="" id="photo_user" style="width: 100px; height: 100px;" class="d-flex align-self-start mr-3 rounded-circle border-shadow">
                   </div>
                   <div class="col-3">
                       <div class="media-body align-self-center">
                           <h4 id="name_user" class="mt-0"></h4>
                           <div id="job_name" class="mt-0 lead_0_9"></div>
                       </div>
                   </div>
               </div>
               <div class="row mt-2 justify-content-center">
                   <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-6">
                       <div class="form-group">
                           <label for="description">Titulo</label>
                           <input type="text" id="description" name="description" class="form-control" placeholder="Evento...">
                       </div>
                   </div>
                   <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-6">
                       <div class="form-group">
                           <label for="date">Fecha</label>
                           <input type="date" id="date" name="date" class="form-control">
                       </div>
                   </div>
               </div>
               <div class="row mt-2 justify-content-center">
                   <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-6">
                       <div class="form-group">
                           <label for="time_start">Hora inicio</label>
                            <input type="time" id="time_start" name="time_start" class="form-control">
                       </div>
                   </div>
                   <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-6">
                       <div class="form-group">
                           <label for="time_end">Hora termino</label>
                           <input type="time" id="time_end" name="time_end" class="form-control">
                       </div>
                   </div>
               </div>
               <div id="content_button_members" class="row mt-2 justify-content-center display_none">
                   <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 form-group">
                       Agregar integrantes <i onclick="MembersSelects();" class="fa fa-plus rosa_nuup_dark cursor-pointer"></i>
                   </div>
               </div>
               <div class="row mt-2 justify-content-center">
                   <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                       <div class="form-group">
                           <label for="note">Descripción</label>
                           <textarea class="form-control" style="resize: none; height: 150px;" id="note" name="note" placeholder="El evento tratara..."></textarea>
                       </div>
                   </div>
               </div>
               <div class="row">
                   <div class="col-12 text-center">
                       <button class="btn button-wrap button">Guardar</button>
                       <button class="btn button-wrap button" type="button" onclick="NewEvent()">Nuevo</button>
                   </div>
               </div>
           </form>
           <div id="members" class="display_none content_events">
               <div class="row justify-content-center">
                   <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 rosa_nuup_dark text-center">
                       <input class="form-control form-control-lg" type="text" placeholder="Buscar">
                       <div class="mt-4 border-top-">
                           <table class="table">
                               <tbody>
                               <tr style="border-bottom: 1px solid black;">
                                   <td class="text-left" style="border-top: none;">
                                       <img src="<?php echo base_url('assets/img/add_button.png'); ?>" style="width: 50px; height: 50px; vertical-align: middle;" class="rounded-circle border-shadow">
                                       <span class="lead_0_9 ml-2" style="vertical-align: middle;">Mario Martinez</span>
                                   </td>
                                   <td class="text-right" style="border-top: none; vertical-align: middle;">
                                       <img class="cursor-pointer" src="<?php echo base_url('assets/img/add_button_simple.png'); ?>">
                                   </td>
                               </tr>
                               <tr style="border-bottom: 1px solid black;">
                                   <td class="text-left" style="border-top: none;">
                                       <img src="<?php echo base_url('assets/img/add_button.png'); ?>" style="width: 50px; height: 50px; vertical-align: middle;" class="rounded-circle border-shadow">
                                       <span class="lead_0_9 ml-2" style="vertical-align: middle;">Mario Martinez</span>
                                   </td>
                                   <td class="text-right" style="border-top: none; vertical-align: middle;">
                                       <img class="cursor-pointer" src="<?php echo base_url('assets/img/add_button_simple.png'); ?>">
                                   </td>
                               </tr>
                               </tbody>
                           </table>
                           <button class="btn button-wrap button">Regresar</button>
                       </div>
                   </div>
               </div>
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
                                <a class="btn button-wrap button mr-2 lead_0_8" href="javascript:void(0)" onclick="AgregarMember()"><i class="fa fa-plus"></i> Agregar</a>
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
                <button class="btn button-wrap button" onclick="AgregarUsuarios()">Guardar</button>
            </div>

        </div>
    </div>
</div>
<script src="<?php echo base_url()?>assets/js/app/calendar.js" type="text/javascript"></script>
