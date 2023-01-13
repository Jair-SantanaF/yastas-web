var tabla_wall_posts = null,
    tabla_wall_comments = null;
var ranking_filtrado = [];

jQuery(document).ready(function ($) {
    //Inicializar tabla de publicaciones
    console.log(ranking)
    // ranking = JSON.parse(ranking);
    // console.log(ranking)
    ranking_filtrado = ranking;
    generar_tabla_ranking()
    ObtenerTablaWallPosts();

    /*Click en imagenes de camara, para subir archivos*/
    $('#wall_post_image_preview').on('click', function () {
        $("#wall_post_image").trigger("click");
    });

    //Validador para formulario de publicación
    $('#form_wall_post').validate({
        rules: {
            wall_post_description: {
                required: true
            }
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
});


/*************QUIZ**************/
/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Inicializacion de la tabla para listar publicaciones
 ***********************************************************************/
function ObtenerTablaWallPosts() {
    //Se agregan los eventos para los botones de "Ver comentarios", "Editar" y "Eliminar" del listado
    $('#tabla_wall_posts').on('tbody click', 'tr', function (e) {
        var record = tabla_wall_posts.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");

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
        "order": [[0, "desc"]],
        "ajax": {
            url: "../index.php/Wall/Posts",
            type: 'POST',
            data: function (d) {
            },
            error: function (xhr, error, code) {
                tabla_wall_posts.clear().draw();
            }

        },
        buttons: [],
        "columns": [
            {
                data: "name",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "wall_description",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "image_path",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2 d-flex justify-content-center"><a href="' + data + '" target="_blank">Ver...<a><p></div>';
                    return t;
                }
            },

            {
                data: "likes",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "id",
                render: function (data, type, row) {
                    var t = '';
                    t = '' +
                        '<div class="pt-2 pb-2 text-center">' +
                        '<a title="Ver comentarios" class="btn btn-info btn-xs editar mr-2 lead_0_8 text-white" cmd="comentarios"><i cmd="comentarios" class="fa fa-eye"></i></a>' +
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
    $('#wall_post_description').val(record.wall_description);
    $("#wall_post_image_preview").attr("src", record.image_path);
    $('#modal_wall_post').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar una publicación(agregar o editar),  por peticion AJAX
 ***********************************************************************/
function GuardarWallPost() {
    var wall_post_id = $('#wall_post_id').val(),
        data = new FormData(),
        image = $("#wall_post_image").prop('files').length != 0 ? $("#wall_post_image").prop('files')[0] : null,
        method = "SavePost";

    data.append('wall_description', $('#wall_post_description').val());
    data.append('image_path', image);

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
            tabla_wall_posts.ajax.reload();
            $('#modal_wall_post').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Muro',
                text: 'El muro ha sido actualizado correctamente'
            });
        },
        error: function () {
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
    Swal.fire({
        title: 'Elminar Publicación',
        text: "¿Estás seguro que deseas eliminar esta publicación?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
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
                    id: record.id
                },
                cache: false,
                success: function (response) {
                    tabla_wall_posts.ajax.reload();
                    $('#modal_wall_post').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Eliminar Publicación',
                        text: 'El muro ha sido actualizado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    $('#modal_wall_post').modal('hide');
                    Swal.fire({
                        type: 'error',
                        title: 'Eliminar Publicación',
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
    $('#tabla_wall_comments').on('tbody click', 'tr', function (e) {
        var record = tabla_wall_comments.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");

        if (es_boton) {
            if (cmd == "editar") {
                EditarWallComment(record);
            }
            if (cmd == "eliminar") {
                EliminarWallComment(record);
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

    var output = document.getElementById('wall_post_image_preview');
    output.src = url;
};


function filtrar() {
    var filtro = document.getElementById("filtro").value
    console.log(filtro)
    ranking_filtrado = ranking.filter(f => f.name.toLowerCase().includes(filtro) || f.last_name.toLowerCase().includes(filtro) || f.job_name.toLowerCase().includes(filtro))

    generar_tabla_ranking()
}

function generar_tabla_ranking() {
    var tabla = document.getElementById("contenedor_ranking")
    var filtro = document.getElementById("filtro").value
    var html = ''
    for (var i = 0; i < ranking_filtrado.length; i++) {
        if (i >= 10) {
            html += "<tr>"
            html += "<td>" + i + "</td>"
            html += "<td>" + ranking_filtrado[i].name + " " + ranking_filtrado[i].last_name + "</td>"
            html += "<td>" + ranking_filtrado[i].job_name + "</td>"
            html += "<td>" + ranking_filtrado[i].score + "</td>"
            html += "</tr>"
        } else if ((filtro != '' && filtro != undefined)) {
            html += "<tr>"
            html += "<td>" + i + "</td>"
            html += "<td>" + ranking_filtrado[i].name + " " + ranking_filtrado[i].last_name + "</td>"
            html += "<td>" + ranking_filtrado[i].job_name + "</td>"
            html += "<td>" + ranking_filtrado[i].score + "</td>"
            html += "</tr>"
        }
    }
    tabla.innerHTML = html
}