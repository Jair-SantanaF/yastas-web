<?php
class Capacitacion extends CI_Controller
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
        $this->load->model('capacitacion_mdl', 'capacitacion');
        $this->load->model('library_mdl', 'library');
    }

    public function getCapacitaciones()
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

        $resultado = $this->capacitacion->getCapacitaciones($valida_token['user_id'], $valida_token['business_id']);
        if ($resultado) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "mi aprendizaje");
            $this->general_mdl->writeLog("Listado de capacitaciones usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Data', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener capacitaciones usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Por ahora no tienes capacitaciones asignadas/pendientes", $this);
        }
    }

    public function getCapacitacionesAdmin()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $resultado = $this->capacitacion->getCapacitacionesAdmin($valida_token['user_id'], $valida_token['business_id']);
        if ($resultado) {
            $this->general_mdl->writeLog("Listado de capacitaciones (admin) usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Data', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener capacitaciones (admin) usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Por ahora no tienes capacitaciones asignadas/pendientes", $this);
        }
    }

    public function getDetailAdmin()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('capacitacion_id'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        $resultado = $this->capacitacion->getDetail($this->input->post('capacitacion_id'), $this->input->post("user_id"), $valida_token['business_id']);
        if ($resultado) {
            $this->general_mdl->writeLog("Consulta detalle de capacitacion usuario " . $valida_token["user_id"] . " capacitacion " . $this->input->post("capacitacion_id"), "<info>");
            successResponse($resultado, 'Data', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener detalle de capacitacion usuario " . $valida_token["user_id"] . " capacitacion " . $this->input->post("capacitacion_id"), "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    public function getDetail()
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

        $validaPost = $this->general_mdl->validapost(array('capacitacion_id'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $resultado = $this->capacitacion->getDetail($this->input->post('capacitacion_id'), $valida_token['user_id'], $valida_token['business_id']);
        if ($resultado) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "mi aprendizaje");
            $this->general_mdl->writeLog("Consulta detalle de capacitacion usuario " . $valida_token["user_id"] . " capacitacion " . $this->input->post("capacitacion_id"), "<info>");
            successResponse($resultado, 'Data', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener detalle de capacitacion usuario " . $valida_token["user_id"] . " capacitacion " . $this->input->post("capacitacion_id"), "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    public function asistenciaCapacitacionPresencial()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
		$capacitacion_id = $this->input->post("capacitacion_id") ?? false;
		$latitud = $this->input->post("latitud") ?? false;
		$longitud = $this->input->post("longitud") ?? false;
		$domicilio = $this->input->post("domicilio") ?? null;
		if(!$capacitacion_id || !$latitud || !$longitud){
			faildResponse('Datos obligatorios.', $this);
			return;
		}
		$respuesta = $this->capacitacion->asistenciaCursoPresencial($valida_token['user_id'], $capacitacion_id, $latitud, $longitud, $domicilio);
		if ($respuesta) {
			successResponse($respuesta, 'Asistencia', $this);
			$this->general_mdl->writeLog("Asistencia usuario " . $valida_token['user_id'], "<info>");
		} else {
			faildResponse('Ya cuenta con asistencia', $this);
			$this->general_mdl->writeLog("No existe el Asistencia usuario " . $valida_token['user_id'], "<warning>");
		}
	}

    public function getCapacitacionByID()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "mi aprendizaje");
        $resultado = $this->capacitacion->getCapacitacionByID($this->input->post("id_capacitacion"), $valida_token["business_id"]);
        $this->general_mdl->writeLog("Consulta detalles de capacitacion usuario " . $valida_token["user_id"] . " capacitacion" . $this->input->post("id_capacitacion"), "<info>");
        successResponse($resultado, 'Detalles de la capacitacion', $this);
    }

    public function save()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $config['upload_path'] = './uploads/business_' . $valida_token["business_id"] . '/capacitaciones/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|mp4|mp3|pdf';

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('imagen')) {
            $error = array('error' => $this->upload->display_errors());
        }

        // echo json_encode($this->input->post());
        $usuarios = json_decode($this->input->post("usuarios"), true);
        $grupos = json_decode($this->input->post("grupos"), true);

        $resultado = $this->capacitacion->save($this->input->post(), $valida_token["business_id"]);
        if ($resultado) {
            $this->capacitacion->agregarUsuarios($resultado, $usuarios);
            $this->capacitacion->agregarGrupos($resultado, $grupos);
            $this->general_mdl->writeLog("Registro de capacitacion usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Data', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar capacitacion usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    public function markCompleted()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('elemento_id', 'catalog'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        if ($this->input->post('catalog') == "library_elements_") {
            $data["library_element_id"] = $this->input->post("elemento_id");
            $data['user_id'] = $valida_token['user_id'];
            $data["numero_clicks"] = 0;
            $this->library->SetVisto($data);
        }

        $resultado = $this->capacitacion->markCompleted($valida_token['user_id'], $this->input->post('elemento_id'), $this->input->post('catalog'));
        $completas = $this->capacitacion->obtenerCapacitacionesCompletas($valida_token['user_id']);
        if ($resultado) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "mi aprendizaje");
            $this->general_mdl->writeLog("Registro elemento capacitacion completo usuario " . $valida_token["user_id"] . " elemento " . $this->input->post("elemento_id") . " catalogo " . $this->input->post("catalog"), "<info>");
            successResponse($resultado, 'Data', $this, $completas);
        } else {
            $this->general_mdl->writeLog("Error al establecer elemento capacitacion completo usuario " . $valida_token["user_id"] . " elemento " . $this->input->post("elemento_id") . " catalogo " . $this->input->post("catalogo"), "<warning>");
            faildResponse("Sin datos", $this);
        }
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
        $data["capacit_id"] = $this->input->post("id");
        $data['user_id'] = $valida_token['user_id'];
        $result = $this->capacitacion->SetVisto($data);
        $this->general_mdl->writeLog("Registro de visto en capacitacion usuario " . $valida_token["user_id"] . " capacitacion " . $data["capacit_id"], "<info>");
        successResponse($result, 'Se ha establecido como visto el capacitacion', $this);
    }

    public function deleteCapacitacion()
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

        $resultado = $this->capacitacion->deleteCapacitacion($this->input->post());
        if ($resultado) {
            $this->general_mdl->writeLog("Eliminacion de capacitacion usuario " . $valida_token["user_id"] . " capacitacion " . $this->input->post("id"), "<info>");
            successResponse($resultado, 'Data', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar capacitacion usuario " . $valida_token["user_id"] . " capacitacion " . $this->input->post("id"), "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    function agregarUsuario()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id_usuario', 'id_capacitacion'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $resultado = $this->capacitacion->agregarUsuario($this->input->post('id_usuario'), $this->input->post('id_capacitacion'));
        if ($resultado) {
            $this->general_mdl->writeLog("Registro de usuario a capacitacion usuario " . $valida_token["user_id"] . " capacitacion " . $this->input->post("id_capacitacion"), "<info>");
            successResponse($resultado, 'Data', $this);
        } else {
            $this->general_mdl->writeLog("Error al registrar usuario en capacitacion usuario " . $valida_token["user_id"] . " capacitacion " . $this->input->post("id_capacitacion"), "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    function eliminarUsuario()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id_usuario', 'id_capacitacion'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $resultado = $this->capacitacion->eliminarUsuario($this->input->post('id_usuario'), $this->input->post('id_capacitacion'));
        if ($resultado) {
            $this->general_mdl->writeLog("Eliminacion de usuario de capacitacion usuario " . $valida_token["user_id"] . " capacitacion " . $this->input->post("id_capacitacion") . " eliminado " . $this->input->post("id_usuario"), "<info>");
            successResponse($resultado, 'Data', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar usuario de capacitacion usuario " . $valida_token["user_id"] . " capacitacion " . $this->input->post("id_capacitacion") . " eliminado " . $this->input->post("id_usuario"), "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    function agregarElemento()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $resultado = $this->capacitacion->agregarElemento($this->input->post(), $valida_token["business_id"]);
        if ($resultado) {
            $this->general_mdl->writeLog("Registro de elemento a capacitacion usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Data', $this);
        } else {
            $this->general_mdl->writeLog("Error al registrar elemento a capacitacion usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    function eliminarElemento()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $resultado = $this->capacitacion->eliminarElemento($this->input->post(), $valida_token["business_id"]);
        if ($resultado) {
            $this->general_mdl->writeLog("Eliminacion de elemento de capacitacion usuario " . $valida_token["user_id"], "<info>");
            successResponse($resultado, 'Data', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar elemento de capacitacion usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("Sin datos", $this);
        }
    }

    function actualizarCapacitacion()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $bandera = json_decode($this->input->post("imagen_actualizada"));
        if ($bandera == true) {
            $config['upload_path'] = './uploads/business_' . $valida_token["business_id"] . '/capacitaciones/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('imagen')) {
                $error = array('error' => $this->upload->display_errors());
                echo json_encode($error);
            }
        }

        $resultado = $this->capacitacion->actualizarCapacitacion($this->input->post());
        if ($resultado) {
            $this->general_mdl->writeLog("Actualizacion de capacitacion usuario " . $valida_token["user_id"] . " capacitacion " . $this->input->post("capacitacion"), "<info>");
            successResponse($resultado, 'Data', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar capacitacion usuario " . $valida_token["user_id"] . " capacitacion " . $this->input->post("capacitacion"), "<warning>");
            faildResponse("Error al actualizar capacitacion", $this);
        }
    }

    function actualizarOrden()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $elementos = $this->input->post("elementos");
        $resultado = $this->capacitacion->actualizarOrden($elementos);
        if ($resultado) {
            $this->general_mdl->writeLog("Actualizacion de orden elementos capacitacion usuario " . $valida_token["user_id"] . " capacitacion " . $this->input->post("capacitacion"), "<info>");
            successResponse($resultado, 'Orden actualizado', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar orden elementos capacitacion usuario " . $valida_token["user_id"] . " capacitacion " . $this->input->post("capacitacion"), "<warning>");
            faildResponse("Error al actualizar orden elementos capacitacion", $this);
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
        $quiz_id = $this->input->post("capacitacion_id");
        $grupos = $this->input->post("grupos");
        $result = $this->capacitacion->agregarGrupos($quiz_id, $grupos);
        if ($result) {
            $this->general_mdl->writeLog("Alta de grupos en capacitacion usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Grupos agregados', $this);
        } else {
            $this->general_mdl->writeLog("Error al agregar grupos en capacitacion usuario " . $valida_token["user_id"], "<warning>");
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
        $quiz_id = $this->input->post("capacitacion_id");
        $group_id = $this->input->post("group_id");
        $grupo = [];
        $grupo["capacit_id"] = $quiz_id;
        $grupo["group_id"] = $group_id;
        $result = $this->capacitacion->agregarGrupo($grupo);
        if ($result) {
            $this->general_mdl->writeLog("Alta de grupo en capacitacion usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Grupo agregado', $this);
        } else {
            $this->general_mdl->writeLog("Error al agregar grupo en capacitacion usuario " . $valida_token["user_id"], "<warning>");
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
        $quiz_id = $this->input->post("capacitacion_id");
        $result = $this->capacitacion->eliminarGrupo($group_id, $quiz_id);
        if ($result) {
            $this->general_mdl->writeLog("Eliminacion de grupo en capacitacion usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Grupo eliminado', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar grupo en capacitacion usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al eliminar grupo', $this);
        }
    }

    function obtener_capacitaciones_obligatorias(){
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse('El token no es valido', $this);
            return;
        }
       
        $result = $this->capacitacion->obtener_capacitaciones_obligatorias($valida_token["user_id"]);
        if ($result) {
            $this->general_mdl->writeLog("Capacitaciones obligatorias usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Capacitaciones obligatorias', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener capacitaciones obligatorias usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al obtener capacitaciones obligatorias grupo', $this);
        }
    }
}
