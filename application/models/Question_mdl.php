<?php

class Question_mdl extends CI_Model

{

    private $question_quiz = "question_quiz",

        $questions = "questions",

        $question_answer = "question_answers",

        $question_type = "question_type",

        $question_ansewer_user = "question_answer_users",

        $question_configuration = "question_configuration",

        $question_categories = "question_categories",

        $user_table = 'user';



    function __construct()

    {

        parent::__construct();

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

        return $query;

    }



    /**

     * Consulta a la BD para tratar de devolver los usuarios que contestaron un cuestionario

     */

    function getListUsersByQuiz($id)

    {



        $query = "SELECT

            u.business_id,

            u.id,

            CONCAT(u.name, ' ', u.last_name) AS nombre_completo

        FROM

            question_answer_users AS q

        

            INNER JOIN questions AS questions

                ON (questions.id = q.question_id)

            INNER JOIN user AS u

                ON (u.id = q.user_id)

        WHERE questions.quiz_id = $id

        GROUP BY

            q.user_id;";

        $query = $this->db->query($query)->result_array();



        if (count($query) > 0) {

            return $query;

        } else {

            return false;

        }

    }



    function getUsersbyIdQuiz($data){

        $query = "

            select 

                u.business_id,

                u.id as id_user,

                concat('#',u.number_employee) as numero_empleado,

                concat(u.name,' ',u.last_name) as nombre_completo,

                qau.date as fecha

            from question_quiz as qq

                join questions as q on q.quiz_id = qq.id

                join question_answer_users as qau on qau.question_id  = q.id

                join question_answers as qa on qa.question_id = q.id

                join user as u on u.id = qau.user_id        

                left join jobs as j on j.id = u.job_id

            where q.quiz_id = {$data['id']}

                and date_format(qau.date,'%Y-%m-%d') >= '{$data['fecha_inicio']}'

                and date_format(qau.date,'%Y-%m-%d') <= '{$data['fecha_fin']}'

            group by concat(u.name,' ',u.last_name)

            order by qau.date asc";

        $resultado = $this->db->query($query)->result_array();



        if (count($resultado) > 0) {

            return $resultado;

        } else {

            return false;

        }

    }



    /**

     * Listamos las preguntas por usuario

     */

    function listQuizPerUser($id, $user)

    {



        $query = "SELECT

            *,

            qu.id AS answer_id

        FROM question_answer_users AS qu

            INNER JOIN questions AS questions

                ON (questions.id = qu.question_id)

            LEFT JOIN user AS u

		        ON (u.id = qu.user_id)

        WHERE

            questions.quiz_id = $id

            AND qu.user_id = $user

        ORDER BY qu.id DESC;";



        $query = $this->db->query($query)->result_array();



        if (count($query) > 0) {

            return $query;

        } else {

            return false;

        }

    }



    function reporte_cuestionario($data)

    {

        $query = "select qq.id, qq.name as nombre_cuestionario, u.number_employee as numero_empleado, concat(u.name, ' ', u.last_name) as nombre,

        j.job_name as nombre_puesto, qau.date as fecha, sum(qau.tried) as intentos, q.question as pregunta,

        qau.answer as respuesta_usuario, qa.answer as respuesta ,qau.correcto, q.type_id

        from question_quiz as qq

        join questions as q on q.quiz_id = qq.id

        join question_answer_users as qau on qau.question_id  = q.id

        join question_answers as qa on qa.question_id = q.id

        join user as u on u.id = qau.user_id        

        left join jobs as j on j.id = u.job_id

        where u.business_id = " . $data["business_id"]

            . " and u.es_prueba = 0 " . $this->validacion_roles_string() . "

            and qq.id = " . $data["quiz_id"] . " and date_format(qau.date,'%Y-%m-%d') >= '" . $data["fecha_inicio"] . "' and date_format(qau.date,'%Y-%m-%d') <= '" . $data["fecha_fin"] . "'";

        if (isset($data["nombre_usuario"]) && $data["nombre_usuario"] !== null && $data["nombre_usuario"] !== 'null') {

            $query .= " and concat(u.name,' ',u.last_name) like " . $data["nombre_usuario"];

        }

        $query .= " group by u.id, qq.id,q.id,qau.date";

        $result = $this->db->query($query)->result_array();



        for ($i = 0; $i < count($result); $i++) {

            if ($result[$i]["type_id"] == 8 || $result[$i]["type_id"] == 13 || $result[$i]["type_id"] == 14 || $result[$i]["type_id"] == 12)

                $result[$i]["respuesta_usuario"] = $result[$i]["respuesta_usuario"];

            else if ($result[$i]["type_id"] == 3 || $result[$i]["type_id"] == 5) {

                $result[$i]["respuesta_usuario"] =  $this->obtener_respuesta_multiple_img($result[$i]["respuesta_usuario"], true);

            } else if ($result[$i]["type_id"] == 7 || $result[$i]["type_id"] == 10 || $result[$i]["type_id"] == 11) {

                $result[$i]["respuesta_usuario"] = "<img height='100' width='100' src='" . base_url() . "/upload/preguntas/" . $result[$i]["respuesta_usuario"] . "'>";

            } else if ($result[$i]["type_id"] == 2 || $result[$i]["type_id"] == 4 || $result[$i]["type_id"] == 6 || $result[$i]["type_id"] == 9) {

                $result[$i]["respuesta_usuario"] =  $result[$i]["respuesta"];

            } else if ($result[$i]["type_id"] == 1) {

                $result[$i]["respuesta_usuario"] =  $this->obtener_respuesta_multiple($result[$i]["respuesta_usuario"]);

            }

        }

        return $result;

    }





    function obtener_respuesta_multiple_img($id_respuesta, $band = null)

    {



        $query = "select concat('" . base_url() . "/upload/preguntas/',answer) as answer from question_answers where id in ($id_respuesta)";

        $arr = $this->get_simple_array($this->db->query($query)->result_array(), $band);

        return join(', ', $arr);

    }



    function obtener_respuesta_multiple($id_respuesta)

    {

        $query = "select answer from question_answers where id in ($id_respuesta)";

        $arr = $this->get_simple_array($this->db->query($query)->result_array());

        return join(', ', $arr);

    }



    function get_simple_array($arr, $band = null)

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



    /**

     * Descripción de respuestas

     */

    function answerDesc($id)

    {

        $query = "SELECT

            answer

        FROM question_answers AS qa

        WHERE

            qa.id = $id";



        $query = $this->db->query($query)->result_array();



        if (count($query) > 0) {

            return $query;

        } else {

            return false;

        }

    }



    /***********************************************************************

     *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/09/2020

     *		   mario.martinez.f@hotmail.es

     *	Nota: Funcion para obtener los quiz en base a la configuracion

     ***********************************************************************/

