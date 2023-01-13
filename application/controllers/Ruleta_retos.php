<?php
class Ruleta_retos extends CI_Controller
{

    public $defaultLang = 'es';

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("America/Mexico_City");

        $headers = $this->input->request_headers();
        if (isset($headers['lenguage'])) {
            $this->lang->load('message', 'es');
            $this->defaultLang = 'es';
        } else {
            $this->lang->load('message', 'en');
            $this->defaultLang = 'en';
        }
        $this->load->model('ruleta_retos_mdl', 'ruleta_retos');
        $this->load->model('games_mdl', 'games');
        $this->load->model('notification_mdl', 'notification');
        $this->load->model('user_model', 'user');
    }

    // public function crear_reto()
    // {
    //     $token = $this->input->post("token");
    //     $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
    //     if (!$valida_token) {
    //         faildResponse($this->lang->line('token_error_msg'), $this);
    //         return;
    //     }

    //     $id_retador = $valida_token["user_id"];
    //     $id_retado = $this->input->post("id_retado");
    //     $result = $this->ruleta_retos->crear_reto($id_retador, $id_retado);
    //     if ($result) {
    //         $this->general_mdl->writeLog("Registro de reto ruleta usuario " . $valida_token["user_id"], "<info>");
    //         successResponse($result, 'Se creo correctamente el reto con el usuario', $this);
    //     } else {
    //         $this->general_mdl->writeLog("Error al guardar el reto ruleta usuario " . $valida_token["user_id"], "<warning>");
    //         faildResponse("No se pudo crear el reto o hay uno activo", $this);
    //     }
    // }

    // public function obtener_solicitudes()
    // {
    //     $token = $this->input->post("token");
    //     $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
    //     if (!$valida_token) {
    //         faildResponse($this->lang->line('token_error_msg'), $this);
    //         return;
    //     }

    //     $user_id = $valida_token["user_id"];
    //     $result = $this->ruleta_retos->obtener_solicitudes($user_id);
    //     if ($result) {
    //         $this->general_mdl->writeLog("Listado de solicitudes retos ruleta usuario " . $valida_token["user_id"], "<info>");
    //         successResponse($result, 'lista de solicitudes para juego ruleta', $this);
    //     } else {
    //         $this->general_mdl->writeLog("Error al obtener solicitudes retos ruleta usuario " . $valida_token["user_id"], "<info>");
    //         faildResponse("Sin solicitudes de retos", $this);
    //     }
    // }

    // public function aceptar_reto()
    // {
    //     $id_reto = $this->input->post("id_reto");
    //     $aceptado = $this->input->post("aceptado");
    //     $result = $this->ruleta_retos->aceptar_reto($id_reto, $aceptado);
    //     if ($result) {
    //         $this->general_mdl->writeLog("Aceptacion de reto ruleta " . $id_reto, "<info>");
    //         successResponse($result, 'Se guardo (aceptar o rechazar reto)', $this);
    //     } else {
    //         $this->general_mdl->writeLog("Error al aceptar reto ruleta ". $id_reto, "<warning>");
    //         faildResponse("Error al actualizar la tabla", $this);
    //     }
    // }

    public function guardar_respuesta()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('question_id', 'answer_id'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $data = $this->input->post();
        $data['user_id'] = $valida_token['user_id'];
        $result = $this->games->SaveAnswerRoulette($data);
        if ($result) {
            if ($result == 'correct') {
                $correct = 1;
                $texto = "Has contestado correctamente";
            } else {
                $correct = 0;
                $texto = "Has contestado incorrectamente. La respuesta correcta es " . $result;
            }
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
            $this->general_mdl->writeLog("Registro de respuesta ruleta usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<info>");
            successResponse(array('correct' => $correct), $texto, $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar respuesta ruleta usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<warning>");
            faildResponse('La respuesta no ha sido registrada correctamente', $this);
        }
    }



    public function crear_reto()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $id_usuario = $valida_token["user_id"];
        $nombre = $this->input->post("nombre");
        $retados = $this->input->post("retados");
        $data = [];
        $data["id_user"] = $id_usuario;
        $data["nombre"] = $nombre;
        $id_reto = $this->ruleta_retos->crear_reto($data);
        $result = $this->ruleta_retos->agregarOponentes($retados, $id_reto);
        $quiz = $this->games->RouletteQuiz($valida_token['business_id']);
        $quiz_elegidos = [];
        $posiciones = $this->generar_random();
        array_push($quiz_elegidos, $quiz[$posiciones[0]]);
        array_push($quiz_elegidos, $quiz[$posiciones[1]]);
        array_push($quiz_elegidos, $quiz[$posiciones[2]]);
        $this->ruleta_retos->registrar_quiz($id_reto, $quiz_elegidos);
        if ($result) {
            $tokens = $this->notification->obtenerTokensRuletaRetos($retados);
            if ($tokens != "") {
                $tokens_ = array();
                $nombre_usuario = $valida_token["name_complete"];
                foreach ($tokens as $index => $value) {
                    array_push($tokens_, $value['token']);
                    /***********************************************************************
                     *	Autor: Mario Adrián Martínez Fernández   Fecha: 09/07/2020
                     *		   mario.martinez.f@hotmail.es
                     *	Nota: Guardarmos a los usuarios que se les enviara la notificacion.
                     ***********************************************************************/
                    $data = array('title' => 'Ruleta', 'notification' => 'Fuiste retado en un juego de Ruleta por ' . $nombre_usuario, 'user_id' => $value['user_id'], 'service_id' => SERVICE_RULETA, 'user_create_id' => $valida_token['user_id']);
                    $this->notification->RegisterNotification($data);
                }

                $this->general_mdl->EnviarNotificacionPush($tokens_, 'Fuiste retado en un juego de Ruleta por ' . $nombre_usuario, 'Ruleta', SERVICE_RULETA);
                // echo json_encode("se debio enviar la notificacion por char");
            }
            $this->crearCron($retados,$valida_token["name_complete"],$valida_token['user_id']);
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
            $this->general_mdl->writeLog("Registro de reto ruleta usuario " . $valida_token["user_id"], "<info>");
            successResponse(["id_reto" => $id_reto], 'Se creo correctamente el reto con el usuario', $this, ["quiz" => $quiz_elegidos]);
        } else {
            $this->general_mdl->writeLog("Error al guardar el reto ruleta usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("No se pudo crear el reto o hay uno activo", $this);
        }
    }

    public function crearCron($retados, $nombre_usuario, $id_own)
    {
        // $retados = [$retados,$retados,$retados];
        $retados = join("-", $retados);
        $mifecha = date("Y-m-d H:i:s");
        
        $NuevaFecha = strtotime('+21 hour', strtotime($mifecha));
        $NuevaFecha = strtotime('+30 minute', $NuevaFecha);
        // $NuevaFecha = strtotime ( '-2 hour' , strtotime($mifecha) ) ;
        $NuevaFecha = strtotime('+1 day', $NuevaFecha);
        $minutos = intval(date('i', $NuevaFecha));
        $hora = date("G", $NuevaFecha);
        $dia = date("j");
        $mes = date("n");
        // for($i = 0; $i < count($retados); $i++){
        $command = $minutos . ' ' . $hora . ' ' . $dia . ' ' . $mes . ' * wget https://kreativeco.com/nuup/Notification/enviarNotificacionRuletaTiempo?id_usuario=' . $retados . '-' . $nombre_usuario . '-' . $id_own;
        // echo "   " . $command;
        //comando para agregar un trabajo al contab de linux
        exec('echo -e "`crontab -l`\n' . $command . '" | crontab -', $output);
        //si en alguna ocacion no funciona 
        //verificar que en php fpm este habilitada la funcion exec
        //al parecer se desabilita si se reinicia php fpm
        //se puede verificar haciendo phpnfo()
        //en disable_functions si aparece ahi, esta desabilitada
        //para habilitar es necesario entrar a whm como root (usuario de luis)
        //en multiphp manager
        //seleccionar la version de php que este habilitada para este server
        //en php fpm click en ManageSettings (puede aparecer como editar)
        //en disable_function remover exec
        //click en verificar
        //click en actualizar
        //ahora todo deberia funcionar
        // }
        successResponse($retados, 'Listado de retos pendientes', $this);
    }

    public function generar_random()
    {
        $numeros = [0, 1, 2, 3, 4, 5, 6, 7];
        $seleccionados = [];
        $random = random_int(0, 7);
        array_push($seleccionados, $numeros[$random]);
        array_splice($numeros, $random, 1);
        $random = random_int(0, 6);
        array_push($seleccionados, $numeros[$random]);
        array_splice($numeros, $random, 1);
        $random = random_int(0, 5);
        array_push($seleccionados, $numeros[$random]);
        return $seleccionados;
    }

    public function obtener_retos_pendientes()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_usuario = $valida_token["user_id"];

        $retos = $this->ruleta_retos->obtener_retos_pendientes($id_usuario);
        if ($retos) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
            $this->general_mdl->writeLog("Listado de retos pendientes ruleta retos usuario " . $valida_token["user_id"], "<info>");
            successResponse($retos, 'Listado de retos pendientes', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener retos pendientes ruleta retos usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('No tienes retos pendientes', $this);
        }
    }

    public function obtener_reto()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_reto = $this->input->post("id_reto");

        $retos = $this->ruleta_retos->obtener_reto($id_reto, $valida_token["user_id"]);
        if ($retos) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
            $this->general_mdl->writeLog("Detalle de reto ruleta retos usuario " . $valida_token["user_id"], "<info>");
            successResponse($retos, 'Detalle de reto', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener detalle de reto ruleta retos usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al obtener reto", $this);
        }
    }

    public function guardar_respuesta_correcta()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_reto = $this->input->post("id_reto");
        $id_usuario = $valida_token["user_id"];
        $correctas = $this->input->post("correctas");
        $quiz_id = $this->input->post("quiz_id");
        $result = $this->ruleta_retos->guardar_respuesta_correcta($id_reto, $id_usuario, $correctas, $quiz_id);
        if ($result) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
            $this->enviarNotificacion($result, $valida_token["user_id"], $valida_token["name_complete"]);
            $this->general_mdl->writeLog("Registro de respuesta ruleta retos usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Respuesta guardada', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar respuesta ruleta retos usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al guardar respuesta", $this);
        }
    }

    public function enviarNotificacion($id_usuario, $id_own, $nombre_usuario)
    {
        $tokens = $this->user->ObtenerToken($id_usuario);
        // echo json_encode($tokens);
        if ($tokens != "") {
            $data = array('title' => 'Ruleta retos', 'notification' => 'El usuario ' . $nombre_usuario . ' respondio tu reto.', 'user_id' => $id_usuario, 'service_id' => SERVICE_RULETA, 'user_create_id' => $id_own);
            $this->notification->RegisterNotification($data);

            $this->general_mdl->EnviarNotificacionPush([$tokens], 'El usuario ' . $nombre_usuario . ' respondio tu reto.', 'Ruleta retos', SERVICE_RULETA, false);
            // echo json_encode("se debio enviar la notificacion por char");
        }
    }

    public function obtener_resultados()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_reto = $this->input->post("id_reto");
        $result = $this->ruleta_retos->obtener_resultados($id_reto);
        if ($result) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
            $this->general_mdl->writeLog("Resultados de reto ruleta retos usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Respuesta guardada', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener resultados ruleta retos usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al obtener resultados", $this);
        }
    }

    public function obtener_retos()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $tipo = $this->input->post("tipo");
        $result = $this->ruleta_retos->obtener_retos($tipo, $valida_token["user_id"]);
        if ($result) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
            $this->general_mdl->writeLog("Listado de retos ruleta retos usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Listado de retos', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener retos ruleta retos usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al obtener retos", $this);
        }
    }

    public function SaveAnswerRoulette()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('question_id', 'answer_id'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $data = $this->input->post();
        $data['user_id'] = $valida_token['user_id'];
        $result = $this->games->SaveAnswerRoulette($data, 1);
        if ($result) {
            if ($result == 'correct') {
                $correct = 1;
                $texto = "Has contestado correctamente";
            } else {
                $correct = 0;
                $texto = "Has contestado incorrectamente. La respuesta correcta es " . $result;
            }
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
            $this->general_mdl->writeLog("Registro de respuesta ruleta usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<info>");
            successResponse(array('correct' => $correct), $texto, $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar respuesta ruleta usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<warning>");
            faildResponse('La respuesta no ha sido registrada correctamente', $this);
        }
    }

    public function agregar_puntos()
    {
        $result = $this->ruleta_retos->agregar_puntos();
        successResponse($result, "Se agregaron los puntos de los retos pendientes", $this);
    }

    public function ObtenerInstrucciones()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
        $instrucciones = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum';
        successResponse($instrucciones, "Instrucciones del juego ruleta retos", $this);
    }

    function notificacionMensual()
    {
        //usuario admin para empresa yastas
        $user_id = 43;
        $mensaje = "Puedes jugar Ruleta para ganar mas puntos";
        //se obtienen los tokens de usuarios de yastas, cambiar el id por el de la empresa correspondiente
        $tokens = $this->notification->ListUserNotification(18);
        if ($tokens) {
            $tokens_ = array();
            foreach ($tokens as $index => $value) {
                array_push($tokens_, $value['token']);
                $data = array('title' => 'Ruleta', 'notification' => $mensaje, 'user_id' => $value['user_id'], 'service_id' => SERVICE_RULETA, 'user_create_id' => $user_id);
                $this->notification->RegisterNotification($data);
            }
            $this->general_mdl->EnviarNotificacionPush($tokens_, $mensaje, 'Ruleta', SERVICE_RULETA);
        }
    }
}
