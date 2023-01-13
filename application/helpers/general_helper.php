<?php
/***********************************************************************
 *    Autor: Mario Adrián Martínez Fernández
 *           mario.martinez.f@hotmail.es
 *    Fecha: 29 may 2018
 *    Nota: Encriptar los textos antes de insertarlos
 ***********************************************************************/
function EncriptarDatos($texto)
{
    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández
     *           mario.martinez.f@hotmail.es
     *    Fecha: 31 may 2018
     *    Nota: Antes de encriptar los datos, se reemplazaran los textos con
     *          acentos y tildes por html codes
     ***********************************************************************/
    //$cadena = "Está Á es la cadéna de ejëmploñ Ñ para sustituir un caracter æ";
    $search = array("Á", "á", "À", "Â", "à", "Â", "â", "Ä", "ä", "Ã", "ã", "Å", "å", "Æ", "æ", "Ç", "ç", "Ð", "ð", "É", "é", "È", "è", "Ê", "ê", "Ë", "ë", "Í", "í", "Ì", "ì", "Î", "î", "Ï", "ï", "Ñ", "ñ", "Ó", "ó", "Ò", "ò", "Ô", "ô", "Ö", "ö", "Õ", "õ", "Ø", "ø", "ß", "Þ", "þ", "Ú", "ú", "Ù", "ù", "Û", "û", "Ü", "ü", "Ý", "ý", "ÿ");
    $replace = array("&Aacute;", "&aacute;", "&Agrave;", "&Acirc;", "&agrave;", "&Acirc;", "&acirc;", "&Auml;", "&auml;", "&Atilde;", "&atilde;", "&Aring;", "&aring;", "&Aelig;", "&aelig;", "&Ccedil;", "&ccedil;", "&Eth;", "&eth;", "&Eacute;", "&eacute;", "&Egrave;", "&egrave;", "&Ecirc;", "&ecirc;", "&Euml;", "&euml;", "&Iacute;", "&iacute;", "&Igrave;", "&igrave;", "&Icirc;", "&icirc;", "&Iuml;", "&iuml;", "&Ntilde;", "&ntilde;", "&Oacute;", "&oacute;", "&Ograve;", "&ograve;", "&Ocirc;", "&ocirc;", "&Ouml;", "&ouml;", "&Otilde;", "&otilde;", "&Oslash;", "&oslash;", "&szlig;", "&Thorn;", "&thorn;", "&Uacute;", "&uacute;", "&Ugrave;", "&ugrave;", "&Ucirc;", "&ucirc;", "&Uuml;", "&uuml;", "&Yacute;", "&yacute;", "&yuml;");
    $text_code = str_replace($search, $replace, $texto);
    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández
     *           mario.martinez.f@hotmail.es
     *    Fecha: 31 may 2018
     *    Nota:Se encripta el texto
     ***********************************************************************/
    $key1 = hex2bin(openssl_digest(KEY_AES, 'sha512'));
    $key3 = substr($key1, 0, 15);
    $method = 'aes-128-ecb';
    $data3 = base64_decode(openssl_encrypt($text_code, $method, $key3));
    return bin2hex($data3);
}
function crypto_rand_secure($min, $max)
{
    $range = $max - $min;
    if ($range < 1) {
        return $min;
    }
    // not so random...
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
 *    Autor: Mario Adrián Martínez Fernández
 *           mario.martinez.f@hotmail.es
 *    Fecha: 23 jun 2017
 *    Nota: Funcion para generar un id aleatorio
 ***********************************************************************/
function random_string($length)
{
    $key = '';
    $keys = array_merge(range(0, 9), range('a', 'z'));

    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }

    return $key;
}
/***********************************************************************
 *    Autor: Mario Adrián Martínez Fernández
 *           mario.martinez.f@hotmail.es
 *    Fecha: 05 jul 2017
 *    Nota: Se agrega funcionalidad para cargar la configuracion de
 * phpmailer
 ***********************************************************************/
