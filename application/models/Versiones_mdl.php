<?php
class Versiones_mdl extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function comprobarVersion($version, $tipo)
    {
        $this->db->select("version");
        $this->db->from("versiones");
        // echo json_encode($tipo);
        // echo json_encode($version);
        if ($tipo == "true") {
            $this->db->where("id", 1);
        } else {
            $this->db->where("id", 2);
        }
        $version_actual = $this->db->get()->result_array()[0]["version"];
        if ($version < $version_actual)
            return 1;
        else
            return 0;
    }
}
