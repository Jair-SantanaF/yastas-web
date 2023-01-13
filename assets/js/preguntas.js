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

var /* db = firebase.firestore(), */
    tabla_catalogo = '',
    tabla_preguntas = '',
    tabla_respuestas = '',
    editor = '',
    editor_respuesta = '',
    decodeEntities = (function () {
        // this prevents any overhead from creating the object each time
        var element = document.createElement('div');

        function decodeHTMLEntities(str) {
            if (str && typeof str === 'string') {
                // strip script/html tags
                str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
                str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
                element.innerHTML = str;
                str = element.textContent;
                element.textContent = '';
            }

            return str;
        }

        return decodeHTMLEntities;
    })();
jQuery(document).ready(function ($) {
    mostrarTablas(0)
    ObtenerCatalogosPreguntas();
    obtenerUsuarios();
    obtenerGrupos();
    //CargarTipoPreguntas();
    $('#form_catalogo').validate({
        rules: {
            nombre_catalogo: {
                required: true
            }
        },
        submitHandler: function (form) {

            //console.log(datos)
            GuardarCatalogo(datos);
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
    $('#form_preguntas').validate({
        rules: {
            question: {
                required: true
            },
            type_id: {
                required: true
            },
            points: {
                required: true
            }
        },
        submitHandler: function (form) {
            //console.log(datos)
            var datos = new FormData($('#form_preguntas')[0]);
            //console.log(datos)
            GuardarPregunta(datos);
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
    $('#form_respuestas').validate({
        rules: {
            answer: {
                required: true
            },
            correct: {
                required: true
            }
        },
        submitHandler: function (form) {
            //console.log(datos)
            var datos = new FormData($('#form_respuestas')[0]);
            //console.log(datos)
            GuardarRespuesta(datos);
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
    editor = new FroalaEditor('#question', {
        fontFamilySelection: true,
        quickInsertButtons: ["embedly", "ul", "ol", "hr"],
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
            }
        }
    });
    editor_respuesta = new FroalaEditor('#answer', {
        quickInsertButtons: ["embedly", "ul", "ol", "hr"],
        toolbarButtons: {
            'moreText': {
                'buttons': ['bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', 'fontSize', 'inlineClass', 'inlineStyle', 'clearFormatting']
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
            }
        }
    });
});

/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 13/05/2020
 *	Nota: Funcion para obtener los catalogos preguntas
 ***********************************************************************/
function ObtenerCatalogosPreguntas() {
    tabla_catalogo = $('#catalogo_preguntas').dataTable({
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
            { "data": "name" },
            { "data": "name_category" },
            { "data": "fecha_limite" },
            {
                "data": "id",
                render: function (data, type, full, meta) {
                    let html =
                        '<a title="Ver preguntas" class="btn btn-info btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="Preguntas(' + data + ')"><i class="fa fa-search"></i></a>' +
                        '<a title="Ver preguntas" class="btn btn-info btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="_Preguntas(' + data + ')"><i class="fa fa-eye"></i></a>' +
                        '<a title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="EditarCatalogo(' + data + ')"><i class="fa fa-edit"></i></a>' +
                        '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8"  href="javascript:void(0)" onclick="EliminarCatalogo(' + data + ')"><i class="fa fa-times"></i></a>';
                    return html;
                }
            }
        ],
        "ajax": {
            url: window.base_url + "questions/ListQuizAdmin",
            type: 'POST',
            data: function (data){          
            },
            error: function (xhr, error, code) {
                console.log(xhr)
                tabla_catalogo.api().clear().draw();
            }
        }
    });
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 13/05/2020
 *	Nota: Funcion para editar un registro de alta de catalogo de
 *	        preguntas
 ***********************************************************************/
var edicion = '',
    eliminar = '';
function EditarCatalogo(id) {
    if (id !== '') {
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 28/09/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: obtenemos el detalle del catalogo
         ***********************************************************************/
        var datos = new FormData();
        datos.append('id', id)
        var config = {
            url: window.base_url + "questions/ListQuizAdmin",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            data: datos,
            success: function (response) {
                console.log(response)
                let data = response.data[0];
                $('#nombre_catalogo').val(data.name);
                $('#category_id').val(data.category_id).change();
                $('#job_id').val(data.job_id).change();
                $("#contenedor_capacitacion_obligatoria").addClass("d-none")
                if (usuarios_library.length > 0) {
                    mostrarTablas(0)
                } else {
                    mostrarTablas(1)
                }
                usuarios_library = data.usuarios
                grupos_cuestionarios = data.grupos
                // console.log(grupos_cuestionarios)
                mostrarGruposCuestionarios()
                mostrarUsuariosLibrary()
            },
            error: function (response) {
                Swal.fire({
                    type: 'error',
                    title: 'Preguntas',
                    text: response.responseJSON.error_msg
                });
            }
        }
        $.ajax(config);
        /*db.collection('catalogo_preguntas').doc(id).get().then(function (doc) {
            $('#nombre_catalogo').val(doc.data().nombre);
        });*/
        edicion = id;
    } else {
        $('#form_catalogo').trigger("reset");
        get("nombre_catalogo").value = ""
        get("fecha_limite").value = ""
        get("category_id").value = ""
        $("#contenedor_capacitacion_obligatoria").removeClass("d-none")
        // usuarios_library = []
        // grupos_cuestionarios = []
        mostrarGruposCuestionarios()
        mostrarUsuariosLibrary()
        edicion = '';
    }
    $('#modal_catalogo').modal('show');
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 13/05/2020
 *	Nota: Guardar edicion o registro nuevo de catalogo de preguntas
 ***********************************************************************/
function GuardarCatalogo(datos) {
    if (edicion == '' && get("capacitacion_obligatoria").value == -1) {
        alert("Selecciona un tipo de elemento")
        return
    }
  /*   if (edicion == '' && get("first_question_is_correct").value == -1) {
        alert("Selecciona un tipo de cuestionario")
        return
    } */
    var datos = {};
    datos.name = get("nombre_catalogo").value
    datos.fecha_limite = get("fecha_limite").value
    datos.category_id = get("category_id").value
    //datos.first_question_is_correct = get("first_question_is_correct").value
    if (usuarios_library.length > 0)
        datos.usuarios = JSON.stringify(usuarios_library)
    if (grupos_cuestionarios.length > 0)
        datos.grupos = JSON.stringify(grupos_cuestionarios)
    console.log(datos)
    if (edicion !== '') {
        // datos.append('id',edicion);
        datos.id = edicion;
    } else
        datos.capacitacion_obligatoria = get("capacitacion_obligatoria").value
    var config = {
        url: window.base_url + "questions/SaveQuiz",
        type: "POST",
        // cache: false,
        // contentType:false,
        // processData: false,
        data: datos,
        success: function (response) {
            Swal.fire({
                type: 'success',
                title: 'Preguntas',
                text: response.msg
            }).then((result) => {
                $('#form_catalogo').trigger("reset");
                $('#modal_catalogo').modal('hide');
                tabla_catalogo.api().ajax.reload();
            });
        },
        error: function (response) {
            console.log(response)
            Swal.fire({
                type: 'error',
                title: 'Preguntas',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
    /*if(edicion !== ''){
        db.collection("catalogo_preguntas").doc(edicion).update({
            nombre: datos[0].value
        }).then(function() {
            edicion = '';
            $('#form_catalogo').trigger("reset");
            $('#modal_catalogo').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Catalogo preguntas',
                text: 'El catalogo de preguntas ha sido actualizado correctamente'
            });
        }).catch(function(error) {
            console.error("Error writing document: ", error);
            edicion = '';
            $('#form_catalogo').trigger("reset");
            $('#modal_catalogo').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Catalogo preguntas',
                text: 'El catalogo de preguntas no ha sido actualizado correctamente'
            });
        });

    }else{
        db.collection("catalogo_preguntas").add({
            nombre: datos[0].value,
            id_empresa: parseInt(empresa_id),
            eliminado: false
        }).then(function() {
            edicion = '';
            $('#form_catalogo').trigger("reset");
            $('#modal_catalogo').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Catalogo preguntas',
                text: 'El catalogo de preguntas ha sido guardado correctamente'
            });
        }).catch(function(error) {
            console.error("Error writing document: ", error);
            edicion = '';
            $('#form_catalogo').trigger("reset");
            $('#modal_catalogo').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Catalogo preguntas',
                text: 'El catalogo de preguntas no ha sido guardado correctamente'
            });
        });
    }*/
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 13/05/2020
 *	Nota: funcion para eliminar un catalogo de preguntas
 ***********************************************************************/
function EliminarCatalogo(id) {
    Swal.fire({
        title: 'Catalogo preguntas',
        text: "¿Estás seguro que deseas eliminar este registro?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            var datos = new FormData();
            datos.append('id', id);
            datos.append('active', 0);
            var config = {
                url: window.base_url + "questions/SaveQuiz",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: datos,
                success: function (response) {
                    Swal.fire({
                        type: 'success',
                        title: 'Preguntas',
                        text: response.msg
                    }).then((result) => {
                        tabla_catalogo.api().ajax.reload();
                    });
                },
                error: function (response) {
                    Swal.fire({
                        type: 'error',
                        title: 'Preguntas',
                        text: response.responseJSON.error_msg
                    });
                }
            }
            $.ajax(config);
            /*db.collection("catalogo_preguntas").doc(id).update({
                eliminado: true
            }).then(function() {
                Swal.fire({
                    type: 'success',
                    title: 'Catalogo preguntas',
                    text: 'El catalogo se ha borrado correctamente'
                });
            }).catch(function(error) {
                console.error("Error writing document: ", error);
                Swal.fire({
                    type: 'error',
                    title: 'Catalogo preguntas',
                    text: 'El catalogo no se ha borrado correctamente'
                });
            });*/
        }
    })
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 13/05/2020
 *	Nota: Obterner el detalle de preguntas de un catalogo seleccionado
 ***********************************************************************/
var id_quiz;
var business_id;
function Preguntas(id) {
    id_quiz = id;
    $('#modal_detalles_ux').modal('show');
    $('#display_error').hide();
    $("#user_quiz_list").html("<option value=\"\">Seleccionar...</option>");

    $.post(window.base_url + "questions/listUserAnsweredQuestion", { id: id, }, function (data) {
        if (data.data) {
            data.data.forEach(item => {
                console.log(item);
                business_id = item.business_id;
                var o = new Option(item.nombre_completo, item.id);
                $("#user_quiz_list").append(o);
            });
        } else {
            $('#display_error').show();
        }
    });
}

function detallarCuestionario() {
    $('#loading_q').show();

    let id_user = $('#user_quiz_list').val();
    $('#cuestionarios_dinamicos').html("");

    $.post(window.base_url + "questions/listQuizPerUser", { id: id_quiz, user: id_user }, function (data) {
        $('#cuestionarios_dinamicos').html("<a href=\"http://localhost:9000/questions/downloadQuizCSV/" + id_quiz + "/" + id_user + "\">Descargar CSV</a><br/><br/>");
        // $('#cuestionarios_dinamicos').html("<a href=\"http://kreativeco.com/nuup/questions/downloadQuizCSV/" + id_quiz + "/" + id_user + "\">Descargar CSV</a><br/><br/>");
        $('#loading_q').hide();
        console.log(data);
        if (data.data) {
            data.data.forEach((item, index) => {
                $('#cuestionarios_dinamicos').append('<b>Cuestionario contestado: ' + item.fecha + '</b><br/>');
                const quiz_results = $('#cuestionarios_dinamicos').append('<table class="table" id="tabla_dinamica_' + index + '"><thead><tr><th scope="col">Pregunta</th><th scope="col">Respuesta</th></tr></thead><tbody></tbody></table>');
                $('#cuestionarios_dinamicos').append('<br/>');
                item.preguntas.forEach((question) => {
                    question.answer = '' + question.answer;
                    if (question.answer.indexOf('.jpeg') >= 0 || question.answer.indexOf('.png') >= 0) {
                        question.answer = '<img src="http://kreativeco.com/nuup/uploads/business_' + business_id + '/preguntas/' + question.answer + '" width="200" />';
                    }
                    $('#tabla_dinamica_' + index).find('tbody').append('<tr><td>' + question.question + '</td><td>' + question.answer + '</td></tr>');
                })
            });
        } else {
            $('#display_error').show();
        }
    });
}

var id_catalogo_select = '';
function _Preguntas(id) {
    $('#quiz_id').val(id);
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
            { "data": "question" },
            { "data": "name_type" },
            { "data": "points" },
            {
                "data": "id",
                render: function (data, type, full, meta) {
                    let html = '';
                    /***********************************************************************
                     *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/09/2020
                     *		   mario.martinez.f@hotmail.es
                     *	Nota: creamos una validacion para que no muestre el icono de ver
                     *  	    respuestas en el tipo de pregunta que no lleva respuestas
                     *  	    posibles.
                     ***********************************************************************/
                    if (full.answers_type == 1) {
                        html = '<a title="Ver respuestas" class="btn btn-info btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="Respuestas(' + data + ',' + full.type_id + ')"><i class="fa fa-eye"></i></a>';
                    }
                    html += '<a title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="EditarPregunta(' + data + ')"><i class="fa fa-edit"></i></a>' +
                        '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8"  href="javascript:void(0)" onclick="EliminarPregunta(' + data + ')"><i class="fa fa-times"></i></a>';
                    return html;
                }
            }
        ],
        "ajax": {
            "url": window.base_url + "questions/ListQuestionsQuiz",
            "type": 'POST',
            "data": function (d) {
                d.quiz_id = $('#quiz_id').val();
            },
            error: function (xhr, error, code) {
                tabla_preguntas.api().clear().draw();
            }
        }
    });
    $('#agregar_pregunta').show();
    $('#modal_preguntas_detalle').modal('show');
    /*id_catalogo_select = id;
    db.collection("preguntas").where("key_catalogo", "==", id_catalogo).where("eliminado","==",false).onSnapshot(function(querySnapshot) {
        if($('#preguntas_tabla').DataTable()){
            $('#preguntas_tabla').DataTable().destroy();
            $('#contenido_preguntas').html('');
            $('#tabla_respuestas').DataTable().destroy();
            $('#contenido_respuestas').html('');
        }
        $('#agregar_pregunta').show();
        $('#agregar_respuesta').hide();
        var html = '';
        querySnapshot.forEach(function (doc)  {
            // doc.data() is never undefined for query doc snapshots
            var detalle = doc.data();
            detalle.tipo_pregunta.get().then(res => {
                    $('#'+doc.id).html(res.data().nombre);
                    switch (res.id) {
                        case '4gvyhjTBGnBtmx2r7PfG':
                        case 'AliRz6iQArJrBefmt46R':
                        case 'r9DVodTyZC14ftrfmXqe':
                        case 'JXTd4bGnI0D2RzfPfG0q':
                        case 'Ldn72WEoLPnC4zKAYUef':
                        case 'QUY3AnSP7ItU2JwcCnPo':
                        case 'hG99eHyIx7SgF7qjvjHA':
                        case 'cMhY5Sqf3AmoyKQxURSw':
                            $('#ver_respuestas_'+doc.id).remove();
                            break;
                        default:
                    }
            }).catch(
                err => console.error(err)
            );
            html += '<tr style="width: 150px;">' +
                '<td class="pt-2 pb-2">'+detalle.pregunta+'</td>' +
                '<td class="pt-2 pb-2" id="'+doc.id+'"></td>' +
                '<td class="pt-2 pb-2 text-center">' +
                    '<a title="Ver respuestas" class="btn btn-info btn-xs editar mr-2 lead_0_8" id="ver_respuestas_'+doc.id+'" href="javascript:void(0)" onclick="Respuestas(\''+doc.id+'\')"><i class="fa fa-eye"></i></a>' +
                    '<a title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="EditarPregunta(\''+doc.id+'\')"><i class="fa fa-edit"></i></a>' +
                    '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8"  href="javascript:void(0)" onclick="EliminarPregunta(\''+doc.id+'\')"><i class="fa fa-times"></i></a>'+
                '</td>' +
            '</tr>';
            $('#contenido_preguntas').html(html);*()
            $('#modal_preguntas_detalle').modal('show');

        });
        $('#preguntas_tabla').dataTable({
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
            "order": [[0, "desc"]]
        });
        $('#tabla_respuestas').dataTable({
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
            "order": [[0, "desc"]]
        });
    });*/
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 14/05/2020
 *	Nota: funcion para cargar los tipos de preguntas
 ***********************************************************************/
/*function CargarTipoPreguntas() {
    db.collection("tipo_preguntas").onSnapshot(function(querySnapshot) {
        $('#tipo_pregunta').empty();
        let index = 0;
        querySnapshot.forEach(function(doc) {
            if(index === 0){
                $("#tipo_pregunta").append(new Option('Seleccionar...', ''));
            }
            $("#tipo_pregunta").append(new Option(doc.data().nombre, doc.id));
            index++;
        });
    });
}*/
var edicion_pregunta = '',
    eliminar_pregunta = '';
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 14/05/2020
 *	Nota: Funcion para editar una pregunta seleccionada
 ***********************************************************************/
function EditarPregunta(id) {
    if (id !== '') {
        var datos = new FormData();
        datos.append('question_id', id)
        datos.append('quiz_id', $('#quiz_id').val());
        datos.append('question_admin', 'si');
        var config = {
            url: window.base_url + "questions/ListQuestionsQuiz",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            data: datos,
            success: function (response) {
                let data = response.data[0];
                editor.html.set(data.question);
                //$("#question").val(data.question);
                $("#type_id").val(data.type_id);
                $("#points").val(data.points);
            },
            error: function (response) {
                Swal.fire({
                    type: 'error',
                    title: 'Preguntas',
                    text: response.responseJSON.error_msg
                });
            }
        }
        $.ajax(config);
        /*$('#tipo_pregunta').attr("disabled",true);
        db.collection('preguntas').doc(id).get().then(function (doc) {
            doc.data().tipo_pregunta.get().then(res => {
                $('#tipo_pregunta').val(res.id).change();
            }).catch(
                err => console.error(err)
            );
            $('#pregunta').val(doc.data().pregunta);
        });*/
        edicion_pregunta = id;
    } else {
        editor.html.set('');
        $('#tipo_pregunta').attr("disabled", false);
        $('#form_preguntas').trigger("reset");
        edicion_pregunta = '';
    }
    $('#modal_preguntas').modal('show');
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 14/05/2020
 *	Nota: Funcion para guardar una pregunta nueva o editar una existente
 ***********************************************************************/
function GuardarPregunta(datos) {
    if (editor.html.get() !== '') {
        datos.append('question', editor.html.get());
    } else {
        Swal.fire({
            type: 'error',
            title: 'Preguntas',
            text: 'La pregunta no puede ser vacia'
        });
    }
    if (edicion_pregunta !== '') {
        datos.append('id', edicion_pregunta);
    }
    datos.append('quiz_id', $('#quiz_id').val());
    var config = {
        url: window.base_url + "questions/SaveQuestion",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: datos,
        success: function (response) {
            Swal.fire({
                type: 'success',
                title: 'Preguntas',
                text: response.msg
            }).then((result) => {
                $('#form_preguntas').trigger("reset");
                $('#modal_preguntas').modal('hide');
                tabla_preguntas.api().ajax.reload();
            });
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Preguntas',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
    /*if(edicion_pregunta !== ''){
        db.collection("preguntas").doc(edicion_pregunta).update({
            pregunta: datos[0].value
        }).then(function() {
            edicion_pregunta = '';
            $('#form_preguntas').trigger("reset");
            $('#modal_preguntas').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Preguntas',
                text: 'La pregunta ha sido actualizada correctamente'
            });
        }).catch(function(error) {
            console.error("Error writing document: ", error);
            edicion_pregunta = '';
            $('#form_preguntas').trigger("reset");
            $('#modal_preguntas').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Preguntas',
                text: 'La pregunta no ha sido actualizada correctamente'
            });
        });

    }else{
        db.collection("preguntas").add({
            pregunta: datos[0].value,
            tipo_pregunta: db.doc('/tipo_preguntas/' + datos[1].value),
            key_catalogo: id_catalogo_select,
            eliminado: false
        }).then(function() {
            edicion_pregunta = '';
            $('#form_preguntas').trigger("reset");
            $('#modal_preguntas').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Preguntas',
                text: 'La pregunta sido ha guardada correctamente'
            });
        }).catch(function(error) {
            console.error("Error writing document: ", error);
            edicion_pregunta = '';
            $('#form_preguntas').trigger("reset");
            $('#modal_preguntas').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Preguntas',
                text: 'La pregunta no ha sido guardada correctamente'
            });
        });
    }*/
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 13/05/2020
 *	Nota: funcion para eliminar un catalogo de preguntas
 ***********************************************************************/
function EliminarPregunta(id) {
    Swal.fire({
        title: 'Preguntas',
        text: "¿Estás seguro que deseas eliminar este registro?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            var datos = new FormData();
            datos.append('id', id);
            datos.append('active', 0);
            var config = {
                url: window.base_url + "questions/SaveQuestion",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: datos,
                success: function (response) {
                    Swal.fire({
                        type: 'success',
                        title: 'Preguntas',
                        text: response.msg
                    }).then((result) => {
                        tabla_preguntas.api().ajax.reload();
                    });
                },
                error: function (response) {
                    console.log(response)
                    Swal.fire({
                        type: 'error',
                        title: 'Preguntas',
                        text: response.responseJSON.error_msg
                    });
                }
            }
            $.ajax(config);
            /*db.collection("preguntas").doc(id).update({
                eliminado: true
            }).then(function() {
                Swal.fire({
                    type: 'success',
                    title: 'Preguntas',
                    text: 'La pregunta se ha borrado correctamente'
                });
            }).catch(function(error) {
                console.error("Error writing document: ", error);
                Swal.fire({
                    type: 'error',
                    title: 'Preguntas',
                    text: 'La pregunta no se ha borrado correctamente'
                });
            });*/
        }
    })
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 14/05/2020
 *	Nota: Funcion para obtener el detalle de preguntas
 ***********************************************************************/
var id_pregunta_select = '';
function Respuestas(id_pregunta, type_id) {
    $('#agregar_respuesta').hide();
    $('#question_id').val(id_pregunta);
    $('#type_id_answers').val(type_id);
    if (tabla_respuestas !== '') {
        $('#tabla_respuestas').DataTable().destroy();
    }
    tabla_respuestas = $('#tabla_respuestas').DataTable({
        ajax: {
            url: window.base_url + "questions/ListQuestionsQuiz",
            type: 'POST',
            data: function (d) {
                d.quiz_id = $('#quiz_id').val();
                d.question_id = $('#question_id').val();
                d.type_id = $('#type_id_answers').val();
            },
            complete: function (response) {
                if (response.responseJSON.data) {
                    if (type_id == 7/*Tache y paloma*/ && response.responseJSON.data.length == 1 || type_id == 10/*Dibujo*/ && response.responseJSON.data.length == 1 || type_id == 9/*Proporciones*/) {
                        $('#agregar_respuesta').hide();
                    } else {
                        $('#agregar_respuesta').show();
                    }
                } else {
                    $('#agregar_respuesta').show();
                }
            },
            dataType: 'json',
            dataSrc: 'data',
        },
        idSrc: "id",
        language: idiomaDataTable,
        columns: [
            {
                "data": "answer",
                render: function (data, type, full, meta) {
                    if (is_url(data)) {
                        return '<a href="' + data + '" target="_blank">Ver...</a>';
                    } else {
                        return data;
                    }
                }
            },
            {
                "data": "correct",
                render: function (data, type, full, meta) {
                    if (data == 1) {
                        return 'correcta';
                    } else {
                        return 'incorrecta';
                    }
                }
            },
            {
                "data": "id",
                render: function (data, type, full, meta) {
                    let html = '<a title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="EditarRespuesta(' + data + ')"><i class="fa fa-edit"></i></a>' +
                        '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8"  href="javascript:void(0)" onclick="EliminarRespuesta(' + data + ')"><i class="fa fa-times"></i></a>';
                    return html;
                }
            }
        ],
        pageLength: 20,
        dom: 'Bfrtip',
        buttons: ['excel'],
        order: [[0, "asc"]],
    });
    $('#modal_respuestas_detalle').modal('show');
    $('#agregar_respuesta').show();
    /*db.collection("respuestas").where("key_pregunta", "==", db.doc('/preguntas/' + id_pregunta)).where("eliminado","==",false).onSnapshot(function(querySnapshot) {
        if($('#tabla_respuestas').DataTable()){
            $('#tabla_respuestas').DataTable().destroy();
            $('#contenido_respuestas').html('');
        }
        $('#agregar_respuesta').show();
        var html = '';
        querySnapshot.forEach(function (doc)  {
            // doc.data() is never undefined for query doc snapshots
            var detalle = doc.data(),
                correcta = 'No';
            if(detalle.correcta){
                correcta = 'Sí'
            }
            html += '<tr>' +
                '<td class="pt-2 pb-2">'+detalle.respuesta+'</td>' +
                '<td class="pt-2 pb-2">'+correcta+'</td>' +
                '<td class="pt-2 pb-2 text-center">' +
                    '<a title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8" href="javascript:void(0)" onclick="EditarRespuesta(\''+doc.id+'\')"><i class="fa fa-edit"></i></a>' +
                    '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8"  href="javascript:void(0)" onclick="EliminarRespuesta(\''+doc.id+'\')"><i class="fa fa-times"></i></a>'+
                '</td>' +
            '</tr>';
            $('#contenido_respuestas').html(html);
            $('#modal_respuestas_detalle').modal('show');

        });
        $('#tabla_respuestas').dataTable({
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
            "order": [[0, "desc"]]
        });
    });*/
}
function is_url(str) {
    regexp = /^(?:(?:https?|ftp):\/\/)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/\S*)?$/;
    if (regexp.test(str)) {
        return true;
    }
    else {
        return false;
    }
}
var edicion_respuesta = '',
    eliminar_respuesta = '';
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 14/05/2020
 *	Nota: Funcion para editar una pregunta seleccionada
 ***********************************************************************/
function EditarRespuesta(id) {
    if (id !== '') {
        /*db.collection('respuestas').doc(id).get().then(function (doc) {
            var data = doc.data(),
                correcta = 0;
            $('#respuesta').val(data.respuesta);
            if(data.correcta){
                correcta = 1
            }
            $('#correcta').val(correcta).change();

        });*/
        var datos = new FormData();
        datos.append('id', id);
        var config = {
            url: window.base_url + "questions/AnswerDetail",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            data: datos,
            success: function (response) {
                let data = response.data[0];
                $('#correct').val(data.correct).change();
                if (
                    data.type_id === 10/*Dibujo*/ ||
                    data.type_id === 3/*Multiple imagen*/ ||
                    data.type_id === 5/*Imagen unica*/ ||
                    data.type_id === 7/*Tache paloma*/
                ) {
                    editor_respuesta.html.set('');
                    $('#answer').hide();
                    $('#answer_file').show();
                    $('#view_response').attr('href', data.description).show();
                } else {
                    editor_respuesta.html.set(data.description);
                    $('#answer').show();
                    $('#answer_file').hide();
                    $('#view_response').attr('href', '').hide();
                }
            },
            error: function (response) {
                Swal.fire({
                    type: 'error',
                    title: 'Preguntas',
                    text: response.responseJSON.error_msg
                });
            }
        }
        $.ajax(config);
        edicion_respuesta = id;
    } else {
        $('#form_respuestas').trigger("reset");
        editor_respuesta.html.set('');
        edicion_respuesta = '';
        if (
            $('#type_id_answers').val() == 10/*Dibujo*/ ||
            $('#type_id_answers').val() == 3/*Multiple imagen*/ ||
            $('#type_id_answers').val() == 5/*Imagen unica*/ ||
            $('#type_id_answers').val() == 7/*Tache paloma*/
        ) {
            $('#answer').hide();
            $('#answer_file').show();
            $('#view_response').hide();
        } else {
            $('#answer').show();
            $('#answer_file').hide();
            $('#view_response').hide();
        }
    }
    $('#modal_respuestas').modal('show');
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 14/05/2020
 *	Nota: Funcion para guardar el registro de una nueva respuesta
 ***********************************************************************/
function GuardarRespuesta(datos) {
    if (edicion_respuesta !== '') {
        datos.append('id', edicion_respuesta);
    }
    if (editor_respuesta.html.get() !== '') {
        datos.append('answer', editor_respuesta.html.get());
    } else {
        Swal.fire({
            type: 'error',
            title: 'Preguntas',
            text: 'La respuesta no puede ser vacia'
        });
    }
    datos.append('type_id', $('#type_id_answers').val());
    datos.append('question_id', $('#question_id').val());
    var config = {
        url: window.base_url + "questions/SaveAnswer",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: datos,
        success: function (response) {
            Swal.fire({
                type: 'success',
                title: 'Preguntas',
                text: response.msg
            }).then((result) => {
                $('#form_respuestas').trigger("reset");
                $('#modal_respuestas').modal('hide');
                tabla_respuestas.api().ajax.reload();
            });
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Preguntas',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
    /*if(edicion_respuesta !== ''){
        db.collection("respuestas").doc(edicion_respuesta).update({
            respuesta: datos[0].value,
            correcta: (datos[1].value == 1)?true:false
        }).then(function() {
            edicion_respuesta = '';
            $('#form_respuestas').trigger("reset");
            $('#modal_respuestas').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Respuestas',
                text: 'La respuesta ha sido actualizada correctamente'
            });
        }).catch(function(error) {
            console.error("Error writing document: ", error);
            edicion_pregunta = '';
            $('#form_respuestas').trigger("reset");
            $('#modal_respuestas').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Respuestas',
                text: 'La respuesta no ha sido actualizada correctamente'
            });
        });

    }else{
        db.collection("respuestas").add({
            correcta:(datos[1].value == 1)?true:false,
            eliminado: false,
            key_pregunta: db.doc('/preguntas/' + id_pregunta_select),
            respuesta: datos[0].value
        }).then(function() {
            edicion_pregunta = '';
            $('#form_respuestas').trigger("reset");
            $('#modal_respuestas').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Respuestas',
                text: 'La respuesta ha sido guardada correctamente'
            });
        }).catch(function(error) {
            console.error("Error writing document: ", error);
            edicion_pregunta = '';
            $('#form_respuestas').trigger("reset");
            $('#modal_respuestas').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Respuestas',
                text: 'La respuesta no ha sido guardada correctamente'
            });
        });
    }*/
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 15/06/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para eliminar un registro de repuestas
 ***********************************************************************/
function EliminarRespuesta(id) {
    Swal.fire({
        title: 'Resouestas',
        text: "¿Estás seguro que deseas eliminar este registro?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            var config = {
                url: window.base_url + "questions/eliminar_respuesta",
                type: "POST",
                cache: false,
                data: { id_respuesta: id },
                success: function (response) {
                    console.log(id)
                    Swal.fire({
                        type: 'success',
                        title: 'Respuestas',
                        text: response.msg
                    }).then((result) => {
                        // $('#form_respuestas').trigger("reset");
                        // $('#modal_respuestas').modal('hide');
                        tabla_respuestas.api().ajax.reload();
                    });
                },
                error: function (response) {
                    console.log(response)
                    Swal.fire({
                        type: 'error',
                        title: 'Preguntas',
                        text: response.responseJSON.error_msg
                    });
                }
            }
            $.ajax(config);
            // db.collection("respuestas").doc(id).update({
            //     eliminado: true
            // }).then(function() {
            //     Swal.fire({
            //         type: 'success',
            //         title: 'Respuestas',
            //         text: 'La respuesta se ha borrado correctamente'
            //     });
            // }).catch(function(error) {
            //     console.error("Error writing document: ", error);
            //     Swal.fire({
            //         type: 'error',
            //         title: 'Respuestas',
            //         text: 'La respuesta no se ha borrado correctamente'
            //     });
            // });
        }
    })
}

function get(id) {
    return document.getElementById(id);
}

function mostrarTablas(tipo) {
    elementos_usuarios = document.getElementsByClassName("usuarios");
    elementos_grupos = document.getElementsByClassName("grupos")
    // usuarios_library = []
    // grupos_cuestionarios = []
    mostrarUsuariosLibrary()
    mostrarGruposCuestionarios()
    if (tipo == 0) {
        for (var i = 0; i < elementos_usuarios.length; i++) {
            elementos_usuarios[i].style.display = "inherit"
            elementos_grupos[i].style.display = "none"
        }
    } else {
        for (var i = 0; i < elementos_usuarios.length; i++) {
            elementos_grupos[i].style.display = "inherit"
            elementos_usuarios[i].style.display = "none"
        }
    }
}


// ------------------------
// ------------------------
// ------------------------
//// a partir de aqui es para que funcione lo de agragar usuarios a los archivos de libreria
// ------------------------
// ------------------------
// ------------------------


function mostrarUsuariosFiltrados() {
    var filtrados = []
    var div = undefined
    var html = ""
    if (editando === true) {
        div = get("contenedor_usuarios_edicion")
        var filtro = get("buscador_edicion").value
    } else {
        div = get("contenedor_usuarios")
        var filtro = get("buscador").value
    }
    filtrados = usuarios.filter(f => (f.number_employee + "").substring(1) === filtro || (f.name + " " + f.last_name).toLowerCase().includes(filtro.toLowerCase()))
    for (var i = 0; i < filtrados.length; i++) {
        var usuario = filtrados[i]
        html += "<tr><td>" + usuario.name + " " + usuario.last_name + "</td><td><button class='btn btn-primary' onclick='agregarUsuario(" + usuario.id + ")'>Agregar</button></td></tr>"
    }
    div.innerHTML = html
}

var grupos = []
var usuarios = []
var usuarios_library = []
var grupos_cuestionarios = []
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

function generarSelectConGrupos() {
    // var select = get("grupos");
    var tabla = get("contenedor_grupos")
    var html = "<option value='0'>Todos</option>";
    var html_tabla = ""
    for (var i = 0; i < grupos.length; i++) {
        html += "<option value='" + grupos[i].id + "'>" + grupos[i].name + "</option>"
        html_tabla += "<tr>"
        html_tabla += "<td>" + grupos[i].name + "</td>"
        html_tabla += "<td><button class='btn btn-success' onclick='agregarGrupo(" + i + ")'>Agregar</button></td>"
        html_tabla += "</tr>"
    }
    console.log(html)
    tabla.innerHTML = html_tabla
    // select.innerHTML = html
}

function obtenerPorGrupo() {
    var id_grupo = get("grupos").value
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
    // var div_edicion = get_("contenedor_usuarios_cuestionario")
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
    if (edicion === '') {
        var usuario = usuarios.filter(f => f.id === id)[0]
        usuarios_library = usuarios_library || []
        if (!usuarios_library.some(u => { return u.id === usuario.id })) {
            usuarios_library.push(usuario)
        }
        mostrarUsuariosLibrary()
    } else {
        if (!usuarios_library.some(u => { return u.id === id })) {
            var usuario = usuarios.filter(f => f.id === id)[0]
            agregarUsuarioACapacitacion(id, usuario)
        }
    }
}

function agregarUsuarioACapacitacion(id, usuario) {
    console.log("entrando a agregar usuario a la capacitacion")
    // inhabilitarBotones()
    $.ajax({
        url: window.base_url + "Questions/agregarUsuario",
        type: 'POST',
        data: {
            user_id: id,
            quiz_id: edicion
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

//esta funcion es para editar el cuestionario
function agregarGrupoACuestionario(id, grupo) {
    console.log("entrando a agregar grupo al cuestionario")
    // inhabilitarBotones()
    $.ajax({
        url: window.base_url + "questions/agregarGrupo",
        type: 'POST',
        data: {
            group_id: id,
            quiz_id: edicion
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            console.log("lo que se regresa al agreagar al grupo", json)
            // usuarios_library = json.data
            grupos_cuestionarios.push(grupo)
            // habilitarBotones();
            mostrarGruposCuestionarios()
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

function agregarTodosGrupos() {
    // if (editando == false) {
    for (var i = 0; i < grupos.length; i++) {
        agregarGrupo(i)
    }
    // } else {

    // }
}

function mostrarUsuariosLibrary() {
    var div = get("contenedor_usuarios_cuestionarios");
    // var div_edicion = get("contenedor_usuarios_library_edicion")
    var html = "";
    for (var i = 0; i < usuarios_library.length; i++) {
        var usuario = usuarios_library[i];
        // if (usuario.id !== id_usuario_actual)
        html += "<tr><td>" + usuario.name + " " + (usuario.last_name || '') + "</td><td><button class='btn btn-danger' onclick='eliminarUsuario(" + i + ")'>Eliminar</button></td></tr>"
    }
    div.innerHTML = html;
    // div_edicion.innerHTML = html;
}


function eliminarUsuario(indice) {
    if (edicion === '') {
        usuarios_library.splice(indice, 1);
        mostrarUsuariosLibrary()
    } else {
        eliminarUsuarioDeLibrary(usuarios_library[indice].id, indice)
    }
}

function eliminarGrupo(indice) {
    console.log(edicion)
    if (edicion === '') {
        grupos_cuestionarios.splice(indice, 1);
        mostrarGruposCuestionarios()
    } else {
        eliminarGrupoDeCuestionario(grupos_cuestionarios[indice].id, indice)
    }
}

function eliminarTodos() {
    if (edicion === '') {
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
        url: window.base_url + "Questions/eliminarUsuario",
        type: 'POST',
        data: {
            user_id: id_usuario,
            quiz_id: edicion
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

function eliminarGrupoDeCuestionario(id_usuario, indice) {
    console.log("entrando a eliminar usuario de la capacitacion")
    // inhabilitarBotones()
    $.ajax({
        url: window.base_url + "Questions/eliminarGrupo",
        type: 'POST',
        data: {
            group_id: id_usuario,
            quiz_id: edicion
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            console.log("lo que se regresa al elimiar el usuario", json)
            // usuarios_library = json.data
            usuarios_library.splice(indice, 1)
            mostrarGruposCuestionarios()
            // habilitarBotones()
        }
    })
}

function agregarGrupo(indice) {
    console.log(edicion)
    if (edicion === '') {
        var grupo = grupos[indice]
        grupos_cuestionarios = grupos_cuestionarios || []
        if (!grupos_cuestionarios.some(u => { return u.id === grupo.id })) {
            grupos_cuestionarios.push(grupo)
        }
        mostrarGruposCuestionarios()
    } else {
        console.log("debe entrar aqui")
        if (!grupos_cuestionarios.some(u => { return u.id === grupos[indice].id }))
            agregarGrupoACuestionario(grupos[indice].id, grupos[indice])
    }
}

function mostrarGruposCuestionarios() {
    var div = get("contenedor_grupos_cuestionarios");
    // var div_edicion = get("contenedor_usuarios_library_edicion")
    var html = "";
    for (var i = 0; i < grupos_cuestionarios.length; i++) {
        var grupo = grupos_cuestionarios[i];
        // if (usuario.id !== id_usuario_actual)
        html += "<tr><td>" + grupo.name + "</td><td><button class='btn btn-danger' onclick='eliminarGrupo(" + i + ")'>Eliminar</button></td></tr>"
    }
    div.innerHTML = html;
    // div_edicion.innerHTML = html;
}