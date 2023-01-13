<?php
class Retos extends CI_Controller
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
        $this->load->model('retos_mdl', 'retos');
        $this->load->model('notification_mdl', 'notification');
        $this->load->model('user_model', 'user');
    }

    // public function CrearReto()
    // {
    //     $token = $this->input->post("token");
    //     $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
    //     if (!$valida_token) {
    //         faildResponse($this->lang->line('token_error_msg'), $this);
    //         return;
    //     }
    //     $validaPost = $this->general_mdl->validapost(array('nombre', 'objetivo', 'colaboradores', 'descripcion'), $this->input->post());
    //     if (!$validaPost['success']) {
    //         faildResponse($validaPost['msg'], $this);
    //         return;
    //     }
    //     $config['upload_path'] = './uploads/business_' . $valida_token["business_id"] . '/games/retos/';
    //     $config['allowed_types'] = 'gif|jpg|png|jpeg';

    //     $this->load->library('upload', $config);

    //     $data = array(
    //         'nombre' => $this->input->post("nombre"),
    //         "user_id" => $valida_token["user_id"],
    //         'objetivo' => $this->input->post("objetivo"),
    //         'descripcion' => $this->input->post("descripcion"),
    //         'aprobado' => 1,
    //         'tipo' => 1
    //     );
    //     $id_reto = $this->retos->CrearReto($data);

    //     $data = [];
    //     $data["colaboradores"] = $this->input->post("colaboradores");
    //     if (is_string($data["colaboradores"])) {
    //         $data["colaboradores"] = json_decode($data["colaboradores"]);
    //     }
    //     $data["id_reto"] = $id_reto;
    //     $data["id_retador"] = $valida_token["user_id"];

    //     $result = $this->retos->Retar($data);

    //     if ($result) {
    //         $this->general_mdl->writeLog("Registro de nuevo reto usuario " . $valida_token["user_id"], "<info>");
    //         successResponse($id_reto, 'El reto se ha creado correctamente.', $this);
    //     } else {
    //         $this->general_mdl->writeLog("Error al crear nuevo reto usuario " . $valida_token["user_id"], "<warning>");
    //         faildResponse('Error al guardar el reto.', $this);
    //     }
    // }

    public function guardarImagenReto()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $config['upload_path'] = './uploads/business_' . $valida_token["business_id"] . '/games/retos/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $this->load->library('upload', $config);
        $result = null;
        if (!$this->upload->do_upload('imagen')) {
            $error = array('error' => $this->upload->display_errors());
            faildResponse($error, $this);
        } else {
            $data = $this->upload->data();
            $imagen = $data['raw_name'] . $data['file_ext'];
            $id_reto = $this->input->post("id_reto");
            $result = $this->retos->GuardarImagenReto($id_reto, $imagen);
        }
        if ($result) {
            $this->general_mdl->writeLog("Registro de nueva imagen reto usuario " . $valida_token["user_id"], "<info>");
            successResponse('', 'Imagen agregada correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar imagen reto usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al guardar la imagen.', $this);
        }
    }

    public function guardarImagenReporte()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $config['upload_path'] = './uploads/business_' . $valida_token["business_id"] . '/games/retos/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|mp4';
        // echo json_encode($config["upload_path"]);
        $this->load->library('upload', $config);
        $result = null;
        if (!$this->upload->do_upload('imagen')) {
            $error = array('error' => $this->upload->display_errors());
            faildResponse($error, $this);
            return;
        } else {
            $data = $this->upload->data();
            $imagen = $data['raw_name'] . $data['file_ext'];
            $video = "";
            $id_reporte = $this->input->post("id_reporte");
            if ($data['file_ext'] == ".mp4") {
                $video = $imagen;
                $imagen = "";
            }
            $result = $this->retos->GuardarImagenReporte($id_reporte, $imagen, $video);
        }
        if ($result) {
            $this->general_mdl->writeLog("Registro de nueva imagen reporte de reto usuario " . $valida_token["user_id"], "<info>");
            successResponse('', 'Imagen agregada correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar imagen reporte de reto usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al guardar la imagen.', $this);
        }
    }

    // public function CrearRetoEmpresa()
    // {
    //     $token = $this->input->post("token");
    //     $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
    //     if (!$valida_token) {
    //         faildResponse($this->lang->line('token_error_msg'), $this);
    //         return;
    //     }
    //     $validaPost = $this->general_mdl->validapost(array('nombre', 'objetivo', 'colaboradores', 'descripcion'), $this->input->post());
    //     if (!$validaPost['success']) {
    //         faildResponse($validaPost['msg'], $this);
    //         return;
    //     }
    //     $config['upload_path'] = './uploads/business_' . $valida_token["business_id"] . '/games/retos/';
    //     $config['allowed_types'] = 'gif|jpg|png|jpeg';

    //     $this->load->library('upload', $config);

    //     if (!$this->upload->do_upload('imagen')) {
    //         $error = array('error' => $this->upload->display_errors());
    //         faildResponse($error, $this);
    //     } else {
    //         $data = $this->upload->data();
    //         $imagen = $data['raw_name'] . $data['file_ext'];
    //         $data = array(
    //             'nombre' => $this->input->post("nombre"),
    //             "user_id" => $valida_token["user_id"],
    //             'objetivo' => $this->input->post("objetivo"),
    //             'descripcion' => $this->input->post("descripcion"),
    //             'imagen' => $imagen,
    //             'aprobado' => 0,
    //             'tipo' => 1
    //         );
    //         $result = $this->retos->CrearReto($data);
    //         $data = [];
    //         $data["colaboradores"] = $this->input->post("colaboradores");
    //         $data["id_reto"] = $result;
    //         $data["id_retador"] = $valida_token["user_id"];
    //         $result = $this->retos->Retar($data);
    //         if ($result) {
    //             $this->general_mdl->writeLog("Registro de nuevo reto empresa usuario " . $valida_token["user_id"], "<info>");
    //             successResponse('', 'El reto se ha creado correctamente.', $this);
    //         } else {
    //             $this->general_mdl->writeLog("Error al crear nuevo reto empresa usuario " . $valida_token["user_id"], "<warning>");
    //             faildResponse('Error al guardar el reto.', $this);
    //         }
    //     }
    // }

    public function ObtenerRetos()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $retos = [];
        $retos["retos"] = $this->retos->ObtenerRetos($valida_token["user_id"]);
        $retos["pendientes"] = $this->retos->obtener_pendientes_cant($valida_token["user_id"]);
        $retos["realizados"] = $this->retos->obtener_realizados_cant($valida_token["user_id"]);
        if ($retos) {
            $this->general_mdl->writeLog("Listado de retos usuario " . $valida_token["user_id"], "<info>");
            successResponse($retos, 'Listado de retos', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener retos usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Sin retos', $this);
        }
    }

    /*******************************************************************************************
     * SE AGREGA FUNCION PARA OBTENER LOS RETOS Y MOSTRARLOS EN EL AREA DE RESULTADOS DEL JUEGO*
     * SE LLENA LA TABLA PARA PODER EJECUTAR ALGUNAS ACCIIONES AGREGAR, EDITAR ELIMINAR        *
     ********************************************************************************************/
    public function ObtenerRetosBiblioteca()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $retos = $this->retos->ObtenerRetosBiblioteca($valida_token["business_id"]);
        if ($retos) {
            $this->general_mdl->writeLog("Listado de retos para agregar a la biblioteca " . $valida_token["user_id"], "<info>");
            successResponse($retos, 'Listado de retos', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener retos agregar a la biblioteca " . $valida_token["user_id"], "<warning>");
            faildResponse('Sin retos', $this);
        }
    }

    public function obtener_retos_lanzados()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $retos = $this->retos->obtener_retos_lanzados($valida_token["user_id"]);
        if ($retos) {
            $this->general_mdl->writeLog("Listado de retos usuario " . $valida_token["user_id"], "<info>");
            successResponse($retos, 'Listado de retos', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener retos usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Sin retos', $this);
        }
    }

    public function obtenerRetosAdmin()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $business_id = $valida_token["business_id"];
        $retos = $this->retos->obtenerRetosAdmin($business_id);
        if ($retos) {
            $this->general_mdl->writeLog("Listado de retos admin usuario " . $valida_token["user_id"], "<info>");
            successResponse($retos, 'Listado de retos', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener retos admin usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Sin retos', $this);
        }
    }

    public function ObtenerRetosCalificar()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('tipo'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $tipo = $this->input->post("tipo");
        $business_id = $valida_token["business_id"];
        $retos = $this->retos->obtenerRetosCalificar($valida_token["user_id"], $tipo, $business_id);
        if ($retos) {
            $this->general_mdl->writeLog("Listado de retos para calificar usuario " . $valida_token["user_id"], "<info>");
            successResponse($retos, 'Listado de retos para calificar', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener retos para calificar usuario " . $valida_token["user_id"], "<info>");
            faildResponse('Sin retos', $this);
        }
    }

    public function ObtenerRetosRealizados()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('tipo'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $tipo = $this->input->post("tipo");
        $business_id = $valida_token["business_id"];
        $id_usuario = $valida_token["user_id"];
        $retos = $this->retos->obtenerRetosRealizados($id_usuario, $tipo, $business_id);
        if ($retos) {
            $this->general_mdl->writeLog("Listado de retos realizados por el usuario " . $valida_token["user_id"], "<info>");
            successResponse($retos, 'Listado de retos realizados por el usuario', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener retos realizados del usuario " . $valida_token["user_id"], "<info>");
            faildResponse('Sin retos', $this);
        }
    }

    public function ObtenerReto()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id_reto'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $id_reto_lanzado = $this->input->post("id_reto"); //debe ser el id de reto lanzado
        $business_id = $valida_token["business_id"];
        $user_id = $valida_token["user_id"];
        $retos = $this->retos->ObtenerReto($id_reto_lanzado, $business_id, $user_id);
        if ($retos) {
            $this->general_mdl->writeLog("Consulta de reto por id usuario " . $valida_token["user_id"] . " reto " . $id_reto_lanzado, "<info>");
            successResponse($retos, 'Detalle del reto', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener reto usuario " . $valida_token["user_id"] . " reto " . $id_reto_lanzado, "<warning>");
            faildResponse('Error al obtener la informacion', $this);
        }
    }

    public function CalificarReto()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id_reto', 'id_usuario', 'desempeno', 'actitud'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $id_reto = $this->input->post("id_reto");
        $id_usuario = $this->input->post("id_usuario");
        $desempeno = $this->input->post("desempeno");
        $actitud = $this->input->post("actitud");
        $desempeno2 = $this->input->post("desempeno2");
        $actitud2 = $this->input->post("actitud2");
        $desempeno3 = $this->input->post("desempeno3");
        $actitud3 = $this->input->post("actitud3");
        $desempeno4 = $this->input->post("desempeno4");
        $actitud4 = $this->input->post("actitud4");
        $retos = $this->retos->calificarReto($id_reto, $desempeno, $actitud, $desempeno2, $actitud2, $desempeno3, $actitud3, $desempeno4, $actitud4, $id_usuario);
        if ($retos) {
            $this->general_mdl->writeLog("Calificacion a reto usuario " . $valida_token["user_id"] . " reto " . $id_reto, "<info>");
            successResponse($retos, 'Reto calificado correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al calificar reto usuario " . $valida_token["user_id"] . " reto " . $id_reto, "<warning>");
            faildResponse('Error al calificar reto', $this);
        }
    }

    public function AgregarReporte()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id_reto', 'conclusion'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $imagen = "";
        $id_reto = $this->input->post("id_reto");
        $conclusion = $this->input->post("conclusion");
        $id_usuario = $valida_token["user_id"];
        $retos = $this->retos->agregarReporte($id_reto, $conclusion, $imagen, $id_usuario);
        if ($retos) {
            $this->general_mdl->writeLog("Registro de reporte reto usuario " . $valida_token["user_id"] . " reto " . $id_reto, "<info>");
            successResponse($retos, 'Reporte guardado con exito', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar reporte reto usuario " . $valida_token["user_id"] . " reto " . $id_reto, "<warning>");
            faildResponse('Error al guardar el reporte', $this);
        }
        // }
    }

    public function Retar()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('retados', 'id_reto', 'fecha_limite'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $data = array(
            'id_retador' => $valida_token["user_id"],
            "retados" => $this->input->post("retados"),
            'id_reto' => $this->input->post("id_reto"),
            'fecha_limite' => $this->input->post("fecha_limite")
        );
        array_push($data["retados"], $valida_token["user_id"]);
        $result = $this->retos->Retar($data);
        if ($result) {
            $preguntas = $this->retos->obtener_preguntas($data["id_reto"]);
            $this->enviar_notificaciones($valida_token, $data, $result);
            $this->crearCron($data["id_reto"], $valida_token["name_complete"], $valida_token["user_id"], $data["fecha_limite"]);
            $this->crearCronRepartirPuntos($data["id_reto"], $valida_token["name_complete"], $valida_token["user_id"], $data["fecha_limite"]);
            $this->general_mdl->writeLog("Registro de reto a usuarios usuario " . $valida_token["user_id"] . " reto " . $data["id_reto"], "<info>");
            successResponse(array("id_reto_lanzado" => $result[count($result) - 1], "preguntas" => $preguntas), 'Se creó el Reto correctamente.', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar usuarios retados usuario " . $valida_token["user_id"] . " reto " . $data["id_reto"], "<warning>");
            faildResponse('Error al guardar retados.', $this);
        }
    }

    public function crearCron($id_reto, $nombre_usuario, $id_own, $fecha)
    {
        // $retados = [$retados,$retados,$retados];

        $mifecha = date("Y-m-d H:i:s", strtotime($fecha . " 14:00:00"));

        //  $NuevaFecha = strtotime('+21 hour', strtotime($mifecha));
        // $NuevaFecha = strtotime('-30 minute', $NuevaFecha);
        // $NuevaFecha = strtotime ( '-2 hour' , strtotime($mifecha) ) ;
        $NuevaFecha = strtotime($mifecha . '- 1 days');
        $minutos = intval(date('i', $NuevaFecha));
        $hora = date("G", $NuevaFecha);
        $dia = date("j", $NuevaFecha);
        $mes = date("n", $NuevaFecha);
        $command = $minutos . ' ' . $hora . ' ' . $dia . ' ' . $mes . ' * wget https://kreativeco.com/nuup/Notification/enviarNotificacionRetoTiempo?id_usuario=' . $id_reto . '-' . $nombre_usuario . '-' . $id_own;
        exec('echo -e "`crontab -l`\n' . $command . '" | crontab -', $output);
        successResponse([], 'Envio de notificacion de reto', $this);
    }

    public function crearCronRepartirPuntos($id_reto, $nombre_usuario, $id_own, $fecha)
    {

        $mifecha = date("Y-m-d H:i:s", strtotime($fecha . " 00:00:00"));
        $mifecha = strtotime($mifecha . '+ 1 days');
        $minutos = intval(date('i', strtotime($mifecha)));
        $hora = date("G", strtotime($mifecha));
        $dia = date("j", strtotime($mifecha));
        $mes = date("n", strtotime($mifecha));
        $command = $minutos . ' ' . $hora . ' ' . $dia . ' ' . $mes . ' * wget https://kreativeco.com/nuup/Retos/agregar_puntos/' . $id_reto;
        exec('echo -e "`crontab -l`\n' . $command . '" | crontab -', $output);
        successResponse([], 'Envio de notificacion de reto', $this);
    }

    function enviar_notificaciones($valida_token, $data, $ids)
    {
        $tokens = $this->notification->obtenerTokensRuletaRetos($data["retados"]);
        if ($tokens != "") {
            $tokens_ = array();
            $nombre_usuario = $valida_token["name_complete"];
            foreach ($tokens as $index => $value) {
                array_push($tokens_, $value['token']);

                $data_ = array('title' => 'Retos', 'notification' => 'Tienes un nuevo reto de ' . $nombre_usuario, 'user_id' => $value['user_id'], 'service_id' => SERVICE_RETOS, 'user_create_id' => $valida_token['user_id'], "id_topic" => $ids[$index]);
                $this->notification->RegisterNotification($data_);
            }
            $this->general_mdl->EnviarNotificacionPush($tokens_, 'Tienes un nuevo reto de ' . $nombre_usuario, 'Retos', SERVICE_RETOS, true, array("id_topic" => $ids[$index]));
            // echo json_encode("se debio enviar la notificacion por char");
        }
    }

    public function eliminarReto()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_reto = $this->input->post("id_reto");
        $result = $this->retos->eliminarReto($id_reto);
        if ($result) {
            $this->general_mdl->writeLog("Eliminacion de reto usuario " . $valida_token["user_id"], "<info>");
            successResponse('', 'Eliminado correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al eliminar retos.', $this);
        }
    }

    public function actualizarReto()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $data = $this->input->post();
        $result = $this->retos->actualizarReto($data);
        if ($result) {
            $this->general_mdl->writeLog("Actualizacion de reto usuario " . $valida_token["user_id"], "<info>");
            successResponse('', 'Actualizado correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al actualizar retos.', $this);
        }
    }

    public function guardar_respuesta()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('question_id', "id_reto_lanzado"), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        $data = $this->input->post();
        $data['user_id'] = $valida_token['user_id'];

        $config['upload_path'] = './uploads/business_' . $valida_token["business_id"] . '/games/retos/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';

        $this->load->library('upload', $config);
        $tipo = 0;
        if (!$this->upload->do_upload('imagen')) {
            // $error = array('error' => $this->upload->display_errors());
            // faildResponse($error, $this);
            // return;
        } else {
            $data1 = $this->upload->data();
            $imagen = $data1['raw_name'] . $data1['file_ext'];
            // echo json_encode($data1);
            $data["answer_id"] = $imagen;
            $tipo = 11;
        }

        $result = $this->retos->SaveAnswer($data, $tipo);
        if ($result) {
            if ($result == 'correct') {
                $correct = 1;
                $texto = "Has contestado correctamente";
            } else {
                $correct = 0;
                $texto = "Has contestado incorrectamente. La respuesta correcta es :  " . $result;
            }
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "a jugar");
            $this->general_mdl->writeLog("Registro de respuesta retos usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<info>");
            successResponse(array('correct' => $correct), $texto, $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar respuesta retos usuario " . $valida_token["user_id"] . " pregunta " . $this->input->post("question_id"), "<warning>");
            faildResponse('La respuesta no ha sido registrada correctamente', $this);
        }
    }

    public function agregar_puntos($id_reto)
    {
        $result = $this->retos->agregar_puntos($id_reto);
        successResponse($result, "Se agregaron los puntos de los retos pendientes", $this);
    }

    function SaveQuestion()
    {

        if ($this->input->post() == []) {
            $_POST = json_decode(file_get_contents('php://input'), true);
        }


        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->input->post();
        if (!isset($data['active'])) {
            $validaPost = $this->general_mdl->validapost(array('question', 'quiz_id', 'type_id', 'points'), $this->input->post());
            if (!$validaPost['success']) {
                faildResponse($validaPost['msg'], $this);
                return;
            }
        }
        //$data['business_id'] = $valida_token['business_id'];
        $result = $this->questions->SaveQuestion($data);

        if (isset($data['active'])) {
            $text_success = 'La pregunta se ha eliminado correctamente';
            $text_error = 'La pregunta no se ha eliminado correctamente';
        } else {
            if (isset($data['id'])) {
                $text_success = 'La pregunta se ha actualizado correctamente';
                $text_error = 'La pregunta no se ha actualizado correctamente';
            } else {
                $text_success = 'La pregunta se ha creado correctamente';
                $text_error = 'La pregunta no se ha creado correctamente';
            }
        }

        // $usuarios = json_decode($this->input->post("usuarios"), true);
        // $grupos = json_decode($this->input->post("grupos"), true);

        if ($result) {
            // $this->library->agregarUsuarios($result, $usuarios);
            // $this->library->agregarGrupos($result, $grupos);
            $this->general_mdl->writeLog("Registro de nueva pregunta usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, $text_success, $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar pregunta usuario " . $valida_token["user_id"], "<warning>");
            faildResponse($text_error, $this);
        }
    }
}
