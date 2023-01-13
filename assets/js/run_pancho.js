var _temas = []
var palabras_buenas = []
var palabras_malas = []
var editando = false
jQuery(document).ready(function ($) {
    obtenerTemas();
})

function obtenerTemas() {
    $.ajax({
        url: window.base_url + "Run_pancho_run/obtenerTemas",
        type: "POST",
        data: {},
        cache: false,
        success: function (response) {
            // tabla_companias.ajax.reload();
            console.log(response)
            generarTablaTemas(response.data)
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function generarTablaTemas(temas) {
    _temas = temas
    var tabla = document.getElementById("contenido_tabla_run_pancho")
    var html = ''
    for (var i = 0; i < temas.length; i++) {
        html += '<tr>'
        html += '<td>' + temas[i].nombre + '</td>'
        html += '<td>' + concatenarPalabras(i, 1) + '</td>'
        html += '<td>' + concatenarPalabras(i, 0) + '</td>'
        html += '<td><button class="btn btn-primary fa fa-edit" onclick="editar(' + i + ')"></button><button class="btn btn-danger fa fa-times" onclick="eliminar(' + temas[i].id + ')"></button></td>'
        html += '</tr>'
    }
    console.log(html)
    tabla.innerHTML = html
}

function concatenarPalabras(indice, tipo) {
    var palabras = ''
    console.log(_temas[indice])
    for (var j = 0; j < _temas[indice].palabras.palabras.length; j++) {
        console.log(j)
        if (_temas[indice].palabras.palabras[j].tipo === tipo) {
            palabras += _temas[indice].palabras.palabras[j].word + ', '
        }
    }
    return palabras
}

function agregar() {
    $("#nuevo_tema").modal("show")
}

function guardar() {
    if (editando === true) {
        actualizar()
    } else {
        var datos = {};
        datos.nombre = document.getElementById("nombre").value
        var _palabras_buenas = []
        var _palabras_malas = []
        for (var i = 0; i < palabras_buenas.length; i++) {
            _palabras_buenas.push({ word: palabras_buenas[i], tipo: 1 })
        }
        for (var i = 0; i < palabras_malas.length; i++) {
            _palabras_malas.push({ word: palabras_malas[i], tipo: 0 })
        }
        datos.palabras = _palabras_buenas.concat(_palabras_malas)
        console.log(datos.palabras)
        $.ajax({
            url: window.base_url + "Run_pancho_run/guardar",
            type: "POST",
            data: datos,
            cache: false,
            success: function (response) {
                $("#nuevo_tema").modal("hide")
                resetearCampos()
                console.log(response)
                obtenerTemas();
            },
            error: function (error) {
                console.log(error)
            }
        });
    }
}

function agregarPalabra(tipo) {
    if (tipo == 0) {
        var palabra = document.getElementById("txt_palabras_malas").value
        if (palabra !== "" && palabra !== undefined)
            palabras_malas.push(palabra)
        document.getElementById("txt_palabras_malas").value = ""
    } else {
        var palabra = document.getElementById("txt_palabras_buenas").value
        if (palabra !== "" && palabra !== undefined)
            palabras_buenas.push(palabra)
        document.getElementById("txt_palabras_buenas").value = ""
    }
    dibujar_palabras();
}

function dibujar_palabras() {
    var div_buenas = document.getElementById("palabras_buenas")
    var div_malas = document.getElementById("palabras_malas")
    var html = ''
    for (var i = 0; i < palabras_buenas.length; i++) {
        html += '<label>' + palabras_buenas[i] + '<button class="btn_quitar" onclick="quitarPalabra(' + 1 + ',' + i + ')">x</button><label>, '
    }
    div_buenas.innerHTML = html
    html = ''
    for (var i = 0; i < palabras_malas.length; i++) {
        html += '<label>' + palabras_malas[i] + '<button class="btn_quitar" onclick="quitarPalabra(' + 0 + ',' + i + ')">x</button><label>, '
    }
    div_malas.innerHTML = html
}

function quitarPalabra(tipo, indice) {
    if (tipo === 0) {
        palabras_malas.splice(indice, 1)
    } else {
        palabras_buenas.splice(indice, 1)
    }
    dibujar_palabras();
}

function resetearCampos() {
    document.getElementById("nombre").value = ""
    document.getElementById("txt_palabras_buenas").value = ""
    document.getElementById("txt_palabras_malas").value = ""
    palabras_buenas = []
    palabras_malas = []
    dibujar_palabras();
}

function eliminar(id) {
    $.ajax({
        url: window.base_url + "Run_pancho_run/eliminar",
        type: "POST",
        data: { id_tema: id },
        cache: false,
        success: function (response) {
            console.log(response)
            obtenerTemas();
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function editar(indice) {
    resetearCampos()
    editando = true
    var tema = _temas[indice]
    id_editado = tema.id
    document.getElementById("nombre").value = tema.nombre
    for (var i = 0; i < tema.palabras.palabras.length; i++) {
        var palabra = tema.palabras.palabras[i]
        if (palabra.tipo === 0) {
            palabras_malas.push(palabra.word)
        } else {
            palabras_buenas.push(palabra.word)
        }
    }
    dibujar_palabras();
    $("#nuevo_tema").modal("show")
}

function actualizar() {
    var datos = {};
    datos.nombre = document.getElementById("nombre").value
    var _palabras_buenas = []
    var _palabras_malas = []
    for (var i = 0; i < palabras_buenas.length; i++) {
        _palabras_buenas.push({ word: palabras_buenas[i], tipo: 1 })
    }
    for (var i = 0; i < palabras_malas.length; i++) {
        _palabras_malas.push({ word: palabras_malas[i], tipo: 0 })
    }
    datos.palabras = _palabras_buenas.concat(_palabras_malas)
    datos.id = id_editado
    console.log(datos)
    $.ajax({
        url: window.base_url + "Run_pancho_run/editar",
        type: "POST",
        data: { datos: datos },
        cache: false,
        success: function (response) {
            $("#nuevo_tema").modal("hide")
            resetearCampos()
            console.log(response)
            obtenerTemas();
        },
        error: function (error) {
            console.log(error)
        }
    });
}