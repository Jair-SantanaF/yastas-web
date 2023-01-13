<?php


class Wall_model extends CI_Model
{

    private $tableName = "wall";
    private $tableWall = "wall";
    private $tableWallLike = "wall_post_like";
    private $tableComments = "wall_comments";
    private $tableUser = "user";

    function __construct()
    {
        parent::__construct();
    }

    public function insert($entity)
    {
        if ($this->db->insert($this->tableName, $entity)) {
            return $this->db->insert_id();
        } else {
            return false;
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

    public function fetchAll()
    {
        return $this->db->get($this->tableName)->result_array();
    }

    public function fetchAllById($id)
    {
        return $this->db->get_where($this->tableName, array("id" => $id))->result_array();
    }

    public function getAll()
    {
        $query = "SELECT 
							w.* ,
							concat( u.name , ' ' , u.last_name) as 'nombre_creador',
							u.profile_photo
						FROM wall w
						LEFT JOIN user u ON u.id = w.user_id";
        return $this->db->query($query)->result_array();
    }


    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para guardar una publicación
     ***********************************************************************/
    function SavePost($data)
    {
        $dataa = array(
            "wall_description" => $data["wall_description"],
            "active" => 1,
            "business_id" => $data["business_id"],
            "user_id" => $data["user_id"],
            "redirigir" => $data["redirect"],
            "tipo" => $data["tipo"],
            "es_noticia" => 1
        );

        if ($data['image_path'] != '')
            $dataa['image_path'] = $data['image_path'];

        if ($data['image_preview'] != '')
            $dataa['image_preview'] = $data['image_preview'];
        else {
            $dataa['image_preview'] = $data['image_path'];
        }

        if ($this->db->insert($this->tableWall, $dataa)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para editar una publicación
     ***********************************************************************/
    function EditPost($data)
    {
        $key = array('id' => $data["id"]);

        $dataa = array(
            "wall_description" => $data["wall_description"]
        );

        if ($data['image_path'] != '')
            $dataa['image_path'] = $data['image_path'];

        if ($data['image_preview'] != '')
            $dataa['image_preview'] = $data['image_preview'];
        else {
            $dataa['image_preview'] = $data['image_path'];
        }

        if ($this->db->update($this->tableWall, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar una publicación
     ***********************************************************************/
    function DeletePost($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => $data["active"]
        );

        if ($this->db->update($this->tableWall, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener las publicaciones
     ***********************************************************************/
    function Posts($business_id, $filtro)
    {
        $this->db->select('w.id, w.wall_description, w.image_path, concat(u.name, " ", u.last_name) AS name, IFNULL(c.likes, 0) AS likes, ifnull(z.comentarios,0) as comentarios, w.active');
        $this->db->from($this->tableWall . ' as w');
        $this->db->join($this->tableUser . ' as u', 'w.user_id = u.id');
        $this->db->join('(SELECT c.post_id, count(c.user_id) AS likes FROM wall_post_like c GROUP BY c.post_id) AS c', 'w.id = c.post_id', 'left');
        $this->db->join('(SELECT z.post_id, count(z.user_id) AS comentarios FROM wall_comments as z where z.active = 1 GROUP BY z.post_id) AS z', 'w.id = z.post_id', 'left');
        $this->db->where('w.business_id =', $business_id);
        // $this->db->where('w.active =', 1);
        $this->db->order_by('w.active', 'desc');
        $this->db->order_by('w.wall_description', 'asc');

        if (isset($filtro) && $filtro == "activo") {
            $this->db->where('w.active', 1);
        }
        if (isset($filtro) && $filtro == "noactivo") {
            $this->db->where('w.active', 0);
        }

        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            $url_upload = base_url('uploads/business_' . $business_id . '/walls/');
            foreach ($result as $index => $value) {
                $result[$index]['image_path'] = $url_upload . $value['image_path'];
                $result[$index]["grupos"] = $this->obtener_grupos($result[$index]["id"]);
                $result[$index]["usuarios"] = $this->obtener_usuarios($result[$index]["id"]);
            }
            return $result;
        } else {
            return false;
        }
    }

    function obtener_grupos($id)
    {
        $query = "select g.* from groups as g
                 join wall_groups as wg on wg.group_id = g.id
                 where wg.wall_id = $id";
        return $this->db->query($query)->result_array();
    }

    function obtener_usuarios($id)
    {
        $query = "select u.id, concat(u.name, ' ', u.last_name) as name from user as u
                 join wall_users as wg on wg.user_id = u.id
                 where wg.wall_id = $id";
        return $this->db->query($query)->result_array();
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para guardar un comentario
     ***********************************************************************/
    function SaveComment($data)
    {
        $dataa = array(
            "comment" => $data["comment"],
            "post_id" => $data["post_id"],
            "user_id" => $data["user_id"],
            "active" => 1
        );

        if ($this->db->insert($this->tableComments, $dataa)) {
            $this->general_mdl->ModificarScoreUsuario($data["user_id"], 1);
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para editar un comentario
     ***********************************************************************/
    function EditComment($data)
    {
        $key = array('id' => $data["id"]);

        $dataa = array(
            "comment" => $data["comment"]
        );

        if ($this->db->update($this->tableComments, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar un comentario
     ***********************************************************************/
    function DeleteComment($data)
    {
        $query = "select user_id from wall_comments where id = " . $data["id"];
        $user_id = $this->db->query($query)->result_array()[0]["user_id"];
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableComments, $dataa, $key)) {
            $this->general_mdl->ModificarScoreUsuario($user_id, -1);
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para listar los comentarios de una publicacion
     ***********************************************************************/
    function Comments($data)
    {
        $this->db->select('c.id, c.comment, c.user_id, concat(u.name, " ", u.last_name) AS name');
        $this->db->from($this->tableWall . ' as w');
        $this->db->join($this->tableComments . ' as c', 'c.post_id = w.id');
        $this->db->join($this->tableUser . ' as u', 'c.user_id = u.id');
        $this->db->where('w.business_id =', $data['business_id']);
        $this->db->where('w.id =', $data['wall_post_id']);
        $this->db->where('c.active =', 1);

        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    function agregarUsuarios($wall_id, $usuarios)
    {
        for ($i = 0; $i < count($usuarios); $i++) {
            $data = [];
            $data["user_id"] = $usuarios[$i];
            $data["wall_id"] = $wall_id;
            $this->db->insert("wall_users", $data);
        }
    }

    function agregarGrupos($wall_id, $grupos)
    {
        for ($i = 0; $i < count($grupos); $i++) {
            $data = [];
            $data["group_id"] = $grupos[$i];
            $data["wall_id"] = $wall_id;
            $this->db->insert("wall_groups", $data);
        }
    }
}
