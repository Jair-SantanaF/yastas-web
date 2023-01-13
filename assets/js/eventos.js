var tabla_events = null,
    tabla_members = null,
    tabla_users = null;

jQuery(document).ready(function ($) {
    LoadCalendar();
    //Inicializar tabla de eventos
    ObtenerTablaEvents();

    //Validador para formulario de evento
    $('#form_event').validate({
        rules: {
            event_description: {
                required: true
            },
            event_note: {
                required: true
            },
            event_date: {
                required: true
            },
            event_time_start: {
                required: true
            },
            event_time_end: {
                required: true
            }
        },
        submitHandler: function (form) {
            GuardarEvent();
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



    // Handle click on "Select all" control
    $('#example-select-all').on('click', function () {
        // Check/uncheck all checkboxes in the table
        var rows = tabla_users.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });
});
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 12/15/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener todos los eventos
 ***********************************************************************/
function ObtenerTodosEventos() {
    $('#date_select').val('');
    tabla_events.ajax.reload();
}
/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Inicializacion de la tabla para listar eventos
 ***********************************************************************/
function ObtenerTablaEvents() {
    //Se agregan los eventos para los botones de "Ver miembros", "Editar" y "Eliminar" del listado
    $('#tabla_events').on('click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_events.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarEvent(record);
                }
                if (cmd == "eliminar") {
                    EliminarEvent(record)
                }
                if (cmd == "comentarios") {
                    $('#modal_miembros_detalle').modal('show');
                    ObtenerTablaMiembros(record)
                }
            }
        }
    });
    $('#tabla_events').on('tbody click', 'tr', function (e) {
        var record = tabla_events.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarEvent(record);
                }
                if (cmd == "eliminar") {
                    EliminarEvent(record)
                }
                if (cmd == "comentarios") {
                    $('#modal_miembros_detalle').modal('show');
                    ObtenerTablaMiembros(record)
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_events = $('#tabla_events').DataTable({
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
        "order": [[2, "desc"]],
        "ajax": {
            url: "../index.php/Events/Events",
            type: 'POST',
            data: function (d) {
                d.date = $('#date_select').val();
            },
            error: function (xhr, error, code) {
                console.log(xhr)
                tabla_events.clear().draw();
            }

        },
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            autoFilter: true,
            sheetName: 'Exported data'
        }],
        "columns": [
            {
                data: "description",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "note",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "usuario",
                render: function (data, type, row) {
                    var t = "";
                    t = "<div class='pt-2 pb-2'>" + data + "</div>"
                    return t;
                }
            },
            {
                data: "date",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "time_start",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "time_end",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "id",
                width: "100px",
                render: function (data, type, row) {
                    var t = '';
                    t = '' +
                        '<div class="pt-2 pb-2 text-center">' +
                        '<a title="Ver miembros" class="btn btn-info btn-xs editar mr-2 lead_0_8 text-white" cmd="comentarios"><i cmd="comentarios" class="fa fa-eye"></i></a>' +
                        '<a title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8 text-white" cmd="editar"><i cmd="editar" class="fa fa-edit"></i></a>' +
                        '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8 text-white" cmd="eliminar"><i cmd="eliminar" class="fa fa-times"></i></a>' +
                        '</div>';
                    return t;
                }
            }
        ]
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para agregar un nuevo evento, se muestra el formulario
 ***********************************************************************/
function AgregarEvent() {
    $('#form_event').trigger("reset");
    $('#event_id').val('');
    $('#modal_event').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para editar un evento, se carga y muestra el formulario
 ***********************************************************************/
function EditarEvent(record) {
    $('#form_event').trigger("reset");
    $('#event_id').val(record.id);
    $('#event_description').val(record.description);
    $('#event_note').val(record.note);
    $('#event_date').val(record.date);
    $('#event_time_start').val(record.time_start);
    $('#event_time_end').val(record.time_end);
    $('#modal_event').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar un evento(agregar o editar),  por peticion AJAX
 ***********************************************************************/
function GuardarEvent() {
    var event_id = $('#event_id').val(),
        data = new FormData(),
        method = "SaveEventAdmin";

    data.append('description', $('#event_description').val());
    data.append('note', $('#event_note').val());
    data.append('date', $('#event_date').val());
    data.append('time_start', $('#event_time_start').val());
    data.append('time_end', $('#event_time_end').val());


    //Si se cargo la llave del evento, entonces es edicion
    if (event_id != '') {
        data.append('id', event_id);
        method = "EditEvent"
    }

    //Cambio en los evento, ocultamos los apartados dependientes: miembros
    $('#seccion_miembros').addClass("d-none");
    if (tabla_members) tabla_members.clear().draw();

    $.ajax({
        url: window.base_url + "Events/" + method,
        type: "POST",
        contentType: false,
        data: data,
        processData: false,
        cache: false,
        success: function (response) {
            tabla_events.ajax.reload();
            LoadCalendar();
            $('#modal_event').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Eventos',
                text: 'El evento ha sido actualizado correctamente'
            });
            $('#modal_miembros_detalle').modal('show');
            ObtenerTablaMiembros({ 'id': response.data })
        },
        error: function () {
            $('#modal_event').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Eventos',
                text: 'El evento no se pudo guardar'
            });
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para eliminar un evento
 ***********************************************************************/
function EliminarEvent(record) {
    Swal.fire({
        title: 'Elminar Evento',
        text: "¿Estás seguro que deseas eliminar esta evento?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            //Cambio en los evento, ocultamos los apartados dependientes: miembros
            $('#seccion_miembros').addClass("d-none");
            if (tabla_members) tabla_members.clear().draw();
            $.ajax({
                url: window.base_url + "Events/DeleteEvent",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function (response) {
                    tabla_events.ajax.reload();
                    $('#modal_event').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Eliminar Evento',
                        text: 'El evento ha sido eliminado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    $('#modal_event').modal('hide');
                    Swal.fire({
                        type: 'error',
                        title: 'Eliminar Evento',
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
 *	Nota: Inicializacion de la tabla para listar miembros de un evento en particular
 ***********************************************************************/
function ObtenerTablaMiembros(record) {
    //Guardamos el evento seleccionado en una variable
    $('#selected_event_id').val(record.id);
    //Mostramos la seccion de miembros, para cargar los del evento seleccionado
    $('#seccion_miembros').removeClass("d-none");

    //Si la tabla de miembros ya habia sido creada con anterioridad
    if (tabla_members) {
        //Actualizamos la tabla de miembros para recibir los comentarios del nuevo evento seleccionado
        tabla_members.ajax.reload();
        return;
    }

    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_members').on('click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_members.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
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
    $('#tabla_members').on('tbody click', 'tr', function (e) {
        var record = tabla_members.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
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
            url: "../index.php/Events/Members",
            type: 'POST',
            data: function (d) {
                d.event_id = $('#selected_event_id').val();
            },
            error: function (xhr, error, code) {
                tabla_members.clear().draw();
            }
        },
        buttons: [],
        "columns": [{
            data: "name",
            render: function (data, type, row) {
                var t = '';
                t = '<div class="pt-2 pb-2">' + data + '</div>';
                return t;
            }
        }, {
            data: "id",
            width: "100px",
            render: function (data, type, row) {
                var t = '';
                t = '' +
                    '<div class="pt-2 pb-2 text-center">' +
                    '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8 text-white" cmd="eliminar"><i cmd="eliminar" class="fa fa-times"></i></a>' +
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
                url: window.base_url + "Events/DeleteMember",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function (response) {
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
    if (tabla_users) {
        $('#example-select-all').prop("checked", false);
        tabla_users.clear().draw();
        //Actualizamos la tabla de usuarios para recibir los usuarios disponibles para agregar
        tabla_users.ajax.reload();
        return;
    }

    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_users').on('click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_users.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
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
    $('#tabla_users').on('tbody click', 'tr', function (e) {
        var record = tabla_users.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
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
            url: "../index.php/Events/NoMembers",
            type: 'POST',
            data: function (d) {
                d.event_id = $('#selected_event_id').val();
            },
            error: function (xhr, error, code) {
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
            'render': function (data, type, full, meta) {
                return '<input type="checkbox" value="' + $('<div/>').text(data).html() + '">';
            }
        },
        {
            data: "name",
            render: function (data, type, row) {
                var t = '';
                t = '<div class="pt-2 pb-2">' + data + '</div>';
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
function AgregarUsuarios() {
    var data = {
        event_id: $('#selected_event_id').val()
    },
        usuarios_seleccionados = [];

    tabla_users.$('input[type="checkbox"]').each(function () {
        if (this.checked) {
            usuarios_seleccionados.push($(this).prop("value"));
        }
    });

    data.usuarios = JSON.stringify(usuarios_seleccionados);

    $.ajax({
        url: window.base_url + "Events/SaveMembers",
        type: "POST",
        data: data,
        cache: false,
        success: function (response) {
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
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/12/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener el plugin de con los datos de calendar
 ***********************************************************************/
function LoadCalendar() {
    var datos = new FormData();
    var config = {
        url: window.base_url + "events/listDateEvents",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: datos,
        success: function (response) {
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/13/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: recorremos el arreglo para obtener las fechas que tienen algun
             *          evento para marcarlo en el calendario
             ***********************************************************************/
            var dates = [];
            $.map(response.data, function (value, index) {
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
                height: 400,
                themeSystem: 'bootstrap',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                locale: 'es',
                dateClick: function (info) {
                    /***********************************************************************
                     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/13/2020
                     *		   mario.martinez.f@hotmail.es
                     *	Nota: obtenemos los datos de la fecha seleccionada
                     ***********************************************************************/
                    $('#date_select').val(info.dateStr);
                    tabla_events.ajax.reload();
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
                height: 400,
                themeSystem: 'bootstrap',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                locale: 'es',
                dateClick: function (info) {
                    /***********************************************************************
                     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/13/2020
                     *		   mario.martinez.f@hotmail.es
                     *	Nota: obtenemos los datos de la fecha seleccionada
                     ***********************************************************************/
                    $('#date_select').val(info.dateStr);
                    tabla_events.ajax.reload();
                    //GetDateSelect(info.dateStr,info.dayEl.outerText);
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
