var tabla_categorias,
    tabla_feedback;
var feedback;
var feedback_filtrado;
jQuery(document).ready(function ($) {
    ObtenerTablaCategorias();
    // ObtenerTablaFeedback();
    obtener_feedback()
    obtener_categorias()
    obtener_grupos()

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
});

function obtener_feedback() {
    var config = {
        url: "../index.php/Feedback/FeedbackList",
        type: "POST",
        data: { type: 3 },
        success: function (response) {
            console.log(response)
            feedback = JSON.parse(JSON.stringify(response.data))
            feedback_filtrado = response.data
            generar_tabla_feedback()
        },
        error: function (response) {
            console.log(response)
        }
    }
    $.ajax(config);
}

function obtener_categorias() {
    var config = {
        url: "../index.php/Feedback/CategoryFeedback",
        type: "POST",
        data: {},
        success: function (response) {
            console.log(response)
            generar_select_categorias(response.data)
        },
        error: function (response) {
            console.log(response)
        }
    }
    $.ajax(config);
}

function generar_select_categorias(data) {
    var select = get("select_categorias")
    var html = "<option value=''>Todas</option>"
    for (var i = 0; i < data.length; i++) {
        html += "<option value='" + data[i].id + "'>" + data[i].description + "</option>"
    }
    select.innerHTML = html
}

function obtener_grupos() {
    var config = {
        url: window.base_url + "Groups/GroupsRegister",
        type: "POST",
        data: {},
        success: function (response) {
            console.log(response)
            generar_select_grupos(response.data)
        },
        error: function (response) {
            console.log(response)
        }
    }
    $.ajax(config);
}

function generar_select_grupos(data) {
    var select = get("select_grupos")
    var html = "<option value=''>Todos</option>"
    for (var i = 0; i < data.length; i++) {
        html += "<option value='" + data[i].id + "'>" + data[i].name + "</option>"
    }
    select.innerHTML = html
}

function get(id) {
    return document.getElementById(id)
}

/*************CATEGORIAS**************/
/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Creacion de la tabla para listar categorías de elementos de biblioteca
 ***********************************************************************/
function ObtenerTablaCategorias() {
    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_categorias').on('click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_categorias.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarCategoria(record);
                }
                if (cmd == "eliminar") {
                    EliminarCategoria(record)
                }
            }
        }
    });
    $('#tabla_categorias').on('tbody click', 'tr', function (e) {
        var record = tabla_categorias.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarCategoria(record);
                }
                if (cmd == "eliminar") {
                    EliminarCategoria(record)
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_categorias = $('#tabla_categorias').DataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
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
            url: "../index.php/Feedback/CategoryFeedback",
            type: 'POST',
            data: function (d) {
                //d.token = "9C4F9Z4us1fcqBx8k5I5iwdaqjg8lknHgcvgrALYdsIMZiOEA9Yi9BxD0rlbAoZAQkPk9uzoz2N6ojAz1Wa04ielphCkkwvStit77jHIeuXp6PSZRIXITBLjGelCWXZj5FgcJP7CaJZrznGCcWlKrlbRq4ymMNFSivhCfQ364uH5ODE1SwwIWN8oUaGilv9DpyVQ7E5PhrTGyR2RNBFy14x7DEtyNiwzT3R8RJnymgJyeAlvgGgdzXyTynqOKqOxzfiHlSuI7Nllon7bCqk7URtkiTvJxCTi1u";
            },
            error: function (xhr, error, code) {
                tabla_categorias.clear().draw();
            }
        },
        buttons: [],
        "columns": [
            {
                data: "description",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + row.description + '</div>';
                    return t;
                }
            },
            {
                width: '100px',
                data: "id",
                render: function (data, type, row) {
                    var t = '';
                    t = '' +
                        '<div class="pt-2 pb-2 text-center">' +
                        '<a title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8 text-white" cmd="editar"><i cmd="editar" class="fa fa-edit"></i></a>' +
                        '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8 text-white" cmd="eliminar"><i cmd="eliminar" class="fa fa-times"></i></a>' +
                        '</div>';
                    return t;
                }
            }
        ]
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para agregar una nueva categoría de elementos de biblioteca, se muestra el formulario
 ***********************************************************************/
