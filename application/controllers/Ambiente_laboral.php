<?php
class Ambiente_laboral extends CI_Controller
{
    public $defaultLang = 'es';

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("America/Mexico_City");

        $headers = $this->input->request_headers();
        $this->lang->load('message', 'es');
        $this->defaultLang = 'es';

        $this->load->model('ambiente_laboral_mdl', 'ambiente_laboral');
    }

    public function obtener_fechas()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $fechas = $this->ambiente_laboral->obtener_fechas();
        successResponse($fechas, 'Rango de fechas habilitadas para ambiente laboral', $this);
    }

    public function insertar_fechas(){
        if ($this->input->post() == []) {
			$_POST = json_decode(file_get_contents('php://input'), true);
		}
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $fecha_inicio = $this->input->post("fecha_inicio");
        $fecha_fin = $this->input->post("fecha_fin");
        $result = $this->ambiente_laboral->insertar_fechas($fecha_inicio,$fecha_fin);
        successResponse($result, 'Fechas registradas correctamente', $this);
    }

    public function guardar_dias_preguntas(){
        if ($this->input->post() == []) {
			$_POST = json_decode(file_get_contents('php://input'), true);
		}
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $datos = $this->input->post("datos");
        $result = $this->ambiente_laboral->guardar_dias_preguntas($datos);
        successResponse($result, 'Guardado correctamente', $this);
    }

    public function establecer_activo(){
        if ($this->input->post() == []) {
			$_POST = json_decode(file_get_contents('php://input'), true);
		}
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_cuestionario = $this->input->post("id_cuestionario");
        
        $result = $this->ambiente_laboral->establecer_activo($id_cuestionario,$valida_token["business_id"]);
        successResponse($result, 'Se establecio activo', $this);
    }
}