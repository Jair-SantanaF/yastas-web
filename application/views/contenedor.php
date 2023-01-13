<html>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NUUP</title>
    <link href="<?php echo base_url() ?>assets/plugins/dataTables_1_10_21/datatables.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/bootstrap_4/css/bootstrap.min.css">

    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/fontawesome_5_0_8/web-fonts-with-css/css/fontawesome-all.min.css">
    <!--<link rel="stylesheet" href="../../../<?php echo base_url() ?>assets/plugins/bootstrap_4/css/bootstrap.css">-->
    <link href="<?php echo base_url() ?>assets/plugins/loadmask/css/jquery.loadmask.spin.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url() ?>assets/plugins/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url() ?>assets/plugins/froala_editor_3.1.0/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/fullcalendar-5.3.2/lib/main.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/chartJs/Chart.min.css"">
</head>
<body>
<div class=" container-fluid h-100" id="body" style="padding: 0 !important;">
    </div>
    <script src="<?php echo base_url() ?>assets/plugins/jquery/js/jquery-3.3.1.min.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/popper/popper.min.js"></script>
    <script src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/bootstrap_4/js/bootstrap.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/loadmask/js/spin.js" type="text/javascript"></script>
    <script src="<?php echo base_url() ?>assets/plugins/form_validation/jquery.validate.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url() ?>assets/plugins/dataTables_1_10_21/datatables.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/sweetalert2/dist/sweetalert2.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/plugins/froala_editor_3.1.0/js/froala_editor.pkgd.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/plugins/froala_editor_3.1.0/js/plugins.pkgd.min.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/fullcalendar-5.3.2/lib/main.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/fullcalendar-5.3.2/lib/locales-all.js"></script>
    <!-- The core Firebase JS SDK is always required and must be listed first -->
    <script src="https://www.gstatic.com/firebasejs/7.14.3/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.14.3/firebase-firestore.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.14.3/firebase-messaging.js"></script>

    <script src="<?php echo base_url() ?>assets/plugins/chartJs/Chart.min.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/chartJs/Chart.bundle.min.js"></script>


    <!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->
    <script src="https://www.gstatic.com/firebasejs/7.14.3/firebase-analytics.js"></script>

    <!--<script>-->
    <!--    // Your web app's Firebase configuration-->
    <!--    const firebaseConfig = {-->
    <!--        apiKey: "AIzaSyBeUSyithvApTQU-pFHWwkEPk1woOf1vZs",-->
    <!--        authDomain: "nuup-613fd.firebaseapp.com",-->
    <!--        databaseURL: "https://nuup-613fd.firebaseio.com",-->
    <!--        projectId: "nuup-613fd",-->
    <!--        storageBucket: "nuup-613fd.appspot.com",-->
    <!--        messagingSenderId: "596787191407",-->
    <!--        appId: "1:596787191407:web:d5726bb5570ade236c379a",-->
    <!--        measurementId: "G-NB0PJEXXFK"-->
    <!--    };-->
    <!--    // Initialize Firebase-->
    <!--    firebase.initializeApp(firebaseConfig);-->
    <!--    firebase.analytics();-->
    <!--</script>-->



    <script>
        var view = <?php echo "'" . $view_ . "'" ?>,
            empresa_id = <?php echo  "'" . $this->session->userdata('empresa_id') . "'"; ?>,
            rol_id = <?php echo  "'" . $this->session->userdata('rol_id') . "'"; ?>;
            id_region = <?php echo  "'" . $this->session->userdata('id_region') . "'"; ?>;
        console.log(rol_id)

        jQuery(document).ready(function() {
            // if (rol_id == 4) {
            //     document.getElementById("li_desbloqueo").style.display = false
            //     document.getElementById("li_capturas").style.display = false
            // }
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández
             *		   mario.martinez.f@hotmail.es
             *	Fecha: 03/06/2019
             *	Nota: Se agrega funcionalidad para cambiar textos de requeridos
             ***********************************************************************/
            jQuery.extend(jQuery.validator.messages, {
                required: "Este campo es requerido",
                remote: "Please fix this field.",
                email: "Por favor, introduzca un email valido.",
                url: "Por favor, introduzca una URL valida",
                date: "Por favor, introduzca una fecha valida",
                dateISO: "Please enter a valid date (ISO).",
                number: "Please enter a valid number.",
                digits: "Please enter only digits.",
                creditcard: "Please enter a valid credit card number.",
                equalTo: "Por favor, introduzca el mismo valor de nuevo.",
                accept: "Please enter a value with a valid extension.",
                maxlength: jQuery.validator.format("Ingrese no más de {0} caracteres."),
                minlength: jQuery.validator.format("Ingrese al menos {0} caracteres."),
                rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
                range: jQuery.validator.format("Please enter a value between {0} and {1}."),
                max: jQuery.validator.format("Por favor ingrese un valor menor o igual a {0}."),
                min: jQuery.validator.format("Por favor ingrese un valor mayor o igual a {0}.")
            });
            /*
        * $('#form_registro').validate({
        rules:{
            nombre_registro:{
                required:true
            },
            apellido_registro:{
                required:true
            },
            email_registro:{
                required:true,
                email:true
            },
            sector_registro:{
                required:true
            },
            password_registro:{
                required:true
            },
            repetir_password_registro:{
                required:true,
                equalTo:"#password_registro"
            }
        },
        submitHandler:function(form){
            //$.post();
            alert('funciono');
        },
        messages: {
            nombre_registro: "Por favor",
            apellido_registro: "Please enter your lastname",
            email_registro: {
                required: "Please enter a username"
            },
            password_registro: {
                required: "Please provide a password"
            },
            repetir_password_registro: {
                required: "Please provide a password",
                equalTo: "Please enter the same password as above"
            },
            sector_registro: "Please enter a valid email address"
        },
        errorElement: "em",
        errorPlacement: function ( error, element ) {
            // Add the `help-block` class to the error element
            error.addClass( "help-block text-white" );
            if ( element.prop( "type" ) === "checkbox" ) {
                error.insertAfter( element.parent( "label" ) );
            } else {
                error.insertAfter( element );
            }
        },
        highlight: function ( element, errorClass, validClass ) {
            $( element ).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
        },
        unhighlight: function (element, errorClass, validClass) {
            $( element ).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
        }
    });
        * */
            window.base_url = "<?php echo base_url() ?>";
            console.log(window.base_url)
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández
             *		   mario.martinez.f@hotmail.es
             *	Fecha: 01 mar 2018
             *	Nota: Aqui se define la vista que se desea cargar
             ***********************************************************************/
            $('#body').load(window.base_url + view, {
                idioma: 'es_ES'
            });

            // var body = document.getElementById("body")
            // body.onclick = validarSesion()

            // function validarSesion() {
            //     console.log("validando la sesion")
            //     $.ajax({
            //         url: window.base_url + "Ws/comprobarSesion",
            //         type: 'POST',
            //         data: {

            //         },
            //         dataType: 'json',
            //         error: function(xhr, error, code) {
            //             console.log(xhr)
            //             location.href = window.base_url
            //         },
            //         success: function(json) {
            //             console.log(json)
            //         }
            //     })
            // }
            var idleTime = 0;
            $(document).ready(function() {
                
                //Increment the idle time counter every minute.
                var idleInterval = setInterval(timerIncrement, 60000); // 1 minute

                //Zero the idle timer on mouse movement.
                $(this).mousemove(function(e) {
                    idleTime = 0;
                });
                $(this).keypress(function(e) {
                    idleTime = 0;
                });
            });

            function timerIncrement() {
                idleTime = idleTime + 1;
                if (idleTime > 4) { // 20 minutes
                    $.ajax({
                        url: window.base_url + "Ws/comprobarSesion",
                        type: 'POST',
                        data: {

                        },
                        dataType: 'json',
                        error: function(xhr, error, code) {
                            console.log(xhr)
                            location.href = window.base_url
                        },
                        success: function(json) {
                            console.log(json)
                        }
                    })
                }
            }

        });
    </script>
    <script>

    </script>
    </body>

</html>