function AgregarCategoria() {
    $('#form_categoria').trigger("reset");
    $('#id_categoria').val('');
    $('#modal_categoria').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para editar una categoría de elementos de biblioteca, se carga y muestra el formulario
 ***********************************************************************/
function EditarCategoria(record) {
    $('#form_categoria').trigger("reset");
    $('#id_categoria').val(record.id);
    $('#nombre_categoria').val(record.description);
    $('#modal_categoria').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar por peticion AJAX una categoría de elementos de biblioteca(agregar o editar)
 ***********************************************************************/
function GuardarCategoria() {
    var id_categoria = $('#id_categoria').val(),
        datos = {
            description: $('#nombre_categoria').val()
        },
        metodo = "SaveCategory";

    if (id_categoria != '') {
        datos.id = id_categoria;
        metodo = "EditCategory"
    }

    $.ajax({
        url: window.base_url + "Feedback/" + metodo,
        type: "POST",
        data: datos,
        cache: false,
        success: function (response) {
            tabla_categorias.ajax.reload();
            $('#modal_categoria').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Catálogo categorías',
                text: 'El catálogo de categorías ha sido actualizado correctamente'
            });
        },
        error: function () {
            $('#modal_categoria').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Catálogo categorías',
                text: 'La categoría no se pudo guardar'
            });
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para eliminar una categoría de elementos de biblioteca
 ***********************************************************************/
function EliminarCategoria(record) {
    Swal.fire({
        title: 'Catálogo categorías',
        text: "¿Estás seguro que deseas eliminar esta categoría?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: window.base_url + "Feedback/DeleteCategory",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function (response) {
                    tabla_categorias.ajax.reload();
                    $('#modal_categoria').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Catálogo categorías',
                        text: 'El catálogo de categorías ha sido actualizado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    $('#modal_categoria').modal('hide');
                    Swal.fire({
                        type: 'error',
                        title: 'Catálogo categorías',
                        text: error_msg
                    });
                }
            });
        }
    })
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Creacion de la tabla para listar feedback de elementos de biblioteca
 ***********************************************************************/
function ObtenerTablaFeedback() {
    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_feedback').on('click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_feedback.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "eliminar") {
                    EliminarFeedback(record)
                }
            }
        }
    });
    $('#tabla_feedback').on('tbody click', 'tr', function (e) {
        var record = tabla_feedback.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "eliminar") {
                    EliminarFeedback(record)
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_feedback = $('#tabla_feedback').DataTable({
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
        "order": [[0, "desc"]],
        "ajax": {
            url: "../index.php/feedback/FeedbackList",
            type: 'POST',
            data: function (d) {
                d.type = 3;
            },
            error: function (xhr, error, code) {
                tabla_feedback.clear().draw();
            }
        },
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            autoFilter: true,
            sheetName: 'Exported data'
        }],
        "columns": [{
            data: "name_owner",
            render: function (data, type, row) {
                var t = '';
                t = '<div class="pt-2 pb-2">' + data + '</div>';
                return t;
            }
        }, {
            data: "name_user",
            render: function (data, type, row) {
                var t = '';
                t = '<div class="pt-2 pb-2">' + data + '</div>';
                return t;
            }
        }, {
            data: "description",
            render: function (data, type, row) {
                var t = '';
                t = '<div class="pt-2 pb-2">' + data + '</div>';
                return t;
            }
        },
        /*{
            data: "media_path",
            render: function ( data, type, row ) {
                var t = '';
                if(data === ''){
                    t = '<div class="pt-2 pb-2">No registrado</div>';
                }else{
                    t = '<div class="pt-2 pb-2"><a target="_blank" href="'+data+'">Ver...</a></div>';
                }
                return t;
            }
        }
        ,*/{
            data: "file_path",
            render: function (data, type, row) {
                var t = '';
                if (data === '') {
                    t = '<div class="pt-2 pb-2">No registrado</div>';
                } else {
                    t = '<div class="pt-2 pb-2"><a target="_blank" href="' + data + '">Ver...</a></div>';
                }
                return t;
            }
        }, {
            data: "name_category",
            render: function (data, type, row) {
                return '<div class="pt-2 pb-2">' + data + '</div>';
            }
        }, {
            data: "total_like",
            render: function (data, type, row) {
                var t = '';
                t = '<div class="pt-2 pb-2">' + data + '</div>';
                return t;
            }
        }, {
            data: "id",
            render: function (data, type, row) {
                var t = '';
                t = '' +
                    '<div class="pt-2 pb-2 text-center">' +
                    '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8 text-white" cmd="eliminar"><i cmd="eliminar" class="fa fa-times"></i></a>' +
                    '</div>';
                return t;
            }
        }]
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para eliminar un área de elementos de biblioteca
 ***********************************************************************/
function EliminarFeedback(record) {
    Swal.fire({
        title: 'Catálogo feedback',
        text: "¿Estás seguro que deseas eliminar esta área?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: window.base_url + "Feedback/DeleteFeedback",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function (response) {
                    tabla_feedback.ajax.reload();
                    $('#modal_feedback').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Catálogo feedback',
                        text: 'El catálogo feedback ha sido actualizado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    $('#modal_feedback').modal('hide');
                    Swal.fire({
                        type: 'error',
                        title: 'Catálogo feedback',
                        text: error_msg
                    });
                }
            });
        }
    })
}

