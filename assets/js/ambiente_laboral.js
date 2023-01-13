var editor = '',
    editor_respuesta = '',
    editor_respuesta = '';

jQuery(document).ready(function ($) {
    obtener_preguntas()
    obtener_fechas()
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
})

var id_pregunta;
var id_quiz;
var type_id;
var points;
var preguntas = []

function obtener_preguntas() {
    var config = {
        url: window.base_url + "Questions/obtener_quiz_a_l",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: {},
        success: function (response) {
            console.log(response)
            preguntas = response.extras.quiz_ambiente_laboral
            generar_tabla_preguntas(response.extras.quiz_ambiente_laboral)
        },
        error: function (response) {
            console.log(response)
            Swal.fire({
                type: 'error',
                title: 'Notificacón',
                text: 'Error al obtener preguntas de ambiente laboral'
            });
        }
    }
    $.ajax(config);
}

function generar_tabla_preguntas(datos) {
    datos = datos || []
    var tabla = get("contenedor_preguntas")
    var html = ''
    for (var i = 0; i < datos.length; i++) {
        html += "<tr>"
        html += "<td>" + datos[i].question + "</td>"
        var inputs = ""
        inputs += "<label class='dias' id='label_l" + i + "' onclick='seleccionar(0," + i + ")'> l</label><input type='checkbox' id='l" + i + "' " + (datos[i].dias[0].lunes ? 'checked' : '') + ">"
        inputs += "<label class='dias' id='label_m" + i + "' onclick='seleccionar(1," + i + ")'> m</label><input type='checkbox' id='m" + i + "' " + (datos[i].dias[0].martes ? 'checked' : '') + ">"
        inputs += "<label class='dias' id='label_mi" + i + "' onclick='seleccionar(2," + i + ")'> mi</label><input type='checkbox' id='mi" + i + "' " + (datos[i].dias[0].miercoles ? 'checked' : '') + ">"
        inputs += "<label class='dias' id='label_j" + i + "' onclick='seleccionar(3," + i + ")'> j</label><input type='checkbox' id='j" + i + "' " + (datos[i].dias[0].jueves ? 'checked' : '') + ">"
        inputs += "<label class='dias' id='label_v" + i + "' onclick='seleccionar(4," + i + ")'> v</label><input type='checkbox' id='v" + i + "' " + (datos[i].dias[0].viernes ? 'checked' : '') + ">"
        inputs += "<label class='dias' id='label_s" + i + "' onclick='seleccionar(5," + i + ")'> s</label><input type='checkbox' id='s" + i + "' " + (datos[i].dias[0].sabado ? 'checked' : '') + ">"
        inputs += "<label class='dias' id='label_d" + i + "' onclick='seleccionar(6," + i + ")'> d</label><input type='checkbox' id='d" + i + "' " + (datos[i].dias[0].domingo ? 'checked' : '') + ">"
        html += "<td>" + inputs + "</td>"
        var btn_respuestas = ""
        if (datos[i].answers_type == 1) {
            btn_respuestas = "<button class='btn btn-info' onclick='Respuestas(" + datos[i].id + "," + datos[i].type_id + ")'><i class='fa fa-search'></i></button>"
        }
        html += "<td>" + btn_respuestas + "<button class='btn btn-primary' onclick='editar(" + datos[i].id + ")'><i class='fa fa-edit'></i></button><button class='btn btn-danger' onclick='eliminar(" + datos[i].id + ")'><i class='fa fa-trash'></i></button></td>"
        html += "</tr>"
    }
    tabla.innerHTML = html
    marcar_seleccionados(datos)
}

function editar(id) {
    id_pregunta = id
    $("#modal_edicion").modal("show")
    var pregunta = preguntas.filter(f => f.id === id)[0]
    id_quiz = pregunta.quiz_id
    type_id = pregunta.type_id
    points = pregunta.points
    get("pregunta").value = pregunta.question;
    console.log(pregunta)
}

