var tabla_categorias = '',
    tabla_subcategorias = '',
    tabla_cursos = '',
    edicion = '';
jQuery(document).ready(function ($) {
    CategoriasSelect();
    ObtenerElearning();
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Se agregar nueva validacion para que en el formulario no
     *          pongan la misma informacion en 2 campos.
     ***********************************************************************/
    jQuery.validator.addMethod("notEqual", function (value, element, param) {
        return this.optional(element) || value != $(param).val();
    }, "Por favor selecciona un cuestionario diferente.");
    $('#form_curso').validate({
        rules: {
            // title: {
            //     required: true
            // },
            // description: {
            //     required: true
            // },
            // max_try: {
            //     required: true
            // },
            // min_score: {
            //     required: true
            // },
            // trail_url: {
            //     required: true,
            //     url: true
            // },
            // quiz_satisfaction_id: {
            //     required: true
            // },
            // quiz_final_evaluation_id: {
            //     required: true,
            //     notEqual: "#quiz_satisfaction_id"
            // },
            // category_id: {
            //     required: true
            // },
            // subcategory_id: {
            //     required: true
            // }
        },
        submitHandler: function (form) {
            // var datos = {};
            // datos.title = 
            // //console.log(datos)
            GuardarCurso();
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
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Cargar la validacion de el formulario para crear una categoria
     ***********************************************************************/
    $('#form_category').validate({
        rules: {
            category: {
                required: true
            }
        },
        submitHandler: function (form) {
            var datos = new FormData($('#form_category')[0]);
            //console.log(datos)
            GuardarCategoria(datos);
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
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Validacion de formulario para registrar una subcategoria
     ***********************************************************************/
    $('#form_subcategory').validate({
        rules: {
            category: {
                required: true
            }
        },
        submitHandler: function (form) {
            var datos = new FormData($('#form_subcategory')[0]);
            //console.log(datos)
            GuardarSubcategoria(datos);
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
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Se detecta el cambio en el select de categorias
     ***********************************************************************/
    $('#category_id').on('change', function () {
        SubcategoriasSelect(this.value, '');
    });
});
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Obtener las categorias de elearning
 ***********************************************************************/
function ObtenerTablaCategorias() {
    if (tabla_categorias !== '') {
        $('#tabla_categorias').DataTable().destroy();
    }
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
                switch (cmd) {
                    case "editar":
                        EditarCategoria(record);
                        break;
                    case "eliminar":
                        EliminarCategoria(record)
                        break;
                    case "view":
                        TablaSubcategorias(record);
                        break;
                    default:
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
                switch (cmd) {
                    case "editar":
                        EditarCategoria(record);
                        break;
                    case "eliminar":
                        EliminarCategoria(record)
                        break;
                    case "view":
                        TablaSubcategorias(record);
                        break;
                    default:
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
        "order": [[0, "asc"]],
        "ajax": {
            url: "../index.php/elearning/ListCategories",
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
                data: "category",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "id",
                width: '100px',
                render: function (data, type, row) {
                    var t = '';
                    t = '' +
                        '<div class="pt-2 pb-2 text-center">' +
                        '<a title="Ver subcategorías" class="btn btn-primary btn-xs editar mr-2 lead_0_8 text-white" cmd="view"><i cmd="view" class="fa fa-eye"></i></a>' +
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
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener el listado de elearning
 ***********************************************************************/
function ObtenerElearning() {
    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_cursos = $('#tabla_cursos').DataTable({
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
            url: "../index.php/elearning/elearningModules",
            type: 'POST',
            data: function (d) {
                //d.category_id = 7;
                //d.area_id = 10;
                //d.token = "9C4F9Z4us1fcqBx8k5I5iwdaqjg8lknHgcvgrALYdsIMZiOEA9Yi9BxD0rlbAoZAQkPk9uzoz2N6ojAz1Wa04ielphCkkwvStit77jHIeuXp6PSZRIXITBLjGelCWXZj5FgcJP7CaJZrznGCcWlKrlbRq4ymMNFSivhCfQ364uH5ODE1SwwIWN8oUaGilv9DpyVQ7E5PhrTGyR2RNBFy14x7DEtyNiwzT3R8RJnymgJyeAlvgGgdzXyTynqOKqOxzfiHlSuI7Nllon7bCqk7URtkiTvJxCTi1u";
            },
            error: function (xhr, error, code) {
                console.log(xhr)
                tabla_cursos.clear().draw();
            }
        },
        buttons: [],
        "columns": [
            {
                data: "title",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "description",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "trail_url",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "max_try",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "min_score",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "name_satisfaction",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "name_evaluation",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "category_name",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "subcategory_name",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "fecha_limite",
                render: function (data, type, row) {
                    var t = '';
                    var fecha = ""
                    if (row.fecha_limite != "") {
                        var date = new Date(row.fecha_limite)
                        var day = ("0" + date.getDate()).slice(-2);
                        var month = ("0" + (date.getMonth() + 1)).slice(-2);

                        fecha = date.getFullYear() + "-" + (month) + "-" + (day);
                    }
                    t = '<div class="pt-2 pb-2">' + fecha + '</div>';
                    return t;
                }
            },
            {
                data: "id",
                width: '100px',
                render: function (data, type, row) {
                    var t = '';
                    t = '' +
                        '<div class="pt-2 pb-2 text-center">' +
                        '<a title="Editar" class="btn btn-primary btn-xs mr-2 lead_0_8 text-white" cmd="editar"><i cmd="editar" class="fa fa-edit"></i></a>' +
                        '<a title="Eliminar" class="btn btn-danger btn-xs lead_0_8 text-white" cmd="eliminar"><i cmd="eliminar" class="fa fa-times"></i></a>' +
                        '</div>';
                    return t;
                }
            }
        ]
    });
    $('#tabla_cursos tbody').on('click', 'a', function (e) {
        var record = tabla_cursos.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (es_boton) {
            if (cmd == "editar") {
                EditarElearning(record);
            }
            if (cmd == "eliminar") {
                EliminarElearning(record)
            }
        }
    });
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para agregar un curso nuevo
 ***********************************************************************/
function AgregarCurso() {
    $('#form_curso').trigger("reset");
    CatalogoPreguntas('', '', '');
    $('#id_curso').val('');
    SubcategoriasSelect(0, '')
    $("#contenedor_capacitacion_obligatoria").removeClass("d-none");
    $('#modal_curso').modal('show');
    edicion = '';
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para guardar un curso
 ***********************************************************************/
function get(id) {
    return document.getElementById(id).value
}

function GuardarCurso(datos_) {
    
    if(edicion == '' && get("capacitacion_obligatoria") == -1){
        alert("Selecciona un tipo de elemento")
        return
    }
    console.log(datos_)
    var datos = new FormData()
    
    // datos.append("datos", datos_)
    var archivo = document.getElementById('zip').files[0]
    console.log(document.getElementById('zip').files)
    console.log(archivo)
    // datos.append("algo","algun valor");
    datos.append("title", get("title"));
    datos.append("description", get("description"));
    datos.append("max_try", get("max_try"));
    datos.append("min_score", get("min_score"));
    datos.append("trail_url", get("trail_url"));
    datos.append("quiz_satisfaction_id", get("quiz_satisfaction_id"));
    datos.append("quiz_final_evaluation_id", get("quiz_final_evaluation_id"));
    datos.append("category_id", get("category_id"));
    datos.append("subcategory_id", get("subcategory_id"));
    datos.append('archivo', archivo);
    datos.append("fecha_limite", get("fecha_limite"))
    datos.append("capacitacion_obligatoria",get("capacitacion_obligatoria"))

    if (edicion !== '') {
        datos.append('id', edicion);
        datos.delete("capacitacion_obligatoria")
    }
    // for (var key of datos.entries()) {
    //     console.log(key[0] + ', ' + key[1]);
    // }

    $.ajax({
        url: window.base_url + "elearning/SaveElearning",
        type: "POST",
        contentType: false,
        data: datos,
        dataType: 'json',
        cache: false,
        processData: false,

        success: function (response) {
            console.log(response)
            Swal.fire({
                type: 'success',
                title: 'Capacitación',
                text: response.msg
            }).then((result) => {
                $('#form_curso').trigger("reset");
                $('#modal_curso').modal('hide');
                tabla_cursos.ajax.reload()
            });
        },
        error: function (response) {
            console.log(response)
            Swal.fire({
                type: 'error',
                title: 'Capacitación',
                text: response.responseJSON.error_msg
            });
        }
    })
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para editar un elearning seleccionada
 ***********************************************************************/
function EditarElearning(record) {
    $('#form_curso').trigger("reset");
    $('#modal_curso').modal('show');
    edicion = record.id;
    $("#title").val(record.title);
    $("#description").val(record.description);
    $("#max_try").val(record.max_try);
    $("#min_score").val(record.min_score);
    $("#trail_url").val(record.trail_url);
    $("#contenedor_capacitacion_obligatoria").addClass("d-none")
    //$("#quiz_satisfaction_id").val(record.quiz_satisfaction_id);
    //$("#quiz_final_evaluation_id").val(record.quiz_final_evaluation_id);
    var fecha = ""
    if (record.fecha_limite != "") {
        var date = new Date(record.fecha_limite)
        var day = ("0" + date.getDate()).slice(-2);
        var month = ("0" + (date.getMonth() + 1)).slice(-2);

        fecha = date.getFullYear() + "-" + (month) + "-" + (day);
        $("#fecha_limite").val(fecha)
    }
    
    $("#category_id").off("change");
    $("#category_id").val(record.category_id).change();
    $('#category_id').on('change', function () {
        SubcategoriasSelect(this.value, '');
    });
    //$("#subcategory_id").val(record.subcategory_id);
    SubcategoriasSelect(record.category_id, record.subcategory_id)
    CatalogoPreguntas(record.id, record.quiz_satisfaction_id, record.quiz_final_evaluation_id);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para eliminar un elearning seleccionada.
 ***********************************************************************/
function EliminarElearning(record) {
    Swal.fire({
        title: 'Capacitación',
        text: "¿Estás seguro que deseas eliminar esta capacitación?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: window.base_url + "elearning/DeleteElearning",
                type: "POST",
                data: {
                    id: record.id,
                    active: 0
                },
                cache: false,
                success: function (response) {
                    tabla_categorias.ajax.reload();
                    Swal.fire({
                        type: 'success',
                        title: 'Capacitación',
                        text: response.msg
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    Swal.fire({
                        type: 'error',
                        title: 'Capacitación',
                        text: error_msg
                    });
                }
            });
        }
    })
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener el catalogo de preguntas que se le puede
 *          asignar a un curso.
 ***********************************************************************/
function CatalogoPreguntas(elearning_id, quiz_satisfaction_id, quiz_final_evaluation_id) {
    var datos = new FormData();
    if (elearning_id !== '') {
        datos.append('elearning_id', elearning_id);
    } else {
        $('#quiz_satisfaction_id').prop('disabled', false);
        $('#quiz_final_evaluation_id').prop('disabled', false);
    }
    $('#quiz_satisfaction_id')
        .find('option')
        .remove()
        .end();
    $('#quiz_final_evaluation_id')
        .find('option')
        .remove()
        .end();
    var config = {
        url: window.base_url + "elearning/QuizLibrary",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: datos,
        success: function (response) {
            let data = response.data;
            $.each(data, function (index, value) {
                if (index === 0) {
                    $('#quiz_satisfaction_id').append($('<option>', {
                        value: '',
                        text: 'Seleccionar...'
                    }));
                    $('#quiz_final_evaluation_id').append($('<option>', {
                        value: '',
                        text: 'Seleccionar...'
                    }));
                }
                let selected = '';
                if (elearning_id !== '' && value.id == quiz_satisfaction_id) {
                    $('#quiz_satisfaction_id').prop('disabled', true);
                    selected = 'selected="selected"';
                }
                $('#quiz_satisfaction_id').append('<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>');

                let selected_ = '';
                if (elearning_id !== '' && value.id == quiz_final_evaluation_id) {
                    $('#quiz_final_evaluation_id').prop('disabled', true);
                    selected_ = 'selected="selected"';
                }
                $('#quiz_final_evaluation_id').append('<option value="' + value.id + '" ' + selected_ + '>' + value.name + '</option>');
            });
        },
        error: function (response) {
            console.log(response)
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener las categorias para cargar el select
 ***********************************************************************/
function CategoriasSelect() {
    var datos = new FormData();
    $('#category_id')
        .find('option')
        .remove()
        .end()
    var config = {
        url: window.base_url + "elearning/ListCategories",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: datos,
        success: function (response) {
            let data = response.data;
            $.each(data, function (index, value) {
                if (index === 0) {
                    $('#category_id').append($('<option>', {
                        value: '',
                        text: 'Seleccionar...'
                    }));
                    $('#category_id').append($('<option>', {
                        value: '0',
                        text: 'Todos'
                    }));
                }
                $('#category_id').append('<option value="' + value.id + '">' + value.category + '</option>');
            });
        },
        error: function (response) {
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener las subcategorias en base a la categoria
 *          seleccionada
 ***********************************************************************/
function SubcategoriasSelect(category_id, select_id) {
    var datos = new FormData();
    $('#subcategory_id')
        .find('option')
        .remove()
        .end();
    datos.append('category_id', category_id);
    var config = {
        url: window.base_url + "elearning/ListSubcategories",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: datos,
        success: function (response) {
            let data = response.data;
            $.each(data, function (index, value) {
                if (index === 0) {
                    $('#subcategory_id').append($('<option>', {
                        value: '',
                        text: 'Seleccionar...'
                    }));
                    $('#subcategory_id').append($('<option>', {
                        value: '0',
                        text: 'Todos'
                    }));
                }
                let selected = '';
                if (value.id == select_id) {
                    selected = 'selected="selected"';
                }
                $('#subcategory_id').append('<option value="' + value.id + '" ' + selected + '>' + value.subcategory + '</option>');
            });
        },
        error: function (response) {
            console.log(response)
            $('#subcategory_id').append($('<option>', {
                value: '',
                text: 'Seleccionar...'
            }));
            if (select_id !== '') {
                $('#subcategory_id').append('<option value="0" selected="selected">Todos</option>');
            } else {
                $('#subcategory_id').append($('<option>', {
                    value: '0',
                    text: 'Todos'
                }));
            }
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener las categorias de un elearning
 ***********************************************************************/
function TablaSubcategorias(record_category) {
    if (tabla_subcategorias !== '') {
        $('#tabla_subcategorias').DataTable().destroy();
    }
    $('#category_id_edicion').val(record_category.id);
    $('#tabla_subcategorias').on('click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_subcategorias.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                switch (cmd) {
                    case "editar":
                        EditarSubcategoria(record);
                        break;
                    case "eliminar":
                        EliminarSubcategoria(record)
                        break;
                    default:
                }
            }
        }
    });
    $('#tabla_subcategorias').on('tbody click', 'tr', function (e) {
        var record = tabla_subcategorias.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                switch (cmd) {
                    case "editar":
                        EditarSubcategoria(record);
                        break;
                    case "eliminar":
                        EliminarSubcategoria(record)
                        break;
                    default:
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_subcategorias = $('#tabla_subcategorias').DataTable({
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
        "order": [[0, "asc"]],
        "ajax": {
            url: "../index.php/elearning/ListSubcategories",
            type: 'POST',
            data: function (d) {
                d.category_id = record_category.id;
                //d.token = "9C4F9Z4us1fcqBx8k5I5iwdaqjg8lknHgcvgrALYdsIMZiOEA9Yi9BxD0rlbAoZAQkPk9uzoz2N6ojAz1Wa04ielphCkkwvStit77jHIeuXp6PSZRIXITBLjGelCWXZj5FgcJP7CaJZrznGCcWlKrlbRq4ymMNFSivhCfQ364uH5ODE1SwwIWN8oUaGilv9DpyVQ7E5PhrTGyR2RNBFy14x7DEtyNiwzT3R8RJnymgJyeAlvgGgdzXyTynqOKqOxzfiHlSuI7Nllon7bCqk7URtkiTvJxCTi1u";
            },
            error: function (xhr, error, code) {
                tabla_subcategorias.clear().draw();
            }
        },
        buttons: [],
        "columns": [
            {
                data: "subcategory",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "id",
                width: '100px',
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
    $('#modal_subcategorias').modal('show');
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para guardar o actualizar una categoria
 ***********************************************************************/
function GuardarCategoria(datos) {
    if ($('#category_id_edicion').val() !== '') {
        datos.append('id', $('#category_id_edicion').val());
    }
    var config = {
        url: window.base_url + "elearning/SaveCategory",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: datos,
        success: function (response) {
            Swal.fire({
                type: 'success',
                title: 'Capacitación',
                text: response.msg
            }).then((result) => {
                $('#form_category').trigger("reset");
                $('#modal_categoria').modal('hide');
                tabla_categorias.ajax.reload();
                CategoriasSelect();
            });
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Capacitación',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para cargar modal para un registro nuevo
 ***********************************************************************/
function AgregarCategoria() {
    $('#form_category').trigger("reset");
    $('#category_id_edicion').val('');
    $('#modal_categoria').modal('show');
}

/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para editar una categoria seleccionada
 ***********************************************************************/
function EditarCategoria(record) {
    $('#form_categoria').trigger("reset");
    $('#category_id_edicion').val(record.id);
    $('#category').val(record.category);
    $('#modal_categoria').modal('show');
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para eliminar una categoria seleccionada.
 ***********************************************************************/
function EliminarCategoria(record) {
    Swal.fire({
        title: 'Categoría',
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
                url: window.base_url + "elearning/SaveCategory",
                type: "POST",
                data: {
                    id: record.id,
                    active: 0
                },
                cache: false,
                success: function (response) {
                    tabla_categorias.ajax.reload();
                    Swal.fire({
                        type: 'success',
                        title: 'Categoría',
                        text: response.msg
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
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
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para registrar o actuliazar una subcategoria
 ***********************************************************************/
function GuardarSubcategoria(datos) {
    if ($('#subcategory_id_edicion').val() !== '') {
        datos.append('id', $('#subcategory_id_edicion').val());
    }
    datos.append('category_id', $('#category_id_edicion').val());
    var config = {
        url: window.base_url + "elearning/SaveSubcategory",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: datos,
        success: function (response) {
            Swal.fire({
                type: 'success',
                title: 'Capacitación',
                text: response.msg
            }).then((result) => {
                $('#form_subcategory').trigger("reset");
                $('#modal_subcategoria').modal('hide');
                tabla_subcategorias.ajax.reload();
            });
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Capacitación',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para cargar modal para un registro nuevo
 ***********************************************************************/
function AgregarSubcategoria() {
    $('#form_subcategory').trigger("reset");
    $('#subcategory_id_edicion').val('');
    $('#modal_subcategoria').modal('show');
}

/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para editar una categoria seleccionada
 ***********************************************************************/
function EditarSubcategoria(record) {
    $('#form_subcategory').trigger("reset");
    $('#subcategory_id_edicion').val(record.id);
    $('#subcategory').val(record.category);
    $('#modal_subcategoria').modal('show');
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para eliminar una categoria seleccionada.
 ***********************************************************************/
function EliminarSubcategoria(record) {
    Swal.fire({
        title: 'Categoría',
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
                url: window.base_url + "elearning/SaveSubcategory",
                type: "POST",
                data: {
                    id: record.id,
                    active: 0
                },
                cache: false,
                success: function (response) {
                    tabla_categorias.ajax.reload();
                    Swal.fire({
                        type: 'success',
                        title: 'Categoría',
                        text: response.msg
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
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
