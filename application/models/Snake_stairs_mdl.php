<?php
class Snake_stairs_mdl extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function insert($entity, $tableName)
    {
        if ($this->db->insert($tableName, $entity)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    public function update($id, $entity, $tableName)
    {
        if ($this->db->update($tableName, $entity, array("id" => $id))) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($where, $tableName)
    {
        if ($this->db->delete($tableName, $where)) {
            return true;
        } else {
            return false;
        }
    }

    public function fetchAll($tableName)
    {
        return $this->db->get($tableName)->result_array();
    }

    public function fetchAllById($by, $id, $tableName)
    {
        return $this->db->get_where($tableName, array($by => $id))->result_array();
    }

    public function fetchAllBy($where, $tableName)
    {
        return $this->db->get_where($tableName, $where)->result_array();
    }



    public function getMyGames($user_id)
    {
        $query  =   "SELECT 
                         id,game_name,map_name,user_id_inturn,my_turn,owned
                        FROM (
                                SELECT 
                                    game_snake_stairs_members.game_id as 'id',
                                    game_snake_stairs_active_game.game_name,
                                    game_snake_stairs_maps.map_name,
                                    game_snake_stairs_active_turn.user_id_inturn,
                                    if(game_snake_stairs_active_turn.user_id_inturn = " . $user_id . " , 1, 0) as 'my_turn',
                                    if(game_snake_stairs_active_game.owner_id = " . $user_id . " , 1, 0) as 'owned'
                                FROM game_snake_stairs_members
                                LEFT JOIN game_snake_stairs_active_game ON game_snake_stairs_active_game.id = game_snake_stairs_members.game_id
                                LEFT JOIN game_snake_stairs_maps ON game_snake_stairs_maps.id = game_snake_stairs_active_game.map_id
                                LEFT JOIN game_snake_stairs_active_turn ON game_snake_stairs_active_turn.game_id = game_snake_stairs_members.game_id
                                WHERE game_snake_stairs_members.user_id = " . $user_id . " ORDER BY game_snake_stairs_members.game_id ASC 
                            ) getGames
                        ORDER BY my_turn DESC";

        return $this->db->query($query)->result_array();
    }

    public function gameMembers($game_id)
    {
        $query  =   "SELECT 
                        game_snake_stairs_members.game_id,
                        game_snake_stairs_members.user_id,
                        game_snake_stairs_members.position,
                        game_snake_stairs_members.turn,
                        game_snake_stairs_members.strike,
                        CONCAT(user.name, ' ' , user.last_name) as 'user_name',
                        user.profile_photo
                    FROM game_snake_stairs_members 
                    LEFT JOIN user ON user.id = game_snake_stairs_members.user_id
                    WHERE game_snake_stairs_members.game_id = " . $game_id;
        return $this->db->query($query)->result_array();
    }



    public function getgGameData($game_id)
    {
        $query = "SELECT 
                         game_snake_stairs_active_game.id,
                         game_snake_stairs_maps.id as 'map_id',
                         game_snake_stairs_maps.map_name,
                         game_snake_stairs_maps.num_boxs
                    FROM game_snake_stairs_active_game
                    LEFT JOIN game_snake_stairs_maps ON game_snake_stairs_active_game.map_id = game_snake_stairs_maps.id
                    WHERE game_snake_stairs_active_game.id = " . $game_id;
        return $this->db->query($query)->result_array();
    }

    public function getMapBoxes($map_id)
    {
        $query = "SELECT 
                        game_snake_stairs_boxes.box_number,
                        CONCAT('https://kreativeco.com/nuup' , game_snake_stairs_boxes.box_image ) as 'box_image',
                        game_snake_stairs_boxes.rule
                    FROM game_snake_stairs_boxes
                    WHERE game_snake_stairs_boxes.map_id = " . $map_id . "
                    ORDER BY game_snake_stairs_boxes.box_number DESC";
        return $this->db->query($query)->result_array();
    }

    public function getUserPositionGame($game_id, $user_id)
    {
        $query = "SELECT * FROM game_snake_stairs_members WHERE game_id = " . $game_id . " AND user_id = " . $user_id;
        return $this->db->query($query)->result_array();
    }

    public function getNextBox($map_id, $box_number)
    {
        $query = "SELECT * FROM game_snake_stairs_boxes WHERE map_id = " . $map_id . " AND  box_number = " . $box_number;
        return $this->db->query($query)->result_array();
    }

    public function getNextTurn($game_id, $current_turn)
    {
        $query = "SELECT 
                        turn ,user_id
                    FROM game_snake_stairs_members 
                    WHERE turn = (SELECT min(turn) FROM game_snake_stairs_members WHERE turn > " . $current_turn . ") AND game_id = " . $game_id;
        $result = $this->db->query($query)->result_array();

        if (!empty($result)) {
            return $result[0]['user_id'];
        } else {
            $query = "SELECT 
                         turn ,user_id
                    FROM game_snake_stairs_members 
                    WHERE turn = 1 AND game_id = " . $game_id;
            $result = $this->db->query($query)->result_array();
            return $result[0]['user_id'];
        }
    }

    public function getQuestion($business_id)
    {
        $this->db->select("*");
        $this->db->from("game_snake_stairs_questions");
        $this->db->where("business_id", $business_id);
        $this->db->order_by("rand()");
        $this->db->limit("1");
        $question = $this->db->get()->result_array();
        $question[0]["answer"] = $this->obtenerRespuestas($question[0]["id"]);
        return $question[0];
    }

    public function obtenerRespuestas($id_pregunta)
    {
        $this->db->select("*");
        $this->db->from("game_snake_stairs_question_answers");
        $this->db->where("question_id", $id_pregunta);
        return $this->db->get()->result_array();
    }

    public function save_answer($data)
    {
        return $this->db->insert("game_snake_stairs_results", $data);
    }

    public function obtenerPreguntas($business_id)
    {
        $this->db->select("*");
        $this->db->from("game_snake_stairs_questions");
        $this->db->where("business_id", $business_id);
        $preguntas = $this->db->get()->result_array();
        for ($i = 0; $i < count($preguntas); $i++) {
            $preguntas[$i]["respuestas"] = $this->obtenerRespuestas($preguntas[$i]["id"]);
        }
        return $preguntas;
    }

    public function agregarPregunta($data, $respuestas)
    {
        $this->db->insert("game_snake_stairs_questions", $data);
        $id = $this->db->insert_id();
        for ($i = 0; $i < count($respuestas); $i++) {
            $data_ = $respuestas[$i];
            $data_["question_id"] = $id;
            $this->db->insert("game_snake_stairs_question_answers", $data_);
        }
        return true;
    }

    public function eliminarPregunta($id_pregunta)
    {
        $this->db->where("id", $id_pregunta);
        return $this->db->delete("game_snake_stairs_questions");
    }

    public function actualizarPregunta($data)
    {
        $this->db->set("question", $data["question"]);
        $this->db->where("id", $data["id"]);
        return $this->db->update("game_snake_stairs_questions");
    }

    public function eliminarRespuesta($id_respuesta)
    {
        $this->db->where("id", $id_respuesta);
        return $this->db->delete("game_snake_stairs_question_answers");
    }

    public function actualizarRespuesta($data)
    {
        $this->db->set("answer", $data["answer"]);
        $this->db->set("correct", $data["correct"]);
        $this->db->where("id", $data["id"]);
        return $this->db->update("game_snake_stairs_question_answer");
    }

    public function agregarRespuesta($data)
    {
        return $this->db->insert("game_snake_stairs_question_answers", $data);
    }
}
