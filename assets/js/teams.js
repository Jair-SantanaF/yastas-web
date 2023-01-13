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

var tutores = [];
var becarios = [];

jQuery(document).ready(function ($) {
    obtenerTutoresAsesores();
    obtenerBecariosTutores();
    obtenerRegiones();
});

function generarFecha(date){
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const union = [day, month, year].join('/');
    
    return union;
}


// funcion para obtener los tutores asociados a un asesor
function obtenerTutoresAsesores() {
    //Este sirve para destruir el datatable
    $('#tabla_tutores').dataTable().fnDestroy();
    $('#tabla_tutores').DataTable({
        ajax: {
            url: window.base_url + "Teams/getTutoresAsesores",
            type: 'POST',
            data: {
            },
            dataType: 'json',
            dataSrc: 'data',
        },
        idSrc: "id",
        language: idiomaDataTable,
        columns: [
            {data: "nombre_tutor"},
            {data: "nombre_asesor"},
            {
                width: '100px',
                data: "tutor_id",
                render: function (data, type, row) {
                    return `
                        <div class="pt-2 pb-2 text-center">
                        <button class="btn btn-primary btn-xs mr-2 lead_0_8 text-white" onclick="editarTutor(${data})"><i class="fa fa-edit"></i></button>
                        <a class="btn btn-danger btn-xs lead_0_8 text-white" onclick="eliminarTutor(${data})"><i class="fa fa-times"></i></a>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'asc']]
    });
}


// funcion para obtener los becarios asociados a un tutor
function obtenerBecariosTutores() {
    //Este sirve para destruir el datatable
    $('#tabla_becarios').dataTable().fnDestroy();
    $('#tabla_becarios').DataTable({
        ajax: {
            url: window.base_url + "Teams/getBecariosTutores",
            type: 'POST',
            data: {
            },
            dataType: 'json',
            dataSrc: 'data',
        },
        idSrc: "becario_id",
        language: idiomaDataTable,
        columns: [
            {data: "nombre_becario"},
            {data: "nombre_tutor"},
            {
                width: '100px',
                data: "becario_id",
                render: function (data, type, row) {
                    return `
                        <div class="pt-2 pb-2 text-center">
                            <button class="btn btn-primary btn-xs mr-2 lead_0_8 text-white" onclick="editarBecario(${data})"><i class="fa fa-edit"></i></button>
                            <a class="btn btn-danger btn-xs lead_0_8 text-white" onclick="eliminarBecario(${data})"><i class="fa fa-times"></i></a>
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
    $.ajax({
        url: window.base_url + "Challenges/getRegiones",
        type: "POST",
        data: {},
        cache: false,
        success: function (response) {
            if(response.status_code == 200) {
                // regiones para equipos
                $('#region_equipo_id').empty();
                $('#region_equipo_id').append('<option hidden selected value="">-- Seleccionar --</option>');
            
                $.each(response.data, function(index, value){
                    $('#region_equipo_id').append("<option value='" + value.id +"'>"+ value.nombre +"</option>");                                
                });


                // regiones para becarios
                $('#region_becario_id').empty();
                $('#region_becario_id').append('<option hidden selected value="">-- Seleccionar --</option>');
            
                $.each(response.data, function(index, value){
                    $('#region_becario_id').append("<option value='" + value.id +"'>"+ value.nombre +"</option>");                                
                }); 


                // regiones para tutores
                $('#region_tutor_id').empty();
                $('#region_tutor_id').append('<option hidden selected value="">-- Seleccionar --</option>');
            
                $.each(response.data, function(index, value){
                    $('#region_tutor_id').append("<option value='" + value.id +"'>"+ value.nombre +"</option>");                                
                }); 
            }
        },
        error: function (error) {
            console.log(error)
        }
    });
}


// funcion para abrir el modal para editar el becario
function editarBecario(id){
    $("#becario_id_editar").val('');
    $("#nombre_becario_editar").val('');
    $("#region_becario_id").val('');
    $("#tutor_becario_id").val('');
    $('#tutor_becario_id').prop('disabled', true);

    $.ajax({
        url: window.base_url + "Teams/getBecario",
        type: "POST",
        data: {
            becario_id: id
        },
        cache: false,
        success: function (response) {
            if(response.status_code == 200){
                loadDropdownTutores(response.data.region_id, response.data.tutor_id);
                $("#becario_id_editar").val(response.data.becario_id);
                $("#nombre_becario_editar").val(response.data.becario_nombre);
                $("#region_becario_id").val(response.data.region_id);

                $("#modal_editar_becario").modal('show');
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


// funcion para obtener los tutores
function loadDropdownTutores(region_id, tutor_id){
    $.ajax({
        url: window.base_url + "Challenges/getTutores",
        type: "POST",
        data: {
            region_id: region_id
        },
        cache: false,
        success: function (response) {
            $('#tutor_becario_id').empty();
            $('#tutor_becario_id').append('<option hidden selected value="">-- Seleccionar --</option>');

            $.each(response.data, function(index, value){
                $('#tutor_becario_id').append("<option value='" + value.id +"'>"+ value.nombre +"</option>");                                
            }); 

            //si se manda un id de tutor seteamos el campo y lo habilitamos
            if(tutor_id != null){
                $("#tutor_becario_id").val(tutor_id);
                $('#tutor_becario_id').prop('disabled', false);
            }

            
            // ====================================================================
            // llenado para el select del modal de equipo
            $('#tutor_equipo_id').empty();
            $('#tutor_equipo_id').append('<option hidden selected value="">-- Seleccionar --</option>');
        
            $.each(response.data, function(index, value){
                $('#tutor_equipo_id').append("<option value='" + value.id +"'>"+ value.nombre +"</option>");                                
            }); 

            $('#tutor_equipo_id').prop('disabled', false);


        },
        error: function (error) {
            console.log(error)
        }
    });
}


// funcion para actualizar los datos de un becario
function actualizarDatosBecario(){
    $.ajax({
        url: window.base_url + "Teams/actualizarDatosBecario",
        type: "POST",
        data: {
            becario_id: $("#becario_id_editar").val(),
            region_id: $("#region_becario_id").val(),
            tutor_id: $("#tutor_becario_id").val(),
        },
        cache: false,
        success: function (response) {
            if(response.status_code == 200){
                Swal.fire({
                    title: "Éxito",
                    text: response.msg,
                    type: 'success'
                });

                $('#tabla_tutores').DataTable().ajax.reload();
                $('#tabla_becarios').DataTable().ajax.reload();
                $('#modal_editar_becario').modal('hide');
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


// funcion para eliminar una asociación entre becario y tutor
function eliminarBecario(id){
    $.ajax({
        url: window.base_url + "Teams/deleteAsociacionBecarioTutor",
        type: "POST",
        data: {
            becario_id: id
        },
        cache: false,
        success: function (response) {
            if(response.status_code == 200){
                Swal.fire({
                    title: "Éxito",
                    text: response.msg,
                    type: 'success'
                });

                $('#tabla_tutores').DataTable().ajax.reload();
                $('#tabla_becarios').DataTable().ajax.reload();
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


// funcion para obtener los asesores
function loadDropdownAsesores(region_id, asesor_id){
    $.ajax({
        url: window.base_url + "Teams/getAsesores",
        type: "POST",
        data: {
            region_id: region_id
        },
        cache: false,
        success: function (response) {
            // llenado para el select del modal de editar tutor
            $('#asesor_id').empty();
            $('#asesor_id').append('<option hidden selected value="">-- Seleccionar --</option>');
        
            $.each(response.data, function(index, value){
                $('#asesor_id').append("<option value='" + value.id +"'>"+ value.nombre +"</option>");                                
            }); 

            //si se manda un id de asesor seteamos el campo y lo habilitamos
            if(asesor_id != null){
                $("#asesor_id").val(asesor_id);
                $('#asesor_id').prop('disabled', false);
            }

            // ====================================================================
            // llenado para el select del modal de equipo
            $('#asesor_equipo_id').empty();
            $('#asesor_equipo_id').append('<option hidden selected value="">-- Seleccionar --</option>');
        
            $.each(response.data, function(index, value){
                $('#asesor_equipo_id').append("<option value='" + value.id +"'>"+ value.nombre +"</option>");                                
            }); 

            $('#asesor_equipo_id').prop('disabled', false);
        },
        error: function (error) {
            console.log(error)
        }
    });
}


// funcion para abrir el modal para editar el tutor
function editarTutor(id){
    $("#tutor_id_editar").val('');
    $("#nombre_tutor_editar").val('');
    $("#region_tutor_id").val('');
    $("#asesor_id").val('');
    $('#asesor_id').prop('disabled', true);

    $.ajax({
        url: window.base_url + "Teams/getTutor",
        type: "POST",
        data: {
            tutor_id: id
        },
        cache: false,
        success: function (response) {
            if(response.status_code == 200){
                loadDropdownAsesores(response.data.region_id, response.data.asesor_id);
                $("#tutor_id_editar").val(response.data.tutor_id);
                $("#nombre_tutor_editar").val(response.data.tutor_nombre);
                $("#region_tutor_id").val(response.data.region_id);

                $("#modal_editar_tutor").modal('show');
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


// funcion para actualizar los datos de un tutor
function actualizarDatosTutor(){
    $.ajax({
        url: window.base_url + "Teams/updateDatosTutor",
        type: "POST",
        data: {
            tutor_id: $("#tutor_id_editar").val(),
            region_id: $("#region_tutor_id").val(),
            asesor_id: $("#asesor_id").val(),
        },
        cache: false,
        success: function (response) {
            if(response.status_code == 200){
                Swal.fire({
                    title: "Éxito",
                    text: response.msg,
                    type: 'success'
                });

                $('#tabla_tutores').DataTable().ajax.reload();
                $('#tabla_becarios').DataTable().ajax.reload();
                $('#modal_editar_tutor').modal('hide');
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


// funcion para eliminar una asociación entre tutor y asesor
function eliminarTutor(id){
    $.ajax({
        url: window.base_url + "Teams/deleteAsociacionTutorAsesor",
        type: "POST",
        data: {
            tutor_id: id
        },
        cache: false,
        success: function (response) {
            if(response.status_code == 200){
                Swal.fire({
                    title: "Éxito",
                    text: response.msg,
                    type: 'success'
                });

                $('#tabla_tutores').DataTable().ajax.reload();
                $('#tabla_becarios').DataTable().ajax.reload();
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


// funcion para abrir el modal de agregar equipo
function agregarEquipo(){
    $("#containerTutor").hide();
    $("#containerBecario").hide();

    $("#tipo_usuario").val('');
    $("#region_equipo_id").val('');
    $("#asesor_equipo_id").val('');
    $('#asesor_equipo_id').prop('disabled', true);
    $("#tutor_equipo_id").val('');
    $('#tutor_equipo_id').prop('disabled', true);
    $("#contenedor_tutores").hide();
    $("#contenedor_becarios").hide();
    $("#btnGuardarEquipo").prop('disabled', false);

    tutores = [];
    becarios = [];

    $("#modal_agregar_equipo").modal('show');
}


// funcion para obtener los tutores de una region
function loadTutoresRegion(region_id){
    $.ajax({
        url: window.base_url + "Teams/getTutoresRegion",
        type: "POST",
        data: {
            region_id: region_id
        },
        cache: false,
        success: function (response) {
            tutores = response.data;
            $("#contenedor_tutores").empty();
            var container = $('#contenedor_tutores');

            if(response.data.length > 0){
                $(container).append('<div class="col-md-12 mb-3">Seleccionar usuarios:</div>');
                $.each(response.data, function(index, valor){
                    let html = `
                        <div class="col-md-4">
                            <div class="form-check form-check-inline">
                                <input class="form-check" type="checkbox" id="cb_tutores_${index}" value="${valor.tutor_id}">
                                <label class="form-check-label" for="cb_tutores_${index}">${valor.nombre_tutor}</label>
                            </div>
                        </div>
                    `;
    
                    $(container).append(html);
                });
            }else{
                $(container).append('<div class="col-md-12 mb-3">Seleccionar usuarios:</div>');
                $(container).append('<div class="col-md-12 mb-3">No se han encontrado tutores disponibles</div>');
                $("#btnGuardarEquipo").prop('disabled', true);
            }


            $("#contenedor_tutores").show();
        },
        error: function (error) {
            console.log(error)
        }
    });
}


// funcion para obtener los becarios de una region
function loadBecariosRegion(region_id){
    $.ajax({
        url: window.base_url + "Teams/getBecariosRegion",
        type: "POST",
        data: {
            region_id: region_id
        },
        cache: false,
        success: function (response) {
            becarios = response.data;
            $("#contenedor_becarios").empty();
            var container = $('#contenedor_becarios');

            if(response.data.length > 0){
                $(container).append('<div class="col-md-12 mb-3">Seleccionar usuarios:</div>');
                $.each(response.data, function(index, valor){
                    let html = `
                        <div class="col-md-4">
                            <div class="form-check form-check-inline">
                                <input class="form-check" type="checkbox" id="cb_becarios_${index}" value="${valor.becario_id}">
                                <label class="form-check-label" for="cb_becarios_${index}">${valor.nombre_becario}</label>
                            </div>
                        </div>
                    `;
    
                    $(container).append(html);
                });
            }else{
                $(container).append('<div class="col-md-12 mb-3">Seleccionar usuarios:</div>');
                $(container).append('<div class="col-md-12 mb-3">No se han encontrado becarios disponibles</div>');
                $("#btnGuardarEquipo").prop('disabled', true);
            }

            $("#contenedor_becarios").show();
        },
        error: function (error) {
            console.log(error)
        }
    });
}


// funcion para guardar un equipo
function guardarEquipo(){
    tutoresArray = [];
    becariosArray = [];

    if($("#tipo_usuario").val() == 1){
        for(let i = 0; i < tutores.length; i++) {
            if (get("cb_tutores_" + i).checked) {
                tutoresArray.push(tutores[i].tutor_id)
            }
        }
    }

    if($("#tipo_usuario").val() == 2){
        for(let i = 0; i < becarios.length; i++) {
            if (get("cb_becarios_" + i).checked) {
                becariosArray.push(becarios[i].becario_id)
            }
        }
    }

    $.ajax({
        url: window.base_url + "Teams/saveTeam",
        type: "POST",
        data: {
            tipo_id : $("#tipo_usuario").val(),
            region_id: $("#region_tutor_id").val(),
            asesor_id: $("#asesor_equipo_id").val(),
            tutor_id: $("#tutor_equipo_id").val(),
            tutores: tutoresArray,
            becarios: becariosArray
        },
        cache: false,
        success: function (response) {
            if(response.status_code == 200){
                Swal.fire({
                    title: "Éxito",
                    text: response.msg,
                    type: 'success'
                });

                $('#tabla_tutores').DataTable().ajax.reload();
                $('#tabla_becarios').DataTable().ajax.reload();
                $('#modal_agregar_equipo').modal('hide');
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


// cuando ocurre un cambio en el select de regiones
$("#region_becario_id").change(function () {
    let region_id = $('#region_becario_id').val();

    $('#tutor_becario_id').prop('disabled', true);
    loadDropdownTutores(region_id);
});


// cuando ocurre un cambio en el select de regiones
$("#tipo_usuario").change(function () {
    $("#region_equipo_id").val('');

    $("#asesor_equipo_id").val('');
    $('#asesor_equipo_id').prop('disabled', true);
    $("#tutor_equipo_id").val('');
    $('#tutor_equipo_id').prop('disabled', true);
    $("#contenedor_tutores").hide();
    $("#contenedor_becarios").hide();
    $("#btnGuardarEquipo").prop('disabled', false);

    tutores = [];
    becarios = [];

    if($("#tipo_usuario").val() == 1){
        $("#containerTutor").show();
        $("#containerBecario").hide();

    }else if($("#tipo_usuario").val() == 2){
        $("#containerTutor").hide();
        $("#containerBecario").show();

    }else{
        $("#containerTutor").hide();
        $("#containerBecario").hide();
    }
});


// cuando ocurre un cambio en el select de regiones
$("#region_equipo_id").change(function () {
    let region_id = $('#region_equipo_id').val();

    if($("#tipo_usuario").val() == 1){
        $('#asesor_equipo_id').prop('disabled', true);
        loadDropdownAsesores(region_id, null);
        loadTutoresRegion(region_id);

    }else if($("#tipo_usuario").val() == 2){
        $('#tutor_equipo_id').prop('disabled', true);
        loadDropdownTutores(region_id, null);
        loadBecariosRegion(region_id);

    }
});