<?php
class Login_admin_mdl extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 19 mar 2018
     *	Nota: Funcion para hacer peticion a la tabla de usuarios con los
     *          parametros para generar logion
     ***********************************************************************/
    public function ValidaUsuario($usuario, $password)
    {
        $query = "select * from user where email ='".$usuario."' and password = AES_ENCRYPT('".$password."', '".KEY_AES."') and rol_id in(1,2)";
        $resultado = $this->db->query( $query )->result_array();
        return $resultado;
    }



}
