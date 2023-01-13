<?php

require_once APPPATH . 'libraries/SimpleXLSXGen.php';

class Reporte_resultados_mdl extends CI_Model
{

    private $tableUser = "user",
        $tableGameProductsResult = "game_products_results",
        $tableGameProductsQuiz = "game_products_quiz",
        $tableGameRouletteResults = "game_roulette_results",
        $tableGameRouletteQuiz = "game_roulette_quiz",
        $tableGameRouletteQuestions = "game_roulette_questions",
        $tablaGameSnakeStairsMembers = "game_snake_stairs_members",
        $tableGameSnakeStairsActiveGame = "game_snake_stairs_active_game",
        $tableGameProfiler = "profiler_quiz",
        $tableGameProfilerResult = "profiler_results",
        $tableGameProfilerQuestion = "profiler_question",
        $tableComMessages = "com_messages",
        $tableComTopics = "com_topics",
        $tableComUserTopics = "com_users_topics",
        $tableWall = "wall",
        $tableWallLike = "wall_post_like",
        $tableWallComments = "wall_comments",
        $tablePodcastUsage = "podcast_usage",
        $tablePodcast = "podcast",
        $tablePodcastComments = "podcast_comments",
        $tablePodcastScore = "podcast_score",
        $tableFeedback = "feedback",
        $question_quiz = "question_quiz",
        $question_answer_user = "question_answer_users",
        $question_categories = "question_categories",
        $questions = "questions",
        $tableLibraryUsage = "library_usage",
        $tableUsersGroups = "users_groups",
        $tableLibraryElements = "library_elements_",
        $tableGroups = "groups",
        $tableLibraryGroups = "library_groups",
        $tableTopics = "com_topics",
        $tableQuestionQuiz = "question_quiz",
        $tableQuestionsCategories = "question_categories";

    public function __construct()
    {
        parent::__construct();
    }

    public function validacion_roles()
    {
        // if ($this->session->userdata("rol_id") == 6) {
        //     $id_asesor = $this->session->userdata("id_user");
        //     $this->db->where("u.id_asesor", $id_asesor);
        // }
        // if ($this->session->userdata("rol_id") == 5) {
        //     $region = $this->session->userdata("id_region");
        //     $this->db->where("u.id_region", $region);
        // }
        if ($this->session->userdata("rol_id") == 8)
            $this->db->where("u.es_prueba", 2);
        else
            $this->db->where("u.es_prueba", 0);
    }

    public function validacion_roles_string()
    {
        $query = "";
        // if ($this->session->userdata("rol_id") == 6) {
        //     $id_asesor = $this->session->userdata("id_user");
        //     $query .= " and u.id_asesor = " . $id_asesor;
        // }
        // if ($this->session->userdata("rol_id") == 5) {
        //     $region = $this->session->userdata("id_region");
        //     $query .= " and u.id_region = " . $region . " ";
        // }
        if ($this->session->userdata("rol_id") == 8)
            $query = " and u.es_prueba  = 2 ";
        else
            $query = " and u.es_prueba  = 0 ";
        return $query;
    }

    public function validacion_usuario_capacitacion($tipo = null)
    {
        if($tipo != null){
            return " and cl.tipo = $tipo ";
        }
        $query = "";
        //el rol 7 es para el usuario que solo ve en el admin el reporte de capacitacion obligatoria
        //asi esta definido en qa
        //el id del rol puede cambiar dependiendo de la base de datos
        if ($this->session->userdata("rol_id") == 7) {
            $query = " and cl.tipo = 1 ";
        } else {
            $query = " and cl.tipo = 0";
        }
        return $query;
    }

    public function validacion_limite_string($bandera)
    {
        if ($bandera) {
            return " limit 20";
        }
        return "";
    }

    public function validacion_limite($bandera)
    {
        if ($bandera) {
            $this->db->limit(20);
        }
    }

