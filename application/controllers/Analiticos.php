<?php
class Analiticos extends CI_Controller
{
    public $defaultLang = 'es';

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("America/Mexico_City");

        $headers = $this->input->request_headers();
        if (isset($headers['lenguage'])) {
            $this->lang->load('message', 'es');
            $this->defaultLang = 'es';
        } else {
            $this->lang->load('message', 'en');
            $this->defaultLang = 'en';
        }
        $this->load->model('analiticos_mdl', 'analiticos');
        $this->load->helper("general");
    }

    public function ObtenerCantidadUsuarios()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->input->post();
        $data = $valida_token['business_id'];
        $usuarios_registrados = $this->ObtenerCantidadUsuariosRegistrados($data);
        $invitados = $this->ObtenerCantidadInvitadosNoRegistrados($data);
        $usuarios = array_merge($usuarios_registrados, $invitados);
        // if($cantidad_usuarios_registrados){
            $this->general_mdl->writeLog("Consulta cantidad de usuarios usuario " . $valida_token["user_id"], "<info>");
        successResponse($usuarios, 'Usuarios', $this);
        // }else{
        //     faildResponse('No existen registros',$this);
        // }
    }

    public function ObtenerCantidadUsuariosRegistrados($data)
    {
        $cantidad_usuarios_registrados = $this->analiticos->ObtenerCantidadUsuariosRegistrados($data);
        return $cantidad_usuarios_registrados;
    }

    public function ObtenerCantidadInvitadosNoRegistrados($data)
    {
        $cantidad_invitados_no_registrados = $this->analiticos->ObtenerCantidadInvitadosNoRegistrados($data);
        return $cantidad_invitados_no_registrados;
    }

    public function ObtenerPostMasComentados()
    {
        $tipo = $this->input->get("tipo");
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->input->post();
        $data = $valida_token['business_id'];
        $rango = [];//$this->ObtenerRango($tipo);
        $rango["fecha_inicio"] = $this->input->get("fecha_inicio");
        $rango["fecha_fin"] = $this->input->get("fecha_fin");
        $rango["nombre_usuario"] = $this->input->get("nombre_usuario");
        $post = [];
        $post["comentarios"] = $this->analiticos->ObtenerPostMasComentados($data, $rango);
        $post["likes"] = $this->analiticos->ObtenerPostMasGustados($data,$rango);
        // if ($post_mas_comentados) {
        successResponse($post, 'Post mas comentados en wall', $this);
        // } else {
        // faildResponse('No existen registros', $this);
        // }
    }

    public function ObtenerPostMasGustados()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->input->post();
        $data = $valida_token['business_id'];
        $post_mas_gustados = $this->analiticos->ObtenerPostMasGustados($data);
        // if ($post_mas_gustados) {
        successResponse($post_mas_gustados, 'Post mas gustados en wall', $this);
        // } else {
        //     faildResponse('No existen registros', $this);
        // }
    }

    public function ObtenerPodcastMasComentados()
    {
        $tipo = $this->input->get("tipo");
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->input->post();
        $data = $valida_token['business_id'];
        $rango = $this->ObtenerRango($tipo);
        $podcast_mas_comentados = $this->analiticos->ObtenerPodcastMasComentados($data, $rango);
        // if ($podcast_mas_comentados) {
        successResponse($podcast_mas_comentados, 'Podcast mas comentados', $this);
        // } else {
        //     faildResponse('No existen registros', $this);
        // }
    }

    public function ObtenerUsuariosMasActivos()
    {
        $tipo = $this->input->get("tipo");
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->input->post();
        $data = $valida_token['business_id'];
        $rango = $this->ObtenerRango($tipo);
        $usuarios_que_mas_retroalimentan = $this->analiticos->ObtenerUsuariosQueMasRetroalimentan($data, $rango);
        $usuarios_que_mas_dan_like_wall = $this->analiticos->ObtenerUsuariosQueMasLikesDanWall($data);
        $usuarios_que_mas_comentan_podcasts = $this->analiticos->ObtenerUsuariosQueComentanPodcast($data, $rango);
        $usuarios_que_mas_comentan_wall = $this->analiticos->ObtenerUsuariosQueMasComentanWall($data, $rango);

        $usuarios_mas_activos = $this->unirArrays($usuarios_que_mas_comentan_podcasts, $usuarios_que_mas_comentan_wall, $usuarios_que_mas_dan_like_wall, $usuarios_que_mas_retroalimentan);
        $usuarios_mas_activos = $this->ordenarArray($usuarios_mas_activos);
        // if ($usuarios_mas_activos) {
        successResponse($usuarios_mas_activos, 'Usuarios mas activos', $this);
        // } else {
        //     faildResponse('No existen registros', $this);
        // }
    }

    private function unirArrays($usuarios_podcast, $usuarios_wall_comments, $usuarios_wall_like, $usuarios_feedback)
    {
        $array = $usuarios_podcast;

        foreach ($usuarios_wall_comments as $usuario) {
            $key = array_search($usuario["name"], array_column($array, "name"));
            if ($key === false) {
                $usuario["num_likes_wall"] = 0;
                $usuario["num_comentarios_feedback"] = 0;
                $usuario["num_comentarios_podcast"] = 0;
                array_push($array, $usuario);
                $key = count($array) - 1;
            }
            $array[$key]['num_comentarios_wall'] = $usuario["num_comentarios_wall"];
        }

        foreach ($usuarios_wall_like as $usuario) {
            $key = array_search($usuario["name"], array_column($array, "name"));
            if ($key === false) {
                $usuario["num_comentarios_wall"] = 0;
                $usuario["num_comentarios_feedback"] = 0;
                $usuario["num_comentarios_podcast"] = 0;
                array_push($array, $usuario);
                $key = count($array) - 1;
            }
            $array[$key]['num_likes_wall'] = $usuario["num_likes_wall"];
        }

        foreach ($usuarios_feedback as $usuario) {
            $key = array_search($usuario["name"], array_column($array, "name"));
            if ($key === false) {
                $usuario["num_comentarios_wall"] = 0;
                $usuario["num_likes_wall"] = 0;
                $usuario["num_comentarios_podcast"] = 0;
                array_push($array, $usuario);
                $key = count($array) - 1;
            }
            $array[$key]['num_comentarios_feedback'] = $usuario["num_comentarios_feedback"];
        }
        return $array;
    }

    private function ordenarArray($array)
    {
        for ($i = 0; $i < count($array); $i++) {
            $usuario = $array[$i];
            $interacciones = isset($usuario["num_comentarios_feedback"]) ? $usuario["num_comentarios_feedback"] : 0;
            $interacciones += isset($usuario["num_likes_wall"]) ? $usuario["num_likes_wall"] : 0;
            $interacciones += isset($usuario["num_comentarios_podcast"]) ? $usuario["num_comentarios_podcast"] : 0;
            $interacciones += isset($usuario["num_comentarios_wall"]) ? $usuario["num_comentarios_wall"] : 0;
            $array[$i]["total_interacciones"] = $interacciones;
        }
        return $array;
    }

    private function ObtenerRango($tipo)
    {
        $rangos = [];
        switch ($tipo) {
            case 1:
                $rangos["fecha_inicio"] = date('Y-m-d');
                $rangos["fecha_fin"] = date("Y-m-d");
                break;
            case 2:
                $rangos = ObtenerInicioFinDeSemana();
                break;
            case 3:
                $rangos = ObtenerInicioFinDeMes();
                break;
            case 4:
                $rangos = ObtenerInicioFinSiempre();
                break;
        }
        return $rangos;
    }
}
