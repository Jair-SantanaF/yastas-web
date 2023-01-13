<style>
    .the_wheel
    {
        background-image: url(./assets/img/wheel_back.png);
        background-position: center;
        background-repeat: no-repeat;
    }
</style>
<div class="container-fluid">
    <div class="row mt-5">
        <div id="content_questions" class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 mt-3 text-center my-auto">
            <?php
                echo img(array(
                'src'   => 'assets/img/1_CORTES_agro-05.png',
                'alt'   => '',
                'class' => '',
                'title' => ''
                ));
            ?>
            <br>
            <?php echo form_button('button', 'Comenzar', array('id'=>"spin_button",'class'=>"btn rojo_nuup_back text-white", 'style'=>'width:250px; font-size:20px;', 'onClick'=>"startSpin();")); ?>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 text-center">
            <div width="434" height="434" style="position: relative;">
                <img style="position: absolute;left: 44.5%; width: 77px;" src="<?php echo base_url('assets/img/arrow_roulette.png'); ?>">
                <canvas id="canvas" width="434" height="434">
                    <p style="color: white" align="center">Lo sentimos, su navegador no es compatible con lienzo. Por favor intente con otro.</p>
                </canvas>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url()?>assets/js/app/ruleta.js"></script>

