<?php
class Business extends CI_Controller
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
		$this->load->model('business_model', 'business');
		$this->load->model('user_model', 'user');
	}
	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 22/12/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para obtener las empresas
	 ***********************************************************************/
	public function BusinessList()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$business = $this->business->BusinessList(array());
		if ($business) {
			$this->general_mdl->writeLog("Listado de empresas usuario " . $valida_token["user_id"], "<info>");
			successResponse($business, 'Listado de usuarios', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener empresas usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros', $this);
		}
	}

	/***********************************************************************
	 *  Autor: Uriel Sánchez Cervantes   Fecha: 22/12/2020
	 *		   urisancer@gmail.com
	 *  Nota: Funcion para guardar las nuevas empresas
	 ***********************************************************************/
	function SaveBusiness()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('business_name', 'plan_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		$data = array(
			'business_name' => $this->input->post("business_name"),
			'plan_id' => $this->input->post("plan_id")
		);
		$bussiness_id = $this->business->SaveBusiness($data);
		if ($bussiness_id) {
			$this->general_mdl->writeLog("Registro de empresa usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'El puesto ha sido guardado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar empresa usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El puesto no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *  Autor: Uriel Sánchez Cervantes   Fecha: 22/12/2020
	 *		   urisancer@gmail.com
	 *  Nota: Funcion para editar los datos de la empresa
	 ***********************************************************************/
	function EditBusiness()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'business_name', "plan_id"), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a editar el puesto requerido
		$data = array(
			'business_name' => $this->input->post("business_name"),
			'plan_id' => $this->input->post("plan_id")
		);

		if ($this->business->EditBusiness($this->input->post("id"), $data)) {
			$this->general_mdl->writeLog("Actualizacion de empresa usuario " . $valida_token["user_id"] . " empresa " . $this->input->post("business_name"), "<info>");
			successResponse('', 'La categoría ha sido guardada correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al actualizar empresa usuario " . $valida_token["user_id"] . " empresa " . $this->input->post("business_name"), "<info>");
			faildResponse('La categoría no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *  Autor: Uriel Sánchez Cervantes   Fecha: 22/12/2020
	 *		   urisancer@gmail.com
	 *  Nota: Funcion para elminar un empresa
	 ***********************************************************************/
	function DeleteBusiness()
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
		$params = array(
			'business_id' => $valida_token['business_id']
		);
		$users = $this->user->UserList($params);
		if ($users) {
			faildResponse('La empresa tiene usuarios asociados.', $this);
		} else {
			//Se procede a eliminar el puesto requerido
			$data = array(
				"id" => $this->input->post("id")
			);
			if ($this->business->DeleteBusiness($data)) {
				$this->general_mdl->writeLog("Eliminacion de empresa usuario " . $valida_token["user_id"] . " empresa " . $this->input->post("id"), "<info>");
				successResponse('', 'El puesto ha sido eliminado correctamente.', $this);
			} else {
				$this->general_mdl->writeLog("Error al eliminar empresa usuario " . $valida_token["user_id"] . " empresa " . $this->input->post("id"), "<warning>");
				faildResponse('El puesto no se pudo eliminar.', $this);
			}
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 22/12/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para obtener las empresas
	 ***********************************************************************/
	public function PlansList()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$business = $this->business->PlansList();
		if ($business) {
			$this->general_mdl->writeLog("Listado de planes usuario " . $valida_token["user_id"], "<info>");
			successResponse($business, 'Listado de usuarios', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener planes usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros', $this);
		}
	}
}
