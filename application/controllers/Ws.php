<?php

// require_once '/var/www/html/nuup_respaldo/application/libraries/dompdf/autoload.inc.php';
//require_once BASE_PATH.'application/libraries/dompdf/autoload.inc.php';

/* use Dompdf\Dompdf; */
//  header('Access-Control-Allow-Origin: website_url');
//  header("Content-Type: application/json; charset=UTF-8");
//  Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); //method allowed
class Ws extends CI_Controller
{
    public $defaultLang = 'es';

    public function __construct()
    {
        // header('Access-Control-Allow-Origin: website_url');
        // header("Content-Type: application/json; charset=UTF-8");
        // Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); //method allowed
        parent::__construct();
        date_default_timezone_set("America/Mexico_City");

        $headers = $this->input->request_headers();
        // if (isset($headers['lenguage'])) {
        //     $this->lang->load('message','es');
        //     $this->defaultLang = 'es';
        // }else{
        $this->lang->load('message', 'en');
        // $this->defaultLang = 'en';
        // }
        $this->load->model('user_model', 'user');
        $this->load->model('groups_mdl', 'groups');
        $this->load->model('notification_mdl', 'notification');
        $this->load->model('games_mdl', 'games');
        $this->load->model('publicacion_mdl', 'publicacion');
        $this->load->model('wall_model', 'wall');
        $this->load->model('posts_mdl', 'post');
        $this->load->model("capacitacion_mdl", "capacitacion");
        $this->load->model("podcast_mdl", "podcast");
        $this->load->model("library_mdl", "library");
        $this->load->model("challenges_mdl", "challenges");
        $this->load->model("incidencias_mdl", "incidencias");
        $this->load->model("Polizas_mdl", "polizas_mdl");
    }

    public function test()
    {
        return $this->load->view('mailtest.php');
    }

