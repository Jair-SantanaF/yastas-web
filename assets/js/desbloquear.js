jQuery(document).ready(function ($) {

})

function desbloquearCuenta() {
    // console.log(email)
    $.ajax({
        url: window.base_url + "Ws/desbloquearCuenta",
        type: "POST",
        data: { email: email },
        dataType: 'json',
        success: function (response) {
            console.log(response)
            alert("La cuenta se ha desbloqueado.")
        },
        error: function (error) {
            console.log(error)
            subcategory_id = '';
        }
    });
}