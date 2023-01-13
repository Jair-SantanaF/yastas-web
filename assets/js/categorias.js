jQuery(document).ready(function ($) {
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 03/06/2019
     *	Nota: Se agrega funcionalidad para cambiar textos de requeridos
     ***********************************************************************/
    jQuery.extend(jQuery.validator.messages, {
        required: "Este campo es requerido",
        remote: "Please fix this field.",
        email: "Por favor, introduzca un email valido.",
        url: "Por favor, introduzca una URL valida",
        date: "Por favor, introduzca una fecha valida",
        dateISO: "Please enter a valid date (ISO).",
        number: "Please enter a valid number.",
        digits: "Please enter only digits.",
        creditcard: "Please enter a valid credit card number.",
        equalTo: "Por favor, introduzca el mismo valor de nuevo.",
        accept: "Please enter a value with a valid extension.",
        maxlength: jQuery.validator.format("Ingrese no más de {0} caracteres."),
        minlength: jQuery.validator.format("Ingrese al menos {0} caracteres."),
        rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
        range: jQuery.validator.format("Please enter a value between {0} and {1}."),
        max: jQuery.validator.format("Por favor ingrese un valor menor o igual a {0}."),
        min: jQuery.validator.format("Por favor ingrese un valor mayor o igual a {0}.")
    });
    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández
     *           mario.martinez.f@hotmail.es
     *    Fecha: 08 sep 2017
     *    Nota: Plugin para tabla.
     ***********************************************************************/
    if(typeof es_seccion_capturas === undefined){
        $('.datatable').dataTable({
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
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
            "order": [[0, "desc"]]
        });

    }else{
        $('#seccion_capturas').dataTable({
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
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
            "order": [[3, "desc"]]
        });
    }

    $('#form_categorias').validate({
        ignore: "",
        rules: {
            mensaje: {
                required: true
            },
            partes_implicadas:{
                required: true
            }
        },
        submitHandler: function (form) {
            var datos = new FormData($('#form_categorias')[0]);
            guardarCategoria(datos);
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

function nuevaCategoria(){
    var categoria = $('#categoria'),
        id_categoria = $('#id_categoria'),
        id_sindicato = $('#id_sindicato');

    id_categoria.val(null);
    id_sindicato.val("");
    categoria.val("");

    $('#agregar_usuario_modal').modal('toggle');
}

function editar(id){
    var datos = new FormData(),
        id_sindicato = $('#id_sindicato'),
        categoria = $('#categoria'),
        id_categoria = $('#id_categoria');

    id_sindicato.val(null);
    categoria.val(null);
    id_categoria.val(null);

    datos.append('id_categoria', id);

    $.ajax({
        url: window.base_url+"Admin/CategoriaEdicion",
        type: 'POST',
        contentType: false,
        data: datos,
        processData: false,
        cache: false,
        beforeSend:function(){
        },
        success: function(respuesta) {
            if(typeof(respuesta)=="string")respuesta=JSON.parse(respuesta);

            if(respuesta.data.length > 0){
                var categoria_ = respuesta.data[0];

                id_categoria.val(categoria_.id);
                id_sindicato.val(categoria_.id_sindicato).change();
                categoria.val(categoria_.categoria);
            }
            $('#agregar_usuario_modal').modal('toggle');
        },
        error: function(respuesta) {
        }
    });
}

function eliminar(id){
    var id_usuario_eliminacion = $('#id_usuario_eliminacion');

    id_usuario_eliminacion.val(id);

    $('#eliminar_usuario_modal').modal('toggle');
}

function eliminarUsuario(){
    var datos = new FormData(),
        id_usuario_eliminacion = $('#id_usuario_eliminacion');

    datos.append('id_usuario', id_usuario_eliminacion.val());

    $.ajax({
        url: "eliminarUsuario",
        type: 'POST',
        contentType: false,
        data: datos,
        processData: false,
        cache: false,
        beforeSend:function(){
        },
        success: function(respuesta) {
            cargarHtml('admin/usuarios');
            $('#eliminar_usuario_modal').modal('toggle');
        },
        error: function(respuesta) {
            $('#eliminar_usuario_modal').modal('toggle');
        }
    });

}

function guardarCategoria(datos) {
    var id_categoria = $('#id_categoria');
    if(id_categoria.val()){
        datos.append('id_categoria', id_categoria.val());
    }

    $.ajax({
        url: window.base_url+'admin/AgregarCategoria',
        type: 'POST',
        contentType: false,
        data: datos,
        processData: false,
        cache: false,
        beforeSend:function(){
        },
        success: function(respuesta) {
            cargarHtml('admin/VistaCategorias');
            $('#agregar_usuario_modal').modal('toggle');
        },
        error: function(respuesta) {
            alert(respuesta.responseJSON.error_msg);
            //$('#agregar_usuario_modal').modal('toggle');
        }
    });
}
