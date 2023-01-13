<?php
class Reporte_mdl extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function guardar_reporte($data)
    {
        $this->db->insert("reportes", $data);
        return $this->db->insert_id();
    }

    function actualizar_reporte($data)
    {
        $id = $data["id_reporte"];
        unset($data["id_reporte"]);
        return $this->db->update("reportes", $data, array("id" => $id));
    }

    function guardar_imagen_reporte($id_reporte, $nombre_archivo, $tipo)
    {
        // $query = "delete from reporte_evidencias where id_reporte = $id_reporte";
        // $this->db->query($query);
        $data = [];
        $data["id_reporte"] = $id_reporte;
        $data["archivo"] = $nombre_archivo;
        $data["tipo"] = $tipo;
        $this->db->insert("reporte_evidencias", $data);
        return $this->db->insert_id();
    }

    function eliminar_imagen_reporte($id_imagen)
    {
        $this->db->where("id", $id_imagen);
        $this->db->set("activo", 0);
        return $this->db->update("reporte_evidencias");
    }

    function eliminar_reporte($id_reporte)
    {
        $this->db->where("id", $id_reporte);
        $this->db->set("activo", 0);
        return $this->db->update("reportes");
    }

    function obtener_reportes($id_user, $business_id)
    {

        $query = "
        select r.id, r.direccion, r.latitud, r.longitud, r.descripcion,
        date_format(r.fecha, '%d-%m-%Y %H:%i') as fecha, rc.id as id_categoria, rc.nombre as nombre_categoria,
        coalesce(rs.id,0) as id_subcategoria, coalesce(rs.nombre,'') as nombre_subcategoria
        from reportes as r
        join reporte_categorias as rc on rc.id = r.id_categoria
        left join reporte_subcategorias as rs on rs.id = r.id_subcategoria
        where user_id = $id_user and r.activo = 1
        ";
        $reportes = $this->db->query($query)->result_array();
        for ($i = 0; $i < count($reportes); $i++) {
            $reportes[$i]["evidencias"] = $this->obtener_imagenes_de_reporte($reportes[$i]["id"], $business_id);
        }
        return $reportes;
    }

    function obtener_reportes_admin($business_id)
    {
        $query = "select concat(u.name, ' ', u.last_name) as name, u.number_employee, j.job_name, r.id, r.direccion, r.latitud, r.longitud, r.descripcion, date_format(r.fecha,'%Y-%m') as fecha, r.activo, rc.nombre as categoria, coalesce(rs.nombre) as subcategoria
        from reportes as r
        join user as u on u.id = r.user_id
        join reporte_categorias as rc on rc.id = r.id_categoria
        join jobs as j on j.id = u.job_id
        left join reporte_subcategorias as rs on rs.id = r.id_subcategoria
        where u.business_id = $business_id order by r.activo desc";
        $reportes = $this->db->query($query)->result_array();
        for ($i = 0; $i < count($reportes); $i++) {
            $reportes[$i]["evidencias"] = $this->obtener_imagenes_de_reporte($reportes[$i]["id"], $business_id);
        }
        return $reportes;
    }

    function obtener_imagenes_de_reporte($id, $business_id)
    {
        $url = base_url() . 'uploads/business_' . $business_id . '/evidencias_reporte/';
        $query = "select id, concat('$url',archivo) as imagen, tipo from reporte_evidencias
        where id_reporte = " . $id . " and activo = 1";
        return  $this->db->query($query)->result_array();
    }

    function obtener_categorias($business_id)
    {
        $query = "select id, nombre from reporte_categorias where business_id = $business_id and activo = 1";
        return $this->db->query($query)->result_array();
    }

    function obtener_subcategorias($id_categoria)
    {
        $query = "select id, nombre from reporte_subcategorias where id_categoria = $id_categoria and activo = 1";
        return $this->db->query($query)->result_array();
    }

    function eliminar_categoria($id_categoria)
    {
        $this->db->where("id", $id_categoria);
        $this->db->set("activo", 0);
        return $this->db->update("reporte_categorias");
    }

    function eliminar_subcategoria($id_subcategoria)
    {
        $this->db->where("id", $id_subcategoria);
        $this->db->set("activo", 0);
        return $this->db->update("reporte_subcategorias");
    }

    function actualizar_categoria($id_categoria, $data)
    {
        return $this->db->update("reporte_categorias", $data, array("id" => $id_categoria));
    }

    function actualizar_subcategoria($id_subcategoria, $data)
    {
        return $this->db->update("reporte_subcategorias", $data, array("id" => $id_subcategoria));
    }

    function guardar_categoria($data)
    {
        unset($data["token"]);
        return $this->db->insert("reporte_categorias", $data);
    }

    function guardar_subcategoria($data)
    {
        unset($data["token"]);
        return $this->db->insert("reporte_subcategorias", $data);
    }
}
