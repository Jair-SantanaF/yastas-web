$(document).ready(function(){
    GetPost();
});

/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/12/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener el listado de publicaciones
 ***********************************************************************/
function GetPost(){
    var datos = new FormData();
    var config = {
        url: window.base_url + "posts/WallsList",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            let html = '';
            $.map(response.data,function (value,index){
                html += '<div class="col col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 mt-5 offset-sm-2 offset-md-2 offset-lg-2 offset-xl-2 mb-1">' +
                    '<div class="p-1 border border-dark border-top-0 border-right-0 border-left-0 mt-2">' +
                        '<div class="media">' +
                            '<img src="'+value['profile_photo']+'" style="width: 80px; height: 80px;" class="d-flex align-self-start mr-3 rounded-circle border-shadow">' +
                            '<div class="media-body align-self-center">' +
                                '<h3 class="mt-0">'+value['name_complete']+'</h3>' +
                                '<h4 class="mt-0">Administrador</h4>' +
                            '</div>' +
                        '</div>' +
                        '<div class="media">' +
                            '<div class="media-body">' +
                               value['wall_description']+
                            '</div>' +
                        '</div>' +
                        '<div class="media">' +
                            '<div class="media-body">' +
                                '<img src="'+value['image_path']+'" style="width: 400px;" class="">' +
                            '</div>' +
                        '</div>' +
                        '<div class="row justify-content-end mr-3">' +
                            '<div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-6 text-right">' +
                                '<i class="far fa-heart cursor-pointer" onclick="LikeOrDislike('+value['id']+')"></i> '+value['likes']+' me gusta' +
                            '</div>' +
                            '<div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-6 text-right">' +
                                '<i class="far fa-comment cursor-pointer" onclick="ViewComments('+value['id']+');"></i> '+value['total_comments']+' Comentarios' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            });
            $('#post').html(html);
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Muro',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/12/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para dar like o quitar like
 ***********************************************************************/
function LikeOrDislike(id){
    var datos = new FormData();
    datos.append('post_id',id);
    var config = {
        url: window.base_url + "posts/SaveLikePost",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            Swal.fire({
                type: 'success',
                title: 'Muro',
                text: response.msg
            });
            GetPost();
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Muro',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/12/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener los comentarios de un post
 ***********************************************************************/
var post_select = 0;
function ViewComments(id){
    post_select = id;
    var datos = new FormData();
    datos.append('post_id',id);
    var config = {
        url: window.base_url + "posts/ListCommentsPost",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            //border border-dark border-top-0 border-right-0 border-left-0
            let html = '';
            $.map(response.data,function (value,index){
                html += '<div class="col col-12 col-sm-10 col-md-10 col-lg-10 col-xl-10 mt-2">' +
                    '<div class="p-1 border border-dark border-top-0 border-right-0 border-left-0">' +
                        '<div class="media">' +
                            '<div class="media-body align-self-center">' +
                                '<h4 class="mt-0">'+value['name_complete']+'</h4>' +
                            '</div>' +
                        '</div>' +
                        '<div class="media">' +
                            '<div class="media-body">' +
                                value['comment']+
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            });
            $('#comments').html(html);
            $('#modal_comments').modal('show');
        },
        error: function (response) {
            let html = '';
            $('#comments').html(html);
            $('#modal_comments').modal('show');
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/12/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para guardar un comentario nuevo
 ***********************************************************************/
function SaveComment(){
    var datos = new FormData(),
        comment = $('#comment_new');
    if(comment.val() === ''){
        Swal.fire({
            type: 'error',
            title: 'Muro',
            text: 'Por favor escribe un comentario.'
        });
        return;
    }
    datos.append('post_id',post_select);
    datos.append('comment',comment.val());
    var config = {
        url: window.base_url + "posts/SaveWallComment",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            Swal.fire({
                type: 'success',
                title: 'Muro',
                text: response.msg
            });
            comment.val('');
            ViewComments(post_select);
            GetPost();
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Muro',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
}