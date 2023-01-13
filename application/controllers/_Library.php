<?php
class Library extends CI_Controller
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
		$this->load->model('library_mdl', 'library');
		$this->load->model('notification_mdl', 'notification');

	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para guardar una catgoría de elementos de la biblioteca
	 ***********************************************************************/
	public function SaveSubcategory(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('subcategory','category_id'), $this->input->post());
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}

		//Se procede a guardar el area requerida
		$data = array(
			'business_id' => $valida_token['business_id'],
			'subcategory' => $this->input->post("subcategory"),
			'category_id' => $this->input->post("category_id")
		);
		if($this->library->SaveSubcategory($data)){
			successResponse('','La subcategoria ha sido guardada correctamente.',$this);
		}else{
			faildResponse('La subcategoria no se pudo guardar.',$this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar una catgoría de elementos de la biblioteca
	 ***********************************************************************/
	public function EditSubcategory(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'subcategory','category_id'), $this->input->post());
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}

		//Se procede a editar el area requerida
		$data = array(
			'id' => $this->input->post("id"),
			'subcategory' => $this->input->post("subcategory")
		);
		if($this->library->EditSubcategory($data)){
			successResponse('','La subcategoria ha sido guardada correctamente.',$this);
		}else{
			faildResponse('La subcategoria no se pudo guardar.',$this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar una catgoría de elementos de la biblioteca
	 ***********************************************************************/
	public function DeleteSubcategory(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id'), $this->input->post());
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}

		//Validar que no tenga elementos asociados. De lo contrario se termina el proceso, enviando error
		$filters = array(
			'business_id' => $valida_token['business_id'],
			'subcategory_id' => $this->input->post("id")
		);
		$elementos_asociados = $this->library->ListLibrary($filters);
		if($elementos_asociados){
			faildResponse('La subcategoria no se puede eliminar, tiene elementos asociados.',$this);
			return;
		}

		//Se procede a eliminar el area requerida
		$data = array(
			"id" => $this->input->post("id")
		);
		if($this->library->DeleteSubcategory($data)){
			successResponse('','La subcategoria ha sido eliminada correctamente.',$this);
		}else{
			faildResponse('La subcategoria no se pudo eliminar.',$this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener el catalogo de areas de biblioteca
	 ***********************************************************************/
	public function ListSubcategory(){
		$validaPost = $this->general_mdl->validapost(array('category_id'), $this->input->post());
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$areas = $this->library->ListSubcategory($valida_token['business_id'],$this->input->post('category_id'));
		if($areas){
			successResponse($areas,'Listado de subcategorias de biblioteca',$this);
		}else{
			faildResponse('No existen registros',$this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para guardar una catgoría de elementos de la biblioteca
	 ***********************************************************************/
	public function SaveCategory(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('name'), $this->input->post());
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}

		//Se procede a guardar la categoría requerida
		$data = array(
			'business_id' => $valida_token['business_id'],
			'name' => $this->input->post("name")
		);
		if($this->library->SaveCategory($data)){
			successResponse('','La categoría ha sido guardada correctamente',$this);
		}else{
			faildResponse('La categoría no se pudo guardar.',$this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar una catgoría de elementos de la biblioteca
	 ***********************************************************************/
	public function EditCategory(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'name'), $this->input->post());
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}

		//Se procede a editar la categoría requerida
		$data = array(
			'id' => $this->input->post("id"),
			'name' => $this->input->post("name")
		);
		if($this->library->EditCategory($data)){
			successResponse('','La categoría ha sido guardada correctamente',$this);
		}else{
			faildResponse('La categoría no se pudo guardar.',$this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar una catgoría de elementos de la biblioteca
	 ***********************************************************************/
	public function DeleteCategory(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id'), $this->input->post());
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}

		//Validar que no tenga elementos asociados. De lo contrario se termina el proceso, enviando error
		$filters = array(
			'business_id' => $valida_token['business_id'],
			'category_id' => $this->input->post("id")
		);
		$elementos_asociados = $this->library->ListLibrary($filters);
		if($elementos_asociados){
			faildResponse('La categoría no se puede eliminar, tiene elementos asociados.',$this);
			return;
		}

		//Se procede a eliminar el area requerida
		$data = array(
			"id" => $this->input->post("id")
		);
		if($this->library->DeleteCategory($data)){
			successResponse('','La categoría ha sido eliminada correctamente.',$this);
		}else{
			faildResponse('La categoría no se pudo eliminar.',$this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener el catalogo de categorias de biblioteca
	 ***********************************************************************/
	public function ListCategories(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$categories = $this->library->ListCategories($valida_token['business_id']);
		if($categories){
			successResponse($categories,'Listado de categorias de biblioteca',$this);
		}else{
			faildResponse('No existen registros',$this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para guardar una catgoría de elementos de la biblioteca
	 ***********************************************************************/
	public function SaveElement(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('title', 'text', 'category_id', 'type'), $this->input->post());
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}

		$type = $this->input->post("type");
		$type_video = $this->input->post("type_video");
		//Guardar archivos
		$archivos = $this->general_mdl->GuardarArchivos($_FILES, $ruta = 'library', $valida_token['business_id']);
		//Si no se subieron los archivos correctamente, terminamos el proceso
		if(!$archivos['success']) {
			//si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
			faildResponse($archivos['msg'], $this);
			return;
		}
		//En cualquier tipo se puede subir la imagen preview, guardamos la referencia
		$image = '';
		if(isset($archivos['success_files']['image'])){
			$image = $archivos['success_files']['image'];
		}
		//Guardamos la referencia del los archivos dependiendo el tipo de elemento
		$file = ''; $video = '';
		if($type ==  'documento' || $type ==  'imagen'){
			if(isset($archivos['success_files']['file'])){
				$file = $archivos['success_files']['file'];
			}
		}
		if($type ==  'video'){
			if($type_video == 'servidor'){
				if(isset($archivos['success_files']['video'])){
					$video = $archivos['success_files']['video'];
				}
			}else{
				$video = $this->input->post("video");
			}
		}

		//Se procede a guardar el elemento requerido
		$data = array(
			'title' => $this->input->post("title"),
			'text' => $this->input->post("text"),
			'category_id' => $this->input->post("category_id"),
			'subcategory_id' => $this->input->post("subcategory_id"),

			'type' => $this->input->post("type"),
			'file' => $file,
			'image' => $image,
			'link' => $this->input->post("link"),
			'type_video' => $this->input->post("type_video"),
			'video' => $video,
			'question' => $this->input->post('quiz_library'),
			'business_id' => $valida_token['business_id']
		);
		if($this->library->SaveElement($data)){
			/***********************************************************************
			 *	Nota: Se obtiene los tokens existentes en la BD
			 ***********************************************************************/
			$tokens = $this->notification->ListUserNotification($valida_token['business_id']);
			if($tokens){
				$tokens_ = array();
				foreach ($tokens as $index=>$value){
					array_push($tokens_,$value['token']);
					/***********************************************************************
					 *	Autor: Mario Adrián Martínez Fernández   Fecha: 09/07/2020
					 *		   mario.martinez.f@hotmail.es
					 *	Nota: Guardarmos a los usuarios que se les enviara la notificacion.
					 ***********************************************************************/
					$data = array('title'=>'Biblioteca','notification'=>'Se ha agregado '.$data['title'].' ha biblioteca','user_id'=>$value['user_id'],'service_id'=>SERVICE_LIBRARY,'user_create_id'=>$valida_token['user_id']);
					$this->notification->RegisterNotification($data);
				}
				/***********************************************************************
				 *	Nota: Se envia notificacion a los multiples tokens
				 ***********************************************************************/
				$this->general_mdl->EnviarNotificacionPush($tokens_,'Se ha agregado '.$data['title'].' ha biblioteca','Biblioteca',SERVICE_LIBRARY);
			}
			successResponse('','El elemento ha sido guardado correctamente',$this);
		}else{
			faildResponse('El elemento no se pudo guardar.',$this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar una catgoría de elementos de la biblioteca
	 ***********************************************************************/
	public function EditElement(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'title', 'text', 'category_id'), $this->input->post());
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}

		$type = $this->input->post("type");
		$type_video = $this->input->post("type_video");
		//Guardar archivos
		$archivos = $this->general_mdl->GuardarArchivos($_FILES, $ruta = 'library', $valida_token['business_id']);
		//Si no se subieron los archivos correctamente, terminamos el proceso
		if(!$archivos['success']) {
			//si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
			faildResponse($archivos['msg'], $this);
			return;
		}
		//En cualquier tipo se puede subir la imagen preview, guardamos la referencia
		$image = '';
		if(isset($archivos['success_files']['image'])){
			$image = $archivos['success_files']['image'];
		}
		//Guardamos la referencia del los archivos dependiendo el tipo de elemento
		$file = ''; $video = '';
		if($type ==  'documento' || $type ==  'imagen'){
			if(isset($archivos['success_files']['file'])){
				$file = $archivos['success_files']['file'];
			}
		}
		if($type ==  'video'){
			if($type_video == 'servidor'){
				if(isset($archivos['success_files']['video'])){
					$video = $archivos['success_files']['video'];
				}
			}else{
				$video = $this->input->post("video");
			}
		}

		//Se procede a editar la categoría requerida
		$data = array(
			'id' => $this->input->post("id"),
			'title' => $this->input->post("title"),
			'text' => $this->input->post("text"),
			'category_id' => $this->input->post("category_id"),
			'subcategory_id' => $this->input->post("subcategory_id"),
			'image' => $image,
			'type' => $this->input->post("type"),
			'file' => $file,
			'link' => $this->input->post("link"),
			'type_video' => $this->input->post("type_video"),
			'question' => ($this->input->post("quiz_library"))?$this->input->post("quiz_library"):'',
			'video' => $video
		);
		if($this->library->EditElement($data)){
			successResponse('','El elemento ha sido guardado correctamente',$this);
		}else{
			faildResponse('El elemento no se pudo guardar.',$this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar una catgoría de elementos de la biblioteca
	 ***********************************************************************/
	public function DeleteElement(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id'), $this->input->post());
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}

		//Se procede a eliminar el area requerida
		$data = array(
			"id" => $this->input->post("id")
		);
		if($this->library->DeleteElement($data)){
			successResponse('','El elemento ha sido eliminado correctamente.',$this);
		}else{
			faildResponse('El elemento no se pudo eliminar.',$this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener llos registros de la biblioteca en base
	 * 			a la empresa con la que esta ligado el usuario del token
	 * 			enviado.
	 ***********************************************************************/
	public function ListLibrary(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$data = $this->input->post();
		$data['business_id'] = $valida_token['business_id'];
		$data['user_id'] = $valida_token['user_id'];
		$library = $this->library->ListLibrary($data);
		if($library){
			successResponse($library,'Listado de biblioteca',$this);
		}else{
			faildResponse('No existen registros',$this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los catalogos de preguntas que se pueden
	 * 			asignar a un usuario
	 ***********************************************************************/
	function QuizLibrary(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$data = $this->input->post();
		$data['business_id'] = $valida_token['business_id'];
		$quizLibrary = $this->library->QuizLibrary($data);
		if($quizLibrary){
			successResponse($quizLibrary,'Listado de catalogo de preguntas de biblioteca',$this);
		}else{
			faildResponse('No existen registros',$this);
		}
	}
}
