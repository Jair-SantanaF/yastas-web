var tabla_companias,
    tabla_servicios;
jQuery(document).ready(function ($) {
    ObtenerTablaCompanias();
    ObtenerTablaServicios();
    ObtenerPlanes();
    $('#form_compania').validate({
        rules: {
            business_name: {
                required: true
            }
        },
        submitHandler: function (form) {
            //console.log(datos)
            GuardarCompania();
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
 *	Fecha: 07 de Enero de 2021
 *	Nota: Creacion de la tabla para listar empresas
 ***********************************************************************/
function ObtenerTablaCompanias() {
    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_companias').on( 'click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_companias.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarCompania(record);
                }
                if (cmd == "eliminar") {
                    EliminarCompania(record)
                }
                if (cmd == "aprobar") {
                    AprobarCompania(record)
                }
            }
        }
    } );
    $('#tabla_companias').on('tbody click','tr',function(e){
        var record = tabla_companias.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined'){
            if(es_boton){
                if(cmd=="editar"){
                    EditarCompania(record);
                }
                if(cmd=="eliminar"){
                    EliminarCompania(record)
                }
                if(cmd=="ver_servicios"){
                    VerServicios(record)
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_companias = $('#tabla_companias').DataTable({
        responsive: true,
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
            url: "../index.php/Business/BusinessList",
            type: 'POST',
            data: function (d) {
                //d.token = "9C4F9Z4us1fcqBx8k5I5iwdaqjg8lknHgcvgrALYdsIMZiOEA9Yi9BxD0rlbAoZAQkPk9uzoz2N6ojAz1Wa04ielphCkkwvStit77jHIeuXp6PSZRIXITBLjGelCWXZj5FgcJP7CaJZrznGCcWlKrlbRq4ymMNFSivhCfQ364uH5ODE1SwwIWN8oUaGilv9DpyVQ7E5PhrTGyR2RNBFy14x7DEtyNiwzT3R8RJnymgJyeAlvgGgdzXyTynqOKqOxzfiHlSuI7Nllon7bCqk7URtkiTvJxCTi1u";
            },
            error: function (xhr, error, code){
                tabla_companias.clear().draw();
            }
        },
        buttons: [],
        "columns": [
            {
                data: "business_name",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "plan_name",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "users",
                render: function ( data, type, row ) {
                    var plan_id = parseInt(row.plan_id),
                    max_users = row.num_users;
                    if(plan_id == 5){
                        max_users = "∞"
                    }
                    var t = ''+
                        '<div class="d-flex">'+
                        '   <div class="d-flex">'+
                        '       <i class="fa fa-user align-self-center pr-2"></i>'+
                        '   </div>' +
                        '   <div class="w-100">'+
                        '       '+data+' / '+ max_users +
                        '   </div>' +
                        '</div>';
                    return t;
                }
            },
            {
                data: "used_space",
                render: function ( data, type, row ) {
                    var porcentaje=0, max = row.formatted_plan_space;

                    porcentaje =  row.percentage_used_space.toFixed(2);
                    if(row.plan_id == 5){
                        max = "∞";
                    }

                    var t = ''+
                        '<div class="">'+
                        '   <div class="w-100">'+
                        '       <div class="progress">' +
                        '          <div class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="'+porcentaje+'" aria-valuemin="0" aria-valuemax="100">'+porcentaje+'%</div>' +
                        '      </div>' +
                        '   </div>' +
                        '   <div class="w-100" style="font-size: .75rem;">'+
                                row.formatted_used_space + ' / ' +max+
                        '   </div>' +
                        '</div>';

                    return t;
                }
            },
            {
                data: "id",
                render: function ( data, type, row, meta ) {
                    t = '' +
                        '<div class="pt-2 pb-2 text-center">' +
                        '<button title="Ver servicios contratados" class="btn btn-info btn-xs aprobar mr-2 lead_0_8 text-white" cmd="ver_servicios"><i cmd="ver_servicios" class="fa fa-list"></i></button>'+
                        '<button title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8 text-white" cmd="editar"><i cmd="editar" class="fa fa-edit"></i></button>' +
                        '<button title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8 text-white" cmd="eliminar"><i cmd="eliminar" class="fa fa-times"></i></button>'+
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
 *	Fecha: 07 de Enero de 2021
 *	Nota: funcion para agregar una nueva empresa
 ***********************************************************************/
function AgregarCompania() {
    $('#form_compania').trigger("reset");
    $('#business_id').val('');
    $('#business_name').val('');
    $('#plan_id').val(1);
    $('#modal_compania').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 07 de Enero de 2021
 *	Nota: funcion para editar una empresa, se carga y muestra el formulario
 ***********************************************************************/
function EditarCompania(record) {
    $('#form_compania').trigger("reset");
    $('#business_id').val(record.id);
    $('#business_name').val(record.business_name);
    $('#plan_id').val(record.plan_id);
    $('#modal_compania').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 07 de Enero de 2021
 *	Nota: función para guardar por peticion AJAX una empresa
 ***********************************************************************/
function GuardarCompania() {
    var business_id = $('#business_id').val(),
        datos = {
            business_name : $('#business_name').val(),
            plan_id: $('#plan_id').val()
        },
        metodo="SaveBusiness";

    if(business_id != ''){
        datos.id = business_id;
        metodo = "EditBusiness"
    }

    $.ajax({
        url:  window.base_url+"Business/"+metodo,
        type: "POST",
        data: datos,
        cache: false,
        success: function(response){
            tabla_companias.ajax.reload();
            $('#modal_compania').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Catálogo compañias',
                text: 'La empresa ha sido guardada correctamente'
            });
        },
        error: function () {
            $('#modal_compania').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Catálogo compañias',
                text: 'La empresa no se pudo guardar'
            });
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 07 de Enero de 2021
 *	Nota: funcion para eliminar una empresa
 ***********************************************************************/
function EliminarCompania(record) {
    Swal.fire({
        title: 'Eliminar empresa',
        text: "¿Estás seguro que deseas eliminar esta empresa?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url:  window.base_url+"Business/DeleteBusiness",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function(response){
                    tabla_companias.ajax.reload();
                    $('#modal_compania').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Eliminar empresa',
                        text: 'La empresa ha sido eliminada correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    $('#modal_compania').modal('hide');
                    Swal.fire({
                        type: 'error',
                        title: 'Eliminar empresa',
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
 *	Fecha: 07 de Enero de 2021
 *	Nota: Funcion para cargar el listado planes disponibles
 ***********************************************************************/
function ObtenerPlanes(){
    $.ajax({
        url:  window.base_url+"Business/PlansList",
        type: 'POST',
        contentType: false,
        //data: datos,
        processData: false,
        cache: false,
        success: function(json) {
            var html = '<option value="">Seleccionar</option>';
            for(var key in json.data){
                html += '<option value="'+json.data[key].id+'">'+json.data[key].name+' ['+json.data[key].num_users+' usuarios/'+json.data[key].formtted_space+']</option>';
            }
            $('#plan_id').html(html).fadeIn();
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 07 de Enero de 2021
 *	Nota: funcion para mostrar, en un modal, los servicios contratados por una empresa
 ***********************************************************************/
function VerServicios(record) {
    $('#services_business_id').val(record.id);
    tabla_servicios.ajax.reload();
    $('#modal_servicios').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 07 de Enero de 2021
 *	Nota: Creacion de la tabla para listar servicios contratados por la empresa seleccionada
 ***********************************************************************/
function ObtenerTablaServicios() {
    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_servicios').on( 'click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_servicios.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if (es_boton) {
                if(cmd=="eliminar"){
                    EliminarServicio(record);
                }
            }
        }
    } );
    $('#tabla_servicios').on('tbody click','tr',function(e){
        var record = tabla_servicios.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined'){
            if(es_boton){
                if(cmd=="eliminar"){
                    EliminarServicio(record);
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_servicios = $('#tabla_servicios').DataTable({
        responsive: true,
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
            url: "../index.php/Services/HiredServices",
            type: 'POST',
            data: function (d) {
                d.business_id = $('#services_business_id').val();
            },
            error: function (xhr, error, code){
                tabla_servicios.clear().draw();
            }
        },
        buttons: [],
        "columns": [
            {
                data: "service_name",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "category_name",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "description",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "id",
                render: function ( data, type, row, meta ) {
                    t = '' +
                        '<div class="pt-2 pb-2 text-center">' +
                        '<button title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8 text-white" cmd="eliminar"><i cmd="eliminar" class="fa fa-times"></i></button>'+
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
 *	Fecha: 07 de Enero de 2021
 *	Nota: funcion para eliminar un servicio contratado por una empresa
 ***********************************************************************/
function EliminarServicio(record) {
    Swal.fire({
        title: 'Eliminar servicio',
        text: "¿Estás seguro que deseas eliminar el servicio contratado por la empresa?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url:  window.base_url+"Services/DeleteHiredService",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function(response){
                    tabla_servicios.ajax.reload();
                    $('#modal_compania').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Eliminar servicio',
                        text: 'El servicio ha sido eliminado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    $('#modal_compania').modal('hide');
                    Swal.fire({
                        type: 'error',
                        title: 'Eliminar servicio',
                        text: error_msg
                    });
                }
            });
        }
    })
}