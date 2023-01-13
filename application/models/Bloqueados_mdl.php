<?php
class Bloqueados_mdl extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function obtenerBloqueados($business_id)
    {
        $where = "";
        if ($this->session->userdata("rol_id") == 6) {
            $id_asesor = $this->session->userdata("id_user");
            $where .= " where id_asesor = $id_asesor ";
        }
        if ($this->session->userdata("rol_id") == 5) {
            $region = $this->session->userdata("id_region");
            $this->db->where("u.id_region", $region);
            $where .= " where id_region = $region ";
        }
        $query = "
        select id, concat(name, ' ',last_name) as name,number_employee, aes_decrypt(password,'kreativecomexico') as pass,business_id
        from user
        $where
        having pass like '%~#bloqueado#~%'
        and business_id =" . $business_id . "
        ";
        $bloqueados = $this->db->query($query)->result_array();
        for ($i = 0; $i < count($bloqueados); $i++) {
            unset($bloqueados[$i]["pass"]);
        }
        return $bloqueados;
    }

    function desbloquear($id)
    {
        $this->db->select("aes_decrypt(u.password,'" . KEY_AES . "') as password");
        $this->db->from("user as u");
        $this->db->join("tokens as t", "t.user_id = u.id");
        $this->db->where("u.id", $id);
        $password = $this->db->get()->result_array()[0]["password"];
        // echo json_encode($password);
        $password = str_replace("~#bloqueado#~", "", $password);
        // echo json_encode($password);
        $query = "select * from user as u
        join invitation as i on (i.number_employee = u.number_employee or i.email = u.email)
        where i.status = 0 and u.id =" . $id;
        $result = $this->db->query($query)->result_array();
        if (count($result) > 0) {
            $query = "update user set login_intent = 0, password = '123' where id = " . $id;
        } else {
            $query = "update user set login_intent = 0, password = AES_ENCRYPT('" . $password . "','" . KEY_AES . "') where id = '" . $id . "'";
        }
        return $this->db->query($query);
    }

    function desbloquear_todos()
    {
        //consulta para desbloquear todos los usuarios que aun no se han registrado
        $query = "update user set login_intent = 0, password = '123'
             where id in (select u.id from (select * from user) as u
             join invitation as i on i.number_employee = u.number_employee
             where aes_decrypt(password,'" . KEY_AES . "') like '%~#bloqueado#~%'
             and i.status = 0)";
        $this->db->query($query);
        //consulta para desbloquera todos los usuarios que ya estan registrados
        $query = "update user set login_intent = 0, password = aes_encrypt(replace(aes_decrypt(password,'" . KEY_AES . "'),'~#bloqueado#~',''),'" . KEY_AES . "')
        where id in (select u.id from (select * from user) as u
        left join invitation as i on i.number_employee = u.number_employee
        where aes_decrypt(password,'" . KEY_AES . "') like '%~#bloqueado#~%'
        and (i.status = 1 or i.status is null))";
        $this->db->query($query);
        return true;
    }

    function prueba()
    {

        $query = "select concat(' ',number_employee) as number_employee from invitation";
        $result = $this->db->query($query)->result_array();
        // for($i = 0; $i < count($result); $i++){
        //     $result[$i]["number_employee"] = strval(substr(strval("".$result[$i]["number_employee"])." ", 1));
        // }
        return $result;
    }
}
