<?php
class Game_snake extends CI_Controller
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
        $this->load->model('game_snake_mdl', 'snake');
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
        $data["listado_correctas"] = $this->input->post("listado_correctas");
        $data["listado_incorrectas"] = $this->input->post("listado_incorrectas");
        $result = $this->snake->guardarPuntuacion($data);
        if ($result) {
            $this->general_mdl->agregar_recurso_visto($data["user_id"]);
            if ($data["score"] - $data["incorrectas"] > 0)
                $this->general_mdl->ModificarScoreUsuario($data["user_id"], 1);
            else {
                $this->general_mdl->ModificarScoreUsuario($data["user_id"], -1);
            }
            $this->general_mdl->writeLog("Registro de puntuacion snake usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Se guardo correctamente la puntuación', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar puntuacion snake usuario " . $valida_token["user_id"], "<warning>");
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
        $result = $this->snake->obtenerMejorPuntuacion($business_id, $user_id);

        if ($result) {
            $this->general_mdl->writeLog("Listado de mejores puntuaciones snake usuario " . $valida_token["user_id"], "<info>");
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
        $result = $this->snake->obtenerMejorPuntuacionEquipo($business_id, $id_job);

        if ($result) {
            $this->general_mdl->writeLog("Listado de mejores puntuaciones snake por equipo usuario " . $valida_token["user_id"], "<info>");
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
        $result = $this->snake->obtenerPalabras($valida_token["business_id"]);
        $result["imagenes"] = $this->snake->obtenerImagenes();
        if ($result) {
            $this->general_mdl->writeLog("Listado de imagenes y palabras snake usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Imagenes y palabras de snake', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener imagenes y palabras snake usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al obtener registros", $this);
        }
    }

    public function obtenerTemas()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }
        $result = $this->snake->obtenerTemas($valida_token["business_id"]);
        if($result){
            $this->general_mdl->writeLog("Listado de temas snake usuario " . $valida_token["user_id"], "<info>");
            return successResponse($result, 'Temas snake', $this);
            
        }else{
            $this->general_mdl->writeLog("Error al obtener temas snake usuario " . $valida_token["user_id"], "<warning>");
            return faildResponse("Error al obtener registros", $this);
        }
    }

    public function obtenerPalabrasResultados()
    {
        $token = $this->input->post("token");
        $data = $this->input->post();
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $result = $this->snake->obtenerPalabrasResultados($valida_token["business_id"],$data);
        if ($result) {
            $this->general_mdl->writeLog("Listado de temas snake usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Temas snake', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener temas snake usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al obtener registros", $this);
        }
    }

    public function guardar()
    {
        if($this->input->post() == []){
            $_POST = json_decode(file_get_contents('php://input'), true);
        }
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }
        $tema = $this->input->post("nombre");
        $palabras = $this->input->post("palabras");
        $result = $this->snake->guardar($tema, $palabras, $valida_token["business_id"]);
        
        if($result){
            $this->general_mdl->writeLog("Registro tema snake usuario " . $valida_token["user_id"], "<info>");
            return successResponse($result, 'Guardado tema snake', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar temas snake usuario " . $valida_token["user_id"], "<warning>");
            return faildResponse("Error al guardar registros", $this);
        }
    }

    function eliminar()
    {
        if($this->input->post() == []){
            $_POST = json_decode(file_get_contents('php://input'), true);
        }
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }
        $id_tema = $this->input->post("id_tema");
        $result = $this->snake->eliminar($id_tema);

        if($result){
            $this->general_mdl->writeLog("eliminacion de tema snake usuario " . $valida_token["user_id"], "<info>");
            return successResponse($result, 'Tema eliminado snake', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar temas snake usuario " . $valida_token["user_id"], "<warning>");
            return faildResponse("Error al eliminar registros", $this);
        }
    }

    function editar()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }
        $datos = $this->input->post("datos");
        $result = $this->snake->editar($datos);

        if ($result) {
            $this->general_mdl->writeLog("Actualización de tema snake usuario " . $valida_token["user_id"], "<info>");
            return successResponse($result, 'Tema actualizado snake', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar temas snake usuario " . $valida_token["user_id"], "<warning>");
            return faildResponse("Error al actualizar registros", $this);
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
                $data = array('title' => 'Corre pancho corre', 'notification' => $mensaje, 'user_id' => $value['user_id'], 'service_id' => SERVICE_GAME_SNAKE, 'user_create_id' => $user_id);
                $this->notification->RegisterNotification($data);
            }
            $this->general_mdl->EnviarNotificacionPush($tokens_, $mensaje, 'Corre pancho corre', SERVICE_GAME_SNAKE);
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
        $id_reto = $this->snake->crear_reto($data);
        $retados_ = $retados;
        array_push($retados_, $valida_token["user_id"]);
        $result = $this->snake->agregarOponentes($retados_, $id_reto);

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
                    $data = array('title' => 'Corre pancho corre', 'notification' => 'Fuiste retado en un juego de Corre pancho corre por ' . $nombre_usuario, 'user_id' => $value['user_id'], 'service_id' => SERVICE_GAME_SNAKE, 'user_create_id' => $valida_token['user_id']);
                    $this->notification->RegisterNotification($data);
                }

                $this->general_mdl->EnviarNotificacionPush($tokens_, 'Fuiste retado en un juego de Corre pancho corre por ' . $nombre_usuario, 'Corre pancho corre', SERVICE_GAME_SNAKE);
                // echo json_encode("se debio enviar la notificacion por char");
            }
            // $this->crearCron($retados, $valida_token["name_complete"], $valida_token['user_id']);
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
            $this->general_mdl->writeLog("Registro de reto snake usuario " . $valida_token["user_id"], "<info>");
            successResponse(["id_reto" => $id_reto, "id_tema" => $this->input->post("id_tema")], 'Se creo correctamente el reto de snake con el usuario', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar el reto snake usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("No se pudo crear el reto snake o hay uno activo", $this);
        }
    }

    // public function crearCron($retados, $nombre_usuario, $id_own)
    // {
    //     // $retados = [$retados,$retados,$retados];
    //     $retados = join("-", $retados);
    //     $mifecha = date("Y-m-d H:i:s");
    //     // echo date('Y-m-d H:i:s', strtotime($mifecha));
    //     $NuevaFecha = strtotime('+21 hour', strtotime($mifecha));
    //     $NuevaFecha = strtotime('+30 minute', $NuevaFecha);
    //     // $NuevaFecha = strtotime ( '-2 hour' , strtotime($mifecha) ) ;
    //     $NuevaFecha = strtotime('+1 day', $NuevaFecha);
    //     $minutos = intval(date('i', $NuevaFecha));
    //     $hora = date("G", $NuevaFecha);
    //     $dia = date("j");
    //     $mes = date("n");
    //     // for($i = 0; $i < count($retados); $i++){
    //     $command = $minutos . ' ' . $hora . ' ' . $dia . ' ' . $mes . ' * wget https://kreativeco.com/nuup/Notification/enviarNotificacionRunPanchoRun?id_usuario=' . $retados . '-' . $nombre_usuario . '-' . $id_own;

    //     exec('echo -e "`crontab -l`\n' . $command . '" | crontab -', $output);

    //     successResponse($retados, 'Notificacion de reto run pancho run', $this);
    // }

    function obtener_retos()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $pendientes = $this->input->post("pendientes");
        $retos = $this->snake->obtener_retos($pendientes, $valida_token["user_id"]);
        if ($retos) {
            $this->general_mdl->writeLog("Listado de retos snake usuario " . $valida_token["user_id"], "<info>");
            successResponse($retos, 'Listado de retos', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener retos snake usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al obtener retos", $this);
        }
    }
}