    function ListQuiz($data)
    {
        $query = "select * from users_groups where active  = 1 and user_id = " . $data["user_id"];
        $grupos = $this->db->query($query)->result_array();
        $grupos_ = [];
        for ($i = 0; $i < count($grupos); $i++) {
            array_push($grupos_, $grupos[$i]["group_id"]);
        }
        $query = "set lc_time_names = 'es_MX';";
        $this->db->query($query);
        $this->db->select('"1" as permitir_hacer, coalesce(date_format(qq.fecha_limite,"%d %M %Y"),"") as fecha_limite,qq.id,qq.name, 
        ifnull(qc.name,"") as name_category, qq.category_id, qq.job_id, qq.connection_id,sum(qau.correcto) as correctas, 
        count(q.id) as numero_preguntas, sum(qau.correcto)/count(q.id) * 100 as calificacion,qq.first_question_is_correct,
        qq.created_at');
        $this->db->from($this->question_quiz . ' qq');
        $this->db->join($this->question_categories . ' qc', 'qq.category_id = qc.id', 'left');
        //para validar que no salga el quiz una vez contestado
        $this->db->join($this->questions . " q", "q.quiz_id = qq.id", "left");
        // $this->db->join($this->question_ansewer_user.' qaw',"qaw.");
        $this->db->join($this->question_ansewer_user . ' qau', 'qau.question_id = q.id and qau.user_id = ' . $data["user_id"], "left");
        // $this->db->where("qau.id is null", null, false);
        //fin de la validacion
        $this->db->join("quiz_users as qu", "qu.quiz_id = qq.id", "left");
        $this->db->join("quiz_groups as qg", "qg.quiz_id = qq.id", "left");
        //esto es para la validacion de cuestionarios por grupo o usuarios
        //pero permitir tambien que los cuestioanrios anteriores a esta fecha 26/sep/2021
        //si se vean aunque no esten agrupados
        $this->db->where('qq.business_id = ', $data['business_id']);
        $this->db->where('capacitacion_obligatoria', 0);
        if (isset($data['category_id'])) {

            if ($data['category_id'] != '') {

                if ($data['category_id'] != 1) {

                    $this->db->where('category_id = ', $data['category_id']);

                }

                if ($data['category_id'] == 1) {

                    $this->db->where('(category_id = 1 or category_id = 4)', null, false);

                }

                if ($data['category_id'] != 1) {

                    $this->db->where('connection_id = ', $data['connection_id']);

                }

            }

        }
        if (isset($data['job_id'])) {

            $this->db->where('job_id =', $data['job_id']);

        }
        if (isset($data['id'])) {

            $this->db->where('qq.id =', $data['id']);

        }
        $this->db->where('qq.active=', 1);
        $this->db->where('(now() <= qq.fecha_limite or qq.fecha_limite is null or fecha_limite = "0000-00-00 00:00:00" )', null, false);
        $this->db->where("(qu.user_id = " . $data["user_id"] . " or qg.group_id in (" . join(",", $grupos_) . "))", null, false);
        /* rango de fechas */
        if(isset($data["fecha_inicio"]) && isset($data["fecha_actual"])){
            $this->db->where('qq.created_at >=', $data["fecha_inicio"]." 00:00:00");
            $this->db->where('qq.created_at <=', $data["fecha_actual"]." 23:59:59");
        }
        $this->db->group_by("qq.id");
        $this->db->having("calificacion < 80 or (correctas/numero_preguntas) is null");
        $result = $this->db->get()->result_array();
        $resultArray = [];
        if (count($result) > 0) {
            if($result)
            // if (isset($data['id'])) {
                for ($i = 0; $i < count($result); $i++) {
                    $query_capacitaciones = "SELECT 
                        l.id,
                        l.name
                    FROM capacit_detail AS d
                        INNER JOIN capacit_list AS l
                            ON (l.id = d.id_capacitacion)
                        INNER JOIN capacit_categorias as cc on cc.id = d.catalog
                    WHERE cc.`catalog` = 'question_quiz' AND d.id_elemento = " . $result[$i]['id'] . ";";
                    $resultado = $this->db->query($query_capacitaciones)->result_array();
                    if (count($resultado) > 0) {
                        $result[$i]['capacitaciones'] = $resultado;
                    } else {
                        $result[$i]['capacitaciones'] = [];
                    }
                /* validar cuestionario primera_pregunta (se refiere a que si la primera pregunta es incorrecta, no permitir mostrar cuestionario) */
                if($result[$i]["first_question_is_correct"]){
                    /* si cumple esta condicion, evaluar que la primera preguna contestada sea correcta de lo contrario no mostrar ya quiz*/
                    $pregunta = $this->db->query("select * from questions where quiz_id = '".$result[$i]['id']."';")->result_array(); /* preguntas cuestionario */
                    /* buscar respuesta relacionada a la pregunta */
                    if(count($pregunta) > 0){
                        //return $pregunta[0]["id"];     
                        //return $data["user_id"];
                        $respuestas = $this->db->query("select * from question_answer_users where user_id = '".$data["user_id"]."' and question_id = '".$pregunta[0]["id"]."';")->result_array();
                        if(count($respuestas) > 0){
                            if($respuestas[0]["correcto"] != 0){
                                /* eliminar quiz de resultado */
                                //unset($result[$i]);
                                array_push($resultArray, $result[$i]);
                            }
                        }else{
                            array_push($resultArray, $result[$i]);
                        }
                    }else{
                        array_push($resultArray, $result[$i]);
                    }
                }else{
                    array_push($resultArray, $result[$i]);
                }
            }
            return $resultArray;
            // }
        } else {
            return false;
        }
    }
    /* funcion para obtener el cuestionario y validar si es de trivia (first_question_is_correcto) */
    function QuizById($data)
    {
       
        $query = "set lc_time_names = 'es_MX';";
        $this->db->query($query);
        
        $result = $this->db->query("SELECT * FROM question_quiz WHERE id = '".$data['quiz_id']."'")->result_array();
        if (count($result) > 0) {
            if($result){
                for ($i = 0; $i < count($result); $i++) {
                /* validar cuestionario primera_pregunta (se refiere a que si la primera pregunta es incorrecta, no permitir mostrar cuestionario) */
                    if($result[$i]["first_question_is_correct"]){
                        /* si cumple esta condicion, evaluar que la primera preguna contestada sea correcta de lo contrario no mostrar ya quiz*/
                        $pregunta = $this->db->query("select * from questions where quiz_id = '".$result[$i]['id']."';")->result_array()[0]; /* preguntas cuestionario */
                        /* buscar respuesta relacionada a la pregunta */
                        if($pregunta){
                            //return $pregunta["id"];     
                            $respuestas = $this->db->query("select * from question_answer_users where user_id = '".$data["user_id"]."' and question_id = '".$pregunta["id"]."';")->result_array()[0];
                            if($respuesta["correcto"] == 0){
                                /* eliminar quiz de resultado */
                                unset($result[$i]);
                            }
                        }
                    }
                }
            }
            return $result;
        } else {
            return false;
        }
    }

    function ListQuizAdmin($data)
    {
        $this->db->select('"1" as permitir_hacer, coalesce(date_format(qq.fecha_limite,"%Y-%m-%d"),"") as fecha_limite,qq.id,qq.name, ifnull(qc.name,"") as name_category, qq.category_id, qq.job_id, qq.connection_id, qq.active,qq.activo_al');

        $this->db->from($this->question_quiz . ' qq');

        $this->db->join($this->question_categories . ' qc', 'qq.category_id = qc.id', 'left');

        //para validar que no salga el quiz una vez contestado

        $this->db->join($this->questions . " q", "q.quiz_id = qq.id", "left");

        // $this->db->join($this->question_ansewer_user.' qaw',"qaw.");

        $this->db->join($this->question_ansewer_user . ' qau', 'qau.question_id = q.id and qau.user_id = ' . $data["user_id"], "left");

        // $this->db->where("qau.id is null", null, false);

        //fin de la validacion

        $this->db->where('qq.business_id = ', $data['business_id']);



        if (isset($data['category_id'])) {

            if ($data['category_id'] != '') {

                $this->db->where('category_id = ', $data['category_id']);

                if ($data['category_id'] != 1 && $data["category_id"] != 5) {

                    $this->db->where('connection_id = ', $data['connection_id']);

                }

            }

        }

        if (isset($data['job_id'])) {

            $this->db->where('job_id =', $data['job_id']);

        }

        if (isset($data['id'])) {

            $this->db->where('qq.id =', $data['id']);

        }

        $this->db->where('qq.active=', 1);

        // $this->db->where('(now() <= qq.fecha_limite or qq.fecha_limite is null or fecha_limite = "0000-00-00 00:00:00")', null, false);

        $this->db->group_by("qq.id");

        $this->db->order_by("qq.active", "desc");

        $result = $this->db->get()->result_array();



        if (count($result) > 0) {

            // if (isset($data['id'])) {

            for ($i = 0; $i < count($result); $i++) {

                $query_capacitaciones = "SELECT 

                    l.id,

                    l.name

                FROM capacit_detail AS d

                    INNER JOIN capacit_list AS l

                        ON (l.id = d.id_capacitacion)

                    INNER JOIN capacit_categorias as cc on cc.id = d.catalog

                WHERE cc.`catalog` = 'question_quiz' AND d.id_elemento = " . $result[$i]['id'] . ";";



                $resultado = $this->db->query($query_capacitaciones)->result_array();

                if (count($resultado) > 0) {

                    $result[$i]['capacitaciones'] = $resultado;

                } else {

                    $result[$i]['capacitaciones'] = [];

                }



                $query_grupos = "

                select g.* from quiz_groups as q

                join groups as g on g.id = q.group_id

                where quiz_id = " . $result[$i]["id"];

                $grupos = $this->db->query($query_grupos)->result_array();

                $query_usuarios = "

                select u.id, concat(u.name, ' ',u.last_name) as name from quiz_users as q

                join user as u on u.id = q.user_id

                where quiz_id = " . $result[$i]["id"];

                $usuarios = $this->db->query($query_usuarios)->result_array();

                $result[$i]["grupos"] = $grupos;

                $result[$i]["usuarios"] = $usuarios;

            }

            return $result;

            // }

        } else {

            return false;

        }

    }



    /***********************************************************************

     *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/09/2020

     *		   mario.martinez.f@hotmail.es

     *	Nota: Funcion para obtener las categorias de los quiz

     ***********************************************************************/

    function ListCategories()

    {

        $this->db->select('id,name');

        $this->db->from($this->question_categories);

        $this->db->where("id !=", 5);

        $result = $this->db->get()->result_array();

        if (count($result) > 0) {

            return $result;

        } else {

            return false;

        }

    }

    /***********************************************************************

     *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/09/2020

     *		   mario.martinez.f@hotmail.es

     *	Nota: Funcion para obtener las preguntas de un quiz, cada pregunta

     *          retornara con sus respectivas respuesatas.

     ***********************************************************************/

    function ListQuestionsQuiz($data){
        /***********************************************************************

         *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/09/2020

         *		   mario.martinez.f@hotmail.es

         *	Nota: Si se manda un id en especifico solo se retornaran las respuestas

         ***********************************************************************/
        if($data['question_id'] === '') {
            /***********************************************************************

             *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/09/2020

             *		   mario.martinez.f@hotmail.es

             *	Nota: Obtenemos las preguntas del quiz

             ***********************************************************************/
            $this->db->select('q.id, question, type_id, qt.name as name_type, qt.answer as answers_type, q.points, qq.category_id,qq.id as quiz_id');
            $this->db->from($this->questions . ' q');
            $this->db->join($this->question_type . ' qt', 'qt.id = q.type_id');
            $this->db->join($this->question_quiz . ' qq', 'qq.id = q.quiz_id');
            $this->db->where('q.quiz_id', $data['quiz_id']);
            $this->db->where('q.active', 1);
            $this->db->order_by("id");

            $questions = $this->db->get()->result_array();
            if (count($questions) > 0) {
                /***********************************************************************

                 *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/09/2020

                 *		   mario.martinez.f@hotmail.es

                 *	Nota: Hacemos un recorriodo en cada una de las preguntas y validamos

                 *          que sea una pregunta de tipo que contiene respuesta o no.

                 ***********************************************************************/
                foreach($questions as $index => $value){
                    if (!$this->session->userdata('id_user') && $value['category_id'] != QUIZ_CATEGORY_ELEARNING) {
                        $questions[$index]['question'] = '<div style="' . DEFAULT_STYLE_QUESTIONS . '">' . $value['question'] . '</div>';
                    }


                    if($value['answers_type'] == 1){
                        $this->db->select('id, answer, image');
                        $this->db->from($this->question_answer);
                        $this->db->where('question_id', $value['id']);

                        $answers = $this->db->get()->result_array();
                        if($value['type_id'] == TIPO_PREGUNTA_MULTIPLE_IMAGEN || $value['type_id'] == TIPO_PREGUNTA_SEMAFORO || $value['type_id'] == TIPO_PREGUNTA_UNICA_IMAGEN || $value['type_id'] == TIPO_PREGUNTA_TACHE_PALOMA || $value['type_id'] == TIPO_PREGUNTA_DIBUJO) {
                            $url = base_url('uploads/business_' . $data['business_id'] . '/preguntas/');
                            foreach ($answers as $index_answer => $value_answer) {
                                $answers[$index_answer]['answer'] = $url . $value_answer['answer'];
                                unset($answers[$index_answer]['image']);
                            }
                        }else if($value['type_id'] == TIPO_PREGUNTA_CAJON){
                            $url = base_url('uploads/business_' . $data['business_id'] . '/preguntas/');
                            foreach ($answers as $index_answer => $value_answer) {
                                $answers[$index_answer]['answer'] = $value_answer['answer'];
                                $answers[$index_answer]['image'] = $url . $value_answer['image'];
                            }
                        }else{
                            if (!$this->session->userdata('id_user') && $value['category_id'] != QUIZ_CATEGORY_ELEARNING) {
                                foreach ($answers as $index_answer => $value_answer) {
                                    $answers[$index_answer]['answer'] = '<div style="' . DEFAULT_STYLE_QUESTIONS_ANSWERS . '">' . $value_answer['answer'] . '</div>';
                                }
                            }
                        }

                        $questions[$index]['answers'] = $answers;

                    }else{
                        $questions[$index]['answers'] = array();
                    }
                }

                return $questions;

            }else{
                return false;
            }

        }else{
            if(isset($data['question_admin'])){
                $this->db->select('q.id, question, type_id, qt.name as name_type, qt.answer as answers_type, q.points');
                $this->db->from($this->questions . ' q');
                $this->db->join($this->question_type . ' qt', 'qt.id = q.type_id');
                $this->db->where('q.id', $data['question_id']);
                $this->db->where('q.active', 1);

                $questions = $this->db->get()->result_array();

                return $questions;
            }else{
                $this->db->select('id, answer, correct');
                $this->db->from($this->question_answer);
                $this->db->where('question_id', $data['question_id']);
                $this->db->where("active", 1);

                $answers = $this->db->get()->result_array();

                if($data['type_id'] == TIPO_PREGUNTA_MULTIPLE_IMAGEN || $data['type_id'] == TIPO_PREGUNTA_SEMAFORO || $data['type_id'] == TIPO_PREGUNTA_UNICA_IMAGEN || $data['type_id'] == TIPO_PREGUNTA_TACHE_PALOMA || $data['type_id'] == TIPO_PREGUNTA_DIBUJO) {
                    $url = base_url('uploads/business_' . $data['business_id'] . '/preguntas/');
                    foreach ($answers as $index_answer => $value_answer) {
                        $answers[$index_answer]['answer'] = $url . $value_answer['answer'];
                    }
                }
                return $answers;
            }
        }
    }



    /***********************************************************************

     *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/09/2020

     *		   mario.martinez.f@hotmail.es

     *	Nota: Funcion registrar una respuesta.

     ***********************************************************************/

    function SaveAnswerUser($data)

    {

        $type_id = $data['type_id'];

        unset($data['type_id']);

        if (isset($data['token'])) {

            unset($data['token']);

        }

        /***********************************************************************

         *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/09/2020

         *		   mario.martinez.f@hotmail.es

         *	Nota: Registramos la respuesta primero

         ***********************************************************************/

        $data['date'] = date('Y-m-d H:i:s');

        $this->db->select("qau.*");

        $this->db->from("question_answer_users as qau");

        $this->db->join("questions as q", "q.id = qau.question_id");

        $this->db->join("question_quiz as qq", "qq.id = q.quiz_id");

        $this->db->where("qau.question_id", $data["question_id"]);

        if (isset($data["id_elemento"]))

            $this->db->where("id_elemento", $data["id_elemento"]);

        $this->db->where("qau.user_id", $data["user_id"]);

        $this->db->where("qq.category_id !=", 5);

        $result = $this->db->get()->result_array();

        $id_respuesta_usuario = 0;

        // echo json_encode($result);

        if (count($result) > 0) {

            $id_respuesta_usuario = $result[0]["id"];

            $fecha = date("Y-m-d H:i:s");

            $this->db->set("answer", $data["answer"]);

            //no se guardan los intentos en cada respuesta

            //se deben guardar cuando se contesten todas las respuestas del cuestionario

            //$this->db->set("tried", ($result[0]["tried"] + 1));

            $this->db->set("date", $fecha);

            $this->db->where("id", $id_respuesta_usuario);

            $this->db->update($this->question_ansewer_user);

        } else {

            $this->db->insert($this->question_ansewer_user, $data);

            $id_respuesta_usuario = $this->db->insert_id();

        }

        /***********************************************************************

         *	Autor: Mario Adrián Martínez Fernández   Fecha: 04/09/2020

         *		   mario.martinez.f@hotmail.es

         *	Nota: Obtenemos el score actual del usuario

         ***********************************************************************/

        $this->db->select('score');

        $score = $this->db->get_where('user', array('id' => $data['user_id']))->result_array();

        $score = intval($score[0]['score']);

        $is_correct = false;

        /***********************************************************************

         *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/09/2020

         *		   mario.martinez.f@hotmail.es

         *	Nota: Una vez que registramos la respuesta realizamos la validacion

         *          de si la respuesta es correcta, para restarle o sumarle

         *          puntos.

         ***********************************************************************/

        if ($data['answer'] !== '') {
            if ($type_id !== TIPO_PREGUNTA_LIKE_NUMEROS && $type_id !== TIPO_PREGUNTA_LIKE_CARAS) {

                $this->db->select('q.id, q.points, qt.answer, qa.correct');

                $this->db->from($this->questions . ' q');

                $this->db->join($this->question_type . ' qt', 'q.type_id = qt.id');

                $this->db->join($this->question_answer . ' qa', 'qa.question_id = q.id');

                $this->db->where('qa.id =', $data['answer']);

                $validate = $this->db->get()->result_array();

                $validate_ = count($validate);

            } else {
                /* incorecto por defecto */
                $validate_ = 0;
            }
            /* si se encontro resultados */
            if ($validate_ > 0) {
                /* si la respuesta es == 1 */
                if ($validate[0]['answer'] == 1) {

                    if ($validate[0]['correct'] == 1) {

                        $is_correct = true;

                        $score = $score + intval($validate[0]['points']);

                    } else {

                        $score = $score - intval($validate[0]['points']);

                        if ($score < 0) {

                            $score = 0;

                        }

                    }

                } else {
                    $is_correct = true; /* TODO: comprobar que pasa aqui, se supone que es incorrecta */
                    $score = $score + intval($validate[0]['points']);
                }
            } else {

                $this->db->select('q.id, q.points');

                $this->db->from($this->questions . ' q');

                $this->db->where('q.id =', $data['question_id']);

                $validate = $this->db->get()->result_array();



                $score = $score + intval($validate[0]['points']);

                $is_correct = true;

            }
        } else {
            /* incorrecto defecto */
            $score = 0;
        }
        //return $is_correct;
        $user = $this->db->update('user', array('score' => $score), array('id' => $data['user_id']));

        if ($user) {

            //aqui estoy modificando para marcar el cuestionario como correcto o no

            $this->setAnswerCorrect($id_respuesta_usuario, $is_correct);

            $respuesta = "";

            if ($type_id != TIPO_PREGUNTA_LIKE_NUMEROS && $type_id != TIPO_PREGUNTA_LIKE_CARAS && $type_id != 13 && $type_id != 12 && $type_id != 11)

                $respuesta = $this->obtener_respuesta_correcta($data["question_id"]);

            return array('is_correct' => $is_correct, 'mensaje' => $respuesta);

        } else {



            return false;

        }

    }



    function obtener_respuesta_correcta($question_id)

    {

        $query = "select answer from question_answers

                  where question_id = " . $question_id . " and active = 1 and correct = 1";

        $respuesta = $this->db->query($query)->result_array()[0]["answer"];

        return strip_tags($respuesta);

    }



    function setAnswerCorrect($id_respuesta_usuario, $is_correct)

    {

        $this->db->set("correcto", $is_correct);

        $this->db->where("id", $id_respuesta_usuario);

        return $this->db->update($this->question_ansewer_user);

    }



    function comprobarCalificacion($question_id, $user_id)

    {

        $query = "

        select qq.id,qq.category_id, q.id as question_id, qq.capacitacion_obligatoria

        from question_quiz as qq

        join questions as q on q.quiz_id = qq.id

        where q.id = " . $question_id;

        $result = $this->db->query($query)->result_array()[0];

        $quiz_id = $result["id"];

        $categoria = $result["category_id"];

        $capacitacion_obligatoria = $result["capacitacion_obligatoria"];





        if ($capacitacion_obligatoria == 1) {



            return $this->comprobar_calificacion_puntos($quiz_id, $user_id);

        } else {

            if ($categoria == 2) {

                return false;

            }



            //se obtiene la fecha en la que se responde la primer pergunta de la evaluacion

            //para que solo se tomen como contestadas las que se respondan despues de esta

            //a esta fecha se le restan 10 segundos por seguridad, por si la primera peticion tarda en llegar

            $query = "select date_format(DATE_SUB(date, INTERVAL 10 SECOND),'%Y-%m-%d %H:%i:%s') AS fecha from question_answer_users where user_id = " . $user_id . " and question_id =

            (select id from questions where quiz_id = " . $quiz_id . "

            order by id asc

            limit 1)";

            $res = $this->db->query($query)->result_array();

            $fecha = date("2021-10-10 00:00:00"); //si no se ha contestado cualquier fecha sirve siempre que sean antes de la fecha actual

            if (count($res) > 0) {

                $fecha = $res[0]["fecha"];

            }



            $query = "

            select count(q.id) as numero_preguntas, sum(if(qa.id is null,0,1)) as contestadas

            from question_quiz as qq

            join questions as q on q.quiz_id = qq.id

            left join question_answer_users as qa on qa.question_id = q.id and qa.user_id = " . $user_id . " and date_format(qa.date,'%Y-%m-%d %H:%i') >= '$fecha'

            where qq.id = " . $quiz_id . "

            group by qq.id

        ";

            $result = $this->db->query($query)->result_array()[0];



            if ($result["numero_preguntas"] == $result["contestadas"]) {

                $query_correctas = "

            select count(q.id) as numero_preguntas, sum(qa.correcto) as correctas

            from question_quiz as qq

            join questions as q on q.quiz_id = qq.id

            left join question_answer_users as qa on qa.question_id = q.id and qa.user_id = " . $user_id . "

            where qq.id = " . $quiz_id . "

            group by qq.id

            ";

                $result = $this->db->query($query_correctas)->result_array()[0];

                $this->guardarCalificacion($result["correctas"], $result["numero_preguntas"], $quiz_id, $user_id);

                $this->sumar_un_intento($quiz_id, $user_id);

                if ($result["correctas"] / $result["numero_preguntas"] >= 0.8) {



                    $this->establecerCapacitacionCompleta($user_id, $quiz_id);

                    return "¡Felicidades! Aprobado";

                } else {

                    return "¡Vuelve a intentar!";

                }

            }

        }

        return false;

    }



    function sumar_un_intento($quiz_id, $user_id)

    {

        $query = "

        update question_answer_users set tried = tried + 1

        where user_id = $user_id and question_id in

        (

        select q.id from questions as q

        join question_quiz as qq on q.quiz_id = qq.id

        where qq.id = $quiz_id

        )";

        $this->db->query($query);

    }



    function comprobar_calificacion_puntos($quiz_id, $user_id, $bandera = null)

    {

        $query = "select date_format(DATE_SUB(date, INTERVAL 10 SECOND),'%Y-%m-%d %H:%i:%s') AS fecha from question_answer_users where user_id = $user_id and question_id =

        (select id from questions where quiz_id = $quiz_id

        order by id asc

        limit 1)";

        $res = $this->db->query($query)->result_array();

        $fecha = date("2021-10-10 00:00:00"); //si no se ha contestado cualquier fecha sirve siempre que sean antes de la fecha actual

        if (count($res) > 0) {

            $fecha = $res[0]["fecha"];

        }







        $query = "

        select count(q.id) as numero_preguntas, sum(if(qa.id is null,0,1)) as contestadas

            from question_quiz as qq

            join questions as q on q.quiz_id = qq.id

            left join question_answer_users as qa on qa.question_id = q.id and qa.user_id = " . $user_id . " and date_format(qa.date,'%Y-%m-%d %H:%i:%s') >= '$fecha'

            where qq.id = " . $quiz_id . "

            group by qq.id

    ";

        $result = $this->db->query($query)->result_array()[0];

        //echo json_encode($result);

        if ($result["numero_preguntas"] == $result["contestadas"]) {

            $query_correctas = "

            select count(q.id) as numero_preguntas, sum(qa.correcto) as correctas, sum(if(qa.correcto = 1, q.points, 0)) as puntos

            from question_quiz as qq

            join questions as q on q.quiz_id = qq.id

            join question_answer_users as qa on qa.question_id = q.id and qa.user_id = " . $user_id . "

            where qq.id = " . $quiz_id . "

            group by qq.id

    ";

            $result = $this->db->query($query_correctas)->result_array()[0];



            if ($bandera == true) {

                return $result["puntos"];

            }

            $this->guardarCalificacion($result["correctas"], $result["numero_preguntas"], $quiz_id, $user_id, $result["puntos"]);

            $this->sumar_un_intento($quiz_id, $user_id);

            $this->establecerCapacitacionCompleta($user_id, $quiz_id);

            if ($result["puntos"] >= 80) {

                $this->agregar_evaluacion_vista($user_id);





                return "¡Felicidades! Aprobado";

            } else {

                return "¡Vuelve a intentar!";

            }

        }

        return false;

    }



    function agregar_evaluacion_vista($user_id)

    {

        $query = "select evaluaciones_vistas from evaluaciones_vistas where user_id = $user_id";

        $numero_evaluaciones = $this->db->query($query)->result_array();

        if (count($numero_evaluaciones) > 0) {

            $numero_evaluaciones = $numero_evaluaciones[0]["evaluaciones_vistas"] + 1;

            $query = "update evaluaciones_vistas 

                      set evaluaciones_vistas = $numero_evaluaciones where user_id = $user_id";

        } else {

            $numero_evaluaciones = 1;

            $query = "insert into evaluaciones_vistas (user_id, evaluaciones_vistas,tipo)

                      values ($user_id, $numero_evaluaciones)";

        }

        $this->db->query($query);



        if ($numero_evaluaciones == 1) {

            $this->general_mdl->asignarInsignia(4, $user_id);

        }

        if ($numero_evaluaciones == 2) {

            $this->general_mdl->asignarInsignia(5, $user_id);

        }



        return true;

    }



    function establecerCapacitacionCompleta($id_usuario, $quiz_id)

    {

        $query = "select cl.id from capacit_list as cl

        join capacit_detail as cd on cd.id_capacitacion = cl.id

        join capacit_categorias as cc on cc.id = cd.catalog

        where cd.id_elemento = " . $quiz_id . " and cc.catalog = 'question_quiz'";

        // where cd.id_elemento = " . $elemento . " and cd.catalog = '" . $catalogo . "'";



        $capacitaciones = $this->db->query($query)->result_array();



        for ($i = 0; $i < count($capacitaciones); $i++) {

            $id_capacitacion = $capacitaciones[$i]["id"];

            $query = "insert into capacit_completed (id_usuario, id_elemento, id_capacitacion,catalog)

            values ('" . $id_usuario . "','" . $quiz_id . "','" . $id_capacitacion . "','question_quiz') ON DUPLICATE KEY UPDATE `fecha` = now(), `actualizado` = 0";

            $any = $this->db->query($query);

            $this->general_mdl->agregar_recurso_visto($id_usuario);

        }

    }





    function guardarCalificacion($correctas, $numero_preguntas, $quiz_id, $user_id, $puntos = null)

    {

        $data = [];

        if ($puntos != null) {

            $data["calificacion"] = $puntos;

        } else

            $data["calificacion"] = $correctas / $numero_preguntas * 100;

        $data["quiz_id"] = $quiz_id;

        $data["user_id"] = $user_id;

        $data["correctas"] = $correctas;

        $data["respuestas"] = $numero_preguntas;

        return $this->db->insert("historial_calificaciones_cuestionarios", $data);

    }

    /***********************************************************************

     *	Autor: Mario Adrián Martínez Fernández   Fecha: 04/09/2020

     *		   mario.martinez.f@hotmail.es

     *	Nota: Funcion para obtener el detalle la configuración de preguntas

     ***********************************************************************/

    function ConfigurationQuestions($business_id)

    {

        $this->db->select('name, value, active');

        $this->db->from($this->question_configuration);

        $this->db->where('business_id =', $business_id);

        $result = $this->db->get()->result_array();

        return $result;

    }

    /***********************************************************************

     *	Autor: Mario Adrián Martínez Fernández   Fecha: 15/09/2020

     *		   mario.martinez.f@hotmail.es

     *	Nota: Funcion para registrar todas las preguntas y generar el calculo

     *          de la evaluacion final para un elearning.

     ***********************************************************************/

    function saveAnswerElearning($data)

    {

        unset($data['token']);

        $results = json_decode($data['questions']);

        $count_response_correct = 0;

        foreach ($results as $index => $value) {

            //unset($value->type_id);

            $value->user_id = $data['user_id'];

            /***********************************************************************

             *	Autor: Mario Adrián Martínez Fernández   Fecha: 15/09/2020

             *		   mario.martinez.f@hotmail.es

             *	Nota: Insertamos las respuesta

             ***********************************************************************/

            $save_response = $this->SaveAnswerUser((array)$value);

            if ($save_response['is_correct']) {

                $count_response_correct++;

            }

        }

        /***********************************************************************

         *	Autor: Mario Adrián Martínez Fernández   Fecha: 16/09/2020

         *		   mario.martinez.f@hotmail.es

         *	Nota: Se valida que tipo de evaluacion es por que si la evaluacion

         *          es de tipo de satisfaccion no se hace ningun calculo,

         *          solo se guardaran las respuestas enviadas.

         ***********************************************************************/

        if ($data['type'] === 'final_evaluation') {

            /***********************************************************************

             *	Autor: Mario Adrián Martínez Fernández   Fecha: 15/09/2020

             *		   mario.martinez.f@hotmail.es

             *	Nota: Obtenemos el total de preguntas que tiene el quiz para obtener

             *          el porcentaje que ha alcanzado el usuario.

             ***********************************************************************/

            $this->db->select('id');

            $this->db->from($this->questions);

            $this->db->where('quiz_id = ', $data['quiz_id']);

            $total = $this->db->count_all_results();

            /***********************************************************************

             *	Autor: Mario Adrián Martínez Fernández   Fecha: 15/09/2020

             *		   mario.martinez.f@hotmail.es

             *	Nota: Obtenemos el porcentaje obtenido.

             ***********************************************************************/

            $porcentaje_obtenido = round((($count_response_correct * 100) / $total), 2);

            /***********************************************************************

             *	Autor: Mario Adrián Martínez Fernández   Fecha: 15/09/2020

             *		   mario.martinez.f@hotmail.es

             *	Nota: Obtenemos el porcentaje de minimo requerido para pasar.

             ***********************************************************************/

            $this->db->select('min_score');

            $this->db->from('elearning_modules');

            $this->db->where('id', $data['module_id']);

            $min_porcentaje = $this->db->get()->result_array();

            $min_porcentaje = floatval($min_porcentaje[0]['min_score']);

            /***********************************************************************

             *	Autor: Mario Adrián Martínez Fernández   Fecha: 16/09/2020

             *		   mario.martinez.f@hotmail.es

             *	Nota: Registramos el score con su respectivo intento

             ***********************************************************************/

            $this->db->insert('elearning_score_log', array('score' => $porcentaje_obtenido, 'module_id' => $data['module_id'], 'user_id' => $data['user_id'], 'quiz_id' => $data['quiz_id'], 'tried' => $data['tried'], 'fecha' => date('Y-m-d H:i:s')));

            if ($porcentaje_obtenido >= $min_porcentaje) {

                return array('success' => true, 'msg' => 'Felicidades, haz obtenido un ' . $porcentaje_obtenido . '%, la evaluación la haz pasado correctamente.', 'score' => $porcentaje_obtenido);

            } else {

                return array('success' => false, 'msg' => 'Vuelve a intentarlo, haz obtenido un ' . $porcentaje_obtenido . '%, la evaluación no la haz pasado correctamente.', 'score' => $porcentaje_obtenido);

            }

        } else {

            return array('success' => true, 'msg' => 'Gracias por contestar nuestra encuesta de satisfacción.');

        }

    }

    /***********************************************************************

     *	Autor: Mario Adrián Martínez Fernández   Fecha: 28/09/2020

     *	                                         	   mario.martinez.f@hotmail.es

     *	Nota: Funcion para guardar un catalogo nuevo de preguntas

     ***********************************************************************/

    function SaveQuiz($data)
    {
        if (isset($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            return $this->db->update($this->question_quiz, $data, array('id' => $id));
        } else {
            $this->db->insert($this->question_quiz, $data);
            return $this->db->insert_id();
        }
    }

    /***********************************************************************

     *	Autor: Mario Adrián Martínez Fernández   Fecha: 28/09/2020

     *		   mario.martinez.f@hotmail.es

     *	Nota: Funcion para obtener el catalogo de tipos de preguntas

     ***********************************************************************/

    function listTypesQuestion()

    {

        $query = "select * from question_type where active = 1";

        return $this->db->query($query)->result_array();

    }

    /***********************************************************************

     *	Autor: Mario Adrián Martínez Fernández   Fecha: 28/09/2020

     *		   mario.martinez.f@hotmail.es

     *	Nota: Funcion para registrar, actualizar o eliminar una pregunta

     ***********************************************************************/

    function SaveQuestion($data)

    {

        if (isset($data['id'])) {

            $id = $data['id'];

            unset($data['id']);

            return $this->db->update($this->questions, $data, array('id' => $id));

        } else {

            $insert = $this->db->insert($this->questions, $data);

            $question_id = $this->db->insert_id();

            if ($insert) {

                /***********************************************************************

                 *	Autor: Mario Adrián Martínez Fernández   Fecha: 28/09/2020

                 *		   mario.martinez.f@hotmail.es

                 *	Nota: Validamos que se haya insertado correctamente la pregunta

                 *          si se inserto correctamente validamos si el tipo de pregunta

                 *          lleva por default respuestas.

                 ***********************************************************************/

                if ($data['type_id'] == TIPO_PREGUNTA_PROPORCIONES) {

                    $this->db->insert_batch(

                        $this->question_answer,

                        array(

                            array('answer' => 'Poco', 'question_id' => $question_id, 'correct' => 1),

                            array('answer' => 'Mucho', 'question_id' => $question_id, 'correct' => 1),

                        )

                    );

                }

            }

            return $question_id;

        }

    }



    function SaveQuestionAL($data)

    {

        if (isset($data['id'])) {

            $id = $data['id'];

            unset($data['id']);

            return $this->db->update($this->questions, $data, array('id' => $id));

        } else {

            $insert = $this->db->insert($this->questions, $data);

            $question_id = $this->db->insert_id();

            if ($insert) {

                /***********************************************************************

                 *	Autor: Mario Adrián Martínez Fernández   Fecha: 28/09/2020

                 *		   mario.martinez.f@hotmail.es

                 *	Nota: Validamos que se haya insertado correctamente la pregunta

                 *          si se inserto correctamente validamos si el tipo de pregunta

                 *          lleva por default respuestas.

                 ***********************************************************************/

                if ($data['type_id'] == TIPO_PREGUNTA_PROPORCIONES) {

                    $this->db->insert_batch(

                        $this->question_answer,

                        array(

                            array('answer' => 'Poco', 'question_id' => $question_id, 'correct' => 1),

                            array('answer' => 'Mucho', 'question_id' => $question_id, 'correct' => 1),

                        )

                    );

                }

            }

            $query = "insert into questions_a_l_conf(question_id,lunes,martes,miercoles,jueves,viernes,sabado,domingo) 

                     values ($question_id,0,0,0,0,0,0,0)";

            $this->db->query($query);

            return $insert;

        }

    }

    /***********************************************************************

     *	Autor: Mario Adrián Martínez Fernández   Fecha: 28/09/2020

     *		   mario.martinez.f@hotmail.es

     *	Nota: Funcion para obtener el detalle de una respuesta

     ***********************************************************************/

    function AnswerDetail($params)

    {

        $this->db->select('q.id, q.points, qt.answer, qa.correct, q.type_id, qa.answer as description');

        $this->db->from($this->questions . ' q');

        $this->db->join($this->question_type . ' qt', 'q.type_id = qt.id');

        $this->db->join($this->question_answer . ' qa', 'qa.question_id = q.id');

        $this->db->where('qa.id =', $params['answer_id']);

        $answers = $this->db->get()->result_array();

        if (

            $answers[0]['type_id'] == TIPO_PREGUNTA_MULTIPLE_IMAGEN ||

            $answers[0]['type_id'] == TIPO_PREGUNTA_UNICA_IMAGEN ||

            $answers[0]['type_id'] == TIPO_PREGUNTA_TACHE_PALOMA ||

            $answers[0]['type_id'] == TIPO_PREGUNTA_DIBUJO

        ) {

            $url = base_url('uploads/business_' . $params['business_id'] . '/preguntas/');

            $answers[0]['description'] = $url . $answers[0]['description'];

        }

        return $answers;

    }

    /***********************************************************************

     *	Autor: Mario Adrián Martínez Fernández   Fecha: 28/09/2020

     *		   mario.martinez.f@hotmail.es

     *	Nota: Funcion para registrar una respuesta nueva nueva

     ***********************************************************************/

    function SaveAnswer($data)

    {

        if (isset($data['id'])) {

            $id = $data['id'];

            unset($data['id']);

            return $this->db->update($this->question_answer, $data, array('id' => $id));

        } else {

            return $this->db->insert($this->question_answer, $data);

        }

    }

    /***********************************************************************

     *	Autor: Mario Adrián Martínez Fernández   Fecha: 12/18/2020

     *		   mario.martinez.f@hotmail.es

     *	Nota: Funcion para obtener las respuestas de los usuario en base a

     * 			las respuestas.

     ***********************************************************************/

    function ListAnswerUsers($data)
    {
        $query = "SELECT u.id, qau.id, q.question, if(qt.answer = 1,qa.answer,qau.answer) as answer, date, if(qt.answer = 1,qa.correct,1) as correct, concat(u.name,' ',u.last_name) as name_user
        FROM question_answer_users qau
        JOIN questions q ON q.id = qau.question_id
        JOIN question_quiz qq ON qq.id = q.quiz_id
        JOIN user u on u.id = qau.user_id
        JOIN question_type qt on qt.id = q.type_id
        LEFT JOIN question_answers qa on q.id = qa.question_id and qau.answer = qa.id
        WHERE qq.business_id = '".$data['business_id']."'";
        if ($data['quiz_id'] !== '') {
            $query .= " AND q.quiz_id = '".$data['quiz_id']."'";
        }
        $query = $this->db->query($query)->result_array();
        if (count($query) > 0) {
            return $query;
        } else {
            return false;
        }
    }

    function VerificarServicioContratado($business_id)

    {

        $this->db->select("*");

        $this->db->from("hired_services");

        $this->db->where("business_id", $business_id);

        $this->db->where("services_id", 17);

        $result = $this->db->get()->result_array();

        if (count($result) > 0) {

            return true;

        } else {

            return false;

        }

    }



    function ComprobarAmbienteLaboralContestado($user_id, $quiz_id)

    {

        $query = "select date_format(fecha_inicio,'%Y-%m-%d') as fecha_inicio,date_format(fecha_fin,'%Y-%m-%d') as fecha_fin  from configuraciones_a_l";

        $rango = $this->db->query($query)->result_array()[0];

        $fecha_inicio = $rango["fecha_inicio"];

        $fecha_fin = $rango["fecha_fin"];



        $fecha_actual = strtotime(date("d-m-Y H:i:00", time()));

        $inicio = strtotime($fecha_inicio);

        $fin = strtotime($fecha_fin);

        $result = [];

        if ($fecha_actual >= $inicio && $fecha_actual <= $fin) {

            $query = "select * from question_answer_users as qau

            join questions as q on q.id = qau.question_id

            join question_quiz as qq on qq.id = q.quiz_id

            where qau.user_id = " . $user_id . " ";

            //esto es solo para yastas

            $query .= " and date_format(qau.date,'%Y-%m-%d') >= '$fecha_inicio' && date_format(qau.date,'%Y-%m-%d') <= '$fecha_fin'";

            //validacion de fecha (dia) se quita en yastas porque quieren que solo se conteste una vez

            $query .= "and date_format(qau.date,'%Y-%m-%d') = '" . date("Y-m-d") . "'";

            $query .= " and qq.category_id = 5 and qq.id = $quiz_id";

            $result = $this->db->query($query)->result_array();

        } else {

            return true;

        }

        if (count($result) > 0) {

            return true;

        } else {

            return false;

        }

    }



    public function validarIngresoEnFecha($id_user)

    {

        $this->db->select('fecha_login');

        $this->db->from('historial_sesiones');

        $this->db->where('id_user', $id_user);

        $this->db->where("date_format(fecha_login,'%Y-%m-%d') < ", "2022-03-11");

        $result = $this->db->get()->result_array();

        if (count($result) > 0)

            return true;

        return false;

    }



    public function validaPrimerIngreso($id_user)

    {

        $this->db->from('historial_sesiones')->where('id_user', $id_user);

        $rows = $this->db->count_all_results();

        if ($rows == 1 && $rows != 0) {

            //otenemos el ultimo ingreso de fecha y comparamos al dia de hoy

            $this->db->select('fecha_login');

            $this->db->from('historial_sesiones');

            $this->db->where('id_user', $id_user);

            $this->db->order_by('id', 'desc');

            $this->db->limit(1);



            $row = $this->db->get()->result_array();

            $now = date("Y-m-d");



            $today = new DateTime($now);

            $lastLogin = new DateTime($row["0"]["fecha_login"]);



            $diff = $lastLogin->diff($today);

            // will output 2 days

            $dias =  $diff->days;

            if ($dias < 1) {

                $query = "select video_visto from user where id = $id_user";

                $video_visto = $this->db->query($query)->result_array()[0]["video_visto"];

                if ($video_visto == 0)

                    return true;

                //sera nuevo

                return false;

            } else {

                //sera viejo

                return false;

            }

        } else {

            $query = "select video_visto from user where id = $id_user";

            $video_visto = $this->db->query($query)->result_array()[0]["video_visto"];

            if ($video_visto == 0)

                return true;

            return false;

        }

    }



    function eliminar_respuesta($id_respuesta)

    {

        $this->db->where("id", $id_respuesta);

        return $this->db->delete("question_answers");

    }

    function agregarUsuarios($usuarios, $quiz_id)
    {
        for ($i = 0; $i < count($usuarios); $i++) {
            $usuario = [];
            $usuario["user_id"] = $usuarios[$i]["id"];
            $usuario["quiz_id"] = $quiz_id;
            $this->agregarUsuario($usuario);
        }
    }
     
    function agregarUsuario($usuario)

    {

        return $this->db->insert("quiz_users", $usuario);

    }



    function eliminarUsuario($user_id, $quiz_id)

    {

        $this->db->where("user_id", $user_id);

        $this->db->where("quiz_id", $quiz_id);

        return $this->db->delete("quiz_users");

    }

    function agregarGrupos($grupos, $quiz_id)
    {
        for ($i = 0; $i < count($grupos); $i++) {
            $grupo = [];
            $grupo["group_id"] = $grupos[$i]["id"];
            $grupo["quiz_id"] = $quiz_id;
            $this->agregarGrupo($grupo);
        }
    }
     
    function agregarGrupo($grupo)

    {

        return $this->db->insert("quiz_groups", $grupo);

    }



    function eliminarGrupo($group_id, $quiz_id)

    {

        $this->db->where("group_id", $group_id);

        $this->db->where("quiz_id", $quiz_id);

        return $this->db->delete("quiz_groups");

    }



    function obtener_quiz_capacitacion($business_id)

    {

        $query = "select * from question_quiz where business_id = $business_id and capacitacion_obligatoria =1";

        return $this->db->query($query)->result_array();

    }



    function obtener_quiz_a_l_activo($business_id)

    {

        $query = "select id from question_quiz where business_id = $business_id and category_id = 5 and activo_al = 1";

        $result = $this->db->query($query)->result_array();

        if(count($result) > 0){

            return $result[0]["id"];

        }

        return [];

    }

    function obtener_visto($id, $user_id)
    {        
        $this->db->select("id");
        $this->db->from("quiz_usage");
        $this->db->where("quiz_id", $id);
        $this->db->where("user_id", $user_id);
        $visto = $this->db->get()->result_array();
        return count($visto) > 0 ? 1 : 0;
    }

    public function SetVisto($data)
    {
        $dataa = array(
            "quiz_id" => $data["quiz_id"],
            "user_id" => $data["user_id"],
        );
        $query = "SELECT * FROM quiz_usage WHERE quiz_id = '".$data["quiz_id"]."' AND user_id = '".$data["user_id"]."'";
        $result = $this->db->query($query)->result_array();
        if(count($result) > 0){
            /* actualizar */
            $veces_visto = $result[0]["veces_visto"] + 1;
            $this->db->set('veces_visto', $veces_visto);
            $this->db->where('id', $result[0]["id"]);
            return $this->db->update('quiz_usage');
        }else{
            /* insertar */
            return $this->db->insert("quiz_usage", $dataa);
        }
    }

    /***********************************************************************
	 *	Autor: Francisco Avalos   Fecha: 17/11/2022
	 *	Nota: Funcion para retornar evaluaciones realizadas a usuarios
	 ***********************************************************************/
    public function getEvaluacionesByUsuario($data)
    {
        /* validar que el user_id_evaluado se encuentre relaciodo al usuario */
        $query = "SELECT tb.*
        FROM user u
        JOIN tutores_becarios tb
            ON tb.becario_id = {$data['user_id_evaluado']} 
            AND tb.tutor_id = {$data['user_id']}
            AND tb.active = 1";
        $usuario_asignado = $this->db->query($query)->result_array();
        if(empty($usuario_asignado)){
            return [];
        }
        /* Obtener lista de cuestionarios asociados al usaurio id */
        $query = "SELECT qq.id, qq.name, qq.created_at, qq.fecha_limite
            FROM question_quiz qq
            JOIN users_groups ug
                ON ug.user_id = {$data['user_id']}
            JOIN quiz_groups qg
                ON qq.id = qg.quiz_id
                AND qg.group_id = ug.group_id
            WHERE qq.active = 1
            ORDER BY qq.created_at ASC;";
        $idsCuestionarios = $this->db->query($query)->result_array();
        if(!empty($idsCuestionarios)){
            $evaluaciones = [];
            foreach($idsCuestionarios as $idQuiz){
                /* validar que el user_id_evaluado fue asignado al usuario entre las fechas del cuestionario */
                if (!($usuario_asignado[0]["created_at"] <= $idQuiz["created_at"])){
                    continue;
                }
                /* contador de preguntas */
                $query = "SELECT COUNT(*) as totalPreguntas
                    FROM questions 
                    WHERE quiz_id = {$idQuiz['id']}";
                $totalPreguntas = $this->db->query($query)->result_array();
                /* contador de respuestas donde solo cuente respuestas a preguntas no duplicadas*/
                $query = "SELECT 
                    COUNT(DISTINCT(question_id)) as totalRespuestas
                    FROM question_answer_users 
                    WHERE user_id = {$data['user_id']} 
                    AND user_id_evaluado = {$data['user_id_evaluado']} 
                    AND question_id IN (
                        SELECT id 
                        FROM questions 
                        WHERE quiz_id = {$idQuiz['id']})
                ";
                $preguntasContestadas = $this->db->query($query)->result_array();
                if($preguntasContestadas[0]["totalRespuestas"] > 0 && $preguntasContestadas[0]["totalRespuestas"] < $totalPreguntas[0]["totalPreguntas"]){
                    /* evaluacion incompleta*/
                    $evaluaciones[] = [
                        "id" => $idQuiz['id'],
                        "cuestionario" => $idQuiz['name'],
                        "fechaInicio" => $idQuiz['created_at'],
                        "fechaFin" => $idQuiz['fecha_limite'],
                        "estatus" => "incompleta",
                    ];
                }elseif($preguntasContestadas[0]["totalRespuestas"] > 0 && $preguntasContestadas[0]["totalRespuestas"] >= $totalPreguntas[0]["totalPreguntas"]){
                    /* evaluación completa */
                    $evaluaciones[] = [
                        "id" => $idQuiz['id'],
                        "cuestionario" => $idQuiz['name'],
                        "fechaInicio" => $idQuiz['created_at'],
                        "fechaFin" => $idQuiz['fecha_limite'],
                        "estatus" => "completa",
                    ];
                }else{
                     /* evaluación no realizada */
                     $evaluaciones[] = [
                        "id" => $idQuiz['id'],
                        "cuestionario" => $idQuiz['name'],
                        "fechaInicio" => $idQuiz['created_at'],
                        "fechaFin" => $idQuiz['fecha_limite'],
                        "estatus" => "no realizada",
                    ];
                }
            }
            return $evaluaciones;
        }
        return [];
    }

}

