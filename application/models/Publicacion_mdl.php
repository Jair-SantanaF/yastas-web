<?php
class Publicacion_mdl extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function guardarSeccionUsuario($datos)
    {

        $fecha = date("Y-m-d H:i:s");

        $seccion_usuario = array(
            'id_usuario' => $datos["id_usuario"],
            'id_seccion' => $datos['id_seccion'],
            'fecha' => $datos['fecha'],
            'hora' => $datos['hora'],
            'fecha_creacion' => $fecha,
            'activo' => 1,
            'latitud' => $datos["latitud"],
            'longitud' => $datos["longitud"]
        );

        if ($datos['imagen']) {
            $seccion_usuario['imagen'] = $datos['imagen'];
        }

        $this->db->insert('secciones_usuarios', $seccion_usuario);

        $id = $this->db->insert_id();

        $nuevo = $this->listarSeccionesUsuarios((object)array("id_seccion_usuario" => $id));

        return $nuevo;
    }

    public function listarSeccionesUsuarios($params = null)
    {
        $this->db->select('                         
             su.id AS id_seccion_usuario, su.id_usuario, su.id_seccion, s.seccion, su.fecha, su.hora       
        ');

        $this->db->from('secciones_usuarios su');
        $this->db->join('secciones s', 'su.id_seccion = s.id');

        $where = array(
            'su.activo' => 1
        );

        if (isset($params->id_seccion_usuario)) {
            $where['su.id'] = $params->id_seccion_usuario;
        }

        $this->db->where($where);
        $this->db->order_by("su.fecha_creacion desc");

        $query = $this->db->get();

        return $query->result_array();
    }
}
