jQuery(document).ready(function () {
    obtenerCapacitaciones();
    obtenerMenuElementos();
    obtenerUsuarios();
    obtenerGrupos();
});

var capacitaciones,
    capacitaciones_originales,
    capacitacion,
    capacitacion_detalle,
    servicios_contratados,
    menu_elementos,
    catalogo_actual,
    id_catalogo_actual,
    categoria_actual,
    elementos,
    elementos_capacitacion,
    usuarios,
    usuarios_capacitacion,
    editando,
    imagen_actualizada,
    grupos;

$('#subir_imagen').on('click', function () {
    $("#imagen").trigger("click");
});

$('#subir_imagen_edicion').on('click', function () {
    $("#imagen_edicion").trigger("click");
});

function loadImagen(event) {
    var url = URL.createObjectURL(event.target.files[0]);

    var file = event.target.files[0];

    $('#nombre_imagen').val(file.name);
}

function loadImagenEdicion(event) {
    var url = URL.createObjectURL(event.target.files[0]);

    var file = event.target.files[0];

    $('#nombre_imagen_edicion').val(file.name);
    imagen_actualizada = true
}

function obtenerGrupos() {
    $.ajax({
        url: window.base_url + "Groups/GroupsRegister",
        type: 'POST',
        data: {

        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log('error', xhr)
        },
        success: function (json) {
            grupos = json.data
            generarSelectConGrupos()
        }
    })
}

// function generarSelectConGrupos() {
//     var select = get("grupos");
//     var html = "<option value='0'>Todos</option>";
//     for (var i = 0; i < grupos.length; i++) {
//         html += "<option value='" + grupos[i].id + "'>" + grupos[i].name + "</option>"
//     }
//     select.innerHTML = html
// }

function obtenerCapacitaciones() {
    $.ajax({
        url: window.base_url + "Capacitacion/getCapacitacionesAdmin",
        type: 'POST',
        data: {

        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log('error', xhr)
        },
        success: function (json) {
            capacitaciones = json.data
            capacitaciones_originales = JSON.parse(JSON.stringify(json.data))
            generarTablaCapacitaciones()
        }
    })
}

function verDetalles(id_capacitacion) {
    $.ajax({
        url: window.base_url + "Capacitacion/getCapacitacionByID",
        type: 'POST',
        data: {
            id_capacitacion: id_capacitacion
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log('error', xhr)
        },
        success: function (json) {
            capacitacion = json.data
            mostrarDetallesDeCapacitacion();
        }
    })
}

function obtenerParaEditar(id_capacitacion) {
    $.ajax({
        url: window.base_url + "Capacitacion/getCapacitacionByID",
        type: 'POST',
        data: {
            id_capacitacion: id_capacitacion
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log('error', xhr)
        },
        success: function (json) {
            capacitacion = json.data
            editarCapacitacion();
        }
    })
}

function mostrarDetallesDeCapacitacion() {

    get("nombre_capacitacion").innerHTML = capacitacion.name
    get("descripcion_capacitacion").innerHTML = capacitacion.description
    get("imagen_capacitacion").src = capacitacion.image
    var tabla = get("contenedor_elementos_detalle")
    var html = "";
    for (var i = 0; i < capacitacion.elementos.length; i++) {
        var elemento = capacitacion.elementos[i]
        html += '<tr><td>' + (elemento.title || elemento.name) + '</td>'
        html += '<td>' + elemento.label + '</td>'
        html += '<td>' + elemento.order + '</td>'
        html += '<td><a href="' + (elemento.file || elemento.audio || 'N/A') + '" target="_blank">Ver</a></td></tr>'
    }
    tabla.innerHTML = html;

    tabla = get("contenedor_usuarios_detalle")
    html = ""
    for (var i = 0; i < capacitacion.usuarios.length; i++) {
        var usuario = capacitacion.usuarios[i]
        if (usuario.id !== id_usuario_actual)
            html += "<tr><td>" + usuario.name + " " + usuario.last_name + "</td><td><button class='btn btn-primary' onclick='obtenerDetallesUsuario(" + usuario.id + ")'>Detalles</button></td></tr>"
    }
    tabla.innerHTML = html

    //AQUI METEMOS LOS GRUPOS
    var div_grupos = get("contenedor_grupos_detalle")
    var html = "";

    for (var i = 0; i < grupos_cuestionarios.length; i++) {
        var grupo = grupos_cuestionarios[i];
        // if (usuario.id !== id_usuario_actual)
        html += "<tr><td>" + grupo.name + "</td><td><button class='btn btn-danger' onclick='eliminarGrupo(" + i + ")'>Eliminar</button></td></tr>"
    }
    div_grupos.innerHTML = html;
    ///////////////////////////////////

    editando = false;
    $('#modal_detalles').modal('show');
}

