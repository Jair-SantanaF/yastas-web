<?php
class User extends CI_Controller
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
		$this->load->model('user_model', 'user');
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para editar un usuario
	 ***********************************************************************/
	public function EditUser()
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
		$validaPost = $this->general_mdl->validapost(array('id', 'name', 'last_name', 'job_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a editar el usuario requerido
		$data = array(
			'id' => $this->input->post("id"),
			'name' => $this->input->post("name"),
			'last_name' => $this->input->post("last_name"),
			'job_id' => $this->input->post("job_id"),
			'password' => $this->input->post("password"),
			'email' => $this->input->post("email"),
			'id_comercio' => $this->input->post("id_comercio"),
			'phone' => $this->input->post("phone"),
			'number_employee' => $this->input->post("number_employee"),
			'fecha_alta_cliente' => $this->input->post("fecha_alta_cliente")
		);

		if ($this->user->EditUser($data)) {
			$this->general_mdl->writeLog("Edicion de datos de usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'La categoría ha sido guardada correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al editar datos de usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('La categoría no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para eliminar un usuario
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

		//Se procede a eliminar el invitado requerido
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->user->DeleteUser($data)) {
			$this->general_mdl->writeLog("Eliminacion de usuario, usuario " . $valida_token["user_id"] . " eliminado " . $this->input->post("id"), "<info>");
			successResponse('', 'El usuario ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar usuario, usuario " . $valida_token["user_id"] . " eliminado " . $this->input->post("id"), "<warning>");
			faildResponse('El usuario no se pudo eliminar.', $this);
		}
	}


	public function UserListAll()
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
		$params = array(
			'business_id' => $valida_token['business_id'],
			'asesores' => $this->input->post("asesores")
		);
		if ($this->input->post("completo") != null && $this->input->post("completo") == 0) {
			$params["user_id"] = $valida_token["user_id"];
		}
		if ($valida_token["rol_id"] == 6) {
			$params["id_asesor"] = $valida_token["user_id"];
		}
		$users = $this->user->UserListAll($params);
		if ($users) {
			$this->general_mdl->writeLog("Listado de usuarios usuario " . $valida_token["user_id"], "<info>");
			successResponse($users, 'Listado de usuarios', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener usuarios usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para obtener los usuarios
	 ***********************************************************************/
	public function UserList()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		// $validaPost = $this->general_mdl->validapost(array('invited_id', 'member_mail', 'group_id'), $this->input->post());
		// if (!$validaPost['success']) {
		// 	faildResponse($validaPost['msg'], $this);
		// 	return;
		// }
		$params = array(
			'business_id' => $valida_token['business_id']
		);
		if ($this->input->post("completo") != null && $this->input->post("completo") == 0) {
			$params["user_id"] = $valida_token["user_id"];
		}
		if ($this->input->post("group_id") != null) {
			$params["group_id"] = $this->input->post("group_id");
		}
		if ($valida_token["rol_id"] == 6) {
			$params["id_asesor"] = $valida_token["user_id"];
		}
		$users = $this->user->UserList($params);
		if ($users) {
			$this->general_mdl->writeLog("Listado de usuarios usuario " . $valida_token["user_id"], "<info>");
			successResponse($users, 'Listado de usuarios', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener usuarios usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros', $this);
		}
	}

	public function DescargarCsvRegistrados()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$params = array(
			'business_id' => $valida_token['business_id']
		);
		if ($this->input->post("completo") != null && $this->input->post("completo") == 0) {
			$params["user_id"] = $valida_token["user_id"];
		}
		if ($this->input->post("group_id") != null) {
			$params["group_id"] = $this->input->post("group_id");
		}
		$users = $this->user->UserListCsv($params);
		header('Content-Type: text/csv; charset=utf-8');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header("Content-Disposition: attachment; filename=UsuariosRegistrados" . date('y-m-d') . ".csv");
		header('Last-Modified: ' . date('D M j G:i:s T Y'));
		$outss = fopen("php://output", "w");
		$resultado = array_merge([["ID", "NOMBRE", "APELLIDO", "FECHA_REGISTRO", "EMAIL", "PUESTO", "TELEFONO", "SCORE", "GRUPOS"]], $users);
		foreach ($resultado as $rows) {
			fputcsv($outss, $rows);
		}
		fclose($outss);
		// if ($users) {
		// 	$this->general_mdl->writeLog("Listado de usuarios usuario " . $valida_token["user_id"], "<info>");
		// 	successResponse($users, 'Listado de usuarios', $this);
		// } else {
		// 	$this->general_mdl->writeLog("Error al obtener usuarios usuario " . $valida_token["user_id"], "<warning>");
		// 	faildResponse('No existen registros', $this);
		// }
	}

	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para obtener los invitados
	 ***********************************************************************/
	public function InvitedListCSV()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$users = $this->user->InvitedList($valida_token['business_id'], $valida_token["rol_id"]);
		header('Content-Type: text/csv; charset=utf-8');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header("Content-Disposition: attachment; filename=UsuariosInvitados" . date('y-m-d') . ".csv");
		header('Last-Modified: ' . date('D M j G:i:s T Y'));
		$outss = fopen("php://output", "w");
		fputcsv($outss, ["NUMERO_EMPLEADO", "FECHA ALTA CLIENTE", "ASESOR", "REGION"]);
		foreach ($users as $rows) {
			$obj = [];
			$obj["number_employee"] = $rows["number_employee"];
			$obj["fecha_alta_cliente"] = $rows["fecha_alta_cliente"];
			$obj["asesor"] = $rows["asesor"];
			$obj["region"] = $rows["region"];
			fputcsv($outss, $obj);
		}
		fclose($outss);
		// if ($users) {
		$this->general_mdl->writeLog("Descarga de reporte de usuarios invitados usuario " . $valida_token["user_id"], "<info>");
		// 	successResponse($users, 'Listado de invitados', $this);
		// } else {
		$this->general_mdl->writeLog("Error al descargar reporte de usuarios invitados usuario " . $valida_token["user_id"], "<warning>");
		// 	faildResponse('No existen registros', $this);
		// }
	}

	public function DescargarCsvPuntosUsuarios()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$users = $this->user->puntos_por_usuario($valida_token['business_id']);
		header('Content-Type: text/csv; charset=utf-8');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header("Content-Disposition: attachment; filename=PuntosPorUsuario" . date('y-m-d') . ".csv");
		header('Last-Modified: ' . date('D M j G:i:s T Y'));
		$outss = fopen("php://output", "w");
		fputcsv($outss, ["NOMBRE", "NÚMERO DE EMPLEADO", "PUNTOS"]);
		foreach ($users as $rows) {
			fputcsv($outss, $rows);
		}
		fclose($outss);
		$this->general_mdl->writeLog("Descarga de archivo de puntos por usuario usuario " . $valida_token["user_id"], "<info>");
		$this->general_mdl->writeLog("Error al descargar archivo de puntos por usuario usuario " . $valida_token["user_id"], "<warning>");
	}

	public function InvitedList()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$users = $this->user->InvitedList($valida_token['business_id'], $valida_token["rol_id"], $valida_token["user_id"], $valida_token["id_region"], 2);
		if ($users) {
			$this->general_mdl->writeLog("Listado de usuarios invitados usuario " . $valida_token["user_id"], "<info>");
			successResponse($users, 'Listado de invitados', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener usuarios invitados usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros', $this);
		}
	}

	public function InvitedListAdmin()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$users = $this->user->InvitedList($valida_token['business_id'], $valida_token["rol_id"], $valida_token["user_id"], $valida_token["id_region"], null);
		if ($users) {
			$this->general_mdl->writeLog("Listado de usuarios invitados usuario " . $valida_token["user_id"], "<info>");
			successResponse($users, 'Listado de invitados', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener usuarios invitados usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros', $this);
		}
	}

	public function InvitedListMC()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$users = $this->user->InvitedList($valida_token['business_id'], $valida_token["rol_id"], $valida_token["user_id"], $valida_token["id_region"], 3, null);
		if ($users) {
			$this->general_mdl->writeLog("Listado de usuarios invitados usuario " . $valida_token["user_id"], "<info>");
			successResponse($users, 'Listado de invitados', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener usuarios invitados usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/8/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para eliminar un invitado
	 ***********************************************************************/
	public function EditInvited()
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
		// $validaPost = $this->general_mdl->validapost(array('invited_id', 'member_mail', 'group_id'), $this->input->post());
		// if (!$validaPost['success']) {
		// 	faildResponse($validaPost['msg'], $this);
		// 	return;
		// }

		//Se procede a eliminar el invitado requerido
		$data = $this->input->post();
		$body = "<div style='background: url(https://kreativeco.com/nuup/mail_images/fondo.png) ; height:100%' >
                    <div style='padding-bottom: 25px; padding-top: 50px; text-align: center;'>
                        <img src='https://kreativeco.com/nuup/mail_images/logo_blanco.png' style='width:300px;'></a>

                        <div style='padding: 1% 25% 0% 25%;'>
                            <p style='color:white;text-align: center;'>
                                " . $this->lang->line('general_invite_msg') . "</p>
                        </div>
                    </div>
                    <div style='padding: 50px'>
                        <div style='content: ''; clear: both; display: table; background: #fff'>
                            <div style='float: left;width: 65%; height: 40%;background: #fff;border-radius: 20px;border-right: 5px dashed black; padding-top: 40px'>
                                <img src='https://kreativeco.com/nuup/mail_images/logo_negro.png' style='width: 15%;padding-left: 5%;padding-top: 3%;'> 
                                <p style='font-size: 10px;padding-left: 5%;padding-top: 1%; width: 90%; letter-spacing: 0.1em;line-height: 2.6'>
                                    " . $this->lang->line('inside_invite_msg') . "
                                </p>
                                <div style='text-align: center; padding: 20px 0px;'>
                                    <button style='background-color: black; border-radius: 20px; color: #fff; padding: 20px; width: 35%;' onclick='mandarARegistro()'>
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
                </div><script>
                function mandarARegistro(){
                    var userAgent = navigator.userAgent || navigator.vendor || window.opera;
    var app = {
        launchApp: function () {
            if (/android/i.test(userAgent))
                window.location.href = 'https://play.google.com/store/apps/details?id=com.kreativeco.nuup&hl=en_US&gl=US'
            else if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream)
                window.location.href = 'https://apps.apple.com/us/app/nuup/id1517158945';
            else if (navigator.userAgent.match(/Chrome|Safari|Mozilla|AppleWebKit/i))
                window.location.href = 'http://kreativeco.com/nuup/index.php/app/register'
        }
    };

    app.launchApp();
                }
                </script>";

		if (true) { //$this->general_mdl->sendemail($this->lang->line('invite_email_title'), $body, $this->input->post('email'), "Agro")) {
			if ($this->user_model->ValidarInvitacionDuplicada($data["email"], $data["number_employee"], $data["id"]))
				if ($this->user->EditInvited($data)) {
					$this->general_mdl->writeLog("Eliminacion, actualizacion de invitado usuario " . $valida_token["user_id"] . " invitado " . $this->input->post("member_mail"), "<info>");
					successResponse('', 'El invitado ha sido actualizado correctamente.', $this);
				} else {
					$this->general_mdl->writeLog("Error al eliminar, actualizar invitado usuario " . $valida_token["user_id"] . " invitado " . $this->input->post("merber_mail"), "<warning>");
					faildResponse('El invitado no se pudo actualizar.', $this);
				}
			else {
				$this->general_mdl->writeLog("Error al eliminar, actualizar invitado usuario " . $valida_token["user_id"] . " invitado " . $this->input->post("merber_mail"), "<warning>");
				faildResponse('El correo/Id que intentas registrar ya cuenta con un registro completo.', $this);
			}
		}
	}
	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para eliminar un invitado
	 ***********************************************************************/
	public function DeleteInvited()
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

		//Se procede a eliminar el invitado requerido
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->user->DeleteInvited($data)) {
			$this->general_mdl->writeLog("Eliminacion de invitado usuario " . $valida_token["user_id"] . " invitado " . $this->input->post("id"), "<info>");
			successResponse('', 'El invitado ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar invitado usuario " . $valida_token["user_id"] . " invitado " . $this->input->post("id"), "<info>");
			faildResponse('El invitado no se pudo eliminar.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 18/08/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para enviar email de contacto
	 ***********************************************************************/
	function Contact()
	{
		$validaPost = $this->general_mdl->validapost(array('message'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		// $body = 'El usuario <b>' . $valida_token['name'] . '</b> con el correo <b>' . $valida_token['email'] . '</b> ha enviado el siguiente mensaje a contacto:<br>' . $this->input->post('message');

		// 		$body = "
		// 		<div style='background: url(https://aapy.com.mx/nuup/assets/img/info_bg.png) ; height:100%' >
		//     <div style='padding-bottom: 25px; padding-top: 50px; text-align: center;'>
		//         <img src='https://.mx/nuup/assets/img/info_logo.png' style='width:300px;'></a>
		// 		El usuario <b>" . $valida_token['name'] . "</b> con el correo <b>" . $valida_token['email'] . "</b> ha enviado el siguiente mensaje a contacto:
		//     </div>
		//     <div style='padding: 50px'>
		//         <div style=' clear: both;  background: #FFFFFF'>
		//             <div style='float: left;width: 100%; height: 40%;background: #FFFFFF;border-radius: 20px; padding-top: 40px; text-align: center;'>
		//                 <p style='font-size: 20px;padding-left: 5%;padding-top: 1%; width: 90%; letter-spacing: 0.1em;line-height: 2.6'>Para recuperar tu contraseña por favor presiona el botón</p>
		//                 <div style='text-align: center; padding: 20px 0px;'>
		//                    " . $this->input->post('message') . "
		//                 </div>
		//             </div>
		//         </div>
		//     </div>
		// </div>
		// 		";

        $body = "";

        // validar si es yastas o cualquier otro
        // ==============================================================================
        if($valida_token['business_id'] != 83){
            $fromname = "APPY Yastas";
            $body = "
                <div style='background: url(https://appy.com.mx/nuup/assets/img/info_bg.png) ; height:1000px'>
                    <div style='padding-bottom: 25px; padding-top: 50px; text-align: center; text-align: center;'>
                        <img style='text-align: center; left: calc(50% - 150px);' src='https://appy.com.mx/nuup/assets/img/info_logo.png' style='width:300px;'></a>
                        <p style='color: #FFFFFF;'>El usuario <b>" . $valida_token['name'] . "</b> con el correo <b>" . $valida_token['email'] . "</b>, 
                            número de empleado " . $valida_token['num_empleado'] . "<br>,  ha
                            enviado el siguiente mensaje a contacto:</p>
                    </div>
                    <div style='padding: 50px'>
                        <div style=' clear: both;  background: #FFFFFF'>
                            <div
                                style='float: left;width: 100%; height: 40%;background: #FFFFFF;border-radius: 20px; padding-top: 40px; text-align: center;'>
                                <div style='text-align: center; padding: 20px 0px;'>
                                    " . $this->input->post('message') . "
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			";
        }


        // validar si es bimbo
        // ==============================================================================
        if($valida_token['business_id'] == 83){
            $fromname = "JCF Bimbo";
            $host_servidor = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            
            if($host_servidor."/qa-nuup"){
                $url_background = $host_servidor."/qa-nuup/assets/img/info_bg_bimbo.png";
                $url_logo = $host_servidor."/qa-nuup/assets/img/info_logo_bimbo.png";
            }

            if($host_servidor."/qa-bimbo-nuup"){
                $url_background = $host_servidor."/qa-bimbo-nuup/assets/img/info_bg_bimbo.png";
                $url_logo = $host_servidor."/qa-bimbo-nuup/assets/img/info_logo_bimbo.png";
            }

            $body = "
                <div style='background: url(". $url_background. "); height:1000px'>
                    <div style='padding-bottom: 25px; padding-top: 50px; text-align: center; text-align: center;'>
                        <img style='text-align: center; left: calc(50% - 150px);' src='". $url_logo . "' style='width:300px;'></a>
                        <p style='color: #FFFFFF;'>El usuario <b>" . $valida_token['name'] . "</b> con el correo <b>" . $valida_token['email'] . "</b>, 
                            número de empleado " . $valida_token['num_empleado'] . ",<br>  ha
                            enviado el siguiente mensaje a contacto:</p>
                    </div>
                    <div style='padding: 50px'>
                        <div style=' clear: both;  background: #FFFFFF'>
                            <div
                                style='float: left;width: 100%; height: 40%;background: #FFFFFF;border-radius: 20px; padding-top: 40px; text-align: center;'>
                                <div style='text-align: center; padding: 20px 0px;'>
                                    " . $this->input->post('message') . "
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			";
        }

		$this->guardarMensaje($valida_token["user_id"], $this->input->post("message"));

		if($this->general_mdl->sendemail('Contacto', $body, "luis@kreativeco.com", $fromname)) {
            $this->general_mdl->sendemail('Contacto', $body, "jg@kreativeco.com", $fromname);
			$this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "home");
			$this->general_mdl->writeLog("Envio de email de contacto usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'Gracias, hemos recibido tu mensaje, en breve nos pondremos en contacto.', $this);
		} else {
			$this->general_mdl->writeLog("Error al enviar email de contacto usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El email no se ha enviado correctamente.', $this);
		}
	}

	public function guardarMensaje($user_id, $mensaje)
	{
		$datos = [];
		$datos["user_id"] = $user_id;
		$datos["mensaje"] = $mensaje;
		$this->user->guardar_mensaje($datos);
	}

	public function obtenerMensajesContacto()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$result = $this->user->obtenerMensajesContacto($valida_token["business_id"]);
		$this->general_mdl->writeLog("Listado de mensajes de contacto usuario" . $valida_token["user_id"], "<info>");
		successResponse($result, 'Mensajes de contacto', $this);
	}

	public function eliminarMensajeContacto()
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
		$id_mensaje = $this->input->post("id_mensaje");
		$result = $this->user->eliminarMensaje($id_mensaje);
		if ($result) {
			$this->general_mdl->writeLog("Mensaje de contacto eliminado usuario" . $valida_token["user_id"], "<info>");
			successResponse($result, 'Eliminado mensajes de contacto', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar mensaje de contacto usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('Error al eliminar mensaje de contacto', $this);
		}
	}

	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para guardar un nuevo numero de empleado
	 ***********************************************************************/
	public function SaveEmployee()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('number', 'email', 'job_id', 'group_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$data['business_id'] = $valida_token['business_id'];
		/***********************************************************************
		 *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
		 *		   mario.martinez.f@hotmail.es
		 *	Nota: Validamos que el numero de empleado no se encuentre registrado
		 ***********************************************************************/
		if ($this->user->ValidateNumberEmployee($data['number'])) {
			faildResponse('El numero de empleado ya se encuentra en registrado o en uso.', $this);
			return false;
		}
		if ($this->user->SaveEmployee($data)) {
			$this->general_mdl->writeLog("Registro de numero de empleado usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'El numero de empleado ha sido guardado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al registrar numero de empleado usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El numero de empleado no se pudo guardar.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Función para editar numeros de empleados
	 ***********************************************************************/
	public function UpdateEmployee()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('number', 'email', 'job_id', 'group_id', 'number_employee_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data = $this->input->post();
		$id = $data['number_employee_id'];
		unset($data['number_employee_id']);
		$update = $this->user->UpdateEmployee($id, $data);
		if ($update === 'number_in_use') {
			faildResponse('El numero de empleado ya se encuentra registrado.', $this);
			return false;
		}
		if ($update) {
			$this->general_mdl->writeLog("Actualizacion de numero de empleado usuario " . $valida_token["user_id"] . " numero de empleado " . $id, "<info>");
			successResponse('', 'El numero de empleado ha sido actualizado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al actualizar numero de empleado usuario " . $valida_token["user_id"] . " numero de empleado " . $id, "<warning>");
			faildResponse('El numero de empleado no se pudo actualizar.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener el listado de numeros de empleados
	 ***********************************************************************/
	public function NumberEmployeeList()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$users = $this->user->NumberEmployeeList($valida_token['business_id']);
		if ($users) {
			$this->general_mdl->writeLog("Listado de numeros de empleado usuario " . $valida_token["user_id"], "<info>");
			successResponse($users, 'Listado de numeros de empleados', $this);
		} else {
			$this->general_mdl->writeLog("Error de numeros de empleado usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros', $this);
		}
	}
	public function DeleteNumbreEmployee()
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
		$data = array(
			"id" => $this->input->post("id")
		);
		if ($this->user->DeleteNumbreEmployee($data)) {
			$this->general_mdl->writeLog("Eliminacion de numero de empleado usuario " . $valida_token["user_id"] . " numero de empleado " . $data["id"], "<info>");
			successResponse('', 'El numero de empleado ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar numero de empleado usuario " . $valida_token["user_id"] . " numero de empleado " . $data["id"], "<warning>");
			faildResponse('El numero de empleado no se pudo eliminar.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para aprobar un usuario que no contaba con invitacion
	 ***********************************************************************/
	public function AcceptInvitation()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'group_id'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}
		$data =  $this->input->post();
		if ($this->user->AcceptInvitation($data)) {
			$this->general_mdl->writeLog("Aprobacion de usuario sin invitacion usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'El usuario ha sido aprobado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al aprobar usuario sin invitacion usuario " . $valida_token["user_id"], "<info>");
			faildResponse('El usuario no ha podido se aprobado correctamente.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/11/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para cambiar de empresas en el admin
	 ***********************************************************************/
	public function SwitchBusiness()
	{
		if ($this->session->userdata('empresa_id')) {
			if ($this->input->post('type_user') === 'internal') {
				$this->session->set_userdata('empresa_id', EMPRESA_INTERNOS);
			} else {
				$this->session->set_userdata('empresa_id', EMPRESA_EXTERNOS);
			}
			$this->user->EditUser(array('id' => $this->session->userdata('id_user'), 'business_id' => $this->session->userdata('empresa_id')));
		}
		$this->general_mdl->writeLog("Cambio de empresa usuario " . $this->session->userdata("id_user"), "<info>");
		return true;
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/11/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para cambiar de empresa en sesion del admin maestro
	 ***********************************************************************/
	public function SwitchAdminBusiness()
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

		$this->session->set_userdata('empresa_id', $this->input->post("business_id"));

		if ($this->user->EditUser(array('id' => $this->session->userdata('id_user'), 'business_id' => $this->session->userdata('empresa_id')))) {
			$this->general_mdl->writeLog("Cambio de empresa admin maestro usuario " . $valida_token["user_id"] . " empresa " . $this->input->post("business_id"), "<info>");
			successResponse('', 'Cambio de empresa exitoso.', $this);
		} else {
			$this->general_mdl->writeLog("Error al cambiar empresa admin maestro usuario " . $valida_token["user_id"] . " empresa " . $this->input->post("business_id"), "<warning>");
			faildResponse('No se pudo hacer el cambio de empresa.', $this);
		}
	}

	public function CambiarGerenteRegion()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id_region'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		$this->session->set_userdata('id_region', $this->input->post("id_region"));

		if ($this->user->EditUser(array('id' => $this->session->userdata('id_user'), 'id_region' => $this->session->userdata('id_region')))) {
			$this->general_mdl->writeLog("Cambio de region del gerente " . $valida_token["user_id"] . " region " . $this->input->post("id_region"), "<info>");
			successResponse('', 'Cambio de region exitoso.', $this);
		} else {
			$this->general_mdl->writeLog("Error al cambiar de region usuario " . $valida_token["user_id"] . " region " . $this->input->post("id_region"), "<warning>");
			faildResponse('No se pudo hacer el cambio de region.', $this);
		}
	}
	/***********************************************************************
	 *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
	 *		   urisancer@gmail.com
	 *	Nota: Funcion para obtener los puestos
	 ***********************************************************************/
	public function JobList()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$params = array(
			'business_id' => $valida_token['business_id']
		);
		$users = $this->user->JobList($params);
		if ($users) {
			$this->general_mdl->writeLog("Listado de puestos usuario " . $valida_token["user_id"], "<info>");
			successResponse($users, 'Listado de puestos', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener puestos usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('No existen registros', $this);
		}
	}

	/***********************************************************************
	 *  Autor: Josue Ali Carrasco Ramirez   Fecha: 16/12/2020
	 *         josue.carrasco.ramirez@gmail.com
	 *  Nota: Funcion para guardar los nuevos puestos
	 ***********************************************************************/
	function SaveJobs()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('job_name'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		$data = array(
			'job_name' => $this->input->post("job_name"),
			'business_id' => $valida_token['business_id'],
			'active' => 1
		);

		if ($this->user->SaveJobs($data)) {
			$this->general_mdl->writeLog("Registro nuevo puesto usuario " . $valida_token["user_id"], "<info>");
			successResponse('', 'El puesto ha sido guardado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al guardar nuevo puesto usuario " . $valida_token["user_id"], "<warning>");
			faildResponse('El puesto no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *  Autor: Josue Ali Carrasco Ramirez   Fecha: 16/12/2020
	 *         josue.carrasco.ramirez@gmail.com
	 *  Nota: Funcion para modificar el nombre del puesto
	 ***********************************************************************/
	function EditJobs()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$validaPost = $this->general_mdl->validapost(array('id', 'job_name'), $this->input->post());
		if (!$validaPost['success']) {
			faildResponse($validaPost['msg'], $this);
			return;
		}

		//Se procede a editar el puesto requerido
		$data = array(
			'job_name' => $this->input->post("job_name"),
		);

		if ($this->user->EditJobs($this->input->post("id"), $data)) {
			$this->general_mdl->writeLog("Actualizacion de puesto usuario " . $valida_token["user_id"] . " puesto " . $this->input->post("id"), "<info>");
			successResponse('', 'El puesto ha sido guardado correctamente', $this);
		} else {
			$this->general_mdl->writeLog("Error al actualizar puesto usuario " . $valida_token["user_id"] . " puesto" . $this->input->post("id"), "<warning>");
			faildResponse('El puesto no se pudo guardar.', $this);
		}
	}

	/***********************************************************************
	 *  Autor: Josue Ali Carrasco Ramirez   Fecha: 16/12/2020
	 *         josue.carrasco.ramirez@gmail.com
	 *  Nota: Funcion para eliminar un puesto
	 ***********************************************************************/
	function DeleteJobs()
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
			'business_id' => $valida_token['business_id'],
			'job_id' => $this->input->post("id"),
			'rol_id' => $valida_token["rol_id"]
		);
		$users = $this->user->UserList($params);

		if ($users) {
			faildResponse('El puesto tiene usuarios asociados.', $this);
		} else {
			//Se procede a eliminar el puesto requerido
			$data = array(
				"id" => $this->input->post("id")
			);
			if ($this->user->DeleteJobs($data)) {
				$this->general_mdl->writeLog("Eliminacion de puesto usuario " . $valida_token["user_id"] . " puesto " . $params["job_id"], "<info>");
				successResponse('', 'El puesto ha sido eliminado correctamente.', $this);
			} else {
				$this->general_mdl->writeLog("Error al eliminar puesto usuario " . $valida_token["user_id"] . " puesto " . $params["job_id"], "<warning>");
				faildResponse('El puesto no se pudo eliminar.', $this);
			}
		}
	}

	function ObtenerUsuariosAdmin()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		$result = $this->user->ObtenerUsuariosAdmin($this->input->post("business_id"));
		if ($result) {
			$this->general_mdl->writeLog("Listado de usuarios admin usuario " . $valida_token["user_id"] . " puesto " . $this->input->post("id"), "<info>");
			successResponse($result, 'Listado de usuarios admin', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener usuarios admin usuario " . $valida_token["user_id"] . " puesto" . $this->input->post("id"), "<warning>");
			faildResponse('error al obtener usuarios admin.', $this);
		}
	}

	function crear_admin()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		// unset($this->input->post("token"));
		// echo json_encode($this->input->post());
		$data = [];
		$data["name"] = $this->input->post("name");
		$data["last_name"] = $this->input->post("last_name");
		$data["email"] = $this->input->post("email");
		$data["password"] = $this->input->post("password");
		$data["business_id"] = $this->input->post("business_id");
		$result = $this->user->crear_admin($data);
		if ($result) {
			$this->general_mdl->writeLog("Registro de usuario administrador usuario " . $valida_token["user_id"] . " puesto " . $this->input->post("id"), "<info>");
			successResponse($result, 'Administrador creado admin', $this);
		} else {
			$this->general_mdl->writeLog("Error al crear usuario admin usuario " . $valida_token["user_id"] . " puesto" . $this->input->post("id"), "<warning>");
			faildResponse('Error al crear usuario admin.', $this);
		}
	}

	function obtener_ranking()
	{
		$token = $this->input->post("token");
		$valida_token = $this->general_mdl->UsuarioDetalleToken($token);
		if (!$valida_token) {
			faildResponse($this->lang->line('token_error_msg'), $this);
			return;
		}
		$where = " WHERE u.business_id = " . $valida_token['business_id'] . " and u.active = 1";
		$result = $this->user_model->ranking_admin($where);
		if ($result) {
			$this->general_mdl->writeLog("Listado de ranking usuario " . $valida_token["user_id"] . " puesto " . $this->input->post("id"), "<info>");
			successResponse($result, 'Listado de ranking', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener ranking usuario " . $valida_token["user_id"] . " puesto" . $this->input->post("id"), "<warning>");
			faildResponse('Error al obtener ranking.', $this);
		}
	}

	//servicios para pantalla base asignar elementos a grupos

	public function desasociar_usuario()
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
		$id_usuario = $this->input->post("id_usuario");

		if ($this->user->desasociar_usuario($catalogo, $id_elemento, $id_usuario)) {
			$this->general_mdl->writeLog("Eliminacion de usuario en " . $catalogo . " usuario " . $valida_token["user_id"] . " elemento " . $this->input->post("id"), "<info>");
			successResponse('', 'El usuario ha sido eliminado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al eliminar usuario en $catalogo usuario " . $valida_token["user_id"] . " elemento " . $this->input->post("id"), "<warning>");
			faildResponse('El usuario no se pudo eliminar.', $this);
		}
	}

	public function asociar_usuario()
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
		$id_usuario = $this->input->post("id_usuario");

		if ($this->user->asociar_usuario($catalogo, $id_elemento, $id_usuario)) {
			$this->general_mdl->writeLog("Asociacion de usuario en " . $catalogo . " usuario " . $valida_token["user_id"] . " elemento " . $this->input->post("id"), "<info>");
			successResponse('', 'El usuario ha sido asociado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al asociar usuario en $catalogo usuario " . $valida_token["user_id"] . " elemento " . $this->input->post("id"), "<warning>");
			faildResponse('El usuario no se pudo asociar.', $this);
		}
	}

	public function actualizar_usuarios_elementos()
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
		$usuarios = json_decode($this->input->post("usuarios"), true);
		$id_elemento = $this->input->post("id_elemento");

		if ($this->user->actualizar_usuarios_elementos($catalogo, $usuarios, $id_elemento)) {
			$this->general_mdl->writeLog("Asociacion de usuarios en " . $catalogo . " usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("id"), "<info>");
			successResponse('', 'El usuario ha sido asociado correctamente.', $this);
		} else {
			$this->general_mdl->writeLog("Error al asociar usuarios en $catalogo usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("id"), "<warning>");
			faildResponse('El usuario no se pudo asociar.', $this);
		}
	}

	public function obtener_usuarios_de_asesores()
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
		$id_asesor = $this->input->post("id_asesor");
		$usuarios = $this->user->obtener_usuarios_de_asesores($id_asesor);
		if ($usuarios) {
			$this->general_mdl->writeLog("Obtener usuarios de asesores usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("id"), "<info>");
			successResponse($usuarios, 'Usuarios de asesores.', $this);
		} else {
			$this->general_mdl->writeLog("Error al obtener usuarios de asesores usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("id"), "<warning>");
			faildResponse('Usuarios de asesores.', $this);
		}
	}

	public function asignar_usuarios()
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

		$users = $this->input->post("users");
		$users = json_decode($users);

		foreach ($users as $index => $value) {
			$guardado = $this->user->asignar_usuarios_a_asesores(
				array(
					'id_asesor' => $this->input->post("id_asesor"),
					'user_id' => $value
				)
			);
			if (!$guardado) {
				$this->general_mdl->writeLog("Error al guardar usuarios en el asesor usuario " . $valida_token["user_id"] . " grupo " . $this->input->post("id_asesor"), "<warning>");
				faildResponse('Los usuarios no se pudieron guardar.', $this);
				return;
			}
		}
		$this->general_mdl->writeLog("Registro de usuarios al asesor usuario " . $valida_token["user_id"] . " grupo " . $this->input->post("id_asesor"), "<info>");
		successResponse('', 'Los usuarios han sido guardado correctamente', $this);
	}

	public function obtener_usuarios_asignables()
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
		$id_asesor = $this->input->post("id_asesor");
		$result = $this->user->obtener_usuarios_asignables($id_asesor);
		$this->general_mdl->writeLog("Listado de usuarios asignables a asesor usuario " . $valida_token["user_id"] . " grupo " . $this->input->post("id_asesor"), "<info>");
		successResponse($result, 'Listado de usuarios asignables a asesor', $this);
	}

	public function eliminar_usuario_de_asesor()
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
		$id_usuario = $this->input->post("id");
		$this->user->eliminar_usuario_de_asesor($id_usuario);
		$this->general_mdl->writeLog("Eliminar usuario de asesor usuario " . $valida_token["user_id"] . " grupo " . $this->input->post("id_asesor"), "<info>");
		successResponse('', 'Eliminar usuario de asesor', $this);
	}
}
