$(document).ready(function () {
    cargarQuiz();
});
function cargarQuiz(){
    window.num_step = 1;
    window.num_quiz = 1;
    window.total_quiz = 0;
    var datos = new FormData();
    var config = {
        url: window.base_url+"/games/ProductQuiz",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            var quizes = response.data, index_=1;
            $.each(quizes, function (index,quiz) {
                quiz.indice = index_;
                crearElementoQuiz(index_, quiz);
                index_++;
                window.total_quiz++;
            });
console.info(quizes);
            if(quizes.length > 0){
                cargarPasos();
            }
        },
        error: function (response) {
            alert(response.responseJSON.error_msg);
        }
    }
    $.ajax(config);
}

function cargarPasos(){
    var datos = new FormData();
    var quiz_id = $("#quiz_"+window.num_quiz).attr("quiz_id");
    datos.append("quiz_id", quiz_id);

    var config = {
        url: window.base_url+"/games/ProductSteps_",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            var pasos = response.data;
            crearPasos(pasos);
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
 *	Nota: Crear el html de una pregunta TIPO = 2, Opción Multiple Con Respuesta Única
 ***********************************************************************/
function crearElementoQuiz(index, quiz){
    var quiz_html = ``;

    let display = (index === 1)?'':'d-none';
    quiz_html = `
            <div class="col-12 h5 contenedor_quiz_products ${display}" id="quiz_${index}" quiz_id="${quiz.id}">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="h5 align-self-center descripcion_quiz">
                            ${quiz.description}
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center pt-3">
                    <div class="col-xl-5 col-lg-6 col-md-7 col-sm-8 col-12">
                        <div class="d-flex justify-content-center">
                            <div class="p-3">
                                <div class="d-flex justify-content-center droppable paso" num_step="1">
                                    <div class="align-self-center h2">
                                        1
                                    </div>
                                </div>                                
                            </div>
                            <div class="p-3">
                                <div class="d-flex justify-content-center droppable paso" num_step="2">
                                    <div class="align-self-center h2">
                                        2
                                    </div>
                                </div>                                
                            </div>
                            <div class="p-3">
                                <div class="d-flex justify-content-center droppable paso" num_step="3">
                                    <div class="align-self-center h2">
                                        3
                                    </div>
                                </div>                                
                            </div>                            
                        </div>
                    </div>
                </div>
                <div class="row pt-3">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="align-self-center small">
                            Selecciona y arrastra el paso siguiente
                        </div>
                    </div>
                </div>
                <div class="row pt-4 pb-inferior">
                    <div class="col-12 contenedor-pasos" >                        
                    </div>
                </div>
            </div>
    `;

    $("#contenedor_quiz").append(quiz_html);

    $(".droppable").droppable({
        drop: function( event, ui ) {
            var step_id = ui.draggable.attr("step_id"),
                num_step = $( this ).attr("num_step");

            ui.draggable.css("left",0);
            ui.draggable.css("top",0);
            if(num_step != window.num_step){
                return;
            }

            //borrar en el destino

            /*
            var paso_borrado = $(`.paso_seleccionado[step_id="${step_id}"]`);
            if(paso_borrado){
                paso_borrado.attr("step_id", "");
                paso_borrado.removeClass("paso_seleccionado");
            }

            //borrar en la fuente

            var step_id_respuesta_borrada = $( this ).attr("step_id"),
                respuesta_borrada = $(`.respuesta[step_id="${step_id_respuesta_borrada}"]`);
            if(respuesta_borrada){
                respuesta_borrada.removeClass("respuesta_usuario");
            }
            */

            $( this ).attr("step_id", step_id);
            $( this ).addClass("paso_seleccionado");

            ui.draggable.addClass("d-none");
            //ui.draggable.addClass("respuesta_usuario");
            window.num_step++;
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Crear el html de una pregunta TIPO = 2, Opción Multiple Con Respuesta Única
 ***********************************************************************/
function crearPasos(pasos){
    var pasos_html = ``;

    for(var i = 0; i < pasos.length; i++){
        var paso = pasos[i];
        pasos_html += `
            <div class="d-flex mb-2" >
                <div class="" style="width: 50px">
                    <div class="draggable cursor-pointer respuesta" style="height: auto; width: auto; margin: 0" step_id="${paso.options[0].id}">
                        <i class="far fa-hand-paper "></i>
                    </div>
                </div>           
                <div class="w-100 p-2 option_description" step_id="${paso.options[0].id}" >
                    ${paso.options[0].option_description}
                </div>
            </div>
        `;
    }

    $(`#quiz_${window.num_quiz}`).append(pasos_html);
    $( ".draggable" ).draggable({
        revert: "invalid",
        activate( event, ui ){

        },
        start: function( event, ui ) {
            var paso = $(`#quiz_${window.num_quiz} .paso[num_step="${window.num_step}"]`);
            console.info(paso);
            paso.addClass("paso_resaltado");
        },
        stop: function( event, ui ) {
            var paso = $(`#quiz_${window.num_quiz} .paso[num_step="${window.num_step}"]`);
            paso.removeClass("paso_resaltado");
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
    if(window.total_quiz === window.num_quiz){
        Swal.fire({
            type: 'success',
            title: 'Quiz',
            text: 'El quiz ha sido contestado correctamente.',
        }).then((result) => {
            var url = window.base_url+"app/games";
            location.href = url;
        });
    }else{

        var pasos = $(`#quiz_${window.num_quiz} .paso`);
        for(var i = 0; i < pasos.length; i++){
            var objeto = {
                step_id: $(pasos[i]).attr("step_id"),
                step_select: $(pasos[i]).attr("num_step"),
                quiz_id: $(`#quiz_${window.num_quiz}`).attr("quiz_id")
            }
            guardarRespuesta(objeto);
        }

        //Avanzar al siguiente
        $('.contenedor_quiz_products').addClass("d-none");
        window.num_quiz++;
        window.num_step=1;
        cargarPasos();
        $('#quiz_'+window.num_quiz).removeClass("d-none");
    }
    return;

    $(`#btn_siguiente`).attr("disabled", true);
    var datos = new FormData(),
        pregunta = {
            question_id: $(`#pregunta_${window.num_quiz}`).attr("question_id")
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
            if(window.total_quiz === window.num_quiz){
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
                window.num_quiz++;
                $('#pregunta_'+window.num_pregunta).removeClass("d-none");
                $(`#btn_siguiente`).attr("disabled", false);
            }
        },
        error: function (response) {
        }
    }
    $.ajax(config);
}

function guardarRespuesta(objeto){
    paso = $( this ).attr("num_step"),
        quiz_id = $(`#quiz_${window.num_quiz}`).attr("quiz_id"),
        datos = new FormData();

    datos.append('quiz_id', objeto.quiz_id);
    datos.append('step_id', objeto.step_id);
    datos.append('step_select', objeto.step_select);

    var config = {
        url:window.base_url+"games/SaveAnswerProducts_",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
        },
        error: function (response) {
        }
    }
    $.ajax(config);
}
