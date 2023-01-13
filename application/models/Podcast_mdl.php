<?php
class Podcast_mdl extends CI_Model
{
    private $tablePodcast = "podcast",
        $tablePodcastComments = "podcast_comments",
        $tablePodcastLike = "podcast_like",
        $tablePodcastScore = "podcast_score",
        $tablePodcastUsage = "podcast_usage";

    public function __construct()
    {
        parent::__construct();
    }

    public function ListPodcast($business_id, $user_id, $es_admin, $ids = null)
    {
        //$ids =  '55, 63';
        $group = 0;
        if (!$es_admin)
            $group = $this->obtener_grupos_implode($user_id);

        $query = "
        select p.id,p.title,p.description,p.preview,p.type,p.audio,p.etiquetas,
        DATE_FORMAT(p.date, '%d-%b-%Y') as date,p.duration,if(avg(ps.score) is null,
        0,avg(ps.score)) as promedio, if(ps.score is null, 0, ps.score) as calificacion,
        coalesce(date_format(p.fecha_limite,'%Y-%m-%d'),'') as fecha_limite, p.active
        from podcast as p force index (primary, ind_business_id, ind_active, ind_fecha_limite,ind_capacitacion_o )
        left join podcast_score as ps force index (ind_podcast_id, ind_user_id) on ps.podcast_id = p.id and ps.user_id = ?"; //user_id
        
        if (!$es_admin)
            $query .= "
		left join podcast_groups as pg force index(ind_podcast_id, ind_group_id) on pg.podcast_id = p.id
        left join podcast_users as pu force index (ind_podcast_id, ind_user_id) on pu.podcast_id = p.id";

        $query .= " where p.business_id = ? and p.id != 25"; //$business_id
        
        if (!$es_admin)
            $query .= "
        and (now() <= p.fecha_limite or p.fecha_limite is null or fecha_limite = '0000-00-00 00:00:00')
        and p.active = 1 and p.capacitacion_obligatoria = 0";

        if (!$es_admin && $ids == null)
            $query .= " and (pu.user_id = ? or pg.group_id in (?))"; //user_id //group_id

        if ($ids != null) {
            /* detectar numero de id a buscar */      
            $query_search = '';      
            $query .= " and p.id in (";
            for($i = 0; $i < count($ids); $i ++){
                $query_search .= "?,";
            }
            $query .= substr($query_search, 0, -1). ") ";
        }
        $query .= " group by p.id ";
        $query_search = '';
        if ($ids != null){
            /* detectar numero de id a buscar */
            $query .= " order by field(p.id,";
            for($i = 0; $i < count($ids); $i ++){
                $query_search .= "?,";
            }
            $query .= substr($query_search, 0, -1). ") ";
        }else {
            $query .= " order by p.active desc, p.id desc ";
        }
        if (!$es_admin && $ids == null)
            $podcast = $this->db->query($query, array($user_id, $business_id, $user_id, $group))->result_array();
        else if ($ids != null){
            $search_array = [$user_id, $business_id];
            /* primera seccion */
            for($i = 0; $i < count($ids); $i ++){
                array_push($search_array, $ids[$i]);
            }
            /* segunda seccion */
            for($i = 0; $i < count($ids); $i ++){
                array_push($search_array, $ids[$i]);
            }
            $podcast = $this->db->query($query, $search_array)->result_array();
        }
        else
            $podcast = $this->db->query($query, array($user_id, $business_id))->result_array();
        foreach ($podcast as $index => $value) {
            $podcast[$index]['preview'] = $this->agregar_ruta_imagenes($business_id, $podcast[$index]['preview']);
            $podcast[$index]['audio'] = base_url('uploads/business_' . $business_id . '/podcasts/') . $value['audio'];

            $podcast[$index]["capacitaciones"] = $this->obtener_capacitaciones_de_podcast($podcast[$index]['id']);
            $podcast[$index]["usuarios"] = $this->obtenerUsuarios($podcast[$index]["id"]);
            $podcast[$index]["grupos"] = $this->obtener_grupos($podcast[$index]["id"]);
            $podcast[$index]["asesores"] = $this->obtener_asesores($podcast[$index]["id"]);
            $podcast[$index]["regiones"] = $this->obtener_regiones($podcast[$index]["id"]);
            if($es_admin){
                $extracto = $this->obtener_extracto($podcast[$index]["id"]);
                $podcast[$index]["resumen"] = $extracto["resumen"];
                //$podcast[$index]["etiquetas"] = $extracto["etiquetas"];
            }
        }
        return $podcast;
    }

