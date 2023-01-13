<!DOCTYPE html>
<html lang="es" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/generalClient.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/client/questions.css">
    <title>Preguntas</title>
</head>

<style>
    * {
        overflow-x: hidden;
    }

    body {
        background-image: url("<?php echo base_url('/assets/img/yastas/back y header') ?>/img_background.png");
        background-repeat: no-repeat;
        background-size: cover;
    }
</style>

<nav class="navbar navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="text_center">
            <img src="<?php echo base_url('/assets/img/yastas/back y header') ?>/img_header_logo.png" alt="logo" width="100px" />
        </div>
        <div class="user_notifications">
            <a href="<?php echo base_url('client/notifications') ?>" class="btn btn-outline-light" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
                    <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z" />
                </svg>
            </a>
            <a href="<?php echo base_url('client/profile') ?>" class="btn btn-outline-light" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
                </svg>
            </a>
        </div>
        <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
            <div class="offcanvas-header">
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Mis logros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contactanos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Tour Nupi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Ayuda</a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url('client/logout') ?>">Eliminar Cuenta</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url('client/logout') ?>">Aviso de privacidad</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url('client/logout') ?>">Terminos y condiciones</a>
                    </li>
                    <li class="nav-item">
                        <a onclick="cerrarSesion();" class="nav-link">Cerrar sesión</a>
                    </li>
                </ul>

            </div>
        </div>
    </div>
</nav>

<body>
    <br><br><br>
    <div class="row" style="display: flex; justify-content: space-around; align-items: center; color: white; padding-bottom: 20px;">
        <div onclick="GoBack();" class="col-md-2" style="display: flex; justify-content: space-around;  align-items: center; cursor: pointer;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
            </svg>
            Regresar
        </div>
        <div class="col-md-10">
            <p style="font-weight: bold; font-size: large; margin-left: 35%;">Preguntas</p>
        </div>
        <div class="col-md-10 progress" style="padding: 0;">
            <div id="progress_bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="8" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
    <div class="container">
        <div class="row gx-5" id="cuestionarios" style="margin: 0 auto; max-width: 800px;">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Cargando preguntas...</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="row text-center">
            <div class="separator"></div>
            <div class="col-5"></div>
            <div class="col-2" id="button_next">
                <button onclick="nextQuestion()" type="button" class="text-center btn btn-danger next_question">Siguiente</button>
            </div>
            <div class="col-5"></div>
        </div>
    </div>


    <script>
        window.base_url = "<?php echo base_url() ?>";
    </script>
    <script src="<?php echo base_url() ?>assets/plugins/jquery/js/jquery-3.3.1.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/client.js"></script>
</body>

</html>

