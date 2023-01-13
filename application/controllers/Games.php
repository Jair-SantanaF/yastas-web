<?php
class Games extends CI_Controller
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
		$this->load->model('games_mdl', 'games');
		$this->load->model('snake_stairs_mdl', "snake");
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para guardar un quiz
	 ***********************************************************************/
	public function SaveProductsQuiz()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('description', 'points'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		$image = '';
		$archivos = $this->general_mdl->GuardarArchivos($_FILES, $ruta = 'games/products', $valida_token['business_id']);
		//Si la subida de archivos fue exitosa
		if ($archivos['success']) {
			//Si se subió el correctamente el archivo deseado ("image"), guardamos el nombre asignado
			if (isset($archivos['success_files']['image'])) {
				$image = $archivos['success_files']['image'];
			}
		} else {
			//si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
			faildResponse($archivos['msg'], $this);
			return;
		}

		//Se procede a guardar el quiz
		$data = array(
			'business_id' => $valida_token['business_id'],
			'description' => $this->input->post("description"),
			'points' => $this->input->post("points"),
			'image' => $image
		);

		$id = $this->games->SaveProductsQuiz($data);

		//Se procede a guardar el paso 1
		$paso1 = array('num_step' => 1, 'option_description' => "Paso 1", 'quiz_id' => $id, 'business_id' => $valida_token['business_id']);
		$this->games->SaveProductsStep($paso1);
		//Se procede a guardar el paso 2
		$paso1 = array('num_step' => 2, 'option_description' => "Paso 2", 'quiz_id' => $id, 'business_id' => $valida_token['business_id']);
		$this->games->SaveProductsStep($paso1);
		//Se procede a guardar el paso 3
		$paso1 = array('num_step' => 3, 'option_description' => "Paso 3", 'quiz_id' => $id, 'business_id' => $valida_token['business_id']);
		$this->games->SaveProductsStep($paso1);

		if ($id) {
			$this->general_mdl->writeLog("Registro de quiz de productos usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'El quiz ha sido guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar quiz de productos usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El quiz no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar un quiz
	 ***********************************************************************/
	public function EditProductsQuiz()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'description', 'points'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a editar el quiz
		$data = array(
			'id' => $this->input->post("id"),
			'description' => $this->input->post("description"),
			'points' => $this->input->post("points")
		);
		if ($this->games->EditProductsQuiz($data)) {
			$this->general_mdl->writeLog("Actualizacion de quiz productos usuario " . $valida_token["user_id"] . " quiz " . $data["id"], "<info>");
			successResponse('', 'El quiz ha sido guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al actualizar quiz productos usuario " . $valida_token["user_id"] . " quiz " . $data["id"], "<warning>");
			faildResponse('El quiz no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para eliminar un quiz
	 ***********************************************************************/
	public function DeleteProductsQuiz()
	{
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

		//Se procede a eliminar un quiz
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->games->DeleteProductsQuiz($data)) {
			$this->general_mdl->writeLog("Eliminacion de quiz productos usuario " . $valida_token["user_id"] . " quiz " . $this->input->post("id"), "<info>");
			successResponse('', 'El quiz ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar quiz productos usuario " . $valida_token["user_id"] . " quiz " . $this->input->post("id"), "<warning>");
			faildResponse('El quiz no se pudo eliminar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 21/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los quiz de productos
	 ***********************************************************************/
	public function ProductQuiz()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$result = $this->games->ProductQuiz($valida_token['business_id']);
		if ($result) {
			$this->general_mdl->writeLog("Listado de quiz productos usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Lista de quiz de productos', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener quiz de productos usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('Nos existen quiz registrados', $this);
		}
	}

	public function TodosLosJuegos()
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
		$result = [];
		$result["juegos__productos"] = $this->games->ProductQuiz($valida_token['business_id']);
		$result["juegos_roulette"] = $this->games->RouletteQuiz($valida_token['business_id']);
		$result["juegos_profiler"] = $this->games->ProfilerQuiz($valida_token['business_id']);
		$result["juegos_serpientes"] = $game = $this->snake->getMyGames($valida_token["user_id"]);
		$this->general_mdl->writeLog("Listado de juegos usuario " . $valida_token["user_id"], "<info>");
		successResponse($result, 'Lista de todos los juegos', $this);
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar una pregunta del quiz
	 ***********************************************************************/
	public function EditProductsStep()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'num_step', 'option_description'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		$image = '';
		$archivos = $this->general_mdl->GuardarArchivos($_FILES, $ruta = 'games/products', $valida_token['business_id']);
		//Si la subida de archivos fue exitosa
		if ($archivos['success']) {
			//Si se subió el correctamente el archivo deseado ("option_image"), guardamos el nombre asignado
			if (isset($archivos['success_files']['option_image'])) {
				$image = $archivos['success_files']['option_image'];
			}
		} else {
			//si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
			faildResponse($archivos['msg'], $this);
			return;
		}

		//Se procede a editar la pregunta requerida
		$data = array(
			'id' => $this->input->post("id"),
			'num_step' => $this->input->post("num_step"),
			'option_description' => $this->input->post("option_description"),
			'option_image' => $image
		);
		if ($this->games->EditProductsStep($data)) {
			$this->general_mdl->writeLog("Actualizacion de pregunta productos usuario " . $valida_token["user_id"] . " pregunta " . $data["id"], "<info>");
			successResponse('', 'La pregunta ha sido guardada correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al actualizar pregunta productos usuario " . $valida_token["user_id"] . " preguntas " . $data["id"], "<warning>");
			faildResponse('La pregunta no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 21/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los pasos en base al quiz enviado
	 ***********************************************************************/
	public function ProductSteps()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('quiz_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$params = array(
			'quiz_id' => $this->input->post('quiz_id'),
			'business_id' => $valida_token['business_id']
		);
		$result = $this->games->ProductSteps($params);
		if ($result) {
			$this->general_mdl->writeLog("Listado de pasos productos usuario " . $valida_token["user_id"] . " quiz " . $params["quiz_id"], "<info>");
			successResponse($result, 'Listado de pasos de productos', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener pasos productos usuario " . $valida_token["user_id"] . " quiz " . $params["quiz_id"], "<warning>");
			faildResponse('No existen pasos', $this);
		}
	}
	public function ProductSteps_()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('quiz_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$result = $this->games->ProductSteps_($this->input->post('quiz_id'));
		if ($result) {
			$this->general_mdl->writeLog("Listado de pasos de productos usuario " . $valida_token["user_id"] . " quiz " . $this->input->post("quiz_id"), "<info>");
			successResponse($result, 'Listado de pasos de productos', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener pasos de productos usuario " . $valida_token["user_id"] . " quiz " . $this->input->post("quiz_id"), "<warning>");
			faildResponse('No existen pasos', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los resultados de quiz
	 ***********************************************************************/
	public function ProductsResults()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$result = $this->games->ProductsResults($valida_token['business_id']);
		if ($result) {
			$this->general_mdl->writeLog("Consulta de resultados productos usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Lista de quiz de ruleta', $this);
		} else {
			$this->general_mdl->writeLog("Error alobtener resultados productos usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('Nos existen quiz registrados', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 21/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para guardar las respuestas de los usuario en productos
	 ***********************************************************************/
	public function SaveAnswerProducts()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('quiz_id', 'step_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['user_id'] = $valida_token['user_id'];
		$result = $this->games->SaveAnswerProducts($data);
		if ($result) {
			$this->general_mdl->writeLog("Registro de respuesta productos usuario " . $valida_token["user_id"] . " paso " . $this->input->post("step_id"), "<info>");
			successResponse($result, 'Tu respuesta ha sido registrada correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar respuesta productos usuario " . $valida_token["user_id"] . " paso " . $this->input->post("step_id"), "<warning>");
			faildResponse('La respuesta no ha sido registrada correctamente', $this);
		}
	}
	public function SaveAnswerProducts_()
	{
		successResponse(array(), 'Tu respuesta ha sido registrada correctamente', $this);
		return;
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('quiz_id', 'step_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['user_id'] = $valida_token['user_id'];
		$result = $this->games->SaveAnswerProducts($data);
		if ($result) {
			$text = 'Tu respuesta ha sido correcta, bien hecho.';
			if ($result['correct'] == 1) {
				$text = 'Tu respuesta no ha sido correcta, vuelve a intentarlo.';
			}
			$this->general_mdl->writeLog("Registro de respuesta productos usuario " . $valida_token["user_id"] . " paso " . $this->input->post("step_id"), "<info>");
			successResponse($result, $text, $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar respuesta productos usuario " . $valida_token["user_id"] . " paso " . $this->input->post("step_id"), "<warning>");
			faildResponse('La respuesta no ha sido registrada correctamente', $this);
		}
	}
	/***************************JUEGO RULETA******************************/
	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para guardar un quizz
	 ***********************************************************************/
	public function SaveRouletteQuiz()
	{
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

		//Se procede a guardar el quiz
		$data = array(
			'business_id' => $valida_token['business_id'],
			'name' => $this->input->post("name"),
			'points' => $this->input->post("points")
		);
		if ($this->games->SaveRouletteQuiz($data)) {
			$this->general_mdl->writeLog("Registro de quiz ruleta usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'El quizz ha sido guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar quiz ruleta usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El quizz no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar un quiz
	 ***********************************************************************/
	public function EditRouletteQuiz()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'name'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a editar el quiz
		$data = array(
			'id' => $this->input->post("id"),
			'name' => $this->input->post("name"),
			'points' => $this->input->post("points")
		);
		if ($this->games->EditRouletteQuiz($data)) {
			$this->general_mdl->writeLog("Actualizacion de quiz ruleta usuario " . $valida_token["user_id"] . " quiz " . $data["id"], "<info>");
			successResponse('', 'El quizz ha sido guardada correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al actualizar quiz ruleta usuario " . $valida_token["user_id"] . " quiz " . $data["id"], "<warning>");
			faildResponse('El quizz no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para eliminar un quiz
	 ***********************************************************************/
	public function DeleteRouletteQuiz()
	{
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

		//Se procede a eliminar un quiz
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->games->DeleteRouletteQuiz($data)) {
			$this->general_mdl->writeLog("Eliminacion de quiz ruleta usuario " . $valida_token["user_id"] . " quiz " . $data["id"], "<info>");
			successResponse('', 'La categoría ha sido eliminada correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar quiz ruleta usuario " . $valida_token["user_id"] . " quiz " . $data["id"], "<warning>");
			faildResponse('La categoría no se pudo eliminar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los quiz de ruleta
	 ***********************************************************************/
	public function RouletteQuiz()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$result = $this->games->RouletteQuiz($valida_token['business_id']);

		if ($result) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
			$this->general_mdl->writeLog("Listado de quiz ruleta usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Lista de quiz de ruleta', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener quiz ruleta usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('Nos existen quiz registrados', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para guardar una pregunta
	 ***********************************************************************/
	public function SaveRouletteQuestion()
	{
		if ($this->input->post() == [])
			$_POST = json_decode(file_get_contents('php://input'), true);
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('question', 'quiz_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a guardar la pregunta
		$data = array(
			'business_id' => $valida_token['business_id'],
			'question' => $this->input->post("question"),
			'quiz_id' => $this->input->post("quiz_id")
		);
		if ($this->games->SaveRouletteQuestion($data)) {
			$this->general_mdl->writeLog("Registro de pregunta ruleta usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'El quizz ha sido guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar pregunta ruleta usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El quizz no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar una pregunta
	 ***********************************************************************/
	public function EditRouletteQuestion()
	{
		if ($this->input->post() == [])
			$_POST = json_decode(file_get_contents('php://input'), true);
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'question'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a editar la pregunta requerida
		$data = array(
			'id' => $this->input->post("id"),
			'question' => $this->input->post("question")
		);
		if ($this->games->EditRouletteQuestion($data)) {
			$this->general_mdl->writeLog("Actualizacion de pregunta ruleta usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'El quizz ha sido guardada correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error alactualizar pregunta ruleta usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El quizz no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para eliminar pregunta
	 ***********************************************************************/
	public function DeleteRouletteQuestion()
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

		//Se procede a eliminar la categoria requerida
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->games->DeleteRouletteQuestion($data)) {
			$this->general_mdl->writeLog("Eliminacion de pregunta ruleta usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("id"), "<info>");
			successResponse('', 'La categoría ha sido eliminada correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar pregunta ruleta usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("id"), "<warning>");
			faildResponse('La categoría no se pudo eliminar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para listar las preguntas de un quiz
	 ***********************************************************************/
	public function RouletteQuestions()
	{
		if ($this->input->post() == [])
			$_POST = json_decode(file_get_contents('php://input'), true);
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('quiz_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$quiz_id = $this->input->post('quiz_id');
		// $es_android = $this->general_mdl->ComprobarSesionAndroid($valida_token["user_id"]);

		$result = $this->games->RouletteQuestions($quiz_id);
		if ($result) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
			$this->general_mdl->writeLog("Listado de preguntas ruleta usuario " . $valida_token["user_id"] . " quiz " . $this->input->post("quiz_id"), "<info>");
			successResponse($result, 'Listado de preguntas', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener preguntas ruleta usuario " . $valida_token["user_id"] . " quiz " . $this->input->post("quiz_id"), "<warning>");
			faildResponse('No existen preguntas', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para guardar una respuesta de pregunta de quiz
	 ***********************************************************************/
	public function SaveRouletteAnswer()
	{
		if ($this->input->post() == [])
			$_POST = json_decode(file_get_contents('php://input'), true);
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('question_id', 'answer', 'correct'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a guardar respuesta
		$data = array(
			'business_id' => $valida_token['business_id'],
			'question_id' => $this->input->post("question_id"),
			'answer' => $this->input->post("answer"),
			'correct' => $this->input->post("correct")
		);
		$result = $this->games->SaveRouletteAnswer($data);
		if ($result) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
			$this->general_mdl->writeLog("Registro de respuesta ruleta usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<info>");
			successResponse('', 'El quizz ha sido guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar respuesta ruleta usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<warning>");
			faildResponse('El quizz no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar una respuesta de pregunta de quiz
	 ***********************************************************************/
	public function EditRouletteAnswer()
	{
		if ($this->input->post() == [])
			$_POST = json_decode(file_get_contents('php://input'), true);
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'question_id', 'answer', 'correct'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a editar la respuesta
		$data = array(
			'id' => $this->input->post("id"),
			'question_id' => $this->input->post("question_id"),
			'answer' => $this->input->post("answer"),
			'correct' => $this->input->post("correct")
		);
		if ($this->games->EditRouletteAnswer($data)) {
			$this->general_mdl->writeLog("Actualizacion de respuesta ruleta usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<info>");
			successResponse('', 'El quizz ha sido guardada correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al actualizar respuesta ruleta usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<warning>");
			faildResponse('El quizz no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para eliminar una respuesta de pregunta de quiz
	 ***********************************************************************/
	public function DeleteRouletteAnswer()
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

		//Se procede a eliminar la categoria requerida
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->games->DeleteRouletteAnswer($data)) {
			$this->general_mdl->writeLog("Eliminacion de respuesta ruleta usuario " . $valida_token["user_id"] . " respuesta " . $this->input->post("id"), "<info>");
			successResponse('', 'La categoría ha sido eliminada correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar respuesta ruleta usuario " . $valida_token["user_id"] . " respuesta " . $this->input->post("id"), "<warning>");
			faildResponse('La categoría no se pudo eliminar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para listar las respuestas a una pregunta de quiz
	 ***********************************************************************/
	public function RouletteAnswers()
	{
		if ($this->input->post() == [])
			$_POST = json_decode(file_get_contents('php://input'), true);
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('question_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$result = $this->games->RouletteAnswers($this->input->post('question_id'));
		if ($result) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
			$this->general_mdl->writeLog("Listado de respuestas ruleta usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<info>");
			successResponse($result, 'Listado de preguntas', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener respuestas ruleta usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<warning>");
			faildResponse('No existen preguntas', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los resultados de quiz
	 ***********************************************************************/
	public function RouletteResults()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$result = $this->games->RouletteResults($valida_token['business_id']);
		if ($result) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
			$this->general_mdl->writeLog("Listado de resultados de ruleta usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Lista de quiz de ruleta', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener resultados de ruleta usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('Nos existen quiz registrados', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Luis Angel Trujillo Gonzalez   Fecha: 29/04/2022
	 *	Nota: Funcion para obtener los resultados de quiz Run Pancho
	 ***********************************************************************/
	public function RunPanchoResults()
	{
		if ($this->input->post() == [])
			$_POST = json_decode(file_get_contents('php://input'), true);
		$token = $this->input->post("token");
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$data["fecha_inicio"] = $this->input->post("fecha_inicio");
		$data["fecha_fin"] = $this->input->post("fecha_fin");
		$result = $this->games->RunPanchoResults($valida_token['business_id'],$data);
		if ($result) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
			$this->general_mdl->writeLog("Listado de resultados de run pancho usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Lista de resultados run pancho', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener resultados de run pancho usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('Nos existen quiz registrados', $this);
		}
	}

	
	/***********************************************************************
	 *	Autor: Luis Angel Trujillo Gonzalez   Fecha: 29/04/2022
	 *	Nota: Funcion para obtener los resultados de quiz Snake
	 ***********************************************************************/
	public function snakeResults()
	{
		if ($this->input->post() == [])
			$_POST = json_decode(file_get_contents('php://input'), true);
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$data["fecha_inicio"] = $this->input->post("fecha_inicio");
		$data["fecha_fin"] = $this->input->post("fecha_fin");
		$result = $this->games->snakeResults($valida_token['business_id'],$data);
		if ($result) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
			$this->general_mdl->writeLog("Listado de resultados de snake usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Lista de resultados run pancho', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener resultados de snake usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('Nos existen quiz registrados', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener las preguntas y respuestas de un quiz
	 *          seleccionado
	 ***********************************************************************/
	public function RouletteQuestionsAnswer()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('quiz_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$quiz_id = $this->input->post('quiz_id');
		$es_android = $this->general_mdl->ComprobarSesionAndroid($valida_token["user_id"]);

		// if ($es_android) {
		// 	if ($quiz_id == 763 || $quiz_id == "763") {
		// 		$quiz_id = 770;
		// 	} else {
		// 		$quiz_id = $quiz_id - 1;
		// 	}
		// }
		$result = $this->games->RouletteQuestionsAnswer($quiz_id);
		if ($result) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
			$this->general_mdl->writeLog("Listado de preguntas y respuestas de quiz usuario " . $valida_token["user_id"] . " quiz " . $this->input->post("quiz_id"), "<info>");
			successResponse($result, 'Listado de preguntas con respuestas', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener preguntas y respuestas de quiz usuario " . $valida_token["user_id"] . " quiz " . $this->input->post("quiz_id"), "<warning>");
			faildResponse('No existen preguntas', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota:Funcion para registrar respuestas de ruleta
	 ***********************************************************************/
	public function SaveAnswerRoulette()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('question_id', 'answer_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['user_id'] = $valida_token['user_id'];
		$result = $this->games->SaveAnswerRoulette($data);
		if ($result) {
			if ($result == 'correct') {
				$correct = 1;
				$texto = "Has contestado correctamente";
			} else {
				$correct = 0;
				$texto = "Has contestado incorrectamente. La respuesta correcta es " . $result;
			}
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
			$this->general_mdl->writeLog("Registro de respuesta ruleta usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<info>");
			successResponse(array('correct' => $correct), $texto, $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar respuesta ruleta usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<warning>");
			faildResponse('La respuesta no ha sido registrada correctamente', $this);
		}
	}


	/***************************JUEGO PROFILER******************************/
	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para guardar un quiz
	 ***********************************************************************/
	public function SaveProfilerQuiz()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('history', 'points'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a guardar el quiz
		$data = array(
			'business_id' => $valida_token['business_id'],
			'history' => $this->input->post("history"),
			'points' => $this->input->post("points")
		);
		if ($this->games->SaveProfilerQuiz($data)) {
			$this->general_mdl->writeLog("Registro de quiz perfilador usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'El quiz ha sido guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar quiz perfilador usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El quiz no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar un quiz
	 ***********************************************************************/
	public function EditProfilerQuiz()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'history', 'points'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a editar el quiz
		$data = array(
			'id' => $this->input->post("id"),
			'history' => $this->input->post("history"),
			'points' => $this->input->post("points")
		);
		if ($this->games->EditProfilerQuiz($data)) {
			$this->general_mdl->writeLog("Actualizacion de quiz perfilador usuario " . $valida_token["user_id"] . " quiz " . $this->input->post("id"), "<info>");
			successResponse('', 'El quiz ha sido guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al actualizar quiz perfilador usuario " . $valida_token["user_id"] . " quiz " . $this->input->post("id"), "<warning>");
			faildResponse('El quiz no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para eliminar un quiz
	 ***********************************************************************/
	public function DeleteProfilerQuiz()
	{
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

		//Se procede a eliminar un quiz
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->games->DeleteProfilerQuiz($data)) {
			$this->general_mdl->writeLog("Eliminacion de quiz perfilador usuario " . $valida_token["user_id"] . " quiz " . $this->input->post("id"), "<info>");
			successResponse('', 'El quiz ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar quiz perfilador usuario " . $valida_token["user_id"] . " quiz " . $this->input->post("id"), "<warning>");
			faildResponse('El quiz no se pudo eliminar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para optener los quiz de profiler
	 ***********************************************************************/
	public function ProfilerQuiz()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$result = $this->games->ProfilerQuiz($valida_token['business_id']);
		if ($result) {
			$this->general_mdl->writeLog("Listado de quiz perfilador usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Lista de quiz de perfilador', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener quiz perfilador usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('Nos existen quiz registrados', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para guardar una pregunta del quiz
	 ***********************************************************************/
	public function SaveProfilerQuestion()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('question', 'quiz_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		$image = '';
		$archivos = $this->general_mdl->GuardarArchivos($_FILES, $ruta = 'games/profiler', $valida_token['business_id']);
		//Si la subida de archivos fue exitosa
		if ($archivos['success']) {
			//Si se subió el correctamente el archivo deseado ("image"), guardamos el nombre asignado
			if (isset($archivos['success_files']['image'])) {
				$image = $archivos['success_files']['image'];
			}
		} else {
			//si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
			faildResponse($archivos['msg'], $this);
			return;
		}

		//Se procede a guardar la pregunta
		$data = array(
			'business_id' => $valida_token['business_id'],
			'question' => $this->input->post("question"),
			'quiz_id' => $this->input->post("quiz_id"),
			'image' => $image
		);
		if ($this->games->SaveProfilerQuestion($data)) {
			$this->general_mdl->writeLog("Registro de pregunta perfilador usuario " . $valida_token["user_id"] . " quiz " . $this->input->post("quiz_id"), "<info>");
			successResponse('', 'La pregunta ha sido guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar pregunta perfilador usuario " . $valida_token["user_id"] . " quiz " . $this->input->post("quiz_id"), "<warning>");
			faildResponse('La pregunta no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar una pregunta del quiz
	 ***********************************************************************/
	public function EditProfilerQuestion()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'question'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		$image = '';
		$archivos = $this->general_mdl->GuardarArchivos($_FILES, $ruta = 'games/profiler', $valida_token['business_id']);
		//Si la subida de archivos fue exitosa
		if ($archivos['success']) {
			//Si se subió el correctamente el archivo deseado ("image"), guardamos el nombre asignado
			if (isset($archivos['success_files']['image'])) {
				$image = $archivos['success_files']['image'];
			}
		} else {
			//si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
			faildResponse($archivos['msg'], $this);
			return;
		}


		//Se procede a editar la pregunta requerida
		$data = array(
			'id' => $this->input->post("id"),
			'question' => $this->input->post("question"),
			'image' => $image
		);
		if ($this->games->EditProfilerQuestion($data)) {
			$this->general_mdl->writeLog("Actualizacion de pregunta perfilador usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("id"), "<info>");
			successResponse('', 'La pregunta ha sido guardada correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al actualizar pregunta perfilador usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("id"), "<warning>");
			faildResponse('La pregunta no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para eliminar pregunta del quiz
	 ***********************************************************************/
	public function DeleteProfilerQuestion()
	{
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

		//Se procede a eliminar la categoria requerida
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->games->DeleteProfilerQuestion($data)) {
			$this->general_mdl->writeLog("Eliminacion de pregunta perfilador usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("id"), "<info>");
			successResponse('', 'La pregunta ha sido eliminada correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar pregunta perfilador usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("id"), "<warning>");
			faildResponse('La pregunta no se pudo eliminar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para listar las preguntas de un quiz
	 ***********************************************************************/
	public function ProfilerQuestions()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('quiz_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$params = array(
			'quiz_id' => $this->input->post('quiz_id'),
			'business_id' => $valida_token['business_id']
		);
		$result = $this->games->ProfilerQuestions($params);
		if ($result) {
			$this->general_mdl->writeLog("Listado de preguntas perfilador usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Listado de preguntas', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener preguntas prefilador usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen preguntas', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para guardar una respuesta a pregunta de quiz
	 ***********************************************************************/
	public function SaveProfilerAnswer()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('question_id', 'answer', 'correct'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a guardar respuesta
		$data = array(
			'business_id' => $valida_token['business_id'],
			'question_id' => $this->input->post("question_id"),
			'answer' => $this->input->post("answer"),
			'correct' => $this->input->post("correct")
		);
		if ($this->games->SaveProfilerAnswer($data)) {
			$this->general_mdl->writeLog("Registro de respuesta profiler usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<info>");
			successResponse('', 'La respuesta ha sido guardada correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar respuesta profiler usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<warning>");
			faildResponse('La respuesta no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar una respuesta a pregunta de quiz
	 ***********************************************************************/
	public function EditProfilerAnswer()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'question_id', 'answer', 'correct'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a editar la respuesta
		$data = array(
			'id' => $this->input->post("id"),
			'question_id' => $this->input->post("question_id"),
			'answer' => $this->input->post("answer"),
			'correct' => $this->input->post("correct")
		);
		if ($this->games->EditProfilerAnswer($data)) {
			$this->general_mdl->writeLog("Actualizacion de respuesta perfilador usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<info>");
			successResponse('', 'La pregunta ha sido guardada correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al actualizar respuesta perfilador usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<warning>");
			faildResponse('La pregunta no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para eliminar una respuesta a pregunta de quiz
	 ***********************************************************************/
	public function DeleteProfilerAnswer()
	{
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

		//Se procede a eliminar la categoria requerida
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->games->DeleteProfilerAnswer($data)) {
			$this->general_mdl->writeLog("Eliminacion de respuesta perfilador usuario " . $valida_token["user_id"] . " respuesta " . $this->input->post("id"), "<info>");
			successResponse('', 'La categoría ha sido eliminada correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar respuesta perfilador usuario " . $valida_token["user_id"] . " respuesta " . $this->input->post("id"), "<warning>");
			faildResponse('La categoría no se pudo eliminar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los resultados de quiz
	 ***********************************************************************/
	public function ProfilerResults()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$result = $this->games->ProfilerResults($valida_token['business_id']);
		if ($result) {
			$this->general_mdl->writeLog("Listado de resultados de perfilador usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Lista de quiz de ruleta', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener resultados perfilador usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('Nos existen quiz registrados', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para listar las respuestas a pregunta de quiz
	 ***********************************************************************/
	public function ProfilerAnswers()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('question_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$result = $this->games->ProfilerAnswers($this->input->post('question_id'));
		if ($result) {
			$this->general_mdl->writeLog("Listado de respuestas perfilador usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<info>");
			successResponse($result, 'Listado de respuestas', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener respuestas perfilador usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<warning>");
			faildResponse('No existen respuestas', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los preguntas con sus respectivas
	 *          respuestas de profile
	 ***********************************************************************/
	public function ProfilerQuestionsAnswer()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('quiz_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$params = array(
			'quiz_id' => $this->input->post('quiz_id'),
			'business_id' => $valida_token['business_id']
		);
		$result = $this->games->ProfilerQuestionsAnswer($params);
		if ($result) {
			$this->general_mdl->writeLog("Listado de preguntas y respuestas perfilador usuario " . $valida_token["user_id"] . " quiz " . $this->input->post("quiz_id"), "<info>");
			successResponse($result, 'Listado de preguntas con respuestas', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener preguntas y respuestas perfilador usuario " . $valida_token["user_id"] . " quiz " . $this->input->post("quiz_id"), "<warning>");
			faildResponse('No existen preguntas', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para guardar la respuesta del usuario y sumar o restar
	 *          puntos.
	 ***********************************************************************/
	public function SaveAnswerProfiler()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('question_id', 'answer_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['user_id'] = $valida_token['user_id'];
		$result = $this->games->SaveAnswerProfiler($data);
		if ($result) {
			$this->general_mdl->writeLog("Registro de respuesta perfilador usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<info>");
			successResponse($result, 'Tu respuesta ha sido registrada correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar respuesta perfilador usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<warning>");
			faildResponse('La respuesta no ha sido registrada correctamente', $this);
		}
	}


	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 06/07/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Obtener lista de juegos para seleccionar los que se
	 * 			visualizaran
	 ***********************************************************************/
	public function ListGamesSelect()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$result = $this->games->ListGamesSelect($valida_token['business_id']);
		if ($result) {
			$this->general_mdl->writeLog("Listado de juegos usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Listado de juegos', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener juegos usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen juegos registrados', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 06/07/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para registrar un game para que lo pueda ver los usuarios
	 * 			de las empresas
	 ***********************************************************************/
	public function ActiveGame()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('game_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['business_id'] = $valida_token['business_id'];
		$result = $this->games->ActiveGame($data);
		if ($result) {
			$this->general_mdl->writeLog("Registro de juego usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'El juego se ha guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar juego usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El juego no se ha guardado correctamente', $this);
		}
	}
	public function DescargarReporteResultadosCulebra($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $data["limite"] = false;
        $this->general_mdl->writeLog("Descarga de reporte de resultados de juego Culebra " . $valida_token["user_id"], "<info>");
        $result = $this->games->ObtenerReporteResultadosCulebra($token, $data);
    }

	public function DescargarReporteResultadosRunPancho($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $data["limite"] = false;
        $this->general_mdl->writeLog("Descarga de reporte de resultados de juego Run Pancho Run " . $valida_token["user_id"], "<info>");
        $result = $this->games->ObtenerReporteResultadosRunPancho($token, $data);
    }
}