function obtenerDetallesUsuario(id_usuario) {
    $.ajax({
        url: window.base_url + "Capacitacion/getDetailAdmin",
        type: 'POST',
        data: {
            capacitacion_id: capacitacion.id,
            user_id: id_usuario
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log('error', xhr)
        },
        success: function (json) {
            capacitacion_detalle = json.data
            mostrarDetalleUsuario()
        }
    })
}

function mostrarDetalleUsuario() {
    $("#modal_detalles_usuario").modal("show");
    var tabla = get("contenedor_detalles_usuario")
    var html = "";
    for (var i = 0; i < capacitacion_detalle.elementos.length; i++) {
        var elemento = capacitacion_detalle.elementos[i]
        for (var j = 0; j < elemento.items.length; j++) {
            var item = elemento.items[j];
            html += "<tr><td>" + item.title + "</td>"
            html += "<td>" + elemento.categoria + "</td>"
            var estado = item.ejecutado ? "Completo" : "Pendiente";
            if (item.ejecutado)
                html += "<td class='completo'>" + estado + "</td>"
            else
                html += "<td class='pendiente'>" + estado + "</td>"
            html += "</tr>"
        }
    }
    tabla.innerHTML = html;
}

function obtenerMenuElementos() {
    $.ajax({
        url: window.base_url + "Ws/HiredServices",
        type: 'POST',
        data: {

        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log('error', xhr)
        },
        success: function (json) {
            servicios_contratados = json.data
            generarMenuElementos()
            generarMenuHTML()
        }
    })
}

function generarMenuElementos() {
    menu_elementos = [];
    for (var i = 0; i < servicios_contratados.length; i++) {
        var servicio = servicios_contratados[i];
        if (servicio.service_id === 4) {
            menu_elementos.push({ nombre: servicio.service_name, tipo: 1 })
        }
        else if (servicio.service_id == 13) {
            menu_elementos.push({ nombre: servicio.service_name, tipo: 2 })
        }
        else if (servicio.service_id == 14) {
            menu_elementos.push({ nombre: servicio.service_name, tipo: 3 })
        }
        else if (servicio.service_id == 11) {
            menu_elementos.push({ nombre: servicio.service_name, tipo: 4 })
        }
        else if (servicio.service_id == 5) {
            menu_elementos.push({ nombre: servicio.service_name, tipo: 5 })
        }
        else if (servicio.service_id == 12) {
            menu_elementos.push({ nombre: servicio.service_name, tipo: 9 })
        }
    }
}

function generarMenuHTML() {
    var menu = get("menu_items");
    var menu_edicion = get("menu_items_edicion");
    var html = "";
    if (editando == false) {
        elementos_capacitacion = []
        mostrarElementosAgregados()
    }
    for (var i = 0; i < menu_elementos.length; i++) {
        if (get("tipo").value == 1 && menu_elementos[i].tipo == 5) {

        } else
            html += "<button class='btn btn-secondary' onclick='obtenerElementosPorTipo(" + menu_elementos[i].tipo + ")'>" + menu_elementos[i].nombre + "</button>"
    }
    menu.innerHTML = html + "<div id='elementos_'></div>";
    menu_edicion.innerHTML = html + "<div id='elementos_edicion_'></div>";
    //obtenerElementosPorTipo(menu_elementos[0].tipo)
}

