$(document).ready(function(){
    $('#login').validate({
        rules: {
            email: {
                required: true
            },
            password: {
                required: true
            }
        },
        submitHandler: function () {
            var datos = new FormData($('#login')[0]);
            //console.log(datos)
            login(datos);
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
});
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/10/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para enviar la peticion para iniciar la sesion
 ***********************************************************************/
function login(datos) {
    //var datos = new FormData();
    datos.append('web',1);
    var config = {
        url: window.base_url + "ws/login",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            $(location).attr('href',window.base_url+'app/home');
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
