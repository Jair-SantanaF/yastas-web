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
			url: window.base_url + "Ws/login",
			type: "POST",
			contentType: false,
			cache: false,
			processData: false,
			data: data,
			success: function (response) {
				console.log(response)
				var datos = response.data;
				window.location = window.base_url + 'Client/inicio';
				document.getElementById("loader_background").style.display = 'none';
				document.getElementById("loader").style.display = 'none';
				localStorage.setItem('token', JSON.stringify(datos));
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

function signin() {
	var email = $('#email').val();
	var password = $('#password').val();
	var name = $('#name').val();
	var last_name = $('#lastname').val();
	var segundo_apellido = $('#segundo_apellido').val();
	var business_name = "Kreativeco";
	var job = 18;
	var phone = $('#phone').val();
	var number_employee = $('#number_employee').val();
	var terminos_aceptados = $('#terminos_aceptados').val();
	var aviso_aceptado = $('#aviso_aceptado').val();

	if (email !== '' && password !== '' && name !== '' && last_name !== '' && business_name !== '' && job !== '' && phone !== '' && number_employee !== '' && terminos_aceptados !== '' && aviso_aceptado !== '') {
		var data = new FormData();
		data.append('email', email);
		data.append('password', password);
		data.append('name', name);
		data.append('last_name', last_name);
		data.append('segundo_apellido', segundo_apellido);
		data.append('business_name', business_name);
		data.append('job', job);
		data.append('phone', phone);
		data.append('number_employee', number_employee);
		data.append('terminos_aceptados', terminos_aceptados);
		data.append('aviso_aceptado', aviso_aceptado);
		document.getElementById("loader_background").style.display = 'inherit';
		document.getElementById("loader").style.display = 'inherit';
		$.ajax({
			url: window.base_url + "Ws/signin",
			type: "POST",
			contentType: false,
			cache: false,
			processData: false,
			data: data,
			success: function (response) {
				console.log(response)
				var datos = response.data;
				window.location = window.base_url + 'Client/index';
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
		alert('Todos los campos son obligatorios');
	}
}

function ShowSignUp() {
	window.location = window.base_url + 'Client/showSignUp';
}

function ShowTest() {
    window.location = window.base_url + 'Client/showTest';
}

function ShowTraining() {
	window.location = window.base_url + 'Client/showTraining';
}

function ShowLibrary() {
	window.location = window.base_url + 'Client/showLibrary';
}

function ShowVideo() {
	window.location = window.base_url + 'Client/showVideoPage';
}

function ShowImage() {
	window.location = window.base_url + 'Client/showImagePage';
}

function ShowPdf() {
	window.location = window.base_url + 'Client/showPdfPage';
}

function ShowNews() {
	window.location = window.base_url + 'Client/showNewsPage';
}

function inicio() {
	window.location = window.base_url + 'Client/inicio';
}

/*
autor: Jair Daniel Santana Feliciano
fecha: 27/11/2022
nota: Se agrega función para mostrar las preguntas del test
esta funcion recibe el id del test que se va a mostrar
*/

function getQuestions(quiz_id) {
	var data = new FormData();
	const localUser = JSON.parse(localStorage.getItem('token'));
	data.append('quiz_id', quiz_id);
	data.append('token', localUser[0].token);
	$.ajax({
		url: window.base_url + "questions/ListQuestionsQuiz",
		type: "POST",
		contentType: false,
		cache: false,
		processData: false,
		data: data,
		success: function (response) {
			console.log(response)
			var datos = response.data;
		},
		error: function (response) {
			console.log(response)
			alert(response.responseJSON.error_msg);
		}
	});
}

function getTrainingDetails(training_id) {
	var data = new FormData();
	const localUser = JSON.parse(localStorage.getItem('token'));
	data.append('capacitacion_id', training_id);
	data.append('token', localUser[0].token);
	$.ajax({
		url: window.base_url + "capacitacion/getDetail",
		type: "POST",
		contentType: false,
		cache: false,
		processData: false,
		data: data,
		success: function (response) {
			console.log(response)
			var datos = response.data;
		},
		error: function (response) {
			console.log(response)
			alert(response.responseJSON.error_msg);
		}
	});
}


function selectQuestionnarie(quiz_id) {
	localStorage.setItem('quiz_id', quiz_id);
	localStorage.setItem('actual_question', 0);
	localStorage.setItem('quiz_status', null);
	window.location = window.base_url + 'Client/showQuestionsPage';
}

function selectTraining(training_id) {
	localStorage.setItem('training_id', training_id);
	window.location = window.base_url + 'Client/showTrainingPage';
}

function getNews(business_id, user_id) {
	let data = new FormData();
	data.append('business_id', business_id);
	data.append('user_id', user_id);
	$.ajax({
		url: window.base_url + "Client/getNews",
		type: "POST",
		contentType: false,
		cache: false,
		processData: false,
		data: data,
		success: function (response) {
			var datos = response[0];
			console.log(datos);
			const news = $('#news')
			news.empty();
			var html = '<p>' + datos.name_complete + '</p>' + 
			'<p>' + datos.wall_description + '</p>';
			news.append(html);
		},
		error: function (response) {
			console.log(response)
			alert(response.responseJSON.error_msg);
		}
	});
}

function getFullNews(business_id, user_id) {
	let data = new FormData();
	data.append('business_id', business_id);
	data.append('user_id', user_id);
	$.ajax({
		url: window.base_url + "Client/getNews",
		type: "POST",
		contentType: false,
		cache: false,
		processData: false,
		data: data,
		success: function (response) {
			var datos = response[0];
			console.log(datos);
			const news = $('#news')
			news.empty();
			var html = `
			<div class="row user_news">
				<div class="col-1">
					<img src="` + datos.profile_photo + `" alt="` + datos.name_complete + `">
				</div>
				<div class="col-11">
					<h3>` + datos.name_complete + `</h3>
				</div>
			</div>		
			<div class="content_news">
				<p>` + datos.wall_description + `</p>
				<img src="` + datos.image_path + `" alt="` + datos.image_path + `">
				<div class="row">
					<div class="col-10"></div>
					<div class="col-2 comments_likes">
						<img src="` + window.base_url + `assets/img/img_muro_icono_comentarios.png" alt="comentarios">
						<img src="` + window.base_url + `assets/img/img_muro_icono_like.png" alt="like">
					</div>
				</div>
			</div>
			`

			news.append(html);
		},
		error: function (response) {
			console.log(response)
			alert(response.responseJSON.error_msg);
		}
	});
}

function SetVisto(id) {
	let data = new FormData();
	let localUser = JSON.parse(localStorage.getItem('token'));
	data.append('id', id);
	data.append('token', localUser[0].token);
	data.append('numero_clicks', 1);
	$.ajax({
		url: window.base_url + "library/setVisto",
		type: "POST",
		contentType: false,
		cache: false,
		processData: false,
		data: data,
		success: function (response) {
			console.log(response)
		},
		error: function (response) {
			console.log(response)
			alert(response.responseJSON.error_msg);
		}
	});
}

function GoBack() {
	window.history.back();
}