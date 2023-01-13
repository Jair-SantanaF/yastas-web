<?php
class Com extends CI_Controller
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
        $this->load->model('groups_mdl', 'groups');
        $this->load->model('notification_mdl', 'notification');
        $this->load->model('games_mdl', 'games');
        $this->load->model('com_mdl', 'com');
        $this->load->model('capacitacion_mdl', 'capacitacion');
    }

    public function getTopicsAdmin()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        if ($valida_token["rol_id"] == 6) {
            $valida_token["id_asesor"] = $valida_token["user_id"];
        }
        $filtro = $this->input->post("filtro");

        $resultado = $this->com->getTopicsAdmin($filtro, $valida_token["business_id"], $valida_token["id_region"], $valida_token["id_asesor"]);
        if ($resultado) {
            // foreach ($resultado as $key => $value) {
            // for ($i = 0; $i < count($resultado); $i++) {
                
            // }
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "aprendiendo juntos");
            $this->general_mdl->writeLog("Listado de topicos comunidad de aprendizaje Admin usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Data', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener topicos comunidad de aprendizaje Admin usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    public function getTopics()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        if ($valida_token["rol_id"] == 6) {
            $valida_token["id_asesor"] = $valida_token["user_id"];
        }
        $resultado = $this->com->getTopics($valida_token['user_id'], $valida_token["id_region"], $valida_token["id_asesor"]);
        if ($resultado) {
            for ($i = 0; $i < count($resultado); $i++) {
                // $users_by_topic = $this->com->getUsersByTopic($resultado[$i]['id'], $valida_token["user_id"]);
                $grupos = $this->com->obtenerGrupos($resultado[$i]["id"]);
                $resultado[$i]['users'] = [];//$users_by_topic;
                $resultado[$i]["grupos"] = $grupos;
            }
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "aprendiendo juntos");
            $this->general_mdl->writeLog("Listado de topicos comunidad de aprendizaje usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Data', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener topicos comunidad de aprendizaje usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    public function getUsersByTopic(){
        $token = $this->input->post("token");
        $id_topic = $this->input->post("id_topic");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $users_by_topic = $this->com->getUsersByTopic($id_topic, $valida_token["user_id"]);
        $grupos = $this->com->obtenerGrupos($id_topic);
        $this->general_mdl->writeLog("Listado de usuarios por topic usuario " . $valida_token["user_id"], "<info>");

        successResponse(array("users" => $users_by_topic,"grupos" => $grupos), 'Data', $this);
    }

    public function getTopicById()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_topic = $this->input->post("id_topic");
        $resultado = $this->com->getTopicById($id_topic, $valida_token['user_id']);
        if ($resultado) {
            // foreach ($resultado as $key => $value) {
            for ($i = 0; $i < count($resultado); $i++) {
                $users_by_topic = [];//$this->com->getUsersByTopic($resultado[$i]['id'], $valida_token["user_id"]);
                $resultado[$i]['users'] = $users_by_topic;
            }
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "aprendiendo juntos");
            $this->general_mdl->writeLog("Topic comunidad de aprendizaje usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Data', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener topic comunidad de aprendizaje usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    public function saveTopic()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('name'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        $data = [];
        $data['name'] = $this->input->post("name");
        $data['id_user'] = $valida_token['user_id'];
        $data['date'] = date('Y-m-d H:i:s');
        $data['capacitacion_obligatoria'] = $this->input->post("capacitacion_obligatoria");
        
        $usuarios = json_decode($this->input->post("usuarios"), true);
        $grupos = json_decode($this->input->post("grupos"), true);
        $regiones = json_decode($this->input->post("regiones"));
        $asesores = json_decode($this->input->post("asesores"));

        // $usuarios = $this->input->post("usuarios");
        // if (!isset($usuarios) && $usuarios != false) {
        //     $usuarios = [];
        // }
        // $grupos = $this->input->post("grupos");
        // if (!isset($grupos)) {
        //     $grupos = [];
        // }

        $registered_record = $this->com->createTopic($data);

        if ($registered_record['result']) {
            $data['id_topic'] = $registered_record['id'];

            $this->com->subscribeToTopic($data);
            // $this->com->agregar_regiones($data["id_topic"], $regiones);
            // $this->com->agregar_asesores($data["id_topic"], $asesores);
            if (is_array($usuarios))
                for ($i = 0; $i < count($usuarios); $i++) {
                    $data = [];
                    $data['id_user'] = $usuarios[$i];
                    $data['id_topic'] = $registered_record['id'];
                    $data['date'] = date('Y-m-d H:i:s');
                    $this->com->subscribeToTopic($data);
                }
            if (is_array($grupos) && count($grupos) > 0)
                $this->com->agregarGrupos($grupos, $data["id_topic"]);
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "aprendiendo juntos");
            $this->general_mdl->writeLog("Registro de topico comunidad de aprendizaje usuario " . $valida_token["user_id"], "<info>");
            successResponse(["registered_record" => $registered_record["id"]], 'El tema se ha guardado correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar topico comunidad de aprendizaje usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Ocurrio un error, por favor vuelve a intentarlo.', $this);
        }
    }

    public function editTopic()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('id'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        $data = [];
        $data["id"] = $this->input->post("id");
        $data["name"] = $this->input->post("name");
        $id_region = $this->input->post("id_region") > 0 ? $this->input->post("id_region") : null;
        $id_asesor = $this->input->post("id_asesor") > 0 ? $this->input->post("id_asesor") : null;
        $data["id_region"] = $id_region;
        $data["id_asesor"] = $id_asesor;
        $regiones = json_decode($this->input->post("regiones"));
        $asesores = json_decode($this->input->post("asesores"));

        if ($this->com->editTopic($data)) {
            // $this->com->agregar_regiones($data["id"], $regiones);
            // $this->com->agregar_asesores($data["id"], $asesores);
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "aprendiendo juntos");
            $this->general_mdl->writeLog("Actualizacion topico comunidad de aprendizaje usuario " . $valida_token["user_id"] . " topico " . $this->input->post("id"), "<info>");
            successResponse('', 'El elemento ha sido editado correctamente.', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar topico comunidad de aprendizaje usuario " . $valida_token["user_id"] . " topico " . $this->input->post("id"), "<warning>");
            faildResponse('El elemento no se pudo eliminar.', $this);
        }
    }

    public function removeTopic()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('id'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        //Se procede a eliminar el area requerida
        $data = array(
            "id" => $this->input->post("id"),
            "active" => $this->input->post("activo")
        );

        if ($this->com->deleteTopic($data)) {
            $this->capacitacion->eliminarElementoAll($data["id"], 3);
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "aprendiendo juntos");
            $this->general_mdl->writeLog("Eliminacion de topico comunidad de aprendizaje usuario " . $valida_token["user_id"] . " topico " . $this->input->post("id"), "<info>");
            successResponse('', 'El elemento ha sido eliminado correctamente.', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar topico comunidad de aprendizaje usuario " . $valida_token["user_id"] . " topico " . $this->input->post("id"), "<warning>");
            faildResponse('El elemento no se pudo eliminar.', $this);
        }
    }

    public function subscribeToTopicMultiple()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $usuarios = $this->input->post("usuarios");
        if (!isset($usuarios)) {
            $usuarios = [];
        }

        $id_com = $this->input->post("id_comunidad");

        for ($i = 0; $i < count($usuarios); $i++) {
            $data = [];
            $data['id_user'] = $usuarios[$i];
            $data['id_topic'] = $id_com;
            $data['date'] = date('Y-m-d H:i:s');
            $this->com->subscribeToTopic($data);
        }
        $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "aprendiendo juntos");
        $this->general_mdl->writeLog("Registro de usuarios en comunidad de aprendizaje usuario " . $valida_token["user_id"], "<info>");
        successResponse(["registered_record" => $id_com], 'Registrados en comunidad', $this);
        // } else {
        //     $this->general_mdl->writeLog("Error al guardar topico comunidad de aprendizaje usuario " . $valida_token["user_id"], "<warning>");
        //     faildResponse('Ocurrio un error, por favor vuelve a intentarlo.', $this);
        // }
    }

    public function subscribeToTopic()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id_user', 'id_topic'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        $data = [];
        $data['id_user'] = $this->input->post('id_user'); //$valida_token['user_id'];
        $data['id_topic'] = $this->input->post('id_topic');
        $data['date'] = date('Y-m-d H:i:s');

        if ($this->com->subscribeToTopic($data)) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "aprendiendo juntos");
            $this->general_mdl->writeLog("Registro de usuarios a topico comunidad de aprendizaje usuario " . $valida_token["user_id"] . " topico " . $this->input->post("id"), "<info>");
            successResponse('', 'Se subscribió al usuario correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al registrar usuarios a topico comunidad de aprendizaje usuario " . $valida_token["user_id"] . " topico " . $this->input->post("id"), "<warning>");
            faildResponse('El usuario ya esta registrado', $this);
        }
    }

    public function unsubscribeToTopic()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id_topic'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        $data = [];
        $data['id_user'] = $this->input->post('user_id');
        $data['id_topic'] = $this->input->post('id_topic');

        if ($this->com->unsubscribeToTopic($data)) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "aprendiendo juntos");
            $this->general_mdl->writeLog("Eliminacion de usuario de topico comunidad de aprendizaje usuario " . $valida_token["user_id"] . " topico " . $this->input->post("id"), "<info>");
            successResponse('', 'Se ha removido el usuario del topic', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar usuario de topico comunidad de aprendizaje usuario " . $valida_token["user_id"] . " topico " . $this->input->post("id"), "<warning>");
            faildResponse('Ocurrio un error, por favor vuelve a intentarlo.', $this);
        }
    }

    public function saveMessage()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id_topic', 'message'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        
        $data = [];
        $data['id_user'] = $valida_token['user_id'];
        $data['id_topic'] = $this->input->post("id_topic");
        $data['message'] = $this->input->post("message");
        $data['date'] = date('Y-m-d H:i:s');
        $tokens = $this->notification->obtenerTokensComunidad($data["id_topic"], $data["id_user"]);
        if ($this->com->saveMessage($data)) {
            $this->establecerCapacitacionCompleta($data["id_user"], $data["id_topic"]);
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
                    $data_ = array('title' => 'Aprendiendo juntos', 'notification' => 'Tienes un mensaje nuevo en comunidad de aprendizaje de ' . $nombre_usuario, 'user_id' => $value['user_id'], 'service_id' => SERVICE_COMUNIDAD, 'user_create_id' => $valida_token['user_id'], "id_topic" => $data["id_topic"]);
                    $this->notification->RegisterNotification($data_);
                }

                //$this->general_mdl->EnviarNotificacionPush($tokens_, 'Tienes un mensaje nuevo en comunidad de aprendizaje de ' . $nombre_usuario, 'Aprendiendo juntos', SERVICE_COMUNIDAD, true, array("id_topic" => $data["id_topic"]));
                // echo json_encode("se debio enviar la notificacion por char");
            }
            //aqui validar si es el primer mensaje que manda en la comunidad
            //si es asi entonces asignarle el recurso como visto
            $numero_mensajes = $this->com->getNumeroMensajes($data["id_user"], $data["id_topic"]);
            // if ($numero_mensajes == 1) //aqui la validacion
            // {
            //     $this->general_mdl->agregar_recurso_visto($data["id_user"]);
            // }
            if ($numero_mensajes == 2) {
                $this->general_mdl->asignarInsignia(17, $data["id_user"]);
            }
            if ($numero_mensajes == 5) {
                $this->general_mdl->asignarInsignia(18, $data["id_user"]);
            }
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "aprendiendo juntos");
            $this->general_mdl->writeLog("Registro de mensaje topico comunidad de aprendizaje usuario " . $valida_token["user_id"] . " topico " . $this->input->post("id_topic"), "<info>");
            successResponse('', 'El mensaje se ha guardado correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar mensaje topico comunidad de aprendizaje usuario " . $valida_token["user_id"] . " topico " . $this->input->post("id_topic"), "<warning>");
            faildResponse('Ocurrio un error, por favor vuelve a intentarlo.', $this);
        }
    }

    public function establecerCapacitacionCompleta($id_usuario, $id_topic)
    {
        $this->com->establecerCapacitacionCompleta($id_usuario, $id_topic);
    }

    public function getMessages()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        if ($valida_token["rol_id"] == 6) {
            $valida_token["id_asesor"] == $valida_token["user_id"];
        }

        $data = array(
            "id_user" => $valida_token['user_id'],
            "id_topic" => $this->input->post("id_topic"),
            "id_region" => $valida_token["id_region"],
            "id_asesor" => $valida_token["id_asesor"]
        );
        $resultado = $this->com->getMessages($data);
        if ($resultado) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "aprendiendo juntos");
            $this->general_mdl->writeLog("Listado de mensajes de topico comunidad de aprendizaje usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Data', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener mensajes de topico comunidad de aprendizaje usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    function agregarGrupos()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('El token no es valido', $this);
            return;
        }
        $quiz_id = $this->input->post("com_id");
        $grupos = $this->input->post("grupos");
        $result = $this->com->agregarGrupos($grupos, $quiz_id);
        if ($result) {
            $this->general_mdl->writeLog("Alta de grupos en comunidad usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Grupos agregados', $this);
        } else {
            $this->general_mdl->writeLog("Error al agregar grupos en comunidad usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al agregar grupos', $this);
        }
    }

    function agregarGrupo()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('El token no es valido', $this);
            return;
        }
        $quiz_id = $this->input->post("com_id");
        $group_id = $this->input->post("group_id");
        $grupo = [];
        $grupo["com_id"] = $quiz_id;
        $grupo["group_id"] = $group_id;
        $result = $this->com->agregarGrupo($grupo);
        if ($result) {
            $this->general_mdl->writeLog("Alta de grupo en comunidad usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Grupo agregado', $this);
        } else {
            $this->general_mdl->writeLog("Error al agregar grupo en comunidad usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al agregar grupo', $this);
        }
    }

    function eliminarGrupo()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('El token no es valido', $this);
            return;
        }
        $group_id = $this->input->post("group_id");
        $quiz_id = $this->input->post("com_id");
        $result = $this->com->eliminarGrupo($group_id, $quiz_id);
        if ($result) {
            $this->general_mdl->writeLog("Eliminacion de grupo en comunidad usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Grupo eliminado', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar grupo en comunidad usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al eliminar grupo', $this);
        }
    }

    public function GuardarLikeCommentario()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id_mensaje'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $data = [];
        $data["message_id"] = $this->input->post("id_mensaje");
        $data['user_id'] = $valida_token['user_id'];
        if ($this->com->SaveLikeMessage($data)) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "aprendiendo juntos");

            $this->general_mdl->writeLog("Registro de like a comentario en Comunidad usuario " . $valida_token["user_id"] . " comentario " . $data["message_id"], "<info>");
            successResponse('', 'El like se ha registrado correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al dar like a comentario en Comunidad usuario " . $valida_token["user_id"] . " comentario " . $data["message_id"], "<warning>");
            faildResponse('El like no ha registrado correctamente', $this);
        }
    }

    public function obtener_comunidades_capacitaciones()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        // echo json_encode($valida_token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $result = $this->elearning_model->obtener_comunidades_capacitaciones($valida_token["business_id"]);
        $this->general_mdl->writeLog("Comunidades para capacitacion obligatoria usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Comunidades para capacitacion obligatoria', $this);
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
        $data["com_id"] = $this->input->post("id");
        $data['user_id'] = $valida_token['user_id'];
        $result = $this->com->SetVisto($data);
        $this->general_mdl->writeLog("Registro de visto en comunidad de aprendizaje usuario " . $valida_token["user_id"] . " comunidad de aprendizaje " . $data["com_id"], "<info>");
        successResponse($result, 'Se ha establecido como visto el comunidad de aprendizaje', $this);
    }
}