function seleccionar(dia, dia_l) {
    // get(dia + dia_l).checked = true
    console.log("entrando")
    dias = ['l', 'm', 'mi', 'j', 'v', 's', 'd'];
    dia = dias[dia];
    console.log(dia)
    console.log(dia_l)
    if (get(dia + dia_l).checked) {
        get(dia + dia_l).checked = false;
        get("label_" + dia + dia_l).classList.remove('seleccionado')
    }
    else{
        get(dia + dia_l).checked = true;
        get("label_" + dia + dia_l).classList.add('seleccionado')
    }
}

function marcar_seleccionados(datos) {
    for (var i = 0; i < datos.length; i++) {
        if (datos[i].dias[0].lunes) {
            get("label_l" + i).classList.add('seleccionado')
        }
        if (datos[i].dias[0].martes) {
            get("label_m" + i).classList.add('seleccionado')
        }
        if (datos[i].dias[0].miercoles) {
            get("label_mi" + i).classList.add('seleccionado')
        }
        if (datos[i].dias[0].jueves) {
            get("label_j" + i).classList.add('seleccionado')
        }
        if (datos[i].dias[0].viernes) {
            get("label_v" + i).classList.add('seleccionado')
        }
        if (datos[i].dias[0].sabado) {
            get("label_s" + i).classList.add('seleccionado')
        }
        if (datos[i].dias[0].domingo) {
            get("label_d" + i).classList.add('seleccionado')
        }
    }
}

function eliminar(id) {
    id_pregunta = id
    var pregunta = preguntas.filter(f => f.id === id)[0]
    id_quiz = pregunta.quiz_id
    type_id = pregunta.type_id
    points = pregunta.points
    data = {};
    data.question = pregunta.question
    data.id = id_pregunta;
    data.quiz_id = id_quiz;
    data.type_id = type_id
    data.points = points
    data.active = 0
    get("pregunta").value = pregunta.question;
    console.log(pregunta)
    console.log(pregunta)
    if (confirm("Estas seguro de eliminar esta pregunta")) {
        var config = {
            url: window.base_url + "Questions/SaveQuestion",
            type: "POST",
            data: data,
            success: function (response) {
                console.log(response)
                obtener_preguntas()
                $("#modal_edicion").modal("hide")
            },
            error: function (response) {
                console.log(response)
                Swal.fire({
                    type: 'error',
                    title: 'Notificacón',
                    text: 'Error al realizar la peticion'
                });
            }
        }
        $.ajax(config);
    }
}

function actualizar() {
    data = {};
    data.question = get("pregunta").value;
    data.id = id_pregunta;
    data.quiz_id = id_quiz;
    data.type_id = type_id
    data.points = points
    var config = {
        url: window.base_url + "Questions/SaveQuestion",
        type: "POST",
        data: data,
        success: function (response) {
            console.log(response)
            obtener_preguntas()
            $("#modal_edicion").modal("hide")
        },
        error: function (response) {
            console.log(response)
            Swal.fire({
                type: 'error',
                title: 'Notificacón',
                text: 'Error al realizar la peticion'
            });
        }
    }
    $.ajax(config);
}

function guardar_fechas() {
    var fecha_inicio = get("fecha_inicio").value
    var fecha_fin = get("fecha_fin").value
    if (!fecha_inicio && !fecha_fin) {
        alert("Define un rango de fechas valido");
        return
    }
    var config = {
        url: window.base_url + "Ambiente_laboral/insertar_fechas",
        type: "POST",
        data: { fecha_inicio: fecha_inicio, fecha_fin: fecha_fin },
        success: function (response) {
            console.log(response)
            Swal.fire({
                type: 'success',
                title: 'Notificacón',
                text: 'Rango de fechas guardado'
            });
        },
        error: function (response) {
            console.log(response)
            Swal.fire({
                type: 'error',
                title: 'Notificacón',
                text: 'Error al realizar la peticion'
            });
        }
    }
    $.ajax(config);
}

