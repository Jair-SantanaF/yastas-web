<?php

class Analiticos_mdl extends CI_Model
{
    private $tableUser = "user",
        $tableBusinesss = "business",
        $tableInvitation = "invitation",
        $tableWall = "wall",
        $tableWallComments = "wall_comments",
        $tableWallPostLikes = "wall_post_like",
        $tablePodcast = "podcast",
        $tablePodcastComments = "podcast_comments",
        $tablePodcastPostLikes = "podcast_like",
        $tableFeedback = "feedback";

    public function __construct()
    {
        parent::__construct();
    }

    public function ObtenerCantidadUsuariosRegistrados($id_empresa)
    {
        $this->db->select('count(*) as usuarios_registrados');
        $this->db->from($this->tableUser . ' as u');
        $this->db->where('u.business_id =', $id_empresa);
        $result = $this->db->get()->result_array();
        return array_slice($result, 0, 10);
    }

    public function ObtenerCantidadInvitadosNoRegistrados($id_empresa)
    {
        $this->db->select('count(*) as invitados');
        $this->db->from($this->tableInvitation . ' as i');
        $this->db->where('i.status =', 0);
        $this->db->where("i.business_id =", $id_empresa);
        $result = $this->db->get()->result_array();
        return array_slice($result, 0, 10);
    }

    public function ObtenerPostMasComentados($id_empresa, $rango_fechas)
    {
        $this->db->select('w.id,w.wall_description, count(wc.comment) as num_comentarios,w.business_id');
        $this->db->from($this->tableWall . ' as w');
        $this->db->join($this->tableWallComments . ' as wc', 'wc.post_id = w.id');
        $this->db->join($this->tableUser . " as u", "u.id = wc.user_id");
        $this->db->where('w.business_id =', $id_empresa, false);
        $this->db->where("w.active = 1");
        if ($rango_fechas["fecha_inicio"] !== null && $rango_fechas["fecha_fin"] !== null) {
            $this->db->where('wc.created_at BETWEEN "' . $rango_fechas["fecha_inicio"] . '" and "' . $rango_fechas["fecha_fin"] . '"');
        }
        if ($rango_fechas["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $rango_fechas["nombre_usuario"]);
        }
        $this->db->group_by('wc.post_id');
        $this->db->order_by('num_comentarios', "desc");
        $result = $this->db->get()->result_array();
        return array_slice($result, 0, 10);
    }

    public function ObtenerPostMasGustados($id_empresa, $rango_fechas)
    {
        $this->db->select('w.id,w.wall_description, count(*) as num_likes');
        $this->db->from($this->tableWall . ' as w');
        $this->db->join($this->tableWallPostLikes . ' as wpl', 'wpl.post_id = w.id');
        $this->db->join($this->tableUser . " as u", "u.id = wpl.user_id");
        $this->db->where('w.business_id =', $id_empresa);
        $this->db->where("w.active = 1");
        if ($rango_fechas["fecha_inicio"] !== null && $rango_fechas["fecha_fin"] !== null) {
            $this->db->where('wpl.created_at BETWEEN "' . $rango_fechas["fecha_inicio"] . '" and "' . $rango_fechas["fecha_fin"] . '"');
        }
        if ($rango_fechas["nombre_usuario"] !== null) {
            $this->db->where("concat(u.name,' ',u.last_name) like " . $rango_fechas["nombre_usuario"]);
        }
        $this->db->group_by('wpl.post_id');
        $this->db->order_by('num_likes', "desc");
        $result = $this->db->get()->result_array();
        return array_slice($result, 0, 10);
    }

    public function ObtenerPodcastMasComentados($id_empresa, $rango_fechas)
    {
        $this->db->select('p.id,p.title, count(*) as num_comentarios');
        $this->db->from($this->tablePodcast . ' as p');
        $this->db->join($this->tablePodcastComments . ' as pc', 'pc.podcast_id = p.id');
        $this->db->where('p.business_id =', $id_empresa);
        $this->db->where('pc.datetime BETWEEN "' . $rango_fechas["fecha_inicio"] . '" and "' . $rango_fechas["fecha_fin"] . '"');
        $this->db->group_by('pc.podcast_id');
        $this->db->order_by('num_comentarios', "desc");
        $result = $this->db->get()->result_array();
        return array_slice($result, 0, 10);
    }

    public function ObtenerUsuariosQueMasRetroalimentan($id_empresa, $rango_fechas)
    {
        $this->db->select('u.name, count(*) as num_comentarios_feedback');
        $this->db->from($this->tableFeedback . ' as f');
        $this->db->join($this->tableUser . ' as u', 'u.id = f.owner_id');
        $this->db->where('u.business_id =', $id_empresa);
        $this->db->where('f.fecha BETWEEN "' . $rango_fechas["fecha_inicio"] . '" and "' . $rango_fechas["fecha_fin"] . '"');
        $this->db->group_by('f.owner_id');
        $this->db->order_by('num_comentarios_feedback', "desc");
        $result = $this->db->get()->result_array();
        return array_slice($result, 0, 10);
    }

    public function ObtenerUsuariosQueMasLikesDanWall($id_empresa)
    {
        $this->db->select('u.name, count(*) as num_likes_wall');
        $this->db->from($this->tablePodcastPostLikes . ' as pl');
        $this->db->join($this->tableUser . ' as u', 'u.id = pl.user_id');
        $this->db->where('u.business_id =', $id_empresa);
        $this->db->group_by('pl.user_id');
        $this->db->order_by('num_likes_wall', "desc");
        $result = $this->db->get()->result_array();
        return array_slice($result, 0, 10);
    }

    public function ObtenerUsuariosQueComentanPodcast($id_empresa, $rango_fechas)
    {
        $this->db->select('u.name, count(*) as num_comentarios_podcast');
        $this->db->from($this->tablePodcastComments . ' as pc');
        $this->db->join($this->tableUser . ' as u', 'u.id = pc.user_id');
        $this->db->where('u.business_id =', $id_empresa);
        $this->db->where('pc.datetime BETWEEN "' . $rango_fechas["fecha_inicio"] . '" and "' . $rango_fechas["fecha_fin"] . '"');
        $this->db->group_by('pc.user_id');
        $this->db->order_by('num_comentarios_podcast', "desc");
        $result = $this->db->get()->result_array();
        return array_slice($result, 0, 10);
    }

    public function ObtenerUsuariosQueMasComentanWall($id_empresa, $rango_fechas)
    {
        $this->db->select('u.name, count(*) as num_comentarios_wall');
        $this->db->from($this->tableWallComments . ' as wc');
        $this->db->join($this->tableUser . ' as u', 'u.id = wc.user_id');
        $this->db->where('u.business_id =', $id_empresa);
        $this->db->where('wc.created_at BETWEEN "' . $rango_fechas["fecha_inicio"] . '" and "' . $rango_fechas["fecha_fin"] . '"');
        $this->db->group_by('wc.user_id');
        $this->db->order_by('num_comentarios_wall', "desc");
        $result = $this->db->get()->result_array();
        return array_slice($result, 0, 10);
    }
}