function obtenerElementosPorTipo(tipo) {
    if (get("tipo").value == -1) {
        alert("Selecciona un tipo de capacitación")
        return;
    }
    var url = obtenerUrl(tipo);
    console.log(url)
    elementos = [];
    $.ajax({
        url: window.base_url + url,
        type: 'POST',
        data: {
            dato: 0
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            elementos = json.data;
            mostrarElementos(elementos)
        }
    })
}

function obtenerUrl(tipo) {
    var url = "";
    var tipo_ = editando == true ? capacitacion.tipo : get("tipo").value
    switch (tipo) {
        case 1:
            if (tipo_ == 1)
                url = "Library/obtener_biblioteca_capacitacion"
            else
                url = "Library/ListLibrary"
            catalogo_actual = "library_elements_"
            id_catalogo_actual = 1
            categoria_actual = "Biblioteca"
            break;
        case 2:
            if (tipo_ == 1)
                url = "Podcast/obtener_podcasts_capacitaciones"
            else
                url = "Podcast/ListPodcast"
            catalogo_actual = "podcast"
            id_catalogo_actual = 2
            categoria_actual = "Podcast"
            break;
        case 3:
            if (tipo_ == 1)
                url = "Com/obtener_comunidades_capacitaciones"
            else
                url = "Com/getTopics"
            catalogo_actual = "com_topics"
            id_catalogo_actual = 3
            categoria_actual = "Comunidad de aprendizaje"
            break;
        case 4:
            if (tipo_ == 1)
                url = "Questions/obtener_quiz_capacitaciones"
            else
                url = "Questions/ListQuiz"
            catalogo_actual = "question_quiz"
            id_catalogo_actual = 4
            categoria_actual = "Preguntas"
            break;
        case 5:
            url = "Games/ListGamesSelect"
            catalogo_actual = "games"
            id_catalogo_actual = 5
            categoria_actual = "Juegos"
            break;
        case 9:
            if (tipo_ == 1)
                url = "Eleraning/obtener_elearnings_capacitaciones"
            else
                url = "Elearning/elearningModules"
            catalogo_actual = "elearning_modules"
            id_catalogo_actual = 9
            categoria_actual = "Elearning"
            break;
    }
    return url;
}

function mostrarElementos(elementos) {
    var div = document.getElementById("elementos_");
    var div_edicion = get("elementos_edicion_")
    var html = "<table class='table'><tr><th>Nombre</th><th>Tipo</th><th></th></tr>";
    for (var i = 0; i < elementos.length; i++) {
        elemento = elementos[i];
        if (elemento.active == 1 || elemento.active === undefined)
            html += "<tr><td>" + (elemento.title || elemento.name) + "</td><td>" + (elemento.type || elemento.name_category || "N/A") + "</td><td><button class='btn btn-primary' onclick='agregarACapacitacion(" + i + ")'>Agregar</button></td></tr>"
    }
    html += "</table>"
    div.innerHTML = html;
    div_edicion.innerHTML = html;
}

function agregarACapacitacion(indice) {
    var elemento = JSON.parse(JSON.stringify(elementos[indice]));
    elemento.catalogo = catalogo_actual;
    elemento.id_catalogo = id_catalogo_actual;
    elemento.categoria = categoria_actual
    if (editando === false) {
        elementos_capacitacion = elementos_capacitacion || [];
        if (!elementos_capacitacion.some(e => { return e.catalogo === catalogo_actual && e.id === elemento.id })) {
            elementos_capacitacion.push(elemento)
        }
        mostrarElementosAgregados()
    } else {
        elementos_capacitacion = elementos_capacitacion || [];
        console.log(elementos_capacitacion)
        if (!elementos_capacitacion.some(e => { return obtenerIdCatalogo(e.catalog) === id_catalogo_actual && e.id === elemento.id })) {
            agregarACapacitacion_(elemento);
        }
    }
}

