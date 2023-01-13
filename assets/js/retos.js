var grupos = []
var usuarios = []
var usuarios_a_retar = []
var archivos = []
var id_reto = undefined
var id_usuario = undefined
var retos_ = []
var editando = false
jQuery(document).ready(function ($) {
    obtenerRetos();
    obtenerRetosCalificar()
    obtenerUsuarios();
    obtenerGrupos();
})

function obtenerRetos() {
    $.ajax({
        url: window.base_url + "Retos/obtenerRetosAdmin",
        type: "POST",
        data: {},
        cache: false,
        success: function (response) {
            // tabla_companias.ajax.reload();
            console.log(response)
            retos_ = response.data
            generarTablaRetos(response.data)
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function generarTablaRetos(retos) {

    console.log(retos)
    var tabla = get("contenido_tabla_retos")
    var html = ""
    for (var i = 0; i < retos.length; i++) {
        html += "<tr>"
        html += "<td>" + retos[i].nombre + "</td>";
        html += "<td>" + retos[i].objetivo + "</td>";
        html += "<td>" + retos[i].descripcion + "</td>";
        html += "<td>" + retos[i].usuario + "</td>";
        if (retos[i].imagenes.length > 0)
            html += "<td><a class='enlace' onclick='verImagenes(" + i + ")'>Ver ...</a></td>";
        else
            html += "<td></td>"
        html += "<td>" + (retos[i].tipo === 0 ? 'usuarios' : 'empresa') + "</td>";
        if (retos[i].tipo === 0) {
            html += '<td></td>'
        } else {
            html += "<td><button class='btn btn-primary fa fa-edit' onclick='editar(" + i + ")'></button><button class='btn btn-danger fa fa-times' onclick='eliminarReto(" + retos[i].id + ")'></button></td>";
        }
        html += "</tr>"
    }
    tabla.innerHTML = html
}

function verImagenes(indice) {
    var imagenes = retos_[indice].imagenes
    var div = get("imagenes")
    var html = ""
    for (var i = 0; i < imagenes.length; i++) {
        html += '<div class="col-4">'
        html += "<img class='col-12' src='" + imagenes[i].imagen + "'>"
        html += "</div>"
    }
    div.innerHTML = html
    $("#modal_imagenes").modal("show")
    //aqui falta eliminar las imagenes
}

function agregar() {
    $("#nuevo_reto").modal("show")
}

function get(id) {
    return document.getElementById(id);
}

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
    var select = get("grupos");
    var html = "<option value='0'>Todos</option>";
    for (var i = 0; i < grupos.length; i++) {
        html += "<option value='" + grupos[i].id + "'>" + grupos[i].name + "</option>"
    }
    console.log(html)
    select.innerHTML = html
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
                usuario_a_retar = []
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
    var html = "";
    for (var i = 0; i < usuarios.length; i++) {
        var usuario = usuarios[i];
        html += "<tr><td>" + (usuario.name || '') + " " + (usuario.last_name || '') +
            "</td><td><input type='checkbox' id='check" + i + "'></td></tr>"
    }
    div.innerHTML = html;
}

function almacenarArchivo() {
    var archivo = get("imagen").files[0]
    archivos.push(archivo);
    get("imagen").value = ""
    mostrarPreview()
}

function mostrarPreview() {
    var div = get("contenedor_preview");
    div.innerHTML = ""
    var html = ""
    for (var i = 0; i < archivos.length; i++) {
        html += "<div style='position:relative; float:left; display: inline-block;'><img width='100' height='100' style='border-radius:70%' src='" + URL.createObjectURL(archivos[i]) + "'><button class='boton' onclick='eliminarPreview(" + i + ")'>x</button></div>"
    }
    div.innerHTML = html
}

function eliminarPreview(indice) {
    archivos.splice(indice, 1)
    mostrarPreview()
}

function guardar() {
    if (editando === false ) {
        for (var i = 0; i < usuarios.length; i++) {
            var checked = get("check" + i).checked
            console.log(checked)
            if (checked == true) {
                usuarios_a_retar.push(usuarios[i].id)
            }
        }
        console.log(usuarios_a_retar)
        data = {}
        data.nombre = get("nombre").value
        data.objetivo = get("objetivo").value
        data.descripcion = get("descripcion").value
        data.colaboradores = usuarios_a_retar
        $.ajax({
            url: window.base_url + "Retos/crearReto",
            type: "POST",
            data: data,
            cache: false,
            success: function (response) {
                console.log(response)
                obtenerRetos()
                id_reto = response.data
                agregarImagenes()
                cerrarModal()
            },
            error: function (error) {
                console.log(error)
            }
        });
    } else {
        actualizar()
    }
}

function agregarImagenes() {
    var data = new FormData()
    data.append("imagen", archivos[0])
    data.append("id_reto", id_reto)
    console.log("entrando a guardar imagen")
    $.ajax({
        url: window.base_url + "Retos/guardarImagenReto",
        type: "POST",
        data: data,
        cache: false,
        processData: false,
        contentType: false,
        success: function (response) {
            archivos.splice(0, 1)
            if (archivos.length > 0)
                agregarImagenes()
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function cerrarModal() {
    $("#nuevo_reto").modal("hide")
    usuarios_a_retar = []
    get("nombre").value = ""
    get("objetivo").value = ""
    get("descripcion").value = ""
}

function eliminarReto(id) {
    $.ajax({
        url: window.base_url + "Retos/eliminarReto",
        type: 'POST',
        data: {
            id_reto: id
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            usuarios = json.data
            obtenerRetos()
        }
    })
}

function editar(indice) {
    console.log("entrando a editar")
    id_reto = retos_[indice].id
    editando = true
    get("nombre").value = retos_[indice].nombre
    get("objetivo").value = retos_[indice].objetivo
    get("descripcion").value = retos_[indice].descripcion
    $("#nuevo_reto").modal("show")
    var ocultos = document.getElementsByClassName("ocultos");
    for (var i = 0; i < ocultos.length; i++) {
        ocultos[i].style.display = "none"
    }
}

function actualizar() {
    var ocultos = document.getElementsByClassName("ocultos");
    for (var i = 0; i < ocultos.length; i++) {
        ocultos[i].style.display = "none"
    }
    var data = {}
    data.nombre = get("nombre").value
    data.objetivo = get("objetivo").value
    data.descripcion = get("descripcion").value
    data.id = id_reto
    $.ajax({
        url: window.base_url + "Retos/actualizarReto",
        type: 'POST',
        data: data,
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            usuarios = json.data
            obtenerRetos()
            cerrarModal();
        }
    })
}

get("imagen").onchange = evt => {
    almacenarArchivo()
}

function mostrarRetos(tipo) {
    if (tipo === 1) {
        get("retos").style.display = "block"
        get("retos_calificar").style.display = "none"
    } else {
        get("retos").style.display = "none"
        get("retos_calificar").style.display = "block"
    }
}

function obtenerRetosCalificar() {
    $.ajax({
        url: window.base_url + "Retos/obtenerRetosCalificar",
        type: "POST",
        data: { tipo: 1 },
        cache: false,
        success: function (response) {
            // tabla_companias.ajax.reload();
            console.log(response)
            retos_c = response.data
            generarTablaRetosCalificar(response.data)
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function generarTablaRetosCalificar(retos) {
    console.log(retos)
    var tabla = get("contenido_calificar")
    var html = ""
    for (var i = 0; i < retos.length; i++) {
        html += "<tr>"
        html += "<td>" + retos[i].nombre + "</td>";
        html += "<td>" + retos[i].objetivo + "</td>";
        html += "<td>" + retos[i].descripcion + "</td>";
        html += "<td>" + retos[i].retado + "</td>";
        if (retos[i].imagenes.length > 0)
            html += "<td><a class='enlace' onclick='verImagenes(" + i + ")'>Ver Reporte...</a></td>";
        else
            html += "<td></td>"
        html += "<td><button class='btn btn-primary' onclick='calificar(" + retos[i].id + "," + retos[i].id_retado + ")'>Calificar</button></td>";
        html += "</tr>"
    }
    tabla.innerHTML = html
}

function calificar(id, id_user) {
    id_reto = id
    id_usuario = id_user
    $("#modal_calificar").modal("show")
}

function guardar_calificacion() {
    var data = {}
    data.id_reto = id_reto
    data.id_usuario = id_usuario
    data.desempeno = get("desempeno_1").value
    data.desempeno2 = get("desempeno_2").value
    data.desempeno3 = get("desempeno_3").value
    data.desempeno4 = get("desempeno_4").value
    data.actitud = get("actitud_1").value
    data.actitud2 = get("actitud_2").value
    data.actitud3 = get("actitud_3").value
    data.actitud4 = get("actitud_4").value
    console.log(data)
    $.ajax({
        url: window.base_url + "Retos/CalificarReto",
        type: "POST",
        data: data,
        cache: false,
        success: function (response) {
            // tabla_companias.ajax.reload();
            console.log(response)
            obtenerRetosCalificar()
            cerrarModalCalificacion()
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function cerrarModalCalificacion() {
    $("#modal_calificar").modal("hide")
    get("desempeno").value = ""
    get("desempeno_2").value = ""
    get("desempeno_3").value = ""
    get("desempeno_4").value = ""
    get("actitud").value = ""
    get("actitud_2").value = ""
    get("actitud_3").value = ""
    get("actitud_4").value = ""
    id_reto = undefined
    id_retado = undefined
}