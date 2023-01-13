<?php
class Wall extends CI_Controller
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
		$this->load->model('wall_model', 'wall');
		$this->load->model('notification_mdl', 'notification');
		$this->load->model('business_model', 'business');
		$this->load->model('user_model', 'user');
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para guardar una publicación en el muro
	 ***********************************************************************/
	public function SavePost()
	{
		if ($this->input->post() == [])
		$_POST = json_decode(file_get_contents('php://input'), true);
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
		// echo json_encode($_FILES);
		// return;
		$image = '';
		if (isset($_FILES["image_path"])) {
			$archivos = $this->general_mdl->GuardarArchivos(["image_path" => $_FILES["image_path"]], $ruta = 'walls', $valida_token['business_id']);
			//Si la subida de archivos fue exitosa
			if ($archivos['success']) {
				//Si se subió el correctamente el archivo deseado ("image_path"), guardamos el nombre asignado
				if (isset($archivos['success_files']['image_path'])) {
					$image = $archivos['success_files']['image_path'];
				}
			} else {
				//si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
				faildResponse($archivos['msg'], $this);
				return;
			}
		}

		$preview = 'default.png';
		if (isset($_FILES["image_preview"])) {
			$preview_g = $this->general_mdl->GuardarArchivos(["image_preview" => $_FILES["image_preview"]], $ruta = "walls", $valida_token["business_id"]);
			if ($preview_g["success"]) {
				if (isset($preview_g["success_files"]["image_preview"])) {
					$preview = $preview_g["success_files"]["image_preview"];
				}
			} else {
				faildResponse($preview_g['msg'], $this);
				return;
			}
		}

		//Se procede a guardar la publicación
		$data = array(
			'business_id' => $valida_token['business_id'],
			'user_id' => $valida_token['user_id'],
			'wall_description' => $this->input->post("wall_description"),
			'redirect' => $this->input->post("redirect"),
			"tipo" => $this->input->post("tipo"),
			'image_path' => $image,
			'image_preview' => $preview,
			"es_noticia" => 1
		);

		$data["wall_description"] = str_replace('Powered by <a href="https://www.froala.com/wysiwyg-editor?pb=1" title="Froala Editor">Froala Editor</a>',"",$data["wall_description"]);

		$data["wall_description"] = preg_replace(
			'!(http|ftp|scp)(s)?:\/\/[a-zA-Z0-9.?%=&_/]+!',
			"<a href=\"\\0\" target='_blank'>\\0</a>",
			$data["wall_description"]
		);

		$usuarios = json_decode($this->input->post("usuarios"), true);
        $grupos = json_decode($this->input->post("grupos"), true);
		// echo json_encode($data);
		// return;
		$resultado = $this->wall->SavePost($data);
		if ($resultado) {

			$this->wall->agregarUsuarios($resultado, $usuarios);
            $this->wall->agregarGrupos($resultado, $grupos);
			$this->enviarNotificacion($valida_token["user_id"], "Se ha agregado una nueva publicación", $valida_token["business_id"]);
			/***********************************************************************
			 *	Nota: Se obtiene los tokens existentes en la BD
			 ***********************************************************************/

			$this->general_mdl->writeLog("Registro de post usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'La publicación ha sido guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guradar post usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('La publicación no se pudo guardar.', $this);
		}
	}

	function enviarNotificacion($user_id, $mensaje, $business_id)
	{
		$tokens = $this->notification->ListUserNotification($business_id);
		if ($tokens) {
			$tokens_ = array();
			foreach ($tokens as $index => $value) {
				array_push($tokens_, $value['token']);
				$data = array('title' => 'Yastás al día', 'notification' => $mensaje, 'user_id' => $value['user_id'], 'service_id' => SERVICE_WALL, 'user_create_id' => $user_id);
				$this->notification->RegisterNotification($data);
			}
			$this->general_mdl->EnviarNotificacionPush($tokens_, $mensaje, 'Yastás al día', SERVICE_WALL);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar una publicación
	 ***********************************************************************/
	public function EditPost()
	{
		if ($this->input->post() == [])
		$_POST = json_decode(file_get_contents('php://input'), true);
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'wall_description'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		$image = '';
		if (isset($_FILES["image_path"])) {
			$archivos = $this->general_mdl->GuardarArchivos(["image_path" => $_FILES["image_path"]], $ruta = 'walls', $valida_token['business_id']);
			//Si la subida de archivos fue exitosa
			if ($archivos['success']) {
				//Si se subió el correctamente el archivo deseado ("media_path"), guardamos el nombre asignado
				if (isset($archivos['success_files']['image_path'])) {
					$image = $archivos['success_files']['image_path'];
				}
			} else {
				//si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
				faildResponse($archivos['msg'], $this);
				return;
			}
		}

		$preview = 'default.png';
		if (isset($_FILES["image_preview"])) {
			$preview_g = $this->general_mdl->GuardarArchivos(["image_preview" => $_FILES["image_preview"]], $ruta = "walls", $valida_token["business_id"]);
			if ($preview_g["success"]) {
				if (isset($preview_g["success_files"]["image_preview"])) {
					$preview = $preview_g["success_files"]["image_preview"];
				}
			} else {
				faildResponse($preview_g['msg'], $this);
				return;
			}
		}

		//Se procede a editar la publicación
		$data = array(
			'id' => $this->input->post("id"),
			'wall_description' => $this->input->post("wall_description"),
			'image_path' => $image,
			'image_preview' => $preview
		);
		if ($this->wall->EditPost($data)) {
			$this->general_mdl->writeLog("Actualizacion de post usuario " . $valida_token["user_id"] . " post " . $this->input->post("id"), "<info>");
			successResponse('', 'La publicación ha sido guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al actualizar post usuario " . $valida_token["user_id"] . " post " . $this->input->post("id"), "<warning>");
			faildResponse('La publicación no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para eliminar una publicación
	 ***********************************************************************/
	public function DeletePost()
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

		//Se procede a eliminar la publicación
		$data = array(
			"id" => $this->input->post("id"),
			"active" => $this->input->post("active")
		);
		if ($this->wall->DeletePost($data)) {
			$this->general_mdl->writeLog("Eliminacion de post usuario " . $valida_token["user_id"] . " post " . $this->input->post("id"), "<info>");
			successResponse('', 'La publicación ha sido eliminada correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar post " . $valida_token["user_id"] . " post " . $this->input->post("id"), "<warning>");
			faildResponse('La publicación no se pudo eliminar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener las publicaciones
	 ***********************************************************************/
	public function Posts()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$filtro = $this->input->post('filtro');
		$result = $this->wall->Posts($valida_token['business_id'], $filtro);
		if ($result) {
			$this->general_mdl->writeLog("Listado de post usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Lista de publicaciones', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener post usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('Nos existen publicaciones', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para guardar un comentario
	 ***********************************************************************/
	public function SaveComment()
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

		//Se procede a guardar un comentario
		$data = array(
			'business_id' => $valida_token['business_id'],
			'user_id' => $valida_token['user_id'],
			'comment' => $this->input->post("comment"),
			'post_id' => $this->input->post("post_id")
		);
		if ($this->wall->SaveComment($data)) {
			$this->general_mdl->writeLog("Registro de comentario en post usuario " . $valida_token["user_id"] . " post " . $this->input->post("post_id"), "<info>");
			successResponse('', 'El comentario ha sido guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar comentario en post usuario " . $valida_token["user_id"] . " post " . $this->input->post("post_id"), "<warning>");
			faildResponse('El comentario no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar un comentario
	 ***********************************************************************/
	public function EditComment()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'comment'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a editar el comentario
		$data = array(
			'id' => $this->input->post("id"),
			'comment' => $this->input->post("comment")
		);
		if ($this->wall->EditComment($data)) {
			$this->general_mdl->writeLog("Actualizacion de comentario usuario " . $valida_token["user_id"] . " comentario " . $this->input->post("id"), "<info>");
			successResponse('', 'El comentario sido guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al actualizar comentario usuario " . $valida_token["user_id"] . " comentario " . $this->input->post("id"), "<warning>");
			faildResponse('El comentario no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para eliminar un comentario
	 ***********************************************************************/
	public function DeleteComment()
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

		//Se procede a eliminar el comentario
		$data = array(
			"id" => $this->input->post("id"),
		);
		if ($this->wall->DeleteComment($data)) {
			$this->general_mdl->writeLog("Eliminacion de comentario usuario " . $valida_token["user_id"] . " comentario " . $data["id"], "<info>");
			successResponse('', 'El comentario ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar el comentario usuario " . $valida_token["user_id"] . " comentario " . $data["id"], "<warning>");
			faildResponse('El comentario no se pudo eliminar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para listar los comentarios de una publicación
	 ***********************************************************************/
	public function Comments()
	{
		if ($this->input->post() == [])
		$_POST = json_decode(file_get_contents('php://input'), true);
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('wall_post_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a guardar el comentario
		$data = array(
			'business_id' => $valida_token['business_id'],
			'wall_post_id' => $this->input->post("wall_post_id")
		);

		$result = $this->wall->Comments($data);
		if ($result) {
			$this->general_mdl->writeLog("Listado de comentarios usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Listado de comentarios', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener comentarios usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen comentarios', $this);
		}
	}
}
