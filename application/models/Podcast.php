<?php

class Podcast extends CI_Controller
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
        $this->load->model('podcast_mdl', 'podcast');
    }
    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández   Fecha: 10/26/2020
     *           mario.martinez.f@hotmail.es
     *    Nota: Funcion para obtener el listado de podcast registrados
     ***********************************************************************/
    public function ListPodcast()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $business_id = $valida_token['business_id'];
        $user_id = $valida_token['user_id'];
        $es_admin = $this->input->post("es_admin");
        if ($es_admin == null) {
            $es_admin = false;
        }
        $response = $this->podcast->ListPodcast($business_id, $user_id, $es_admin);
        if ($response) {
            $this->general_mdl->writeLog("Listado de podcast usuario " . $valida_token["user_id"], "<info>");
            successResponse($response, 'Listado de podcast', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener podcast usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Ocurrio un error, por favor vuelve a intentarlo.', $this);
        }
    }

    public function get_podcast_by_id()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $business_id = $valida_token['business_id'];
        $podcast_id = $this->input->post("id");

        $user_id = $valida_token['user_id'];
        $es_admin = false;

        $response = $this->podcast->get_podcast_by_id($business_id, $podcast_id, $user_id);
        if ($response) {
            $this->general_mdl->writeLog("obtener podcast por id usuario " . $valida_token["user_id"], "<info>");
            successResponse($response, 'Elemento de podcast', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener elemento de podcast usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Ocurrio un error, por favor vuelve a intentarlo.', $this);
        }
    }

    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández   Fecha: 10/26/2020
     *           mario.martinez.f@hotmail.es
     *    Nota: Funcion para obtener el listado de comentarios de un podcast
     ***********************************************************************/
    public function ListComments()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('podcast_id'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $data = array(
            'business_id' => $valida_token['business_id'],
            'user_id' => $valida_token['user_id'],
            'podcast_id' => $this->input->post('podcast_id')
        );
        $response = $this->podcast->ListComments($data);

        $this->general_mdl->writeLog("Listado de comentarios de podcast usuario " . $valida_token["user_id"], "<info>");
        successResponse($response, 'Listado de comentarios', $this);
    }
    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández   Fecha: 10/26/2020
     *           mario.martinez.f@hotmail.es
     *    Nota: Funcion para guardar un comentario nuevo
     ***********************************************************************/
    public function SaveComment()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('podcast_id', 'comment'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $data = $this->input->post();
        $data['user_id'] = $valida_token['user_id'];
        if ($this->podcast->SaveComment($data)) {
            $this->general_mdl->writeLog("Registro de comentario de podcast usuario " . $valida_token["user_id"], "<info>");
            successResponse('', 'El comentario se ha guardado correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar comentario de podcast usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Ocurrio un error, por favor vuelve a intentarlo.', $this);
        }
    }
    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández   Fecha: 10/26/2020
     *           mario.martinez.f@hotmail.es
     *    Nota: Funcion para dar like o unlike a un comentario
     ***********************************************************************/
    public function LikeUnlike()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('comment_id'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $data = $this->input->post();
        $data['user_id'] = $valida_token['user_id'];
        if ($this->podcast->LikeUnlike($data)) {
            $this->general_mdl->writeLog("Registro delike a comentario podcast usuario " . $valida_token["user_id"] . " comentario " . $data["comment_id"], "<info>");
            successResponse('', 'Se ha guardado correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar like a comentario podcast usuario " . $valida_token["user_id"] . " comentario " . $data["comment_id"], "<warning>");
            faildResponse('Ocurrio un error, por favor vuelve a intentarlo.', $this);
        }
    }

    public function ObtenerCalificacion()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_podcast = $this->input->post("id");
        $result = $this->podcast->ObtenerEstrellas($id_podcast);
        $this->general_mdl->writeLog("Consulta de calificacion de podcast usuario " . $valida_token["user_id"] . " podcast " . $id_podcast, "<info>");
        successResponse($result, 'Calificacion del podcast', $this);
    }

    public function CalificarPodcast()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = [];
        $data["podcast_id"] = $this->input->post("id");
        $data["score"] = $this->input->post("score");
        $data['user_id'] = $valida_token['user_id'];
        $result = $this->podcast->CalificarPodcast($data);
        $this->general_mdl->writeLog("Registro de calificacion de podcast usuario " . $valida_token["user_id"] . " podcast " . $data["podcast_id"], "<info>");
        successResponse($result, 'Se ha establecido la calificación', $this);
    }

    public function SetVisto()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = [];
        $data["podcast_id"] = $this->input->post("id");
        $data['user_id'] = $valida_token['user_id'];
        $result = $this->podcast->SetVisto($data);
        $this->general_mdl->agregar_recurso_visto($data["user_id"]);
        $this->general_mdl->writeLog("Registro de visto en podcast usuario " . $valida_token["user_id"] . " podcast " . $data["podcast_id"], "<info>");
        successResponse($result, 'Se ha establecido como visto el podcast', $this);
    }

    public function obtener_podcasts_capacitaciones()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        // echo json_encode($valida_token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $result = $this->podcast->obtener_podcasts_capacitaciones($valida_token["business_id"]);
        $this->general_mdl->writeLog("Podcasts para capacitacion obligatoria usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Podcasts para capacitacion obligatoria', $this);
    }
}