    function obtener_extracto($library_id)
    {
        $query = "select resumen, etiquetas, id_categoria
        from extractos where library_id = ?";
        $result = $this->db->query($query, array($library_id))->result_array();
        if (count($result) > 0) {
            return $result[0];
        }
        return array("resumen" => "", "etiquetas" => "", "id_categoria" => 0);
    }


    function agregar_ruta_imagenes($business_id, $valor)
    {
        $url = '';
        if ($valor != '') {
            $url = base_url('uploads/business_' . $business_id . '/podcasts/') . $valor;
        } else
            $url = base_url('assets/img/img_h_podcast.png');
        return $url;
    }

    function obtener_capacitaciones_de_podcast($id_capacitacion)
    {
        $query = "SELECT l.id, l.name
        FROM capacit_detail AS d
        INNER JOIN capacit_list AS l ON (l.id = d.id_capacitacion)
        INNER JOIN capacit_categorias as cc on cc.id = d.catalog
        WHERE cc.`catalog` = 'podcast'
        AND d.id_elemento = ?;";

        return $this->db->query($query, array($id_capacitacion))->result_array();
    }

    function obtener_grupos_implode($user_id)
    {
        $query = "
            select ug.group_id
            from users_groups as ug  force index (ind_group_id, ind_user_id, ind_active)
            join groups as g force index (primary, ind_active) on g.id = ug.group_id
            where ug.user_id = ?
            and g.active = 1 and ug.active = 1";
        $group = $this->db->query($query, array($user_id))->result_array();

        $group = implode(',', array_map(function ($string) {
            return $string['group_id'];
        }, $group));
        return $group;
    }

    public function get_podcast_by_id($business_id, $podcast_id, $user_id)
    {
        unset($params['token']);
        $query = "select p.id,p.title,p.description,p.preview,p.type,p.audio,
            DATE_FORMAT(p.date, '%d-%b-%Y') as date,p.duration,
            if(avg(ps.score) is null,0,avg(ps.score)) as promedio,
            if(ps.score is null, 0, ps.score) as calificacion,
            coalesce(date_format(p.fecha_limite,'%Y-%m-%d'),'')
            as fecha_limite, p.active
            from podcast as p
            left join podcast_score as ps on ps.podcast_id = p.id and ps.user_id = 87
            where p.business_id = ? and p.id = ?
            group by p.id";

        $podcast = $this->db->query($business_id, $podcast_id)->result_array();

        foreach ($podcast as $index => $value) {
            $podcast[$index]['preview'] = $this->agregar_ruta_imagenes($business_id, $podcast[$index]['preview']);
            $podcast[$index]['audio'] = base_url('uploads/business_' . $business_id . '/podcasts/') . $value['audio'];

            $podcast[$index]["capacitaciones"] = $this->obtener_capacitaciones_de_podcast($podcast[$index]['id']);
            $podcast[$index]["usuarios"] = $this->obtenerUsuarios($podcast[$index]["id"]);
            $podcast[$index]["grupos"] = $this->obtener_grupos($podcast[$index]["id"]);
            $podcast[$index]["asesores"] = $this->obtener_asesores($podcast[$index]["id"]);
            $podcast[$index]["regiones"] = $this->obtener_regiones($podcast[$index]["id"]);
        }
        return $podcast;
    }

