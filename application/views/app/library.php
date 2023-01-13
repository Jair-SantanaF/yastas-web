
<div class="container-fluid h-100">
    
    <!--h1> Biblioteca </h1-->

    <div class="row justify-content-center pt-5">
        <div class="col-xl-9 col-lg-10 col-md-11 col-12">
            <div class="row justify-content-start">
                <div class="col-xl-4">
                    <select id="category_id" name="category_id" class="form-control">
                        <!--option value="-" selected="selected">Categoria</option>
                        <option value="cat1">Categoria 1</option-->
                    </select>
                </div>
                <div class="col-xl-4">
                    <select id="subcategory_id" name="subcategory_id" class="form-control" disabled>
                        <!--option value="-" selected="selected">Subcateogira</option>
                        <option value="subcat1">Subcategoria 1</option-->
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10 col-md-11 col-12">
            <div id="contenedor_elementos" class="row">
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url()?>assets/js/app/library.js"></script>