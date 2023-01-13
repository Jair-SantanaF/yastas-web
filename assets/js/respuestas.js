var tabla_resultados = '';
jQuery(document).ready(function ($) {
    $('#cuestionarios_').change(function (){
    	if(tabla_resultados === ''){
			ObtenerCatalogosPreguntas();
		}else{
			tabla_resultados.api().ajax.reload();
		}
	});
});
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 13/05/2020
 *	Nota: Funcion para obtener los catalogos preguntas
 ***********************************************************************/
function ObtenerCatalogosPreguntas() {
    tabla_resultados = $('#catalogo_preguntas').dataTable({
        pageLength: 10,
        dom: 'Bfrtip',
        buttons: ['excel'],
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
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
        "order": [[3, "asc"]],
        "columns": [
            { "data": "name_user" },
            { "data": "question" },
			{ "data": "answer" },
			{ "data": "date" },
			{
				"data": "correct",
				render: function (data, type, full, meta) {
					if(data == 1){
						return 'Correcta';
					}else{
						return 'Incorrecta';
					}
				}
			}
        ],
        "ajax": {
            "url": window.base_url+"questions/ListAnswerUsers",
			type: 'POST',
			data: function (d) {
				d.quiz_id = $('#cuestionarios_').val();
			},
            error: function (xhr, error, code)
            {
                tabla_resultados.api().clear().draw();
            }
        }
    });
}
