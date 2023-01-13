$(document).ready(function(){
    $('#register_intern').validate({
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
            number_employee: {
                required: true
            },
            rol_employee: {
                required: true
            },
            area_employee: {
                required:true
            }
        },
        submitHandler: function () {
            var datos = new FormData($('#register_intern')[0]);
            //console.log(datos)
            register_intern(datos);
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
    GetRol();
    GetArea();
    GetCountry();
    $('#country_id').change(function (){
        GetState($(this).val());
    });
    GetActivities();
});
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/10/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para enviar la peticion para iniciar la sesion
 ***********************************************************************/
function register_intern(datos) {
    datos.append('type_user',1);
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

/**
 * Funcion para obtener el catalogo de actividades
 * Josue Carrasco
 */
function GetArea(){
    var datos = new FormData();
    var config = {
       url: window.base_url + "user/ListAreas",
       type: "GET",
       cache: false,
       contentType:false,
       processData: false,
       data: datos,
       success: function(response) {
           $.map(response.data,function (value,index){
               $('#area_employee').append($('<option>', {value:value['id'], text:value['area']}));
           });
       },
       error: function (response) {
           Swal.fire({
               type: 'error',
               title: 'Area',
               text: response.responseJSON.error_msg
           });
       }
    }
    $.ajax(config);
}

function GetRol(){
    var datos = new FormData();
    var config = {
       url: window.base_url + "user/ListJobsBasf",
       type: "GET",
       cache: false,
       contentType:false,
       processData: false,
       data: datos,
       success: function(response) {
           $.map(response.data,function (value,index){
               $('#rol_employee').append($('<option>', {value:value['id'], text:value['job_name']}));
           });
       },
       error: function (response) {
           Swal.fire({
               type: 'error',
               title: 'Area',
               text: response.responseJSON.error_msg
           });
       }
    }
    $.ajax(config);
}