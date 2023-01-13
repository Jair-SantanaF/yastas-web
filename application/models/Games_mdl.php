<?php
class Games_mdl extends CI_Model
{
    private $tableProductsQuiz      = "game_products_quiz",
        $tableProductsSteps     = "game_products_steps",
        $tableProductsResults   = "game_products_results",
        $tableRouletteQuiz      = "game_roulette_quiz",
        $tableRouletteQuestion  = "game_roulette_questions",
        $tableRouletteAnswer    = "game_roulette_question_answers",
        $tableRouletteResults   = "game_roulette_results",
        $tableRunPanchoResults  = "game_run_pancho_results",
        $tableSnakeResults      = "game_snake_results",
        $tableSnakeTemas        = "game_snake_temas",
        $tableRunPanchoTemas    = "game_run_pancho_run_temas",
        $tableProfilerQuiz      = "profiler_quiz",
        $tableProfilerQuestion  = "profiler_question",
        $tableProfilerAnswer    = "profiler_question_answer",
        $tableProfilerResults   = "profiler_results",
        $tableGames             = "games",
        $tableGamesBusiness     = "services_games",
        $tableUser              = "user";

    function __construct()
    {
        parent::__construct();
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para guardar un quiz nuevo
     ***********************************************************************/
    function SaveProductsQuiz($data)
    {
        $dataa = array(
            "description" => $data["description"],
            "points" => $data["points"],
            "active" => 1,
            "business_id" => $data["business_id"]
        );

        if ($data['image'] != '')
            $dataa['image'] = $data['image'];

        if ($this->db->insert($this->tableProductsQuiz, $dataa)) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para editar un quiz
     ***********************************************************************/
    function EditProductsQuiz($data)
    {
        $key = array('id' => $data["id"]);

        $dataa = array(
            "description" => $data["description"],
            "points" => $data["points"]
        );

        if ($this->db->update($this->tableProductsQuiz, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar un quiz
     ***********************************************************************/
    function DeleteProductsQuiz($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableProductsQuiz, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 21/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los quiz de productos
     ***********************************************************************/
    function ProductQuiz($business_id)
    {
        $this->db->select('id, description, points, image');
        $result = $this->db->get_where($this->tableProductsQuiz, array('business_id' => $business_id, 'active' => 1))->result_array();
        if (count($result) > 0) {
            $url_upload = base_url('uploads/business_' . $business_id . '/games/products/');
            foreach ($result as $index => $value) {
                $result[$index]['image'] = $url_upload . $value['image'];
            }
            return $result;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para guardar una pregunta del quiz
     ***********************************************************************/
    function SaveProductsStep($data)
    {
        $dataa = array(
            "num_step" => $data["num_step"],
            "option_description" => $data["option_description"],
            "quiz_id" => $data["quiz_id"],
            "correct" => 1,
            "active" => 1,
            "business_id" => $data["business_id"]
        );

        if ($this->db->insert($this->tableProductsSteps, $dataa)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para editar una pregunta del quiz
     ***********************************************************************/
    function EditProductsStep($data)
    {
        $key = array('id' => $data["id"]);

        $dataa = array(
            "num_step" => $data["num_step"],
            "option_description" => $data["option_description"],
        );

        if ($data['option_image'] != '')
            $dataa['option_image'] = $data['option_image'];

        if ($this->db->update($this->tableProductsSteps, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 21/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los pasos en base al quiz enviado
     ***********************************************************************/
    function ProductSteps($params)
    {
        $this->db->select('id,num_step,option_description,option_image,quiz_id');
        $result = $this->db->get_where($this->tableProductsSteps, array('quiz_id' => $params['quiz_id']))->result_array();
        if (count($result) > 0) {
            $url_upload = base_url('uploads/business_' . $params['business_id'] . '/games/products/');
            foreach ($result as $index => $value) {
                $result[$index]['option_image'] = $url_upload . $value['option_image'];
            }
            return $result;
        } else {
            return false;
        }
    }
    function ProductSteps_($quiz_id)
    {
        $this->db->select('num_step');
        $this->db->from($this->tableProductsSteps . ' ps');
        $this->db->where('ps.quiz_id =', $quiz_id);
        $this->db->where('ps.correct =', 1);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            foreach ($result as $index => $value) {
                $this->db->select('id,option_description,option_image,quiz_id,correct');
                $this->db->from($this->tableProductsSteps . ' ps');
                $this->db->where('ps.quiz_id =', $quiz_id);
                $this->db->where('ps.num_step =', $value['num_step']);
                $options = $this->db->get()->result_array();
                $result[$index]['options'] = $options;
            }
            return $result;
        }
    }


    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los resultados del quiz
     ***********************************************************************/
    function ProductsResults($business_id)
    {
        $this->db->select('
            pr.id, pr.user_id, pr.quiz_id, pr.step_id, pr.created_at, pr.created_at,
            concat(u.name, " ", u.last_name) AS name, 
            ps.num_step, ps.option_description, ps.option_image, ps.correct,            
            pq.description AS quiz
            ');
        $this->db->from($this->tableProductsResults . ' as pr');
        $this->db->join($this->tableUser . ' as u', 'pr.user_id = u.id');
        $this->db->join($this->tableProductsSteps . ' as ps', 'pr.step_id = ps.id');
        $this->db->join($this->tableProductsQuiz . ' as pq', 'pr.quiz_id = pq.id');
        $this->db->where('pq.business_id =', $business_id);
        //print_r($this->db->get_compiled_select());exit;
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 21/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para guardar las respuestas de los usuario en productos
     ***********************************************************************/
    function SaveAnswerProducts($data)
    {
        unset($data['token']);
        if ($this->db->insert($this->tableProductsResults, $data)) {
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Actualizamos el score del usuario en base a los puntos que haya
             *          ganado o perdido
             ***********************************************************************/
            $this->db->select('score');
            $score = $this->db->get_where('user', array('id' => $data['user_id']))->result_array();
            $score = intval($score[0]['score']);
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Obtenemos si la respuesta que dio es la correcta y obtenemos
             *          los puntos que se le restaran o aumentaran
             ***********************************************************************/
            $this->db->select('gpq.points,gps.correct');
            $this->db->from($this->tableProductsSteps . ' as gps');
            $this->db->join($this->tableProductsQuiz . ' as gpq', 'gps.quiz_id = gpq.id');
            $this->db->where('gps.id =', $data['step_id']);
            $validate_answer = $this->db->get()->result_array();
            if ($validate_answer[0]['correct'] == 1) {
                $score = $score + intval($validate_answer[0]['points']);
            } else {
                $score = $score - intval($validate_answer[0]['points']);
                if ($score < 0) {
                    $score = 0;
                }
            }
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Actualizamos el score del usuario
             ***********************************************************************/
            return $this->db->update('user', array('score' => $score), array('id' => $data['user_id']));
        } else {
            return false;
        }
    }
    function SaveAnswerProducts_($data)
    {
        unset($data['token']);
        if ($this->db->insert($this->tableProductsResults, $data)) {
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Actualizamos el score del usuario en base a los puntos que haya
             *          ganado o perdido
             ***********************************************************************/
            $this->db->select('score');
            $score = $this->db->get_where('user', array('id' => $data['user_id']))->result_array();
            $score = intval($score[0]['score']);
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Obtenemos si la respuesta que dio es la correcta y obtenemos
             *          los puntos que se le restaran o aumentaran
             ***********************************************************************/
            $this->db->select('gpq.points,gps.correct');
            $this->db->from($this->tableProductsSteps . ' as gps');
            $this->db->join($this->tableProductsQuiz . ' as gpq', 'gps.quiz_id = gpq.id');
            $this->db->where('gps.id =', $data['step_id']);
            $validate_answer = $this->db->get()->result_array();
            if ($validate_answer[0]['correct'] == 1) {
                $score = $score + intval($validate_answer[0]['points']);
            } else {
                $score = $score - intval($validate_answer[0]['points']);
                if ($score < 0) {
                    $score = 0;
                }
            }
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Actualizamos el score del usuario
             ***********************************************************************/
            $this->db->update('user', array('score' => $score), array('id' => $data['user_id']));
            return array('correct' => $validate_answer[0]['correct']);
        } else {
            return false;
        }
    }

    /***************************JUEGO RULETA********************************/
    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para guardar un quiz nuevo
     ***********************************************************************/
    function SaveRouletteQuiz($data)
    {
        $dataa = array(
            "name" => $data["name"],
            "points" => $data["points"],
            "active" => 1,
            "business_id" => $data["business_id"]
        );

        if ($this->db->insert($this->tableRouletteQuiz, $dataa)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para editar un quiz
     ***********************************************************************/
    function EditRouletteQuiz($data)
    {
        $key = array('id' => $data["id"]);

        $dataa = array(
            "name" => $data["name"],
            "points" => $data["points"]
        );

        if ($this->db->update($this->tableRouletteQuiz, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar un quiz
     ***********************************************************************/
    function DeleteRouletteQuiz($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableRouletteQuiz, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los quiz de ruleta
     ***********************************************************************/
    function RouletteQuiz($business_id)
    {
        $this->db->select('id,name, points');
        $result = $this->db->get_where($this->tableRouletteQuiz, array('business_id' => $business_id, 'active' => 1))->result_array();
        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para guardar una pregunta del quiz
     ***********************************************************************/
    function SaveRouletteQuestion($data)
    {
        $dataa = array(
            "question" => $data["question"],
            "quiz_id" => $data["quiz_id"],
            "active" => 1,
            "business_id" => $data["business_id"]
        );

        if ($this->db->insert($this->tableRouletteQuestion, $dataa)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para editar una pregunta del quiz
     ***********************************************************************/
    function EditRouletteQuestion($data)
    {
        $key = array('id' => $data["id"]);

        $dataa = array(
            "question" => $data["question"]
        );

        if ($this->db->update($this->tableRouletteQuestion, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar una pregunta del quiz
     ***********************************************************************/
    function DeleteRouletteQuestion($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableRouletteQuestion, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para listar preguntas de un quizz
     ***********************************************************************/
    function RouletteQuestions($quiz_id)
    {
        $this->db->select('id,question');
        $this->db->from($this->tableRouletteQuestion);
        $this->db->where("quiz_id", $quiz_id);
        $this->db->where("active", 1);
        $questions = $this->db->get()->result_array();
        // $questions = $this->db->get_where($this->tableRouletteQuestion, array('quiz_id'=>$quiz_id, 'active'=>1))->result_array();
        if (count($questions) > 0) {
            // for ($i = 0; $i < count($questions); $i++) {
            //     $questions[$i]["question"] .= $quiz_id;
            // }
            return $questions;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para guardar una respuesta a pregunta del quiz
     ***********************************************************************/
    function SaveRouletteAnswer($data)
    {
        $dataa = array(
            "question_id" => $data["question_id"],
            "answer" => $data["answer"],
            "correct" => $data["correct"],
            "active" => 1
        );

        if ($this->db->insert($this->tableRouletteAnswer, $dataa)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para editar una respuesta a pregunta del quiz
     ***********************************************************************/
    function EditRouletteAnswer($data)
    {
        $key = array('id' => $data["id"]);

        $dataa = array(
            'question_id' => $data["question_id"],
            'answer' => $data["answer"],
            'correct' => $data["correct"],
        );

        if ($this->db->update($this->tableRouletteAnswer, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar una respuesta a pregunta del quiz
     ***********************************************************************/
    function DeleteRouletteAnswer($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableRouletteAnswer, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para listar las respuestas a pregunta del quiz
     ***********************************************************************/
    function RouletteAnswers($question_id)
    {
        $this->db->select('id, answer, correct');
        $questions = $this->db->get_where($this->tableRouletteAnswer, array('question_id' => $question_id, 'active' => 1))->result_array();
        if (count($questions) > 0) {
            return $questions;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los resultados del quiz
     ***********************************************************************/
    function RouletteResults($business_id)
    {
        $this->db->select('rr.id, rr.question_id, rr.answer_id, rr.user_id, rr.created_at, concat(u.name, " ", u.last_name) AS name, rq.question, ra.answer, ra.correct, rqz.name AS quiz');
        $this->db->from($this->tableRouletteResults . ' as rr');
        $this->db->join($this->tableUser . ' as u', 'rr.user_id = u.id');
        $this->db->join($this->tableRouletteQuestion . ' as rq', 'rr.question_id = rq.id');
        $this->db->join($this->tableRouletteQuiz . ' as rqz', 'rq.quiz_id = rqz.id');
        $this->db->join($this->tableRouletteAnswer . ' as ra', 'rr.answer_id = ra.id');
        $this->db->where('rqz.business_id =', $business_id);
        //$this->db->where('grq.id =',$data['question_id']);

        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    /***********************************************************************
	 *	Autor: Luis Angel Trujillo Gonzalez   Fecha: 29/04/2022
	 *	Nota: Funcion para obtener los resultados de quiz Run Pancho
	 ***********************************************************************/
    function RunPanchoResults($business_id,$data)
    {
        $this->db->select('concat(u.name," ",u.last_name) usuario, concat("#", u.number_employee) num_empleado, ifnull(rp.score - (ifnull(rp.incorrectas,0)),0) score, rpt.nombre tema, date_format(rp.fecha,"%d-%m-%Y") fecha, ifnull(rp.score,0) correctas, ifnull(incorrectas,0) incorrectas');
        $this->db->from($this->tableRunPanchoResults . ' as rp');
        $this->db->join($this->tableUser . ' as u', 'rp.user_id = u.id');
        $this->db->join($this->tableRunPanchoTemas . ' as rpt', 'rpt.id = rp.id_tema');
        $this->db->where('u.business_id =', $business_id);
        $this->db->where('date_format(rp.fecha,"%Y-%m-%d") >= ', $data["fecha_inicio"]);
        $this->db->where('date_format(rp.fecha,"%Y-%m-%d") <= ', $data["fecha_fin"]);
        $this->db->order_by('rp.fecha','DESC');
        //$this->db->where('grq.id =',$data['question_id']);

        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    /***********************************************************************
	 *	Autor: Luis Angel Trujillo Gonzalez   Fecha: 29/04/2022
	 *	Nota: Funcion para obtener los resultados de quiz Snake
	 ***********************************************************************/
    function snakeResults($business_id,$data)
    {
        $this->db->select('concat(u.name," ",u.last_name) usuario,concat("#",u.number_employee) num_empleado, (snr.score - snr.incorrectas)as score,snt.nombre tema, date_format(snr.fecha,"%d-%m-%Y") fecha,snr.score correctas,snr.incorrectas');
        $this->db->from($this->tableSnakeResults . ' as snr');
        $this->db->join($this->tableUser . ' as u', 'snr.user_id = u.id');
        $this->db->join($this->tableSnakeTemas . ' as snt', 'snt.id = snr.id_tema');
        $this->db->where('u.business_id =', $business_id);
        $this->db->where('date_format(snr.fecha,"%Y-%m-%d") >= ', $data["fecha_inicio"]);
        $this->db->where('date_format(snr.fecha,"%Y-%m-%d") <= ', $data["fecha_fin"]);
        $this->db->group_by('snr.fecha');
        $this->db->order_by('snr.fecha','DESC');
        //$this->db->where('grq.id =',$data['question_id']);

        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener las preguntas y respuestas de un quiz
     *          seleccionado
     ***********************************************************************/
    function RouletteQuestionsAnswer($quiz_id)
    {
        $this->db->select('id,question');
        $questions = $this->db->get_where($this->tableRouletteQuestion, array('quiz_id' => $quiz_id, "active" => 1))->result_array();
        if (count($questions) > 0) {
            foreach ($questions as $index => $value) {
                // $questions[$index]["question"] .= $quiz_id- ;
                $this->db->select('id,answer');
                $answers = $this->db->get_where($this->tableRouletteAnswer, array('question_id' => $value['id']))->result_array();
                if (count($answers) > 0) {
                    $questions[$index]['answers'] = $answers;
                } else {
                    $questions[$index]['answers'] = array();
                }
            }
            $questions = $this->ordenarAleatorio($questions);
            return $questions;
        } else {
            return false;
        }
    }

    function ordenarAleatorio($questions)
    {
        for ($i = 0; $i < count($questions); $i++) {
            $r = rand(0, count($questions) - 1);
            $temp = $questions[$r];
            $questions[$r] = $questions[$i];
            $questions[$i] = $temp;
        }
        return $questions;
    }

    /**********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota:Funcion para registrar respuestas de ruleta
     ***********************************************************************/
    function SaveAnswerRoulette($data, $tipo = 0)
    {
        unset($data['token']);
        unset($data["id_reto"]);
        if ($this->db->insert($this->tableRouletteResults, $data)) {
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Actualizamos el score del usuario en base a los puntos que haya
             *          ganado o perdido
             ***********************************************************************/
            $this->db->select('score');
            $score = $this->db->get_where('user', array('id' => $data['user_id']))->result_array();
            $score = intval($score[0]['score']);
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Obtenemos si la respuesta que dio es la correcta y obtenemos
             *          los puntos que se le restaran o aumentaran
             ***********************************************************************/
            $this->db->select('grz.points,gra.correct');
            $this->db->from($this->tableRouletteQuestion . ' as grq');
            $this->db->join($this->tableRouletteAnswer . ' as gra', 'gra.question_id = grq.id');
            $this->db->join($this->tableRouletteQuiz . ' as grz', 'grz.id = grq.quiz_id');
            $this->db->where('grq.id =', $data['question_id']);
            $this->db->where('gra.id =', $data['answer_id']);
            $validate_answer = $this->db->get()->result_array();
            $encontro_pregunta = 1;
            if (count($validate_answer) > 0) {
                if ($validate_answer[0]['correct'] == 1) {
                    $score = $score + intval($validate_answer[0]['points']);
                } else {
                    $score = $score - intval($validate_answer[0]['points']);
                    if ($score < 0) {
                        $score = 0;
                    }
                }
                if ($validate_answer[0]['correct'] == 1) {
                    $text = 'correct';
                } else {
                    $text = $this->obtener_respuesta_correcta($data);
                    // $text = 'incorrect';
                }
            } else {
                $this->db->select('qz.points');
                $this->db->from($this->tableRouletteQuestion . ' as q');
                $this->db->join($this->tableRouletteQuiz . ' as qz', 'q.quiz_id = qz.id');
                $this->db->where('q.id =', $data['question_id']);
                $points = $this->db->get()->result_array();
                if (count($points) > 0) {
                    $score = $score - intval($points[0]['points']);
                    if ($score < 0) {
                        $score = 0;
                    }
                    $text = $this->obtener_respuesta_correcta($data);
                } else {
                    $encontro_pregunta = 0;
                }
                // $text = 'incorrect';
            }
            if ($encontro_pregunta == 0) {
                $query = "select qa.question_id from game_roulette_question_answers as qa
                where qa.id = " . $data["answer_id"];
                $data["question_id"] = $this->db->query($query)->result_array()[0]["question_id"];

                $this->db->select('grz.points,gra.correct');
                $this->db->from($this->tableRouletteQuestion . ' as grq');
                $this->db->join($this->tableRouletteAnswer . ' as gra', 'gra.question_id = grq.id');
                $this->db->join($this->tableRouletteQuiz . ' as grz', 'grz.id = grq.quiz_id');
                $this->db->where('grq.id =', $data['question_id']);
                $this->db->where('gra.id =', $data['answer_id']);
                $validate_answer = $this->db->get()->result_array();
                $encontro_pregunta = 1;
                if (count($validate_answer) > 0) {
                    if ($validate_answer[0]['correct'] == 1) {
                        $score = $score + intval($validate_answer[0]['points']);
                    } else {
                        $score = $score - intval($validate_answer[0]['points']);
                        if ($score < 0) {
                            $score = 0;
                        }
                    }
                    if ($validate_answer[0]['correct'] == 1) {
                        $text = 'correct';
                    } else {
                        $text = $this->obtener_respuesta_correcta($data);
                        // $text = 'incorrect';
                    }
                } else {
                    $this->db->select('qz.points');
                    $this->db->from($this->tableRouletteQuestion . ' as q');
                    $this->db->join($this->tableRouletteQuiz . ' as qz', 'q.quiz_id = qz.id');
                    $this->db->where('q.id =', $data['question_id']);
                    $points = $this->db->get()->result_array();
                    if (count($points) > 0) {
                        $score = $score - intval($points[0]['points']);
                        if ($score < 0) {
                            $score = 0;
                        }
                        $text = $this->obtener_respuesta_correcta($data);
                    } else {
                        $encontro_pregunta = 0;
                    }
                    // $text = 'incorrect';
                }
            }
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Actualizamos el score del usuario
             ***********************************************************************/
            if ($tipo == 0)
                $this->db->update('user', array('score' => $score), array('id' => $data['user_id']));
            return $text;
        } else {
            return false;
        }
    }

    function obtener_respuesta_correcta($data)
    {
        $this->db->select('gra.answer,grz.points,gra.correct');
        $this->db->from($this->tableRouletteQuestion . ' as grq');
        $this->db->join($this->tableRouletteAnswer . ' as gra', 'gra.question_id = grq.id');
        $this->db->join($this->tableRouletteQuiz . ' as grz', 'grz.id = grq.quiz_id');
        $this->db->where('grq.id =', $data['question_id']);

        $this->db->where('gra.correct', 1);
        return $this->db->get()->result_array()[0]["answer"];
    }


    /***************************JUEGO PROFILER******************************/
    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para guardar un quiz nuevo
     ***********************************************************************/
    function SaveProfilerQuiz($data)
    {
        $dataa = array(
            "history" => $data["history"],
            "points" => $data["points"],
            "active" => 1,
            "business_id" => $data["business_id"]
        );

        if ($this->db->insert($this->tableProfilerQuiz, $dataa)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para editar un quiz
     ***********************************************************************/
    function EditProfilerQuiz($data)
    {
        $key = array('id' => $data["id"]);

        $dataa = array(
            "history" => $data["history"],
            "points" => $data["points"]
        );

        if ($this->db->update($this->tableProfilerQuiz, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar un quiz
     ***********************************************************************/
    function DeleteProfilerQuiz($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableProfilerQuiz, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para optener los quiz de profiler
     ***********************************************************************/
    function ProfilerQuiz($business_id)
    {
        $this->db->select('id, history, points');
        $result = $this->db->get_where($this->tableProfilerQuiz, array('business_id' => $business_id, 'active' => 1))->result_array();
        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para guardar una pregunta del quiz
     ***********************************************************************/
    function SaveProfilerQuestion($data)
    {
        $dataa = array(
            "question" => $data["question"],
            "quiz_id" => $data["quiz_id"],
            "active" => 1,
            "status" => 1,
            "order" => 1,
            "business_id" => $data["business_id"]
        );

        if ($data['image'] != '')
            $dataa['image'] = $data['image'];

        if ($this->db->insert($this->tableProfilerQuestion, $dataa)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para editar una pregunta del quiz
     ***********************************************************************/
    function EditProfilerQuestion($data)
    {
        $key = array('id' => $data["id"]);

        $dataa = array(
            "question" => $data["question"]
        );

        if ($data['image'] != '')
            $dataa['image'] = $data['image'];

        if ($this->db->update($this->tableProfilerQuestion, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar una pregunta del quiz
     ***********************************************************************/
    function DeleteProfilerQuestion($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableProfilerQuestion, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para listar preguntas de un quizz
     ***********************************************************************/
    function ProfilerQuestions($params)
    {
        $this->db->select('id,question,image');
        $questions = $this->db->get_where($this->tableProfilerQuestion, array('quiz_id' => $params['quiz_id'], 'active' => 1))->result_array();
        if (count($questions) > 0) {
            $url_upload = base_url('uploads/business_' . $params['business_id'] . '/games/profiler/');
            foreach ($questions as $index => $value) {
                $questions[$index]['image'] = $url_upload . $value['image'];
            }
            return $questions;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para guardar una respuesta a pregunta del quiz
     ***********************************************************************/
    function SaveProfilerAnswer($data)
    {
        $dataa = array(
            "question_id" => $data["question_id"],
            "answer" => $data["answer"],
            "correct" => $data["correct"],
            "active" => 1
        );

        if ($this->db->insert($this->tableProfilerAnswer, $dataa)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para editar una respuesta a pregunta del quiz
     ***********************************************************************/
    function EditProfilerAnswer($data)
    {
        $key = array('id' => $data["id"]);

        $dataa = array(
            'question_id' => $data["question_id"],
            'answer' => $data["answer"],
            'correct' => $data["correct"],
        );

        if ($this->db->update($this->tableProfilerAnswer, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar una respuesta a pregunta del quiz
     ***********************************************************************/
    function DeleteProfilerAnswer($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableProfilerAnswer, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para listar las respuestas a pregunta del quiz
     ***********************************************************************/
    function ProfilerAnswers($question_id)
    {
        $this->db->select('id, answer, correct');
        $questions = $this->db->get_where($this->tableProfilerAnswer, array('question_id' => $question_id, 'active' => 1))->result_array();
        if (count($questions) > 0) {
            return $questions;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los resultados del quiz
     ***********************************************************************/
    function ProfilerResults($business_id)
    {
        $this->db->select('pr.id, pr.question_id, pr.answer_id, pr.user_id, pr.created_at, concat(u.name, " ", u.last_name) AS name, pq.question, pa.answer, pa.correct, pqz.history AS quiz');
        $this->db->from($this->tableProfilerResults . ' as pr');
        $this->db->join($this->tableUser . ' as u', 'pr.user_id = u.id');
        $this->db->join($this->tableProfilerQuestion . ' as pq', 'pr.question_id = pq.id');
        $this->db->join($this->tableProfilerQuiz . ' as pqz', 'pq.quiz_id = pqz.id');
        $this->db->join($this->tableProfilerAnswer . ' as pa', 'pr.answer_id = pa.id');
        $this->db->where('pqz.business_id =', $business_id);
        //$this->db->where('grq.id =',$data['question_id']);
        //print_r($this->db->get_compiled_select());exit;
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los preguntas con sus respectivas
     *          respuestas de profile
     ***********************************************************************/
    function ProfilerQuestionsAnswer($params)
    {
        $this->db->select('id,question,image');
        $questions = $this->db->get_where($this->tableProfilerQuestion, array('quiz_id' => $params["quiz_id"]))->result_array();
        if (count($questions) > 0) {
            foreach ($questions as $index => $value) {
                $url_upload = base_url('uploads/business_' . $params["business_id"] . '/games/profiler/');
                $questions[$index]['image'] = $url_upload . $value['image'];
                $this->db->select('id,answer');
                $answers = $this->db->get_where($this->tableProfilerAnswer, array('question_id' => $value['id']))->result_array();
                if (count($answers) > 0) {
                    $questions[$index]['answers'] = $answers;
                } else {
                    $questions[$index]['answers'] = array();
                }
            }
            return $questions;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para guardar la respuesta del usuario y sumar o restar
     *          puntos.
     ***********************************************************************/
    function SaveAnswerProfiler($data)
    {
        unset($data['token']);
        if ($this->db->insert($this->tableProfilerResults, $data)) {
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Actualizamos el score del usuario en base a los puntos que haya
             *          ganado o perdido
             ***********************************************************************/
            $this->db->select('score');
            $score = $this->db->get_where('user', array('id' => $data['user_id']))->result_array();
            $score = intval($score[0]['score']);
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Obtenemos si la respuesta que dio es la correcta y obtenemos
             *          los puntos que se le restaran o aumentaran
             ***********************************************************************/
            $this->db->select('gpz.points,gpa.correct');
            $this->db->from($this->tableProfilerQuestion . ' as gpq');
            $this->db->join($this->tableProfilerAnswer . ' as gpa', 'gpa.question_id = gpq.id');
            $this->db->join($this->tableProfilerQuiz . ' as gpz', 'gpz.id = gpq.quiz_id');
            $this->db->where('gpq.id =', $data['question_id']);
            $validate_answer = $this->db->get()->result_array();
            if ($validate_answer[0]['correct'] == 1) {
                $score = $score + intval($validate_answer[0]['points']);
            } else {
                $score = $score - intval($validate_answer[0]['points']);
                if ($score < 0) {
                    $score = 0;
                }
            }
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Actualizamos el score del usuario
             ***********************************************************************/
            return $this->db->update('user', array('score' => $score), array('id' => $data['user_id']));
        } else {
            return false;
        }
    }


    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 06/07/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los juegos seleccionados y sin seleccionar
     *          por parte de la empresa
     ***********************************************************************/
    function ListGamesSelect($bussines_id)
    {
        $this->db->select('g.id,g.name,g.image,if(gb.business_id is null,0,if(gb.estatus = 1,1,0)) as service_hired');
        $this->db->from($this->tableGames . ' as g');
        $this->db->join($this->tableGamesBusiness . ' as gb', 'g.id = gb.game_id');
        $this->db->where('gb.business_id ', $bussines_id);
        // $this->db->where("g.id !=",2);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            foreach ($result as $index => $value) {
                if ($value['image'] !== '') {
                    $result[$index]['image'] = base_url('assets/img/images_games/') . $value['image'];
                }
            }
        } else {
            $result = false;
        }
        return $result;
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 06/07/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para registrar o quitar un juego de la lista a usar
     ***********************************************************************/
    function ActiveGame($data)
    {
        unset($data['token']);
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 06/07/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Validamos que no se haya registrado antes, si se registro
         *          entonces corremos un update el registro en base al registro
         *          que tenemos
         ***********************************************************************/
        $validate = $this->db->get_where($this->tableGamesBusiness, $data)->result_array();
        if (count($validate) == 0) {
            $this->db->insert($this->tableGamesBusiness, $data);
        } else {
            $estatus = ($validate[0]['estatus'] == 1) ? 0 : 1;
            $this->db->update($this->tableGamesBusiness, array('estatus' => $estatus), $data);
        }
    }

    function ObtenerReporteResultadosCulebra($token,$data){
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteResultadosCulebra" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
            $this->db->select('concat(u.name," ",u.last_name) usuario,concat("#",u.number_employee) num_empleado,snt.nombre tema, date_format(snr.fecha,"%d-%m-%Y") fecha,snr.score correctas,snr.incorrectas,(snr.score - snr.incorrectas) score');
            $this->db->from($this->tableSnakeResults . ' as snr');
            $this->db->join($this->tableUser . ' as u', 'snr.user_id = u.id');
            $this->db->join($this->tableSnakeTemas . ' as snt', 'snt.id = snr.id_tema');
            $this->db->where('u.business_id =', $data["business_id"]);
            $this->db->where('date_format(snr.fecha,"%Y-%m-%d") >= ', $data["fecha_inicio"]);
            $this->db->where('date_format(snr.fecha,"%Y-%m-%d") <= ', $data["fecha_fin"]);
            $this->db->group_by('snr.fecha');
            $this->db->order_by('snr.fecha','DESC');
            //$this->db->where('grq.id =',$data['question_id']);

        $resultado = [["USUARIO", "#_EMPLEADO", "TEMA", "FECHA", "CORRECTAS", "INCORRECTAS","SCORE"]];
        $resultado = array_merge($resultado, $this->db->get()->result_array());

        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }

    function ObtenerReporteResultadosRunPancho($token,$data){
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=ReporteResultadosRunPancho" . date('y-m-d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        $outss = fopen("php://output", "w");
            $this->db->select('concat(u.name," ",u.last_name) usuario, concat("#", u.number_employee) num_empleado, rpt.nombre tema, date_format(rp.fecha,"%d-%m-%Y") fecha, IFNULL(rp.score,0) correctas, IFNULL(rp.incorrectas,0) incorrectas, ifnull(rp.score - (ifnull(rp.incorrectas,0)),0) score');
            $this->db->from($this->tableRunPanchoResults . ' as rp');
            $this->db->join($this->tableUser . ' as u', 'rp.user_id = u.id');
            $this->db->join($this->tableRunPanchoTemas . ' as rpt', 'rpt.id = rp.id_tema');
            $this->db->where('u.business_id =', $data["business_id"]);
            $this->db->where('date_format(rp.fecha,"%Y-%m-%d") >= ', $data["fecha_inicio"]);
            $this->db->where('date_format(rp.fecha,"%Y-%m-%d") <= ', $data["fecha_fin"]);
            $this->db->order_by('rp.fecha','DESC');
            //$this->db->where('grq.id =',$data['question_id']);

        $resultado = [["USUARIO", "#_EMPLEADO", "TEMA", "FECHA", "CORRECTAS", "INCORRECTAS","SCORE"]];
        $resultado = array_merge($resultado, $this->db->get()->result_array());

        foreach ($resultado as $rows) {
            fputcsv($outss, $rows);
        }
        fclose($outss);
        return;
    }
}
