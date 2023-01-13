var tabla_profiler_quiz = null,
    tabla_profiler_questions = null,
    tabla_profiler_answers = null;

jQuery(document).ready(function ($) {
    //Inicializar tabla de quiz
    ObtenerTablaProfilerQuiz();

    /*Click en imagenes de camara, para subir archivos*/
    $('#profiler_question_image_preview').on('click', function () {
        $("#profiler_question_image").trigger("click");
    });

    //Validador para formulario de quiz
    $('#form_profiler_quiz').validate({
        rules: {
            profiler_quiz_history: {
                required: true
            },
            profiler_quiz_points: {
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

    //Validador para formulario de pregunta
    $('#form_profiler_question').validate({
        rules: {
            profiler_question: {
                required: true
            }
        },
        submitHandler: function (form) {
            GuardarPregunta();
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

    //Validador para formulario de respuesta
    $('#form_profiler_answer').validate({
        rules: {
            profiler_answer: {
                required: true
            },
            profiler_correct: {
                required: true
            }
        },
        submitHandler: function (form) {
            GuardarRespuesta();
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
function ObtenerTablaProfilerQuiz() {
    //Se agregan los eventos para los botones de "Ver Preguntas", "Editar" y "Eliminar" del listado
    $('#tabla_profiler_quiz').on( 'click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_profiler_quiz.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if(es_boton){
                if(cmd=="editar"){
                    EditarProfilerQuiz(record);
                }
                if(cmd=="eliminar"){
                    EliminarProfilerQuiz(record)
                }
                if(cmd=="preguntas"){
                    $('#modal_preguntas').modal('show');
                    ObtenerTablaPreguntas(record)
                }
            }
        }
    } );
    $('#tabla_profiler_quiz').on('tbody click','tr',function(e){
        var record = tabla_profiler_quiz.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarProfilerQuiz(record);
                }
                if (cmd == "eliminar") {
                    EliminarProfilerQuiz(record)
                }
                if (cmd == "preguntas") {
                    $('#modal_preguntas').modal('show');
                    ObtenerTablaPreguntas(record)
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_profiler_quiz = $('#tabla_profiler_quiz').DataTable({
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
            url: "../index.php/Games/ProfilerQuiz",
            type: 'POST',
            data: function (d) {
            },
            error: function (xhr, error, code){
                tabla_profiler_quiz.clear().draw();
            }

        },
        buttons: [],
        "columns": [
            {
                data: "history",
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
                width:"150px",
                render: function ( data, type, row ) {
                    var t = '';
                    t = '' +
                        '<div class="pt-2 pb-2 text-center">' +
                        '<a title="Ver preguntas" class="btn btn-info btn-xs editar mr-2 lead_0_8 text-white" cmd="preguntas"><i cmd="preguntas" class="fa fa-eye"></i></a>' +
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
function AgregarProfilerQuiz() {
    $('#form_profiler_quiz').trigger("reset");
    $('#title_profiler_quiz').html("Agregar categoría");
    $('#profiler_quiz_id').val('');
    $('#modal_profiler_quiz').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para editar un quiz, se carga y muestra el formulario
 ***********************************************************************/
function EditarProfilerQuiz(record) {
    $('#form_profiler_quiz').trigger("reset");
    $('#title_profiler_quiz').html("Editar categoría");
    $('#profiler_quiz_id').val(record.id);
    $('#profiler_quiz_history').val(record.history);
    $('#profiler_quiz_points').val(record.points);
    $('#modal_profiler_quiz').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar un quiz(agregar o editar),  por peticion AJAX
 ***********************************************************************/
function GuardarQuizz() {
    var quiz_id = $('#profiler_quiz_id').val(),
        data = {
            history : $('#profiler_quiz_history').val(),
            points : $('#profiler_quiz_points').val()
        },
        method="SaveProfilerQuiz";

    //Si se cargo la llave del quiz, entonces es edicion
    if(quiz_id != ''){
        data.id = quiz_id;
        method = "EditProfilerQuiz"
    }

    //Cambio en los quiz, ocultamos los apartados dependientes: preguntas y respuestas
    $('#seccion_preguntas').addClass("d-none");
    if(tabla_profiler_questions)tabla_profiler_questions.clear().draw();
    $('#seccion_respuestas').addClass("d-none");
    if(tabla_profiler_answers)tabla_profiler_answers.clear().draw();

    $.ajax({
        url:  window.base_url+"Games/"+method,
        type: "POST",
        data: data,
        cache: false,
        success: function(response){
            tabla_profiler_quiz.ajax.reload();
            $('#modal_profiler_quiz').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Catálogo quiz',
                text: 'El catálogo de quiz ha sido actualizado correctamente'
            });
        },
        error: function () {
            $('#modal_profiler_quiz').modal('hide');
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
function EliminarProfilerQuiz(record) {
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
            //Cambio en los quiz, ocultamos los apartados dependientes: preguntas y respuestas
            $('#seccion_preguntas').addClass("d-none");
            if(tabla_profiler_questions)tabla_profiler_questions.clear().draw();
            $('#seccion_respuestas').addClass("d-none");
            if(tabla_profiler_answers)tabla_profiler_answers.clear().draw();
            $.ajax({
                url:  window.base_url+"Games/DeleteProfilerQuiz",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function(response){
                    tabla_profiler_quiz.ajax.reload();
                    $('#modal_profiler_quiz').modal('hide');
                    Swal.fire({
                        type: 'success',
                        title: 'Catálogo quiz',
                        text: 'El catálogo de quiz ha sido actualizado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    $('#modal_profiler_quiz').modal('hide');
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
 *	Nota: Inicializacion de la tabla para listar preguntas de un quiz en particular
 ***********************************************************************/
function ObtenerTablaPreguntas(record) {
    //Guardamos el quiz seleccionado en una variable
    $('#selected_profiler_quiz_id').val(record.id);
    //Mostramos la seccion de preguntas, para cargar las del quiz seleccionado
    $('#seccion_preguntas').removeClass("d-none");

    //Si la tabla de preguntas ya habia sido creada con anterioridad
    if(tabla_profiler_questions){
        //Hubo un cambio de pregunta, por lo tanto ocultamos y limpiamos la seccion de respuestas
        $('#seccion_respuestas').addClass("d-none");
        if(tabla_profiler_answers)tabla_profiler_answers.clear().draw();
        //Actualizamos la tabla de preguntas para recibir las preguntas del nuevo quiz seleccionado
        tabla_profiler_questions.ajax.reload();
        return;
    }

    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_profiler_questions').on( 'click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_profiler_questions.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if(es_boton){
                if(cmd=="respuestas"){
                    ObtenerTablaRespuestas(record);
                    $('#modal_respuestas').modal('show');
                }
                if(cmd=="editar"){
                    EditarPregunta(record);
                }
                if(cmd=="eliminar"){
                    EliminarPregunta(record);
                }
            }
        }
    } );
    $('#tabla_profiler_questions').on('tbody click','tr',function(e){
        var record = tabla_profiler_questions.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "respuestas") {
                    ObtenerTablaRespuestas(record);
                    $('#modal_respuestas').modal('show');
                }
                if (cmd == "editar") {
                    EditarPregunta(record);
                }
                if (cmd == "eliminar") {
                    EliminarPregunta(record);
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_profiler_questions = $('#tabla_profiler_questions').DataTable({
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
            url: "../index.php/games/ProfilerQuestions",
            type: 'POST',
            data: function (d) {
                d.quiz_id = $('#selected_profiler_quiz_id').val();
            },
            error: function (xhr, error, code){
                tabla_profiler_questions.clear().draw();
            }
        },
        buttons: [],
        "columns": [{
            data: "question",
            render: function ( data, type, row ) {
                var t = '';
                t = '<div class="pt-2 pb-2">'+data+'</div>';
                return t;
            }
        },{
            data: "id",
            width:"150px",
            render: function ( data, type, row ) {
                var t = '';
                t = '' +
                    '<div class="pt-2 pb-2 text-center">' +
                    '<a title="Ver respuestas" class="btn btn-info btn-xs editar mr-2 lead_0_8 text-white" cmd="respuestas"><i cmd="respuestas" class="fa fa-eye"></i></a>' +
                    '<a title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8 text-white" cmd="editar"><i cmd="editar" class="fa fa-edit"></i></a>' +
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
 *	Nota: funcion para agregar una nueva pregunta, se muestra el formulario
 ***********************************************************************/
function AgregarPregunta() {
    $('#form_profiler_question').trigger("reset");
    $('#profiler_question_id').val('');
    $("#profiler_question_image_preview").attr("src", window.base_url + "assets/img/Camara.png");
    $('#modal_profiler_question').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para editar una pregunta, se carga y muestra el formulario
 ***********************************************************************/
function EditarPregunta(record) {
    $('#form_profiler_question').trigger("reset");
    $('#profiler_question_id').val(record.id);
    $('#profiler_question').val(record.question);
    $("#profiler_question_image_preview").attr("src", record.image);
    $('#modal_profiler_question').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar una pregunta(agregar o editar), por peticion AJAX
 ***********************************************************************/
function GuardarPregunta() {
    var question_id = $('#profiler_question_id').val(),
        data = new FormData(),
        image = $("#profiler_question_image").prop('files').length != 0 ? $("#profiler_question_image").prop('files')[0] : null,
        metodo="SaveProfilerQuestion";

    data.append('question', $('#profiler_question').val());
    data.append('quiz_id', $('#selected_profiler_quiz_id').val());
    data.append('image', image);

    //Si se cargo la llave de la pregunta, entonces es edicion
    if(question_id != ''){
        data.append('id', question_id);
        metodo = "EditProfilerQuestion"
    }

    //Hubo un cambio en el listado de preguntas, ocultamos y limpiamos la seccion de respuestas
    $('#seccion_respuestas').addClass("d-none");
    if(tabla_profiler_answers)tabla_profiler_answers.clear().draw();
    $.ajax({
        url:  window.base_url+"Games/"+metodo,
        type: "POST",
        contentType: false,
        data: data,
        processData: false,
        cache: false,
        success: function(response){
            tabla_profiler_questions.ajax.reload();
            $('#modal_profiler_question').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Catálogo Preguntas',
                text: 'El catálogo de preguntas ha sido actualizado correctamente'
            });
        },
        error: function () {
            $('#modal_profiler_question').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Catálogo Preguntas',
                text: 'La pregunta no se pudo guardar'
            });
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para eliminar una pregunta
 ***********************************************************************/
function EliminarPregunta(record) {
    Swal.fire({
        title: 'Catálogo quiz',
        text: "¿Estás seguro que deseas eliminar esta pregunta?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            //Hubo un cambio en el listado de preguntas, ocultamos y limpiamos la seccion de respuestas
            $('#seccion_respuestas').addClass("d-none");
            if(tabla_profiler_answers)tabla_profiler_answers.clear().draw();
            $.ajax({
                url:  window.base_url+"Games/DeleteProfilerQuestion",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function(response){
                    tabla_profiler_questions.ajax.reload();
                    Swal.fire({
                        type: 'success',
                        title: 'Catálogo quiz',
                        text: 'El catálogo de preguntas ha sido actualizado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    Swal.fire({
                        type: 'error',
                        title: 'Catálogo preguntas',
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
 *	Nota: Creacion de la tabla para listar las respuestas a una pregunta
 ***********************************************************************/
function ObtenerTablaRespuestas(record)  {
    //Guardamos la pregunta seleccionada en una variable
    $('#selected_question_id').val(record.id);
    //Mostramos la seccion de respuestas, para cargar las de la pregunta seleccionada
    $('#seccion_respuestas').removeClass("d-none");

    //Si la tabla de preguntas ya habia sido creada con anterioridad, la limpiamos y cargamos con las respuestas de la pregunta seleccionada
    if(tabla_profiler_answers){
        tabla_profiler_answers.ajax.reload();
        return;
    }

    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_profiler_answers').on( 'click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_profiler_answers.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if(es_boton){
                if(cmd=="editar"){
                    EditarRespuesta(record);
                }
                if(cmd=="eliminar"){
                    EliminarRespuesta(record);
                }
            }
        }
    } );
    $('#tabla_profiler_answers').on('tbody click','tr',function(e){
        var record = tabla_profiler_answers.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarRespuesta(record);
                }
                if (cmd == "eliminar") {
                    EliminarRespuesta(record);
                }
            }
        }
    });

    //Se crea la tabla con ayuda del plugin DataTable
    tabla_profiler_answers = $('#tabla_profiler_answers').DataTable({
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
            url: "../index.php/games/ProfilerAnswers",
            type: 'POST',
            data: function (d) {
                d.question_id = $('#selected_question_id').val();
            },
            error: function (xhr, error, code){
                tabla_profiler_answers.clear().draw();
            }
        },
        buttons: [],
        "columns": [{
            data: "answer",
            render: function ( data, type, row ) {
                var t = '';
                t = '<div class="pt-2 pb-2">'+data+'</div>';
                return t;
            }
        },{
            data: "correct",
            render: function ( data, type, row ) {
                var t = '';
                t = '<div class="pt-2 pb-2">'+(data == 1 ? "Sí" : "No")+'</div>';
                return t;
            }
        },{
            data: "id",
            width:"100px",
            render: function ( data, type, row ) {
                var t = '';
                t = '' +
                    '<div class="pt-2 pb-2 text-center">' +
                    '<a title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8 text-white" cmd="editar"><i cmd="editar" class="fa fa-edit"></i></a>' +
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
 *	Nota: funcion para agregar una nueva respuesta, se muestra el formulario
 ***********************************************************************/
function AgregarRespuesta() {
    $('#form_profiler_answer').trigger("reset");
    $('#profiler_answer_id').val('');
    $('#modal_profiler_answer').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para editar una respuesta, se carga y muestra el formulario
 ***********************************************************************/
function EditarRespuesta(record) {
    $('#form_profiler_answer').trigger("reset");
    $('#profiler_answer_id').val(record.id);
    $('#profiler_answer').val(record.answer);
    $('#profiler_correct').val(record.correct);
    $('#modal_profiler_answer').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar una respuesta(agregar o editar), por peticion AJAX
 ***********************************************************************/
function GuardarRespuesta() {
    var profiler_answer_id = $('#profiler_answer_id').val(),
        datos = {
            answer : $('#profiler_answer').val(),
            correct : $('#profiler_correct').val(),
            question_id : $('#selected_question_id').val()
        },
        metodo="SaveProfilerAnswer";

    //Si se cargo la llave de la respuesta, entonces es edicion
    if(profiler_answer_id != ''){
        datos.id = profiler_answer_id;
        metodo = "EditProfilerAnswer"
    }

    $.ajax({
        url:  window.base_url+"Games/"+metodo,
        type: "POST",
        data: datos,
        cache: false,
        success: function(response){
            tabla_profiler_answers.ajax.reload();
            $('#modal_profiler_answer').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Catálogo Respuestas',
                text: 'El catálogo de respuestas ha sido actualizado correctamente'
            });
        },
        error: function () {
            $('#modal_profiler_answer').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Catálogo Respuestas',
                text: 'La respuesta no se pudo guardar'
            });
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para eliminar una respuesta
 ***********************************************************************/
function EliminarRespuesta(record) {
    Swal.fire({
        title: 'Catálogo quiz',
        text: "¿Estás seguro que deseas eliminar esta respuesta?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url:  window.base_url+"Games/DeleteProfilerAnswer",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function(response){
                    tabla_profiler_answers.ajax.reload();
                    Swal.fire({
                        type: 'success',
                        title: 'Catálogo quiz',
                        text: 'El catálogo de respuestas ha sido actualizado correctamente'
                    });
                },
                error: function (error) {
                    var error_msg = error.responseJSON.error_msg;
                    Swal.fire({
                        type: 'error',
                        title: 'Catálogo respuestas',
                        text: error_msg
                    });
                }
            });
        }
    })
}

/*CARGA DE ARCHIVOS*/
function loadProfilerQuestionImage(event) {
    var url = URL.createObjectURL(event.target.files[0]);

    var output = document.getElementById('profiler_question_image_preview');
    output.src = url;
};
