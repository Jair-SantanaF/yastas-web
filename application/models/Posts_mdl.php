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
    function SaveWall($data)
    {
        unset($data['token']);
        if ($this->db->insert($this->tableWall, $data)) {
            return true;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los post de la empresa de acuerdo al
     *          usuario de la sesion
     ***********************************************************************/
    function WallsList($business_id, $user_id)
    {

        $id_region = $this->user_model->obtener_region($user_id);
        $id_asesor = $this->user_model->obtener_asesor($user_id);
        $id_rol = $this->user_model->obtener_rol($user_id);

        $this->db->select("w.id, w.user_id, w.image_path,w.image_preview,w.tipo, w.wall_description,w.redirigir, concat(u.name,' ',u.last_name) as name_complete, u.profile_photo");
        $this->db->from($this->tableWall . ' as w');
        $this->db->join('user as u', 'w.user_id = u.id');
        $this->db->where("date_format(w.created_at,'%Y-%m-%d') < '2025-03-10'", null, false);
        $this->db->where('w.business_id =', $business_id);
        $this->db->where('w.active =', 1);
        $this->db->where('w.redirigir !=', 0);

        if ($id_rol == 6) {
            // $id_asesor = $this->session->userdata("id_user");
            // if ($id_asesor == null) {
            //     $id_asesor = $user_id;
            // }
            // $this->db->where("u.id_asesor", $id_asesor);
        }
        if ($id_rol == 5) {
            // $region = $this->session->userdata("id_region");
            // if ($region == null) {
            //     $query = "select id_region from user where id = " . $user_id;
            //     $result = $this->db->query($query)->result_array()[0]["id_region"];
            //     $region = $result;
            // }
            // $this->db->where("u.id_region", $id_region);
        }

        if ($id_rol == 3) {
            // $id_asesor = ""; //$this->session->userdata("id_asesor");
            // if ($region == null) {
            //     $query = "select id_asesor from user where id = " . $user_id;
            //     $result = $this->db->query($query)->result_array()[0]["id_asesor"];
            //     $id_asesor = $result;
            // }
            // $this->db->where("u.id_asesor", $id_asesor);
        }
        $this->db->order_by('w.id', 'desc');
        $walls = $this->db->get()->result_array();

        $query = "
            select w.id, w.user_id, w.image_path,w.image_preview,w.tipo, w.wall_description,w.redirigir, concat(u.name,' ',u.last_name) as name_complete, u.profile_photo
            from wall as w
            join user as u on u.id = w.user_id
            left join wall_users as wu on wu.wall_id = w.id 
            left join wall_groups as wg on wg.wall_id = w.id
            left join users_groups as ug on ug.group_id = wg.group_id
            where (ug.user_id = $user_id or wu.user_id = $user_id) and date_format(w.created_at,'%Y-%m-%d') >= '2025-03-10'
            and w.business_id = $business_id and (wu.id is not null || wg.id is not null)
            and w.active = 1
            and w.redirigir != 0
            order by w.id desc
            ";
        $wall1 = $this->db->query($query)->result_array();

        $wall_total = array_merge($wall1, $walls);
        
        if ($wall_total > 0) {
            $walls = $wall_total;
            
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Recorremos el arreglo para obtener la ruta real de la imagen
             *          del usuario
             ***********************************************************************/
            foreach ($walls as $index => $value) {
                if ($value['image_path'] !== '') {
                    $walls[$index]['image_path'] = base_url('uploads/business_' . $business_id . '/walls/') . $value['image_path'];
                } else {
                    // $walls[$index]['image_path'] = base_url('assets/img/img_h_newsfeed.png');
                }

                if ($value["tipo"] == "video")
                    if ($value['image_preview'] !== '')
                        $walls[$index]['image_preview'] = base_url('uploads/business_' . $business_id . '/walls/') . $value['image_preview'];
                    else {
                        $walls[$index]['image_preview'] = base_url('uploads/business_' . $business_id . '/walls/') . "default.png";
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
                $valida = $this->db->get_where($this->tableWallLike, array('user_id' => $user_id, 'post_id' => $value['id']))->result_array();
                if (count($valida) === 0) {
                    $walls[$index]['validate_like'] = 1;
                } else {
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

                $this->db->select('count(id) as total_reshare');
                $this->db->from("wall_post_reshare");
                $this->db->where('post_id =', $value['id']);
                $comments = $this->db->get()->result_array();
                $walls[$index]['total_reshare'] = $comments[0]['total_reshare'];

                $comments_list = $this->ListCommentsPost($value['id'], 2);
                $walls[$index]['comments'] = $comments_list;
            }
            return $walls;
        } else {
            return false;
        }
    }

    function obtener_noticia($business_id, $user_id)
    {

        $this->db->select("w.id, w.user_id, w.image_path,w.image_preview,w.tipo, w.wall_description,w.redirigir, concat(u.name,' ',u.last_name) as name_complete, u.profile_photo");
        $this->db->from($this->tableWall . ' as w');
        $this->db->join('user as u', 'w.user_id = u.id');
        $this->db->where('w.business_id =', $business_id);
        $this->db->where('w.active =', 1);
        $this->db->where("w.es_noticia", 1);

        $this->db->order_by('w.id', 'desc');
        $this->db->limit(1);
        $walls = $this->db->get()->result_array();
        if ($walls > 0) {

            foreach ($walls as $index => $value) {
                if ($value['image_path'] !== '') {
                    $walls[$index]['image_path'] = base_url('uploads/business_' . $business_id . '/walls/') . $value['image_path'];
                } else {
                    // $walls[$index]['image_path'] = base_url('assets/img/img_h_newsfeed.png');
                }

                // if ($value["tipo"] == "video")
                    if ($value['image_preview'] !== '')
                        $walls[$index]['image_preview'] = base_url('uploads/business_' . $business_id . '/walls/') . $value['image_preview'];
                    else {
                        $walls[$index]['image_preview'] = base_url('uploads/business_' . $business_id . '/walls/') . "default.png";
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
                $valida = $this->db->get_where($this->tableWallLike, array('user_id' => $user_id, 'post_id' => $value['id']))->result_array();
                if (count($valida) === 0) {
                    $walls[$index]['validate_like'] = 1;
                } else {
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

                $this->db->select('count(id) as total_reshare');
                $this->db->from("wall_post_reshare");
                $this->db->where('post_id =', $value['id']);
                $comments = $this->db->get()->result_array();
                $walls[$index]['total_reshare'] = $comments[0]['total_reshare'];

                $comments_list = $this->ListCommentsPost($value['id'], 2);
                $walls[$index]['comments'] = $comments_list;
            }
            return $walls;
        } else {
            return [];
        }
    }

    function obtener_rutas($walls, $index, $value, $business_id)
    {
        if ($value['image_path'] !== '') {
            $walls[$index]['image_path'] = base_url('uploads/business_' . $business_id . '/walls/') . $value['image_path'];
        } else {
            // $walls[$index]['image_path'] = base_url('assets/img/img_h_newsfeed.png');
        }

        if ($value["tipo"] == "video")
            if ($value['image_preview'] !== '')
                $walls[$index]['image_preview'] = base_url('uploads/business_' . $business_id . '/walls/') . $value['image_preview'];
            else {
                $walls[$index]['image_preview'] = base_url('uploads/business_' . $business_id . '/walls/') . "default.png";
            }
        return $walls;
    }

    function obtener_likes($walls, $value, $index)
    {
        $this->db->select('id');
        $this->db->from($this->tableWallLike);
        $this->db->where('post_id =', $value['id']);
        $likes = $this->db->get()->result_array();
        $walls[$index]['likes'] = count($likes);
        return $walls;
    }

    function valida_likes($walls, $user_id, $value, $index)
    {
        $valida = $this->db->get_where($this->tableWallLike, array('user_id' => $user_id, 'post_id' => $value['id']))->result_array();
        if (count($valida) === 0) {
            $walls[$index]['validate_like'] = 1;
        } else {
            $walls[$index]['validate_like'] = 0;
        }
        return $walls;
    }

    function obtener_comentarios($walls, $value, $index)
    {
        $this->db->select('count(id) as total_comments');
        $this->db->from($this->tableWallComments);
        $this->db->where('post_id =', $value['id']);
        $comments = $this->db->get()->result_array();
        $walls[$index]['total_comments'] = $comments[0]['total_comments'];

        $comments_list = $this->ListCommentsPost($value['id'], 2);
        $walls[$index]['comments'] = $comments_list;
        return $walls;
    }

    function obtener_reshare($walls, $value, $index)
    {
        $this->db->select('count(id) as total_reshare');
        $this->db->from("wall_post_reshare");
        $this->db->where('post_id =', $value['id']);
        $comments = $this->db->get()->result_array();
        $walls[$index]['total_reshare'] = $comments[0]['total_reshare'];
        return $walls;
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para guardar un comentario a un post
     ***********************************************************************/
    function SaveWallComment($data)
    {
        unset($data['token']);
        if ($this->db->insert($this->tableWallComments, $data)) {
            $this->general_mdl->ModificarScoreUsuario($data["user_id"], 1);
            return true;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los comentarios de los posts.
     ***********************************************************************/
    function ListCommentsPost($post_id, $limit = 0, $user_id = null)
    {
        if ($user_id != null)
            $id_asesor = $this->user_model->obtener_asesor($user_id);
        $this->db->select("wc.id, wc.user_id, wc.comment, concat(u.name,' ',u.last_name) as name_complete, u.profile_photo");
        $this->db->from($this->tableWallComments . ' as wc');
        $this->db->join('user as u', 'wc.user_id = u.id');
        $this->db->where('wc.post_id =', $post_id);
        if ($user_id != null)
            $this->db->where("u.id_asesor", $id_asesor);

        if ($limit > 0) {
            $this->db->limit($limit, 0);
        }

        $comments = $this->db->get()->result_array();
        if (count($comments) > 0) {
            return $comments;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para registrar un like en un post
     ***********************************************************************/
    function SaveLikePost($data)
    {
        unset($data['token']);
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Validamos que no tengar un like registrado
         ***********************************************************************/
        $valida = $this->db->get_where($this->tableWallLike, array('user_id' => $data['user_id'], 'post_id' => $data['post_id']))->result_array();
        if (count($valida) === 0) {
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Si no encontramos registro de a ver tenido un like previo para
             *          el id del post enviando entonces se insertara un registro
             *          nuevo.
             ***********************************************************************/

            if ($this->db->insert($this->tableWallLike, $data)) {
                $this->general_mdl->ModificarScoreUsuario($data["user_id"], 1);
                return true;
            } else {
                return false;
            }
        } else {
            if ($this->db->delete($this->tableWallLike, $data)) {
                $this->general_mdl->ModificarScoreUsuario($data["user_id"], -1);
                return true;
            } else {
                return false;
            }
        }
    }

    function SharePost($data)
    {
        unset($data['token']);
        $valida = $this->db->get_where("wall_post_reshare", array('user_id' => $data["user_id"], 'post_id' => $data["post_id"]))->result_array();
        if (count($valida) === 0) {
            if ($this->db->insert("wall_post_reshare", $data)) {
                $this->general_mdl->ModificarScoreUsuario($data["user_id"], 1);
                return true;
            } else {
                return false;
            }
        } else {
            if ($this->db->delete("wall_post_reshare", $data)) {
                $this->general_mdl->ModificarScoreUsuario($data["user_id"], -1);
                return true;
            } else {
                return false;
            }
        }
    }

    function SaveLikeComment($data)
    {
        $where = array("user_id" => $data["user_id"], "comment_id" => $data["comment_id"]);
        $validacion = $this->db->get_where("wall_comments_like", $where)->result_array();
        if (count($validacion) == 0) {
            if ($this->db->insert("wall_comments_like", $data)) {
                $this->general_mdl->ModificarScoreUsuario($data["user_id"], 1);
                return true;
            } else {
                return false;
            }
        } else {
            if ($this->db->delete("wall_comments_like", $data)) {
                $this->general_mdl->ModificarScoreUsuario($data["user_id"], -1);
                return true;
            } else {
                return false;
            }
        }
    }

    function ObtenerCategorias()
    {
        $this->db->select("*");
        $this->db->from("wall_categories");
        return $this->db->get()->result_array();
    }
}
