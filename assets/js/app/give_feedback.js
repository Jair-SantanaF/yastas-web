var tabla_users = null
    usuario_seleccionado=null;

$(document).ready(function(){
    CargarCategorias();
    ObtenerTablaUsuarios(null);

    $('#form_feedback').validate({
        rules: {
            category_id: {
                required: true
            },
            description: {
                required: true
            },
        },
        submitHandler: function () {
            //console.log(datos)
            GuardarFeedback();
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
    /*
    var datos = new FormData();
    datos.append('type',3);
    var config = {
        url: window.base_url + "feedback/FeedbackList",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            var retros = response.data;
            $.each(retros, function (index,value) {
                crearElementoFeedback(value);
            });
            $("#total_feedback").html(response.extras.total_feedback);
            $("#total_aplausos").html(response.extras.total_like);
            return;
        },
        error: function (response) {
            $("#total_feedback").html("0");
            $("#total_aplausos").html("0");
            return;
            Swal.fire({
                type: 'error',
                title: 'Login',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
     */

    $(`#buscar_usuario`).click(function () {
        MostrarBusqueda();
    });
    $(`#seleccionar_usuario`).click(function () {
        SeleccionarUsuario();
    });

    $('#media_trigger').on('click', function () {
        $("#media").trigger("click");
    });
    $('#file_trigger').on('click', function () {
        $("#file").trigger("click");
    });
});

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Crear el html de una pregunta TIPO = 1, Opción Multiple Con Multiples Respuestas
 ***********************************************************************/
function crearElementoFeedback(feedback){
    var feedback_html = ``;

    feedback_html = `           
                <div class="w-100 mt-2 border-top">
                    <div class="w-100 d-flex mt-4">
                        <div class="p-2">
                            <img style="min-width:150px; width: 150px; height: 150px" class="img-fluid rounded-circle" src="${feedback.photo_user}" alt="Generic placeholder image">
                        </div>
                        <div class="w-100 d-flex pl-3">
                            <div class="align-self-center">
                                <div class="w-100"><h1> ${feedback.name_user} </h1></div>   
                                <div class="w-100 py-3">Administrador</div>
                            </div>
                        </div>   
                        <div class="p-2">
                            <i style="width: 50px" class="fas fa-sign-language fa-3x"></i>
                            <div class="text-center">
                                ${feedback.total_like}
                            </div>
                        </div>               
                    </div>
                    <div class="w-100 mb-4">
                        <!--p class="mb-0"> ${feedback.description} </p-->
                        <p class="mb-0"> Donec sed odio dui. Nullam quis risus eget urna mollis ornare vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>
                    </div>                       
                    <!--div class="w-100 d-flex py-3">
                        <a href="${base_url}app/addnote" class="btn back_negro_text_blanco rounded-circle text-white">
                            <i class="fas fa-plus"></i>
                        </a>
                        <div class="align-self-center pl-3">
                            Generar Nota
                        </div>  
                    </div-->                                                                    
                </div>
    `;

    $("#contenedor_feedback").append(feedback_html);
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 22 Octubre 2020
 *	Nota: Funcion para finalizar el curso
 ***********************************************************************/
function mostrarFeedback(id_feedback){
    var url = window.base_url+"app/cuestionario/"+id_feedback;
    location.href = url;
}

function CargarCategorias(){
    $.ajax({
        url:  window.base_url+"feedback/CategoryFeedback",
        type: 'POST',
        contentType: false,
        //data: datos,
        processData: false,
        cache: false,
        success: function(json) {
            var html = '<option value="">Categoría</option>';
            for(var key in json.data){
                html += '<option value="'+json.data[key].id+'">'+json.data[key].description+'</option>';
            }
            $('#category_id').html(html).fadeIn();
        }
    });
}

/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/10/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para enviar la peticion para iniciar la sesion
 ***********************************************************************/
function GuardarFeedback() {
    var datos = new FormData($('#form_feedback')[0]),
        user_id = $('#user_id').val(),
        file = $("#file").prop('files').length != 0 ? $("#file").prop('files')[0] : null,
        media = $("#media").prop('files').length != 0 ? $("#media").prop('files')[0] : null;

    if(user_id == ""){
        $('#full_name-error').removeClass("d-none");
    }
    datos.append('user_id',user_id);
    if(file)
        datos.append('file_path',file);
    if(media)
        datos.append('media_path',media);

    var config = {
        url: window.base_url + "feedback/CreateFeedback",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            Swal.fire({
                type: 'success',
                title: 'Feedback',
                text: response.msg
            }).then((result) => {
                $(location).attr('href',window.base_url+'app/feedback');
            });
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Feedback',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
}

function MostrarBusqueda(){
    $('#feedback_view').addClass("d-none");
    $('#search_view').removeClass("d-none");
}

function ObtenerTablaUsuarios(record) {
    $('#tabla_users tbody').on('click','td',function(e){
        var user_id = $(this).children().val();
        tabla_users.$('input[type="checkbox"]').each(function(){
            if($(this).prop("value") != user_id){
                this.checked = false;
            }
        });
    });
    $('#tabla_users').on('tbody click','tr',function(e){
        var record = tabla_users.row(this).data();
        usuario_seleccionado = record;
    });
    //Se crea la tabla con eyuda del plugin DataTable
    tabla_users = $('#tabla_users').DataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "",
            "sInfoEmpty": "",
            "sInfoFiltered": "",
            /*"sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",*/

            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        "idSrc": "id",
        "order": [[0, "desc"]],
        "ajax": {
            url: window.base_url+"User/UserList",
            type: 'POST',
            data: function (d) {
                d.event_id = 1;
            },
            error: function (xhr, error, code){
                tabla_users.clear().draw();
            }
        },
        buttons: [],
        "columns": [{
            data: "full_name",
            render: function ( data, type, row ) {
                var user = ``;

                user = `                           
                    <div class="w-100 d-flex">
                        <div class="p-2">
                            <img style="min-width:50px; width: 50px; height: 50px" class="img-fluid rounded-circle" src="${row.profile_photo}" alt="Generic placeholder image">
                        </div>
                        <div class="w-100 d-flex pl-3">
                            <div class="align-self-center">
                                <div class="w-100"> ${data} </div>                                   
                            </div>
                        </div>   
                    </div>`;
                return user;
            }
        },{
            targets: 0,
            data: "id",
            defaultContent: '',
            orderable: false,
            'className': 'dt-body-center',
            'render': function (data, type, full, meta){
                return '<input style="margin-top: 1.6rem" type="checkbox" value="'+ $('<div/>').text(data).html() + '">';
            }
        }]
    });
}

function SeleccionarUsuario(){
    $('#full_name').html(usuario_seleccionado.full_name);
    $('#job_name').html(usuario_seleccionado.job_name);
    $('#profile_photo').attr("src", usuario_seleccionado.profile_photo);
    $('#user_id').val(usuario_seleccionado.id);

    $('#full_name-error').addClass("d-none");

    $('#feedback_view').removeClass("d-none");
    $('#search_view').addClass("d-none");
}