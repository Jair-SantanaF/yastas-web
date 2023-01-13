$(document).ready(function(){
    var datos = new FormData();
    var config = {
        url: window.base_url + "ws/getShoppingCart",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            var respuesta = response.data;
            $.each(respuesta.cart_items, function (index,value) {
                crearElementoCart(value);
            });

            $(`#number_items`).html(respuesta.cart_items.length);
            $(`#cart_total`).html(respuesta.cart_total);

            //Agregar evento cuando el usuario responde la pregunta
            $(`.cart-item .delete-item`).click(function () {
                var item_id = $(this).parent().parent().parent().attr("item_id")
                eliminarElemento(item_id);
            });
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

    //Agregar evento cuando el usuario responde la pregunta
    $(`#pagar`).click(function () {
        pagar();
    });
});

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 23/09/2020
 *	Nota: Crear el html de una pregunta TIPO = 1, Opción Multiple Con Multiples Respuestas
 ***********************************************************************/
function crearElementoCart(elemento){
    var elemento_html = ``;

    elemento_html = `
        <div class="media border-bottom mt-2 cart-item" item_id="${elemento.id}">
            <img class="img-fluid mr-3" src="${window.base_url+'uploads/services/'+elemento.image}" style="height: 100px;">
            <div class="media-body">
                <h5 class="mt-0">${elemento.service_name}</h5>
            </div>
            <div class="media-body">
                <span>${elemento.price}</span>
            </div>
            <div class="media-body">
                <span><i class="far fa-times-circle cursor-pointer delete-item"></i></span>
            </div>
        </div>
    `;
    $("#cart_items").append(elemento_html);
}

/***********************************************************************
 *	Autor: Uriel Sánchez Cervantes
 *		   urisancer@gmail.com
 *	Fecha: 22 Octubre 2020
 *	Nota: Funcion para finalizar el curso
 ***********************************************************************/
function mostrarCart(id_elemento){
    var url = window.base_url+"app/elemento/"+id_elemento;
    location.href = url;
}

function eliminarElemento(item_id){
    Swal.fire({
        title: 'Cart',
        text: "¿Estás seguro que deseas eliminar este servicio?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            var datos = new FormData();
            datos.append('item_id',item_id);
            var config = {
                url: window.base_url + "ws/deleteItemShoppingCart",
                type: "POST",
                cache: false,
                contentType:false,
                processData: false,
                data: datos,
                success: function(response) {
                    Swal.fire({
                        type: 'success',
                        title: 'Cart',
                        text: response.msg
                    });
                },
                error: function (response) {
                    Swal.fire({
                        type: 'error',
                        title: 'Eventos',
                        text: response.responseJSON.error_msg
                    });
                }
            }
            $.ajax(config);
        }
    });
}

function pagar(){
    Swal.fire({
        title: 'Cart',
        text: "¿Estás seguro que deseas completar la compra?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            var datos = new FormData();
            var config = {
                url: window.base_url + "ws/SendPurchase",
                type: "POST",
                cache: false,
                contentType:false,
                processData: false,
                data: datos,
                success: function(response) {
                    Swal.fire({
                        type: 'success',
                        title: 'Cart',
                        text: response.msg
                    });
                },
                error: function (response) {
                    Swal.fire({
                        type: 'error',
                        title: 'Cart',
                        text: response.responseJSON.error_msg
                    });
                }
            }
            $.ajax(config);
        }
    });
}