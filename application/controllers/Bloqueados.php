<?php
class Bloqueados extends CI_Controller
{
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
        $this->load->model('bloqueados_mdl', 'bloqueados');
    }

    public function obtenerBloqueados()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $resultado = $this->bloqueados->obtenerBloqueados($valida_token["business_id"]);
        // if ($resultado != false) {
        $this->general_mdl->writeLog("Listado de usuarios bloqueados usuario " . $valida_token["user_id"], "<info>");
        successResponse($resultado, 'Usuarios bloqueados', $this);
        // } else {
        //     $this->general_mdl->writeLog("Error al obtener usuarios bloqueados usuario " . $valida_token["user_id"], "<warning>");
        //     faildResponse("Sin datos", $this);
        // }
    }

    function desbloquear()
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
        $id = $this->input->post("id");
        $resultado = $this->bloqueados->desbloquear($id);
        if ($resultado) {
            $this->general_mdl->writeLog("Desbloqueo de cuenta usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Usuarios desbloqueado', $this);
        } else {
            $this->general_mdl->writeLog("Error al desbloquear cuenta usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    function desbloquear_todos(){
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $resultado = $this->bloqueados->desbloquear_todos();
        if ($resultado) {
            $this->general_mdl->writeLog("Desbloqueo de cuentas usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Usuarios desbloqueados', $this);
        } else {
            $this->general_mdl->writeLog("Error al desbloquear cuentas usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    function prueba()
    {
        $numero = $this->input->post("numero");
        $result = $this->bloqueados->prueba();
        echo json_encode($numero);
        successResponse("", 'Usuarios desbloqueado', $this);
    }
}