<script>
    $(document).ready(function() {
        getQuestions();
    });

    function getQuestions() {
        const localUser = JSON.parse(localStorage.getItem('token'));
        const quiz_id = localStorage.getItem('quiz_id');
        const actualQuestion = localStorage.getItem('actual_question');

        $.ajax({
            url: window.base_url + "questions/ListQuestionsQuiz",
            type: "POST",
            dataType: "json",
            data: {
                quiz_id: quiz_id,
                token: localUser[0].token
            },
            success: function(data) {
                let quesionnaries = $("#cuestionarios");
                let progress_bar = $("#progress_bar");
                let button_next = $("#button_next");

                let question_type = data.data[actualQuestion].type_id;
                let question = getOnlyText(data.data[actualQuestion].question);
                let question_id = data.data[actualQuestion].id;
                let answers = data.data[actualQuestion].answers;
                let progress = ((parseInt(actualQuestion) + 1) / data.data.length) * 100;

                localStorage.setItem('last_question', data.data.length - 1);
                progress_bar.css("width", progress + "%");
                progress_bar.html((parseInt(actualQuestion) + 1) + "/" + data.data.length);
                button_next.empty();
                quesionnaries.empty();
                quesionnaries.append(getQuestionByType(question_type, question, question_id, answers));
                
                if (actualQuestion >= data.data.length - 1) {
                    button_next.append('<button onclick="finish()" type="button" class="text-center btn btn-danger finish">Finalizar</button>');
                    $('#button_next').addClass('finish');
                }

                if (!button_next.hasClass('finish') && !button_next.hasClass('open_question')) {
                    button_next.append('<button onclick="nextQuestion()" type="button" class="text-center btn btn-danger next_question">Siguiente</button>');
                }

            },
            error: function(data) {
                quesionnaries.empty();
                questionnaires.append("<tr><td colspan='3'>No hay cuestionarios disponibles</td></tr>");
            }
        });
    }

    function getOnlyText(element) {
        if (element.includes("<p>")) {
            return element.split("<p>")[1].split("</p>")[0];
        } else {
            return element.split(">")[1].split("<")[0];

        }
    }

    function nextQuestion() {
        const actualQuestion = localStorage.getItem('actual_question');
        localStorage.setItem('actual_question', parseInt(actualQuestion) + 1);
        // sendAnswer();
        getQuestions();
    }

    function finish() {
        // sendAnswer();
        setLastAnsweredQuestion();
        ShowTest();
    }

    function setOpenAnswer(question_id, type_id) {
        let answer = $("#openAnswer").val();
        selectAnswer(answer, question_id, type_id);
        $('#button_next').removeClass('open_question');
        // sendAnswer();
        if (localStorage.getItem('actual_question') >= localStorage.getItem('last_question')) {
            ShowTest();
        } else {
            nextQuestion();
        }
    }

    function selectAnswer(answer_id, question_id, type_id) {
        selectedAnswer = {
            answer_id: answer_id,
            question_id: question_id,
            type_id: type_id
        };
        localStorage.setItem('selected_answer', JSON.stringify(selectedAnswer));
    }

    function sendAnswer() {
        let data = new FormData();
        let localUser = JSON.parse(localStorage.getItem('token'));
        let selectedAnswer = JSON.parse(localStorage.getItem('selected_answer'));
        data.append('token', localUser[0].token);
        data.append('answer', selectedAnswer.answer_id);
        data.append('question_id', selectedAnswer.question_id);
        data.append('type_id', selectedAnswer.type_id);
        $.ajax({
            url: window.base_url + "questions/SaveAnswerUser",
            type: "POST",
            dataType: "json",
            data: data,
            processData: false,
            contentType: false,
            success: function(data) {
                alert("Respuesta enviada");
                localStorage.removeItem('selected_answer');
            },
            error: function(data) {
                alert("Error al enviar la respuesta");
                console.log(data);
            }
        });
    }

    function setLastAnsweredQuestion() {
        let quizStatus = {
            quiz_id: localStorage.getItem('quiz_id'),
            last_question: localStorage.getItem('actual_question')
        };
        let lastQuizStatus = JSON.parse(localStorage.getItem('quiz_status'));
        if (lastQuizStatus == null) {
            lastQuizStatus = [];
        }
        lastQuizStatus.push(quizStatus);
        localStorage.setItem('quiz_status', JSON.stringify(lastQuizStatus));
        

    }

    function getQuestionByType(question_type, question, question_id, answers) {
        switch (question_type) {
            case 1:
                return getQuestionType1(question, question_id, answers);
                break;
            case 2:
                return getQuestionType2(question, question_id, answers);
                break;
            case 3:
                return getQuestionType3(question, question_id, answers);
                break;
            case 4:
                return getQuestionType4(question, question_id, answers);
                break;
            case 5:
                return getQuestionType5(question, question_id, answers);
                break;
            case 6:
                return getQuestionType6(question, question_id, answers);
                break;
            case 7:
                return getQuestionType7(question, question_id, answers);
                break;
            case 8:
                return getQuestionType8(question, question_id, answers);
                break;
            case 9:
                return getQuestionType9(question, question_id, answers);
                break;
            case 10:
                return getQuestionType10(question, question_id, answers);
                break;
            case 11:
                return getQuestionType11(question, question_id, answers);
                break;
            case 12:
                return getQuestionType12(question, question_id, answers);
                break;
            case 13:
                return getQuestionType13(question, question_id, answers);
                break;
            case 14:
                return getQuestionType14(question, question_id, answers);
                break;
            default:
                break;
        }
    }

    function getQuestionType1(question, question_id, answers) {
        let type_id = 1;
        let html = ` <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title text-center">${question}</h3>
                                <br>
                    `;
        $.each(answers, function(i, item) {
            html += `<div class="row">
                        <div class="col-2"></div>
                            <div class="col-8">
                                <button onClick="selectAnswer(${item.id}, ${question_id}, ${type_id})" type="button" class="col-12 btn btn-outline-danger rounded-3 opc_mul_buttons" >${getOnlyText(item.answer)}</button>
                                <div class="separator_buttons"></div>
                            </div>
                        <div class="col-2"></div>
                    </div>`;
        });
        return html;
    }

    function getQuestionType2(question, question_id, answers) {
        let type_id = 2;
        let html = ` <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title text-center">${question}</h3>
                                <br>
                    `;
        $.each(answers, function(i, item) {
            html += `<div class="row">
                        <div class="col-2"></div>
                            <div class="col-8">
                                <button onClick="selectAnswer(${item.id}, ${question_id}, ${type_id})" type="button" class="col-12 btn btn-outline-danger rounded-3 opc_mul_buttons" >${getOnlyText(item.answer)}</button>
                                <div class="separator_buttons"></div>
                            </div>
                        <div class="col-2"></div>
                    </div>`;
        });
        return html;
    }

    function getQuestionType8(question, question_id, answers) {
        let type_id = 8;
        let html = `<div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title text-center">${question}</h3>
                                <br><br><br>
                                <div class="row text-center">
                                    <div class="mb-sm-4 mb-md-4 col-lg-1"></div>
                                    <button onClick="selectAnswer(${1}, ${question_id}, ${type_id})" class="mb-sm-4 mb-md-4 col-lg-2 liker_cara"><img src="<?php echo base_url('/assets/img') ?>/img_liker_green_1.png" alt="logo" width="100px"/></button>
                                    <button onClick="selectAnswer(${2}, ${question_id}, ${type_id})" class="mb-sm-4 mb-md-4 col-lg-2 liker_cara"><img src="<?php echo base_url('/assets/img') ?>/img_liker_green_2.png" alt="logo" width="100px"/></button>
                                    <button onClick="selectAnswer(${3}, ${question_id}, ${type_id})" class="mb-sm-4 mb-md-4 col-lg-2 liker_cara"><img src="<?php echo base_url('/assets/img') ?>/img_liker_green_3.png" alt="logo" width="100px"/></button>
                                    <button onClick="selectAnswer(${4}, ${question_id}, ${type_id})" class="mb-sm-4 mb-md-4 col-lg-2 liker_cara"><img src="<?php echo base_url('/assets/img') ?>/img_liker_green_4.png" alt="logo" width="100px"/></button>
                                    <button onClick="selectAnswer(${5}, ${question_id}, ${type_id})" class="mb-sm-4 mb-md-4 col-lg-2 liker_cara"><img src="<?php echo base_url('/assets/img') ?>/img_liker_green_5.png" alt="logo" width="100px"/></button>
                                    <div class="mb-sm-4 mb-md-4 col-lg-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>`
        return html;
    }

    function getQuestionType13(question, question_id, answers) {
        let type_id = 13;
        let button_next = $("#button_next");
        button_next.empty();
        $('#button_next').addClass('open_question');
        button_next.append(`<button onClick="setOpenAnswer(${question_id}, ${type_id})" type="button" class="text-center btn btn-danger open_question">Siguiente</button>`);
        let html = `<div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title text-center">${question}</h3>
                                <br>
                                <div class="row">
                                    <div class="col-1"></div>
                                        <div class="col-10">
                                            <div class="form-floating">
                                                <textarea id="openAnswer" class="form-control" style="height: 200px; border-radius: 15px;"></textarea>
                                                <label for="floatingTextarea2">Escribe tu respuesta...</label>
                                            </div>
                                        </div>
                                    <div class="col-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>`;
        return html;
    }

    function getQuestionType14(question, question_id, answers) {
        let type_id = 14;
        let html = `<div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title text-center">${question}</h3>
                                <br><br><br>
                                <div class="row text-center">
                                    <div class="mb-sm-4 mb-md-4 col-lg-1"></div>
                                    <button onClick="selectAnswer(${1}, ${question_id}, ${type_id})" class="mb-sm-4 mb-md-4 col-lg-2 liker_numero"><img src="<?php echo base_url('/assets/img/yastas/questions') ?>/img_preguntas_numero_1_naranja.png" alt="logo" width="100px"/></button>
                                    <button onClick="selectAnswer(${2}, ${question_id}, ${type_id})" class="mb-sm-4 mb-md-4 col-lg-2 liker_numero"><img src="<?php echo base_url('/assets/img/yastas/questions') ?>/img_preguntas_numero_2_naranja.png" alt="logo" width="100px"/></button>
                                    <button onClick="selectAnswer(${3}, ${question_id}, ${type_id})" class="mb-sm-4 mb-md-4 col-lg-2 liker_numero"><img src="<?php echo base_url('/assets/img/yastas/questions') ?>/img_preguntas_numero_3_naranja.png" alt="logo" width="100px"/></button>
                                    <button onClick="selectAnswer(${4}, ${question_id}, ${type_id})" class="mb-sm-4 mb-md-4 col-lg-2 liker_numero"><img src="<?php echo base_url('/assets/img/yastas/questions') ?>/img_preguntas_numero_4_naranja.png" alt="logo" width="100px"/></button>
                                    <button onClick="selectAnswer(${5}, ${question_id}, ${type_id})" class="mb-sm-4 mb-md-4 col-lg-2 liker_numero"><img src="<?php echo base_url('/assets/img/yastas/questions') ?>/img_preguntas_numero_5_naranja.png" alt="logo" width="100px"/></button>
                                    <div class="mb-sm-4 mb-md-4 col-lg-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>`
        return html;
    }
</script>