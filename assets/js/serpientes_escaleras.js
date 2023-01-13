var respuestas = [];
var preguntas = []
var editando = false
var question_id = null

jQuery(document).ready(function ($) {
    obtenerPreguntas();
})

function obtenerPreguntas() {
    $.ajax({
        url: window.base_url + "Snakestairs/obtenerPreguntas",
        type: "POST",
        data: {},
        cache: false,
        success: function (response) {
            // tabla_companias.ajax.reload();
            console.log(response)
            preguntas = response.data
            generarTablaPreguntas(response.data)
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function generarTablaPreguntas(preguntas) {
    var tabla = get("contenido_tabla_serpientes_escaleras")
    var html = "";
    for (var i = 0; i < preguntas.length; i++) {
        html += "<tr>"
        html += "<td>" + preguntas[i].question + "</td>"
        html += "<td>" + concatenarRespuestas(preguntas[i].respuestas) + "</td>"
        var botones = "<button class='btn btn-primary' onclick='editar(" + i + ")'><i class='fa fa-edit'></i></button>"
        botones += "<button class='btn btn-danger' onclick='eliminarPregunta(" + preguntas[i].id + ")'><i class='fa fa-times'></i></button>"
        html += "<td>" + botones + "</td>"
        html += "</tr>"
    }
    tabla.innerHTML = html
}

function concatenarRespuestas(respuestas) {
    var cadena = "";
    for (var i = 0; i < respuestas.length; i++) {
        cadena += "<p>" + respuestas[i].answer + "</p>"
    }
    return cadena;
}

function eliminarPregunta(id) {
    $.ajax({
        url: window.base_url + "Snakestairs/eliminarPregunta",
        type: "POST",
        data: { id_pregunta: id },
        cache: false,
        success: function (response) {
            console.log(response)
            obtenerPreguntas()
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function agregar() {
    $("#nueva_pregunta").modal("show")
    editando = false
}

function editar(indice) {
    $("#nueva_pregunta").modal("show")
    get("pregunta").value = preguntas[indice].question
    respuestas = preguntas[indice].respuestas
    construirRespuestas()
    editando = true
    question_id = preguntas[indice].id
}

function agregarRespuesta() {
    var respuesta = {}
    respuesta.answer = get("respuesta").value
    respuesta.correct = get("es_correcta").value
    respuestas.push(respuesta)
    get("respuesta").value = ""
    if (editando == true) {
        guardarRespuesta(respuesta)
    } else {
        construirRespuestas();
    }
}

function guardarRespuesta(respuesta) {
    console.log(respuesta)
    respuesta.question_id = question_id;
    $.ajax({
        url: window.base_url + "Snakestairs/agregarRespuesta",
        type: "POST",
        data: respuesta,
        cache: false,
        success: function (response) {
            console.log(response)
            construirRespuestas()
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function construirRespuestas() {
    var tabla = get("contenedor_respuestas")
    var html = "";
    for (var i = 0; i < respuestas.length; i++) {
        html += "<tr>"
        html += "<td>" + respuestas[i].answer + "</td>"
        html += "<td>" + (respuestas[i].correct == 1 ? 'Si' : 'No') + "</td>"
        html += "<td><button class='btn btn-danger' onclick='eliminarRespuesta(" + i + ")'><i class='fa fa-times'></i></button></td>"
        html += "</tr>"
    }
    tabla.innerHTML = html
}

function eliminarRespuesta(indice) {
    var id = respuestas[indice].id
    respuestas.splice(indice, 1)
    if (editando == false) {
        construirRespuestas()
    } else {
        eliminarRespuesta_(id);
    }
}

function eliminarRespuesta_(id) {
    console.log(id)
    $.ajax({
        url: window.base_url + "Snakestairs/eliminarRespuesta",
        type: "POST",
        data: { id_respuesta: id },
        cache: false,
        success: function (response) {
            console.log(response)
            construirRespuestas()
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function guardar() {
    var pregunta = get("pregunta").value
    if (editando == true) {
        actualizarPregunta(pregunta)
    } else {
        $.ajax({
            url: window.base_url + "Snakestairs/agregarPregunta",
            type: "POST",
            data: { question: pregunta, respuestas: respuestas },
            cache: false,
            success: function (response) {
                console.log(response)
                obtenerPreguntas()
                resetearCampos()
                cerrarModal()
            },
            error: function (error) {
                console.log(error)
            }
        });
    }
}

function actualizarPregunta(pregunta) {
    $.ajax({
        url: window.base_url + "Snakestairs/actualizarPregunta",
        type: "POST",
        data: { question: pregunta, id: question_id },
        cache: false,
        success: function (response) {
            console.log(response)
            obtenerPreguntas()
            resetearCampos()
            cerrarModal()
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function resetearCampos() {
    respuestas = []
    get("pregunta").value = "";
}

function cerrarModal() {
    $("#nueva_pregunta").modal("hide")
}

function get(id) {
    return document.getElementById(id);
}