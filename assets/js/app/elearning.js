$(document).ready(function(){
    GetCategories();
    $('#category_id').change(function (){
        $('#detail_elearning').html('');
        GetSubcategories($(this).val());
    });
    $('#subcategory_id').change(function (){
        $('#detail_elearning').html('');
        GetElearnings();
    });
});

/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/11/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener las categorias.
 ***********************************************************************/
function GetCategories(){
    var datos = new FormData();
    var config = {
        url: window.base_url + "elearning/ListCategories",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            $.map(response.data,function (value,index){
                $('#category_id').append($('<option>', {value:value['id'], text:value['category']}));
            });
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Categorías',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/11/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener las suncategorias en base a la categoria
 *          previamente seleccionada
 ***********************************************************************/
function GetSubcategories(category_id) {
    if (category_id === '') {
        $('#subcategory_id').empty();
        $('#subcategory_id').append($('<option>', {value: '', text: 'Selecciona una categoría primero'}));
    } else {
        var datos = new FormData();
        datos.append('category_id', category_id)
        var config = {
            url: window.base_url + "elearning/ListSubcategories",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            data: datos,
            success: function (response) {
                $('#subcategory_id').empty();
                $.map(response.data, function (value, index) {
                    let subcategory = $('#subcategory_id');
                    if (index === 0) {
                        subcategory.append($('<option>', {value: '', text: 'Selecciona subcategoría...'}));
                    }
                    subcategory.append($('<option>', {value: value['id'], text: value['subcategory']}));
                });
            },
            error: function (response) {
                $('#subcategory_id').empty();
                Swal.fire({
                    type: 'error',
                    title: 'Subcategorías',
                    text: response.responseJSON.error_msg
                });
            }
        }
        $.ajax(config);
    }
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/11/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener el detalle de los cursos existentes
 ***********************************************************************/
function GetElearnings(){
    let category_id = $('#category_id').val(),
        subcategory_id = $('#subcategory_id').val();
    var datos = new FormData();
    datos.append('category_id',category_id);
    datos.append('subcategory_id',subcategory_id);
    var config = {
        url: window.base_url + "elearning/elearningModules",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            var html = '';
            $.each(response.data, function (index,value){
                let button = '', calif = 'Sin calificación registrada';
                if(value['my_last_score'] !== ''){
                    calif = value['my_last_score']+'%';
                }
                if(value['my_last_score'] >= value['min_score'] ){
                    button = '<p class="mb-0 verde_basf_dark font-weight-bold">El curso ha sido aprobado.</p>';
                }else{
                    button = '<p class="mb-0 rojo_nuup font-weight-bold">El curso no ha sido aprobado.</p>';

                }
                if(value['can_eval']){
                    button = '<a href="'+value['trail_url']+'/?curso='+value['id']+'&token=web" class="btn verde_basf_dark_back text-white">Acceder</a>';
                }
                html+='<div class="col col-12 col-sm-12 col-md-12 col-lg-10 col-xl-10 mt-5">' +
                    '<div class="media">' +
                        '<img src="'+value['preview']+'" alt="'+value['title']+'" class="d-flex align-self-center mr-3" title="'+value['title']+'">' +
                        '<div class="media-body">' +
                            '<h3 class="mt-0">'+value['title']+'</h3>' +
                            '<p class="mb-0">' +
                                value['title']+
                            '</p>' +
                            '<p class="mb-0">Max intentos: ' +value['max_try']+'</p>'+
                            '<p class="mb-0"> Intento actual: ' +value['tried']+'</p>'+
                            '<p class="mb-0"> Ultima calificación: ' +calif+'</p>'+
                            '<br> '+button+
                        '</div>' +
                    '</div>' +
                '</div>';
            });
            $('#detail_elearning').html(html);
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Capacitación',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
}