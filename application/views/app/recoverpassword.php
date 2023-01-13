    
    <div class="container-fluid h-100">

        <div class="row h-100">
            
            <div class="col col-md-8 col-lg-8 bg-img"></div>
            <div class="col col-12 col-sm-12 col-md-4 col-lg-4 bg-light">
                
                <div class="row">
                    <div class="col col-10 col-sm-10 offset-1 d-flex justify-content-center">
                        <?php echo img($logo); ?>
                    </div>
                </div>
                
                <div class="forms">
                    
                    <div class="form-group row">
                        <div class="col col-10 col-sm-10 offset-1">
                            <h1>Recuperar contraseña</h1>
                        </div>
                    </div>
                    
                    
                    <?php echo form_open('', array('id'=>'form_recover')); ?>
                        <div class="form-group row">
                            <div class="col col-10 col-sm-10 offset-1">
                                <?php echo form_input('email', '', array('class'=>'form-control', 'placeholder'=>'Email')); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col col-10 col-sm-10 offset-1">
                                <?php echo form_input('conf_email', '', array('class'=>'form-control', 'placeholder'=>'Confirmar Email')); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                        <div class="col col-10 col-sm-10 offset-1" >
                            <button class="btn btn-success btn-block">Recuperar Contraseña</button>
                        </div>
                    </div>

                    <?php echo form_close(); ?>

                    <div class="form-group row">
                        <div class="col col-10 col-sm-10 offset-1">
                            <?php echo anchor('app', 'Cancelar'); ?>
                        </div>
                    </div>
                    
                </div>
                
            </div>
        
        </div>
        
    </div>
    <script src="<?php echo base_url()?>assets/js/app/recoverPassword.js"></script>