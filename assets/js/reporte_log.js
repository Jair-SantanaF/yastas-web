jQuery(document).ready(function ($) {

})

function buscar_reporte() {
    var fecha = get("fecha").value;
    console.log(fecha)
    var arr = fecha.split("-");
    var a = arr[0]
    var m = arr[1]
    var d = arr[2]
    fecha = d + '_' + m + '_' + a
    console.log(fecha)
    $.ajax({
        url: window.base_url + "ws/reporteEliminados",
        type: "POST",
        data: { fecha: fecha },
        cache: false,
        success: function (response) {
            // tabla_companias.ajax.reload();
            console.log(response)
            crearDocumento(response.data)
        },
        error: function (error) {
            console.log(error)


        }
    });
}

function get(id) {
    return document.getElementById(id);
}

function crearDocumento(info) {
    w = window.open();
    var html = '<div><p>Reporte de Logs ' + get("fecha").value + '</p></div><br><br>'
    for (var i = 0; i < info.length; i++) {
        html += '<div>' + info[i] + '</div>'
    }
    w.document.write(html);
    w.print();
    w.close();
}