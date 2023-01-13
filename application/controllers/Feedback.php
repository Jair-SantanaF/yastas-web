<?php
class Feedback extends CI_Controller
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
		$this->load->model('feedback_mdl', 'feedback');
		$this->load->model('user_model', 'user');
		$this->load->model('notification_mdl', 'notification');
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para guardar una categoría de feedback
	 ***********************************************************************/
	public function SaveCategory()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('description'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a guardar la categoría requerida
		$data = array(
			'business_id' => $valida_token['business_id'],
			'description' => $this->input->post("description")
		);
		if ($this->feedback->SaveCategory($data)) {
			$this->general_mdl->writeLog("Registro de categoria feedback usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'La categoría ha sido guardada correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar categoria feedback usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('La categoría no se pudo guardar.', $this);
		}
	}



	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar una categoría de feedback
	 ***********************************************************************/
	public function EditCategory()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'description'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a editar la categoría requerida
		$data = array(
			'id' => $this->input->post("id"),
			'description' => $this->input->post("description")
		);
		if ($this->feedback->EditCategory($data)) {
			$this->general_mdl->writeLog("Actualizacion de categoria feedback usuario " . $valida_token["user_id"] . " categoria " . $this->input->post("id"), "<info>");
			successResponse('', 'La categoría ha sido guardada correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al actualizar categoria feedback usuario " . $valida_token["user_id"] . " categoria " . $this->input->post("id"), "<warning>");
			faildResponse('La categoría no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para eliminar una categoría de feedback
	 ***********************************************************************/
	public function DeleteCategory()
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

		//Validar que no tenga elementos asociados. De lo contrario se termina el proceso, enviando error
		$filters = array(
			'id' => $this->input->post("id")
		);
		$elementos_asociados = $this->feedback->ValidCategory($filters);
		if ($elementos_asociados) {
			faildResponse('La categoría no se puede eliminar, tiene elementos asociados.', $this);
			return;
		}

		//Se procede a eliminar la categoria requerida
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->feedback->DeleteCategory($data)) {
			$this->general_mdl->writeLog("Eliminacion de categoria feedback usuario " . $valida_token["user_id"] . " categoria " . $this->input->post("id"), "<info>");
			successResponse('', 'La categoría ha sido eliminada correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar categoria feedback usuario " . $valida_token["user_id"] . " categoria " . $this->input->post("id"), "<warning>");
			faildResponse('La categoría no se pudo eliminar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 02/06/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener las categorias de feedback
	 ***********************************************************************/
	public function CategoryFeedback()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$categories = $this->feedback->CategoryFeedback($valida_token['business_id']);
		if ($categories) {
			$this->general_mdl->writeLog("Listado de categorias feedback usuario " . $valida_token["user_id"], "<info>");
			successResponse($categories, 'Listado de categorias de feedback', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener categorias feedback usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para eliminar un elemento del feedback
	 ***********************************************************************/
	public function DeleteFeedback()
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

		//Se procede a eliminar el feedback requerido
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->feedback->DeleteFeedback($data)) {
			$this->general_mdl->writeLog("Eliminacion de elemento feedback usuario " . $valida_token["user_id"] . " elemento " . $this->input->post("id"), "<info>");
			successResponse('', 'El feedback ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar elemento feedback usuario " . $valida_token["user_id"] . " elemento " . $this->input->post("id"), "<warning>");
			faildResponse('El feedback no se pudo eliminar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 02/06/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: funcion para registrar un feedback
	 ***********************************************************************/
	public function CreateFeedback()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('user_id', 'description', 'category_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['owner_id'] = $valida_token['user_id'];
		$archivos = $this->general_mdl->GuardarArchivos($_FILES, 'feedback', $valida_token['business_id']);
		//Si la subida de archivos fue exitosa
		if ($archivos['success']) {
			//Si se subió el correctamente el archivo deseado ("media_path", "file_path"), guardamos el nombre asignado
			if (isset($archivos['success_files']['media_path'])) {
				$data['media_path'] = $archivos['success_files']['media_path'];
			}
			if (isset($archivos['success_files']['file_path'])) {
				$data['file_path'] = $archivos['success_files']['file_path'];
			}
		} else {
			//si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
			faildResponse($archivos['msg'], $this);
			return;
		}
		$createFeedback = $this->feedback->CreateFeedback($data);
		if ($createFeedback) {
			$this->enviarNotificacion($data["user_id"], $valida_token["user_id"], $valida_token["name_complete"]);
			$this->general_mdl->writeLog("Registro de elemento feedback usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'El feedback se ha creado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar elemento feedback usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El feedback no se ha creado correctamente', $this);
		}
	}

	public function enviarNotificacion($id_usuario, $id_own, $nombre_usuario)
	{
		$tokens = $this->user->ObtenerToken($id_usuario);
		// echo json_encode($tokens);
		if ($tokens != "") {
			$data = array('title' => 'Feedback', 'notification' => 'Obtuviste retroalimentacion de ' . $nombre_usuario, 'user_id' => $id_usuario, 'service_id' => SERVICE_FEEDBACK, 'user_create_id' => $id_own);
			$this->notification->RegisterNotification($data);

			$this->general_mdl->EnviarNotificacionPush([$tokens], 'Obtuviste retroalimentacion de ' . $nombre_usuario, 'Feedback', SERVICE_FEEDBACK, false);
			// echo json_encode("se debio enviar la notificacion por char");
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 02/06/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener la listas de feedback
	 ***********************************************************************/
	public function FeedbackList()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('type'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['business_id'] = $valida_token['business_id'];
		$data['user_id'] = $valida_token['user_id'];
		$list = $this->feedback->FeedbackList($data);
		if ($list) {
			$this->general_mdl->writeLog("Listado de feedback usuario " . $valida_token["user_id"], "<info>");
			successResponse($list['list_feedback'], 'Listado de feedback', $this, $list['counts']);
		} else {
			$this->general_mdl->writeLog("Error al obtener elementos feedback usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existe feedback para este usuario', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 02/06/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para registrar el like de un feedback
	 ***********************************************************************/
	public function SaveLike()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('feedback_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['user_id'] = $valida_token['user_id'];
		$saveLike = $this->feedback->SaveLike($data);
		if ($saveLike) {
			$this->general_mdl->writeLog("Registro de like a comentario feedback usuario " . $valida_token["user_id"] . " comentario " . $this->input->post("feedback_id"), "<info>");
			successResponse('', 'Like registrado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar like a comentario feedback usuario " . $valida_token["user_id"] . " comentario " . $this->input->post("feedback_id"), "<warning>");
			faildResponse('Like no registrado correctamente.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 04/06/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Función para ocultar y mostrar un feedback
	 ***********************************************************************/
	public function ShowHideFeedback()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('feedback_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['user_id'] = $valida_token['user_id'];
		$showhideFeedback = $this->feedback->ShowHideFeedback($data);
		if ($showhideFeedback === 'not_owner') {
			faildResponse('No eres dueño de feedback para mostra y ocultar.', $this);
			return;
		}
		if ($showhideFeedback) {
			$this->general_mdl->writeLog("Actualizacion de estado elemento feedback usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'Actualizado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al actualizar estado elemento feedback usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No se ha actualizado correctamente.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 05/06/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los usuarios de una empresa en base
	 * 			del token actual.
	 ***********************************************************************/
	public function ListUsersBusiness()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$search = ($this->input->post("search")) ? $this->input->post("search") : '';
		$list = $this->user->ListUsersBusiness($valida_token['business_id'], $search, $valida_token["user_id"]);
		if ($list) {
			$this->general_mdl->writeLog("Listado de usuarios por empresa usuario " . $valida_token["user_id"], "<info>");
			successResponse($list, 'Listado de usuarios por empresa', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener usuarios por empresa usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen usuario en esta empresa.', $this);
		}
	}
}
