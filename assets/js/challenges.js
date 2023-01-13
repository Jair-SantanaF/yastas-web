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
    obtenerRetos();
    obtenerRetosRealizados();
});

function generarFecha(date){
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const union = [day, month, year].join('/');
    
    return union;
}


// funcion para obtener los retos cargados
function obtenerRetos() {
    //Este sirve para destruir el datatable
    $('#tabla_retos_cargados').dataTable().fnDestroy();
    $('#tabla_retos_cargados').DataTable({
        ajax: {
            url: window.base_url + "Challenges/getRetosCargados",
            type: 'POST',
            data: {
            },
            dataType: 'json',
            dataSrc: 'data',
        },
        idSrc: "reto_id",
        language: idiomaDataTable,
        columns: [
            {data: "reto_nombre"},
            {data: "tutor_nombre"},
            {data: "becario_nombre"},
            {data: "reto_estatus_nombre"},
            {
                data: "created_at",
                render: function (data, type, row) {
                    let fecha_carga = new Date(Date.parse(data));
                    return generarFecha(fecha_carga);
                }
            },
            {
                width: '100px',
                data: "reto_id",
                render: function (data, type, row) {
                    return `
                        <div class="pt-2 pb-2 text-center">
                        <button class="btn btn-primary btn-xs mr-2 lead_0_8 text-white" onclick="editarReto(${data})"><i class="fa fa-edit"></i></button>
                        <a class="btn btn-danger btn-xs lead_0_8 text-white" onclick="eliminarReto(${data})"><i class="fa fa-times"></i></a>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'asc']]
    });
}


// funcion para obtener los retos realizados
function obtenerRetosRealizados() {
    //Este sirve para destruir el datatable
    $('#tabla_retos_realizados').dataTable().fnDestroy();
    $('#tabla_retos_realizados').DataTable({
        ajax: {
            url: window.base_url + "Challenges/getRetosRealizados",
            type: 'POST',
            data: {
            },
            dataType: 'json',
            dataSrc: 'data',
        },
        idSrc: "reto_id",
        language: idiomaDataTable,
        columns: [
            {data: "reto_nombre"},
            {data: "tutor_nombre"},
            {data: "becario_nombre"},
            {data: "reto_estatus_nombre"},
            {
                data: "created_at",
                render: function (data, type, row) {
                    let fecha_carga = new Date(Date.parse(data))
                    return generarFecha(fecha_carga);
                }
            },
            {
                width: '100px',
                data: "reto_id",
                render: function (data, type, row) {
                    return `
                        <div class="pt-2 pb-2 text-center">
                            <a class="btn btn-danger btn-xs lead_0_8 text-white" onclick="eliminarReto(${data})"><i class="fa fa-times"></i></a>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'asc']]
    });
}


// funcion para obtener las regiones
function obtenerRegiones() {
    $('#tutor_id').prop('disabled', true);

    $.ajax({
        url: window.base_url + "Challenges/getRegiones",
        type: "POST",
        data: {},
        cache: false,
        success: function (response) {
            $('#region_id').empty();
            $('#region_id').append('<option hidden selected value="">-- Seleccionar --</option>');

            $.each(response.data, function(index, value){
                $('#region_id').append("<option value='" + value.id +"'>"+ value.nombre +"</option>");                                
            }); 

            // regiones para el modal de agregar a todos
            $('#region_id_todos').empty();
            $('#region_id_todos').append('<option hidden selected value="">-- Seleccionar --</option>');
            $('#region_id_todos').append('<option value="todos">Todos</option>');

            $.each(response.data, function(index, value){
                $('#region_id_todos').append("<option value='" + value.id +"'>"+ value.nombre +"</option>");                                
            }); 
        },
        error: function (error) {
            console.log(error)
        }
    });
}


// funcion para obtener los tutores
function loadDropdownTutores(region_id){
    $.ajax({
        url: window.base_url + "Challenges/getTutores",
        type: "POST",
        data: {
            region_id: region_id
        },
        cache: false,
        success: function (response) {
            $('#tutor_id').empty();
            $('#tutor_id').append('<option hidden selected value="">-- Seleccionar --</option>');

            $.each(response.data, function(index, value){
                $('#tutor_id').append("<option value='" + value.id +"'>"+ value.nombre +"</option>");                                
            }); 

            $('#tutor_id').prop('disabled', false);
        },
        error: function (error) {
            console.log(error)
        }
    });
}

// funcion para obtener los alumnos
function loadAlumnos(tutor_id){
    $.ajax({
        url: window.base_url + "Challenges/getBecarios",
        type: "POST",
        data: {
            tutor_id: tutor_id
        },
        cache: false,
        success: function (response) {
            becarios = response.data;
            $("#contenedor_alumnos").empty();
            var container = $('#contenedor_alumnos');
            $(container).append('<div class="col-md-12 mb-3">Seleccionar usuarios:</div>');
            $.each(response.data, function(index, valor){
                let html = `
                    <div class="col-md-6">
                        <div class="form-check form-check-inline">
                            <input class="form-check" type="checkbox" id="cb_${index}" value="${valor.becario_id}">
                            <label class="form-check-label" for="cb_${index}">${valor.becario_nombre}</label>
                        </div>
                    </div>
                `;

                $(container).append(html);
            }); 
        },
        error: function (error) {
            console.log(error)
        }
    });
}


// funcion para cargar los datos y abrir el modal de agregar reto
function agregarReto(){
    $("#nombre_reto").val('');
    $("#mes_curso").val('');
    $("#descripcion_reto").val('');
    $("#region_id").val('');
    $("#tutor_id").val('');
    $("#contenedor_alumnos").empty();

    obtenerRegiones();
    $('#modal_agregar_reto').modal('show');
}


// funcion para cargar los datos y abrir el modal de agregar reto a todos
function agregarRetoTodos(){
    $("#nombre_reto_todos").val('');
    $("#mes_curso_todos").val('');
    $("#descripcion_reto_todos").val('');
    $("#region_id_todos").val('');

    obtenerRegiones();
    $('#modal_agregar_reto_todos').modal('show');
}


// funcion para guardar los datos del reto
function guardarReto(){
    becariosArray = [];
    for(let i = 0; i < becarios.length; i++) {
        if (get("cb_" + i).checked) {
            becariosArray.push(becarios[i].becario_id)
        }
    }

    $.ajax({
        url: window.base_url + "Challenges/crearReto",
        type: "POST",
        data: {
            tutor_id: $("#tutor_id").val(),
            reto: $("#nombre_reto").val(),
            detalles: $("#descripcion_reto").val(),
            mes: $("#mes_curso").val(),
            becarios: becariosArray,
        },
        cache: false,
        success: function (response) {
            if(response.status_code == 200){
                Swal.fire({
                    title: "Éxito",
                    text: response.msg,
                    type: 'success'
                });

                $('#tabla_retos_cargados').DataTable().ajax.reload();
                $('#tabla_retos_realizados').DataTable().ajax.reload();
                $('#modal_agregar_reto').modal('hide');
            }
        },
        error: function (error) {
            console.log('error', error);

            if(error.responseJSON.status_code == 422){
                Swal.fire({
                    title: "Error",
                    text: error.responseJSON.msg,
                    type: 'error'
                });
            }
        }
    });
}


// funcion para guardar los datos del reto que es para todos
function guardarRetoTodos(){
    $.ajax({
        url: window.base_url + "Challenges/crearRetoTodos",
        type: "POST",
        data: {
            reto: $("#nombre_reto_todos").val(),
            detalles: $("#descripcion_reto_todos").val(),
            mes: $("#mes_curso_todos").val(),
            region: $("#region_id_todos").val(),
        },
        cache: false,
        success: function (response) {
            if(response.status_code == 200){
                Swal.fire({
                    title: "Éxito",
                    text: response.msg,
                    type: 'success'
                });

                $('#tabla_retos_cargados').DataTable().ajax.reload();
                $('#tabla_retos_realizados').DataTable().ajax.reload();
                $('#modal_agregar_reto_todos').modal('hide');
            }
        },
        error: function (error) {
            console.log('error', error);

            if(error.responseJSON.status_code == 422){
                Swal.fire({
                    title: "Error",
                    text: error.responseJSON.msg,
                    type: 'error'
                });
            }
        }
    });
}


// funcion para abrir el modal para editar el reto
function editarReto(id){
    $("#reto_id_editar").val('');
    $("#nombre_reto_editar").val('');
    $("#descripcion_reto_editar").val('');

    $.ajax({
        url: window.base_url + "Challenges/getReto",
        type: "POST",
        data: {
            reto_id: id
        },
        cache: false,
        success: function (response) {
            if(response.status_code == 200){
                $("#reto_id_editar").val(id);
                $("#nombre_reto_editar").val(response.data.reto_nombre);
                $("#descripcion_reto_editar").val(response.data.reto_detalles);

                $("#modal_editar_reto").modal('show');
            }
        },
        error: function (error) {
            console.log('error', error);

            if(error.responseJSON.status_code == 422){
                Swal.fire({
                    title: "Error",
                    text: error.responseJSON.msg,
                    type: 'error'
                });
            }
        }
    });
}


// funcion para guardar los datos actualizados del reto
function actualizarReto(){
    $.ajax({
        url: window.base_url + "Challenges/actualizarReto",
        type: "POST",
        data: {
            reto_id: $("#reto_id_editar").val(),
            reto: $("#nombre_reto_editar").val(),
            detalles: $("#descripcion_reto_editar").val(),
        },
        cache: false,
        success: function (response) {
            if(response.status_code == 200){
                Swal.fire({
                    title: "Éxito",
                    text: response.msg,
                    type: 'success'
                });

                $('#tabla_retos_cargados').DataTable().ajax.reload();
                $('#tabla_retos_realizados').DataTable().ajax.reload();
                $('#modal_editar_reto').modal('hide');
            }
        },
        error: function (error) {
            console.log('error', error);

            if(error.responseJSON.status_code == 422){
                Swal.fire({
                    title: "Error",
                    text: error.responseJSON.msg,
                    type: 'error'
                });
            }
        }
    });
}

// funcion para eliminar un reto
function eliminarReto(id){
    $.ajax({
        url: window.base_url + "Challenges/eliminarReto",
        type: "POST",
        data: {
            reto_id: id
        },
        cache: false,
        success: function (response) {
            if(response.status_code == 200){
                Swal.fire({
                    title: "Éxito",
                    text: response.msg,
                    type: 'success'
                });

                $('#tabla_retos_cargados').DataTable().ajax.reload();
                $('#tabla_retos_realizados').DataTable().ajax.reload();
            }
        },
        error: function (error) {
            console.log('error', error);

            if(error.responseJSON.status_code == 422){
                Swal.fire({
                    title: "Error",
                    text: error.responseJSON.msg,
                    type: 'error'
                });
            }
        }
    });
}

// funcion para obtener los retos cargados por filtro
function obtenerRetosCargadosFiltro(filtro){
    //Este sirve para destruir el datatable
    $('#tabla_retos_cargados').dataTable().fnDestroy();
    $('#tabla_retos_cargados').DataTable({
        ajax: {
            url: window.base_url + "Challenges/getRetosCargadosFiltro",
            type: 'POST',
            data: {
                filtro: filtro
            },
            dataType: 'json',
            dataSrc: 'data',
        },
        idSrc: "reto_id",
        language: idiomaDataTable,
        columns: [
            {data: "reto_nombre"},
            {data: "tutor_nombre"},
            {data: "becario_nombre"},
            {data: "reto_estatus_nombre"},
            {
                data: "created_at",
                render: function (data, type, row) {
                    let fecha_carga = new Date(Date.parse(data));
                    return generarFecha(fecha_carga);
                }
            },
            {
                width: '100px',
                data: "reto_id",
                render: function (data, type, row) {
                    return `
                        <div class="pt-2 pb-2 text-center">
                        <button class="btn btn-primary btn-xs mr-2 lead_0_8 text-white" onclick="editarReto(${data})"><i class="fa fa-edit"></i></button>
                        <a class="btn btn-danger btn-xs lead_0_8 text-white" onclick="eliminarReto(${data})"><i class="fa fa-times"></i></a>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'asc']]
    });
}


// funcion para obtener los retos realizados por filtro
function obtenerRetosRealizadosFiltro(filtro){
    //Este sirve para destruir el datatable
    $('#tabla_retos_realizados').dataTable().fnDestroy();
    $('#tabla_retos_realizados').DataTable({
        ajax: {
            url: window.base_url + "Challenges/getRetosRealizadosFiltro",
            type: 'POST',
            data: {
                filtro: filtro
            },
            dataType: 'json',
            dataSrc: 'data',
        },
        idSrc: "reto_id",
        language: idiomaDataTable,
        columns: [
            {data: "reto_nombre"},
            {data: "tutor_nombre"},
            {data: "becario_nombre"},
            {data: "reto_estatus_nombre"},
            {
                data: "created_at",
                render: function (data, type, row) {
                    let fecha_carga = new Date(Date.parse(data))
                    return generarFecha(fecha_carga);
                }
            },
            {
                width: '100px',
                data: "reto_id",
                render: function (data, type, row) {
                    return `
                        <div class="pt-2 pb-2 text-center">
                            <a class="btn btn-danger btn-xs lead_0_8 text-white" onclick="eliminarReto(${data})"><i class="fa fa-times"></i></a>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'asc']]
    });
}


// funcion para descargar un archivo csv con los datos
function descargarCsv(){
    var url = window.base_url + "Challenges/descargarCsv/"
    window.open(url);
}


// cuando ocurre un cambio en el select de regiones
$("#region_id").change(function () {
    let region_id = $('#region_id').val();

    loadDropdownTutores(region_id);
});

// cuando ocurre un cambio en el select de tutores
$("#tutor_id").change(function () {
    let tutor_id = $('#tutor_id').val();

    loadAlumnos(tutor_id);
});


// cuando ocurre un cambio en el select de tutores
$("#mes_curso_filtro").change(function () {
    let filtro = $('#mes_curso_filtro').val();

    if(filtro != 'null'){
        obtenerRetosCargadosFiltro(filtro);
        obtenerRetosRealizadosFiltro(filtro);

    }else{
        obtenerRetos();
        obtenerRetosRealizados();
    }
});


