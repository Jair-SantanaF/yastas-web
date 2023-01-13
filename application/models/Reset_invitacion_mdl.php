<?php
class Reset_invitacion_mdl extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function obtenerUsuarios($business_id)
    {
        $this->db->select("concat(u.name,' ',u.last_name) as name, u.number_employee, u.id");
        $this->db->from("user as u");
        $this->db->join("invitation as i","i.number_employee = u.number_employee");
        $this->db->where("u.active", 1);
        $this->db->where('i.active = ', 1);
        $this->db->where('i.status = ', 0);
        $this->db->where('u.password !=', 123);
        $this->db->where("u.business_id", $business_id);
        $this->db->order_by("u.id");
        if ($this->session->userdata("rol_id") == 6) {
            $id_asesor = $this->session->userdata("id_user");
            $this->db->where("u.id_asesor", $id_asesor);
        }
        if ($this->session->userdata("rol_id") == 5) {
            $region = $this->session->userdata("id_region");
            $this->db->where("u.id_region", $region);
        }
        return $this->db->get()->result_array();
    }

    function resetearPassword($id_user)
    {
        $password = "123";
        $query = "UPDATE user SET password = '" . $password . "' WHERE id = " . $id_user;
        $this->db->query($query);
        return true;
    }
}