function mostrarEA() {

    var div = document.getElementById("elementos_agregados");
    // var div_edicion = document.getElementById("elementos_agregados_edicion")

    // <th>Orden</th>
    var html = "<table class='table'><tr><th>Nombre</th><th>Categoria</th><th>Orden</th></tr>";
    for (var i = 0; i < elementos_capacitacion.length; i++) {
        var elemento = elementos_capacitacion[i];
        html += "<tr><td>" + (elemento.title || elemento.name) + "</td>";
        html += "<td>" + (elemento.categoria || elemento.label) + "</td>";
        html += "<td><input style='width:100px' type='number' value=" + elemento.order + " id='elemento" + i + "' onchange='establecerOrden(" + i + ")' required></td>";
        html += "<td><button class='btn btn-danger' onclick='eliminarDeCapacitacion(" + i + ")'>Eliminar</button></td></tr>";
    }
    //aqui falta ordenar la tabla
    if (editando == false)
        div.innerHTML = html + "</table>";
    else
        // div_edicion.innerHTML = html + "</table>"
        $("#elementos_agregados_edicion2").html(html + "</table>");
}


function mostrarElementosAgregados() {

    var div = document.getElementById("elementos_agregados")
    var div_edicion = document.getElementById("elementos_agregados_edicion")
    console.log(elementos_capacitacion)
    // <th>Orden</th>
    var html = "<table class='table'><tr><th>Nombre</th><th>Categoria</th><th>Orden</th></tr>"
    for (var i = 0; i < elementos_capacitacion.length; i++) {
        var elemento = elementos_capacitacion[i]
        html += "<tr><td>" + (elemento.title || elemento.name) + "</td>"
        html += "<td>" + (elemento.categoria || elemento.label) + "</td>"
        html += "<td><input style='width:100px' type='number' value=" + elemento.order + " id='elemento" + i + "' onchange='establecerOrden(" + i + ")' required></td>"
        html += "<td><button class='btn btn-danger' onclick='eliminarDeCapacitacion(" + i + ")'>Eliminar</button></td></tr>"
    }
    //aqui falta ordenar la tabla
    console.log(editando)
    if (editando == false)
        div.innerHTML = html + "</table>"
    else
        div_edicion.innerHTML = html + "</table>"
}



function establecerOrden(indice) {
    var orden = get("elemento" + indice).value
    console.log(orden)
    elementos_capacitacion[indice].order = orden
    console.log(elementos_capacitacion)
}

function eliminarDeCapacitacion(indice) {
    if (editando === false) {
        elementos_capacitacion.splice(indice, 1);
        mostrarElementosAgregados()
    } else {
        var elemento = elementos_capacitacion[indice]
        console.log(elementos_capacitacion)
        eliminarElementoDeCapacitacion(elemento)
    }
}

function obtenerPorGrupo() {
    var id_grupo = get("grupos").value
    if (id_grupo !== 0) {
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
            console.log(usuarios)
            mostrarUsuarios()
        }
    })
}

function mostrarUsuarios() {
    var div = document.getElementById("contenedor_usuarios")
    var div_edicion = get("contenedor_usuarios_edicion")
    var html = "";

    for (var i = 0; i < usuarios.length; i++) {
        var usuario = usuarios[i];
        if (usuario.id !== id_usuario_actual)
            html += "<tr><td>" + usuario.name + " " + usuario.last_name + "</td><td><button class='btn btn-primary' onclick='agregarUsuario(" + i + ")'>Agregar</button></td></tr>"
    }
    //actualmente no funciona, se cambio por buscador, la tabla se llena con el filtro
    // div.innerHTML = html;
    // div_edicion.innerHTML = html;
}

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

function agregarUsuario(id) {
    if (editando === false) {
        console.log(usuarios)
        var usuario = usuarios.filter(f => f.id === id)[0]
        usuarios_capacitacion = usuarios_capacitacion || []
        if (usuario.id !== id_usuario_actual) {
            if (!usuarios_capacitacion.some(u => { return u.id === usuario.id })) {
                usuarios_capacitacion.push(usuario)
            }
        } else {
            alert("Este usuario crea la capacitación, se agrega automaticamente");
        }
        mostrarUsuariosCapacitacion()
    } else {
        if (id !== id_usuario_actual) {
            if (!usuarios_capacitacion.some(u => { return u.id === id }))
                agregarUsuarioACapacitacion(id)
        } else {
            alert("Este usuario creó la capacitación, se agrega automaticamente");
        }
    }
}

