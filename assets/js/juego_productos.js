var tabla_products_quiz = null,
    tabla_products_steps = null;

jQuery(document).ready(function ($) {
    //Inicializar tabla de quiz
    ObtenerTablaProductsQuiz();

    /*Click en imagenes de camara, para subir archivos*/
    $('#products_quiz_image_preview').on('click', function () {
        $("#products_quiz_image").trigger("click");
    });
    $('#products_step_image_preview').on('click', function () {
        $("#products_step_image").trigger("click");
    });

    //Validador para formulario de quiz
    $('#form_products_quiz').validate({
        rules: {
            products_quiz_description: {
                required: true
            },
            products_quiz_points: {
                required: true,
                min: 0
            }
        },
        submitHandler: function (form) {
            GuardarQuizz();
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

    //Validador para formulario del paso
    $('#form_products_step').validate({
        rules: {
            products_step_option_description: {
                required: true
            }
        },
        submitHandler: function (form) {
            GuardarPaso();
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


/*************QUIZ**************/
/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Inicializacion de la tabla para listar quiz
 ***********************************************************************/
function ObtenerTablaProductsQuiz() {
    //Se agregan los eventos para los botones de "Ver Pasos", "Editar" y "Eliminar" del listado
    $('#tabla_products_quiz').on( 'click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_products_quiz.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if(es_boton){
                if(cmd=="editar"){
                    EditarProductsQuiz(record);
                }
                if(cmd=="eliminar"){
                    EliminarProductsQuiz(record)
                }
                if(cmd=="pasos"){
                    ObtenerTablaPasos(record);
                    $('#modal_pasos').modal('show');
                }
            }
        }
    } );
    $('#tabla_products_quiz').on('tbody click','tr',function(e){
        var record = tabla_products_quiz.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarProductsQuiz(record);
                }
                if (cmd == "eliminar") {
                    EliminarProductsQuiz(record)
                }
                if (cmd == "pasos") {
                    ObtenerTablaPasos(record);
                    $('#modal_pasos').modal('show');
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_products_quiz = $('#tabla_products_quiz').DataTable({
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
            url: "../index.php/Games/ProductQuiz",
            type: 'POST',
            data: function (d) {
            },
            error: function (xhr, error, code){
                tabla_products_quiz.clear().draw();
            }
        },
        buttons: [],
        "columns": [
            {
                data: "description",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "points",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '<div class="pt-2 pb-2">'+data+'</div>';
                    return t;
                }
            },
            {
                data: "id",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '' +
                        '<div class="pt-2 pb-2 text-center">' +
                        '<a title="Ver pasos" class="btn btn-info btn-xs editar mr-2 lead_0_8 text-white" cmd="pasos"><i cmd="pasos" class="fa fa-eye"></i></a>' +
                        '<a title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8 text-white" cmd="editar"><i cmd="editar" class="fa fa-edit"></i></a>' +
                        '<a title="Eliminar" class="btn btn-danger btn-xs borrar lead_0_8 text-white" cmd="eliminar"><i cmd="eliminar" class="fa fa-times"></i></a>'+
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
 *	Fecha: 29/07/2021
 *	Nota: funcion para agregar un nuevo quiz, se muestra el formulario
 ***********************************************************************/
function AgregarProductsQuiz() {
    $('#form_products_quiz').trigger("reset");
    $('#products_quiz_id').val('');
    $("#products_quiz_image_preview").attr("src", window.base_url + "assets/img/Camara.png");
    $('#modal_products_quiz').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para editar un quiz, se carga y muestra el formulario
 ***********************************************************************/
function EditarProductsQuiz(record) {
    $('#form_products_quiz').trigger("reset");
    $('#products_quiz_id').val(record.id);
    $('#products_quiz_description').val(record.description);
    $('#products_quiz_points').val(record.points);
    $("#products_quiz_image_preview").attr("src", record.image);
    $('#modal_products_quiz').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar un quiz(agregar o editar),  por peticion AJAX
 ***********************************************************************/
function GuardarQuizz() {
    var quiz_id = $('#products_quiz_id').val(),
        data = new FormData(),
        image = $("#products_quiz_image").prop('files').length != 0 ? $("#products_quiz_image").prop('files')[0] : null,
        method="SaveProductsQuiz";

    data.append('description', $('#products_quiz_description').val());
    data.append('points', $('#products_quiz_points').val());
    data.append('image', image);

    //Si se cargo la llave del quiz, entonces es edicion
    if(quiz_id != ''){
        data.append('id', quiz_id);
        method = "EditProductsQuiz"
    }

    //Cambio en los quiz, ocultamos los apartados dependientes: pasos
    $('#seccion_pasos').addClass("d-none");
    if(tabla_products_steps)tabla_products_steps.clear().draw();

    $.ajax({
        url:  window.base_url+"Games/"+method,
        type: "POST",
        contentType: false,
        data: data,
        processData: false,
        cache: false,
        success: function(response){
            tabla_products_quiz.clear().draw();
            tabla_products_quiz.ajax.reload();
            $('#modal_products_quiz').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Catálogo quiz',
                text: 'El catálogo de quiz ha sido actualizado correctamente'
            });
        },
        error: function () {
            $('#modal_products_quiz').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Catálogo quiz',
                text: 'El quiz no se pudo guardar'
            });
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para eliminar un quiz
 ***********************************************************************/
function EliminarProductsQuiz(record) {
    Swal.fire({
        title: 'Catálogo quiz',
        text: "¿Estás seguro que deseas eliminar este quiz?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            //Cambio en los quiz, ocultamos los apartados dependientes: pasos
            $('#seccion_pasos').addClass("d-none");
            if(tabla_products_steps)tabla_products_steps.clear().draw();
            $.ajax({
                url:  window.base_url+"Games/DeleteProductsQuiz",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function(response){
                    tabla_products_quiz.ajax.reload();
                    Swal.fire({
                        type: 'success',
                        title: 'Catálogo quiz',
                        text: 'El catálogo de quiz ha sido actualizado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    Swal.fire({
                        type: 'error',
                        title: 'Catálogo quiz',
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
 *	Nota: Inicializacion de la tabla para listar pasos de un quiz en particular
 ***********************************************************************/
function ObtenerTablaPasos(record) {
    //Guardamos el quiz seleccionado en una variable
    $('#selected_products_quiz_id').val(record.id);
    //Mostramos la seccion de pasos, para cargar las del quiz seleccionado
    $('#seccion_pasos').removeClass("d-none");

    //Si la tabla de pasos ya habia sido creada con anterioridad, la limpiamos y cargamos con las pasos del quiz seleccionado
    if(tabla_products_steps){
        tabla_products_steps.ajax.reload();
        return;
    }

    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_products_steps').on( 'click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_products_steps.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if(es_boton){
                if(cmd=="respuestas"){
                    ObtenerTablaRespuestas(record);
                }
                if(cmd=="editar"){
                    EditarPaso(record);
                }
                if(cmd=="eliminar"){
                    EliminarPaso(record);
                }
            }
        }
    } );
    $('#tabla_products_steps').on('tbody click','tr',function(e){
        var record = tabla_products_steps.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "respuestas") {
                    ObtenerTablaRespuestas(record);
                }
                if (cmd == "editar") {
                    EditarPaso(record);
                }
                if (cmd == "eliminar") {
                    EliminarPaso(record);
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_products_steps = $('#tabla_products_steps').DataTable({
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
            url: "../index.php/games/ProductSteps",
            type: 'POST',
            data: function (d) {
                d.quiz_id = $('#selected_products_quiz_id').val();
            },
            error: function (xhr, error, code){
                tabla_products_steps.clear().draw();
            }
        },
        buttons: [],
        "columns": [{
            data: "num_step",
            render: function ( data, type, row ) {
                var t = '';
                t = '<div class="pt-2 pb-2">'+data+'</div>';
                return t;
            }
        },{
            data: "option_description",
            render: function ( data, type, row ) {
                var t = '';
                t = '<div class="pt-2 pb-2">'+data+'</div>';
                return t;
            }
        },{
            data: "id",
            render: function ( data, type, row ) {
                var t = '';
                t = '' +
                    '<div class="pt-2 pb-2 text-center">' +
                    '<a title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8 text-white" cmd="editar"><i cmd="editar" class="fa fa-edit"></i></a>' +
                    //'<a class="btn btn-danger btn-xs borrar lead_0_8" cmd="eliminar"><i class="fa fa-times"></i> Eliminar</a>'+
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
 *	Nota: funcion para editar un paso, se carga y muestra el formulario
 ***********************************************************************/
function EditarPaso(record) {
    $('#form_products_step').trigger("reset");
    $('#products_step_id').val(record.id);
    $('#products_step_num_step').val(record.num_step);
    $('#products_step_option_description').val(record.option_description);
    $("#products_step_image_preview").attr("src", record.option_image);
    $('#modal_products_step').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar un paso(agregar o editar), por peticion AJAX
 ***********************************************************************/
function GuardarPaso() {
    var question_id = $('#products_step_id').val(),
        data = new FormData(),
        option_image = $("#products_step_image").prop('files').length != 0 ? $("#products_step_image").prop('files')[0] : null,
        metodo="SaveProductsStep";

    data.append('num_step', $('#products_step_num_step').val());
    data.append('option_description', $('#products_step_option_description').val());
    data.append('quiz_id', $('#selected_products_quiz_id').val());
    data.append('option_image', option_image);

    //Si se cargo la llave del paso, entonces es edicion
    if(question_id != ''){
        data.append('id', question_id);
        metodo = "EditProductsStep"
    }

    $.ajax({
        url:  window.base_url+"Games/"+metodo,
        type: "POST",
        contentType: false,
        data: data,
        processData: false,
        cache: false,
        success: function(response){
            tabla_products_steps.ajax.reload();
            $('#modal_products_step').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Catálogo Pasos',
                text: 'El catálogo de pasos ha sido actualizado correctamente'
            });
        },
        error: function () {
            $('#modal_products_step').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Catálogo Pasos',
                text: 'El paso no se pudo guardar'
            });
        }
    });
}

/*CARGA DE ARCHIVOS*/
function loadProductsQuizImage(event) {
    var url = URL.createObjectURL(event.target.files[0]);

    var output = document.getElementById('products_quiz_image_preview');
    output.src = url;
};

function loadProductsStepImage(event) {
    var url = URL.createObjectURL(event.target.files[0]);

    var output = document.getElementById('products_step_image_preview');
    output.src = url;
};
