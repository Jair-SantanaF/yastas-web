var tabla_categorias,
    tabla_subcategorys,
    tabla_elementos,
    subcategory_id = '';
var filtro = "";

/* 
|---------------------------------------------------------------
| Variable para traduccion de las datatables
|---------------------------------------------------------------
*/
var idiomaDataTable = {
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
};

jQuery(document).ready(function ($) {
    ObtenerTablaCategorias();
    ObtenerTablaElementos();
    CatalogoPreguntas('');
    obtenerUsuarios();
    obtenerGrupos();
    obtenerRegiones();

    if (rol_id != 2 && rol_id != 5) {
        ocultar_select_regiones()
    }
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

    $('#form_subcategory').validate({
        rules: {
            nombre_subcategory: {
                required: true
            },
            category_id: {
                required: true
            }
        },
        submitHandler: function (form) {
            //console.log(datos)
            GuardarSubcategoria();
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

    $("#tipo").change(function () {
        $("#div_tipo_video").addClass("d-none");

        $("#div_video").addClass("d-none");
        $("#div_documento").addClass("d-none");
        $("#div_imagen").addClass("d-none");
        $("#div_video_id").addClass("d-none");
        $("#div_link").addClass("d-none");

        var tipo = $(this).val();
        switch (tipo) {
            case "video":
                $("#div_tipo_video").removeClass("d-none");
                break;
            case "documento":
                $("#div_documento").removeClass("d-none");
                break;
            case "imagen":
                $("#div_imagen").removeClass("d-none");
                break;
            case "link":
                $("#div_link").removeClass("d-none");
                break;
        }
    });

    $("#tipo_video").change(function () {
        $("#div_video").addClass("d-none");
        $("#div_video_id").addClass("d-none");

        var tipo = $(this).val();
        switch (tipo) {
            case "servidor":
                $("#div_video").removeClass("d-none");
                break;
            case "youtube":
                $("#div_video_id").removeClass("d-none");
                break;
            case "vimeo":
                $("#div_video_id").removeClass("d-none");
                break;
        }
    });

    $('#subir_documento').on('click', function () {
        $("#documento").trigger("click");
    });
    $('#subir_video').on('click', function () {
        $("#video").trigger("click");
    });
    $('#subir_imagen').on('click', function () {
        $("#imagen").trigger("click");
    });
    $('#subir_preview').on('click', function () {
        $("#preview").trigger("click");
    });
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 19/08/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcionalidad para obtener las subcategorias en base al id
     *          de categoria que selecciones
     ***********************************************************************/
    $('#id_categoria_elemento').change(function () {
        GetSubcategory($(this).val());
    });

    //funcion pora mostrar filtro habilitado o no habilitados
    $('#select_filtro').on('change', function () {
        filtro = $(this).val();
        $('#tabla_elementos').DataTable().ajax.reload();
        //ObtenerTablaElementos();
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

function obtener_asesores() {
    var id_region = get("select_regiones_library").value
    $.ajax({
        url: window.base_url + "Ws/ObtenerAsesores",
        type: "POST",
        data: { id_region: id_region },
        cache: false,
        success: function (response) {
            console.log(response)
            generarSelectAsesores(response.data)
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function generarSelectAsesores(asesores) {
    var select = get("select_asesores")
    var html = '<option>Todos...</option>'
    for (var i = 0; i < asesores.length; i++) {
        html += "<option value='" + asesores[i].id + "'>" + asesores[i].nombre + "</option>"
    }
    select.innerHTML = html
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para agregar una nueva subcategoría de elementos de
 *      	biblioteca, se muestra el formulario
 ***********************************************************************/
function AgregarSubcategoria() {
    $('#form_subcategory').trigger("reset");
    $('#id_subcategory').val('');
    $('#modal_subcategory').modal('show');
}


/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar por peticion AJAX un subcategoría de elementos de biblioteca(agregar o editar)
 ***********************************************************************/
function GuardarSubcategoria() {
    var id_subcategory = $('#id_subcategory').val(),
        datos = {
            subcategory: $('#nombre_subcategory').val(),
            category_id: $('#category_id').val()
        },
        metodo = "SaveSubcategory";

    if (id_subcategory != '') {
        datos.id = id_subcategory;
        metodo = "EditSubcategory";
        mensaje = 'El subcategoría no se pudo editar'
    }

    $.ajax({
        url: window.base_url + "Library/" + metodo,
        type: "POST",
        data: datos,
        cache: false,
        success: function (response) {
            tabla_subcategorys.ajax.reload();
            $('#modal_subcategory').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Catálogo subcategorías',
                text: 'El catálogo de subcategorías ha sido actualizado correctamente'
            });
            subcategory_id = '';
        },
        error: function () {
            $('#modal_subcategory').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Catálogo subcategorías',
                text: 'El subcategoría no se pudo guardar'
            });
            subcategory_id = '';
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para eliminar un subcategoría de elementos de biblioteca
 ***********************************************************************/
function EliminarSubcategoria(record) {
    Swal.fire({
        title: 'Catálogo subcategorías',
        text: "¿Estás seguro que deseas eliminar esta subcategoría?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: window.base_url + "Library/DeleteSubcategory",
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
                        title: 'Catálogo subcategorías',
                        text: 'El catálogo de subcategorías ha sido actualizado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    $('#modal_subcategory').modal('hide');
                    Swal.fire({
                        type: 'error',
                        title: 'Catálogo subcategorías',
                        text: error_msg
                    });
                }
            });
        }
    })
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
                if (cmd == "ver_subcategorias") {
                    VerSubcategorias(record)
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
                if (cmd == "ver_subcategorias") {
                    VerSubcategorias(record)
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
        "order": [[0, "desc"]],
        "ajax": {
            url: "../index.php/Library/ListCategories",
            type: 'POST',
            data: function (d) {
                d.es_admin = true
            },
            error: function (xhr, error, code) {
                tabla_categorias.clear().draw();
            },
            success: function (json) {
                var html = '<option value="" hidden disabled>Seleccionar</option>';
                for(var key in json.data){
                    html += '<option value="' + json.data[key].id + '">' + json.data[key].name + '</option>';
                }
                $('#id_categoria_elemento').html(html).fadeIn();
                $('#category_id').html(html).fadeIn();
                $('#tabla_categorias').dataTable().fnClearTable();
                $('#tabla_categorias').dataTable().fnAddData(json.data);
            }
        },
        buttons: [],
        "columns": [
            {
                data: "name",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + row.name + '</div>';
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
                        '<button title="Ver subcategorias" class="btn btn-info btn-xs editar mr-2 lead_0_8" cmd="ver_subcategorias"><i cmd="ver_subcategorias" class="fa fa-sort-amount-up"></i></button>' +
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
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 19/08/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener las subcategorias que tiene una categoria
 ***********************************************************************/
function VerSubcategorias(record) {
    //Este sirve para destruir el datatable
    $('#tabla_subcategorys').dataTable().fnDestroy();
    $('#tabla_subcategorys').DataTable({
        ajax: {
            url: window.base_url + 'Library/ListSubcategory',
            type: 'POST',
            data: {
                category_id: record.id
            },
            dataType: 'json',
            dataSrc: 'data',
        },
        idSrc: "id",
        language: idiomaDataTable,
        columns: [
            {data: "subcategory"},
            {data: "category"},
            {
                width: '100px',
                data: "id",
                render: function (data, type, row) {
                    var t = '';
                    t = '' +
                        '<div class="pt-2 pb-2 text-center">' +
                        '<button class="btn btn-primary btn-xs editar-subcategory mr-2 lead_0_8 text-white"' + 'data-id="' + row.id + '"' + 'data-category="' + row.category_id + '"' + 'data-subcategory="' + row.subcategory + '"><i class="fa fa-edit"></i></button>' +
                        '<a class="btn btn-danger btn-xs borrar lead_0_8 text-white" cmd="eliminar"><i cmd="eliminar" class="fa fa-times"></i></a>' +
                        '</div>';
                    return t;
                }
            }
        ],
        order: [[0, 'asc']]
    });

    $('#modal_subcategorys').modal('show');
}


// funcion para editar una subcategoria
$('body').on('click', '.editar-subcategory', function () {
    let id = $(this).attr('data-id');
    let category_id = $(this).attr('data-category');
    let subcategory = $(this).attr('data-subcategory');

    $('#form_subcategory').trigger("reset");
    $('#id_subcategory').val(id);
    $('#category_id').val(category_id);
    $('#nombre_subcategory').val(subcategory);
    $('#modal_subcategory').modal('show');
});


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
    console.log('record editar', record);
    $('#form_categoria').trigger("reset");
    $('#id_categoria').val(record.id);
    $('#nombre_categoria').val(record.name);
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
            name: $('#nombre_categoria').val()
        },
        metodo = "SaveCategory";

    if (id_categoria != '') {
        datos.id = id_categoria;
        metodo = "EditCategory"
    }

    $.ajax({
        url: window.base_url + "Library/" + metodo,
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
                url: window.base_url + "Library/DeleteCategory",
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


/*************ELEMENTOS**************/
/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Creacion de la tabla para listar categorías de elementos de biblioteca
 ***********************************************************************/
function ObtenerTablaElementos() {
    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_elementos').on('click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_elementos.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        console.log('record');
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
            url: "../index.php/Library/ListLibrary",
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
                data: "title",
                render: function (data, type, row) {
                    var eliminado = row.active ? "" : "eliminado";
                    var t = '';
                    t = '<div class="pt-2 pb-2 ' + eliminado + '">' + row.title + '</div>';
                    return t;
                }
            },
            {
                data: "name_category",
                render: function (data, type, row) {
                    var eliminado = row.active ? "" : "eliminado";
                    var t = '';
                    t = '<div class="pt-2 pb-2 ' + eliminado + '">' + row.name_category + '</div>';
                    return t;
                }
            },
            {
                data: "etiquetas",
                render: function (data, type, row) {
                    var eliminado = row.active ? "" : "eliminado";
                    var t = '';
                    t = '<div class="pt-2 pb-2 ' + eliminado + '">' + row.etiquetas + '</div>';
                    return t;
                }
            },
            {
                data: "subcategory",
                render: function (data, type, row) {
                    var eliminado = row.active ? "" : "eliminado";
                    var t = '';
                    t = '<div class="pt-2 pb-2 ' + eliminado + '">' + row.subcategory != 'null' ? row.subcategory : "" + '</div>';
                    return t;
                }
            },
            {
                data: "grupos",
                render: function (data, type, row) {
                    var eliminado = row.active ? "" : "eliminado";
                    var t = '';
                    t = '<div class="pt-2 pb-2 ' + eliminado + '">' + row.grupos + '</div>'
                    return t;
                }
            },
            {
                data: "id",
                render: function (data, type, row) {
                    var t = '';
                    var eliminado = row.active ? "" : "eliminado";
                    switch (row.type) {
                        case "video":
                            switch (row.type_video) {
                                case "servidor":
                                    t = row.video;
                                    break;
                                case "youtube":
                                case "vimeo":
                                    t = row.video;
                                    break;
                            }
                            break;
                        case "documento":
                            t = row.file;
                            break;
                        case "imagen":
                            t = row.file;
                            break;
                        case "link":
                            t = row.link;
                            break;
                    }
                    if (t != '') {
                        t = '<div class="pt-2 pb-2 d-flex justify-content-center ' + eliminado + '"><a href="' + t + '" target="_blank">Ver...<a><p></div>';
                    }
                    return t;
                }
            },
            {
                data: "fecha_limite",
                render: function (data, type, row) {
                    var t = '';
                    var eliminado = row.active ? "" : "eliminado";
                    var fecha = ""
                    if (row.fecha_limite != "") {
                        var date = new Date(row.fecha_limite)
                        var day = ("0" + date.getDate()).slice(-2);
                        var month = ("0" + (date.getMonth() + 1)).slice(-2);

                        fecha = date.getFullYear() + "-" + (month) + "-" + (day);
                    }
                    t = '<div class="pt-2 pb-2 ' + eliminado + '">' + fecha + '</div>'
                    return t;
                }
            },
            {
                data: "id",
                width: '100px',
                render: function (data, type, row) {
                    var t = '';
                    var eliminado = row.active ? "" : "eliminado";
                    t = '' +
                        '<div class="pt-2 pb-2 text-center ' + eliminado + '">' +
                        '<a title="Editar" class="btn btn-primary btn-xs mr-2 lead_0_8 text-white" cmd="editar"><i cmd="editar" class="fa fa-edit"></i></a>' +
                        '<a title="Eliminar" class="btn btn-danger btn-xs lead_0_8 text-white" cmd="eliminar">' + (!row.active ? "Habilitar" : "Deshabilitar") + '</a>' +
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
 *	Nota: funcion para agregar un nuevo elemento de elementos de biblioteca, se muestra el formulario
 ***********************************************************************/
function AgregarElemento() {
    $('#form_elemento').trigger("reset");

    $("#div_tipo_video").addClass("d-none");
    $("#div_video").addClass("d-none");
    $("#div_documento").addClass("d-none");
    $("#div_imagen").addClass("d-none");
    $("#div_video_id").addClass("d-none");
    $("#div_link").addClass("d-none");
    $("#contenedor_capacitacion_obligatoria").removeClass("d-none");
    CatalogoPreguntas('');
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

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para editar un elemento de la biblioteca, se carga y muestra el formulario
 ***********************************************************************/
function EditarElemento(record) {
    console.log("cuando se entra a editar", record)
    editando = true;
    id_library = record.id
    $('#form_elemento').trigger("reset");

    $("#div_tipo_video").addClass("d-none");
    $("#div_video").addClass("d-none");
    $("#div_documento").addClass("d-none");
    $("#div_imagen").addClass("d-none");
    $("#div_video_id").addClass("d-none");
    $("#div_link").addClass("d-none");
    $("#contenedor_capacitacion_obligatoria").addClass("d-none")

    $('#id_elemento').val(record.id);
    $('#titulo').val(record.title);
    $('#texto').val(record.text);
    $('#etiquetas').val(record.etiquetas);
    if (record.fecha_limite != "") {
        var date = new Date(record.fecha_limite)
        var day = ("0" + date.getDate()).slice(-2);
        var month = ("0" + (date.getMonth() + 1)).slice(-2);

        var fecha = date.getFullYear() + "-" + (month) + "-" + (day);

        $('#fecha_limite').val(fecha)
    }
    subcategory_id = record.subcategory_id;
    $('#id_categoria_elemento').val(record.category_id).change();

    $("#tipo").val(record.type);
    $("#tipo_video").val(record.type_video);
    $("#nombre_preview").val(record.image)
    $("quiz_library").val(record.question)
    switch (record.type) {
        case "video":
            $("#div_tipo_video").removeClass("d-none");
            switch (record.type_video) {
                case "servidor":
                    $("#nombre_video").val(record.video);
                    $("#div_video").removeClass("d-none");
                    break;
                case "youtube":
                case "vimeo":
                    $("#video_id").val(record.video_id);
                    $("#div_video_id").removeClass("d-none");
                    $("#div_video_id").removeClass("d-none");
                    break;
            }
            break;
        case "documento":
            $("#nombre_documento").val(record.file);
            $("#div_documento").removeClass("d-none");
            break;
        case "imagen":
            $("#nombre_imagen").val(record.file);
            $("#div_imagen").removeClass("d-none");
            break;
        case "link":
            $("#link").val(record.link);
            $("#div_link").removeClass("d-none");
            break;
    }
    usuarios_library = record.usuarios
    mostrarUsuariosLibrary();
    CatalogoPreguntas(record.id, record.question)
    $('#modal_elemento').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar por peticion AJAX un elemento de la biblioteca(agregar o editar)
 ***********************************************************************/
function GuardarElemento() {
    console.log(get("capacitacion_obligatoria").value)
   
    var id_elemento = $('#id_elemento').val() || '',
        datos = new FormData(),
        type = $('#tipo').val(),
        file = "", link = "", type_video = "", video = "",
        metodo = "SaveElement";
    console.log(id_elemento)
    /* if (id_elemento == '' && get("capacitacion_obligatoria").value == -1) {
        alert("Selecciona un tipo de elemento");
        return;
    } */
    document.getElementById("loader_background").style.display = 'inherit';
    document.getElementById("loader").style.display = 'inherit';

    datos.append('title', $('#titulo').val());
    datos.append('text', $('#texto').val());
    datos.append('etiquetas', $('#etiquetas').val());
    datos.append('category_id', $('#id_categoria_elemento').val());
    datos.append('subcategory_id', $('#id_subcategory_elemento').val());
    datos.append('quiz_library', $('#quiz_library').val());
    datos.append('capacitacion_obligatoria', get("capacitacion_obligatoria").value)

    if (id_elemento != '') {
        datos.append('id', id_elemento);
        metodo = "EditElement";
        datos.delete("capacitacion_obligatoria")
    }

    switch (type) {
        case "video":
            type_video = $("#tipo_video").val();
            switch (type_video) {
                case "servidor":
                    video = $("#video").prop('files').length != 0 ? $("#video").prop('files')[0] : null;
                    break;
                case "youtube":
                case "vimeo":
                    video = $("#video_id").val();
                    break;
            }
            break;
        case "documento":
            file = $("#documento").prop('files').length != 0 ? $("#documento").prop('files')[0] : null;
            break;
        case "imagen":
            file = $("#imagen").prop('files').length != 0 ? $("#imagen").prop('files')[0] : null;
            break;
        case "link":
            link = $("#link").val();
            break;
    }
    datos.append('image', $("#preview").prop('files')[0])
    datos.append('type', type);
    datos.append('file', file);
    datos.append('link', link);
    datos.append('type_video', type_video);
    datos.append('video', video);

    datos.append('fecha_limite', $("#fecha_limite").val());
    datos.append('usuarios', JSON.stringify(usuarios_library));
    id_region = get("select_regiones_library").value
    id_asesor = get("select_asesores").value
    console.log(id_region)
    if (id_region != 'Todas...') {
        datos.append('id_region', id_region);
    }
    if (id_asesor != 'Todos...' && id_asesor != '') {
        datos.append('id_asesor', $id_asesor)
    }
    $.ajax({
        url: window.base_url + "Library/" + metodo,
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

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para eliminar un elemento de la biblioteca
 ***********************************************************************/
function EliminarElemento(record) {
    var activo = record.active == 1 ? 0 : 1
    var texto = record.active == 1 ? "¿Estas seguro de deshabilitar este elemento?" : "¿Estas seguro de habilitar este elemento?"
    var texto_boton = record.active == 1 ? "deshabilitar" : "Habilitar"
    Swal.fire({
        title: 'Catálogo elementos',
        text: texto,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, ' + texto_boton,
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: window.base_url + "Library/DeleteElement",
                type: "POST",
                data: {
                    id: record.id,
                    activo: activo
                },
                cache: false,
                success: function (response) {
                    tabla_elementos.ajax.reload();
                    $('#modal_elemento').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Catálogo elementos',
                        text: 'El catálogo de elementos ha sido actualizado correctamente'
                    });
                },
                error: function () {
                    $('#modal_elemento').modal('hide');
                    Swal.fire({
                        type: 'error',
                        title: 'Catálogo elementos',
                        text: 'El elemento no se pudo actualizar'
                    });
                }
            });
        }
    })
}
function loadPreview(event) {
    var url = URL.createObjectURL(event.target.files[0]);

    var file = event.target.files[0];

    $('#nombre_preview').val(file.name);
    //var output = document.getElementById('preview_imagen');
    //output.src = url;
}
function loadDocumento(event) {
    var url = URL.createObjectURL(event.target.files[0]);

    var file = event.target.files[0];

    $('#nombre_documento').val(file.name);
    //var output = document.getElementById('preview_imagen');
    //output.src = url;
}

function loadVideo(event) {
    var url = URL.createObjectURL(event.target.files[0]);

    var file = event.target.files[0];

    $('#nombre_video').val(file.name);
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
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 19/08/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener la subcategorias de una categoria en
 *          especifico
 ***********************************************************************/
function GetSubcategory(category_id) {
    var datos = new FormData();
    datos.append('category_id', category_id);
    var config = {
        url: window.base_url + "Library/ListSubcategory",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: datos,
        success: function (response) {
            var html = '<option value="">Seleccionar</option>';
            $.each(response.data, function (index, value) {
                html += '<option value="' + value['id'] + '">' + value['subcategory'] + '</option>';
            });
            $('#id_subcategory_elemento').html(html).fadeIn();
            if (subcategory_id !== '') {
                $('#id_subcategory_elemento').val(subcategory_id)
            }
        },
        error: function (response) {

        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener el catalogo de preguntas para biblioteca
 ***********************************************************************/
function CatalogoPreguntas(library_id, valor_editado) {
    var datos = new FormData();
    if (library_id !== '') {
        // datos.append('library_id',library_id);
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
                $("#quiz_library").val(valor_editado)
            });
        },
        error: function (response) {
        }
    }
    $.ajax(config);
}
// ------------------------
// ------------------------
// ------------------------
//// a partir de aqui es para que funcione lo de agragar usuarios a los archivos de libreria
// ------------------------
// ------------------------
// ------------------------

var grupos = []
var usuarios = []
var usuarios_library = []
var editando = false
var id_library = undefined

function obtenerGrupos() {
    $.ajax({
        url: window.base_url + "Groups/GroupsRegister",
        type: 'POST',
        data: {

        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            grupos = json.data
            console.log(grupos)
            generarSelectConGrupos()
        }
    })
}

function get_(id) {
    return document.getElementById(id)
}

function generarSelectConGrupos() {
    var select = get_("grupos");
    var html = "<option value='0'>Todos</option>";
    for (var i = 0; i < grupos.length; i++) {
        html += "<option value='" + grupos[i].id + "'>" + grupos[i].name + "</option>"
    }
    console.log(html)
    select.innerHTML = html
}

function obtenerPorGrupo() {
    var id_grupo = get_("grupos").value
    console.log(id_grupo)
    console.log(id_grupo !== 0)
    console.log(typeof id_grupo)
    if (id_grupo != 0) {
        $.ajax({
            url: window.base_url + "Groups/UsersGroups",
            type: 'POST',
            data: {
                group_id: id_grupo
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
    } else {
        obtenerUsuarios();
    }
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
        mostrarUsuariosLibrary()
    } else {
        if (!usuarios_library.some(u => { return u.id === id })) {
            var usuario = usuarios.filter(f => f.id == id)[0]
            agregarUsuarioACapacitacion(id, usuario)
        }
    }
}

function agregarUsuarioACapacitacion(id, usuario) {
    console.log("entrando a agregar usuario a la capacitacion")
    // inhabilitarBotones()
    $.ajax({
        url: window.base_url + "Library/agregarUsuario",
        type: 'POST',
        data: {
            id_usuario: id,
            id_library: id_library
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            console.log("lo que se regresa al agreagar al usuario", json)
            // usuarios_library = json.data
            usuarios_library.push(usuario)
            // habilitarBotones();
            mostrarUsuariosLibrary()
        }
    })
}

function agregarTodos() {
    // if (editando == false) {
    for (var i = 0; i < usuarios.length; i++) {
        agregarUsuario(i)
    }
    // } else {

    // }
}

function mostrarUsuariosLibrary() {
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


function eliminarUsuario(indice) {
    if (editando === false) {
        usuarios_library.splice(indice, 1);
        mostrarUsuariosLibrary()
    } else {
        eliminarUsuarioDeLibrary(usuarios_library[indice].id, indice)
    }
}

function eliminarTodos() {
    if (editando === true) {
        var usuarios_eliminar = JSON.parse(JSON.stringify(usuarios_library))
        for (var i = 0; i < usuarios_eliminar.length; i++) {
            eliminarUsuario(i);
        }
    } else {
        usuarios_library = []
    }
    mostrarUsuariosLibrary()
}

function eliminarUsuarioDeLibrary(id_usuario, indice) {
    console.log("entrando a eliminar usuario de la capacitacion")
    // inhabilitarBotones()
    $.ajax({
        url: window.base_url + "Library/eliminarUsuario",
        type: 'POST',
        data: {
            id_usuario: id_usuario,
            id_library: id_library
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            console.log("lo que se regresa al elimiar el usuario", json)
            // usuarios_library = json.data
            usuarios_library.splice(indice, 1)
            mostrarUsuariosLibrary()
            // habilitarBotones()
        }
    })
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