    public function CambiarPassword()
    {
        $token = $this->input->post("token");
        $password = $this->input->post("password");
        // echo json_encode($token);
        // echo json_encode($password);
        $valida_token = $this->general_mdl->ValidaTokenRecuperarPassword($token);
        if (!$valida_token) {
            faildResponse('El token no es valido', $this);
            return;
        }
        $id_usuario = $valida_token;
        // echo json_encode($id_usuario);
        $this->general_mdl->writeLog("Cambio de contraseña usuario " . $id_usuario, "<info>");
        $respuesta = $this->general_mdl->CambiarPassword($id_usuario, $password, $token);
        if (!$respuesta) {
            $this->general_mdl->writeLog("Error al cambiar password usuario" . $id_usuario, "<warning>");
            faildResponse('Por favor vuelve a intentarlo', $this);
            return;
        }
        $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "home");
        successResponse("Se actualizó la contraseña", 'El password se ha cambiado correctamente', $this);
    }

    function cambiarPassword1()
    {
        $token = $this->input->post("token");
        $old_password = $this->input->post("old_password");
        $new_password = $this->input->post("new_password");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('El token no es valido', $this);
            return;
        }
        $valida_pass = $this->general_mdl->validar_password($old_password, $valida_token["user_id"]);
        if (!$valida_pass) {
            faildResponse('La contraseña no es correcta', $this);
            return;
        }
        $result = $this->general_mdl->actualizar_pass($new_password, $valida_token["user_id"]);
        // if($result){
        successResponse('Se actualizó contraseña', '', $this);
        // }else{
        //     faildResponse('Error al actualizar contraseña', $this);
        //     return;
        // }
    }

    public function recuperar($token)
    {
        $valida_token = $this->general_mdl->ValidaTokenRecuperarPassword($token);
        $this->load->view('admin/recuperar.php', array('id_user' => $valida_token, 'token' => $token));
    }

    public function desbloquear($email)
    {
        // $valida_token = $this->general_mdl->ValidaTokenRecuperarPassword($token);
        $this->load->view('desbloquear_cuenta.php', array('email' => $email));
    }

    public function desbloquearCuenta()
    {
        $token = $this->input->post("token");
        $email = $this->input->post("email");


        $respuesta = $this->general_mdl->desbloquearCuenta($email);
        if (!$respuesta) {
            $this->general_mdl->writeLog("Error al desbloquear cuenta usuario " . $email, "<warning>");
            faildResponse('', $this);
        }
        $this->general_mdl->writeLog("Desbloqueo de cuenta usuario " . $email, "<info>");
        successResponse('', '', $this);
    }

    public function recuperarPassword()
    {
        $email = $this->input->post("email");
        /***********************************************************************
         *    Nota: Se valida que el email tenga una estructura correcta
         ***********************************************************************/
        if (!valid_email($email)) {
            faildResponse('El email no cuenta con la estructura correcta', $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('email'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        /***********************************************************************
         *    Nota: Enviamos peticion para guardar y obtener el token para esta
         *          recuperacion de password
         ***********************************************************************/
        $token_email = $this->general_mdl->RecuperarPassword($email);
        if (!$token_email) {
            $this->general_mdl->writeLog("Error al recuperar contraseña el correo no existe usuario " . $email, "<warning>");
            faildResponse('Este correo no esta registrado, confirma el email con el que te registraste', $this);
            return;
        }

        // $body = "<div style='background: url(https://kreativeco.com/nuup/mail_images/fondo.png) ; height:100%' >
        //             <div style='padding-bottom: 25px; padding-top: 50px; text-align: center;'>
        //                 <img src='https://kreativeco.com/nuup/mail_images/logo_blanco.png' style='width:300px;'></a>
        //             </div>
        //             <div style='padding: 50px'>
        //                 <div style='content: ''; clear: both; display: table; background: #fff'>
        //                     <div style='float: left;width: 100%; height: 40%;background: #fff;border-radius: 20px; padding-top: 40px; text-align: center;'>
        //                         <p style='font-size: 10px;padding-left: 5%;padding-top: 1%; width: 90%; letter-spacing: 0.1em;line-height: 2.6'>" . $this->lang->line('recovery_msg') . "</p>
        //                         <div style='text-align: center; padding: 20px 0px;'>
        //                             <a href='" . BASE_URL . "ws/recuperar/" . $token_email . "'>
        //                                 <button style='background-color: black; border-radius: 20px; color: #fff; padding: 20px; width: 35%;'>" . $this->lang->line('recovery_title') . "</button>
        //                             </a>
        //                         </div>
        //                     </div>
        //                 </div>
        //             </div>
        //         </div>
        //         ";

        $body = "<div style='background: url(https://appy.com.mx/nuup/assets/img/info_bg.png) ; height:1000px' >
                <div style='padding-bottom: 25px; padding-top: 50px; text-align: center;'>
                    <img src='https://appy.com.mx/nuup/assets/img/info_logo.png' style='width:300px;'></a>
                </div>
                <div style='padding: 50px'>
                    <div style=' clear: both;  background: #FFFFFF'>
                        <div style='float: left;width: 100%; height: 40%;background: #FFFFFF;border-radius: 20px; padding-top: 40px; text-align: center;'>
                            <p style='font-size: 20px;padding-left: 5%;padding-top: 1%; width: 90%; letter-spacing: 0.1em;line-height: 2.6'>Para recuperar tu contraseña por favor presiona el botón</p>
                            <div style='text-align: center; padding: 20px 0px;'>
                                <a href='" . BASE_URL . "ws/recuperar/" . $token_email . "'>
                                    <button style='background-color: #593085; border-radius: 20px; color: #FFFFFF; padding: 20px; width: 35%;'>Recuperar contraseña</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>";
        $email = $this->general_mdl->sendemail($this->lang->line('recovery_title'), $body, $email, 'APPY Yastas');
        if ($email === true) {
            $this->general_mdl->writeLog("Recuperacion de contraseña usuario" . $email, "<info>");
            successResponse('', 'Revisa tu email para encontrar la liga', $this);
        } else {
            output_json($email, $this);
            /*Me quede en probar el envio de correo.*/
        }
    }


    /***********************************************************************
     *    Nota: Funcion para login de usarios
     ***********************************************************************/
    public function login()
    {

        if ($this->input->post() == []) {
            $_POST = json_decode(file_get_contents('php://input'), true);
        }

        $numero_empleado = preg_replace('/\s+/', '', $this->input->post("numero_empleado")); // trim($this->input->post("numero_empleado")," ");
        $email = $this->input->post("email");
        $password = $this->input->post("password");
        $web = ($this->input->post("web")) ? $this->input->post("web") : '';


        $validaPost = $this->general_mdl->validapost(array('password'), $this->input->post(), $this->defaultLang);
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $tipo = $this->input->post("tipo");
        //solo para yastas
        if ($email) {
            $email = $email; //al parecer mobile esta mandando el email en este campo    
        } else {
            $email = $numero_empleado; //al parecer mobile esta mandando el email en este campo
        }
        //en qa no se va a validar el email porque en algunos casos para probar yastas 
        //mandan el numero de empleado nadamas
        // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //     faildResponse($this->lang->line('error_mail_format'), $this);
        //     return;
        // }
        // $this->general_mdl->writeLog("Se recibe estos datos email " . $email . " password = " . $password.$numero_empleado, "<info>");
        $esta_registrado = $this->general_mdl->esta_registrado($email); //en yastas se manda numero de empleado
        $resultado = [];
        if ($esta_registrado == true) {
            $resultado = $this->general_mdl->Login($email, $password, $web, $tipo);
        } else {
            if ($esta_registrado == "invitacion") {
                faildResponse("El usuario no cuenta con una invitación", $this);
                return;
            } else {
                faildResponse("El usuario no esta registrado aún, debes completar el registro antes de iniciar sesión ", $this);
                return;
            }
        }

        if($resultado == "user_inactive"){
            $this->general_mdl->writeLog("Usuario no activo, contacta al administrador", "<warning>");
            return faildResponse("Usuario no activo, contacta al administrador", $this);
        }

        if ($resultado !== false && $resultado !== "false") {
            if ($web !== '') {
                $resultado[0]['session_web'] = 1;
                $this->session->set_userdata($resultado[0]);
            }
            $this->general_mdl->writeLog("Inicio de session correcto usuario " . $email, "<info>");
            $resultado[0]["imagenes_puntos"] = $this->user->obtener_imagenes_puntos($resultado[0]["score"]);
            $this->user->set_instrucciones_vistas($resultado[0]["user_id"]);
            successResponse($resultado, 'Inicio de session correcto', $this);
        } else {
            if ($resultado == "false") {
                $this->general_mdl->writeLog("Se alcanzo limite de intentos fallidos " . $email, "<warning>");
                faildResponse("Se ha alcanzado el limite de 3 intentos fallidos. La cuenta se ha bloqueado. Se envió un correo al email del usuario", $this);
            } else {
                $this->general_mdl->writeLog($this->lang->line('email_pass_error') . " " . $email . " ", "<warning>");
                if ($this->general_mdl->esta_bloqueado($email)) {
                    faildResponse("La cuenta se ha bloqueado.Se envió un correo al email del usuario", $this);
                } else {                
                    faildResponse($this->lang->line('email_pass_error'), $this);
                }
            }
        }
    }

    /***********************************************************************
     *    Nota: Funcion para registrar usuario
     ***********************************************************************/
    public function signin()
    {

        // if (count($this->input->post()) == 0) {
        //     $_POST = json_decode(file_get_contents('php://input'), true);
        // }

        $tipo_inicio = 'nuup';
        if ($tipo_inicio === 'nuup') {
            $data = array('password', 'name', 'business_name');
        } else {
            // $data = array(
            //     'email',
            //     'password',
            //     'name',
            //     'type_user',
            //     'country_id',
            //     'state_id',
            //     'number_employee',
            // );
            // if ($this->input->post('type_user') == USER_EXTERNAL) {
            //     $data = array(
            //         'email',
            //         'password',
            //         'name',
            //         'business_name',
            //         'type_user',
            //         'country_id',
            //         'state_id',
            //         'activity_id',
            //         'priority_crop_id',
            //     );
            // }
        }
        $validaPost = $this->general_mdl->validapost($data, $this->input->post(), $this->defaultLang);
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $params = $this->input->post();

        $email = $params['email'];
        $password = $params['password'];
        $name = $params['name'];
        $last_name = (isset($params['last_name'])) ? $params['last_name'] : '';
        $segundo_apellido = (isset($params["segundo_apellido"])) ? $params["segundo_apellido"] : '';
        $business_name = $params['business_name'];
        $job_name = (isset($params['job'])) ? $params['job'] : '';
        $phone = (isset($params['phone'])) ? $params['phone'] : '';
        $numero_empleado = (isset($params['number_employee'])) ? $params['number_employee'] : '';
        $register_no_invitation = 0;
        $web = ($this->input->post("web")) ? $this->input->post("web") : '';
        if ($email == '' || $email == null) {
            $email = "info" . $numero_empleado . "@appy.com.mx";
        }
        $this->general_mdl->writeLog("intento de registro usuario " . $email . " " . $numero_empleado, "<info>");
        // if (!valid_email($email)) {
        //     faildResponse($this->lang->line('error_mail_format'), $this);
        //     return;
        // }

        // if ($this->user_model->mailExists($email)) {
        //     faildResponse('El correo ya se encuentra registrado', $this);
        //     return;
        // }
        /***********************************************************************
         *    Autor: Mario Adrián Martínez Fernández
         *           mario.martinez.f@hotmail.es
         *    Fecha: 15/05/2020
         *    Nota: Se valida que el correo no cuente con invitacion por parte
         *             de uno de los creadores de empresa
         ***********************************************************************/
        if ($tipo_inicio === 'nuup') {
            $validate_invitation = $this->user_model->ValidateInvitation($email, $numero_empleado);
            if ($validate_invitation == 'in_use') {
                if ($this->general_mdl->esta_bloqueado($numero_empleado)) {
                    faildResponse('El usuario se enccuentra bloqueado, pide a un administrador que lo desbloquee', $this);
                    return;
                } else if ($validate_invitation == false)
                    faildResponse('El ID que intentas registrar ya cuenta con un registro', $this);
                return;
            }
            // if ($validate_invitation == 'plan_limit') {
            //     faildResponse('Tu plan actual ha llego a su limite de usuarios que puedes registrar.', $this);
            //     return;
            // }
            if ($validate_invitation == false) {
                faildResponse('Debes contar con una invitación para poder registrarte.', $this);
                return;
            }
            $is_invitation = false;
            if ($validate_invitation) {
                $is_invitation = true;
                $business_id = $validate_invitation['business_id'];
                $group_id = $validate_invitation['group_id'];
                //$rol = 3;
            } else {
                $business_id = $this->business_model->SaveBusiness(array(
                    "business_name" => $business_name,
                    "plan_id" => 1,
                ));
                //$rol = 2;
            }
            if ($job_name !== '') {
                if (!is_numeric($job_name)) {
                    $job_id = $this->jobs_model->findJobName($job_name, $business_id);
                    if ($job_id == false) {
                        faildResponse('Debes elegir un puesto', $this);
                        return;
                    }
                } else {
                    $job_id = $job_name;
                }
            } else {
                $job_id = 0;
            }
        } else {
            //$rol = 3;
            $is_invitation = false;
            $business_id = 500;
            $group_id = 0;
            if ($params['type_user'] == USER_EXTERNAL) {
                $validate_invitation = $this->user_model->ValidateInvitation($email);
                if ($validate_invitation == 'in_use') {
                    faildResponse('El correo que intentas registrar ya cuenta con un registro', $this);
                    return;
                }
                if ($validate_invitation == 'plan_limit') {
                    faildResponse('Tu plan actual ha llego a su limite de usuarios que puedes registrar.', $this);
                    return;
                }
                if ($validate_invitation) {
                    $is_invitation = true;
                    $group_id = $validate_invitation['group_id'];
                } else {
                    /***********************************************************************
                     *    Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
                     *           mario.martinez.f@hotmail.es
                     *    Nota: validamos si la empresa acepta registro sin invitacion
                     ***********************************************************************/
                    if ($this->general_mdl->GetConfigApp(EMPRESA_EXTERNOS, 'register_no_invitation') == 1) {
                        $register_no_invitation = 1;
                    } else {
                        faildResponse('No cuentas con invitación', $this);
                        return;
                    }
                }
                $job = 0;
            } else {
                /***********************************************************************
                 *    Autor: Mario Adrián Martínez Fernández   Fecha: 17/08/2020
                 *           mario.martinez.f@hotmail.es
                 *    Nota: Se valida que el numero de empleado no se encuentre en uso
                 *             y que el numero de empleado coincida con el email de
                 *             registro
                 ***********************************************************************/
                $employee = $this->user_model->ValidationNumberEmployee($params);
                if ($employee) {
                    $job = $employee['job_id'];
                    $group_id = $employee['group_id'];
                } else {
                    faildResponse('El número de empleado no existe o ya se encuentra en uso, por favor de validar con un administrador.', $this);
                    return;
                }
            }
        }

        if ($tipo_inicio === 'nuup') {
            $entity = array(
                "name" => $name,
                "last_name" => $last_name,
                "segundo_apellido" => $segundo_apellido,
                "email" => $email,
                "password" => $password,
                "business_id" => $business_id,
                "job_id" => $job_id,
                "phone" => $phone,

                "number_employee" => $numero_empleado,
                "terminos_aceptados" => $params["terminos_aceptados"],
                "aviso_aceptado" => $params["aviso_aceptado"]
            );
        } else {
            $entity = array(
                "name" => $name,
                "last_name" => $last_name,
                "segundo_apellido" => $segundo_apellido,
                "email" => $email,
                "password" => $password,
                "business_id" => $business_id,
                "job_id" => $job,
                "phone" => $phone,

                "user_type" => $params['type_user'],
                "county_id" => $params['country_id'],
                "state_id" => $params['state_id'],
                "area_id" => (isset($params['area_id'])) ? $params['area_id'] : 0,
                "number_employee" => (isset($params['number_employee'])) ? $params['number_employee'] : 0,
                "register_no_invitation" => $register_no_invitation,
                "terminos_aceptados" => $params["terminos_aceptados"],
                "aviso_aceptado" => $params["aviso_aceptado"]
            );
            if ($params['type_user'] == USER_EXTERNAL) {
                $entity = array(
                    "name" => $name,
                    "last_name" => $last_name,
                    "segundo_apellido" => $segundo_apellido,
                    "email" => $email,
                    "password" => $password,
                    "business_id" => $business_id,
                    "job_id" => $job,
                    "phone" => $phone,

                    "user_type" => $params['type_user'],
                    "county_id" => $params['country_id'],
                    "state_id" => $params['state_id'],
                    "activity_id" => $params['activity_id'],
                    "priority_crop_id" => $params['priority_crop_id'],
                    "register_no_invitation" => $register_no_invitation,
                    "terminos_aceptados" => $params["terminos_aceptados"],
                    "aviso_aceptado" => $params["aviso_aceptado"]
                );
            }
        }
        if (!isset($params["terminos_aceptados"]) || $params["terminos_aceptados"] != 1) {
            faildResponse("Debes aceptar los Terminos y condiciones para poder registrarte", $this);
            return;
        }
        if (!isset($params["aviso_aceptado"]) || $params["aviso_aceptado"] != 1) {
            faildResponse("Debes aceptar el Aviso de Privacidad para poder registrarte", $this);
            return;
        }
        $resultado = $this->general_mdl->Registro($entity);
        if ($resultado === 'email') {
            faildResponse($this->lang->line('login_error'), $this);
            return;
        }
        //si llego hasta aqui se debe registrar que el usuario acepto los terminos y condiciones
        $this->admin_mdl->AceptarAviso($numero_empleado);
        $this->admin_mdl->AceptarTerminos($numero_empleado);
        if ($resultado) {
            if ($tipo_inicio !== 'nuup') {
                if ($params['type_user'] == USER_INTERNAL) {
                    $this->user_model->UpdateEmployee($employee['id'], array('estatus' => 1));
                }
            }
            if ($is_invitation && $register_no_invitation === 0) {
                /***********************************************************************
                 *    Autor: Mario Adrián Martínez Fernández   Fecha: 15/05/2020
                 *           mario.martinez.f@hotmail.es
                 *    Nota: Si el registro es correcto y el correo tenia una invitacion
                 *             registramos que la invitacion ha sido reistrada.
                 ***********************************************************************/
                $this->user_model->ConfirmInvitation($numero_empleado);
            }
            if ($register_no_invitation == 1) {
                $msg = 'Un administrador aprobará tu registro.';
                //$resultado = '';
            } else {
                /***********************************************************************
                 *    Autor: Mario Adrián Martínez Fernández   Fecha: 11/9/2020
                 *           mario.martinez.f@hotmail.es
                 *    Nota: Registramos el grupo del usuario al que se asigno.
                 ***********************************************************************/
                if (isset($group_id)) {
                    $this->groups->SaverUser(
                        array(
                            'group_id' => $group_id,
                            'user_id' => $resultado['user_id'],
                        )
                    );
                }
                $msg = 'Registrado correctamente.';
            }
            /***********************************************************************
             *    Autor: Mario Adrián Martínez Fernández   Fecha: 11/11/2020
             *           mario.martinez.f@hotmail.es
             *    Nota: Registramos una notificacion por default
             ***********************************************************************/
            // $this->notification->RegisterNotification(
            //     array(
            //         'title' => 'Bienvenido',
            //         'notification' => 'Bienvenido has ingresado a la app de Universidad Agro',
            //         'service_id' => '',
            //         'user_id' => $resultado['user_id'],
            //     )
            // );
            /***********************************************************************
             *    Autor: Mario Adrián Martínez Fernández   Fecha: 12/14/2020
             *           mario.martinez.f@hotmail.es
             *    Nota: Funcion para registrar las categorias de una ruleta
             ***********************************************************************/
            // for ($i = 1; $i <= 8; $i++) {
            //     $save_categories = array(
            //         "name" => 'Categoria ' . $i,
            //         "points" => 0,
            //         "business_id" => $business_id,
            //     );
            //     $this->games->SaveRouletteQuiz($save_categories);
            // }
            if ($web !== '' && $register_no_invitation == 0) {
                $resultado[0]['session_web'] = 1;
                $this->session->set_userdata($resultado[0]);
            }
            if ($register_no_invitation == 1) {
                $resultado = array();
            }

            $this->general_mdl->writeLog("Registro correcto usuario" . $email, "<info>");
            successResponse($resultado, $msg, $this);
        } else {
            $this->general_mdl->writeLog("Error al registrar usuario " . $email, "<warning>");
            faildResponse($this->lang->line('not_signin_error'), $this);
        }
    }
    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández   Fecha: 10/27/2020
     *           mario.martinez.f@hotmail.es
     *    Nota: Funcion para encriptar password
     ***********************************************************************/
    public function fnEncrypt($sValue)
    {
        return rtrim(
            base64_encode(
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_256,
                    KEY_AES,
                    $sValue,
                    MCRYPT_MODE_ECB,
                    mcrypt_create_iv(
                        mcrypt_get_iv_size(
                            MCRYPT_RIJNDAEL_256,
                            MCRYPT_MODE_ECB
                        ),
                        MCRYPT_RAND
                    )
                )
            ),
            "\0"
        );
    }
    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández   Fecha: 15/05/2020
     *           mario.martinez.f@hotmail.es
     *    Nota: Funcion para obtener el nombre de la empresa en caso de que
     *             el correo se encuentra invitado por otra empresa ya creada.
     ***********************************************************************/
    public function ValidateBusiness()
    {
        $validaPost = $this->general_mdl->validapost(array('email'), $this->input->post(), $this->defaultLang);
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $validate = $this->user_model->ValidateInvitation($this->input->post('email'), $this->input->post("number_employee"));
        if ($validate == 'in_use') {
            faildResponse('El correo que intentas registrar ya cuenta con un registro.', $this);
            return;
        }
        if ($validate == 'plan_limit') {
            faildResponse('Tu plan actual ha llego a su limite de usuarios que puedes registrar.', $this);
            return;
        }
        if ($validate) {
            if ($validate['business_id'] == 61 || $validate["business_name"] == "Banorte") {
                $validacion_banorte = preg_match("/[\S]@banorte.com/", $this->input->post('email'));
                if ($validacion_banorte == 1) {
                    successResponse(array('business_name' => $validate['business_name'], 'business_id' => $validate['business_id']), 'Validar invitación', $this);
                } else {
                    faildResponse('No puede registrar un correo que no pertenezca al dominio de banorte.', $this);
                }
            } else {
                successResponse(array('business_name' => $validate['business_name'], 'business_id' => $validate['business_id']), 'Validar invitación', $this);
            }
        } else {
            faildResponse('El correo que intentas registrar no cuenta con invitación.', $this);
        }
    }
    /***********************************************************************
     *  Funcion para obtener todas las empresas registradoas
     ***********************************************************************/
    public function getBusiness()
    {
        $business = $this->business_model->fetchAll("business");
        //en este servicio se necesita obtener el token para saber el id del usuario que hace la peticion
        $this->general_mdl->writeLog("Listado de todas las empresas ", "<info>");
        successResponse($business, "Business", $this);
    }
    /***********************************************************************
     *  Funcion para obtener los puestos por empresan e idioma registrado
     ***********************************************************************/
    public function getJobs()
    {

        $business_id = $this->input->post("business_id");
        if (!isset($business_id)) {
            $business_id = 18;
        }
        // $validaPost = $this->general_mdl->validapost(array('business_id'), $this->input->post(), $this->defaultLang);
        // if (!$validaPost['success']) {
        //     faildResponse($validaPost['msg'], $this);
        //     return;
        // }
        $jobs = $this->jobs_model->fetchAllByBusinessId($business_id);
        $this->general_mdl->writeLog("Listado de todos los puestos por empresa ", "<info>");
        successResponse($jobs, "Business", $this);
    }

    public function getAllServices()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $services = $this->services_model->fetchAll($valida_token['business_id']);
        // Se da formato de moneda el precio del servicio
        if ($services) {
            foreach ($services as $key => $value) {
                $services[$key]['formatted_price'] = "$ " . number_format($value['price'], 2);
            }
        }
        $this->general_mdl->writeLog("Listado de todos los servicios usuario " . $valida_token["user_id"], "<info>");
        successResponse($services, "Services", $this);
    }
    /***********************************************************************
     *  Funcion para obtener el carro de compras del usuario
     ***********************************************************************/
    public function getShoppingCart()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $shopping_cart = $this->_userShoppingCart($valida_token['user_id']);
        $this->general_mdl->writeLog("Listado del carro de compras usuario " . $valida_token["user_id"], "<info>");
        successResponse($shopping_cart, 'Shopping Cart', $this);
    }
    /***********************************************************************
     *  Funcion para agregar un item al carro de compras del usuarios
     ***********************************************************************/
    public function addItemShoppinCart()
    {
        $token = $this->input->post("token");
        $service_id = $this->input->post("service_id");
        $quantity = $this->input->post("quantity");
        $discount = $this->input->post("discount");

        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('service_id', 'quantity'), $this->input->post(), $this->defaultLang);
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        /***********************************************************************
         *    Autor: Mario Adrián Martínez Fernández   Fecha: 23/06/2020
         *           mario.martinez.f@hotmail.es
         *    Nota: Se valida que el item no haya sido seleccionado previamente
         ***********************************************************************/
        if ($this->shoppingcart_model->validateItemShopping($valida_token["user_id"], $service_id)) {
            $this->general_mdl->writeLog("Error al agregar item al carro de compras usuario " . $valida_token["user_id"] . " service_id " . $service_id, "<warning>");
            faildResponse("El item ya ha sido agregado previamente.", $this);
            return;
        }
        $entity = array(
            "user_id" => $valida_token["user_id"],
            "quantity" => $quantity,
            "discount" => 0,
            "service_id" => $service_id,
        );
        /***********************************************************************
         *    Autor: Mario Adrián Martínez Fernández   Fecha: 23/06/2020
         *           mario.martinez.f@hotmail.es
         *    Nota: Validamos que el item no este agregado ya por el usuario
         ***********************************************************************/
        $this->shoppingcart_model->insert($entity);
        $shopping_cart = $this->_userShoppingCart($valida_token['user_id']);
        $this->general_mdl->writeLog("Item agregado al carro de compras usuario " . $valida_token["user_id"] . " service_id " . $service_id, "<info>");
        successResponse($shopping_cart, 'Shopping Cart - item added', $this);
    }
    /***********************************************************************
     *  Funcion para eliminar un item de carrito de compras
     ***********************************************************************/
    public function deleteItemShoppingCart()
    {
        $token = $this->input->post("token");
        $item_id = $this->input->post("item_id");

        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('item_id'), $this->input->post(), $this->defaultLang);
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        $result = $this->shoppingcart_model->delete($item_id, $valida_token['user_id']);

        if ($this->shoppingcart_model->delete($item_id, $valida_token['user_id'])) {
            $shopping_cart = $this->_userShoppingCart($valida_token['user_id']);
            $this->general_mdl->writeLog("Eliminacion de item shoping cart usuario " . $valida_token["user_id"] . " item_id " . $item_id, "<info>");
            successResponse($shopping_cart, 'Shopping Cart - item deleted', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar item shoping cart usuario " . $valida_token["user_id"] . " item_id " . $item_id, "<warning>");
            faildResponse($this->lang->line('invalid_item_msg'), $this);
        }
    }
    /***********************************************************************
     *  Funcion para pagar un carrito de compra
     ***********************************************************************/
    public function PayShoppingCart()
    {

        $token = $this->input->post("token");

        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $shopping_cart = $this->_userShoppingCart($valida_token['user_id']);
        $services = "";
        foreach ($shopping_cart["cart_items"] as $key => $value) {
            $this->shoppingcart_model->delete($value["id"], $valida_token['user_id']);
            $services .= "<tr>
                            <td>" . $value["service_name"] . "</td>
                            <td>" . $value["category_name"] . "</td>
                            <td>" . $value["description"] . "</td>
                            <td>" . $value["price"] . "</td>
                            <td>" . $value["quantity"] . "</td>
                            <td>" . $value["subtotal"] . "</td>
                        </tr>";
        }

        $body = "<table>
                    <tr>
                        <th>Servicio</th>
                        <th>Categoria</th>
                        <th>Descripcion</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                    " . $services . "
                </table>";

        if ($this->general_mdl->sendemail('Servicios cotizacion', $body, "mario@kreativeco.com", "APPY Yastas")) {
            $this->general_mdl->writeLog("Carrito de compras pagado usuario " . $valida_token["user_id"], "<info>");
            successResponse('', $this->lang->line('mail_sended'), $this);
        } else {
            $this->general_mdl->writeLog("Error al pagar carrito de compras usuario " . $valida_token["user_id"], "<warning>");
            faildResponse($this->lang->line('mail_not_sended'), $this);
        }
    }
    /***********************************************************************
     *  Funcion para obtener el perfil del usuario
     ***********************************************************************/
    public function getProfile()
    {
        if ($this->input->post() == []) {
            $_POST = json_decode(file_get_contents('php://input'), true);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        // echo json_encode($valida_token);
        if ($valida_token["user_type"] == "Integrante") {
            $valida_token['users_business'] = [];
        } else {
            $valida_token['users_business'] = $this->user->ListUsersBusiness($valida_token['business_id']);
        }
        if (!$valida_token) {
            $this->general_mdl->writeLog("Listado de perfil usuario " . $valida_token["user_id"], "<info>");
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $insignias = $this->general_mdl->ObtenerInsignias($valida_token["user_id"], $valida_token["business_id"]);

        $valida_token["imagenes_puntos"] = $this->user->obtener_imagenes_puntos($valida_token["score"]);
        /* polizas */
        $valida_token["polizas"] = $this->polizas_mdl->getPolizasByUserId($valida_token["user_id"], $valida_token["business_id"]);
        $this->general_mdl->writeLog("Cosulta perfil usuario " . $valida_token["user_id"], "<warning>");
        $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "home");
        successResponse($valida_token, 'User Profile', $this, $insignias);
    }

    /***********************************************************************
     *  Funcion para actualizar el perfil del usuario
     ***********************************************************************/
    public function updateProfile()
    {
        $token = $this->input->post("token");
        $name = $this->input->post("name");
        $last_name = $this->input->post("last_name");
        $phone = $this->input->post("phone");
        $email = $this->input->post("email");

        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('name', 'last_name'), $this->input->post(), $this->defaultLang);
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $entity = array(
            'name' => $name,
            'last_name' => $last_name,
            'phone' => $phone,
        );
        if ($email != null) {
            $entity["email"] = $email;
        }
        $this->user_model->update($valida_token['user_id'], $entity);
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "home");
        $this->general_mdl->writeLog("Actualizacion de perfil usuario " . $valida_token["user_id"], "<info>");
        successResponse($valida_token, "Tu perfil se actulizó correctamente", $this);
    }

    /***********************************************************************
     *  Funcion para actualizar el perfil del usuario
     ***********************************************************************/
    public function updateProfilePhoto()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $files = $_FILES['profile_photo'];
        $profile_photo = "";
        if ($files['name'] != "") {
            $file = $this->general_mdl->GuardarArchivos($_FILES, "", $valida_token['business_id']);
            //Si la subida de archivos fue exitosa
            if ($file['success']) {
                //Si se subió el correctamente el archivo deseado ("media_path"), guardamos el nombre asignado
                if (isset($file['success_files']['profile_photo'])) {
                    $profile_photo = $file['success_files']['profile_photo'];
                }
            } else {
                //si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
                faildResponse($file['msg'], $this);
                return;
            }
            $url_photo = "https://$_SERVER[HTTP_HOST]";
            
            // url para kreativeco (nupi)
            if($valida_token['business_id'] == 13){
                $url_photo = $url_photo . PHOTO_URL_NUPI;
            }

            // url para bimbo
            if($valida_token['business_id'] == 83){
                $url_photo = $url_photo . PHOTO_URL_BIMBO;
            }

            $entity = array(
                'profile_photo' => $url_photo . "business_" . $valida_token['business_id'] . "/" . $profile_photo,
            );
            $this->user_model->update($valida_token['user_id'], $entity);
            $userDt = $this->general_mdl->UsuarioDetalleToken($token);
            if (isset($_SESSION['id_user']) || isset($_SESSION['user_id'])) {
                $this->session->set_userdata($url_photo . "business_" . $valida_token['business_id'] . "/" . $profile_photo);
            }
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "home");
            $this->general_mdl->writeLog("Actualizacion de foto de perfil usuario " . $valida_token["user_id"], "<info>");
            successResponse($userDt, "Tu foto de perfil se actualizó", $this);
            return;
        } else {
            $this->general_mdl->writeLog("Error al actualizar foto de perfil usuario " . $valida_token["user_id"], "<warning>");
            faildResponse($this->lang->line('no_photo_file'), $this);
            return;
        }
    }

    public function sendMemberRequest()
    {
        $token = $this->input->post("token");
        $member_mail = $this->input->post("email") != 'null' ? $this->input->post("email") : '';
        $number_employee = $this->input->post("number_employee");
        $apellido = $this->input->post("last_name");
        $job_id = $this->input->post("job_id");
        $phone = $this->input->post("phone");
        $id_comercio = $this->input->post("id_comercio");

        if(strlen($number_employee) > 6){
            faildResponse("Solo se permite un máximo de 6 carateres para el id operador", $this);
            return;
        }

        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('number_employee'), $this->input->post(), $this->defaultLang);
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $validate_invitation = $this->user_model->ValidarInvitacionDuplicada($member_mail, $number_employee);
        if ($validate_invitation == false) {
            faildResponse('El correo/Id que intentas registrar ya cuenta con un registro completo.', $this);
            return;
        }

        $group_id = ($this->input->post('group_id') != 'null') ? $this->input->post('group_id') : $valida_token["business_id"];
        $nombre = ($this->input->post("name") != 'null') ? $this->input->post("name") : '';
        $apellido = ($this->input->post("last_name") != 'null') ? $this->input->post("last_name") : '';

        $asesor_ = ($this->input->post("id_asesor") != 0) ? $this->input->post("id_asesor") : null;
        $region_ = ($this->input->post("id_region"));
        $rol_id = $this->input->post("rol_id");
        //si en el admin quien esta guardando la invitacion es un asesor
        //se obtiene la region y el asesor de sus datos
        if ($valida_token["rol_id"] == 6) {
            $region = $valida_token["id_region"];
            $asesor = $valida_token["user_id"];
        } else {
            $region = $region_;
            $asesor = $asesor_;
        }
        $fecha_alta = $this->input->post("fecha_alta_cliente");

        $respuesta = $this->user_model->SaveInvitation(array('email' => $member_mail, 'business_id' => $valida_token['business_id'], 'group_id' => $group_id, 'name' => $nombre, 'last_name' => $apellido, 'number_employee' => $number_employee, 'region_id' => $region, 'id_asesor' => $asesor, 'fecha_alta_cliente' => $fecha_alta, 'rol_id' => $rol_id, "job_id" => $job_id, "id_comercio" => $id_comercio, "phone" => $phone, "tipo" => 2, "id_creador" => $valida_token["user_id"]));

        $this->general_mdl->writeLog("Envio de invitacion usuario " . $valida_token["user_id"] . " invitado " . $member_mail, "<info>");
        successResponse($respuesta, $this->lang->line('mail_sended'), $this);
    }

    public function sendMemberRequestAdmin()
    {
        $token = $this->input->post("token");
        $member_mail = $this->input->post("email") != 'null' ? $this->input->post("email") : '';
        $number_employee = $this->input->post("number_employee");
        $apellido = $this->input->post("last_name");
        $job_id = $this->input->post("job_id");
        $phone = $this->input->post("phone");
        $id_comercio = $this->input->post("id_comercio");

        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('number_employee'), $this->input->post(), $this->defaultLang);
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $validate_invitation = $this->user_model->ValidarInvitacionDuplicada($member_mail, $number_employee);
        if ($validate_invitation == false) {
            faildResponse('El correo/Id que intentas registrar ya cuenta con un registro completo.', $this);
            return;
        }

        $group_id = ($this->input->post('group_id') != 'null') ? $this->input->post('group_id') : $valida_token["business_id"];
        $nombre = ($this->input->post("name") != 'null') ? $this->input->post("name") : '';
        $apellido = ($this->input->post("last_name") != 'null') ? $this->input->post("last_name") : '';

        $asesor_ = ($this->input->post("id_asesor") != 0) ? $this->input->post("id_asesor") : null;
        $region_ = ($this->input->post("id_region"));
        $rol_id = $this->input->post("rol_id");

        if ($valida_token["rol_id"] == 6) {
            $region = $valida_token["id_region"];
            $asesor = $valida_token["user_id"];
        } else {
            $region = $region_;
            $asesor = $asesor_;
        }
        $fecha_alta = $this->input->post("fecha_alta_cliente");

        $respuesta = $this->user_model->SaveInvitation(array('email' => $member_mail, 'business_id' => $valida_token['business_id'], 'group_id' => $group_id, 'name' => $nombre, 'last_name' => $apellido, 'number_employee' => $number_employee, 'region_id' => $region, 'id_asesor' => $asesor, 'fecha_alta_cliente' => $fecha_alta, 'rol_id' => $rol_id, "job_id" => $job_id, "id_comercio" => $id_comercio, "phone" => $phone, "tipo" => 1, "id_creador" => $valida_token["user_id"]));
        // }
        $this->general_mdl->writeLog("Envio de invitacion usuario " . $valida_token["user_id"] . " invitado " . $member_mail, "<info>");
        successResponse($respuesta, $this->lang->line('mail_sended'), $this);
    }

    public function sendMemberRequestMC()
    {
        $token = $this->input->post("token");
        $member_mail = $this->input->post("email") != 'null' ? $this->input->post("email") : '';
        $number_employee = $this->input->post("number_employee");
        $apellido = $this->input->post("last_name");
        $job_id = $this->input->post("job_id");
        $phone = $this->input->post("phone");
        $id_comercio = $this->input->post("id_comercio");

        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('number_employee'), $this->input->post(), $this->defaultLang);
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $validate_invitation = $this->user_model->ValidarInvitacionDuplicada($member_mail, $number_employee);
        if ($validate_invitation == false) {
            faildResponse('El correo/Id que intentas registrar ya cuenta con un registro completo.', $this);
            return;
        }

        $group_id = ($this->input->post('group_id') != 'null') ? $this->input->post('group_id') : $valida_token["business_id"];
        $nombre = ($this->input->post("name") != 'null') ? $this->input->post("name") : '';
        $apellido = ($this->input->post("last_name") != 'null') ? $this->input->post("last_name") : '';

        $asesor_ = ($this->input->post("id_asesor") != 0) ? $this->input->post("id_asesor") : null;
        $region_ = ($this->input->post("id_region"));
        $rol_id = $this->input->post("rol_id");


        if ($valida_token["rol_id"] == 6) {
            $region = $valida_token["id_region"];
            $asesor = $valida_token["user_id"];
        } else {
            $region = $region_;
            $asesor = $asesor_;
        }
        $fecha_alta = $this->input->post("fecha_alta_cliente");

        $respuesta = $this->user_model->SaveInvitation(array('email' => $member_mail, 'business_id' => $valida_token['business_id'], 'group_id' => $group_id, 'name' => $nombre, 'last_name' => $apellido, 'number_employee' => $number_employee, 'region_id' => $region, 'id_asesor' => $asesor, 'fecha_alta_cliente' => $fecha_alta, 'rol_id' => $rol_id, "job_id" => $job_id, "id_comercio" => $id_comercio, "phone" => $phone, "tipo" => 3, "id_creador" => $valida_token["user_id"]));
        $this->general_mdl->writeLog("Envio de invitacion usuario " . $valida_token["user_id"] . " invitado " . $member_mail, "<info>");
        successResponse($respuesta, $this->lang->line('mail_sended'), $this);
    }


    public function getLibrary()
    {
        $token = $this->input->post("token");
        $media_type = $this->input->post("media_type");

        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $result = $this->library_model->fetchAllByMediaType($media_type);
        $this->general_mdl->writeLog("Listado de biblioteca multimedia usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, "Multimedia", $this);
    }

    public function newPost()
    {
        $token = $this->input->post("token");
        $description = $this->input->post("descripcion");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('descripcion'), $this->input->post(), $this->defaultLang);
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        $files = $_FILES['imagen_post'];
        $imagen_post = "";
        if ($files['name'] != "") {
            $file = $this->general_mdl->GuardarArchivos($_FILES, "", $valida_token['business_id']);
            //Si la subida de archivos fue exitosa
            if ($file['success']) {
                //Si se subió el correctamente el archivo deseado ("media_path"), guardamos el nombre asignado
                if (isset($file['success_files']['imagen_post'])) {
                    $imagen_post = $file['success_files']['imagen_post'];
                }
            } else {
                //si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
                faildResponse($file['msg'], $this);
                return;
            }
            $entity = array(
                'image_path' => PHOTO_URL . "business_" . $valida_token['business_id'] . "/" . $imagen_post,
                'user_id' => $valida_token['user_id'],
                'wall_description' => $description,
            );

            $this->wall_model->insert($entity);
            $this->general_mdl->writeLog("Alta de post usuario " . $valida_token["user_id"], "<info>");
            successResponse('', 'New Post', $this);

            return;
        } else {
            $this->general_mdl->writeLog("Error al crear post usuario " . $valida_token["user_id"], "<info>");
            faildResponse($this->lang->line('no_post'), $this);
            return;
        }
    }

    public function getWall()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $result = $this->wall_model->getAll();

        foreach ($result as $key => $value) {
            $comments = $this->comments_model->getCommentsByPost($value["id"]);
            $result[$key]['comments'] = $comments;
        }
        $this->general_mdl->writeLog("Listado de Muro usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Wall', $this);
    }

    public function newCommentPost()
    {
        $token = $this->input->post("token");
        $comment = $this->input->post("comentario");
        $post_id = $this->input->post("post_id");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('comentario', 'post_id'), $this->input->post(), $this->defaultLang);
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        $entity = array(
            "comment" => $comment,
            "user_id" => $valida_token['user_id'],
            "post_id" => $post_id,
        );

        $this->comments_model->insert($entity);
        $this->general_mdl->writeLog("Alta de comentario de Muro usuario " . $valida_token["user_id"] . " post " . $post_id, "<info>");
        successResponse('', 'New Comment Post', $this);
    }

    /***********************************************************************
     *  Funcion para Modelar el resultado de carrito de compras
     ***********************************************************************/
    public function _userShoppingCart($user_id)
    {
        $shopping_cart = $this->shoppingcart_model->getMyShoppingCart($user_id);
        $response = array();
        $cart_items = array();
        $total = 0;
        foreach ($shopping_cart as $key => $value) {

            $subtotal = $value['price'] * $value['quantity'];
            $total = $total + $subtotal;

            $cart_items[] = array(
                "id" => $value["id"],
                "service_name" => $value["service_name"],
                "category_name" => $value["category_name"],
                "description" => $value["description"],
                "image" => $value["image"],
                "price" => "$ " . number_format($value["price"], 2),
                "quantity" => $value["quantity"],
                "subtotal" => "$ " . number_format($subtotal, 2),
            );
        }
        $response['cart_total'] = "$ " . number_format($total, 2);
        $response['cart_items'] = $cart_items;
        return $response;
    }
    /***********************************************************************
     *    Nota: Funcion para enviar notificacion push a un solo token
     ***********************************************************************/
    public function enviarNotificacionPush()
    {
        $token = $this->input->post("token");
        $token_firebase = $this->input->post("token_firebase");
        $notificacion = $this->input->post("notificacion");
        $titulo = $this->input->post("titulo");

        $validaPost = $this->general_mdl->validapost(array('token_firebase', 'notificacion', 'titulo'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('El token no es valido.', $this);
            return;
        }
        $tokens = array();
        array_push($tokens, $token_firebase);
        $enviar_notificacion = $this->general_mdl->EnviarNotificacionPush($tokens, $notificacion, $titulo);
        $this->general_mdl->writeLog("Envio de notificacion push un solo token usuario " . $valida_token["user_id"], "<info>");
        echo $enviar_notificacion;
    }
    /***********************************************************************
     *    Nota: Enviar notificacion push multiple
     ***********************************************************************/
    public function enviarMultiplePush()
    {
        $token = $this->input->post("token");
        $titulo = $this->input->post("titulo");
        $notificacion = $this->input->post("notificacion");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('El token no es valido.', $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('titulo', 'notificacion'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        /***********************************************************************
         *    Nota: Se obtiene los tokens existentes en la BD
         ***********************************************************************/
        $tokens = $this->general_mdl->SendMultipleDevices();
        if (!$tokens) {
            $this->general_mdl->writeLog("Error al enviar notificacion push multiple usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('No existen devices registrados', $this);
            return;
        }
        /***********************************************************************
         *    Nota: Se envia notificacion a los multiples tokens
         ***********************************************************************/
        $enviar_notificacion = $this->general_mdl->EnviarNotificacionPush($tokens, $notificacion, $titulo);
        $this->general_mdl->writeLog("Envio de notificacion push multiple usuario " . $valida_token["user_id"], "<info>");
        echo $enviar_notificacion;
    }
    /***********************************************************************
     *    Nota: Funcion para actualizar o agregar nuevos devices
     ***********************************************************************/
    public function registerDevice()
    {
        $token = $this->input->post("token");
        $token_firebase = $this->input->post("token_firebase");
        $validaPost = $this->general_mdl->validapost(array('token_firebase'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('El token no es valido.', $this);
            return;
        }
        $respuesta = $this->general_mdl->RegistroDevices($valida_token['user_id'], $token_firebase);
        if ($respuesta) {
            $this->general_mdl->writeLog("Registro de dispositivo usuario " . $valida_token["user_id"], "<info>");
            successResponse('', 'El registro se ha completado satisfactoriamente.', $this);
        } else {
            $this->general_mdl->writeLog("Error al registrar dispositivo usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('El registro no se completo correctamente.', $this);
        }
    }
    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández         Fecha: 08/05/2020
     *           mario.martinez.f@hotmail.es
     *    Nota: Funcion para enviar un correo con los servicios contratados
     ***********************************************************************/
    public function SendPurchase()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('El token no es valido.', $this);
            return;
        }
        $respuesta = $this->shoppingcart_model->getMyShoppingCart($valida_token['user_id']);
        if (count($respuesta) > 0) {
            $html = '<div style="padding-bottom: 10px;">
				Nombre: ' . $valida_token['name_complete'] . '<br>
				Correo: ' . $valida_token['email'] . '<br>
			</div><br><table style="border: 1px solid black;">
				<thead>
					<tr>
						<th style="border: 1px solid black;">Servicio</th>
						<th style="border: 1px solid black;">Descripción</th>
						<th style="border: 1px solid black;">Categoría</th>
						<th style="border: 1px solid black;">Cantidad</th>
						<th style="border: 1px solid black;">Descuento</th>
						<th style="border: 1px solid black;">Precio</th>
					</tr>
				</thead>
				<tbody>
			';
            foreach ($respuesta as $index => $value) {
                $html .= '<tr>
				 <td style="border: 1px solid black;">' . $value['service_name'] . '</td>
			     <td style="border: 1px solid black;">' . $value['description'] . '</td>
			     <td style="border: 1px solid black;">' . $value['category_name'] . '</td>
			     <td style="border: 1px solid black;">' . $value['quantity'] . '</td>
			     <td style="border: 1px solid black;">' . $value['discount'] . '</td>
			     <td style="border: 1px solid black;">' . $value['price'] . '</td>
				</tr>';
            }
            $html .= '</tbody></table>';
            $email = $this->general_mdl->sendemail('Compra', $html, 'luis@kreativeco.com', 'APPY Yastas');
            if ($email === true) {
                /***********************************************************************
                 *    Autor: Mario Adrián Martínez Fernández   Fecha: 03/10/2020
                 *           mario.martinez.f@hotmail.es
                 *    Nota: Si el email se envia correctamente eliminamos los servicios de
                 *             el carrito de compras y los pasamos a una tabla temporal.
                 ***********************************************************************/
                $this->shoppingcart_model->ServicesPurchase($valida_token['user_id'], $valida_token['business_id']);
                $this->general_mdl->writeLog("Envio de email servicios contratados usuario " . $valida_token["user_id"], "<info>");
                successResponse('', 'Compra hecha correctamente', $this);
            } else {
                output_json($email, $this);
            }
        } else {
            $this->general_mdl->writeLog("Error al enviar email servicios contratados usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('No existén registro en el carrito de compras.', $this);
        }
    }
    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *           mario.martinez.f@hotmail.es
     *    Nota: Funcion para obtener los servicios contratados por empresa
     *             en base al usuario que regrese el token
     ***********************************************************************/
    public function HiredServices()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('El token no es valido.', $this);
            return;
        }
        $respuesta = $this->services_model->HiredServices($valida_token);
        
        if($valida_token["business_id"] == 83){

            $respuestaArray = [];
            foreach($respuesta as $value){
                if($value["service_name"] == "Biblioteca"){
                    $respuestaArray[0] = $value;
                }
                if($value["service_name"] == "Capacitacion"){
                    $respuestaArray[1] = $value;
                }
                if($value["service_name"] == "Chat"){
                    $respuestaArray[2] = $value;
                }
                if($value["service_name"] == "Retos"){
                    $respuestaArray[3] = $value;
                }
                if($value["service_name"] == "Ranking"){
                    $respuestaArray[4] = $value;
                }
                if($value["service_name"] == "Preguntas"){
                    $respuestaArray[5] = $value;
                }
                if($value["service_name"] == "Juegos"){
                    $respuestaArray[6] = $value;
                }
                if($value["service_name"] == "Podcast"){
                    $respuestaArray[7] = $value;
                }
                if($value["service_name"] == "Muro"){
                    $respuestaArray[8] = $value;
                }
                if($value["service_name"] == "Comunidad de aprendizaje"){
                    $respuestaArray[9] = $value;
                }

                // return print_r($respuestaArray);

            }
            ksort($respuestaArray);
            $respuesta = $respuestaArray;
        }

        $this->user->set_video_visto($valida_token["user_id"]);
        $noticias =  $this->post->obtener_noticia($valida_token["business_id"], $valida_token["user_id"]);
        if (count($noticias) > 0) {
            $noticias[0]["vacio"] = 0;
            $noticia = array("noticia" => $noticias[0]);
        } else
            $noticia = array("noticia" => ["vacio" => 1]);
        if ($respuesta) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "home");
            $this->general_mdl->writeLog("Listado de servicios contratados usuario " . $valida_token["user_id"], "<info>");
            successResponse($respuesta, 'Listado de servicios contratados', $this, $noticia);
        } else {
            $this->general_mdl->writeLog("Error al listar servicios contratados usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('No existen registros.', $this);
        }
    }
    
    /***********************************************************************
     *  Autor: Rodolfo Terrones Ruiz   Fecha: 23/06/2020
     *  Nota: Obtenemos el ranking de usuarios con la posibilidad de filtrarlos
     *      por empresa y puesto
     ***********************************************************************/
    public function getRanking()
    {
        $token = $this->input->post("token");
        $filtro = $this->input->post("filtro");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('El token no es valido.', $this);
            return;
        }

        $where = "";
        // Filtro 1 es por empresa , else por puesto
        if ($filtro == 1) {
            $where = " WHERE u.id_region = " . $valida_token['id_region'];
            $result = $this->user_model->ranking($where, $valida_token["user_id"]);
        } else if ($filtro == 0) {
            $where = " WHERE u.job_id = " . $valida_token['job_id'] . " and u.business_id = " . $valida_token["business_id"];
            $result = $this->user_model->ranking($where, $valida_token["user_id"]);
        } else if ($filtro == 2 || $filtro == 3) {
            $where = " where u.business_id = " . $valida_token["business_id"];
            $result = $this->user_model->ranking($where, $valida_token["user_id"]);
        }
        // Modificamos y agregamos campos en el arreglo
        foreach ($result as $key => $value) {
            $result[$key]["position"] = $key + 1;
            $result[$key]["score"] = $value["score"] . " Puntos";
            $gemas = $this->user->obtener_gemas($value["id"]);

            $monstruo = $this->user->obtener_monstruo($value["id"]);
            /* successResponse($monstruo[0], 'Listado de ranking', $this);
            return; */
            $result[$key]["gemas"] = $gemas;
            if($monstruo){
                $result[$key]["monstruo"] = $monstruo[0];
                $result[$key]["nivel"] = $monstruo[0]["nivel"];
            }else{
                $result[$key]["monstruo"] = null;
                $result[$key]["nivel"] = null;
            }
        }
        $this->general_mdl->writeLog("Listado ranking usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Listado de ranking', $this);
    }
    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández   Fecha: 10/22/2020
     *           mario.martinez.f@hotmail.es
     *    Nota: Funcion para ocultar o mostrar un services
     ***********************************************************************/
    public function ServicesHideShow()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('El token no es valido.', $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('services_id'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $data = $this->input->post();
        $data['business_id'] = $valida_token['business_id'];
        $respuesta = $this->services_model->ServicesHideShow($data);
        if ($respuesta) {
            $this->general_mdl->writeLog("Actualizacion de servicio usuario " . $valida_token["user_id"], "<info>");
            successResponse($respuesta, 'El servicio se ha actualizado correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar servicio usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('El servicio no se ha actualizado correctamente.', $this);
        }
    }

    public function DebeBloquearCaptura()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('El token no es valido.', $this);
            return;
        }

        $business_id = $valida_token['business_id'];
        $respuesta = $this->services_model->ObtenerBloquearCaptura($business_id);
        $this->general_mdl->writeLog("Consulta variable de bloqueo de captura usuario " . $valida_token["user_id"], "<info>");
        successResponse($respuesta, 'Var que define si se bloquea la captura o no', $this);
    }

    public function guardarSeccionUsuario()
    {
        $token = $this->input->post('token');
        $id_seccion = $this->input->post('id_seccion');
        $fecha = $this->input->post('fecha');
        $hora = $this->input->post('hora');
        $latitud = $this->input->post("latitud");
        $longitud = $this->input->post("longitud");

        $validaPost = $this->general_mdl->validapost(array('token', 'id_seccion', 'fecha', 'hora'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        // echo json_encode($valida_token);
        if (!$valida_token) {
            faildResponse('El token no es valido.', $this);
            return;
        } else {
            $id_usuario = $valida_token['user_id'];
        }

        if (count($_FILES) > 0) {
            $archivos = $this->general_mdl->GuardarArchivos($_FILES, $ruta = "capturas", $valida_token["business_id"]);
            // echo json_encode($archivos);
            if (isset($archivos['success_files']['imagen'])) {
                // $imagen = 'uploads/seccionusuario/'.$subir_archivos['imagen']['ruta'];
                $imagen = $archivos['success_files']['imagen'];
            }
        }

        $datos = array(
            'id_usuario' => $id_usuario,
            'id_seccion' => $id_seccion,
            'imagen' => $imagen,
            'fecha' => $fecha,
            'hora' => $hora,
            'latitud' => $latitud,
            'longitud' => $longitud
        );
        //print_r($datos);exit;
        $like = $this->publicacion->guardarSeccionUsuario($datos);
        // $this->general_mdl->sendPushToAdmin();
        // $body = "<p>Se generó una alerta con la app</p>
        // <p><strong>Usuario : </strong>" . $valida_token["name_complete"] . "</p>
        // <p><strong>Coordenadas : </strong> Lat : $latitud, Long : $longitud</p>
        // <p><strong>Hora : </strong>" . $hora . "</p>";
        // $this->general_mdl->sendemail("Alerta", $body, "luis@kreativeco.com", "Alerta Admin");
        // if ($valida_token["business_id"] == 82) {
        //     $this->general_mdl->sendemail("Alerta", $body, "j.rubiell@documentcontrol.com", "Alerta Admin");
        //     $this->general_mdl->sendemail("Alerta", $body, "juanrubiell@yahoo.com", "Alerta Admin");
        // }
        $this->general_mdl->writeLog("Registro de seccion screenshot usuario " . $valida_token["user_id"], "<info>");
        successResponse($like, 'Registro guardado con éxito', $this);
    }

    public function ObtenerTerminos()
    {
        if(empty($this->input->post("business_id"))) {
            return validationResponse("No se ha ingresado el id de la empresa", $this);
        }
        $business_id = $this->input->post("business_id");
        $respuesta = $this->admin_mdl->ObtenerTerminos($business_id);
        if ($respuesta) {
            $this->general_mdl->writeLog("Consulta de terminos y condiciones usuario ", "<info>");
            successResponse($respuesta, 'Terminos y condiciones', $this);
        } else {
            $this->general_mdl->writeLog("Error al consultar terminos y condiciones usuario ", "<warning>");
            faildResponse('Error al obtener terminos y condiciones', $this);
        }
    }

    public function ObtenerAvisoPrivacidad()
    {
        if(empty($this->input->post("business_id"))) {
            return validationResponse("No se ha ingresado el id de la empresa", $this);
        }
        $business_id = $this->input->post("business_id");
        $respuesta = $this->admin_mdl->ObtenerAvisoPrivacidad($business_id);

        if ($respuesta) {
            $this->general_mdl->writeLog("Consulta de aviso privacidad usuario ", "<info>");
            successResponse($respuesta, 'Aviso de privacidad', $this);
        } else {
            $this->general_mdl->writeLog("Error al consultar aviso privacidad usuario ", "<warning>");
            faildResponse('Error al obtener aviso de privacidad', $this);
        }
    }

    public function AceptarAviso()
    {
        if ($this->input->post() == []) {
            $_POST = json_decode(file_get_contents('php://input'), true);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('El token no es valido.', $this);
            return;
        }
        $user_id = $valida_token['user_id'];
        $respuesta = $this->admin_mdl->AceptarAviso($user_id);
        if ($respuesta) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "home");
            $this->general_mdl->writeLog("Aceptacion de aviso privacidad usuario " . $valida_token["user_id"], "<info>");
            successResponse($respuesta, 'Se acepto Aviso de privacidad', $this);
        } else {
            $this->general_mdl->writeLog("Error al aceptar aviso privacidad usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al aceptar aviso de privacidad', $this);
        }
    }

    public function AceptarTerminos()
    {
        if ($this->input->post() == []) {
            $_POST = json_decode(file_get_contents('php://input'), true);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('El token no es valido.', $this);
            return;
        }
        $user_id = $valida_token['user_id'];
        $respuesta = $this->admin_mdl->AceptarTerminos($user_id);
        if ($respuesta) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "home");
            $this->general_mdl->writeLog("Aceptacion de terminos y condiciones usuario " . $valida_token["user_id"], "<info>");
            successResponse($respuesta, 'Se acepto Terminos', $this);
        } else {
            $this->general_mdl->writeLog("Error al aceptar terminos y condiciones usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al aceptar Terminos', $this);
        }
    }


    public function reporteEliminados()
    {
        $reporte = [];
        $fecha = $this->input->post("fecha");
        $gestor = @fopen(BASE_PATH . "application/logs/nup_log" . $fecha . ".log", "r");
        echo json_encode($gestor);
        if ($gestor) {
            while (($bufer = fgets($gestor, 4096)) !== false) {
                if (strpos($bufer, "Eliminacion") != false) {
                    // $reporte .= $bufer ."\n";
                    array_push($reporte, $bufer);
                }
                // echo $bufer;
            }
            if (!feof($gestor)) {
                echo json_encode("Error: fallo inesperado de fgets()\n");
            }
            fclose($gestor);
        }
        successResponse($reporte, 'Reporte de logs ', $this);
    }

    //pongo los parametros porque este servicio lo voy a utilizar para obtener la url del diploma de los
    //usuarios dentro del reporte capacitaciones obligatorias
    //se establece que si no se le manda algun parametro al servicio estos parametros sean nulos para que no
    //cause error con lo que ya funciona
    public function obtenerDiploma($user_id = null, $business_id = null, $name_complete = null, $id_insignia_ = null)
    {
        $token = $this->input->post("token");
        $id_insignia = $this->input->post("id_insignia");
        $valida_token = [];

        if ($token == null) {
            $name_complete = str_replace("-", ' ', $name_complete); //reemplazamos los guiones medios por espacios como venia oroginalmente
            $token = "1234567890" . $name_complete . "-" . ($id_insignia * $user_id) . "-a1s1d2f3g1s";
        }

        if ($user_id != null) {
            $valida_token["user_id"] = $user_id;
            $valida_token["business_id"] = $business_id;
            $valida_token["name_complete"] = str_replace("-", ' ', $name_complete);
            $valida_token["name_complete"] =  str_replace(array('1a', '1e', '1i', '1o', '1u', '1n', '1A', '1E', '1I', '1O', '1U', '1N'), array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'), $valida_token["name_complete"]);
            // $valida_token["name_complete"] = str_replace(array('1a', '1e', '1i', '1o', '1u', '1n'), array('á', 'é', 'í', 'ó', 'ú', 'ñ'), $valida_token["name_complete"]);
            $id_insignia = $id_insignia_;
        } else {
            $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        }
        $bandera = $this->general_mdl->comprobar_insignia_ganada($id_insignia, $valida_token["user_id"]);
        if (!$bandera) {
            faildResponse('Completa la capacitación para obtener el diploma.', $this);
            return;
        }
        if (!$valida_token) {
            faildResponse('El token no es valido.', $this);
            return;
        }
        $ruta_real = "";
        if ($id_insignia > 0) {
            $insignia = $this->general_mdl->obtenerInsigniaById($id_insignia);
            // if($id_insignia == 21){
            //     $insignia["nombre"] = "Yastás Básico";
            // }
            // if($id_insignia == 22){
            //     $insignia["nombre"] = "Manejo de estrés";
            // }
            // if($id_insignia == 23){
            //     $insignia["nombre"] = "¿Cuál es el negocio de tus sueños?";
            // }
            $dompdf = new Dompdf();
            $dompdf = new Dompdf(array('enable_remote' => true));
            //obtener la fecha de la evaluacion o del fin de la capacitacion
            //validar que los usuarios no se puedan dar de alta usuarios si no tienen numero de empleado
            //los numeros de empleado tienen una longitud siempre de 6 caracteres validar tambien  
            $meses = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
            $fecha = $this->user->obtener_fecha_cap($id_insignia, $valida_token["user_id"]); //date("d") . "/" . ($meses[date("n") - 1]) . "/" . date("Y");
            // echo json_encode(base_url().'assets/fuentes/AmorSansPro.ttf');
            // src: local("Amor") url("'.base_url().'assets/fuentes/AmorSansPro.ttf");
            $html = '
                 <style>

                 
                 @font-face {
                    font-family: "AmorBold";
                    font-style: normal;
                    font-weight: normal;
                    src: local("AmorBold") url("lib/fonts/AmorSansPro-Bold.ttf") format("truetype");
                  }
                  @font-face {
                    font-family: "Amor";
                    font-style: normal;
                    font-weight: normal;
                    src: local("Amor") url("lib/fonts/AmorSansPro.ttf") format("truetype");
                  }
                    *{
                        // background:#eee;
                        font-family : "AmorBold"!important;
                        z-index :2;
                    }
                    html,body{
                        padding: 0!important;
                        margin : none!important;
                    }
                    body{
                        height: 100%;
                        widht:100%;
                    }
                 </style>
                 <img src="' . base_url() . 'assets/img/diploma.jpg" style="width:1100px; height:820px;position:fixed; z-index : 1; top: 0; left : 0;">
                 <p style="font-size:65px; position:fixed; top:230px; margin-left:20%!important; width:80%!important;text-align:center!important; font-weight:bolder!important;">' . $valida_token["name_complete"] . '</p>
                 <p style="font-size:30px; position:fixed; top:540px; margin-left:20%!important; width:80%!important;text-align:center!important; font-weight:bolder;!important">' . $insignia["titulo"] . '</p>
                 <p style="font-size:25px; position:fixed; top:650px; margin-left:20%!important; width:80%!important;text-align:center!important; font-weight:bolder;!important">Fecha : ' . $fecha . '</p>';
            $dompdf->loadHtml($html);

            $dompdf->setPaper('letter', 'landscape');
            $dompdf->render();

            $output = $dompdf->output();



            $ruta_real = BASE_URL . "uploads/business_" . $valida_token["business_id"] . "/diplomas/" . str_replace(" ", '', substr($token, 10, 20)) . ".pdf";
            $ruta_guardado = BASE_PATH . "uploads/business_" . $valida_token["business_id"] . "/diplomas/" .  str_replace(" ", '', substr($token, 10, 20)) . ".pdf";
            file_put_contents($ruta_guardado, $output);
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "home");
            successResponse(['ruta' => $ruta_real], 'Ruta dilpoma de insignia ', $this);
        } else {
            faildResponse('¡Felicidades con tu logro! Esta insignia no otorga diploma', $this);
        }
    }

    public function alta_masiva()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $return = [];
        require_once  APPPATH . 'libraries/SimpleXLSX.php';
        require_once APPPATH . 'libraries/SimpleXLSXGen.php';
        $path = 'uploads/';
        $filename = 'import-data';
        $config['upload_path'] = 'uploads/';
        $config['file_name'] = $filename;
        $config['overwrite'] = TRUE;
        $config['allowed_types'] = '*';
        $config['max_size'] = '1024';

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('uploadFile')) {
            // echo json_encode($this->upload);
            $error = array('error' => $this->upload->display_errors());
        } else {
            $data = array('upload_data' => $this->upload->data());
        }
        if (empty($error)) {
            if (!empty($data['upload_data']['file_name'])) {
                $import_xls_file = $data['upload_data']['file_name'];
            } else {
                $import_xls_file = 0;
            }
            $inputFileName =  $import_xls_file;
            $inserdata = [];
            $array = [];
            $obj = [];
            $obj["name"] = "Nombre";
            $obj["last_name"] = "Apellido";
            $obj["email"] = "Email";
            $obj["number_employee"] = "Numero de empleado";
            $obj["group_id"] = "Grupo";
            $obj["region"] = "Region";
            $obj["asesor"] = "Asesor";
            $obj["rol_id"] = "Rol";
            $obj["fecha_alta_cliente"] = "Fecha";
            $obj["id_comercio"] = "Id comercio";
            $obj["puesto"] = "Puesto";
            $obj["razon"] = "Causa por la que no se pudo guardar";
            array_push($array, $obj);
            try {
                //echo json_encode(BASE_PATH . "/uploads/" . $inputFileName);
                // if ($xlsx = new SimpleXLSX('/home/yastasapp/public_html/nuup/uploads/' . $inputFileName)) {
                if ($xlsx = new SimpleXLSX(BASE_PATH . '/uploads/' . $inputFileName)) {
                    $filas = $xlsx->rows();
                    //echo json_encode($filas);
                    // print_r( $xlsx->rows() );
                    for ($i = 1; $i < count($filas); $i++) {
                        $name = $filas[$i][0]; //
                        $last_name = $filas[$i][1];
                        $email = $filas[$i][2];
                        $number_employee = $filas[$i][3];
                        $grupo_id = $this->user->obtenerGrupoId($filas[$i][4]); //se guarda en bd
                        $grupo = $filas[$i][4]; //se regresa en caso de error
                        $region = $filas[$i][5];
                        $asesor = $filas[$i][6];
                        $rol = $filas[$i][7];
                        $fecha_alta = date("Y-m-d", strtotime(str_replace("/", "-", $filas[$i][8])));
                        $valida_numero_empleado = $this->user->ValidaNumeroEmpleado($number_employee);
                        $id_comercio = $filas[$i][9];
                        $puesto = $filas[$i][10];
                        $id_puesto = $this->user->valida_puesto($puesto, $valida_token["business_id"]);
                        $validaIdRol = $this->user->ValidaRolId($rol);
                        if (!$validaIdRol) {
                            $id_rol = $rol;
                        } else {
                            $id_rol = $validaIdRol;
                        }

                        $validaRegiones = $this->user->ValidaNumeroDeRegiones($region);
                        $validaAG = $this->user->ValidaAsesorGerente($asesor);
                        $id_asesor = $validaAG;

                        //se prepara el objeto en caso de que se requiera
                        //solo si por alguna razon el usuario no se puede guardar
                        //se devuelve este mismo objeto junto con la razon del fallo
                        $obj = [];
                        $obj["name"] = $name;
                        $obj["last_name"] = $last_name;
                        $obj["email"] = $email;
                        $obj["numero_empleado"] = $number_employee;
                        $obj["group_id"] = $grupo;
                        $obj["region"] = $region;
                        $obj["asesor"] = $asesor;
                        $obj["rol_id"] = $rol;
                        $obj["fecha_alta_cliente"] = $fecha_alta;
                        $obj["id_comercio"] = $id_comercio;
                        $obj["puesto"] = $puesto;

                        //si es true las regiones [2]
                        if ($id_asesor) {
                            if ($validaRegiones[2]) {
                                //si es 2 es varias regiones
                                if ($validaRegiones[1] == 2) {
                                    //rgreso la primera id de la region
                                    $id_region = $validaRegiones[0];
                                    //obtengo el array de todos los ids de regiones
                                    $regiones = $validaRegiones[3];
                                } else {
                                    //si no es 2 es solamente una region
                                    $id_region = $validaRegiones[0];
                                }
                                $valida_email = false; //$this->user->validarEmail($email);
                                if (!$valida_numero_empleado) { //validar numero de empleado
                                    if (!$valida_email) {
                                        if ($grupo_id != "") {
                                            if ($id_comercio != '' && $id_comercio != null) {
                                                if ($id_puesto != 0) {
                                                    $idback = $this->user_model->SaveInvitation(array(
                                                        'email' => $email,
                                                        'business_id' => $valida_token['business_id'],
                                                        'group_id' => $grupo_id,
                                                        'name' => $name,
                                                        'last_name' => $last_name,
                                                        'number_employee' => $number_employee,
                                                        'region_id' => $id_region,
                                                        'id_asesor' => $id_asesor,
                                                        'rol_id' => $id_rol,
                                                        'fecha_alta_cliente' => $fecha_alta,
                                                        'id_comercio' => $id_comercio,
                                                        'job_id' => $id_puesto
                                                    ));
                                                    if ($id_rol == 5) {
                                                        $this->user_model->SaveRegionesGerente(implode(',', $regiones), $idback);
                                                    }
                                                    // $this->user->guardar_invitacion($name, $last_name, $email, $number_employee, $grupo_id, $valida_token["business_id"]);
                                                } else {
                                                    $obj["razon"] = "No se encontro el puesto, el puesto debe existir antes de agregar integrantes";
                                                    array_push($array, $obj);
                                                }
                                            } else {
                                                $obj["razon"] = "Debe ingresar el id de comercio";
                                                array_push($array, $obj);
                                            }
                                        } else {
                                            $obj["razon"] = "No se encontro el grupo, el grupo debe existir antes de agregar integrantes";
                                            array_push($array, $obj);
                                        }
                                    } else {
                                        $obj["razon"] = "El email ya existe con otro usuario";
                                        array_push($array, $obj);
                                    }
                                } else {
                                    $obj["razon"] = "Numero de empleado duplicado";
                                    array_push($array, $obj);
                                }
                            } else {
                                $obj["razon"] = "No existen las regiones en la BD";

                                array_push($array, $obj);
                            }
                        } else {
                            $obj["razon"] = "No existe el asesor ";
                            array_push($array, $obj);
                        }
                    }
                    if (count($array) > 1) {
                        $xlsx = new SimpleXLSXGen();
                        $xlsx->addSheet($array, 'Correcciones');
                        $xlsx->saveAs(BASE_PATH . 'uploads/Correcciones.xlsx');
                        $return["archivo"] = BASE_URL . "uploads/Correcciones.xlsx";
                    } else {
                        // $this->altas_bajas(); //no se para que funciona este
                    }
                    successResponse($return, 'Usuarios registrados correctamente', $this);
                } else {
                    // echo SimpleXLSX::parseError();
                    faildResponse(SimpleXLSX::parseError(), $this);
                }
                $result = true;
                if ($result) {
                    successResponse($return, 'Usuarios registrados correctamente', $this);
                } else {
                    faildResponse("error", $this);
                }
            } catch (Exception $e) {
                faildResponse('Error al cargar el archivo "' . pathinfo($inputFileName, PATHINFO_BASENAME)
                    . '": ' . $e->getMessage(), $this);
                // die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
                //     . '": ' . $e->getMessage());
            }
        } else {
            faildResponse('Se encontraron errores al subir el archivo, por favor verifique que el archivo este en formato .xlsx y que el nombre no contenga acentos ni espacios.', $this);
            // echo "Se encontraron errores al subir el archivo, por favor verifique que el archivo este en formato .xlsx y que el nombre no contenga acentos ni espacios.";
        }
    }

    public function comprobarSesion()
    {
        $token = $this->input->post("token");
        $id_insignia = $this->input->post("id_insignia");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('Error al obtener la sesion', $this);
            return;
        }
        successResponse([], 'La sesión esta activa', $this);
    }

    public function ObtenerRegiones()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $regiones = $this->general_mdl->obtener_regiones($valida_token["user_id"], $valida_token["rol_id"], $valida_token["business_id"]);
        successResponse($regiones, 'Listado de regiones', $this);
    }

    public function ObtenerContratos()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $contratos = $this->general_mdl->obtener_contratos($valida_token["user_id"], $valida_token["rol_id"]);
        successResponse($contratos, 'Listado de Contratos', $this);
    }

    public function ObtenerPlantas()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $contratos = $this->general_mdl->obtener_plantas($valida_token["user_id"], $valida_token["rol_id"]);
        successResponse($contratos, 'Listado de Plantas', $this);
    }

    public function ObtenerAsesores()
    {
        if ($this->input->post() == []) {
            $_POST = json_decode(file_get_contents('php://input'), true);
        }
        $token = $this->input->post("token");
        // $id_region = $this->session->userdata("id_region");
        $id_region = $this->input->post("id_region");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $regiones = $this->general_mdl->obtener_asesores($id_region, $valida_token["user_id"], $valida_token["rol_id"]);
        successResponse($regiones, 'Listado de asesores por region', $this);
    }
    
    public function ObtenerAsesoresMultiple()
    {
        if ($this->input->post() == []) {
            $_POST = json_decode(file_get_contents('php://input'), true);
        }
        $token = $this->input->post("token");
        // $id_region = $this->session->userdata("id_region");
        $id_region = $this->input->post("regiones");
        $id_region = json_decode($id_region);
        if (count($id_region) > 0) {
            $id_region = join(",", $id_region); //trae varias regiones
            $regiones = $this->general_mdl->obtener_asesores_multiples($id_region);
            successResponse($regiones, 'Listado de asesores por region', $this);
        } else {
            successResponse([], 'Listado de asesores por region', $this);
        }
    }

    function eliminar_completos_obligatoria()
    {
        $token = $this->input->post("token");
        $id_capacitacion = $this->input->post("id_capacitacion");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $regiones = $this->capacitacion->eliminar_completos_obligatoria($id_capacitacion, $valida_token["user_id"]);
        successResponse($regiones, 'Listado de asesores por region', $this);
    }

    function obtener_rol_actual()
    {
        successResponse(["rol" => $this->session->userdata("rol_id")], 'rol actual', $this);
    }

    function obtener_menu_general()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $menu = $this->admin_mdl->obtener_modulos_por_rol_angular($valida_token['rol_id']);
        successResponse($menu, "menu general", $this);
    }

    function subFireWebToken()
    {
        $token = $_POST["token"];

        // key=AAAAa0wtIjE:APA91bEHwub8QlU7obcfdNfc33leTQgU2Ya-L1QBfr1przc3STQ8yI2S4LZjA5HtAz4iOveDzWZYNplzACdEE9K9lsnCNZrU1ur_Wn8C95JVOZpwMF4H2v_E38LWUSwZ8BIoHUx9fkjR

        $headers =  array(
            'Content-type: application/json',
            'Authorization: key=AAAAivNJ3m8:APA91bFXDu_y08RjGASREAYN8WdH222DQwyz3mjRjqW7i_9rEe4CXqy6yPzLf-ma_Zqt2oxPL8IwG9T3zmy6MSUWf5nN04zufnnfVWIzh6U3GEURor64qmsukSvAZ9A-13dQWYm05hFv'
        );

        $url = "https://iid.googleapis.com/iid/v1/" . $token . "/rel/topics/admin_alert_";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);


        $jsonArray = array(
            "status_code" => 200,
            "success" => true,
            "Message" => $result,
        );
        return json_encode($jsonArray);
    }

    function obtener_info_registro()
    {
        $token = $this->input->post("token");
        $numero_empleado = $this->input->post("numero_empleado");
        //$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $info = $this->user->obtener_info_registro($numero_empleado);
        if ($info) {
            successResponse($info, 'Informacion para registro', $this);
        } else {
            faildResponse("Error al obtener la información", $this);
        }
    }

    function crear_url_dinamico()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('el token no es valido', $this);
            return;
        }

        $id_servicio = $this->input->post("id_servicio");
        $id_topic = $this->input->post("id_topic");

        try {
            // $api_key_web = "AIzaSyAGSbaSFEHcAYFI2sOD6ZWL8nHxsc-NLcI";
            //para yastsa
            $api_key_web = "AIzaSyDCasKGFE49JuZmKdCrfCTeMyNbr9IdHiI";
            $url = 'https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=' . $api_key_web;

            $data = array(
                "dynamicLinkInfo" => array(
                    //este es el prefijo para nuup segun entiendo es el que hay que poner en el manifest de las aplicaciones
                    // "domainUriPrefix" => "appnuup.page.link",
                    //para yastas
                    "domainUriPrefix" => "kreativeco.page.link",
                    //este es el link para nuup si se crean mas no hay que olvidar agregar el id_servicio e id_topic
                    // "link" => "https://appnuup.page.link/?link=https://nuup.com/nupi&apn=com.kreativeco.nuup&isi=1544310282&ibi=com.kreativeco.appnuup&service_id=$id_servicio&id_topic=$id_topic&st=Nupi+App&sd=Hola,+esta+es+una+prueba+de+links+din%C3%A1micos+en+la+app&si=https://kreativeco.com/nuup_cemex/assets/img/info_logo.png",
                    //para yastas
                    "link" => "https://kreativeco.page.link/?link=https://appy.com.mx/&apn=com.kreativeco.appy&isi=1544310282&ibi=com.kreativeco.appnuup.yastas&service_id=$id_servicio&id_topic=$id_topic&st=Appy&sd=¡Hola!,+Compartieron+contigo+información+sobre+Yastás,+¡no+tardes+en+checarla!&si=https://appy.com.mx/info_logo.png",
                    // "androidInfo" => array(
                    //     "androidPackageName" => "com.kreativeco.nuup"
                    // ),
                    // "iosInfo" => array(
                    //     "iosBundleId" => "com.kreativeco.appnuup"
                    // )
                    // para yastas
                    "androidInfo" => array(
                        "androidPackageName" => "com.kreativeco.appy"
                    ),
                    "iosInfo" => array(
                        "iosBundleId" => "com.kreativeco.appnuup.yastas"
                    )
                )
            );

            $headers = array('Content-Type: application/json');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $data = curl_exec($ch);

            if ($data == false) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
            curl_close($ch);
            $short_url = json_decode($data);
            // echo json_encode($data);
            if (isset($short_url->error)) {
                return $short_url->error->message;
            } else {
                if ($id_servicio == 4) {
                    $this->library->sumar_compartido($id_topic, $valida_token["user_id"]);
                } else if ($id_servicio == 13) {
                    $this->podcast->sumar_compartido($id_topic, $valida_token["user_id"]);
                }
                successResponse($short_url->shortLink, 'Informacion para registro', $this);
            }
        } catch (Exception $e) {

            trigger_error(
                sprintf(
                    'Curl failed with error #%d: %s',
                    $e->getCode(),
                    $e->getMessage()
                ),
                E_USER_ERROR
            );
        }
    }

    function cambiar_contrasena()
    {
        $numero_empleado = $this->input->post("numero_empleado");
        $id_comercio = $this->input->post("id_comercio");
        $password = $this->input->post("password");
        $validar = $this->admin_mdl->validar_ids_comercio_empleado($numero_empleado, $id_comercio);
        if ($validar) {
            $result = $this->admin_mdl->actualizar_password($password, $numero_empleado);
            if ($result) {
                successResponse($result, 'La contraseña se actualizó correctamente', $this);
            } else {
                faildResponse('Error al actualizar contraseña', $this);
            }
        } else {
            faildResponse('Los ids no coinciden, no es posible actualizar la contraseña', $this);
        }
    }

    function insertar_miles_datos()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('el token no es valido', $this);
            return;
        }
        // for ($i = 0; $i < 100000; $i++) {
        $this->library->sumar_compartido(563, $valida_token["user_id"]);
        // }
        successResponse(true, 'Se insertaron miles de datos', $this);
    }

    public function actualizacion_masiva()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $return = [];
        require_once  APPPATH . 'libraries/SimpleXLSX.php';
        require_once APPPATH . 'libraries/SimpleXLSXGen.php';
        $path = 'uploads/';
        $filename = 'import-data';
        $config['upload_path'] = 'uploads/';
        $config['file_name'] = $filename;
        $config['overwrite'] = TRUE;
        $config['allowed_types'] = '*';
        $config['max_size'] = '1024';

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('uploadFile')) {
            // echo json_encode($this->upload);
            $error = array('error' => $this->upload->display_errors());
        } else {
            $data = array('upload_data' => $this->upload->data());
        }
        if (empty($error)) {
            if (!empty($data['upload_data']['file_name'])) {
                $import_xls_file = $data['upload_data']['file_name'];
            } else {
                $import_xls_file = 0;
            }
            $inputFileName =  $import_xls_file;
            $inserdata = [];
            $array = [];
            $obj = [];
            $obj["name"] = "Nombre";
            $obj["last_name"] = "Apellido";
            $obj["email"] = "Email";
            $obj["number_employee"] = "Numero de empleado";
            $obj["group_id"] = "Grupo";
            $obj["region"] = "Region";
            $obj["asesor"] = "Asesor";
            $obj["rol_id"] = "Rol";
            $obj["fecha_alta_cliente"] = "Fecha";
            $obj["id_comercio"] = "Id comercio";
            $obj["puesto"] = "Puesto";
            $obj["razon"] = "Causa por la que no se pudo guardar";
            array_push($array, $obj);
            try {
                //echo json_encode(BASE_PATH . "/uploads/" . $inputFileName);
                // if ($xlsx = new SimpleXLSX('/home/yastasapp/public_html/nuup/uploads/' . $inputFileName)) {
                if ($xlsx = new SimpleXLSX(BASE_PATH . '/uploads/' . $inputFileName)) {
                    $filas = $xlsx->rows();
                    //echo json_encode($filas);
                    // print_r( $xlsx->rows() );
                    for ($i = 1; $i < count($filas); $i++) {
                        $name = $filas[$i][0]; //
                        $last_name = $filas[$i][1];
                        $email = $filas[$i][2];
                        $number_employee = $filas[$i][3];
                        $grupo_id = $this->user->obtenerGrupoId($filas[$i][4]); //se guarda en bd
                        $grupo = $filas[$i][4]; //se regresa en caso de error
                        $region = $filas[$i][5];
                        $asesor = $filas[$i][6];
                        $rol = $filas[$i][7];
                        $fecha_alta = date("Y-m-d", strtotime(str_replace("/", "-", $filas[$i][8])));
                        $valida_numero_empleado = $this->user->ValidaNumeroEmpleado($number_employee);
                        $id_comercio = $filas[$i][9];
                        $puesto = $filas[$i][10];
                        $id_puesto = $this->user->valida_puesto($puesto, $valida_token["business_id"]);
                        $validaIdRol = $this->user->ValidaRolId($rol);
                        if (!$validaIdRol) {
                            $id_rol = $rol;
                        } else {
                            $id_rol = $validaIdRol;
                        }

                        $validaRegiones = $this->user->ValidaNumeroDeRegiones($region);
                        $validaAG = $this->user->ValidaAsesorGerente($asesor);
                        $id_asesor = $validaAG;

                        //se prepara el objeto en caso de que se requiera
                        //solo si por alguna razon el usuario no se puede guardar
                        //se devuelve este mismo objeto junto con la razon del fallo
                        $obj = [];
                        $obj["name"] = $name;
                        $obj["last_name"] = $last_name;
                        $obj["email"] = $email;
                        $obj["numero_empleado"] = $number_employee;
                        $obj["group_id"] = $grupo;
                        $obj["region"] = $region;
                        $obj["asesor"] = $asesor;
                        $obj["rol_id"] = $rol;
                        $obj["fecha_alta_cliente"] = $fecha_alta;
                        $obj["id_comercio"] = $id_comercio;
                        $obj["puesto"] = $puesto;

                        //si es true las regiones [2]
                        if ($id_asesor) {
                            if ($validaRegiones[2]) {
                                //si es 2 es varias regiones
                                if ($validaRegiones[1] == 2) {
                                    //rgreso la primera id de la region
                                    $id_region = $validaRegiones[0];
                                    //obtengo el array de todos los ids de regiones
                                    $regiones = $validaRegiones[3];
                                } else {
                                    //si no es 2 es solamente una region
                                    $id_region = $validaRegiones[0];
                                }
                                $valida_email = false; //$this->user->validarEmail($email);
                                if (!$valida_numero_empleado) { //validar numero de empleado
                                    if (!$valida_email) {
                                        if ($grupo_id != "") {
                                            if ($id_comercio != '' && $id_comercio != null) {
                                                if ($id_puesto != 0) {
                                                    $idback = $this->user_model->SaveInvitation(array(
                                                        'email' => $email,
                                                        'business_id' => $valida_token['business_id'],
                                                        'group_id' => $grupo_id,
                                                        'name' => $name,
                                                        'last_name' => $last_name,
                                                        'number_employee' => $number_employee,
                                                        'region_id' => $id_region,
                                                        'id_asesor' => $id_asesor,
                                                        'rol_id' => $id_rol,
                                                        'fecha_alta_cliente' => $fecha_alta,
                                                        'id_comercio' => $id_comercio,
                                                        'job_id' => $id_puesto
                                                    ));
                                                    if ($id_rol == 5) {
                                                        $this->user_model->SaveRegionesGerente(implode(',', $regiones), $idback);
                                                    }
                                                    // $this->user->guardar_invitacion($name, $last_name, $email, $number_employee, $grupo_id, $valida_token["business_id"]);
                                                } else {
                                                    $obj["razon"] = "No se encontro el puesto, el puesto debe existir antes de agregar integrantes";
                                                    array_push($array, $obj);
                                                }
                                            } else {
                                                $obj["razon"] = "Debe ingresar el id de comercio";
                                                array_push($array, $obj);
                                            }
                                        } else {
                                            $obj["razon"] = "No se encontro el grupo, el grupo debe existir antes de agregar integrantes";
                                            array_push($array, $obj);
                                        }
                                    } else {
                                        $obj["razon"] = "El email ya existe con otro usuario";
                                        array_push($array, $obj);
                                    }
                                } else {
                                    $obj["razon"] = "Numero de empleado duplicado";
                                    array_push($array, $obj);
                                }
                            } else {
                                $obj["razon"] = "No existen las regiones en la BD";

                                array_push($array, $obj);
                            }
                        } else {
                            $obj["razon"] = "No existe el asesor ";
                            array_push($array, $obj);
                        }
                    }
                    if (count($array) > 1) {
                        $xlsx = new SimpleXLSXGen();
                        $xlsx->addSheet($array, 'Correcciones');
                        $xlsx->saveAs(BASE_PATH . 'uploads/Correcciones.xlsx');
                        $return["archivo"] = BASE_URL . "uploads/Correcciones.xlsx";
                    } else {
                        // $this->altas_bajas(); //no se para que funciona este
                    }
                    successResponse($return, 'Usuarios registrados correctamente', $this);
                } else {
                    // echo SimpleXLSX::parseError();
                    faildResponse(SimpleXLSX::parseError(), $this);
                }
                $result = true;
                if ($result) {
                    successResponse($return, 'Usuarios registrados correctamente', $this);
                } else {
                    faildResponse("error", $this);
                }
            } catch (Exception $e) {
                faildResponse('Error al cargar el archivo "' . pathinfo($inputFileName, PATHINFO_BASENAME)
                    . '": ' . $e->getMessage(), $this);
                // die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
                //     . '": ' . $e->getMessage());
            }
        } else {
            faildResponse('Se encontraron errores al subir el archivo, por favor verifique que el archivo este en formato .xlsx y que el nombre no contenga acentos ni espacios.', $this);
            // echo "Se encontraron errores al subir el archivo, por favor verifique que el archivo este en formato .xlsx y que el nombre no contenga acentos ni espacios.";
        }
    }

    public function enviar_mail()
    {
        $archivo = $_FILES["imagen"];
        $respuesta = $this->general_mdl->sendemail("hola", "hola", "sergio@kreativeco.com", "checo", $archivo);
        echo json_encode($respuesta);
    }


    // =========================================================================================
    // =============  APARTADO DE RETOS ========================================================
    // =========================================================================================

    // obtener todos los estatus de los retos
    public function getEstatusRetos(){
        if(empty($this->input->post("token"))) {
            return faildResponse("No se ha ingresado el token del usuario", $this);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $estatus = $this->challenges->getEstatusReto();

        if(!empty($estatus)){
            $this->general_mdl->writeLog("Lista de estatus", "<info>");
            return successResponse($estatus, 'Lista de estatus', $this);

        }else if(empty($estatus)){
            $this->general_mdl->writeLog("Lista de estatus", "<info>");
            return successResponse($estatus, 'Lista de estatus', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener el listado de estatus", "<warning>");
            return faildResponse("Error al obtener el listado de estatus", $this);
        }
    }

    // obtener todos los becarios del tutor
    public function getBecarios(){
        if(empty($this->input->post("token"))) {
            return faildResponse("No se ha ingresado el token del usuario", $this);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        if($valida_token["rol_id"] == 2){
            $becarios = $this->challenges->getBecarios(null, $valida_token["business_id"], true);
        }else{
            $becarios = $this->challenges->getBecarios($valida_token["user_id"], $valida_token["business_id"], false);
        }

        if(!empty($becarios)){
            $this->general_mdl->writeLog("Lista de becarios asignados al tutor", "<info>");
            return successResponse($becarios, 'Lista de becarios asignados al tutor', $this);

        }else if(empty($becarios)){
            $this->general_mdl->writeLog("Lista de becarios asignados al tutor", "<info>");
            return successResponse($becarios, 'Lista de becarios asignados al tutor', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener el listado de becarios asignados al tutor", "<warning>");
            return faildResponse("Error al obtener el listado de becarios asignados al tutor", $this);
        }
    }


    // todos los retos del tutor
    public function getRetosTutor(){
        if($this->input->post() == []) {
            return faildResponse("No se ha ingresado el token del usuario", $this);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        if($valida_token["rol_id"] != 8) {
            return faildResponse("El usuario logueado no tiene acceso a este endpoint", $this);
        }

        $retos = $this->challenges->getRetosTutor($valida_token["user_id"], $valida_token["business_id"]);

        if(count($retos) > 0){
            $this->general_mdl->writeLog("Listado de todos los retos", "<info>");
            return successResponse($retos, 'Listado de todos los retos', $this);

        }else if(count($retos) == 0){
            $this->general_mdl->writeLog("Listado de todos los retos", "<info>");
            return successResponse($retos, 'Listado de todos los retos', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de retos", "<warning>");
            return faildResponse("Error al obtener listado de retos", $this);
        }
    }


    // Este endpoint nos permite obtener el total de retos de todos los becarios que estan con estatus pendiente de feedback.
    public function getRetosPendientesTutor(){
        if($this->input->post() == []) {
            return faildResponse("No se ha ingresado el token del usuario", $this);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        if($valida_token["rol_id"] != 8) {
            return faildResponse("El usuario logueado no tiene acceso a este endpoint", $this);
        }

        $retosPendientes = $this->challenges->getRetosPendientesTutor($valida_token["user_id"], $valida_token["business_id"]);

        if(count($retosPendientes) > 0){
            $this->general_mdl->writeLog("Listado de todos los retos de todos los becarios pendientes", "<info>");
            return successResponse($retosPendientes, 'Listado de todos los retos de todos los becarios pendientes', $this);

        }else if(count($retosPendientes) == 0){
            $this->general_mdl->writeLog("Listado de todos los retos pendientes", "<info>");
            return successResponse($retosPendientes, 'Listado de todos los retos de todos los becarios pendientes', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de retos de todos lo becarios pendientes", "<warning>");
            return faildResponse("Error al obtener listado de retos de todos lo becarios pendientes", $this);
        }
    }


    // crear reto
    public function crearReto(){
        if($this->input->post() == []) {
            return faildResponse("No se ha ingresado dato alguno", $this);
        }

        if(empty($this->input->post("token"))) {
            return faildResponse("No se ha ingresado el token del usuario", $this);
        }

        if(empty($this->input->post("becario_id"))) {
            return faildResponse("No se ha ingresado el id del becario", $this);
        }

        if(empty($this->input->post("reto"))) {
            return faildResponse("No se ha ingresado el nombre del reto", $this);
        }

        if(empty($this->input->post("detalles"))) {
            return faildResponse("No se ha ingresado los detalles del reto", $this);
        }

        if(empty($this->input->post("mes"))) {
            return faildResponse("No se ha ingresado el mes en curso. El valor a ingresar debe ser de la siguiente lista disponible 01,02,03,04,05,06,07,08,09,10,11,12", $this);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        if($valida_token["rol_id"] != 8 || $valida_token["user_type"] != 'Tutor') {
            return faildResponse("No puedes crear un reto por que no eres tutor", $this);
        }

        foreach ($this->input->post("becario_id") as $key => $value) {
            
            $this->challenges->crearReto($valida_token["business_id"], 1, $valida_token["user_id"], $value, $this->input->post("reto"), $this->input->post("detalles"), $this->input->post("mes"));
        }
 
        if($this->db->affected_rows() > 0){
            $this->general_mdl->writeLog("Reto creado exitosamente", "<info>");
            return successResponse([], 'Reto creado exitosamente', $this);

        }else{
            $this->general_mdl->writeLog("Error al crear el reto", "<warning>");
            return faildResponse("Error al crear el reto", $this);
        }
    }


    // actualizar el feedback del reto
    public function actualizarFeedBack(){
        if($this->input->post() == []) {
            return faildResponse("No se ha ingresado el token del usuario", $this);
        }

        if(empty($this->input->post("reto_id"))) {
            return faildResponse("No se ha ingresado el id del reto", $this);
        }

        if(empty($this->input->post("feedback"))) {
            return faildResponse("No se ha ingresado el feedback", $this);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $reto = $this->challenges->updateFeedBackReto($this->input->post("reto_id"), $this->input->post("feedback"), $valida_token["user_id"]);

        if($reto["success"]){
            $this->general_mdl->writeLog("FeedBack del reto actualizado exitosamente", "<info>");
            return successResponse([], 'FeedBack del reto actualizado exitosamente', $this);

        }else{
            $this->general_mdl->writeLog($reto["message"], "<info>");
            return faildResponse($reto["message"], $this);
        }
    }


    // obtener un reto por el id
    public function getReto(){
        if($this->input->post() == []) {
            return faildResponse("No se ha ingresado el token del usuario", $this);
        }

        if(empty($this->input->post("reto_id"))) {
            return faildResponse("No se ha ingresado el id del reto", $this);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $reto = $this->challenges->getReto($this->input->post("reto_id"));

        if(!empty($reto)){
            $this->general_mdl->writeLog("Detalle del reto", "<info>");
            return successResponse($reto, 'Detalle del reto', $this);

        }else if(empty($reto)){
            $this->general_mdl->writeLog("Detalle del reto", "<info>");
            return successResponse($reto, 'Detalle del reto', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener el detalle del reto", "<warning>");
            return faildResponse("Error al obtener el detalle del reto", $this);
        }
    }


    // todos los retos del becario
    public function getRetosBecario(){
        if($this->input->post() == []) {
            return faildResponse("No se ha ingresado el token del usuario", $this);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $retos = $this->challenges->getRetosBecario($valida_token["user_id"], $valida_token["business_id"]);

        if(count($retos) > 0){
            $this->general_mdl->writeLog("Listado de todos los retos", "<info>");
            return successResponse($retos, 'Listado de todos los retos', $this);

        }else if(count($retos) == 0){
            $this->general_mdl->writeLog("Listado de todos los retos", "<info>");
            return successResponse($retos, 'Listado de todos los retos', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de retos", "<warning>");
            return faildResponse("Error al obtener listado de retos", $this);
        }
    }


    // todos los retos pendientes de terminar
    public function getRetosPendientesBecario(){
        if($this->input->post() == []) {
            return faildResponse("No se ha ingresado el token del usuario", $this);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $retosPendientes = $this->challenges->getRetosPendientesBecario($valida_token["user_id"], $valida_token["business_id"]);

        if(count($retosPendientes) > 0){
            $this->general_mdl->writeLog("Listado de todos los retos pendientes", "<info>");
            return successResponse($retosPendientes, 'Listado de todos los retos pendientes', $this);

        }else if(count($retosPendientes) == 0){
            $this->general_mdl->writeLog("Listado de todos los retos pendientes", "<info>");
            return successResponse($retosPendientes, 'Listado de todos los retos pendientes', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de retos pendientes", "<warning>");
            return faildResponse("Error al obtener listado de retos pendientes", $this);
        }
    }


    // actualizar el estatus del reto por el becario
    public function actualizarEstatus(){
        if($this->input->post() == []) {
            return faildResponse("No se ha ingresado el token del usuario", $this);
        }

        if(empty($this->input->post("reto_id"))) {
            return faildResponse("No se ha ingresado el id del reto", $this);
        }

        if(empty($this->input->post("comentarios"))) {
            return faildResponse("No se han agregado comentarios", $this);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $reto = $this->challenges->updateEstatusReto($this->input->post("reto_id"), $valida_token["user_id"], 2, $this->input->post("comentarios"));

        if($reto["success"]){
            $this->general_mdl->writeLog("Estatus del reto actualizado exitosamente", "<info>");
            return successResponse([], 'Estatus del reto actualizado exitosamente', $this);

        }else{
            $this->general_mdl->writeLog($reto["message"], "<info>");
            return successResponse([], $reto["message"], $this);
        }
    }


    // Este endpoint nos permite obtener todos los retos pendientes por el becario, dado el id del becario.
    public function getRetosPendientesBecarioByBecarioId(){
        if(empty($this->input->post())) {
            return validationResponse("No se ha ingresado los datos necesarios", $this);
        }

        if(empty($this->input->post("becario_id"))) {
            return validationResponse("No se ha ingresado el id del becario", $this);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        if($valida_token["rol_id"] != 8) {
            return faildResponse("El usuario logueado no tiene acceso a este endpoint", $this);
        }

        $retosPendientesByBecario = $this->challenges->getRetosPendientesBecarioByBecarioId($this->input->post("becario_id"), $valida_token["user_id"]);

        if(count($retosPendientesByBecario) > 0){
            $this->general_mdl->writeLog("Listado de los retos pendientes por el becario dado el id de becario", "<info>");
            return successResponse($retosPendientesByBecario, 'Listado de los retos pendientes por el becario dado el id de becario', $this);

        }else if(count($retosPendientesByBecario) == 0){
            $this->general_mdl->writeLog("Listado de los retos pendientes por el becario dado el id de becario", "<info>");
            return successResponse($retosPendientesByBecario, 'Listado de los retos pendientes por el becario dado el id de becario', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de retos pendientes por el becario dado el id de becario", "<warning>");
            return faildResponse("Error al obtener listado de retos pendientes por el becario dado el id de becario", $this);
        }
    }


    // Este endpoint nos permite obtener todos los retos pendientes por el tutor, dado el id del becario.
    public function getRetosPendientesTutorByBecarioId(){
        if(empty($this->input->post())) {
            return validationResponse("No se ha ingresado los datos necesarios", $this);
        }

        if(empty($this->input->post("becario_id"))) {
            return validationResponse("No se ha ingresado el id del becario", $this);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        if($valida_token["rol_id"] != 8) {
            return faildResponse("El usuario logueado no tiene acceso a este endpoint", $this);
        }

        $retosPendientesByBecario = $this->challenges->getRetosPendientesTutorByBecarioId($this->input->post("becario_id"), $valida_token["user_id"]);

        if(count($retosPendientesByBecario) > 0){
            $this->general_mdl->writeLog("Listado de los retos pendientes por el tutor dado el id de becario", "<info>");
            return successResponse($retosPendientesByBecario, 'Listado de los retos pendientes por el tutor dado el id de becario', $this);

        }else if(count($retosPendientesByBecario) == 0){
            $this->general_mdl->writeLog("Listado de los retos pendientes por el tutor dado el id de becario", "<info>");
            return successResponse($retosPendientesByBecario, 'Listado de los retos pendientes por el tutor dado el id de becario', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de retos pendientes por el tutor dado el id de becario", "<warning>");
            return faildResponse("Error al obtener listado de retos pendientes por el tutor dado el id de becario", $this);
        }
    }


    // ===============================================================================================
    // =============  APARTADO DE INCIDENCIAS ========================================================
    // ===============================================================================================
    
    // obtener listado de estatus de incidencias
    public function getEstatusIncidencias(){
        if(empty($this->input->post("token"))) {
            return faildResponse("No se ha ingresado el token del usuario", $this);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $responseModel = $this->incidencias->getEstatusIncidencias($valida_token["business_id"]);
        
        if(!empty($responseModel)){
            $this->general_mdl->writeLog("Listado de estatus", "<info>");
            return successResponse($responseModel, 'Listado de estatus', $this);

        }else if(empty($responseModel)){
            $this->general_mdl->writeLog("Listado de estatus", "<info>");
            return successResponse($responseModel, 'Listado de estatus', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener el listado de estatus", "<warning>");
            return faildResponse("Error al obtener el listado de estatus", $this);
        }
    }


    // obtener total de incidencias por estatus
    public function getTotalEstatus(){
        if(empty($this->input->post("token"))) {
            return validationResponse("No se ha ingresado el token del usuario", $this);
        }

        if(empty($this->input->post("becario_id"))) {
            return validationResponse("No se ha ingresado el id del becario", $this);
        }

        if(empty($this->input->post("estatus_id"))) {
            return validationResponse("No se ha ingresado el estatus_id", $this);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        if($valida_token["rol_id"] == 2){
            $responseModal = $this->incidencias->getTotalEstatus($this->input->post("estatus_id"), $this->input->post("becario_id"), null, $valida_token["business_id"], true);
        }else{
            $responseModal = $this->incidencias->getTotalEstatus($this->input->post("estatus_id"), $this->input->post("becario_id"), $valida_token["user_id"], $valida_token["business_id"], false);
        }

        if(!empty($responseModal)){
            $this->general_mdl->writeLog("Total de resultados con el estatus solicitado", "<info>");
            return successResponse($responseModal, 'Total de resultados con el estatus solicitado', $this);

        }else if(empty($responseModal)){
            $this->general_mdl->writeLog("Total de resultados con el estatus solicitado", "<info>");
            return successResponse($responseModal, 'Total de resultados con el estatus solicitado', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener el total de resultados", "<warning>");
            return faildResponse("Error al obtener el total de resultados", $this);
        }
    }


    // crear incidencia
    public function crearIncidencia(){
        if($this->input->post() == []) {
            return validationResponse("No se ha ingresado dato alguno", $this);
        }

        if(empty($this->input->post("token"))) {
            return validationResponse("No se ha ingresado el token del usuario", $this);
        }

        if(empty($this->input->post("becario_id"))) {
            return validationResponse("No se ha ingresado el id del becario", $this);
        }

        if(empty($this->input->post("estatus_id"))) {
            return validationResponse("No se ha ingresado el id del estatus", $this);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }


        foreach ($this->input->post("becario_id") as $key => $becario_id) {
            $tieneIncidenciasDay = $this->incidencias->getIncidenciasCurrentDay($becario_id, $valida_token["business_id"], $valida_token["user_id"]);
            if($tieneIncidenciasDay->total > 0){
                return faildResponse("El becario ya tiene una incidencia registrada en el día de hoy", $this);
            }
            $this->incidencias->crearIncidencia($valida_token["business_id"], $this->input->post("estatus_id"), $valida_token["user_id"], $becario_id, $this->input->post("comentarios"), $this->input->post("tiempo_retardo"));
        }
    
        if($this->db->affected_rows() > 0){
            $this->general_mdl->writeLog("Incidencia creada exitosamente", "<info>");
            return successResponse([], 'Incidencia creada exitosamente', $this);

        }else{
            $this->general_mdl->writeLog("Error al crear la incidencia", "<warning>");
            return faildResponse("Error al crear la incidencia", $this);
        }
    }


    // obtener todas las incidencias del becario
    public function getIncidencias(){
        if(empty($this->input->post("token"))) {
            return validationResponse("No se ha ingresado el token del usuario", $this);
        }

        if(empty($this->input->post("becario_id"))) {
            return validationResponse("No se ha ingresado el id del becario", $this);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $responseModal = $this->incidencias->getIncidencias($valida_token["business_id"], $this->input->post("becario_id"), $valida_token["user_id"]);

        $totalFaltas = $this->incidencias->getTotalEstatusCurrentMonth(3, $this->input->post("becario_id"), $valida_token["business_id"], $valida_token["user_id"], true);
        $totalAsistencias = $this->incidencias->getTotalEstatusCurrentMonth(1, $this->input->post("becario_id"), $valida_token["business_id"], $valida_token["user_id"], false);
        $totalRetardos = $this->incidencias->getTotalEstatusCurrentMonth(2, $this->input->post("becario_id"), $valida_token["business_id"], $valida_token["user_id"], false);

        $data = [
            "totalFaltas" => $totalFaltas->total,
            "totalAsistencias" => $totalAsistencias->total,
            "totalRetardos" => $totalRetardos->total
        ];

        if(!empty($responseModal)){
            $this->general_mdl->writeLog("Listado de incidencias.", "<info>");
            return successResponse($responseModal, 'Listado de incidencias.', $this, $data);

        }else if(empty($responseModal)){
            $this->general_mdl->writeLog("Listado de incidencias.", "<info>");
            return successResponse($responseModal, 'Listado de incidencias.', $this, $data);

        }else{
            $this->general_mdl->writeLog("Error al obtener el listado de incidencias", "<warning>");
            return faildResponse("Error al obtener el listado de incidencias", $this);
        }
    }
}