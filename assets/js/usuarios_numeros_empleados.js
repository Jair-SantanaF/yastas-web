var tabla_usuarios,
    tabla_numeros;
jQuery(document).ready(function ($) {
    ObtenerTablaNumeros();
    GroupsRegistrados();
    PuestosRegistrados();
    $('#form_numero_empleado').validate({
        rules: {
            email: {
                required: true
            },
            group_id: {
                required: true
            },
            job_id: {
                required: true
            },
            number: {
                required: true
            }
        },
        submitHandler: function (form) {
            var datos = new FormData($('#form_numero_empleado')[0]);
            //console.log(datos)
            GuardarNumeroEmpleado(datos);
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
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para agregar una nuevo usuario
 ***********************************************************************/
function AgregarNumeroEmpleado() {
    $('#form_numero_empleado').trigger("reset");
    $('#number_employee_id').val('');
    $('#modal_numero_empleado').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para editar un usuario, se carga y muestra el formulario
 ***********************************************************************/
function EditarNumeroEmpleado(record) {
    $('#form_numero_empleado').trigger("reset");
    $('#number_employee_id').val(record.id);
    $('#email').val(record.email);
    $('#number').val(record.number);
    $('#group_id').val(record.group_id).change();
    $('#job_id').val(record.job_id).change();

    $('#modal_numero_empleado').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar por peticion AJAX un usuario
 ***********************************************************************/
function GuardarNumeroEmpleado(data) {
    var number_employee_id = $('#number_employee_id').val(),
        metodo="User/SaveEmployee";

    if(number_employee_id != ''){
        data.append('number_employee_id',number_employee_id);
        metodo = "User/UpdateEmployee"
    }
    $.ajax({
        url:  window.base_url+metodo,
        type: "POST",
        data: data,
        cache: false,
        contentType:false,
        processData: false,
        success: function(response){
            tabla_numeros.ajax.reload();
            $('#modal_numero_empleado').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Numeros de empleados',
                text: 'El numero de empleado se ha guardado correctamente'
            });
        },
        error: function (response) {
            //$('#modal_numero_empleado').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Numeros de empleados',
                text: response.responseJSON.error_msg
            });
        }
    });
}

/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener la tabla de numeros de empleados.
 ***********************************************************************/
function ObtenerTablaNumeros() {
    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_numeros').on( 'click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_numeros.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        console.log(cmd);
        if(typeof record !== 'undefined') {
            if(es_boton){
                if(cmd=="eliminar"){
                    EliminarNumeroEmpleado(record)
                }
                if(cmd=="editar"){
                    EditarNumeroEmpleado(record)
                }
            }
        }
    } );
    $('#tabla_numeros').on('tbody click','tr',function(e){
        var record = tabla_numeros.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        console.log(cmd);
        if(typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "eliminar") {
                    EliminarNumeroEmpleado(record)
                }
                if (cmd == "editar") {
                    EditarNumeroEmpleado(record)
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_numeros = $('#tabla_numeros').DataTable({
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
            url: "../index.php/User/NumberEmployeeList",
            type: 'POST',
            data: function (d) {
            },
            error: function (xhr, error, code){
                tabla_numeros.clear().draw();
            }
        },
        buttons: [],
        "columns": [
            {
                data: "email",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "number",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "name_group",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "job_name",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "estatus",
                render: function ( data, type, row ) {
                    var t = '';
                    if(data == 1){
                        t = '<div class="pt-2 pb-2">Empleado registrado</div>';
                    }else{
                        t = '<div class="pt-2 pb-2">Empleado sin registrar</div>';
                    }
                    return t;
                }
            },
            {
                data: "id",
                width:"100px",
                render: function ( data, type, row ) {
                    var t = '';
                    if(row.status == 1){
                        t = '<div class="pt-2 pb-2">Sin acciones, invitado registrado.</div>';
                    }else{
                        t = '<div class="pt-2 pb-2 text-center">' +
                            '<a title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8 text-white" cmd="editar"><i  cmd="editar" class="fa fa-edit"></i></a>' +
                            '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8 text-white" cmd="eliminar"><i cmd="eliminar" class="fa fa-times"></i></a>'+
                            '</div>';
                    }
                    return t;
                }
            }
        ]
    });
}

/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para eliminar un numero de empleado
 ***********************************************************************/
function EliminarNumeroEmpleado(record) {
    Swal.fire({
        title: 'Numeros de empleados',
        text: "¿Estás seguro que deseas eliminar este numero de empleado?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url:  window.base_url+"User/DeleteNumbreEmployee",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function(response){
                    tabla_numeros.ajax.reload();
                    $('#modal_numero_empleados').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Numeros de empleados',
                        text: 'El invitado se ha eliminado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    $('#modal_numero_empleados').modal('hide');
                    Swal.fire({
                        type: 'error',
                        title: 'Numeros de empleados',
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
function GroupsRegistrados(){
    var datos = new FormData();
    var config = {
        url: window.base_url + "Groups/GroupsRegister",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            var grupo_id = $('#group_id');
            $.each(response.data,function (index,value){
                if(index === 0){
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
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para cargar los puestos disponibles.
 ***********************************************************************/
function PuestosRegistrados() {
    var datos = new FormData();
    var config = {
        url: window.base_url + "User/JobList",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            var job_id = $('#job_id');
            $.each(response.data,function (index,value){
                if(index === 0){
                    job_id.append($('<option>', {
                        value: '',
                        text: 'Seleccionar...'
                    }));
                }
                job_id.append($('<option>', {
                    value: value['id'],
                    text: value['job_name']
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
