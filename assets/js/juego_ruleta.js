var tabla_roulette_quiz = null,
    tabla_roulette_questions = null,
    tabla_roulette_answers = null;

jQuery(document).ready(function ($) {
    ObtenerTablaQuizz();
    //ObtenerTablaPreguntas();

    $('#form_roulette_quiz').validate({
        rules: {
            roulette_quiz_name: {
                required: true
            },
            roulette_quiz_points: {
                required: true,
                min: 0
            }
        },
        submitHandler: function (form) {
            //console.log(datos)
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

    $('#form_roulette_question').validate({
        rules: {
            roulette_question: {
                required: true
            }
        },
        submitHandler: function (form) {
            //console.log(datos)
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

    $('#form_roulette_answer').validate({
        rules: {
            roulette_answer: {
                required: true
            },
            roulette_correct: {
                required: true
            }
        },
        submitHandler: function (form) {
            //console.log(datos)
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


/*************CATEGORIAS**************/
/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Creacion de la tabla para listar quizz
 ***********************************************************************/
function ObtenerTablaQuizz() {
    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_roulette_quiz').on( 'click', 'li', function (e) {
        var current_row = $(this).parents('tr');//Get the current row
        if (current_row.hasClass('child')) {//Check if the current row is a child row
            current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
        }
        var record = tabla_roulette_quiz.row(current_row).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if(es_boton){
                if(cmd=="editar"){
                    EditarQuizz(record);
                }
                if(cmd=="eliminar"){
                    EliminarQuizz(record);
                }
                if(cmd=="preguntas"){
                    ObtenerTablaPreguntas(record);
                    $('#modal_preguntas').modal('show');
                }
            }
        }
    } );
    $('#tabla_roulette_quiz').on('tbody click','tr',function(e){
        var record = tabla_roulette_quiz.row(this).data(),
            es_boton = e.target.hasAttribute("cmd"),
            cmd = e.target.getAttribute("cmd");
        if(typeof record !== 'undefined') {
            if (es_boton) {
                if (cmd == "editar") {
                    EditarQuizz(record);
                }
                if (cmd == "preguntas") {
                    ObtenerTablaPreguntas(record);
                    $('#modal_preguntas').modal('show');
                }
            }
        }
    });

    //Se crea la tabla con eyuda del plugin DataTable
    tabla_roulette_quiz = $('#tabla_roulette_quiz').DataTable({
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
            url: "../index.php/Games/RouletteQuiz",
            type: 'POST',
            data: function (d) {
                //d.token = "9C4F9Z4us1fcqBx8k5I5iwdaqjg8lknHgcvgrALYdsIMZiOEA9Yi9BxD0rlbAoZAQkPk9uzoz2N6ojAz1Wa04ielphCkkwvStit77jHIeuXp6PSZRIXITBLjGelCWXZj5FgcJP7CaJZrznGCcWlKrlbRq4ymMNFSivhCfQ364uH5ODE1SwwIWN8oUaGilv9DpyVQ7E5PhrTGyR2RNBFy14x7DEtyNiwzT3R8RJnymgJyeAlvgGgdzXyTynqOKqOxzfiHlSuI7Nllon7bCqk7URtkiTvJxCTi1u";
            },
            error: function (xhr, error, code){
                console.log(xhr)
                tabla_roulette_quiz.clear().draw();
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
                        '<a title="Ver preguntas" class="btn btn-info btn-xs editar mr-2 lead_0_8 text-white" cmd="preguntas"><i cmd="preguntas" class="fa fa-eye"></i></a>' +
                        '<a title="Editar" class="btn btn-primary btn-xs editar mr-2 lead_0_8 text-white" cmd="editar"><i cmd="editar" class="fa fa-edit"></i></a>' +
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
 *	Nota: funcion para editar un quizz, se carga y muestra el formulario
 ***********************************************************************/
function EditarQuizz(record) {
    $('#form_roulette_quiz').trigger("reset");
    $('#roulette_quiz_id').val(record.id);
    $('#roulette_quiz_name').val(record.name);
    $('#roulette_quiz_points').val(record.points);
    $('#modal_roulette_quiz').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar por peticion AJAX un quizz(agregar o editar)
 ***********************************************************************/
function GuardarQuizz() {
    var quiz_id = $('#roulette_quiz_id').val(),
        datos = {
            name : $('#roulette_quiz_name').val(),
            points : $('#roulette_quiz_points').val()
        },
        metodo="SaveRouletteQuiz";

    if(quiz_id != ''){
        datos.id = quiz_id;
        metodo = "EditRouletteQuiz"
    }

    //Cambio en los quiz, ocultamos los apartados dependientes: preguntas y respuestas
    $('#seccion_preguntas').addClass("d-none");
    if(tabla_roulette_questions)tabla_roulette_questions.clear().draw();
    $('#seccion_respuestas').addClass("d-none");
    if(tabla_roulette_answers)tabla_roulette_answers.clear().draw();

    $.ajax({
        url:  window.base_url+"Games/"+metodo,
        type: "POST",
        data: datos,
        cache: false,
        success: function(response){
            tabla_roulette_quiz.ajax.reload();
            $('#modal_roulette_quiz').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Catálogo quizz',
                text: 'El catálogo de quizz ha sido actualizado roulette_correctmente'
            });
        },
        error: function () {
            $('#modal_roulette_quiz').modal('hide');
            Swal.fire({
                type: 'error',
                title: 'Catálogo quizz',
                text: 'El quizz no se pudo guardar'
            });
        }
    });
}
/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: Creacion de la tabla para listar preguntas del quizz
 ***********************************************************************/
function ObtenerTablaPreguntas(record) {
    //Guardamos el quiz seleccionado en una variable
    $('#selected_quiz_id').val(record.id);
    //Mostramos la seccion de preguntas, para cargar las del quiz seleccionado
    $('#seccion_preguntas').removeClass("d-none");

    //Si la tabla de preguntas ya habia sido creada con anterioridad
    if(tabla_roulette_questions){
        //Hubo un cambio de pregunta, por lo tanto ocultamos y limpiamos la seccion de respuestas
        $('#seccion_respuestas').addClass("d-none");
        if(tabla_roulette_answers)tabla_roulette_answers.clear().draw();
        //Actualizamos la tabla de preguntas para recibir las preguntas del nuevo quiz seleccionado
        tabla_roulette_questions.ajax.reload();
        return;
    }

    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_roulette_questions').on('tbody click','tr',function(e){
        var record = tabla_roulette_questions.row(this).data(),
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
    tabla_roulette_questions = $('#tabla_roulette_questions').DataTable({
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
            url: "../index.php/games/RouletteQuestions",
            type: 'POST',
            data: function (d) {
                d.quiz_id = $('#selected_quiz_id').val();
            },
            error: function (xhr, error, code){
                tabla_roulette_questions.clear().draw();
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
            render: function ( data, type, row ) {
                var t = '';
                t = '' +
                    '<div class="pt-2 pb-2 text-center">' +
                    '<a title="Ver respuestas" class="btn btn-info btn-xs editar mr-2 lead_0_8 text-white" cmd="respuestas"><i cmd="respuestas" class="fa fa-Eye"></i></a>' +
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
    $('#form_roulette_question').trigger("reset");
    $('#roulette_question_id').val('');
    $('#modal_roulette_question').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para editar una pregunta, se carga y muestra el formulario
 ***********************************************************************/
function EditarPregunta(record) {
    $('#form_roulette_question').trigger("reset");
    $('#roulette_question_id').val(record.id);
    $('#roulette_question').val(record.question);
    $('#modal_roulette_question').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar por peticion AJAX una pregunta(agregar o editar)
 ***********************************************************************/
function GuardarPregunta() {
    var question_id = $('#roulette_question_id').val(),
        datos = {
            question : $('#roulette_question').val(),
            quiz_id : $('#selected_quiz_id').val()
        },
        metodo="SaveRouletteQuestion";

    if(question_id != ''){
        datos.id = question_id;
        metodo = "EditRouletteQuestion"
    }

    //Hubo un cambio en el listado de preguntas, ocultamos y limpiamos la seccion de respuestas
    $('#seccion_respuestas').addClass("d-none");
    if(tabla_roulette_answers)tabla_roulette_answers.clear().draw();
    $.ajax({
        url:  window.base_url+"Games/"+metodo,
        type: "POST",
        data: datos,
        cache: false,
        success: function(response){
            tabla_roulette_questions.ajax.reload();
            $('#modal_roulette_question').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Catálogo Preguntas',
                text: 'El catálogo de preguntas ha sido actualizado roulette_correctmente'
            });
        },
        error: function () {
            $('#modal_roulette_question').modal('hide');
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
        title: 'Catálogo quizz',
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
            if(tabla_roulette_answers)tabla_roulette_answers.clear().draw();
            $.ajax({
                url:  window.base_url+"Games/DeleteRouletteQuestion",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function(response){
                    tabla_roulette_questions.ajax.reload();
                    Swal.fire({
                        type: 'success',
                        title: 'Catálogo quizz',
                        text: 'El catálogo de preguntas ha sido actualizado roulette_correctmente'
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
    if(tabla_roulette_answers){
        tabla_roulette_answers.ajax.reload();
        return;
    }

    //Se agregan los eventos para los botones de "Editar" y "Eliminar" del listado
    $('#tabla_roulette_answers').on('tbody click','tr',function(e){
        var record = tabla_roulette_answers.row(this).data(),
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
    tabla_roulette_answers = $('#tabla_roulette_answers').DataTable({
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
            url: "../index.php/games/RouletteAnswers",
            type: 'POST',
            data: function (d) {
                d.question_id = $('#selected_question_id').val();
            },
            error: function (xhr, error, code){
                tabla_roulette_answers.clear().draw();
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
    $('#form_roulette_answer').trigger("reset");
    $('#roulette_answer_id').val('');
    $('#modal_roulette_answer').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para editar una respuesta, se carga y muestra el formulario
 ***********************************************************************/
function EditarRespuesta(record) {
    $('#form_roulette_answer').trigger("reset");
    $('#roulette_answer_id').val(record.id);
    $('#roulette_answer').val(record.answer);
    $('#roulette_correct').val(record.correct);
    //$('#roulette_answer').val(record.answer);
    $('#modal_roulette_answer').modal('show');
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 29/07/2021
 *	Nota: funcion para guardar por peticion AJAX una respuesta(agregar o editar)
 ***********************************************************************/
function GuardarRespuesta() {
    var answer_id = $('#roulette_answer_id').val(),
        datos = {
            answer : $('#roulette_answer').val(),
            correct : $('#roulette_correct').val(),
            question_id : $('#selected_question_id').val()
        },
        metodo="SaveRouletteAnswer";

    if(answer_id != ''){
        datos.id = answer_id;
        metodo = "EditRouletteAnswer"
    }

    $.ajax({
        url:  window.base_url+"Games/"+metodo,
        type: "POST",
        data: datos,
        cache: false,
        success: function(response){
            tabla_roulette_answers.ajax.reload();
            $('#modal_roulette_answer').modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Catálogo Respuestas',
                text: 'El catálogo de respuestas ha sido actualizado roulette_correctmente'
            });
        },
        error: function () {
            $('#modal_roulette_answer').modal('hide');
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
        title: 'Catálogo quizz',
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
                url:  window.base_url+"Games/DeleteRouletteAnswer",
                type: "POST",
                data: {
                    id: record.id
                },
                cache: false,
                success: function(response){
                    tabla_roulette_answers.ajax.reload();
                    Swal.fire({
                        type: 'success',
                        title: 'Catálogo quizz',
                        text: 'El catálogo de respuestas ha sido actualizado roulette_correctmente'
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

