<?php
class Game_hormigas_mdl extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function obtener_tema()
    {
        $query = "select * from game_hormigas_temas
        order by rand()
        limit 1";
        $tema = $this->db->query($query)->result_array()[0];
        $tema["palabras"] = $this->obtener_palabras($tema["id"]);
        return $tema;
    }

    function obtener_palabras($id_tema)
    {
        $query = "select * from game_hormigas_palabras
        where id_tema = ?";
        return $this->db->query($query, array($id_tema))->result_array();
    }

    function guardar_resultado($data)
    {
        return $this->db->insert("game_hormigas_result", $data);
    }

    public function obtenerMejorPuntuacion($business_id)
    {
        $query = "select concat(u.name, ' ',u.last_name) as nombre_usuario,
        concat(u.name, ' ',u.last_name) as nombre,
        gr.id_tema, gt.nombre as nombre_tema, sum(gr.resultado) as score
        from game_hormigas_result as gr
        join game_hormigas_temas as gt on gt.id = gr.id_tema
        join user as u on u.id = gr.user_id
        where u.business_id = ?
        group by u.id
        order by score desc
        limit 10";
        $result =  $this->db->query($query, array($business_id))->result_array();

        for ($i = 0; $i < count($result); $i++) {
            $result[$i]["numero"] =  base_url("assets/img/run_pancho_run/" . ($i + 1) . ".png");
        }
        return $result;
    }

    public function obtenerMejorPuntuacionEquipo($business_id, $id_job)
    {
        $query = "select concat(u.name, ' ',u.last_name) as nombre_usuario,
        concat(u.name, ' ',u.last_name) as nombre,
        gr.id_tema, gt.nombre as nombre_tema, sum(gr.resultado) as score
        from game_hormigas_result as gr
        join game_hormigas_temas as gt on gt.id = gr.id_tema
        join user as u on u.id = gr.user_id
        where u.business_id = ?
        and u.job_id = ?
        group by u.id
        order by score desc
        limit 10";
        $result =  $this->db->query($query, array($business_id, $id_job))->result_array();

        for ($i = 0; $i < count($result); $i++) {
            $result[$i]["numero"] =  base_url("assets/img/run_pancho_run/" . ($i + 1) . ".png");
        }
        return $result;
    }
}
