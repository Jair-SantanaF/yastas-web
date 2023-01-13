$(document).ready(function () {
    cargarQuiz();
});
function cargarQuiz(){
    window.num_pregunta = 1;
    window.total_preguntas = 0;
    var datos = new FormData();
    datos.append('quiz_id', quiz_id);
    var config = {
        url: window.base_url+"/games/ProfilerQuestionsAnswer",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            var cuestionario = response.data;
            var pregunta_html = "",
                index_=1;
            $.each(cuestionario, function (index,value) {
                value.indice = index_;
                crearPreguntaTipoOpcionUnica(index_,value);
                index_++;
                window.total_preguntas++;

                //value.indice = index_;
                //crearPreguntaTipoOpcionUnica(index_,value);
                //index_++;
                //window.total_preguntas++;
            });
        },
        error: function (response) {
            alert(response.responseJSON.error_msg);
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Crear el html de una pregunta TIPO = 1, Opción Multiple Con Multiples Respuestas
 ***********************************************************************/
function crearPreguntaTipoOpcionesMultiples(index,pregunta){
    var respuestas = ``,
        pregunta_html = ``;

    for(var i = 0; i < pregunta.answers.length; i++){
        var respuesta = pregunta.answers[i];
        respuestas += `
            <div class="row h5 align-self-center p-2 respuesta" answer_id="${respuesta.id}">
                ${respuesta.answer}
            </div>
        `;
    }
    let display = (index === 1)?'':'d-none';
    pregunta_html = `
            <div class="col-12 h5 contenedor_pregunta ${display}" id="pregunta_${index}" question_id="${pregunta.id}" type_id="1">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="h5 align-self-center descripcion_pregunta">
                            ${pregunta.question}
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center pt-3">
                    <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8 col-12">
                        <img class="w-100" src="${pregunta.image}">
                    </div>
                </div>
                <div class="row pt-3">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="align-self-center small">
                            Toma tu decisión
                        </div>
                    </div>
                </div>
                <div class="row pt-4 pb-inferior">
                    <div class="col-12">
                        ${respuestas}
                    </div>
                </div>
            </div>
    `;

    $("#contenedor_preguntas").append(pregunta_html);

    //Agregar evento cuando el usuario responde la pregunta
    $(`.contenedor_pregunta[question_id="${pregunta.id}"] .respuesta`).click(function () {
        var respuesta_usuario = $(this).hasClass("respuesta_usuario");

        if(respuesta_usuario){
            $(this).removeClass("respuesta_usuario");
        }else{
            $(this).addClass("respuesta_usuario");
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Obtener la respuesta de una pregunta TIPO = 1, Opción Multiple Con Multiples Respuestas
 ***********************************************************************/
function respuestaPreguntaTipoOpcionesMultiples(pregunta){
    var respuestas_usuario = '';
    $(`.contenedor_pregunta[question_id="${pregunta.question_id}"] .respuesta_usuario`).map(function(i) {
        respuestas_usuario = respuestas_usuario + ($(this).attr("answer_id"))+',';
    });
    return respuestas_usuario.slice(0, -1);
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Crear el html de una pregunta TIPO = 2, Opción Multiple Con Respuesta Única
 ***********************************************************************/
function crearPreguntaTipoOpcionUnica(index,pregunta){
    var respuestas = ``,
        pregunta_html = ``;

    for(var i = 0; i < pregunta.answers.length; i++){
        var respuesta = pregunta.answers[i];
        respuestas += `
            <div class="row h5 align-self-center p-2 respuesta" answer_id="${respuesta.id}">
                ${respuesta.answer}
            </div>
        `;
    }
    let display = (index === 1)?'':'d-none';
    pregunta_html = `
            <div class="col-12 h5 contenedor_pregunta ${display}" id="pregunta_${index}" question_id="${pregunta.id}" type_id="2">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="h5 align-self-center descripcion_pregunta">
                            ${pregunta.question}
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center pt-3">
                    <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8 col-12">
                        <img class="w-100" src="${pregunta.image}">
                    </div>
                </div>
                <div class="row pt-3">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="align-self-center small">
                            Toma tu decisión
                        </div>
                    </div>
                </div>
                <div class="row pt-4 pb-inferior">
                    <div class="col-12">
                        ${respuestas}
                    </div>
                </div>
            </div>
    `;

    $("#contenedor_preguntas").append(pregunta_html);

    //Agregar evento cuando el usuario responde la pregunta
    $(`.contenedor_pregunta[question_id="${pregunta.id}"] .respuesta`).click(function () {
        var respuesta_usuario = $(this).hasClass("respuesta_usuario");

        if(respuesta_usuario){
            $(this).removeClass("respuesta_usuario");
        }else{
            $(`.contenedor_pregunta[question_id="${pregunta.id}"] .respuesta_usuario`).map(function(i) {
                $(this).removeClass("respuesta_usuario");
            });
            $(this).addClass("respuesta_usuario");
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Obtener la respuesta de una pregunta TIPO = 2, Opción Multiple Con Respuesta Única
 ***********************************************************************/
function respuestaPreguntaTipoOpcionUnica(pregunta){
    var respuestas_usuario="";
    $(`.contenedor_pregunta[question_id="${pregunta.question_id}"] .respuesta_usuario`).map(function(i) {
        respuestas_usuario = respuestas_usuario + ($(this).attr("answer_id"))+',';
    });
    return respuestas_usuario.slice(0, -1);
}

/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández
 *		   mario.martinez.f@hotmail.es
 *	Fecha: 20/11/2019
 *	Nota: Funcion para siguiente en la navegacion
 ***********************************************************************/
function siguienteEvaluacion() {
    $(`#btn_siguiente`).attr("disabled", true);
    var datos = new FormData(),
        pregunta = {
            question_id: $(`#pregunta_${window.num_pregunta}`).attr("question_id")
        };

    //pregunta.answer_id = respuestaPreguntaTipoOpcionesMultiples(pregunta);
    pregunta.answer_id = respuestaPreguntaTipoOpcionUnica(pregunta);

    if(!pregunta.answer_id){
        Swal.fire({
            type: 'error',
            title: 'Cuestionario',
            text: 'Por favor responde la pregunta',
        }).then((result) => {
            $(`#btn_siguiente`).attr("disabled", false);
        });
        return;
    }

    datos.append('answer_id', pregunta.answer_id);
    datos.append('question_id',pregunta.question_id);

    var config = {
        url:window.base_url+"games/SaveAnswerProfiler",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            if(window.total_preguntas === window.num_pregunta){
                Swal.fire({
                    type: 'success',
                    title: 'Quiz',
                    text: 'El quiz ha sido contestado correctamente.',
                }).then((result) => {
                    var url = window.base_url+"app/games";
                    location.href = url;
                });
            }else{
                $('.contenedor_pregunta').addClass("d-none");
                window.num_pregunta++;
                $('#pregunta_'+window.num_pregunta).removeClass("d-none");
                $(`#btn_siguiente`).attr("disabled", false);
            }
        },
        error: function (response) {
        }
    }
    $.ajax(config);
}
