<?php
class Ws extends CI_Controller
{
    public $defaultLang = 'es';

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("America/Mexico_City");

        $headers = $this->input->request_headers();
        if (isset($headers['lenguage'])) {
            $this->lang->load('message','es');
            $this->defaultLang = 'es';
        }else{
            $this->lang->load('message','en');
            $this->defaultLang = 'en';
        }
		$this->load->model('user_model', 'user');
		$this->load->model('groups_mdl', 'groups');
		$this->load->model('notification_mdl', 'notification');
		$this->load->model('games_mdl', 'games');

    }

    public function test()
    {
        return $this->load->view('mailtest.php');
    }

    public function CambiarPassword(){
        $token = $this->input->post("token");
        $password = $this->input->post("password");
        $valida_token = $this->general_mdl->ValidaTokenRecuperarPassword($token);
        if(!$valida_token){
            faildResponse('El token no es valido',$this);
            return;
        }
        $id_usuario = $valida_token;
        $respuesta = $this->general_mdl->CambiarPassword($id_usuario,$password,$token);
        if(!$respuesta){
            faildResponse('Por favor vuelve a intentarlo',$this);
        }
        successResponse('','El password se ha cambiado correctamente',$this);
    }

    public  function recuperar($token){
        $valida_token = $this->general_mdl->ValidaTokenRecuperarPassword($token);
        $this->load->view('admin/recuperar.php',array('id_user'=>$valida_token,'token'=>$token));
    }

    public function recuperarPassword(){
        $email = $this->input->post("email");
        /***********************************************************************
         *	Nota: Se valida que el email tenga una estructura correcta
         ***********************************************************************/
        if(!valid_email($email)){
            faildResponse('El email no cuenta con la estructura correcta',$this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('email'), $this->input->post());
        if(!$validaPost['success']){
            faildResponse($validaPost['msg'],$this);
            return;
        }

        /***********************************************************************
         *	Nota: Enviamos peticion para guardar y obtener el token para esta
         *          recuperacion de password
         ***********************************************************************/
        $token_email=$this->general_mdl->RecuperarPassword($email);
        if(!$token_email){
            faildResponse('El correo no existe.',$this);
            return;
        }

        $body = "<div style='background: url(https://kreativeco.com/nuup/mail_images/fondo.png) ; height:100%' >
                    <div style='padding-bottom: 25px; padding-top: 50px; text-align: center;'>
                        <img src='https://kreativeco.com/nuup/mail_images/logo_blanco.png' style='width:300px;'></a>
                    </div>
                    <div style='padding: 50px'>
                        <div style='content: ''; clear: both; display: table; background: #fff'>
                            <div style='float: left;width: 100%; height: 40%;background: #fff;border-radius: 20px; padding-top: 40px; text-align: center;'>
                                <p style='font-size: 10px;padding-left: 5%;padding-top: 1%; width: 90%; letter-spacing: 0.1em;line-height: 2.6'>". $this->lang->line('recovery_msg') ."</p>
                                <div style='text-align: center; padding: 20px 0px;'>
                                    <a href='". BASE_URL ."ws/recuperar/".$token_email."'>
                                        <button style='background-color: black; border-radius: 20px; color: #fff; padding: 20px; width: 35%;'>".$this->lang->line('recovery_title')."</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>";
        $email = $this->general_mdl->sendemail($this->lang->line('recovery_title'), $body, $email, 'Nuup');
        if($email === true)
        {
            successResponse('','Recuperar password',$this);
        }
        else
        {
            output_json($email,$this);
            /*Me quede en probar el envio de correo.*/
        }
    }
    /***********************************************************************
     *	Nota: Funcion para login de usarios
     ***********************************************************************/
    public function login(){
        $email = $this->input->post("email");
        $password = $this->input->post("password");
		$web = ($this->input->post("web"))?$this->input->post("web"):'';

        if(!valid_email($email)){
            faildResponse( $this->lang->line('error_mail_format') ,$this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('email','password'), $this->input->post(), $this->defaultLang );
        if(!$validaPost['success']){
            faildResponse($validaPost['msg'],$this);
            return;
        }
        $resultado = $this->general_mdl->Login($email,$password,$web);
        if($resultado){
        	if($web !== ''){
				$resultado[0]['session_web']=1;
				$this->session->set_userdata($resultado[0]);
			}
            successResponse($resultado,'Inicio de session correcto',$this);
        }else{
            faildResponse( $this->lang->line('email_pass_error') ,$this);
        }
    }

    /***********************************************************************
     *	Nota: Funcion para registrar usuario
     ***********************************************************************/
    public  function signin(){
    	$tipo_inicio = 'nuup';
    	if($tipo_inicio === 'nuup'){
			$data =array('email','password','name','business_name');
		}else{
			$data = array(
				'email',
				'password',
				'name',
				'type_user',
				'country_id',
				'state_id',
				'number_employee'
			);
			if($this->input->post('type_user') == USER_EXTERNAL){
				$data = array(
					'email',
					'password',
					'name',
					'business_name',
					'type_user',
					'country_id',
					'state_id',
					'activity_id',
					'priority_crop_id'
				);
			}
		}
		$validaPost = $this->general_mdl->validapost($data, $this->input->post() , $this->defaultLang );
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}
        $params = $this->input->post();

        $email         = $params['email'];
        $password      = $params['password'];
        $name          = $params['name'];
        $last_name     = (isset($params['last_name']))?$params['last_name']:'';
        $business_name = $params['business_name'];
        $job_name      = (isset($params['job']))?$params['job']:'';
        $phone         = (isset($params['phone']))?$params['phone']:'';
		$register_no_invitation = 0;
		$web = ($this->input->post("web"))?$this->input->post("web"):'';

        if(!valid_email($email)){
            faildResponse( $this->lang->line('error_mail_format') ,$this);
            return;
        }

        if($this->user_model->mailExists( $email )){
            faildResponse( 'El correo ya se encuentra registrado' ,$this);
            return;
        }
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández
         *		   mario.martinez.f@hotmail.es
         *	Fecha: 15/05/2020
         *	Nota: Se valida que el correo no cuente con invitacion por parte
		 * 			de uno de los creadores de empresa
         ***********************************************************************/
		if($tipo_inicio === 'nuup'){
			$validate_invitation = $this->user_model->ValidateInvitation($email);
			if($validate_invitation == 'in_use'){
				faildResponse('El correo que intentas registrar ya cuenta con un registro',$this);
				return;
			}
			if($validate_invitation == 'plan_limit'){
				faildResponse('Tu plan actual ha llego a su limite de usuarios que puedes registrar.',$this);
				return;
			}
			$is_invitation = false;
			if($validate_invitation){
				$is_invitation = true;
				$business_id = $validate_invitation['business_id'];
				$rol = 3;
			}else{
				$business_id = $this->business_model->SaveBusiness(array(
					"business_name" => $business_name,
					"plan_id" => 1
				));
				$rol = 2;
			}
			if($job_name !== ''){
				$job_id = $this->jobs_model->findJobName( $job_name , $business_id );
			}else{
				$job_id = 0;
			}
		}else{
			$rol = 3;
			$is_invitation = false;
			$business_id = 500;
			$group_id = 0;
			if($params['type_user'] == USER_EXTERNAL){
				$validate_invitation = $this->user_model->ValidateInvitation($email);
				if($validate_invitation == 'in_use'){
					faildResponse('El correo que intentas registrar ya cuenta con un registro',$this);
					return;
				}
				if($validate_invitation == 'plan_limit'){
					faildResponse('Tu plan actual ha llego a su limite de usuarios que puedes registrar.',$this);
					return;
				}
				if($validate_invitation) {
					$is_invitation = true;
					$group_id = $validate_invitation['group_id'];
				}else{
					/***********************************************************************
					 *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
					 *		   mario.martinez.f@hotmail.es
					 *	Nota: validamos si la empresa acepta registro sin invitacion
					 ***********************************************************************/
					if($this->general_mdl->GetConfigApp(EMPRESA_EXTERNOS,'register_no_invitation') == 1){
						$register_no_invitation = 1;
					}else{
						faildResponse('No cuentas con invitación',$this);
						return;
					}
				}
				$job = 0;
			}else{
				/***********************************************************************
				 *	Autor: Mario Adrián Martínez Fernández   Fecha: 17/08/2020
				 *		   mario.martinez.f@hotmail.es
				 *	Nota: Se valida que el numero de empleado no se encuentre en uso
				 * 			y que el numero de empleado coincida con el email de
				 * 			registro
				 ***********************************************************************/
				$employee = $this->user_model->ValidationNumberEmployee($params);
				if($employee){
					$job = $employee['job_id'];
					$group_id = $employee['group_id'];
				}else{
					faildResponse('El número de empleado no existe o ya se encuentra en uso, por favor de validar con un administrador.',$this);
					return;
				}
			}
		}

		if($tipo_inicio === 'nuup'){
			$entity = array(
				"name"          => $name,
				"last_name"     => $last_name,
				"email"         => $email,
				"password"      => $password,
				"business_id"   => $business_id,
				"job_id"        => $job_id,
				"phone"         => $phone,
				"rol_id"        => $rol
			);
		}else{
			$entity = array(
				"name"          	=> $name,
				"last_name"     	=> $last_name,
				"email"         	=> $email,
				"password"      	=> $password,
				"business_id"   	=> $business_id,
				"job_id"        	=> $job,
				"phone"         	=> $phone,
				"rol_id"        	=> $rol,
				"user_type"        	=> $params['type_user'],
				"county_id" 		=> $params['country_id'],
				"state_id" 			=> $params['state_id'],
				"area_id" 			=> (isset($params['area_id']))?$params['area_id']:0,
				"number_employee" 	=> (isset($params['number_employee']))?$params['number_employee']:0,
				"register_no_invitation" => $register_no_invitation
			);
			if($params['type_user'] == USER_EXTERNAL){
				$entity = array(
					"name"          	=> $name,
					"last_name"     	=> $last_name,
					"email"         	=> $email,
					"password"      	=> $password,
					"business_id"   	=> $business_id,
					"job_id"        	=> $job,
					"phone"         	=> $phone,
					"rol_id"        	=> $rol,
					"user_type"        	=> $params['type_user'],
					"county_id"		    => $params['country_id'],
					"state_id"			=> $params['state_id'],
					"activity_id"		=> $params['activity_id'],
					"priority_crop_id"	=> $params['priority_crop_id'],
					"register_no_invitation" => $register_no_invitation
				);
			}
		}
        $resultado = $this->general_mdl->Registro($entity);
        if($resultado === 'email'){
            faildResponse($this->lang->line('login_error'),$this);
            return;
        }
        if($resultado){
			if($tipo_inicio !== 'nuup'){
				if($params['type_user'] == USER_INTERNAL){
					$this->user_model->UpdateEmployee($employee['id'],array('estatus'=>1));
				}
			}
        	if($is_invitation && $register_no_invitation === 0){
				/***********************************************************************
				 *	Autor: Mario Adrián Martínez Fernández   Fecha: 15/05/2020
				 *		   mario.martinez.f@hotmail.es
				 *	Nota: Si el registro es correcto y el correo tenia una invitacion
				 * 			registramos que la invitacion ha sido reistrada.
				 ***********************************************************************/
				$this->user_model->ConfirmInvitation($email);
			}
			if($register_no_invitation == 1) {
				$msg = 'Un administrador aprobará tu registro.';
				//$resultado = '';
			}else{
				/***********************************************************************
				 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/9/2020
				 *		   mario.martinez.f@hotmail.es
				 *	Nota: Registramos el grupo del usuario al que se asigno.
				 ***********************************************************************/
				if(isset($group_id)){
					$this->groups->SaverUser(
						array(
							'group_id' => $group_id,
							'user_id' => $resultado['user_id']
						)
					);
				}
				$msg ='Registrado correctamente.';
			}
			/***********************************************************************
			 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/11/2020
			 *		   mario.martinez.f@hotmail.es
			 *	Nota: Registramos una notificacion por default
			 ***********************************************************************/
			// $this->notification->RegisterNotification(
			// 	array(
			// 		'title' => 'Bienvenido',
			// 		'notification'=>'Bienvenido has ingresado a la app de Universidad Agro',
			// 		'service_id'=>'',
			// 		'user_id' => $resultado['user_id']
			// 	)
			// );
			/***********************************************************************
			 *	Autor: Mario Adrián Martínez Fernández   Fecha: 12/14/2020
			 *		   mario.martinez.f@hotmail.es
			 *	Nota: Funcion para registrar las categorias de una ruleta
			 ***********************************************************************/
			for ($i = 1; $i <= 8; $i++) {
				$save_categories = array(
					"name" => 'Categoria '.$i,
					"points" => 0,
					"business_id" => $business_id
				);
				$this->games->SaveRouletteQuiz($save_categories);
			}
			if($web !== '' && $register_no_invitation == 0){
				$resultado[0]['session_web']=1;
				$this->session->set_userdata($resultado[0]);
			}
			if($register_no_invitation == 1) {
				$resultado = array();
			}
            successResponse($resultado,$msg,$this);
        }else{
            faildResponse($this->lang->line('not_signin_error'),$this);
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 10/27/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para encriptar password
     ***********************************************************************/
	function fnEncrypt($sValue)
	{
		return rtrim(
			base64_encode(
				mcrypt_encrypt(
					MCRYPT_RIJNDAEL_256,
					KEY_AES, $sValue,
					MCRYPT_MODE_ECB,
					mcrypt_create_iv(
						mcrypt_get_iv_size(
							MCRYPT_RIJNDAEL_256,
							MCRYPT_MODE_ECB
						),
						MCRYPT_RAND)
				)
			), "\0"
		);
	}
	/***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 15/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener el nombre de la empresa en caso de que
	 * 			el correo se encuentra invitado por otra empresa ya creada.
     ***********************************************************************/
	function ValidateBusiness(){
		$validaPost = $this->general_mdl->validapost(array('email'), $this->input->post() , $this->defaultLang );
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}
		$validate = $this->user_model->ValidateInvitation($this->input->post('email'));
		if($validate == 'in_use'){
			faildResponse('El correo que intentas registrar ya cuenta con un registro.',$this);
			return;
		}
		if($validate == 'plan_limit'){
			faildResponse('Tu plan actual ha llego a su limite de usuarios que puedes registrar.',$this);
			return;
		}
		if($validate){
			successResponse(array('business_name'=>$validate['business_name'],'business_id'=>$validate['business_id']),'Validar invitación',$this);
		}else {
			faildResponse('El correo que intentas registrar no cuenta con invitación.',$this);
		}
	}
    /***********************************************************************
     *  Funcion para obtener todas las empresas registradoas
    ***********************************************************************/
    public function getBusiness(){
        $business = $this->business_model->fetchAll("business");
        successResponse($business , "Business" , $this );
    }
    /***********************************************************************
     *  Funcion para obtener los puestos por empresan e idioma registrado
    ***********************************************************************/
    public function getJobs(){

        $business_id = $this->input->post("business_id");

        $validaPost = $this->general_mdl->validapost(array('business_id'), $this->input->post(), $this->defaultLang );
        if(!$validaPost['success']){
            faildResponse($validaPost['msg'],$this);
            return;
        }
        $jobs = $this->jobs_model->fetchAllByBusinessId( $business_id );
        successResponse($jobs , "Business" , $this );
    }

    public function getAllServices()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if(!$valida_token){
            faildResponse( $this->lang->line('token_error_msg') ,$this);
            return;
        }
        $services = $this->services_model->fetchAll($valida_token['business_id']);
        // Se da formato de moneda el precio del servicio
		if($services){
			foreach ($services as $key => $value) {
				$services[$key]['formatted_price'] = "$ " . number_format($value['price'],2);
			}
		}
        successResponse($services , "Services" , $this);
    }
    /***********************************************************************
     *  Funcion para obtener el carro de compras del usuario
    ***********************************************************************/
    public function getShoppingCart()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if(!$valida_token){
            faildResponse( $this->lang->line('token_error_msg') ,$this);
            return;
        }
        $shopping_cart = $this->_userShoppingCart( $valida_token['user_id'] );
        successResponse($shopping_cart , 'Shopping Cart' , $this);
    }
    /***********************************************************************
     *  Funcion para agregar un item al carro de compras del usuarios
    ***********************************************************************/
    public function addItemShoppinCart()
    {
        $token      = $this->input->post("token");
        $service_id = $this->input->post("service_id");
        $quantity   = $this->input->post("quantity");
        $discount   = $this->input->post("discount");

        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if(!$valida_token){
            faildResponse( $this->lang->line('token_error_msg') ,$this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('service_id' , 'quantity' ), $this->input->post(), $this->defaultLang );
        if(!$validaPost['success']){
            faildResponse($validaPost['msg'],$this);
            return;
        }
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 23/06/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Se valida que el item no haya sido seleccionado previamente
         ***********************************************************************/
		if($this->shoppingcart_model->validateItemShopping($valida_token["user_id"],$service_id)){
			faildResponse("El item ya ha sido agregado previamente.",$this);
			return;
		}
        $entity = array(
			"user_id"       => $valida_token["user_id"],
			"quantity"      => $quantity,
			"discount"      => 0,
			"service_id"    => $service_id
		);
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 23/06/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Validamos que el item no este agregado ya por el usuario
         ***********************************************************************/
        $this->shoppingcart_model->insert($entity);
        $shopping_cart = $this->_userShoppingCart( $valida_token['user_id'] );
        successResponse($shopping_cart , 'Shopping Cart - item added' , $this);
    }
    /***********************************************************************
     *  Funcion para eliminar un item de carrito de compras
    ***********************************************************************/
    public function deleteItemShoppingCart()
    {
        $token      = $this->input->post("token");
        $item_id    = $this->input->post("item_id");

        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if(!$valida_token){
            faildResponse( $this->lang->line('token_error_msg') ,$this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('item_id'), $this->input->post(), $this->defaultLang );
        if(!$validaPost['success']){
            faildResponse($validaPost['msg'],$this);
            return;
        }

        $result = $this->shoppingcart_model->delete($item_id , $valida_token['user_id']);

        if ($this->shoppingcart_model->delete($item_id , $valida_token['user_id'])) {
            $shopping_cart = $this->_userShoppingCart( $valida_token['user_id'] );
            successResponse($shopping_cart , 'Shopping Cart - item deleted' , $this);
        }else{
            faildResponse( $this->lang->line('invalid_item_msg') ,$this);
        }
    }
    /***********************************************************************
     *  Funcion para pagar un carrito de compra
    ***********************************************************************/
    public function PayShoppingCart()
    {

        $token = $this->input->post("token");

        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if(!$valida_token){
            faildResponse( $this->lang->line('token_error_msg') ,$this);
            return;
        }

        $shopping_cart = $this->_userShoppingCart( $valida_token['user_id'] );
        $services = "";
        foreach ($shopping_cart["cart_items"] as $key => $value) {
            $this->shoppingcart_model->delete($value["id"] , $valida_token['user_id']);
            $services .= "<tr>
                            <td>".$value["service_name"]."</td>
                            <td>".$value["category_name"]."</td>
                            <td>".$value["description"]."</td>
                            <td>".$value["price"]."</td>
                            <td>".$value["quantity"]."</td>
                            <td>".$value["subtotal"]."</td>
                        </tr>";
        }

        $body = "<table>
                    <tr>
                        <th>Servicio</th>
                        <th>Categoria</th>
                        <th>Descripcion</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                    ".$services."
                </table>";

        if ($this->general_mdl->sendemail('Servicios cotizacion', $body, "mario@kreativeco.com", "Nuup")) {
            successResponse('' , $this->lang->line('mail_sended'), $this);
        }else{
            faildResponse( $this->lang->line('mail_not_sended') ,$this);
        }

    }
    /***********************************************************************
     *  Funcion para obtener el perfil del usuario
    ***********************************************************************/
    public function getProfile()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		$valida_token['users_business']=$this->user->ListUsersBusiness($valida_token['business_id']);
        if(!$valida_token){
            faildResponse( $this->lang->line('token_error_msg') ,$this);
            return;
        }
        successResponse($valida_token , 'User Profile' , $this);
    }

    /***********************************************************************
     *  Funcion para actualizar el perfil del usuario
    ***********************************************************************/
    public function updateProfile()
    {
        $token      = $this->input->post("token");
        $name       = $this->input->post("name");
        $last_name  = $this->input->post("last_name");
        $phone      = $this->input->post("phone");


        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if(!$valida_token){
            faildResponse( $this->lang->line('token_error_msg') ,$this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('name' , 'last_name'), $this->input->post(), $this->defaultLang );
        if(!$validaPost['success']){
            faildResponse($validaPost['msg'],$this);
            return;
        }
		$entity = array(
			'name'      => $name,
			'last_name' => $last_name,
			'phone'     => $phone
		);
        $this->user_model->update( $valida_token['user_id'] , $entity );
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        successResponse($valida_token , $this->lang->line('profile_updated_msg') , $this);
    }

    /***********************************************************************
     *  Funcion para actualizar el perfil del usuario
    ***********************************************************************/
    public function updateProfilePhoto()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if(!$valida_token){
            faildResponse( $this->lang->line('token_error_msg') ,$this);
            return;
        }
        $files = $_FILES['profile_photo'];
		$profile_photo = "";
        if ($files['name'] != "") {
            $file = $this->general_mdl->GuardarArchivos($_FILES, "", $valida_token['business_id']);
			//Si la subida de archivos fue exitosa
			if($file['success']){
				//Si se subió el correctamente el archivo deseado ("media_path"), guardamos el nombre asignado
				if(isset($file['success_files']['profile_photo'])){
					$profile_photo = $file['success_files']['profile_photo'];
				}
			}else{
				//si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
				faildResponse($file['msg'], $this);
				return;
			}
            $entity = array(
                                'profile_photo' => PHOTO_URL."business_".$valida_token['business_id']."/".$profile_photo
                            );
            $this->user_model->update( $valida_token['user_id'] , $entity );
            $userDt = $this->general_mdl->UsuarioDetalleToken($token);
			if(isset($_SESSION['id_user']) || isset($_SESSION['user_id'])){
				$this->session->set_userdata(PHOTO_URL."business_".$valida_token['business_id']."/".$profile_photo);
			}
            successResponse($userDt , $this->lang->line('photo_updated_msg') , $this );
            return;
        }else{
            faildResponse($this->lang->line('no_photo_file'),$this);
            return;
        }
    }


    public function sendMemberRequest()
    {
        $token          = $this->input->post("token");
        $member_mail    = $this->input->post("member_mail");

        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if(!$valida_token){
            faildResponse( $this->lang->line('token_error_msg') ,$this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('member_mail'), $this->input->post(), $this->defaultLang );
        if(!$validaPost['success']){
            faildResponse($validaPost['msg'],$this);
            return;
        }
		$validate_invitation = $this->user_model->ValidateInvitation($member_mail,$valida_token['business_id']);
		if($validate_invitation == 'in_use'){
			faildResponse('El correo que intentas registrar ya cuenta con un registro completo.',$this);
			return;
		}
		if($validate_invitation == 'plan_limit'){
			faildResponse('Tu plan actual ha llego a su limite de usuarios que puedes registrar.',$this);
			return;
		}
        $body = "<div style='background: url(https://kreativeco.com/nuup/mail_images/fondo.png) ; height:100%' >
                    <div style='padding-bottom: 25px; padding-top: 50px; text-align: center;'>
                        <img src='https://kreativeco.com/nuup/mail_images/logo_blanco.png' style='width:300px;'></a>

                        <div style='padding: 1% 25% 0% 25%;'>
                            <p style='color:white;text-align: center;'>
                                ". $this->lang->line('general_invite_msg') ."</p>
                        </div>
                    </div>
                    <div style='padding: 50px'>
                        <div style='content: ''; clear: both; display: table; background: #fff'>
                            <div style='float: left;width: 65%; height: 40%;background: #fff;border-radius: 20px;border-right: 5px dashed black; padding-top: 40px'>
                                <img src='https://kreativeco.com/nuup/mail_images/logo_negro.png' style='width: 15%;padding-left: 5%;padding-top: 3%;'> 
                                <p style='font-size: 10px;padding-left: 5%;padding-top: 1%; width: 90%; letter-spacing: 0.1em;line-height: 2.6'>
                                    ". $this->lang->line('inside_invite_msg') ."
                                </p>
                                <div style='text-align: center; padding: 20px 0px;'>
                                    <button style='background-color: black; border-radius: 20px; color: #fff; padding: 20px; width: 35%;'>
                                        " . $this->lang->line('accept_btn_msg') . "
                                    </button>
                                </div>
                            </div>
                            <div style='float: left; width: 30%;height: 40%; ; background: #fff; border-radius: 20px; padding-top: 40px'>
                                <div style='text-align: center;'>
                                    <img src='" . $valida_token['profile_photo'] . "' style='width: 45%; border-radius:50%; '>   
                                    <p style='font-size: 10px;padding-left: 5%;padding-top: 1%; width: 90%; letter-spacing: 0.1em;line-height: 2.6'><strong>" . $valida_token['name'] . "</strong></p>
                                    <p style='font-size: 10px;padding-left: 5%;padding-top: 1%; width: 90%; letter-spacing: 0.1em;line-height: 2.6; margin-top: -20px'>" . $valida_token['job_name'] . "</p>
                                </div>
                                <p style='font-size: 18px;padding-left: 5%;padding-top: 20%; width: 90%; letter-spacing: 0.1em;line-height: 2.6; text-align: center;'>" . $this->lang->line('invite_you_msg') . "</p>
                            </div>
                        </div>
                    </div>
                </div>";

        if ($this->general_mdl->sendemail($this->lang->line('invite_email_title'), $body, $member_mail, "Nuup")) {
			if(!$validate_invitation){
				$group_id = ($this->input->post('group_id'))?$this->input->post('group_id'):'';
				/***********************************************************************
				 *	Autor: Mario Adrián Martínez Fernández   Fecha: 15/05/2020
				 *		   mario.martinez.f@hotmail.es
				 *	Nota: Si el email se envia correctamente entonces se registra
				 * 			la invitacion para obtener de que empresa ha sido invitado
				 ***********************************************************************/
				$this->user_model->SaveInvitation(array('email'=>$member_mail,'business_id'=>$valida_token['business_id'],'group_id'=>$group_id));
			}
            successResponse('' , $this->lang->line('mail_sended'), $this);
        }else{
            faildResponse( $this->lang->line('mail_not_sended') ,$this);
        }
    }

    public function getLibrary()
    {
        $token          = $this->input->post("token");
        $media_type     = $this->input->post("media_type");

        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if(!$valida_token){
            faildResponse( $this->lang->line('token_error_msg') ,$this);
            return;
        }

        $result = $this->library_model->fetchAllByMediaType( $media_type );

        successResponse( $result , "Multimedia", $this);
    }


    public function newPost()
    {
        $token          = $this->input->post("token");
        $description    = $this->input->post("descripcion");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if(!$valida_token){
            faildResponse( $this->lang->line('token_error_msg') ,$this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('descripcion'), $this->input->post(), $this->defaultLang );
        if(!$validaPost['success']){
            faildResponse($validaPost['msg'],$this);
            return;
        }

        $files = $_FILES['imagen_post'];
		$imagen_post="";
        if ($files['name'] != "") {
            $file = $this->general_mdl->GuardarArchivos($_FILES, "", $valida_token['business_id']);
			//Si la subida de archivos fue exitosa
			if($file['success']){
				//Si se subió el correctamente el archivo deseado ("media_path"), guardamos el nombre asignado
				if(isset($file['success_files']['imagen_post'])){
					$imagen_post = $file['success_files']['imagen_post'];
				}
			}else{
				//si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
				faildResponse($file['msg'], $this);
				return;
			}
            $entity = array(
                                'image_path'        => PHOTO_URL."business_".$valida_token['business_id']."/".$imagen_post,
                                'user_id'           => $valida_token['user_id'],
                                'wall_description'  => $description
                            );

            $this->wall_model->insert( $entity );
            successResponse('' , 'New Post' , $this );

            return;
        }else{
            faildResponse($this->lang->line('no_post'),$this);
            return;
        }
    }

    public function getWall()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if(!$valida_token){
            faildResponse( $this->lang->line('token_error_msg') ,$this);
            return;
        }

        $result = $this->wall_model->getAll();

        foreach ($result as $key => $value) {
            $comments = $this->comments_model->getCommentsByPost($value["id"]);
            $result[$key]['comments'] = $comments;
        }

        successResponse($result , 'Wall' , $this );
    }

    public function newCommentPost()
    {
        $token          = $this->input->post("token");
        $comment        = $this->input->post("comentario");
        $post_id        = $this->input->post("post_id");
        $valida_token   = $this->general_mdl->UsuarioDetalleToken($token);
        if(!$valida_token){
            faildResponse( $this->lang->line('token_error_msg') ,$this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('comentario', 'post_id'), $this->input->post(), $this->defaultLang );
        if(!$validaPost['success']){
            faildResponse($validaPost['msg'],$this);
            return;
        }

        $entity = array(
                            "comment"   => $comment,
                            "user_id"   => $valida_token['user_id'],
                            "post_id"   => $post_id
                        );

        $this->comments_model->insert( $entity );

        successResponse('' , 'New Comment Post' , $this );
    }

    /***********************************************************************
     *  Funcion para Modelar el resultado de carrito de compras
    ***********************************************************************/
    public function _userShoppingCart( $user_id )
    {
        $shopping_cart = $this->shoppingcart_model->getMyShoppingCart( $user_id );
        $response   = array();
        $cart_items = array();
        $total = 0;
        foreach ($shopping_cart as $key => $value) {

            $subtotal   = $value['price'] * $value['quantity'];
            $total      = $total + $subtotal;

            $cart_items[] = array(
				"id" => $value["id"],
				"service_name" => $value["service_name"],
				"category_name" => $value["category_name"],
				"description" => $value["description"],
				"image" => $value["image"],
				"price" => "$ ".number_format($value["price"],2),
				"quantity" => $value["quantity"],
				"subtotal" => "$ " . number_format($subtotal,2)
			);
        }
        $response['cart_total'] = "$ " . number_format($total,2);
        $response['cart_items'] = $cart_items;
        return $response;
    }
    /***********************************************************************
     *	Nota: Funcion para enviar notificacion push a un solo token
    ***********************************************************************/
    public function enviarNotificacionPush(){
        $token = $this->input->post("token");
        $token_firebase = $this->input->post("token_firebase");
        $notificacion = $this->input->post("notificacion");
        $titulo = $this->input->post("titulo");

        $validaPost = $this->general_mdl->validapost(array('token_firebase','notificacion','titulo'), $this->input->post());
        if(!$validaPost['success']){
            faildResponse($validaPost['msg'],$this);
            return;
        }
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if(!$valida_token){
            faildResponse('El token no es valido.',$this);
            return;
        }
        $tokens = array();
        array_push($tokens,$token_firebase);
        $enviar_notificacion = $this->general_mdl->EnviarNotificacionPush($tokens,$notificacion,$titulo);
        echo $enviar_notificacion;
    }
    /***********************************************************************
     *	Nota: Enviar notificacion push multiple
     ***********************************************************************/
    public function enviarMultiplePush(){
        $token = $this->input->post("token");
        $titulo = $this->input->post("titulo");
        $notificacion = $this->input->post("notificacion");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if(!$valida_token){
            faildResponse('El token no es valido.',$this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('titulo','notificacion'), $this->input->post());
        if(!$validaPost['success']){
            faildResponse($validaPost['msg'],$this);
            return;
        }
        /***********************************************************************
         *	Nota: Se obtiene los tokens existentes en la BD
         ***********************************************************************/
        $tokens = $this->general_mdl->SendMultipleDevices();
        if(!$tokens){
            faildResponse('No existen devices registrados',$this);
            return;
        }
        /***********************************************************************
         *	Nota: Se envia notificacion a los multiples tokens
         ***********************************************************************/
        $enviar_notificacion = $this->general_mdl->EnviarNotificacionPush($tokens,$notificacion,$titulo);
        echo $enviar_notificacion;
    }
    /***********************************************************************
     *	Nota: Funcion para actualizar o agregar nuevos devices
     ***********************************************************************/
    public function registerDevice(){
        $token = $this->input->post("token");
        $token_firebase = $this->input->post("token_firebase");
        $validaPost = $this->general_mdl->validapost(array('token_firebase'), $this->input->post());
        if(!$validaPost['success']){
            faildResponse($validaPost['msg'],$this);
            return;
        }
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if(!$valida_token){
            faildResponse('El token no es valido.',$this);
            return;
        }
        $respuesta = $this->general_mdl->RegistroDevices($valida_token['user_id'],$token_firebase);
        if($respuesta){
            successResponse('','El registro se ha completado satisfactoriamente.',$this);
        }else{
            faildResponse('El registro no se completo correctamente.',$this);
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández 		Fecha: 08/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para enviar un correo con los servicios contratados
     ***********************************************************************/
	public function SendPurchase(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse('El token no es valido.',$this);
			return;
		}
		$respuesta = $this->shoppingcart_model->getMyShoppingCart($valida_token['user_id']);
		if(count($respuesta) > 0){
			$html = '<div style="padding-bottom: 10px;">
				Nombre: '.$valida_token['name_complete'].'<br>
				Correo: '.$valida_token['email'].'<br>
			</div><br><table style="border: 1px solid black;">
				<thead>
					<tr>
						<th style="border: 1px solid black;">Servicio</th>
						<th style="border: 1px solid black;">Descripción</th>
						<th style="border: 1px solid black;">Categoría</th>
						<th style="border: 1px solid black;">Cantidad</th>
						<th style="border: 1px solid black;">Descuento</th>
						<th style="border: 1px solid black;">Precio</th>
					</tr>
				</thead>
				<tbody>
			';
			foreach ($respuesta as $index => $value){
				$html .= '<tr>
				 <td style="border: 1px solid black;">'.$value['service_name'].'</td>
			     <td style="border: 1px solid black;">'.$value['description'].'</td>
			     <td style="border: 1px solid black;">'.$value['category_name'].'</td>
			     <td style="border: 1px solid black;">'.$value['quantity'].'</td>
			     <td style="border: 1px solid black;">'.$value['discount'].'</td>
			     <td style="border: 1px solid black;">'.$value['price'].'</td>
				</tr>';
			}
			$html .='</tbody></table>';
			$email = $this->general_mdl->sendemail('Compra', $html, 'luis@kreativeco.com', 'Nuup');
			if($email === true)
			{
				/***********************************************************************
				 *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/10/2020
				 *		   mario.martinez.f@hotmail.es
				 *	Nota: Si el email se envia correctamente eliminamos los servicios de
				 * 			el carrito de compras y los pasamos a una tabla temporal.
				 ***********************************************************************/
				$this->shoppingcart_model->ServicesPurchase($valida_token['user_id'],$valida_token['business_id']);

				successResponse('','Compra hecha correctamente',$this);
			} else {
				output_json($email,$this);
			}
		}else{
			faildResponse('No existén registro en el carrito de compras.',$this);
		}

	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener los servicios contratados por empresa
	 * 			en base al usuario que regrese el token
	 ***********************************************************************/
	public function HiredServices(){
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse('El token no es valido.',$this);
			return;
		}
		$respuesta = $this->services_model->HiredServices($valida_token);
		if($respuesta){
			successResponse($respuesta,'Listado de servicios contratados',$this);
		}else{
			faildResponse('No existen registros.',$this);
		}
	}

    /***********************************************************************
     *  Autor: Rodolfo Terrones Ruiz   Fecha: 23/06/2020
     *  Nota: Obtenemos el ranking de usuarios con la posibilidad de filtrarlos
     *      por empresa y puesto
     ***********************************************************************/
    public function getRanking(){
        $token          = $this->input->post("token");
        $filtro         = $this->input->post("filtro");
        $valida_token   = $this->general_mdl->UsuarioDetalleToken($token);
        if(!$valida_token){
            faildResponse('El token no es valido.',$this);
            return;
        }

        $where = "";
        // Filtro 1 es por empresa , else por puesto
        if ($filtro == 1 ) {
            $where = " WHERE u.business_id = " . $valida_token['business_id'];
        }else{
            $where = " WHERE u.job_id = ". $valida_token['job_id'];
        }

        $result = $this->user_model->ranking( $where );

        // Modificamos y agregamos campos en el arreglo
        foreach ($result as $key => $value) {
            $result[$key]["position"]   = $key + 1;
            $result[$key]["score"]      = $value["score"] . " Puntos";
        }

        successResponse($result,'Listado de ranking',$this);
    }
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 10/22/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para ocultar o mostrar un services
	 ***********************************************************************/
	function ServicesHideShow(){
		$token          = $this->input->post("token");
		$valida_token   = $this->general_mdl->UsuarioDetalleToken($token);
		if(!$valida_token){
			faildResponse('El token no es valido.',$this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('services_id'), $this->input->post());
		if(!$validaPost['success']){
			faildResponse($validaPost['msg'],$this);
			return;
		}
		$data = $this->input->post();
		$data['business_id'] = $valida_token['business_id'];
		$respuesta = $this->services_model->ServicesHideShow($data);
		if($respuesta){
			successResponse($respuesta,'El servicio se ha actualizado correctamente',$this);
		}else{
			faildResponse('El servicio no se ha actualizado correctamente.',$this);
		}
	}
}
