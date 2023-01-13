var tabla_categorias,
    tabla_subcategorys,
    tabla_elementos,
    subcategory_id = '',
    audio_duration,
    audio_type;
    var filtro = "";
jQuery(document).ready(function ($) {
    obtenerCategoriaFaqs();
});

jQuery(document).ready(function ($) {
    CargarFaq();
    $('#form_categoria').validate({
        rules: {
            nombre_categoria: {
                required: true
            }
        },
        submitHandler: function (form) {
            //console.log(datos)
            GuardarCategoria();
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

    //funcion pora mostrar filtro habilitado o no habilitados
    $('#select_filtro').on('change', function(){
        filtro = $(this).val();
        $('#tabla_elementos').DataTable().ajax.reload();
        //ObtenerTablaElementos();
    });

    $('#form_elemento').validate({
        /* rules: {
            titulo: {
                required: true
            },
            texto: {
                required: true
            },
            preview: {
                required: true
            },
            id_categoria_elemento: {
                required: true
            },
            tipo: {
                required: true
            },
            tipo_video: {
                required: true
            },

            nombre_video: {
                required: true
            },
            nombre_documento: {
                required: true
            },
            nombre_imagen: {
                required: true
            },

            video_id: {
                required: true
            },
            link: {
                required: true,
                url: true
            }
        }, */
        submitHandler: function (form) {
            GuardarElemento();
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

    $('#subir_imagen').on('click', function () {
        $("#imagen").trigger("click");
    });

    $('#subir_preview').on('click', function () {
        $("#preview").trigger("click");
    });
});

/**
 * Hacemos el submit del formulario de preguntas
 */
function GuardarElemento() {
    /* if (id_elemento == '' && $("#capacitacion_obligatoria").val() == -1) {
        alert("Selecciona un tipo de archivo")
        return
    } */
    document.getElementById("loader_background").style.display = 'inherit';
    document.getElementById("loader").style.display = 'inherit';
    var id_elemento = $('#id_elemento').val(),
        datos = new FormData(),
        //type = "audio",
        //file = "", link = "", type_video = "", video = "",
        metodo = "guardar_pregunta",
        extension = "";
    datos.append('pregunta', $('#pregunta').val());
    datos.append('respuesta', $('#respuesta').val());
    datos.append('etiquetas', $('#etiquetas').val());
    datos.append('id_categoria', categorias_faqs[0].id);
    /* validar si es editar */
    if (id_elemento != '') {
        datos.append('id', id_elemento);
        datos.delete("capacitacion_obligatoria")
        metodo = "actualizar_pregunta";
    }
    file = $("#preview").prop('files').length != 0 ? $("#preview").prop('files')[0] : null;
    if(file){
        /* imagen */
        console.log("SI IMAGEN");
        datos.append('image', $("#preview").prop('files')[0])
    }else{
        console.log("NO IMAGEN");
    }
    /* request */
    console.log("run REQUEST");
    $.ajax({
        url: window.base_url + "Faqs/" + metodo,
        type: "POST",
        contentType: false,
        data: datos,
        processData: false,
        cache: false,
        success: function (response) {
            console.log(response)
            tabla_elementos.ajax.reload();
            $('#modal_elemento').modal('hide');
            document.getElementById("loader_background").style.display = 'none';
            document.getElementById("loader").style.display = 'none';
            Swal.fire({
                type: 'success',
                title: 'Catálogo elementos',
                text: 'El catálogo de elementos ha sido actualizado correctamente'
            });
        },
        error: function (error) {
            console.log(error)
            $('#modal_elemento').modal('hide');
            document.getElementById("loader_background").style.display = 'none';
            document.getElementById("loader").style.display = 'none';
            Swal.fire({
                type: 'error',
                title: 'Catálogo elementos',
                text: 'El elemento no se pudo guardar'
            });
        }
    });
}

/**
 * Pone el nombre del elemento seleccionado en la casilla de texto
 * @param {*} event 
 */
function loadPreview(event) {
    var url = URL.createObjectURL(event.target.files[0]);

    var file = event.target.files[0];

    $('#nombre_preview').val(file.name);
    //var output = document.getElementById('preview_imagen');
    //output.src = url;
}

/**
 * Cargamos la lista de preguntas en la tabla
 */
function CargarFaq() {

    $('#tabla_elementos').on('click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_elementos.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarElemento(record);
                }
                if (cmd == "eliminar") {
                    EliminarPreguntaFaq(record)
                }
            }
        }
    });

    $('#tabla_elementos').on('tbody click', 'tr', function (e) {
        var record = tabla_elementos.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarElemento(record);
                }
                if (cmd == "eliminar") {
                    EliminarPreguntaFaq(record)
                }
            }
        }
    });

    tabla_elementos = $('#tabla_elementos').DataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Registros del 0 al 0 de un total de 0 registros",
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
        "idSrc": "id",
        "order": [],
        "ajax": {
            url: "../index.php/Faqs/obtener_preguntas",
            type: 'POST',
            data: function (d) {
                d.filtro = filtro;
                d.es_admin = true
                //d.category_id = 7;
                //d.area_id = 10;
                //d.token = "9C4F9Z4us1fcqBx8k5I5iwdaqjg8lknHgcvgrALYdsIMZiOEA9Yi9BxD0rlbAoZAQkPk9uzoz2N6ojAz1Wa04ielphCkkwvStit77jHIeuXp6PSZRIXITBLjGelCWXZj5FgcJP7CaJZrznGCcWlKrlbRq4ymMNFSivhCfQ364uH5ODE1SwwIWN8oUaGilv9DpyVQ7E5PhrTGyR2RNBFy14x7DEtyNiwzT3R8RJnymgJyeAlvgGgdzXyTynqOKqOxzfiHlSuI7Nllon7bCqk7URtkiTvJxCTi1u";
            },
            error: function (xhr, error, code) {
                console.log(xhr)
                tabla_elementos.clear().draw();
            },
            // success: function(json){
            //     console.log(json)
            // }
        },
        buttons: [],
        "columns": [
            {
                data: "pregunta",
                render: function (data, type, row) {
                    var eliminado = row.active ? "" : "eliminado";
                    var t = '';
                    t = '<div class="pt-2 pb-2 ' + eliminado + '">' + row.pregunta + '</div>';
                    return t;
                }
            },
            {
                data: "respuesta",
                render: function (data, type, row) {
                    var eliminado = row.active ? "" : "eliminado";
                    var t = '';
                    t = '<div class="pt-2 pb-2 ' + eliminado + '">' + row.respuesta + '</div>';
                    return t;
                }
            },
            {
                data: "categoria",
                render: function (data, type, row) {
                    var eliminado = row.active ? "" : "eliminado";
                    var t = '';
                    t = '<div class="pt-2 pb-2 ' + eliminado + '">' + row.categoria + '</div>';
                    return t;
                }
            },
            {
                data: "imagen",
                render: function (data, type, row) {
                    var eliminado = row.active ? "" : "eliminado";
                    var t = '';
                    t = '<div class="pt-2 pb-2 ' + eliminado + '">' + row.imagen + '</div>';
                    return t;
                }
            }, {
                data: "etiquetas",
                render: function (data, type, row) {
                    var eliminado = row.active ? "" : "eliminado";
                    var t = '';
                    t = '<div class="pt-2 pb-2 ' + eliminado + '">' + row.etiquetas + '</div>';
                    return t;
                }
            },
            {
                data: "id",
                width: '100px',
                render: function (data, type, row) {
                    var eliminado = row.active ? "" : "eliminado";
                    var t = '';
                    t = '' +
                        '<div class="pt-2 pb-2 ' + eliminado + ' text-center">' +
                        '<a title="Editar" class="btn btn-primary btn-xs mr-2 lead_0_8 text-white" cmd="editar"><i cmd="editar" class="fa fa-edit"></i></a>' +
                        '<a title="Eliminar" class="btn btn-danger btn-xs lead_0_8 text-white" cmd="eliminar">' + (!row.active ? "Eliminar" : "Eliminar") + '</a>' +
                        '</div>';
                    return t;
                }
            }
        ]
    });
}

