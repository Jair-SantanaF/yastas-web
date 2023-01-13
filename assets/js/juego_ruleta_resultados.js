var tabla_resultados;

jQuery(document).ready(function ($) {
    ObtenerTablaResultados();
});


/*************CATEGORIAS**************/
/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Creacion de la tabla para listar resultados
 ***********************************************************************/
function ObtenerTablaResultados() {
    //Se crea la tabla con eyuda del plugin DataTable
    tabla_resultados = $('#tabla_resultados').DataTable({
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
            url: "../index.php/Games/RouletteResults",
            type: 'POST',
            data: function (d) {
                //d.token = "9C4F9Z4us1fcqBx8k5I5iwdaqjg8lknHgcvgrALYdsIMZiOEA9Yi9BxD0rlbAoZAQkPk9uzoz2N6ojAz1Wa04ielphCkkwvStit77jHIeuXp6PSZRIXITBLjGelCWXZj5FgcJP7CaJZrznGCcWlKrlbRq4ymMNFSivhCfQ364uH5ODE1SwwIWN8oUaGilv9DpyVQ7E5PhrTGyR2RNBFy14x7DEtyNiwzT3R8RJnymgJyeAlvgGgdzXyTynqOKqOxzfiHlSuI7Nllon7bCqk7URtkiTvJxCTi1u";
            },
            error: function (xhr, error, code){
                tabla_resultados.clear().draw();
            }
        },
        buttons: [],
        "columns": [
            {
                data: "quiz",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },{
                data: "name",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "question",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "answer",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "correct",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">' + (data == 1 ? "Sí" : "No") + '</div>';
                    return t;
                }
            },
            {
                data: "created_at",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            }
        ]
    });
}