function agregarTodos() {
    for (var i = 0; i < usuarios.length; i++) {
        agregarUsuario(i)
    }
}

function mostrarUsuariosCapacitacion() {
    var div = get("contenedor_usuarios_capacitacion");
    var div_edicion = get("contenedor_usuarios_capacitacion_edicion")
    var html = "";
    for (var i = 0; i < usuarios_capacitacion.length; i++) {
        var usuario = usuarios_capacitacion[i];
        if (usuario.id !== id_usuario_actual)
            html += "<tr><td>" + usuario.name + " " + usuario.last_name + "</td><td><button class='btn btn-danger' onclick='eliminarUsuario(" + i + ")'>Eliminar</button></td></tr>"
    }
    div.innerHTML = html;
    div_edicion.innerHTML = html;
}
// finalizar
function eliminarUsuario(indice) {
    if (editando === false) {
        usuarios_capacitacion.splice(indice, 1);
        mostrarUsuariosCapacitacion()
    } else {
        eliminarUsuarioDeCapacitacion(usuarios_capacitacion[indice].id)
    }
}

function eliminarTodos() {
    usuarios_capacitacion = []
    mostrarUsuariosCapacitacion()
}

function generarTablaCapacitaciones() {
    var tabla = document.getElementById("contenido_elementos");
    var html = "";
    if (capacitaciones.length > 0)
        for (i = 0; i < capacitaciones.length; i++) {
            var texto = capacitaciones[i].active == 1 ? "Deshabilitar" : "Habilitar";
            var clase = capacitaciones[i].active == 1 ? "" : "eliminado";
            var botones = '<div class="pt-2 pb-2 text-center">' +
                '<a title="Ver detalles" class="btn btn-info btn-xs editar mr-2 lead_0_8 text-white" onclick="verDetalles(' + capacitaciones[i].id + ')" cmd="pasos"><i cmd="pasos" class="fa fa-eye"></i></a>' +
                '<a title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8 text-white" onclick="obtenerParaEditar(' + capacitaciones[i].id + ')" cmd="editar"><i cmd="editar" class="fa fa-edit"></i></a>' +
                '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8 text-white" onclick="eliminarCapacitacion(' + capacitaciones[i].id + ')" cmd="eliminar">' + texto + '</a>' +
                '</div>';
            html += "<tr class='" + clase + "'><td>" + capacitaciones[i].name + "</td><td>" + capacitaciones[i].description + "</td><td>" + capacitaciones[i].fecha_limite + "</td><td>" + capacitaciones[i].fecha_programada + "</td><td>" + botones + "</td></tr>";
        }
    tabla.innerHTML = html;
}

function agregarCapacitacion() {
    $('#modal_new_capacitacion').modal('show');
    editando = false
    get("txt_nombre").value = "";
    get("txt_descripcion").value = "";
    get("nombre_imagen").value = "";
    get("imagen").value = null;
    usuarios_capacitacion = []
    elementos_capacitacion = []
    mostrarElementosAgregados()
    mostrarUsuariosCapacitacion()
    get("submit").disabled = false;
}

function mostrar(id) {
    var divs = document.getElementsByClassName("extras");
    for (var i = 0; i < divs.length; i++) {
        divs[i].style.display = "none";
    }
    document.getElementById(id).style.display = "block"
}

