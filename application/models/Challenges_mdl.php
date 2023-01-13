<?php
class Challenges_mdl extends CI_Model
{
    function __construct(){
        parent::__construct();
    }

    //====================================================================================================
    //=====================================  ASESOR O ADMINISTRADOR  ======================================
    function getRetosCsv($asesorId, $empresaId, $isAdmin){
        $query = "
        SELECT
            retos.id AS reto_id, 
            retos.tutor_id AS tutor_id, 
            retos.becario_id AS becario_id, 
            retos.estatus_id AS reto_estatus_id, 
            retos.nombre AS reto_nombre, 
            retos.detalles AS reto_detalles, 
            retos.feedback AS reto_feedback,
            retos.created_at,
            CONCAT(' ', user_tutor.name, user_tutor.last_name ) AS tutor_nombre,
            CONCAT(' ', user_becario.name, user_becario.last_name ) AS becario_nombre,
            estatus_reto.nombre AS reto_estatus_nombre
        FROM
            asesores_tutores
            INNER JOIN
            user AS user_tutor
            ON 
                asesores_tutores.tutor_id = user_tutor.id
            INNER JOIN
            retos
            ON 
                user_tutor.id = retos.tutor_id
            INNER JOIN
            user AS user_becario
            ON 
                retos.becario_id = user_becario.id
            INNER JOIN
            estatus_reto
            ON 
                retos.estatus_id = estatus_reto.id
        WHERE
            asesores_tutores.empresa_id = ?
        ";

        if($isAdmin){
            return $this->db->query($query, [$empresaId])->result_object();
        }

        $query .= " AND asesores_tutores.asesor_id = ?";
        return $this->db->query($query, [$empresaId, $asesorId])->result_object();
    }
    
    // En retos cargados solo se mostrarán los retos 
    // que aun no estan completados ya sea por el becario o por el tutor
    function getRetosCargados($asesorId, $empresaId, $isAdmin){
        $query = "
        SELECT
            retos.id AS reto_id, 
            retos.tutor_id AS tutor_id, 
            retos.becario_id AS becario_id, 
            retos.estatus_id AS reto_estatus_id, 
            retos.nombre AS reto_nombre, 
            retos.detalles AS reto_detalles, 
            retos.feedback AS reto_feedback,
            retos.created_at,
            CONCAT(' ', user_tutor.name, user_tutor.last_name ) AS tutor_nombre,
            CONCAT(' ', user_becario.name, user_becario.last_name ) AS becario_nombre,
            estatus_reto.nombre AS reto_estatus_nombre
        FROM
            asesores_tutores
            INNER JOIN
            user AS user_tutor
            ON 
                asesores_tutores.tutor_id = user_tutor.id
            INNER JOIN
            retos
            ON 
                user_tutor.id = retos.tutor_id
            INNER JOIN
            user AS user_becario
            ON 
                retos.becario_id = user_becario.id
            INNER JOIN
            estatus_reto
            ON 
                retos.estatus_id = estatus_reto.id
        WHERE
            MONTH(retos.created_at) = MONTH(CURRENT_DATE()) AND
            (retos.estatus_id = 1 OR retos.estatus_id = 2) AND
            asesores_tutores.empresa_id = ?
        ";

        if($isAdmin){
            return $this->db->query($query, [$empresaId])->result_object();
        }

        $query .= " AND asesores_tutores.asesor_id = ?";
        return $this->db->query($query, [$empresaId, $asesorId])->result_object();
    }

    // En retos realizados solo se mostrarán los retos
    // que ya estan completados por el becario y el tutor
    function getRetosRealizados($asesorId, $empresaId, $isAdmin){
        $query = "
        SELECT
            retos.id AS reto_id, 
            retos.tutor_id AS tutor_id, 
            retos.becario_id AS becario_id, 
            retos.estatus_id AS reto_estatus_id, 
            retos.nombre AS reto_nombre, 
            retos.detalles AS reto_detalles, 
            retos.feedback AS reto_feedback,
            retos.created_at,
            CONCAT(' ', user_tutor.name, user_tutor.last_name ) AS tutor_nombre,
            CONCAT(' ', user_becario.name, user_becario.last_name ) AS becario_nombre,
            estatus_reto.nombre AS reto_estatus_nombre
        FROM
            asesores_tutores
            INNER JOIN
            user AS user_tutor
            ON 
                asesores_tutores.tutor_id = user_tutor.id
            INNER JOIN
            retos
            ON 
                user_tutor.id = retos.tutor_id
            INNER JOIN
            user AS user_becario
            ON 
                retos.becario_id = user_becario.id
            INNER JOIN
            estatus_reto
            ON 
                retos.estatus_id = estatus_reto.id
        WHERE
            MONTH(retos.created_at) = MONTH(CURRENT_DATE()) AND
            retos.estatus_id = 3 AND
            asesores_tutores.empresa_id = ?
        ";

        if($isAdmin){
            return $this->db->query($query, [$empresaId])->result_object();
        }

        $query .= " AND asesores_tutores.asesor_id = ?";
        return $this->db->query($query, [$empresaId, $asesorId])->result_object();
    }

