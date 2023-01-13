<div class="container-fluid h-100">
    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-7 col-md-8 col-sm-9 col-10"">
            <form id="contenedor_preguntas" class="row justify-content-center pt-5">
            </form>
        </div>
        <div class="col-12 pt-5 pb-5">
            <div class="row justify-content-center">
                <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-7">
                    <button id="btn_siguiente" class="btn btn-red btn-block" onclick="siguienteEvaluacion()">Siguiente</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var type = 'final_evaluation',
        quiz_id = <?php echo $quiz_id?>;
</script>
<script src="<?php echo base_url()?>assets/js/app/cuestionario.js"></script>

