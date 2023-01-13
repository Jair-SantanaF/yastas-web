$(document).ready(function(){
    $('#form_recover').validate({
        rules: {
            email: {
                required: true
            },
            conf_email: {
                required: true
            }
        },
        submitHandler: function () {
            var datos = new FormData($('#form_recover')[0]);
            //console.log(datos)
            form_recover(datos);
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

function form_recover(datos)
{
    //var datos = new FormData();
    datos.append('type_user', 1)
    datos.append('web',1);
    var config = {
        url: window.base_url + "ws/recuperarPassword",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            Swal.fire({
                type: 'success',
                title: 'Recuperar Contraseña',
                text: response.msg
            }).then((result) => {
                $(location).attr('href',window.base_url+'app/home');
            });
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Recuperar Contraseña',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
}