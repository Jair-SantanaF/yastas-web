$(document).ready(function(){
    cargarFeedback(3);

    $(`#todos`).click(function () {
        cargarFeedback(3);
    });
    $(`#recibidos`).click(function () {
        cargarFeedback(1);
    });
    $(`#dados`).click(function () {
        cargarFeedback(2);
    });
});
function cargarFeedback(type){
    var datos = new FormData();

    datos.append('type',type);

    $("#contenedor_feedback").html("");
    var config = {
        url: window.base_url + "feedback/FeedbackList",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            var retros = response.data;
            $.each(retros, function (index,value) {
                crearElementoFeedback(value);
            });
            $("#total_feedback").html(response.extras.total_feedback);
            $("#total_aplausos").html(response.extras.total_like);
            return;
        },
        error: function (response) {
            $("#total_feedback").html("0");
            $("#total_aplausos").html("0");
            return;
            Swal.fire({
                type: 'error',
                title: 'Login',
                text: response.responseJSON.error_msg
            });
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
function crearElementoFeedback(feedback){
    var feedback_html = ``;

    feedback_html = `           
                <div class="w-100 mt-2 border-top elemento-feedback cursor-pointer" feedback_id="${feedback.id}">
                    <div class="w-100 d-flex mt-4">
                        <div class="p-2">
                            <img style="min-width:150px; width: 150px; height: 150px" class="img-fluid rounded-circle" src="${feedback.photo_user}" alt="Generic placeholder image">
                        </div>
                        <div class="w-100 d-flex pl-3">
                            <div class="align-self-center">
                                <div class="w-100"><h1> ${feedback.name_user} </h1></div>   
                                <div class="w-100 py-3">${feedback.job_name}</div>
                            </div>
                        </div>   
                        <div class="p-2">
                            <i style="width: 50px" class="fas fa-sign-language fa-3x"></i>
                            <div class="text-center">
                                ${feedback.total_like}
                            </div>
                        </div>               
                    </div>
                    <div class="w-100 mb-4">
                        <p class="mb-0"> ${feedback.description} </p>                        
                    </div>                                                                       
                </div>
    `;

    $("#contenedor_feedback").append(feedback_html);

    $(`.elemento-feedback`).click(function () {
        var feedback_id = $(this).attr("feedback_id");
        mostrarFeedback(feedback_id);
    });
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 22 Octubre 2020
 *	Nota: Funcion para finalizar el curso
 ***********************************************************************/
function mostrarFeedback(id_feedback){
    var url = window.base_url+"app/feedbackpost/"+id_feedback;
    location.href = url;
}