    function obtenerUsuarios($podcast_id)
    {
        $query = "
            select u.id, u.name, u.last_name
            from user as u
            join podcast_users as pu on pu.user_id = u.id
            where pu.podcast_id = ?";
        return $this->db->query($query, array($podcast_id))->result_array();
    }

    function obtener_grupos($podcast_id)
    {
        $query = "select g.* from groups as g
                  join podcast_groups as pg on pg.group_id = g.id
                  where pg.podcast_id = ?";
        return $this->db->query($query, array($podcast_id))->result_array();
    }

    function obtener_asesores($podcast_id)
    {
        $query = "select u.id, concat(u.name, ' ', u.last_name) as name
                  from user as u
                  join podcast_asesores as pg on pg.id_asesor = u.id
                  where pg.podcast_id = ?";
        return $this->db->query($query, array($podcast_id))->result_array();
    }

    function obtener_regiones($podcast_id)
    {
        $query = "select r.* from regiones as r
                  join podcast_regiones as pg on pg.id_region = r.id
                  where pg.podcast_id = ?";
        return $this->db->query($query, array($podcast_id))->result_array();
    }

    public function ListComments($data)
    {
        unset($data['token']);
        $query = "
            select pc.id,pc.comment,pc.user_id,pc.likes,
            concat(u.name,' ',u.last_name) as name_user,
            u.profile_photo, if(pl.user_id is null,0,1) as like_user
            from podcast_comments as pc 
            join user as u on pc.user_id = u.id
            left join podcast_like as pl on pl.comment_id = pc.id and pl.user_id = ?
            where pc.podcast_id = ?
            group by pc.id";
        return $this->db->query($query, array($data["user_id"], $data["podcast_id"]))->result_array();
    }

    public function SaveComment($data)
    {
        unset($data['token']);
        $this->general_mdl->ModificarScoreUsuario($data["user_id"], 1);
        return $this->db->insert($this->tablePodcastComments, $data);
    }

    public function LikeUnlike($data)
    {
        unset($data['token']);
        $data_insert = $data;
        $validate = $this->db->get_where($this->tablePodcastLike, $data)->result_array();
        $data['id'] = $data['comment_id'];
        unset($data['comment_id']);
        $cant = 0;
        if (count($validate) > 0) {
            $cant = -1;
            $this->eliminar_like($data_insert);
        } else {
            $cant = 1;
            $this->agregar_like($data_insert);
        }
        $this->general_mdl->ModificarScoreUsuario($data["user_id"], $cant);
        return $this->db->editar_numero_likes($data["id"], $cant);
    }

    function eliminar_like($data)
    {
        $this->db->where("comment_id", $data["comment_id"]);
        $this->db->where("user_id", $data["user_id"]);
        return $this->db->delete("podcast_like");
    }

    function agregar_like($data)
    {
        return $this->db->insert("podcast_like", $data);
    }

    function editar_numero_likes($id, $numero)
    {
        $query = "update podcast_comments set likes = likes + ? where id = ?";
        $this->db->query($query, $id, $numero);
    }

    public function ObtenerEstrellas($id_podcast)
    {
        $this->db->select('score');
        $this->db->from($this->tablePodcastScore);
        $this->db->where('podcast_id', $id_podcast);
        return $this->db->get()->result_array()[0];
    }

    public function CalificarPodcast($data)
    {
        $result = $this->validar_calificado($data);
        if ($result) {
            return $this->actualizar_calificacion($data, $result[0]["id"]);
        } else {
            $this->general_mdl->ModificarScoreUsuario($data["user_id"], 1);
            return $this->insertar_calificacion($data);
        }
    }

    function validar_calificado($data)
    {
        $this->db->select("id");
        $this->db->from("podcast_score");
        $this->db->where('podcast_id', $data["podcast_id"]);
        $this->db->where('user_id', $data["user_id"]);
        return $this->db->get()->result_array();
    }

