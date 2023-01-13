var tabla_usuarios,
    tabla_invitados;
jQuery(document).ready(function ($) {
    ObtenerTablaInvitados();
    GroupsRegistrados();
    obtener_regiones();
    $('#form_invitado').validate({
        rules: {
            // email: {
            //     required: true
            // },
            number_employee: {
                required: true
            },
            // nombre: {
            //     required: true
            // },
            // apellido: {
            //     required: true
            // },
            group_id: {
                required: true
            }
        },
        submitHandler: function (form) {
            var datos = new FormData($('#form_invitado')[0]);
            //console.log(datos)
            GuardarInvitado(datos);
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
    $("#region_id").on('change', function(){
        var id = $(this).val();
       
        if (id != 0) {
            //cargamos el select de asesores
            obtener_asesores(id);
        }
        
    });

});
/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para agregar una nuevo usuario
 ***********************************************************************/
function AgregarInvitado() {
    $('#form_invitado').trigger("reset");
    $('#invited_id').val('');
    $('#modal_invitado').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para editar un usuario, se carga y muestra el formulario
 ***********************************************************************/
function EditarInvitado(record) {
    $('#form_invitado').trigger("reset");
    $('#invited_id').val(record.id);
    $('#email').val(record.email);
    $('#nombre').val(record.name)
    $("#apellido").val(record.last_name)
    $("#number_employee").val(record.number_employee)
    $('#group_id').val(record.group_id).change();

    $('#modal_invitado').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar por peticion AJAX un usuario
 ***********************************************************************/
function GuardarInvitado(data) {
    var invited_id = $('#invited_id').val(),
        metodo = "Ws/sendMemberRequest";

    if (invited_id != '') {
        data.append('invited_id', invited_id);
        metodo = "User/EditInvited"
    }
    $.ajax({
        url: window.base_url + metodo,
        type: "POST",
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        success: function (response) {
            console.log(response)
            tabla_invitados.ajax.reload();
            $('#modal_invitado').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Invitados',
                text: 'El invitado se ha guardado correctamente'
            });
        },
        error: function (error) {
            console.log(error)
            $('#modal_invitado').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Invitados',
                text: error.responseJSON.error_msg
            });
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Creacion de la tabla para listar invitados
 ***********************************************************************/
function ObtenerTablaInvitados() {
    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_invitados').on('click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_invitados.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "eliminar") {
                    EliminarInvitado(record)
                }
                if (cmd == "editar") {
                    EditarInvitado(record)
                }
            }
        }
    });
    $('#tabla_invitados').on('tbody click', 'tr', function (e) {
        var record = tabla_invitados.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if (typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "eliminar") {
                    EliminarInvitado(record)
                }
                if (cmd == "editar") {
                    EditarInvitado(record)
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_invitados = $('#tabla_invitados').DataTable({
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
            url: "../index.php/User/InvitedList",
            type: 'POST',
            data: function (d) {
            },
            error: function (xhr, error, code) {
                console.log(xhr)
                tabla_invitados.clear().draw();
            }
        },
        buttons: [],
        "columns": [
            {
                data: "email",
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
                    t = '<div class="pt-2 pb-2">' + (data || "").substring(1) + '</div>';
                    return t;
                }
            },
            {
                data: "status",
                render: function (data, type, row) {
                    var t = '';
                    if (data == 1) {
                        t = '<div class="pt-2 pb-2">Invitado registrado</div>';
                    } else {
                        t = '<div class="pt-2 pb-2">Invitado sin registrar</div>';
                    }
                    return t;
                }
            },
            {
                data: "id",
                width: "100px",
                render: function (data, type, row) {
                    var t = '';
                    if (row.status == 1) {
                        t = '<div class="pt-2 pb-2">Sin acciones, invitado registrado.</div>';
                    } else {
                        t = '<div class="pt-2 pb-2 text-center">' +
                            '<a class="btn btn-primary btn-xs editar mr-2 lead_0_8 text-white" cmd="editar"><i class="fa fa-edit"></i></a>' +
                            '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8 text-white" cmd="eliminar"><i cmd="eliminar" class="fa fa-times"></i></a>' +
                            '</div>';
                    }
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
 *	Nota: funcion para eliminar un área de elementos de biblioteca
 ***********************************************************************/
function EliminarInvitado(record) {
    Swal.fire({
        title: 'Catálogo invitados',
        text: "¿Estás seguro que deseas eliminar esta invitado?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: window.base_url + "User/DeleteInvited",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function (response) {
                    tabla_invitados.ajax.reload();
                    $('#modal_invitados').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Invitados',
                        text: 'El invitado se ha eliminado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    $('#modal_invitados').modal('hide');
                    Swal.fire({
                        type: 'error',
                        title: 'Invitados',
                        text: error_msg
                    });
                }
            });
        }
    })
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/8/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener los grupos registrados y agregarlos
 *          en el selector
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

//Obtenemos las regiones del usuairo log
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

function generar_select_regiones(regiones) {
    var select = get("region_id")
    var html = ""
    
    for (var i = 0; i < regiones.length; i++) {
        html += '<option value="' + regiones[i].id + '">' + regiones[i].nombre + '</option>'
    }
    select.innerHTML = html
    console.log(id_region)
    select.value = id_region
}

function obtener_asesores(id_region) {
    
    var config = {
        url: window.base_url + "ws/ObtenerAsesores",
        type: "POST",
        data: {id_region:id_region},
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
    var select = get("asesor_id")
    var html = ""
    for (var i = 0; i < asesor.length; i++) {
        html += '<option value="' + asesor[i].id + '">' + asesor[i].nombre + '</option>'
    }
    select.innerHTML = html
  //  console.log(id_planta)
    //select.value = id_planta
}