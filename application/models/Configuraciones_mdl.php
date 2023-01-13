<?php
class Configuraciones_mdl extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function ObtenerConfiguraciones($business_id)
    {
        $query = "bg_body,bg_header,
        concat('" . base_url('') . "',icono_menu) as icono_menu,
        concat('" . base_url('') . "',icono_empresa) as icono_empresa,
        concat('" . base_url('') . "',icono_empresa_header) as icono_empresa_header,
        concat('" . base_url('') . "',campana_notificaciones) as campana_notificaciones,
        concat('" . base_url('') . "',icono_perfil) as icono_perfil,
        btn_background, btn_text_color,titulo, subtitulo,perfil_text,lbl_home,menu_text, retos_max_file,news_bg_color";
        $this->db->select($query);
        $this->db->from("configuraciones");
        $this->db->where("id_business", $business_id);
        $result = $this->db->get()->result_array();
        if (count($result) === 0) {
            $this->db->select($query);
            $this->db->from("configuraciones");
            $this->db->where("id_business", 0);
            $result = $this->db->get()->result_array();
        }
        $result = $result[0];
        // if($result["bg_body"] == 0){
        //     $result["bg_body"] = "000000";
        // }
        // if($result["bg_header"] == 0){
        //     $result["bg_header"] = "000000";
        // }
        // if($result["btn_background"] == 0){
        //     $result["btn_background"] = "000000";
        // }
        // if($result["btn_text_color"] == 0){
        //     $result["btn_text_color"] = "000000";
        // }
        // if($result["titulo"] == 0){
        //     $result["titulo"] = "000000";
        // }
        // if($result["subtitulo"] == 0){
        //     $result["subtitulo"] = "000000";
        // }
        // if($result["perfil_text"] == 0){
        //     $result["perfil_text"] = "000000";
        // }
        return $result;
    }

    function ObtenerBloquearCaptura($business_id){
        $this->db->select("bloquear_captura");
        $this->db->from("configuraciones");
        $this->db->where("id_business",$business_id);
        $resultado = $this->db->get()->result_array();
        if(count($resultado) == 0){
            return false;
        }
        // return false;
        if($resultado[0]["bloquear_captura"] == 0){
            return false;
        }
        else{
            return true;
        }
        
    }

}
