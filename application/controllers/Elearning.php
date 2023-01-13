<?php

class Elearning extends CI_Controller
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
		$this->load->model('notification_mdl', 'notification');
	}

	// OBTENEMOS EL LISTADO DE TODOS LOS CURSOS DISPONBILES
	public function elearningModules()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);

		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}

		$data = $this->input->post();
		$data['category_id'] = (isset($data['category_id']) && $data["category_id"] != '') ? $data['category_id'] : null;
		$data['subcategory_id'] = (isset($data['subcategory_id']) && $data["subcategory_id"] != '') ? $data['subcategory_id'] : null;
		$data['id'] = (isset($data['id'])) ? $data['id'] : '';
		$data['user_id'] = $valida_token['user_id'];
		$data['business_id'] = $valida_token['business_id'];
		$modules = $this->elearning_model->fetchAll($data);

		// SE RECORREN TODOS LOS CURSOS PARA OBTENER LOS NUMEROS DE INTENTOS Y MAXIMO DE CALIFICACIÓN
		// OJO ESTO SE PUEDE HACER POR MYSQL EN CASO QUE SEA MUY LENTO
		foreach ($modules as $key => $value) {
			$last_score 	= $this->elearning_score_model->getLastScore($valida_token['user_id'], $value["id"]);
			$last_score 	= (!empty($last_score)) ? $last_score[0]["score"] : "";

			$count_access 	= $this->elearningaccess_model->countAcccess($valida_token['user_id'], $value["id"]);
			$count_access 	= (!empty($count_access)) ? $count_access[0]["access"] : "";

			// PARÁMETRO QUE DEFINE SI PODEMOS HACER O NO LA EVALUACION FINAL
			$can_eval 		= true;

			// SI TU ULTIMA CALIFICACIÓN ES MAYOR A LA MINIMA REQUERIDO O YA LLEGASTE A NUMERO DE INTENTOS MAXIMOS YA NO SE PUEDE HACER LA EVALUACION
			if (($last_score >= $value["min_score"]) || ($count_access >= $value["max_try"])) {
				$can_eval = false;
			}

			// SE ANEXAN AL RESPONSE LOS PARAMTROS DE ULTIMA CALIFICACION, NUMERO DE INTENTOS Y SI PUEDE O NO HACER LA EVALUACION
			$modules[$key]["my_last_score"] = $last_score;
			//$modules[$key]["my_tries"] 		= $count_access;
			$modules[$key]["can_eval"] 		= $can_eval;
		}
		$this->general_mdl->writeLog("Listado de elearning usuario " . $valida_token["user_id"], "<info>");
		successResponse($modules, 'Cursos', $this);
	}

	// SERVICIO QUE SIMPLEMENTE VA REGISTRANDO EL ACCESO A UN CURSO
	public function newAcces()
	{

		$token 		= $this->input->post("token");
		// TPYE 0 ES DE ENTRADA TYPE 1 ES DE SALIDA
		$type 		= $this->input->post("type");
		$module_id 	= $this->input->post("module_id");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);

		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}

		$validaPost = $this->general_mdl->validapost(array('type', 'module_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		$entity = array(
			"type" 			=> $type,
			"user_id" 		=> $valida_token['user_id'],
			"modules_id"	=> $module_id,
			"fecha"			=> date('Y-m-d H:i:s'),
		);

		$this->elearningaccess_model->insert($entity);

		$this->general_mdl->writeLog("Registro de acceso a un curso usuario " . $valida_token["user_id"], "<info>");
		successResponse('', 'Acceso/termino registrado', $this);
	}

	// SERVICIO QUE VA REGISRANDO LAS CALIFICACIONES DE UNA EVALUACION DE UN CURSO DETERMINADO REALIZADO POR UN USUAIRO
	public function newScore()
	{

		$token 		= $this->input->post("token");
		$score 		= $this->input->post("score");
		$module_id 	= $this->input->post("module_id");
		$type = $this->input->post("type");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);

		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}

		$validaPost = $this->general_mdl->validapost(array('score', 'module_id', 'type'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		$entity = array(
			"score" 		=> $score,
			"user_id" 		=> $valida_token['user_id'],
			"module_id"		=> $module_id,
			"type" 			=> $type
		);

		$result = $this->elearning_score_model->insert($entity);
		if ($type === 'final_evaluation') {
			if ($result === 'no_score') {
				faildResponse('No has pasado la evaluación por favor vuelve a intentarlo.', $this);
				return;
			} else {
				successResponse('', 'Felicidades has pasado la evaluación.', $this);
			}
		}
		$this->general_mdl->writeLog("Registro de calificacion de elearning usuario " . $valida_token["user_id"], "<info>");
		successResponse('', 'Calificación almacenada', $this);
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 26/08/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Listado de categorias enviadas
	 ***********************************************************************/
	public function ListCategories()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$categories = $this->elearning_model->ListCategories($valida_token['business_id']);
		if ($categories) {
			$this->general_mdl->writeLog("Listado de categorias elearning usuario " . $valida_token["user_id"], "<info>");
			successResponse($categories, 'Listado de categorias de elearning', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener categorias elearning usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 26/08/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Listar subcategories en base a la categoria enviada
	 ***********************************************************************/
	public function ListSubcategories()
	{
		if ($this->input->post() == [])
			$_POST = json_decode(file_get_contents('php://input'), true);
		$validaPost = $this->general_mdl->validapost(array('category_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$areas = $this->elearning_model->ListSubcategories($valida_token['business_id'], $this->input->post('category_id'));
		if ($areas) {
			$this->general_mdl->writeLog("Listado de subcategorias elearning usuario " . $valida_token["user_id"], "<info>");
			successResponse($areas, 'Listado de subcategorias de elearning', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener subcategorias elearning usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para registrar un elearning nuevo
	 ***********************************************************************/
	public function SaveElearning()
	{
		if ($this->input->post() == [])
			$_POST = json_decode(file_get_contents('php://input'), true);
		$data = $this->input->post();
		if (isset($data['id'])) {
			$validaPost = $this->general_mdl->validapost(array('title', 'description', 'max_try', 'min_score'), $this->input->post());
		} else {
			$validaPost = $this->general_mdl->validapost(array('title', 'description', 'max_try', 'min_score', 'quiz_satisfaction_id', 'quiz_final_evaluation_id'), $this->input->post());
		}
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$data['business_id'] = $valida_token['business_id'];

		if (count($_FILES) > 0) {
			$config['upload_path'] = './uploads/business_' . $valida_token["business_id"] . '/elearnings/';
			$config['allowed_types'] = 'zip';

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('archivo')) {
				$error = array('error' => $this->upload->display_errors());
				echo json_encode($error);
				return;
			} else {
				$data_ = $this->upload->data();
				$data["trail_url"] = base_url() . "uploads/business_" . $valida_token['business_id'] . "/elearnings/" . $data_['raw_name'];; //
				$ruta = './uploads/business_' . $valida_token["business_id"] . '/elearnings/' . $data_['raw_name'];
				$this->extraerZip($ruta . ".zip", $ruta);
			}
		}
		unset($data["archivo"]);
		$elearning = $this->elearning_model->insert($data);
		if ($elearning) {
			if (isset($data['id'])) {
				$notification = 'La capacitación ' . $data['title'] . ' ha cambiado, entra y revisalo.';
			} else {
				$notification = 'Se agrego una nueva capacitación ' . $data['title'];
			}
			/***********************************************************************
			 *	Nota: Se obtiene los tokens existentes en la BD
			 ***********************************************************************/
			$tokens = $this->notification->ListUserNotification($valida_token['business_id']);
			if ($tokens) {
				$tokens_ = array();
				foreach ($tokens as $index => $value) {
					array_push($tokens_, $value['token']);
					/***********************************************************************
					 *	Autor: Mario Adrián Martínez Fernández   Fecha: 09/07/2020
					 *		   mario.martinez.f@hotmail.es
					 *	Nota: Guardarmos a los usuarios que se les enviara la notificacion.
					 ***********************************************************************/
					$data = array('title' => 'Capacitación', 'notification' => $notification, 'user_id' => $value['user_id'], 'service_id' => SERVICE_ELEARNING, 'user_create_id' => $valida_token['user_id']);
					$this->notification->RegisterNotification($data);
				}
				/***********************************************************************
				 *	Nota: Se envia notificacion a los multiples tokens
				 ***********************************************************************/
				$this->general_mdl->EnviarNotificacionPush($tokens_, $notification, 'Capacitación', SERVICE_ELEARNING);
			}
			$this->general_mdl->writeLog("Registro de elearning usuario " . $valida_token["user_id"], "<info>");
			successResponse(array(), 'El elearning se ha registrado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar elearning usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El elearning no se ha registrado correctamente', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 14/10/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para eliminar un curso
	 ***********************************************************************/
	public function DeleteElearning()
	{
		if ($this->input->post() == [])
			$_POST = json_decode(file_get_contents('php://input'), true);
		$data = $this->input->post();
		$validaPost = $this->general_mdl->validapost(array('id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$elearning = $this->elearning_model->update($data['id'], array('active' => 0));
		if ($elearning) {
			$this->general_mdl->writeLog("Eliminacion de elearning usuario " . $valida_token["user_id"] . " elearning " . $data["id"], "<info>");
			successResponse(array(), 'El elearning se ha eliminado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar elearning usuario " . $valida_token["user_id"] . " elearning " . $data["id"], "<warning>");
			faildResponse('El elearning no se ha eliminado correctamente', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtneer los quiz disponibles en elearning
	 ***********************************************************************/
	function QuizLibrary()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$data = $this->input->post();
		$data['business_id'] = $valida_token['business_id'];
		$quizLibrary = $this->elearning_model->QuizLibrary($data);
		if ($quizLibrary) {
			$this->general_mdl->writeLog("Listado de quiz elearning usuario " . $valida_token["user_id"], "<info>");
			successResponse($quizLibrary, 'Listado de catalogo de preguntas de elearning', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener quiz elearning usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros cuestionarios', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para registrar, actualizar y eleminar una categoria
	 ***********************************************************************/
	function SaveCategory()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$data = $this->input->post();
		if (!isset($data['active'])) {
			$validaPost = $this->general_mdl->validapost(array('category'), $this->input->post());
			if (!$validaPost['success']) {
				faildResponse($validaPost['msg'], $this);
				return;
			}
		}
		$data['business_id'] = $valida_token['business_id'];
		$result = $this->elearning_model->SaveCategory($data);
		if (isset($data['active'])) {
			$text_success = 'La categoría se ha eliminado correctamente';
			$text_error = 'La categoría no se ha eliminado correctamente';
		} else {
			if (isset($data['id'])) {
				$text_success = 'La categoría se ha actualizado correctamente';
				$text_error = 'La categoría no se ha actualizado correctamente';
			} else {
				$text_success = 'La categoría se ha creado correctamente';
				$text_error = 'La categoría no se ha creado correctamente';
			}
		}
		if ($result) {
			$this->general_mdl->writeLog("Registro de categoria elearning usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, $text_success, $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar categoria elearning usuario " . $valida_token["user_id"], "<warning>");
			faildResponse($text_error, $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para registrar, actualizar y eliminar una subcategoria
	 ***********************************************************************/
	function SaveSubcategory()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$data = $this->input->post();
		if (!isset($data['active'])) {
			$validaPost = $this->general_mdl->validapost(array('subcategory', 'category_id'), $this->input->post());
			if (!$validaPost['success']) {
				faildResponse($validaPost['msg'], $this);
				return;
			}
		}
		//$data['business_id'] = $valida_token['business_id'];
		$result = $this->elearning_model->SaveSubcategory($data);
		if (isset($data['active'])) {
			$text_success = 'La subcategoría se ha eliminado correctamente';
			$text_error = 'La subcategoría no se ha eliminado correctamente';
		} else {
			if (isset($data['id'])) {
				$text_success = 'La subcategoría se ha actualizado correctamente';
				$text_error = 'La subcategoría no se ha actualizado correctamente';
			} else {
				$text_success = 'La subcategoría se ha creado correctamente';
				$text_error = 'La subcategoría no se ha creado correctamente';
			}
		}
		if ($result) {
			$this->general_mdl->writeLog("Registro de subcategoria elearning usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, $text_success, $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar subcategoria elearning usuario " . $valida_token["user_id"], "<warning>");
			faildResponse($text_error, $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener el detalle del reporte de elearning
	 ***********************************************************************/
	function ElearningDetailUsers()
	{
		if ($this->input->post() == [])
			$_POST = json_decode(file_get_contents('php://input'), true);
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('elearning_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['business_id'] = $valida_token['business_id'];
		$quizLibrary = $this->elearning_model->ElearningDetailUsers($data);
		if ($quizLibrary) {
			$this->general_mdl->writeLog("Consulta detalle de reporte elearning usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("elearning_id"), "<info>");
			successResponse($quizLibrary, 'Listado para detalle de reporte', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener detalle de reporte elearning usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("elearning_id"), "<warning>");
			faildResponse('No existen registros', $this);
		}
	}

	public function extraerZip($ruta, $rutaSalida)
	{
		$zip = new ZipArchive;
		$rutaArchivo = $ruta;
		$directorioSalida = $rutaSalida;
		if (!$zip->open($rutaArchivo)) {
			exit("No se puede abrir el archivo $rutaArchivo");
		}
		if (!file_exists($directorioSalida)) {
			mkdir($directorioSalida, 0777, true);
		}
		$zip->extractTo($directorioSalida);
		$zip->close();
	}

	public function SetVisto()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		// echo json_encode($valida_token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$data = [];
		$data["elearning_id"] = $this->input->post("id");
		$data["numero_clicks"] = $this->input->post("numero_clicks");
		$data['user_id'] = $valida_token['user_id'];
		$result = $this->elearning_model->SetVisto($data);
		// $this->capacitacion->markCompleted($data["user_id"],$data["library_element_id"],"library_elements_");
		// $this->general_mdl->agregar_recurso_visto($data["user_id"]);
		$this->general_mdl->writeLog("Registro de visto de elearning usuario " . $valida_token["user_id"], "<info>");
		successResponse($result, 'Se ha establecido como visto el elearning', $this);
	}

	public function obtener_elearnings_capacitaciones(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		// echo json_encode($valida_token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$result = $this->elearning_model->obtener_elearnings_capacitaciones($valida_token["business_id"]);
		$this->general_mdl->writeLog("Elearning para capacitacion obligatoria usuario " . $valida_token["user_id"], "<info>");
		successResponse($result, 'Elearnings para capacitacion obligatoria', $this);
	}
}
