<div class="container-fluid h-100">
    <div class="row h-100">
        <div class="col col-md-8 col-lg-8 bg-img"></div>
        <div class="col col-12 col-sm-12 col-md-4 col-lg-4 bg-light">
            <div class="row">
                <div class="col col-10 col-sm-10 offset-1 d-flex justify-content-center">
                    <?php echo img($logo); ?>
                </div>
            </div>
            <div class="forms mt-3">
                
                <?php echo form_open('', array('id'=>'login')); ?>
                    <div class="form-group row">
                        <div class="col col-8 col-sm-8 offset-2">
                            <?php echo form_input(array('name'=>'email','type'=>'email'),'', array('class'=>'form-control', 'placeholder'=>'Email', 'type'=>'email')); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col col-8 col-sm-8 offset-2" >
                            <?php echo form_password('password','', array('class'=>'form-control', 'placeholder'=>'Contraseña')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col col-8 col-sm-8 offset-2 d-flex flex-wrap justify-content-center">
                            <sub>
                                <?php echo anchor('app/recoverpassword','Olvide mi contraseña', array('title'=>'Olvide mi contraseña')) ?>
                            </sub>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col col-8 col-sm-8 offset-2 mt-5 mb-5">
                            <button class="btn button-wrap button btn-block">Iniciar sesión</button>
                        </div>
                    </div>
                <?php echo form_close(); ?>

                <div class="form-group row">
                    <div class="col col-12 col-sm-12 mt-5 d-flex flex-wrap justify-content-center">
                        <?php echo anchor('app/register','Registrate', array('title'=>'Registrate', 'class'=>'')) ?>&nbsp;y obten tu version gratuita con 5 usuarios
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url()?>assets/js/app/login.js"></script>