$("#submit").click(function (event) {
    get("submit").disabled = true;
    if (get("tipo").value == -1) {
        alert("Selecciona un tipo de capacitación")
        return;
    }
    if (validarRequeridos()) {
        var datos = new FormData();//{};
        var foto = $('#imagen')
        var archivos = foto[0].files
        file = archivos[0]
        //datos.usuarios = JSON.parse(JSON.stringify(usuarios_capacitacion));
        var usuario = usuarios.filter(u => { return u.id === id_usuario_actual })[0];


        var usuario_ = JSON.parse(JSON.stringify(usuarios_capacitacion));
        usuario_ = usuario_ || [];
        usuario_.push(usuario)
        console.log(usuario)
        console.log(usuario_)

        datos.append("usuarios", JSON.stringify(usuario_));
        datos.append("grupos", JSON.stringify(grupos_cuestionarios))
        elementos_capacitacion = elementos_capacitacion || [];
        datos.append("elementos_capacitacion", JSON.stringify(elementos_capacitacion));
        datos.append("nombre", get("txt_nombre").value)
        datos.append("descripcion", get("txt_descripcion").value)
        datos.append("imagen", file);
        datos.append("name_image", get("nombre_imagen").value)
        datos.append("fecha_limite", get("fecha_limite").value)
        datos.append("tipo", get("tipo").value)
        datos.append("fecha_programada", get("fecha_programada").value)
        for (var key of datos.entries()) {
            console.log(key[0] + ', ' + key[1]);
        }
        $.ajax({
            url: window.base_url + "Capacitacion/save",
            type: 'POST',
            contentType: false,
            data: datos,
            dataType: 'json',
            cache: false,
            processData: false,
            error: function (xhr, error, code) {
                console.log(xhr)
                // console.log(error)
                // console.log(code)
                $('#modal_new_capacitacion').modal('hide');
                alert("Error al guardar la capacitacion")
            },
            success: function (json) {
                // usuarios = json.data
                // mostrarUsuarios()
                obtenerCapacitaciones()
                $('#modal_new_capacitacion').modal('hide');
            }
        })
    }
})

function validarRequeridos() {
    var bandera = true;
    if (editando === false) {
        var nombre = document.getElementById("txt_nombre");
        var descripcion = document.getElementById("txt_descripcion");
        if (!nombre.value || !descripcion.value) {
            bandera = false;
            mostrarRequeridos(nombre, descripcion);
        }
    } else {
        var nombre = get("txt_nombre_edicion");
        var descripcion = get("txt_descripcion_edicion")
        if (!nombre.value || !descripcion.value) {
            bandera = false;
            mostrarRequeridos(nombre, descripcion)
        }
    }
    return bandera;

}

function mostrarRequeridos(nombre, descripcion) {
    if (!nombre.value) {
        nombre.style.borderColor = "red";
    }
    if (!descripcion.value) {
        descripcion.style.borderColor = "red";
    }
}

function escribiendoNombre() {
    document.getElementById("txt_nombre").style.borderColor = "#ccc";
}

function escribiendoDescripcion() {
    document.getElementById("txt_descripcion").style.borderColor = "#ccc";
}

function get(id) {
    return document.getElementById(id);
}

function eliminarCapacitacion(id) {
    $.ajax({
        url: window.base_url + "Capacitacion/deleteCapacitacion",
        type: 'POST',
        data: {
            id: id
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log('error', xhr)
        },
        success: function (json) {
            obtenerCapacitaciones()
        }
    })
}

function editarCapacitacion() {
    editando = true
    get("txt_nombre_edicion").value = capacitacion.name
    get("txt_descripcion_edicion").innerHTML = capacitacion.description
    get("imagen_edicion").src = capacitacion.image
    get("nombre_imagen_edicion").value = capacitacion.image_name
    get("fecha_limite_edicion").value = capacitacion.fecha_limite
    get("fecha_programada_edicion").value = capacitacion.fecha_programada
    get("tipo").value = capacitacion.tipo
    elementos_capacitacion = capacitacion.elementos

    usuarios_capacitacion = capacitacion.usuarios
    grupos_cuestionarios = capacitacion.grupos
    imagen_actualizada = false;

    mostrarElementosAgregados()
    //mostrarEA();

    mostrarGruposCuestionarios()
    mostrarUsuariosCapacitacion()
    mostrarUsuarios();
    generarSelectConGrupos()
    generarMenuHTML()

    $('#modal_edit_capacitacion').modal('show');
}

