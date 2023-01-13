$(document).ready(function ($) {

});
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 01 may 2018
 *	Nota: Funcion para enviar recuperacion de password
 ***********************************************************************/
function Enviar() {

    var password = $('#password').val(),
        confirm_password = $('#confirmar_password').val();
    // data = new FormData();
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 01 may 2018
     *	Nota: Se valida que el password no sea diferente al de confirmar y
     *          que el password no se encuentre vacio.
     ***********************************************************************/
    if (password !== confirm_password || password === '' || confirm_password === '') {
        $('#password').val('').focus();
        $('#confirmar_password').val('');
        alert('El password no coincide.');
        return;
    }

    if (comprobarPassword()) {
        var data = {}
        data.token = token
        data.password = password
        console.log(data)
        $.ajax({
            url: base_url + "Ws/CambiarPassword",
            type: "POST",
            data: data,
            success: function (response) {
                console.log(response)
                var datos = response.data;
                alert("La contraseña se cambio exitosamente, ve a la app e ingresa con tu nueva  contraseña.");
                // window.location = base_url;
            },
            error: function (response) {
                console.log(response)
                alert(response.responseJSON.error_msg);
            }
        });
        // contentType: false,
        //     cache: false,
        //     processData: false,
    }
}

function get(id) {
    return document.getElementById(id)
}

function comprobarPassword() {
    var password = get("password").value
    var bandera = false;
    if (password.length < 8) {
        alert("La contraseña debe tener al menos 8 caracteres")
    }
    else if (!contieneNumero(password)) {
        alert("La contraseña debe tener al menos un numero")
    }
    else if (!contieneMinuscula(password)) {
        alert("La contraseña debe tener al menos una minuscula")
    } else if (!contieneMayuscula(password)) {
        alert("La contraseña debe tener al menos una mayuscula")
    } 
    else if (!contieneCaracterEspecial(password)) {
        alert("La contraseña debe tener al menos un caracter especial")
    }
     else {
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