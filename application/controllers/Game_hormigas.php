<?php
class Game_hormigas extends CI_Controller
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
        $this->load->model('game_hormigas_mdl', 'hormigas');
        $this->load->model('notification_mdl', 'notification');
    }

    public function obtener_tema(){
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('El token no es valido.', $this);
            return;
        }
        $respuesta = $this->hormigas->obtener_tema();
        if ($respuesta) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "games");
            $this->general_mdl->writeLog("Obtener tema hormigas " . $valida_token["user_id"], "<info>");
            successResponse($respuesta, 'Tema hormigas', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener tema hormigas usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al obtener tema hormigas', $this);
        }
    }

    function guardar_resultado(){
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = [];
        $data["user_id"] = $valida_token["user_id"];
        $data["id_tema"] = $this->input->post("id_tema");
        $data["resultado"] = $this->input->post("resultado");
        $result = $this->hormigas->guardar_resultado($data);
        if ($result) {
            $this->general_mdl->agregar_recurso_visto($data["user_id"]);
            if ($data["resultado"] == 1)
                $this->general_mdl->ModificarScoreUsuario($data["user_id"], 1);
            else {
                $this->general_mdl->ModificarScoreUsuario($data["user_id"], -1);
            }
            $this->general_mdl->writeLog("Registro de puntuacion hormigas usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Se guardo correctamente la puntuación', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar puntuacion hormigas usuario " . $valida_token["user_id"], "<warning>");
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
        $result = $this->hormigas->obtenerMejorPuntuacion($business_id);
        if ($result) {
            $this->general_mdl->writeLog("Listado de mejores puntuaciones hormiguero usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Mejor puntuacion del usuario', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener mejores puntuaciones hormiguero usuario " . $valida_token["user_id"], "<warning>");
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
        $result = $this->hormigas->obtenerMejorPuntuacionEquipo($business_id, $id_job);

        if ($result) {
            $this->general_mdl->writeLog("Listado de mejores puntuaciones hormiguero por equipo usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Mejor puntuacion de usuarios por equipo', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener mejores puntuaciones hormiguero por equipo usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin registros", $this);
        }
    }
}