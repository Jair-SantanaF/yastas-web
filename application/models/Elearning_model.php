<?php

class Elearning_model extends CI_Model
{

    private $tableName = "elearning_modules",
        $tableCategories = "elearning_categories",
        $tableSubcategories = "elearning_subcategories",
        $tableScoreLog = "elearning_score_log",
        $tableQuizQuestions = "question_quiz",
        $tableUsersGroups = "users_groups",
        $table_groups = "groups",
        $tableElearningGroups = "elearning_groups";

    function __construct()
    {
        parent::__construct();
    }

    public function insert($entity)
    {
        if (isset($entity['id'])) {
            $id = $entity['id'];
            unset($entity['id']);
            return $this->db->update($this->tableName, $entity, array('id' => $id));
        } else {
            $this->db->insert($this->tableName, $entity);
            $id_insert = $this->db->insert_id();
            $this->db->update($this->tableQuizQuestions, array('connection_id' => $id_insert), array('id' => $entity['quiz_satisfaction_id']));
            $this->db->update($this->tableQuizQuestions, array('connection_id' => $id_insert), array('id' => $entity['quiz_final_evaluation_id']));
            return $id_insert;
        }
    }

    public function update($id, $entity)
    {
        if ($this->db->update($this->tableName, $entity, array("id" => $id))) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        if ($this->db->delete($this->tableName, array('id' => $id))) {
            return true;
        } else {
            return false;
        }
    }

