$(document).ready(function(){
    GetNotificationFull();
});

/**
 * Funcion para mostrar las notificaciones de usuario
 * return = ["id", "title", "notification", "profile_photo", "name", "service_id"];
 * Josue Carrasco
 */
function GetNotificationFull(){
    var datos = new FormData();
    var html='',
        new_notif = "";
    var config = {
	    url: window.base_url + 'Notification/ListNotifications',
	    type: "POST",
	    cache: false,
	    contentType: false,
	    processData: false,
	    data: datos,
        success: function(response){
            $.map(response.data,function(value,index){
                if(value['view'] == 0){
                    new_notif = "active";
                }else{
                    new_notif = "";
                }
            	html = `
                    <div class="d-flex cursor-pointer notification-element" value="${value['id']}" service_id="${value['service_id']}">
                        <div class="profile-img p-2">
                            <img class="rounded-circle fondo_blanco" src="${value['profile_photo']}" width="60px" height="60px"/>
                        </div>
                        <div class="w-100 align-self-center">
                            <b>${value['title']}</b><br>
                            ${value['notification']}
                        </div>
                        <div class="">
                            <div class="rounded-circle new-notif ${new_notif}">!</div>
                        </div>
                    </div>`;
            	$("#full_notif").append(html);
            })

            $(`.notification-element`).click(function () {
                var service_id = $(this).attr("service_id"),
                    notification_id = $(this).attr("value"),
                    new_notif = $(this).find("div.new-notif").hasClass("active");

                if(new_notif){
                    $(this).find("div.new-notif").removeClass("active");
                    marcarNotificacionFull(notification_id, service_id);
                }else{
                    mostrarVistaFull(service_id)
                }
            });
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
        		$("#notification").append(html);
        	})

		},
        error: function(response){
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
function marcarNotificacionFull(notification_id, service_id){
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
            mostrarVistaFull(service_id);
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
function mostrarVistaFull(service_id){
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