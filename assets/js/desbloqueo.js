jQuery(document).ready(function ($) {
    obtenerBloqueados()
})

function obtenerBloqueados() {
    $.ajax({
        url: window.base_url + "Bloqueados/obtenerBloqueados",
        type: "POST",
        data: {},
        success: function (response) {
            console.log(response)
            construir_tabla(response.data)
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function construir_tabla(bloqueados) {
    var tabla = get("contenedor_bloqueados")
    var html = ""
    for (var i = 0; i < bloqueados.length; i++) {
        html += "<tr>"
        html += "<td>" + bloqueados[i].name + "</td>"
        html += "<td>" + bloqueados[i].number_employee + "</td>"
        html += "<td><button class='btn btn-success' onclick='desbloquear(" + bloqueados[i].id + ")'>Desbloquear</button></td>"
        html += "</tr>"
    }
    tabla.innerHTML = html
}

function desbloquear(id) {
    $.ajax({
        url: window.base_url + "Bloqueados/desbloquear",
        type: "POST",
        data: { id: id },
        success: function (response) {
            obtenerBloqueados()
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function desbloquear_todos(){
    $.ajax({
        url: window.base_url + "Bloqueados/desbloquear_todos",
        type: "POST",
        data: { },
        success: function (response) {
            console.log(response)
            obtenerBloqueados()
            Swal.fire({
                type: 'success',
                title: 'Desbloqueo',
                text: 'Se han desbloqueado todos los usuarios'
            });
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function get(id) {
    return document.getElementById(id)
}