    // obtener las regiones de una empresa
    function getRegiones($empresaId){
        $query = "
        SELECT
            id,
            nombre
        FROM
            regiones
        WHERE
            business_id = ?
        ";

        return $this->db->query($query, [$empresaId])->result_object();
    }

    // obtener los tutores dado una region
    function getTutores($regionId, $empresaId){
        $query = "
        SELECT
            id,
            CONCAT(' ', name, last_name ) AS nombre
        FROM
            user
        WHERE
            rol_id = 8 AND
            id_region = ? AND
            business_id = ?
        ";

        return $this->db->query($query, [$regionId, $empresaId])->result_object();
    }

    // actualizar nombre y detalle del reto
    function updateReto($id, $nombre, $detalle){
        $data["nombre"] = $nombre;
        $data["detalles"] = $detalle;
        $this->db->where("id", $id);

        $this->db->update("retos", $data);

        if($this->db->affected_rows() > 0){
            $data["success"] = true;
            $data["message"] = "Reto actualizado correctamente";

            return $data;

        }else{
            $data["success"] = false;
            $data["message"] = "El reto no se pudo actualizar";

            return $data;
        }
    }

    // eliminar un reto
    function deleteReto($id){
        $this->db->where("id", $id);
        $this->db->delete("retos");

        if($this->db->affected_rows() > 0){
            $data["success"] = true;
            $data["message"] = "Reto eliminado correctamente";

            return $data;

        }else{
            $data["success"] = false;
            $data["message"] = "El reto no se pudo eliminar";

            return $data;
        }
    }

    // En retos cargados solo se mostrarán los retos 
    // que aun no estan completados ya sea por el becario o por el tutor
    // y que contienen un filtro
    function getRetosCargadosFiltro($asesorId, $empresaId, $filtro, $isAdmin){
        $query = "
        SELECT
            retos.id AS reto_id, 
            retos.tutor_id AS tutor_id, 
            retos.becario_id AS becario_id, 
            retos.estatus_id AS reto_estatus_id, 
            retos.nombre AS reto_nombre, 
            retos.detalles AS reto_detalles, 
            retos.feedback AS reto_feedback,
            retos.created_at,
            CONCAT(' ', user_tutor.name, user_tutor.last_name ) AS tutor_nombre,
            CONCAT(' ', user_becario.name, user_becario.last_name ) AS becario_nombre,
            estatus_reto.nombre AS reto_estatus_nombre
        FROM
            asesores_tutores
            INNER JOIN
            user AS user_tutor
            ON 
                asesores_tutores.tutor_id = user_tutor.id
            INNER JOIN
            retos
            ON 
                user_tutor.id = retos.tutor_id
            INNER JOIN
            user AS user_becario
            ON 
                retos.becario_id = user_becario.id
            INNER JOIN
            estatus_reto
            ON 
                retos.estatus_id = estatus_reto.id
        WHERE
            retos.mes_curso = ? AND
            (retos.estatus_id = 1 OR retos.estatus_id = 2) AND
            asesores_tutores.empresa_id = ?
        ";

        if($isAdmin){
            return $this->db->query($query, [$filtro, $empresaId])->result_object();
        }

        $query .= " AND asesores_tutores.asesor_id = ?";
        return $this->db->query($query, [$filtro, $empresaId, $asesorId])->result_object();
    }

