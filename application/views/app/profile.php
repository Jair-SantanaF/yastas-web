<div class="container-fluid">
    <div class="row justify-content-center">
        <form id="form_usuario" method="post" action="javascript:void(0)" novalidate="novalidate" class="col-xl-9 col-11">
                <div class="row justify-content-center">
                    <div class="col-xl-6 col-lg-6 col-md-9 mt-5">
                        <div class="row justify-content-center">
                            <div class="col-xl-9 col-sm-11 col-12">
                                <div class="row justify-content-center">
                                    <div class="portrait-crop rounded-circle profile_photo">
                                        <input type="file" id="imagen" accept="image/*" onchange="loadImagen(event)" style="display: none;">
                                        <img id="preview_imagen" class="cursor-pointer w-100" title="Imagen de perfil" alt="Imagen de perfil"  src="<?PHP echo $this->session->userdata('profile_photo') ?>" />
                                    </div>
                                </div>
                                <div class="row justify-content-center pt-4">
                                    <div class="col-12">
                                        <div id="full_name_label" class="row">
                                            <div class="col-12 text-center h4 rosa_nuup_dark">
                                                <span id="name_label"></span> <span id="last_name_label"></span>
                                            </div>
                                        </div>
                                        <div id="full_name" class="row d-none">
                                            <div class="col-6 px-2">
                                                <input type="text" name="name" class="form-control input-white" id="name"  placeholder="Nombre">
                                            </div>
                                            <div class="col-6 px-2">
                                                <input type="text" name="last_name" class="form-control input-white" id="last_name"  placeholder="Apellido">
                                            </div>
                                        </div>
                                        <div id="job_name_label" class="w-100 text-center rosa_nuup_dark">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-9 rosa_nuup_dark_back shadow-lg d-flex mt-5" style="min-height: 400px">
                        <div class="w-100 align-self-center py-5">
                            <div class="row justify-content-center">
                                <div class="col-7">
                                    <div id="phone_label" class="w-100 text-center text-white">
                                    </div>
                                    <input type="text" name="phone" class="form-control input-green text-center d-none" id="phone"  placeholder="Teléfono">
                                </div>
                                <div class="col-7 pt-3">
                                    <div id="email_label" class="w-100 text-center text-white">
                                    </div>
                                </div>
                                <div class="col-7 pt-3">
                                    <div id="bussiness_name_label" class="w-100 text-center text-white">
                                    </div>
                                </div>
                                <div class="col-6 pt-3">
                                    <div id="btn_editar" class="btn btn-light btn-block">Editar</div>
                                    <button id="btn_guardar" type="submit" class="btn btn-light btn-block d-none">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>
<script src="<?php echo base_url()?>assets/js/app/profile.js"></script>

