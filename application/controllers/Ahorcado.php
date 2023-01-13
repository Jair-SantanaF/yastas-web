<?php
class Ahorcado extends CI_Controller
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
        $this->load->model('ahorcado_mdl', 'ahorcado');
    }

    public function obtenerFrase()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $frase = $this->ahorcado->obtenerFrase($valida_token["business_id"]);
        if ($frase) {
            $this->general_mdl->writeLog("Consulta de frase juego ahorcado usuario " . $valida_token["user_id"], "<info>");
            successResponse($frase, 'Frase aleatoria juego ahorcado', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener frase juego ahorcado usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al obtener frase.', $this);
        }
    }

    public function guardarPuntos()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id_frase', 'fallas'), $this->input->post(), $this->defaultLang);
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $id_usuario = $valida_token["user_id"];
        $id_frase = $this->input->post("id_frase");
        $fallas = $this->input->post("fallas");
        $result = $this->ahorcado->guardarPuntos($id_usuario, $id_frase, $fallas);
        if ($result) {
            $this->general_mdl->writeLog("Guardar puntos en juego ahorcado usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Puntos guardados en juego juego ahorcado', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar puntos en juego ahorcado usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al guardar puntos en juego ahorcado frase.', $this);
        }
    }

    public function obtenerInstrucciones()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $result = [];
        $result[0]["instrucciones"] = "1. ¡Comienza un nuevo juego! Observa con atención la frase e intenta adivinar la letra faltante.

2. Selecciona la letra correcta entre los tablones de abajo.
        
3. ¡Cuidado! Si te equivocas nuestro amigo la pasará muy mal.
        
4. Al finalizar el juego sin fallas ganarás 4 puntos, si tienes una falla, ganarás 3 puntos, con dos fallas, ganarás 2 puntos, y con 3 fallas únicamente te llevarás 1 punto.
        
5. Si tienes 4 fallas, perderás 2 puntos.";
        if ($result) {
            $this->general_mdl->writeLog("Guardar puntos en juego ahorcado usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Puntos guardados en juego juego ahorcado', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar puntos en juego ahorcado usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al guardar puntos en juego ahorcado frase.', $this);
        }
    }

    public function obtenerFrases()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $result = $this->ahorcado->obtenerFrases($valida_token["business_id"]);
        if ($result) {
            $this->general_mdl->writeLog("Listado de frases de ahorcado usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Listado de frases de ahorcado', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener listado de ahorcado usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al obtener frases de ahorcado.', $this);
        }
    }

    public function guardarFrase()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->input->post();
        $data["business_id"] = $valida_token["business_id"];
        $result = $this->ahorcado->guardarFrase($data);
        if ($result) {
            $this->general_mdl->writeLog("Insersion de frase ahorcado usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Frase guardada', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar frase de ahorcado usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al guardar frases de ahorcado.', $this);
        }
    }

    public function editarFrase(){
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->input->post();
        $result = $this->ahorcado->editarFrase($data);
        if ($result) {
            $this->general_mdl->writeLog("Actualizacion de frase ahorcado usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Frase guardada', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar frase de ahorcado usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al actualizar frase de ahorcado.', $this);
        }
    }

    public function eliminarFrase(){
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_frase = $this->input->post("id");
        $result = $this->ahorcado->eliminarFrase($id_frase);
        if ($result) {
            $this->general_mdl->writeLog("Eliminacion de frase ahorcado usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Frase eliminada', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar frase de ahorcado usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al eliminar frase de ahorcado.', $this);
        }
    }

    function notificacionMensual(){
        // $user_id = 43;
        // $mensaje = "Puedes jugar Ahorcado para ganar mas puntos";
        // $tokens = $this->notification->ListUserNotification(18);
		// if ($tokens) {
		// 	$tokens_ = array();
		// 	foreach ($tokens as $index => $value) {
		// 		array_push($tokens_, $value['token']);
		// 		$data = array('title' => 'Ahorcado', 'notification' => $mensaje, 'user_id' => $value['user_id'], 'service_id' => SERVICE_AHORCADO, 'user_create_id' => $user_id);
		// 		$this->notification->RegisterNotification($data);
		// 	}
		// 	$this->general_mdl->EnviarNotificacionPush($tokens_, $mensaje, 'Ahorcado', SERVICE_AHORCADO);
		// }
    }
}
