<?php
class Run_pancho_run extends CI_Controller
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
        $this->load->model('run_pancho_run_mdl', 'run');
        $this->load->model('notification_mdl', 'notification');
    }

    public function guardarPuntuacion()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $data = [];
        $data["user_id"] = $valida_token["user_id"];
        $data["score"] = $this->input->post("score");
        $data["id_tema"] = $this->input->post("id_tema");
        $data["incorrectas"] = $this->input->post("incorrectas");
        $data["id_reto"] = $this->input->post("id_reto");
        $data["listado_correctas"] = $this->input->post("listado_correctas");
        $data["listado_incorrectas"] = $this->input->post("listado_incorrectas");
        $result = $this->run->guardarPuntuacion($data);
        if ($result) {
            $this->general_mdl->agregar_recurso_visto($data["user_id"]);
            $this->general_mdl->ModificarScoreUsuario($data["user_id"], 1);
            $this->general_mdl->writeLog("Registro de puntuacion run pancho usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Se guardo correctamente la puntuación', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar puntuacion run pancho usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al guardar la puntuacion", $this);
        }
    }

    public function obtenerMejorPuntuacion()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $business_id = $valida_token["business_id"];
        $user_id = $valida_token["user_id"];
        $result = $this->run->obtenerMejorPuntuacion($business_id, $user_id);

        if ($result) {
            $this->general_mdl->writeLog("Listado de mejores puntuaciones run pancho usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Mejor puntuacion del usuario', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener mejores puntuaciones usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin registros", $this);
        }
    }

    public function obtenerMejorPuntuacionEquipo()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $business_id = $valida_token["business_id"];
        $id_job = $valida_token["job_id"];
        $result = $this->run->obtenerMejorPuntuacionEquipo($business_id, $id_job);

        if ($result) {
            $this->general_mdl->writeLog("Listado de mejores puntuaciones run pancho por equipo usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Mejor puntuacion de usuarios por equipo', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener mejores puntuaciones por equipo usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin registros", $this);
        }
    }

    public function obtenerImagenesPalabras()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $result =  [];
        $result = $this->run->obtenerPalabras($valida_token["business_id"]);
        $result["imagenes"] = $this->run->obtenerImagenes();
        if ($result) {
            $this->general_mdl->writeLog("Listado de imagenes y palabras run pancho usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Imagenes y palabras de run pancho run', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener imagenes y palabras run pancho usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al obtener registros", $this);
        }
    }

    public function obtenerTemas()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $result = $this->run->obtenerTemas($valida_token["business_id"]);
        if ($result) {
            $this->general_mdl->writeLog("Listado de temas run pancho usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Temas run pancho run', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener temas run pancho usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al obtener registros", $this);
        }
    }

    public function guardar()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $tema = $this->input->post("nombre");
        $palabras = $this->input->post("palabras");
        $result = $this->run->guardar($tema, $palabras, $valida_token["business_id"]);
        if ($result) {
            $this->general_mdl->writeLog("Registro tema run pancho usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Guardado tema run pancho run', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar temas run pancho usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al guardar registros", $this);
        }
    }

    function eliminar()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_tema = $this->input->post("id_tema");
        $result = $this->run->eliminar($id_tema);
        if ($result) {
            $this->general_mdl->writeLog("eliminacion de tema run pancho usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Tema eliminado run pancho run', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar temas run pancho usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al eliminar registros", $this);
        }
    }

    function editar()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $datos = $this->input->post();
        $result = $this->run->editar($datos);
        if ($result) {
            $this->general_mdl->writeLog("Actualizacion de tema run pancho usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Tema actualizado run pancho run', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar temas run pancho usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al actualizar registros", $this);
        }
    }

    function notificacionMensual()
    {
        $user_id = 43;
        $mensaje = "Puedes jugar Corre pancho corre para ganar mas puntos";
        $tokens = $this->notification->ListUserNotification(18);
        if ($tokens) {
            $tokens_ = array();
            foreach ($tokens as $index => $value) {
                array_push($tokens_, $value['token']);
                $data = array('title' => 'Corre pancho corre', 'notification' => $mensaje, 'user_id' => $value['user_id'], 'service_id' => SERVICE_RUN_PANCHO, 'user_create_id' => $user_id);
                $this->notification->RegisterNotification($data);
            }
            $this->general_mdl->EnviarNotificacionPush($tokens_, $mensaje, 'Corre pancho corre', SERVICE_RUN_PANCHO);
        }
    }

    function retar()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $retados = $this->input->post("retados");
        $data = [];
        $data["id_user"] = $valida_token["user_id"];
        $data["id_tema"] = $this->input->post("id_tema");
        $id_reto = $this->run->crear_reto($data);
        $retados_ = $retados;
        array_push($retados_, $valida_token["user_id"]);
        $result = $this->run->agregarOponentes($retados_, $id_reto);

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
                    $data = array('title' => 'Corre pancho corre', 'notification' => 'Fuiste retado en un juego de Corre pancho corre por ' . $nombre_usuario, 'user_id' => $value['user_id'], 'service_id' => SERVICE_RUN_PANCHO, 'user_create_id' => $valida_token['user_id']);
                    $this->notification->RegisterNotification($data);
                }

                $this->general_mdl->EnviarNotificacionPush($tokens_, 'Fuiste retado en un juego de Corre pancho corre por ' . $nombre_usuario, 'Corre pancho corre', SERVICE_RUN_PANCHO);
                // echo json_encode("se debio enviar la notificacion por char");
            }
            $this->crearCron($retados, $valida_token["name_complete"], $valida_token['user_id']);
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
            $this->general_mdl->writeLog("Registro de reto run pancho run usuario " . $valida_token["user_id"], "<info>");
            successResponse(["id_reto" => $id_reto, "id_tema" => $this->input->post("id_tema")], 'Se creo correctamente el reto de run pancho con el usuario', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar el reto run pancho usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("No se pudo crear el reto run pancho o hay uno activo", $this);
        }
    }

    public function crearCron($retados, $nombre_usuario, $id_own)
    {
        // $retados = [$retados,$retados,$retados];
        $retados = join("-", $retados);
        $mifecha = date("Y-m-d H:i:s");
        // echo date('Y-m-d H:i:s', strtotime($mifecha));
        $NuevaFecha = strtotime('+21 hour', strtotime($mifecha));
        $NuevaFecha = strtotime('+30 minute', $NuevaFecha);
        // $NuevaFecha = strtotime ( '-2 hour' , strtotime($mifecha) ) ;
        $NuevaFecha = strtotime('+1 day', $NuevaFecha);
        $minutos = intval(date('i', $NuevaFecha));
        $hora = date("G", $NuevaFecha);
        $dia = date("j");
        $mes = date("n");
        // for($i = 0; $i < count($retados); $i++){
        $command = $minutos . ' ' . $hora . ' ' . $dia . ' ' . $mes . ' * wget https://kreativeco.com/nuup/Notification/enviarNotificacionRunPanchoRun?id_usuario=' . $retados . '-' . $nombre_usuario . '-' . $id_own;

        exec('echo -e "`crontab -l`\n' . $command . '" | crontab -', $output);

        successResponse($retados, 'Notificacion de reto run pancho run', $this);
    }

    function obtener_retos()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $pendientes = $this->input->post("pendientes");
        $retos = $this->run->obtener_retos($pendientes, $valida_token["user_id"]);
        if ($retos) {
            $this->general_mdl->writeLog("Listado de retos run pancho run usuario " . $valida_token["user_id"], "<info>");
            successResponse($retos, 'Listado de retos', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener retos run pancho usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al obtener retos", $this);
        }
    }
}
