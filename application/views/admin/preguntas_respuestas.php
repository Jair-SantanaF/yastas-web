<div class="row contenedor_detalle">
    <div style="border-bottom: 1px solid #ccc;" class="col-12 text-right h4 my-auto titulo_pantallas">Cuestionarios</div>
    <div class="col-md-12 col-12">
        <div class="row justify-content-center">
            <div class="col-xl-11 col-md-11 col-sm-11 col-12">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12 pt-2 pb-2 text-left">
						<div class="form-group">
							<label for="cuestionarios_">Cuestionarios</label>
							<select class="form-control" id="cuestionarios_">
								<option value="">Seleccionar...</option>
								<?php
									foreach ($quiz as $index => $value){
										echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
									}
								?>
							</select>
							<small id="emailHelp" class="form-text text-muted">Selecciona un cuestionario para ver las respuestas.</small>
						</div>
					</div>
                </div>
                <input type="hidden" id="quiz_id" value="">
                <table id="catalogo_preguntas" class="datatable table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Nombre usuario</th>
                        <th>Pregunta</th>
                        <th>Respuesta</th>
						<th>Fecha</th>
						<th>Es correcta</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/respuestas.js"></script>
