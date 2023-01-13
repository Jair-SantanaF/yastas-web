<?php
class Events extends CI_Controller
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
		$this->load->model('events_mdl', 'events');
		$this->load->model('user_model', 'user');
		$this->load->model('notification_mdl', 'notification');
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para guardar un nuevo evento
	 ***********************************************************************/
	public function SaveEventAdmin()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('description', 'note', 'date', 'time_start', 'time_end'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a guardar el evento
		$data = array(
			'business_id' => $valida_token['business_id'],
			'user_id' => $valida_token['user_id'],
			'description' => $this->input->post("description"),
			'note' => $this->input->post("note"),
			'date' => $this->input->post("date"),
			'time_start' => $this->input->post("time_start"),
			'time_end' => $this->input->post("time_end")
		);
		$save = $this->events->SaveEventAdmin($data);
		if ($save) {
			$this->general_mdl->writeLog("Registro de evento usuario " . $valida_token["user_id"], "<info>");
			successResponse($save, 'El evento ha sido guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar evento usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El evento no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar un evento
	 ***********************************************************************/
	public function EditEvent()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'description', 'note', 'date', 'time_start', 'time_end'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a editar el evento
		$data = array(
			'id' => $this->input->post("id"),
			'description' => $this->input->post("description"),
			'note' => $this->input->post("note"),
			'date' => $this->input->post("date"),
			'time_start' => $this->input->post("time_start"),
			'time_end' => $this->input->post("time_end"),
			'members' => $this->input->post("members")
		);
		if ($this->events->EditEvent($data)) {
			$this->general_mdl->writeLog("Actualizacion de evento usuario " . $valida_token["user_id"] . " evento " . $this->input->post("id"), "<info>");
			successResponse('', 'El evento ha sido guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al actualizar evento usuario " . $valida_token["user_id"] . " evento " . $this->input->post("id"), "<warning>");
			faildResponse('El evento no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para eliminar un evento
	 ***********************************************************************/
	public function DeleteEvent()
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

		//Se procede a eliminar el evento
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->events->DeleteEvent($data)) {
			$this->general_mdl->writeLog("Eliminacion de evento usuario " . $valida_token["user_id"] . " evento " . $this->input->post("id"), "<info>");
			successResponse('', 'El evento ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar evento usuario " . $valida_token["user_id"] . " evento " . $this->input->post("id"), "<warning>");
			faildResponse('El evento no se pudo eliminar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para optener los eventos
	 ***********************************************************************/
	public function Events()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$date = ($this->input->post("date")) ? $this->input->post("date") : '';
		$result = $this->events->Events($valida_token['business_id'], $valida_token['user_id'], $valida_token['rol_id'], $date);
		if ($result) {
			$this->general_mdl->writeLog("Listado de eventos usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Lista de quiz de perfilador', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener eventos usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('Nos existen quiz registrados', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para guardar un conjunto de usuarios como miembros del evento
	 ***********************************************************************/
	public function SaveMembers()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('event_id', 'usuarios'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		$usuarios = $this->input->post("usuarios");
		$usuarios = json_decode($usuarios);

		//Guardar uno por uno
		foreach ($usuarios as $index => $value) {
			$guardado = $this->events->SaveMember(
				array(
					'event_id' => $this->input->post("event_id"),
					'user_id' => $value
				)
			);
			/***********************************************************************
			 *	Nota: Se obtiene los tokens existentes en la BD
			 ***********************************************************************/
			$tokens = $this->notification->ListUserNotification($valida_token['business_id'], $value);
			if ($tokens) {
				$tokens_ = array();
				foreach ($tokens as $index_token => $value_token) {
					array_push($tokens_, $value_token['token']);
					/***********************************************************************
					 *	Autor: Mario Adrián Martínez Fernández   Fecha: 09/07/2020
					 *		   mario.martinez.f@hotmail.es
					 *	Nota: Guardarmos a los usuarios que se les enviara la notificacion.
					 ***********************************************************************/
					$data = array('title' => 'Agenda', 'notification' => 'Se te ha invitado a un nuevo evento.', 'user_id' => $value_token['user_id'], 'service_id' => SERVICE_EVENTS, 'user_create_id' => $valida_token['user_id']);
					$this->notification->RegisterNotification($data);
				}
				/***********************************************************************
				 *	Nota: Se envia notificacion a los multiples tokens
				 ***********************************************************************/
				$this->general_mdl->EnviarNotificacionPush($tokens_, 'Se te ha invitado a un nuevo evento.', 'Agenda', SERVICE_EVENTS);
			}
			if (!$guardado) {
				$this->general_mdl->writeLog("Error al guardar miembros al evento usuario " . $valida_token["user_id"] . " evento " . $this->input->post("event_id"), "<warning>");
				faildResponse('Los miembros no se pudieron guardar.', $this);
				return;
			}
		}
		$this->general_mdl->writeLog("Registro de miembros a evento usuario " . $valida_token["user_id"] . " evento " . $this->input->post("event_id"), "<info>");
		successResponse('', 'Los miembros han sido guardado correctamente', $this);
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para eliminar un miembro del evento
	 ***********************************************************************/
	public function DeleteMember()
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
		if ($this->events->DeleteMember($data)) {
			$this->general_mdl->writeLog("Elminacion de miembro de evento usuario " . $valida_token["user_id"] . " miembro " . $this->input->post("id"), "<info>");
			successResponse('', 'El miembro ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar miembro de evento usuario " . $valida_token["user_id"] . " miembro " . $this->input->post("id"), "<warning>");
			faildResponse('El miembro no se pudo eliminar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para listar los miembros de un evento dado
	 ***********************************************************************/
	public function Members()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('event_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a guardar la pregunta
		$data = array(
			'business_id' => $valida_token['business_id'],
			'event_id' => $this->input->post("event_id")
		);

		$result = $this->events->Members($data);
		if ($result) {
			$this->general_mdl->writeLog("Listado de miembros de evento usuario " . $valida_token["user_id"] . " evento " . $this->input->post("event_id"), "<info>");
			successResponse($result, 'Listado de miembros', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener miembros de evento usuario " . $valida_token["user_id"] . " evento " . $this->input->post("event_id"), "<warning>");
			faildResponse('No existen miembros', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para listar los usuarios disponibles para convertirse en miembros
	 ***********************************************************************/
	public function NoMembers()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('event_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		$data = array(
			'business_id' => $valida_token['business_id'],
			'event_id' => $this->input->post("event_id")
		);

		$result = $this->events->NoMembers($data);
		if ($result) {
			$this->general_mdl->writeLog("Listado de usuarios no miembros de evento usuario " . $valida_token["user_id"], "<info>");
			successResponse($result, 'Listado de usuarios disponibles', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener usuarios no miembros de evento usuario " . $valida_token["user_id"], "<info>");
			faildResponse('No existen usuarios', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 17/06/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para guardar un evento
	 ***********************************************************************/
	public function SaveEvent()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('description', 'date'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['user_id'] = $valida_token['user_id'];
		$data['business_id'] = $valida_token['business_id'];
		$saveEvent = $this->events->SaveEvent($data);
		if ($saveEvent) {
			$members = (isset($data['members'])) ? $data['members'] : '';
			if ($members !== '') {
				$members = explode(',', $members);
				foreach ($members as $index => $value) {
					/***********************************************************************
					 *	Nota: Se obtiene los tokens existentes en la BD
					 ***********************************************************************/
					$tokens = $this->notification->ListUserNotification($valida_token['business_id'], $value);
					if ($tokens) {
						$tokens_ = array();
						foreach ($tokens as $index_token => $value_token) {
							array_push($tokens_, $value_token['token']);
							/***********************************************************************
							 *	Autor: Mario Adrián Martínez Fernández   Fecha: 09/07/2020
							 *		   mario.martinez.f@hotmail.es
							 *	Nota: Guardarmos a los usuarios que se les enviara la notificacion.
							 ***********************************************************************/
							$data = array('title' => 'Agenda', 'notification' => 'Se te ha invitado a un nuevo evento.', 'user_id' => $value_token['user_id'], 'service_id' => SERVICE_EVENTS, 'user_create_id' => $valida_token['user_id']);
							$this->notification->RegisterNotification($data);
						}
						/***********************************************************************
						 *	Nota: Se envia notificacion a los multiples tokens
						 ***********************************************************************/
						$this->general_mdl->EnviarNotificacionPush($tokens_, 'Se te ha invitado a un nuevo evento.', 'Agenda', SERVICE_EVENTS);
					}
				}
			}
			$this->general_mdl->writeLog("Registro de evento usuario " . $valida_token["user_id"], "<info>");
			successResponse($saveEvent, 'El evento se ha creado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar evento usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El evento no se ha creado correctamente', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 17/06/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los eventos por una fecha en especifico
	 ***********************************************************************/
	public function ListEvents()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('date'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['business_id'] = $valida_token['business_id'];
		$data['user_id'] = $valida_token['user_id'];
		$listEvent = $this->events->ListEvents($data);
		if ($listEvent) {
			$this->general_mdl->writeLog("Listado de eventos usuario " . $valida_token["user_id"], "<info>");
			successResponse($listEvent, 'Listado de eventos', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener eventos usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existes registros', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/06/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener las fechas de que contienen eventos
	 ***********************************************************************/
	public function listDateEvents()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$listEvent = $this->events->listDateEvents($valida_token['business_id'], $valida_token['user_id'], $valida_token['rol_id']);
		if ($listEvent) {
			$this->general_mdl->writeLog("Listado de fechas con eventos usuario " . $valida_token["user_id"], "<info>");
			successResponse($listEvent, 'Listado de eventos', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener fechas con eventos usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existes registros', $this);
		}
	}
}


