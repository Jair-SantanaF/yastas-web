
<div class="container-fluid h-100">
    <div class="row justify-content-center">
        <div class="col-xl-11 col-lg-11 col-md-11 col-sm-12 col-12">
            <h1 class="verde_basf_dark"> Capacitación </h1>
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-5 col-6"> <?php echo form_dropdown('category_id',$categoria,'', array('class'=>'form-control','id'=>'category_id')); ?> </div>
                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-5 col-6"> <?php echo form_dropdown('subcategory_id',$subcategoria,'', array('class'=>'form-control','id'=>'subcategory_id')); ?> </div>
            </div>
            <div id="detail_elearning" class="row justify-content-center">
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url()?>assets/js/app/elearning.js"></script>