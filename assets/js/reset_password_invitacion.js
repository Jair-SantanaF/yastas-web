var usuarios_originales = []
jQuery(document).ready(function ($) {
    obtenerUsuarios()
})

function obtenerUsuarios() {
    $.ajax({
        url: window.base_url + "ResetInvitacion/obtenerUsuarios",
        type: "POST",
        data: {},
        cache: false,
        success: function (response) {
            console.log(response)
            usuarios_originales = response.data
            generarTablaUsuarios(JSON.parse(JSON.stringify(response.data)))
        },
        error: function (error) {
            console.log(error)
        }
    })
}

function resetearPassword(id_usuario) {
    if (confirm("Se restablecerá el usuario para que pueda registrarse, ¿Está seguro de continuar?"))
        $.ajax({
            url: window.base_url + "ResetInvitacion/resetear_password",
            type: "POST",
            data: { id_user: id_usuario },
            cache: false,
            success: function (response) {
                console.log(response)
                obtenerUsuarios()
                Swal.fire({
                    type: 'success',
                    title: 'Cambio de contraseña',
                    text: 'Ahora el usuario puede registrarse'
                });
            },
            error: function (error) {
                console.log(error)
            }
        })
}

function generarTablaUsuarios(usuarios) {
    var tabla = get("contenedor_usuarios")
    var html = ""
    for (var i = 0; i < usuarios.length; i++) {
        html += "<tr>"
        html += "<td>" + usuarios[i].name + "</td>"
        html += "<td>" + usuarios[i].number_employee + "</td>"
        // html += "<td>" + usuarios[i].email + "</td>"
        html += "<td><button class='btn btn-danger' onclick='resetearPassword(" + usuarios[i].id + ")'>Permitir registro</button></td>"
        html += "</tr>"
    }
    tabla.innerHTML = html
}

function filtrar() {
    var filtro = get("filtro").value
    var usuarios_filtrados = usuarios_originales.filter(f => f.number_employee == filtro || f.name.toLowerCase().includes(filtro.toLowerCase()))
    generarTablaUsuarios(usuarios_filtrados)
}

function get(id) {
    return document.getElementById(id)
}