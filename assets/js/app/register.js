$(document).ready(function(){
    $('#register').validate({
        rules: {
            name: {
                required: true
            },
            email: {
                required: true,
                email: true
            },
            password: {
                required: true
            },
            repeat_password: {
                required: true,
                equalTo: "#password"
            },
            business_name: {
                required: true
            }
        },
        submitHandler: function () {
            var datos = new FormData($('#register')[0]);
            //console.log(datos)
            register(datos);
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
function register(datos) {
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
                text: "Registro exitoso, ahora puedes descargar la app y comenzar a navegar."
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

function redirigir(tipo){
    if(tipo === "apple"){
        window.location.href = "https://apps.apple.com/us/app/nuup/id1517158945";
    }else if("android"){
        window.location.href = "https://play.google.com/store/apps/details?id=com.kreativeco.nuup&hl=en_US&gl=US";
    }
}