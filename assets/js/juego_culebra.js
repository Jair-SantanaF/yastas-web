var _temas = []
var palabras_buenas = []
var palabras_malas = []
var editando = false
jQuery(document).ready(function ($) {
    obtenerTemas();
})


// funcion para obtener los temas disponibles
function obtenerTemas() {
    $.ajax({
        url: window.base_url + "Game_snake/obtenerTemas",
        type: "POST",
        data: {},
        cache: false,
        success: function (response) {
            console.log('obtener temas');
            console.log(response)
            generarTablaTemas(response.data)
        },
        error: function (error) {
            console.log(error)
        }
    });
}


// generar la tabla con los temas disponibles
function generarTablaTemas(temas) {
    _temas = temas
    var tabla = document.getElementById("contenido_tabla_juego_culebra")
    var html = ''
    for (var i = 0; i < temas.length; i++) {
        html += '<tr>'
        html += '<td>' + temas[i].nombre + '</td>'
        html += '<td>' + concatenarPalabras(i, 1) + '</td>'
        html += '<td>' + concatenarPalabras(i, 0) + '</td>'
        html += '<td><button class="btn btn-primary m-2 fa fa-edit" onclick="editar(' + i + ')"></button><button class="btn btn-danger fa fa-times" onclick="eliminar(' + temas[i].id + ')"></button></td>'
        html += '</tr>'
    }
    tabla.innerHTML = html
}


// funcion auxiliar para concatenar las palabras dependiendo si son buenas o malas
function concatenarPalabras(indice, tipo) {
    var palabras = ''
    for (var j = 0; j < _temas[indice].palabras.palabras.length; j++) {
        if (_temas[indice].palabras.palabras[j].tipo === tipo) {
            palabras += _temas[indice].palabras.palabras[j].palabra + ', '
        }
    }
    return palabras
}


function agregar() {
    editando = false;
    $("#nuevo_tema").modal("show")
}

// funcion para guardar un nuevo tema
function guardar() {
    if (editando === true) {
        actualizar();
    } else {
        var datos = {};
        datos.nombre = document.getElementById("nombre").value
        var _palabras_buenas = []
        var _palabras_malas = []
        for (var i = 0; i < palabras_buenas.length; i++) {
            _palabras_buenas.push({ palabra: palabras_buenas[i], tipo: 1 })
        }
        for (var i = 0; i < palabras_malas.length; i++) {
            _palabras_malas.push({ palabra: palabras_malas[i], tipo: 0 })
        }
        datos.palabras = _palabras_buenas.concat(_palabras_malas)
        $.ajax({
            url: window.base_url + "Game_snake/guardar",
            type: "POST",
            data: datos,
            cache: false,
            success: function (response) {
                if(response.status_code && response.success){
                    Swal.fire({
                        title: "Éxito",
                        text: response.msg,
                        type: 'success'
                    });
                    
                    obtenerTemas();
                    $("#nuevo_tema").modal("hide")
                    resetearCampos();
    
                }else{
                    Swal.fire({
                        title: "Error",
                        text: response.msg,
                        type: 'error'
                    });
                }
            },
            error: function (error) {
                console.log(error)
            }
        });
    }
}


// funcion para agregar una palabra al arreglo de palabras
function agregarPalabra(tipo) {
    if (tipo == 0) {
        var palabra = document.getElementById("txt_palabras_malas").value;
        if (palabra !== "" && palabra !== undefined){
            palabras_malas.push(palabra);
        }

        document.getElementById("txt_palabras_malas").value = "";
    } else {
        var palabra = document.getElementById("txt_palabras_buenas").value;
        if (palabra !== "" && palabra !== undefined){
            palabras_buenas.push(palabra);
        }

        document.getElementById("txt_palabras_buenas").value = "";
    }
    dibujar_palabras();
}


// funcion para dibujar las palabras en los contenedores correspondientes del modal
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


// funcion para retirar la palabra del arreglo de palabras
function quitarPalabra(tipo, indice) {
    if (tipo === 0) {
        palabras_malas.splice(indice, 1)
    } else {
        palabras_buenas.splice(indice, 1)
    }
    dibujar_palabras();
}


// funcion para limpiar los campos del modal
function resetearCampos() {
    document.getElementById("nombre").value = ""
    document.getElementById("txt_palabras_buenas").value = ""
    document.getElementById("txt_palabras_malas").value = ""
    palabras_buenas = []
    palabras_malas = []
    dibujar_palabras();
}


// funcion para eliminar un tema del juego
function eliminar(id) {
    $.ajax({
        url: window.base_url + "Game_snake/eliminar",
        type: "POST",
        data: { id_tema: id },
        cache: false,
        success: function (response) {
            if(response.status_code == 200&& response.success){
                Swal.fire({
                    title: "Éxito",
                    text: response.msg,
                    type: 'success'
                });
                obtenerTemas();
            }else{
                Swal.fire({
                    title: "Error",
                    text: response.msg,
                    type: 'error'
                });
            }

        },
        error: function (error) {
            console.log(error)
        }
    });
}


// funcion para cuando se edita un tema
function editar(indice) {
    resetearCampos()
    editando = true;
    var tema = _temas[indice]
    id_editado = tema.id
    document.getElementById("nombre").value = tema.nombre;
    for (var i = 0; i < tema.palabras.palabras.length; i++) {
        var palabra = tema.palabras.palabras[i];
        if (palabra.tipo === 0) {
            palabras_malas.push(palabra.palabra);
        } else {
            palabras_buenas.push(palabra.palabra);
        }
    }
    dibujar_palabras();
    $("#nuevo_tema").modal("show")
}


// función para cuando se actualiza un tema
function actualizar() {
    var datos = {};
    datos.nombre = document.getElementById("nombre").value
    var _palabras_buenas = []
    var _palabras_malas = []
    for (var i = 0; i < palabras_buenas.length; i++) {
        _palabras_buenas.push({ palabra: palabras_buenas[i], tipo: 1 });
    }
    for (var i = 0; i < palabras_malas.length; i++) {
        _palabras_malas.push({ palabra: palabras_malas[i], tipo: 0 });
    }
    datos.palabras = _palabras_buenas.concat(_palabras_malas);
    datos.id = id_editado;

    $.ajax({
        url: window.base_url + "Game_snake/editar",
        type: "POST",
        data: {datos: datos},
        cache: false,
        success: function (response) {
            if(response.status_code && response.success){
                Swal.fire({
                    title: "Éxito",
                    text: response.msg,
                    type: 'success'
                });
                
                obtenerTemas();
                $("#nuevo_tema").modal("hide")
                resetearCampos();

            }else{
                Swal.fire({
                    title: "Error",
                    text: response.msg,
                    type: 'error'
                });
            }
        },
        error: function (error) {
            console.log(error)
        }
    });
}