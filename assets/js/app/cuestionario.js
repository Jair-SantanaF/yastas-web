$(document).ready(function () {
    window.num_pregunta = 1;
    window.total_preguntas = 0;
    var datos = new FormData();
    //datos.append('token',window.token);
    datos.append('quiz_id', quiz_id);
    var config = {
        url: window.base_url+"/questions/ListQuestionsQuiz",
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
                switch (value.type_id) {
                    case 1:
                        value.indice = index_;
                        crearPreguntaTipoOpcionesMultiples(index_,value);
                        index_++;
                        window.total_preguntas++;
                        break;
                    case 2:
                        value.indice = index_;
                        crearPreguntaTipoOpcionUnica(index_,value);
                        index_++;
                        window.total_preguntas++;
                        break;
                    case 3:
                        value.indice = index_;
                        crearPreguntaTipoOpcionesMultiplesImagenes(index_,value);
                        index_++;
                        window.total_preguntas++;
                        break;
                    case 5:
                        value.indice = index_;
                        crearPreguntaTipoOpcionUnicaImagen(index_,value);
                        index_++;
                        window.total_preguntas++;
                        break;
                    case 8:
                        value.indice = index_;
                        crearPreguntaTipoLikeCaras(index_,value);
                        index_++;
                        window.total_preguntas++;
                        break;
                    case 9:
                        value.indice = index_;
                        crearPreguntaTipoProporciones(index_,value);
                        index_++;
                        window.total_preguntas++;
                        break;
                    case 13:
                        value.indice = index_;
                        crearPreguntaTipoAbierta(index_,value);
                        index_++;
                        window.total_preguntas++;
                        break;
                    case 14:
                        value.indice = index_;
                        crearPreguntaTipoLikeNumeros(index_,value);
                        index_++;
                        window.total_preguntas++;
                        break;
                    default:
                        //console.info("No soportado");
                        break;
                }
            });
        },
        error: function (response) {
            alert(response.responseJSON.error_msg);
        }
    }
    $.ajax(config);
});

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
            <div class="col-12 h5 contenedor_pregunta ${display}" id="pregunta_${index}" question_id="${pregunta.id}" type_id="${pregunta.type_id}">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="h5 align-self-center descripcion_pregunta">
                            ${pregunta.question}
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
            <div class="col-12 h5 contenedor_pregunta ${display}" id="pregunta_${index}" question_id="${pregunta.id}" type_id="${pregunta.type_id}">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="h5 align-self-center descripcion_pregunta">
                            ${pregunta.question}
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
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Crear el html de una pregunta TIPO = 3, Opción Multiple De IMAGENES Con Respuesta Múltiple
 ***********************************************************************/
