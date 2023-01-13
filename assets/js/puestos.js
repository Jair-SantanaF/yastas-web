var tabla_puestos;

$(document).ready(function ($){

	ObtenerTablaPuestos();

	$('#form_job').validate({
        rules: {
            job_name: {
                required: true
            }
        },
        submitHandler: function () {
            //console.log(datos)
            GuardarPuesto();
        },
        errorElement: "em",
        errorPlacement: function (error, element) {
            // Add the `help-block` class to the error element
            error.addClass("help-block rojo_error");
            if (element.prop("type") === "text") {
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

/**
 * Obtener tabla de puestos
 */
function ObtenerTablaPuestos(){

	//Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_puestos').on( 'click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_puestos.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if (es_boton) {
                if(cmd=="editar"){
                    EditarPuesto(record);
                }
                if(cmd=="eliminar"){
                    EliminarPuesto(record)
                }
            }
        }
    } );
    $('#tabla_puestos').on('tbody click','tr',function(e){
        var record = tabla_puestos.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined'){
            if(es_boton){
                if(cmd=="editar"){
                    EditarPuesto(record);
                }
                if(cmd=="eliminar"){
                    EliminarPuesto(record)
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_puestos = $('#tabla_puestos').DataTable({
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
        "order": [[0, "desc"]],
        "ajax": {
            url: "../index.php/User/JobList",
            type: 'POST',
            data: function (d) {
                //d.token = "9C4F9Z4us1fcqBx8k5I5iwdaqjg8lknHgcvgrALYdsIMZiOEA9Yi9BxD0rlbAoZAQkPk9uzoz2N6ojAz1Wa04ielphCkkwvStit77jHIeuXp6PSZRIXITBLjGelCWXZj5FgcJP7CaJZrznGCcWlKrlbRq4ymMNFSivhCfQ364uH5ODE1SwwIWN8oUaGilv9DpyVQ7E5PhrTGyR2RNBFy14x7DEtyNiwzT3R8RJnymgJyeAlvgGgdzXyTynqOKqOxzfiHlSuI7Nllon7bCqk7URtkiTvJxCTi1u";
            },
            error: function (xhr, error, code){
                tabla_puestos.clear().draw();
            }
        },
        buttons: [],
        "columns": [
            /*{
                data: "id",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },*/
            {
                data: "job_name",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "id",
                render: function ( data, type, row, meta ) {
                    var t = '',
                        button_no_invitation = '';
                    if(row.register_no_invitation == 1){
                        button_no_invitation = '<button title="Aprobar acceso" class="btn btn-info btn-xs aprobar mr-2 lead_0_8 text-white" cmd="aprobar"><i cmd="aprobar" class="fa fa-check"></i></button>';
                    }
                    t = '' +
                        '<div class="pt-2 pb-2 text-center">' +
                        button_no_invitation +
                        '<button title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8 text-white" cmd="editar"><i cmd="editar" class="fa fa-edit"></i></button>' +
                        '<button title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8 text-white" cmd="eliminar"><i cmd="eliminar" class="fa fa-times"></i></button>'+
                        '</div>';
                    return t;
                }
            }
        ]
    });

};

function AgregarPuesto() {
    $('#form_job').trigger("reset");
    $('#job_id').val('');
    $('#job_name').val('');
    $('#modal_puesto').modal('show');
}

function EditarPuesto(record) {
    $('#form_job').trigger("reset");
    $('#job_id').val(record.id);
    $('#job_name').val(record.job_name);
    $('#modal_puesto').modal('show');
}

function GuardarPuesto() {
    var job_id = $('#job_id').val(),
        datos = {
            job_name : $('#job_name').val()
        },
        metodo="SaveJobs";

    if(job_id != ''){
        datos.id = job_id;
        metodo = "EditJobs"
    }

    $.ajax({
        url:  window.base_url+"User/"+metodo,
        type: "POST",
        data: datos,
        cache: false,
        success: function(response){
            tabla_puestos.ajax.reload();
            $('#modal_puesto').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Catálogo puestos',
                text: 'El catálogo de puestos ha sido actualizado correctamente'
            });
        },
        error: function () {
            $('#modal_puesto').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Catálogo puestos',
                text: 'El puesto no se pudo guardar'
            });
        }
    });
}

function EliminarPuesto(record) {
    Swal.fire({
        title: 'Catálogo puestos',
        text: "¿Estás seguro que deseas eliminar este puesto?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url:  window.base_url+"User/DeleteJobs",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function(response){
                    tabla_puestos.ajax.reload();
                    $('#modal_puesto').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Catálogo puestos',
                        text: 'El catálogo de puestos ha sido actualizado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    $('#modal_puesto').modal('hide');
                    Swal.fire({
                        type: 'error',
                        title: 'Catálogo puestos',
                        text: error_msg
                    });
                }
            });
        }
    })
}