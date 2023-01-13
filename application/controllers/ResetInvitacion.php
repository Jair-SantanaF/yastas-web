<?php
class ResetInvitacion extends CI_Controller
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
        $this->load->model('reset_invitacion_mdl', 'reset');
    }

    public function obtenerUsuarios()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $result = $this->reset->obtenerUsuarios($valida_token["business_id"]);
        $this->general_mdl->writeLog("Usuarios para resetear password usuario" . $valida_token["user_id"], "<info>");
        successResponse($result, 'Usuarios', $this);
    }

    public function resetear_password()
    {
        if ($this->input->post() == []) {
			$_POST = json_decode(file_get_contents('php://input'), true);
		}
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_user = $this->input->post("id_user");
        $result = $this->reset->resetearPassword($id_user);
        $this->general_mdl->writeLog("Reseteo de password usuario" . $valida_token["user_id"] . " al usuario " . $id_user, "<info>");
        successResponse($result, 'Reseteo de password exitoso', $this);
    }
}
