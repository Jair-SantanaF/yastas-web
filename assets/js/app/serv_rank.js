var agenda = null;
$(document).ready(function(){var datos = new FormData();
    var config = {
        url: window.base_url + "ws/getAllServices",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            var respuesta = response.data;
            $.each(respuesta, function (index,value) {
                if(value.id == 9){
                    agenda = value;
                }
            });
            if(agenda){

            }else{
                $(`#addCartItem`).hide();
            }
            return;
        },
        error: function (response) {
            return;
            Swal.fire({
                type: 'error',
                title: 'Servicios',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);

    $(`#addCartItem`).click(function () {
        addCartItem();
    });
});

function addCartItem(){
    var datos = new FormData();

    if(agenda){
        datos.append("service_id", agenda.id);
        datos.append("quantity", 1);
    }else{
        return;
    }

    var config = {
        url: window.base_url + "ws/addItemShoppinCart",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            var respuesta = response.data;
            Swal.fire({
                type: 'success',
                title: 'Servicios',
                text: 'El ítem se agregó correctamente'
            });
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Servicios',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
}