jQuery(document).ready(function ($) {
});
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 19 mar 2018
 *	Nota: Se agrega evento para comprobar login
 ***********************************************************************/
function Login() {
	var email = $('#email').val(),
		password = $('#password').val();
	if (email !== '' && password !== '') {
		var data = new FormData();
		data.append('email', email);
		data.append('password', password);
		document.getElementById("loader_background").style.display = 'inherit';
		document.getElementById("loader").style.display = 'inherit';
		$.ajax({
			url: window.base_url + "Admin/login",
			type: "POST",
			contentType: false,
			cache: false,
			processData: false,
			data: data,
			success: function (response) {
				console.log(response)
				var datos = response.data;
				window.location = window.base_url + 'Admin/inicio';
				document.getElementById("loader_background").style.display = 'none';
				document.getElementById("loader").style.display = 'none';
			},
			error: function (response) {
				console.log(response)
				document.getElementById("loader_background").style.display = 'none';
				document.getElementById("loader").style.display = 'none';
				alert(response.responseJSON.error_msg);
			}
		});

	} else {
		alert('El usuario y/o password se encuentra vacio(s)');
	}
}
