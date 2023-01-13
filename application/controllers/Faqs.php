<?php

use FontLib\Table\Type\post;

class Faqs extends CI_Controller
{
    public $defaultLang = 'es';

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("America/Mexico_City");
        $headers = $this->input->request_headers();
        $this->lang->load('message', 'en');
        $this->load->model('Faqs_mdl', 'model');
        /* $this->load->model('Podcast_mdl', 'model'); */
        $this->load->model('notification_mdl', 'notification');
    }

    function obtener_resultados()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $busqueda = $this->input->post("busqueda");
        $id_categoria = $this->input->post("id_categoria");
        $result = $this->model->buscar_en_resumen($busqueda, $valida_token["user_id"], $valida_token["business_id"], $id_categoria);
        $result["preguntas"] = $this->model->buscar_en_preguntas($busqueda, $id_categoria, $valida_token["business_id"]);
        if ($result) {
            $this->general_mdl->writeLog("Busqueda en q&a usuario " . 43, "<info>");
            successResponse($result, 'Resultados', $this);
        } else {
            $this->general_mdl->writeLog("Error al buscar en q&a usuario " . 43, "<warning>");
            faildResponse("Error al obtener resultados", $this);
        }
    }

    function obtener_categorias()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $result = $this->model->obtener_categorias();
        if ($result) {
            $this->general_mdl->writeLog("Categorias usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Categorias', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener categorias usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al obtener categorias", $this);
        }
    }

    function obtener_carrusel()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_pregunta = $this->input->post("id_pregunta");
        $result = $this->model->obtener_carrusel($valida_token["business_id"], $id_pregunta);
        if ($result) {
            $this->general_mdl->writeLog("Carrusel faqs usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Carrusel', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener carrusel usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al obtener carrusel", $this);
        }
    }

    function actualizar_extracto()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $id_elemento = $this->input->post("id_elemento"); /* si es editar */
        $tipo = $this->input->post("tipo");
        $data = $this->input->post(); /* mas columnas */
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        unset($data["id_elemento"]);
        unset($data["token"]);
        $result = $this->model->actualizar($data, $id_elemento, $tipo);
        if ($result) {
            $this->general_mdl->writeLog("Actualizacion de extracto usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Actualizacion de extracto', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar extracto usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al actualizar extracto", $this);
        }
    }

    function obtener_preguntas()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $result = $this->model->obtener_preguntas($valida_token["business_id"]);
        if ($result) {
            $this->general_mdl->writeLog("Preguntas faqs usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Preguntas', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener preguntas faqs usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al obtener preguntas", $this);
        }
    }

    function guardar_pregunta()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $data = $this->input->post();
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $archivos = $this->general_mdl->GuardarArchivos($_FILES, $ruta = 'faqs', $valida_token['business_id']);
        if (!$archivos['success']) {
            faildResponse($archivos['msg'], $this);
            return;
        }
        $image = '';
        if (isset($archivos['success_files']['image'])) {
            $image = $archivos['success_files']['image'];
        }
        $data["imagen"] = $image;
        unset($data['token']);
        $id_pregunta = $this->model->guardar_pregunta($data);
        if ($id_pregunta) {
            /* notificacion */
            /* $id_region =  $this->input->post('id_region') == null ? 0 :  $this->input->post('id_region');
            $id_asesor =  $this->input->post('id_asesor') == null ? 0 :  $this->input->post('id_asesor');
            $tokens = $this->notification->ListUserNotification($valida_token['business_id'], 0, $id_region, $id_asesor);
            if ($tokens) {
                $tokens_ = array();
                foreach ($tokens as $index => $value) {
                    array_push($tokens_, $value['token']);
                    $data_ = array('title' => 'Q&A', 'notification' => 'Se ha agregado una pregunta nueva', 'user_id' => $value['user_id'], 'service_id' => SERVICE_QA, 'user_create_id' => $valida_token['user_id'], "id_topic" => $id_pregunta);
                    $this->notification->RegisterNotification($data_);
                }
                $this->general_mdl->EnviarNotificacionPush($tokens_, 'Se ha agregado un nuevo elemento en Q&A', 'Q&A', SERVICE_QA, true, array("id_topic" => $id_pregunta));
            } */
            $this->general_mdl->writeLog("Se guardo pregunta faqs usuario " . $valida_token["user_id"], "<info>");
            successResponse($id_pregunta, 'Se guardo pregunta ', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar pregunta faqs usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al guardar pregunta", $this);
        }
    }

    function actualizar_pregunta()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $data = $this->input->post();
        $id_pregunta = $this->input->post("id");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $image = '';
        $archivos = $this->general_mdl->GuardarArchivos($_FILES, $ruta = 'faqs', $valida_token['business_id']);
        if (!$archivos['success'] || $archivos['msg'] == "No se subieron archivos.") {
            unset($data["imagen"]);
        } else if (isset($archivos['success_files']['imagen'])) {
            $image = $archivos['success_files']['imagen'];
            $data["imagen"] = $image;
        }
        unset($data["id"]);
        $result = $this->model->actualizar_pregunta($data, $id_pregunta);
        if ($result) {
            $this->general_mdl->writeLog("Se actualizo pregunta faqs usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Se actualizo pregunta ', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar pregunta faqs usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al actualizar pregunta", $this);
        }
    }

    function eliminar_pregunta()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_pregunta = $this->input->post("id");
        $result = $this->model->eliminar_pregunta($id_pregunta);
        if ($result) {
            $this->general_mdl->writeLog("Eliminar Preguntas faqs usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Eliminar Pregunta', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar preguntas faqs usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al eliminar preguntas", $this);
        }
    }

    function eliminar_imagen_carrusel()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id = $this->input->post("id");
        $result = $this->model->eliminar_imagen_carrusel($id);
        if ($result) {
            $this->general_mdl->writeLog("Eliminar imagen carrusel faqs usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Eliminar imagen carrusel', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar imagen faqs usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al eliminar imagen carrusel", $this);
        }
    }

    function agregar_imagenes_carrusel()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $archivos = $this->general_mdl->GuardarArchivos($_FILES, $ruta = 'faqs', $valida_token['business_id']);
        if (!$archivos['success']) {
            faildResponse($archivos['msg'], $this);
            return;
        }
        $image = '';
        if (isset($archivos['success_files']['image'])) {
            $image = $archivos['success_files']['image'];
        }
        $data["imagen"] = $image;
        $data["id_pregunta"] = $this->input->post("id_pregunta");;
        unset($data['token']);
        $result = $this->model->agregar_imagen_carrusel($data);
        if ($result) {
            $this->general_mdl->writeLog("Agregar imagen carrusel faqs usuario " . $valida_token["user_id"], "<info>");
            successResponse($image, 'Agregar imagen carrusel', $this);
        } else {
            $this->general_mdl->writeLog("Error al agregar imagen faqs usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al agregar imagen carrusel", $this);
        }
    }

    function actualizar_orden_imagenes()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $id = $this->input->post("id");
        $orden = $this->input->post("orden");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $result = $this->model->actualizar_orden_imagenes($id, $orden);
        if ($result) {
            $this->general_mdl->writeLog("Se actualizo carrusel faqs usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Se actualizo carrusel ', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar carrusel faqs usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al actualizar carrusel", $this);
        }
    }

    function obtener_ultimo_carrusel()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $result = $this->model->obtener_ultimo_carrusel($valida_token["business_id"]);
        if ($result) {
            $this->general_mdl->writeLog("Ultimo carrusel usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Ultimo carrusel', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener ultimo carrusel usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Error al obtener ultimo carrusel", $this);
        }
    }

}
