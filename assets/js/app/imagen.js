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
                var elemento = elementos[0];

                var elemento_html =`<img class="w-100" alt="preview" src="${elemento.file}">`;

                $("#titulo").text(elemento.title);
                $("#contenedor_imagen").append(elemento_html);
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