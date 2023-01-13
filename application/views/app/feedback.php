<div class="container-fluid">
    <div class="row d-flex justify-content-center">
        <div class="col-11 mt-4">
            <div class="row">
                <div class="col col-12 col-sm-10 col-md-10 col-lg-10 col-xl-10 offset-md-1 offset-lg-1 offset-xl-1">
                    <div class="w-100 d-flex justify-content-center">
                        <div id="todos" class="cursor-pointer p-2 mx-2" style="background-color: black; color:white">Todos</div>
                        <div id="recibidos" class="cursor-pointer p-2 mx-2" style="background-color: black; color:white">Recibidos</div>
                        <div id="dados" class="cursor-pointer p-2 mx-2" style="background-color: black; color:white">Dados</div>
                    </div>
                </div>
            </div>
            <div class="row">
            	<div class="col col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 d-flex flex-wrap justify-content-center">
            		<table class="table table-responsive w-50">
            			<tr>
            				<td rowspan="2" class="border-0">
            					<i class="far fa-comment-alt fa-5x"></i>
            				</td>
            				<td class="border-0">
            					<h1 id="total_feedback"></h1>
            				</td>
            			</tr>
            			<tr>
            				<td class="border-0">
            					<span>Total de Feedback</span>
            				</td>
            			</tr>
            		</table>
            	</div>
            	<div class="col col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 d-flex flex-wrap justify-content-center">
            		<table class="table table-responsive w-50">
            			<tr>
            				<td rowspan="2" class="border-0">
            					<i class="fas fa-sign-language fa-5x"></i>
            				</td>
            				<td class="border-0">
            					<h1 id="total_aplausos"></h1>
            				</td>
            			</tr>
            			<tr>
            				<td class="border-0">
            					<span>Total de aplausos</span>
            				</td>
            			</tr>
            		</table>
            	</div>
            </div>
            <div class="row mt-2">
            	<div class="col col-12 col-sm-10 col-md-10 col-lg-10 col-xl-10 offset-md-1 offset-lg-1 offset-xl-1">
            		<div id="contenedor_feedback" class="w-100 pb-5">
                    </div>
            	</div>
            </div>
        </div>

    </div>
</div>
<a href="<?php echo base_url()?>app/givefeedback">
<div style="
    position: fixed;
    width: 50px;
    height: 50px;
    bottom: 40px;
    right: 9%;
    background-color: black;
    color: white;
    border-radius: 50%;
    cursor: pointer;
    font-size: 2rem;
    padding-top: 0rem;
    " class="d-flex justify-content-center"><div class="align-self-center pb-2">+</div></div>
</a>
<script src="<?php echo base_url()?>assets/js/app/feedback.js"></script>