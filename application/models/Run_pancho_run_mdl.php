<?php
class Run_pancho_run_mdl extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function guardarPuntuacion($data)
    {
        return $this->db->insert("game_run_pancho_results", $data);
    }

    public function obtenerMejorPuntuacion($business_id, $user_id)
    {
        $query = "set lc_time_names = 'es_MX';";
        $this->db->query($query);
        $this->db->select("concat(u.name, ' ',u.last_name) as nombre_usuario,concat(u.name, ' ',u.last_name) as nombre,gr.score, date_format(gr.fecha,'%d %M %Y') as fecha, gr.id_tema, gt.nombre as nombre_tema");
        $this->db->from("game_run_pancho_results as gr");
        $this->db->join("game_run_pancho_run_temas as gt", "gt.id = gr.id_tema");
        $this->db->join("user as u", "u.id = gr.user_id");
        $this->db->where("u.business_id", $business_id);
        $this->db->where("u.id", $user_id);
        $this->db->order_by("score", "desc");
        $this->db->limit("10");
        $result =  $this->db->get()->result_array();

        for ($i = 0; $i < count($result); $i++) {
            $result[$i]["numero"] =  base_url("assets/img/run_pancho_run/" . ($i + 1) . ".png");
        }
        return $result;
    }

    public function obtenerMejorPuntuacionEquipo($business_id, $id_job)
    {
        $query = "set lc_time_names = 'es_MX';";
        $this->db->query($query);
        $this->db->select("concat(u.name, ' ',u.last_name) as nombre_usuario,concat(u.name, ' ',u.last_name) as nombre,gr.score, date_format(gr.fecha,'%d %M %Y') as fecha, gr.id_tema, gt.nombre as nombre_tema");
        $this->db->from("game_run_pancho_results as gr");
        $this->db->join("game_run_pancho_run_temas as gt", "gt.id = gr.id_tema");
        $this->db->join("user as u", "u.id = gr.user_id");
        $this->db->where("u.business_id", $business_id);
        $this->db->where("u.job_id", $id_job);
        $this->db->order_by("score", "desc");
        $this->db->group_by("u.id");
        $this->db->limit("10");
        $result =  $this->db->get()->result_array();

        for ($i = 0; $i < count($result); $i++) {
            $result[$i]["numero"] =  base_url("assets/img/run_pancho_run/" . ($i + 1) . ".png");
        }
        return $result;
    }

    public function obtenerImagenes()
    {
        $this->db->select("concat('" . base_url("assets/img/run_pancho_run/") . "',image) as image");
        $this->db->from("game_run_pancho_images");
        $this->db->order_by("rand()");
        return $this->db->get()->result_array();
    }

    public function obtenerTemas($business_id)
    {
        $this->db->select("id,nombre");
        $this->db->from("game_run_pancho_run_temas");
        $this->db->where("business_id", $business_id);
        $temas = $this->db->get()->result_array();
        for ($i = 0; $i < count($temas); $i++) {
            $id = $temas[$i]["id"];
            $this->db->select("word, tipo");
            $this->db->from("game_run_pancho_words");
            $this->db->where("id_tema", $id);
            $this->db->order_by("rand()");
            $palabras = ["palabras" => $this->db->get()->result_array()];
            $temas[$i]["palabras"] = $palabras;
        }
        return $temas;
    }

    public function obtenerPalabras($business_id)
    {
        $this->db->select("id,nombre");
        $this->db->from("game_run_pancho_run_temas");
        $this->db->where("business_id", $business_id);
        $this->db->order_by("rand()");
        $this->db->limit("1");
        $tema = $this->db->get()->result_array()[0];
        $id = $tema["id"];
        $this->db->select("word, tipo");
        $this->db->from("game_run_pancho_words");
        $this->db->where("id_tema", $id);
        $this->db->order_by("rand()");
        $palabras = ["palabras" => $this->db->get()->result_array()];
        $tema = ["tema" => $tema];
        return array_merge($tema, $palabras);
    }

    public function guardar($tema, $palabras, $business_id)
    {
        // echo json_encode($palabras);
        $data = [];
        $data["nombre"] = $tema;
        $data["business_id"] = $business_id;
        $this->db->insert("game_run_pancho_run_temas", $data);
        $id_tema = $this->db->insert_id();
        for ($i = 0; $i < count($palabras); $i++) {
            $data_ = [];
            $data_["word"] = $palabras[$i]["word"];
            $data_["tipo"] = $palabras[$i]["tipo"] == 1 ? true : false;
            $data_["id_tema"] = $id_tema;
            $this->db->insert("game_run_pancho_words", $data_);
        }
        return true;
    }

    public function eliminar($id_tema)
    {
        $this->db->where("id", $id_tema);
        $this->db->delete("game_run_pancho_run_temas");

        $this->db->where("id_tema", $id_tema);
        $this->db->delete("game_run_pancho_words");
        return true;
    }

    public function editar($datos)
    {
        $this->db->set("nombre", $datos["nombre"]);
        $this->db->where("id", $datos["id"]);
        $this->db->update("game_run_pancho_run_temas");

        $this->db->where("id_tema", $datos["id"]);
        $this->db->delete("game_run_pancho_words");
        
        $datos["palabras"] = json_decode($datos["palabras"], true);

        for ($i = 0; $i < count($datos["palabras"]); $i++) {
            $data_ = [];
            $data_["word"] = $datos["palabras"][$i]["word"];
            $data_["tipo"] = $datos["palabras"][$i]["tipo"] == 1 ? true : false;
            $data_["id_tema"] = $datos["id"];
            $this->db->insert("game_run_pancho_words", $data_);
        }
        return true;
    }

    public function crear_reto($data)
    {
        $this->db->insert("game_run_pancho_retos", $data);
        return $this->db->insert_id();
    }

    public function agregarOponentes($data, $id_reto)
    {
        for ($i = 0; $i < count($data); $i++) {
            $data_ = [];
            $data_["id_reto"] = $id_reto;
            $data_["id_usuario"] = $data[$i];
            $this->db->insert("game_run_pancho_retos_users", $data_);
        }

        return true;
    }

    public function obtener_retos($pendientes, $user_id)
    {
        $where  = 'where grr.id is null ';
        if ($pendientes == 1) {
            $where = 'where grr.id is not null ';
        } else if ($pendientes == 2) {
            $where = "";
        }

        $query = "select gr.id as id_reto, gr.id_user, if(gr.id_user = $user_id, 1,0) as propio,
        if(grr.id is null, 1,0) as pendiente,
        if(score is null, -1, score) as correctas, if(incorrectas is null,-1,incorrectas) as incorrectas,
        gr.id_tema, g.nombre
        from game_run_pancho_retos as gr
        join game_run_pancho_run_temas as g on g.id = gr.id_tema
        left join game_run_pancho_results as grr on grr.id_reto = gr.id and grr.user_id = $user_id
        $where";
        $retos =  $this->db->query($query)->result_array();
        for ($i = 0; $i < count($retos); $i++) {
            $retos[$i]["resultados"] = $this->obtener_resultados($retos[$i]["id_reto"]);
        }
        return $retos;
    }

    function obtener_resultados($id)
    {
        $query = "select u.id, concat(u.name, ' ',u.last_name) as nombre, 
        if(gr.score is null,-1,gr.score) as correctas, 
        if(gr.incorrectas is null,-1,gr.incorrectas) as incorrectas, 
        listado_correctas,listado_incorrectas
        from game_run_pancho_retos_users as gu
        left join game_run_pancho_results as gr on gu.id_reto = gr.id_reto and gr.user_id = gu.id_usuario
        join user as u on u.id = gu.id_usuario
        where gu.id_reto = $id";

        $resultados =  $this->db->query($query)->result_array();

        for ($i = 0; $i < count($resultados); $i++) {
            if ($resultados[$i]["listado_correctas"] != null)
                $resultados[$i]["listado_correctas"] = $this->obtener_respuestas($resultados[$i]["listado_correctas"]);
            else {
                $resultados[$i]["listado_correctas"] = '';
            }
            if ($resultados[$i]["listado_incorrectas"] != null)
                $resultados[$i]["listado_incorrectas"] = $this->obtener_respuestas($resultados[$i]["listado_incorrectas"]);
            else {
                $resultados[$i]["listado_incorrectas"] = '';
            }
        }
        return $resultados;
    }

    function obtener_respuestas($listado)
    {
        $query = "select word from game_run_pancho_words where id in ($listado)";
        $palabras =  $this->db->query($query)->result_array();
        $palabras_ = [];
        for ($i = 0; $i < count($palabras); $i++) {
            array_push($palabras_, $palabras[$i]["word"]);
        }
        return $palabras_;
    }
}
