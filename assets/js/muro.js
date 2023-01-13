var tabla_wall_posts = null,
    tabla_wall_comments = null;
var editor = '';
var editor_edit = '';
var filtro = "";
jQuery(document).ready(function ($) {
    //Inicializar tabla de publicaciones
    ObtenerTablaWallPosts();

    iniciar_editor_froala()

    /*Click en imagenes de camara, para subir archivos*/
    $('#wall_post_image_preview').on('click', function () {
        $("#wall_post_image").trigger("click");
    });

    //Validador para formulario de publicación
    $('#form_wall_post').validate({
        rules: {
            wall_post_description: {
                required: true
            },

        },
        submitHandler: function (form) {
            GuardarWallPost();
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

    //Validador para formulario de publicación
    $('#form_wall_comment').validate({
        rules: {
            wall_comment: {
                required: true
            }
        },
        submitHandler: function (form) {
            GuardarWallComment();
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

    //select mostrar habilitados y deshabilitados
    //funcion pora mostrar filtro habilitado o no habilitados
    $('#select_filtro').on('change', function () {
        filtro = $(this).val();
        $('#tabla_wall_posts').DataTable().ajax.reload();
        //ObtenerTablaElementos();
    });
});


function iniciar_editor_froala() {
    editor = new FroalaEditor('#wall_post_description', {
        fontFamilySelection: true,
        quickInsertButtons: ["embedly", "ul", "ol", "hr", 'emoticons'],
        toolbarButtons: {
            'moreText': {
                'buttons': ['bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', 'fontSize', 'fontFamily', 'inlineClass', 'inlineStyle', 'clearFormatting']
            },
            'moreRich': {
                'buttons': ['fontAwesome', 'specialCharacters', 'embedly', 'insertHR']
            },
            'moreParagraph': {
                'buttons': ['alignLeft', 'alignCenter', 'formatOLSimple', 'alignRight', 'alignJustify', 'formatOL', 'formatUL', 'paragraphFormat', 'paragraphStyle', 'lineHeight', 'outdent', 'indent', 'quote']
            },
            'moreMisc': {
                'buttons': ['undo', 'redo', 'fullscreen', 'spellChecker', 'selectAll', 'html', 'help'],
                'align': 'right',
                'buttonsVisible': 2
            },'emoticons':{
                'buttons': ['emoticons']
            }
        },
        emoticonsStep: 4,
        emoticonsSet: [{
            id: 'people',
            name: 'Smileys & People',
            code: '1f600',
            emoticons: [
                { code: '1f600', desc: 'Grinning face' },
                { code: '1f601', desc: 'Grinning face with smiling eyes' },
                { code: '1f602', desc: 'Face with tears of joy' },
                { code: '1f603', desc: 'Smiling face with open mouth' },
                { code: '1f604', desc: 'Smiling face with open mouth and smiling eyes' },
                { code: '1f605', desc: 'Smiling face with open mouth and cold sweat' },
                { code: '1f606', desc: 'Smiling face with open mouth and tightly-closed eyes' },
                { code: '1f607', desc: 'Smiling face with halo' }
            ]
        }, {
            'id': 'nature',
            'name': 'Animals & Nature',
            'code': '1F435',
            'emoticons': [
                { code: '1F435', desc: 'Monkey Face' },
                { code: '1F412', desc: 'Monkey' },
                { code: '1F436', desc: 'Dog Face' },
                { code: '1F415', desc: 'Dog' },
                { code: '1F429', desc: 'Poodle' },
                { code: '1F43A', desc: 'Wolf Face' },
                { code: '1F431', desc: 'Cat Face' },
                { code: '1F408', desc: 'Cat' },
                { code: '1F42F', desc: 'Tiger Face' },
                { code: '1F405', desc: 'Tiger' },
                { code: '1F406', desc: 'Leopard' },
                { code: '1F434', desc: 'Horse Face' },
                { code: '1F40E', desc: 'Horse' },
                { code: '1F42E', desc: 'Cow Face' },
                { code: '1F402', desc: 'Ox' },
                { code: '1F403', desc: 'Water Buffalo' },
            ]
        }]
    });
}

/*************QUIZ**************/
/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Inicializacion de la tabla para listar publicaciones
 ***********************************************************************/
function ObtenerTablaWallPosts() {
    //Se agregan los eventos para los botones de "Ver comentarios", "Editar" y "Eliminar" del listado
    $('#tabla_wall_posts').on('click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_wall_posts.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarWallPost(record);
                }
                if (cmd == "eliminar") {
                    EiminarWallPost(record)
                }
                if (cmd == "comentarios") {
                    $('#modal_comentarios_detalle').modal('show');
                    ObtenerTablaWallComments(record)
                }
            }
        }
    });
    $('#tabla_wall_posts').on('tbody click', 'tr', function (e) {
        var record = tabla_wall_posts.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarWallPost(record);
                }
                if (cmd == "eliminar") {
                    EiminarWallPost(record)
                }
                if (cmd == "comentarios") {
                    $('#modal_comentarios_detalle').modal('show');
                    ObtenerTablaWallComments(record)
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_wall_posts = $('#tabla_wall_posts').DataTable({

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
            url: "../index.php/Wall/Posts",
            type: 'POST',
            data: function (d) {
                d.filtro = filtro;
            },
            error: function (xhr, error, code) {
                console.log(xhr)
                tabla_wall_posts.clear().draw();
            }

        },
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            autoFilter: true,
            sheetName: 'Exported data'
        }],
        "columns": [
            {
                data: "name",
                render: function (data, type, row) {
                    var eliminado = row.active ? "" : "eliminado";
                    var t = '';
                    t = '<div class="pt-2 pb-2 ' + eliminado + '">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "wall_description",
                render: function (data, type, row) {
                    var t = '';
                    var eliminado = row.active ? "" : "eliminado";
                    t = '<div class="pt-2 pb-2 ' + eliminado + '">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "image_path",
                render: function (data, type, row) {
                    var t = '';
                    var eliminado = row.active ? "" : "eliminado";
                    t = '<div class="pt-2 pb-2 d-flex justify-content-center ' + eliminado + '"><a href="' + data + '" target="_blank">Ver...<a><p></div>';
                    return t;
                }
            },

            {
                data: "likes",
                render: function (data, type, row) {
                    var t = '';
                    var eliminado = row.active ? "" : "eliminado";
                    t = '<div class="pt-2 pb-2 ' + eliminado + '">' + data + '</div>';
                    return t;
                }
            }, {
                data: "comentarios",
                render: function (data, type, row) {
                    var t = '';
                    var eliminado = row.active ? "" : "eliminado";
                    t = '<div class="pt-2 pb-2 ' + eliminado + '" cmd="comentarios" style="cursor:pointer; text-decoration:underline">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "id",
                render: function (data, type, row) {
                    var t = '';
                    var eliminado = row.active ? "" : "eliminado";
                    t = '' +
                        '<div class="pt-2 pb-2 text-center ' + eliminado + '">' +
                        '<a title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8 text-white" cmd="editar"><i cmd="editar" class="fa fa-edit"></i></a>' +
                        '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8 text-white" cmd="eliminar">' + (!row.active ? "Habilitar" : "Deshabilitar") + '</a>' +
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
 *	Nota: funcion para agregar una publicación, se muestra el formulario
 ***********************************************************************/
function AgregarWallPost() {
    $('#form_wall_post').trigger("reset");
    $('#wall_post_id').val('');
    $("#wall_post_image_preview").attr("src", window.base_url + "assets/img/Camara.png");
    $('#modal_wall_post').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para editar un publicación, se carga y muestra el formulario
 ***********************************************************************/
function EditarWallPost(record) {
    $('#form_wall_post').trigger("reset");
    $('#wall_post_id').val(record.id);
    editor.html.set(record.wall_description);
    //$('#wall_post_description').val(record.wall_description);
    $("#wall_post_image_preview").attr("src", record.image_path);
    $("#wall_post_preview").attr("src", record.image_preview);
    $('#modal_wall_post').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar una publicación(agregar o editar),  por peticion AJAX
 ***********************************************************************/
function GuardarWallPost() {
    document.getElementById("loader_background").style.display = 'inherit';
    document.getElementById("loader").style.display = 'inherit';
    var wall_post_id = $('#wall_post_id').val(),
        data = new FormData(),
        image = $("#wall_post_image").prop('files').length != 0 ? $("#wall_post_image").prop('files')[0] : null,
        // video = $("#wall_post_video").prop('files').length != 0 ? $("#wall_post_video").prop('files')[0] : null,
        method = "SavePost";

    // data.append('wall_description', $('#wall_post_description').val());
    data.append('wall_description', editor.html.get())
    data.append('redirect', 1);
    // if (image)
    data.append('image_path', image);
    var preview = $("#wall_post_preview").prop('files').length != 0 ? $("#wall_post_preview").prop('files')[0] : null

    data.append("image_preview", preview);
    var bandera = false;
    if (image)
        if (image.type.includes("video")) {
            bandera = true
            data.append("tipo", "video");
        } else if (image.type.includes("gif")) {
            data.append("tipo", "gif")
        } else {
            data.append("tipo", "imagen")
        }

    //Si se cargo la llave del quiz, entonces es edicion
    if (wall_post_id != '') {
        data.append('id', wall_post_id);
        method = "EditPost"
    }

    //Cambio en los quiz, ocultamos los apartados dependientes: comentarios
    $('#seccion_comentarios').addClass("d-none");
    if (tabla_wall_comments) tabla_wall_comments.clear().draw();

    $.ajax({
        url: window.base_url + "Wall/" + method,
        type: "POST",
        contentType: false,
        data: data,
        processData: false,
        cache: false,
        success: function (response) {
            console.log(response)
            document.getElementById("loader_background").style.display = 'none';
            document.getElementById("loader").style.display = 'none';
            tabla_wall_posts.ajax.reload();
            $('#modal_wall_post').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Muro',
                text: 'El muro ha sido actualizado correctamente'
            });
        },
        error: function (error) {
            console.log(error)
            document.getElementById("loader_background").style.display = 'none';
            document.getElementById("loader").style.display = 'none';
            $('#modal_wall_post').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Muro',
                text: 'La publicación no se pudo guardar'
            });
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para eliminar una publicación
 ***********************************************************************/
function EiminarWallPost(record) {
    var texto = record.active == 1 ? "¿Estas seguro de deshabilitar este elemento?" : "¿Estas seguro de habilitar este elemento?"
    var texto_boton = record.active == 1 ? "deshabilitar" : "Habilitar"
    Swal.fire({
        title: 'Elminar Publicación',
        text: texto,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, ' + texto_boton,
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            //Cambio en los quiz, ocultamos los apartados dependientes: comentarios
            $('#seccion_comentarios').addClass("d-none");
            if (tabla_wall_comments) tabla_wall_comments.clear().draw();
            $.ajax({
                url: window.base_url + "Wall/DeletePost",
                type: "POST",
                data: {
                    id: record.id,
                    active: (record.active ? 0 : 1)
                },
                cache: false,
                success: function (response) {
                    tabla_wall_posts.ajax.reload();
                    $('#modal_wall_post').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Actualizar Publicación',
                        text: 'El muro ha sido actualizado correctamente'
                    });
                },
                error: function (error) {
                    console.log(error)
                    var error_msg = error.responseJSON.error_msg;
                    $('#modal_wall_post').modal('hide');
                    Swal.fire({
                        type: 'error',
                        title: 'Actualizar Publicación',
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
 *	Nota: Inicializacion de la tabla para listar comentarios de una publicación en particular
 ***********************************************************************/
function ObtenerTablaWallComments(record) {
    //Guardamos la publicación seleccionada en una variable
    $('#selected_wall_post_id').val(record.id);
    //Mostramos la seccion de comentarios, para cargar las de la publicación seleccionada
    $('#seccion_comentarios').removeClass("d-none");

    //Si la tabla de comentarios ya habia sido creada con anterioridad
    if (tabla_wall_comments) {
        //Actualizamos la tabla de comentarios para recibir los comentarios de la publicación seleccionada
        tabla_wall_comments.ajax.reload();
        return;
    }

    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_wall_comments').on('click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_wall_comments.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarWallComment(record);
                }
                if (cmd == "eliminar") {
                    EliminarWallComment(record);
                }
            }
        }
    });
    $('#tabla_wall_comments').on('tbody click', 'tr', function (e) {
        var record = tabla_wall_comments.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarWallComment(record);
                }
                if (cmd == "eliminar") {
                    EliminarWallComment(record);
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_wall_comments = $('#tabla_wall_comments').DataTable({
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
            url: "../index.php/Wall/Comments",
            type: 'POST',
            data: function (d) {
                d.wall_post_id = $('#selected_wall_post_id').val();
            },
            error: function (xhr, error, code) {
                tabla_wall_comments.clear().draw();
            }
        },
        buttons: [],
        "columns": [{
            data: "name",
            render: function (data, type, row) {
                var t = '';
                t = '<div class="pt-2 pb-2">' + data + '</div>';
                return t;
            }
        }, {
            data: "comment",
            render: function (data, type, row) {
                var t = '';
                t = '<div class="pt-2 pb-2">' + data + '</div>';
                return t;
            }
        }, {
            data: "id",
            width: '100px',
            render: function (data, type, row) {
                var t = '';
                t = '' +
                    '<div class="pt-2 pb-2 text-center">' +
                    //'<a class="btn btn-primary btn-xs editar mr-2 lead_0_8" cmd="editar"><i class="fa fa-edit"></i> Editar</a>' +
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
 *	Nota: funcion para agregar un comentario, se muestra el formulario
 ***********************************************************************/
function AgregarWallComment() {
    $('#form_wall_comment').trigger("reset");
    $('#wall_comment_id').val('');
    $('#modal_wall_comment').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para editar un comentario, se carga y muestra el formulario
 ***********************************************************************/
function EditarWallComment(record) {
    $('#form_wall_comment').trigger("reset");
    $('#wall_comment_id').val(record.id);
    $('#wall_comment').val(record.comment);
    $('#modal_wall_comment').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar una pregunta(agregar o editar), por peticion AJAX
 ***********************************************************************/
function GuardarWallComment() {
    var comment_id = $('#wall_comment_id').val(),
        data = new FormData(),
        metodo = "SaveComment";

    data.append('comment', $('#wall_comment').val());
    data.append('post_id', $('#selected_wall_post_id').val());

    //Si se cargo la llave de la pregunta, entonces es edicion
    if (comment_id != '') {
        data.append('id', comment_id);
        metodo = "EditComment"
    }

    $.ajax({
        url: window.base_url + "Wall/" + metodo,
        type: "POST",
        contentType: false,
        data: data,
        processData: false,
        cache: false,
        success: function (response) {
            tabla_wall_comments.ajax.reload();
            $('#modal_wall_comment').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Agregar Comentario',
                text: 'El comentario ha sido actualizado correctamente'
            });
        },
        error: function () {
            $('#modal_wall_comment').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Catálogo Preguntas',
                text: 'El comentario no se pudo guardar'
            });
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para eliminar un comentario
 ***********************************************************************/
function EliminarWallComment(record) {
    Swal.fire({
        title: 'Eliminar Comentario',
        text: "¿Estás seguro que deseas eliminar este comentario?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: window.base_url + "Wall/DeleteComment",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function (response) {
                    tabla_wall_comments.ajax.reload();
                    Swal.fire({
                        type: 'success',
                        title: 'Eliminar Comentario',
                        text: 'El comentario ha sido eliminado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    Swal.fire({
                        type: 'error',
                        title: 'Eliminar Comentario',
                        text: error_msg
                    });
                }
            });
        }
    })
}

/*CARGA DE ARCHIVOS*/
function loadWallPostImage(event) {
    var url = URL.createObjectURL(event.target.files[0]);
    var tipo = document.getElementById("tipo")
    console.log(event.target.files)
    var output = document.getElementById('wall_post_image_preview');
    if (event.target.files[0].type.includes("video")) {
        output.src = "./../assets/img/img_video.png"
        tipo.value = "video"
        document.getElementById("wall_post_preview").style.display = "block"
    } else {
        output.src = url;
        tipo.value = "imagen"
        document.getElementById("wall_post_preview").style.display = "none"
    }
};
