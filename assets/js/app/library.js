$(document).ready(function(){
    Libreria();
    Categorias();
    //Subcategorias()
    $('#category_id').on('change', function() {
        Subcategorias(this.value);
        Libreria();
    });
    $('#subcategory_id').on('change', function() {
        Libreria();
    });
});
function Libreria(){
    var datos = new FormData(),
        category_id = $('#category_id').val(),
        subcategory_id = $('#subcategory_id').val();

    if(category_id != null && category_id != ""){
        datos.append('category_id', category_id);
    }
    if(subcategory_id != null && subcategory_id != ""){
        datos.append('subcategory_id', subcategory_id);
    }

    $("#contenedor_elementos").empty();
    var config = {
        url: window.base_url + "library/ListLibrary",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            var elementos = response.data;
            $.each(elementos, function (index,value) {
                crearElementoBiblioteca(value);
            });

            $(`.elemento-biblioteca`).click(function () {
                var id = $(this).attr("id_elemento"),
                    tipo = $(this).attr("tipo_elemento"),
                    link = $(this).attr("link"),
                    url = "";

                switch (tipo) {
                    case "video":
                        url = window.base_url+"app/video/"+id;
                        location.href = url;
                        break;
                    case "documento":
                        url = window.base_url+"app/pdf/"+id;
                        location.href = url;
                        break;
                    case "imagen":
                        url = window.base_url+"app/imagen/"+id;
                        location.href = url;
                        break;
                    case "link":
                        var win = window.open(link, '_blank');
                        win.focus();
                        break;
                }
            });
            return;
        },
        error: function (response) {
            return;
            Swal.fire({
                type: 'error',
                title: 'Login',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Crear el html de una pregunta TIPO = 1, Opción Multiple Con Multiples Respuestas
 ***********************************************************************/
function crearElementoBiblioteca(elemento){
    var elemento_html = ``;

    elemento_html = `
        <div class="col col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4 pt-5">
            <div class="w-100 position-relative cursor-pointer elemento-biblioteca" id_elemento=${elemento.id} tipo_elemento="${elemento.type}" link='${elemento.link}'>           
                <img class="card-img" alt="preview" src="${elemento.image}">
                <div class="position-absolute" style="height: 100%;width: 100%;top: 0;left: 0;">
                    <div style="background-color: #007a3a7a;height: 100%;width: 100%;">                
                    </div>
                </div>
                <!-- <div class="card-img-overlay">
                    <div class="card-text text-white h3">${elemento.title}</div>
                    <p class="card-text text-white">${elemento.text}</p>
                </div> -->
            </div>        
        </div>                    
    `;

    $("#contenedor_elementos").append(elemento_html);

}

/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener las categorias para cargar el select
 ***********************************************************************/
function Categorias() {
    var datos = new FormData();
    $('#category_id')
        .find('option')
        .remove()
        .end()
    var config = {
        url: window.base_url + "library/ListCategories",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            let data = response.data;
            $.each(data, function (index,value) {
                if(index === 0){
                    $('#category_id').append($('<option>', {
                        value: '',
                        text : 'Seleccionar...'
                    }));
                    /*
                    $('#category_id').append($('<option>', {
                        value: '0',
                        text : 'Todos'
                    }));
                     */
                }
                $('#category_id').append('<option value="'+value.id+'">'+value.name+'</option>');
            });
        },
        error: function (response) {
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener las subcategorias en base a la categoria
 *          seleccionada
 ***********************************************************************/
function Subcategorias(category_id) {
    $('#subcategory_id').attr("disabled", true);
    var datos = new FormData();
    $('#subcategory_id')
        .find('option')
        .remove()
        .end();
    datos.append('category_id',category_id);
    var config = {
        url: window.base_url + "library/ListSubcategory",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            $('#subcategory_id').attr("disabled", false);
            let data = response.data;
            $.each(data, function (index,value) {
                if(index === 0){
                    $('#subcategory_id').append($('<option>', {
                        value: '',
                        text : 'Seleccionar...'
                    }));
                    /*
                    $('#subcategory_id').append($('<option>', {
                        value: '0',
                        text : 'Todos'
                    }));
                     */
                }
                $('#subcategory_id').append('<option value="'+value.id+'">'+value.subcategory+'</option>');
            });
        },
        error: function (response) {
            $('#subcategory_id').append($('<option>', {
                value: '',
                text : 'Seleccionar...'
            }));
            /*
            if(select_id !== ''){
                $('#subcategory_id').append('<option value="0" selected="selected">Todos</option>');
            }else{
                $('#subcategory_id').append($('<option>', {
                    value: '0',
                    text : 'Todos'
                }));
            }*/
        }
    }
    $.ajax(config);
}