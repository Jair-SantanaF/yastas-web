<?php
class Chat_mdl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Com_mdl', 'com');
    }

    public function obtenerMensajes($usuarioDe, $usuarioPara, $utc_ref)
    {
        $query = "select c.id, DATE_FORMAT(c.fecha, '%d %b %Y %h:%i') as fecha,
        c.usuario_de, concat(u.name,' ',u.last_name) as nombre_de,c.usuario_para,concat(us.name,' ',us.last_name) as nombre_para,
        u.profile_photo as foto_de, us.profile_photo as foto_para,
        case when AES_DECRYPT(UNHEX(c.mensaje),'" . KEY_AES . "') IS NULL or AES_DECRYPT(UNHEX(c.mensaje),'" . KEY_AES . "') = ''
            then c.mensaje
            else AES_DECRYPT(UNHEX(c.mensaje),'" . KEY_AES . "')
        end as message
        from chat as c
        join user as u on u.id = c.usuario_de
        join user as us on us.id = c.usuario_para
        where
        (c.usuario_de=".$usuarioDe." AND c.usuario_para =".$usuarioPara.")
        or (c.usuario_de=".$usuarioPara." AND c.usuario_para = ".$usuarioDe. ")
        order by c.fecha asc";

        $resultado = $this->db->query($query)->result_array();
        if (count($resultado) === 0) {
            return [];
        }
        return $resultado;
    }

    public function mandarMensaje($data)
    {
        $query = "INSERT INTO chat (usuario_de, usuario_para, mensaje, leido)
        VALUES ('".$data['usuario_de']."', '".$data['usuario_para']."', HEX(AES_ENCRYPT('".$data['mensaje']."','" . KEY_AES . "')), '".$data['leido']."')";
        return $this->db->query($query);
    }

    public function obtenerUltimoMensaje($usuarioPara, $usuarioDe)
    {
        $this->db->select("*");
        $this->db->from("chat");
        $this->db->where("usuario_de", $usuarioDe);
        $this->db->where("usuario_para", $usuarioPara);
        $this->db->order_by("fecha", "desc");
        $resultado = $this->db->get()->result_array();
        if (count($resultado) === 0) {
            return [];
        } else {
            return $resultado[0];
        }
    }

    public function eliminarMensaje($data)
    {
        if ($this->db->delete('chat', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function listarChats($user_id)
    {
        $this->db->select("u.*");
        $this->db->from("chat as c");
        $this->db->join("user as u", "(u.id = c.usuario_de or u.id = usuario_para) and u.id !=" . $user_id);
        $this->db->where("usuario_de", $user_id);
        $this->db->or_where("usuario_para", $user_id);
        $this->db->group_by("u.id");
        $resultado = $this->db->get()->result_array();

        for ($i = 0; $i < count($resultado); $i++) {
            $this->db->select("*");
            $this->db->from("chat");
            $this->db->where("leido", 0);
            $this->db->where("usuario_para", $user_id);
            $this->db->where("usuario_de", $resultado[$i]["id"]);
            $result = $this->db->get()->result_array();
            // echo json_encode($this->db->last_query());
            if (count($result) > 0) {
                $resultado[$i]["tiene_mensajes"] = 1;
            } else {
                $resultado[$i]["tiene_mensajes"] = 0;
            }
        }

        return $resultado;
    }

    public function tieneMensajesNuevos($user_id)
    {
        $this->db->select("u.*");
        $this->db->from("chat as c");
        $this->db->join("user as u", "u.id = usuario_para");
        $this->db->where("usuario_para", $user_id);
        $this->db->where("c.leido", 0);
        $this->db->group_by("u.id");
        $resultado = $this->db->get()->result_array();
        if (count($resultado) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function marcarLeidos($user_id)
    {
        $this->db->set("leido", 1);
        $this->db->where("usuario_para", $user_id);
        return $this->db->update("chat");
    }
}