    public function fetchAll($datos)
    {
        if (isset($_SESSION['id_user'])) {
            $filtro = array();
            // $filtro['em.active'] = 1;
            if (isset($datos['category_id'])) {
                $filtro['em.category_id'] = $datos['category_id'];
            }
            if (isset($datos['subcategory_id'])) {
                $filtro['em.subcategory_id'] = $datos['subcategory_id'];
            }
            //$filtro['em.business_id'] = $datos['business_id'];
            $this->db->select('
                    em.*, coalesce(date_format(em.fecha_limite,"%d %M %Y"),"") as fecha_limite,
                    ifnull(qqs.name,"Sin asignar") as name_satisfaction, 
                    ifnull(qqe.name,"Sin asignar") as name_evaluation,
                    if(em.category_id = 0,"Todos",ifnull(c.category,"Sin asignar")) as category_name,
                    if(em.subcategory_id = 0,"Todos",ifnull(s.subcategory,"Sin asignar")) as subcategory_name
                ');
            $this->db->from($this->tableName . ' em');
            $this->db->join($this->tableQuizQuestions . ' qqs', 'qqs.id = em.quiz_satisfaction_id', 'left');
            $this->db->join($this->tableQuizQuestions . ' qqe', 'qqe.id = em.quiz_final_evaluation_id', 'left');
            $this->db->join($this->tableCategories . ' c', 'c.id = em.category_id', 'left');
            $this->db->join($this->tableSubcategories . ' s', 's.id = em.subcategory_id', 'left');
            //$this->db->join($this->tableElearningGroups.' eg', ' em.id = eg.elearning_id and eg.group_id in ('.$group.')');
            if ($datos['id'] !== '') {
                $filtro['em.id'] = $datos['id'];
            }
            $this->db->where($filtro);
            
            $this->db->where(array('em.business_id' => $datos['business_id']));
            if ($datos['id'] === '') {
                $this->db->or_where('em.business_id =', 0);
            }
            //print_r($this->db->get_compiled_select());exit;
            $result = $this->db->get()->result_array();
            foreach ($result as $index => $value) {
                /***********************************************************************
                 *	Autor: Mario Adrián Martínez Fernández   Fecha: 15/09/2020
                 *		   mario.martinez.f@hotmail.es
                 *	Nota: Se valida que intento tendra el usuario actual.
                 ***********************************************************************/
                $this->db->select('ifnull(tried,0) as tried');
                $this->db->from($this->tableScoreLog);
                $this->db->where('module_id = ', $value['id']);
                $this->db->where('user_id = ', $datos['user_id']);
                $this->db->order_by('tried', 'DESC');
                $this->db->limit(1);
                $tried = $this->db->get()->result_array();
                if (count($tried) > 0) {
                    $result[$index]['tried'] = intval($tried[0]['tried']) + 1;
                    $result[$index]['my_tries'] = intval($tried[0]['tried']) + 1;
                } else {
                    $result[$index]['tried'] = 1;
                    $result[$index]['my_tries'] = 1;
                }
                if ($result[$index]['tried'] > $value['max_try']) {
                    $result[$index]['open_elearning'] = 0;
                } else {
                    $result[$index]['open_elearning'] = 1;
                }
                $result[$index]['preview'] = base_url('uploads/business_' . $datos['business_id'] . '/elearnings/') . $value['preview'];


                //obtener los usuarios del elearnings
                $this->db->select("u.id, u.name, u.last_name");
                $this->db->from("user as u");
                $this->db->join("elearning_users as eu", "eu.user_id = u.id");
                $this->db->where("eu.elearning_id", $value["id"]);
                $result[$index]["usuarios"] = $this->db->get()->result_array();
            }
            return $result;
        } else {
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/3/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Validamos que el usuarios tenga un grupo asignado si no tiene
             *          un grupo asignado no podra ver nada en elearning
             ***********************************************************************/
            $this->db->select('ug.group_id');
            $this->db->from($this->tableUsersGroups . ' as ug');
            $this->db->join($this->table_groups . ' as g', 'ug.group_id = g.id');
            $this->db->where('ug.user_id', $datos['user_id']);
            $this->db->where('g.active', 1);
            $this->db->where('ug.active', 1);
            $group = $this->db->get()->result_array();
            // echo json_encode($group);
            // if (count($group) > 0) {
            $group = implode(',', array_map(function ($string) {

                return $string['group_id'];
            }, $group));
            $filtro = array();
            $filtro['em.active'] = 1;
            if (isset($datos['category_id'])) {
                $filtro['em.category_id'] = $datos['category_id'];
            }
            if (isset($datos['subcategory_id'])) {
                $filtro['em.subcategory_id'] = $datos['subcategory_id'];
            }
            //$filtro['em.business_id'] = $datos['business_id'];
            $this->db->select('
                    em.*, coalesce(date_format(em.fecha_limite,"%d %M %Y"),"") as fecha_limite,
                    ifnull(qqs.name,"Sin asignar") as name_satisfaction, 
                    ifnull(qqe.name,"Sin asignar") as name_evaluation,
                    if(em.category_id = 0,"Todos",ifnull(c.category,"Sin asignar")) as category_name,
                    if(em.subcategory_id = 0,"Todos",ifnull(s.subcategory,"Sin asignar")) as subcategory_name
                ');
            $this->db->from($this->tableName . ' em');
            $this->db->join($this->tableQuizQuestions . ' qqs', 'qqs.id = em.quiz_satisfaction_id', 'left');
            $this->db->join($this->tableQuizQuestions . ' qqe', 'qqe.id = em.quiz_final_evaluation_id', 'left');
            $this->db->join($this->tableCategories . ' c', 'c.id = em.category_id', 'left');
            $this->db->join($this->tableSubcategories . ' s', 's.id = em.subcategory_id', 'left');
            // $this->db->join("elearning_users as eu", "eu.elearning_id = em.id");
            $this->db->join($this->tableElearningGroups . ' eg', ' em.id = eg.elearning_id and eg.group_id in (' . $group . ')');
            if ($datos['id'] !== '') {
                $filtro['em.id'] = $datos['id'];
            }
            $this->db->where($filtro);
            $this->db->where("em.capacitacion_obligatoria",0);
            $this->db->where('(now() <= em.fecha_limite or em.fecha_limite is null or em.fecha_limite = "0000-00-00 00:00:00" )', null, false);
            // $this->db->where("user_id", $datos['user_id']);
            if ($datos['id'] === '') {
                $this->db->where(array('em.business_id' => $datos['business_id']));
                $this->db->or_where('em.business_id =', 0);
            }
            //print_r($this->db->get_compiled_select());exit;
            $result = $this->db->get()->result_array();
            // echo json_encode($this->db->last_query());
            foreach ($result as $index => $value) {
                /***********************************************************************
                 *	Autor: Mario Adrián Martínez Fernández   Fecha: 15/09/2020
                 *		   mario.martinez.f@hotmail.es
                 *	Nota: Se valida que intento tendra el usuario actual.
                 ***********************************************************************/
                $this->db->select('ifnull(tried,0) as tried');
                $this->db->from($this->tableScoreLog);
                $this->db->where('module_id = ', $value['id']);
                $this->db->where('user_id = ', $datos['user_id']);
                $this->db->order_by('tried', 'DESC');
                $this->db->limit(1);
                $tried = $this->db->get()->result_array();
                if (count($tried) > 0) {
                    $result[$index]['tried'] = intval($tried[0]['tried']) + 1;
                    $result[$index]['my_tries'] = intval($tried[0]['tried']) + 1;
                } else {
                    $result[$index]['tried'] = 1;
                    $result[$index]['my_tries'] = 1;
                }
                if ($result[$index]['tried'] > $value['max_try']) {
                    $result[$index]['open_elearning'] = 0;
                } else {
                    $result[$index]['open_elearning'] = 1;
                }
                $result[$index]['preview'] = base_url('uploads/business_' . $datos['business_id'] . '/elearnings/') . $value['preview'];

                // $this->db->select("*");
                // $this->db->from("elearning_usage");
                // $this->db->where("elearning_id", $result[$index]['id']);
                // $this->db->where("user_id", $datos["user_id"]);
                // $visto = $this->db->get()->result_array();
                // $result[$index]["visto"] = count($visto) > 0 ? 1 : 0;


                $query_capacitaciones = "SELECT 
                l.id,
                l.name
                FROM capacit_detail AS d
                    INNER JOIN capacit_list AS l
                        ON (l.id = d.id_capacitacion)
                    INNER JOIN capacit_categorias as cc on cc.id = d.catalog
                WHERE cc.`catalog` = 'elearning_modules' AND d.id_elemento = " . $result[$index]['id'] . ";";

                $resultado = $this->db->query($query_capacitaciones)->result_array();
                if (count($resultado) > 0) {
                    $result[$index]['capacitaciones'] = $resultado;
                } else {
                    $result[$index]['capacitaciones'] = [];
                }
            }
            return $result;
            // } else {
            //     return array();
            // }
        }
    }

    public function fetchAllById($id)
    {
        return $this->db->get_where($this->tableName, array("id" => $id))->result_array();
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 26/08/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para listar categorias
     ***********************************************************************/
    function ListCategories($business_id)
    {
        $this->db->select('id, category');
        $this->db->from($this->tableCategories);
        $this->db->where('business_id = ', $business_id);
        $this->db->where('active = ', 1);
        $this->db->order_by('order', 'ASC');

        $categories = $this->db->get()->result_array();
        if (count($categories) > 0) {
            return $categories;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener las subcategorias en base a la categoria
     *          y empresa enviada.
     ***********************************************************************/
    function ListSubcategories($business_id, $category_id = '')
    {
        $this->db->select('s.id, s.subcategory, s.category_id, a.category');
        $this->db->from($this->tableSubcategories . ' s');
        $this->db->join($this->tableCategories . ' a', 's.category_id = a.id');
        $this->db->where('a.business_id = ', $business_id);
        if ($category_id !== '') {
            $this->db->where('s.category_id = ', $category_id);
        }
        $this->db->where('s.active = ', 1);
        $this->db->order_by('s.order', 'ASC');
        $categories = $this->db->get()->result_array();
        if (count($categories) > 0) {
            return $categories;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los cuestionarios que se le pueden asignar
     *          a un elearning
     ***********************************************************************/
    function QuizLibrary($data)
    {
        $this->db->select('id, name, connection_id');
        $this->db->from($this->tableQuizQuestions);
        $this->db->where('business_id =', $data['business_id']);
        $this->db->where('active =', 1);
        $this->db->where('category_id =', QUIZ_CATEGORY_ELEARNING);
        // $this->db->where('connection_id =', 0);
        if (isset($data['elearning_id'])) {
            $this->db->or_where('connection_id =', $data['elearning_id']);
        }
        return $this->db->get()->result_array();
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para registrar y actualizar una categoria.
     ***********************************************************************/
    function SaveCategory($data)
    {
        if (isset($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            return $this->db->update($this->tableCategories, $data, array('id' => $id));
        } else {
            return $this->db->insert($this->tableCategories, $data);
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 30/09/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para registrar y actualizar una categoria.
     ***********************************************************************/
    function SaveSubcategory($data)
    {
        if (isset($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            return $this->db->update($this->tableSubcategories, $data, array('id' => $id));
        } else {
            return $this->db->insert($this->tableSubcategories, $data);
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener el detalle de los elearning
     ***********************************************************************/
    function ElearningDetailUsers($data)
    {
        $report = "
                select
                       e.id,
                       e.name,
                       e.last_name,
                       e.email,
                       ifnull(e.max_score,'') as max_score,
                       ifnull(e.date_entry,'') as date_entry,
                       ifnull(e.date_exit,'') as date_exit,
                       if(e.date_exit is null,time('00:00:00'),TIMEDIFF(e.date_exit,e.date_entry)) as diff,
                       ifnull(max(e.tried),0) as tried_final,
                       ifnull(max(e.max_try),0) as max_try,
                       if(e.max_score > e.min_score,'Aprobado',if(date_entry is null,'Pendiente',if(e.max_try = max(e.tried),'Reprobado',''))) as status
                from (
                    select
                       u.*,
                       (
                           select
                               ifnull(max(esl.score),'Sin registro') as max_score
                           from elearning_score_log esl
                           where esl.user_id = u.id and esl.module_id = " . $data['elearning_id'] . "
                           group by esl.user_id
                       ) as max_score,
                       (
                           select
                               ifnull(min(eal.fecha),'Sin registro') as date_entry
                           from elearning_access_log eal
                           where eal.user_id = u.id and eal.type = 0 and eal.modules_id = " . $data['elearning_id'] . "
                           group by eal.user_id
                       ) as date_entry,
                       (
                           select
                               ifnull(max(eal.fecha),'Sin registro') as date_exit
                           from elearning_access_log eal
                           where eal.user_id = u.id and eal.type = 1 and eal.modules_id = " . $data['elearning_id'] . "
                           group by eal.user_id
                       ) as date_exit,
                       esl2.tried,
                       em.min_score,
                       em.max_try
                   from user u
                    left join elearning_access_log eal2 on u.id = eal2.user_id and eal2.modules_id = " . $data['elearning_id'] . "
                    left join elearning_score_log esl2 on u.id = esl2.user_id and eal2.modules_id = " . $data['elearning_id'] . "
                    left join elearning_modules em on eal2.modules_id = em.id
                   where u.business_id = " . $data['business_id'] . "
                    ) as e group by e.id;
            ";
        $report = $this->db->query($report)->result_array();
        if (count($report) > 0) {
            foreach ($report as $index => $value) {
                if ($value['date_entry'] !== '') {
                    $datetime1 = new DateTime($value['date_entry']);
                    $datetime2 = new DateTime($value['date_exit']);
                    $interval = $datetime1->diff($datetime2);
                    $elapsed = $interval->format('%m meses %a dias %h horas %i min %s seg');
                    $report[$index]['diff'] = $elapsed;
                } else {
                    $report[$index]['diff'] = '';
                }
                if ($value['date_entry'] !== '') {
                    $date_entry = new DateTime($value['date_entry']);
                    $report[$index]['date_entry'] = $date_entry->format('d-m-Y H:i:s');
                }
                if ($value['date_exit'] !== '') {
                    $date_exit = new DateTime($value['date_exit']);
                    $report[$index]['date_exit'] = $date_exit->format('d-m-Y H:i:s');
                }
            }
            return $report;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener la fecha de castellano
     ***********************************************************************/
    function fechaCastellano($fecha, $tiempo = false)
    {
        //$fecha = substr($fecha, 0, 10);
        $numeroDia = date('d', strtotime($fecha));
        $dia = date('l', strtotime($fecha));
        $mes = date('F', strtotime($fecha));
        $anio = date('Y', strtotime($fecha));
        $horas = date('H', strtotime($fecha));
        $minutos = date('i', strtotime($fecha));
        $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
        $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        $nombredia = str_replace($dias_EN, $dias_ES, $dia);
        $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
        $texto = $nombredia . " " . $numeroDia . " de " . $nombreMes . " de " . $anio;
        if ($tiempo) {
            $texto .= " - " . $horas . ":" . $minutos;
        }
        return $texto;
    }

    function agregarUsuario($id_elearning, $id_usuario)
    {
        // $usuarios = json_decode($usuarios, true);;
        // echo json_encode($usuarios);
        // for ($i = 0; $i < count($usuarios); $i++) {
        $data = [];
        $data["user_id"] = $id_usuario;
        $data["elearning_id"] = $id_elearning;
        return $this->db->insert("elearning_users", $data);
        // }
    }

    function agregarUsuarios($id_elearning, $usuarios)
    {
        $usuarios = json_decode($usuarios, true);;
        // echo json_encode($usuarios);
        for ($i = 0; $i < count($usuarios); $i++) {
            $data = [];
            $data["user_id"] = $usuarios[$i]["id"];
            $data["elearning_id"] = $id_elearning;
            return $this->db->insert("elearning_users", $data);
        }
    }

    function eliminarUsuario($id_elearning, $id_usuario)
    {
        $this->db->where("user_id", $id_usuario);
        $this->db->where("elearning_id", $id_elearning);
        return $this->db->delete("elearning_users");
    }

    public function SetVisto($data)
    {
        $dataa = array(
            "veces_visto" => 1,
            "elearning_id" => $data["elearning_id"],
            "user_id" => $data["user_id"],
            "numero_clicks" => $data["numero_clicks"]
        );
        $this->general_mdl->ModificarScoreUsuario($data["user_id"], 1);
        return $this->db->insert("elearning_usage", $dataa);
    }

    public function obtener_elearnings_capacitacion($business_id){
        $query ="select * from elearning_modules where business_id = $business_id and capacitacion_obligatoria = 1";
        return $this->db->query($query)->result_array();
    }
}
