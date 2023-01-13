$(document).ready(function(){
    $('#register_extern').validate({
        rules: {
            name: {
                required: true
            },
            last_name: {
                required: true
            },
            email: {
                required: true,
                email: true
            },
            repeat_email:{
                required: true,
                equalTo: "#email"
            },
            password: {
                required: true
            },
            repeat_password: {
                required: true,
                equalTo: "#password"
            },
            phone: {
                required: true
            },
            country_id: {
                required: true
            },
            state_id: {
                required: true
            },
            activity_id: {
                required: true
            },
            business_name: {
                required: true
            },
            priority_crop_id: {
                required: true
            }
        },
        submitHandler: function () {
            var datos = new FormData($('#register_extern')[0]);
            //console.log(datos)
            register_extern(datos);
        },
        errorElement: "em",
        errorPlacement: function (error, element) {
            // Add the `help-block` class to the error element
            error.addClass("help-block rojo_error");
            if (element.prop("type") === "checkbox") {
                error.insertAfter(element.parent("label"));
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass("error_").removeClass("success_");
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).addClass("success_").removeClass("error_");
        }
    });
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/10/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Se carga catalogos de paises
     ***********************************************************************/
    GetCountry();
    $('#country_id').change(function (){
        GetState($(this).val());
        GetCrop($(this).val());
    });
    GetActivities();
});
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/10/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para enviar la peticion para iniciar la sesion
 ***********************************************************************/
function register_extern(datos) {
    datos.append('type_user',2);
    datos.append('web',1);
    var config = {
        url: window.base_url + "ws/signin",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            Swal.fire({
                type: 'success',
                title: 'Registro',
                text: response.msg
            }).then((result) => {
                $(location).attr('href',window.base_url+'app/home');
            });
        },
        error: function (response) {
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
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/10/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener catalogo de paises
 ***********************************************************************/
function GetCountry(){
   var datos = new FormData();
   var config = {
       url: window.base_url + "user/ListCountries",
       type: "POST",
       cache: false,
       contentType:false,
       processData: false,
       data: datos,
       success: function(response) {
           $.map(response.data,function (value,index){
               $('#country_id').append($('<option>', {value:value['id'], text:value['country']}));
           });
       },
       error: function (response) {
           Swal.fire({
               type: 'error',
               title: 'Paises',
               text: response.responseJSON.error_msg
           });
       }
   }
   $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/10/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener los estado en base al pais que se
 *      	seleccione
 ***********************************************************************/
function GetState(country_id){
    if(country_id === ''){
        $('#state_id').empty();
        $('#state_id').append($('<option>', {value:'', text:'Selecciona un país primero'}));
    }else{
        var datos = new FormData();
        datos.append('country_id',country_id)
        var config = {
            url: window.base_url + "user/ListStates",
            type: "POST",
            cache: false,
            contentType:false,
            processData: false,
            data: datos,
            success: function(response) {
                $('#state_id').empty();
                $.map(response.data,function (value,index){
                    let state = $('#state_id');
                    if(index === 0){
                        state.append($('<option>', {value:'', text:'Selecciona estado...'}));
                    }
                    state.append($('<option>', {value:value['id'], text:value['state']}));
                });
            },
            error: function (response) {
                $('#state_id').empty();
                Swal.fire({
                    type: 'error',
                    title: 'Estados',
                    text: response.responseJSON.error_msg
                });
            }
        }
        $.ajax(config);
    }
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/10/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener catalogo de actividades
 ***********************************************************************/
function GetActivities(){
    var datos = new FormData();
    var config = {
        url: window.base_url + "user/ListActivities",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            $.map(response.data,function (value,index){
                $('#activity_id').append($('<option>', {value:value['id'], text:value['activity']}));
            });
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Actividades',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/10/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener el catalogo de cultivos
 ***********************************************************************/
function GetCrop(country_id){
    if(country_id === ''){
        $('#priority_crop_id').empty();
        $('#priority_crop_id').append($('<option>', {value:'', text:'Selecciona un país primero'}));
    }else{
        var datos = new FormData();
        datos.append('country_id',country_id)
        var config = {
            url: window.base_url + "user/ListPriorityCrop",
            type: "POST",
            cache: false,
            contentType:false,
            processData: false,
            data: datos,
            success: function(response) {
                $('#priority_crop_id').empty();
                $.map(response.data,function (value,index){
                    let state = $('#priority_crop_id');
                    if(index === 0){
                        state.append($('<option>', {value:'', text:'Selecciona cultivo...'}));
                    }
                    state.append($('<option>', {value:value['id'], text:value['priority_crop']}));
                });
            },
            error: function (response) {
                $('#priority_crop_id').empty();
                Swal.fire({
                    type: 'error',
                    title: 'Cultivos',
                    text: response.responseJSON.error_msg
                });
            }
        }
        $.ajax(config);
    }
}