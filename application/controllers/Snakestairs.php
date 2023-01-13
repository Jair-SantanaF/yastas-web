<?php
class Snakestairs extends CI_Controller
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
		$this->load->model('Snake_stairs_mdl', 'ssm');
	}


	public function creatNewGame()
	{
		$token 			= $this->input->post("token");
		$member_id		= $this->input->post("member_id");
		$game_name		= $this->input->post("game_name");
		$valida_token 	= $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}


		$validaPost = $this->general_mdl->validapost(array('game_name', 'member_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}


		$entity  = array("map_id" => "1", "owner_id" => $valida_token["user_id"], 'game_name' => $game_name);
		$game_id = $this->ssm->insert($entity, "game_snake_stairs_active_game");
		// Se crea el orden de turnos para cada integrante solo el creador es el primero en tirar
		$members = explode(",", $member_id);
		shuffle($members);
		$turn = 1;
		$entity = array(
			"game_id" 		=> $game_id,
			"user_id"		=> $valida_token["user_id"],
			"position"		=> 1,
			"turn"			=> $turn
		);
		$this->ssm->insert($entity, "game_snake_stairs_members");
		foreach ($members as $key => $value) {
			$turn++;
			$entity = array(
				"game_id" 		=> $game_id,
				"user_id"		=> $value,
				"position"		=> 1,
				"turn"			=> $turn
			);
			$this->ssm->insert($entity, "game_snake_stairs_members");
		}

		// Se activa el primer turno para el creador tiene 5 horas para realizarlo
		$entity = array(
			"user_id_inturn" 	=> $valida_token["user_id"],
			"game_id"			=> $game_id
		);
		$this->ssm->insert($entity, "game_snake_stairs_active_turn");

		$this->general_mdl->writeLog("Creacion de nuevo juego usuario " . $valida_token["user_id"], "<info>");
		successResponse(array("game_id" => $game_id), 'El juego fue creado con exito ya puedes realizar tu primer tiro', $this);
	}


	public function getActiveGames()
	{
		$token 		= $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}

		$game = $this->ssm->getMyGames($valida_token["user_id"]);
		$response = array();
		foreach ($game as $key => $value) {
			$response[] = $value;
		}
		$this->general_mdl->writeLog("Listado de juegos activos usuario " . $valida_token["user_id"], "<info>");
		successResponse($response, 'Mis juegos', $this);
	}


	public function getGameBrief()
	{
		$token 			= $this->input->post("token");
		$valida_token 	= $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}

		$brief = $this->ssm->fetchAll("game_snake_strairs_brief");
		$this->general_mdl->writeLog("Listado de instrucciones del juego usuario " . $valida_token["user_id"], "<info>");
		successResponse($brief, 'Instrucciones del juego', $this);
	}



	public function getGame()
	{

		$token 		= $this->input->post("token");
		$game_id 	= $this->input->post("game_id");

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

		$gameDt 		= $this->ssm->getgGameData($game_id);
		$gameMembers 	= $this->ssm->gameMembers($game_id);
		$mapBoxes		= $this->ssm->getMapBoxes($game_id);
		$myPosition 	= "1";

		if (!empty($gameDt)) {

			$gameDt 	= $gameDt[0];
			$mapBoxes	= $this->ssm->getMapBoxes($gameDt['map_id']);
			$boxes 		= array();

			//Recorremos los participantes para determinar la posición del usuario en consulta
			foreach ($gameMembers as $key => $value) {
				if ($value["user_id"] == $valida_token["user_id"]) {
					$myPosition = $value["position"];
				}
			}


			foreach ($mapBoxes as $key => $value) {
				$memberInbox = array();
				foreach ($gameMembers as $key => $v) {
					if ($v["position"] == $value['box_number']) {
						$memberInbox[] = $v;
					}
				}
				$value["member_inbox"] = $memberInbox;
				$boxes[] = $value;
			}
			$response = ["boxes" => $boxes, "currentPosition" => $myPosition];
			$this->general_mdl->writeLog("Consulta de juego usuario " . $valida_token["user_id"] . " juego " . $game_id, "<info>");
			successResponse($response, 'Game', $this);
		} else {
			$this->general_mdl->writeLog("Error al consultar juego usuario " . $valida_token["user_id"] . " juego " . $game_id, "<warning>");
			faildResponse('Ese juego no existe', $this);
		}
	}


	public function myTurn()
	{
		$token 		= $this->input->post("token");
		$game_id	= $this->input->post("game_id");
		$dice_num	= $this->input->post("dice_num");
		$is_correct	= $this->input->post("is_correct");

		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}

		$validaPost = $this->general_mdl->validapost(array('game_id', 'dice_num', 'is_correct'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}


		$currentPosition 	= $this->ssm->getUserPositionGame($game_id, $valida_token["user_id"]);
		$isMyPosition 		= $currentPosition[0]['position'];
		$gameDt				= $this->ssm->getgGameData($game_id);
		$youWon 			= "0";

		if ($is_correct == "1") {
			$newPosition 		= $currentPosition[0]['position'] + $dice_num;
			$msg 				= 'Tiro efectuado, esperando al siguiente jugador';


			if ($newPosition < $gameDt[0]['num_boxs']) {

				$nextBox = $this->ssm->getNextBox($gameDt[0]['map_id'], $newPosition);

				if ($nextBox[0]['rule'] !=  "0") {
					$newPosition = $nextBox[0]['rule'];
					if ($isMyPosition < $newPosition) {
						$msg = "¡Ups! caíste en serpiente bajas hasta la casilla " . $nextBox[0]['rule'];
					} else {
						$msg = "Excelente! caíste en escalera subes hasta la casilla " . $nextBox[0]['rule'];
					}
				} else {
					$msg = "¡Bien!, avanzas a la casilla " . $newPosition;
				}
			} else {
				$newPosition 	= $gameDt[0]['num_boxs'];
				$youWon 		= "1";
			}
		} else {
			$newPosition 		= $currentPosition[0]['position'];
			$msg = "¡Lo siento!, no avanzas respondiste incorrecto";
		}

		$this->ssm->update($currentPosition[0]['id'], array('position' => $newPosition), 'game_snake_stairs_members');
		$this->ssm->delete(array("game_id" => $game_id), "game_snake_stairs_active_turn");
		$nextTurn = $this->ssm->getNextTurn($game_id, $currentPosition[0]['turn']);
		$this->ssm->insert(array("user_id_inturn" => $nextTurn, "game_id" => $game_id), "game_snake_stairs_active_turn");




		if ($youWon != "0") {
			$nextTurn = "";
			$msg = '¡FELICIDADES! Ganaste la partida :)';
		} else {

			$userToken = $this->ssm->fetchAllById("id_user", $nextTurn, "devices");

			if (!empty($userToken)) {
				$this->_sendNotification($userToken[0]["token"]);
			}
		}

		$boxes 			= array();
		$gameMembers 	= $this->ssm->gameMembers($game_id);
		$mapBoxes		= $this->ssm->getMapBoxes($gameDt[0]['map_id']);
		$myPosition 	= "";

		foreach ($mapBoxes as $key => $value) {
			$memberInbox = array();
			foreach ($gameMembers as $key => $v) {
				if ($v["position"] == $value['box_number']) {
					$memberInbox[] = $v;
				}
				if ($v["user_id"] == $valida_token["user_id"]) {
					$myPosition = $v["position"];
				}
			}
			$value["member_inbox"] = $memberInbox;
			$boxes[] = $value;
		}

		$response = array(
			"youWon" => $youWon,
			"nextTurn" => $nextTurn,
			"boxes" => $boxes,
			"currentPosition" => $myPosition
		);
		$this->general_mdl->writeLog("Registro de turno del jugador usuario " . $valida_token["user_id"], "<info>");
		successResponse($response, $msg, $this);
	}



	public function getQuestion()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		// $answer[] = array(
		// 	"id" 		=> "1",
		// 	"answer" 	=> "¿Lorem",
		// 	"isCorrect" => "1"
		// );

		// $answer[] = array(
		// 	"id" 		=> "2",
		// 	"answer" 	=> "¿Lorem 2",
		// 	"isCorrect" => "0"
		// );

		// $answer[] = array(
		// 	"id" 		=> "3",
		// 	"answer" 	=> "¿Lorem 3",
		// 	"isCorrect" => "0"
		// );
		// $response = array(
		// 	"question" 	=> "¿Lorem ipsum?",
		// 	"answer" 	=> $answer
		// );
		// $this->general_mdl->writeLog("Listado de comentarios usuario " . $valida_token["user_id"], "<info>");
		$response = $this->ssm->getQuestion($valida_token["business_id"]);
		successResponse($response, "Snake Stairs Question", $this);
	}


	public function saveAnswer()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$data = [];
		$data["user_id"] = $valida_token["user_id"];
		$data["question_id"] = $this->input->post("question_id");
		$data["answer_id"] = $this->input->post("answer_id");
		$response = $this->ssm->save_answer($data);
		successResponse($response, "Respuesta guardada", $this);
	}

	public function cron_skipTurn()
	{
		$activeTurn = $this->ssm->fetchAll('game_snake_stairs_active_turn');

		$currentTime = date('Y-m-d H:i:s');


		foreach ($activeTurn as $key => $value) {

			$now 		= strtotime($currentTime);
			$turn 		= strtotime($value["created_at"]);
			$delayHrs 	= abs($now - $turn) / (60 * 60);

			if ($delayHrs > 12) {
				echo "<pre>";
				print_r($value);
				echo "</pre>";
				$this->ssm->delete(array("game_id" => $value['game_id']), "game_snake_stairs_active_turn");
				$nextTurn = $this->ssm->getNextTurn($value['game_id'], $value["user_id_inturn"]);
				$this->ssm->insert(array("user_id_inturn" => $nextTurn, "game_id" => $value['game_id']), "game_snake_stairs_active_turn");
			}
		}
	}

	public function _sendNotification($token_firebase)
	{

		// $token_firebase = "cId_j4hRe4I:APA91bEtEBJqbomVQQXdYEDG7bW2nACyjFRO_KFJA7zo3yySx5vbK8ZJtFXwrcQBhX4KzwNSHnj2htLcqPtZfYjH1ghNDp6OZdusHqMytK6Wk-AjQfmV2y4syjHKucwCr18M3_drQjix";
		$tokens = array();
		array_push($tokens, $token_firebase);
		$this->general_mdl->writeLog("Envio de notificacion de turno ", "<info>");
		$enviar_notificacion = $this->general_mdl->EnviarNotificacionPush($tokens, "Es tu turno de tirar en 🐍🪜 Serpientes y Escaleras 🐍🪜 ", "Nuup", "0");
		// echo $enviar_notificacion;

	}

	public function testPush()
	{
		$token = "cExe1h80ht8:APA91bFvAlnbJwB6vBzyyeXFOXJ1BmeTGYedcrE6-YGGOfGEzzEH8Vx8baDsk8YDRR0WeXCt6WOgJNewC2VeDahey_A9HIc1_Dq36LaXXmo9U4OpDH_mXp3NKqBwrdwlwJ4NyeToxRyo";
		$this->_sendNotification($token);
	}

	public function obtenerPreguntas()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$response = $this->ssm->obtenerPreguntas($valida_token["business_id"]);
		successResponse($response, "Listado de preguntas para serpientes y escaleras", $this);
	}

	public function agregarPregunta()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$data = [];
		$data["question"] = $this->input->post("question");
		$data["business_id"] = $valida_token["business_id"];
		$respuestas = $this->input->post("respuestas");
		$response = $this->ssm->agregarPregunta($data, $respuestas);
		successResponse($response, "Listado de preguntas para serpientes y escaleras", $this);
	}

	public function eliminarPregunta()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$id_pregunta = $this->input->post("id_pregunta");
		$response = $this->ssm->eliminarPregunta($id_pregunta);
		successResponse($response, "Pregunta eliminada", $this);
	}

	public function actualizarPregunta()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$data = $this->input->post();
		$response = $this->ssm->actualizarPregunta($data);
		successResponse($response, "Actualizado con exito", $this);
	}

	public function eliminarRespuesta()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$id_respuesta = $this->input->post("id_respuesta");
		$response = $this->ssm->eliminarRespuesta($id_respuesta);
		successResponse($response, "Respuesta eliminada", $this);
	}

	public function actualizarRespuesta()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$data = $this->input->post();
		$response = $this->ssm->actualizarRespuesta($data);
		successResponse($response, "Respuesta actualizada", $this);
	}

	public function agregarRespuesta()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$data = $this->input->post(); //answer, question_id, correct
		$response = $this->ssm->agregarRespuesta($data);
		successResponse($response, "Respuesta insertada", $this);
	}
}
