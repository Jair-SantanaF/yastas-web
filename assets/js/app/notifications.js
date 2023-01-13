$(document).ready(function(){
	validaNotif();
	GetNotification();
});

/**
 * [validaNotif Funcion para validar si existen notificaciones]
 * @return {[type]} [description]
 */
function validaNotif(){
    var msg = new FormData();
    var config = {
    	url: window.base_url + "Notification/ValidateNotification",
    	type: "POST",
    	cache: false,
    	contentType: false,
    	processData: false,
    	mesg:msg,
    	success: function(response){
    		$("#new_notification").html('Nuevas notificaciones');
    	},
    	error: function(response){
			$("#new_notification").html('Sin notificaciones');
    	}
    };
    $.ajax(config);
}

/**
 * Funcion para mostrar las notificaciones de usuario
 * return = ["id", "title", "notification", "profile_photo", "name", "service_id"];
 * Josue Carrasco
 */
function GetNotification(){
    var datos = new FormData();
    var html='', new_notif = "";
    var config = {
	    url: window.base_url + 'Notification/ListNotifications',
	    type: "POST",
	    cache: false,
	    contentType: false,
	    processData: false,
	    data: datos,
        success: function(response){
	    	//Mostrar en ventana flotante unicamente las últimas 5 notificaciones
	    	for(var i=0; i<5; i++) {
				//$.map(response.data,function(value,index){
				var value = response.data[i];

				//Si no ha sido vista marcarla con un icono
				if (value['view'] == 0) {
					new_notif = "active";
				}else{
					new_notif = "";
				}
				html = `
					<a class="dropdown-item cursor-pointer" value="${value['id']}" service_id="${value['service_id']}">
						<div class="d-flex">
							<div class="profile-img p-2">
								<img class="rounded-circle" src="${value['profile_photo']}" />
							</div>
							<div class="w-100">
								<b>${value['name']}</b><br>
								${value['title']}<br>
							</div>
							<div class="">
                            	<div class="rounded-circle new-notif ${new_notif}">!</div>
                        	</div>
						</div>
					</a>`;
				$("#notification").append(html);
				//})
			}

	    	//Agregar evento click para seleccion de una notificacion
			$(`.dropdown-item`).click(function () {
				var service_id = $(this).attr("service_id"),
					notification_id = $(this).attr("value");

				//Quitar marca de no leída
				$(this).removeClass("new-notif");

				//Marcar en bd como leida
				marcarNotificacion(notification_id, service_id);
			});
        },
        error: function(response){
	    	return;
            Swal.fire({
                type:'error',
                title: 'Notificaciones',
                text: response.responseJSON.error_msg
            });
        }
    };
    $.ajax(config);
}

function GetNotifInd(id){
	var datos = new FormData();
	datos.append('id',id)
	var html='';
	var config = {
		url: window.base_url + 'Notification/ListNotifications',
		type: "POST",
		cache: false,
		contentType: false,
		processData: false,
		data: datos,
		success: function(response){
        	$.map(respose.data, function(value,index){
        		html = '<div class="row"><div class="col-md-10"> '+ value['notification'] +' </div></div>';
        		$("#notification").empty();
        		$("#notification").append(html);
        	})

		},
        error: function(response){
			return;
            Swal.fire({
                type:'error',
                title: 'Notificaciones',
                text: response.responseJSON.error_msg
            });
        }
	}
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes   Fecha: 25/11/2020
 *		   urisancer@gmail.com
 *	Nota: Marcar una notificacion como vista(view=1) en la bd
 ***********************************************************************/
function marcarNotificacion(notification_id, service_id){
	var datos = new FormData();

	datos.append("notification_id", notification_id);

	var config = {
		url: window.base_url + 'Notification/NotificationView',
		type: "POST",
		cache: false,
		contentType: false,
		processData: false,
		data: datos,
		success: function(response){
			mostrarVista(service_id);
		},
		error: function(response){
			Swal.fire({
				type:'error',
				title: 'Notificaciones',
				text: response.responseJSON.error_msg
			});
		}
	};
	$.ajax(config);
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes   Fecha: 25/11/2020
 *		   urisancer@gmail.com
 *	Nota: Mostrar la vista correspondiente a la notificacion seleccionada
 ***********************************************************************/
function mostrarVista(service_id){
	var vista = "";

	switch (service_id){
		case "3": //Muro
			vista = "Newsletter"; break;
		case "4": //Biblioteca
			vista = "library"; break;
		case "5": //Juegos
			vista = "Games"; break;
		case "8": //Retroalimentación
			vista = ""; break;
		case "9": //Ranking
			vista = "ranking"; break;
		case "10": //Agenda
			vista = "Agenda"; break;
		case "11": //Cuestionarios
			vista = "Cuestionarios"; break;
		case "12": //Capacitación
			vista = "Elearning"; break;
	}

	if(vista != ""){
		var url = window.base_url+"app/"+vista;
		location.href = url;
	}
}