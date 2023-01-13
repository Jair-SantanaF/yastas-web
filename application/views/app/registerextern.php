    
    <div class="container-fluid h-100">

        <div class="row h-100">
            
            <div class="col col-md-8 col-lg-8 bg-img"></div>
            <div class="col col-12 col-sm-12 col-md-4 col-lg-4 bg-light">
                
                <div class="row">
                    <div class="col col-10 col-sm-10 offset-1">
                        <?php echo img($logo); ?>
                    </div>
                </div>
                
                <div class="forms">
                    
                    <div class="form-group row">
                        <div class="col col-10 col-sm-10 offset-1">
                            <h1>Registro Externo</h1>
                        </div>
                    </div>

                    <?php echo form_open('', array('id'=>'register_extern')); ?>
                    <div class="form-group row">
                        <div class="col col-10 col-sm-10 offset-1">
                            <?php echo form_input('name','', array('class'=>'form-control', 'placeholder'=>'Nombre')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col col-10 col-sm-10 offset-1">
                            <?php echo form_input('last_name','', array('class'=>'form-control', 'placeholder'=>'Apellido')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col col-10 col-sm-10 offset-1">
                            <?php echo form_input('email','', array('class'=>'form-control', 'placeholder'=>'Email', 'id'=>'email')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col col-10 col-sm-10 offset-1">
                            <?php echo form_input('repeat_email','', array('class'=>'form-control', 'placeholder'=>'Confirmar Email')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col col-10 col-sm-10 offset-1">
                            <?php echo form_password('password','', array('class'=>'form-control', 'placeholder'=>'Contraseña', 'id'=>'password')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col col-10 col-sm-10 offset-1">
                            <?php echo form_password('repeat_password','', array('class'=>'form-control', 'placeholder'=>'Confirmar Contraseña')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col col-10 col-sm-10 offset-1">
                            <?php echo form_input('phone','', array('class'=>'form-control', 'placeholder'=>'Telefono')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col col-10 col-sm-10 offset-1">
                            <?php echo form_dropdown('country_id', $pais, '', array('class'=>'form-control','id'=>'country_id')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col col-10 col-sm-10 offset-1">
                            <?php echo form_dropdown('state_id', $estados, '', array('class'=>'form-control','id'=>'state_id')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col col-10 col-sm-10 offset-1">
                            <?php echo form_dropdown('activity_id',$actividad,'', array('class'=>'form-control','id'=>'activity_id')); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col col-10 col-sm-10 offset-1">
                            <?php echo form_input('business_name','', array('class'=>'form-control', 'placeholder'=>'Nombre Empresa')); ?>
                        </div>
                    </div>

                        <div class="form-group row">
                            <div class="col col-10 col-sm-10 offset-1">
                                <?php echo form_dropdown('priority_crop_id', $cultivo, '-',  array('class'=>'form-control','id'=>'priority_crop_id')); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col col-10 col-sm-10 offset-1">
                                <button class="btn btn-success btn-block">Registrame</button>
                            </div>
                        </div>
                    
                    <?php echo form_close(); ?>
                    <div class="form-group row">
                        <div class="col col-10 col-sm-10 offset-1">
                            <?php echo anchor('app/register', 'Cancelar'); ?>
                        </div>
                    </div>
                    
                </div>
                
            </div>
            
        </div>
        
    </div>
    <script src="<?php echo base_url()?>assets/js/app/registerExtern.js"></script>