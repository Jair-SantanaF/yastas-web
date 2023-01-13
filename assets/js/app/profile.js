$(document).ready(function(){
    Perfil();

    //Validaciones para los campos
    $('#form_usuario').validate({
        rules: {
            name: {
                required: true
            },
            last_name: {
                required: true
            },
            phone: {
                required: true
            }
        },
        submitHandler: function (form) {
            //console.log(datos)
            GuardarPerfil();
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

    //Boton editar: mostrar campos de captura y ocultar etiquetas, mostrar boton para guardar
    $(`#btn_editar`).click(function () {
        $(this).addClass("d-none");
        $(`#btn_guardar`).removeClass("d-none");
        $(`#phone_label`).addClass("d-none");
        $(`#phone`).removeClass("d-none");
        $(`#full_name_label`).addClass("d-none");
        $(`#full_name`).removeClass("d-none");
    });

    //Eventos para cambiar imagen de perfil
    $('#preview_imagen').on('click', function () {
        $("#imagen").trigger("click");
    });
    $("#imagen").change(function(){
        GuardarFoto();
    });
});

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 14/11/2020
 *	Nota: Funcion para cargar los datos del perfil, por peticion AJAX
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
            $(`#email_label`).text(perfil.email);
            $(`#job_name_label`).text(perfil.job_name);
            $(`#bussiness_name_label`).text(perfil.business_name);
            $(`#phone_label`).text(perfil.phone);
            $(`#phone`).val(perfil.phone);
            $(`#last_name_label`).text(perfil.last_name);
            $(`#last_name`).val(perfil.last_name);
            $(`#name_label`).text(perfil.name);
            $(`#name`).val(perfil.name);
            var preview_imagen = document.getElementById('preview_imagen');
            preview_imagen.src = perfil.profile_photo;
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
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 14/11/2020
 *	Nota: funcion para guardar los nuevos datos del perfil
 ***********************************************************************/
function GuardarPerfil() {
    var datos = {
            name : $('#name').val(),
            last_name : $('#last_name').val(),
            phone : $('#phone').val()
        };

    $.ajax({
        url:  window.base_url+"ws/updateProfile",
        type: "POST",
        data: datos,
        cache: false,
        success: function(response){

            //Boton editar: ocultar campos de captura y mostrar etiquetas, mostrar boton para editar
            $(`#btn_guardar`).addClass("d-none");
            $(`#btn_editar`).removeClass("d-none");
            $(`#phone_label`).removeClass("d-none");
            $(`#phone`).addClass("d-none");
            $(`#full_name_label`).removeClass("d-none");
            $(`#full_name`).addClass("d-none");

            //Cargar nuevamente todos los campos y etiqeutas, con los datos ya almacenados
            var perfil = response.data;
            $(`#email_label`).text(perfil.email);
            $(`#job_name_label`).text(perfil.job_name);
            $(`#bussiness_name_label`).text(perfil.business_name);
            $(`#phone_label`).text(perfil.phone);
            $(`#phone`).val(perfil.phone);
            $(`#last_name_label`).text(perfil.last_name);
            $(`#last_name`).val(perfil.last_name);
            $(`#name_label`).text(perfil.name);
            $(`#name`).val(perfil.name);

            Swal.fire({
                type: 'success',
                title: 'Perfil',
                text: 'El perfil se guardo correctamente'
            });
        },
        error: function () {
            Swal.fire({
                type: 'error',
                title: 'Perfil',
                text: 'El perfil no se pudo guardar'
            });
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 14/11/2020
 *	Nota: Función para guardar la foto de perfil
 ***********************************************************************/
function GuardarFoto() {
    var imagen = $("#imagen").prop('files').length != 0 ? $("#imagen").prop('files')[0] : null,
        datos = new FormData();

    datos.append('profile_photo', imagen);

    $.ajax({
        url:  window.base_url+"ws/updateProfilePhoto",
        type: "POST",
        contentType: false,
        data: datos,
        processData: false,
        cache: false,
        success: function(response){
            Swal.fire({
                type: 'success',
                title: 'Perfil',
                text: 'La imagen de perfil se guardo correctamente'
            }).then((result) => {
                var url = window.base_url+"app/profile";
                location.href = url;
            });
        },
        error: function () {
            Swal.fire({
                type: 'error',
                title: 'Perfil',
                text: 'La imagen de perfil no se pudo guardar'
            });
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 14/11/2020
 *	Nota: Función para mostrar la imagen seleccionada
 ***********************************************************************/
function loadImagen(event) {
    var url = URL.createObjectURL(event.target.files[0]);
    var output = document.getElementById('preview_imagen');
    output.src = url;
};
