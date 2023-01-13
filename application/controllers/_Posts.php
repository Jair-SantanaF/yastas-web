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

	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para guardar un post nuevo.
	 ***********************************************************************/
	public function SaveWall(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('wall_description'), $this->input->post());
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}
		$data = $this->input->post();
		$data['user_id'] = $valida_token['user_id'];
		$data['business_id'] = $valida_token['business_id'];
		$archivos = $this->general_mdl->GuardarArchivos($_FILES, $ruta = 'walls', $valida_token['business_id']);
		//Si la subida de archivos fue exitosa
		if($archivos['success']){
			//Si se subió el correctamente el archivo deseado ("image"), guardamos el nombre asignado
			if(isset($archivos['success_files']['image'])){
				$data['image_path'] = $archivos['success_files']['image'];
			}
		}else{
			//si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
			faildResponse($archivos['msg'], $this);
			return;
		}
		if($this->posts->SaveWall($data)){
			successResponse('','El post ha sido guardado correctamente',$this);
		}else{
			faildResponse('El post no se ha registrado correctamente.',$this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los posts generados para una empresa
	 ***********************************************************************/
	public function WallsList(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$data = $this->posts->WallsList($valida_token['business_id'],$valida_token['user_id']);
		if($data){
			successResponse($data,'Listado de post registrados',$this);
		}else{
			faildResponse('No existen registros.',$this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para registrar un comentario a un post
	 ***********************************************************************/
	public function SaveWallComment(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('comment','post_id'), $this->input->post());
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}
		$data = $this->input->post();
		$data['user_id'] = $valida_token['user_id'];
		if($this->posts->SaveWallComment($data)){
			successResponse('','El comentario se ha registrado correctamente',$this);
		}else{
			faildResponse('El comentario no ha registrado correctamente',$this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtner el listado de comentarios
	 ***********************************************************************/
	public function ListCommentsPost(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('post_id'), $this->input->post());
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}
		$data = $this->posts->ListCommentsPost($this->input->post('post_id'));
		if($data){
			successResponse($data,'Listado de comentarios registrados',$this);
		}else{
			faildResponse('No existen registros.',$this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: funcion para registrar un like a post en especifico
	 ***********************************************************************/
	public function SaveLikePost(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('post_id'), $this->input->post());
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}
		$data = $this->input->post();
		$data['user_id'] = $valida_token['user_id'];
		if($this->posts->SaveLikePost($data)){
			successResponse('','El like se ha registrado correctamente',$this);
		}else{
			faildResponse('El like no ha registrado correctamente',$this);
		}
	}
}