function inhabilitarBotones() {
    var botones = document.getElementsByClassName('btn')
    for (var i = 0; i < botones.length; i++) {
        botones[i].disabled = true
    }
}

function habilitarBotones() {
    var botones = document.getElementsByClassName('btn')
    for (var i = 0; i < botones.length; i++) {
        botones[i].disabled = false
    }
}

function agregarUsuarioACapacitacion(id) {
    inhabilitarBotones()
    $.ajax({
        url: window.base_url + "Capacitacion/agregarUsuario",
        type: 'POST',
        data: {
            id_usuario: id,
            id_capacitacion: capacitacion.id
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            usuarios_capacitacion = json.data
            habilitarBotones();
            mostrarUsuariosCapacitacion()
        }
    })
}

function eliminarUsuarioDeCapacitacion(id_usuario) {
    inhabilitarBotones()
    $.ajax({
        url: window.base_url + "Capacitacion/eliminarUsuario",
        type: 'POST',
        data: {
            id_usuario: id_usuario,
            id_capacitacion: capacitacion.id
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            usuarios_capacitacion = json.data
            mostrarUsuariosCapacitacion()
            habilitarBotones()
        }
    })
}

function agregarACapacitacion_(elemento) {
    inhabilitarBotones()
    $.ajax({
        url: window.base_url + "Capacitacion/agregarElemento",
        type: 'POST',
        data: {
            elemento: elemento,
            id_capacitacion: capacitacion.id
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            elementos_capacitacion = json.data

            mostrarElementosAgregados()
            habilitarBotones()
        }
    })
}

function eliminarElementoDeCapacitacion(elemento) {
    inhabilitarBotones()
    elemento.id_catalogo = obtenerIdCatalogo(elemento.catalog)
    $.ajax({
        url: window.base_url + "Capacitacion/eliminarElemento",
        type: 'POST',
        data: {
            id_elemento: elemento.id,
            id_capacitacion: capacitacion.id,
            id_catalogo: elemento.id_catalogo
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            elementos_capacitacion = json.data
            habilitarBotones()

            mostrarElementosAgregados()
        }
    })
}

function obtenerIdCatalogo(catalogo) {
    var id = 0;
    switch (catalogo) {
        case 'library_elements_':
            id = 1;
            break;
        case 'podcast':
            id = 2
            break;
        case 'com_topics':
            id = 3;
            break;
        case 'question_quiz':
            id = 4;
            break;
        case 'game':
            id = 5;
            break;
    }
    return id;
}

function actualizarCapacitacion() {
    inhabilitarBotones()
    if (validarRequeridos()) {
        var datos = new FormData();//{};
        var foto = $('#imagen_edicion')
        var archivos = foto[0].files
        file = archivos[0]

        datos.append("name", get("txt_nombre_edicion").value)
        datos.append("description", get("txt_descripcion_edicion").value)
        datos.append("imagen", file);
        datos.append("image_name", get("nombre_imagen_edicion").value)
        datos.append("imagen_actualizada", JSON.stringify(imagen_actualizada))
        datos.append("fecha_limite", get("fecha_limite_edicion").value)
        datos.append("id", capacitacion.id)
        datos.append("elementos_capacitacion", JSON.stringify(elementos_capacitacion));
        datos.append("fecha_programada", get("fecha_programada_edicion").value)
        for (var key of datos.entries()) {
            console.log(key[0] + ', ' + key[1]);
        }
        $.ajax({
            url: window.base_url + "Capacitacion/actualizarCapacitacion",
            type: 'POST',
            contentType: false,
            data: datos,
            dataType: 'json',
            cache: false,
            processData: false,
            error: function (xhr, error, code) {
                habilitarBotones()
                alert("Error al actualizar capacitacion")
                $('#modal_edit_capacitacion').modal('hide');
            },
            success: function (json) {
                // actualizarOrden()
                obtenerCapacitaciones()
                habilitarBotones()
                $('#modal_edit_capacitacion').modal('hide');
            }
        })
    } else {
        habilitarBotones()
    }
}

