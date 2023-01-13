<?php
class Alertas_mdl extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function ObtenerAlertas($business_id)
    {
        $this->db->select("w.id,w.created_at as fecha, concat(u.name, ' ',u.last_name) as name, 'NEWSFEED' as seccion,w.wall_description as publicacion");
        $this->db->from("wall as w");
        $this->db->join("palabras_alerta_laboral as p", "w.wall_description like concat('%',p.palabra,'%')");
        $this->db->join("user as u", "u.id = w.user_id");
        $this->db->where("u.business_id", $business_id);
        $this->db->where("w.active", 1);
        $post = $this->db->get()->result_array();

        //feedback
        $this->db->select("c.id,c.fecha, concat(u.name, ' ',u.last_name) as name, 'Feedback' as seccion, c.description as publicacion");
        $this->db->from("feedback as c");
        $this->db->join("palabras_alerta_laboral as p", "c.description like concat('%',p.palabra,'%')");
        $this->db->join("user as u", "u.id = c.owner_id");
        $this->db->where("u.business_id", $business_id);
        $chat = $this->db->get()->result_array();

        $alertas = [];
        $alertas = array_merge($post, $chat);
        return $alertas;
    }
}
