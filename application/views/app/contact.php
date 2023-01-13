<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-9 col-md-9 col-sm-10 col-11 shadow-lg verde_basf_dark_back mt-5">
            <div class="col align-items-center pt-5">
                <form id="form_message" method="post" action="javascript:void(0)" novalidate="novalidate" class="">
                    <div class="form-group row justify-content-center">
                        <label class="col-sm-10">Nombre</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-dark" id="name" name="name" placeholder="Nombre" disabled>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center"">
                        <label class="col-sm-10">Email</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control text-dark" id="email" name="email" placeholder="Email" disabled>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center"">
                        <div class="col-sm-10">
                            <textarea class="form-control text-dark" id="message" name="message" rows="10" placeholder="Mensaje..." style="background: white !important;"></textarea>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center">
                        <button id="btn_enviar" type="submit" class="btn button button-wrap">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url()?>assets/js/app/contact.js"></script>