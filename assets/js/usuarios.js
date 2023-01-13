var tabla_usuarios,
    tabla_invitados,
    grupos;
jQuery(document).ready(function ($) {
    ObtenerTablaUsuarios();
    ObtenerPuestos();
    obtenerGrupos();
    $('#form_usuario').validate({
        rules: {
            nombre_usuario: {
                required: true
            }
        },
        submitHandler: function (form) {
            //console.log(datos)
            GuardarUsuario();
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

function ObtenerPuestos() {
    $.ajax({
        url: window.base_url + "User/JobList",
        type: 'POST',
        contentType: false,
        //data: datos,
        processData: false,
        cache: false,
        success: function (json) {
            var html = '<option value="">Seleccionar</option>';
            for (var key in json.data) {
                html += '<option value="' + json.data[key].id + '">' + json.data[key].job_name + '</option>';
            }
            $('#id_puesto').html(html).fadeIn();
        }
    });
}

/*************CATEGORIAS**************/
/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Creacion de la tabla para listar usuarios
 ***********************************************************************/
function ObtenerTablaUsuarios(id_grupo) {
    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_usuarios').on('click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_usuarios.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarUsuario(record);
                }
                if (cmd == "eliminar") {
                    EliminarUsuario(record)
                }
                if (cmd == "aprobar") {
                    AprobarUsuario(record)
                }
            }
        }
    });
    $('#tabla_usuarios').on('tbody click', 'tr', function (e) {
        var record = tabla_usuarios.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarUsuario(record);
                }
                if (cmd == "eliminar") {
                    EliminarUsuario(record)
                }
                if (cmd == "aprobar") {
                    AprobarUsuario(record)
                }
            }
        }
    });

    var url = "";
    var data = {};
    console.log(id_grupo)
    if (id_grupo === undefined || id_grupo === 0) {
        url = "../index.php/User/UserList";
        data = {};
    } else {
        url = "../index.php/User/UserList";
        // url = "../index.php/Groups/UsersGroups";
        data = { group_id: id_grupo }
    }

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_usuarios = $('#tabla_usuarios').DataTable({
        responsive: false,
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
        // "order": [[0, 10]],
        "ajax": {
            url: url,
            type: 'POST',
            data: data,
            error: function (xhr, error, code) {
                console.log(xhr)
                tabla_usuarios.clear().draw();
            }
        },
        buttons: [],
        "columns": [
            {
                data: "number_employee",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + (data + "").substring(1) + '</div>';
                    return t;
                }
            },
            {
                data: "name",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            }, {
                data: "last_name",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },{
                data:"fecha_registro",
                sType:"date-uk",
                render:function(data,type, row){
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            }, {
                data: "email",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            }, {
                data: "id",
                render: function (data, type, row) {
                    var t = '', text = 'Aprobado';
                    if (row.register_no_invitation == 1) {
                        text = 'Pendiente';
                    }
                    t = '<div class="pt-2 pb-2">' + text + '</div>';
                    return t;
                }
            }, {
                data: "job_name",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            }, {
                data: "phone",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            }, {
                data: "score",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            }, {
                data: "grupos",
                render: function (data, type, row) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + data + '</div>';
                    return t;
                }
            },
            {
                data: "id",
                render: function (data, type, row, meta) {
                    var t = '',
                        button_no_invitation = '';
                    if (row.register_no_invitation == 1) {
                        button_no_invitation = '<button title="Aprobar acceso" class="btn btn-info btn-xs aprobar mr-2 lead_0_8 text-white" cmd="aprobar"><i cmd="aprobar" class="fa fa-check"></i></button>';
                    }
                    t = '' +
                        '<div class="pt-2 pb-2 text-center">' +
                        button_no_invitation +
                        '<button title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8 text-white" cmd="editar"><i cmd="editar" class="fa fa-edit"></i></button>' +
                        '<button title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8 text-white" cmd="eliminar"><i cmd="eliminar" class="fa fa-times"></i></button>' +
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
 *	Nota: funcion para agregar una nuevo usuario
 ***********************************************************************/
function AgregarUsuario() {
    $('#form_usuario').trigger("reset");
    $('#id_usuario').val('');
    $('#nombre_usuario').val('');
    $('#apellido_usuario').val('');
    $('#id_puesto').val('');
    $('#password').val('');
    $('#modal_usuario').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para editar un usuario, se carga y muestra el formulario
 ***********************************************************************/
function EditarUsuario(record) {
    $('#form_usuario').trigger("reset");
    $('#id_usuario').val(record.id);
    $('#nombre_usuario').val(record.name);
    $('#apellido_usuario').val(record.last_name);
    if ($('#id_puesto').length) {
        $('#id_puesto').val(record.job_id);
    }
    $('#password').val('');
    console.log(record)
    $("#email").val(record.email)

    $('#modal_usuario').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar por peticion AJAX un usuario
 ***********************************************************************/
function GuardarUsuario() {
    var id_usuario = $('#id_usuario').val(),
        password = $('#password').val(),
        datos = {
            name: $('#nombre_usuario').val(),
            last_name: $('#apellido_usuario').val(),
            job_id: ($('#id_puesto').length) ? $('#id_puesto').val() : 0,
            email : $("#email").val()
        },
        metodo = "SaveUser";

    if (id_usuario != '') {
        datos.id = id_usuario;
        metodo = "EditUser"
    }

    if (password != '') {
        datos.password = password;
    }

    $.ajax({
        url: window.base_url + "User/" + metodo,
        type: "POST",
        data: datos,
        cache: false,
        success: function (response) {
            console.log(response)
            tabla_usuarios.ajax.reload();
            $('#modal_usuario').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Catálogo usuarios',
                text: 'El catálogo de usuarios ha sido actualizado correctamente'
            });
        },
        error: function (error) {
            console.log(error)
            $('#modal_usuario').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Catálogo usuarios',
                text: 'El usuario no se pudo guardar'
            });
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para eliminar un usuario
 ***********************************************************************/
function EliminarUsuario(record) {
    Swal.fire({
        title: 'Catálogo usuarios',
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
                url: window.base_url + "User/DeleteUser",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function (response) {
                    tabla_usuarios.ajax.reload();
                    tabla_invitados.ajax.reload();
                    $('#modal_usuario').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Catálogo usuarios',
                        text: 'El catálogo de usuarios ha sido actualizado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    $('#modal_usuario').modal('hide');
                    Swal.fire({
                        type: 'error',
                        title: 'Catálogo usuarios',
                        text: error_msg
                    });
                }
            });
        }
    })
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para aprobar un usuario que no contaba con invitacion.
 ***********************************************************************/
function AprobarUsuario(record) {
    var datos = new FormData();
    var config = {
        url: window.base_url + "Groups/GroupsRegister",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: datos,
        success: function (response) {
            var datos = response.data,
                options = {};
            $.map(datos,
                function (o) {
                    options[o.id] = o.name;
                });
            Swal.fire({
                title: 'Usuarios',
                text: "¿Estás seguro que deseas aprobar este usuario?, una vez aprobado el usuario podra acceder a la app, por favor selecciona un grupo.",
                type: 'warning',
                input: 'select',
                inputOptions: options,
                animation: 'slide-from-top',
                inputPlaceholder: 'Seleccionar...',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'No'
            }).then((result) => {
                if (typeof result.dismiss === 'undefined') {
                    if (result.value !== '') {
                        $.ajax({
                            url: window.base_url + "User/AcceptInvitation",
                            type: "POST",
                            data: {
                                id: record.id,
                                group_id: result.value
                            },
                            cache: false,
                            success: function (response) {
                                tabla_usuarios.ajax.reload();
                                Swal.fire({
                                    type: 'success',
                                    title: 'Catálogo usuarios',
                                    text: 'El usuario ha sido aprobado ahora podra acceder a la app'
                                });
                            },
                            error: function (error) {
                                var error_msg = error.responseJSON.error_msg;
                                Swal.fire({
                                    type: 'error',
                                    title: 'Usuarios',
                                    text: error_msg
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            type: 'error',
                            title: 'Usuarios',
                            text: 'Por favor selecciona un grupo'
                        });
                    }
                }
            })
        },
        error: function (response) {
            var error_msg = error.responseJSON.error_msg;
            Swal.fire({
                type: 'error',
                title: 'Grupos',
                text: error_msg
            });
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener los grupos registrados
 ***********************************************************************/
function GroupsRegistrados() {
    var datos = new FormData();
    var config = {
        url: window.base_url + "Groups/GroupsRegister",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: datos,
        success: function (response) {
            var grupo_id = $('#group_id');
            $.each(response.data, function (index, value) {
                if (index === 0) {
                    grupo_id.append($('<option>', {
                        value: '',
                        text: 'Seleccionar...'
                    }));
                }
                grupo_id.append($('<option>', {
                    value: value['id'],
                    text: value['name']
                }));
            });
        },
        error: function (response) {
            var error_msg = error.responseJSON.error_msg;
            Swal.fire({
                type: 'error',
                title: 'Grupos',
                text: error_msg
            });
        }
    }
    $.ajax(config);
}

function obtenerGrupos() {
    $.ajax({
        url: window.base_url + "Groups/GroupsRegister",
        type: 'POST',
        data: {

        },
        dataType: 'json',
        error: function (xhr, error, code) {
            console.log(xhr)
        },
        success: function (json) {
            grupos = json.data
            console.log(grupos)
            generarSelectConGrupos()
        }
    })
}

function generarSelectConGrupos() {
    var select = document.getElementById("grupos");
    var html = "<option value='0'>Todos</option>";
    for (var i = 0; i < grupos.length; i++) {
        html += "<option value='" + grupos[i].id + "'>" + grupos[i].name + "</option>"
    }
    select.innerHTML = html
}


function obtenerUsuarios() {
    var id_grupo = document.getElementById("grupos").value;
    $('#tabla_usuarios').dataTable().fnDestroy();
    ObtenerTablaUsuarios(id_grupo)
}

function descargarCsv(){
    var url = window.base_url + "User/DescargarCsvRegistrados/"
    window.open(url);
}