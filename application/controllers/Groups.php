<?php
class Groups extends CI_Controller
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
		$this->load->model('groups_mdl', 'groups');
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para guardar un grupo nuevo
	 ***********************************************************************/
	public function SaveGroup()
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

		//Se procede a guardar el area requerida
		$data = $this->input->post();
		$data['business_id'] = $valida_token['business_id'];
		if ($this->groups->SaveGroup($data)) {
			$this->general_mdl->writeLog("Registro de nuevo grupo usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'El grupo se ha guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al crear grupo usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El grupo no se pudo guardar.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para enviar la edicion de un grupo
	 ***********************************************************************/
	public function EditGroup()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('name', 'id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a guardar el area requerida
		$data = $this->input->post();
		if ($this->groups->EditGroup($data)) {
			$this->general_mdl->writeLog("Actualizacion de grupo usuario " . $valida_token["user_id"] . " grupo " . $data["id"], "<info>");
			successResponse('', 'El grupo se ha actualizado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Actualizacion de grupo usuario " . $valida_token["user_id"] . " grupo " . $data["id"], "<warning>");
			faildResponse('El grupo no se pudo actualizar.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para eliminar un grupo (Se cambia el campo active a 0)
	 ***********************************************************************/
	public function DeleteGroup()
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
		$validaPost = $this->general_mdl->validapost(array('id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a guardar el area requerida
		$data = $this->input->post();
		if ($this->groups->DeleteGroup($data)) {
			$this->general_mdl->writeLog("Eliminacion de grupo usuario " . $valida_token["user_id"] . " grupo " . $data["id"], "<info>");
			successResponse('', 'El grupo se ha eliminado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar grupo usuario " . $valida_token["user_id"] . " grupo " . $data["id"], "<warning>");
			faildResponse('El grupo no se pudo eliminar.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener el listado de un grupo
	 ***********************************************************************/
	public function GroupsRegister()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		//Se procede a guardar el area requerida
		$id = ($this->input->post('id')) ? $this->input->post('id') : '';
		$data = $this->groups->GroupsRegister($valida_token['business_id'], $id);
		if ($data) {
			$this->general_mdl->writeLog("Listado de grupos usuario " . $valida_token["user_id"], "<info>");
			successResponse($data, 'Listado de grupos registrados', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener grupos usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los registros de usuarios que pertenecen
	 * 			a un grupo
	 ***********************************************************************/
	public function UsersGroups()
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
		$validaPost = $this->general_mdl->validapost(array('group_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		//Se procede a guardar el area requerida
		$data = $this->input->post();
		$data["business_id"] = $valida_token["business_id"];
		$data = $this->groups->UsersGroups($data);
		if ($data) {
			$this->general_mdl->writeLog("Listado de usuarios de grupo usuario " . $valida_token["user_id"] . " grupo " . $this->input->post("group_id"), "<info>");
			successResponse($data, 'Listado de usuarios de un grupo registrados', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener usuarios de grupo usuario " . $valida_token["user_id"] . " grupo " . $this->input->post("group_id"), "<warning>");
			faildResponse('No existen registros.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los miembros que no han sido seleccionados
	 * 			en el grupo enviado
	 ***********************************************************************/
	public function NoUsersGroups()
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
		$validaPost = $this->general_mdl->validapost(array('group_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		//Se procede a guardar el area requerida
		$data = $this->input->post();
		$data['business_id'] = $valida_token['business_id'];
		$data = $this->groups->NoUsersGroups($data);
		if ($data) {
			$this->general_mdl->writeLog("Listado de usuarios en grupo usuario " . $valida_token["user_id"], "<info>");
			successResponse($data, 'Listado de usuarios de un grupo registrados', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener usuarios de grupo usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: funcion para registrar a un usuario a un grupo
	 ***********************************************************************/
	public function SaverUser()
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
		$validaPost = $this->general_mdl->validapost(array('group_id', 'users'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a guardar el area requerida
		$users = $this->input->post("users");
		$users = json_decode($users);

		//Guardar uno por uno
		foreach ($users as $index => $value) {
			$guardado = $this->groups->SaverUser(
				array(
					'group_id' => $this->input->post("group_id"),
					'user_id' => $value
				)
			);
			if (!$guardado) {
				$this->general_mdl->writeLog("Error al guardar usuarios en el grupo usuario " . $valida_token["user_id"] . " grupo " . $this->input->post("group_id"), "<warning>");
				faildResponse('Los usuarios no se pudieron guardar.', $this);
				return;
			}
		}
		$this->general_mdl->writeLog("Registro de usuarios al grupo usuario " . $valida_token["user_id"] . " grupo " . $this->input->post("group_id"), "<info>");
		successResponse('', 'Los usuarios han sido guardado correctamente', $this);
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para eliminar un usuario de un grupo
	 ***********************************************************************/
	public function DeleteUser()
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
		$validaPost = $this->general_mdl->validapost(array('id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a eliminar el miembro
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->groups->DeleteUser($data)) {
			$this->general_mdl->writeLog("Eliminacion de usuario en grupo usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'El usuario ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar usuario de grupo usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El usuario no se pudo eliminar.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los registros de biblioteca que pertenecen
	 * 			a un grupo
	 ***********************************************************************/
	public function LibraryGroups()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('group_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		//Se procede a guardar el area requerida
		$data = $this->input->post();
		$data["business_id"] = $valida_token["business_id"];
		$data = $this->groups->LibraryGroups($data);
		//if($data){
		$this->general_mdl->writeLog("Listado de biblioteca de un grupo usuario " . $valida_token["user_id"], "<info>");
		successResponse($data, 'Listado de biblioteca de un grupo registrados', $this);
		//}else{
		//	faildResponse('No existen registros.',$this);
		//}
	}

	public function PodcastGroups()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		// echo json_encode($this->input->post());
		$validaPost = $this->general_mdl->validapost(array('group_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		//Se procede a guardar el area requerida
		$data = $this->groups->PodcastGroups($this->input->post());
		//if($data){
		$this->general_mdl->writeLog("Listado de podcast de un grupo usuario " . $valida_token["user_id"], "<info>");
		successResponse($data, 'Listado de biblioteca de un grupo registrados', $this);
		//}else{
		//	faildResponse('No existen registros.',$this);
		//}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los elementos que no han sido seleccionados
	 * 			en el grupo enviado
	 ***********************************************************************/
	public function NoLibraryGroups()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('group_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		//Se procede a guardar el area requerida
		$data = $this->input->post();
		$data['business_id'] = $valida_token['business_id'];
		$data = $this->groups->NoLibraryGroups($data);
		if ($data) {
			$this->general_mdl->writeLog("Listado de biblioteca de grupo usuario " . $valida_token["user_id"], "<info>");
			successResponse($data, 'Listado de biblioteca de un grupo registrados', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener biblioteca de grupo usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros.', $this);
		}
	}

	public function NoPodcastGroups()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('group_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		//Se procede a guardar el area requerida
		$data = $this->input->post();
		$data['business_id'] = $valida_token['business_id'];
		$data = $this->groups->NoPodcastGroups($data);
		if ($data) {
			$this->general_mdl->writeLog("Listade de podcast de grupo usuario " . $valida_token["user_id"], "<info>");
			successResponse($data, 'Listado de podcast de un grupo registrados', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener podcast de grupo usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: funcion para registrar a un elemento de biblioteca a un grupo
	 ***********************************************************************/
	public function SaveLibrary()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('group_id', 'elements'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a guardar el area requerida
		$elements = $this->input->post("elements");
		$elements = json_decode($elements);

		//Guardar uno por uno
		foreach ($elements as $index => $value) {
			$guardado = $this->groups->SaveLibrary(
				array(
					'group_id' => $this->input->post("group_id"),
					'library_id' => $value
				)
			);
			if (!$guardado) {
				$this->general_mdl->writeLog("Error al guardar elemento biblioteca usuario " . $valida_token["user_id"], "<warning>");
				faildResponse('Los elementos no se pudieron guardar.', $this);
				return;
			}
		}
		$this->general_mdl->writeLog("Registro de elemento biblioteca usuario " . $valida_token["user_id"], "<info>");
		successResponse('', 'Los elementos han sido guardado correctamente', $this);
	}

	public function SavePodcast()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('group_id', 'elements'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a guardar el area requerida
		$elements = $this->input->post("elements");
		$elements = json_decode($elements);
		//Guardar uno por uno
		foreach ($elements as $index => $value) {
			$guardado = $this->groups->SavePodcast(
				array(
					'group_id' => $this->input->post("group_id"),
					'podcast_id' => $value
				)
			);
			if (!$guardado) {
				$this->general_mdl->writeLog("Error al guardar elemento biblioteca usuario " . $valida_token["user_id"], "<warning>");
				faildResponse('Los elementos no se pudieron guardar.', $this);
				return;
			}
		}
		$this->general_mdl->writeLog("Registro de elemento biblioteca usuario " . $valida_token["user_id"], "<info>");
		successResponse('', 'Los elementos han sido guardado correctamente', $this);
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para eliminar un elemento de biblioteca de un grupo
	 ***********************************************************************/
	public function DeleteLibrary()
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

		//Se procede a eliminar el miembro
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->groups->DeleteLibrary($data)) {
			$this->general_mdl->writeLog("Eliminacion de elemento biblioteca usuario " . $valida_token["user_id"] . " elemento " . $data["id"], "<info>");
			successResponse('', 'El elemento ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar elemento biblioteca usuario " . $valida_token["user_id"] . " elemento " . $data["id"], "<warning>");
			faildResponse('El elemento no se pudo eliminar.', $this);
		}
	}

	public function DeletePodcast()
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

		//Se procede a eliminar el miembro
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->groups->DeletePodcast($data)) {
			$this->general_mdl->writeLog("Eliminacion de elemento biblioteca usuario " . $valida_token["user_id"] . " elemento " . $data["id"], "<info>");
			successResponse('', 'El elemento ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar elemento biblioteca usuario " . $valida_token["user_id"] . " elemento " . $data["id"], "<warning>");
			faildResponse('El elemento no se pudo eliminar.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los registros de elearning que pertenecen
	 * 			a un grupo
	 ***********************************************************************/
	public function ElearningGroups()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('group_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		//Se procede a guardar el area requerida
		$data = $this->groups->ElearningGroups($this->input->post());
		if ($data) {
			$this->general_mdl->writeLog("Listado de elearning de grupo usuario " . $valida_token["user_id"] . " grupo " . $this->input->post("group_id"), "<info>");
			successResponse($data, 'Listado de elearning de un grupo registrados', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener elearning de grupo usuario " . $valida_token["user_id"] . " grupo " . $this->input->post("group_id"), "<warning>");
			faildResponse('No existen registros.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los elearning que no han sido seleccionados
	 * 			en el grupo enviado
	 ***********************************************************************/
	public function NoElearningGroups()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('group_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		//Se procede a guardar el area requerida
		$data = $this->input->post();
		$data['business_id'] = $valida_token['business_id'];
		$data = $this->groups->NoElearningGroups($data);
		if ($data) {
			$this->general_mdl->writeLog("Listado de elearning de grupo usuario " . $valida_token["user_id"] . " grupo " . $this->input->post("group_id"), "<info>");
			successResponse($data, 'Listado de elearning de un grupo registrados', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener elearning de grupo usuario " . $valida_token["user_id"] . " grupo " . $this->input->post("group_id"), "<warning>");
			faildResponse('No existen registros.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: funcion para registrar a un elearning a un grupo
	 ***********************************************************************/
	public function SaveElearning()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('group_id', 'elements'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a guardar el area requerida
		$elements = $this->input->post("elements");
		$elements = json_decode($elements);

		//Guardar uno por uno
		foreach ($elements as $index => $value) {
			$guardado = $this->groups->SaveElearning(
				array(
					'group_id' => $this->input->post("group_id"),
					'elearning_id' => $value
				)
			);
			if (!$guardado) {
				$this->general_mdl->writeLog("Error al registrar elearning a grupo usuario " . $valida_token["user_id"] . " grupo " . $this->input->post("group_id"), "<warning>");
				faildResponse('Los elearning no se pudieron guardar.', $this);
				return;
			}
		}
		$this->general_mdl->writeLog("Registro de elearning a grupo usuario " . $valida_token["user_id"] . " grupo " . $this->input->post("group_id"), "<info>");
		successResponse('', 'Los elearning han sido guardado correctamente', $this);
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para eliminar un elemento de elearning de un grupo
	 ***********************************************************************/
	public function DeleteElearning()
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

		//Se procede a eliminar el miembro
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->groups->DeleteElearning($data)) {
			$this->general_mdl->writeLog("Eliminacion de elearning en grupo usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("id"), "<info>");
			successResponse('', 'El elearning ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar elearning de grupo usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("id"), "<warning>");
			faildResponse('El elearning no se pudo eliminar.', $this);
		}
	}

	public function desasociar_grupo()
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

		$catalogo = $this->input->post("catalogo");
		$id_elemento = $this->input->post("id_elemento");
		$id_grupo = $this->input->post("id_grupo");

		if ($this->groups->desasociar_grupo($catalogo, $id_elemento, $id_grupo)) {
			$this->general_mdl->writeLog("Eliminacion de grupo en " . $catalogo . " usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("id"), "<info>");
			successResponse('', 'El grupo ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar grupo en $catalogo usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("id"), "<warning>");
			faildResponse('El grupo no se pudo eliminar.', $this);
		}
	}

	public function asociar_grupo()
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

		$catalogo = $this->input->post("catalogo");
		$id_elemento = $this->input->post("id_elemento");
		$id_grupo = $this->input->post("id_grupo");

		if ($this->groups->asociar_grupo($catalogo, $id_elemento, $id_grupo)) {
			$this->general_mdl->writeLog("Asociacion de grupo en " . $catalogo . " usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("id"), "<info>");
			successResponse('', 'El grupo ha sido asociado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al asociar grupo en $catalogo usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("id"), "<warning>");
			faildResponse('El grupo no se pudo asociar.', $this);
		}
	}

	public function actualizar_grupos_elementos()
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

		$catalogo = $this->input->post("catalogo");
		$grupos = json_decode($this->input->post("grupos"), true);
		$id_elemento = $this->input->post("id_elemento");

		if ($this->groups->actualizar_grupos_elementos($catalogo, $grupos, $id_elemento)) {
			$this->general_mdl->writeLog("Asociacion de grupo en " . $catalogo . " usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("id"), "<info>");
			successResponse('', 'El grupo ha sido asociado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al asociar grupo en $catalogo usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("id"), "<warning>");
			faildResponse('El grupo no se pudo asociar.', $this);
		}
	}
}
