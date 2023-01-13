<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notificaction extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("America/Mexico_City");
		$this->load->model('notification_mdl', 'notification');
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 09/07/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener las notificaciones que no hemos leido
	 ***********************************************************************/
	public function ListNotifications(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$list = $this->notification->ListNotifications($valida_token['user_id']);
		if($list){
			successResponse($list,'Listado de notificaciones',$this);
		}else{
			faildResponse('No existen notificaciones',$this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 09/07/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para marcar una notificacion como vista
	 ***********************************************************************/
	public function NotificationView(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('notification_id'), $this->input->post());
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}
		$list = $this->notification->NotificationView($this->input->post('notification_id'));
		if($list){
			successResponse(array(),'La notificacion ha sido actualizada',$this);
		}else{
			faildResponse('No existen notificaciones',$this);
		}
	}
}
