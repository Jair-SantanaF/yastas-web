var filtro = "";
var _regiones;
var _asesores;
jQuery(document).ready(function () {

    obtenerTopics();
    obtenerUsuarios();
    obtenerGrupos();
    obtenerRegiones()
    console.log(rol_id)
    //no se debe ocultar porque siempre va a haber regiones y asesores
    //para rol 2 todas las regiones y sus asesores
    //para rol 5 las regiones que tiene acceso y sus asesores
    //para rol 6 su region y el mismo como asesor
    //esto se valida desde back
    // if (rol_id != 2 && rol_id != 5) {
    //     ocultar_select_regiones()
    // }

    //funcion pora mostrar filtro habilitado o no habilitados
    $('#select_filtro').on('change', function () {
        filtro = $(this).val();
        obtenerTopics();
        //$('#contenido_tabla_topics').ajax.reload();
        //ObtenerTablaElementos();
    });

});

function ocultar_select_regiones() {
    get("regiones").style.display = "none";
    get("asesores").style.display = "none"
    get("regiones_edicion").style.display = "none";
    get("asesores_edicion").style.display = "none"
}

function obtenerRegiones() {
    $.ajax({
        url: window.base_url + "Ws/ObtenerRegiones",
        type: "POST",
        data: {},
        cache: false,
        success: function (response) {
            console.log(response)
            _regiones = response.data
            generarSelectRegiones(response.data)
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function generarSelectRegiones(regiones) {
    var tabla = get("tbl_regiones")
    var tabla_edicion = get("tbl_regiones_edicion")
    var html = ""
    var html_edicion = ""
    for (var i = 0; i < regiones.length; i++) {
        html += "<tr>"
        html += "<td><input type='checkbox' id='cbr" + i + "' onclick='obtener_asesores()'></td>"
        html += "<td>" + regiones[i].nombre + "</td>"
        html += "</tr>"

        html_edicion += "<tr>"
        html_edicion += "<td><input type='checkbox' id='cbr_edicion" + i + "' onclick='obtener_asesores()'></td>"
        html_edicion += "<td>" + regiones[i].nombre + "</td>"
        html_edicion += "</tr>"
    }
    tabla.innerHTML = html
    tabla_edicion.innerHTML = html_edicion
}

function obtener_regiones_seleccionadas() {
    var seleccionados = []
    for (var i = 0; i < _regiones.length; i++) {
        var extra = "";
        if (editando)
            extra = "_edicion"
        if (get("cbr" + i + extra).checked == 1) {
            seleccionados.push(_regiones[i].id)
        }
    }
    return JSON.stringify(seleccionados)
}


function obtener_asesores_seleccionados() {
    var seleccionados = []
    for (var i = 0; i < _asesores.length; i++) {
        var extra = "";
        if (editando)
            extra = "_edicion"
        if (get("cba" + i + extra).checked == 1) {
            seleccionados.push(_asesores[i].id)
        }
    }
    return JSON.stringify(seleccionados)
}


function seleccionar_todas_regiones() {
    for (var i = 0; i < _regiones.length; i++) {
        if (!editando)
            get("cbr" + i).checked = get("cb_regiones_todas").checked
        else
            get("cbr_edicion" + i).checked = get("cb_regiones_todas_edicion").checked
    }
    obtener_asesores();
}

function seleccionar_todos_asesores() {
    for (var i = 0; i < _asesores.length; i++) {
        if (!editando)
            get("cba" + i).checked = get("cb_asesores_todos").checked
        else
            get("cba_edicion" + i).checked = get("cb_asesores_todos_edicion").checked
    }
}

function get(id) {
    return document.getElementById(id)
}

function obtener_asesores() {
    var regiones = obtener_regiones_seleccionadas()
    $.ajax({
        url: window.base_url + "Ws/ObtenerAsesoresMultiple",
        type: "POST",
        data: { id_region: regiones },
        cache: false,
        success: function (response) {
            console.log(response)
            _asesores = response.data
            generarSelectAsesores(response.data)
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function generarSelectAsesores(asesores) {
    var tabla = get("tbl_asesores")
    var tabla_edicion = get("tbl_asesores_edicion")
    var html = ""
    var html_edicion = ""
    for (var i = 0; i < asesores.length; i++) {
        html += "<tr>"
        html += "<td><input type='checkbox' id='cba" + i + "'></td>"
        html += "<td>" + asesores[i].nombre + "</td>"
        html += "</tr>"

        html_edicion += "<tr>"
        html_edicion += "<td><input type='checkbox' id='cba_edicion" + i + "'></td>"
        html_edicion += "<td>" + asesores[i].name + "</td>"
        html_edicion += "</tr>"
    }
    tabla.innerHTML = html
    tabla_edicion.innerHTML = html_edicion
}

var topics,
    usuarios,
    id_seleccionado,
    topic_detalles,
    topic_edicion,
    id_seleccionado_edicion;
var usuarios_seleccionados = [];
var usuarios_seleccionados_edicion = [];
var editando;
var topic_mensaje;

function obtenerUsuarios() {
    console.log("entro a obtener usuarios");
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
            console.log("usuarios", JSON.parse(JSON.stringify(json)))
            usuarios = json.data;
            eliminarUsuarioActual();
            mostrarUsuarios()
            console.log(usuarios)
        }
    })
}

function eliminarUsuarioActual() {
    var posicion;
    for (var i = 0; i < usuarios.length; i++) {
        if (usuarios[i].id === id_usuario_actual) {
            posicion = i;
        }
    }
    usuarios.splice(posicion, 1);
}

function obtenerTopics() {
    filtro = get("select_filtro").value
    $.ajax({
        url: window.base_url + "Com/getTopicsAdmin",
        type: 'POST',
        data: {
            filtro: filtro
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            topics = json.data
            console.log(topics)
            generarTablaTopics();
        }
    })
}

function generarTablaTopics() {
    var tabla = document.getElementById("contenido_tabla_topics");
    var html = "";
    if (topics.length > 0)
        for (i = 0; i < topics.length; i++) {
            var eliminado = topics[i].active ? "" : "eliminado"
            var texto_eliminar = topics[i].active ? "Deshabilitar" : "Habilitar"
            var botones = '<div class="pt-2 pb-2 text-center">' +
                '<a title="Mandar mensaje" class="btn btn-success" onclick="mandarMensaje(' + topics[i].id + ')" cmd="mensaje"><i class="fa fa-comment-alt"></i></a>' +
                '<a title="Ver pasos" class="btn btn-info btn-xs editar mr-2 lead_0_8 text-white" onclick="verDetalles(' + topics[i].id + ')" cmd="pasos"><i cmd="pasos" class="fa fa-eye"></i></a>' +
                '<a title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8 text-white" onclick="editarTopic(' + topics[i].id + ')" cmd="editar"><i cmd="editar" class="fa fa-edit"></i></a>' +
                '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8 text-white" onclick="eliminarTopic(' + i + ')" cmd="eliminar">' + texto_eliminar + '</a>' +
                '</div>';
            html += "<tr class='" + eliminado + "'><td>" + topics[i].name + "</td><td>" + topics[i].user_count + "</td><td>" + botones + "</td></tr>";
        }
    tabla.innerHTML = html;
}

function agregarTopic() {
    $('#modal_new_topic').modal('show');
    $("#contenedor_capacitacion_obligatoria").removeClass("d-none")
    document.getElementById("txt_nombre").value = "";
    // document.getElementById("txt_usuarios").value = "";
    usuarios_seleccionados = [];
    // reconstruirTablaUsuarios();
    id_seleccionado = undefined;
    editando = false
}

function escribiendoNombre() {
    document.getElementById("txt_nombre").classList.remove("requerido")
    document.getElementById("txt_nombre_edicion").classList.remove("requerido")
}

function llenar_autocomplete_list() {
    var name = document.getElementById("txt_usuarios").value.toLowerCase();
    var lista = document.getElementById("lista_autocomplete");
    var usuarios_filtrados = usuarios.filter(u => { return (u.name + u.last_name).toLowerCase().includes(name) });
    if (name === '')
        usuarios_filtrados = [];
    var html = "";
    for (i = 0; i < usuarios_filtrados.length; i++) {
        id = usuarios_filtrados[i].id
        html += "<li onclick='seleccionarUsuario(" + id + ")'>" + usuarios_filtrados[i].name + " " + usuarios_filtrados[i].last_name + "</li>"
    }
    lista.innerHTML = html;
}

function seleccionarUsuario(id) {
    var name = document.getElementById("txt_usuarios");
    var usuario = usuarios.filter(u => { return u.id === id })[0];
    name.value = usuario.name + " " + usuario.last_name;
    document.getElementById("lista_autocomplete").innerHTML = "";
    id_seleccionado = id;
}

function agregarUsuario() {
    if (id_seleccionado) {
        if (validarNoDuplicado()) {
            var tabla = document.getElementById("contenido_tabla_usuarios");
            var usuario = usuarios.filter(u => { return u.id === id_seleccionado })[0];
            tabla.innerHTML = tabla.innerHTML + "<tr><td>" + usuario.name + " " + usuario.last_name + "</td><td><button class='btn btn-danger' onclick='eliminarUsuario(" + id_seleccionado + ")'>Eliminar</button></td></tr>";
            usuarios_seleccionados.push(usuario.id);
            document.getElementById("txt_usuarios").value = '';
            id_seleccionado = undefined;
        }
    }
}

function validarNoDuplicado() {
    var bandera = true;
    if (usuarios_seleccionados.some(u => { return u === id_seleccionado })) {
        document.getElementById("txt_usuarios").value = '';
        bandera = false;
    }
    return bandera;
}

function mandarMensaje(id_topic) {
    topic_mensaje = id_topic
    $("#modal_mensaje").modal("show");

}

function guardarMensaje() {
    var mensaje = get("mensaje").value
    $.ajax({
        url: window.base_url + "Com/saveMessage",
        type: 'POST',
        data: {
            message: mensaje,
            id_topic: topic_mensaje
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
            $('#modal_mensaje').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Error',
                text: 'Error al guardar el mensaje'
            });
        },
        success: function (json) {
            $('#modal_mensaje').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Mensaje enviado',
                text: 'El mensaje se envió a la comunidad'
            });

            obtenerTopics();
        }
    })
}

function eliminarUsuario(id) {
    var k = usuarios_seleccionados.indexOf(id);
    usuarios_seleccionados.splice(k, 1);
    reconstruirTablaUsuarios();
}

function reconstruirTablaUsuarios() {
    var tabla = document.getElementById("contenido_tabla_usuarios");
    var html = "";
    for (i = 0; i < usuarios_seleccionados.length; i++) {
        var nombre = obtenerNombre(usuarios_seleccionados[i]);
        html += "<tr><td>" + nombre + "</td><td><button class='btn btn-danger' onclick='eliminarUsuario(" + usuarios_seleccionados[i] + ")'>Eliminar</button></td></tr>";
    }
    tabla.innerHTML = html;
}

function obtenerNombre(id) {
    var nombre = "";
    for (j = 0; j < usuarios.length; j++) {
        if (usuarios[j].id === id)
            nombre = usuarios[j].name + " " + usuarios[j].last_name;
    }
    return nombre;
}

function guardarTopic() {
    if (validarTopic()) {
        var nombre_topic = document.getElementById("txt_nombre").value;
        // var id_region = document.getElementById("select_regiones_com").value || undefined
        // var id_asesor = document.getElementById("select_asesores").value || undefined
        var capacitacion_obligatoria = document.getElementById("capacitacion_obligatoria").value
        var regiones = obtener_regiones_seleccionadas()
        var asesores = obtener_asesores_seleccionados()
        if (capacitacion_obligatoria == -1) {
            alert("Selecciona un tipo de elemento")
            return
        }
        if (regiones == []) {
            alert("Debes seleccionar una región para el grupo")
            return;
        }

        if (asesores == []) {
            alert("Debes seleccionar un asesor para el grupo")
            return
        }

        $.ajax({
            url: window.base_url + "Com/saveTopic",
            type: 'POST',
            data: {
                name: nombre_topic,
                usuarios: usuarios_library,
                grupos: grupos_cuestionarios,
                regiones: regiones,
                asesores: asesores
            },
            dataType: 'json',
            error: function (xhr, error, code) {
                console.log(xhr)
                Swal.fire({
                    type: 'error',
                    title: 'Error',
                    text: 'Error al guardar el topic'
                });
            },
            success: function (json) {
                $('#modal_new_topic').modal('hide');
                obtenerTopics();
                Swal.fire({
                    type: 'success',
                    title: 'Éxito',
                    text: 'El topic se creó correctamente'
                });
            }
        })
    }
}

function validarTopic() {
    var nombre_topic = document.getElementById("txt_nombre").value;
    var bandera = true;
    if (nombre_topic === undefined || nombre_topic === null || nombre_topic === "") {
        document.getElementById("txt_nombre").classList.add("requerido")
        bandera = false;
    }
    return bandera;
}

function eliminarTopic(indice) {
    var topic = topics[indice]
    $.ajax({
        url: window.base_url + "Com/removeTopic",
        type: 'POST',
        data: {
            id: topic.id,
            active: (topic.active ? 0 : 1)
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            console.log(json)
            obtenerTopics();
        }
    })
}

function verDetalles(id) {
    var topic_detalles = topics.filter(t => { return t.id === id })[0];
    document.getElementById("lbl_nombre_topic").innerHTML = topic_detalles.name;
    var tabla = document.getElementById("contenido_tabla_usuarios_detalles");
    var html = "";
    for (i = 0; i < topic_detalles.users.length; i++) {
        var nombre = topic_detalles.users[i].name + " " + topic_detalles.users[i].last_name;
        html += '<tr><td>' + nombre + '</td></tr>'
    }
    tabla.innerHTML = html;
    html = ""
    var tabla_grupos = document.getElementById("contenido_tabla_grupos_detalles")
    for (i = 0; i < topic_detalles.grupos.length; i++) {
        var nombre = topic_detalles.grupos[i].name
        html += '<tr><td>' + nombre + '</td></tr>'
    }
    tabla_grupos.innerHTML = html
    $('#modal_detalles').modal('show');
}

//para editar topic

function editarTopic(id) {
    $('#modal_edicion').modal('show');
    topic_edicion = topics.filter(t => { return t.id === id })[0];
    console.log(topic_edicion)
    document.getElementById("txt_nombre_edicion").value = topic_edicion.name;
    // document.getElementById("txt_usuarios_edicion").value = "";
    usuarios_seleccionados_edicion = [];
    // obtenerUsuariosEdicion();
    // reconstruirTablaUsuariosEdicion();

    $("#contenedor_capacitacion_obligatoria").addClass("d-none")

    usuarios_library = topic_edicion.users
    grupos_cuestionarios = topic_edicion.grupos
    mostrarGruposCuestionarios()
    mostrarUsuariosLibrary()
    mostrarUsuarios();
    generarSelectConGrupos()
    id_seleccionado_edicion = undefined;
    editando = true
}

function llenar_autocomplete_list_edicion() {
    var name = document.getElementById("txt_usuarios_edicion").value.toLowerCase();
    var lista = document.getElementById("lista_autocomplete_edicion");
    var usuarios_filtrados = usuarios.filter(u => { return (u.name + u.last_name).toLowerCase().includes(name) });
    if (name === '')
        usuarios_filtrados = [];
    var html = "";
    for (i = 0; i < usuarios_filtrados.length; i++) {
        id = usuarios_filtrados[i].id
        html += "<li onclick='seleccionarUsuarioEdicion(" + id + ")'>" + usuarios_filtrados[i].name + " " + usuarios_filtrados[i].last_name + "</li>"
    }
    lista.innerHTML = html;
}

function seleccionarUsuarioEdicion(id) {
    var name = document.getElementById("txt_usuarios_edicion");
    var usuario = usuarios.filter(u => { return u.id === id })[0];
    name.value = usuario.name + " " + usuario.last_name;
    document.getElementById("lista_autocomplete_edicion").innerHTML = "";
    id_seleccionado_edicion = id;
}

function agregarUsuarioEdicion() {
    if (id_seleccionado_edicion) {
        if (validarNoDuplicadoEdicion()) {
            $.ajax({
                url: window.base_url + "Com/subscribeToTopic",
                type: 'POST',
                data: {
                    id_topic: topic_edicion.id,
                    id_user: id_seleccionado_edicion
                },
                dataType: 'json',
                error: function (xhr, error, code) {
                    console.log(xhr)
                },
                success: function (json) {
                    var tabla = document.getElementById("contenido_tabla_usuarios_edicion");
                    var usuario = usuarios.filter(u => { return u.id === id_seleccionado_edicion })[0];
                    tabla.innerHTML = tabla.innerHTML + "<tr><td>" + usuario.name + " " + usuario.last_name + "</td><td><button class='btn btn-danger' onclick='eliminarUsuarioEdicion(" + id_seleccionado + ")'>Eliminar</button></td></tr>";
                    usuarios_seleccionados_edicion.push(usuario.id);
                    document.getElementById("txt_usuarios_edicion").value = '';
                    id_seleccionado_edicion = undefined;
                }
            })
        }
    }
}

function validarNoDuplicadoEdicion() {
    var bandera = true;
    if (usuarios_seleccionados_edicion.some(u => { return u === id_seleccionado })) {
        document.getElementById("txt_usuarios_edicion").value = '';
        bandera = false;
    }
    return bandera;
}

function eliminarUsuarioEdicion(id) {
    console.log(id)
    $.ajax({
        url: window.base_url + "Com/unsubscribeToTopic",
        type: 'POST',
        data: {
            id_topic: topic_edicion.id,
            id_user: id
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            var k = usuarios_seleccionados_edicion.indexOf(id);
            usuarios_seleccionados_edicion.splice(k, 1);
            reconstruirTablaUsuariosEdicion();
        }
    })
}

function reconstruirTablaUsuariosEdicion() {
    var tabla = document.getElementById("contenido_tabla_usuarios_edicion");
    var html = "";
    for (i = 0; i < usuarios_seleccionados_edicion.length; i++) {
        var nombre = obtenerNombre(usuarios_seleccionados_edicion[i]);
        html += "<tr><td>" + nombre + "</td><td><button class='btn btn-danger' onclick='eliminarUsuarioEdicion(" + usuarios_seleccionados_edicion[i] + ")'>Eliminar</button></td></tr>";
    }
    tabla.innerHTML = html;
}

function obtenerUsuariosEdicion() {
    usuarios_seleccionados_edicion = [];
    for (i = 0; i < topic_edicion.users.length; i++) {
        if (topic_edicion.users[i].id !== id_usuario_actual)
            usuarios_seleccionados_edicion.push(topic_edicion.users[i].id);
    }
    console.log(usuarios_seleccionados_edicion)
}

function editarTopicAjax() {
    if (validarTopicEdicion()) {
        var nombre_topic = document.getElementById("txt_nombre_edicion").value;
        var id_region = document.getElementById("select_regiones_com_edicion").value
        var id_asesor = document.getElementById("select_asesores_edicion").value || undefined

        console.log(nombre_topic)
        $.ajax({
            url: window.base_url + "Com/editTopic",
            type: 'POST',
            data: {
                name: nombre_topic,
                id: topic_edicion.id,
                id_region: id_region,
                id_asesor: id_asesor
            },
            dataType: 'json',
            error: function (xhr, error, code) {
                console.log(xhr)
                Swal.fire({
                    type: 'error',
                    title: 'Error',
                    text: 'Error al actualizar el topic'
                });
            },
            success: function (json) {
                console.log(json)
                Swal.fire({
                    type: 'success',
                    title: 'Actualizado',
                    text: 'El topic actualizado correctamente'
                });
                $('#modal_edicion').modal('hide');
                obtenerTopics();
            }
        })
    }
}

function validarTopicEdicion() {
    var nombre_topic = document.getElementById("txt_nombre_edicion").value;
    var bandera = true;
    if (nombre_topic === undefined || nombre_topic === null || nombre_topic === "") {
        document.getElementById("txt_nombre_edicion").classList.add("requerido")
        bandera = false;
    }
    return bandera;
}

// 
// 
// 
//A PARTIR DE AQUI SON FUNCIONES PARA AGREGAR USUARIOS Y GRUPOS
// 
// 
// 

function get(id) {
    return document.getElementById(id);
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
    var select = get("grupos");
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
    console.log(html)
    tabla.innerHTML = html_tabla
    tabla_edicion.innerHTML = html_tabla
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
    var div_edicion = get("contenedor_usuarios_edicion")
    var html = "";
    for (var i = 0; i < usuarios.length; i++) {
        var usuario = usuarios[i];
        // if (usuario.id !== id_usuario_actual)
        html += "<tr><td>" + (usuario.name || '') + " " + (usuario.last_name || '') + "</td><td><button class='btn btn-primary' onclick='agregarUsuario(" + i + ")'>Agregar</button></td></tr>"
    }
    div.innerHTML = html;
    div_edicion.innerHTML = html;
}

function agregarUsuario(indice) {
    if (!editando) {
        var usuario = usuarios[indice]
        usuarios_library = usuarios_library || []
        if (!usuarios_library.some(u => { return u.id === usuario.id })) {
            usuarios_library.push(usuario)
        }
        mostrarUsuariosLibrary()
    } else {
        if (!usuarios_library.some(u => { return u.id === usuarios[indice].id }))
            agregarUsuarioACapacitacion(usuarios[indice].id, usuarios[indice])
    }
}

function agregarUsuarioACapacitacion(id, usuario) {
    console.log("entrando a agregar usuario a la capacitacion")
    // inhabilitarBotones()
    $.ajax({
        url: window.base_url + "com/subscribeToTopic",
        type: 'POST',
        data: {
            id_user: id,
            id_topic: topic_edicion.id
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
        url: window.base_url + "com/agregarGrupo",
        type: 'POST',
        data: {
            group_id: id,
            com_id: topic_edicion.id
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
    var div_edicion = get("contenedor_usuarios_cuestionarios_edicion")
    var html = "";
    for (var i = 0; i < usuarios_library.length; i++) {
        var usuario = usuarios_library[i];
        // if (usuario.id !== id_usuario_actual)
        html += "<tr><td>" + usuario.name + " " + (usuario.last_name || '') + "</td><td><button class='btn btn-danger' onclick='eliminarUsuario(" + i + ")'>Eliminar</button></td></tr>"
    }
    div.innerHTML = html;
    div_edicion.innerHTML = html;
}


function eliminarUsuario(indice) {
    if (!editando) {
        usuarios_library.splice(indice, 1);
        mostrarUsuariosLibrary()
    } else {
        eliminarUsuarioDeLibrary(usuarios_library[indice].id, indice)
    }
}

function eliminarGrupo(indice) {
    if (!editando) {
        grupos_cuestionarios.splice(indice, 1);
        mostrarGruposCuestionarios()
    } else {
        eliminarGrupoDeCuestionario(grupos_cuestionarios[indice].id, indice)
    }
}

function eliminarTodos() {
    if (!editando) {
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
        url: window.base_url + "com/unsubscribeToTopic",
        type: 'POST',
        data: {
            user_id: id_usuario,
            id_topic: topic_edicion.id
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
        url: window.base_url + "com/eliminarGrupo",
        type: 'POST',
        data: {
            group_id: id_usuario,
            com_id: topic_edicion.id
        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            console.log("lo que se regresa al elimiar el usuario", json)
            // usuarios_library = json.data
            grupos_cuestionarios.splice(indice, 1)
            mostrarGruposCuestionarios()
            // habilitarBotones()
        }
    })
}

function agregarGrupo(indice) {
    if (!editando) {
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