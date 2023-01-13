$(document).ready(function(){
    GetRanking();
});

/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/11/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener el listado de ranking
 ***********************************************************************/
function GetRanking(){
    var datos = new FormData();
    datos.append('filtro', 1);
    var config = {
        url: window.base_url + "ws/getRanking",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            let table = '';
            $.map(response.data,function (value,index){
                if(index <=2){
                    switch (value['position']){
                        case 1:
                            $('#first_user_image').attr('src',value['profile_photo']);
                            $('#first_user_name').html(value['name']+' '+value['last_name']);
                            $('#first_user_points').html(value['score']);
                            break;
                        case 2:
                            $('#second_user_image').attr('src',value['profile_photo']);
                            $('#second_user_name').html(value['name']+' '+value['last_name']);
                            $('#second_user_points').html(value['score']);
                            break;
                        case 3:
                            $('#three_user_image').attr('src',value['profile_photo']);
                            $('#three_user_name').html(value['name']+' '+value['last_name']);
                            $('#three_user_points').html(value['score']);
                            break;
                        default:

                    }
                }else{
                    table +='<tr class="border-button" >' +
                    '<th scope="row">'+value['position']+'°</th>' +
                    '<td>'+value['name']+' '+value['last_name']+'</td>' +
                    '<td>'+value['score']+'</td>' +
                    '</tr>';
                }
            });
            $('#tbody_users').html(table);
            $('#content_ranking').show();
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Ranking',
                text: response.responseJSON.error_msg
            });
            $('#content_ranking').hide();
        }
    }
    $.ajax(config);
}