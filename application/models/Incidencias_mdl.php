<?php
class Incidencias_mdl extends CI_Model
{
    function __construct(){
        parent::__construct();
    }


    function getEstatusIncidencias($empresaId){
        $query = "
        SELECT * FROM 
            estatus_incidencia 
        WHERE 
            activo = 1 AND
            empresa_id = ?
        ";

        $query = $this->db->query($query, [$empresaId]);
        $result = $query->result_object();

        return $result;
    }


    function getTotalEstatus($estatusId, $becarioId, $tutorID, $empresaId, $isAdmin){
        $query = "
        SELECT
            count(*) AS total,
            estatus_id
        FROM
            incidencias
        WHERE
            incidencias.empresa_id = ? AND
            incidencias.estatus_id = ? AND
            incidencias.becario_id = ?
        ";

        if($isAdmin){
            return $this->db->query($query, [$empresaId, $estatusId, $becarioId])->result_object();
        }

        $query .= " AND tutor_id = ?";
        return $this->db->query($query, [$empresaId, $estatusId, $becarioId, $tutorID])->result_object();
    }


    function crearIncidencia($empresaId, $estatus_id, $tutorId, $becarioId, $comentarios, $tiempo_retardo){
        $data["empresa_id"] = $empresaId;
        $data["estatus_id"] = $estatus_id;
        $data["tutor_id"] = $tutorId;
        $data["becario_id"] = $becarioId;

        if($comentarios){
            $data["comentarios"] = $comentarios;
        }

        if($tiempo_retardo){
            $data["tiempo_retardo"] = $tiempo_retardo;
        }

        return $this->db->insert("incidencias", $data);
    }


    function getIncidencias($empresaId, $becarioId, $tutorId){
        $query = "
        SELECT
            incidencias.id AS incidencia_id,
            estatus_incidencia.id AS estatus_id,
            estatus_incidencia.nombre AS estatus_nombre,
            incidencias.comentarios,
            incidencias.tiempo_retardo,
            user_tutor.id AS tutor_id,
            CONCAT(' ', user_tutor.NAME, user_tutor.last_name ) AS tutor_nombre,
            user_becario.id AS becario_id,
            CONCAT(' ', user_becario.NAME, user_becario.last_name ) AS becario_nombre,
            incidencias.created_at
        FROM
            incidencias
            INNER JOIN estatus_incidencia ON incidencias.estatus_id = estatus_incidencia.id
            INNER JOIN user AS user_tutor ON incidencias.tutor_id = user_tutor.id
            INNER JOIN user AS user_becario ON incidencias.becario_id = user_becario.id 
        WHERE
            MONTH(incidencias.created_at) = MONTH(CURRENT_DATE()) 
            AND incidencias.empresa_id = ?
            AND incidencias.becario_id = ?
            AND incidencias.tutor_id = ?
        ";

        $query = $this->db->query($query, [$empresaId, $becarioId, $tutorId]);
        $result = $query->result_object();

        return $result;
    }


    function getTotalEstatusCurrentMonth($estatusId, $becarioId, $empresaId, $tutorId, $isFalta){
        $query = "
        SELECT
            count(incidencias.id) AS total
        FROM
            incidencias
            INNER JOIN
            estatus_incidencia
            ON 
                incidencias.estatus_id = estatus_incidencia.id
        WHERE
            MONTH(incidencias.created_at) = MONTH(CURRENT_DATE()) 
            AND incidencias.empresa_id = ? 
            AND incidencias.becario_id = ?
            AND incidencias.tutor_id = ?
        ";

        if($isFalta){
            $query .= "AND (estatus_incidencia.id = ? OR estatus_incidencia.padre = 3)";
            return $this->db->query($query, [$empresaId, $becarioId, $tutorId, $estatusId])->row();
        }

        $query .= "AND estatus_incidencia.id = ?";
        return $this->db->query($query, [$empresaId, $becarioId, $tutorId, $estatusId])->row();
    }


    function getIncidenciasCurrentDay($becarioId, $empresaId, $tutorId){
        $query = "
        SELECT
            count(incidencias.id) AS total
        FROM
            incidencias
        WHERE
            DAY(incidencias.created_at) = DAY(CURRENT_DATE()) 
            AND incidencias.empresa_id = ? 
            AND incidencias.becario_id = ?
            AND incidencias.tutor_id = ?
        ";

        return $this->db->query($query, [$empresaId, $becarioId, $tutorId])->row();
    }


    // apartado de panel web
    // =================================================
    function getIncidenciasWeb($empresaId, $tutorId, $regionId, $fechaInicio, $fechaFin, $isAdmin){
        $query = "
        SELECT
            user_becario.id AS becario_id, 
            CONCAT(' ', user_becario.NAME, user_becario.last_name ) AS becario_nombre, 
            user_becario.number_employee AS becario_numero_empleado, 
            user_tutor.id AS tutor_id, 
            CONCAT(' ', user_tutor.NAME, user_tutor.last_name ) AS tutor_nombre,
            user_tutor.number_employee AS tutor_numero_empleado,
            incidencias.comentarios, 
            incidencias.tiempo_retardo, 
            incidencias.created_at, 
            SUM(IF(incidencias.estatus_id = 1, 1, 0)) AS asistencias,
            SUM(IF(incidencias.estatus_id = 2, 1, 0)) AS retardos,
            SUM(IF(incidencias.estatus_id = 3 OR incidencias.estatus_id = 4 OR incidencias.estatus_id = 5, 1, 0)) AS faltas,
            SUM(IF(incidencias.estatus_id = 4, 1, 0)) AS enfermedad_sin_justificante,
            SUM(IF(incidencias.estatus_id = 5, 1, 0)) AS enfermedad_con_justificante
        FROM
            incidencias
            INNER JOIN estatus_incidencia ON incidencias.estatus_id = estatus_incidencia.id
            INNER JOIN user AS user_tutor ON incidencias.tutor_id = user_tutor.id
            INNER JOIN user AS user_becario ON incidencias.becario_id = user_becario.id
        WHERE
            MONTH(incidencias.created_at) = MONTH(CURRENT_DATE())
        ";

        $array_data = [];
        if($regionId != null){
            $query .= " AND user_becario.id_region = ?";
            array_push($array_data, $regionId);
        }

        if($fechaInicio){
            $query .= " AND incidencias.created_at >= ?";
            array_push($array_data, $fechaInicio);
        }

        if($fechaFin){
            $query .= " AND incidencias.created_at <= ?";
            array_push($array_data, $fechaFin);
        }

        $query .= " AND incidencias.empresa_id = ? ";
        array_push($array_data, $empresaId);

        if($isAdmin){
            $query .= "GROUP BY incidencias.becario_id;";
            return $this->db->query($query, $array_data)->result_object();
        }

        $query .= " AND incidencias.tutor_id = ?";
        $query .= "GROUP BY incidencias.becario_id;";
        array_push($array_data, $tutorId);
        return $this->db->query($query, $array_data)->result_object();
    }
}
