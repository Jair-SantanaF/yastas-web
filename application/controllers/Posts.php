<?php
class Posts extends CI_Controller
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
		$this->load->model('posts_mdl', 'posts');
		$this->load->model('User_model', 'user');
		$this->load->model('Notification_mdl', 'notification');
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para guardar un post nuevo.
	 ***********************************************************************/
	public function SaveWall()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('wall_description'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['user_id'] = $valida_token['user_id'];
		$data['business_id'] = $valida_token['business_id'];
		$archivos = $this->general_mdl->GuardarArchivos($_FILES, $ruta = 'walls', $valida_token['business_id']);
		//Si la subida de archivos fue exitosa
		if ($archivos['success']) {
			//Si se subió el correctamente el archivo deseado ("image"), guardamos el nombre asignado
			if (isset($archivos['success_files']['image'])) {
				$data['image_path'] = $archivos['success_files']['image'];
			}
		} else {
			//si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
			faildResponse($archivos['msg'], $this);
			return;
		}
		if ($this->posts->SaveWall($data)) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "yastas al dia");
			$this->enviarNotificacion($valida_token["user_id"], "Se ha agregado una nueva publicación");
			$this->general_mdl->writeLog("Registro nuevo en Muro usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'El post ha sido guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar publicacion en Muro usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El post no se ha registrado correctamente.', $this);
		}
	}

	function enviarNotificacion($user_id, $mensaje)
	{
		$grupos = $this->user->getUsersInGroup($user_id);
		$tokens = $this->user->getTokensByGroups($grupos);
		if ($tokens) {
			$tokens_ = array();
			foreach ($tokens as $index => $value) {
				array_push($tokens_, $value['token']);
				$data = array('title' => 'Muro', 'notification' => $mensaje, 'user_id' => $value['user_id'], 'service_id' => SERVICE_WALL, 'user_create_id' => $user_id);
				$this->notification->RegisterNotification($data);
			}
			$this->general_mdl->EnviarNotificacionPush($tokens_, $mensaje, 'Muro', SERVICE_WALL);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los posts generados para una empresa
	 ***********************************************************************/
	public function WallsList()
	{

		if($this->input->post() == []){
            $_POST = json_decode(file_get_contents('php://input'), true);
        }

		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$data = $this->posts->WallsList($valida_token['business_id'], $valida_token['user_id']);
		if ($data) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "yastas al dia");
			$this->general_mdl->writeLog("Listado de publicaciones de Muro usuario " . $valida_token["user_id"], "<info>");
			successResponse($data, 'Listado de post registrados', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener publicaciones de Muro usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para registrar un comentario a un post
	 ***********************************************************************/
	public function SaveWallComment()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('comment', 'post_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['user_id'] = $valida_token['user_id'];
		if ($this->posts->SaveWallComment($data)) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "yastas al dia");
			$this->general_mdl->writeLog("Registro de comentario de Muro usuario " . $valida_token["user_id"] . " publicacion " . $data["post_id"], "<info>");
			successResponse('', 'El comentario se ha registrado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar comentario de Muro usuario " . $valida_token["user_id"] . " publicacion " . $data["post_id"], "<warning>");
			faildResponse('El comentario no ha registrado correctamente', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtner el listado de comentarios
	 ***********************************************************************/
	public function ListCommentsPost()
	{
		$token = $this->input->post("token");
		$limit = $this->input->post('limit') ?: 0;
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('post_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->posts->ListCommentsPost($this->input->post('post_id'), $limit,$valida_token["user_id"]);
		if ($data) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "yastas al dia");
			$this->general_mdl->writeLog("Listado de comentarios de Muro usuario " . $valida_token["user_id"] . " publicacion " . $this->input->post("post_id"), "<info>");
			successResponse($data, 'Listado de comentarios registrados', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener comentarios de Muro usuario " . $valida_token["user_id"] . " publicacion " . $this->input->post("post_id"), "<warning>");
			faildResponse('No existen registros.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: funcion para registrar un like a post en especifico
	 ***********************************************************************/
	public function SaveLikePost()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('post_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['user_id'] = $valida_token['user_id'];
		if ($this->posts->SaveLikePost($data)) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "yastas al dia");
			$this->general_mdl->writeLog("Registro de like a publicacion usuario " . $valida_token["user_id"] . " publicacion " . $data["post_id"], "<info>");
			successResponse('', 'El like se ha registrado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar like a publicacion usuario " . $valida_token["user_id"] . " publicacion " . $data["post_id"], "<warning>");
			faildResponse('El like no ha registrado correctamente', $this);
		}
	}
	
	public function SharePost()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('post_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['user_id'] = $valida_token['user_id'];
		if ($this->posts->SharePost($data)) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "yastas al dia");
			$this->general_mdl->writeLog("Reshare de publicacion en Muro usuario " . $valida_token["user_id"] . " publicacion " . $data["post_id"], "<info>");
			successResponse('', 'El post se ha guardardo en reshare', $this);
		} else {
			$this->general_mdl->writeLog("Error al hacer reshare de publicacion en Muro usuario " . $valida_token["user_id"] . " publicacion " . $data["post_id"], "<warning>");
			faildResponse('Error al hacer reshare', $this);
		}
	}

	public function SaveLikeComment()
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
		$data = [];
		$data["comment_id"] = $this->input->post("comment_id");
		$data['user_id'] = $valida_token['user_id'];
		if ($this->posts->SaveLikeComment($data)) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "yastas al dia");

			$this->general_mdl->writeLog("Registro de like a comentario en Muro usuario " . $valida_token["user_id"] . " comentario " . $data["comment_id"], "<info>");
			successResponse('', 'El like se ha registrado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al dar like a comentario en Muro usuario " . $valida_token["user_id"] . " comentario " . $data["comment_id"], "<warning>");
			faildResponse('El like no ha registrado correctamente', $this);
		}
	}

	public function ObtenerCategorias()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$result = $this->posts->ObtenerCategorias();
		if ($result) {
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "yastas al dia");
			$this->general_mdl->writeLog("Listado de categorias de Muro usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Listado de categorias', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener categorias de Muro usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('Sin registros en categorias', $this);
		}
	}
}
