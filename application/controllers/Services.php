<?php
class Services extends CI_Controller
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
		$this->load->model('services_model', 'service');
	}
	/***********************************************************************
	 *    Autor: Uriel Sánchez Cervantes   Fecha: 07/01/2021
	 *           urisancer@gmail.com
	 *    Nota: Listado de servicios contratados
	 ***********************************************************************/
	public function HiredServices()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('business_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$params = array(
			"business_id" => $this->input->post("business_id")
		);
		$users = $this->service->HiredServices($params);
		if ($users) {
			$this->general_mdl->writeLog("Listado de servicios contratados usuario " . $valida_token["user_id"], "<info>");
			successResponse($users, 'Listado de servicios contratados', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener servicios contratados usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros', $this);
		}
	}

	/***********************************************************************
	 *    Autor: Uriel Sánchez Cervantes   Fecha: 07/01/2021
	 *           urisancer@gmail.com
	 *    Nota: Eliminar registro de servicio contratado
	 ***********************************************************************/
	public function DeleteHiredService()
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

		//Se procede a eliminar el servicio requerido
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->service->DeleteHiredService($data)) {
			$this->general_mdl->writeLog("Eliminacion de servicio contratado usuario " . $valida_token["user_id"] . " servicio " . $data["id"], "<info>");
			successResponse('', 'El servicio ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar servicio contratado usuario " . $valida_token["user_id"] . " servicio " . $data["id"], "<warning>");
			faildResponse('El servicio no se pudo eliminar.', $this);
		}
	}
	/***********************************************************************
	 *    Autor: Uriel Sánchez Cervantes   Fecha: 11/12/2020
	 *           urisancer@gmail.com
	 *    Nota: Listado de servicios solicitados
	 ***********************************************************************/
	public function PurchaseServicesList()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$users = $this->service->PurchaseServicesList($valida_token['business_id']);
		if ($users) {
			$this->general_mdl->writeLog("Listado de servicios solicitados usuario " . $valida_token["user_id"], "<info>");
			successResponse($users, 'Listado de usuarios', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener servicios solicitados usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros', $this);
		}
	}

	/***********************************************************************
	 *    Autor: Uriel Sánchez Cervantes   Fecha: 11/12/2020
	 *           urisancer@gmail.com
	 *    Nota: Aprobar servicio solicitado
	 ***********************************************************************/
	public function ApprovePurchaseService()
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

		//Se procede a eliminar el invitado requerido
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->service->ApprovePurchaseService($data)) {
			$this->general_mdl->writeLog("Aprovacion de servicio solicitado usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'El usuario ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al aprobar servicio solicitado usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El servicio no se pudo aprobar.', $this);
		}
	}

	/***********************************************************************
	 *    Autor: Uriel Sánchez Cervantes   Fecha: 11/12/2020
	 *           urisancer@gmail.com
	 *    Nota: Eliminar registro de servicio solicitado
	 ***********************************************************************/
	public function DeletePurchaseService()
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

		//Se procede a eliminar el invitado requerido
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->service->DeletePurchaseService($data)) {
			$this->general_mdl->writeLog("Eliminacion de servicio solicitado usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'El usuario ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar servicio solicitado usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El servicio no se pudo eliminar.', $this);
		}
	}
}
