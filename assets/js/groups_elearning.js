var table_groups = null,
    tabla_pending = null,
    tabla_elements = null;

jQuery(document).ready(function ($) {
    //Inicializar tabla de eventos
    ObtenerTablaGrupos();
    // Handle click on "Select all" control
    $('#example-select-all').on('click', function(){
        // Check/uncheck all checkboxes in the table
        var rows = tabla_elements.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });
});

/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Obtener tabla de grupos registrados
 ***********************************************************************/
function ObtenerTablaGrupos() {
    //Se agregan los eventos para los botones de "Ver miembros", "Editar" y "Eliminar" del listado
    $('#table_groups').on( 'click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = table_groups.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if(es_boton){
                if(cmd=="asignados"){
                    $('#modal_elements_detalle').modal('show');
                    ObtenerTablaElementos(record)
                }
            }
        }
    } );
    $('#table_groups').on('tbody click','tr',function(e){
        var record = table_groups.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "asignados") {
                    $('#modal_elements_detalle').modal('show');
                    ObtenerTablaElementos(record)
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    table_groups = $('#table_groups').DataTable({
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
            url: "../index.php/Groups/GroupsRegister",
            type: 'POST',
            data: function (d) {
            },
            error: function (xhr, error, code){
                table_groups.clear().draw();
            }

        },
        buttons: [],
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
                data: "id",
                width:"100px",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '' +
                        '<div class="pt-2 pb-2 text-center">' +
                        '<a title="Elementos asignados" class="btn btn-info btn-xs editar mr-2 lead_0_8 text-white" cmd="asignados"><i cmd="asignados" class="fa fa-eye"></i></a>';
                    return t;
                }
            }
        ]
    });
}
/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Inicializacion de la tabla para listar elementos
 ***********************************************************************/
function ObtenerTablaElementos(record) {
    //Guardamos el evento seleccionado en una variable
    $('#selected_group_id').val(record.id);
    //Mostramos la seccion de miembros, para cargar los del evento seleccionado
    $('#seccion_elements').removeClass("d-none");

    //Si la tabla de miembros ya habia sido creada con anterioridad
    if(tabla_pending){
        //Actualizamos la tabla de miembros para recibir los comentarios del nuevo evento seleccionado
        tabla_pending.ajax.reload();
        return;
    }

    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_pending').on( 'click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_pending.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if(es_boton){
                if(cmd=="eliminar"){
                    EliminarElemento(record);
                }
            }
        }
    } );
    $('#tabla_pending').on('tbody click','tr',function(e){
        var record = tabla_pending.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "eliminar") {
                    EliminarElemento(record);
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_pending = $('#tabla_pending').DataTable({
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
            url: "../index.php/Groups/ElearningGroups",
            type: 'POST',
            data: function (d) {
                d.group_id = $('#selected_group_id').val();
            },
            error: function (xhr, error, code){
                tabla_pending.clear().draw();
            }
        },
        buttons: [],
        "columns": [{
            data: "title",
            render: function ( data, type, row ) {
                var t = '';
                t = '<div class="pt-2 pb-2">'+data+'</div>';
                return t;
            }
        },{
            data: "id",
            width:"100px",
            render: function ( data, type, row ) {
                var t = '';
                t = '' +
                    '<div class="pt-2 pb-2 text-center">' +
                    '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8 text-white" cmd="eliminar"><i cmd="eliminar" class="fa fa-times"></i></a>'+
                    '</div>';
                return t;
            }
        }]
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Funcion para eliminar un nuevo elemento
 ***********************************************************************/
function AgregarElemento() {
    $('#modal_element').modal('show');
    ObtenerTablaElementosNoAsignados();
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para eliminar un miembro
 ***********************************************************************/
function EliminarElemento(record) {
    Swal.fire({
        title: 'Eliminar Elemento',
        text: "¿Estás seguro que deseas eliminar este elemento?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url:  window.base_url+"Groups/DeleteElearning",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function(response){
                    tabla_pending.ajax.reload();
                    Swal.fire({
                        type: 'success',
                        title: 'Eliminar elemento',
                        text: 'El elemento ha sido eliminado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    Swal.fire({
                        type: 'error',
                        title: 'Eliminar elemento',
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
 *	Fecha: 29/07/2021
 *	Nota: Inicializacion de la tabla para listar elementos disponibles para agregar
 ***********************************************************************/
function ObtenerTablaElementosNoAsignados(record) {
    //Si la tabla de usuarios ya habia sido creada con anterioridad
    if(tabla_elements){
        $('#example-select-all').prop( "checked", false );
        tabla_elements.clear().draw();
        //Actualizamos la tabla de usuarios para recibir los usuarios disponibles para agregar
        tabla_elements.ajax.reload();
        return;
    }
    $('#tabla_elements').on( 'click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_elements.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if(es_boton){
                if(cmd=="eliminar"){
                    EliminarMember(record);
                }
            }
        }
    } );
    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_elements').on('tbody click','tr',function(e){
        var record = tabla_elements.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "eliminar") {
                    EliminarMember(record);
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_elements = $('#tabla_elements').DataTable({
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
        "order": [[1, "asc"]],
        "ajax": {
            url: "../index.php/Groups/NoElearningGroups",
            type: 'POST',
            data: function (d) {
                d.group_id = $('#selected_group_id').val();
            },
            error: function (xhr, error, code){
                tabla_elements.clear().draw();
            }
        },
        buttons: [],
        "columns": [{
            targets: 0,
            data: "id",
            defaultContent: '',
            orderable: false,
            'className': 'dt-body-center',
            'render': function (data, type, full, meta){
                return '<input type="checkbox" value="'+ $('<div/>').text(data).html() + '">';
            }
        },
            {
                data: "title",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            }]
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Funcion para guardar los elementos seleccionados
 ***********************************************************************/
function AgregarElementos(){
    var data={
            group_id: $('#selected_group_id').val()
        },
        elementos_seleccionados = [];

    tabla_elements.$('input[type="checkbox"]').each(function(){
        if(this.checked){
            elementos_seleccionados.push($(this).prop("value"));
        }
    });

    data.elements = JSON.stringify(elementos_seleccionados);

    $.ajax({
        url:  window.base_url+"Groups/SaveElearning",
        type: "POST",
        data: data,
        cache: false,
        success: function(response){
            tabla_pending.ajax.reload();
            Swal.fire({
                type: 'success',
                title: 'Agregar elemento',
                text: 'Los elementos han sido agregados correctamente'
            });
        },
        error: function (error) {
            tabla_pending.ajax.reload();
            var error_msg = error.responseJSON.error_msg;
            Swal.fire({
                type: 'error',
                title: 'Agrear elementos',
                text: error_msg
            });
        }
    });
    $('#modal_element').modal('hide');
}
