var idiomaDataTable = {
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
};

var becarios = [];
var becariosArray = [];

jQuery(document).ready(function ($) {
    obtenerRegiones();
    obtenerIncidencias();
});

function generarFecha(date){
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const union = [day, month, year].join('/');
    
    return union;
}


// funcion para obtener las incidencias
function obtenerIncidencias() {
    //Este sirve para destruir el datatable
    $('#tabla_incidencias').dataTable().fnDestroy();
    $('#tabla_incidencias').DataTable({
        ajax: {
            url: window.base_url + "Incidencias/getIncidencias",
            type: 'POST',
            data: {
            },
            dataType: 'json',
            dataSrc: 'data',
        },
        idSrc: "incidencia_id",
        language: idiomaDataTable,
        columns: [
            {data: "becario_nombre"},
            {data: "becario_numero_empleado"},
            {data: "asistencias"},
            {data: "retardos"},
            {data: "faltas"},
            {data: "enfermedad_con_justificante"},
            {data: "enfermedad_sin_justificante"},
        ],
        order: [[0, 'asc']]
    });
}


// funcion para obtener las regiones
function obtenerRegiones() {
    $.ajax({
        url: window.base_url + "Challenges/getRegiones",
        type: "POST",
        data: {},
        cache: false,
        success: function (response) {
            $('#region_id').empty();
            $('#region_id').append('<option hidden selected value="">-- Seleccionar --</option>');
            $('#region_id').append('<option value="null">Todos</option>');

            $.each(response.data, function(index, value){
                $('#region_id').append("<option value='" + value.id +"'>"+ value.nombre +"</option>");                                
            }); 
        },
        error: function (error) {
            console.log(error)
        }
    });
}

// funcion para descargar un archivo csv con los datos
function descargarCsv(){
    var url = window.base_url + "Incidencias/descargarCsv/"
    window.open(url);
}

// funcion para obtener las incidencias por filtro
function obtenerIncidenciasFiltro(region_id, fecha_inicio, fecha_fin) {
    //Este sirve para destruir el datatable
    $('#tabla_incidencias').dataTable().fnDestroy();
    $('#tabla_incidencias').DataTable({
        ajax: {
            url: window.base_url + "Incidencias/getIncidencias",
            type: 'POST',
            data: {
                region_id: region_id,
                fecha_inicio: fecha_inicio,
                fecha_fin: fecha_fin
            },
            dataType: 'json',
            dataSrc: 'data',
        },
        idSrc: "incidencia_id",
        language: idiomaDataTable,
        columns: [
            {data: "becario_nombre"},
            {data: "becario_numero_empleado"},
            {data: "asistencias"},
            {data: "retardos"},
            {data: "faltas"},
            {data: "enfermedad_con_justificante"},
            {data: "enfermedad_sin_justificante"},
        ],
        order: [[0, 'asc']]
    });
}

// funcion para obtener las incidencias por filtro
function filtrarResultados(){
    let region_id = $("#region_id").val();
    let fecha_inicio = $("#start-date").val();
    let fecha_fin = $("#end-date").val();

    obtenerIncidenciasFiltro(region_id, fecha_inicio, fecha_fin);
}

// funcion para limpiar resultados
function limpiarResultados(){
    $("#region_id").val('');
    $("#start-date").val('');
    $("#end-date").val('');

    obtenerIncidencias();
}

