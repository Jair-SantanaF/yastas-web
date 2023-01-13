<div class="container-fluid h-100">

    <div class="row h-100">

        <div class="col col-md-4 col-lg-4 bg-img"></div>
        <div class="col col-12 col-sm-12 col-md-4 col-lg-4 bg-light">

            <div class="row">
                <div class="col col-10 col-sm-10 offset-1 d-flex justify-content-center">
                    <?php echo img($logo); ?>
                </div>
            </div>

            <div class="forms">

                <?php echo form_open('', array('id' => 'register')); ?>

                <div class="form-group row">
                    <div class="col col-10 col-sm-10 offset-1">
                        <?php echo form_input('name', '', array('class' => 'form-control', 'placeholder' => 'Nombre')); ?>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col col-10 col-sm-10 offset-1">
                        <?php echo form_input('email', '', array('class' => 'form-control', 'placeholder' => 'Email', 'id' => 'email')); ?>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col col-10 col-sm-10 offset-1">
                        <?php echo form_password('password', '', array('class' => 'form-control', 'placeholder' => 'Contraseña', 'id' => 'password')); ?>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col col-10 col-sm-10 offset-1">
                        <?php echo form_password('repeat_password', '', array('class' => 'form-control', 'placeholder' => 'Confirmar Contraseña')); ?>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col col-10 col-sm-10 offset-1">
                        <?php echo form_input('business_name', '', array('class' => 'form-control', 'placeholder' => 'Compañia')); ?>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col col-10 col-sm-10 offset-1">
                        <?php echo form_input('position', '', array('class' => 'form-control', 'placeholder' => 'Posición')); ?>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col col-10 col-sm-10 offset-1">
                        <button class="btn button-wrap button btn-block">Registrame</button>
                    </div>
                </div>

                <?php echo form_close(); ?>

                <div class="form-group row">
                    <div class="col col-10 col-sm-10 offset-1">
                        <?php echo anchor('app', 'Cancelar'); ?>
                    </div>
                </div>
                <div class="col-md-12  row">
                    <div class="col-md-6 text-center">
                            <img src="http://kreativeco.com/nuup/assets/img/btn_App_store_sp.png" onclick="redirigir('apple')" href='https://apps.apple.com/us/app/nuup/id1517158945'>
                    </div>
                    <div class="col-md-6 text-center">
                            <img src='http://kreativeco.com/nuup/assets/img/btn_playstore_sp.png' onclick="redirigir('android')" href='https://play.google.com/store/apps/details?id=com.kreativeco.nuup&hl=en_US&gl=US'>
                    </div>

                </div>
            </div>

        </div>
        <div class="col col-md-4 col-lg-4 bg-img"></div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/app/register.js"></script>