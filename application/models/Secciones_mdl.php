<?php
class Secciones_mdl extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 02/07/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener las fotos tomadas desde los despositivos
     ***********************************************************************/
    function SeccionesCapturas($business_id){
        $where = "";
        if ($this->session->userdata("rol_id") == 6) {
            $id_asesor = $this->session->userdata("id_user");
            $where .= " and u.id_asesor = $id_asesor ";
        }
        if ($this->session->userdata("rol_id") == 5) {
            $region = $this->session->userdata("id_region");
            $this->db->where("u.id_region", $region);
            $where .= " and u.id_region = $region ";
        }
        $query = "
            select 
                   su.*,
                   s.seccion,
                   concat(u.name, ' ', u.last_name) as nombre_usuario
            from secciones_usuarios su 
                join user u on u.id = su.id_usuario
                join secciones s on s.id = su.id_seccion
            where imagen != '' and u.created_at is not null and u.business_id = $business_id
            $where
            order by id desc 
        ";
        $query = $this->db->query($query)->result_array();
        if(count($query)>0){
            foreach ($query as $index => $value){
                $query[$index]['imagen'] = base_url()."uploads/business_$business_id/capturas/".$value['imagen'];
            }
            return $query;
        }else{
            return false;
        }
    }
}
