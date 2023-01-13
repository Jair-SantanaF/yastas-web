<style>
    .draggable { width: 100px; height: 100px; padding: 0.5em; float: left; margin: 10px 10px 10px 0; }
    .droppable { width: 150px; height: 150px; padding: 0.5em; float: left; margin: 10px; }
</style>
<div class="container-fluid h-100">
    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-7 col-md-8 col-sm-9 col-10"">
            <form id="contenedor_quiz" class="row justify-content-center pt-5">
            </form>
        </div>
        <div class="col-12 pt-5 pb-5">
            <div class="row justify-content-center">
                <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-7">
                    <button id="btn_siguiente" class="btn btn-red btn-block" onclick="siguienteEvaluacion()">Siguiente</button>
                </div>
            </div>
        </div>
        <div class="col-12 pt-5 pb-5" id="algo">
        </div>
    </div>
</div>

<script>
    var quiz_id = 2;//< ?php echo $quiz_id?>;
</script>
<script src="<?php echo base_url()?>assets/js/app/products.js"></script>

