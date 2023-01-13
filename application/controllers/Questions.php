<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Questions extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("America/Mexico_City");
		$this->load->model('question_mdl', 'questions');
		$this->load->model('configuraciones_mdl', 'configuraciones');
		$this->load->model('versiones_mdl', 'versiones');
		$this->load->model('admin_mdl', 'admin');
		$this->load->model("ambiente_laboral_mdl", "ambiente_laboral");

		$headers = $this->input->request_headers();
		if (isset($headers['lenguage'])) {
			$this->lang->load('message', 'es');
			$this->defaultLang = 'es';
		} else {
			$this->lang->load('message', 'en');
			$this->defaultLang = 'en';
		}
	}

	/**
	 * POST
	 * Lista de usuarios por cuestionaro
	 * @param Id Int Recibe el id del cuestionario para devolver la lista de usuarios que contestaron ese cuestionario
	 */
	function listUserAnsweredQuestion()
	{
		if ($this->input->post() == []) {
			$_POST = json_decode(file_get_contents('php://input'), true);
		}
		//print_r($this->input->post());
		$post_data = $this->input->post();

		// Validación de token
		// $token = $this->input->post("token");
		// $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		// if(!$valida_token){
		// 	faildResponse( $this->lang->line('token_error_msg') ,$this);
		// 	return;
		// }

		$quiz_id = $post_data['id'];

		$result = $this->questions->getListUsersByQuiz($quiz_id);
		$this->general_mdl->writeLog("Lista de usuario que contestaron el cuestionario " . $post_data["id"], "<info>");
		successResponse($result, '', $this);
	}

	function getUsersbyIdQuiz()
	{
		if ($this->input->post() == []) {
			$_POST = json_decode(file_get_contents('php://input'), true);
		}
		//print_r($this->input->post());
		$data = $this->input->post();

		$result = $this->questions->getUsersbyIdQuiz($data);
		//$this->general_mdl->writeLog("Lista de usuario que contestaron el cuestionario " . $data, "<info>");
		//successResponse($result, '', $this);
		successResponse($result, 'Lista de usuario que contestaron el cuestionario', $this);
	}


	/**
	 * POST
	 * Lista de usuarios por cuestionaro
	 * @param Id Int Recibe el id del cuestionario para devolver la lista de usuarios que contestaron ese cuestionario
	 */
	function listQuizPerUser()
	{

		if ($this->input->post() == []) {
			$_POST = json_decode(file_get_contents('php://input'), true);
		}

		$post_data = $this->input->post();

		$quiz_id = $post_data['id'];
		$user_id = $post_data['user'];

		$result = $this->questions->listQuizPerUser($quiz_id, $user_id);

		$cuestionarios = [];
		$fecha = '';
		if ($result) {
			$preguntas = [];
			$prev_id = 9000000;
			foreach ($result as $key => $answer) {
				$respuesta = $answer['answer'];
				$respuesta_str = "";
				if (
					$answer['type_id'] == 1 ||
					$answer['type_id'] == 2 ||
					$answer['type_id'] == 3 ||
					$answer['type_id'] == 5 ||
					$answer['type_id'] == 8 ||
					$answer['type_id'] == 14
				) {
					$respuestas = explode(",", $respuesta);
					foreach ($respuestas as $key => $_respuesta) {
						if (is_numeric($_respuesta)) {
							$request_answer = $this->questions->answerDesc($_respuesta);
							if ($request_answer) {
								$respuesta_str .= $request_answer[0]['answer'];
							}
						}
					}
				} else {
					$respuesta_str = $answer['answer'];
				}

				$answer['answer'] = $respuesta_str;

				if ((int) $answer['question_id'] < $prev_id) {
					array_push($preguntas, $answer);
					$fecha = $answer['date'];
				} else {
					array_push($cuestionarios, array("fecha" => $answer['date'], "preguntas" => $preguntas));
					$preguntas = [];
					array_push($preguntas, $answer);
				}
				$prev_id = (int) $answer['question_id'];
			}
			array_push($cuestionarios, array("fecha" => $fecha, "preguntas" => $preguntas));
		}

		$this->general_mdl->writeLog("Listado de cuestionarios por usuario " . $user_id, "<info>");
		successResponse($cuestionarios, '', $this);
	}

	function reporte_cuestionario()
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

		$data = $this->input->post();
		$data["business_id"] = $valida_token["business_id"];
		$result = $this->questions->reporte_cuestionario($data);

		successResponse($result, 'Reporte de cuestionario', $this);
	}

	/**
	 * Descargamos el cuestionario en formato CSV
	 */
	public function downloadQuizCSV($id, $user)
	{


		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=\"Cuestionario" . ".csv\"");
		header("Pragma: no-cache");
		header("Expires: 0");

		$handle = fopen('php://output', 'w');
		fputcsv($handle, array("Pregunta", "Respuesta"));

		$cuestionarios = [];
		$fecha = '';

		$result = $this->questions->listQuizPerUser($id, $user);

		if ($result) {
			$preguntas = [];
			$prev_id = 9000000;
			foreach ($result as $key => $answer) {
				$respuesta = $answer['answer'];
				$respuesta_str = "";
				if (
					$answer['type_id'] == 1 ||
					$answer['type_id'] == 2 ||
					$answer['type_id'] == 3 ||
					$answer['type_id'] == 5 ||
					$answer['type_id'] == 8 ||
					$answer['type_id'] == 14
				) {
					$respuestas = explode(",", $respuesta);
					foreach ($respuestas as $key => $_respuesta) {
						if (is_numeric($_respuesta)) {
							$request_answer = $this->questions->answerDesc($_respuesta);
							if ($request_answer) {
								$respuesta_str .= $request_answer[0]['answer'];
							}
						}
					}
				} else {
					$respuesta_str = $answer['answer'];
				}

				$answer['answer'] = $respuesta_str;

				if ((int) $answer['question_id'] < $prev_id) {
					if ($prev_id == 9000000) {
						fputcsv($handle, array("Cuestionario contestado:", $answer['date']));
					}

					if (strpos($answer['answer'], ".jpeg") >= 1 || strpos($answer['answer'], ".png") >= 1) {
						$answer['answer'] = "http://kreativeco.com/nuup/uploads/business_" . $answer['business_id'] . "/preguntas/" . $answer['answer'];
					}

					fputcsv($handle, array($answer['question'], $answer['answer']));
				} else {
					fputcsv($handle, array("Cuestionario contestado:", $answer['date']));
					if (strpos($answer['answer'], ".jpeg") >= 1 || strpos($answer['answer'], ".png") >= 1) {
						$answer['answer'] = "http://kreativeco.com/nuup/uploads/business_" . $answer['business_id'] . "/preguntas/" . $answer['answer'];
					}
					fputcsv($handle, array($answer['question'], $answer['answer']));
				}
				$prev_id = (int) $answer['question_id'];
			}
		}

		// fputcsv($handle, $cuestionarios);
		$this->general_mdl->writeLog("Descarga de respuestas de cuestionarios en csv usuario " . $user . " cuestionario " . $id, "<info>");
		fclose($handle);
		exit;
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los quiz en base a la configuracion
	 ***********************************************************************/
	function ListQuiz()
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
		$data = $this->input->post();
		if (isset($data['category_id'])) {
			if ($data['category_id'] != 1 && $data['category_id'] != '') {
				$validaPost = $this->general_mdl->validapost(array('connection_id'), $data);
				if (!$validaPost['success']) {
					faildResponse($validaPost['msg'], $this);
					return;
				}
			}
		}
		$data['business_id'] = $valida_token['business_id'];
		$data['user_id'] = $valida_token["user_id"];
		$result = $this->questions->ListQuiz($data);
		if ($result) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "cuanto he aprendido");
			$this->general_mdl->writeLog("Listado de cuestionarios usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Lista de quiz de preguntas', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener cuestionarios usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen quiz registrados', $this);
		}
	}

	function ListQuizAdmin()
	{

		if ($this->input->post() == []) {
			$_POST = json_decode(file_get_contents('php://input'), true);
			$data = null;
		}else{
			$data = $this->input->post();
		}
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		if (isset($data['category_id'])) {
			if ($data['category_id'] != 1 && $data['category_id'] != 5 && $data['category_id'] != '') {
				$validaPost = $this->general_mdl->validapost(array('connection_id'), $data);
				if (!$validaPost['success']) {
					faildResponse($validaPost['msg'], $this);
					return;
				}
			}
		}
		$data['business_id'] = $valida_token['business_id'];
		$data['user_id'] = $valida_token["user_id"];
		$result = $this->questions->ListQuizAdmin($data);
		if ($result) {
			$this->general_mdl->writeLog("Listado de cuestionarios usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Lista de quiz de preguntas', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener cuestionarios usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen quiz registrados', $this);
		}
	}

	/* opbtener quiz id */
	function QuizById()
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
		$data = $this->input->post();
		
		$data['business_id'] = $valida_token['business_id'];
		$data['user_id'] = $valida_token["user_id"];
		$result = $this->questions->QuizById($data);
		if ($result) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "cuanto he aprendido");
			$this->general_mdl->writeLog("Listado de cuestionarios usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Lista de quiz de preguntas', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener cuestionarios usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen quiz registrados', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener las categorias de los quiz
	 ***********************************************************************/
	function ListCategories()
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
		$result = $this->questions->ListCategories();
		if ($result) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "cuanto he aprendido");
			$this->general_mdl->writeLog("Listado de categorias de cuestionarios usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Lista de categorias de quiz', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener categorias de cuestionarios usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen categorias registradas', $this);
		}
	}

	function ListTypesQuestion()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$result = $this->questions->listTypesQuestion();
		if ($result) {
			$this->general_mdl->writeLog("Listado de tipos de preguntas usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Lista de tipos de preguntas', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener tipos de preguntas usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen tipos registrados', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener las preguntas de un quiz, cada pregunta
	 *          retornara con sus respectivas respuesatas.
	 ***********************************************************************/
	function ListQuestionsQuiz()
	{

		if ($this->input->post() == []) {
			$_POST = json_decode(file_get_contents('php://input'), true);
		}
		$validaPost = $this->general_mdl->validapost(array('quiz_id'), $this->input->post());
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
		$data = $this->input->post();
		$data['question_id'] = ($this->input->post('question_id')) ? $this->input->post('question_id') : '';
		$data['business_id'] = $valida_token['business_id'];
		$result = $this->questions->ListQuestionsQuiz($data);
		$arr = array("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&iquest;");
		if($result){
            if($data['question_id'] == ''){
                for ($i = 0; $i < count($result); $i++) {
                    $result[$i]["question"] = str_replace(array('á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú','¿'),$arr, $result[$i]["question"]);
                    if(isset($result[$i]["answers"]) && $result[$i]["answers"]){
                        for($j = 0; $j < count($result[$i]["answers"]); $j++){
                            $result[$i]["answers"][$j]["answer"] = str_replace(array('á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú','¿'),$arr, $result[$i]["answers"][$j]["answer"]);
                        }
                    }
                }
            }
            
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "cuanto he aprendido");
			$this->general_mdl->writeLog("Listado de preguntas de cuestionario usuario " . $valida_token["user_id"] . " cuestionario " . $data["quiz_id"], "<info>");
			successResponse($result, 'Lista de preguntas de quiz', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener preguntas de cuestionario usuario " . $valida_token["user_id"] . " cuestionario " . $data["quiz_id"], "<warning>");
			faildResponse('No existen preguntas registradas', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion registrar una respuesta.
	 ***********************************************************************/
	// el color que estaba
	function SaveAnswerUser()
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
		$data = $this->input->post();
		if ($data['type_id'] == TIPO_PREGUNTA_DIBUJO || $data['type_id'] == TIPO_PREGUNTA_SUBIR_IMAGEN || $data['type_id'] == TIPO_PREGUNTA_TACHE_PALOMA) {
			$archivos = $this->general_mdl->GuardarArchivos($_FILES, 'preguntas', $valida_token['business_id']);
			//Si la subida de archivos fue exitosa
			if ($archivos['success']) {
				//Si se subió el correctamente el archivo deseado ("answer"), guardamos el nombre asignado
				if (isset($archivos['success_files']['answer'])) {
					$data['answer'] = $archivos['success_files']['answer'];
				} else {
					//No se subió el archivo deseado, retornar error
					faildResponse('Por favor selecciona una imagen', $this);
					return;
				}
			} else {
				//si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
				faildResponse($archivos['msg'], $this);
				return;
			}
		}
		$validaPost = $this->general_mdl->validapost(array('answer', 'question_id'), $data);
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data['user_id'] = $valida_token['user_id'];
		$result = $this->questions->SaveAnswerUser($data);
		if ($result) {
			$mensaje = '';
			$respuesta = false;
			$respuesta = $this->questions->comprobarCalificacion($data["question_id"], $valida_token["user_id"]);
			if ($respuesta != false) {
				$mensaje = $respuesta;
			}
			$msg = "";
			// $texto = "Has contestado correctamente";
			// } else {
			// 	$correct = 0;
			// 	$texto = "Has contestado incorrectamente. La respuesta correcta es " . $result;
			if ($result["is_correct"] == true) {
				$msg = "Has contestado correctamente";
			} else {

				$msg = "Has contestado incorrectamente. La respuesta correcta es " . $result["mensaje"];
			}
			//aqui validar que si el usuario ya termino de contestar el quiz entonces agregarlo como recurso visto
			//va de la mano con la verificacion de quiz respondido correctamente
			unset($result["mensaje"]);
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "cuanto he aprendido");
			$this->general_mdl->writeLog("Registro de respuesta usuario " . $valida_token["user_id"] . " pregunta " . $data["question_id"], "<info>");
			successResponse($result, $msg, $this, ["mensaje" => $mensaje]);
		} else {
			$this->general_mdl->writeLog("Error al guardar respuesta usuario " . $valida_token["user_id"] . " pregunta " . $data["question_id"], "<warning>");
			faildResponse('No existen preguntas registradas', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 04/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener el detalle la configuración de preguntas
	 ***********************************************************************/
	function ConfigurationQuestions()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$version = $this->input->post("version");
		$version = isset($version) ? $version : 0;
		$tipo = $this->input->post("tipo");
		$tipo = isset($tipo) ? $tipo : false;
		$result = $this->questions->ConfigurationQuestions($valida_token['business_id']);
		$configuraciones = ["0" => []];
		$configuraciones[0]["app_config"] = $this->configuraciones->ObtenerConfiguraciones($valida_token['business_id']);
		$configuraciones[0]["app_config"]["blockscreen"] = $this->configuraciones->ObtenerBloquearCaptura($valida_token["business_id"]);
		$configuraciones[0]["app_config"]["actualizar"] = $this->versiones->comprobarVersion($version, $tipo);
		$configuraciones[0]["app_config"]["aviso_aceptado"] = $this->admin->AvisoAceptado($valida_token["user_id"]);
		$configuraciones[0]["app_config"]["terminos_aceptados"] = $this->admin->TerminosAceptados($valida_token["user_id"]);
		if ($result || $configuraciones) {
			$this->general_mdl->writeLog("Consulta detalle configuracion preguntas usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Lista de configuración preguntas', $this, $configuraciones);
		} else {
			$this->general_mdl->writeLog("Error al obtener detalle configuracion preguntas usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existe configuracion registrada', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 15/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: funcion para guardar las respuestas de las preguntas de un
	 * 			curso.
	 ***********************************************************************/
	function saveAnswerElearning()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('questions', 'tried', 'quiz_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['user_id'] = $valida_token['user_id'];
		$result = $this->questions->saveAnswerElearning($data);
		if ($result['success']) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "cuanto he aprendido");
			if (isset($result['score'])) {
				successResponse(array('score' => $result['score']), $result['msg'], $this);
			} else {
				$this->general_mdl->writeLog("Registro respuestas elearning usuario " . $valida_token["user_id"] . " cuestionario " . $data["quiz_id"], "<info>");
				successResponse(array(), $result['msg'], $this);
			}
		} else {
			$this->general_mdl->writeLog("Error al guardar respuestas elearning usuario " . $valida_token["user_id"] . " cuestionarios " . $data["quiz_id"], "<warning>");
			faildResponse($result['msg'], $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 28/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para guardar un quiz nuevo.
	 ***********************************************************************/
	function SaveQuiz()
	{
		if ($this->input->post() == [])
			$_POST = json_decode(file_get_contents('php://input'), true);
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$data = $this->input->post();
		if (!isset($data['active'])) {
			$validaPost = $this->general_mdl->validapost(array('name', 'category_id'), $this->input->post());
			if (!$validaPost['success']) {
				faildResponse($validaPost['msg'], $this);
				return;
			}
		}
		$data['business_id'] = $valida_token['business_id'];
		//$data['first_question_is_correct'] == 1 || $data['first_question_is_correct'] == "1" ? $data['first_question_is_correct'] = true : $data['first_question_is_correct'] == null;
		/* eliminar token */
		unset($data["token"]);
		$usuarios = [];
		$grupos = [];
		if ($this->input->post("usuarios") != '' && $this->input->post("usuarios") != null) {
			$usuarios = json_decode($this->input->post("usuarios"), true);
			unset($data["usuarios"]);
		}
		if ($this->input->post("grupos") != '' && $this->input->post("grupos") != null) {
			$grupos = json_decode($this->input->post("grupos"), true);
			unset($data["grupos"]);
		}

		$result = $this->questions->SaveQuiz($data);
		if (isset($data['active'])) {
			$text_success = 'El catalogo se ha eliminado correctamente';
			$text_error = 'El catalogo no se ha eliminado correctamente';
		} else {
			if (isset($data['id'])) {
				$text_success = 'El catalogo se ha actualizado correctamente';
				$text_error = 'El catalogo no se ha actualizado correctamente';
			} else {
				$text_success = 'El catalogo se ha creado correctamente';
				$text_error = 'El catalogo no se ha creado correctamente';
			}
		}
		/* asociar a grupos y usuarios */
		if ($result) {
			if (!isset($data['id'])) {
				$this->questions->agregarUsuarios($usuarios, $result);
				$this->questions->agregarGrupos($grupos, $result);
			}
			$this->general_mdl->writeLog("Registro de nuevo cuestionario usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, $text_success, $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar cuestionario usuario " . $valida_token["user_id"], "<warning>");
			faildResponse($text_error, $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 28/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para registrar, actualizar y eliminar una pregunta
	 ***********************************************************************/
	function SaveQuestion()
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
		$data = $this->input->post();
		if (!isset($data['active'])) {
			$validaPost = $this->general_mdl->validapost(array('question', 'quiz_id', 'type_id', 'points'), $this->input->post());
			if (!$validaPost['success']) {
				faildResponse($validaPost['msg'], $this);
				return;
			}
		}
		//$data['business_id'] = $valida_token['business_id'];
		$result = $this->questions->SaveQuestion($data);

		if (isset($data['active'])) {
			$text_success = 'La pregunta se ha eliminado correctamente';
			$text_error = 'La pregunta no se ha eliminado correctamente';
		} else {
			if (isset($data['id'])) {
				$text_success = 'La pregunta se ha actualizado correctamente';
				$text_error = 'La pregunta no se ha actualizado correctamente';
			} else {
				$text_success = 'La pregunta se ha creado correctamente';
				$text_error = 'La pregunta no se ha creado correctamente';
			}
		}

		// $usuarios = json_decode($this->input->post("usuarios"), true);
		// $grupos = json_decode($this->input->post("grupos"), true);

		if ($result) {
			// $this->library->agregarUsuarios($result, $usuarios);
			// $this->library->agregarGrupos($result, $grupos);
			$this->general_mdl->writeLog("Registro de nueva pregunta usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, $text_success, $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar pregunta usuario " . $valida_token["user_id"], "<warning>");
			faildResponse($text_error, $this);
		}
	}

	function ListQuestionsAmbienteLaboral($business_id = null, $user_id = null, $quiz_id = null)
	{

		$data = $this->input->post();

		$data['question_id'] = ($this->input->post('question_id')) ? $this->input->post('question_id') : '';
		$data['quiz_id'] = $quiz_id != null ? $quiz_id : 34; //aqui en lugar de poner el 34 directo obtener el que esta activo
		$data['business_id'] = $business_id;
		$result = $this->questions->ListQuestionsQuiz($data);
		// $primeraVez = $this->questions->validaPrimerIngreso($user_id);
		$result_ = [];
		if (!$result) {
			$result = [];
		}
		for ($i = 0; $i < count($result); $i++) {
			$result[$i]["dias"] = $this->ambiente_laboral->obtener_dias_pregunta($result[$i]["id"]);
			if (!$this->ambiente_laboral->comprobar_dia($result[$i]["id"])) {
				array_push($result_, $result[$i]);
			}
		}
		// $dia = date("N");
		// $servicio_contratado = $this->questions->VerificarServicioContratado($business_id);
		// $en_rango = $this->ambiente_laboral->comprobar_rango_fechas();
		// $contestado = $this->questions->ComprobarAmbienteLaboralContestado($user_id);
		// if ($result && $contestado == false) {
		// if ($primeraVez) {
		return ["quiz_ambiente_laboral" => $result, "hacer_quiz" => 1];
		// } else {
		// 	return ["quiz_ambiente_laboral" => $result, "hacer_quiz" => 0];
		// }



		//return ["quiz_ambiente_laboral" => $result, "hacer_quiz" => 1];
		// } else {
		// 	return ["hacer_quiz" => 0];
		// }
	}

	function obtener_quiz_a_l()
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
		$quiz_id = $this->input->post("quiz_id");
		$list = []; //$this->notification->comprobarNotificaciones($valida_token['user_id'], false);
		$quiz = $this->ListQuestionsAmbienteLaboral($valida_token["business_id"], $valida_token["user_id"], $quiz_id);
		// $list["extras"] = $quiz;
		$arr = [];
		if (count($quiz) > 0) {
			$arr = $quiz;
		}
		if ($list == false)
			$list = [];

		array_push($list, ["extra" => $quiz]);
		if ($list) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "home");
			$this->general_mdl->writeLog("Preguntas de ambiente laboral usuario " . $valida_token["user_id"], "<info>");
			successResponse([$list], 'Tiene notificaciones pendientes', $this, $quiz);
		} else {
			$this->general_mdl->writeLog("Error al comprobar preguntas de ambiente laboral usuario " . $valida_token["user_id"], "<warning>");
			successResponse(array(), 'No existen preguntas de ambiente laboral', $this, $quiz);
		}
	}

	function SaveQuestionAL()
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
		$data = $this->input->post();
		if (!isset($data['active'])) {
			$validaPost = $this->general_mdl->validapost(array('question', 'quiz_id', 'type_id', 'points'), $this->input->post());
			if (!$validaPost['success']) {
				faildResponse($validaPost['msg'], $this);
				return;
			}
		}
		//$data['business_id'] = $valida_token['business_id'];
		$result = $this->questions->SaveQuestionAL($data);
		if (isset($data['active'])) {
			$text_success = 'La pregunta se ha eliminado correctamente';
			$text_error = 'La pregunta no se ha eliminado correctamente';
		} else {
			if (isset($data['id'])) {
				$text_success = 'La pregunta se ha actualizado correctamente';
				$text_error = 'La pregunta no se ha actualizado correctamente';
			} else {
				$text_success = 'La pregunta se ha creado correctamente';
				$text_error = 'La pregunta no se ha creado correctamente';
			}
		}
		if ($result) {
			$this->general_mdl->writeLog("Registro de nueva pregunta usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, $text_success, $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar pregunta usuario " . $valida_token["user_id"], "<warning>");
			faildResponse($text_error, $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 28/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener el detalle de un respuesta
	 ***********************************************************************/
	function AnswerDetail()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$data = $this->input->post();
		$validaPost = $this->general_mdl->validapost(array('id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$params = array(
			'answer_id' => $data['id'],
			'business_id' => $valida_token['business_id']
		);
		$result = $this->questions->AnswerDetail($params);
		if ($result) {
			$this->general_mdl->writeLog("Consulta detalle de respuesta usuario " . $valida_token["user_id"] . " respuesta " . $params["answer_id"], "<info>");
			successResponse($result, 'Lista de detalle de respuesta', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener detalle respuesta usuario " . $valida_token["user_id"] . " preguntas " . $params["answer_id"], "<warning>");
			faildResponse('No existe respuesta registrada', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 28/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para registrar un respuesta de una pregunta en
	 * 			especifico
	 ***********************************************************************/
	function SaveAnswer()
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
		$data = $this->input->post();
		$validaPost = $this->general_mdl->validapost(array('question_id', 'correct', 'type_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		/***********************************************************************
		 *	Autor: Mario Adrián Martínez Fernández   Fecha: 28/09/2020
		 *		   mario.martinez.f@hotmail.es
		 *	Nota: Validamos que tipo de respuesta viene en base al tipo de pregunta
		 * 			si es algun tipo donde se tenga que subir imagenes se agrega
		 * 			la imagen antes de enviar a guardar la informacion.
		 ***********************************************************************/
		if (
			$data['type_id'] == TIPO_PREGUNTA_MULTIPLE_IMAGEN ||
			$data['type_id'] == TIPO_PREGUNTA_UNICA_IMAGEN ||
			$data['type_id'] == TIPO_PREGUNTA_TACHE_PALOMA ||
			$data['type_id'] == TIPO_PREGUNTA_DIBUJO
		) {
			$archivos = $this->general_mdl->GuardarArchivos($_FILES, 'preguntas', $valida_token['business_id']);
			//Si la subida de archivos fue exitosa
			if ($archivos['success']) {
				//Si se subió el correctamente el archivo deseado ("answer"), guardamos el nombre asignado
				if (isset($archivos['success_files']['answer'])) {
					$data['answer'] = $archivos['success_files']['answer'];
				} else {
					if (!isset($data['id'])) {
						//No se subió el archivo deseado, retornar error
						faildResponse('Por favor selecciona una imagen', $this);
						return;
					} else {
						unset($data['answer']);
					}
				}
			} else {
				//si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
				faildResponse($archivos['msg'], $this);
				return;
			}
		}

		unset($data['type_id']);
		$result = $this->questions->SaveAnswer($data);
		if (isset($data['active'])) {
			$text_success = 'La respuesta se ha eliminado correctamente';
			$text_error = 'La respuesta no se ha eliminado correctamente';
		} else {
			if (isset($data['id'])) {
				$text_success = 'La respuesta se ha actualizado correctamente';
				$text_error = 'La respuesta no se ha actualizado correctamente';
			} else {
				$text_success = 'La respuesta se ha creado correctamente';
				$text_error = 'La respuesta no se ha creado correctamente';
			}
		}
		if ($result) {
			$this->general_mdl->writeLog("Registro de respuesta usuario " . $valida_token["user_id"] . " pregunta " . $data["question_id"], "<info>");
			successResponse($result, $text_success, $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar respuesta usuario " . $valida_token["user_id"] . " pregunta " . $data["question_id"], "<warning>");
			faildResponse($text_error, $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 12/22/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener las respuetas de los usuarios en base
	 * 			al id del cuestionario.
	 ***********************************************************************/
	function ListAnswerUsers()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse('El token no es valido', $this);
			return;
		}
		$data = $this->input->post();
		$data['business_id'] = $valida_token['business_id'];
		$result = $this->questions->ListAnswerUsers($data);
		if ($result) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "cuanto he aprendido");
			$this->general_mdl->writeLog("Listado de respuestas de usuarios usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Lista respuestas de usuarios', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener respuestas de usuarios usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existe respuestas de usuarios', $this);
		}
	}

	function eliminar_respuesta()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse('El token no es valido', $this);
			return;
		}
		$id_respuesta = $this->input->post("id_respuesta");
		$result = $this->questions->eliminar_respuesta($id_respuesta);
		if ($result) {
			$this->general_mdl->writeLog("Eliminacion de respuesta usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Respuesta eliminada', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliinar respuesta usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('Error al eliminar respuesta', $this);
		}
	}

	function agregarUsuarios()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse('El token no es valido', $this);
			return;
		}
		$quiz_id = $this->input->post("quiz_id");
		$usuarios = $this->input->post("usuarios");
		$result = $this->questions->agregarUsuarios($usuarios, $quiz_id);
		if ($result) {
			$this->general_mdl->writeLog("Alta de usuarios en cuestionarios usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Usuarios agregados', $this);
		} else {
			$this->general_mdl->writeLog("Error al agregar usuarios en cuestionarios usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('Error al agregar usuarios', $this);
		}
	}

	function agregarUsuario()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse('El token no es valido', $this);
			return;
		}
		$quiz_id = $this->input->post("quiz_id");
		$user_id = $this->input->post("user_id");
		$usuario = [];
		$usuario["quiz_id"] = $quiz_id;
		$usuario["user_id"] = $user_id;
		$result = $this->questions->agregarUsuario($usuario);
		if ($result) {
			$this->general_mdl->writeLog("Alta de usuario en cuestionarios usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Usuario agregado', $this);
		} else {
			$this->general_mdl->writeLog("Error al agregar usuario en cuestionarios usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('Error al agregar usuario', $this);
		}
	}

	function eliminarUsuario()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse('El token no es valido', $this);
			return;
		}
		$user_id = $this->input->post("user_id");
		$quiz_id = $this->input->post("quiz_id");
		$result = $this->questions->eliminarUsuario($user_id, $quiz_id);
		if ($result) {
			$this->general_mdl->writeLog("Eliminacion de usuario en cuestionarios usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Usuarios eliminado', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar usuario en cuestionarios usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('Error al eliminar usuario', $this);
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
		$quiz_id = $this->input->post("quiz_id");
		$grupos = $this->input->post("grupos");
		$result = $this->questions->agregarGrupos($grupos, $quiz_id);
		if ($result) {
			$this->general_mdl->writeLog("Alta de grupos en cuestionarios usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Grupos agregados', $this);
		} else {
			$this->general_mdl->writeLog("Error al agregar grupos en cuestionarios usuario " . $valida_token["user_id"], "<warning>");
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
		$quiz_id = $this->input->post("quiz_id");
		$group_id = $this->input->post("group_id");
		$grupo = [];
		$grupo["quiz_id"] = $quiz_id;
		$grupo["group_id"] = $group_id;
		$result = $this->questions->agregarGrupo($grupo);
		if ($result) {
			$this->general_mdl->writeLog("Alta de grupo en cuestionarios usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Grupo agregado', $this);
		} else {
			$this->general_mdl->writeLog("Error al agregar grupo en cuestionarios usuario " . $valida_token["user_id"], "<warning>");
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
		$quiz_id = $this->input->post("quiz_id");
		$result = $this->questions->eliminarGrupo($group_id, $quiz_id);
		if ($result) {
			$this->general_mdl->writeLog("Eliminacion de grupo en cuestionarios usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Grupo eliminado', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar grupo en cuestionarios usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('Error al eliminar grupo', $this);
		}
	}

	public function obtener_quiz_capacitaciones()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		// echo json_encode($valida_token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$result = $this->questions->obtener_quiz_capacitacion($valida_token["business_id"]);
		$this->general_mdl->writeLog("Quiz para capacitacion obligatoria usuario " . $valida_token["user_id"], "<info>");
		successResponse($result, 'Quiz para capacitacion obligatoria', $this);
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
        $data["quiz_id"] = $this->input->post("id");
        $data['user_id'] = $valida_token['user_id'];
        $result = $this->questions->SetVisto($data);
        $this->general_mdl->writeLog("Registro de visto en cuestionario usuario " . $valida_token["user_id"] . " cuestionario " . $data["quiz_id"], "<info>");
        successResponse($result, 'Se ha establecido como visto el cuestionario', $this);
    }

	/***********************************************************************
	 *	Autor: Francisco Avalos   Fecha: 17/11/2022
	 *	Nota: Funcion para retornar evaluaciones realizadas a usuarios
	 ***********************************************************************/
	public function getEvaluacionesByUsuario()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = [];
        $data["user_id_evaluado"] = $this->input->post("user_id_evaluado");
        $data['user_id'] = $valida_token['user_id'];
        $result = $this->questions->getEvaluacionesByUsuario($data);
        $this->general_mdl->writeLog("Registro de evaluaciones realizadas por el usuario " . $valida_token["user_id"] . " del usuario user_id_evaluado " . $data["user_id_evaluado"], "<info>");
        successResponse($result, 'Lista de evaluaciones', $this);
    }
	
}
