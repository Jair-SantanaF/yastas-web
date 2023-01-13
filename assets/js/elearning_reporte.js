var tabla_cursos_detalle = '';
jQuery(document).ready(function ($) {
    ObtenerElearning();
    $('#select_elearning').on('change', function() {
        TableReportElearning(this.value);
    });
});
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener el listado de elearning
 ***********************************************************************/
function ObtenerElearning() {
    var datos = new FormData();
    var config = {
        url: window.base_url + "elearning/elearningModules",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            $.each(response.data, function (index,value){
                $('#select_elearning').append($('<option>', {
                    value: value['id'],
                    text : value['title']
                }));
            });
        },
        error: function (response) {
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener el reporte de cursos
 ***********************************************************************/
function TableReportElearning(elearning_id) {
    if(tabla_cursos_detalle !== ''){
        $('#tabla_cursos_detalle').DataTable().destroy();
    }
    //Se crea la tabla con eyuda del plugin DataTable
    tabla_cursos_detalle = $('#tabla_cursos_detalle').DataTable({
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
            url: "../index.php/elearning/ElearningDetailUsers",
            type: 'POST',
            data: function (d) {
                d.elearning_id = elearning_id;
                //d.token = "9C4F9Z4us1fcqBx8k5I5iwdaqjg8lknHgcvgrALYdsIMZiOEA9Yi9BxD0rlbAoZAQkPk9uzoz2N6ojAz1Wa04ielphCkkwvStit77jHIeuXp6PSZRIXITBLjGelCWXZj5FgcJP7CaJZrznGCcWlKrlbRq4ymMNFSivhCfQ364uH5ODE1SwwIWN8oUaGilv9DpyVQ7E5PhrTGyR2RNBFy14x7DEtyNiwzT3R8RJnymgJyeAlvgGgdzXyTynqOKqOxzfiHlSuI7Nllon7bCqk7URtkiTvJxCTi1u";
            },
            error: function (xhr, error, code){
                tabla_cursos_detalle.clear().draw();
            }
        },
        dom: 'Bfrtip',
        buttons: [
            'excel'
        ],
        "columns": [
            {
                data: "name",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "last_name",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "max_score",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "date_entry",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "date_exit",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "diff",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "tried_final",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "max_try",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "status",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            }
        ]
    });
}
