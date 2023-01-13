<?php
class Teams_mdl extends CI_Model
{
    function __construct(){
        parent::__construct();
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

    function getBecarios($tutorId, $empresaId){
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
            tutores_becarios.tutor_id = ? AND
            user.business_id = ?
        ";

        return $this->db->query($query, [$tutorId, $empresaId])->result_object();
    }


    // =========================================================================
    function getTutoresAsesores($empresaId){
        $query = "
        SELECT
            asesores_tutores.id AS id,
            user_asesor.id AS asesor_id, 
            CONCAT(' ', user_asesor.name, user_asesor.last_name) AS nombre_asesor, 
            asesores_tutores.tutor_id AS tutor_id,
            CONCAT(' ', user_tutor.name, user_tutor.last_name) AS nombre_tutor
        FROM
            asesores_tutores
            INNER JOIN
            user AS user_asesor
            ON 
                asesores_tutores.asesor_id = user_asesor.id
            INNER JOIN
            user AS user_tutor
            ON 
                asesores_tutores.tutor_id = user_tutor.id
        WHERE
            asesores_tutores.empresa_id = ?
        ";

        return $this->db->query($query, [$empresaId])->result_object();
    }

    function getBecariosTutores($empresaId){
        $query = "
            SELECT
            tutores_becarios.id, 
            user_tutor.id AS tutor_id, 
            user_becario.id AS becario_id,
            CONCAT(' ', user_tutor.name, user_tutor.last_name) AS nombre_tutor,
            CONCAT(' ', user_becario.name, user_becario.last_name) AS nombre_becario
        FROM
            tutores_becarios
            INNER JOIN
            user AS user_tutor
            ON 
                tutores_becarios.tutor_id = user_tutor.id
            INNER JOIN
            user AS user_becario
            ON 
                tutores_becarios.becario_id = user_becario.id
        WHERE
            tutores_becarios.empresa_id = ?
        ";

        return $this->db->query($query, [$empresaId])->result_object();
    }

    function getBecario($id, $empresaId){
        $sql = "
        SELECT
            tutores_becarios.id AS tutores_becarios_id, 
            tutores_becarios.tutor_id, 
            tutores_becarios.becario_id, 
            tutores_becarios.empresa_id, 
            CONCAT(' ', user.name, user.last_name) AS becario_nombre, 
            user.id_region AS region_id
        FROM
            tutores_becarios
            INNER JOIN
                user
            ON 
                tutores_becarios.becario_id = user.id
        WHERE
            tutores_becarios.becario_id = ? AND
            tutores_becarios.empresa_id = ?
        ";

        $query = $this->db->query($sql, [$id, $empresaId]);
        $result = $query->row();

        return $result;
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

    // actualizar region
    function updateRegion($user_id, $region_id){
        $dataDB["id_region"] = $region_id;
        $this->db->where("id", $user_id);
        $this->db->update("user", $dataDB);

        $data["success"] = true;
        $data["message"] = "Información actualizada correctamente";

        return $data;
    }


    // actualizar asociación de un tutor y un becario
    function updateTutorBecario($becario_id, $tutor_id){
        $dataDB["tutor_id"] = $tutor_id;
        $this->db->where("becario_id", $becario_id);
        $this->db->update("tutores_becarios", $dataDB);

        $data["success"] = true;
        $data["message"] = "Información actualizada correctamente";

        return $data;
    }


    // actualizar asociación de un tutor y un becario
    function deleteAsociacionBecarioTutor($becarioId){
        $this->db->where("becario_id", $becarioId);
        $this->db->delete("tutores_becarios");

        if($this->db->affected_rows() > 0){
            $data["success"] = true;
            $data["message"] = "Asociación de becario a un tutor eliminado exitosamente";

            return $data;

        }else{
            $data["success"] = false;
            $data["message"] = "La asociación de becario a un tutor no se pudo eliminar";

            return $data;
        }
    }


    // obtener los datos de un tutor
    function getTutor($id, $empresaId){
        $sql = "
        SELECT
            asesores_tutores.id AS asesores_tutores_id,
            asesores_tutores.tutor_id,
            asesores_tutores.asesor_id,
            asesores_tutores.empresa_id,
            CONCAT( ' ', user.NAME, user.last_name ) AS tutor_nombre,
            user.id_region AS region_id 
        FROM
            asesores_tutores
            INNER JOIN user ON asesores_tutores.tutor_id = user.id 
        WHERE
            asesores_tutores.tutor_id = ? AND 
            asesores_tutores.empresa_id = ?
        ";

        $query = $this->db->query($sql, [$id, $empresaId]);
        $result = $query->row();

        return $result;
    }


    // obtener los asesores dado una region
    function getAsesores($regionId, $empresaId){
        $sql = "
        SELECT
            user.id,
            user.business_id, 
            user.rol_id, 
            user.id_region,
            CONCAT(' ', user.name, user.last_name) AS nombre
        FROM
            user
        WHERE
            user.rol_id = 6 AND
            user.id_region = ? AND
            user.business_id = ?
        ";

        $query = $this->db->query($sql, [$regionId, $empresaId]);
        $result = $query->result_object();

        return $result;
    }


    // actualizar asociación de un asesor y un tutor
    function updateAsesorTutor($tutorId, $asesorId){
        $dataDB["asesor_id"] = $asesorId;
        $this->db->where("tutor_id", $tutorId);
        $this->db->update("asesores_tutores", $dataDB);

        $data["success"] = true;
        $data["message"] = "Información actualizada correctamente";

        return $data;
    }


    // actualizar asociación de un tutor y un asesor
    function deleteAsociacionTutorAsesor($tutorId){
        $this->db->where("tutor_id", $tutorId);
        $this->db->delete("asesores_tutores");

        if($this->db->affected_rows() > 0){
            $data["success"] = true;
            $data["message"] = "Asociación de tutor a un asesor eliminado exitosamente";

            return $data;

        }else{
            $data["success"] = false;
            $data["message"] = "La asociación de tutor a un asesor no se pudo eliminar";

            return $data;
        }
    }


    // obtener lista de tutores de una region
    function getTutoresRegion($regionId, $empresaId){
        $query = "
        SELECT
            user.id AS tutor_id, 
            CONCAT(' ', user.name, user.last_name) AS nombre_tutor
        FROM
            user
            LEFT JOIN asesores_tutores ON user.id = asesores_tutores.tutor_id
        WHERE
            user.rol_id = 8 
            AND user.id_region = ?
            AND user.business_id = ?
            AND asesores_tutores.tutor_id IS NULL
        ";

        return $this->db->query($query, [$regionId, $empresaId])->result_object();
    }


    // obtener lista de becarios de una region
    function getBecariosRegion($regionId, $empresaId){
        $query = "
        SELECT
            user.id AS becario_id,
            CONCAT(' ', user.name, user.last_name ) AS nombre_becario 
        FROM
            user
            LEFT JOIN tutores_becarios ON user.id = tutores_becarios.becario_id 
        WHERE
            user.rol_id = 9 
            AND user.id_region = ?
            AND user.business_id = ?
            AND tutores_becarios.becario_id IS NULL
        ";

        return $this->db->query($query, [$regionId, $empresaId])->result_object();
    }


    // guardar relación entre asesor y tutor
    function saveAsesorTutor($empresaId, $asesorId, $tutores){
        $dataDB["empresa_id"] = $empresaId;
        $dataDB["asesor_id"] = $asesorId;

        foreach($tutores as $tutor){
            $dataDB["tutor_id"] = $tutor;
            $this->db->insert("asesores_tutores", $dataDB);
        }

        $data["success"] = true;
        $data["message"] = "Información guardada correctamente";

        return $data;
    }


    // guardar relación entre tutor y becario
    function saveTutorBecario($empresaId, $tutorId, $becarios){
        $dataDB["empresa_id"] = $empresaId;
        $dataDB["tutor_id"] = $tutorId;

        foreach($becarios as $becario){
            $dataDB["becario_id"] = $becario;
            $this->db->insert("tutores_becarios", $dataDB);
        }

        $data["success"] = true;
        $data["message"] = "Información guardada correctamente";

        return $data;
    }
}