    public function ObtenerUsuariosPorFecha($data)
    {
        $this->db->select('u.id,concat(u.name," ", u.last_name) as name ');
        $this->db->from($this->tableUser . ' as u');
        // if ($data["fecha_inicio"] !== null && $data["fecha_fin"]) {
        //     $this->db->where('u.created_at between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        // }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->where('u.active', 1);
        $this->validacion_roles();
        $this->db->where('u.business_id', $data["business_id"]);
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerUsuariosPorFechaGrupo($data)
    {
        $this->db->select('u.id,concat(u.name," ", u.last_name) as name ');
        $this->db->from($this->tableUser . ' as u');
        if ($data["group_id"] !== null) {
            $this->db->join($this->tableUsersGroups . ' as ug', 'ug.user_id = u.id');
        }
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"]) {
            $this->db->where('u.created_at between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        $this->db->where('u.active', 1);
        $this->validacion_roles();
        $this->db->where('u.business_id', $data["business_id"]);
        if ($data["group_id"] !== null) {
            $this->db->where('ug.group_id', $data["group_id"]);
        }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerBibliotecaPorFecha($data)
    {
        $this->db->select('l.id,l.title');
        $this->db->from($this->tableLibraryElements . ' as l');
        $this->db->join($this->tableLibraryGroups . ' as lg', 'lg.library_id = l.id');
        $this->db->join($this->tableGroups . ' as g', 'g.id = lg.group_id');
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"]) {
            $this->db->where('l.date between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        $this->db->where('l.active', 1);
        // $this->validacion_roles();
        if ($data["group_id"] !== null) {
            $this->db->where('lg.group_id', $data["group_id"]);
        }
        $this->db->where('l.business_id', $data["business_id"]);
        $this->db->where('g.business_id', $data["business_id"]);
        $this->db->where('l.capacitacion_obligatoria', 0);
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerJuegos($data)
    {
        $this->db->select("g.*");
        $this->db->from("games as g");
        $this->db->join("services_games as sg", "sg.game_id = g.id");
        $this->db->where("sg.business_id", $data["business_id"]);
        $this->validacion_limite($data["limite"]);
        return $this->db->get()->result_array();
    }

    public function ObtenerPodcastPorFecha($data)
    {
        $this->db->select('id, description,title');
        $this->db->from($this->tablePodcast);
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            // $this->db->where('date between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        $this->db->where("business_id", $data["business_id"]);
        $this->db->where("capacitacion_obligatoria", 0);
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerTopicsPorFecha($data)
    {
        $this->db->select('ct.id, ct.name');
        $this->db->from($this->tableTopics . " as ct");
        $this->db->join($this->tableUser . " as u", "u.id = ct.id_user");
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            // $this->db->where('ct.date between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        $this->db->where("ct.active = 1");
        $this->validacion_roles();
        $this->db->where("u.business_id", $data["business_id"]);
        $this->db->where("ct.capacitacion_obligatoria", 0);
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function obtenerElementosMuroPorFecha($data)
    {
        $this->db->select('id, wall_description');
        $this->db->from($this->tableWall);
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            // $this->db->where('created_at between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        $this->db->where("active = 1");
        $this->db->where("business_id", $data["business_id"]);
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerCuestionariosPorFecha($data)
    {
        $this->db->select('id, name, category_id');
        $this->db->from($this->tableQuestionQuiz);
        $this->db->where("category_id", 1);
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            // $this->db->where('created_at between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        $this->db->where("active = 1");
        $this->db->where("business_id", $data["business_id"]);
        $this->db->where("capacitacion_obligatoria", 0);
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerCategoriasCuestionarios($data)
    {
        $this->db->select("*");
        $this->db->from($this->tableQuestionsCategories);
        $this->validacion_limite($data["limite"]);
        return $this->db->get()->result_array();
    }

    public function ObtenerJuegosProductosPorFecha($data)
    {
        $this->db->select('id, description');
        $this->db->from($this->tableGameProductsQuiz);
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            // $this->db->where('created_at between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        $this->db->where("active = 1");
        $this->db->where("business_id", $data["business_id"]);
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerJuegosRunPanchoPorFecha($data)
    {
        $this->db->select("id, nombre");
        $this->db->from("game_run_pancho_run_temas");
        $this->db->where("business_id", $data["business_id"]);
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerJuegosRetos($data)
    {
        $this->db->select("g.id, g.nombre");
        $this->db->from("game_retos as g");
        $this->db->join("user as u", "u.id = g.user_id");
        $this->validacion_roles();
        $this->db->where("u.business_id", $data["business_id"]);
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerJuegosRuletaPorFecha($data)
    {
        $this->db->select('id, name');
        $this->db->from($this->tableGameRouletteQuiz);
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            // $this->db->where('created_at between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        $this->db->where("active = 1");
        $this->db->where("business_id", $data["business_id"]);
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerJuegosSerpientesYEscalerasPorFecha($data)
    {
        $this->db->select('g.id, g.game_name');
        $this->db->from($this->tableGameSnakeStairsActiveGame . " as g");
        $this->db->join($this->tableUser . " as u", "u.id = g.owner_id");
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            // $this->db->where('g.created_at between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        $this->db->where("g.game_name != ''");
        $this->validacion_roles();
        $this->db->where("u.business_id", $data["business_id"]);
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerJuegosProfiler($data)
    {
        $this->db->select('id, history');
        $this->db->from($this->tableGameProfiler);
        // if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
        //     $this->db->where('created_at between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        // }
        $this->db->where("active = 1");
        $this->db->where("business_id", $data["business_id"]);
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }
    public function ObtenerReporteInteraccionesComentariosWall($data)
    {
        $this->db->select("w.id as post_id, w.wall_description,wc.user_id, concat(u.name,' ',u.last_name) as name,count(wc.comment) as num_comentarios");
        $this->db->from($this->tableWall . " as w");
        $this->db->join($this->tableWallComments . " as wc", "wc.post_id = w.id");
        $this->db->join($this->tableUser . " as u", "u.id = wc.user_id");
        $this->db->where("w.business_id", $data["business_id"]);
        $this->db->where("w.active", 1);
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            // $this->db->where('wc.created_at between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->validacion_roles();
        $this->db->group_by("wc.post_id, user_id");
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerReporteInteraccionesLikeWall($data)
    {
        $this->db->select("w.id as post_id, w.wall_description,wl.user_id, concat(u.name,' ',u.last_name) as name");
        $this->db->from($this->tableWall . " as w");
        $this->db->join($this->tableWallLike . " as wl", "wl.post_id = w.id");
        $this->db->join($this->tableUser . " as u", "u.id = wl.user_id");
        $this->db->where("w.business_id", $data["business_id"]);
        $this->db->where("w.active", 1);
        // $this->db->where("u.es_prueba", 0);
        $this->db->where('date_format(wl.created_at,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(wl.created_at,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"');
        // if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
        //     // ;
        // }
        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] != "undefined") {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->validacion_roles();
        $this->db->group_by("wl.id");
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerReporteDeUso($data)
    {
        $this->db->select("if(pu.veces_visto is null, 0, pu.veces_visto) as veces_visto,if(pu.podcast_id is null, 0,pu.podcast_id) as podcast_id,u.id, concat(u.name,' ',u.last_name) as name");
        $this->db->from($this->tablePodcastUsage . " as pu");
        $this->db->join($this->tableUser . " as u", "u.id = pu.user_id", "right");
        $this->db->where('u.business_id', $data["business_id"]);
        if ($data["nombre_usuario"] !== null) {
            $this->db->where('concat(u.name," ",u.last_name) like', "%" . $data["nombre_usuario"] . "%");
        }
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            $this->db->where('pu.fecha between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        $this->validacion_roles();
        $this->db->group_by("pu.id");
        $this->db->order_by("veces_visto");
        $this->validacion_limite($data["limite"]);
        // $this->db->group_by("p.id, user_id");
        return $this->db->get()->result_array();
    }

    public function ObtenerGraficaLibrary($data)
    {
        $this->db->select("le.id, le.title, sum(lu.veces_visto) as veces_visto");
        $this->db->from($this->tableLibraryElements . " as le");
        $this->db->join($this->tableLibraryUsage . " as lu", "lu.library_element_id = le.id");
        $this->db->join($this->tableUser . " as u", "u.id = lu.user_id", "left");
        $this->db->where('le.business_id', $data["business_id"]);
        // $this->db->where('u.es_prueba', 0);
        $this->db->where('le.active', 1);
        $this->db->where("le.capacitacion_obligatoria", 0);
        $this->db->where('date_format(lu.fecha,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(lu.fecha,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"', null, false);
        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] != "undefined") {
            $this->db->where('concat(u.name," ",u.last_name) like', "%" . $data["nombre_usuario"] . "%");
        }
        $this->validacion_roles();
        // if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
        // }
        $this->db->group_by("le.id");
        $this->db->order_by('veces_visto', "desc");
        $this->validacion_limite($data["limite"]);
        return $this->db->get()->result_array();
    }

    public function ObtenerGraficaPodcastMasConsumido($data)
    {
        $query = "
            select 
                p.title title,
                sum(pu.veces_visto) visto
            from podcast p
                join podcast_usage as pu on p.id = pu.podcast_id
                join user u on u.id = pu.user_id
            where p.capacitacion_obligatoria = 0
                and date_format(pu.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(pu.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'     
                and u.business_id = " . $data['business_id'] . "
                and p.active = 1
                " . $this->validacion_roles_string() . "
            group by p.title
        order by sum(pu.veces_visto) desc
        ";
        $result = $this->db->query($query)->result_array();
        return $result;
    }

    public function ObtenerGraficaPodcastMejorCalificacion($data)
    {
        $query = "
            select 
                p.title,
                truncate(avg(ps.score),1) score
            from podcast p
                join podcast_score ps on ps.podcast_id = p.id
                join user u on u.id = ps.user_id
            where p.capacitacion_obligatoria = 0
                and date_format(ps.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(ps.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'   
                and u.business_id = 13
                and p.active = 1
                " . $this->validacion_roles_string() . "
            group by p.title
            order by avg(ps.score) desc
        ";
        $result = $this->db->query($query)->result_array();
        return $result;
    }

    public function ObtenerGraficaPodcastMasComentarios($data)
    {
        $query = "
            select 
                p.title,
                count(pc.comment) num_comentarios
            from podcast p
                join podcast_comments pc on pc.podcast_id = p.id
                join user u on u.id = pc.user_id
            where p.capacitacion_obligatoria = 0
                and date_format(pc.datetime,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(pc.datetime,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "' 
                and u.business_id = 13
                and p.active = 1
                " . $this->validacion_roles_string() . "
            group by p.title
        ";
        $result = $this->db->query($query)->result_array();
        return $result;
    }

    //cosulta string
    public function ObtenerGraficaLibraryValorado($data)
    {
        $query = "
        select l.id, l.title,sum(q.answer) as calificacion
        from question_answer_users as q 
        join user as u on u.id = q.user_id
        join library_elements_ as l on l.id = q.id_elemento
        where question_id = 56
        and l.capacitacion_obligatoria = 0
        and u.business_id = " . $data["business_id"] . "
        and date_format(q.date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(q.date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
         and u.active = 1 and q.id_elemento is not null " . $this->validacion_roles_string() . "
        group by l.id
        order by user_id
        " . $this->validacion_limite_string($data["limite"]) . "
        ";
        $result = [];
        $result["totales"] = $this->db->query($query)->result_array();
        $query = "
        select l.id, l.title,count(*) as cantidad
        from question_answer_users as q 
        join user as u on u.id = q.user_id
        join library_elements_ as l on l.id = q.id_elemento
        where question_id = 56
        and l.capacitacion_obligatoria = 0
        and u.business_id = " . $data["business_id"] . "
        and date_format(q.date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(q.date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
       and u.active = 1 and q.id_elemento is not null " . $this->validacion_roles_string() . "
        group by l.id
        order by user_id
        " . $this->validacion_limite_string($data["limite"]) . "
        ";
        $result["respuestas"] = $this->db->query($query)->result_array();
        $query = "
        select l.id, l.title,sum(q.answer)/count(*) as promedio
        from question_answer_users as q 
        join user as u on u.id = q.user_id
        join library_elements_ as l on l.id = q.id_elemento
        where question_id = 56
        and l.capacitacion_obligatoria = 0
        and u.business_id = " . $data["business_id"] . "
        and date_format(q.date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(q.date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
         and u.active = 1 and q.id_elemento is not null " . $this->validacion_roles_string() . "
        group by l.id
        order by user_id
        " . $this->validacion_limite_string($data["limite"]) . "
        ";
        $result["promedio"] = $this->db->query($query)->result_array();
        return $result;
    }

    //consulta string
    public function DescargarReporteValoradosLibrary($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteCalificacionesBiblioteca" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
        select l.id, l.title,count(*) as cantidad, sum(q.answer) as respuestas, sum(q.answer)/count(*) as promedio
        from question_answer_users as q 
        join user as u on u.id = q.user_id
        join library_elements_ as l on l.id = q.id_elemento
        where question_id = 56
        and l.capacitacion_obligatoria = 0
        and u.business_id = " . $data["business_id"] . "
        and date_format(q.date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(q.date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        and u.active = 1 and q.id_elemento is not null " . $this->validacion_roles_string() . "
        group by l.id
        order by user_id
       
        ";
        $resultado = $this->db->query($query)->result_array();
        $header = [["ID", "TITULO", "NUMERO DE RESPUESTAS", "SUMA DE RESPUESTAS", "PROMEDIO"]];
        $resultado = array_merge($header, $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
    }

    public function DescargarReporteTiposConsumidoLibrary($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteTiposMasConsumidosBiblioteca" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
        select 
            le.id,
            le.title,
            (if((le.type = 'video'),
            if((le.type_video = 'servidor'),'.mp4',
            le.type_video),
            if(substring(le.file,-4,4) != '',(substring(le.file,-4,4)),'link'))) as tipo, 
            concat('#', u.number_employee) id_usuario,
            concat(u.name, ' ', u.last_name) usuario,
            j.job_name perfil,
            lu.fecha,
            sum(lu.veces_visto) as numero_vistas
        from library_usage as lu
        join library_elements_ as le on le.id = lu.library_element_id
        join user as u on u.id = lu.user_id
        left join jobs j on j.id = u.job_id
        where le.business_id = " . $data["business_id"] . "
        and le.capacitacion_obligatoria = 0
        and le.active = 1  
        " . $this->validacion_roles_string() . "
        and date_format(lu.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "'
        and date_format(lu.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        group by tipo, u.id
        order by numero_vistas desc 
       
        ";
        $resultado = $this->db->query($query)->result_array();
        $header = [["ID", "TITULO", "TIPO", "NUM_EMPLEADO", "USUARIO", "PERFIL",  "FECHA", "VECES VISTO"]];
        $resultado = array_merge($header, $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
    }

    public function DescargarReporteMasActivosLibrary($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteUsuariosActivosBiblioteca" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
            select 
                le.id,
                le.title,
                le.type,
                concat('#', u.number_employee) num_empleado,
                concat(u.name, ' ',u.last_name) as name,
                j.job_name perfil, 
                l.fecha,
                sum(l.veces_visto) as numero_vistas
            from library_usage as l
            join library_elements_ as le on le.id = l.library_element_id
            join user as u on u.id = l.user_id
            left join jobs j on j.id = u.job_id
            where u.business_id = " . $data["business_id"] . "
            and u.active = 1 
            and le.capacitacion_obligatoria = 0
            and le.active = 1 
            " . $this->validacion_roles_string() . "
            and date_format(l.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' 
            and date_format(l.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
            group by u.id
            order by numero_vistas desc 
        ";
        $resultado = $this->db->query($query)->result_array();
        $header = [["ID", "TITULO", "TIPO", "NUM_EMPLEADO", "USUARIO", "PERFIL",  "FECHA", "VECES VISTO"]];
        $resultado = array_merge($header, $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
    }

    public function DescargarReporteDiasMasActividad($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteDiasMasActividadBiblioteca" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
            select
                le.id,
                le.title,
                le.type,
                concat('#',u.number_employee)num_empleado,
                concat(u.name,' ',u.last_name) usuario,
                j.job_name perfil,
                lu.fecha,
                date_format(lu.fecha,'%Y-%m-%d') as fecha_, sum(lu.veces_visto) as numero_vistas
            from library_usage as lu
            join library_elements_ as le on le.id = lu.library_element_id
            join user as u on u.id = lu.user_id
            left join jobs j on j.id = u.job_id
            where le.business_id = " . $data["business_id"] . "
            and le.capacitacion_obligatoria = 0
            and le.active = 1  
            " . $this->validacion_roles_string() . "
            and date_format(lu.fecha,'%Y-%m-%d') >=  '" . $data["fecha_inicio"] . "' 
            and date_format(lu.fecha,'%Y-%m-%d') <=  '" . $data["fecha_fin"] . "' 
            group by fecha_
            order by numero_vistas desc
        ";
        $resultado = $this->db->query($query)->result_array();
        $header = [["ID", "TITULO", "TIPO", "NUM_EMPLEADO", "USUARIO", "PERFIL",  "FECHA", "VECES VISTO"]];
        $resultado = array_merge($header, $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
    }

    public function DescargarReporteCalificacionesElementosLibrary($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteDiasCalificacionesElementosBiblioteca" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
            select 
                le.id,
                le.title, 
                le.type,
                concat('#',u.number_employee) num_empleado,
                concat(u.name,' ',u.last_name) usuario,
                j.job_name perfil,
                date_format(ls.fecha,'%Y-%m-%d') as fecha_,
                ls.score
            from library_elements_ le
                join library_score ls on ls.library_id = le.id
                join user u on u.id = ls.user_id
                left join jobs j on j.id = u.job_id
            where 
                le.business_id = " . $data["business_id"] . "
                and le.capacitacion_obligatoria = 0
                " . $this->validacion_roles_string() . "
                and date_format(ls.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' 
                and date_format(ls.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "' 
            group by le.id
            order by ls.score asc
        ";
        $resultado = $this->db->query($query)->result_array();
        $header = [["ID", "TITULO", "TIPO", "NUM_EMPLEADO", "USUARIO", "PERFIL",  "FECHA", "SCORE"]];
        $resultado = array_merge($header, $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
    }

    public function ObtenerReporteInteraccionesComentariosPodcast($data)
    {
        $this->db->select("p.id as podcast_id, p.description,pc.user_id, concat(u.name,' ',u.last_name) as name,count(pc.comment) as num_comentarios");
        $this->db->from($this->tablePodcast . " as p");
        $this->db->join($this->tablePodcastComments . " as pc", "pc.podcast_id = p.id");
        $this->db->join($this->tableUser . " as u", "u.id = pc.user_id");
        $this->validacion_roles();
        $this->db->where("p.business_id", $data["business_id"]);
        $this->db->where("p.capacitacion_obligatoria", 0);
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            $this->db->where('pc.datetime between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->group_by("p.id");
        $this->db->order_by("num_comentarios");
        // $this->db->group_by("p.id, user_id");
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerReporteInteraccionesCalificacionesPodcast($data)
    {
        $this->db->select("p.id as podcast_id, p.description ,pl.user_id, concat(u.name,' ',u.last_name) as name, pl.score");
        $this->db->from($this->tablePodcast . " as p");
        $this->db->join($this->tablePodcastScore . " as pl", "pl.podcast_id = p.id");
        $this->db->join($this->tableUser . " as u", "u.id = pl.user_id");
        $this->validacion_roles();
        $this->db->where("p.business_id", $data["business_id"]);
        $this->db->where("p.capacitacion_obligatoria", 0);
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            $this->db->where('pl.fecha between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->group_by("p.id");
        $this->db->order_by("pl.score");
        // $this->db->group_by("p.id, user_id");
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerPodcastMasInteracciones($data)
    {
        $this->db->select("p.id, p.title, count(pc.comment) as num_comentarios");
        $this->db->from($this->tablePodcast . " as p");
        $this->db->join($this->tablePodcastComments . " as pc", "pc.podcast_id = p.id");
        $this->db->join($this->tableUser . " as u", "u.id = pc.user_id");
        $this->validacion_roles();
        $this->db->where("p.business_id", $data["business_id"]);
        $this->db->where("p.capacitacion_obligatoria", 0);
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            $this->db->where('pc.datetime between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->group_by("p.id");
        $this->db->order_by("num_comentarios");
        $result = [];
        $this->validacion_limite($data["limite"]);
        $result["comentarios"] = $this->db->get()->result_array();

        $this->db->select("p.id, p.title, avg(ps.score) as promedio");
        $this->db->from($this->tablePodcast . " as p");
        $this->db->join($this->tablePodcastScore . " as ps", "ps.podcast_id = p.id");
        $this->db->join($this->tableUser . " as u", "u.id = ps.user_id");
        $this->validacion_roles();
        $this->db->where("p.business_id", $data["business_id"]);
        $this->db->where("p.capacitacion_obligatoria", 0);
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            $this->db->where('ps.fecha between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->group_by("p.id");
        $this->db->order_by("promedio");
        $this->validacion_limite($data["limite"]);
        $result["score"] = $this->db->get()->result_array();

        $this->db->select("p.id, p.title, sum(pu.veces_visto) as veces_visto");
        $this->db->from($this->tablePodcast . " as p");
        $this->db->join($this->tablePodcastUsage . " as pu", "pu.podcast_id = p.id");
        $this->db->join($this->tableUser . " as u", "u.id = pu.user_id");
        $this->validacion_roles();
        $this->db->where("p.business_id", $data["business_id"]);
        $this->db->where("p.capacitacion_obligatoria", 0);
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            $this->db->where('pu.fecha between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->group_by("p.id");
        $this->db->order_by("veces_visto");
        $this->validacion_limite($data["limite"]);
        $result["vistos"] = $this->db->get()->result_array();

        return $result;
    }

    public function ObtenerReporteFeedback($data)
    {
        $this->db->select("u.number_employee, CONCAT(u.name,' ',u.last_name) as name, count(u.id) as num_comentarios");
        $this->db->from($this->tableFeedback . " as f");
        $this->db->join($this->tableUser . " as u", "u.id = f.user_id");
        $this->db->where("u.business_id", $data["business_id"]);
        // $this->db->where("u.es_prueba", 0);
        $this->validacion_roles();
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            $this->db->where('date_format(f.fecha,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(f.fecha,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"');
        }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->group_by("u.id");
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerRankingUsuarios($data)
    {
        $this->db->select("u.number_employee, concat(u.name,' ',u.last_name) as name, u.score");
        $this->db->from($this->tableUser . " as u");
        $this->db->where("u.business_id", $data["business_id"]);
        $this->db->where("u.active = 1");
        // $this->db->where("u.es_prueba", 0);
        $this->validacion_roles();
        $this->db->order_by("u.score", "desc");
        $this->db->limit("10");
        $this->validacion_limite($data["limite"]);
        return $this->db->get()->result_array();
    }

    // public function descargarReporteComunidad($token, $data)
    // {
    //     header('Content-Type: text/csv; charset=utf-8');
    //     header('Expires: 0');
    //     header('Cache-Control: must-revalidate');
    //     header("Content-Disposition: attachment; filename=ReporteComunidadAprendizaje" . date('y-m-d') . ".csv");
    //     header('Last-Modified: ' . date('D M j G:i:s T Y'));
    //     $outss = fopen("php://output", "w");

    //     $this->db->select("concat(u.name,' ',u.last_name) as name, ct.name as name_topic, count(cm.id_user) as numero_mensajes");
    //     $this->db->from($this->tableUser . " as u");
    //     $this->db->join($this->tableComMessages . " as cm", "cm.id_user = u.id");
    //     $this->db->join($this->tableComTopics . " as ct", "ct.id = cm.id_topic");
    //     $this->db->where("u.business_id", $data["business_id"]);
    //     if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
    //         $this->db->where('date_format(cm.date,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(cm.date,"%Y-%m-$d") <= "' . $data["fecha_fin"] . '"');
    //     }
    //     if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] != "undefined") {
    //         $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
    //     }
    //     // $this->db->group_by("cm.id_topic ,cm.id_user");
    //     // no se debe filtrar para que coincida con los datos de la grafica
    //     $result = $this->db->get()->result_array();
    //     $resultado = array_merge([["USUARIO", "TOPIC", "NUM COMENTARIOS"]], $result);
    //     foreach ($resultado as $rows) {
    //         fputcsv($outss, $rows);
    //     }
    //     fclose($outss);
    //     return;
    // }

    //consulta string
    public function descargarReporteComunidadAprendizaje($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteComunidad" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "select ct.id, ct.name, u.number_employee, concat(u.name,' ',u.last_name) as nombre, j.job_name, cm.date
                from com_topics as ct
                join com_messages as cm on cm.id_topic = ct.id
                join user as u on u.id = cm.id_user
                left join jobs as j on j.id = u.job_id
                where u.business_id = " . $data["business_id"]
            . " and ct.capacitacion_obligatoria = 0  " . $this->validacion_roles_string() . "
            and date_format(cm.date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(cm.date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";
        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'null') {
            $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
        }
        $query .= " group by cm.id ";
        $resultado = $this->db->query($query)->result_array();
        $header = [["ID", "TITULO DE LA CONVERSACIÓN", "ID_USUARIO", "NOMBRE USUARIO", "PERFIL USUARIO", "FECHA DE COMENTARIO"]];
        $resultado = array_merge($header, $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    public function descargarReporteBiblioteca($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteBiblioteca" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        // , coalesce(sum(lu.numero_clicks),0) as numero_clicks
        $this->db->select("e.id, e.title,(if((e.type = 'video'),if((e.type_video = 'servidor'),'.mp4',e.type_video),if(substring(e.file,'-4',4) != '',(substring(e.file,'-4',4)),'link'))) as tipo, u.number_employee ,concat(u.name,' ',u.last_name) as nombre,j.job_name, lu.fecha, count(*) as veces_visto, q.answer");
        $this->db->from("library_elements_ as e");
        $this->db->join("library_usage as lu", "e.id = lu.library_element_id");
        $this->db->join("user as u", "u.id = lu.user_id", "left");
        $this->db->join("jobs as j", "j.id = u.job_id", "left");
        $this->db->join("question_answer_users as q", "q.id_elemento = e.id and q.user_id = u.id", "left");
        $this->db->where("u.business_id", $data["business_id"]);
        // $this->db->where("u.es_prueba", 0);
        $this->db->where("u.active", 1);
        $this->db->where("e.active", 1);
        $this->db->where("e.capacitacion_obligatoria", 0);
        $this->validacion_roles();
        // $this->db->where("e.id",217);
        // if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
        $this->db->where('(date_format(lu.fecha,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(lu.fecha,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '")', null, false);
        // }
        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] != "undefined") {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->group_by("e.id,u.id");
        $this->db->order_by("veces_visto desc");

        // si lo agrupo aqui, ya no se ven todas los resultados y no cuadra con la grafica (solo se verian los ultimos)
        $result = $this->db->get()->result_array();

        $resultado = array_merge([["ID", "TITULO", "TIPO", "ID USUARIO", "NOMBRE USUARIO", "PERFIL USUARIO", "FECHA ULTIMA CONSULTA", "VECES VISTO", "RESPUESTA"]], $result);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    //consulta string
    public function ObtenerJuegosUsuariosQuery($token, $data)
    {
        // $database  = $this->getRealDatabase($token);
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteJuegos" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");

        // $query = "select concat(u.name, ' ', u.last_name) as name, count(if(g.correct = 1,1,null)) as correctas,
        //           count(if(g.correct = 0, 1, null)) as incorrectas
        //           from game_products_results as g
        //           join user as u on u.id = g.user_id
        //           where u.business_id =" . $data["business_id"]
        //     . " and g.created_at between '" . $data["fecha_inicio"] . "' and '" . $data["fecha_fin"] . "'";

        // if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'undefined') {
        //     $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
        // }

        // $query .= " group by u.id";

        // $resultado = $this->db->query($query)->result_array();
        // $resultado = array_merge([["JUEGO PRODUCTOS"], ["NOMBRE", "CORRECTAS", "INCORRECTAS"]], $resultado);

        $query = "select 'Ruleta' as nombre_juego,u.number_employee, concat(u.name, ' ',u.last_name) as name, j.job_name, gr.created_at, count(if(ga.correct = 1,1,null)) as correctas,count(*) as intentos
                  from (select distinct question_id, answer_id, user_id, created_at from game_roulette_results) as gr
                  join user as u on u.id = gr.user_id
                  join game_roulette_question_answers as ga on ga.id = gr.answer_id
                  join game_roulette_questions as gqq on gqq.id = gr.question_id
                  join game_roulette_quiz as gq on gqq.quiz_id = gq.id
                  left join jobs as j on j.id = u.job_id
                  where u.business_id =" . $data["business_id"]
            . "  " . $this->validacion_roles_string() . "
            and date_format(gr.created_at,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(gr.created_at,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'null') {
            $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
        }

        $query .= " group by u.id,gq.id,gr.created_at " . $this->validacion_limite_string($data["limite"]) . "";
        $resultado = [["Juego", "Id usuario", "Nombre usuario", "Perfil usuario", "Fecha ultima consulta", "Puntos", "Intentos"]];
        $resultado = array_merge($resultado, $this->db->query($query)->result_array());
        // $resultado = array_merge($resultado, [[""], ["JUEGO RULETA"], ["NOMBRE", "CORRECTAS", "INCORRECTAS"]], $resultado_ruleta);

        // //aqui estoy editando la descarga de csv

        // $query = "select concat(u.name, ' ',u.last_name) as name, count(if(pa.correct = 1, 1,null)) as correctas,
        //           count(if(pa.correct = 0, 1, null)) as incorrectas
        //           from profiler_results as pr
        //           join user as u on u.id = pr.user_id
        //           join profiler_question_answer as pa on pa.id = pr.answer_id
        //           where u.business_id =" . $data["business_id"]
        //     . " and date_format(pr.created_at,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(pr.created_at,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

        // if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'undefined') {
        //     $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
        // }

        // $query .= " group by u.id";

        // $resultado_perfilador = $this->db->query($query)->result_array();
        // $resultado = array_merge($resultado, [[""], ["JUEGO PERFILADOR"], ["NOMBRE", "CORRECTAS", "INCORRECTAS"]], $resultado_perfilador);

        $query = "
            select 
                'Run pancho run' as nombre_juego,
                concat('#',u.number_employee) num_empleado, 
                concat(u.name, ' ',u.last_name) as name,
                j.job_name,
                date_format(gr.fecha,'%Y-%m-%d') fecha, 
                gr.score as puntaje,
                 count(*) as intentos
            from game_run_pancho_results as gr
                join game_run_pancho_run_temas as grn on grn.id = gr.id_tema
                join user as u on u.id = gr.user_id
                join jobs as j on j.id = u.job_id
            where u.business_id =" . $data["business_id"]
            . "  " . $this->validacion_roles_string() . "
                and date_format(gr.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(gr.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] != null && $data["nombre_usuario"] != 'null') {
            $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
        }

        $query .= " group by u.id,gr.fecha " . $this->validacion_limite_string($data["limite"]) . "";

        $resultado_run = $this->db->query($query)->result_array();
        $resultado = array_merge($resultado, $resultado_run);

        $query = "
            select 
                'Ahorcado' as nombre_juego, 
                concat('#',u.number_employee) num_empleado, 
                concat(u.name,' ',u.last_name) as name, 
                j.job_name,
                date_format(au.fecha,'%Y-%m-%d') fecha,
                au.puntos,
                count(*) as intentos
            from game_ahorcado_users as au
                join user as u on u.id = au.id_usuario
                join jobs as j on j.id = u.job_id
            where u.business_id = " . $data["business_id"]
            . "  " . $this->validacion_roles_string() . "
            and date_format(au.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(au.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";
        // $query = "select concat(u.name, ' ',u.last_name) as name,grn.nombre, gr.desempeno,gr.desempeno2, gr.actitud, gr.actitud2
        // from game_retos_users as gr
        // join game_retos as grn on grn.id = gr.id_reto
        // join user as u on u.id = gr.id_retado
        // where u.business_id =" . $data["business_id"]
        //     . " and date_format(gr.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(gr.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        //     and (desempeno is not null || desempeno2 is not null || desempeno3 is not null || actitud is not null || actitud2 is not null || actitud3 is not null || actitud4 is not null)";

        // if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'undefined') {
        //     $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
        // }


        $query .= " group by u.id, au.fecha " . $this->validacion_limite_string($data["limite"]) . "";

        $resultado_run = $this->db->query($query)->result_array();
        $resultado = array_merge($resultado, $resultado_run);

        $query = "
            select 
                'culebra' as nombre_juego,
                concat('#',u.number_employee) num_empleado, 
                concat(u.name, ' ',u.last_name) as name,
                j.job_name,
                date_format(gr.fecha,'%Y-%m-%d') fecha,
                gr.score as puntaje, 
                count(*) as intentos
            from game_snake_results gr 
                join game_snake_temas gt on gt.id = gr.id_tema
                join user u on u.id = gr.user_id
                join jobs j on j.id = u.job_id
            where u.business_id = " . $data["business_id"]
            . "  " . $this->validacion_roles_string() . "
                and date_format(gr.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(gr.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

        $query .= " group by nombre_juego" . $this->validacion_limite_string($data["limite"]) . "";

        $resultado_run = $this->db->query($query)->result_array();
        $resultado = array_merge($resultado, $resultado_run);


        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    public function DescargarReporteResultadosRuleta($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteResultadosRuleta" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
            select
                gr.question_id,
                'Ruleta' as nombre_juego,
                concat('#',u.number_employee) num_empleado,
                concat(u.name,' ',u.last_name) usuario,
                j.job_name perfil,
                date_format(gr.created_at,'%Y-%m-%d') fecha,
                count(if(ga.correct = 1, 1, null)) as correctas,count(if(ga.correct = 0, 1, null)) as incorrectas
            from (select distinct question_id, answer_id, user_id, created_at from game_roulette_results) as gr
                join user as u on u.id = gr.user_id
                join game_roulette_question_answers as ga on ga.id = gr.answer_id
                join game_roulette_questions as gqq on gqq.id = gr.question_id
                join game_roulette_quiz as gq on gqq.quiz_id = gq.id
                left join jobs as j on j.id = u.job_id
            where 
                u.business_id = " . $data["business_id"] . "
                " . $this->validacion_roles_string() . "
                and date_format(gr.created_at,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' 
                and date_format(gr.created_at,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'null') {
            $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
        }
        $query .= "group by u.id   ";
        $resultado = $this->db->query($query)->result_array();
        $header = [["ID", "NOMBRE DEL JUEGO", "NUM_EMPLEADO", "USUARIO", "PERFIL", "FECHA", "CORRECTAS", "INCORRECTAS"]];
        $resultado = array_merge($header, $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    public function DescargarReporteResultadosRunPancho($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteResultadosRunPancho" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
            select
                gr.id,
                'Run pancho run' as nombre_juego,
                concat('#',u.number_employee) num_empleado,
                concat(u.name, ' ', u.last_name) usuario,
                j.job_name perfil,
                date_format(grs.fecha,'%Y-%m-%d') fecha,
                count(if(gr.tipo = 1, 1, null)) as buenas,
                count(if(gr.tipo = 0, 1, null)) as malas 
            from game_run_pancho_words as gr
                left join game_run_pancho_results as grs on grs.id_tema = gr.id_tema
                join user as u on u.id = grs.user_id
                left join jobs as j on j.id = u.job_id
            where 
                u.business_id = " . $data["business_id"] . "
                " . $this->validacion_roles_string() . "
                and date_format(grs.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' 
                and date_format(grs.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'null') {
            $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
        }
        $query .= "group by u.id, grs.id_tema, grs.id   ";
        $resultado = $this->db->query($query)->result_array();
        $header = [["ID", "NOMBRE DEL JUEGO", "NUM_EMPLEADO", "USUARIO", "PERFIL", "FECHA", "CORRECTAS", "INCORRECTAS"]];
        $resultado = array_merge($header, $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    public function DescargarReporteResultadosCulebra($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteResultadosCulebra" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
            select
                gsr.id,
                'Culebra' as nombre_juego,
                concat('#',u.number_employee) num_empleado,
                concat(u.name,' ',u.last_name) usuario,
                j.job_name perfil,
                date_format(gsr.fecha,'%Y-%m-%d') fecha,
                count(if(gsp.tipo = 1, 1, null)) as buenas,count(if(gsp.tipo = 0, 1, null)) as malas
            from game_snake_palabras gsp    
                left join game_snake_results gsr on gsr.id_tema = gsp.id_tema
                join user u on u.id = gsr.user_id
                left join jobs j on j.id = u.job_id
            where 
                u.business_id = " . $data["business_id"] . "
                " . $this->validacion_roles_string() . "
                and date_format(gsr.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' 
                and date_format(gsr.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'null') {
            $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
        }
        $query .= "group by u.id   ";
        $resultado = $this->db->query($query)->result_array();
        $header = [["ID", "NOMBRE DEL JUEGO", "NUM_EMPLEADO", "USUARIO", "PERFIL", "FECHA", "CORRECTAS", "INCORRECTAS"]];
        $resultado = array_merge($header, $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    public function descargarReporteMuro($token, $data)
    {
        // header('Content-Type: text/csv; charset=utf-8');
        // header('Expires: 0');
        // header('Cache-Control: must-revalidate');
        // header("Content-Disposition: attachment; filename=ReporteComunidadAprendizaje" . date('y-m-d') . ".csv");
        // header('Last-Modified: ' . date('D M j G:i:s T Y'));
        // $outss = fopen("php://output", "w");

        // $this->db->select("concat(u.name,' ',u.last_name) as nombre, coalesce(wa.wall_description, wb.wall_description) as wall_name, if(wl.id is not null,'si','no') as num_like, count(*) as comentarioss ");
        // $this->db->from($this->tableUser . " as u");
        // $this->db->join("wall_comments as wc", "wc.user_id = u.id", "left");
        // $this->db->join("wall_post_like as wl", "wl.user_id = u.id", "left");
        // $this->db->join("wall as wa ", "wa.id = wc.post_id", "left");
        // $this->db->join("wall as wb ", "wb.id = wl.post_id", "left");
        // $this->db->where("u.business_id", $data["business_id"]);

        // if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
        //     $this->db->where('(date_format(wl.created_at,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(wl.created_at,"%Y-%m-$d") <= "' . $data["fecha_fin"] . '")
        //     or (date_format(wc.created_at,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(wc.created_at,"%Y-%m-$d") <= "' . $data["fecha_fin"] . '")', null, false);
        // }
        // if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] != "undefined") {
        //     $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        // }
        // $this->db->group_by("u.id");
        // $result = $this->db->get()->result_array();
        // $resultado = array_merge([["USUARIO", "PUBLICACIÓN", "LKIE", "NUM COMENTARIOS"]], $result);
        // foreach ($resultado as $rows) {
        //     fputcsv($outss, $rows);
        // }
        // fclose($outss);
        // return;
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteMuro" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");

        $query = "
        select w.id, w.wall_description,u.number_employee, concat(u.name, ' ', u.last_name) as name,j.job_name,  sum(if(wp.id is null,0,1)) as numero_likes, 0 as numero_comentarios
        from wall as w        
        join wall_post_like as wp on wp.post_id = w.id
        join user as u on u.id = wp.user_id
        join jobs as j on j.id = u.job_id
        where w.business_id = " . $data["business_id"] . " and w.active = 1
        and date_format(wp.created_at,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(wp.created_at,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        " . $this->validacion_roles_string() . "
        group by w.id
        ";
        $likes =  $this->db->query($query)->result_array();
        $query = "
        select w.id, w.wall_description,u.number_employee, concat(u.name, ' ', u.last_name) as name,j.job_name, 0 as numero_likes,  sum(if(wc.id is null,0,1)) as numero_comentarios
        from wall as w
        join wall_comments as wc on wc.post_id = w.id
        join user as u on u.id = wc.user_id
        join jobs as j on j.id = u.job_id
        where w.business_id = " . $data["business_id"] . " and w.active = 1
        and date_format(wc.created_at,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(wc.created_at,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        " . $this->validacion_roles_string() . "
        group by w.id
        ";
        $comentarios = $this->db->query($query)->result_array();
        $muro = [];
        if (count($likes) > 0) {
            for ($i = 0; $i < count($likes); $i++) {
                $indice = -1;
                for ($j = 0; $j < count($comentarios); $j++) {
                    if ($likes[$i]["wall_description"] == $comentarios[$j]["wall_description"]) {
                        $indice = $j;
                    }
                }

                if ($indice > -1) {
                    $obj = [];
                    $obj["id"] = $likes[$i]["id"];
                    $obj["wall_description"] = $likes[$i]["wall_description"];
                    $obj["number_employee"] = $likes[$i]["number_employee"];
                    $obj["name"] = $likes[$i]["name"];
                    $obj["job_name"] = $likes[$i]["job_name"];
                    $obj["numero_likes"] = $likes[$i]["numero_likes"] + $comentarios[$indice]["numero_likes"];
                    $obj["numero_comentarios"] = $likes[$i]["numero_comentarios"] + $comentarios[$indice]["numero_comentarios"];
                    array_push($muro, $obj);
                } else {
                    $obj = [];
                    $obj["id"] = $likes[$i]["id"];
                    $obj["wall_description"] = $likes[$i]["wall_description"];
                    $obj["number_employee"] = $likes[$i]["number_employee"];
                    $obj["name"] = $likes[$i]["name"];
                    $obj["job_name"] = $likes[$i]["job_name"];
                    $obj["numero_likes"] = $likes[$i]["numero_likes"];
                    $obj["numero_comentarios"] = $likes[$i]["numero_comentarios"];
                    array_push($muro, $obj);
                }
            }
        } else {
            $muro = $comentarios;
        }
        // echo json_encode($muro);
        // return $muro;

        // $result = $this->db->get()->result_array();
        $resultado = array_merge([["ID", "TITULO DE LA PUBLICACIÓN", "ID USUARIO", "NOMBRE USUARIO", "PERFIL USUARIO", "LIKE", "COMENTARIOS"]], $muro);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    //funciones para reportes de podcast
    public function ObtenerPodcastMasConsumido($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReportePodcastMasConsumido" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
        select 
            p.id,
            p.title titulo,
            concat(u.name,' ',u.last_name) nombre,
            concat('#',u.number_employee) as num_empleado, 
            j.job_name,
            pu.fecha,
            pu.veces_visto
        from podcast p 
            join podcast_usage pu on pu.podcast_id = p.id
            join user u on u.id = pu.user_id
            join jobs j on j.id = u.job_id
        where p.capacitacion_obligatoria = 0
            and date_format(pu.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(pu.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'  
            and u.business_id = " . $data['business_id'] . "
            and p.active = 1
            " . $this->validacion_roles_string() . "
            group by p.id, p.title, concat(u.name,' ',u.last_name),concat('#',u.number_employee),j.job_name,pu.fecha,pu.veces_visto
        ";

        $resultado = $this->db->query($query)->result_array();
        $resultado = array_merge([["ID", "TITULO", "USUARIO", "#_EMPLEADO", "PERFIL", "FECHA", "VECES_VISTO"]], $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    public function ObtenerPodcastMasCalificado($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReportePodcast" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
            select 
                p.id,
                p.title,
                concat(u.name,' ',u.last_name) nombre,
                concat('#',u.number_employee) num_empleado,
                j.job_name,
                ps.fecha,
                ps.score
            from podcast p
                join podcast_score ps on p.id = ps.podcast_id
                join user u on u.id = ps.user_id
                join jobs j on j.id = u.job_id
            where p.capacitacion_obligatoria = 0
                and date_format(ps.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(ps.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
                and u.business_id = " . $data['business_id'] . "
                and p.active = 1
                " . $this->validacion_roles_string() . "
                group by p.id, p.title, concat(u.name,' ',u.last_name),concat('#',u.number_employee),j.job_name,ps.fecha,ps.score
        ";
        $resultado = $this->db->query($query)->result_array();
        $resultado = array_merge([["ID", "TITULO", "USUARIO", "#_EMPLEADO", "PERFIL", "FECHA", "SCORE"]], $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    public function ObtenerPodcastMasComentado($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReportePodcast" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
            select 
                p.id,
                p.title,
                concat(u.name,' ',u.last_name) nombre,
                concat('#',u.number_employee) num_empleado,
                j.job_name,
                pc.datetime fecha,
                pc.comment comentario
            from podcast p
                join podcast_comments pc on pc.podcast_id = p.id
                join user u on u.id  = pc.user_id
                join jobs j on j.id = u.job_id
            where p.capacitacion_obligatoria = 0
                and date_format(pc.datetime,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(pc.datetime,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
                and u.business_id = " . $data['business_id'] . "
                and p.active = 1
                " . $this->validacion_roles_string() . "
            group by p.id, p.title, concat(u.name,' ',u.last_name),concat('#',u.number_employee),j.job_name,pc.datetime,pc.comment
        ";
        $resultado = $this->db->query($query)->result_array();
        $resultado = array_merge([["ID", "TITULO", "USUARIO", "#_EMPLEADO", "PERFIL", "FECHA", "COMENTARIO"]], $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    //consulta string
    public function ObtenerPodcastUsuariosQuery($token, $data)
    {
        // $database  = $this->getRealDatabase($token);
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReportePodcast" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "select p.title, count(pc.comment) as num_comentarios
                          from podcast as p
                          join podcast_comments as pc on pc.podcast_id = p.id
                          join user as u on u.id = pc.user_id
                          where p.business_id = " . $data["business_id"]
            . " and p.capacitacion_obligatoria = 0  " . $this->validacion_roles_string() . "
            and pc.datetime between '" . $data["fecha_inicio"] . "' and '" . $data["fecha_fin"] . "'";

        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'undefined') {
            $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
        }
        $query .= " group by p.id
                   order by num_comentarios desc ";
        $result = [];
        $result["comentarios"] = $this->db->query($query)->result_array();

        $this->db->select("p.title, avg(ps.score) as promedio");
        $this->db->from($this->tablePodcast . " as p");
        $this->db->join($this->tablePodcastScore . " as ps", "ps.podcast_id = p.id");
        $this->db->join($this->tableUser . " as u", "u.id = ps.user_id");
        $this->db->where("p.business_id", $data["business_id"]);
        $this->db->where("p.capacitacion_obligatoria", 0);
        // $this->db->where("u.es_prueba", 0);
        $this->validacion_roles();
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            $this->db->where('ps.fecha between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] != "undefined") {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->group_by("p.id");
        $this->db->order_by("promedio", "desc");

        $result["score"] = $this->db->get()->result_array();

        $this->db->select("p.title, sum(pu.veces_visto) as veces_visto");
        $this->db->from($this->tablePodcast . " as p");
        $this->db->join($this->tablePodcastUsage . " as pu", "pu.podcast_id = p.id");
        $this->db->join($this->tableUser . " as u", "u.id = pu.user_id", "right");
        $this->db->where("p.business_id", $data["business_id"]);
        $this->db->where("p.capacitacion_obligatoria", 0);
        // $this->db->where("u.es_prueba", 0);
        $this->validacion_roles();
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            $this->db->where('date_format(pu.fecha,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(pu.fecha,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"');
        }
        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] != "undefined") {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->group_by("pu.id");
        $this->db->order_by("veces_visto", "desc");

        $result["vistos"] = $this->db->get()->result_array();

        $resultado = array_merge([["NOMBRE", "NUM COMENTARIOS"]], $result["comentarios"]);
        $resultado = array_merge($resultado, [["NOMBRE", "PROMEDIO CALIF"]], $result["score"]);
        $resultado = array_merge($resultado, [["NOMBRE", "VECES VISTO"]], $result["vistos"]);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }


    //consulta string
    public function ObtenerRankingUsuariosQuery($token, $data)
    {
        // $database  = $this->getRealDatabase($token);
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteRanking" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = $this->db->query("select concat(u.name,' ',u.last_name) as name, u.score
                                             from user as u
                                             where u.business_id =" . $data["business_id"]
            . " and " . $this->validacion_roles_string() . "
            and u.active = 1 order by u.score desc ");
        $resultado = $query->result_array();
        $resultado = array_merge([["NOMBRE", "PUNTUACIÓN"]], $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    // public function ObtenerJuegosUsuariosQuery($token, $data)
    // {
    //     // $database  = $this->getRealDatabase($token);
    //     header('Content-Type: text/csv; charset=utf-8');
    //     header('Expires: 0');
    //     header('Cache-Control: must-revalidate');
    //     header("Content-Disposition: attachment; filename=ReporteJuegos" . date('y-m-d') . ".csv");
    //     header('Last-Modified: ' . date('D M j G:i:s T Y'));
    //     $outss = fopen("php://output", "w");

    //     $query = "select concat(u.name, ' ', u.last_name) as name, count(if(g.correct = 1,1,null)) as correctas,
    //               count(if(g.correct = 0, 1, null)) as incorrectas
    //               from game_products_results as g
    //               join user as u on u.id = g.user_id
    //               where u.business_id =" . $data["business_id"]
    //         . " and g.created_at between '" . $data["fecha_inicio"] . "' and '" . $data["fecha_fin"] . "'";

    //     if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'undefined') {
    //         $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
    //     }

    //     $query .= " group by u.id";

    //     $resultado = $this->db->query($query)->result_array();
    //     $resultado = array_merge([["JUEGO PRODUCTOS"], ["NOMBRE", "CORRECTAS", "INCORRECTAS"]], $resultado);

    //     $query = "select concat(u.name, ' ',u.last_name) as name, count(if(ga.correct = 1,1,null)) as correctas,
    //               count(if(ga.correct = 0,1,null)) as incorrectas
    //               from game_roulette_results as gr
    //               join user as u on u.id = gr.user_id
    //               join game_roulette_question_answers as ga on ga.id = gr.answer_id
    //               where u.business_id =" . $data["business_id"]
    //         . " and date_format(gr.created_at,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(gr.created_at,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

    //     if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'undefined') {
    //         $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
    //     }

    //     $query .= " group by u.id";

    //     $resultado_ruleta = $this->db->query($query)->result_array();
    //     $resultado = array_merge($resultado, [[""], ["JUEGO RULETA"], ["NOMBRE", "CORRECTAS", "INCORRECTAS"]], $resultado_ruleta);

    //     // //aqui estoy editando la descarga de csv

    //     $query = "select concat(u.name, ' ',u.last_name) as name, count(if(pa.correct = 1, 1,null)) as correctas,
    //               count(if(pa.correct = 0, 1, null)) as incorrectas
    //               from profiler_results as pr
    //               join user as u on u.id = pr.user_id
    //               join profiler_question_answer as pa on pa.id = pr.answer_id
    //               where u.business_id =" . $data["business_id"]
    //         . " and date_format(pr.created_at,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(pr.created_at,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

    //     if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'undefined') {
    //         $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
    //     }

    //     $query .= " group by u.id";

    //     $resultado_perfilador = $this->db->query($query)->result_array();
    //     $resultado = array_merge($resultado, [[""], ["JUEGO PERFILADOR"], ["NOMBRE", "CORRECTAS", "INCORRECTAS"]], $resultado_perfilador);

    //     $query = "select concat(u.name, ' ',u.last_name) as name,grn.nombre, gr.score as puntaje
    //               from game_run_pancho_results as gr
    //               join game_run_pancho_run_temas as grn on grn.id = gr.id_tema
    //               join user as u on u.id = gr.user_id
    //               where u.business_id =" . $data["business_id"]
    //         . " and date_format(gr.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(gr.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

    //     if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'undefined') {
    //         $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
    //     }

    //     $query .= " group by u.id";

    //     $resultado_run = $this->db->query($query)->result_array();
    //     $resultado = array_merge($resultado, [[""], ["JUEGO RUN PANCHO RUN"], ["NOMBRE", "NOMBRE JUEGO", "PUNTAJE"]], $resultado_run);

    //     $query = "select concat(u.name, ' ',u.last_name) as name,grn.nombre, gr.desempeno,gr.desempeno2, gr.actitud, gr.actitud2
    //     from game_retos_users as gr
    //     join game_retos as grn on grn.id = gr.id_reto
    //     join user as u on u.id = gr.id_retado
    //     where u.business_id =" . $data["business_id"]
    //         . " and date_format(gr.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(gr.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
    //         and (desempeno is not null || desempeno2 is not null || desempeno3 is not null || actitud is not null || actitud2 is not null || actitud3 is not null || actitud4 is not null)";

    //     if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'undefined') {
    //         $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
    //     }

    //     $query .= " group by u.id";

    //     $resultado_run = $this->db->query($query)->result_array();
    //     $resultado = array_merge($resultado, [[""], ["JUEGO RETOS"], ["NOMBRE", "NOMBRE JUEGO", "DESEMPEÑO", "DESEMPEÑO 1", "ACTITUD", "ACTITUD 1"]], $resultado_run);


    //     foreach ($resultado as $rows) {
    //         fputcsv($outss, $rows);
    //     }
    //     fclose($outss);
    //     return;
    // }


    public function ObtenerReporteJuegos($data)
    {
        $this->db->select("u.id, concat(u.name,' ',u.last_name) as name, if(gpq.id is null, 0,gpq.id) as game_id, if(gpq.description is null, 0, gpq.description) as descripcion,'Juegos Productos' as categoria");
        $this->db->from($this->tableUser . " as u");
        $this->db->join($this->tableGameProductsResult . " as gpr", "gpr.user_id = u.id");
        $this->db->join($this->tableGameProductsQuiz . " as gpq", "gpq.id = gpr.quiz_id and gpq.active = 1");
        $this->db->where("u.business_id", $data["business_id"]);
        $this->validacion_roles();
        // $this->db->where("u.es_prueba", 0);
        // if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
        $this->db->where('date_format(gpr.created_at,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(gpr.created_at,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"');
        // }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->group_by("u.id,game_id");
        $this->validacion_limite($data["limite"]);
        $result1 = $this->db->get()->result_array();

        $this->db->select("u.id, concat(u.name, ' ',u.last_name) as name, if(grprt.id is null, 0, grprt.id) as game_id, if(grprt.nombre is null,0,grprt.nombre) as descripcion, 'Juegos Run Pancho Run' as categoria");
        $this->db->from("user as u");
        $this->db->join("game_run_pancho_results as grpr", "grpr.user_id = u.id");
        $this->db->join("game_run_pancho_run_temas as grprt", "on grprt.id = grpr.id_tema");
        $this->db->where("u.business_id", $data["business_id"]);
        // $this->db->where("u.es_prueba", 0);
        $this->validacion_roles();
        //aqui iria los filtros por fecha pero en las demas consultas se omitieron
        // if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
        $this->db->where('date_format(grpr.fecha,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(grpr.fecha,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"');
        // }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->group_by("u.id,game_id");
        $this->validacion_limite($data["limite"]);
        $result_run = $this->db->get()->result_array();

        $this->db->select("u.id, concat(u.name, ' ',u.last_name) as name, if(grprt.id is null, 0, grprt.id) as game_id, if(grprt.nombre is null,0,grprt.nombre) as descripcion, 'Juegos Retos' as categoria");
        $this->db->from("user as u");
        $this->db->join("game_retos_users as grpr", "grpr.id_retado = u.id");
        $this->db->join("game_retos as grprt", "on grprt.id = grpr.id_reto");
        $this->db->where("u.business_id", $data["business_id"]);
        // $this->db->where("u.es_prueba", 0);
        $this->validacion_roles();
        //aqui iria los filtros por fecha pero en las demas consultas se omitieron
        // if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
        $this->db->where('date_format(grpr.fecha,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(grpr.fecha,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"');
        // }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->where("(desempeno is not null || desempeno2 is not null || desempeno3 is not null || desempeno4 is not null || actitud is not null || actitud2 is not null || actitud3 is not null || actitud4 is not null)", null, false);
        $this->db->group_by("u.id,game_id");
        $this->validacion_limite($data["limite"]);
        $result_retos = $this->db->get()->result_array();

        $this->db->select("u.id, concat(u.name, ' ',u.last_name) as name, if(grprt.id is null, 0, grprt.id) as game_id, if(grprt.frase is null,0,grprt.frase) as descripcion, 'Juegos Ahorcado' as categoria");
        $this->db->from("user as u");
        $this->db->join("game_ahorcado_users as grpr", "grpr.id_usuario = u.id");
        $this->db->join("game_ahorcado as grprt", "on grprt.id = grpr.id_frase");
        $this->db->where("u.business_id", $data["business_id"]);
        // $this->db->where("u.es_prueba", 0);
        $this->validacion_roles();
        //aqui iria los filtros por fecha pero en las demas consultas se omitieron
        // if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
        $this->db->where('date_format(grpr.fecha,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(grpr.fecha,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"');
        // }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->group_by("u.id,game_id");
        $this->validacion_limite($data["limite"]);
        $result_ahorcado = $this->db->get()->result_array();

        $this->db->select("u.id, concat(u.name,' ',u.last_name) as name, if(grq.id is null, 0,grq.id) as game_id, if(grq.name is null, 0, grq.name) as descripcion, 'Juegos Ruleta' as categoria");
        $this->db->from($this->tableUser . " as u");
        $this->db->join($this->tableGameRouletteResults . " as grr", "grr.user_id = u.id");
        $this->db->join($this->tableGameRouletteQuestions . " as grqu", "grqu.id = grr.question_id");
        $this->db->join($this->tableGameRouletteQuiz . " as grq", "grq.id = grqu.quiz_id");
        $this->db->where("u.business_id", $data["business_id"]);
        $this->db->where("grq.active  = 1");
        // $this->db->where("u.es_prueba", 0);
        $this->validacion_roles();
        // if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
        $this->db->where('date_format(grr.created_at,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(grr.created_at,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"');
        // }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->group_by("u.id,game_id");
        $this->validacion_limite($data["limite"]);
        $result2 = $this->db->get()->result_array();

        $this->db->select("u.id, concat(u.name,' ',u.last_name) as name, if(gp.id is null, 0, gp.id) as game_id, if(gp.history is null, 0, gp.history) as descripcion,'Juegos Profiler' as categoria");
        $this->db->from($this->tableUser . " as u");
        $this->db->join($this->tableGameProfilerResult . " as gpr", "gpr.user_id = u.id");
        $this->db->join($this->tableGameProfilerQuestion . " as gpq", "gpq.id = gpr.question_id");
        $this->db->join($this->tableGameProfiler . " as gp", "gp.id = gpq.quiz_id");
        $this->db->where("u.business_id", $data["business_id"]);
        // $this->db->where("u.es_prueba", 0);
        $this->validacion_roles();
        // if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
        $this->db->where('date_format(gpr.created_at,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(gpr.created_at,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"');
        // }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->group_by("u.id,game_id");
        $this->validacion_limite($data["limite"]);
        $result4 = $this->db->get()->result_array();

        $this->db->select("u.id, concat(u.name,' ',u.last_name) as name, if(gssag.id is null, 0,gssag.id) as game_id, if(gssag.game_name is null, 0, gssag.game_name) as descripcion,'Juegos Serpientes y Escaleras' as categoria");
        $this->db->from($this->tableUser . " as u");
        $this->db->join($this->tablaGameSnakeStairsMembers . " as gssm", "gssm.user_id = u.id");
        $this->db->join($this->tableGameSnakeStairsActiveGame . " as gssag", "gssag.id = gssm.game_id");
        $this->db->where("u.business_id", $data["business_id"]);
        // $this->db->where("u.es_prueba", 0);
        $this->validacion_roles();
        // if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
        $this->db->where('date_format(gssm.created_at,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(gssm.created_at,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"');
        // }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->group_by("u.id,game_id");
        $this->validacion_limite($data["limite"]);
        $result3 = $this->db->get()->result_array();

        return array_merge($result1, $result2, $result3, $result4, $result_run, $result_retos, $result_ahorcado);
    }

    public function ObtenerReporteComunidadDeAprendizaje($data)
    {
        $this->db->select("u.id, concat(u.name,' ',u.last_name) as name, ct.id as id_topic, ct.name as name_topic, count(*) as numero_mensajes");
        $this->db->from($this->tableUser . " as u");
        $this->db->join($this->tableComMessages . " as cm", "cm.id_user = u.id");
        $this->db->join($this->tableComTopics . " as ct", "ct.id = cm.id_topic");
        $this->db->where("u.business_id", $data["business_id"]);
        $this->db->where("ct.capacitacion_obligatoria", 0);
        $this->validacion_roles();
        // $this->db->where("u.es_prueba", 0);
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            $this->db->where('date_format(cm.date,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(cm.date,"%Y-%m-$d") <= "' . $data["fecha_fin"] . '"');
        }
        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'null') {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->group_by("cm.id_topic,cm.id"); //,cm.id_user
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerUsuariosInvitadosComunidad($data)
    {
        $this->db->select("*");
        $this->db->from($this->tableComUserTopics . " as ut");
        $this->db->join($this->tableUser . " as u", "u.id = ut.id_user");
        $this->db->where("u.business_id", $data["business_id"]);
        $this->validacion_roles();
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerReporteParticipacionEnCuestionarios($data)
    {
        $this->db->select("qq.id, qq.name,'general' as category_name, concat(u.name,' ',u.last_name) as user_name,u.id as user_id,qq.category_id, count(*) as num");
        $this->db->from($this->question_quiz . " as qq");
        $this->db->join($this->questions . " as q", "q.quiz_id = qq.id");
        $this->db->join($this->question_answer_user . " as qau", "qau.question_id = q.id", "left");
        $this->db->join($this->tableUser . " as u", "u.id = qau.user_id");
        // $this->db->join($this->question_categories . " as qc", "qc.id = qq.category_id ", "left");
        $this->db->where("qq.business_id", $data["business_id"]);
        $this->db->where("u.business_id", $data["business_id"]);
        $this->db->where("qq.active = 1");
        // $this->db->where("u.es_prueba", 0);
        $this->db->where("qq.category_id = 1");
        $this->db->where("u.active = 1");
        $this->db->where("qq.capacitacion_obligatoria", 0);
        $this->validacion_roles();
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            $this->db->where('date(qau.date) between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        // group by u.id, qq.id
        $this->db->group_by("qq.id, u.id");
        $this->db->order_by("qq.id");
        $this->validacion_limite($data["limite"]);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerReporteDeUsoLibrary($data)
    {
        $this->db->select("if(lu.veces_visto is null, 0, lu.veces_visto) as veces_visto,if(lu.library_element_id is null, 0,lu.library_element_id) as library_id,u.id, concat(u.name,' ',u.last_name) as name");
        $this->db->from($this->tableLibraryUsage . " as lu");
        $this->db->join("library_elements_ as le", "le.id = lu.library_element_id");
        $this->db->join($this->tableUser . " as u", "u.id = lu.user_id", "right");
        if ($data["group_id"] !== null) {
            $this->db->join($this->tableUsersGroups . " as ug", "ug.user_id = u.id");
            $this->db->where('ug.group_id', $data["group_id"]);
        }
        $this->db->where('u.business_id', $data["business_id"]);
        if ($data["nombre_usuario"] !== null) {
            $this->db->where('concat(u.name," ",u.last_name) like', "%" . $data["nombre_usuario"] . "%");
        }
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            $this->db->where('lu.fecha between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        $this->db->where("le.capacitacion_obligatoria", 0);
        $this->validacion_roles();
        $this->validacion_limite($data["limite"]);
        return $this->db->get()->result_array();
    }

    public function ObtenerPromedioDeAmbienteLaboral($data)
    {
        $this->db->select("cast(avg(answer) as decimal(7,0)) as promedio, count(*) as num_respuestas");
        $this->db->from("question_answer_users as qau");
        $this->db->join("user as u", "u.id = qau.user_id");
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            // $this->db->where("date between '" . $data["fecha_inicio"] . "' and '" . $data["fecha_fin"] . "'");
            $this->db->where("date_format(date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'");
        }
        $this->db->where("question_id = 116");
        // $this->db->where("u.es_prueba = 0");
        $this->db->where("business_id", $data["business_id"]);
        $this->validacion_roles();
        $this->db->group_by("question_id");
        $this->validacion_limite($data["limite"]);
        return $this->db->get()->result_array();
    }

    //consulta string
    public function ObtenerUsuariosMasActivosLibrary($data)
    {
        $query = "select concat(u.name, ' ',u.last_name) as name, sum(l.veces_visto) as numero_vistas
              from library_usage as l
              join library_elements_ as le on le.id = l.library_element_id
              join user as u on u.id = l.user_id
              where u.business_id = " . $data["business_id"] .
            " and u.active = 1 
            and le.capacitacion_obligatoria = 0
            and le.active = 1 " . $this->validacion_roles_string() . "
            and date_format(l.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(l.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
              group by u.id
              order by numero_vistas desc " . $this->validacion_limite_string($data["limite"]) . "";
        return $this->db->query($query)->result_array();
    }

    //consulta string
    public function ObtenerTipoMasConsumidoLibrary($data)
    {
        $query = '
        select (if((le.type = "video"),if((le.type_video = "servidor"),".mp4",le.type_video),if(substring(le.file,-4,4) != "",(substring(le.file,-4,4)),"link"))) as tipo, sum(lu.veces_visto) as numero_vistas
        from library_usage as lu
        join library_elements_ as le on le.id = lu.library_element_id
        join user as u on u.id = lu.user_id
        where le.business_id = ' . $data["business_id"] . '
        and le.capacitacion_obligatoria = 0
        and le.active = 1  ' . $this->validacion_roles_string() . '
        and date_format(lu.fecha,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(lu.fecha,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        group by tipo
        order by numero_vistas desc ' . $this->validacion_limite_string($data["limite"]) . '
        ';
        return $this->db->query($query)->result_array();
    }

    //consulta string
    public function diasMasConsumoLibrary($data)
    {
        $query = '
        select date_format(lu.fecha,"%Y-%m-%d") as fecha_, sum(lu.veces_visto) as numero_vistas
        from library_usage as lu
        join library_elements_ as le on le.id = lu.library_element_id
        join user as u on u.id = lu.user_id
        where le.business_id = ' . $data["business_id"] . '
        and le.capacitacion_obligatoria = 0
        and le.active = 1  ' . $this->validacion_roles_string() . '
        and date_format(lu.fecha,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(lu.fecha,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        group by fecha_
        order by numero_vistas desc
      
        ';
        return $this->db->query($query)->result_array();
    }

    //consulta string
    public function ObtenerComunidadesMasActivas($data)
    {
        $query = "
        select ct.name, count(*) as numero_mensajes
        from com_messages as cm
        join com_topics as ct on ct.id = cm.id_topic
        join user as u on u.id = ct.id_user
        where u.business_id = " . $data["business_id"] . " " . $this->validacion_roles_string() . "
        and ct.capacitacion_obligatoria = 0
        and date_format(cm.date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(cm.date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        group by ct.id
        order by numero_mensajes desc
        " . $this->validacion_limite_string($data["limite"]) . "
        ";
        return $this->db->query($query)->result_array();
    }

    public function obtener_likes_comunidades($data)
    {
        $query = "
        select cm.message, count(*) as numero_likes
        from com_messages as cm
        join com_message_like as cml on cml.message_id = cm.id
        join com_topics as ct on ct.id = cm.id_topic 
        join user as u on u.id = cm.id_user
        where u.business_id = " . $data["business_id"] . " " . $this->validacion_roles_string() . "
        and ct.capacitacion_obligatoria = 0
        and date_format(cm.date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(cm.date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        group by cm.id
        order by numero_likes desc
        " . $this->validacion_limite_string($data["limite"]) . "
        ";
        return $this->db->query($query)->result_array();
    }

    //consulta string
    public function ObtenerUsuariosMasActivosComunidad($data)
    {
        $query = "
        select concat(u.name,' ',u.last_name) as name, count(*) as numero_mensajes 
        from com_messages as cm
        join com_topics as ct on ct.id = cm.id_topic
        join user as u on u.id = cm.id_user
        where u.business_id = " . $data["business_id"] . "
        and ct.capacitacion_obligatoria = 0
         " . $this->validacion_roles_string() . "
        and date_format(cm.date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(cm.date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        group by u.id
        order by numero_mensajes desc
        " . $this->validacion_limite_string($data["limite"]) . "
        ";
        return $this->db->query($query)->result_array();
    }

    //consulta string
    public function ObtenerDiasMasActivosComunidad($data)
    {
        $query = "
        select date_format(cm.date,'%Y-%m-%d') as fecha, count(*) as numero_mensajes
        from com_messages as cm
        join com_topics as ct on ct.id = cm.id_topic
        join user as u on u.id = cm.id_user
        where u.business_id = " . $data["business_id"] . "
        and ct.capacitacion_obligatoria = 0
         " . $this->validacion_roles_string() . "
        and date_format(cm.date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(cm.date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        group by fecha
        order by fecha
        
        ";
        return $this->db->query($query)->result_array();
    }

    //consulta strign
    public function ObtenerUsuariosMasLikesMuro($data)
    {
        $query = "
        select concat(u.name,' ',u.last_name) as name, count(*) as numero_likes
        from wall_post_like as wl
        join wall as w on w.id = wl.post_id
        join user as u on u.id = wl.user_id
        where u.business_id = " . $data["business_id"] . "
      
        and w.active = 1 " . $this->validacion_roles_string() . "
        and date_format(wl.created_at,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(wl.created_at,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        group by wl.id
        order by numero_likes desc
        " . $this->validacion_limite_string($data["limite"]) . "
        ";
        return $this->db->query($query)->result_array();
    }

    public function ObtenerPostMasActivos($data)
    {
        $query = "
        select w.wall_description, sum(if(wp.id is null,0,1)) as numero_likes
        from wall as w        
        join wall_post_like as wp on wp.post_id = w.id
        join user as u on u.id = wp.user_id
        where w.business_id = " . $data["business_id"] . " and w.active = 1
        and date_format(wp.created_at,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(wp.created_at,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        " . $this->validacion_roles_string() . "
        group by w.id
        ";
        $likes =  $this->db->query($query)->result_array();
        $query = "
        select w.wall_description, sum(if(wc.id is null,0,1)) as numero_likes
        from wall as w
        join wall_comments as wc on wc.post_id = w.id
        join user as u on u.id = wc.user_id
        where w.business_id = " . $data["business_id"] . " and w.active = 1
        and date_format(wc.created_at,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(wc.created_at,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        " . $this->validacion_roles_string() . "
        group by w.id
        ";
        $comentarios = $this->db->query($query)->result_array();
        $muro = [];
        if (count($likes) > 0) {
            for ($i = 0; $i < count($likes); $i++) {
                $indice = -1;
                for ($j = 0; $j < count($comentarios); $j++) {
                    if ($likes[$i]["wall_description"] == $comentarios[$j]["wall_description"]) {
                        $indice = $j;
                    }
                }
                if ($indice > -1) {
                    $obj = [];
                    $obj["wall_description"] = $likes[$i]["wall_description"];
                    // $_likes = $likes[$i]["numero_likes"] ? 
                    $obj["numero_likes"] = $likes[$i]["numero_likes"] + $comentarios[$indice]["numero_likes"];
                    // $obj["numero_comentarios"] = $comentarios[$indice]["numero_comentarios"];
                    array_push($muro, $obj);
                } else {
                    $obj = [];
                    $obj["wall_description"] = $likes[$i]["wall_description"];
                    $obj["numero_likes"] = $likes[$i]["numero_likes"];
                    array_push($muro, $obj);
                }
            }
        } else {
            $muro = $comentarios;
        }
        // echo json_encode($muro);
        return $muro;
    }

    public function ObtenerUsuariosNumUsuariosTotales($data)
    {
        $this->db->select("count(*) as numero_usuarios");
        $this->db->from("user as u");
        $this->db->join("invitation as i", "i.number_employee = u.number_employee", "left");
        $this->db->where("u.business_id", $data["business_id"]);
        $this->db->where("u.active", 1);
        $this->db->where("u.password !=", 123);
        // $this->db->where("u.es_prueba", 0);
        $this->validacion_roles();
        $this->db->where("(i.status = 1 or i.status is null)", null, false);
        return $this->db->get()->result_array()[0]["numero_usuarios"];
    }

    public function ObtenerUsuariosNumUsuariosTotalesPre($data)
    {
        $this->db->select("count(*) as numero_usuarios");
        $this->db->from("user as u");
        $this->db->join("invitation as i", "i.number_employee = u.number_employee");
        $this->db->where("u.business_id", $data["business_id"]);
        $this->db->where("u.active", 1);
        $this->db->where("u.es_prueba", 0);
        $this->validacion_roles();
        $this->db->where("i.status", 0);
        return $this->db->get()->result_array()[0]["numero_usuarios"];
    }

    //consulta string
    public function ObtenerNumUsuariosNuevos($data)
    {
        $query = 'select count(*) as usuarios_nuevos from (select count(*) as usuarios_nuevos from user as u 
                  join historial_sesiones as hs on hs.id_user = u.id
                  join historial_sesiones as hs1 on hs1.id_user = u.id
                  where u.business_id = ' . $data["business_id"] . '
                   ' . $this->validacion_roles_string() . '
                  and date_format(hs.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(hs.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
                  and hs1.id_user not in 
                  (
                      select id_user from historial_sesiones where u.business_id = ' . $data["business_id"] . '
                      and date_format(fecha_login,"%Y-%m-%d") >= "2020-07-26" and date_format(fecha_login,"%Y-%m-%d") <= "' . $data["fecha_inicio"] . '"
                  )
                  group by u.id
                  ) as tabla';
        return $this->db->query($query)->result_array()[0]["usuarios_nuevos"];
    }

    //consulta string
    public function ObtenerNumVisitantesRegreso($data)
    {
        $query = 'select count(*) usuarios_regreso from (
            select count(*) as usuarios_regreso
            from historial_sesiones as hs
            join user as u on u.id = hs.id_user
            join historial_sesiones as hs1 on hs.id_user = hs1.id_user
            where u.business_id = ' . $data["business_id"] . '
             ' . $this->validacion_roles_string() . '
            and hs.fecha_login between "2020-07-26" and "' . $data["fecha_inicio"] . '"
            and date_format(hs1.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(hs1.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
            group by u.id
             )  as tablA';
        return $this->db->query($query)->result_array()[0]["usuarios_regreso"];
    }

    //consulta string
    public function ObtenerNumSesionesActivas($data)
    {
        $where = ' and (tipo != "PostmanRun" and tipo != "web" or tipo is null) ';
        $query = 'select sum(sesiones_activas) as sesiones_activas from(
            select count(*) as sesiones_activas from historial_sesiones as hs
            join user as u on u.id = hs.id_user
            where u.business_id = ' . $data["business_id"] . '
             ' . $this->validacion_roles_string() . '
            and date_format(hs.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(hs.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
            and hs.fecha_logout is null
            ' . $where . '
            group by hs.id_user
              ) as tabla
            ';
        $result = [];
        $result["sesiones_activas"] = $this->db->query($query)->result_array()[0]["sesiones_activas"];
        $where = " and (tipo = 'Android' or tipo is null)";
        $query = 'select sum(sesiones_activas) as sesiones_activas from(
            select count(*) as sesiones_activas from historial_sesiones as hs
            join user as u on u.id = hs.id_user
            where u.business_id = ' . $data["business_id"] . '
             ' . $this->validacion_roles_string() . '
            and date_format(hs.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(hs.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
            and hs.fecha_logout is null
            ' . $where . '
            group by hs.id_user
              ) as tabla
            ';
        $result["sesiones_android"] = $this->db->query($query)->result_array()[0]["sesiones_activas"];
        $where = " and tipo = 'Nuup/4 CFN' ";
        $query = 'select sum(sesiones_activas) as sesiones_activas from(
            select count(*) as sesiones_activas from historial_sesiones as hs
            join user as u on u.id = hs.id_user
            where u.business_id = ' . $data["business_id"] . '
             ' . $this->validacion_roles_string() . '
            and date_format(hs.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(hs.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
            and hs.fecha_logout is null
            ' . $where . '
            group by hs.id_user
              ) as tabla
            ';
        $result["sesiones_ios"] = $this->db->query($query)->result_array()[0]["sesiones_activas"];
        $where = " and tipo = 'web' ";
        $query = 'select sum(sesiones_activas) as sesiones_activas from(
            select count(*) as sesiones_activas from historial_sesiones as hs
            join user as u on u.id = hs.id_user
            where u.business_id = ' . $data["business_id"] . '
             ' . $this->validacion_roles_string() . '
            and date_format(hs.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(hs.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
            and hs.fecha_logout is null
            ' . $where . '
            group by hs.id_user
              ) as tabla
            ';
        // $result["sesiones_web"] = $this->db->query($query)->result_array()[0]["sesiones_activas"];
        return $result;
    }

    //consulta string
    public function ObtenerNumSesionesPorUsuario($data)
    {
        $where = ' and (tipo != "PostmanRun" and tipo != "web" or tipo is null)';
        $query = 'select avg(sesiones) as promedio from (
            select count(*) as sesiones from historial_sesiones as h
            join user as u on u.id = h.id_user
            where u.business_id = ' . $data["business_id"] . '
             ' . $this->validacion_roles_string() . '
            and date_format(h.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(h.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
            ' . $where . '
            group by u.id 
              ) as tabla';
        $result = [];
        $result["promedio_sesiones"] = $this->db->query($query)->result_array()[0]["promedio"];
        $where = " and (tipo = 'Android' or tipo is null)";
        $query = 'select avg(sesiones) as promedio from (
            select count(*) as sesiones from historial_sesiones as h
            join user as u on u.id = h.id_user
            where u.business_id = ' . $data["business_id"] . '
             ' . $this->validacion_roles_string() . '
            and date_format(h.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(h.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
            ' . $where . '
            group by u.id 
              ) as tabla';
        $r = $this->db->query($query)->result_array();
        $result["promedio_android"] = count($r) > 0 ? $r[0]["promedio"] : 0;
        $where = " and tipo = 'Nuup/4 CFN' ";
        $query = 'select avg(sesiones) as promedio from (
            select count(*) as sesiones from historial_sesiones as h
            join user as u on u.id = h.id_user
            where u.business_id = ' . $data["business_id"] . '
             ' . $this->validacion_roles_string() . '
            and date_format(h.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(h.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
            ' . $where . '
            group by u.id 
              ) as tabla';
        $r = $this->db->query($query)->result_array();
        $result["promedio_ios"] = count($r) > 0 ? $r[0]["promedio"] : 0;
        $where = " and tipo = 'web' ";
        $query = 'select avg(sesiones) as promedio from (
            select count(*) as sesiones from historial_sesiones as h
            join user as u on u.id = h.id_user
            where u.business_id = ' . $data["business_id"] . '
             ' . $this->validacion_roles_string() . '
            and date_format(h.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(h.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
            ' . $where . '
            group by u.id 
              ) as tabla';
        // $result["promedio_web"] = $this->db->query($query)->result_array()[0]["promedio"];
        return $result;
    }

    //consulta string
    public function ObtenerDuracionSesion($data)
    {
        $where = ' and tipo != "PostmanRun" and tipo != "web"';
        $query = 'select avg(tiempo_sesion) as promedio_sesion from (
            select TIMESTAMPDIFF(minute,h.fecha_login, h.fecha_logout) as tiempo_sesion
            from historial_sesiones as h
            join user as u on u.id = h.id_user
            where u.business_id = ' . $data["business_id"] . '
             ' . $this->validacion_roles_string() . '
            and h.fecha_logout is not null
            and date_format(h.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(h.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
            ' . $where . '
            ) as tabla';
        $result = [];
        $result["duracion_sesion"] = $this->db->query($query)->result_array()[0]["promedio_sesion"];
        $where = ' and (tipo = "Android" or tipo is null)';
        $query = 'select avg(tiempo_sesion) as promedio_sesion from (
            select TIMESTAMPDIFF(minute,h.fecha_login, h.fecha_logout) as tiempo_sesion
            from historial_sesiones as h
            join user as u on u.id = h.id_user
            where u.business_id = ' . $data["business_id"] . '
             ' . $this->validacion_roles_string() . '
            and h.fecha_logout is not null
            and date_format(h.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(h.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
            ' . $where . '
            ) as tabla';
        $result["duracion_android"] = $this->db->query($query)->result_array()[0]["promedio_sesion"];
        $where = ' and tipo = "Nuup/4 CFN"';
        $query = 'select avg(tiempo_sesion) as promedio_sesion from (
            select TIMESTAMPDIFF(minute,h.fecha_login, h.fecha_logout) as tiempo_sesion
            from historial_sesiones as h
            join user as u on u.id = h.id_user
            where u.business_id = ' . $data["business_id"] . '
            ' . $this->validacion_roles_string() . '
            and h.fecha_logout is not null
            and date_format(h.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(h.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
            ' . $where . '
            ) as tabla';
        $result["duracion_ios"] = $this->db->query($query)->result_array()[0]["promedio_sesion"];
        $where = ' and tipo = "web"';
        $query = 'select avg(tiempo_sesion) as promedio_sesion from (
            select TIMESTAMPDIFF(minute,h.fecha_login, h.fecha_logout) as tiempo_sesion
            from historial_sesiones as h
            join user as u on u.id = h.id_user
            where u.business_id = ' . $data["business_id"] . '
             ' . $this->validacion_roles_string() . '
            and h.fecha_logout is not null
            and date_format(h.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(h.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
            ' . $where . '
            ) as tabla';
        // $result["duracion_web"] = $this->db->query($query)->result_array()[0]["promedio_sesion"];
        return $result;
    }

    //consulta string
    public function ObtenerGraficaSesionesActivas($data)
    {
        $query = '
        select date_format(hs.fecha_login,"%Y-%m-%d") as fecha, count(*) as sesiones_activas from historial_sesiones as hs
        join user as u on u.id = hs.id_user
        where u.business_id = ' . $data["business_id"] . '
         ' . $this->validacion_roles_string() . '
        and date_format(hs.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(hs.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        and hs.fecha_logout is null
        and hs.tipo != "PostmanRun" and tipo != "web"
        group by fecha
        ';
        $result = [];
        $resultado = $this->db->query($query)->result_array();
        if (count($resultado) > 0)
            $result["totales"] = $resultado;
        else
            $result["totales"] = [];
        $query = '
        select date_format(hs.fecha_login,"%Y-%m-%d") as fecha, count(*) as sesiones_activas from historial_sesiones as hs
        join user as u on u.id = hs.id_user
        where u.business_id = ' . $data["business_id"] . '
         ' . $this->validacion_roles_string() . '
        and date_format(hs.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(hs.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        and hs.fecha_logout is null
        and (hs.tipo = "Android" or tipo is null)
        group by fecha
        ';
        $resultado = $this->db->query($query)->result_array();
        if (count($resultado) > 0)
            $result["android"] = $resultado;
        else
            $result["android"] = [];
        $query = '
        select date_format(hs.fecha_login,"%Y-%m-%d") as fecha, count(*) as sesiones_activas from historial_sesiones as hs
        join user as u on u.id = hs.id_user
        where u.business_id = ' . $data["business_id"] . '
         ' . $this->validacion_roles_string() . '
        and date_format(hs.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(hs.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        and hs.fecha_logout is null
        and hs.tipo = "Nuup/4 CFN"
        group by fecha
        ';
        $resultado = $this->db->query($query)->result_array();
        if (count($resultado) > 0)
            $result["ios"] = $resultado;
        else
            $result["ios"] = [];
        $query = '
        select date_format(hs.fecha_login,"%Y-%m-%d") as fecha, count(*) as sesiones_activas from historial_sesiones as hs
        join user as u on u.id = hs.id_user
        where u.business_id = ' . $data["business_id"] . '
         ' . $this->validacion_roles_string() . '
        and date_format(hs.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(hs.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        and hs.fecha_logout is null
        and hs.tipo = "web"
        group by fecha
        ';
        $resultado = $this->db->query($query)->result_array();
        // if (count($resultado) > 0)
        //     // $result["web"] = $resultado;
        // else
        $result["web"] = [];
        return $result;
    }

    //consulta string
    public function ObtenerGraficaSesionesPorUsuario($data)
    {
        $query = '
        select date_format(h.fecha_login,"%Y-%m-%d")as fecha, count(*) as sesiones
        from historial_sesiones as h
        join user as u on u.id = h.id_user
        where u.business_id = ' . $data["business_id"] . '
         ' . $this->validacion_roles_string() . '
        and date_format(h.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(h.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        and tipo != "PostmanRun" and tipo != "web"
        group by fecha
        ';
        $result = [];
        $resultado = $this->db->query($query)->result_array();
        if (count($resultado) > 0)
            $result["total"] = $resultado;
        else
            $result["total"] = [];
        $query = '
        select date_format(h.fecha_login,"%Y-%m-%d")as fecha, count(*) as sesiones
        from historial_sesiones as h
        join user as u on u.id = h.id_user
        where u.business_id = ' . $data["business_id"] . '
         ' . $this->validacion_roles_string() . '
        and date_format(h.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(h.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        and (tipo = "Android" or tipo is null)
        group by fecha 
        ';
        $resultado = $this->db->query($query)->result_array();
        if (count($resultado) > 0)
            $result["android"] = $resultado;
        else
            $result["android"] = [];
        $query = '
        select date_format(h.fecha_login,"%Y-%m-%d")as fecha, count(*) as sesiones
        from historial_sesiones as h
        join user as u on u.id = h.id_user
        where u.business_id = ' . $data["business_id"] . '
         ' . $this->validacion_roles_string() . '
        and date_format(h.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(h.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        and tipo = "Nuup/4 CFN"
        group by fecha
        ';
        $resultado = $this->db->query($query)->result_array();
        if (count($resultado) > 0)
            $result["ios"] = $resultado;
        else
            $result["ios"] = [];
        $query = '
        select date_format(h.fecha_login,"%Y-%m-%d")as fecha, count(*) as sesiones
        from historial_sesiones as h
        join user as u on u.id = h.id_user
        where u.business_id = ' . $data["business_id"] . '
         ' . $this->validacion_roles_string() . '
        and date_format(h.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(h.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        and tipo = "web"
        group by fecha
        ';
        $resultado = $this->db->query($query)->result_array();
        // if (count($resultado) > 0)
        //     $result["web"] = $resultado;
        // else
        $result["web"] = [];
        return $result;
    }

    //consulta string
    public function ObtenerGraficaDuracionSesion($data)
    {
        $result = [];
        $query = '
        select date_format(h.fecha_login,"%Y-%m-%d") as fecha,sum(TIMESTAMPDIFF(minute,h.fecha_login, h.fecha_logout)) as tiempo_sesion
        from historial_sesiones as h
        join user as u on u.id = h.id_user
        where u.business_id = ' . $data["business_id"] . '
         ' . $this->validacion_roles_string() . '
        and h.fecha_logout is not null
        and date_format(h.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(h.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        and tipo != "PostmanRun" and tipo != "web"
        group by fecha
        ';
        $resultado = $this->db->query($query)->result_array();
        if (count($resultado) > 0)
            $result["total"] = $resultado;
        else
            $result["total"] = [];
        $query = '
        select date_format(h.fecha_login,"%Y-%m-%d") as fecha,sum(TIMESTAMPDIFF(minute,h.fecha_login, h.fecha_logout)) as tiempo_sesion
        from historial_sesiones as h
        join user as u on u.id = h.id_user
        where u.business_id = ' . $data["business_id"] . '
         ' . $this->validacion_roles_string() . '
        and h.fecha_logout is not null
        and date_format(h.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(h.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        and (tipo = "Android" or tipo is null)
        group by fecha
        ';
        $resultado = $this->db->query($query)->result_array();
        if (count($resultado) > 0)
            $result["android"] = $resultado;
        else
            $result["android"] = [];
        $query = '
        select date_format(h.fecha_login,"%Y-%m-%d") as fecha,sum(TIMESTAMPDIFF(minute,h.fecha_login, h.fecha_logout)) as tiempo_sesion
        from historial_sesiones as h
        join user as u on u.id = h.id_user
        where u.business_id = ' . $data["business_id"] . '
         ' . $this->validacion_roles_string() . '
        and h.fecha_logout is not null
        and date_format(h.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(h.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        and tipo = "Nuup/4 CFN"
        group by fecha
        ';
        $resultado = $this->db->query($query)->result_array();
        if (count($resultado) > 0)
            $result["ios"] = $resultado;
        else
            $result["ios"] = [];
        $query = '
        select date_format(h.fecha_login,"%Y-%m-%d") as fecha,sum(TIMESTAMPDIFF(minute,h.fecha_login, h.fecha_logout)) as tiempo_sesion
        from historial_sesiones as h
        join user as u on u.id = h.id_user
        where u.business_id = ' . $data["business_id"] . '
         ' . $this->validacion_roles_string() . '
        and h.fecha_logout is not null
        and date_format(h.fecha_login,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(h.fecha_login,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        and tipo = "web"
        group by fecha
        ';
        $resultado = $this->db->query($query)->result_array();
        // if (count($resultado) > 0)
        //     $result["web"] = $resultado;
        // else
        $result["web"] = [];
        return $result;
    }

    //consulta string
    public function descargarReporteCuestionarios($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteCuestionarios" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
        (select qq.id, qq.name, u.number_employee,concat(u.name, ' ',u.last_name) as user_name,g.name as grupo ,sum(qau.correcto) / count(q.id)  * 100 as calificacion,date_format(qau.date,'%d-%m-%Y') as fecha
        from question_quiz as qq
        join questions as q on q.quiz_id = qq.id
        join question_answer_users as qau on qau.question_id = q.id
        join user as u on u.id = qau.user_id
        join users_groups as ug on ug.user_id = u.id 
        join groups as g on g.id = ug.group_id
        where qq.business_id = " . $data["business_id"] . " and qq.category_id = 1 and u.id not in (select user_id from historial_calificaciones_cuestionarios)
         " . $this->validacion_roles_string() . "
        and qq.capacitacion_obligatoria = 0
        and date_format(qau.date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(qau.date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        group by qq.id,qau.user_id
         ) 
        union 
        (
        select qq.id,qq.name,u.number_employee,concat(u.name, ' ',u.last_name) as user_name,g.name as grupo ,h.calificacion, date_format(h.fecha,'%d-%m-%Y') as fecha
        from historial_calificaciones_cuestionarios as h
        join user as u on u.id = h.user_id
        join users_groups as ug on ug.user_id = u.id
        join groups as g on g.id = ug.group_id
        join question_quiz as qq on qq.id = h.quiz_id
        where u.business_id = " . $data["business_id"] . "  and u.password != '123' " . $this->validacion_roles_string() . "
          and qq.capacitacion_obligatoria = 0 and date_format(h.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(h.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
          group by qq.id
         )
        ";


        $resultado = $this->db->query($query)->result_array();
        $header = [["ID", "TITULO", "ID_USUARIO", "NOMBRE USUARIO", "PERFIL USUARIO", "CALIFICACIÓN", "FECHA REALIZACIÓN"]];
        $resultado = array_merge($header, $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
        // select qq.id, qq.name, u.number_employee, concat(u.name, ' ', u.last_name) as nombre, j.job_name, qau.date,max(qau.tried) as intento
        // from question_quiz as qq
        // join questions as q on q.quiz_id = qq.id
        // join question_answer_users as qau on qau.question_id  = q.id
        // join user as u on u.id = qau.user_id
        // join jobs as j on j.id = u.job_id
        // where u.business_id = " . $data["business_id"]
        //     . " and u.es_prueba = 0
        //     and qq.category_id = 1 and date_format(qau.date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(qau.date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        // if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'null') {
        //     $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
        // }

        // $query .= " group by u.id, qq.id";

    }

    //consulta string
    public function descargarReporteCuestionariosPreguntas($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteCuestionariosPreguntas" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "select qq.id, qq.name, u.number_employee, concat(u.name, ' ', u.last_name) as nombre, j.job_name, qau.date, sum(qau.tried), q.question, qau.answer ,qau.correcto
        from question_quiz as qq
        join questions as q on q.quiz_id = qq.id
        join question_answer_users as qau on qau.question_id  = q.id
        join user as u on u.id = qau.user_id
        left join jobs as j on j.id = u.job_id
        where u.business_id = " . $data["business_id"]
            . " and qq.capacitacion_obligatoria = 0  " . $this->validacion_roles_string() . "
            and qq.category_id = 1 and date_format(qau.date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(qau.date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";
        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'null') {
            $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
        }
        $query .= " group by u.id, qq.id,q.id";
        $resultado = $this->db->query($query)->result_array();
        $header = [["ID", "TITULO", "ID_USUARIO", "NOMBRE USUARIO", "PERFIL USUARIO", "FECHA REALIZACIÓN", "INTENTOS", "PREGUNTA", "RESPUESTA", "ES CORRECTO"]];
        $resultado = array_merge($header, $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    public function obtener_reporte_likes_mensajes_comunidad($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteLikesComentariosComunidad" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "select ct.id, ct.name, u.number_employee, concat(u.name, ' ',u.last_name) as nombre,j.job_name, date_format(cm.date,'%Y-%m-%d') as fecha, cm.message, count(*) as numero_likes
        from com_messages as cm
        join com_topics as ct on ct.id = cm.id_topic 
        join com_message_like as cml on cml.message_id = cm.id
        join user as u on u.id = cm.id_user
        join jobs as j on j.id = u.job_id
        where u.business_id = " . $data["business_id"] . " " . $this->validacion_roles_string() . "
        and ct.capacitacion_obligatoria = 0
        and date_format(cm.date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(cm.date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        group by cm.id
        order by numero_likes desc
        ";
        $resultado = $this->db->query($query)->result_array();
        $header = [["ID", "TITULO", "ID_USUARIO", "NOMBRE USUARIO", "PERFIL USUARIO", "FECHA REALIZACIÓN", "MENSAJE", "NÚMERO DE LIKES"]];
        $resultado = array_merge($header, $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    public function descargarReporteCuestionarioAmbiente($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteCuestionariosAmbienteLaboral" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
            select
                qq.id,
                qq.name,
                u.number_employee,
                concat(u.name,' ',u.last_name) as nombre,
                j.job_name, 
                qau.date,
                sum(qau.tried),
                q.id,
                q.question,
                qau.answer,
                qau.correcto
            from
                question_quiz as qq
                join questions as q on q.quiz_id = qq.id
                join question_answer_users as qau on qau.question_id = q.id
                left join question_answers qa on qa.question_id = q.id
                join user as u on u.id = qau.user_id
                left join jobs as j on j.id = u.job_id
            where
                qq.capacitacion_obligatoria = 0
                and u.business_id = " . $data['business_id'] . "
                " . $this->validacion_roles_string() . "
                and qq.id = " . $data["quiz_id"] . " 
                and date_format(qau.date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' 
                and date_format(qau.date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";
        if ($data['id_question'] != null && $data['id_question'] != 0) {
            $query .= "and q.id = " . $data['id_question'];
        }
        $query .= " group by qq.id,qq.name,u.number_employee,j.job_name, qau.date,q.id,q.question,qau.answer,qau.correcto";
        $resultado = $this->db->query($query)->result_array();
        $header = [["ID", "TITULO", "ID_USUARIO", "NOMBRE USUARIO", "PERFIL USUARIO", "FECHA REALIZACIÓN", "INTENTOS", "PREGUNTA", "RESPUESTA", "ES CORRECTO"]];
        $resultado = array_merge($header, $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }
    /************************************************************
     * AUTOR: LUIS ANGEL TRUJILLO GONZALEZ
     * FUNCION PARA GENERAR ARCHIVO CSV DE LOS USUARIOS QUE RESPONDIERON UN CUESTIONARIO
     *************************************************************/
    public function descargarReporteCuestionariosPorUsuarios($data)
    {
        $handle = fopen("php://output", "w");
        $resultado = $this->obtener_cuestionarios_reporte($data);
        $header = [['ID', 'CUESTIONARIO', '#EMPLEADO', 'NOMBRE_USUARIO', 'PERFIL', 'FECHA_REALIZACION', 'INTENTOS', 'PREGUNTA', 'RESPUESTA']];
        $datos = array_merge($header, $resultado);
        $xlsx = new SimpleXLSXGen();
        $xlsx->addSheet($datos, 'HOJA1');
        $xlsx->downloadAs('Reporte Cuestionarios' . date('y-m-d') . '.xlsx');
        return;
    }

    //consulta string
    public function obtenerGraficaCapacitacionConsumo($data)
    {
        $where = "";
        if (isset($data["id_capacitacion"])) {
            $where = " and cl.id = " . $data["id_capacitacion"] . " ";
        }
        $query = '
        select cl.name,count(*) as veces_visto
        from capacit_list as cl
        join capacit_completed as cc on cc.id_capacitacion = cl.id
        join user as u on u.id = cc.id_usuario
        where u.business_id = ' . $data["business_id"] . $where . '
         ' . $this->validacion_roles_string() . $this->validacion_usuario_capacitacion($data["tipo"]) . '
        and date_format(cc.fecha,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(cc.fecha,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        group by cl.id
        order by veces_visto desc
        ' . $this->validacion_limite_string($data["limite"]) . '
        ';
        return $this->db->query($query)->result_array();
    }

    //consulta string
    public function obtenerGraficaCapacitacionesTerminadas($data)
    {
        $where = "";
        if (isset($data["id_capacitacion"])) {
            $where = " and cl.id = " . $data["id_capacitacion"] . " ";
        }
        $query = '
        select tabla.id, tabla.name,count(*) as terminados, cccm.total, count(*) / cccm.total * 100 as promedio from (
            select cl.id, cl.name,ccm.number_employee,ccm.user_name,ccm.grupo,cm.numero_elementos, ccm.completados,
            (ccm.completados / cm.numero_elementos) * 100 as promedio, ccm.fecha
            from capacit_list as cl
            join (
                    select cl.id, count(*) as numero_elementos from capacit_list as cl
                  join capacit_detail as cd on cl.id = cd.id_capacitacion
                  where cl.business_id = ' . $data["business_id"] . $where . '
                    group by cl.id
                 ) as cm on cm.id = cl.id
            join (
                  select cl.id,u.number_employee,concat(u.name," ",u.last_name) as user_name,g.name as grupo, count(*) as completados,cc.fecha
                    from capacit_list as cl
                    join capacit_completed as cc on cl.id = cc.id_capacitacion
                    join user as u on u.id = cc.id_usuario
                  join users_groups as ug on ug.user_id = u.id
                    join groups as g on g.id = ug.group_id
                    where cl.business_id = ' . $data["business_id"] . '
                     and u.active = 1 ' . $where . '
                    ' . $this->validacion_roles_string() .  $this->validacion_usuario_capacitacion($data["tipo"]) . '
                  and date_format(cc.fecha,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(cc.fecha,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
                    group by cl.id, cc.id_usuario
                    order by cc.fecha desc
                 ) as ccm on ccm.id = cl.id
            where cl.business_id = ' . $data["business_id"] . $where . ' 
            having promedio = 100
              ) as tabla
              join (
              select cl.id, cl.name, sum(if(u.id is not null,1,0)) + cm.cantidad_grupos as total
            from capacit_list as cl
            left join capacit_users as cu on cu.id_list = cl.id
            left join user as u on cu.id_user = u.id and u.business_id = ' . $data["business_id"] . ' and u.active = 1  and u.password != "123"
            
            left join (select cl.id, count(*) as cantidad_grupos from capacit_list as cl
                  join capacit_groups as cg on cg.capacit_id = cl.id
                  join users_groups as ug on ug.group_id = cg.group_id
            join user as u on ug.user_id = u.id and u.id not in (select cu.id_user from capacit_users as cu where cu.id_list = cl.id) 
            where u.business_id = ' . $data["business_id"] . $where . ' and u.active = 1  and u.password != "123" ' . $this->validacion_roles_string() . $this->validacion_usuario_capacitacion($data["tipo"]) . '
                 group by cl.id) as cm on cm.id = cl.id
                 where cl.business_id = ' . $data["business_id"] . '
            group by cl.id
              ) as cccm on cccm.id = tabla.id
              group by id
              ' . $this->validacion_limite_string($data["limite"]) . '
        ';
        
        return $this->db->query($query)->result_array();
    }

    //consulta string
    public function ObtenerGraficaUsuariosActivosCapacitacion($data)
    {
        $where = "";
        if (isset($data["id_capacitacion"])) {
            $where = " and cl.id = " . $data["id_capacitacion"] . " ";
        }

        $query = '
        select u.name ,count(*) as consumo
        from capacit_list as cl
        join capacit_completed as cc on cc.id_capacitacion = cl.id
        join user as u on u.id = cc.id_usuario
        where u.business_id = ' . $data["business_id"] . $where . '
         ' . $this->validacion_roles_string() .  $this->validacion_usuario_capacitacion($data["tipo"]) . '
        and date_format(cc.fecha,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(cc.fecha,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        group by u.id
        order by consumo desc
        ' . $this->validacion_limite_string($data["limite"]) . '
        ';
        return $this->db->query($query)->result_array();
    }

    //consulta string
    public function ObtenerGraficaDiasConsumoCapacitacion($data)
    {
        $where = "";
        if (isset($data["id_capacitacion"])) {
            $where = " and cl.id = " . $data["id_capacitacion"] . " ";
        }

        $query = '
        select date_format(cc.fecha,"%Y-%m-%d") as fecha_ ,count(*) as consumo
        from capacit_list as cl
        join capacit_completed as cc on cc.id_capacitacion = cl.id
        join user as u on u.id = cc.id_usuario
        where u.business_id = ' . $data["business_id"] . $where . '
         ' . $this->validacion_roles_string() .  $this->validacion_usuario_capacitacion($data["tipo"]) . '
        and date_format(cc.fecha,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(cc.fecha,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        group by fecha_
        
        ';
        return $this->db->query($query)->result_array();
    }

    //consulta string
    public function DescargarReporteCsvCapacitacion($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteCapacitacion" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = '
        select cl.id, cl.name,ccm.number_employee,ccm.user_name,ccm.grupo,cm.numero_elementos, ccm.completados,
        (ccm.completados / cm.numero_elementos) * 100 as promedio, ccm.fecha
        from capacit_list as cl
        join (
            select cl.id, count(*) as numero_elementos from capacit_list as cl
            join capacit_detail as cd on cl.id = cd.id_capacitacion
            where cl.business_id = ' . $data["business_id"] . ' and cl.tipo = 0
            group by cl.id
            ) as cm on cm.id = cl.id
        join (
            select cl.id,u.number_employee,upper(concat(u.name," ",u.last_name)) as user_name,g.name as grupo, count(*) as completados,date_format(cc.fecha,"%d-%m-%Y %H:%i") as fecha
            from capacit_list as cl
            join capacit_completed as cc on cl.id = cc.id_capacitacion
            join user as u on u.id = cc.id_usuario
            join users_groups as ug on ug.user_id = u.id
            join groups as g on g.id = ug.group_id
            where cl.business_id = ' . $data["business_id"] . '
             and u.active = 1 and cl.tipo = 0
            ' . $this->validacion_roles_string() .  $this->validacion_usuario_capacitacion($data["tipo"]) . '
            and date_format(cc.fecha,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(cc.fecha,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
            group by cl.id, cc.id_usuario
            order by cc.fecha desc
            ) as ccm on ccm.id = cl.id
        where cl.business_id = ' . $data["business_id"] . '
        ';
        $resultado = $this->db->query($query)->result_array();
        // echo json_encode($resultado);
        $resultado = array_merge([["TÍTULO CAPACITACIÓN", "CAPACITACIÓN", "ID USUARIO", "NOMBRE USUARIO", "PERFIL", "NÚMERO ELEMENTOS", "COMPLETADOS", "PORCENTAJE AVANCE", "ÚLTIMO AVANCE"]], $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    //consulta string
    public function GraficaCuestionariosMasIntentos($data)
    {
        $query = '
        select qq.name, sum(qau.tried) as numero_intentos
        from question_quiz as qq
        join questions as q on q.quiz_id = qq.id
        join question_answer_users as qau on qau.question_id = q.id
        join user as u on u.id = qau.user_id
        where qq.business_id = ' . $data["business_id"] . '
        and qq.category_id = 1 and qq.capacitacion_obligatoria = 0
       
        ' . $this->validacion_roles_string() . '
        and date_format(qau.date,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(qau.date,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        group by qq.id
        ' . $this->validacion_limite_string($data["limite"]) . '
        ';
        return $this->db->query($query)->result_array();
    }

    //consulta string
    public function GraficaCalificacionCuestionarioLibrary($data)
    {
        $query = '
        select id, name, avg(calificacion) as calificacion from
        (select qq.id, qq.name, qau.answer calificacion
        from question_quiz as qq
        join questions as q on q.quiz_id = qq.id
        join question_answer_users as qau on qau.question_id = q.id
        join user as u on u.id = qau.user_id
        where qq.business_id = ' . $data["business_id"] . ' and qq.category_id = 2
         and qq.capacitacion_obligatoria = 0
        and u.active = 1
        ' . $this->validacion_roles_string() . '
        and qau.id_elemento is not null
        and date_format(qau.date,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(qau.date,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
         ) as tabla
         group by id
         ' . $this->validacion_limite_string($data["limite"]) . '
        ';
        $result = $this->db->query($query)->result_array();;
        return $result;
    }

    //consulta string
    public function ObtenerCantidadUsuariosRespuestaLibrary($data)
    {
        $query = "
        select count(*) as cantidad from question_answer_users as q 
        join user as u on u.id = q.user_id
        where question_id = 56
        and u.business_id = " . $data["business_id"] . "
        " . $this->validacion_roles_string() . "
        and date_format(date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
         and u.active = 1 and q.id_elemento is not null
        order by user_id
        " . $this->validacion_limite_string($data["limite"]) . "
        ";
        $result = $this->db->query($query)->result_array();
        if (count($result) > 0) {
            return $result[0]["cantidad"];
        } else {
            return 0;
        }
    }

    //consulta string
    public function GraficaCalificacionCuestionarios($data)
    {
        $query = '
        select id, name, avg(calificacion) as calificacion from
        (select qq.id, qq.name, sum(qau.correcto) / count(q.id)  * 100 as calificacion
        from question_quiz as qq
        join questions as q on q.quiz_id = qq.id
        join question_answer_users as qau on qau.question_id = q.id
        join user as u on u.id = qau.user_id
        where qq.business_id = ' . $data["business_id"] . ' and qq.category_id = 1
        and qq.capacitacion_obligatoria = 0
        ' . $this->validacion_roles_string() . '
        and date_format(qau.date,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(qau.date,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        group by qq.id,qau.user_id
         ) as tabla
         group by id
         ' . $this->validacion_limite_string($data["limite"]) . '
        ';
        return $this->db->query($query)->result_array();
    }

    //consulta string
    public function GraficaCuestionarioCalificacionFinalPorUsuario($data)
    {
        $query = '
        select concat(u.name," ",u.last_name) as name, qq.id, qq.name as quiz_name, sum(qau.correcto) / count(q.id)  * 100 as calificacion
        from question_quiz as qq
        join questions as q on q.quiz_id = qq.id
        join question_answer_users as qau on qau.question_id = q.id
        join user as u on u.id = qau.user_id
        where qq.business_id = ' . $data["business_id"] . ' and qq.category_id = 1
         and qq.capacitacion_obligatoria  = 0
        ' . $this->validacion_roles_string() . '
        and date_format(qau.date,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(qau.date,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        group by qq.id,qau.user_id
        order by calificacion desc
        ' . $this->validacion_limite_string($data["limite"]) . '
        ';
        return $this->db->query($query)->result_array();
    }

    //consulta string
    public function GraficaCuestionariosDiasMasConsumo($data)
    {
        $query = '
        select fecha_,count(*) as cantidad from(
        select date_format(qau.date,"%Y-%m-%d") as fecha_
        from question_quiz as qq
        join questions as q on q.quiz_id = qq.id
        join question_answer_users as qau on qau.question_id = q.id
        join user as u on u.id = qau.user_id
        where qq.business_id = ' . $data["business_id"] . ' and qq.category_id = 1
         and qq.capacitacion_obligatoria = 0
        ' . $this->validacion_roles_string() . '
        and date_format(qau.date,"%Y-%m-%d") >= "' . $data["fecha_inicio"] . '" and date_format(qau.date,"%Y-%m-%d") <= "' . $data["fecha_fin"] . '"
        group by qau.user_id
        ) as tabla
        group by fecha_
        order by cantidad desc
       
        ';
        return $this->db->query($query)->result_array();
    }

    //consulta string
    public function ObtenerIngresosApp($data, $son_totales)
    {
        $query = "
        select uu.* from uso_usuarios as uu
        join user as u on u.id = uu.user_id
        where u.business_id = " . $data["business_id"] . "
        " . $this->validacion_roles_string() . "
        and date_format(uu.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(uu.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        order by uu.id, u.id
        ";
        $interacciones = $this->db->query($query)->result_array();
        $query = "select u.id, concat(u.name,' ',u.last_name) as name from uso_usuarios as uu
        join user as u on u.id = uu.user_id
        where u.business_id = " . $data["business_id"] . "
        " . $this->validacion_roles_string() . "
        and date_format(uu.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(uu.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        group by user_id
        ";
        $usuarios = $this->db->query($query)->result_array();
        $result = [];
        $fecha_inicio = $data["fecha_inicio"];
        $fecha_fin = date('Y-m-d', strtotime($data["fecha_fin"] . ' +1 day'));
        $j = 0;
        while ($fecha_inicio != $fecha_fin && $j < 100) {
            $j = $j + 1;
            $result[$fecha_inicio] = [];
            for ($i = 0; $i < count($usuarios); $i++) {
                $x = $this->obtenerIngresosPorUsuario($usuarios[$i]["id"], $usuarios[$i]["name"], $interacciones, $fecha_inicio);
                if ($x["ingresos"] > 0)
                    array_push($result[$fecha_inicio], $x);
            }
            $fecha_inicio = date('Y-m-d', strtotime($fecha_inicio . ' +1 day'));
        }
        if ($son_totales) {
            $fecha_inicio = $data["fecha_inicio"];
            $result = $this->obtenerIngresosTotales($result, $fecha_inicio, $fecha_fin);
        }
        return $result;
    }

    public function obtenerIngresosTotales($ingresos, $fecha_inicio, $fecha_fin)
    {
        $j = 0;
        while ($fecha_inicio != $fecha_fin && $j < 100) {
            $j = $j + 1;
            $total = 0;
            // echo json_encode($fecha_inicio);
            // echo json_encode($ingresos[$fecha_inicio]);
            for ($i = 0; $i < count($ingresos[$fecha_inicio]); $i++) {
                $total += $ingresos[$fecha_inicio][$i]["ingresos"];
            }
            $ingresos[$fecha_inicio] = $total;
            $fecha_inicio = date('Y-m-d', strtotime($fecha_inicio . ' +1 day'));
        }
        return $ingresos;
    }

    // public function obtnerTotales(){

    // }

    public function obtenerIngresosPorUsuario($user_id, $name, $interacciones, $fecha)
    {
        $result = [];
        $result["user_id"] = $user_id;
        $result["name"] = $name;
        $result["ingresos"] = 0;
        for ($i = 0; $i < count($interacciones); $i++) {
            $fecha_ = explode(" ", $interacciones[$i]["fecha"])[0];
            if ($interacciones[$i]["user_id"] == $user_id && $fecha == $fecha_) {
                if ($result["ingresos"] == 0) {
                    $result["ingresos"] = 1;
                } else {
                    $start_date = new DateTime($interacciones[$i - 1]["fecha"]);
                    $since_start = $start_date->diff(new DateTime($interacciones[$i]["fecha"]));
                    if ($since_start->i > 30) {
                        $result["ingresos"] += 1;
                    }
                }
            }
        }
        return $result;
    }

    //consulta string
    public function obtenerInteraccionesTotalesPorUsuario($data)
    {
        $query = "
        select u.id, concat(u.name, ' ',u.last_name) as name,date_format(uu.fecha,'%Y-%m-%d') as fecha_
        , count(*) as interacciones
        from uso_usuarios as uu
        join user as u on u.id = uu.user_id
        where u.business_id = " . $data["business_id"] . "
        " . $this->validacion_roles_string() . "
        and date_format(uu.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(uu.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        group by user_id, fecha_
        order by uu.id, u.id
        ";
        $interacciones = $this->db->query($query)->result_array();
        $result = [];
        $fecha_inicio = $data["fecha_inicio"];
        $fecha_fin = date('Y-m-d', strtotime($data["fecha_fin"] . ' +1 day'));
        $j = 0;
        while ($fecha_inicio != $fecha_fin && $j < 100) {
            $j = $j + 1;
            $result[$fecha_inicio] = $this->obtenerRegistrosPorFecha($interacciones, $fecha_inicio);
            $fecha_inicio = date('Y-m-d', strtotime($fecha_inicio . ' +1 day'));
        }
        return $result;
    }

    public function obtenerRegistrosPorFecha($interacciones, $fecha)
    {
        $result = [];
        for ($i = 0; $i < count($interacciones); $i++) {
            if ($interacciones[$i]["fecha_"] == $fecha) {
                array_push($result, $interacciones[$i]);
            }
        }
        return $result;
    }

    //consulta string
    public function obtenerInteraccionesTotales($data)
    {
        $query = "
        select date_format(uu.fecha,'%Y-%m-%d') as fecha_, count(*) as interacciones
        from uso_usuarios as uu
        join user as u on u.id = uu.user_id
        where u.business_id = " . $data["business_id"] . "
        " . $this->validacion_roles_string() . "
        and date_format(uu.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(uu.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        group by fecha_
        order by uu.id, u.id
        ";
        return $this->db->query($query)->result_array();
    }

    public function obtenerUsuariosActivos($data, $tipo)
    {
        $where = "";
        if ($tipo == true) {
            $where = " and u.es_prueba = 0 ";
        } else
            $where = " and u.es_prueba = 2 ";
        $query = "
            select count(*) as cantidad from (
            select count(*) from user as u
            join uso_usuarios as uu on uu.user_id = u.id
            where uu.seccion != 'home' and date_format(uu.fecha,'%Y-%m') >= '" . date("Y-m", strtotime($data["fecha_inicio"])) . "' and date_format(uu.fecha,'%Y-%m') <= '" . date("Y-m", strtotime($data["fecha_fin"])) . "'
            and u.business_id = " . $data["business_id"] . " 
        " . $this->validacion_roles_string() . "
            $where
            group by u.id
            ) as tabla
        ";
        return $this->db->query($query)->result_array();
    }

    public function descargarReporteInteracciones($data, $tipo)
    {
        $where = "";
        if ($tipo == true) {
            $where = " and u.es_prueba = 0 ";
        } else
            $where = " and u.es_prueba = 2 ";
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteDeInteracciones" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
        select * from (
            select u.number_employee, concat(u.name,' ',u.last_name) as name, uu.seccion, date_format(uu.fecha,'%Y-%m-%d')
            from user as u
            join uso_usuarios as uu on uu.user_id = u.id
            where uu.seccion != 'home' and date_format(uu.fecha,'%Y-%m') >= '" . date("Y-m", strtotime($data["fecha_inicio"])) . "' and date_format(uu.fecha,'%Y-%m') <= '" . date("Y-m", strtotime($data["fecha_fin"])) . "'
            and u.business_id = " . $data["business_id"] . " 
        " . $this->validacion_roles_string() . "
            $where
            group by u.id
            ) as tabla
        ";
        $resultado = $this->db->query($query)->result_array();
        // echo json_encode($resultado);
        $resultado = array_merge([["NÚMERO DE EMPLEADO", "NOMBRE", "SECCION", "FECHA"]], $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    public function obtener_reporte_capacitacion_obligatoria($token, $data)
    {
        $business_id = $data["business_id"];
        $id_capacitacion = $data["id_capacitacion"];
        $query = "
        select u.number_employee as numero_empleado, concat(u.name, ' ',u.last_name) as nombre, u.email
        ,qau.answer as respuesta, q.id, date_format(qau.date,'%Y-%m-%d') as fecha, qau.tried as intentos,
        t.id_capacitacion , u.id as user_id, t.id_insignia, r.nombre as region,
        concat(u.name, ' ',u.last_name) as name_complete
        from user as u
        join 
        (
              select * from (
            select cg.capacit_id as id_capacitacion, ug.user_id as user_id, cl.id_insignia from capacit_groups as cg
            join capacit_list as cl on cl.id = cg.capacit_id
              join users_groups as ug on cg.group_id = ug.group_id
              where cl.tipo = 1 and cl.business_id = ?  and cl.id = ?
              union
              select cu.id_list as id_capacitacion,cu.id_user as user_id, cl.id_insignia from capacit_users as cu
            join capacit_list as cl on cl.id = cu.id_list
              where cl.tipo = 1 and cl.business_id = ?  and cl.id = ?
            ) as tabla
              group by user_id
        ) as t on t.user_id = u.id
        join capacit_detail as cd on cd.id_capacitacion = t.id_capacitacion and cd.catalog = 4
        join question_quiz as qq on qq.id = cd.id_elemento
        join questions as q on q.points = 0 and q.quiz_id = qq.id
        join question_answer_users as qau on qau.question_id = q.id and qau.user_id = u.id
        join regiones as r on r.id = u.id_region
        where u.business_id = ?
        " . $this->validacion_roles_string() . "
        and date_format(qau.date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(qau.date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
       
        group by u.id, q.id
        order by u.id, q.id
        
        ";
        $result = $this->db->query($query, array($business_id, $id_capacitacion, $business_id, $id_capacitacion, $business_id))->result_array();

        $id_user_temp = 0;
        $reporte = [];
        $indice = -1;
        for ($i = 0; $i < count($result); $i++) {
            if ($id_user_temp != $result[$i]["user_id"]) {
                if ($i != 0) {
                    //$indice = count($reporte) - 1;
                    $reporte[$indice]["fecha"] = $result[$i - 1]["fecha"];
                    $reporte[$indice]["intentos"] = $result[$i - 1]["intentos"];
                    $reporte[$indice]["puntos"] = $this->obtener_puntos($result[$i - 1]["id_capacitacion"], $result[$i - 1]["user_id"]);
                    if ($reporte[$indice]["puntos"] >= 80)
                        $reporte[$indice]["url"] = $this->obtener_url_diploma($result[$i - 1]["user_id"], $data["business_id"], $result[$i - 1]["name_complete"], $result[$i - 1]["id_insignia"]);
                    else
                        $reporte[$indice]["url"] = "";
                    $reporte[$indice]["estatus"] = $result[$i - 1]["intentos"] == 0 ? "Pendiente" : ($reporte[$indice]["puntos"] >= 80 ? "Aprobado" : "No aprobado");
                }
                $id_user_temp = $result[$i]["user_id"];
                $indice = $indice + 1;
                $obj = [];
                $obj["numero_empleado_appy"] = $result[$i]["numero_empleado"];
                $obj["nombre_appy"] = $result[$i]["nombre"];
                $obj["region"] = $result[$i]["region"];
                $obj["respuesta" . $i] = $result[$i]["respuesta"];
                array_push($reporte, $obj);
            } else {
                $reporte[$indice]["respuesta" . $i] = $result[$i]["respuesta"];
            }
            if ($i == count($result) - 1) {

                $reporte[$indice]["fecha"] = $result[$i]["fecha"];
                $reporte[$indice]["intentos"] = $result[$i]["intentos"];
                $reporte[$indice]["puntos"] = $this->obtener_puntos($result[$i]["id_capacitacion"], $result[$i]["user_id"]);
                if ($reporte[$indice]["puntos"] >= 80)
                    $reporte[$indice]["url"] = $this->obtener_url_diploma($result[$i]["user_id"], $data["business_id"], $result[$i]["name_complete"], $result[$i]["id_insignia"]);
                else
                    $reporte[$indice]["url"] = "";
                $reporte[$indice]["estatus"] = $result[$i]["intentos"] == 0 ? "Pendiente" : ($reporte[$indice]["puntos"] >= 80 ? "Aprobado" : "No aprobado");
            }
        }

        $resultado = array_merge([["ID", "NOMBRE APPY","REGION", "ID COMERCIO", "ID OPERADOR", "NOMBRE", "ÚLTIMA FECHA DE REALIZACIÓN", "NÚM INTENTOS", "ÚLTIMOS PUNTOS OBTENIDOS", "URL", "ESTATUS"]], $reporte);
        // foreach ($resultado as $rows) {
        //     fputcsv($outss, $rows);
        // }
        // fclose($outss);
        return $resultado;
    }

    public function obtener_url_diploma($user_id, $business_id, $name_complete, $id_insignia)
    {
        //esta url es de yastas, para pruebas cambiar a qa-nuup
        $name_complete = $this->urlCleanString($name_complete);
        $url = "https://appy.com.mx/nuup/Ws/obtenerDiploma/$user_id/$business_id/$name_complete/$id_insignia";
        //str_replace(" ", '-', $url); //reemplazamos los espacios en blanco para que no causen error en la url
        //se manda llamar el metodo obtener diploma por http
        $ch = curl_init($url); //se crea un manejador de cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //se indica dentro de las opciones que queremos que regrese la respuesta
        $res = curl_exec($ch); //se ejecuta y se asigna la respuesta a una variable

        $res = json_decode($res, true); //se decodifica como array asosiativo

        return strip_tags(html_entity_decode("<a href='" . $res["data"]["ruta"] . "' >" . $res["data"]["ruta"] . "</a>")); //se devuelve la ruta del diploma (url)
    }

    function urlCleanString($str)
    {
        // Reemplazamos los espacios por guiones (-)
        $str = preg_replace('/\s+/', '-', $str);

        // Reemplazamos la caracteres acentuados
        return str_replace(array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'), array('1a', '1e', '1i', '1o', '1u', '1n', '1A', '1E', '1I', '1O', '1U', '1N'), $str);
    }

    public function obtener_puntos($id_capacitacion, $id_usuario)
    {
        $query = "
        select sum(if(qau.correcto = 1, q.points, 0)) as puntos
        from question_answer_users as qau
        join questions as q on q.id = qau.question_id
        join question_quiz as qq on qq.id = q.quiz_id
        join capacit_detail as cd on cd.id_elemento = qq.id
        where cd.id_capacitacion = $id_capacitacion and qau.user_id = $id_usuario
        ";
        return $this->db->query($query)->result_array()[0]["puntos"];
    }

    public function ObtenerReporteCalificacionesLibrary($data)
    {
        $this->db->select("p.id as library_id, p.title ,pl.user_id, concat(u.name,' ',u.last_name) as name, pl.score");
        $this->db->from("library_elements_" . " as p");
        $this->db->join("library_score" . " as pl", "pl.library_id = p.id");
        $this->db->join($this->tableUser . " as u", "u.id = pl.user_id");
        $this->validacion_roles();
        $this->db->where("p.business_id", $data["business_id"]);
        $this->db->where("p.capacitacion_obligatoria", 0);
        if ($data["fecha_inicio"] !== null && $data["fecha_fin"] !== null) {
            $this->db->where('pl.fecha between "' . $data["fecha_inicio"] . '" and "' . $data["fecha_fin"] . '"');
        }
        if ($data["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"]);
        }
        $this->db->group_by("p.id");
        $this->db->order_by("pl.score");
        $this->validacion_limite($data["limite"]);
        // $this->db->group_by("p.id, user_id");
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function ObtenerUsuariosRegistradosSemanal($data, $tipo, $tipo_fecha)
    {
        $query = "
        select count(*) as cantidad from user as u
        join historial_sesiones as h on h.id =(select h1.id from historial_sesiones as h1
        where h1.id_user = u.id
        and u.business_id = " . $data["business_id"] . " 
        " . $this->validacion_roles_string() . "
        order by h1.fecha_login asc
        limit 1)";
        if ($tipo_fecha == true)
            $query .= " where date_format(h.fecha_login,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(h.fecha_login,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        ";
        else
            $query .= " where date_format(h.fecha_login,'%Y-%m') >= '" . date("Y-m", strtotime($data["fecha_inicio"])) . "' and date_format(h.fecha_login,'%Y-%m') <= '" . date("Y-m", strtotime($data["fecha_fin"])) . "'
        ";
        if ($tipo == true) {
            $query .= " and u.es_prueba = 0";
        } else {
            $query .= " and u.es_prueba = 2 ";
        }
        return $this->db->query($query)->result_array();
    }

    public function DescargarUsuariosRegistradosSemanal($data, $tipo, $tipo_fecha)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteRegistrados" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
        select u.number_employee, concat(u.name, ' ',u.last_name) as name, date_format(h.fecha_login,'%Y-%m-%d') as fecha_registro
        from user as u
        join historial_sesiones as h on h.id =(select h1.id from historial_sesiones as h1
        where h1.id_user = u.id
        and u.business_id = " . $data["business_id"] . " 
        " . $this->validacion_roles_string() . "
        order by h1.fecha_login asc
        limit 1)        ";
        if ($tipo_fecha == true)
            $query .= " where date_format(h.fecha_login,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(h.fecha_login,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        ";
        else
            $query .= " where date_format(h.fecha_login,'%Y-%m') >= '" . date("Y-m", strtotime($data["fecha_inicio"])) . "' and date_format(h.fecha_login,'%Y-%m') <= '" . date("Y-m", strtotime($data["fecha_fin"])) . "'
        ";
        if ($tipo == true) {
            $query .= " and u.es_prueba = 0";
        } else {
            $query .= " and u.es_prueba = 2 ";
        }
        $resultado = $this->db->query($query)->result_array();
        $resultado = array_merge([["NÚMERO DE EMPLEADO", "NOMBRE", "FECHA DE REGISTRO"]], $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    public function DescargarReporteCSVScoreBibliotecaPromedios($data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteScoreBibliotecaPromedio" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
        select le.title, round(avg(ls.score),0) as score
        from library_score as ls
        join library_elements_ as le on le.id = ls.library_id
        where le.active = 1 and le.business_id = " . $data["business_id"] . " 
        and date_format(ls.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(ls.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        group by le.id
        ";
        //echo json_encode($query);

        $resultado = $this->db->query($query)->result_array();
        $resultado = array_merge([["TÍTULO ELEMENTO", "SCORE"]], $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    public function DescargarReporteCSVScoreBiblioteca($data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteScoreBiblioteca" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
        select concat('# ',u.number_employee) as number_employee, concat(u.name, ' ',u.last_name) as name,
        le.title, ls.score
        from user as u
        join library_score as ls on ls.user_id = u.id
        join library_elements_ as le on le.id = ls.library_id
        where le.active = 1 and u.business_id = " . $data["business_id"] . "
        and date_format(ls.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(ls.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        ";
        $resultado = $this->db->query($query)->result_array();
        $resultado = array_merge([["NÚMERO DE EMPLEADO", "NOMBRE", "TÍTULO", "SCORE"]], $resultado);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    public function obtener_grafica_al_like_caras($data)
    {
        $query = "
        select avg(q.answer) as promedio, concat(u.name, ' ', u.last_name) as name
        from question_answer_users as q
        join user as u on u.id = q.user_id
        where question_id = " . $data["id_pregunta"] . " and u.business_id = " . $data["business_id"] . " and u.es_prueba = 0
        and date_format(q.date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(q.date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'
        group by u.id
        order by promedio desc
        limit 20";
        return $this->db->query($query)->result_array();
    }

    public function obtener_total_respuestas($data)
    {
        $query = "
        select 
            count(*) total
        from
            (select	
                qau.id id_qau,
                qq.id id_qq,		
                qq.name,		
                u.number_employee,		
                concat(u.name,' ',u.last_name) as nombre,
                j.job_name,
                qau.date,		
                sum(qau.tried),		
                q.id id_q,		
                q.question,		
                qau.answer,		
                qau.correcto		
            from		
                question_quiz as qq		
                join questions as q on q.quiz_id = qq.id		
                join question_answer_users as qau on qau.question_id = q.id		
                left join question_answers qa on qa.question_id = q.id		
                join user as u on u.id = qau.user_id		
                left join jobs as j on j.id = u.job_id		
            where		
                qq.capacitacion_obligatoria = 0		
                and u.business_id = " . $data['business_id'] . "		
                " . $this->validacion_roles_string() . "                 		
                and q.quiz_id = " . $data['quiz_id'] . " 
                and qau.question_id = " . $data['id_pregunta'] . "
                and date_format(qau.date,'%Y-%m-%d') >= '" . $data['fecha_inicio'] . "' 	
                and date_format(qau.date,'%Y-%m-%d') <= '" . $data['fecha_fin'] . "' 
            group by qau.id, qq.id,qq.name,u.number_employee,concat(u.name,' ',u.last_name),j.job_name, qau.date,
                qau.tried,q.id,q.question,qau.answer,qau.correcto)q1
            ";
        //  echo $query;
        $result = $this->db->query($query)->result_array();
        if (count($result) > 0) {
            return $result[0]["total"];
        } else {
            return 0;
        }
    }

    public function obtener_cuestionarios_reporte($data)
    {
        $query = "
            select
                qq.id as quiz_id,
                qq.name as titulo,
                concat('#',u.number_employee) as num_empleado,
                concat(u.name, ' ', u.last_name) as nombre,
                j.job_name as perfil_usuario,
                qau.date as fecha_realizacion,
                sum(qau.tried) as intentos,
                q.question as pregunta,
                qau.answer as id_respuesta,
                qa.answer as respuesta,
                qau.correcto,
                q.type_id as type_id,
                u.business_id as business_id,
                qa.id as answer_id
            from
                question_quiz as qq
                join questions as q on q.quiz_id = qq.id
                join question_answer_users as qau on qau.question_id = q.id
                left join question_answers as qa on qa.id = qau.answer
                join user as u on u.id = qau.user_id
                left join jobs as j on j.id = u.job_id
            where
                qq.id =" . $data['id'] . "
                and u.id in (" . $data['usuarios'] . ")
            group by
                u.id,
                qq.id,
                q.id,
                qau.date;
        ";
        $result = $this->db->query($query)->result_array();

        $resultado = $this->agrupar_por_cuestionario($result);
        return $resultado;
    }

    function agrupar_por_cuestionario($datos)
    {
        $d = [];
        for ($i = 0; $i < count($datos); $i++) {
            $d[$i]["quiz_id"]           = $datos[$i]["quiz_id"];
            $d[$i]["titulo"]            = $datos[$i]["titulo"];
            $d[$i]["num_empleado"]      = $datos[$i]["num_empleado"];
            $d[$i]["nombre"]            = $datos[$i]["nombre"];
            $d[$i]["perfil_usuario"]    = $datos[$i]["perfil_usuario"];
            $d[$i]["fecha_realizacion"] = $datos[$i]["fecha_realizacion"];
            $d[$i]["intentos"]          = $datos[$i]["intentos"];
            $d[$i]["pregunta"]          = strip_tags(html_entity_decode($datos[$i]["pregunta"]));

            switch ($datos[$i]["type_id"]) {
                case 1:
                    $d[$i]["respuesta"] =  $this->obtener_respuesta_multiple($datos[$i]["id_respuesta"]);
                    break;
                case 2:
                case 4:
                case 6:
                    $d[$i]["respuesta"] =  $this->obtener_respuesta($datos[$i]["id_respuesta"]);
                    break;
                case 7:
                case 10:
                case 11:
                    $d[$i]["respuesta"] = base_url() . "/uploads/business_" . $datos[$i]["business_id"] . "/preguntas/" . $datos[$i]["id_respuesta"];
                    break;
                case 3:
                case 5:
                    $d[$i]["respuesta"] =  $this->obtener_respuesta_multiple_img($datos[$i]["id_respuesta"], $datos[$i]["business_id"]);
                    break;
                case 8:
                case 12:
                    $d[$i]["respuesta"] = $datos[$i]["respuesta"];
                    break;
                case 9:
                case 13:
                case 14:
                    $d[$i]["respuesta"] =  $datos[$i]["id_respuesta"];
                    break;
            }

            // if ($datos[$i]["type_id"] == 8 || $datos[$i]["type_id"] == 13 || $datos[$i]["type_id"] == 14 || $datos[$i]["type_id"] == 12){
            //     $d[$i]["respuesta"] = $datos[$i]["respuesta"];
            // }
            // if ($datos[$i]["type_id"] == 3 || $datos[$i]["type_id"] == 5) {
            //     $d[$i]["respuesta"] =  $this->obtener_respuesta_multiple_img($datos[$i]["id_respuesta"],$datos[$i]["business_id"], true);
            // }
            // if ($datos[$i]["type_id"] == 7 || $datos[$i]["type_id"] == 10 || $datos[$i]["type_id"] == 11) {
            //     $d[$i]["respuesta"] = base_url() . "/uploads/business_".$datos[$i]["business_id"]."/preguntas/". $datos[$i]["id_respuesta"];
            // }
            // if ($datos[$i]["type_id"] == 2 || $datos[$i]["type_id"] == 4 || $datos[$i]["type_id"] == 6 || $datos[$i]["type_id"] == 9) {
            //     $d[$i]["respuesta"] =  $this->obtener_respuesta($datos[$i]["id_respuesta"]);
            // }
            // if ($datos[$i]["type_id"] == 1) {
            //     $d[$i]["respuesta"] =  $this->obtener_respuesta_multiple($datos[$i]["id_respuesta"]);
            // }
        }
        //print_r($d);
        return $d;
    }

    public function obtener_respuesta($id_respuesta)
    {
        $query = "select answer from question_answers where id in ('$id_respuesta')";
        $arr = $this->get_simple_array($this->db->query($query)->result_array());
        return join(', ', $arr);
    }

    public function obtener_respuesta_multiple_img($id_respuesta, $business_id, $band = null)
    {

        $query = "select concat('" . base_url() . "/uploads/business_$business_id/preguntas/',answer) as answer from question_answers where id in ('$id_respuesta')";
        $arr = $this->get_simple_array($this->db->query($query)->result_array(), $band);
        return join(', ', $arr);
    }

    public function obtener_respuesta_multiple($id_respuesta)
    {
        $query = "select answer from question_answers where id in ('$id_respuesta')";
        $arr = $this->get_simple_array($this->db->query($query)->result_array());
        return join(', ', $arr);
    }

    public function get_simple_array($arr, $band = null)
    {
        $result = [];
        $html = '';
        $html_end = '';
        $html = '';
        $html_end = '';
        if ($band == true) {
            $html = "<img height='100' width='100' src='";
            $html_end = "'>";
        }
        for ($k = 0; $k < count($arr); $k++) {
            array_push($result, $html . $arr[$k]["answer"] . $html_end);
        }
        return $result;
    }

    public function ObtenerGraficaJuegos($data)
    {
        $resultado = [];
        $query = "
            select 'Ruleta' as nombre_juego, count(u.number_employee) total
                    from (select distinct question_id, answer_id, user_id, created_at from game_roulette_results) as gr
                    join user as u on u.id = gr.user_id
                    join game_roulette_question_answers as ga on ga.id = gr.answer_id
                    join game_roulette_questions as gqq on gqq.id = gr.question_id
                    join game_roulette_quiz as gq on gqq.quiz_id = gq.id
                    left join jobs as j on j.id = u.job_id
                    where u.business_id"
            . "  " . $this->validacion_roles_string() . "
                    and date_format(gr.created_at,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(gr.created_at,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'null') {
            $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
        }
        $query .= " group by  nombre_juego " . $this->validacion_limite_string($data["limite"]) . "";
        $resultado = array_merge($resultado, $this->db->query($query)->result_array());

        $query = "select 'Run pancho run' as nombre_juego, count(u.number_employee) total
                  from game_run_pancho_results as gr
                  join game_run_pancho_run_temas as grn on grn.id = gr.id_tema
                  join user as u on u.id = gr.user_id
                  left join jobs as j on j.id = u.job_id
                  where u.business_id =" . $data["business_id"]
            . "  " . $this->validacion_roles_string() . "
            and date_format(gr.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(gr.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] != null && $data["nombre_usuario"] != 'null') {
            $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
        }

        $query .= " group by  nombre_juego " . $this->validacion_limite_string($data["limite"]) . "";

        $resultado_run = $this->db->query($query)->result_array();
        $resultado = array_merge($resultado, $resultado_run);

        $query = "select 'Ahorcado' as nombre_juego, count(u.number_employee) total
                  from game_ahorcado_users as au
                  join user as u on u.id = au.id_usuario
                  left join jobs as j on j.id = u.job_id
                  where u.business_id = " . $data["business_id"]
            . "  " . $this->validacion_roles_string() . "
            and date_format(au.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(au.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

        $query .= " group by  nombre_juego " . $this->validacion_limite_string($data["limite"]) . "";

        $resultado_run = $this->db->query($query)->result_array();
        $resultado = array_merge($resultado, $resultado_run);

        $query = "select 'Culebra' as nombre_juego,count(u.number_employee) total
            from game_snake_results gr 
            join game_snake_temas gt on gt.id = gr.id_tema
            join user u on u.id = gr.user_id
            left join jobs j on j.id = u.job_id
            where u.business_id = " . $data["business_id"]
            . "  " . $this->validacion_roles_string() . "
            and date_format(gr.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(gr.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

        $query .= " group by  nombre_juego " . $this->validacion_limite_string($data["limite"]) . "";

        $resultado_run = $this->db->query($query)->result_array();
        $resultado = array_merge($resultado, $resultado_run);

        return $resultado;
    }

    public function ObtenerGraficaJuegosCalificacionesRuleta($data)
    {
        $query = "
            select 'Ruleta' as nombre_juego,count(if(ga.correct = 1, 1, null)) as correctas,count(if(ga.correct = 0, 1, null)) as incorrectas
                    from (select distinct question_id, answer_id, user_id, created_at from game_roulette_results) as gr
                    join user as u on u.id = gr.user_id
                    join game_roulette_question_answers as ga on ga.id = gr.answer_id
                    join game_roulette_questions as gqq on gqq.id = gr.question_id
                    join game_roulette_quiz as gq on gqq.quiz_id = gq.id
                    left join jobs as j on j.id = u.job_id
                    where u.business_id = " . $data['business_id'] . "
                    " . $this->validacion_roles_string() . "
                    and date_format(gr.created_at,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(gr.created_at,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'null') {
            $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
        }
        $query .= " group by  nombre_juego " . $this->validacion_limite_string($data["limite"]) . "";
        $resultado = $this->db->query($query)->result_array();
        return $resultado;
    }

    public function ObtenerGraficaJuegosCalificacionesRunPancho($data)
    {
        $query = "select 'Run pancho run' as nombre_juego,count(if(gr.tipo = 1, 1, null)) as buenas,count(if(gr.tipo = 0, 1, null)) as malas 
                    from game_run_pancho_words as gr
                    left join game_run_pancho_results as grs on grs.id_tema = gr.id_tema
                    join user as u on u.id = grs.user_id
                    join jobs as j on j.id = u.job_id
                  where u.business_id =" . $data["business_id"]
            . "  " . $this->validacion_roles_string() . "
            and date_format(grs.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(grs.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] != null && $data["nombre_usuario"] != 'null') {
            $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
        }

        $query .= " group by gr.id_tema, grs.id" . $this->validacion_limite_string($data["limite"]) . "";

        $resultado = $this->db->query($query)->result_array();
        return $resultado;
    }

    public function ObtenerGraficaJuegosCalificacionesAhorcado($data)
    {

        $query = "select 'Ahorcado' as nombre_juego,count(if(ga.correct = 1, 1, null)) as correctas,count(if(ga.correct = 0, 1, null)) as incorrectas
                  from game_ahorcado_users as au
                  join user as u on u.id = au.id_usuario
                  join jobs as j on j.id = u.job_id
                  where u.business_id = " . $data["business_id"]
            . "  " . $this->validacion_roles_string() . "
            and date_format(au.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(au.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] != null && $data["nombre_usuario"] != 'null') {
            $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];
        }

        $query .= " group by nombre_juego" . $this->validacion_limite_string($data["limite"]) . "";

        $resultado = $this->db->query($query)->result_array();
        return $resultado;
    }

    public function ObtenerGraficaJuegosCalificacionesculebra($data)
    {
        $query = "select 'Culebra' as nombre_juego,count(if(gsp.tipo = 1, 1, null)) as buenas,count(if(gsp.tipo = 0, 1, null)) as malas
                    from game_snake_palabras gsp    
                    left join game_snake_results gsr on gsr.id_tema = gsp.id_tema
                    join user u on u.id = gsr.user_id
                    left join jobs j on j.id = u.job_id
                    where u.business_id = " . $data["business_id"]
            . "  " . $this->validacion_roles_string() . "
            and date_format(gsr.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(gsr.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";
        $query .= "group by nombre_juego" . $this->validacion_limite_string($data["limite"]) . "";

        $resultado = $this->db->query($query)->result_array();
        return $resultado;
    }

    public function obtener_grafica_compartidos_library($data)
    {
        $query = "
        select le.title, count(*) as veces_compartido
        from library_elements_  as le force index (PRIMARY, ind_date, ind_active, ind_id_business)
        join library_shared as ls force index (ind_element_id, ind_fecha) on ls.element_id = le.id
        join user as u force index (primary) on u.id = ls.user_id
        where le.business_id = " . $data["business_id"]
            . " " . $this->validacion_roles_string() . "
        and date_format(ls.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "'
        and date_format(ls.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "' and le.active = 1 and u.es_prueba = 0 
        group by le.id
        order by veces_compartido desc";
        return $this->db->query($query)->result_array();
    }

    public function obtener_graficas_compartidos_usuarios_library($data)
    {
        $query = "
        select concat(u.name,' ', u.last_name) as name, count(*) as veces_compartido
        from library_elements_  as le force index (PRIMARY, ind_date, ind_active, ind_id_business)
        join library_shared as ls force index (ind_element_id, ind_fecha) on ls.element_id = le.id
        join user as u force index (primary) on u.id = ls.user_id
        where le.business_id = " . $data["business_id"]
            . " " . $this->validacion_roles_string() . "
        and date_format(ls.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "'
        and date_format(ls.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "' and le.active = 1 and u.es_prueba = 0 
        group by u.id
        order by veces_compartido desc";
        return $this->db->query($query)->result_array();
    }

    public function obtener_csv_library_compartidos_usuarios($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteLibraryCompartidos" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");

        $query = "
        select u.number_employee, concat(u.name,' ', u.last_name) as name,le.title, count(*) as veces_compartido
        from library_elements_  as le force index (PRIMARY, ind_date, ind_active, ind_id_business)
        join library_shared as ls force index (ind_element_id, ind_fecha) on ls.element_id = le.id
        join user as u force index (primary) on u.id = ls.user_id
        where le.business_id = " . $data["business_id"]
            . " " . $this->validacion_roles_string() . "
        and date_format(ls.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "'
        and date_format(ls.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "' and le.active = 1 and u.es_prueba = 0 
        group by u.id, le.id
        order by veces_compartido desc";
        $result = $this->db->query($query)->result_array();

        $resultado = array_merge([["NÚMERO DE EMPLEADO", "NOMBRE", "TÍTULO", "VECES COMPARTIDO"]], $result);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    public function obtener_grafica_compartidos_podcast($data)
    {
        $query = "
        select p.title, count(*) as veces_compartido
        from podcast as p force index (primary, ind_active, ind_business_id) 
        join podcast_shared as ps force index (ind_element_id, ind_user_id, ind_fecha) on ps.element_id = p.id
        join user as u force index (primary, ind_es_prueba) on u.id = ps.user_id
        where p.business_id = " . $data["business_id"] . " and p.active = 1 and u.es_prueba = 0 
         " . $this->validacion_roles_string() . "
        and date_format(ps.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "'
        and date_format(ps.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "' and p.active = 1
        group by p.id
        order by veces_compartido desc
        ";
        return $this->db->query($query)->result_array();
    }

    public function obtener_grafica_compartidos_usuarios_podcast($data)
    {
        $query = "
        select concat(u.name,' ', u.last_name) as name, count(*) as veces_compartido
        from podcast as p force index (primary, ind_active, ind_business_id) 
        join podcast_shared as ps force index (ind_element_id, ind_user_id, ind_fecha) on ps.element_id = p.id
        join user as u force index (primary, ind_es_prueba) on u.id = ps.user_id
        where p.business_id = " . $data["business_id"] . " and p.active = 1 and u.es_prueba = 0 
         " . $this->validacion_roles_string() . "
        and date_format(ps.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "'
        and date_format(ps.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "' and p.active = 1
        group by u.id
        order by veces_compartido desc
        ";
        return $this->db->query($query)->result_array();
    }

    public function obtener_csv_podcast_compartidos_usuarios($token, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReportePodcastCompartidos" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
        $query = "
        select u.number_employee, concat(u.name,' ', u.last_name) as name,p.title, count(*) as veces_compartido
        from podcast as p force index (primary, ind_active, ind_business_id) 
        join podcast_shared as ps force index (ind_element_id, ind_user_id, ind_fecha) on ps.element_id = p.id
        join user as u force index (primary, ind_es_prueba) on u.id = ps.user_id
        where p.business_id = " . $data["business_id"] . " and p.active = 1 and u.es_prueba = 0 
         " . $this->validacion_roles_string() . "
        and date_format(ps.fecha,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "'
        and date_format(ps.fecha,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "' and p.active = 1
        group by u.id, p.id
        order by veces_compartido desc
        ";
        $result = $this->db->query($query)->result_array();

        $resultado = array_merge([["NÚMERO DE EMPLEADO", "NOMBRE", "TÍTULO", "VECES COMPARTIDO"]], $result);
        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }
}
