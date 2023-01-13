var tabla_servicios_contratados;
jQuery(document).ready(function ($) {
    ObtenerTablaServicios();
});
/***********************************************************************
 *    Autor: Uriel Sánchez Cervantes   Fecha: 11/12/2020
 *           urisancer@gmail.com
 *    Nota:
 ***********************************************************************/
function ObtenerTablaServicios() {
    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_servicios_contratados').on( 'click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_servicios_contratados.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "eliminar") {
                    EliminarServicio(record)
                }
                if (cmd == "aprobar") {
                    AprobarServicio(record)
                }
            }
        }
    } );
    $('#tabla_servicios_contratados').on('tbody click','tr',function(e){
        var record = tabla_servicios_contratados.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined'){
            if(es_boton){
                if (cmd == "eliminar") {
                    EliminarServicio(record)
                }
                if(cmd=="aprobar"){
                    AprobarServicio(record)
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_servicios_contratados = $('#tabla_servicios_contratados').DataTable({
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
            url: "../index.php/Services/PurchaseServicesList",
            type: 'POST',
            data: function (d) {
                //d.token = "9C4F9Z4us1fcqBx8k5I5iwdaqjg8lknHgcvgrALYdsIMZiOEA9Yi9BxD0rlbAoZAQkPk9uzoz2N6ojAz1Wa04ielphCkkwvStit77jHIeuXp6PSZRIXITBLjGelCWXZj5FgcJP7CaJZrznGCcWlKrlbRq4ymMNFSivhCfQ364uH5ODE1SwwIWN8oUaGilv9DpyVQ7E5PhrTGyR2RNBFy14x7DEtyNiwzT3R8RJnymgJyeAlvgGgdzXyTynqOKqOxzfiHlSuI7Nllon7bCqk7URtkiTvJxCTi1u";
            },
            error: function (xhr, error, code){
                tabla_servicios_contratados.clear().draw();
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
            },{
                data: "service_name",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },{
                data: "category_name",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },{
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
                    var t = '';
                    
                    t = '' +
                        '<div class="pt-2 pb-2 text-center">' +
                        '<button title="Aprobar" class="btn btn-info btn-xs aprobar mr-2 lead_0_8 text-white" cmd="aprobar"><i cmd="aprobar" class="fa fa-check"></i></button>'+
                        '<button title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8 text-white" cmd="eliminar"><i cmd="eliminar" class="fa fa-times"></i></button>'+
                        '</div>';
                    
                    return t;
                }
            }
        ]
    });
}

/***********************************************************************
 *    Autor: Uriel Sánchez Cervantes   Fecha: 11/12/2020
 *           urisancer@gmail.com
 *    Nota: Aprobar servicio solicitado
 ***********************************************************************/
function AprobarServicio(record) {
    Swal.fire({
        title: 'Servicios',
        text: "¿Estás seguro que deseas aprobar este servicio?, una vez aprobado la empresa podrá hacer uso del mismo.",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value !== ''){
            $.ajax({
                url:  window.base_url+"Services/ApprovePurchaseService",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function(response){
                    tabla_servicios_contratados.ajax.reload();
                    Swal.fire({
                        type: 'success',
                        title: 'Servicios',
                        text: 'El servicio ha sido aprobado ahora se podrá usar en la app'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    Swal.fire({
                        type: 'error',
                        title: 'Servicios',
                        text: error_msg
                    });
                }
            });
        }
    })
}

/***********************************************************************
 *    Autor: Uriel Sánchez Cervantes   Fecha: 11/12/2020
 *           urisancer@gmail.com
 *    Nota: Eliminar registro de servicio solicitado
 ***********************************************************************/
function EliminarServicio(record) {
    Swal.fire({
        title: 'Servicios',
        text: "¿Estás seguro que deseas eliminar esta solicitud?, una vez eliminada, la empresa tendrá que volver a solicitarlo.",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value !== ''){
            $.ajax({
                url:  window.base_url+"Services/DeletePurchaseService",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function(response){
                    tabla_servicios_contratados.ajax.reload();
                    Swal.fire({
                        type: 'success',
                        title: 'Servicios',
                        text: 'El servicio solicitado ha sido eliminaro'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    Swal.fire({
                        type: 'error',
                        title: 'Servicios',
                        text: error_msg
                    });
                }
            });
        }
    })
}