var grupos = []
var grupos_cuestionarios = []
function generarSelectConGrupos() {
    // var select = get("grupos");//el select no es necesario si esta el buscador
    var tabla = get("contenedor_grupos")
    var tabla_edicion = get("contenedor_grupos_edicion")
    var html = "<option value='0'>Todos</option>";
    var html_tabla = ""
    for (var i = 0; i < grupos.length; i++) {
        html += "<option value='" + grupos[i].id + "'>" + grupos[i].name + "</option>"
        html_tabla += "<tr>"
        html_tabla += "<td>" + grupos[i].name + "</td>"
        html_tabla += "<td><button class='btn btn-success' onclick='agregarGrupo(" + i + ")'>Agregar</button></td>"
        html_tabla += "</tr>"
    }
    tabla.innerHTML = html_tabla
    tabla_edicion.innerHTML = html_tabla
    // select.innerHTML = html
}

//esta funcion es para editar el cuestionario
function agregarGrupoACuestionario(id, grupo) {
    // inhabilitarBotones()
    $.ajax({
        url: window.base_url + "capacitacion/agregarGrupo",
        type: 'POST',
        data: {
            group_id: id,
            capacitacion_id: capacitacion.id
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log('error', xhr)
        },
        success: function (json) {
            // usuarios_library = json.data
            grupos_cuestionarios.push(grupo)
            mostrarGruposCuestionarios()
        }
    })
}

function agregarTodosGrupos() {
    for (var i = 0; i < grupos.length; i++) {
        agregarGrupo(i)
    }
}

function eliminarGrupo(indice) {
    if (editando === false) {
        grupos_cuestionarios.splice(indice, 1);
        mostrarGruposCuestionarios()
    } else {
        eliminarGrupoDeCuestionario(grupos_cuestionarios[indice].id, indice)
    }
}

function eliminarGrupoDeCuestionario(id_usuario, indice) {
    $.ajax({
        url: window.base_url + "capacitacion/eliminarGrupo",
        type: 'POST',
        data: {
            group_id: id_usuario,
            capacitacion_id: capacitacion.id
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log('error', xhr)
        },
        success: function (json) {
            // usuarios_library = json.data
            grupos_cuestionarios.splice(indice, 1)
            mostrarGruposCuestionarios()
            // habilitarBotones()
        }
    })
}

function agregarGrupo(indice) {
    if (editando === false) {
        var grupo = grupos[indice]
        grupos_cuestionarios = grupos_cuestionarios || []
        if (!grupos_cuestionarios.some(u => { return u.id === grupo.id })) {
            grupos_cuestionarios.push(grupo)
        }
        mostrarGruposCuestionarios()
    } else {
        if (!grupos_cuestionarios.some(u => { return u.id === grupos[indice].id }))
            agregarGrupoACuestionario(grupos[indice].id, grupos[indice])
    }
}

function mostrarGruposCuestionarios() {
    var div = get("contenedor_grupos_cuestionarios");
    var div_edicion = get("contenedor_grupos_cuestionarios_edicion")
    var html = "";
    for (var i = 0; i < grupos_cuestionarios.length; i++) {
        var grupo = grupos_cuestionarios[i];
        // if (usuario.id !== id_usuario_actual)
        html += "<tr><td>" + grupo.name + "</td><td><button class='btn btn-danger' onclick='eliminarGrupo(" + i + ")'>Eliminar</button></td></tr>"
    }
    div.innerHTML = html;
    div_edicion.innerHTML = html;
}

function eliminar_fecha() {
    get("fecha_programada").value = undefined
    get("fecha_programada_edicion").value = undefined
}


function cambiarTipoListado() {
    var tipo = get("tipo_listado").value
    if (tipo > -1)
        capacitaciones = JSON.parse(JSON.stringify(capacitaciones_originales.filter(f => f.active == tipo)))//se filtra por elementos activos/inactivos
    else
        capacitaciones = JSON.parse(JSON.stringify(capacitaciones_originales))
    generarTablaCapacitaciones()
}