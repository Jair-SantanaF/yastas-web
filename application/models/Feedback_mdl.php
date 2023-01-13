<?php
class Feedback_mdl extends CI_Model
{
    private $tableFeedback = 'feedback',
        $tableFeedbackCategory = 'feedback_category',
        $tableUsers = 'user',
        $tableFeedbackLike = 'feedback_like';
    function __construct()
    {
        parent::__construct();
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para guardar una categoría de feedback
     ***********************************************************************/
    function SaveCategory($data)
    {
        $dataa = array(
            "description" => $data["description"],
            "active" => 1,
            "business_id" => $data["business_id"]
        );

        if ($this->db->insert($this->tableFeedbackCategory, $dataa)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para editar una categoría de feedback
     ***********************************************************************/
    function EditCategory($data)
    {
        $key = array('id' => $data["id"]);

        $dataa = array(
            "description" => $data["description"]
        );

        if ($this->db->update($this->tableFeedbackCategory, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar una categoría de feedback
     ***********************************************************************/
    function DeleteCategory($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableFeedbackCategory, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para validar feedback asociados a la categoria
     ***********************************************************************/
    function ValidCategory($data)
    {
        $this->db->from($this->tableFeedback);
        $this->db->select('*');
        $this->db->where('category_id =', $data["id"]);
        $list = $this->db->get()->result_array();

        if (count($list) > 0) {
            return ($list);
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 02/06/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener las categorias para feedback
     ***********************************************************************/
    function CategoryFeedback($business_id)
    {
        $this->db->select('id, description');
        $this->db->from($this->tableFeedbackCategory);
        $this->db->where('business_id = ', $business_id);
        $this->db->where('active = ', 1);

        $categories = $this->db->get()->result_array();
        if (count($categories) > 0) {
            return $categories;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 02/06/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para crear un feedback a un usuario en especifico
     ***********************************************************************/
    function CreateFeedback($data)
    {
        unset($data['token']);
        if ($this->db->insert($this->tableFeedback, $data)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar un elemento del feedback
     ***********************************************************************/
    function DeleteFeedback($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableFeedback, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 02/06/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: funcion para obtener los feedback de un usuario en especifico
     ***********************************************************************/
    function FeedbackList($data)
    {
        $this->db->select('
            f.id,
            f.owner_id,
            f.user_id,
            uu.profile_photo as photo_user,
            uo.profile_photo as photo_owner,
            f.description,
            f.category_id,
            f.media_path,
            f.file_path,
            f.is_visible,
            fc.description as name_category,
            concat(uu.name,\' \',uu.last_name) as name_user,
            concat(uo.name,\' \',uo.last_name) as name_owner,
            IFNULL(j.job_name, "") AS job_name,
            if(fl.id is null,0,1) as i_like_it,
            f.fecha
            
        ');
        $this->db->from($this->tableFeedback . ' as f');
        $this->db->join($this->tableFeedbackCategory . ' as fc', 'f.category_id = fc.id');
        $this->db->join("feedback_like as fl", "fl.user_id = " . $data["user_id"] . " and fl.feedback_id = f.id", "left");
        $this->db->join('user as uu', 'uu.id = f.user_id');
        $this->db->join('user as uo', 'uo.id = f.owner_id');
        $this->db->join('jobs as j', 'uu.job_id = j.id', 'left');
        $this->db->where('f.active =', 1);
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 04/06/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Se valida el tipo de listado que se requiere, recibidos, dados
         *          y todos.
         ***********************************************************************/
        switch ($data['type']) {
            case '1': //Feedback recibidos
                $this->db->where('f.user_id =', $data['user_id']);
                $this->db->where('uu.business_id =', $data['business_id']);
                break;
            case '2': //Feedback dados
                $this->db->where('f.owner_id =', $data['user_id']);
                $this->db->where('uo.business_id =', $data['business_id']);
                break;
            case '3': //Feedback todos
            default:
                $this->db->where('f.is_visible =', 1);
                $this->db->where('uo.business_id =', $data['business_id']);
                //$this->db->or_where('uu.business_id =', $data['business_id']);
                break;
        }
        //print_r($this->db->get_compiled_select());exit;
        $this->db->order_by('id', 'desc');
        $list = $this->db->get()->result_array();
        $url = base_url('uploads/business_' . $data['business_id'] . '/feedback/');
        if (count($list) > 0) {
            foreach ($list as $index => $value) {
                $list[$index]['media_path'] = ($value['media_path'] === '') ? '' : $url . $value['media_path'];
                $list[$index]['file_path'] = ($value['file_path'] === '') ? '' : $url . $value['file_path'];
                /***********************************************************************
                 *	Autor: Mario Adrián Martínez Fernández   Fecha: 04/06/2020
                 *		   mario.martinez.f@hotmail.es
                 *	Nota: Creamos bandera para decirle si el es propietario del feedback
                 ***********************************************************************/
                if ($value['user_id'] == $data['user_id']) {
                    $list[$index]['actions'] = 1;
                } else {
                    $list[$index]['actions'] = 0;
                }
                /***********************************************************************
                 *	Autor: Mario Adrián Martínez Fernández   Fecha: 02/06/2020
                 *		   mario.martinez.f@hotmail.es
                 *	Nota: Obtenemos el total de likes que tiene el usuario
                 ***********************************************************************/
                $this->db->select('count(id) as count');
                $this->db->from($this->tableFeedbackLike);
                $this->db->where('feedback_id =', $value['id']);
                $count = $this->db->get()->result_array();
                $list[$index]['total_like'] = $count[0]['count'];
                $query = "select group_id from users_groups where user_id = " . $value["owner_id"];
                $list[$index]["grupos_owner"] = $this->db->query($query)->result_array();
                $query = "select group_id from users_groups where user_id = " . $value["user_id"];
                $list[$index]["grupos_user"] = $this->db->query($query)->result_array();
            }
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 05/06/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Se optienen los conteos totales
             ***********************************************************************/
            $this->db->select('count(f.id) as total_feedback');
            $this->db->from($this->tableFeedback . ' as f');
            switch ($data['type']) {
                case '1': //Feedback recibidos
                    $this->db->join('user as uu', 'uu.id = f.user_id');
                    $this->db->where('f.active =', 1);
                    $this->db->where('f.user_id =', $data['user_id']);
                    $this->db->where('uu.business_id =', $data['business_id']);
                    break;
                case '2': //Feedback dados
                    $this->db->join('user as uo', 'uo.id = f.owner_id');
                    $this->db->where('f.active =', 1);
                    $this->db->where('f.owner_id =', $data['user_id']);
                    $this->db->where('uo.business_id =', $data['business_id']);
                    break;
                case '3': //Feedback todos
                default:
                    $this->db->join('user as uu', 'uu.id = f.user_id');
                    $this->db->join('user as uo', 'uo.id = f.owner_id');
                    $this->db->where('f.active =', 1);
                    $this->db->where('f.is_visible =', 1);
                    $this->db->where('uo.business_id =', $data['business_id']);
                    //$this->db->or_where('uu.business_id =', $data['business_id']);
                    break;
            }
            //print_r($this->db->get_compiled_select());exit;
            $total_feedback = $this->db->get()->result_array();
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 05/06/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Total likes feedbacks.
             ***********************************************************************/
            $this->db->select('count(f.id) as total_like');
            $this->db->from($this->tableFeedback . ' as f');
            $this->db->join('feedback_like as fl', 'fl.feedback_id = f.id');
            switch ($data['type']) {
                case '1': //Feedback recibidos
                    $this->db->join('user as uu', 'uu.id = f.user_id');
                    $this->db->where('f.active =', 1);
                    $this->db->where('f.user_id =', $data['user_id']);
                    $this->db->where('uu.business_id =', $data['business_id']);
                    break;
                case '2': //Feedback dados
                    $this->db->join('user as uo', 'uo.id = f.owner_id');
                    $this->db->where('f.active =', 1);
                    $this->db->where('f.owner_id =', $data['user_id']);
                    $this->db->where('uo.business_id =', $data['business_id']);
                    break;
                case '3': //Feedback todos
                default:
                    $this->db->join('user as uu', 'uu.id = f.user_id');
                    $this->db->join('user as uo', 'uo.id = f.owner_id');
                    $this->db->where('f.active =', 1);
                    $this->db->where('f.is_visible =', 1);
                    $this->db->where('uo.business_id =', $data['business_id']);
                    //$this->db->or_where('uu.business_id =', $data['business_id']);
                    break;
            }
            $total_likes = $this->db->get()->result_array();

            return array('list_feedback' => $list, 'counts' => array('total_feedback' => $total_feedback[0]['total_feedback'], 'total_like' => $total_likes[0]['total_like']));
        } else {
            return  false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 02/06/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para registrar un like de un feedback en especifico
     ***********************************************************************/
    function SaveLike($data)
    {
        unset($data['token']);
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 02/06/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Validamos que no existe un like previo
         ***********************************************************************/
        $this->db->select('id');
        $this->db->from($this->tableFeedbackLike);
        $this->db->where(array('feedback_id =' => $data['feedback_id'], 'user_id =' => $data['user_id']));
        $validate = $this->db->get()->result_array();
        if (count($validate) === 0) {
            if ($this->db->insert($this->tableFeedbackLike, $data)) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 04/06/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para mostrar o ocultar un feedback
     ***********************************************************************/
    function ShowHideFeedback($data)
    {
        $this->db->select('is_visible');
        $validate = $this->db->get_where($this->tableFeedback, array('user_id =' => $data['user_id'], 'id =' => $data['feedback_id']))->result_array();
        if (count($validate) > 0) {
            if ($validate[0]['is_visible'] == 1) {
                return $this->db->update($this->tableFeedback, array('is_visible' => 0), array('id' => $data['feedback_id']));
            } else {
                return $this->db->update($this->tableFeedback, array('is_visible' => 1), array('id' => $data['feedback_id']));
            }
        } else {
            return 'not_owner';
        }
    }
}