function generar_tabla_feedback() {
    var tabla = get("contenedor_feedback")
    data = feedback_filtrado;
    var html = ""
    for (var i = 0; i < data.length; i++) {
        html += "<tr>"
        html += "<td>" + data[i].name_owner + "</td>"
        html += "<td>" + data[i].name_user + "</td>"
        html += "<td>" + data[i].description + "</td>"
        var t = '';
        if (data[i].file_path === '') {
            t = '<div class="pt-2 pb-2">No registrado</div>';
        } else {
            t = '<div class="pt-2 pb-2"><a target="_blank" href="' + data[i].file_path + '">Ver...</a></div>';
        }
        html += "<td>" + t + "</td>"
        html += "<td>" + data[i].name_category + "</td>"
        html += "<td>" + data[i].total_like + "</td>"
        html += "<td><button class='btn btn-danger' onclick='EliminarFeedback(" + data[i] + ")'><i class='fa fa-times'></i></button></td>"
        html += "</tr>"
    }
    tabla.innerHTML = html
}

function filtrar() {
    //filtro por nombre, categoria, grupo, fecha

    feedback_filtrado = feedback.filter(filtro_valido)
    console.log(feedback_filtrado)
    generar_tabla_feedback()
}

function filtro_valido(f) {
    var nombre = get("filtro_usuario").value.toLowerCase()
    var categoria = get("select_categorias").value.toLowerCase()
    var grupo = get("select_grupos").value.toLowerCase()
    var fecha_inicio = new Date(get("fecha_inicio").value).getTime()
    var fecha_fin = new Date(get("fecha_fin").value).getTime()

    if (!fecha_inicio && !fecha_fin && nombre == '' && grupo == '' && categoria == '')
        return true;
    var fecha = new Date(f.fecha).getTime()
    var bandera = true;
    if (nombre !== '')
        bandera = bandera && (f.name_owner.toLowerCase().includes(nombre) || f.name_user.toLowerCase().includes(nombre))
    if (categoria != '')
        bandera = bandera && f.category_id == categoria
    if (grupo != '')
        bandera = bandera && (f.grupos_owner.some(s => s.group_id == grupo) || f.grupos_user.some(s => s.group_id == grupo))
    if (fecha_inicio && fecha_fin)
        bandera = bandera && (fecha >= fecha_inicio && fecha <= fecha_fin)
    return bandera
}