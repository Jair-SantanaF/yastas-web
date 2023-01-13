<?php
class Retos_mdl extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function crearReto($data)
    {
        $this->db->insert("game_retos", $data);
        return $this->db->insert_id();
    }

    public function retar($data)
    {
        $ids = [];
        for ($i = 0; $i < count($data["retados"]); $i++) {
            $data_ = [];
            $data_["id_retador"] = $data["id_retador"];
            $data_["id_retado"] = $data["retados"][$i];
            $data_["id_reto"] = $data["id_reto"];
            $data_["fecha_limite"] = $data["fecha_limite"];
            $this->db->insert("game_retos_users", $data_);
            array_push($ids, $this->db->insert_id());
        }
        return $ids;
    }

    public function GuardarImagenReto($id_reto, $imagen)
    {
        $data = [];
        $data["id_reto"] = $id_reto;
        $data["imagen"] = $imagen;
        return $this->db->insert("game_retos_imagenes", $data);
    }

    public function GuardarImagenReporte($id_reporte, $imagen, $video)
    {
        $data = [];
        $data["id_reporte"] = $id_reporte;
        $data["imagen"] = $imagen;
        $data["video"] = $video;
        return $this->db->insert("game_retos_reporte_imagenes", $data);
    }

    public function obtener_retos_lanzados($id_usuario)
    {
        $query = "
        select gr.*, fru.fecha
        from game_retos as gr
        join game_retos_users as gru on gru.id_reto = gr.id
        join user as u on u.id = gru.id_retador
        where gru.id_retador = $id_usuario
        group by gr.id
        ";
        $result = $this->db->query($query)->result_array();
        for ($i = 0; $i < count($result); $i++) {
            //$result[$i]["puntos"] = $this->obtener_puntos($result[$i]["id"], $id_usuario);
            $result[$i]["usuarios"] = $this->obtener_usuarios($result[$i]["id"], $result[$i]["fecha"]);
        }
        return $result;
    }

    public function obtener_realizados_cant($id_usuario)
    {
        $query = "
        select count(DISTINCT grr.id_reto_lanzado) as realizados
        from game_retos as gr
        join game_retos_users as gru on gru.id_reto = gr.id
        join game_retos_questions as grq on grq.id_reto = gr.id
        join game_retos_library as grl on grl.id_reto = gr.id
        join library_elements_ as le on le.id = grl.id_library
        left join game_retos_results as grr on grr.question_id = grq.id and gru.id = grr.id_reto_lanzado
        where gru.id_retado = $id_usuario
        group by gru.id_retado
       ";
        return $this->db->query($query)->result_array()[0]["realizados"];
    }

    public function obtener_pendientes_cant($id_usuario)
    {
        $query = "
        select
        count(DISTINCT gru.id) - count(distinct grr.id_reto_lanzado) as pendientes
        from game_retos as gr
        join game_retos_users as gru on gru.id_reto = gr.id
        join game_retos_questions as grq on grq.id_reto = gr.id
        join game_retos_library as grl on grl.id_reto = gr.id
        join library_elements_ as le on le.id = grl.id_library
        left join game_retos_results as grr on grr.question_id = grq.id and gru.id = grr.id_reto_lanzado
        where gru.id_retado = $id_usuario
        group by gru.id_retado
        ";
        return $this->db->query($query)->result_array()[0]["pendientes"];
    }

    public function obtenerRetos($id_usuario)
    {
        $query = "
        select gr.*, gru.fecha_limite, if(grr.id is null,0,1) as realizado, gru.id as id_reto_lanzado, gru.fecha, date_format(gru.fecha,'%Y-%m-%d') as fecha_formateada, le.title as nombre_archivo
        from game_retos as gr
        join game_retos_users as gru on gru.id_reto = gr.id
        join game_retos_questions as grq on grq.id_reto = gr.id
        join game_retos_library as grl on grl.id_reto = gr.id
        join library_elements_ as le on le.id = grl.id_library
        left join game_retos_results as grr on grr.question_id = grq.id and gru.id = grr.id_reto_lanzado
        where gru.id_retado = $id_usuario
        group by gr.id, gru.fecha
        order by realizado asc, gru.id desc
       ";
        $result = $this->db->query($query)->result_array();
        for ($i = 0; $i < count($result); $i++) {
            $fecha = date("Y-m-d");
            if ($fecha >= $result[$i]["fecha_limite"]) {
                $result[$i]["realizado"] = 1;
            }
            $result[$i]["puntos"] = $this->obtener_puntos($result[$i]["id"], $id_usuario, $result[$i]["fecha"]);
            $result[$i]["usuarios"] = $this->obtener_usuarios($result[$i]["id"], $result[$i]["fecha"]);
            $result[$i]["retador"] = $this->obtener_retador($result[$i]["id"], $result[$i]["fecha"]);
            $result[$i]["fecha"] = $result[$i]["fecha_formateada"];

            $result[$i]["questions"] = $this->obtener_preguntas($result[$i]["id"]);

            unset($result[$i]["fecha_formateada"]);
        }
        return $result;
    }

    function obtener_preguntas($id)
    {
        $query = "select * from game_retos_questions as gr 
                        where gr.id_reto = " . $id;
        $preguntas = $this->db->query($query)->result_array();
        for ($i = 0; $i < count($preguntas); $i++) {
            $query = "select * from game_retos_question_answers as gr
                        where gr.question_id = " . $preguntas[$i]["id"];
            $preguntas[$i]["answers"] = $this->db->query($query)->result_array();
        }
        return $preguntas;
    }

    public function ObtenerRetosBiblioteca($business_id)
    {
        $query = "
            select 
                gr.id,
                gr.nombre,
                gr.objetivo,	
                gr.descripcion,
                gr.puntos,
                count(grq.question) preguntas
            from game_retos gr
                join game_retos_questions grq on grq.id_reto = gr.id
                left join user u on u.id = gr.user_id
            where u.es_prueba = 0
                and u.business_id = $business_id
            group by 
                    gr.id
       ";

        $result = $this->db->query($query)->result_array();
        return $result;
    }

    function obtener_retador($id_reto, $fecha)
    {
        $query = "select concat(u.name, ' ', u.last_name) as name from game_retos_users as gru
        join user as u on u.id = gru.id_retador
        where id_reto = $id_reto and fecha = '$fecha'";
        return $this->db->query($query)->result_array()[0]["name"];
    }

    function obtener_puntos($id_reto, $user_id, $fecha_)
    {
        $query = "
        select count(distinct gru.id) as retados, count(distinct grr.id_reto_lanzado) as realizado, date_format(gru.fecha_limite,'%Y-%m-%d') as fecha_limite
        from game_retos as gr
        join game_retos_users as gru on gru.id_reto = gr.id
        join game_retos_questions as grq on grq.id_reto = gr.id
        left join game_retos_results as grr on grr.question_id = grq.id and grr.user_id = gru.id_retado and gru.id = grr.id_reto_lanzado
        where gr.id = $id_reto and gru.fecha = '$fecha_'
        group by gr.id
        ";
        $result = $this->db->query($query)->result_array()[0];
        $fecha = date("Y-m-d");
        if ($result["retados"] != $result["realizado"] && $fecha <= $result["fecha_limite"]) {
            return -1000;
        }
        $query = "
        select gru.id_retado as user_id, coalesce(sum(grr.correct),0) as correct, count(*) as total
        from game_retos as gr
        join game_retos_users as gru on gru.id_reto = gr.id
        join game_retos_questions as grq on grq.id_reto = gr.id
        left join game_retos_results as grr on grr.question_id = grq.id and grr.user_id = gru.id_retado and gru.id = grr.id_reto_lanzado
        where gr.id = $id_reto and gru.fecha = '$fecha_'
        group by gru.id_retado
        order by grr.correct desc, grr.created_at asc";
        $result = $this->db->query($query)->result_array();

        if (count($result) == 1) {
            if ($result[0]["correct"] == $result[0]["total"] && $result[0]["user_id"] == $user_id) {
                return 3;
            } else if ($result[0]["correct"] > 0 && $result[0]["user_id"] == $user_id) {
                return 1;
            } else {
                return -1;
            }
        }
        if ((count($result) == 2 || count($result) == 3) && count($result) < 4) {
            if ($result[0]["correct"] == $result[0]["total"] && $result[0]["user_id"] == $user_id) {
                return 3;
            } else if ($result[0]["correct"] > 0 && $result[0]["user_id"] == $user_id) {
                return 1;
            } else if ($result[1]["correct"] == $result[1]["total"] && $result[1]["user_id"] == $user_id) {
                return 1;
            } else if ($result[1]["correct"] > 0 && $result[1]["user_id"] == $user_id) {
                return 0;
            } else {
                return -1;
            }
        }
        if(count($result) == 4){
            return -2;
        }
    }

    function obtener_usuarios($id_reto, $fecha)
    {
        $query = "
        select count(distinct gru.id) as retados, count(distinct grr.id_reto_lanzado) as realizado, date_format(gru.fecha_limite,'%Y-%m-%d') as fecha_limite
        from game_retos as gr
        join game_retos_users as gru on gru.id_reto = gr.id
        join game_retos_questions as grq on grq.id_reto = gr.id
        left join game_retos_results as grr on grr.question_id = grq.id and grr.user_id = gru.id_retado and gru.id = grr.id_reto_lanzado
        where gr.id = $id_reto and gru.fecha = '$fecha'
        group by gr.id
        ";
        $result1 = $this->db->query($query)->result_array()[0];

        $query = "
        select coalesce(sum(grr.correct),0) as correct, count(*) as total, concat(u.name,' ', u.last_name) as name, u.profile_photo
        from game_retos as gr
        join game_retos_users as gru on gru.id_reto = gr.id
        join game_retos_questions as grq on grq.id_reto = gr.id
        left join game_retos_results as grr on grr.question_id = grq.id and grr.user_id = gru.id_retado and gru.id = grr.id_reto_lanzado
        join user as u on u.id = gru.id_retado
        where gr.id = $id_reto and gru.fecha = '$fecha'
        group by gru.id_retado
        order by grr.correct desc, grr.created_at asc";
        $result = $this->db->query($query)->result_array();

        $fecha = date("Y-m-d");

        if ($result1["retados"] != $result1["realizado"] && $fecha <= $result1["fecha_limite"]) {
            for ($i = 0; $i < count($result); $i++) {
                $result[$i]["puntos"] = -1000;
            }
            return $result;
        }

        if (count($result) == 1) {
            $result[0]["puntos"] = $this->definir_puntos($result, 0);
        }
        if (count($result) >= 2) {
            $result[0]["puntos"] = $this->definir_puntos($result,0);
            $result[1]["puntos"] = $this->definir_puntos($result,1);
        }
        if (count($result) >= 3) {
            $result[2]["puntos"] = -1;
        }
        if(count($result) == 4){
            $result[2]["puntos"] = -2;
        }
        return $result;
    }

    public function obtenerImagenes($id_reto, $business_id)
    {
        $this->db->select("concat('" . base_url() . "/uploads/business_" . $business_id . "/games/retos/',imagen) as imagen");
        $this->db->from("game_retos_imagenes");
        $this->db->where("id_reto", $id_reto);
        return $this->db->get()->result_array();
    }

    public function obtenerImagenesReporte($id_reporte, $business_id)
    {
        $this->db->select(
            "if(imagen != '',concat('" . base_url() . "/uploads/business_" . $business_id . "/games/retos/',imagen),'') as imagen,
             if(video != '',concat('" . base_url() . "/uploads/business_" . $business_id . "/games/retos/', video),'') as video"
        );
        $this->db->from("game_retos_reporte_imagenes");
        $this->db->where("id_reporte", $id_reporte);
        return $this->db->get()->result_array();
    }

    public function obtenerRetosCalificar($id_usuario, $tipo, $business_id)
    {
        $this->db->select("gr.id, gr.user_id,gru.id_retado, gr.nombre, gr.objetivo, gr.descripcion, 
         gr.aprobado, gr.tipo,gru.conclusion, gru.id as id_reporte, concat(u.name, ' ',u.last_name) as retado");
        $this->db->from("game_retos as gr");
        $this->db->join("game_retos_users as gru", "gru.id_reto = gr.id");
        $this->db->join("user as u", "u.id = gru.id_retado");
        // $this->db->where("tipo", $tipo);
        // $this->db->where("gr.user_id", $id_usuario);
        $this->db->where("gru.conclusion is not null", NULL, "false");
        // $this->db->where("gru.conclusion is not null", null,"false");
        $this->db->where("gru.desempeno", NULL);
        $this->db->where("gru.actitud", NULL);
        $result = $this->db->get()->result_array();
        for ($i = 0; $i < count($result); $i++) {
            $result[$i]["imagenes"] = $this->obtenerImagenes($result[$i]["id"], $business_id);
            $result[$i]["imagenes_reporte"] = $this->obtenerImagenesReporte($result[$i]["id_reporte"], $business_id);
        }
        return $result;
    }

    public function obtenerRetosRealizados($id_usuario, $tipo, $business_id)
    {
        $this->db->select("gr.id, gr.user_id, gr.nombre, gr.objetivo, gr.descripcion, 
                            gr.aprobado, gr.tipo, gru.id as id_reporte");
        $this->db->from("game_retos as gr");
        $this->db->join("game_retos_users as gru", "gru.id_reto = gr.id");
        $this->db->where("tipo", $tipo);
        $this->db->where("gru.id_retado", $id_usuario);
        $this->db->where("gru.conclusion is not NULL");
        $result = $this->db->get()->result_array();
        for ($i = 0; $i < count($result); $i++) {
            $result[$i]["imagenes"] = $this->obtenerImagenes($result[$i]["id"], $business_id);
            $result[$i]["imagenes_reporte"] = $this->obtenerImagenesReporte($result[$i]["id_reporte"], $business_id);
            $result[$i]["calificaciones"] = $this->obtenerCalificaciones($result[$i]["id"], $id_usuario);
        }
        return $result;
    }

    public function obtenerCalificaciones($id_reto, $id_usuario)
    {
        $this->db->select("coalesce(desempeno,0) as desempeno, coalesce(actitud,0) as actitud, coalesce(desempeno2,0) as desempeno2, coalesce(actitud2,0) as actitud2, coalesce(desempeno3,0) as desempeno3, coalesce(actitud3,0) as actitud3, coalesce(desempeno4,0) as desempeno4, coalesce(actitud4,0) as actitud4");
        $this->db->from("game_retos_users");
        $this->db->where("id_reto", $id_reto);
        $this->db->where("id_retado", $id_usuario);
        return $this->db->get()->result_array();
    }

    public function obtenerReto($id_reto, $business_id, $user_id)
    {
        $query = "select gr.*, gru.fecha_limite, if(grr.id is null,0,1) as realizado, gru.id as id_reto_lanzado, gru.fecha, date_format(gru.fecha,'%Y-%m-%d') as fecha_formateada, le.title as nombre_archivo
        from game_retos as gr
        join game_retos_users as gru on gru.id_reto = gr.id
        join game_retos_questions as grq on grq.id_reto = gr.id
        join game_retos_library as grl on grl.id_reto = gr.id
        join library_elements_ as le on le.id = grl.id_library
        left join game_retos_results as grr on grr.question_id = grq.id and gru.id = grr.id_reto_lanzado
        where gru.id_retado = $user_id and gru.id = $id_reto
        group by gr.id, gru.fecha";
        $result = $this->db->query($query)->result_array();
        for ($i = 0; $i < count($result); $i++) {
            $result[$i]["puntos"] = $this->obtener_puntos($result[$i]["id"], $user_id, $result[$i]["fecha"]);
            $result[$i]["usuarios"] = $this->obtener_usuarios($result[$i]["id"], $result[$i]["fecha"]);
            $result[$i]["fecha"] = $result[$i]["fecha_formateada"];
            $result[$i]["questions"] = $this->obtener_preguntas($result[$i]["id"]);
            unset($result[$i]["fecha_formateada"]);
        }
        return $result[0];
    }

    public function agregarReporte($id_reto, $conclusion, $imagen, $id_usuario)
    {
        $this->db->set("conclusion", $conclusion);
        $this->db->set("imagen", $imagen);
        $this->db->where("id_reto", $id_reto);
        $this->db->where("id_retado", $id_usuario);
        $this->db->update("game_retos_users");

        $this->db->select("id");
        $this->db->from("game_retos_users");
        $this->db->where("id_reto", $id_reto);
        $this->db->where("id_retado", $id_usuario);
        return ['id_reporte' => $this->db->get()->result_array()[0]["id"]];
    }

    public function calificarReto($id_reto, $desempeno, $actitud, $desempeno2, $actitud2, $desempeno3, $actitud3, $desempeno4, $actitud4, $id_usuario)
    {
        $this->db->set("actitud", $actitud);
        $this->db->set("desempeno", $desempeno);
        $this->db->set("actitud2", $actitud2);
        $this->db->set("desempeno2", $desempeno2);
        $this->db->set("actitud3", $actitud3);
        $this->db->set("desempeno3", $desempeno3);
        $this->db->set("actitud4", $actitud4);
        $this->db->set("desempeno4", $desempeno4);
        $this->db->where("id_retado", $id_usuario);
        $this->db->where("id_reto", $id_reto);
        return $this->db->update("game_retos_users");
    }

    public function obtenerRetosAdmin($business_id)
    {
        $this->db->select("gr.id, gr.user_id, gr.nombre, gr.objetivo, gr.descripcion, 
                            gr.aprobado, gr.tipo, concat(u.name,' ',u.last_name) as usuario");
        $this->db->from("game_retos as gr");
        $this->db->join("game_retos_users as gru", "gru.id_reto = gr.id", "left");
        $this->db->join("user as u", "u.id = gr.user_id");
        $this->db->where("u.business_id", $business_id);
        $this->db->where("gru.conclusion", NULL);
        $this->db->where("gru.imagen", NULL);
        $this->db->group_by("gr.id");
        $result = $this->db->get()->result_array();
        for ($i = 0; $i < count($result); $i++) {
            $result[$i]["imagenes"] = $this->obtenerImagenes($result[$i]["id"], $business_id);
        }
        return $result;
    }

    function eliminarReto($id_reto)
    {
        $this->db->where("id", $id_reto);
        $this->db->delete("game_retos");
        $this->db->where("id_reto", $id_reto);
        $this->db->delete("game_retos_imagenes");
        $this->db->where("id_reto", $id_reto);
        $this->db->delete("game_retos_users");
        return true;
    }

    function actualizarReto($data)
    {
        $this->db->set("nombre", $data["nombre"]);
        $this->db->set("objetivo", $data["objetivo"]);
        $this->db->set("descripcion", $data["descripcion"]);
        $this->db->where("id", $data["id"]);
        return $this->db->update("game_retos");
    }

    function SaveAnswer($data, $tipo = 0)
    {
        unset($data['token']);
        unset($data["id_reto"]);
        $this->db->select('gra.correct, grq.type_id');
        $this->db->from("game_retos_questions " . ' as grq');
        $this->db->join("game_retos_question_answers" . ' as gra', 'gra.question_id = grq.id', "left");
        // $this->db->join($this->tableRouletteQuiz . ' as grz', 'grz.id = grq.quiz_id');
        $this->db->where('grq.id =', $data['question_id']);
        $this->db->where('gra.id =', $data['answer_id']);
        $validate_answer = $this->db->get()->result_array();
        if (count($validate_answer) > 0)
            $data["correct"] = $validate_answer[0]['correct'];
        else
            $data["correct"] = 0;
        if ($tipo == 11)
            $data["correct"] = 1;
        if ($this->db->insert("game_retos_results", $data)) {

            $this->db->select('score');
            $score = $this->db->get_where('user', array('id' => $data['user_id']))->result_array();
            $score = intval($score[0]['score']);

            $this->db->select('gr.puntos');
            $this->db->from("game_retos_questions " . ' as grq');
            $this->db->join("game_retos" . ' as gr', 'gr.id = grq.id_reto');
            $this->db->where('grq.id =', $data['question_id']);
            $validate_answer = $this->db->get()->result_array();

            if (count($validate_answer) > 0) {
                // if ($data["correct"] == 1) {
                //     $score = $score + intval($validate_answer[0]['puntos']);
                // } else {
                //     $score = $score - intval($validate_answer[0]['puntos']);
                //     if ($score < 0) {
                //         $score = 0;
                //     }
                // }
                if ($data["correct"] == 1) {
                    $text = 'correct';
                } else {
                    $text = strip_tags(html_entity_decode($this->obtener_respuesta_correcta($data)));
                }
            }

            return $text;
        } else {
            return false;
        }
    }

    function obtener_respuesta_correcta($data)
    {
        $this->db->select('gra.answer,grz.puntos,gra.correct');
        $this->db->from("game_retos_questions " . ' as grq');
        $this->db->join("game_retos_question_answers" . ' as gra', 'gra.question_id = grq.id');
        $this->db->join("game_retos" . ' as grz', 'grz.id = grq.id_reto');
        $this->db->where('grq.id =', $data['question_id']);

        $this->db->where('gra.correct', 1);
        return $this->db->get()->result_array()[0]["answer"];
    }

    public function definir_puntos($result, $lugar)
    {
        $puntos = 0;
        if ($result[$lugar]["correct"] == $result[$lugar]["total"]) {
            if ($lugar == 0)
                $puntos = 3;
            else
                $puntos = 1;
        } else if ($result[$lugar]["correct"] > 0) {
            if ($lugar == 0)
                $puntos = 1;
            else
                $puntos = 0;
        } else if ($result[$lugar]["correct"] == 0) {
            $puntos = -1;
        }
        return $puntos;
    }

    public function agregar_puntos($id_reto, $fecha_reto)
    {
        $fecha = date("Y-m-d");
        $query = "
        select coalesce(sum(grr.correct),0) as correct, count(*) as total, u.id
        from game_retos as gr
        join game_retos_users as gru on gru.id_reto = gr.id
        join game_retos_questions as grq on grq.id_reto = gr.id
        left join game_retos_results as grr on grr.question_id = grq.id and grr.user_id = gru.id_retado and gru.id = grr.id_reto_lanzado
        join user as u on u.id = gru.id_retado
        where gr.id = $id_reto and date_format(gru.fecha_limite,'%Y-%m-%d') = '$fecha' and gru.fecha = '$fecha_reto'
        group by gru.id_retado
        order by grr.correct desc, grr.created_at asc";
        $result = $this->db->query($query)->result_array();
        if (count($result) == 1) {
            $puntos = $this->definir_puntos($result, 0);
            $this->general_mdl->ModificarScoreUsuario($result[0]["id"], $puntos);
        }
        if (count($result) >= 2) {
            $puntos = $this->definir_puntos($result, 0);
            $this->general_mdl->ModificarScoreUsuario($result[0]["id"], $puntos);
            $puntos = $this->definir_puntos($result, 1);
            $this->general_mdl->ModificarScoreUsuario($result[1]["id"], $puntos);
        }
        if (count($result) == 3) {
            $puntos = -1;
            $this->general_mdl->ModificarScoreUsuario($result[2]["id"], $puntos);
        }
    }

    function omitir_realizados($id_reto)
    {
        $fecha = date("Y-m-d");
        $fecha = date("Y-m-d", strtotime($fecha . " + 1 days"));
        $query = "select gru.id_retado from game_retos_users as gru 
        left join game_retos_results as grr on grr.user_id = gru.id_retado and grr.id_reto_lanzado = gru.id
        where grr.id is null and gru.id_reto = $id_reto and date_format(gru.fecha_limite,'%Y-%m-%d') = $fecha";
        $result = $this->db->query($query)->result_array();
        $users = [];
        for ($i = 0; $i < count($result); $i++) {
            array_push($users, $result[$i]["id_retado"]);
        }
        return $users;
    }

    function SaveQuestion($data)
    {
        if (isset($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            return $this->db->update($this->questions, $data, array('id' => $id));
        } else {
            $insert = $this->db->insert($this->questions, $data);
            $question_id = $this->db->insert_id();
            return $insert;
        }
    }
}