function EliminarPregunta(record) {
    console.log(record);
    Swal.fire({
        title: 'Preguntas',
        text: "¿Estás seguro que deseas eliminar esta pregunta?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: window.base_url + "Faqs/eliminar_pregunta",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function (response) {
                    tabla_subcategorys.ajax.reload();
                    $('#modal_subcategory').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Preguntas',
                        text: 'La pregunta fue eliminada correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    $('#modal_subcategory').modal('hide');
                    Swal.fire({
                        type: 'error',
                        title: 'Preguntas',
                        text: error_msg
                    });
                }
            });
        }
    })
}

function AgregarElemento() {
    $('#form_elemento').trigger("reset");
    $('#pregunta').val("");
    $('#respuesta').val("");
    $('#imagen').val('');
    $('#nombre_imagen').val('');
    $('#etiquetas').val(""); 
    $('#fecha_limite').val("");

    $("#div_imagen").removeClass("d-none");
    $('#id_elemento').val("")
    $('#modal_elemento').modal('show');
    editando = false
}

function EditarElemento(record) {
    $('#form_elemento').trigger("reset");

    /* $("#div_audio").addClass("d-none");
    $("#div_preview").addClass("d-none");
    $("#contenedor_capacitacion_obligatoria").addClass("d-none") */
    $('#id_elemento').val(record.id);
    $('#pregunta').val(record.pregunta);
    $('#respuesta').val(record.respuesta);
    $('#etiquetas').val(record.etiquetas);

    $('#modal_elemento').modal('show');
    categorias_faqs = [{nombre: record.categoria, id: record.id_categoria}]
    mostrarCategoriasFaqsSeleccionada()
    editando = true
    id_podcast = record.id
}