function sendemail($subject, $body, $address, $fromname, $email_from, $password)
{
    require "/home/todossomospymes/public_html/phpmailer/class.phpmailer.php";

    $mail = new PHPMailer();
    $mail->Timeout = 60;

    $mail->IsSMTP(); // set mailer to use SMTP
    $mail->Host = "localhost"; // specify main and backup server
    $mail->SMTPAuth = true; // turn on SMTP authentication
    $mail->Username = $email_from; // SMTP username
    $mail->Password = $password; // SMTP password

    $mail->From = $email_from;
    $mail->FromName = $fromname;
    $mail->AddAddress($address); // name is optional

    //$mail->WordWrap = 50;                                 // set word wrap to 50 characters
    $mail->IsHTML(true); // set email format to HTML

    $mail->Subject = $subject;
    $mail->Body = $body;

    if (!$mail->Send()) {
        return array('success' => 'error', 'message' => $mail->ErrorInfo);
    }

    return true;
}
/***********************************************************************
 *    Autor: Mario Adrián Martínez Fernández
 *           mario.martinez.f@hotmail.es
 *    Fecha: 04 ene 2018
 *    Nota: Funcion para validar email
 ***********************************************************************/
function validateEmail($email)
{
    $respuesta = true;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) === true) {
        $respuesta = false;
    }
    return $respuesta;
}

/***********************************************************************
 *    Autor: Mario Adrián Martínez Fernández   Fecha: 12/22/2020
 *           mario.martinez.f@hotmail.es
 *    Nota: Funcion para obtener el espacio
 ***********************************************************************/
function dirSize($directory)
{
    $size = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
        $size += $file->getSize();
    }
    return $size;
}
/***********************************************************************
 *    Autor: Mario Adrián Martínez Fernández
 *           mario.martinez.f@hotmail.es
 *    Fecha: 02 oct 2017
 *    Nota: Funcion para crear dinamicamente el actualizar con lo que se
 *          recibe de post.
 ***********************************************************************/
