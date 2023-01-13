<?php
class Demo_mdl extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function fetchAll()
    {
       return $this->db->get_where("usuarios", array("estatus"=>1))->result_array();
    }


}