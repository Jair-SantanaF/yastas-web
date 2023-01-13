var data_select = '',
    tabla_members = null,
    tabla_users = null;
$(document).ready(function () {
    LoadCalendar();
    $('#photo_user').attr('src',window.photo);
    $('#name_user').html(window.name);
    $('#job_name').html(window.job_name);
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/13/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Formulario apra registro de agenda
     ***********************************************************************/
    $('#new_event').validate({
        rules: {
            description:{
                required: true
            },
            date:{
                required: true
            },
            time_start:{
                required: true
            },
            time_end:{
                required: true
            },
            note:{
                required: true
            }
        },
        submitHandler: function () {
            var datos = new FormData($('#new_event')[0]);
            //console.log(datos)
            SaveEvent(datos);
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
});
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/12/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener el plugin de con los datos de calendar
 ***********************************************************************/
function LoadCalendar(){
    var datos = new FormData();
    var config = {
        url: window.base_url + "events/listDateEvents",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/13/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: recorremos el arreglo para obtener las fechas que tienen algun
             *          evento para marcarlo en el calendario
             ***********************************************************************/
            var dates = [];
            $.map(response.data,function (value,index){
                dates.push({
                    start: value['date'],
                    end: value['date'],
                    display: 'background',
                    overlap: false,
                    color: '#00793A'
                });
            });
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                height:400,
                themeSystem: 'bootstrap',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                locale: 'es',
                dateClick: function(info) {
                    /***********************************************************************
                     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/13/2020
                     *		   mario.martinez.f@hotmail.es
                     *	Nota: obtenemos los datos de la fecha seleccionada
                     ***********************************************************************/
                    GetDateSelect(info.dateStr,info.dayEl.outerText);
                },
                navLinks: false, // can click day/week names to navigate views
                businessHours: true, // display business hours
                editable: false,
                selectable: false,
                events: dates
            });

            calendar.render();
        },
        error: function (response) {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                height:400,
                themeSystem: 'bootstrap',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                locale: 'es',
                dateClick: function(info) {
                    /***********************************************************************
                     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/13/2020
                     *		   mario.martinez.f@hotmail.es
                     *	Nota: obtenemos los datos de la fecha seleccionada
                     ***********************************************************************/
                    GetDateSelect(info.dateStr,info.dayEl.outerText);
                },
                navLinks: false, // can click day/week names to navigate views
                businessHours: true, // display business hours
                editable: false,
                selectable: false,
                events: []
            });

            calendar.render();
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/13/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener el detalle de una fecha en especifico.
 ***********************************************************************/
var day_select = '',date_select = '';
function GetDateSelect(date,day){
    day_select = day;
    date_select = date;
    $('#date_select').val(date);
    $('#members_register').html('');
    var datos = new FormData();
    datos.append('date',date);
    var config = {
        url: window.base_url + "events/ListEvents",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/13/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Recorremos la respuesta para mostrar la informacion precargada
             *          adicional cargamos el listado de los demas eventos.
             ***********************************************************************/
            var events = '';
            data_select = response.data;
            $.map(response.data,function (value,index){
                //Seleccionar automaticamente el primer avento
                if(index === 0){
                    $('#day_select').html(day);
                    $('#title_event').html(value['description']);
                    $('#detail_time').html(value['time_start']+' - '+value['time_end']);
                    $('#description_event').html(value['note']);
                    ConstrucMember(value['id'], value['owner']);
                }
                var buttons = '';
                if(value['owner'] == 1){
                    buttons = '<i title="Editar" class="fa fa-edit cursor-pointer text-black-50 ml-2" onclick="EditEvent('+index+')"></i>' +
                        '<i title="Eliminar" class="fa fa-window-close cursor-pointer text-black-50 ml-1" onclick="DeleteEvent('+value['id']+')"></i>';
                }
                events+=' <tr style="border-bottom: 1px solid black;">' +
                    '<td class="verde_basf_dark border_table_events">'+value['time_start']+' - '+value['time_end']+'</td>' +
                    '<td class="pl-3 verde_basf_dark" style="border-top: none;">'+value['description']+'<i title="Ver detalle" class="fa fa-eye cursor-pointer text-black-50 ml-1" onclick="ViewEvent('+index+')"></i>' +buttons+'</td>' +
                '</tr>';

            });
            $('#tbody_events').html(events);
            $('.content_events').hide();
            $('#view_event').show();
        },
        error: function (response) {
            $('#day_select').html(day);
            $('#title_event').html('');
            $('#detail_time').html('Sin registros');
            $('#description_event').html('');
            $('#tbody_events').html('');
            $('.content_events').hide();
            $('#view_event').show();
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/13/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para eliminar un evento.
 ***********************************************************************/
function DeleteEvent(id){
    Swal.fire({
        title: 'Eventos',
        text: "¿Estás seguro que deseas eliminar este evento?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            var datos = new FormData();
            datos.append('id',id);
            var config = {
                url: window.base_url + "events/DeleteEvent",
                type: "POST",
                cache: false,
                contentType:false,
                processData: false,
                data: datos,
                success: function(response) {
                    Swal.fire({
                        type: 'success',
                        title: 'Eventos',
                        text: response.msg
                    });
                    LoadCalendar();
                    GetDateSelect(date_select,day_select);
                },
                error: function (response) {
                    Swal.fire({
                        type: 'error',
                        title: 'Eventos',
                        text: response.responseJSON.error_msg
                    });
                }
            }
            $.ajax(config);
        }
    });
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/13/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para cargar los datos de un event
 ***********************************************************************/
var event_id = '';
function EditEvent(index){
    var data = data_select[index];
    event_id = data.id;
    $('.content_events').hide();
    $('#new_event').show().trigger('reset');
    $('#date').val(date_select).change();
    $("#description").val(data.description);
    $("#time_start").val(data.time_start);
    $("#time_end").val(data.time_end);
    $("#note").val(data.note);
    $('#content_button_members').css('display', 'flex');
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/13/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para abrir un modal para agregar un evento nuevo
 ***********************************************************************/
function NewEvent(){
    event_id = '';
    $('.content_events').hide();
    $('#new_event').show().trigger('reset');
    $('#date').val(date_select).change();
    $('#content_button_members').hide();
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/13/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para guardar un evento nuevo
 ***********************************************************************/
function SaveEvent(datos){
    var metodo = 'SaveEvent';
    if(event_id !== ''){
        metodo = 'EditEvent';
        datos.append('id',event_id);
    }

    var config = {
        url: window.base_url + "events/"+metodo,
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            Swal.fire({
                type: 'success',
                title: 'Eventos',
                text: response.msg
            }).then((result) => {
                $('#modal_miembros_detalle').modal('show');
                event_id = response.data;
                ObtenerTablaMiembros({'id':event_id});
                LoadCalendar();
                GetDateSelect(date_select,day_select);
            });
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Eventos',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Inicializacion de la tabla para listar miembros de un evento en particular
 ***********************************************************************/
function ObtenerTablaMiembros(record) {
    //Guardamos el evento seleccionado en una variable
    $('#selected_event_id').val(record.id);
    //Mostramos la seccion de miembros, para cargar los del evento seleccionado
    $('#seccion_miembros').removeClass("d-none");

    //Si la tabla de miembros ya habia sido creada con anterioridad
    if(tabla_members){
        //Actualizamos la tabla de miembros para recibir los comentarios del nuevo evento seleccionado
        tabla_members.ajax.reload();
        return;
    }

    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_members').on( 'click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_members.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if(es_boton){
                if(cmd=="editar"){
                    EditarMember(record);
                }
                if(cmd=="eliminar"){
                    EliminarMember(record);
                }
            }
        }
    } );
    $('#tabla_members').on('tbody click','tr',function(e){
        var record = tabla_members.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarMember(record);
                }
                if (cmd == "eliminar") {
                    EliminarMember(record);
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_members = $('#tabla_members').DataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Registros del 0 al 0 de un total de 0 registros",
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
        "idSrc": "id",
        "order": [[0, "desc"]],
        "ajax": {
            url: window.base_url+"Events/Members",
            type: 'POST',
            data: function (d) {
                d.event_id = event_id;
            },
            error: function (xhr, error, code){
                tabla_members.clear().draw();
            }
        },
        buttons: [],
        "columns": [{
            data: "name",
            render: function ( data, type, row ) {
                var t = '';
                t = '<div class="pt-2 pb-2">'+data+'</div>';
                return t;
            }
        },{
            data: "id",
            width:"100px",
            render: function ( data, type, row ) {
                var t = '';
                t = '' +
                    '<div class="pt-2 pb-2 text-center">' +
                    '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8 text-white" cmd="eliminar"><i cmd="eliminar" class="fa fa-times"></i></a>'+
                    '</div>';
                return t;
            }
        }]
    });
}
/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para agregar un nuevo miembro, se muestra el modal
 ***********************************************************************/
function AgregarMember() {
    $('#modal_member').modal('show');
    ObtenerTablaUsuarios();
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para editar un miembro, se carga y muestra el modal
 ***********************************************************************/
function EditarMember(record) {
    $('#modal_member').modal('show');
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/13/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para ver el detalle del evento
 ***********************************************************************/
function ViewEvent(index){
    var data = data_select[index];
    $('#day_select').html(day_select);
    console.info(data);
    ConstrucMember(data.id, data.owner);
    $('#title_event').html(data.description);
    $('#detail_time').html(data.time_start +' - '+data.time_end);
    $('#description_event').html(data.note);
    $('.content_events').hide();
    $('#view_event').show();
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/13/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para cargar y dibujar el contenido de los miembros
 ***********************************************************************/
function ConstrucMember(event_id, owner){
    var datos = new FormData();
    var html = '';

    datos.append("event_id", event_id);

    $('#members_register').html('<div class="verde_basf_dark text-center col-12 lead">Cargando...</div>');
    var config = {
        url: window.base_url + "events/Members",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            var members = response.data;
            $.map(members,function(value,index){
                html+='<div class="row justify-content-center mb-2 pb-2" style="border-bottom: 1px solid black;">' +
                    '<div class="col-xl-2"><img style="width: 50px; height: 50px;" src="'+value['profile_photo']+'" class="rounded-circle border-shadow"></div>' +
                    '<div class="col-xl-8 my-auto text-left">'+value['name']+'</div>'+
                    '</div>';
            });
            $('#members_register').html(html);
        },
        error: function (response) {
            html = '<div class="verde_basf_dark text-center col-12 lead">Sin registros</div>';
            $('#members_register').html(html);
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para eliminar un miembro
 ***********************************************************************/
function EliminarMember(record) {
    Swal.fire({
        title: 'Eliminar Miembro',
        text: "¿Estás seguro que deseas eliminar este miembro?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url:  window.base_url+"Events/DeleteMember",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function(response){
                    tabla_members.ajax.reload();
                    Swal.fire({
                        type: 'success',
                        title: 'Eliminar miembro',
                        text: 'El miembro ha sido eliminado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    Swal.fire({
                        type: 'error',
                        title: 'Eliminar miembro',
                        text: error_msg
                    });
                }
            });
        }
    })
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Inicializacion de la tabla para listar usuarios disponibles para agregar
 ***********************************************************************/
function ObtenerTablaUsuarios(record) {
    //Si la tabla de usuarios ya habia sido creada con anterioridad
    if(tabla_users){
        $('#example-select-all').prop( "checked", false );
        tabla_users.clear().draw();
        //Actualizamos la tabla de usuarios para recibir los usuarios disponibles para agregar
        tabla_users.ajax.reload();
        return;
    }

    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_users').on( 'click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_users.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if(es_boton){
                if(cmd=="editar"){
                    EditarMember(record);
                }
                if(cmd=="eliminar"){
                    EliminarMember(record);
                }
            }
        }
    } );
    $('#tabla_users').on('tbody click','tr',function(e){
        var record = tabla_users.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarMember(record);
                }
                if (cmd == "eliminar") {
                    EliminarMember(record);
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_users = $('#tabla_users').DataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Registros del 0 al 0 de un total de 0 registros",
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
        "idSrc": "id",
        "order": [[0, "desc"]],
        "ajax": {
            url: window.base_url+"Events/NoMembers",
            type: 'POST',
            data: function (d) {
                d.event_id = event_id;
            },
            error: function (xhr, error, code){
                tabla_users.clear().draw();
            }
        },
        buttons: [],
        "columns": [{
            targets: 0,
            data: "id",
            defaultContent: '',
            orderable: false,
            'className': 'dt-body-center',
            'render': function (data, type, full, meta){
                return '<input type="checkbox" value="'+ $('<div/>').text(data).html() + '">';
            }
        },
            {
                data: "name",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            }]
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Funcion para guardar los usuarios seleccionados como miembros del evento
 ***********************************************************************/
function AgregarUsuarios(){
    var data={
            event_id: event_id
        },
        usuarios_seleccionados = [];

    tabla_users.$('input[type="checkbox"]').each(function(){
        if(this.checked){
            usuarios_seleccionados.push($(this).prop("value"));
        }
    });

    data.usuarios = JSON.stringify(usuarios_seleccionados);

    $.ajax({
        url:  window.base_url+"Events/SaveMembers",
        type: "POST",
        data: data,
        cache: false,
        success: function(response){
            tabla_members.ajax.reload();
            Swal.fire({
                type: 'success',
                title: 'Agregar miembros',
                text: 'Los miembros han sido agregados correctamente'
            });
        },
        error: function (error) {
            tabla_members.ajax.reload();
            var error_msg = error.responseJSON.error_msg;
            Swal.fire({
                type: 'error',
                title: 'Eliminar miembro',
                text: error_msg
            });
        }
    });
    $('#modal_member').modal('hide');
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/13/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para cargar la vista de usuarios agregados
 ***********************************************************************/
function MembersSelects(){
    $('#modal_miembros_detalle').modal('show');
    ObtenerTablaMiembros({'id':event_id});
}