var tabla_categorias,
    tabla_subcategorys,
    tabla_elementos,
    tabla_preguntas = '',
    subcategory_id = '';
var filtro = "";

jQuery(document).ready(function ($) {
    obtenerUsuarios();
    ObtenerTablaUsuariosConPolizas();
    if (rol_id != 2 && rol_id != 5) {
        ocultar_select_regiones()
    }

    $('#form_elemento').validate({
        rules: {
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
        },
        submitHandler: function (form) {
            //console.log(datos)

            guardarPoliza();
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
    $('#select_filtro').on('change', function () {
        filtro = $(this).val();
        $('#tabla_elementos').DataTable().ajax.reload();
        //ObtenerTablaUsuariosConPolizas();
    });


});

function ocultar_select_regiones() {
    get("regiones").style.display = "none";
    get("asesores").style.display = "none"
}

function obtenerRegiones() {
    $.ajax({
        url: window.base_url + "Ws/ObtenerRegiones",
        type: "POST",
        data: {},
        cache: false,
        success: function (response) {
            console.log(response)
            generarSelectRegiones(response.data)
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function generarSelectRegiones(regiones) {
    var select = get("select_regiones_library")
    var html = '<option>Todas...</option>'
    for (var i = 0; i < regiones.length; i++) {
        html += "<option value='" + regiones[i].id + "'>" + regiones[i].nombre + "</option>"
    }
    select.innerHTML = html
}

function get(id) {
    return document.getElementById(id)
}

function ObtenerTablaUsuariosConPolizas() {
    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
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
                    EliminarElemento(record)
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
                    EliminarElemento(record)
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
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
            url: "../index.php/Polizas/listUserWithPoliza",
            type: 'POST',
            data: function (d) {
                d.filtro = filtro;
                //d.category_id = 7;
                d.area_id = 10;
                //d.token = "9C4F9Z4us1fcqBx8k5I5iwdaqjg8lknHgcvgrALYdsIMZiOEA9Yi9BxD0rlbAoZAQkPk9uzoz2N6ojAz1Wa04ielphCkkwvStit77jHIeuXp6PSZRIXITBLjGelCWXZj5FgcJP7CaJZrznGCcWlKrlbRq4ymMNFSivhCfQ364uH5ODE1SwwIWN8oUaGilv9DpyVQ7E5PhrTGyR2RNBFy14x7DEtyNiwzT3R8RJnymgJyeAlvgGgdzXyTynqOKqOxzfiHlSuI7Nllon7bCqk7URtkiTvJxCTi1u";
            },
            error: function (xhr, error, code) {
                console.log(xhr)
                tabla_elementos.clear().draw();
            }
        },
        buttons: [],
        "columns": [
            {
                data: "nombre",
                render: function (data, type, row) {
                    var eliminado = true ? "" : "eliminado";
                    var t = '';
                    t = '<div class="pt-2 pb-2 ' + eliminado + '">' + row.nombre + '</div>';
                    return t;
                }
            },
            {
                "data": "id",
                render: function (data, type, full, meta) {
                    let html =
                       /*  '<a title="Ver preguntas" class="btn btn-info btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="Preguntas(' + data + ')"><i class="fa fa-search"></i></a>' + */
                        '<a title="Ver preguntas" class="btn btn-info btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="TablaPolizasUsuario(' + data + ')"><i class="fa fa-eye"></i></a>'
                        /* '<a title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="EditarCatalogo(' + data + ')"><i class="fa fa-edit"></i></a>' + */
                        /* '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8"  href="javascript:void(0)" onclick="EliminarCatalogo(' + data + ')"><i class="fa fa-times"></i></a>'; */
                    return html;
                }
            }
        ]
    });
}

function agregarPoliza() {
    $('#form_elemento').trigger("reset");

    $("#div_tipo_video").addClass("d-none");
    $("#div_video").addClass("d-none");
    $("#div_documento").addClass("d-none");
    $("#div_imagen").addClass("d-none");
    $("#div_video_id").addClass("d-none");
    $("#div_link").addClass("d-none");
    $("#contenedor_capacitacion_obligatoria").removeClass("d-none");
    $('#id_elemento').val('');

    $('#modal_elemento').modal('show');
    //resetear elementos
    $('#titulo').val('')
    $('#texto').val('')
    $('#etiquetas').val('')
    $('#id_categoria_elemento').val('')
    $('#id_subcategory_elemento').val('')
    $('#nombre_preview').val('')
    $('#tipo').val('')
    $('#tipo_video').val('')
    $('#video').val('')
    $('#nombre_video').val('')
    $('#documento').val('')
    $('#nombre_documento').val('')
    $('#imagen').val('')
    $('#nombre_imagen').val('')
    $('#video_id').val('')
    $('#link').val('')
    $('#quiz_library').val('')
    $('#fecha_limite').val('')
    editando = false
    id_library = undefined
}

function guardarPoliza() {
    var id_elemento = $('#id_elemento').val() || '',
        datos = new FormData(),
        type = "documento",
        file = "", link = "", type_video = "", video = "";
    document.getElementById("loader_background").style.display = 'inherit';
    document.getElementById("loader").style.display = 'inherit';
    if (id_elemento != '') {
        datos.append('id', id_elemento);
        metodo = "EditElement";
        datos.delete("capacitacion_obligatoria")
    }
    file = $("#documento").prop('files').length != 0 ? $("#documento").prop('files')[0] : null;
    datos.append('type', type);
    datos.append('file', file);
    datos.append('usuarios', JSON.stringify(usuarios_library));
    console.log( JSON.stringify(usuarios_library));
    $.ajax({
        url: window.base_url + "Polizas/guardarPoliza",
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

function loadPreview(event) {
    var url = URL.createObjectURL(event.target.files[0]);
    var file = event.target.files[0];
    $('#nombre_preview').val(file.name);
}

function loadDocumento(event) {
    var url = URL.createObjectURL(event.target.files[0]);

    var file = event.target.files[0];

    $('#nombre_documento').val(file.name);
}

function TablaPolizasUsuario(id) {
    console.log(id);
    if (tabla_preguntas !== '') {
        $('#preguntas_tabla').DataTable().destroy();
    }
    tabla_preguntas = $('#preguntas_tabla').dataTable({
        pageLength: 20,
        dom: 'Bfrtip',
        buttons: ['excel'],
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
        "order": [[0, "asc"]],
        "columns": [
            {
                "data": "url",
                render: function (data, type, full, meta) {
                    let html = '';
                    html = '<div class="pt-2 pb-2 d-flex justify-content-center ' + "" + '"><a href="' + data + '" target="_blank">Ver...<a><p></div>';
                    return html;
                }
            },
            { "data": "created_at" },
            {
                "data": "id",
                render: function (data, type, full, meta) {
                    let html = '';
                    html = '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8"  href="javascript:void(0)" onclick="eliminarPoliza(' + data + ')"><i class="fa fa-times"></i></a>';
                    return html;
                }
            }
        ],
        "ajax": {
            "url": window.base_url + "Polizas/getPolizasByUserId",
            "type": 'POST',
            "data": function (d) {
                d.user_id = id;
            },
            error: function (xhr, error, code) {
                tabla_preguntas.api().clear().draw();
            }
        }
    });
    $('#modal_preguntas_detalle').modal('show');
}

function eliminarPoliza(data){
    console.log(data);
    $.ajax({
        url: window.base_url + "Polizas/eliminarPoliza",
        type: 'POST',
        data: {
            id: data
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            console.log(json)
            tabla_elementos.ajax.reload();
            $('#modal_preguntas_detalle').modal('hide');
            $('#modal_elemento').modal('hide');
            document.getElementById("loader_background").style.display = 'none';
            document.getElementById("loader").style.display = 'none';
            Swal.fire({
                type: 'success',
                title: 'Polizas',
                text: 'Las polizas han sido actualizado correctamente'
            });
        }
    })
}

var grupos = []
var usuarios = []
var usuarios_library = []
var editando = false
var id_library = undefined


function get_(id) {
    return document.getElementById(id)
}


function obtenerUsuarios() {
    $.ajax({
        url: window.base_url + "User/UserList",
        type: 'POST',
        data: {

        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            usuarios = json.data
            mostrarUsuarios()
        }
    })
}

function mostrarUsuarios() {
    var div = document.getElementById("contenedor_usuarios")
    var div_edicion = get_("contenedor_usuarios_edicion")
    var html = "";
    for (var i = 0; i < usuarios.length; i++) {
        var usuario = usuarios[i];
        // if (usuario.id !== id_usuario_actual)
        html += "<tr><td>" + (usuario.name || '') + " " + (usuario.last_name || '') + "</td><td><button class='btn btn-primary' onclick='agregarUsuario(" + i + ")'>Agregar</button></td></tr>"
    }
    // div.innerHTML = html;
    // div_edicion.innerHTML = html;
}

function agregarUsuario(id) {
    if (editando === false) {
        var usuario = usuarios.filter(f => f.id == id)[0]
        usuarios_library = usuarios_library || []
        if (!usuarios_library.some(u => { return u.id === usuario.id })) {
            usuarios_library.push(usuario)
        }
        mostrarUsuarios()
    } else {
        if (!usuarios_library.some(u => { return u.id === id })) {
            var usuario = usuarios.filter(f => f.id == id)[0]
            agregarUsuarioACapacitacion(id, usuario)
        }
    }
}

function agregarTodos() {
    // if (editando == false) {
    for (var i = 0; i < usuarios.length; i++) {
        agregarUsuario(i)
    }
    // } else {

    // }
}

function mostrarUsuarios() {
    var div = get_("contenedor_usuarios_library");
    // var div_edicion = get("contenedor_usuarios_library_edicion")
    var html = "";
    for (var i = 0; i < usuarios_library.length; i++) {
        var usuario = usuarios_library[i];
        // if (usuario.id !== id_usuario_actual)
        html += "<tr><td>" + usuario.name + " " + usuario.last_name + "</td><td><button class='btn btn-danger' onclick='eliminarUsuario(" + i + ")'>Eliminar</button></td></tr>"
    }
    div.innerHTML = html;
    // div_edicion.innerHTML = html;
}

function mostrarUsuariosFiltrados() {
    var filtrados = []
    var div = undefined
    var html = ""
    // if (editando === true) {
    //     div = get("contenedor_usuarios")
    //     var filtro = get("buscador_edicion").value
    // } else {
    div = get_("contenedor_usuarios")
    var filtro = get_("buscador").value
    // }
    filtrados = usuarios.filter(f => (f.number_employee + "").substring(1) === filtro || (f.name + " " + f.last_name).toLowerCase().includes(filtro.toLowerCase()))
    for (var i = 0; i < filtrados.length; i++) {
        var usuario = filtrados[i]
        html += "<tr><td>" + usuario.name + " " + usuario.last_name + "</td><td><button class='btn btn-primary' onclick='agregarUsuario(" + usuario.id + ")'>Agregar</button></td></tr>"
    }
    div.innerHTML = html
}
