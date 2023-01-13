jQuery(document).ready(function ($) {
    obtenerMensajes()
})

function obtenerMensajes() {
    $.ajax({
        url: window.base_url + "User/obtenerMensajesContacto",
        type: "POST",
        data: {},
        cache: false,
        success: function (response) {
            console.log(response)
            generarTablaMensajes(response.data)

        },
        error: function (error) {
            console.log(error)
        }
    })
}

function eliminarMensaje(id_mensaje) {
    $.ajax({
        url: window.base_url + "User/eliminarMensajeContacto",
        type: "POST",
        data: { id_mensaje: id_mensaje },
        cache: false,
        success: function (response) {
            console.log(response)
            obtenerMensajes()
        },
        error: function (error) {
            console.log(error)
        }
    })
}

function generarTablaMensajes(mensajes) {
    var tabla = get("contenedor_mensajes")
    var html = ""
    for (var i = 0; i < mensajes.length; i++) {
        html += "<tr>"
        html += "<td>" + mensajes[i].name + "</td>"
        html += "<td>" + mensajes[i].number_employee + "</td>"
        html += "<td>" + mensajes[i].mensaje + "</td>"
        // html += "<td>" + mensajes[i].email + "</td>"
        html += "<td><button class='btn btn-danger' onclick='eliminarMensaje(" + mensajes[i].id_mensaje + ")'>Eliminar</button></td>"
        html += "</tr>"
    }
    tabla.innerHTML = html
}

function get(id) {
    return document.getElementById(id)
}