    // En retos realizados solo se mostrarán los retos
    // que ya estan completados por el becario y el tutor
    // y que contienen un filtro
    function getRetosRealizadosFiltro($asesorId, $empresaId, $filtro, $isAdmin){
        $query = "
        SELECT
            retos.id AS reto_id, 
            retos.tutor_id AS tutor_id, 
            retos.becario_id AS becario_id, 
            retos.estatus_id AS reto_estatus_id, 
            retos.nombre AS reto_nombre, 
            retos.detalles AS reto_detalles, 
            retos.feedback AS reto_feedback,
            retos.created_at,
            CONCAT(' ', user_tutor.name, user_tutor.last_name ) AS tutor_nombre,
            CONCAT(' ', user_becario.name, user_becario.last_name ) AS becario_nombre,
            estatus_reto.nombre AS reto_estatus_nombre
        FROM
            asesores_tutores
            INNER JOIN
            user AS user_tutor
            ON 
                asesores_tutores.tutor_id = user_tutor.id
            INNER JOIN
            retos
            ON 
                user_tutor.id = retos.tutor_id
            INNER JOIN
            user AS user_becario
            ON 
                retos.becario_id = user_becario.id
            INNER JOIN
            estatus_reto
            ON 
                retos.estatus_id = estatus_reto.id
        WHERE
            retos.mes_curso = ? AND
            retos.estatus_id = 3 AND
            asesores_tutores.empresa_id = ?
        ";

        if($isAdmin){
            return $this->db->query($query, [$filtro, $empresaId])->result_object();
        }

        $query .= " AND asesores_tutores.asesor_id = ?";
        return $this->db->query($query, [$filtro, $empresaId, $asesorId])->result_object();
    }


    //====================================================================================================
    //=====================================  TUTOR  ==================================================
    function getBecarios($tutorId, $empresaId, $isAdmin){
        $query = "
        SELECT
            user.id AS becario_id,
            CONCAT(' ', user.name, user.last_name) AS becario_nombre,
            user.rol_id
        FROM
            user
            INNER JOIN
            tutores_becarios
            ON 
                user.id = tutores_becarios.becario_id
        WHERE
            user.business_id = ?
        ";
        
        if($isAdmin){
            return $this->db->query($query, [$empresaId])->result_object();
        }

        $query .= " AND tutores_becarios.tutor_id = ?";
        return $this->db->query($query, [$empresaId, $tutorId])->result_object();
    }

    function getRetosTutor($tutorId, $empresaId){
        $query = "
        SELECT
            retos.id AS reto_id, 
            retos.nombre AS reto_nombre, 
            retos.detalles AS reto_detalles, 
            retos.mes_curso, 
            retos.comentarios AS reto_comentarios,
            retos.feedback AS reto_feedback, 
            user.id AS becario_id, 
            CONCAT(' ', user.name, user.last_name) AS becario_nombre,
            retos.estatus_id AS reto_estatus_id, 
            estatus_reto.nombre AS reto_estatus_nombre,
            retos.created_at
        FROM
            user
            INNER JOIN
            retos
            ON 
                user.id = retos.becario_id
            INNER JOIN
            estatus_reto
            ON 
                retos.estatus_id = estatus_reto.id
        WHERE
            retos.tutor_id = ? AND
            retos.empresa_id = ?
        ";

        return $this->db->query($query, [$tutorId, $empresaId])->result_object();
    }

    function getRetosPendientesTutor($tutorId, $empresaId){
        $query = "
        SELECT
            retos.id AS reto_id, 
            retos.nombre AS reto_nombre, 
            retos.detalles AS reto_detalles, 
            retos.mes_curso,
            retos.comentarios AS reto_comentarios,
            retos.feedback AS reto_feedback, 
            user.id AS becario_id, 
            CONCAT('', user.name, user.last_name) AS becario_nombre, 
            retos.estatus_id AS reto_estatus_id,
            estatus_reto.nombre AS reto_estatus_nombre,
            retos.created_at
        FROM
            user
            INNER JOIN
            retos
            ON 
                user.id = retos.becario_id
            INNER JOIN
            estatus_reto
            ON 
                retos.estatus_id = estatus_reto.id
        WHERE
            MONTH(retos.created_at) = MONTH(CURRENT_DATE()) AND
            retos.estatus_id = 2 AND
            retos.tutor_id = ? AND
            retos.empresa_id = ?
        ";
        return $this->db->query($query, [$tutorId, $empresaId])->result_object();
    }

