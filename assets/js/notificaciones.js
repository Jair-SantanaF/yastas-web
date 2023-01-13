jQuery(document).ready(function ($) {
    obtener_notificaciones()
    obtener_regiones()
    
    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández
     *           mario.martinez.f@hotmail.es
     *    Fecha: 08 sep 2017
     *    Nota: Plugin para tabla.
     ***********************************************************************/
    $('.datatable').dataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        "order": [[0, "desc"]]
    });
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 07/07/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Se carga la acción para hacer la peticion de notificacion, para
     *          enviarla a firebase
     ***********************************************************************/
    $('#form_notificacion').validate({
        rules: {
            notificacion: {
                required: true
            },
            titulo: {
                required: true
            },
            service_id: {
                required: true
            }
        },
        submitHandler: function (form) {
         var datos = new FormData($('#form_notificacion')[0]);
         var r = $('#select_region').val();
         var regiones = r.toString(); 
         var a = $('#select_asesor').val();
         var asesores = a.toString(); 
        
         
        datos.delete('select_asesor');  
        datos.append('select_asesor',asesores);
        datos.delete('select_region');  
        datos.append('select_region',regiones);
            
            EnviarNotificacion(datos);
        },
        errorElement: "em",
        errorPlacement: function (error, element) {
            // Add the `help-block` class to the error element
            error.addClass("help-block rojo_error");
            if (element.prop("type") === "checkbox") {
                error.insertAfter(element.parent("label"));
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass("error_").removeClass("success_");
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).addClass("success_").removeClass("error_");
        }
    });

                
    //funcion onchange regiones para mostrar asesores
    
    $('#select_region').on('change',function () {
        
        var regiones = new Array();
        var selectRegiones = $(this).val();
        //limpiamos el array y lo rellenamos de nuevo
        regiones.length = 0;
        regiones.push(selectRegiones);
        obtener_asesores(regiones);
    });

    $('#select_asesor').on('change',function () {
        
        var asesores = new Array();
        var selectAsesores = $(this).val();
        asesores.length = 0;
        //limpiamos el array y lo rellenamos de nuevo
        asesores.push(selectAsesores);
        
    });
            
    
});
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 09/07/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: funcion para registrar una notificacion y enviarla al grupo
 *          de la empresa de la sesion
 ***********************************************************************/
function EnviarNotificacion(datos) {
    alert(datos.get('select_region'));
    if($("#select_region").val() == 0){
        alert("Seleccionar una Región para enviar notificaciones.");
        return
    }
    
    
    var config = {
        url: window.base_url + "Notification/enviarMultiplePush",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: datos,
        success: function (response) {
            Swal.fire({
                type: 'success',
                title: 'Notificación',
                text: 'La notificación ha sido enviada correctamente'
            });
            limpiarForm();
        },
        error: function (response) {
            console.log(response)
            Swal.fire({
                type: 'error',
                title: 'Notificación',
                text: 'La notificación no ha sido enviada correctamente'
            });
        }
    }
    $.ajax(config);
}


function obtener_notificaciones() {
    var config = {
        url: window.base_url + "Notification/obtener_notificaciones",
        type: "POST",
        data: {},
        success: function (response) {
            console.log(response)
            generar_tabla_notificaciones(response.data)
        },
        error: function (response) {
            console.log(response)
        }
    }
    $.ajax(config);
}

function generar_tabla_notificaciones(data) {
    var tabla = document.getElementById("contenedor_notificaciones")
    var html = ''
    for (var i = 0; i < data.length; i++) {
        html += "<tr>"
        html += "<td>" + data[i].notification + "</td>"
        html += "<td>" + data[i].service_name + "</td>"
        html += "<td>" + data[i].fecha + "</td>"
        html += "<td>" + data[i].name + "</td>"
        html += "</tr>"
    }
    tabla.innerHTML = html
}



function obtener_asesores(id_region) {
    var regiones = id_region.toString();
    var config = {
        url: window.base_url + "ws/ObtenerAsesoresMultiple",
        type: "POST",
        data: {id_region:regiones},
        success: function (response) {
            console.log(response)
            generar_select_asesores(response.data)
        },
        error: function (response) {
            console.log(response)
        }
    }
    $.ajax(config);
}

function generar_select_asesores(asesor) {
    var select = get("select_asesor")
    var html = ""
    for (var i = 0; i < asesor.length; i++) {
        html += '<option value="' + asesor[i].id + '">' + asesor[i].nombre + '</option>'
    }
    select.innerHTML = html
  //  console.log(id_planta)
    //select.value = id_planta
}

function obtener_plantas(id_region) {
    
    var config = {
        url: window.base_url + "ws/ObtenerPlantas",
        type: "POST",
        data: {id_region:id_region},
        success: function (response) {
            console.log(response)
            generar_select_plantas(response.data)
        },
        error: function (response) {
            console.log(response)
        }
    }
    $.ajax(config);
}

function generar_select_plantas(planta) {
    var select = get("select_planta")
    var html = ""
    for (var i = 0; i < asesor.length; i++) {
        html += '<option value="' + asesor[i].id + '">' + asesor[i].planta + '</option>'
    }
    select.innerHTML = html
  //  console.log(id_planta)
    //select.value = id_planta
}


function obtener_regiones() {
    var config = {
        url: window.base_url + "ws/ObtenerRegiones",
        type: "POST",
        data: {},
        success: function (response) {
            console.log(response)
            generar_select_regiones(response.data)
        },
        error: function (response) {
            console.log(response)
        }
    }
    $.ajax(config);
}

// //para el select en checkbox
// function generar_select_regiones(region) {
//     var checkb = get("checkRegion")
//     var html = ""
//     for (var i = 0; i < region.length; i++) {
//         html += '<div class="form-check form-check-inline"><input class="form-check-input" onclick="checkboxselect(' + region[i].id + ');" type="checkbox" id="checkboxregion" value="' + region[i].id + '"><label class="form-check-label" for="inlineCheckbox1">' + region[i].nombre + '</label></div>'
//     }
//     checkb.innerHTML  = html
//   //  console.log(id_planta)
//     //select.value = id_planta
// }

//para el select de las regiones
function generar_select_regiones(region) {
    var select = get("select_region")
    var html = ""
    for (var i = 0; i < region.length; i++) {
        html += '<option value="' + region[i].id + '">' + region[i].nombre + '</option>'
    }
    select.innerHTML  = html
  //  console.log(id_planta)
    //select.value = id_planta
}


function limpiarForm(){
    $("form select").each(function() { this.selectedIndex = 0 });
    $("form input[type=text] , form textarea").each(function() { this.value = '' });
}

function selectAllRegiones() {
    $('#select_region option').prop('selected', true);
    var regiones = $('#select_region').val();    
    obtener_asesores(regiones);
}

function selectAllAsesores() {
    $('#select_asesor option').prop('selected', true);
    var asesores = $('#select_asesores').val();    
    
}

function descargar() {
    var url = base_url + "Notification/descargar_notificaciones";
    window.open(url);
}