<?php
class Posts_mdl extends CI_Model
{
    private $tableWall = "wall",
        $tableWallComments = "wall_comments",
        $tableWallLike = "wall_post_like";

    function __construct()
    {
        parent::__construct();
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para guardar un post nuevo
     ***********************************************************************/
    function SaveWall($data){
        unset($data['token']);
        if($this->db->insert($this->tableWall, $data)){
            return true;
        }else{
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los post de la empresa de acuerdo al
     *          usuario de la sesion
     ***********************************************************************/
    function WallsList($business_id,$user_id){
        $this->db->select("w.id, w.user_id, w.image_path, w.wall_description, concat(u.name,' ',u.last_name) as name_complete, u.profile_photo");
        $this->db->from($this->tableWall.' as w');
        $this->db->join('user as u', 'w.user_id = u.id');
        $this->db->where('w.business_id =', $business_id);
        $this->db->where('w.active =', 1);
        $this->db->order_by('w.id', 'desc');
        $walls = $this->db->get()->result_array();
        if($walls > 0){
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Recorremos el arreglo para obtener la ruta real de la imagen
             *          del usuario
             ***********************************************************************/
            foreach ($walls as $index => $value){
                if($value['image_path'] !== ''){
                    $walls[$index]['image_path'] = base_url('uploads/business_'.$business_id.'/walls/').$value['image_path'];
                }
                /***********************************************************************
                 *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
                 *		   mario.martinez.f@hotmail.es
                 *	Nota: Obtendremos el total de like
                 ***********************************************************************/
                $this->db->select('id');
                $this->db->from($this->tableWallLike);
                $this->db->where('post_id =', $value['id']);
                $likes = $this->db->get()->result_array();
                $walls[$index]['likes'] = count($likes);
                /***********************************************************************
                 *	Autor: Mario Adrián Martínez Fernández   Fecha: 05/06/2020
                 *		   mario.martinez.f@hotmail.es
                 *	Nota: validar si el usuario ha dado like al post
                 ***********************************************************************/
                $valida = $this->db->get_where($this->tableWallLike,array('user_id'=> $user_id,'post_id'=>$value['id']))->result_array();
                if(count($valida) === 0){
                    $walls[$index]['validate_like'] = 1;
                }else{
                    $walls[$index]['validate_like'] = 0;
                }
                /***********************************************************************
                 *	Autor: Mario Adrián Martínez Fernández   Fecha: 05/06/2020
                 *		   mario.martinez.f@hotmail.es
                 *	Nota: Funcion para obtener el total de comentarios por post
                 ***********************************************************************/
                $this->db->select('count(id) as total_comments');
                $this->db->from($this->tableWallComments);
                $this->db->where('post_id =', $value['id']);
                $comments = $this->db->get()->result_array();
                $walls[$index]['total_comments'] = $comments[0]['total_comments'];
            }
            return $walls;
        }else{
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para guardar un comentario a un post
     ***********************************************************************/
    function SaveWallComment($data){
        unset($data['token']);
        if($this->db->insert($this->tableWallComments, $data)){
            return true;
        }else{
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los comentarios de los posts.
     ***********************************************************************/
    function ListCommentsPost($post_id){
        $this->db->select("wc.id, wc.user_id, wc.comment, concat(u.name,' ',u.last_name) as name_complete");
        $this->db->from($this->tableWallComments.' as wc');
        $this->db->join('user as u', 'wc.user_id = u.id');
        $this->db->where('wc.post_id =', $post_id);
        $comments = $this->db->get()->result_array();
        if(count($comments) > 0){
            return $comments;
        }else{
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para registrar un like en un post
     ***********************************************************************/
    function SaveLikePost($data){
        unset($data['token']);
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Validamos que no tengar un like registrado
         ***********************************************************************/
        $valida = $this->db->get_where($this->tableWallLike,array('user_id'=>$data['user_id'],'post_id'=>$data['post_id']))->result_array();
        if(count($valida) === 0){
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Si no encontramos registro de a ver tenido un like previo para
             *          el id del post enviando entonces se insertara un registro
             *          nuevo.
             ***********************************************************************/
            if($this->db->insert($this->tableWallLike, $data)){
                return true;
            }else{
                return false;
            }
        }else{
            if($this->db->delete($this->tableWallLike, $data)){
                return true;
            }else{
                return false;
            }
        }
    }
}