function crearPreguntaTipoOpcionesMultiplesImagenes(index,pregunta){
    var respuestas = ``,
        pregunta_html = ``;

    for(var i = 0; i < pregunta.answers.length; i++){
        var respuesta = pregunta.answers[i];
        //imagen_local = URL.createObjectURL(partido.imagen_local);
        respuestas += `
            <div class="pt-3 col-xl-4 col-lg-6 col-sm-6 col-10">
                <div class="w-100 p-2 respuesta" answer_id="${respuesta.id}">
                    <img class="w-100" src="${respuesta.answer}">
                </div>
            </div>
        `;
    }
    let display = (index === 1)?'':'d-none';
    pregunta_html = `
            <div class="col-12 h5 contenedor_pregunta ${display}" id="pregunta_${index}" question_id="${pregunta.id}" type_id="${pregunta.type_id}">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="h5 align-self-center descripcion_pregunta">
                            ${pregunta.question}
                        </div>
                    </div>
                </div>
                <div class="row pt-4 pb-inferior">
                    <div class="col-12">
                        <div class="row justify-content-center">
                            ${respuestas}
                        </div>
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

    return;
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Obtener la respuesta de una pregunta TIPO = 3, Opción Multiple De IMAGENES Con Respuesta Múltiple
 ***********************************************************************/
function respuestaPreguntaTipoOpcionesMultiplesImagenes(pregunta){
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
 *	Nota: Crear el html de una pregunta TIPO = 5, Opción Multiple De IMAGENES Con Respuesta Única
 ***********************************************************************/
function crearPreguntaTipoOpcionUnicaImagen(index,pregunta){
    var respuestas = ``,
        pregunta_html = ``;

    for(var i = 0; i < pregunta.answers.length; i++){
        var respuesta = pregunta.answers[i];
        //imagen_local = URL.createObjectURL(partido.imagen_local);
        respuestas += `
            <div class="pt-3 col-xl-4 col-lg-6 col-sm-6 col-12">
                <div class="w-100 p-2 respuesta" answer_id="${respuesta.id}">
                    <img class="w-100" src="${respuesta.answer}">
                </div>
            </div>
        `;
    }
    let display = (index === 1)?'':'d-none';
    pregunta_html = `
            <div class="col-12 h5 contenedor_pregunta ${display}" id="pregunta_${index}" question_id="${pregunta.id}" type_id="${pregunta.type_id}">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="h5 align-self-center descripcion_pregunta">
                            ${pregunta.question}
                        </div>
                    </div>
                </div>
                <div class="row pt-4 pb-inferior">
                    <div class="col-12">
                        <div class="row justify-content-center">
                            ${respuestas}
                        </div>
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

    return;
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Obtener la respuesta de una pregunta TIPO = 5, Opción Multiple De IMAGENES Con Respuesta Única
 ***********************************************************************/
function respuestaPreguntaTipoOpcionUnicaImagen(pregunta){
    var respuestas_usuario="";
    $(`.contenedor_pregunta[question_id="${pregunta.question_id}"] .respuesta_usuario`).map(function(i) {
        respuestas_usuario = respuestas_usuario + ($(this).attr("answer_id"))+',';
    });
    return respuestas_usuario.slice(0, -1);
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Crear el html de una pregunta TIPO = 8, Opción Multiple De CARAS Con Respuesta Única
 ***********************************************************************/
function crearPreguntaTipoLikeCaras(index,pregunta){
    var pregunta_html = ``;
    let display = (index === 1)?'':'d-none';
    pregunta_html = `
            <div class="col-12 h5 contenedor_pregunta ${display}" id="pregunta_${index}" question_id="${pregunta.id}" type_id="${pregunta.type_id}">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="h5 align-self-center descripcion_pregunta">
                            ${pregunta.question}
                        </div>
                    </div>
                </div>
                <div class="row pt-4 pb-inferior">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            <div class="d-flex justify-content-center contenedor-respuestas">
                                <div class="py-2 px-1 px-sm-2">
                                    <div class="d-flex justify-content-center respuesta" answer_id="1">
                                    </div>
                                </div>
                                <div class="py-2 px-1 px-sm-2">
                                    <div class="d-flex justify-content-center respuesta" answer_id="2">
                                    </div>
                                </div>
                                <div class="py-2 px-1 px-sm-2">
                                    <div class="d-flex justify-content-center respuesta" answer_id="3">
                                    </div>
                                </div>
                                <div class="py-2 px-1 px-sm-2">
                                    <div class="d-flex justify-content-center respuesta" answer_id="4">
                                    </div>
                                </div>
                                <div class="py-2 px-1 px-sm-2">
                                    <div class="d-flex justify-content-center respuesta" answer_id="5">
                                    </div>
                                </div>
                            </div>
                        </div>
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

    return;
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Obtener la respuesta de una pregunta TIPO = 8, Opción Multiple De CARAS Con Respuesta Única
 ***********************************************************************/
function respuestaPreguntaTipoLikeCaras(pregunta){
    var respuestas_usuario="";
    $(`.contenedor_pregunta[question_id="${pregunta.question_id}"] .respuesta_usuario`).map(function(i) {
        respuestas_usuario = respuestas_usuario + ($(this).attr("answer_id"))+',';
    });
    return respuestas_usuario.slice(0, -1);
}

/*function crearPreguntaTipo81(pregunta){
    var pregunta_html = ``;
    let display = (index === 1)?'':'d-none';
    pregunta_html = `
            <div class="col-12 h5 contenedor_pregunta ${display}" id=pregunta_${index} question_id="${pregunta.id}" type_id="${pregunta.type_id}">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="h5 align-self-center descripcion_pregunta">
                            ${pregunta.question}
                        </div>
                    </div>
                </div>
                <div class="row pt-4 pb-inferior">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            <div class="d-flex justify-content-center contenedor-respuestas">
                                <div class="py-2 px-1 px-sm-2">
                                    <div class="d-flex justify-content-center respuesta" answer_id="1">
                                    </div>
                                </div>
                                <div class="py-2 px-1 px-sm-2">
                                    <div class="d-flex justify-content-center respuesta" answer_id="2">
                                    </div>
                                </div>
                                <div class="py-2 px-1 px-sm-2">
                                    <div class="d-flex justify-content-center respuesta" answer_id="3">
                                    </div>
                                </div>
                                <div class="py-2 px-1 px-sm-2">
                                    <div class="d-flex justify-content-center respuesta" answer_id="4">
                                    </div>
                                </div>
                                <div class="py-2 px-1 px-sm-2">
                                    <div class="d-flex justify-content-center respuesta" answer_id="5">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    `;

    $("#contenedor_preguntas").append(pregunta_html);

    $(`.contenedor_pregunta[question_id="${pregunta.id}"] .respuesta`).mouseenter(function () {
        var answer_id = $(this).attr("answer_id");

        answer_id = parseInt(answer_id);

        for(var i = 1; i<=5; i++ ){
            if(i<=answer_id){
                $(`.contenedor_pregunta[question_id="${pregunta.id}"] .respuesta[answer_id="${i}"]`).addClass("hover");
            }else{
                $(`.contenedor_pregunta[question_id="${pregunta.id}"] .respuesta[answer_id="${i}"]`).removeClass("hover");
            }
        }
        return;
    });

    $(`.contenedor_pregunta[question_id="${pregunta.id}"] .contenedor-respuestas`).mouseleave(function () {
        var answer_id = $(this).attr("answer_id");

        for(var i = 1; i<=5; i++ ){
            $(`.contenedor_pregunta[question_id="${pregunta.id}"] .respuesta[answer_id="${i}"]`).removeClass("hover");
        }
        return;
    });

    $(`.contenedor_pregunta[question_id="${pregunta.id}"] .respuesta`).click(function () {
        var answer_id = $(this).attr("answer_id");

        answer_id = parseInt(answer_id);

        for(var i = 1; i<=5; i++ ){
            if(i<=answer_id){
                $(`.contenedor_pregunta[question_id="${pregunta.id}"] .respuesta[answer_id="${i}"]`).addClass("respuesta_usuario");
            }else{
                $(`.contenedor_pregunta[question_id="${pregunta.id}"] .respuesta[answer_id="${i}"]`).removeClass("respuesta_usuario");
            }
        }
        return;
    });

    return;
}
function respuestaPreguntaTipo81(pregunta){
    var respuestas_usuario = 0;
    $(`.contenedor_pregunta[question_id="${pregunta.id}"] .respuesta_usuario`).map(function(i) {
        var answer_id = $(this).attr("answer_id");
        answer_id = parseInt(answer_id);
        if(respuestas_usuario<answer_id){
            respuestas_usuario = answer_id;
        }
    });
    if(respuestas_usuario == 0){return "";}else{return respuestas_usuario};
}*/

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Crear el html de una pregunta TIPO = 9, Captura de Porcentaje
 ***********************************************************************/
function crearPreguntaTipoProporciones(index,pregunta){
    var pregunta_html = ``;
    let display = (index === 1)?'':'d-none';
    pregunta_html = `
            <div class="col-12 h5 contenedor_pregunta ${display}" id="pregunta_${index}" question_id="${pregunta.id}" type_id="${pregunta.type_id}">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="h5 align-self-center descripcion_pregunta">
                            ${pregunta.question}
                        </div>
                    </div>
                </div>
                <div class="row pt-4 pb-inferior">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            <div id="slider_${pregunta.id}"></div>
                        </div>
                    </div>
                </div>
            </div>
    `;

    $("#contenedor_preguntas").append(pregunta_html);

    //Crear componente para captura de porcentaje
    //https://roundsliderui.com/
    $(`#slider_${pregunta.id}`).roundSlider({
        sliderType: "min-range",
        circleShape: "pie",
        startAngle: "315",
        lineCap: "round",
        radius: 130,
        width: 20,
        min: 0,
        max: 100,
        svgMode: true,
        pathColor: "#eee",//"#A8A8A8",//
        borderWidth: 0,
        //startValue: 0,
        valueChange: function (e) {
            var color = "#808080"; //e.isInvertedRange ? "#FF5722" : "#8BC34A";
            $(`#slider_${pregunta.id}`).roundSlider({ "rangeColor": color, "tooltipColor": color });
        }
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Obtener la respuesta de una pregunta TIPO = 9, Captura de Porcentaje
 ***********************************************************************/
function respuestaPreguntaTipoProporciones(pregunta){
    var respuestas_usuario = "",
        sliderObj  = $(`#slider_${pregunta.question_id}`).data("roundSlider");
    console.log(sliderObj);
    respuestas_usuario = sliderObj.getValue();

    return respuestas_usuario;
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Crear el html de una pregunta TIPO = 13, Respuesta De Texto ABIERTA
 ***********************************************************************/
function crearPreguntaTipoAbierta(index,pregunta){
    var pregunta_html = ``;
    let display = (index === 1)?'':'d-none';
    pregunta_html = `
            <div class="col-12 h5 contenedor_pregunta ${display}" id="pregunta_${index}" question_id="${pregunta.id}" type_id="${pregunta.type_id}">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="h5 align-self-center descripcion_pregunta">
                            ${pregunta.question}
                        </div>
                    </div>
                </div>
                <div class="row pt-4 pb-inferior">
                    <div class="col-12">
                        <textarea class="w-100 respuesta_usuario" rows=10></textarea>
                    </div>
                </div>
            </div>
    `;

    $("#contenedor_preguntas").append(pregunta_html);
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Obtener la respuesta de una pregunta TIPO = 13, Respuesta De Texto ABIERTA
 ***********************************************************************/
function respuestaPreguntaTipoAbierta(pregunta){
    var respuestas_usuario = "";
    respuestas_usuario = $(`.contenedor_pregunta[question_id="${pregunta.question_id}"] .respuesta_usuario`).val();
    return respuestas_usuario;
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Crear el html de una pregunta TIPO = 14, Opción Múltiple de NUMEROS Con Respuesa Única
 ***********************************************************************/
function crearPreguntaTipoLikeNumeros(index,pregunta){
    var respuestas = ``,
        pregunta_html = ``;
    let display = (index === 1)?'':'d-none';
    pregunta_html = `
            <div class="col-12 h5 contenedor_pregunta ${display}" id="pregunta_${index}" question_id="${pregunta.id}" type_id="${pregunta.type_id}">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="h5 align-self-center descripcion_pregunta">
                            ${pregunta.question}
                        </div>
                    </div>
                </div>
                <div class="row pt-4 pb-inferior">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            <div class="py-2 px-1 px-sm-2">
                                <div class="d-flex justify-content-center respuesta" answer_id="1"><div class="align-self-center">1</div></div>
                            </div>
                            <div class="py-2 px-1 px-sm-2">
                                <div class="d-flex justify-content-center respuesta" answer_id="2"><div class="align-self-center">2</div></div>
                            </div>
                            <div class="py-2 px-1 px-sm-2">
                                <div class="d-flex justify-content-center respuesta" answer_id="3"><div class="align-self-center">3</div></div>
                            </div>
                            <div class="py-2 px-1 px-sm-2">
                                <div class="d-flex justify-content-center respuesta" answer_id="4"><div class="align-self-center">4</div></div>
                            </div>
                            <div class="py-2 px-1 px-sm-2">
                                <div class="d-flex justify-content-center respuesta" answer_id="5"><div class="align-self-center">5</div></div>
                            </div>
                        </div>
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

    return;
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Obtener la respuesta de una pregunta TIPO = 14, Opción Múltiple de NUMEROS Con Respuesa Única
 ***********************************************************************/
function respuestaPreguntaTipoLikeNumeros(pregunta){
    var respuestas_usuario="";
    $(`.contenedor_pregunta[question_id="${pregunta.question_id}"] .respuesta_usuario`).map(function(i) {
        respuestas_usuario = respuestas_usuario + ($(this).attr("answer_id"))+',';
    });
    return respuestas_usuario.slice(0, -1);
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Obtener las respuestas de todas las preguntas respondidas
 ***********************************************************************/
function respuestaPregunta(index){

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
            question_id: $(`#pregunta_${window.num_pregunta}`).attr("question_id"),
            type_id: parseInt($(`#pregunta_${window.num_pregunta}`).attr("type_id"))
        };

    switch (pregunta.type_id) {
        case 1:
            pregunta.answer = respuestaPreguntaTipoOpcionesMultiples(pregunta);
            break;
        case 2:
            pregunta.answer = respuestaPreguntaTipoOpcionUnica(pregunta);
            break;
        case 3:
            pregunta.answer = respuestaPreguntaTipoOpcionesMultiplesImagenes(pregunta);
            break;
        case 5:
            pregunta.answer = respuestaPreguntaTipoOpcionUnicaImagen(pregunta);
            break;
        case 8:
            pregunta.answer = respuestaPreguntaTipoLikeCaras(pregunta);
            break;
        case 9:
            pregunta.answer = respuestaPreguntaTipoProporciones(pregunta);
            break;
        case 13:
            pregunta.answer = respuestaPreguntaTipoAbierta(pregunta);
            break;
        case 14:
            pregunta.answer = respuestaPreguntaTipoLikeNumeros(pregunta);
            break;
        default:
            break;
    }

    if(!pregunta.answer){
        Swal.fire({
            type: 'error',
            title: 'Cuestionario',
            text: 'Por favor responde la pregunta',
        }).then((result) => {
            $(`#btn_siguiente`).attr("disabled", false);
        });
        return;
    }

    datos.append('answer', pregunta.answer);
    datos.append('question_id',pregunta.question_id);
    datos.append('type_id',pregunta.type_id);

    var config = {
        url:window.base_url+"questions/SaveAnswerUser",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            if(window.total_preguntas === window.num_pregunta){
                Swal.fire({
                    type: 'success',
                    title: 'Cuestionario',
                    text: 'El cuestionario ha sido contestado correctamente.',
                }).then((result) => {
                    var url = window.base_url+"app/cuestionarios";
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