    function getRetosRealizadosTutor($tutorId, $empresaId){
        $query = "
        SELECT
            retos.id AS reto_id, 
            retos.nombre AS reto_nombre, 
            retos.detalles AS reto_detalles, 
            retos.mes_curso, 
            retos.feedback AS reto_feedback, 
            user.id AS becario_id, 
            CONCAT('', user.name, user.last_name) AS becario_nombre, 
            retos.estatus_id AS reto_estatus_id,
            estatus_reto.nombre AS reto_estatus_nombre,
            retos.created_at
        FROM
            user
            INNER JOIN
            retos
            ON 
                user.id = retos.becario_id
            INNER JOIN
            estatus_reto
            ON 
                retos.estatus_id = estatus_reto.id
        WHERE
            retos.tutor_id = ? AND
            retos.empresa_id = ? AND
            retos.estatus_id = 3
        ";
        return $this->db->query($query, [$tutorId, $empresaId])->result_object();
    }

    function crearReto($empresaId, $estatus_id, $tutorId, $becarioId, $reto, $detalles, $mes){
        $data["empresa_id"] = $empresaId;
        $data["estatus_id"] = $estatus_id;
        $data["tutor_id"] = $tutorId;
        $data["becario_id"] = $becarioId;
        $data["nombre"] = $reto;
        $data["detalles"] = $detalles;
        $data["mes_curso"] = $mes;

        return $this->db->insert("retos", $data);
    }

    function updateFeedBackReto($id, $feedback, $userId){
        $query = $this->db->query('SELECT id, estatus_id, feedback, tutor_id FROM retos WHERE id = ?', [$id]);
        $result = $query->row();

        if($result->tutor_id != $userId){
            $data["success"] = false;
            $data["message"] = "El reto no pertenece al usuario";

            return $data;
        }

        if($result->estatus_id == 1){
            $data["success"] = false;
            $data["message"] = "No es posible agregar un feedback por que el reto aún no se encuentra finalizado por el becario.";

            return $data;
        }

        if($result->feedback != null){
            $data["success"] = false;
            $data["message"] = "El reto ya cuenta con un feedback";

            return $data;
        }

        $data["estatus_id"] = 3;
        $data["feedback"] = $feedback;
        $data["completed_tutor_at"] = date('y-m-d');
        $this->db->where("id", $id);

        $this->db->update("retos", $data);

        if($this->db->affected_rows() > 0){
            $data["success"] = true;
            $data["message"] = "Feedback actualizado";

            return $data;
        }
    }

    function getRetosPendientesBecarioByBecarioId($becarioId, $tutorId){
        $query = "
        SELECT
            retos.id AS reto_id, 
            retos.nombre AS reto_nombre, 
            retos.detalles AS reto_detalles, 
            retos.mes_curso,
            retos.comentarios AS reto_comentarios,
            retos.feedback AS reto_feedback, 
            user.id AS tutor_id, 
            CONCAT(' ', user.name, user.last_name) AS tutor_nombre,
            retos.estatus_id AS reto_estatus_id, 
            estatus_reto.nombre AS reto_estatus_nombre,
            retos.created_at
        FROM
            user
            INNER JOIN
            retos
            ON 
                user.id = retos.tutor_id
            INNER JOIN
            estatus_reto
            ON 
                retos.estatus_id = estatus_reto.id
        WHERE
            MONTH(retos.created_at) = MONTH(CURRENT_DATE()) AND
            retos.estatus_id = 1 AND
            retos.becario_id = ? AND
            retos.tutor_id = ?
        ";

        return $this->db->query($query, [$becarioId, $tutorId])->result_object();
    }

    function getRetosPendientesTutorByBecarioId($becarioId, $tutorId){
        $query = "
        SELECT
            retos.id AS reto_id, 
            retos.nombre AS reto_nombre, 
            retos.detalles AS reto_detalles, 
            retos.mes_curso,
            retos.comentarios AS reto_comentarios,
            retos.feedback AS reto_feedback, 
            user.id AS tutor_id, 
            CONCAT(' ', user.name, user.last_name) AS tutor_nombre,
            retos.estatus_id AS reto_estatus_id, 
            estatus_reto.nombre AS reto_estatus_nombre,
            retos.created_at
        FROM
            user
            INNER JOIN
            retos
            ON 
                user.id = retos.tutor_id
            INNER JOIN
            estatus_reto
            ON 
                retos.estatus_id = estatus_reto.id
        WHERE
            MONTH(retos.created_at) = MONTH(CURRENT_DATE()) AND
            retos.estatus_id = 2 AND
            retos.becario_id = ? AND
            retos.tutor_id = ?
        ";

        return $this->db->query($query, [$becarioId, $tutorId])->result_object();
    }


