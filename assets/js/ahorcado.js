var id_frase = undefined
var frases_ = []
jQuery(document).ready(function ($) {
    obtenerFrases();
})

function obtenerFrases() {
    $.ajax({
        url: window.base_url + "Ahorcado/obtenerFrases",
        type: "POST",
        data: {},
        cache: false,
        success: function (response) {
            console.log(response)
            frases_ = response.data
            generarTablaFrases(response.data)
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function generarTablaFrases(frases) {
    var tabla = get("contenido_ahorcado")
    var html = ""
    for (var i = 0; i < frases.length; i++) {
        html += "<tr>"
        html += "<td>" + frases[i].frase + "</td>"
        html += "<td>" + frases[i].ocultas + "</td>"
        var botones = "<button class='btn btn-primary fa fa-edit' onclick='editar(" + i + ")'></button>" +
            "<button class='btn btn-danger fa fa-times' onclick='eliminar(" + frases[i].id + ")'></button>"
        html += "<td>" + botones + "</td>"
        html += "</tr>"
    }
    tabla.innerHTML = html;
}

function get(id) {
    return document.getElementById(id)
}

function editar(indice) {
    get("frase_edit").value = frases_[indice].frase
    get("ocultas_edit").value = frases_[indice].ocultas
    id_frase = frases_[indice].id
    $("#modal").modal("show")
}

function agregar() {
    $("#modal").modal("show")

}

function guardar() {
    data = {}
    data.frase = get("frase").value
    data.ocultas = get("ocultas").value
    var ocultas = get("ocultas").value
    var a = ocultas.split(",")
    if (a.length > 3) {
        alert("Las palabras ocultas no pueden ser mas de 3")
        return
    }
    $.ajax({
        url: window.base_url + "Ahorcado/guardarFrase",
        type: "POST",
        data: data,
        cache: false,
        success: function (response) {
            console.log(response)
            get("frase").value = ""
            get("ocultas").value = ""
            obtenerFrases()
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function actualizar() {
    data = {}
    data.id = id_frase
    data.frase = get("frase_edit").value
    data.ocultas = get("ocultas_edit").value
    $.ajax({
        url: window.base_url + "Ahorcado/editarFrase",
        type: "POST",
        data: data,
        cache: false,
        success: function (response) {
            console.log(response)
            obtenerFrases()
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function eliminar(id) {
    $.ajax({
        url: window.base_url + "Ahorcado/eliminarFrase",
        type: "POST",
        data: { id: id },
        cache: false,
        success: function (response) {
            console.log(response)
            obtenerFrases()
        },
        error: function (error) {
            console.log(error)
        }
    });
}