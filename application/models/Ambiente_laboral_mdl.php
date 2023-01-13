<?php
class Ambiente_laboral_mdl extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function obtener_fechas()
    {
        $query = "select date_format(fecha_inicio,'%Y-%m-%d') as fecha_inicio, date_format(fecha_fin,'%Y-%m-%d') as fecha_fin from configuraciones_a_l";
        return $this->db->query($query)->result_array();
    }

    function insertar_fechas($fecha_inicio, $fecha_fin)
    {
        $query = "delete from configuraciones_a_l";
        $this->db->query($query);
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        return $this->db->insert("configuraciones_a_l", $data);
    }

    function obtener_dias_pregunta($question_id)
    {
        $query = "select * from questions_a_l_conf where question_id = $question_id";
        return $this->db->query($query)->result_array(); //[{id : 1,nombre : 'cuestionario',cantidad:10}]
    }

    function guardar_dias_preguntas($datos)
    {
        for ($i = 0; $i < count($datos); $i++) {
            $id = $datos[$i]["id"];
            unset($datos[$i]["id"]);
            $this->db->where("question_id", $id);
            $this->db->update("questions_a_l_conf", $datos[$i]);
        }
        return true;
    }

    function comprobar_dia($id)
    {
        $dia = date("N");
        $dias = ["domingo", "lunes", "martes", "miercoles", "jueves", "viernes", "sabado", "domingo"];
        $dia = $dias[$dia];
        $query = "select $dia from questions_a_l_conf where question_id = $id";
        $result =  $this->db->query($query)->result_array();
        if (count($result) > 0) {
            return $result[0][$dia];
        } else {
            return 0;
        }
    }

    function comprobar_rango_fechas()
    {
        $fecha_actual = date("Y-m-d");
        $query = "select * from configuraciones_a_l
        where date_format(fecha_inicio,'%Y-%m-%') <= '" . date("Y-m-d") . "' && date_format(fecha_fin,'%Y-%m-%d') >= '" . date("Y-m-d") . "'";
        $result = $this->db->query($query)->result_array();
        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function establecer_activo($id_cuestionario, $business_id)
    {
        $this->db->where("business_id", $business_id);
        $this->db->set("activo_al", 0);
        $this->db->update("question_quiz");

        $this->db->where("id", $id_cuestionario);
        $this->db->set("activo_al", 1);
        return $this->db->update("question_quiz");
    }
}
