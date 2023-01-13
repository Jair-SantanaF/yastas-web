let theWheel = '';
$(document).ready(function(){
    $('body').addClass('bg-white');
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 14/11/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener el catalogo de los quiz disponibles
     ***********************************************************************/
    var datos = new FormData();
    var config = {
        url: window.base_url + "games/RouletteQuiz",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            let segments = [],
                total_segments = response.data.length,
                total_pins = total_segments * 2;
            $.map(response.data,function (value,index) {
                segments.push({'fillStyle' : HexaColor(), 'text' : value['name'], 'strokeStyle' : '#01356d', 'textFillStyle':'#FFFFFF', 'quiz_id':value['id']})
            });
            theWheel = new Winwheel({
                'numSegments'  : total_segments,     // Specify number of segments.
                'centerX'    : 215,         // Set x and y as number.
                'centerY'    : 200,
                'outerRadius'  : 154,   // Set outer radius so wheel fits inside the background.
                'textFontSize' : 14,    // Set font size as desired.
                'lineWidth'    : 6,
                'segments'     : segments,
                'animation' :           // Specify the animation to use.
                    {
                        'type'     : 'spinToStop',
                        'duration' : 15,
                        'spins'    : total_segments,
                        'callbackFinished' : alertPrize,
                        'callbackSound'    : playSound,   // Function to call when the tick sound is to be triggered.
                        'soundTrigger'     : 'pin'        // Specify pins are to trigger the sound, the other option is 'segment'.
                    },
                'pins' :
                    {
                        'number': total_pins   // Number of pins. They space evenly around the wheel.
                    }
            });
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Ruleta',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
    // Create new wheel object specifying the parameters at creation time.
});
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 14/11/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para generar colores dinamicos
 ***********************************************************************/
function HexaColor(){
    // storing all letter and digit combinations
    // for html color code
    //var letters = "0123456789ABCDEF";
    var letters = ["#F39500", "#C50022", "#21A0D2", "#004A96", "#65AC1E", "#00793A"];

    // html color code starts with #
    //var color = '#';

    // generating 6 times as HTML color code consist
    // of 6 letter or digits
    //for (var i = 0; i < 6; i++)
        //color += letters[(Math.floor(Math.random() * 16))];
        //color += letters[i];

    var color = letters[Math.floor(Math.random() * letters.length)];

    return color;
}
// Vars used by the code in this page to do power controls.
let wheelPower    = 0;
let wheelSpinning = false;

// -------------------------------------------------------
// Function to handle the onClick on the power buttons.
// -------------------------------------------------------
function powerSelected(powerLevel)
{
    // Ensure that power can't be changed while wheel is spinning.
    if (wheelSpinning == false) {
        // Reset all to grey incase this is not the first time the user has selected the power.
        document.getElementById('pw1').className = "";
        document.getElementById('pw2').className = "";
        document.getElementById('pw3').className = "";

        // Now light up all cells below-and-including the one selected by changing the class.
        if (powerLevel >= 1) {
            document.getElementById('pw1').className = "pw1";
        }

        if (powerLevel >= 2) {
            document.getElementById('pw2').className = "pw2";
        }

        if (powerLevel >= 3) {
            document.getElementById('pw3').className = "pw3";
        }

        // Set wheelPower var used when spin button is clicked.
        wheelPower = powerLevel;

        // Light up the spin button by changing it's source image and adding a clickable class to it.
        //document.getElementById('spin_button').src = "<?php echo base_url()?>assets/img/spin_on.png";
        //document.getElementById('spin_button').className = "clickable";
    }
}

// -------------------------------------------------------
// Click handler for spin button.
// -------------------------------------------------------
function startSpin()
{
    // Ensure that spinning can't be clicked again while already running.
    if (wheelSpinning == false) {
        // Based on the power level selected adjust the number of spins for the wheel, the more times is has
        // to rotate with the duration of the animation the quicker the wheel spins.
        if (wheelPower == 1) {
            theWheel.animation.spins = 3;
        } else if (wheelPower == 2) {
            theWheel.animation.spins = 8;
        } else if (wheelPower == 3) {
            theWheel.animation.spins = 15;
        }

        // Disable the spin button so can't click again while wheel is spinning.
        document.getElementById('spin_button').className = "btn btn-success diabled";

        // Begin the spin animation by calling startAnimation on the wheel object.
        theWheel.startAnimation();

        // Set to true so that power can't be changed and spin button re-enabled during
        // the current animation. The user will have to reset before spinning again.
        wheelSpinning = true;
        $('#spin_button').hide();
    }
}

// -------------------------------------------------------
// Function for reset button.
// -------------------------------------------------------
function resetWheel()
{
    theWheel.stopAnimation(false);  // Stop the animation, false as param so does not call callback function.
    theWheel.rotationAngle = 0;     // Re-set the wheel angle to 0 degrees.
    theWheel.draw();                // Call draw to render changes to the wheel.

    /*document.getElementById('pw1').className = "";  // Remove all colours from the power level indicators.
    document.getElementById('pw2').className = "";
    document.getElementById('pw3').className = ""; */

    wheelSpinning = false;          // Reset to false to power buttons and spin can be clicked again.
}

// -------------------------------------------------------
// Called when the spin animation has finished by the callback feature of the wheel because I specified callback in the parameters
// note the indicated segment is passed in as a parmeter as 99% of the time you will want to know this to inform the user of their prize.
// -------------------------------------------------------
function alertPrize(indicatedSegment)
{
    Questions(indicatedSegment.quiz_id);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 14/11/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener las preguntas en base al quiz
 ***********************************************************************/
function Questions(quiz_id){
    var datos = new FormData();
    datos.append('quiz_id',quiz_id)
    var config = {
        url: window.base_url + "games/RouletteQuestionsAnswer",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 14/11/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: recorremos todas las preguntas y sus respuestas
             ***********************************************************************/
            let questions = '';
            $.map(response.data,function (value,index) {
                let display = '';
                if(index > 0){
                    display = 'display_none';
                }
                questions +='<div id="question_'+index+'" class="row mt-4 '+display+'">';
                questions +='<div class="col-12">' +
                    '<div class="fondo_blanco lead font-weight-bold" style="border-radius: 15px; border: 12px solid blue; padding: 10px;">' +
                        value['question']+
                    '</div>' +
                '</div>';
                /***********************************************************************
                 *	Autor: Mario Adrián Martínez Fernández   Fecha: 14/11/2020
                 *		   mario.martinez.f@hotmail.es
                 *	Nota: Ahora recorremos las respuestas para generar el html
                 ***********************************************************************/
                questions += '<div class="col-12">';
                $.map(value['answers'],function (value_answer,index_answer) {
                    questions += '<button id="button_answer_'+value_answer['id']+'" class="btn btn-lg btn-light btn-block mt-3 lead" onclick="ResponseQuestion('+value['id']+','+value_answer['id']+','+index+')">'+value_answer['answer']+'</button>';
                });
                questions +='</div>'
                questions +='</div>';
            });
            $('#content_questions').html(questions);
        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Preguntas',
                text: response.responseJSON.error_msg
            });
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/11/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para redireccionar a su vista
 ***********************************************************************/
let audio = new Audio('assets/audio/tick.mp3');  // Create audio object and load tick.mp3 file.

function playSound()
{
    // Stop and rewind the sound if it already happens to be playing.
    audio.pause();
    audio.currentTime = 0;

    // Play the sound.
    audio.play();
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 14/11/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para guardar la respuesta de preguntas
 ***********************************************************************/
function ResponseQuestion(question_id,answer_id,index) {
    var datos = new FormData();
    datos.append('question_id',question_id);
    datos.append('answer_id',answer_id);
    $('#button_answer_'+answer_id).addClass('rojo_nuup_back rojo_nuup_border text-white');
    var config = {
        url: window.base_url + "games/SaveAnswerRoulette",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            let type = '';
            if(response.data['correct'] == 1){
                type = 'success';
            }else{
                type = 'error';
            }
            Swal.fire({
                type: type,
                title: 'Ruleta',
                text: response.msg
            }).then((result) => {
                if($('#question_'+(index+1)).length){
                    $('#question_'+index).hide();
                    $('#question_'+(index+1)).show();
                }else{
                    Swal.fire({
                        type: 'success',
                        title: 'Ruleta',
                        text: 'El juego ha terminado.'
                    }).then((result) => {
                        window.location.href=window.base_url+"app/Games";
                        $('body').removeClass('bg-primary');
                    });
                }
            });

        },
        error: function (response) {
            Swal.fire({
                type: 'error',
                title: 'Ruleta',
                text: response.responseJSON.error_msg
            }).then((result) => {
                window.location.href=window.base_url+"app/Games";
            });
        }
    }
    $.ajax(config);
}
