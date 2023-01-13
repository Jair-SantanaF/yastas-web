<?php
// require_once BASE_PATH . 'vendor/autoload.php';

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;

class Reporte_resultados extends CI_Controller
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
        $this->load->model('reporte_resultados_mdl', 'reporte');
        $this->load->helper("general");
    }

    public function get_data($post)
    {
        $data = [];
        $data["fecha_inicio"] = $post["fecha_inicio"];
        $data["fecha_fin"] = $post["fecha_fin"];
        $data["limite"] = !empty($post["limite"])? $post["limite"]: false;
        $data["nombre_usuario"] = null; //$post["nombre_usuario"];
        $data["group_id"] = null; //$post["group_id"];
        if (isset($post["id_capacitacion"]))
            $data["id_capacitacion"] = $post["id_capacitacion"];
        if (isset($post["tipo"]))
            $data["tipo"] = $post["tipo"];
        return $data;
    }

    public function graficas_individuales()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->get_data($this->input->post());
        $data["business_id"] = $valida_token["business_id"];
        $data["limite"] = false;
        $grafica = $this->input->post("grafica");
        $result = [];
        //solo una de las siguientes se va a realizar es para obtener todos los datos
        //sin ningun limite como en las graficas generales

        if ($grafica == "juegos")
            $result["juegos"] = $this->reporte->ObtenerJuegos($data);
        if ($grafica == "reporte_ranking")
            $result["reporte_ranking"] = $this->reporte->ObtenerRankingUsuarios($data);
        if ($grafica == "reporte_juegos")
            $result["reporte_juegos"] = $this->reporte->ObtenerReporteJuegos($data);
        if ($grafica == "reporte_feedback")
            $result["reporte_feedback"] = $this->reporte->ObtenerReporteFeedback($data);
        if ($grafica == "reporte_ambiente_laboral_semana")
            $result["reporte_ambiente_laboral_semana"] = $this->reporte->ObtenerPromedioDeAmbienteLaboral($data);
        if ($grafica == "reporte_ambiente_laboral_mes")
            $result["reporte_ambiente_laboral_mes"] = $this->reporte->ObtenerPromedioDeAmbienteLaboral($data);
        if ($grafica == "cuestionarios_mas_intentos")
            $result["cuestionarios_mas_intentos"] = $this->reporte->GraficaCuestionariosMasIntentos($data);
        if ($grafica == "calificacion_cuestionarios")
            $result["calificacion_cuestionarios"] = $this->reporte->GraficaCalificacionCuestionarios($data);
        if ($grafica == "cuestionarios_calificacion_final")
            $result["cuestionarios_calificacion_final"] = $this->reporte->GraficaCuestionarioCalificacionFinalPorUsuario($data);
        if ($grafica == "cuestionarios_dias_mas_consumo")
            $result["cuestionarios_dias_mas_consumo"] = $this->reporte->GraficaCuestionariosDiasMasConsumo($data);
        if ($grafica == "participacion_quiz")
            $result["participacion_quiz"] = $this->reporte->ObtenerReporteParticipacionEnCuestionarios($data);
        if ($grafica == "cuestionarios")
            $result["cuestionarios"] = $this->reporte->ObtenerCuestionariosPorFecha($data);
        if ($grafica == "capacitaciones_terminadas")
            $result["capacitaciones_terminadas"] = $this->reporte->obtenerGraficaCapacitacionesTerminadas($data);
        if ($grafica == "capacitacion_mas_activa")
            $result["capacitacion_mas_activa"] = $this->reporte->obtenerGraficaCapacitacionConsumo($data);
        if ($grafica == "usuarios_activos_capacitacion")
            $result["usuarios_activos_capacitacion"] = $this->reporte->ObtenerGraficaUsuariosActivosCapacitacion($data);
        if ($grafica == "dias_consumo_capacitacion")
            $result["dias_consumo_capacitacion"] = $this->reporte->ObtenerGraficaDiasConsumoCapacitacion($data);
        if ($grafica == "reporte_comunidad")
            $result["reporte_comunidad"] = $this->reporte->ObtenerReporteComunidadDeAprendizaje($data);
        if ($grafica == "like_mensaje_comunidad")
            $result["likes_mensaje_comunidad"] = $this->reporte->obtener_likes_comunidades($data);
        if ($grafica == "usuarios_mas_activos_comunidad")
            $result["usuarios_mas_activos_comunidad"] = $this->reporte->ObtenerUsuariosMasActivosComunidad($data);
        if ($grafica == "dias_mas_activos_comunidad")
            $result["dias_mas_activos_comunidad"] = $this->reporte->ObtenerDiasMasActivosComunidad($data);
        if ($grafica == "reporte_wall")
            $result["reporte_wall"] = $this->reporte->ObtenerPostMasActivos($data);
        if ($grafica == "grafica_library")
            $result["grafica_library"] = $this->reporte->ObtenerGraficaLibrary($data);
        if ($grafica == "library_mas_valorados")
            $result["library_mas_valorados"] = $this->reporte->ObtenerGraficaLibraryValorado($data);
        if ($grafica == "calificacion_library")
            $result["calificacion_library"] = $this->reporte->GraficaCalificacionCuestionarioLibrary($data);
        if ($grafica == "cantidad_respuestas_library")
            $result["cantidad_respuestas_library"] = $this->reporte->ObtenerCantidadUsuariosRespuestaLibrary($data);
        if ($grafica == "usuarios_activos_library")
            $result["usuarios_activos_library"] = $this->reporte->ObtenerUsuariosMasActivosLibrary($data);
        if ($grafica == "tipo_mas_consumido_library")
            $result["tipo_mas_consumido_library"] = $this->reporte->ObtenerTipoMasConsumidoLibrary($data);
        if ($grafica == "dias_mas_consumo_library")
            $result["dias_mas_consumo_library"] = $this->reporte->diasMasConsumoLibrary($data);
        if ($grafica == "score_library")
            $result["score_library"] = $this->reporte->ObtenerReporteCalificacionesLibrary($data);
        if ($grafica == "grafica_sesiones_activas")
            $result["grafica_sesiones_activas"] = $this->reporte->ObtenerGraficaSesionesActivas($data);
        if ($grafica == "grafica_sesiones_usuario")
            $result["grafica_sesiones_usuario"] = $this->reporte->ObtenerGraficaSesionesPorUsuario($data);
        if ($grafica == "grafica_duracion_sesion")
            $result["grafica_duracion_sesion"] = $this->reporte->ObtenerGraficaDuracionSesion($data);
        if ($grafica == "grafica_ingresos_app_usuarios")
            $result["grafica_ingresos_app_usuarios"] = $this->reporte->ObtenerIngresosApp($data, false);
        if ($grafica == "grafica_ingresos_app")
            $result["grafica_ingresos_app"] = $this->reporte->ObtenerIngresosApp($data, true);
        if ($grafica == "grafica_interacciones_totales_usuarios")
            $result["grafica_interacciones_totales_usuarios"] = $this->reporte->obtenerInteraccionesTotalesPorUsuario($data);
        /** PARA LAS GRAFICA DE PODCAST */
        if ($grafica == "podcast_mas_consumido")
            $result["podcast_mas_consumido"] = $this->reporte->ObtenerGraficaPodcastMasConsumido($data);
        if ($grafica == "podcast_mejor_califacion")
            $result["podcast_mejor_califacion"] = $this->reporte->ObtenerGraficaPodcastMejorCalificacion($data);
        if ($grafica == "podcast_mas_comentarios")
            $result["podcast_mas_comentarios"] = $this->reporte->ObtenerGraficaPodcastMasComentarios($data);
        if ($grafica == "juegos_resultados")
            $result["juegos_resultados"] = $this->reporte->ObtenerGraficaJuegos($data);
        if ($grafica == "libary_shared")
            $result["library_shared"] = $this->reporte->obtener_grafica_compartidos_library($data);
        if ($grafica == "libary_shared_users")
            $result["library_shared_users"] = $this->reporte->obtener_grafica_compartidos_usuarios_library($data);
        if ($grafica == "podcast_shared")
            $result["podcast_shared"] = $this->reporte->obtener_grafica_compartidos_podcast($data);
        if ($grafica == "podcast_shared_users")
            $result["podcast_shared_users"] = $this->reporte->obtener_grafica_compartidos_usuarios_podcast($data);
        $this->general_mdl->writeLog("Consulta de informacion de reporte general usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Objeto general de reporte de resultados', $this);
    }

    public function obtener_grafica_al_like_caras()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->get_data($this->input->post());
        $data["id_pregunta"] = $this->input->post("id_pregunta");
        $data["business_id"] = $valida_token["business_id"];
        $result = $this->reporte->obtener_grafica_al_like_caras($data);
        $this->general_mdl->writeLog("Consulta de grafica de like caras ambiente laboral usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Objeto general de reporte de resultados', $this);
    }

    public function obtener_total_respuestas()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->get_data($this->input->post());
        $data["id_pregunta"] = $this->input->post("id_pregunta");
        $data["business_id"] = $valida_token["business_id"];
        $data["quiz_id"] = $this->input->post("quiz_id");
        $result = $this->reporte->obtener_total_respuestas($data);
        $this->general_mdl->writeLog("Consulta de total preguntas ambiente laboral usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Objeto general de reporte de resultados', $this);
    }

    public function ReporteDeResultados()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->get_data($this->input->post());
        $data["business_id"] = $valida_token["business_id"];
        $result = [];
        $result["juegos"] = $this->reporte->ObtenerJuegos($data);
        $result["reporte_ranking"] = $this->reporte->ObtenerRankingUsuarios($data);
        $result["reporte_juegos"] = $this->reporte->ObtenerReporteJuegos($data);
        $result["reporte_feedback"] = $this->reporte->ObtenerReporteFeedback($data);
        $result["reporte_ambiente_laboral_semana"] = $this->reporte->ObtenerPromedioDeAmbienteLaboral($data);
        $result["reporte_ambiente_laboral_mes"] = $this->reporte->ObtenerPromedioDeAmbienteLaboral($data);
        $this->general_mdl->writeLog("Consulta de informacion de reporte general usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Objeto general de reporte de resultados', $this);
    }

    public function GraficasCuestionarios()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->get_data($this->input->post());
        $data["business_id"] = $valida_token["business_id"];
        $result = [];
        $result["cuestionarios_mas_intentos"] = $this->reporte->GraficaCuestionariosMasIntentos($data);
        $result["calificacion_cuestionarios"] = $this->reporte->GraficaCalificacionCuestionarios($data);
        $result["cuestionarios_calificacion_final"] = $this->reporte->GraficaCuestionarioCalificacionFinalPorUsuario($data);
        $result["cuestionarios_dias_mas_consumo"] = $this->reporte->GraficaCuestionariosDiasMasConsumo($data);
        $result["participacion_quiz"] = $this->reporte->ObtenerReporteParticipacionEnCuestionarios($data);
        $result["cuestionarios"] = $this->reporte->ObtenerCuestionariosPorFecha($data);

        $this->general_mdl->writeLog("Consulta de informacion de reporte general usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Objeto general de reporte de resultados', $this);
    }

    public function GraficasCapacitacion()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->get_data($this->input->post());
        $data["business_id"] = $valida_token["business_id"];
        $result = [];
        $result["capacitaciones_terminadas"] = $this->reporte->obtenerGraficaCapacitacionesTerminadas($data);
        $result["capacitacion_mas_activa"] = $this->reporte->obtenerGraficaCapacitacionConsumo($data);
        $result["usuarios_activos_capacitacion"] = $this->reporte->ObtenerGraficaUsuariosActivosCapacitacion($data);
        $result["dias_consumo_capacitacion"] = $this->reporte->ObtenerGraficaDiasConsumoCapacitacion($data);

        $this->general_mdl->writeLog("Consulta de informacion de reporte general usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Objeto general de reporte de resultados', $this);
    }

    public function GraficasComunidad()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->get_data($this->input->post());
        $data["business_id"] = $valida_token["business_id"];
        $result = [];
        $result["reporte_comunidad"] = $this->reporte->ObtenerReporteComunidadDeAprendizaje($data);
        $result["likes_mensaje_comunidad"] = $this->reporte->obtener_likes_comunidades($data);
        $result["usuarios_mas_activos_comunidad"] = $this->reporte->ObtenerUsuariosMasActivosComunidad($data);
        $result["dias_mas_activos_comunidad"] = $this->reporte->ObtenerDiasMasActivosComunidad($data);

        $this->general_mdl->writeLog("Consulta de informacion de reporte general usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Objeto general de reporte de resultados', $this);
    }

    public function GraficasMuro()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->get_data($this->input->post());
        $data["business_id"] = $valida_token["business_id"];
        $result = [];
        $result["reporte_wall"] = [];
        // $result["reporte_wall"]["comentarios"] = $this->reporte->ObtenerReporteInteraccionesComentariosWall($data);
        // $result["reporte_wall"]["likes"] = $this->reporte->ObtenerReporteInteraccionesLikeWall($data);
        $result["reporte_wall"] = $this->reporte->ObtenerPostMasActivos($data);
        $result["usuarios_mas_likes_muro"] = $this->reporte->ObtenerUsuariosMasLikesMuro($data);

        $this->general_mdl->writeLog("Consulta de informacion de reporte general usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Objeto general de reporte de resultados', $this);
    }

    public function GraficasBiblioteca()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->get_data($this->input->post());
        $data["business_id"] = $valida_token["business_id"];
        $result = [];
        $result["grafica_library"] = $this->reporte->ObtenerGraficaLibrary($data);
        $result["library_mas_valorados"] = $this->reporte->ObtenerGraficaLibraryValorado($data);
        $result["calificacion_library"] = $this->reporte->GraficaCalificacionCuestionarioLibrary($data);
        $result["cantidad_respuestas_library"] = $this->reporte->ObtenerCantidadUsuariosRespuestaLibrary($data);
        $result["usuarios_activos_library"] = $this->reporte->ObtenerUsuariosMasActivosLibrary($data);
        $result["tipo_mas_consumido_library"] = $this->reporte->ObtenerTipoMasConsumidoLibrary($data);
        $result["dias_mas_consumo_library"] = $this->reporte->diasMasConsumoLibrary($data);
        $result["score_library"] = $this->reporte->ObtenerReporteCalificacionesLibrary($data);
        $result["library_shared"] = $this->reporte->obtener_grafica_compartidos_library($data);
        $result["library_shared_users"] = $this->reporte->obtener_graficas_compartidos_usuarios_library($data);
        $this->general_mdl->writeLog("Consulta de informacion de reporte general usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Objeto general de reporte de resultados', $this);
    }

    /** 
     * SE AGREGA NUEVA FUNCION PARA REALIZAR LA GRAFICACION DE LA INFORMACION DE PODCAST
     */

    public function GraficasPodcast()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->get_data($this->input->post());
        $data["business_id"] = $valida_token["business_id"];
        $result = [];
        $result["podcast_mas_consumido"] = $this->reporte->ObtenerGraficaPodcastMasConsumido($data);
        $result["podcast_mejor_califacion"] = $this->reporte->ObtenerGraficaPodcastMejorCalificacion($data);
        $result["podcast_mas_comentarios"] = $this->reporte->ObtenerGraficaPodcastMasComentarios($data);
        $result["podcast_shared"] = $this->reporte->obtener_grafica_compartidos_podcast($data);
        $result["podcast_shared_users"] = $this->reporte->obtener_grafica_compartidos_usuarios_podcast($data);
        $this->general_mdl->writeLog("Consulta de informacion de reporte Podcast " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Objeto general Consulta para Podcast', $this);
    }

    public function GraficasAJugar()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->get_data($this->input->post());
        $data["business_id"] = $valida_token["business_id"];
        $result = [];
        $result["juegos_grafica"] = $this->reporte->ObtenerGraficaJuegos($data);
        $result["juegos_calificacion_ruleta"] = $this->reporte->ObtenerGraficaJuegosCalificacionesRuleta($data);
        $result["juegos_calificacion_run_pancho"] = $this->reporte->ObtenerGraficaJuegosCalificacionesRunPancho($data);
        // $result["juegos_calificacion_ahorcado"] = $this->reporte->ObtenerGraficaJuegosCalificacionesAhorcado($data);
        $result["juegos_calificacion_culebra"] = $this->reporte->ObtenerGraficaJuegosCalificacionesculebra($data);
        $this->general_mdl->writeLog("Consulta de informacion de reporte Juegos " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Objeto general Consulta para Juegos', $this);
    }

    public function GraficasUsuarios()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->get_data($this->input->post());
        $data["business_id"] = $valida_token["business_id"];
        $result = [];

        $result["grafica_sesiones_activas"] = $this->reporte->ObtenerGraficaSesionesActivas($data);
        $result["grafica_sesiones_usuario"] = $this->reporte->ObtenerGraficaSesionesPorUsuario($data);
        $result["grafica_duracion_sesion"] = $this->reporte->ObtenerGraficaDuracionSesion($data);
        $result["grafica_ingresos_app_usuarios"] = $this->reporte->ObtenerIngresosApp($data, false);
        $result["grafica_ingresos_app"] = $this->reporte->ObtenerIngresosApp($data, true);
        $result["grafica_interacciones_totales_usuarios"] = $this->reporte->obtenerInteraccionesTotalesPorUsuario($data);
        $result["grafica_interacciones_totales"] = $this->reporte->obtenerInteraccionesTotales($data);

        $this->general_mdl->writeLog("Consulta de informacion de reporte general usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Objeto general de reporte de resultados', $this);
    }

    public function DatosUsuarios()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->get_data($this->input->post());
        $data["business_id"] = $valida_token["business_id"];
        $result = [];
        $result["total_usuarios"] = $this->reporte->ObtenerUsuariosNumUsuariosTotales($data);
        $result["total_usuarios_pre"] = $this->reporte->ObtenerUsuariosNumUsuariosTotalesPre($data);
        $result["usuarios_nuevos"] = $this->reporte->ObtenerNumUsuariosNuevos($data);
        $result["usuarios_regreso"] = $this->reporte->ObtenerNumVisitantesRegreso($data);
        $result["sesiones_activas"]  = $this->reporte->ObtenerNumSesionesActivas($data);
        $result["promedio_sesiones"] = $this->reporte->ObtenerNumSesionesPorUsuario($data);
        $result["duracion_sesion"] = $this->reporte->ObtenerDuracionSesion($data);
        $result["usuarios_registrados_semana_normal"] = $this->reporte->ObtenerUsuariosRegistradosSemanal($data, true, true);
        $result["usuarios_registrados_semana_asesor"] = $this->reporte->ObtenerUsuariosRegistradosSemanal($data, false, true);
        $result["usuarios_registrados_mes_normal"] = $this->reporte->ObtenerUsuariosRegistradosSemanal($data, true, false);
        $result["usuarios_registrados_mes_asesor"] = $this->reporte->ObtenerUsuariosRegistradosSemanal($data, false, false);
        $result["usuarios_activos_normal"] = $this->reporte->obtenerUsuariosActivos($data, true);
        $result["usuarios_activos_asesor"] = $this->reporte->obtenerUsuariosActivos($data, false);
        $this->general_mdl->writeLog("Consulta de informacion de reporte general usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Objeto general de reporte de resultados', $this);
    }

    public function DescargarReportePodcastConsumido($fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $this->general_mdl->writeLog("Descarga reporte de podcast mas comentado " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->ObtenerPodcastMasConsumido($token, $data);
    }

    public function DescargarReportePodcastCalificado($fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $this->general_mdl->writeLog("Descarga reporte de podcast mas comentado " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->ObtenerPodcastMasCalificado($token, $data);
    }

    public function DescargarReportePodcastComentados($fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $this->general_mdl->writeLog("Descarga reporte de podcast mas comentado " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->ObtenerPodcastMasComentado($token, $data);
    }

    public function DescargarReporteRanking($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga reporte de ranking usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->ObtenerRankingUsuariosQuery($token, $data);
    }

    public function DescargarReporteResultadosNormalSemana($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga reporte registrados normal semana usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->DescargarUsuariosRegistradosSemanal($data, true, true);
    }

    public function DescargarReporteResultadosAsesorSemana($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga reporte registrados asesor semana usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->DescargarUsuariosRegistradosSemanal($data, false, true);
    }

    public function DescargarReporteResultadosNormalMes($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga reporte registrados normal mes usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->DescargarUsuariosRegistradosSemanal($data, true, false);
    }

    public function DescargarReporteResultadosAsesorMes($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga reporte registrados asesor mes asesor usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->DescargarUsuariosRegistradosSemanal($data, false, false);
    }

    public function DescargarReporteInteracionesNormal($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga reporte interacciones asesor usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->descargarReporteInteracciones($data, true);
    }

    public function DescargarReporteInteracionesAsesor($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga reporte interacciones normal usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->descargarReporteInteracciones($data, false);
    }

    public function DescargarRerporteBibliotecaScorePromedio($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga reporte biblioteca score promedio usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->DescargarReporteCSVScoreBibliotecaPromedios($data, false);
    }

    public function DescargarRerporteBibliotecaScore($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga reporte biblioteca score usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->DescargarReporteCSVScoreBiblioteca($data, false);
    }

    public function DescargarReporteCapacitacionObligatoria($nombre_usuario = null, $id_capacitacion = null, $fecha_inicio = null, $fecha_fin = null)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $data["id_capacitacion"] = $id_capacitacion;
        $this->general_mdl->writeLog("Descarga de reporte de capacitacion obligatoria usuario " . $valida_token["user_id"], "<info>");
        // echo json_encode($data);
        // $result = $this->reporte->obtener_reporte_capacitacion_obligatoria($token, $data);
        // $nombre_quiz = $this->questions->obtenerNombreQuiz($quiz_id);
        // header("Content-Disposition: attachment; filename=\"" . $nombre_quiz . date('y-m-d') . ".csv\"");

        // header("Pragma: no-cache");
        // header("Expires: 0");

        require_once APPPATH . 'libraries/SimpleXLSXGen.php';

        $handle = fopen('php://output', 'w');

        $result = $this->reporte->obtener_reporte_capacitacion_obligatoria($token, $data);
        $xlsx = new SimpleXLSXGen();
        $xlsx->addSheet($result, 'Reporte_Capacitacion_PLD');
        // $xlsx->saveAs('upload/Correcciones.xlsx');
        $xlsx->downloadAs("Reporte_Capacitacion_PLD" . date('y-m-d') . ".xlsx");
        exit;
    }

    public function DescargarReporteBiblioteca($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga reporte de biblioteca usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->descargarReporteBiblioteca($token, $data);
    }

    public function DescargarReporteCapacitacion($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga de reporte de capacitacion usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->DescargarReporteCsvCapacitacion($token, $data);
    }

    public function DescargarReportePodcast($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga de reporte de podcast usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->ObtenerPodcastUsuariosQuery($token, $data);
    }

    public function DescargarReporteComunidad($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        // echo json_encode($this->input->post("token"));
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga de reporte de comunidad usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->descargarReporteComunidadAprendizaje($token, $data);
    }

    public function DescargarReporteLikeMensajeComunidad($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        // echo json_encode($this->input->post("token"));
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga de reporte de like en comunidad usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->obtener_reporte_likes_mensajes_comunidad($token, $data);
    }

    public function DescargarReporteMuro($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        // echo json_encode($this->input->post("token"));
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga de reporte en muro usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->descargarReporteMuro($token, $data);
    }

    public function DescargarReporteCuestionarios($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga de reporte de cuestionarios usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->descargarReporteCuestionarios($token, $data);
    }

    public function DescargarReporteCuestionariosPreguntas($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga de reporte de cuestionarios preguntas usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->descargarReporteCuestionariosPreguntas($token, $data);
    }

    public function DescargarReporteCuestionariosAmbiente($nombre_usuario, $fecha_inicio, $fecha_fin, $quiz_id, $id_question = null)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $data["quiz_id"] = $quiz_id;
        $data["id_question"] = $id_question;
        $this->general_mdl->writeLog("Descarga de reporte de cuestionario ambiente laboral usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->descargarReporteCuestionarioAmbiente($token, $data);
    }

    public function DescargarReportePodcastCompartidos($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga de reporte de podcast compartidos usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->obtener_csv_podcast_compartidos_usuarios($token, $data);
    }


    public function DescargarReporteLibraryCompartidos($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga de reporte de biblioteca compartidos usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->obtener_csv_library_compartidos_usuarios($token, $data);
    }

    /**
     * 
     */
    public function DescargarReporteCuestionariosPorUsuarios($id, $usuarios)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["id"] = $id;
        $usuarios = str_replace("-", ",", $usuarios);
        $data["usuarios"] = $usuarios;
        $this->general_mdl->writeLog("Descarga de reporte de cuestionarios respondidos por usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->descargarReporteCuestionariosPorUsuarios($data);
    }

    public function DescargarReporteValoradosLibrary($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga de reporte de valorados library usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->DescargarReporteValoradosLibrary($token, $data);
    }

    public function DescargarReporteTiposConsumidoLibrary($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga de reporte de tipos mas consumidos library usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->DescargarReporteTiposConsumidoLibrary($token, $data);
    }

    public function DescargarReporteMasActivosLibrary($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga de reporte de usuarios activos library usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->DescargarReporteMasActivosLibrary($token, $data);
    }

    public function DescargarReporteDiasMasActividad($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga de reporte de dias de mas actividad library usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->DescargarReporteDiasMasActividad($token, $data);
    }

    public function DescargarReporteCalificacionesElementosLibrary($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $this->general_mdl->writeLog("Descarga de reporte de calificaciones de elementos library usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->DescargarReporteCalificacionesElementosLibrary($token, $data);
    }

    public function DescargarReporteJuegos($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $data["limite"] = false;
        $this->general_mdl->writeLog("Descarga de reporte de juegos usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->ObtenerJuegosUsuariosQuery($token, $data);
    }

    public function DescargarReporteResultadosRuleta($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $data["limite"] = false;
        $this->general_mdl->writeLog("Descarga de reporte de juegos usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->DescargarReporteResultadosRuleta($token, $data);
    }

    public function DescargarReporteResultadosRunPancho($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $data["limite"] = false;
        $this->general_mdl->writeLog("Descarga de reporte de juegos usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->DescargarReporteResultadosRunPancho($token, $data);
    }

    public function DescargarReporteResultadosCulebra($nombre_usuario, $fecha_inicio, $fecha_fin)
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $fecha_inicio;
        $data["fecha_fin"] = $fecha_fin;
        $data["nombre_usuario"] = $nombre_usuario;
        $data["limite"] = false;
        $this->general_mdl->writeLog("Descarga de reporte de juegos usuario " . $valida_token["user_id"], "<info>");
        $result = $this->reporte->DescargarReporteResultadosCulebra($token, $data);
    }

    public function ReporteInteraccionWall()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $result = [];
        $result["comentarios"] = $this->reporte->ObtenerReporteInteraccionesComentariosWall($data);
        $result["likes"] = $this->reporte->ObtenerReporteInteraccionesLikeWall($data);

        successResponse($result, 'reporte de interacciones en wall (comentarios y likes)', $this);
    }

    public function ReporteAmbienteLaboral()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $data["fecha_inicio"] = $this->input->post("fecha_inicio");
        $data["fecha_fin"] = $this->input->post("fecha_fin");
        $data["nombre_usuario"] = $this->input->post("nombre_usuario");
        $data["group_id"] = $this->input->post("group_id");
        $result = $this->reporte->ObtenerPromedioDeAmbienteLaboral($data);
        successResponse($result, 'Reporte de ambiente laboral', $this);
    }

    public function ObtenerReporteUsoPodcast()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = [];
        $data["business_id"] = $valida_token['business_id'];
        $data["fecha_inicio"] = $this->input->get("fecha_inicio");
        $data["fecha_fin"] = $this->input->get("fecha_fin");
        $data["nombre_usuario"] = $this->input->get("nombre_usuario");
        $result["uso"] = $this->reporte->ObtenerReporteDeUso($data);
        $result["comentarios"] = $this->reporte->ObtenerReporteInteraccionesComentariosPodcast($data);
        $result["calificaciones"] = $this->reporte->ObtenerReporteInteraccionesCalificacionesPodcast($data);
        successResponse($result, 'Reporte de uso e interacciones de recursos de podcast', $this);
    }

    public function ReporteParticipacionQuiz()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = [];
        $data["business_id"] = $valida_token["business_id"];
        $result = $this->reporte->ObtenerReporteParticipacionEnCuestionarios($data);
        successResponse($result, 'Reporte de participacion en cuestionarios', $this);
    }

    public function ReporteRanking()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->input->post();
        $data = $valida_token['business_id'];
        $result = $this->reporte->ObtenerRankingUsuarios($data);
        successResponse($result, 'Reporte de ranking de usuarios', $this);
    }

    public function ReporteJuegos()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->input->post();
        $data["business_id"] = $valida_token['business_id'];
        $result = $this->reporte->ObtenerReporteJuegos($data);
        successResponse($result, 'Reporte de participacion en juegos', $this);
    }

    public function ReporteComunidad()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->input->post();
        $data["business_id"] = $valida_token['business_id'];
        $result = $this->reporte->ObtenerReporteComunidadDeAprendizaje($data);
        successResponse($result, 'Reporte de participacion en comunidad de aprendizaje', $this);
    }

    public function ReporteFeedback()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = [];
        $data["business_id"] = $valida_token['business_id'];
        $result = $this->reporte->ObtenerReporteFeedback($data);
        successResponse($result, 'Reporte  de usuarios que hacen retroalimentacion', $this);
    }

    public function ObtenerReporteUsoLibrary()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = [];
        $data["business_id"] = $valida_token['business_id'];
        $data["fecha_inicio"] = $this->input->get("fecha_inicio");
        $data["fecha_fin"] = $this->input->get("fecha_fin");
        $data["nombre_usuario"] = $this->input->get("nombre_usuario");
        $data["id_grupo"] = $this->input->get("id_grupo");
        $result = $this->reporte->ObtenerReporteDeUsoLibrary($data);
        successResponse($result, 'Reporte de uso de recursos de biblioteca', $this);
    }

    public function obtener_reporte_firebase()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->get_data($this->input->post());
        $obj = [];
        $result = array();
        /**
         * TODO(developer): Replace this variable with your Google Analytics 4
         *   property ID before running the sample.
         */
        // putenv("GOOGLE_APPLICATION_CREDENTIALS=C:\Users\EQUIPO\Downloads\appy-45add-cadd9d61a1b5.json");
        putenv("GOOGLE_APPLICATION_CREDENTIALS=" . __DIR__ . "\appy-45add-cadd9d61a1b5.json");
        // putenv("GOOGLE_APPLICATION_CREDENTIALS=/home/yastasapp/public_html/nuup/application/controllers/appy-45add-cadd9d61a1b5.json");


        $property_id = '286833461';




        // Using a default constructor instructs the client to use the credentials
        // specified in GOOGLE_APPLICATION_CREDENTIALS environment variable.
        $client = new BetaAnalyticsDataClient();

        // $client->useApplicationDefaultCredentials();

        // Make an API call.
        $response = $client->runReport([
            'property' => 'properties/' . $property_id,
            'dateRanges' => [
                new DateRange([
                    'start_date' => $data['fecha_inicio'],
                    'end_date' => $data['fecha_fin'],
                ]),
            ],
            'dimensions' => [
                new Dimension(
                    [
                        'name' => 'date',
                    ]
                ),
            ],
            'metrics' => [
                new Metric(
                    [
                        'name' => 'averageSessionDuration',
                    ]
                )
            ]
        ]);

        // Print results of an API call.
        // print 'Report result: ' . PHP_EOL;


        foreach ($response->getRows() as $row) {
            $obj['dia'] = $row->getDimensionValues()[0]->getValue();
            $obj['interacciones'] = number_format(($row->getMetricValues()[0]->getValue() / 60), 2, '.', '');
            array_push($result, $obj);
            // print $row->getDimensionValues()[0]->getValue()
            //     . ' ' . $row->getMetricValues()[0]->getValue() . PHP_EOL;
        }
        successResponse($result, 'REPORTE DE INTERACCION DE USUARIOS DESDE FIREBASE', $this);
        // return $response;
    }

    public function verRutasRelativas()
    {
        echo "GOOGLE_APPLICATION_CREDENTIALS=" . __DIR__ . "\appy-45add-cadd9d61a1b5.json";
    }
}
