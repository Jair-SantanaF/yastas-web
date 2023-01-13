<style type="text/css">
    table { border: none !important;}
</style>
<div class="container-fluid" id="feedback_view">
    <div class="row d-flex justify-content-center">
        <div class="col-11 mt-4">
            <div class="row">
            	<div class="col col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 offset-sm-2">
                    <div class="w-100 mt-2 border-top">
                        <div class="w-100 d-flex mt-4">
                            <div class="p-2">
                                <img style="min-width:150px; width: 150px; height: 150px" class="img-fluid rounded-circle" src="https://picsum.photos/200" alt="Generic placeholder image" id="profile_photo">
                            </div>
                            <div class="w-100 d-flex pl-3">
                                <div class="align-self-center">
                                    <input type="hidden" id="user_id">
                                    <div class="w-100"><h1 id="full_name"> <span style="color:gray">Nombre</span> </h1></div>
                                    <em id="full_name-error" class="error help-block rojo_error d-none">Este campo es requerido</em>
                                    <div class="w-100 py-3" id="job_name"> <span style="color:gray">Puesto</span> </div>
                                </div>
                            </div>
                            <div class="p-2">
                                <i id="buscar_usuario" class="fas fa-search fa-2x cursor-pointer"></i>
                            </div>
                        </div>
                    </div>
        		</div>
            </div>
            <div class="row">
            	<div class="col col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 offset-sm-2">
                    <?php echo form_open('', array('id'=>'form_feedback')); ?>
	        			<div class="form-group row mt-4">
					    	<div class="col-sm-12">
					      		<select id="category_id" class="custom-select border-left-0 border-right-0 rounded-0" name="category_id">
					      		</select>
				    		</div>
					  	</div>
					  	<div class="form-group row">
					  		<div class="col-sm-10">
					  			<textarea type="descripcion" class="form-control border-left-0 border-right-0 rounded-0" id="descripcion" placeholder="Descripci&oacute;n" rows="5" name="description"></textarea>
					  		</div>
					  		<div class="col col-sm-1 d-flex align-items-center justify-content-center p-3">
                                <input type="file" id="file" accept="image/*,application/pdf" style="display: none">
					  			<i id="file_trigger" class="fas fa-film fa-2x cursor-pointer"></i>
					  		</div>
					  		<div class=" col col-sm-1 d-flex align-items-center justify-content-center p-3">
                                <input type="file" id="media" accept="image/*" style="display: none">
					  			<i id="media_trigger" class="far fa-image fa-2x cursor-pointer"></i>
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
<div class="container-fluid d-none" id="search_view">
    <div class="row d-flex justify-content-center">
        <div class="col-11 mt-4">
            <div class="row pb-4">
                <div class="col col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 offset-md-2 mt-4">
                    <table id="tabla_users" class="table datatable dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead class="d-none">
                        <tr>
                            <th>Usuario</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row pb-5">
                <div class="col col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 offset-sm-2">
                    <div class="row">
                        <div class="col-12 d-flex justify-content-center">
                            <button id="seleccionar_usuario" class="btn button button-wrap">Confirmar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--div class="col-11 mt-4">
            <div class="row">
                <div class="col col-12 col-sm-6 offset-md-3 d-flex justify-content-center mt-3">
                    <input type="search" id="search" class="form-control rounded p-4 text-center" placeholder="Buscar">
                </div>
            </div>
            <div class="row">
                <div class="col col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 offset-md-2 mt-4">
                    <table class="table table-responsive">
                        <th>
                        <td style="width: 33%;">
                            <img src="https://picsum.photos/40" class="img-fluid rounded-circle">
                        </td>
                        <td class="" style="width: 33%;">
                            <p class="font-weight-bold ml-5">
                                Jorge L&oacute;pez
                            </p>
                        </td>
                        <td class="text-center" style="width: 33%;">
                            <p class="text-center">
                                <input type="checkbox">
                            </p>
                        </td>
                        </th>
                    </table>
                </div>
                <div class="col col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 offset-md-2 mt-4">
                    <table class="table table-responsive">
                        <th>
                        <td style="width: 33%;">
                            <img src="https://picsum.photos/40" class="img-fluid rounded-circle">
                        </td>
                        <td class="" style="width: 33%;">
                            <p class="font-weight-bold ml-5">
                                Adriana Reyes
                            </p>
                        </td>
                        <td class="text-center" style="width: 33%;">
                            <p class="text-center">
                                <input type="checkbox">
                            </p>
                        </td>
                        </th>
                    </table>
                </div>
                <div class="col col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 offset-md-2 mt-4">
                    <table class="table table-responsive">
                        <th>
                        <td style="width: 33%;">
                            <img src="https://picsum.photos/40" class="img-fluid rounded-circle">
                        </td>
                        <td class="" style="width: 33%;">
                            <p class="font-weight-bold ml-5">
                                Julia Herrera
                            </p>
                        </td>
                        <td class="text-center" style="width: 33%;">
                            <p class="text-center">
                                <input type="checkbox">
                            </p>
                        </td>
                        </th>
                    </table>
                </div>
                <div class="col col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 offset-md-2 mt-4">
                    <table class="table table-responsive">
                        <th>
                        <td style="width: 33%;">
                            <img src="https://picsum.photos/40" class="img-fluid rounded-circle">
                        </td>
                        <td class="" style="width: 33%;">
                            <p class="font-weight-bold ml-5">
                                Carlos Torres
                            </p>
                        </td>
                        <td class="text-center" style="width: 33%;">
                            <p class="text-center">
                                <input type="checkbox">
                            </p>
                        </td>
                        </th>
                    </table>
                </div>
                <div class="col col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 offset-md-2 mt-4">
                    <table class="table table-responsive">
                        <th>
                        <td style="width: 33%;">
                            <img src="https://picsum.photos/40" class="img-fluid rounded-circle">
                        </td>
                        <td class="" style="width: 33%;">
                            <p class="font-weight-bold ml-5">
                                Leonardo Alarc&oacute;n
                            </p>
                        </td>
                        <td class="text-center" style="width: 33%;">
                            <p class="text-center">
                                <input type="checkbox">
                            </p>
                        </td>
                        </th>
                    </table>
                </div>
                <div class="col col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 offset-md-2 mt-4">
                    <table class="table table-responsive">
                        <th>
                        <td style="width: 33%;">
                            <img src="https://picsum.photos/40" class="img-fluid rounded-circle">
                        </td>
                        <td class="" style="width: 33%;">
                            <p class="font-weight-bold ml-5">
                                Erica Rojas
                            </p>
                        </td>
                        <td class="text-center" style="width: 33%;">
                            <p class="text-center">
                                <input type="checkbox">
                            </p>
                        </td>
                        </th>
                    </table>
                </div>
            </div>
        </div-->
    </div>
</div>
<script src="<?php echo base_url()?>assets/js/app/give_feedback.js"></script>