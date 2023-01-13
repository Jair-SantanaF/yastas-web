<?php
class Ruleta_retos_mdl extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    // public function crear_reto($id_retador, $id_retado)
    // {
    //     if ($this->validar_reto($id_retador, $id_retado)) {
    //         $data = array(
    //             "id_retador" => $id_retador,
    //             "id_retado" => $id_retado
    //         );
    //         return $this->db->insert("game_roulette_retos_1_vs_1", $data);
    //     } else {
    //         return false;
    //     }
    // }

    // public function validar_reto($id_retador, $id_retado)
    // {
    //     $this->db->select("*");
    //     $this->db->from("game_roulette_retos_1_vs_1");
    //     $this->db->where("id_retador", $id_retador);
    //     $this->db->where("id_retado", $id_retado);
    //     $this->db->where("activo", true);
    //     $resultado = $this->db->get()->result_array();
    //     if (count($resultado) > 0) {
    //         return false;
    //     } else {
    //         return true;
    //     }
    // }

    // public function obtener_solicitudes($user_id)
    // {
    //     $this->db->select("g.id, g.id_retador, concat(u.name, ' ',u.last_name) as name_retador ,
    //     u.profile_photo as photo_retador,g.id_retado, concat(us.name,' ', us.last_name) as name_retado,
    //     us.profile_photo as photo_retado , g.fecha");
    //     $this->db->from("game_roulette_retos_1_vs_1 as g");
    //     $this->db->join("user as u", "u.id = g.id_retador");
    //     $this->db->join("user as us", "us.id = g.id_retado");
    //     $this->db->where("g.id_retado", $user_id);
    //     $this->db->or_where("g.id_retador", $user_id);
    //     $resultado = $this->db->get()->result_array();
    //     return $resultado;
    // }

    // public function aceptar_reto($id_reto, $aceptado)
    // {
    //     $this->db->set("aceptado", $aceptado);
    //     $this->db->where("id", $id_reto);
    //     return $this->db->update("game_roulette_retos_1_vs_1");
    // }

    // public function guardar_respuesta($data)
    // {
    //     $data["correct"] = $this->definirRespuestaCorrecta($data["answer_id"]);
    //     $this->db->insert("game_roulette_retos_1_vs_1_results", $data);
    //     if ($data["correct"] == true) {
    //         return "correct";
    //     } else {
    //         return "incorrect";
    //     }
    // }

    // private function definirRespuestaCorrecta($answer_id)
    // {
    //     $this->db->select("correct");
    //     $this->db->from("game_roulette_question_answers");
    //     $this->db->where("id", $answer_id);
    //     return $this->db->get()->result_array()[0]["correct"];
    // }

    public function crear_reto($data)
    {
        $this->db->insert("game_roulette_retos", $data);
        return $this->db->insert_id();
    }

    public function agregarOponentes($data, $id_reto)
    {
        for ($i = 0; $i < count($data); $i++) {
            $data_ = [];
            $data_["id_reto"] = $id_reto;
            $data_["id_usuario"] = $data[$i];
            $this->db->insert("game_roulette_retos_users", $data_);
        }

        return true;
    }

    public function obtener_reto($id_reto, $user_id)
    {
        $this->db->select("r.*, concat(u.name,' ',u.last_name) as retador");
        $this->db->from("game_roulette_retos as r");
        $this->db->join("user as u", "u.id = r.id_user");
        $this->db->where("r.id", $id_reto);
        $result = $this->db->get()->result_array();
        $this->db->select("concat(u.name, ' ',u.last_name) as nombre");
        $this->db->from("game_roulette_retos_users as ru");
        $this->db->join("user as u", "u.id = ru.id_usuario");
        $this->db->where("ru.id_reto", $id_reto);
        $result[0]["retados"] = $this->db->get()->result_array();
        $fecha = new DateTime($result[0]["fecha"]);
        $hoy = new DateTime(date("Y-m-d H:i:s"));
        $diff = $hoy->diff($fecha);
        $minutos = $diff->i > 9 ? $diff->i : ('0' . $diff->i);
        $horas = (24 - $diff->h + ($diff->days * 24));
        $horas = $horas > 0 ? $horas : 0;
        $minutos = $horas >= -1 ? $minutos : 0;
        unset($result[0]["fecha"]);
        $result[0]["restante"] =   $horas . ":" . $minutos . ' Hrs';
        $this->db->select("q.id,q.name,q.points");
        $this->db->from("game_roulette_retos_quiz as rq");
        $this->db->join("game_roulette_quiz as q", "q.id = rq.quiz_id");
        $this->db->where("rq.id_reto", $id_reto);
        $result[0]["quiz"] = $this->db->get()->result_array();
        unset($result[0]["quiz_id"]);
        unset($result[0]["correctas"]);
        $query = "select contestado from (
            select id_user,contestado from game_roulette_retos 
            where id = " . $id_reto . "
            union 
            select id_usuario as id_user, contestado from game_roulette_retos_users
            where id_reto = " . $id_reto . "
              ) as tabla where id_user  = " . $user_id;
        $contestado = $this->db->query($query)->result_array()[0]["contestado"];
        $result[0]["contestado"] = $contestado;
        if ($contestado == 1) {
            $result[0]["vigente"] = 0;
        }
        return $result;
    }

    public function obtener_retos_pendientes($id_usuario)
    {
        /*$this->db->select("r.id,r.nombre, concat(u.name, ' ',u.last_name) as usuario, 
        if(TIMESTAMPDIFF(DAY,r.fecha,NOW()) = 0,concat(24 - mod(TIMESTAMPDIFF(HOUR,r.fecha,NOW()),24),':', if(60 - mod(TIMESTAMPDIFF(MINUTE,r.fecha,NOW()),60) > 9,60 - mod(TIMESTAMPDIFF(MINUTE,r.fecha,NOW()),60),concat('0',60 - mod(TIMESTAMPDIFF(MINUTE,r.fecha,NOW()),60)))),'Tiempo agotado') AS restante,
        r.fecha,if(TIMESTAMPDIFF(DAY,r.fecha,NOW()) = 0,1,0) as vigente");
        $this->db->from("game_roulette_retos as r");
        $this->db->join("game_roulette_retos_users as ru", "ru.id_reto = r.id");
        $this->db->join("user as u", "(u.id = ru.id_usuario or u.id = r.id_user)");
        $this->db->where("u.id", $id_usuario);
        $this->db->order_by("restante","desc");
        $this->db->having("restante !=", "Tiempo agotado");*/
        $query = "
        SELECT `r`.`id`, `r`.`nombre`, (select concat(name, ' ',last_name) as name from user where id = r.id_user ) as usuario, 
        if(TIMESTAMPDIFF(DAY, `r`.`fecha`, NOW()) = 0, concat(24 - mod(TIMESTAMPDIFF(HOUR, `r`.`fecha`, NOW()), 24), ':',
        if(60 - mod(TIMESTAMPDIFF(MINUTE, `r`.`fecha`, NOW()), 60) > 9, 60 - mod(TIMESTAMPDIFF(MINUTE, `r`.`fecha`, NOW()), 60),
        concat('0', 60 - mod(TIMESTAMPDIFF(MINUTE, `r`.`fecha`, NOW()), 60)))), 'Tiempo agotado') AS restante, `r`.`fecha`,
        if(TIMESTAMPDIFF(DAY, `r`.`fecha`, NOW()) = 0, 1, 0) as vigente
        FROM `game_roulette_retos` as `r`
        JOIN `game_roulette_retos_users` as `ru` ON `ru`.`id_reto` = `r`.`id`
        JOIN `user` as `u` ON (`u`.`id` = `ru`.`id_usuario` or `u`.`id` = r.id_user)
        WHERE `u`.`id` = " . $id_usuario . "
        HAVING `restante` != 'Tiempo agotado'
        ORDER BY `restante` DESC
        ";
        $result =  $this->db->query($query)->result_array();
        // echo json_encode($this->db->last_query());
        for ($i = 0; $i < count($result); $i++) {
            $id_reto = $result[$i]["id"];
            $query = "select contestado from (
                select id_user,contestado from game_roulette_retos 
                where id = " . $id_reto . "
                union 
                select id_usuario as id_user, contestado from game_roulette_retos_users
                where id_reto = " . $id_reto . "
                  ) as tabla where id_user  = " . $id_usuario;
            $contestado = $this->db->query($query)->result_array()[0]["contestado"];
            if ($contestado == 1) {
                $result[$i]["restante"] = "Tiempo agotado";
                $result[$i]["vigente"] = 0;
            }
            // $result[0]["contestado"] = $contestado;
        }
        return $result;
    }

    public function guardar_respuesta_correcta($id_reto, $id_usuario, $correctas, $quiz_id)
    {
        $this->db->select("*");
        $this->db->from("game_roulette_retos_users");
        $this->db->where("id_reto", $id_reto);
        $this->db->where("id_usuario", $id_usuario);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            $this->db->set("correctas", $correctas);
            $this->db->set("quiz_id", $quiz_id);
            $this->db->set("contestado", 1);
            $this->db->where("id_reto", $id_reto);
            $this->db->where("id_usuario", $id_usuario);
            $this->db->update("game_roulette_retos_users");
        } else {
            $this->db->set("correctas", $correctas);
            $this->db->set("quiz_id", $quiz_id);
            $this->db->set("contestado", 1);
            $this->db->where("id", $id_reto);
            $this->db->where("id_user", $id_usuario);
            $this->db->update("game_roulette_retos");
        }
        $this->db->select('id_user');
        $this->db->from('game_roulette_retos');
        $this->db->where('id', $id_reto);
        return  $this->db->get()->result_array()[0]['id_user'];
    }

    public function obtener_resultados($id_reto)
    {
        $this->db->select("id,nombre");
        $this->db->from("game_roulette_retos");
        $this->db->where("id", $id_reto);
        $reto = $this->db->get()->result_array();
        $query = "select * from (SELECT 
        u.id,concat(u.name, ' ',u.last_name) as nombre, ru.correctas, ru.contestado
        FROM `game_roulette_retos` as `r`
        JOIN `game_roulette_retos_users` as `ru` ON `ru`.`id_reto` = `r`.`id`
        JOIN `user` as `u` ON `u`.`id` = `ru`.`id_usuario`
        WHERE `r`.`id` = " . $id_reto . "
        union 
        SELECT 
        u.id,concat(u.name, ' ',u.last_name) as nombre, r.correctas, r.contestado
        FROM `game_roulette_retos` as `r`
        JOIN `user` as `u` ON `u`.`id` = `r`.`id_user`
        WHERE `r`.`id` = " . $id_reto . ") as a
        order by correctas desc";
        $reto[0]["usuarios"] = $this->db->query($query)->result_array();
        $correctas = [];
        for ($i = 0; $i < count($reto[0]["usuarios"]); $i++) {
            $correctas[$i] = $reto[0]["usuarios"][0]["correctas"];
        }

        if (count($reto[0]["usuarios"]) == 2) {
            if ($reto[0]["usuarios"][0]["correctas"] > $reto[0]["usuarios"][1]["correctas"]) {
                $reto[0]["usuarios"][0]["mensaje"] = "Gana 3 puntos";
                if ($reto[0]["usuarios"][1]["contestado"] == 1)
                    $reto[0]["usuarios"][1]["mensaje"] = "Pierde 1 punto";
                else
                    $reto[0]["usuarios"][1]["mensaje"] = "Pendiente";
            } else if ($reto[0]["usuarios"][0]["correctas"] < $reto[0]["usuarios"][1]["correctas"]) {
                $reto[0]["usuarios"][1]["mensaje"] = "Gana 3 puntos";
                if ($reto[0]["usuarios"][0]["contestado"] == 1)
                    $reto[0]["usuarios"][0]["mensaje"] = "Pierde 1 punto";
                else
                    $reto[0]["usuarios"][0]["mensaje"] = "Pendiente";
            } else if ($reto[0]["usuarios"][0]["correctas"] == $reto[0]["usuarios"][1]["correctas"] && $reto[0]["usuarios"][0]["correctas"] > 0) {
                $reto[0]["usuarios"][0]["mensaje"] = "Gana 1 punto";
                $reto[0]["usuarios"][1]["mensaje"] = "Gana 1 punto";
            } else {
                if ($reto[0]["usuarios"][0]["contestado"] == 1)
                    $reto[0]["usuarios"][0]["mensaje"] = "Piede 1 punto";
                else
                    $reto[0]["usuarios"][0]["mensaje"] = "Pendiente";
                if ($reto[0]["usuarios"][1]["contestado"] == 1)
                    $reto[0]["usuarios"][1]["mensaje"] = "Piede 1 punto";
                else
                    $reto[0]["usuarios"][1]["mensaje"] = "Pendiente";
            }
        } else {
            for ($i = 0; $i < count($correctas); $i++) {
                for ($j = 0; $j < count($correctas) - $i - 1; $j++) {
                    $a = $correctas[$j];
                    $b = $correctas[$j + 1];
                    if ($a > $b) {
                        $correctas[$j] = $b;
                        $correctas[$j + 1] = $a;

                        $temp = $reto[0]["usuarios"];
                        $reto[0]["usuarios"][$j] = $reto[0]["usuarios"][$j + 1];
                        $reto[0]["usuarios"][$j + 1] = $temp;
                    }
                }
            }
            if ($correctas[0] > 0) {
                $reto[0]["usuarios"][0]["mensaje"] = "Gana 3 puntos";
                if ($correctas[0] > $correctas[1]) {
                    if ($reto[0]["usuarios"][1]["contestado"])
                        $reto[0]["usuarios"][1]["mensaje"] = "Gana 1 punto";
                    else
                        $reto[0]["usuarios"][1]["mensaje"] = "Pendiente";
                    if ($correctas[1] > $correctas[2]) {
                        if ($reto[0]["usuarios"][2]["contestado"] == 1)
                            $reto[0]["usuarios"][2]["mensaje"] = "Pierde 1 punto";
                        else
                            $reto[0]["usuarios"][2]["mensaje"] = "Pendiente";
                        if (count($correctas) == 4 && $correctas[2] > $correctas[3]) {
                            if ($reto[0]["usuarios"][3]["contestado"] == 1)
                                $reto[0]["usuarios"][3]["mensaje"] = "Pierde 3 puntos";
                            else
                                $reto[0]["usuarios"][3]["mensaje"] = "Pendiente";
                        } else {
                            if ($reto[0]["usuarios"][3]["contestado"] == 1)
                                $reto[0]["usuarios"][3]["mensaje"] = "Pierde 1 punto";
                            else
                                $reto[0]["usuarios"][3]["mensaje"] = "Pendiente";
                        }
                    } else {
                        if ($reto[0]["usuarios"][2]["contestado"] == 1)
                            $reto[0]["usuarios"][2]["mensaje"] = "Gana 1 punto";
                        else
                            $reto[0]["usuarios"][2]["mensaje"] = "Pendiente";
                        if (count($correctas) == 4 && $correctas[2] > $correctas[3]) {
                            if ($reto[0]["usuarios"][2]["contestado"] == 1)
                                $reto[0]["usuarios"][2]["mensaje"] = "Pierde 1 punto";
                            else
                                $reto[0]["usuarios"][2]["mensaje"] = "Pendiente";
                        } else {
                            if ($reto[0]["usuarios"][2]["contestado"] == 1)
                                $reto[0]["usuarios"][2]["mensaje"] = "Gana 1 punto";
                            else
                                $reto[0]["usuarios"][2]["mensaje"] = "Pendiente";
                        }
                    }
                } else {
                    if ($reto[0]["usuarios"][1]["contestado"] == 1)
                        $reto[0]["usuarios"][1]["mensaje"] = "Gana 3 puntos";
                    else
                        $reto[0]["usuarios"][1]["mensaje"] = "Pendiente";
                    if ($correctas[1] > $correctas[2]) {
                        if ($reto[0]["usuarios"][2]["contestado"] == 1)
                            $reto[0]["usuarios"][2]["mensaje"] = "Gana 1 punto";
                        else
                            $reto[0]["usuarios"][2]["mensaje"] = "Pendiente";
                        if (count($correctas) == 4 && $correctas[2] > $correctas[3]) {
                            if ($reto[0]["usuarios"][3]["contestado"] == 1)
                                $reto[0]["usuarios"][3]["mensaje"] = "Pierde 1 punto";
                            else
                                $reto[0]["usuarios"][3]["mensaje"] = "Pendiente";
                        } else {
                            if ($reto[0]["usuarios"][3]["contestado"] == 1)
                                $reto[0]["usuarios"][3]["mensaje"] = "Gana 1 punto";
                            else
                                $reto[0]["usuarios"][3]["mensaje"] = "Pendiente";
                        }
                    } else {
                        if ($reto[0]["usuarios"][2]["contestado"] == 1)
                            $reto[0]["usuarios"][2]["mensaje"] = "Gana 3 puntos";
                        else
                            $reto[0]["usuarios"][2]["mensaje"] = "Pendiente";
                        if (count($correctas) == 4 && $correctas[2] > $correctas[3]) {
                            if ($reto[0]["usuarios"][3]["contestado"] == 1)
                                $reto[0]["usuarios"][3]["mensaje"] = "Gana 1 punto";
                            else
                                $reto[0]["usuarios"][3]["mensaje"] = "Pendiente";
                        } else {
                            if ($reto[0]["usuarios"][3]["contestado"] == 1)
                                $reto[0]["usuarios"][3]["mensaje"] = "Gana 3 puntos";
                            else
                                $reto[0]["usuarios"][3]["mensaje"] = "Pendiente";
                        }
                    }
                }
            } else {
                $reto[0]["usuarios"][0]["mensaje"] = "Pendiente";
                if (count($correctas) > 1)
                    $reto[0]["usuarios"][1]["mensaje"] = "Pendiente";
                if (count($correctas) > 2)
                    $reto[0]["usuarios"][2]["mensaje"] = "Pendiente";
                if (count($correctas) == 4) {
                    $reto[0]["usuarios"][3]["mensaje"] = "Pendiente";
                }
            }
        }
        $query = "select count(*) as numero from game_roulette_retos_users
        where id_reto = " . $id_reto;
        $num_usuarios = $this->db->query($query)->result_array()[0]["numero"];
        if ($num_usuarios == 2) {
            unset($reto[0]["usuarios"][3]);
        }
        return $reto;
    }

    public function agregar_puntos()
    {
        $this->db->select("id");
        $this->db->from("game_roulette_retos");
        $this->db->where("puntos_registrados", 0);
        $this->db->where("TIMESTAMPDIFF(DAY,fecha,now()) >", 0);
        $result = $this->db->get()->result_array();
        for ($i = 0; $i < count($result); $i++) {
            $result[$i]["resultados"] = $this->obtener_resultados($result[$i]["id"])[0];

            for ($j = 0; $j < count($result[$i]["resultados"]["usuarios"]); $j++) {
                $id = $result[$i]["resultados"]["usuarios"][$j]["id"];
                $puntos = 0;
                switch ($result[$i]["resultados"]["usuarios"][$j]["mensaje"]) {
                    case "Gana 3 puntos":
                        $puntos = 3;
                        break;
                    case "Gana 1 punto":
                        $puntos = 1;
                        break;
                    case "Pierde 1 punto":
                        $puntos = -1;
                        break;
                    case "Pierde 3 puntos":
                        $puntos = -3;
                        break;
                    case "Pendiente":
                        $puntos = -1;

                        break;
                }
                $this->db->set("puntos_registrados", 1);
                $this->db->where("id", $result[$i]["id"]);
                $this->db->update("game_roulette_retos");
                $this->general_mdl->ModificarScoreUsuario($id, $puntos);
            }
        }
        // echo json_encode($result);
    }

    public function registrar_quiz($id_reto, $quiz)
    {
        for ($i = 0; $i < count($quiz); $i++) {
            $data = [];
            $data["id_reto"] = $id_reto;
            $data["quiz_id"] = $quiz[$i]["id"];
            $this->db->insert("game_roulette_retos_quiz", $data);
        }
        return true;
    }

    public function obtener_retos($tipo, $id_usuario)
    {
        if ($tipo == 0) {
            $this->db->select("id, nombre");
            $this->db->from("game_roulette_retos");
            $this->db->where("id_user", $id_usuario);
            return $this->db->get()->result_array();
        } else {
            $this->db->select("r.id, r.nombre");
            $this->db->from("game_roulette_retos as r");
            $this->db->join("game_roulette_retos_users as ru", "ru.id_reto = r.id");
            $this->db->where("ru.id", $id_usuario);
            return $this->db->get()->result_array();
        }
    }
}
