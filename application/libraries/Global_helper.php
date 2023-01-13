<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Global_helper {

    protected $ci;
    
    function __construct(){
      $this->ci =& get_instance();
    }

    function getToken(){
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

    function crypto_rand_secure($min, $max){
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

    public function encrypt( $q ) {
      $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
      $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
      return( $qEncoded );
    }

    function randomString( $length ) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    /*****************************************************************
     * Nombre: Mario Adrian Martinez Fernandez
     *   mario.martinez.f@hotmail.es
     * Fecha:08 mar 2017
     * Descripción: Funcion para extraer la ruta actual del sevidor.
     * Parametros:
     *      @complemento_ruta: Variable con complemento de la ruta
     *          actual de la web.
     *****************************************************************/
     function urlActual($complemento_ruta = '',$niveles = 2){
        $complemento_ruta = ($complemento_ruta != '')?$complemento_ruta . '/':'';
        $host= $_SERVER["HTTP_HOST"];
        $url= $_SERVER["PHP_SELF"];
        $explode = explode('/',$host.$url);
        switch ($niveles){
            case 1:
                $ruta =  "http://" . $explode[0] . '/' . $complemento_ruta;
                break;
            case 2:
                $ruta =  "http://" . $explode[0] . '/' . $explode[1] . '/' . $complemento_ruta;
                break;
        }

        return $ruta;
    }

}