var table_groups = null,
    tabla_members = null,
    tabla_users = null;

jQuery(document).ready(function ($) {
    //Inicializar tabla de eventos
    ObtenerTablaGrupos();

    //Validador para formulario de evento
    $('#form_group').validate({
        rules: {
            name: {
                required: true
            }
        },
        submitHandler: function (form) {
            var datos = new FormData($('#form_group')[0]);
            //console.log(datos)
            GuardarGrupo(datos);
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
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Obtener tabla de grupos registrados
 ***********************************************************************/
function ObtenerTablaGrupos() {
    //Se agregan los eventos para los botones de "Ver miembros", "Editar" y "Eliminar" del listado
    $('#table_groups').on('click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = table_groups.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarGroup(record);
                }
                if (cmd == "eliminar") {
                    EliminarGroup(record)
                }
                if (cmd == "asignados") {
                    $('#modal_miembros_detalle').modal('show');
                    ObtenerTablaMiembros(record)
                }
            }
        }
    });
    $('#table_groups').on('tbody click', 'tr', function (e) {
        var record = table_groups.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarGroup(record);
                }
                if (cmd == "eliminar") {
                    EliminarGroup(record)
                }
                if (cmd == "asignados") {
                    $('#modal_miembros_detalle').modal('show');
                    ObtenerTablaMiembros(record)
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    table_groups = $('#table_groups').DataTable({
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
        "order": [[0, "asc"]],
        "ajax": {
            url: "../index.php/Groups/GroupsRegister",
            type: 'POST',
            data: function (d) {
            },
            error: function (xhr, error, code) {
                table_groups.clear().draw();
            }

        },
        buttons: [],
        "columns": [
            {
                data: "name",
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
                        '<a title="Usuarios asignados" class="btn btn-info btn-xs editar mr-2 lead_0_8 text-white" cmd="asignados"><i cmd="asignados" class="fa fa-eye"></i></a>' +
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
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para agregar un nuevo grupo.
 ***********************************************************************/
function AgregarGrupo() {
    $('#form_group').trigger("reset");
    $('#group_id').val('');
    $('#modal_grupo').modal('show');
}

/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para editar un grupo
 ***********************************************************************/
function EditarGroup(record) {
    $('#form_group').trigger("reset");
    $('#group_id').val(record.id);
    $('#name').val(record.name);
    $('#modal_grupo').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar un evento(agregar o editar),  por peticion AJAX
 ***********************************************************************/
function GuardarGrupo(data) {
    var group_id = $('#group_id').val(),
        method = "SaveGroup";


    //Si se cargo la llave del evento, entonces es edicion
    if (group_id != '') {
        data.append('id', group_id);
        method = "EditGroup"
    }
    //Cambio en los evento, ocultamos los apartados dependientes: miembros
    $('#seccion_miembros').addClass("d-none");
    if (tabla_members) tabla_members.clear().draw();
    $.ajax({
        url: window.base_url + "Groups/" + method,
        type: "POST",
        contentType: false,
        data: data,
        processData: false,
        cache: false,
        success: function (response) {
            table_groups.ajax.reload();
            $('#modal_grupo').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Grupos',
                text: 'El el grupo ha sido guardado correctamente'
            });
        },
        error: function () {
            $('#modal_grupo').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Grupos',
                text: 'El grupo no se pudo guardar'
            });
        }
    });
}

/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para eliminar un grupo
 ***********************************************************************/
function EliminarGroup(record) {
    Swal.fire({
        title: 'Elminar grupo',
        text: "¿Estás seguro que deseas eliminar este grupo?",
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
                url: window.base_url + "Groups/DeleteGroup",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function (response) {
                    table_groups.ajax.reload();
                    $('#modal_grupo').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Eliminar grupo',
                        text: 'El grupo ha sido eliminado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    $('#modal_grupo').modal('hide');
                    Swal.fire({
                        type: 'error',
                        title: 'Eliminar grupo',
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
    $('#selected_group_id').val(record.id);
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
        "order": [[0, "asc"]],
        "ajax": {
            url: "../index.php/Groups/UsersGroups",
            type: 'POST',
            data: function (d) {
                d.group_id = $('#selected_group_id').val();
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
            data: "numero_empleado",
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
 *	Nota: funcion para eliminar un miembro
 ***********************************************************************/
function EliminarMember(record) {
    Swal.fire({
        title: 'Eliminar Usuario',
        text: "¿Estás seguro que deseas eliminar este usuario?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: window.base_url + "Groups/DeleteUser",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function (response) {
                    tabla_members.ajax.reload();
                    Swal.fire({
                        type: 'success',
                        title: 'Eliminar usuario',
                        text: 'El usuario ha sido eliminado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    Swal.fire({
                        type: 'error',
                        title: 'Eliminar usuario',
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
        "order": [[1, "asc"]],
        "ajax": {
            url: "../index.php/Groups/NoUsersGroups",
            type: 'POST',
            data: function (d) {
                d.group_id = $('#selected_group_id').val();
            },
            error: function (xhr, error, code) {
                console.log(xhr)
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
        },
        {
            data: "number_employee",
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
        group_id: $('#selected_group_id').val()
    },
        usuarios_seleccionados = [];

    tabla_users.$('input[type="checkbox"]').each(function () {
        if (this.checked) {
            usuarios_seleccionados.push($(this).prop("value"));
        }
    });

    data.users = JSON.stringify(usuarios_seleccionados);

    $.ajax({
        url: window.base_url + "Groups/SaverUser",
        type: "POST",
        data: data,
        cache: false,
        success: function (response) {
            tabla_members.ajax.reload();
            Swal.fire({
                type: 'success',
                title: 'Agregar usuario',
                text: 'Los usuarios se han sido agregados correctamente'
            });
        },
        error: function (error) {
            tabla_members.ajax.reload();
            var error_msg = error.responseJSON.error_msg;
            Swal.fire({
                type: 'error',
                title: 'Agrear usuario',
                text: error_msg
            });
        }
    });
    $('#modal_member').modal('hide');
}
