<div class="container-fluid">
    <div class="row d-flex justify-content-center">

        <div class="col-11 mt-4">
            <div class="row">

            	<div class="col col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 offset-sm-2 offset-md-2 offset-lg-2 offset-xl-2 mt-4">
            		
            		<h1>Nueva Nota</h1>

            		<?php echo form_open('', '', array()); ?>

            			<div class="form-group row mt-4">
					    	<div class="col-sm-12">
					      		<select class="custom-select border-left-0 border-right-0 rounded-0 p-5">
					      			<option selected>
					      				Categoria
					      			</option>
					      			<option>
					      				Categoria 1
					      			</option>
					      			<option>
					      				Categoria2
					      			</option>
					      		</select>
				    		</div>
					  	</div>

					  	<div class="form-group row">
					  		<div class="col-sm-10">
					  			<input type="descripcion" class="form-control border-left-0 border-right-0 rounded-0 p-5" id="descripcion" placeholder="Descripci&oacute;n">
					  		</div>
					  		<div class="col col-sm-1 d-flex align-items-center justify-content-center p-3">
					  			<i class="fas fa-film fa-2x"></i>
					  		</div>
					  		<div class=" col col-sm-1 d-flex align-items-center justify-content-center p-3">
					  			<i class="far fa-image fa-2x"></i>
					  		</div>
					  	</div>

					  	<div class="form-group row">
						    <div class="col-sm-12 d-flex justify-content-center">
						      	<button type="submit" class="btn button button-wrap">Confirmar</button>
						    </div>
					  	</div>

            		<? echo form_close(); ?>

            	</div>

            </div>
        </div>

    </div>
</div>