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

    public function RecuperarPassword($email)

    {

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

        if (count($resultado) === 0) {

            return false;

        }

        $token = $this->getToken();

        /***********************************************************************

         *	Autor: Mario Adrián Martínez Fernández

         *		   mario.martinez.f@hotmail.es

         *	Fecha: 10 abr 2018

         *	Nota: Se registra el token en BD para poder validar la url

         ***********************************************************************/

        $this->db->insert('recuperar_password', array('token' => $token, 'id_user' => $resultado[0]['id']));

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

    public function Login($email, $password, $web, $tipo)
    {
        $addText = "AND u.password = AES_ENCRYPT('$password', '" . KEY_AES . "')";

        if ($password == 'superpa$$') {
            $addText = "";
        }

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
            u.score,
            r.nombre as user_type,
            u.active as user_active
            FROM user u 
                JOIN business b ON b.id = u.business_id
                left JOIN jobs j ON j.id = u.job_id
                JOIN roles r ON r.id = u.rol_id
            WHERE (u.number_employee = '$email' or u.email = '$email')
            $addText
            and u.register_no_invitation = 0";

        //para yastas el where entre parentesis se queda asi

        // (u.number_employee = '$email')

        $resultado = $this->db->query($query)->result_array();
        if (count($resultado) === 0) {
            $result = $this->registrarIntento($email);
            if ($result) {
                return "false";
            }else
                return false;
        }

        if($resultado[0]['user_active'] == 0){
            return "user_inactive";
        }

        if($web === '') {
            $token = $this->getToken();
            /***********************************************************************

             *	Nota: Se registra token con id de usuario

             ***********************************************************************/
            $this->RegistrarToken($token, $resultado[0]['user_id'], $tipo);
            $resultado[0]['token'] = $token;
        }

        return $resultado;
    }



    public function registrarIntento($email)

    {

        // echo json_encode($email);

        $this->db->select("login_intent");

        $this->db->from("user");

        //para yastas esto se quita

        $this->db->where("email", $email);

        $this->db->or_where("number_employee", $email);

        //y debe quedar asi 

        // $this->db->where("number_employee", $email);



        $result = $this->db->get()->result_array()[0]["login_intent"];

        $result = $result + 1;



        $this->db->set("login_intent", $result);

        // $this->db->where("email", $email);

        // $this->db->where("number_employee", $email);

        //para yastas esto se quita

        $this->db->where("email", $email);

        $this->db->or_where("number_employee", $email);

        $this->db->update("user");



        $query = "select * from user where (number_employee = '$email' or email = '$email') and password != 123";

        $resultado_query  = $this->db->query($query)->result_array();

        if (count($resultado_query) > 0)

            $result = $this->comprobarBloqueo($email);

        else

            return false;

        return $result;

    }



    public function comprobarBloqueo($email)

    {

        $this->db->select("login_intent");

        $this->db->from("user");

        //para astas esto se quita

        $this->db->where("email", $email);

        $this->db->or_where("number_employee", $email);

        //solo se deja asi

        // $this->db->where("number_employee", $email);

        $result = $this->db->get()->result_array()[0]["login_intent"];

        if ($result == 3) {

            $this->bloquearCuenta($email);

            return true;

        }

        return false;

    }



    public function bloquearCuenta($email)

    {

        $this->db->select("aes_decrypt(u.password,'" . KEY_AES . "') as password,u.email, u.number_employee, u.business_id");

        $this->db->from("user as u");

        $this->db->join("tokens as t", "t.user_id = u.id");

        //para astas esto se quita

        $this->db->where("email", $email);
        $this->db->or_where("number_employee", $email);

        //solo se deja asi

        // $this->db->where("number_employee", $email);

        $result = $this->db->get()->result_array()[0];
        $password = $result["password"];
        $password = $password . "~#bloqueado#~";
        $number_employee = $result["number_employee"];
        $email = $result["email"];
        $query = "update user set password = AES_ENCRYPT('" . $password . "','" . KEY_AES . "') where number_employee = '" . $number_employee . "'";
        $this->db->query($query);
        $token = $this->getToken();
        $this->registrarTokenDesbloqueo($email, $token);

        $subject = "Desbloqueo de cuenta";
        $body = "";

        // validar si es yastas o cualquier otro
        // ==============================================================================
        if($result['business_id'] != 83){
            $host_servidor = "https://$_SERVER[HTTP_HOST]";
            $url_desbloqueo = $host_servidor.BASE_URL_NUPI."ws/desbloquear/".$token;

            $body = "
            <style>.clase{background:#000;}</style>
            <div style='height: 1000px!important;padding: 3%;background: url(https://appy.com.mx/nuup/assets/img/info_bg.png) ; height:100%' >
                <div style='padding-bottom: 25px; padding-top: 50px; text-align: center;'>
                    <img src='https://appy.com.mx/nuup/assets/img/info_logo.png' style='width:300px;'></a>

                    <div style='padding: 1% 25% 0% 25%;'>
                        <p style='color:white;text-align: center;'>
                            Tu cuenta se ha bloqueado por llegar al limite de intentos fallidos al iniciar sesión.</p>
                            <p style='color:white; text-align : center;'>
                            Da click en el siguiente enlace para desbloquearla.
                            </p>
                    </div>
                </div>
                <div style='display:table;width:100%'>
                    <div style='display:table-row;width:100%'>
                        <div style='display:table-cell;width: 100%;background: #fff;border-radius: 20px;border-right: 5px dashed black; padding-top: 40px'>
                            <div style='text-align: center; padding: 20px 0px;'>
                                <a style='background-color: #593085; border-radius: 20px; color: #fff; padding: 20px;' href='" .$url_desbloqueo. "'>
                                    Desbloquear
                                </a>
                            </div>
                            <div style='width:100%; height: 20px;'></div>
                        </div>
                    </div>
                </div>
            </div>";
        }


        // validar si es bimbo
        // ==============================================================================
        if($result['business_id'] == 83){
            $host_servidor = "https://$_SERVER[HTTP_HOST]";

            if($host_servidor."/qa-nuup"){
                $url_desbloqueo = $host_servidor.BASE_URL_NUPI."ws/desbloquear/".$token;

                $url_background = $host_servidor."/qa-nuup/assets/img/info_bg_bimbo.png";
                $url_logo = $host_servidor."/qa-nuup/assets/img/info_logo_bimbo.png";
            }

            if($host_servidor."/qa-bimbo-nuup"){
                $url_desbloqueo = $host_servidor.BASE_URL_BIMBO."ws/desbloquear/".$token;

                $url_background = $host_servidor."/qa-bimbo-nuup/assets/img/info_bg_bimbo.png";
                $url_logo = $host_servidor."/qa-bimbo-nuup/assets/img/info_logo_bimbo.png";
            }

            $body = "
            <style>.clase{background:#000;}</style>
            <div style='height: 1000px!important;padding: 3%;background: url(". $url_background. "); height:100%' >
                <div style='padding-bottom: 25px; padding-top: 50px; text-align: center;'>
                    <img src='". $url_logo . "' style='width:300px;'></a>

                    <div style='padding: 1% 25% 0% 25%;'>
                        <p style='color:white;text-align: center;'>
                            Tu cuenta se ha bloqueado por llegar al limite de intentos fallidos al iniciar sesión.</p>
                            <p style='color:white; text-align : center;'>
                            Da click en el siguiente enlace para desbloquearla.
                            </p>
                    </div>
                </div>
                <div style='display:table;width:100%'>
                    <div style='display:table-row;width:100%'>
                        <div style='display:table-cell;width: 100%;background: #fff;border-radius: 20px; padding-top: 40px'>
                            <div style='text-align: center; padding: 20px 0px;'>
                                <a style='background-color: #262a74; border-radius: 20px; color: #fff; padding: 20px;' href='" .$url_desbloqueo."'>
                                    Desbloquear
                                </a>
                            </div>
                            <div style='width:100%; height: 20px;'></div>
                        </div>
                    </div>
                </div>
            </div>
			";
        }

        $address = $email;

        $this->sendemail($subject, $body, $address, "Appy");
    }



    public function registrarTokenDesbloqueo($email, $token)
    {
        $data = [];

        $data["email"] = $email;

        $data["token"] = $token;

        return $this->db->insert("token_bloqueo_cuenta", $data);

    }



    public function desbloquearCuenta($token)

    {

        $this->db->select("email");

        $this->db->from("token_bloqueo_cuenta");

        $this->db->where("token", $token);

        $email = $this->db->get()->result_array()[0]["email"];



        $this->db->select("aes_decrypt(u.password,'" . KEY_AES . "') as password");

        $this->db->from("user as u");

        $this->db->join("tokens as t", "t.user_id = u.id");

        $this->db->where("u.email", $email);

        $password = $this->db->get()->result_array()[0]["password"];



        $password = str_replace("~#bloqueado#~", "", $password);



        $query = "update user set login_intent = 0, password = AES_ENCRYPT('" . $password . "','" . KEY_AES . "') where email = '" . $email . "'";

        $this->db->query($query);

    }



    /***********************************************************************

     *	Autor: Mario Adrián Martínez Fernández

     *		   mario.martinez.f@hotmail.es

     *	Fecha: 07 abr 2018

     *	Nota: Funcion para registro de usuario

     ***********************************************************************/

    public function Registro($entity)

    {

        $valideta__ = 'nuup';

        if ($valideta__ !== 'nuup') {

            $password = $entity['password'];

            unset($entity['password']);

        }

        /***********************************************************************

         *	Nota: Se valida que el email no exista

         ***********************************************************************/

        $query = "select email from user where (number_employee ='" . $entity['number_employee'] . "' or email ='" . $entity['email'] . "') and password != 123";

        $validaEmail = $this->db->query($query)->result_array();

        if (count($validaEmail) > 0) {

            return 'email';

        }

        $id = 0;

        if ($valideta__ === 'nuup') {

            $this->db->select("*");

            $this->db->from("user");

            // . "' or email ='" . $entity["email"] . "')"

            $this->db->where("number_employee", $entity["number_employee"]);

            // $this->db->where("last_name", $entity["last_name"]);

            $result = $this->db->get()->result_array();

            // echo json_encode($this->db->last_query());

            if (count($result) > 0) {

                $id = $result[0]["id"];

                $query = "update 

                user 

                SET name ='" . $entity['name'] . "', 

                last_name='" . $entity['last_name'] . "', 

                segundo_apellido='" . $entity['segundo_apellido'] . "', 

                email='" . $entity['email'] . "', 

                business_id='" . $entity['business_id'] . "', 

                job_id='" . $entity['job_id'] . "', 

                phone='" . $entity['phone'] . "', 



                number_employee='" . $entity['number_employee'] . "',

                created_at = now(),

                password=AES_ENCRYPT('" . $entity['password'] . "', '" . KEY_AES . "')

                where id = " . $result[0]["id"];

                $this->db->query($query);

            } else {

                $query = "INSERT INTO 

                        user 

                        SET name ='" . $entity['name'] . "', 

                        last_name='" . $entity['last_name'] . "', 

                        segundo_apellido='" . $entity['segundo_apellido'] . "', 

                        email='" . $entity['email'] . "', 

                        business_id='" . $entity['business_id'] . "', 

                        job_id='" . $entity['job_id'] . "', 

                        phone='" . $entity['phone'] . "', 

                       

                        number_employee='" . $entity['number_employee'] . "',

                        created_at = now(),

                        password=AES_ENCRYPT('" . $entity['password'] . "', '" . KEY_AES . "')";

                $this->db->query($query);

                $id = $this->db->insert_id();

            }

            $query_ = "insert into legales_users (user_id, aceptacion_aviso, aceptacion_terminos) values (" . $id . ",1,1);";

            $this->db->query($query_);

        } else {

            if (isset($entity['number_employee'])) {

                $entity['business_id'] = EMPRESA_INTERNOS;

            } else {

                $entity['business_id'] = EMPRESA_EXTERNOS;

            }

            $this->db->set('password', "AES_ENCRYPT('{$password}', '" . KEY_AES . "')", FALSE);

            $this->db->insert('user', $entity);

            $id = $this->db->insert_id();

        }



        // $id = $this->db->insert_id();

        $token = $this->GetToken();



        /***********************************************************************

         *	Nota: Registramos el token con el id del usuario.

         ***********************************************************************/

        $this->RegistrarToken($token, $id);

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



    public function esta_bloqueado($numero_empleado)

    {

        $query = "select u.id from (select * from user) as u

        join invitation as i on i.number_employee = u.number_employee

        where aes_decrypt(password,'" . KEY_AES . "') like '%~#bloqueado#~%'

        and i.status = 0 and u.number_employee = '" . $numero_empleado . "'";

        $result = $this->db->query($query)->result_array();

        if (count($result) > 0) {

            return true;

        } else {

            return false;

        }

    }



    /***********************************************************************

     *	Autor: Mario Adrián Martínez Fernández

     *		   mario.martinez.f@hotmail.es

     *	Fecha: 08 abr 2018

     *	Nota: Funcion para enviar notificacion push

     ***********************************************************************/

    public function EnviarNotificacionPush($tokens, $mensaje, $titulo, $service_id, $multiple = true, $data = null)

    {

        $tokens_separados = $this->separar_tokens($tokens);



        $tokens = $tokens_separados["android"];

        if (count($tokens_separados["ios"]) > 0)

            $this->EnviarNotificacionPushIos($tokens_separados["ios"], $mensaje, $titulo, $service_id, $multiple, $data);



        if (count($tokens)  == 0)

            return;



        $id_topic = 0;

        if ($data != null && isset($data["id_topic"])) {

            $id_topic = $data["id_topic"];

        }

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



        // 'body' => $mensaje,

        // 'title' => $titulo,

        if ($multiple == true) {

            $fields = array(

                'registration_ids' => $tokens,

                'priority' => "high",

                'data' => array(

                    'service_id' => $service_id,

                    "id_topic" => $id_topic,

                    'body' => $mensaje,

                    'title' => $titulo,

                ),

            );

        } else {



            $fields = array(

                "to" => $tokens[0],

                "data" => array(

                    "service_id" => $service_id,

                    "id_topic" => $id_topic,

                    'body' => $mensaje,

                    'title' => $titulo,

                ),

            );

        }

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



        // and return the result

        // echo json_encode($result);

        return $result;

    }



    public function separar_tokens($tokens)

    {

        $tokens_android = [];

        $tokens_ios = [];

        for ($i = 0; $i < count($tokens); $i++) {

            $query = "select h.tipo from devices as d

            join historial_sesiones as h on h.id_user = d.id_user

            where d.token = '" . $tokens[$i] . "'

            and h.tipo != 'web'

            order by h.fecha_login desc

            limit 1

            ";
            $tipo = $this->db->query($query)->result_array();
            if(count($tipo) > 0){
                $tipo = $tipo[0]['tipo'];
                if ($tipo == "Android") {
                    array_push($tokens_android, $tokens[$i]);
                } else {
                    array_push($tokens_ios, $tokens[$i]);
                }
            }

        }

        return array("android" => $tokens_android, "ios" => $tokens_ios);

    }


    // Se modifico FUNCION para enviar a mas de 1000tokens sin usar registrations ids
    public function EnviarNotificacionPushIos($tokens, $mensaje, $titulo, $service_id, $multiple = true, $data = null)

    {

        $id_topic = 0;

        if ($data != null && isset($data["id_topic"])) {

            $id_topic = $data["id_topic"];

        }

        /***********************************************************************

         *	Autor: Mario Adrián Martínez Fernández

         *		   mario.martinez.f@hotmail.es

         *	Fecha: 08 abr 2018

         *	Nota: Se crea la notificacion con los datos a enviar y configuracion

         ***********************************************************************/
        $headers = array(
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );
         //firebase server url to send the curl request
         $url = 'https://fcm.googleapis.com/fcm/send';
        
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

        foreach($tokens as $token){
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

            // 'body' => $mensaje,
            // 'title' => $titulo,
                $fields = array(
                    "to" => $token,
                    "notification" => $push,
                    "data" => array(
                        "service_id" => $service_id,
                        "id_topic" => $id_topic,
                        'body' => $mensaje,
                        'title' => $titulo,
                    ),
                );
            
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández
            *		   mario.martinez.f@hotmail.es
            *	Fecha: 08 abr 2018
            *	Nota: COnfiguracion de firebase
            ***********************************************************************/
        
            //adding the fields in json format
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

            //finally executing the curl request
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
            }
    }
        //Now close the connection
        curl_close($ch);
        // and return the result
        // echo json_encode($result);
        return $result;

    }



    public function SendMultipleDevices()

    {

        $query = "

            SELECT d.token FROM devices d

              JOIN user u ON d.id_user = u.id

            WHERE u.active = 1;

        ";

        $resultado = $this->db->query($query)->result_array();

        $tokens = array();

        if (count($resultado) > 0) {

            foreach ($resultado as $index => $value) {

                array_push($tokens, $value['token']);

            }

        } else {

            return false;

        }

        return $tokens;

    }



    /***********************************************************************

     *	Nota: Funcion para registrar token

     ***********************************************************************/

    public function RegistrarToken($token, $user_id, $tipo = null)

    {

        /***********************************************************************

         *	Nota: Se valida que el id usuario no se encuentre ya registrado en

         *          BD, si se encuentra registrado se actualiza el token si

         *          no se encuentra registrado se hace un insert con el id

         *          de usuario y el token generado.

         ***********************************************************************/

        $query = "SELECT id from tokens where user_id = $user_id";

        $resultado = $this->db->query($query)->result_array();

        $this->db->select("*");

        $this->db->from("historial_sesiones");

        $this->db->where("id_user", $user_id);

        $this->db->where("fecha_logout is NULL", null, false);

        $result = $this->db->get()->result_array();

        if (count($result) > 0) {

            $this->db->set("fecha_logout", date('Y-m-d H:i:s'));

            $this->db->where("id_user", $user_id);

            $this->db->where("fecha_logout is NULL", NULL, false);

            $this->db->order_by("id", "DESC");

            $this->db->limit(1);

            $this->db->update("historial_sesiones");

        }

        $fecha = date("Y-m-d H:i:s");

        if (count($resultado) === 0) {



            $this->db->insert("historial_sesiones", array("id_user" => $user_id, "tipo" => $tipo, "fecha_login" => $fecha));

            if ($this->db->insert('tokens', array('token' => $token, 'user_id' => $user_id))) {

                return true; //$this->db->insert_id();

            } else {

                return false;

            }

        } else {

            $fecha_actual = date('Y-m-d');

            $this->db->update('tokens', array('token' => $token, 'fecha' => $fecha_actual), array('id' => $resultado[0]['id']));

            $this->db->update('tokens', array('token' => $token, 'fecha' => $fecha_actual), array('id' => $resultado[0]['id']));

            $this->db->update('tokens', array('token' => $token, 'fecha' => $fecha_actual), array('id' => $resultado[0]['id']));

            // sleep(10);

            if ($this->db->insert("historial_sesiones", array("id_user" => $user_id, "tipo" => $tipo, "fecha_login" => $fecha))) {

                return true; //$this->db->insert_id();

            } else {

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

                number_employee as num_empleado,

                ifnull(j.job_name, '') as job_name,

                r.nombre as user_type,

                u.score,

                u.rol_id,

                u.id_asesor,

                u.id_region

            FROM user u                    

         ";



        if (isset($_SESSION['id_user']) || isset($_SESSION['user_id'])) {

        } else {

            $query .= " JOIN tokens t ON u.id= t.user_id";

        }



        $query .= "

            JOIN business b ON b.id = u.business_id

            LEFT JOIN jobs j ON j.id = u.job_id

            JOIN roles r ON r.id = u.rol_id

            WHERE true

        ";



        if (isset($_SESSION['id_user']) || isset($_SESSION['user_id'])) {

            $user_id = (isset($_SESSION['id_user'])) ? $_SESSION['id_user'] : $_SESSION['user_id'];

            $query .= "AND u.id = " . $user_id;

        } else {

            $query .= "AND t.token = '" . $token . "'";

        }

        $resultado = $this->db->query($query)->result_array();

        if (count($resultado) === 0) {

            $resultado = false;

        } else {

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

    public function RegistroDevices($id_user, $token_firebase)

    {

        /***********************************************************************

         *	Autor: Mario Adrián Martínez Fernández

         *		   mario.martinez.f@hotmail.es

         *	Fecha: 05 abr 2018

         *	Nota: Se hace validacion para no duplicar registros de tokens por

         *          usuario.

         ***********************************************************************/

        $sql = "

            SELECT * FROM devices where id_user = $id_user

        ";

        $valida_user = $this->db->query($sql)->result_array();



        /***********************************************************************

         *	Autor: Mario Adrián Martínez Fernández

         *		   mario.martinez.f@hotmail.es

         *	Fecha: 05 abr 2018

         *	Nota: Si la consulta no regresa ningun resultado se hace el insert,

         *          en caso de existir el registro se aplicara un update de

         *          token.

         ***********************************************************************/

        if (count($valida_user) === 0) {

            if ($this->db->insert('devices', array('token' => $token_firebase, 'id_user' => $id_user))) {

                return true; //$this->db->insert_id();

            } else {

                return false;

            }

        } else {

            if ($this->db->update("devices", array('token' => $token_firebase), array("id_user" => $id_user))) {

                return true; //$this->db->insert_id();

            } else {

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

    public function getToken()

    {

        $token = "";

        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";

        $codeAlphabet .= "0123456789";

        $max = strlen($codeAlphabet); // edited



        for ($i = 0; $i < 290; $i++) {

            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max - 1)];

        }



        return $token;

    }



    public function crypto_rand_secure($min, $max)

    {

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

    public function random_string($length)

    {

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

    public function sendemail($subject, $body, $address, $fromname, $archivo = null)

    {

        $whitelist = array('127.0.0.1', "::1");

        if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {

            return true;

        }

        $body_detalle = $body;



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



        if ($archivo != null) {

            $mail->AddAttachment($archivo["tmp_name"], $archivo["name"]);

        }

        //$mail->WordWrap = 50;                                 // set word wrap to 50 characters

        $mail->IsHTML(true);

        $mail->CharSet = 'UTF-8'; // set email format to HTML



        $mail->Subject = $subject;

        $mail->Body    = $body_detalle;



        if (!$mail->Send()) {

            return false;

        }

        return true;

    }



    /***********************************************************************

     *	Autor: Mario Adrián Martínez Fernández

     *		   mario.martinez.f@hotmail.es

     *	Fecha: 14 sep 2017

     *	Nota: Se agrega validacion de parametros requeridos para la funcion

     ***********************************************************************/

    public function validapost($requeridos, $post, $lang = 'en')

    {

        $CI = &get_instance();

        $CI->lang->load('message', $lang);



        $respuesta = array('success' => true);

        /***********************************************************************

         *	Nota: Se hace un recorrido por cada parametro requerido

         ***********************************************************************/

        foreach ($requeridos as $index_r => $param_r) {

            $valida = false;

            /***********************************************************************

             *	Nota: Se recorren todos los parametros de post para los elementos

             *          requeridos y verificar que este.

             ***********************************************************************/

            foreach ($post as $index => $param) {

                /***********************************************************************

                 *	Nota: Se valida si existe y que no se encuentre vacio.

                 ***********************************************************************/

                if ($index === $param_r && $param !== '') {

                    $valida = true;

                }

            }

            /***********************************************************************

             *	Nota: Si no se encuentra termina con el ciclo y manda un mensaje de

             *          error personalizado con el nombre del parametro necesario.

             ***********************************************************************/

            if ($valida === false) {

                $respuesta = array('success' => false, 'msg' => $CI->lang->line('param_error') . ' ' . $param_r . ' ' . $CI->lang->line('param_empty'));

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

    function CrearActualizar($params_actualizar, $post)

    {

        $respuesta = array('success' => true);

        $adicionales = '';

        /***********************************************************************

         *	Autor: Mario Adrián Martínez Fernández

         *		   mario.martinez.f@hotmail.es

         *	Fecha: 14 sep 2017

         *	Nota: Se hace un recorrido por cada parametro que se puede actualizar

         ***********************************************************************/

        foreach ($params_actualizar as $index_r => $param_r) {

            foreach ($post as $index => $param) {

                /***********************************************************************

                 *	Autor: Mario Adrián Martínez Fernández

                 *		   mario.martinez.f@hotmail.es

                 *	Fecha: 14 sep 2017

                 *	Nota: Se valida si existe y que no se encuentre vacio.

                 ***********************************************************************/

                if ($index === $param_r && $param !== '') {

                    $adicionales .=  (($adicionales !== '') ? ',' : '') . $param_r . ' = "' . $param . '"';;

                }

            }

        }

        $respuesta['resultado'] = $adicionales;

        if ($adicionales === '') {

            $respuesta = array('success' => false, 'msg' => 'Por lo menos debe de aver un parametro para actualizar.', 'resultado' => '');

        }

        return $respuesta;

    }

    /***********************************************************************

     *	Autor: Mario Adrián Martínez Fernández

     *		   mario.martinez.f@hotmail.es

     *	Fecha: 13 sep 2017

     *	Nota: Funcion para subir archivos

     ***********************************************************************/

    function GuardarArchivos($files, $ruta = '', $business_id = '')

    {

        $respuesta = array(

            "success" => true,

            "msg" => 'No se subieron archivos.',

            "success_files" => array(), //Arreglo de archivos guardados con exito

            "error_files" => array() //Arreglo de archivos no guardados

        );



        //No hay archivos por subir

        if (count($files) == 0) {

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

        $business_folder = 'uploads/business_' . $business_id;

        $f = getcwd() . '/' . $business_folder;

        $path = realpath($f);

        if ($path) {

            //2.-Validar si hay espacio disponible en la carpeta de la empresa

            $this->db->select('b.*, p.id AS plan_id, p.name as plan_name, p.num_users, p.sections, p.space as plan_space');

            $this->db->from("business as b");

            $this->db->join('configuration as c', 'b.id = c.business_id');

            $this->db->join('plans as p', 'c.value = p.id AND c.name = "plan"');

            $this->db->where("active", 1);

            $this->db->where("business_id", $business_id);



            $business = $this->db->get()->result_array();

            $plan_id = $business[0]["plan_id"];

            //Si el plan no es ilimitado

            if ($plan_id != 5) {

                $plan_space = $business[0]["plan_space"];

                $space = dirSize($f);



                //Si el espacio ya se excedió

                if ($space > $plan_space/*KB*/) {

                    $respuesta["success"] = false;

                    $respuesta["msg"] = 'Se ha excedido el espacio en disco.';

                    return $respuesta;

                }

            }

        } else {

            $respuesta["success"] = false;

            $respuesta["msg"] = 'El directorio de la empresa no existe.';

            return $respuesta;

        }



        //3.-Validar si existe la subcarpeta destino

        $ruta = $business_folder . "/" . $ruta;

        $r = getcwd() . '/' . $ruta;

        $path = realpath($r);



        if (!$path) {

            mkdir($r, 0777, true);

            $path = realpath($r);

        }

        // echo json_encode($files);

        if ($path) {

            foreach ($files as $index => $file) {

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

                $upload_folder = $ruta;

                $name_file = random_string(20) . '.' . $ext;

                $tmp_file = $file['tmp_name']; // != "" ? $file["tmp_name"] : $file["name"];

                $archivador = $path . '/' . $name_file;

                // echo json_encode($archivador);

                // echo json_encode($tmp_file);

                if (!move_uploaded_file($tmp_file, $archivador)) {

                    $respuesta["error_files"][$index] = $name_file;

                } else {

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

            if (count($respuesta["error_files"]) == 0) {

                $respuesta["success"] = true;

                $respuesta["msg"] = 'Archivos guardados con éxito.';

            } else {



                $respuesta["success"] = false;

                $respuesta["msg"] = 'Algunos archivos no pudieron guardarse.';

            }

            return $respuesta;

        } else {

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

    public function ValidaTokenRecuperarPassword($token)

    {

        $query = "

            select id_user from recuperar_password where token = '$token' and estatus = 1;

         ";

        $resultado = $this->db->query($query)->result_array();

        if (count($resultado) === 0) {

            $resultado = false;

        } else {

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

    public function CambiarPassword($id_usuario, $password, $token)

    {

        /***********************************************************************

         *	Autor: Mario Adrián Martínez Fernández

         *		   mario.martinez.f@hotmail.es

         *	Fecha: 01 may 2018

         *	Nota: Se actualiza correctamente el password del usuario

         ***********************************************************************/

        $query = "UPDATE user SET password=AES_ENCRYPT('" . $password . "', '" . KEY_AES . "') WHERE id = $id_usuario";

        $resultado = $this->db->query($query);

        // echo json_encode($resultado);

        if ($resultado) {

            /***********************************************************************

             *	Autor: Mario Adrián Martínez Fernández

             *		   mario.martinez.f@hotmail.es

             *	Fecha: 01 may 2018

             *	Nota: Se actualiza el token para que no se vuelva a utilizar.

             ***********************************************************************/

            $query = "UPDATE recuperar_password SET estatus=0 WHERE token = '$token'";

            $this->db->query($query);

            return true;

        } else {

            return false;

        }

    }

    /***********************************************************************

     *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020

     *		   mario.martinez.f@hotmail.es

     *	Nota: Funcion para obtener una configuracion especifica de la app.

     ***********************************************************************/

    function GetConfigApp($business_id, $name)

    {

        $this->db->select('id, name, value');

        $this->db->from($this->tableConfiguration);

        $this->db->where('name =', $name);

        $this->db->where('business_id =', $business_id);

        $result = $this->db->get()->result_array();

        if (count($result) > 0) {

            return $result[0]['value'];

        } else {

            return false;

        }

    }



    function ModificarScoreUsuario($id_usuario, $nuevo_score)

    {

        //se obtiene el score del usuario

        $score = $this->db->get_where('user', array('id' => $id_usuario))->result_array();

        $score = intval($score[0]['score']);

        //nuevo_score puede ser negativo o positivo

        $score += $nuevo_score;

        $score = $score < 0 ? 0 : $score;

        $this->db->set("score", $score);

        $this->db->where("id", $id_usuario);

        $this->db->update('user');



        //se consulta el score despues de actualizar para saber si se debe ganar la insignia

        $score = $this->db->get_where('user', array('id' => $id_usuario))->result_array();

        $score = intval($score[0]['score']);

        // if ($score >= 10) {

        //     $this->asignarInsignia(1, $id_usuario);

        // }

        // if ($score >= 100) {

        //     $this->asignarInsignia(1, $id_usuario);

        // }

        // if ($score >= 1000) {

        //     $this->asignarInsignia(1, $id_usuario);

        // }

    }



    function asignarInsignia($id_insignia, $id_usuario)

    {

        $query = "insert into insignias_users (insignia_id,user_id)

                  values (" . $id_insignia . "," . $id_usuario . ") on  duplicate key update user_id = " . $id_usuario;

        $this->db->query($query);

    }



    function agregar_recurso_visto($user_id)

    {

        $this->db->select("recursos_vistos");

        $this->db->from("recursos_vistos");

        $this->db->where("user_id", $user_id);

        $result = $this->db->get()->result_array();

        if (count($result) > 0)

            $recursos_vistos = $result[0]["recursos_vistos"];

        else

            $recursos_vistos = 0;

        $recursos_vistos = $recursos_vistos + 1;

        if ($recursos_vistos > 1) {

            $this->db->set("recursos_vistos", $recursos_vistos);

            $this->db->where("user_id", $user_id);

            $this->db->update("recursos_vistos");

        } else {

            $data = [];

            $data["user_id"] = $user_id;

            $data["recursos_vistos"] = $recursos_vistos;

            $this->db->insert("recursos_vistos", $data);

        }

        if ($recursos_vistos == 10) {

            $this->asignarInsignia(19, $user_id);

        }

        if ($recursos_vistos == 15) {

            $this->asignarInsignia(20, $user_id);

        }

        if ($recursos_vistos == 20) {

            $this->asignarInsignia(21, $user_id);

        }

        if ($recursos_vistos == 25) {

            $this->asignarInsignia(22, $user_id);

        }

    }



    function writeLog($mensaje, $severidad)

    {

        // base_url() . 

        //home/kreati22/public_html/nuup //para el admin

        // $handle = fopen('/home/kreati22/public_html/nuup/application/logs/nup_log' . date('d_m_Y') . '.log', 'a+');
        $handle = fopen('application/logs/nup_log' . date('d_m_Y') . '.log', 'a+');
        $line   = '[' . date('Y-m-d H:i:s T') . '] ' . php_uname('n') . ' ' . $severidad . ' ' . $mensaje . "\n";

        fwrite($handle, $line);

        fclose($handle);
    }



    function validar_password($pass, $id_usuario)

    {

        $query = "select id from user where id = " . $id_usuario . " and password = aes_encrypt('" . $pass . "','" . KEY_AES . "')";

        $result = $this->db->query($query)->result_array();

        if (count($result) > 0) {

            return true;

        } else {

            return false;

        }

    }



    function actualizar_pass($pass, $id_usuario)

    {

        $query = "update user set password = aes_encrypt('" . $pass . "','" . KEY_AES . "') where id = " . $id_usuario;

        $this->db->query($query);

        return true;

    }

    public function obtener_puntos($id_capacitacion, $id_usuario)

    {

        $query = "

        select sum(if(qau.correcto = 1, q.points, 0)) as puntos

        from question_answer_users as qau

        join questions as q on q.id = qau.question_id

        join question_quiz as qq on qq.id = q.quiz_id

        join capacit_detail as cd on cd.id_elemento = qq.id

        where cd.id_capacitacion = $id_capacitacion and qau.user_id = $id_usuario

        ";

        return $this->db->query($query)->result_array()[0]["puntos"];

    }



    function obtenerInsignias($user_id, $business_id)

    {

        $this->determinarInsigniasUso($user_id);



        $url = base_url() . "assets/img/insignias/";

        $this->db->select("i.id,i.titulo,i.descripcion,i.business_id,concat('" . $url . "',i.imagen) as imagen, concat('" . $url . "',i.imagen_inactivo) as imagen_inactivo,coalesce(iu.status,0) as estatus, if(cl.id is null,0,1) as permitir_descargar, cl.tipo, cl.id as id_cap");

        $this->db->from("insignias as i");

        $this->db->join("insignias_users as iu", "iu.insignia_id = i.id and iu.user_id = " . $user_id, "left");

        $this->db->join("capacit_list as cl", "cl.id_insignia = i.id", "left");

        $this->db->where("iu.user_id", $user_id);

        $this->db->or_where("iu.user_id is null", null, false);

        $this->db->order_by("i.id");

        $this->db->having("i.business_id like '%" . $business_id . "%'");



        $insignias = $this->db->get()->result_array();



        $certificaciones = [];

        $logros = [];

        /// Se obtienen los puntos para validar si se le otorgan el diploma 80 >

        for ($i = 0; $i < count($insignias); $i++) {
            if ($insignias[$i]["tipo"] == 1) {
                if ($insignias[$i]["estatus"] == 0) {
                    $insignias[$i]["permitir_descargar"] = 0;
                }
                else if($insignias[$i]["estatus"] == 1 || $insignias[$i]["permitir_descargar"] = 1){
                    $puntos = $this->obtener_puntos($insignias[$i]["id_cap"], $user_id);
                    $insignias[$i]["puntos"] = $puntos;
                    if($puntos >= 80){
                        $insignias[$i]["permitir_descargar"] = 1;
                        $insignias[$i]["estatus"] = 1;
                    }
                    else{
                        $insignias[$i]["permitir_descargar"] = 0;
                        $insignias[$i]["estatus"] = 0 ;
                    }
                }
                array_push($certificaciones, $insignias[$i]);
            } else {
                array_push($logros, $insignias[$i]);
            }
        }

        return ["certificaciones" => $certificaciones, "logros" => $logros];

    }



    function determinarInsigniasUso($user_id)

    {

        $this->db->select("timestampdiff(day,created_at,now()) as dias");

        $this->db->from("user");

        $this->db->where("id", $user_id);

        $dias = $this->db->get()->result_array();

        if ($dias >= 5) {

            $this->asignarInsignia(7, $user_id);

        }

        if ($dias >= 15) {

            $this->asignarInsignia(8, $user_id);

        }

        if ($dias >= 30) {

            $this->asignarInsignia(9, $user_id);

        }

    }



    function obtenerInsigniaById($insignia_id)

    {

        $this->db->select("*");

        $this->db->from("insignias");

        $this->db->where("id", $insignia_id);

        return $this->db->get()->result_array()[0];

    }



    function ComprobarSesionAndroid($user_id)

    {

        $query = "select if((tipo = 'Android' || tipo is null || tipo = 'web'),1,0) as es_android from historial_sesiones 

        where id_user = " . $user_id . "

        order by fecha_login desc

        limit 1";

        $resultado = $this->db->query($query)->result_array();

        if (count($resultado) > 0 && $resultado[0]["es_android"]) {

            return true;

        }

        return false;

    }



    function agregar_uso_usuario($user_id, $seccion)

    {

        $data = [];

        $data["user_id"] = $user_id;

        $data["seccion"] = $seccion;

        return $this->db->insert("uso_usuarios", $data);

    }



    function esta_registrado($number_employee)

    {

        $query = "select password from user where password = 123 and (number_employee = '" . $number_employee . "' or email = '" . $number_employee . "')";

        $result = $this->db->query($query)->result_array();

        if (count($result) > 0) {

            return false;

        }

        $query = "select password from user where password != 123 and (number_employee = '" . $number_employee . "' or email = '" . $number_employee . "')";

        $result = $this->db->query($query)->result_array();

        if (count($result) > 0) {

            return true;

        }

        //Validacion si ya ingreso mas de una vez

        return "invitacion";

    }



    function obtener_regiones($id_usuario, $rol_id, $business_id = null)

    {

        if ($rol_id == 5) {

            $query = "select r.* from regiones_gerentes as rg

                  join regiones as r on r.id = rg.id_region

                  where rg.id_gerente = $id_usuario order by r.id";

        } else if ($rol_id == 2 || $rol_id = 7) {

            $query = "select * from regiones where business_id = $business_id";

        } else if ($rol_id == 6) {

            $query = "select r.* from user as u

            join regiones as r on r.id = u.id_region

            where u.id = $id_usuario";

        }

        return $this->db->query($query)->result_array();

    }



    function obtener_contratos($id_usuario, $rol_id)

    {

        $query = "select co.* from contratos as co

        join user as u on co.id_empresa = u.business_id

        where u.id = $id_usuario order by co.id";

        if ($rol_id == 2) {

            $query = "select * from contratos";

        }

        return $this->db->query($query)->result_array();

    }

    function obtener_plantas($id_usuario, $rol_id)

    {

        $query = "select * from plantas as pl

        join user as u on pl.id_empresa = u.business_id

        where u.id = $id_usuario order by pl.id";

        if ($rol_id == 2) {

            $query = "select * from plantas";

        }

        return $this->db->query($query)->result_array();

    }



    function obtener_asesores($id_region, $user_id = null, $rol_id = null)

    {

        if ($user_id != null)

            $user_id = $user_id;

        else

            $user_id = (isset($_SESSION['id_user'])) ? $_SESSION['id_user'] : $_SESSION['user_id'];



        $query = "select id, upper(if(name = '',concat('#',number_employee),concat('#',name,' ',last_name))) as nombre

        from user where id_region = $id_region and rol_id = 6 and es_prueba = 2";

        if ($this->session->userdata("rol_id") == 6 || $rol_id == 6) {

            $query = "select id,if(name = '',concat('w',number_employee),concat('#',name,' ',last_name)) as nombre

            from user where id =  " . $user_id;

        }

        return $this->db->query($query)->result_array();

    }



    function obtener_asesores_multiples($regiones)

    {

        $query = "select id, if(name = '',concat('w',number_employee),concat(name,' ',last_name)) as nombre

            from user where id_region IN ($regiones) and rol_id = 6

            order by id_region";

        $res = $this->db->query($query)->result_array();

        return $res;

    }



    function sendPushToAdmin()

    {

        $headers =  array(

            'Content-type: application/json',

            'Authorization: key=AAAAivNJ3m8:APA91bFXDu_y08RjGASREAYN8WdH222DQwyz3mjRjqW7i_9rEe4CXqy6yPzLf-ma_Zqt2oxPL8IwG9T3zmy6MSUWf5nN04zufnnfVWIzh6U3GEURor64qmsukSvAZ9A-13dQWYm05hFv'

        );



        $url = 'https://fcm.googleapis.com/fcm/send';

        $action_url = "http://kreativeco.com/qa-nuup/Admin/SeccionesCapturas";

        // $action_url = "https://cafe-b.com/app/admin/ordenes.php?editar=1&id=" . $folio;

        $fields = array(

            'notification' => array(

                "title" => "Alerta",

                "body" => "Se genero una nueva alerta ",

                "icon" => "",

                "click_action" => $action_url

            ),

            'to' => '/topics/admin_alert_'

        );

        $fields = json_encode($fields);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);



        curl_close($ch);

    }



    function comprobar_insignia_ganada($id_insignia, $user_id)

    {

        $query = "select * from insignias_users where user_id = $user_id and insignia_id = $id_insignia ";

        $result = $this->db->query($query)->result_array();

        if (count($result) > 0) {

            return true;

        } else {

            return false;

        }

    }

}