function CrearActualizar($params_actualizar, $context)
{
    $respuesta = array('success' => true);
    $adicionales = '';
    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández
     *           mario.martinez.f@hotmail.es
     *    Fecha: 14 sep 2017
     *    Nota: Se hace un recorrido por cada parametro que se puede actualizar
     ***********************************************************************/
    foreach ($params_actualizar as $index_r => $param_r) {
        foreach ($context->input->post() as $index => $param) {
            /***********************************************************************
             *    Autor: Mario Adrián Martínez Fernández
             *           mario.martinez.f@hotmail.es
             *    Fecha: 14 sep 2017
             *    Nota: Se valida si existe y que no se encuentre vacio.
             ***********************************************************************/
            if ($index === $param_r && $param !== '') {
                $adicionales .= (($adicionales !== '') ? ',' : '') . $param_r . ' = "' . $param . '"';
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
 *    Autor: Mario Adrián Martínez Fernández
 *           mario.martinez.f@hotmail.es
 *    Fecha: 14 sep 2017
 *    Nota: Se agrega validacion de parametros requeridos para la funcion
 ***********************************************************************/
function validapost($requeridos, $context)
{
    $respuesta = array('success' => true);
    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández
     *           mario.martinez.f@hotmail.es
     *    Fecha: 14 sep 2017
     *    Nota: Se hace un recorrido por cada parametro requerido
     ***********************************************************************/
    foreach ($requeridos as $index_r => $param_r) {
        $valida = false;
        /***********************************************************************
         *    Autor: Mario Adrián Martínez Fernández
         *           mario.martinez.f@hotmail.es
         *    Fecha: 14 sep 2017
         *    Nota: Se recorren todos los parametros de post para los elementos
         *          requeridos y verificar que este.
         ***********************************************************************/
        foreach ($context->input->post() as $index => $param) {
            /***********************************************************************
             *    Autor: Mario Adrián Martínez Fernández
             *           mario.martinez.f@hotmail.es
             *    Fecha: 14 sep 2017
             *    Nota: Se valida si existe y que no se encuentre vacio.
             ***********************************************************************/
            if ($index === $param_r && $param !== '') {
                $valida = true;
            }
        }
        /***********************************************************************
         *    Autor: Mario Adrián Martínez Fernández
         *           mario.martinez.f@hotmail.es
         *    Fecha: 14 sep 2017
         *    Nota: Si no se encuentra termina con el ciclo y manda un mensaje de
         *          error personalizado con el nombre del parametro necesario.
         ***********************************************************************/
        if ($valida === false) {
            $respuesta = array('success' => false, 'msg' => 'El parametro ' . $param_r . ' se encuentra vacio.');
            break;
        }
    }
    return $respuesta;
}

/***********************************************************************
 *    Autor: Mario Adrián Martínez Fernández
 *           mario.martinez.f@hotmail.es
 *    Fecha: 22 feb 2018
 *    Nota: Retornar respuesta success
 ***********************************************************************/
function successResponse($data, $msg, $context, $extras = '', $merge = '')
{
    http_response_code(200);
    $response = array();
    $response['status_code'] = 200;
    $response['success'] = true;
    $response['msg'] = $msg;
    $response['extras'] = $extras;
    $response['data'] = $data;
    if ($merge !== '') {
        $response = array_merge($response, $merge);
    }
    output_json($response, $context);
}

function validationResponse($msg, $context)
{
    http_response_code(422);
    $response = array();
    $response['status_code'] = 422;
    $response['success'] = false;
    $response['msg'] = $msg;

    output_json($response, $context);
}
/***********************************************************************
 *    Autor: Mario Adrián Martínez Fernández
 *           mario.martinez.f@hotmail.es
 *    Fecha: 22 feb 2018
 *    Nota: Retornar respuesta error
 ***********************************************************************/
function faildResponse($msg, $context)
{
    http_response_code(401);
    $response = array();
    $response['success'] = false;
    $response['error_code'] = 401;
    $response['error_msg'] = $msg;
    output_json($response, $context);

}
/***********************************************************************
 *    Autor: Mario Adrián Martínez Fernández
 *           mario.martinez.f@hotmail.es
 *    Fecha: 22 feb 2018
 *    Nota: Retornar mensaje encriptado en JSON
 ***********************************************************************/
function output_json($response, $context)
{
    $context->output->set_header('Content-type: application/json; charset=UTF-8');
    $context->output->set_output(json_encode($response, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES));
}

function array_sort($array, $on, $order = SORT_DESC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
                break;
            case SORT_DESC:
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }
    return $new_array;
}

function ObtenerInicioFinDeSemana()
    {
        $diaSemana = date("w") > 0 ? (date("w") - 1) : (6) ;
        $tiempo = strtotime("-" . $diaSemana . " days");
        $fecha_inicio = date("Y-m-d", $tiempo);
        $tiempo = strtotime("+" . 6 . " days", $tiempo);
        $fecha_fin = date("Y-m-d", $tiempo);
        return ["fecha_inicio" => $fecha_inicio, "fecha_fin" => $fecha_fin];
    }

    function ObtenerInicioFinDeMes()
    {
        $dia = date("d") - 1;
        $tiempo = strtotime("-" . $dia . " days");
        $fecha_inicio = date("Ymd", $tiempo);
        $fecha_fin = date("Ymt", $tiempo);
        return ["fecha_inicio" => $fecha_inicio, "fecha_fin" => $fecha_fin];
    }

    function ObtenerInicioFinSiempre()
    {
        $dia = 1000;
        $tiempo = strtotime("-" . $dia . " days");
        $fecha_inicio = date("Y-m-d", $tiempo);
        $tiempo = strtotime("+" . $dia . " days");
        $fecha_fin = date("Y-m-t", $tiempo);
        return ["fecha_inicio" => $fecha_inicio, "fecha_fin" => $fecha_fin];
    }