function obtener_fechas() {
    var config = {
        url: window.base_url + "Ambiente_laboral/obtener_fechas",
        type: "POST",
        data: {},
        success: function (response) {
            console.log(response)
            mostrar_fechas(response.data)
        },
        error: function (response) {
            console.log(response)

        }
    }
    $.ajax(config);
}

function mostrar_fechas(fechas) {
    if (fechas.length > 0) {
        get("fecha_inicio").value = fechas[0].fecha_inicio
        get("fecha_fin").value = fechas[0].fecha_fin
    }
}

function guardar_dias() {
    var datos = []
    for (var i = 0; i < preguntas.length; i++) {
        var dato = {}
        dato.id = preguntas[i].id
        dato.lunes = get("l" + i).checked ? 1 : 0
        dato.martes = get("m" + i).checked ? 1 : 0
        dato.miercoles = get("mi" + i).checked ? 1 : 0
        dato.jueves = get("j" + i).checked ? 1 : 0
        dato.viernes = get("v" + i).checked ? 1 : 0
        dato.sabado = get("s" + i).checked ? 1 : 0
        dato.domingo = get("d" + i).checked ? 1 : 0
        datos.push(dato)
    }
    var config = {
        url: window.base_url + "Ambiente_laboral/guardar_dias_preguntas",
        type: "POST",
        data: { datos: datos },
        success: function (response) {
            console.log(response)
            Swal.fire({
                type: 'success',
                title: 'Notificacón',
                text: 'Guardado con exito'
            });
        },
        error: function (response) {
            console.log(response)
            Swal.fire({
                type: 'error',
                title: 'Notificacón',
                text: 'Error al guardar'
            });

        }
    }
    $.ajax(config);

}

function nueva_pregunta() {
    $("#modal_preguntas").modal("show")
}


function GuardarPregunta(datos) {

    datos = new FormData();
    datos.append("question", get("question").value)
    datos.append("points", get("points").value)
    datos.append("type_id", get("type_id").value)
    datos.append('quiz_id', 34);
    var config = {
        url: window.base_url + "questions/SaveQuestionAL",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: datos,
        success: function (response) {
            Swal.fire({
                type: 'success',
                title: 'Preguntas',
                text: "Guardada con exito"
            })
            obtener_preguntas()
            $("#modal_pregunta").modal("show")
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Preguntas',
                text: "Error al guardar"
            });
        }
    }
    $.ajax(config);
}

function Respuestas(id_pregunta, type_id) {
    $('#agregar_respuesta').hide();
    $('#question_id').val(id_pregunta);
    $('#type_id_answers').val(type_id);
    if (tabla_respuestas !== '') {
        $('#tabla_respuestas').DataTable().destroy();
    }
    tabla_respuestas = $('#tabla_respuestas').dataTable({
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
        "ajax": {
            "url": window.base_url + "questions/ListQuestionsQuiz",
            "type": 'POST',
            "data": function (d) {
                d.quiz_id = 34;
                d.question_id = $('#question_id').val();
                d.type_id = $('#type_id_answers').val();
            },
            complete: function (a) {
                if (a.responseJSON.data) {
                    if (type_id == 7/*Tache y paloma*/ && a.responseJSON.data.length == 1 || type_id == 10/*Dibujo*/ && a.responseJSON.data.length == 1 || type_id == 9/*Proporciones*/) {
                        $('#agregar_respuesta').hide();
                    } else {
                        $('#agregar_respuesta').show();
                    }
                } else {
                    $('#agregar_respuesta').show();
                }
            },
            error: function (xhr, error, code) {
                console.log(xhr)
                tabla_respuestas.api().clear().draw();
            }
        }
    });
    $('#modal_respuestas_detalle').modal('show');
    $('#agregar_respuesta').show();

}

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

}

function get(id) {
    return document.getElementById(id)
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