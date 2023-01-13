<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notification extends CI_Controller
{

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
		$this->load->model('notification_mdl', 'notification');
		$this->load->model('question_mdl', 'questions');
		$this->load->model('user_model', 'user');
		$this->load->model('ambiente_laboral_mdl', 'ambiente_laboral');
		$this->load->model('capacitacion_mdl', 'capacitacion');
		$this->load->model('retos_mdl', "retos");
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 09/07/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener las notificaciones que no hemos leido
	 ***********************************************************************/
	public function ListNotifications()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$list = $this->notification->ListNotifications($valida_token['user_id'], true);
		if ($list) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "home");
			$this->general_mdl->writeLog("Listado de notificaciones usuario " . $valida_token["user_id"], "<info>");
			successResponse($list, 'Listado de notificaciones', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener notificaciones usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen notificaciones', $this);
		}
	}

	function ListQuestionsAmbienteLaboral($business_id = null, $user_id = null)
	{
		$data = $this->input->post();

		$data['question_id'] = ($this->input->post('question_id')) ? $this->input->post('question_id') : '';
		$data['quiz_id'] = $this->questions->obtener_quiz_a_l_activo($business_id);
		if ($data["quiz_id"] != []) {
			$data['business_id'] = $business_id;
			$result = $this->questions->ListQuestionsQuiz($data);
			$result_ = [];
			for ($i = 0; $i < count($result); $i++) {
				$result[$i]["dias"] = $this->ambiente_laboral->obtener_dias_pregunta($result[$i]["id"]);
				if (!$this->ambiente_laboral->comprobar_dia($result[$i]["id"]) == 0) {
					array_push($result_, $result[$i]);
				}
			}
			$dia = date("N");
			$servicio_contratado = $this->questions->VerificarServicioContratado($business_id);
			$en_rango = $this->ambiente_laboral->comprobar_rango_fechas();
			$contestado = $this->questions->ComprobarAmbienteLaboralContestado($user_id, $data["quiz_id"]);
			$primeraVez = false; //$this->questions->validaPrimerIngreso($user_id);
			$usuarioNuevoFecha = true; //$this->questions->validarIngresoEnFecha($user_id);

			if ($servicio_contratado && $en_rango && $usuarioNuevoFecha) {

				//validacion para que solo muestre dos preguntas el viernes se quito porque yastas necesita las dos preguntas una unica vez
				// if ($dia != 5) {
				// 	$result = [0 => $result[0]];
				// }
				if ($result_ && $contestado == false) {
					if ($primeraVez) {
						return ["hacer_quiz" => 0];
					} else
						return ["quiz_ambiente_laboral" => $result_, "hacer_quiz" => 1];
				} else {
					return ["hacer_quiz" => 0];
				}
			} else {
				return ["hacer_quiz" => 0];
			}
		} else {
			return ["hacer_quiz" => 0];
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 09/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Validate notificacion icono
	 ***********************************************************************/
	public function ValidateNotification()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$list = $this->notification->comprobarNotificaciones($valida_token['user_id'], false);
		$quiz = $this->ListQuestionsAmbienteLaboral($valida_token["business_id"], $valida_token["user_id"]);
		// $list["extras"] = $quiz;
		$arr = [];
		if (count($quiz) > 0) {
			$arr = $quiz;
		}
		if ($list == false)
			$list = [];
		$primeraVez = $this->questions->validaPrimerIngreso($valida_token["user_id"]);
		$video_instrucciones_visto = $this->user->get_video_visto($valida_token["user_id"]);
		$this->user->set_instrucciones_vistas($valida_token["user_id"]);
		// if ($primeraVez)
		// 	$quiz["ver_video"] = 1;
		// else
		// 	$quiz["ver_video"] = 0;
		// $quiz["video_tour"] = "https://appy.com.mx/tour/video_tour.mp4";
		// if (!$video_instrucciones_visto)
		// $quiz["ver_video"] = 1;
		// else
        if ($primeraVez){
            $quiz["ver_video"] = 1;

        }else{
            $quiz["ver_video"] = 0;
        }
        $quiz["video_tour"] = "";

        if($_SERVER['SERVER_NAME'] == "kreativeco.com"){
            if($valida_token["business_id"] == 83){
                $quiz["video_tour"] = "https://".$_SERVER['SERVER_NAME']."/qa-bimbo-nuup/tour/video_tour.mp4";            
            }
        }else{
            $quiz["video_tour"] = "https://".$_SERVER['SERVER_NAME']."/tour/video_tour.mp4";
        }

		$obligatorias_pendientes = $this->obtener_capacitaciones_obligatorias_pendientes($valida_token);
		$quiz["obligatorias_pendientes"] = $obligatorias_pendientes;
		//array_push($list, ["extra" => $quiz]);
		if ($list) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "home");
			$this->general_mdl->writeLog("Validacion de notificaciones pendientes usuario " . $valida_token["user_id"], "<info>");
			successResponse([$list], 'Tiene notificaciones pendientes', $this, $quiz);
		} else {
			$this->general_mdl->writeLog("Error al comprobar notificaciones pendientes usuario " . $valida_token["user_id"], "<warning>");
			successResponse(array(), 'No existen notificaciones pendientes', $this, $quiz);
		}
	}

	function obtener_capacitaciones_obligatorias_pendientes($valida_token)
	{
		$empezadas = $this->capacitacion->obtener_capacitaciones_empezadas($valida_token['user_id']);
		$capacitaciones = $this->capacitacion->getCapacitaciones($valida_token["user_id"], $valida_token["business_id"]);

		$ids = [];
		if (is_array($capacitaciones) && count($capacitaciones) > 1)
			for ($i = 0; $i < count($capacitaciones); $i++) {
				if ($capacitaciones[$i]["tipo"] == 1) {
					$bandera = false;
					for ($j = 0; $j < count($empezadas); $j++) {
						if ($empezadas[$j]["id_capacitacion"] == $capacitaciones[$i]["id"]) {
							$bandera = true;
						}
					}
					if ($bandera == false) {
						array_push($ids, ["id" => $capacitaciones[$i]["id"], "name" => $capacitaciones[$i]["name"]]);
					}
				}
			}
		return $ids;
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 09/07/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para marcar una notificacion como vista
	 ***********************************************************************/
	public function NotificationView()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('notification_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$list = $this->notification->NotificationView($this->input->post('notification_id'));
		if ($list) {
			$this->general_mdl->writeLog("Registro de notificacion vista usuario " . $valida_token["user_id"] . " notificacion " . $this->input->post("notification_id"), "<info>");
			successResponse(array(), 'La notificacion ha sido actualizada', $this);
		} else {
			$this->general_mdl->writeLog("Error al establecer notificacion como vista usuario " . $valida_token["user_id"] . " notificacion " . $this->input->post("notification_id"), "<warning>");
			faildResponse('No existen notificaciones', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 09/07/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para enviar multiple notificaciones
	 ***********************************************************************/
	public function enviarMultiplePush()
	{
		// if ($this->input->post() == []) {
		// 	$_POST = json_decode(file_get_contents('php://input'), true);
		// }

		$sess_id = "";

		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);

		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}

		$sess_id = $valida_token["user_id"];
		if (empty($sess_id)) {
			faildResponse('No existe una sesion iniciada', $this);
		} else {

			$validaPost = $this->general_mdl->validapost(array('titulo', 'notificacion'), $this->input->post());
			if (!$validaPost['success']) {
				faildResponse($validaPost['msg'], $this);
				return;
			}

			$titulo = $this->input->post("titulo");
			$notificacion = $this->input->post("notificacion");
			$service_id = $this->input->post("service_id");
			$id_region =  null; //$this->input->post("select_region");
			$id_asesor =  null; //$this->input->post("select_asesor");

			// if ($this->session->userdata('rol_id') == 5) {
			// 	$asesor = $this->input->post("select_asesor");
			// } else {
			// 	$asesor = $this->input->post("id_user");
			// }

			$grupos = json_decode($this->input->post("grupos"), true);
			$regiones = json_decode($this->input->post("regiones"));
			$asesores = json_decode($this->input->post("asesores"));
			/***********************************************************************
			 *	Nota: Se obtiene los tokens existentes en la BD
			 ***********************************************************************/
			if ($grupos[0] == 29) {
				$tokens = $this->notification->ListUserNotificationRecertificacion();
			} else {
				$tokens = $this->notification->ListUserNotification($valida_token["business_id"], $valida_token["user_id"], $id_region, $id_asesor, $regiones, $asesores, $grupos);
			}
			// echo json_encode($tokens);
			if (!$tokens) {
				faildResponse('No existen devices registrados', $this);
				return;
			} else {
				$tokens_ = array();
				foreach ($tokens as $index => $value) {
					array_push($tokens_, $value['token']);
					/***********************************************************************
					 *	Autor: Mario Adrián Martínez Fernández   Fecha: 09/07/2020
					 *		   mario.martinez.f@hotmail.es
					 *	Nota: Guardarmos a los usuarios que se les enviara la notificacion.
					 ***********************************************************************/

					$data = array('title' => $titulo, 'notification' => $notificacion, 'user_id' => $value['user_id'], 'service_id' => $service_id, 'user_create_id' => $this->session->userdata('id_user'));

					$this->notification->RegisterNotification($data);
				}
			}
			/***********************************************************************
			 *	Nota: Se envia notificacion a los multiples tokens
			 ***********************************************************************/
			// $enviar_notificacion = $this->general_mdl->EnviarNotificacionPush($tokens_, $notificacion, $titulo, $service_id);
			$this->general_mdl->writeLog("Envio de notificacion push multiple usuario " . $valida_token["user_id"], "<info>");
			// echo $enviar_notificacion;
		}
	}

	public function enviarNotificacionRuletaTiempo()
	{
		//$id_usuario,$nombre_usuario,$id_own
		$string = $this->input->get("id_usuario");
		$arr = explode("-", $string);
		$longitud = count($arr);
		$nombre_usuario = $arr[$longitud - 2];
		$id_own = $arr[$longitud - 1];
		$users = array_slice($arr, 0, ($longitud - 2));
		$tokens = $this->user->getTokensUsuarios($users);
		$data = [];
		$data['id_usuario'] = $users;
		$data['nombre_usuario'] = $nombre_usuario;
		$data['id_own'] = $id_own;
		$data['token'] = $tokens;

		if ($tokens != "") {
			for ($i = 0; $i < count($users); $i++) {
				$data_ = array('title' => 'Ruleta retos', 'notification' => 'Te quedan solo 30 minutos para contestar el reto de ' . $nombre_usuario, 'user_id' => $users[$i], 'service_id' => SERVICE_RULETA, 'user_create_id' => $id_own);
				$this->notification->RegisterNotification($data_);
			}

			$this->general_mdl->EnviarNotificacionPush($tokens, 'Te quedan solo 30 minutos para contestar el reto de ' . $nombre_usuario, 'Ruleta retos', SERVICE_RULETA);
			// echo json_encode(" se debio enviar la notificacion por char");
		}
		successResponse($data, 'Se envio notificacion de tiempo limite ruleta retos', $this);
	}

	public function enviarNotificacionRetoTiempo()
	{
		//$id_usuario,$nombre_usuario,$id_own
		$string = $this->input->get("id_usuario");
		$arr = explode("-", $string);
		$longitud = count($arr);
		$nombre_usuario = $arr[$longitud - 2];
		$id_own = $arr[$longitud - 1];
		$id_reto = $arr[0];
		$users = $this->retos->omitir_realizados($id_reto);
		$tokens = $this->user->getTokensUsuarios($users);
		$data = [];
		$data['id_usuario'] = $users;
		$data['nombre_usuario'] = $nombre_usuario;
		$data['id_own'] = $id_own;
		$data['token'] = $tokens;

		if ($tokens != "") {
			for ($i = 0; $i < count($users); $i++) {
				$data_ = array('title' => 'Retos', 'notification' => 'Te queda poco tiempo para contestar el reto de ' . $nombre_usuario, 'user_id' => $users[$i], 'service_id' => SERVICE_RETOS, 'user_create_id' => $id_own);
				$this->notification->RegisterNotification($data_);
			}

			$this->general_mdl->EnviarNotificacionPush($tokens, 'Te queda poco tiempo para contestar el reto de ' . $nombre_usuario, 'Retos', SERVICE_RETOS);
			// echo json_encode(" se debio enviar la notificacion por char");
		}
		successResponse($data, 'Se envio notificacion de tiempo limite de retos', $this);
	}

	//esta funcion se manda a llamar desde un cron en el servidor
	//el periodo esta definido en el cron, de incio se establecio cada semana
	public function eliminarNotificaciones()
	{
		$this->notification->eliminarNotificaciones();
	}

	public function obtener_notificaciones()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$list = $this->notification->obtener_notificaciones($valida_token["business_id"]);
		if ($list) {
			$this->general_mdl->writeLog("Listado de notificaciones usuario " . $valida_token["user_id"] . " notificacion " . $this->input->post("notification_id"), "<info>");
			successResponse($list, 'Listado de notificaciones', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener listado de notificaciones usuario " . $valida_token["user_id"] . " notificacion " . $this->input->post("notification_id"), "<warning>");
			faildResponse('No existen notificaciones', $this);
		}
	}

	public function descargar_notificaciones()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$list = $this->notification->obtener_notificaciones($valida_token["business_id"]);
		header('Content-Type: text/csv; charset=utf-8');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header("Content-Disposition: attachment; filename=Notificaciones" . date('y-m-d') . ".csv");
		header('Last-Modified: ' . date('D M j G:i:s T Y'));
		$outss = fopen("php://output", "w");
		$resultado = array_merge([["NOTIFICACION", "SECCION", "FECHA", "GRUPO"]], $list);
		foreach ($resultado as $rows) {
			fputcsv($outss, $rows);
		}
		fclose($outss);
		return;
	}
}