    //====================================================================================================
    //=====================================  BECARIO  ==================================================
    function getRetosBecario($becarioId, $empresaId){
        $query = "
        SELECT
            retos.id AS reto_id, 
            retos.nombre AS reto_nombre, 
            retos.detalles AS reto_detalles, 
            retos.mes_curso,
            retos.comentarios AS reto_comentarios,
            retos.feedback AS reto_feedback, 
            user.id AS tutor_id, 
            CONCAT(' ', user.name, user.last_name) AS tutor_nombre,
            retos.estatus_id AS reto_estatus_id, 
            estatus_reto.nombre AS reto_estatus_nombre,
            retos.created_at
        FROM
            user
            INNER JOIN
            retos
            ON 
                user.id = retos.tutor_id
            INNER JOIN
            estatus_reto
            ON 
                retos.estatus_id = estatus_reto.id
        WHERE
            retos.becario_id = ? AND
            retos.empresa_id = ?
        ";

        return $this->db->query($query, [$becarioId, $empresaId])->result_object();
    }

    function getRetosPendientesBecario($becarioId, $empresaId){
        $query = "
        SELECT
            retos.id AS reto_id, 
            retos.nombre AS reto_nombre, 
            retos.detalles AS reto_detalles, 
            retos.mes_curso,
            retos.comentarios AS reto_comentarios,
            retos.feedback AS reto_feedback, 
            user.id AS tutor_id, 
            CONCAT(' ', user.name, user.last_name) AS tutor_nombre,
            retos.estatus_id AS reto_estatus_id, 
            estatus_reto.nombre AS reto_estatus_nombre,
            retos.created_at
        FROM
            user
            INNER JOIN
            retos
            ON 
                user.id = retos.tutor_id
            INNER JOIN
            estatus_reto
            ON 
                retos.estatus_id = estatus_reto.id
        WHERE
            MONTH(retos.created_at) = MONTH(CURRENT_DATE()) AND
            retos.estatus_id = 1 AND
            retos.becario_id = ? AND
            retos.empresa_id = ?
        ";

        return $this->db->query($query, [$becarioId, $empresaId])->result_object();
    }

    function updateEstatusReto($id, $userId, $estatus_id, $comentarios){
        $query = $this->db->query('SELECT id, estatus_id, becario_id, comentarios FROM retos WHERE id = ?', [$id]);
        $result = $query->row();

        if($result->becario_id != $userId){
            $data["success"] = false;
            $data["message"] = "El reto no pertenece al usuario";

            return $data;
        }

        if($result->estatus_id == 2){
            $data["success"] = false;
            $data["message"] = "El reto ya fue completado";

            return $data;
        }

        $data["estatus_id"] = $estatus_id;
        $data["comentarios"] = $comentarios;
        $data["completed_becario_at"] = date('y-m-d');
        $this->db->where("id", $id);

        $this->db->update("retos", $data);

        if($this->db->affected_rows() > 0){
            $data["success"] = true;
            $data["message"] = "Reto completado";

            return $data;
        }
    }


    //====================================================================================================
    //=====================================  GENERAL  ==================================================
    function getReto($id){
        $sql = "
        SELECT
            retos.id AS reto_id, 
            retos.nombre AS reto_nombre, 
            retos.detalles AS reto_detalles, 
            retos.mes_curso,
            retos.comentarios AS reto_comentarios,
            retos.feedback AS reto_feedback, 
            user.id AS becario_id, 
            CONCAT(' ', user.name, user.last_name) AS becario_nombre,
            retos.estatus_id AS reto_estatus_id, 
            estatus_reto.nombre AS reto_estatus_nombre,
            retos.created_at
        FROM
            user
            INNER JOIN
            retos
            ON 
                user.id = retos.becario_id
            INNER JOIN
            estatus_reto
            ON 
                retos.estatus_id = estatus_reto.id
        WHERE
            retos.id = ?
        ";

        $query = $this->db->query($sql, [$id]);
        $result = $query->row();

        return $result;
    }

    function getEstatusReto(){
        $query = $this->db->query('SELECT * FROM estatus_reto WHERE activo = 1');
        $result = $query->result_object();

        return $result;
    }

    function crearRetosTodos($empresaId, $estatus_id, $tutorId, $becarioId, $reto, $detalles, $mes){
        $data["empresa_id"] = $empresaId;
        $data["estatus_id"] = $estatus_id;
        $data["tutor_id"] = $tutorId;
        $data["becario_id"] = $becarioId;
        $data["nombre"] = $reto;
        $data["detalles"] = $detalles;
        $data["mes_curso"] = $mes;

        return $this->db->insert("retos", $data);
    }
}