<?php
class Chat extends CI_Controller
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
        $this->load->model('user_model', 'user');
        $this->load->model('chat_mdl', 'chat');
        $this->load->model('notification_mdl', 'notification');
    }

    public function obtenerMensajes()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $usuario_de = $valida_token["user_id"];
        $usuario_para = $this->input->post("usuario_para");
        $utc_ref = $this->input->post("utc_ref");
        $resultado = $this->chat->obtenerMensajes($usuario_de, $usuario_para, $utc_ref);
        if ($resultado) {
            $this->general_mdl->writeLog("Listado de mensajes de chat usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Mensajes del chat', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener mensajes de chat usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    public function mandarMensaje()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $data = array(
            "usuario_de" => $valida_token["user_id"],
            "usuario_para" => $this->input->post("usuario_para"),
            "mensaje" => $this->input->post("mensaje"),
            "leido" => 0
        );

        $resultado = $this->chat->mandarMensaje($data);
        if ($resultado) {
            $this->enviarNotificacion($this->input->post("usuario_para"), $valida_token["user_id"], $valida_token["name_complete"]);
            $this->general_mdl->writeLog("Envio de mensaje de chat usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Mensajes del chat', $this);
        } else {
            $this->general_mdl->writeLog("Error al enviar mensaje de chat usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    public function enviarNotificacion($id_usuario, $id_own, $nombre_usuario)
    {
        $tokens = $this->user->ObtenerToken($id_usuario);
        if ($tokens != "" || !$tokens) {
            $data = array('title' => 'Chat', 'notification' => 'Tienes un mensaje nuevo en chat de ' . $nombre_usuario, 'user_id' => $id_usuario, 'service_id' => SERVICE_CHAT, 'user_create_id' => $id_own);
            $this->notification->RegisterNotification($data);

            $this->general_mdl->EnviarNotificacionPush([$tokens], 'Tienes un mensaje nuevo en chat de ' . $nombre_usuario, 'Chat', SERVICE_CHAT, false);
            // echo json_encode("se debio enviar la notificacion por char");
        }
    }

    public function obtenerUltimoMensaje()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $usuario_de = $this->input->post("usuario_de");
        $usuario_para = $this->input->post("usuario_para");

        $resultado = $this->chat->obtenerUltimoMensaje($usuario_de, $usuario_para);
        if ($resultado) {
            $this->general_mdl->writeLog("Consulta ultimo mensaje de chat usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Ultimo mensaje del chat', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener ultimo mensaje de chat usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    public function eliminarMensaje()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $data = array(
            "id" => $this->input->post('id_chat')
        );

        $resultado = $this->chat->eliminarMensaje($data);
        if ($resultado) {
            $this->general_mdl->writeLog("Eliminacion de mensaje de chat usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Mensaje eliminado', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar mensaje de chat usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    public function listarChats()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $resultado = $this->chat->listarChats($valida_token["user_id"]);
        if ($resultado) {
            $this->general_mdl->writeLog("Listado de chats usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Chats actuales del usuario', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtner chats usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    public function tieneMensajesNuevos()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $resultado = $this->chat->tieneMensajesNuevos($valida_token["user_id"]);
        if ($resultado) {
            $this->general_mdl->writeLog("Validacion de mensajes nuevos usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Tiene mensajes nuevos', $this);
        } else {
            $this->general_mdl->writeLog("Error al validar mensajes nuevos usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    public function marcarLeidos()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $resultado = $this->chat->marcarLeidos($valida_token["user_id"]);
        if ($resultado) {
            $this->general_mdl->writeLog("Registro de mensaje leido usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Mensajes marcados correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al registrar mensaje leido usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin datos", $this);
        }
    }
}