    function actualizar_calificacion($data, $id)
    {
        $this->db->set('score', $data["score"]);
        $this->db->where('id', $id);
        return $this->db->update("podcast_score");
    }

    function insertar_calificacion($data)
    {
        $dataa = array(
            "score" => $data["score"],
            "podcast_id" => $data["podcast_id"],
            "user_id" => $data["user_id"],
        );
        return $this->db->insert("podcast_score", $dataa);
    }

    public function SetVisto($data)
    {
        $result = $this->valida_visto($data);
        if ($result) {
            return $this->actualizar_visto($result[0]["veces_visto"], $result[0]["id"]);
        } else {
            $this->general_mdl->ModificarScoreUsuario($data["user_id"], 1);
            return $this->insertar_visto($data);
        }
    }

    function valida_visto($data)
    {
        $this->db->select("id,veces_visto");
        $this->db->from("podcast_usage");
        $this->db->where('podcast_id', $data["podcast_id"]);
        $this->db->where('user_id', $data["user_id"]);
        return $this->db->get()->result_array();
    }

    function actualizar_visto($veces_visto, $id)
    {
        $this->db->set('veces_visto', $veces_visto + 1);
        $this->db->where('id', $id);
        return $this->db->update("podcast_usage");
    }

    function insertar_visto($data)
    {
        $dataa = array(
            "veces_visto" => 1,
            "podcast_id" => $data["podcast_id"],
            "user_id" => $data["user_id"],
        );
        return $this->db->insert("podcast_usage", $dataa);
    }

    public function obtener_podcasts_capacitacion($business_id)
    {
        $query = "select * from podcast 
                  where business_id = ? 
                  and capacitacion_obligatoria = 1";
        return $this->db->query($query, array($business_id))->result_array();
    }

    public function sumar_compartido($id_topic, $user_id)
    {
        $data = [];
        $data["user_id"] = $user_id;
        $data["element_id"] = $id_topic;
        return $this->db->insert("podcast_shared", $data);
    }

    /* Obtener lista de podcast nuevos con rango de fecha */
    public function obtenerNuevoContenidoPorRangoFecha($user_id, $business_id, $fecha_inicio, $fecha_actual)
    {
        $group = $this->obtener_grupos_implode($user_id);
        $query = "SELECT p.id,p.title,p.description,p.preview,p.type,p.audio,p.etiquetas,p.date,p.active
                    from podcast as p force index (primary, ind_business_id, ind_active, ind_fecha_limite,ind_capacitacion_o )
                    left join podcast_score as ps force index (ind_podcast_id, ind_user_id)
                        on ps.podcast_id = p.id
                        and ps.user_id = ?
                    left join podcast_groups as pg force index(ind_podcast_id, ind_group_id)
                        on pg.podcast_id = p.id
                    left join podcast_users as pu force index (ind_podcast_id, ind_user_id)
                        on pu.podcast_id = p.id 
                    WHERE p.business_id = ? and p.id != 25
                        and (now() <= p.fecha_limite or p.fecha_limite is null or fecha_limite = '0000-00-00 00:00:00')
                        and p.active = 1 and p.capacitacion_obligatoria = 0
                        and (pu.user_id = ? or pg.group_id in (?))
                        and p.date between ? and ?
                    group by p.id  order by p.active desc, p.id desc";
        $podcast = $this->db->query($query, array($user_id, $business_id, $user_id, $group, $fecha_inicio, $fecha_actual))->result_array();
        if(count($podcast) > 0){
            return $podcast;
        }else{
            return [];
        }
    }
 
    /* obtener fecha del ultimo elemento */
    public function obtenerFechaUltimoContenido()
    {
        $query = 'SELECT date FROM podcast ORDER BY date DESC';
        return $this->db->query($query)->result_array()[0];
    }
    
}
