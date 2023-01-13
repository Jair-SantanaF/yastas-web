$(document).ready(function(){
    var datos = new FormData();
    datos.append('category_id',1);
    var config = {
        url: window.base_url + "questions/ListQuiz",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            var cuestionarios = response.data;
            $.each(cuestionarios, function (index,value) {
                crearElementoCuestionario(value);
            });
            return;
        },
        error: function (response) {
            return;
            Swal.fire({
                type: 'error',
                title: 'Login',
                text: response.responseJSON.error_msg
            });
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
function crearElementoCuestionario(cuestionario){
    var cuestionario_html = ``;

    cuestionario_html = `
            <div class="row pt-5">
                <div class="col-lg-2 col-md-2 col-sm-2 col-3">
                    <div class="row">
                        <img class="card-img" src="${window.base_url+'assets/img/Agro-Cortes-111.png'}">                        
                    </div>
                </div>
                <div class="col-lg-8 col-md-7 col-sm-10 col-9 d-flex">
                    <div class="h3 align-self-center pl-4">
                         ${cuestionario.name}
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12 col-12 d-flex">
                    <div class="w-100 align-self-center">
                        <div class="row justify-content-end">
                            <div class="col-md-12 col-sm-4 col-5">
                                <button class="btn btn-red btn-block" onclick="mostrarCuestionario(${cuestionario.id})">Ver más</button>                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    `;

    $("#contenedor_cuestionarios").append(cuestionario_html);
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 22 Octubre 2020
 *	Nota: Funcion para finalizar el curso
 ***********************************************************************/
function mostrarCuestionario(id_cuestionario){
    var url = window.base_url+"app/cuestionario/"+id_cuestionario;
    location.href = url;
}