function EliminarPreguntaFaq(record) {
    var texto = record.active == 1 ? "¿Estas seguro de eliminar este elemento?" : "¿Estas seguro de eliminar este elemento?"
    var texto_boton = record.active == 1 ? "Eliminar" : "Eliminar"
    Swal.fire({
        title: 'Preguntas',
        text: texto,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, '+texto_boton,
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: window.base_url + "Faqs/eliminar_pregunta",
                type: "POST",
                data: {
                    id: record.id,
                    active : (record.active ? 0 : 1)
                },
                cache: false,
                success: function (response) {
                    tabla_elementos.ajax.reload();
                    $('#modal_elemento').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Preguntas',
                        text: 'Las Preguntas se han actualizado correctamente'
                    });
                },
                error: function () {
                    $('#modal_elemento').modal('hide');
                    Swal.fire({
                        type: 'error',
                        title: 'Preguntas',
                        text: 'La pregunta no se pudo eliminar'
                    });
                }
            });
        }
    })
}

function loadDocumento(event) {
    var url = URL.createObjectURL(event.target.files[0]);

    var file = event.target.files[0];

    $('#nombre_documento').val(file.name);
    //var output = document.getElementById('preview_imagen');
    //output.src = url;
}

function loadImagen(event) {
    var url = URL.createObjectURL(event.target.files[0]);

    var file = event.target.files[0];

    $('#nombre_imagen').val(file.name);
    //var output = document.getElementById('preview_imagen');
    //output.src = url;
}

function CatalogoPreguntas(library_id) {
    var datos = new FormData();
    if (library_id !== '') {
        datos.append('library_id', library_id);
    } else {
        $('#quiz_library').prop('disabled', false);
    }
    $('#quiz_library')
        .find('option')
        .remove()
        .end()
    var config = {
        url: window.base_url + "library/QuizLibrary",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: datos,
        success: function (response) {
            let data = response.data;
            $.each(data, function (index, value) {
                if (index === 0) {
                    $('#quiz_library').append($('<option>', {
                        value: '0',
                        text: 'Seleccionar...'
                    }));
                }
                let selected = '';
                if (library_id !== '' && value.connection_id == library_id) {
                    $('#quiz_library').prop('disabled', true);
                    selected = 'selected="selected"';
                }

                $('#quiz_library').append('<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>');
            });
        },
        error: function (response) {
        }
    }
    $.ajax(config);
}

var grupos = []
var categorias = []
var categorias_faqs = []
var editando = false
var id_podcast = undefined

function get_(id) {
    return document.getElementById(id)
}

function obtenerCategoriaFaqs() {
    $.ajax({
        url: window.base_url + "Faqs/obtener_categorias",
        type: 'POST',
        data: {

        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            categorias = json.data
            mostrarCategoriasFaqs()
        }
    })
}

function mostrarCategoriasFaqs() {
    var div = document.getElementById("contenedor_categorias")
    console.log("mostrarCategoriasFaqs");
    var html = "";
    for (var i = 0; i < categorias.length; i++) {
        var categoria = categorias[i];
        html += "<tr><td>" + (categoria.nombre || '') + "</td><td><button class='btn btn-primary' onclick='agregarCategoria(" + i + ")'>Agregar</button></td></tr>"
    }
    div.innerHTML = html;
    // div_edicion.innerHTML = html;
}

function agregarCategoria(indice) {
    //if (!editando) {
        var categoria = categorias[indice]
        console.log(categoria);
        categorias_faqs = categorias_faqs || []
        if (!categorias_faqs.some(u => { return u.id === categoria.id })) {
            if(categorias_faqs.length < 1){
                categorias_faqs.push(categoria)
            }
        }
        mostrarCategoriasFaqsSeleccionada()
   
}

function agregarTodos() {
    for (var i = 0; i < categorias.length; i++) {
        agregarCategoria(i)
    }
}

function mostrarCategoriasFaqsSeleccionada() {
    console.log("mostrarCategoriasFaqsSeleccionada");
    console.log(categorias_faqs);
    var div = get_("contenedor_categorias_faqs");
    var html = "";
    if(categorias_faqs){
        for (var i = 0; i < categorias_faqs.length; i++) {
            var categoria = categorias_faqs[i];
            // if (usuario.id !== id_usuario_actual)
            html += "<tr><td>" + categoria.nombre + "</td><td><button class='btn btn-danger' onclick='eliminarCategoriaFaqsSeleccionada(" + i + ")'>Eliminar</button></td></tr>"
        }
    }
    div.innerHTML = html;
}

function eliminarCategoriaFaqsSeleccionada(indice) {
    if (editando === false) {
        console.log("false editando");
        categorias_faqs.splice(indice, 1);
        mostrarCategoriasFaqsSeleccionada()
    } else {
        console.log("false true");
        categorias_faqs.splice(indice, 1);
        mostrarCategoriasFaqsSeleccionada()
        //eliminarTodos(categorias_faqs[indice].id, indice)
    }
}

function eliminarTodos() {
    if (editando === true) {
        var categorias_eliminar = JSON.parse(JSON.stringify(categorias_faqs))
        for (var i = 0; i < categorias_eliminar.length; i++) {
            eliminarCategoriaFaqsSeleccionada(i);
        }
    } else {
        categorias_faqs = []
    }
    mostrarCategoriasFaqsSeleccionada();
}
