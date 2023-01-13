$(document).ready(function(){
    cargarFeedback();
    $(`#save_like`).click(function () {
        saveLike();
    });
});
function cargarFeedback() {
    var datos = new FormData();

    datos.append('type', 3);

    $("#contenedor_feedback").html("");
    $("#media_path").html("");
    $("#file_path").html("");
    $("#description").html("");
    var config = {
        url: window.base_url + "feedback/FeedbackList",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: datos,
        success: function (response) {
            var retros = response.data;
            $.each(retros, function (index,value) {
                if(value.id == feedback_id)
                    record = value;
            });
            $("#name_user").html(record.name_user);
            $("#job_name").html(record.job_name);
            $("#photo_user").attr("src",record.photo_user);
            $("#total_like").html(record.total_like);

            if(record.media_path != ""){
                $("#media_path").removeClass("d-none");
                $("#media_path").append(`<img src="${record.media_path}" class="w-100">`);
            }
            if(record.file_path != ""){
                $("#file_path").removeClass("d-none");

                var cadenas = record.file_path.split("."),
                    extension = cadenas[cadenas.length - 1],
                    file_html =``;

                if(extension == "pdf"){
                    file_html =`
                    <div class="w-100">
                        <object data="${record.file_path}" type="application/pdf" width="100%" height="600px">
                            alt : <a href="${record.file_path}">test.pdf</a>
                        </object>
                    </div>`;
                }else{
                    file_html =`<img src="${record.file_path}" class="w-100">`;
                }
                $("#file_path").append(file_html);

            }
            if(record.media_path != "" && record.file_path != ""){
                $("#description").addClass("col-12");
                $("#description").append(`<p  class="">${record.description}</p>`);
            }else{
                $("#description").addClass("col-6");
                $("#description").append(`<p  class="">${record.description}</p>`);
            }
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Login',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
}

function saveLike(){
    var datos = new FormData();

    datos.append('feedback_id', feedback_id);

    var config = {
        url: window.base_url + "feedback/SaveLike",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: datos,
        success: function (response) {
            Swal.fire({
                type: 'success',
                title: 'Feedback',
                text: response.msg
            }).then((result) => {
                cargarFeedback();
            });
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Login',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
}