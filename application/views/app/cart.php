<div class="container-fluid h-100">
    <div class="row d-flex justify-content-center">
        <div class="col-11">
            <div class="row">
        		<div id="cart_items" class="col col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 mt-5">
        		</div>

        		<div class="col col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4 back_negro_text_blanco">
        			<div class="forms mt-5">
        				<!--?php echo form_open('', array('id'=>'cart')); ?-->
        				<div class="form-group row">
        					<div class="col col-10 col-sm-10 offset-1 pt-2 pb-2">
        						Resumen de compra
        					</div>
        				</div>
        				<div class="form-group row">
        					<div class="col col-10 col-sm-10 offset-1 pt-3 pb-3">
                                <span id="number_items">0</span> articulo(s) <span id="cart_total" class="float-right">$0.00</span>
        					</div>
        				</div>
        				<div class="form-group row">
        					<div class="col col-10 col-sm-10 offset-1 pt-2">
        						<label for="pago_id">Metodo de pago</label>
        					</div>
        				</div>
        				<div class="form-group row">
        					<div class="col col-10 col-sm-10 offset-1 pb-2">
					      		<?php
		        				$array=array(
		        					'-' => '-',
		        					'credito'=>'Tarjeta de crédito',
		        					'debito'=>'Tarjeta de débito',
		        					'transfer'=>'Transferencía'
		        				);
		        				echo form_dropdown('pago_id',$array,'', array('class'=>'form-control','id'=>'activity_id'));
		        				?>
						    </div>
        				</div>
        				<div class="form-group row">
        					<div class="col col-10 col-sm-10 offset-1 pt-2">
        						Código de promoción
        					</div>
        				</div>
        				<div class="form-group row">
        					<div class="col col-10 col-sm-10 offset-1">
        						<?php echo form_input('promo','', array('class'=>'form-control')); ?>
        					</div>
        				</div>
        				<div class="form-group row">
        					<div class="col col-10 col-sm-10 offset-1 pb-2">
        						<button class="btn btn-light">Aplicar</button>
        					</div>
        				</div>
        				<div class="form-group row">
        					<div class="col col-10 col-sm-10 offset-1 border-top pt-3 pb-3">
        						Total a pagar <span class="float-right">$0.00</span>
        					</div>
        				</div>
        				<div class="form-group row">
        					<div class="col col-10 col-sm-10 offset-1">
        						<button id="pagar" class="btn btn-block button button-wrap">Pagar</button>
        					</div>
        				</div>
        				<!--?php echo form_close(); ?-->
        			</div>
        		</div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url()?>assets/js/app/cart.js"></script>