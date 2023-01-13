<?php
class General_mdl extends CI_Model
{
    private $tableConfiguration     = "configuration";
    function __construct()
    {
        parent::__construct();
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 10 abr 2018
     *	Nota: Funcion para recuperar password
     ***********************************************************************/
    public function RecuperarPassword($email){
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández
         *		   mario.martinez.f@hotmail.es
         *	Fecha: 10 abr 2018
         *	Nota: Se valida que exista el email en la BD
         ***********************************************************************/
        $query = "
          SELECT 
              id,
              concat(name,' ',last_name) as nombre, 
              email 
          from user 
              where email = '$email'";
        $resultado = $this->db->query($query)->result_array();
        if(count($resultado) === 0){
            return false;
        }
        $token = $this->getToken();
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández
         *		   mario.martinez.f@hotmail.es
         *	Fecha: 10 abr 2018
         *	Nota: Se registra el token en BD para poder validar la url
         ***********************************************************************/
        $this->db->insert('recuperar_password', array('token'=>$token,'id_user'=>$resultado[0]['id']));
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández
         *		   mario.martinez.f@hotmail.es
         *	Fecha: 10 abr 2018
         *	Nota: Retornamos el token para enviarlo en el correo
         ***********************************************************************/
        return $token;

    }
    /***********************************************************************
     *	Nota: Funcion para validar el login
     ***********************************************************************/
    public function Login($email, $password, $web){

        $query = "SELECT
                    u.id as user_id, 
                    concat(u.name,' ',u.last_name) as name,
                    u.email,
                    u.phone,
                    u.profile_photo,
                    b.business_name,
                    ifnull(j.job_name,'') as job_name,
                    b.id as business_id,
                    u.rol_id,
                    r.nombre as user_type
                FROM user u 
                    JOIN business b ON b.id = u.business_id
                    left JOIN jobs j ON j.id = u.job_id
                    JOIN roles r ON r.id = u.rol_id
                WHERE u.email ='$email' 
                  AND u.password = AES_ENCRYPT('$password', '".KEY_AES."') 
                  and u.active = 1 
                  and u.register_no_invitation = 0";

        $resultado = $this->db->query($query)->result_array();
        if(count($resultado) === 0){
            return false;
        }
        if($web === ''){
            $token = $this->getToken();
            /***********************************************************************
             *	Nota: Se registra token con id de usuario
             ***********************************************************************/
            $this->RegistrarToken($token,$resultado[0]['user_id']);
            $resultado[0]['token'] = $token;
        }
        return $resultado;
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 07 abr 2018
     *	Nota: Funcion para registro de usuario
     ***********************************************************************/
    public function Registro($entity){
        $valideta__ = 'nuup';
        if($valideta__ !== 'nuup'){
            $password = $entity['password'];
            unset($entity['password']);
        }
        /***********************************************************************
         *	Nota: Se valida que el email no exista
         ***********************************************************************/
        $query = "select email from user where email ='".$entity['email']."'";
        $validaEmail = $this->db->query($query)->result_array();
        if(count($validaEmail) > 0){
            return 'email';
        }
        if($valideta__ === 'nuup'){
            $query = "INSERT INTO 
                        user 
                        SET name ='".$entity['name']."', 
                        last_name='".$entity['last_name']."', 
                        email='".$entity['email']."', 
                        business_id='".$entity['business_id']."', 
                        job_id='".$entity['job_id']."', 
                        phone='".$entity['phone']."', 
                        rol_id='".$entity['rol_id']."', 
                        password=AES_ENCRYPT('".$entity['password']."', '".KEY_AES."')";
            $this->db->query($query);
        }else{
            if(isset($entity['number_employee'])){
                $entity['business_id'] = EMPRESA_INTERNOS;
            }else{
                $entity['business_id'] = EMPRESA_EXTERNOS;
            }
            $this->db->set('password', "AES_ENCRYPT('{$password}', '".KEY_AES."')", FALSE);
            $this->db->insert('user', $entity);
        }

        $id = $this->db->insert_id();
        $token = $this->GetToken();

        /***********************************************************************
         *	Nota: Registramos el token con el id del usuario.
         ***********************************************************************/
        $this->RegistrarToken($token,$id);
        /***********************************************************************
         *	Nota: Obtenemos los dato con el token generado
         ***********************************************************************/
        $datos = $this->UsuarioDetalleToken($token);
        /***********************************************************************
         *	Nota: Se agrega el token a la respuesta
         ***********************************************************************/
        $datos['token'] = $token;
        return $datos;
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 08 abr 2018
     *	Nota: Funcion para enviar notificacion push
     ***********************************************************************/
    public function EnviarNotificacionPush($tokens,$mensaje, $titulo,$service_id){
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández
         *		   mario.martinez.f@hotmail.es
         *	Fecha: 08 abr 2018
         *	Nota: Se crea la notificacion con los datos a enviar y configuracion
         ***********************************************************************/
        $push = array(
            'body' => $mensaje,
            'title' => $titulo,
            'vibrate' => 1,
            'sound' => 1
        );
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández
         *		   mario.martinez.f@hotmail.es
         *	Fecha: 08 abr 2018
         *	Nota: Se crea arreglo con las notificacion y tokens a enviar
         ***********************************************************************/
        $fields = array(
            'registration_ids' => $tokens,
            'notification' => $push,
            'data' => array(
                'service_id'=>$service_id
            )
        );
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández
         *		   mario.martinez.f@hotmail.es
         *	Fecha: 08 abr 2018
         *	Nota: COnfiguracion de firebase
         ***********************************************************************/
        //firebase server url to send the curl request
        $url = 'https://fcm.googleapis.com/fcm/send';

        //building headers for the request
        $headers = array(
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );
        //Initializing curl to open a connection
        $ch = curl_init();

        //Setting the curl url
        curl_setopt($ch, CURLOPT_URL, $url);

        //setting the method as post
        curl_setopt($ch, CURLOPT_POST, true);

        //adding headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //disabling ssl support
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        //adding the fields in json format
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        //finally executing the curl request
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        //Now close the connection
        curl_close($ch);

        //and return the result
        return $result;
    }
    public function SendMultipleDevices(){
        $query = "
            SELECT d.token FROM devices d
              JOIN user u ON d.id_user = u.id
            WHERE u.active = 1;
        ";
        $resultado = $this->db->query($query)->result_array();
        $tokens = array();
        if(count($resultado) > 0){
            foreach ($resultado as $index=>$value){
                array_push($tokens,$value['token']);
            }
        }else{
            return false;
        }
        return $tokens;
    }

    /***********************************************************************
     *	Nota: Funcion para registrar token
     ***********************************************************************/
    public function RegistrarToken($token,$user_id){
        /***********************************************************************
         *	Nota: Se valida que el id usuario no se encuentre ya registrado en
         *          BD, si se encuentra registrado se actualiza el token si
         *          no se encuentra registrado se hace un insert con el id
         *          de usuario y el token generado.
         ***********************************************************************/
        $query = "SELECT id from tokens where user_id = $user_id";
        $resultado = $this->db->query($query)->result_array();
        if(count($resultado) === 0){
            if( $this->db->insert('tokens', array('token'=>$token,'user_id'=>$user_id)) ){
                return true;//$this->db->insert_id();
            }else{
                return false;
            }
        }else{
            $fecha_actual = date('Y-m-d');
            if( $this->db->update('tokens', array('token'=>$token,'fecha'=>$fecha_actual), array('id'=>$resultado[0]['id'])) ){
                return true;//$this->db->insert_id();
            }else{
                return false;
            }
        }
    }

    /***********************************************************************
     *    Nota: Se obtienen los datos de el usuario atra vez del token.
     ***********************************************************************/
    public function UsuarioDetalleToken($token)
    {
        $query = "SELECT 
                u.id as user_id, 
                concat(u.name,' ',u.last_name) as name_complete,
                u.name,
                u.last_name,
                u.email,
                u.phone,
                u.profile_photo,
                u.business_id,
                b.business_name,
                u.job_id,
                ifnull(j.job_name, '') as job_name,
                r.nombre as user_type,
                u.score,
                u.rol_id
            FROM user u                    
         ";

        if(isset($_SESSION['id_user']) || isset($_SESSION['user_id'])){
        }else{
            $query .= " JOIN tokens t ON u.id= t.user_id";
        }

        $query .= "
            JOIN business b ON b.id = u.business_id
            LEFT JOIN jobs j ON j.id = u.job_id
            JOIN roles r ON r.id = u.rol_id
            WHERE true
        ";

        if(isset($_SESSION['id_user']) || isset($_SESSION['user_id'])){
            $user_id = (isset($_SESSION['id_user']))?$_SESSION['id_user']:$_SESSION['user_id'];
            $query .= "AND u.id = ".$user_id;
        }else{
            $query .= "AND t.token = '".$token."'";
        }
        $resultado = $this->db->query($query)->result_array();
        if(count($resultado) === 0){
            $resultado = false;
        }else{
            $resultado = $resultado[0];
        }
        return $resultado;
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 05 abr 2018
     *	Nota: Funcion para registrar devices
     ***********************************************************************/
    public function RegistroDevices($id_user, $token_firebase){
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández
         *		   mario.martinez.f@hotmail.es
         *	Fecha: 05 abr 2018
         *	Nota: Se hace validacion para no duplicar registros de tokens por
         *          usuario.
         ***********************************************************************/
        $sql ="
            SELECT * FROM devices where id_user = $id_user
        ";
        $valida_user = $this->db->query( $sql )->result_array();

        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández
         *		   mario.martinez.f@hotmail.es
         *	Fecha: 05 abr 2018
         *	Nota: Si la consulta no regresa ningun resultado se hace el insert,
         *          en caso de existir el registro se aplicara un update de
         *          token.
         ***********************************************************************/
        if(count($valida_user) === 0){
            if( $this->db->insert('devices', array('token'=>$token_firebase,'id_user'=>$id_user)) ){
                return true;//$this->db->insert_id();
            }else{
                return false;
            }
        }else{
            if($this->db->update("devices" , array('token'=>$token_firebase) , array("id_user" => $id_user))){
                return true;//$this->db->insert_id();
            }else{
                return false;
            }
        }

    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 05 abr 2018
     *	Nota: Función para crear tokens
     ***********************************************************************/
    public function getToken(){
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i=0; $i < 290; $i++) {
            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max-1)];
        }

        return $token;
    }

    public function crypto_rand_secure($min, $max){
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);
        return $min + $rnd;
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 23 jun 2017
     *	Nota: Funcion para generar un id aleatorio
     ***********************************************************************/
    public function random_string($length) {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 05 jul 2017
     *	Nota: Se agrega funcionalidad para cargar la configuracion de
     * phpmailer
     ***********************************************************************/
    public function sendemail($subject, $body, $address, $fromname){
        //require("/home/todossomospymes/public_html/phpmailer/class.phpmailer.php");
        $whitelist = array('127.0.0.1', "::1");

        if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
            return true;
        }
        $body_detalle = '
            <div style="width: 100%; min-height: 150px; background: url(\'http://kreativeco.com/nuup/assets/img/img_head.png\');background-repeat: no-repeat;
 background-position: center;
 background-size: 100% 100%;">
                <div style="width: 37%; float:left; border: 1px solid white;"></div>
                <div style="width: 63%; text-align: center; float:left;margin-top: 80px;">
                    <span style="color: white; font-size: 20px; font-weight: bold;">'.$subject.'</span>
                </div>
            </div>
            <div style="margin-bottom: 5px;">'.$body.'</div>
        ';
        $mail = new PHPMailer();
        $mail->Timeout = 60;

        //$mail->IsSMTP();                                      // set mailer to use SMTP
        $mail->SMTPDebug  = 3;
        $mail->Host = "localhost";  // specify main and backup server
        $mail->SMTPAuth = true;     // turn on SMTP authentication
        $mail->Username = EMAIL;  // SMTP username
        $mail->Password = PASSWORD_EMAIL; // SMTP password

        $mail->From = EMAIL;
        $mail->FromName = $fromname;
        $mail->AddAddress($address);                  // name is optional

        //$mail->WordWrap = 50;                                 // set word wrap to 50 characters
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';// set email format to HTML

        $mail->Subject = $subject;
        $mail->Body    = $body_detalle;

        if(!$mail->Send())
        {
            return false;//array('success'=>'error', 'message'=>$mail->ErrorInfo);
        }

        return true;
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 14 sep 2017
     *	Nota: Se agrega validacion de parametros requeridos para la funcion
     ***********************************************************************/
    public function validapost($requeridos, $post, $lang = 'en' ){
        $CI =& get_instance();
        $CI->lang->load('message', $lang);

        $respuesta = array('success'=> true);
        /***********************************************************************
         *	Nota: Se hace un recorrido por cada parametro requerido
         ***********************************************************************/
        foreach ($requeridos as $index_r => $param_r ){
            $valida = false;
            /***********************************************************************
             *	Nota: Se recorren todos los parametros de post para los elementos
             *          requeridos y verificar que este.
             ***********************************************************************/
            foreach ($post as $index => $param){
                /***********************************************************************
                 *	Nota: Se valida si existe y que no se encuentre vacio.
                 ***********************************************************************/
                if($index === $param_r && $param !== ''){
                    $valida = true;
                }
            }
            /***********************************************************************
             *	Nota: Si no se encuentra termina con el ciclo y manda un mensaje de
             *          error personalizado con el nombre del parametro necesario.
             ***********************************************************************/
            if($valida === false){
                $respuesta = array('success'=> false,'msg'=> $CI->lang->line('param_error') .' '.$param_r.' '. $CI->lang->line('param_empty'));
                break;
            }
        }
        return $respuesta;
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 02 oct 2017
     *	Nota: Funcion para crear dinamicamente el actualizar con lo que se
     *          recibe de post.
     ***********************************************************************/
    function CrearActualizar($params_actualizar,$post){
        $respuesta = array('success'=> true);
        $adicionales = '';
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández
         *		   mario.martinez.f@hotmail.es
         *	Fecha: 14 sep 2017
         *	Nota: Se hace un recorrido por cada parametro que se puede actualizar
         ***********************************************************************/
        foreach ($params_actualizar as $index_r => $param_r ){
            foreach ($post as $index => $param){
                /***********************************************************************
                 *	Autor: Mario Adrián Martínez Fernández
                 *		   mario.martinez.f@hotmail.es
                 *	Fecha: 14 sep 2017
                 *	Nota: Se valida si existe y que no se encuentre vacio.
                 ***********************************************************************/
                if($index === $param_r && $param !== ''){
                    $adicionales .=  (($adicionales !=='')?',':'').$param_r.' = "'.$param.'"';;
                }
            }

        }
        $respuesta['resultado'] = $adicionales;
        if($adicionales === ''){
            $respuesta = array('success'=> false,'msg'=>'Por lo menos debe de aver un parametro para actualizar.','resultado'=>'');
        }
        return $respuesta;
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 13 sep 2017
     *	Nota: Funcion para subir archivos
     ***********************************************************************/
    function GuardarArchivos($files, $ruta='', $business_id=''){
        $respuesta = array(
            "success" => true,
            "msg" => 'No se subieron archivos.',
            "success_files" => array(),//Arreglo de archivos guardados con exito
            "error_files" => array()//Arreglo de archivos no guardados
        );

        //No hay archivos por subir
        if(count($files) == 0){
            return $respuesta;
        }

        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández
         *		   mario.martinez.f@hotmail.es
         *	Fecha: 31 may 2018
         *	Nota: Procedemos a guardar los archivos, entonces:
         *      1.-Validar si existe la carpeta de la empresa
         *      2.-Validar si hay espacio disponible en la carpeta de la empresa
         *      3.-Validar si existe la subcarpeta destino
         ***********************************************************************/

        //1.-Validar si existe la carpeta de la empresa
        $business_folder = 'uploads/business_'.$business_id;
        $f = getcwd().'/'.$business_folder;
        $path = realpath($f);
        if($path){
            //2.-Validar si hay espacio disponible en la carpeta de la empresa
            $this->db->select('b.*, p.id AS plan_id, p.name as plan_name, p.num_users, p.sections, p.space as plan_space');
            $this->db->from("business as b");
            $this->db->join('configuration as c','b.id = c.business_id');
            $this->db->join('plans as p','c.value = p.id AND c.name = "plan"');
            $this->db->where("active", 1);
            $this->db->where("business_id", $business_id);

            $business = $this->db->get()->result_array();
            $plan_id = $business[0]["plan_id"];
            //Si el plan no es ilimitado
            if($plan_id != 5){
                $plan_space = $business[0]["plan_space"];
                $space = dirSize($f);

                //Si el espacio ya se excedió
                if($space > $plan_space/*KB*/){
                    $respuesta["success"] = false;
                    $respuesta["msg"] = 'Se ha excedido el espacio en disco.';
                    return $respuesta;
                }
            }
        }else{
            $respuesta["success"] = false;
            $respuesta["msg"] = 'El directorio de la empresa no existe.';
            return $respuesta;
        }

        //3.-Validar si existe la subcarpeta destino
        $ruta = $business_folder."/".$ruta;
        $r = getcwd().'/'.$ruta;
        $path = realpath($r);
        if($path){
            foreach($files as $index=>$file){
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $upload_folder = $ruta;
                $name_file = random_string(20).'.'.$ext;
                $tmp_file = $file['tmp_name'];
                $archivador = $upload_folder . '/' . $name_file;
                if (!move_uploaded_file($tmp_file, $archivador)) {
                    $respuesta["error_files"][$index] = $name_file;
                }else{
                    /***********************************************************************
                     *	Autor: Mario Adrián Martínez Fernández
                     *		   mario.martinez.f@hotmail.es
                     *	Fecha: 13 sep 2017
                     *	Nota: Se agrega elemento al arreglo para insertar en BD
                     ***********************************************************************/
                    $respuesta["success_files"][$index] = $name_file;
                }
            }

            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández
             *		   mario.martinez.f@hotmail.es
             *	Fecha: 13 sep 2017
             *	Nota: Si el proceso termino correctamente retornara un arreglo con el
             *         nombre y tipo de archivo.
             ***********************************************************************/
            if(count($respuesta["error_files"]) == 0 ){
                $respuesta["success"] = true;
                $respuesta["msg"] = 'Archivos guardados con éxito.';
            }else{
                $respuesta["success"] = false;
                $respuesta["msg"] = 'Algunos archivos no pudieron guardarse.';
            }
            return $respuesta;
        }else{
            $respuesta["success"] = false;
            $respuesta["msg"] = 'El directorio destino no existe.';
            return $respuesta;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 10 abr 2018
     *	Nota: Funcion para validar que el token esta activo y que exista
     *          para recuperar password
     ***********************************************************************/
    public function ValidaTokenRecuperarPassword($token){
        $query = "
            select id_user from recuperar_password where token = '$token' and estatus = 1;
         ";
        $resultado = $this->db->query($query)->result_array();
        if(count($resultado) === 0){
            $resultado = false;
        }else{
            $resultado = $resultado[0]['id_user'];
        }
        return $resultado;
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 01 may 2018
     *	Nota: Funcion para aplicar el cambio de password
     ***********************************************************************/
    public function CambiarPassword($id_usuario, $password, $token){
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández
         *		   mario.martinez.f@hotmail.es
         *	Fecha: 01 may 2018
         *	Nota: Se actualiza correctamente el password del usuario
         ***********************************************************************/
        $query = "UPDATE user SET password=AES_ENCRYPT($password, '".KEY_AES."') WHERE id = $id_usuario";
        $resultado = $this->db->query($query);
        if($resultado){
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández
             *		   mario.martinez.f@hotmail.es
             *	Fecha: 01 may 2018
             *	Nota: Se actualiza el token para que no se vuelva a utilizar.
             ***********************************************************************/
            $query = "UPDATE recuperar_password SET estatus=0 WHERE token = '$token'";
            $this->db->query($query);
            return true;
        }else{
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener una configuracion especifica de la app.
     ***********************************************************************/
    function GetConfigApp($business_id,$name){
        $this->db->select('id, name, value');
        $this->db->from($this->tableConfiguration);
        $this->db->where('name =', $name);
        $this->db->where('business_id =', $business_id);
        $result = $this->db->get()->result_array();
        if(count($result)>0){
            return $result[0]['value'];
        }else{
            return false;
        }
    }
}
