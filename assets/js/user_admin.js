jQuery(document).ready(function ($) {
    obtenerUsuarios();
})

function obtenerUsuarios() {
    datos = {}
    datos.business_id = empresa_id
    $.ajax({
        url: window.base_url + "User/ObtenerUsuariosAdmin",
        type: "POST",
        data: datos,
        cache: false,
        success: function (response) {
            // tabla_companias.ajax.reload();
            console.log(response)
            crearTablaUsuarios(response.data)
        },
        error: function (error) {
            console.log(error)


        }
    });
}

function crearTablaUsuarios(usuarios) {
    console.log(usuarios)
    var tabla = get("tabla_usuarios")
    var html = ""
    for (var i = 0; i < usuarios.length; i++) {
        html += "<tr>"
        html += "<td>" + usuarios[i].name + "</td>"
        html += "<td>" + usuarios[i].last_name + "</td>"
        html += "<td>" + usuarios[i].created_at + "</td>"
        html += "<td>" + usuarios[i].email + "</td>"
        html += "<td><button class='btn btn-danger' onclick='eliminarUsuario(" + usuarios[i].id + ")'></button></td>"
        html += "</tr>"
    }
    tabla.innerHTML = html
}

function AgregarAdmin() {
    get("nombre").value = "";
    get("apellido").value = "";
    get("email").value = "";
    get("password").value = "";
    $("#nuevo_usuario").modal("show");
}

function guardarAdmin() {
    if (comprobarPassword()) {
        datos = {};
        datos.name = get("nombre").value
        datos.last_name = get("apellido").value
        datos.email = get("email").value
        datos.password = get("password").value
        datos.business_id = empresa_id
        $.ajax({
            url: window.base_url + "User/crear_admin",
            type: "POST",
            data: datos,
            cache: false,
            success: function (response) {
                // tabla_companias.ajax.reload();
                $('#nuevo_usuario').modal('hide');
                Swal.fire({
                    type: 'success',
                    title: 'Usuario',
                    text: 'El usuario se creo correctamente'
                });
                obtenerUsuarios();
            },
            error: function (error) {
                console.log(error)
                $('#nuevo_usuario').modal('hide');
                Swal.fire({
                    type: 'error',
                    title: 'Usuario',
                    text: 'Error al crear el usuario'
                });
            }
        });
    }
}

function change() {
    console.log(get("password").value.charCodeAt(0))
}

function comprobarPassword() {
    var password = get("password").value
    var bandera = false;
    if (password.length < 12) {
        alert("la contraseña debe tener al menos 12 caracteres")
    }
    else if (!contieneNumero(password)) {
        alert("La contraseña debe tener al menos un numero")
    }
    else if (!contieneMinuscula(password)) {
        alert("La contraseña debe tener al menos una minuscula")
    } else if (!contieneMayuscula(password)) {
        alert("la contraseña debe tener al menos una mayuscula")
    } else if (!contieneCaracterEspecial(password)) {
        alert("la contraseña debe tener al menos un caracter especial")
    } else {
        bandera = true
    }
    return bandera
}

function contieneNumero(password) {
    var bandera = false;
    for (var i = 0; i < password.length; i++) {
        if (password.charCodeAt(i) >= 48 && password.charCodeAt(i) <= 57) {
            bandera = true
        }
    }
    return bandera
}

function contieneMinuscula(password) {
    var bandera = false;
    for (var i = 0; i < password.length; i++) {
        if (password.charCodeAt(i) >= 97 && password.charCodeAt(i) <= 122) {
            bandera = true
        }
    }
    return bandera
}

function contieneMayuscula(password) {
    var bandera = false;
    for (var i = 0; i < password.length; i++) {
        if (password.charCodeAt(i) >= 65 && password.charCodeAt(i) <= 90) {
            bandera = true
        }
    }
    return bandera
}

function contieneCaracterEspecial(password) {
    var bandera = false;
    for (var i = 0; i < password.length; i++) {
        if ((password.charCodeAt(i) >= 33 && password.charCodeAt(i) <= 47) || (password.charCodeAt(i) >= 58 && password.charCodeAt(i) <= 64) || (password.charCodeAt(i) >= 91 && password.charCodeAt(i) <= 96) || (password.charCodeAt(i) >= 123 && password.charCodeAt(i) <= 255)) {
            bandera = true
        }
    }
    return bandera
}

function eliminarUsuario(id) {
    $.ajax({
        url: window.base_url + "User/DeleteUser",
        type: "POST",
        data: { id: id },
        cache: false,
        success: function (response) {
            // tabla_companias.ajax.reload();
            $('#nuevo_usuario').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Usuario',
                text: 'El usuario se elimino correctamente'
            });
            obtenerUsuarios();
        },
        error: function (error) {
            console.log(error)
            $('#nuevo_usuario').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Usuario',
                text: 'Error al eliminar el usuario'
            });
        }
    });
}

function get(id) {
    return document.getElementById(id)
}