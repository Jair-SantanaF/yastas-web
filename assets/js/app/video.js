$(document).ready(function(){
    Libreria();
});
function Libreria(){
    var datos = new FormData();

    datos.append('id', id_elemento);
    
    var config = {
        url: window.base_url + "library/ListLibrary",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            var elementos = response.data;

            if(elementos.length != 0){
                var elemento = elementos[0],
                    elemento_html =`
                    `;

                switch (elemento.type_video) {
                    case "servidor":
                        elemento_html = `
                        <video style="width: 100%" controls>
                            <source src="${elemento.video}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        `;
                        break;
                    case "youtube":
                        elemento_html = `
                        <div class="embed-container">                
                            <iframe src="https://www.youtube.com/embed/${elemento.video_id}" frameborder="0"></iframe>
                        </div>
                        `;
                        break;
                    case "vimeo":
                        elemento_html = `
                        <div class="embed-container">
                            <iframe src="https://player.vimeo.com/video/${elemento.video_id}" frameborder="0"></iframe>
                        </div>
                        `;
                        break;
                }
                $("#titulo").text(elemento.title);
                $("#contenedor_video").append(elemento_html);
            }
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
}