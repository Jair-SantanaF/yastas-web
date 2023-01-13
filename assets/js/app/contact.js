$(document).ready(function(){
    Perfil();

    //Validaciones para los campos
    $('#form_message').validate({
        rules: {
            message: {
                required: true
            }
        },
        submitHandler: function (form) {
            //console.log(datos)
            EnviarMensaje();
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
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 14/11/2020
 *	Nota: Funcion para cargar los datos para el mensaje, por peticion AJAX
 ***********************************************************************/
function Perfil(){
    var datos = new FormData();

    var config = {
        url: window.base_url + "ws/getProfile",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            var perfil = response.data;

            //Cargar datos en campos editables y etiquetas
            $(`#email`).val(perfil.email);
            $(`#name`).val(perfil.name+' '+perfil.last_name);
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Perfil',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);

}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 14/11/2020
 *	Nota: funcion para enviar el mensaje
 ***********************************************************************/
function EnviarMensaje() {
    var datos = {
        message : $('#message').val()
        };

    $.ajax({
        url:  window.base_url+"user/Contact",
        type: "POST",
        data: datos,
        cache: false,
        success: function(response){
            $('#message').val("");
            Swal.fire({
                type: 'success',
                title: 'Contacto',
                text: 'El mensaje se envió correctamente'
            });
        },
        error: function () {
            Swal.fire({
                type: 'error',
                title: 'Contacto',
                text: 'El mensaje no de pudo enviar'
            });
        }
    });
}