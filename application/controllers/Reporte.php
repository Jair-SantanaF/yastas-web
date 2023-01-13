<?php
class Reporte extends CI_Controller
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
        $this->load->model('reporte_mdl', 'reporte');
    }

    function guardar_reporte()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('direccion', 'latitud', 'longitud', 'id_categoria', 'id_subcategoria', 'descripcion'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        $data = $this->input->post();
        $data["user_id"] = $valida_token["user_id"];
        unset($data["token"]);
        $id_reporte = $this->reporte->guardar_reporte($data);
        if ($id_reporte) {
            $this->general_mdl->writeLog("Nuevo reporte generado " . $valida_token["user_id"], "<info>");
            successResponse(array('id_reporte' => $id_reporte), 'Nuevo reporte generado', $this);
        } else {
            $this->general_mdl->writeLog("Error al generar reporte " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al generar reporte', $this);
        }
    }

    function guardar_imagen_reporte()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        $config['upload_path'] = './uploads/business_' . $valida_token["business_id"] . '/evidencias_reporte/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|mp4';

        $this->load->library('upload', $config);
        $result = null;
        if (!$this->upload->do_upload('evidencia')) {
            $error = array('error' => $this->upload->display_errors());
            faildResponse($error, $this);
            return;
        } else {
            $data = $this->upload->data();
            $nombre_archivo = $data['raw_name'] . $data['file_ext'];
            $tipo = "imagen";
            $id_reporte = $this->input->post("id_reporte");
            if ($data['file_ext'] == ".mp4") {
                $tipo = "video";
            }
            $result = $this->reporte->guardar_imagen_reporte($id_reporte, $nombre_archivo, $tipo);
            if ($result) {
                $this->general_mdl->writeLog("Imagen de reporte guardada " . $valida_token["user_id"], "<info>");
                successResponse(array("id" => $result), 'Imagen guardada', $this);
            } else {
                $this->general_mdl->writeLog("Error al guardar evidencias de reporte " . $valida_token["user_id"], "<warning>");
                faildResponse('Error al guardar evidencias de reporte', $this);
            }
        }
    }

    function eliminar_imagen_reporte()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id_imagen'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        $id_imagen = $this->input->post("id_imagen");
        $id_reporte = $this->reporte->eliminar_imagen_reporte($id_imagen);
        if ($id_reporte) {
            $this->general_mdl->writeLog("Imagen de reporte eliminada " . $valida_token["user_id"], "<info>");
            successResponse(true, 'Imagen de reporte eliminada', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar imagen de reporte " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al eliminar imagen de reporte', $this);
        }
    }

    function eliminar_reporte()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id_reporte'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        $id_reporte = $this->input->post("id_reporte");
        $id_reporte = $this->reporte->eliminar_reporte($id_reporte);
        if ($id_reporte) {
            $this->general_mdl->writeLog("Reporte eliminado " . $valida_token["user_id"], "<info>");
            successResponse(true, 'Reporte eliminado', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar reporte " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al eliminar reporte', $this);
        }
    }

    function actualizar_reporte()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('direccion', 'latitud', 'longitud', 'id_categoria', 'id_subcategoria', 'descripcion', "id_reporte"), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        $data = $this->input->post();
        unset($data["token"]);
        $id_reporte = $this->reporte->actualizar_reporte($data);
        if ($id_reporte) {
            $this->general_mdl->writeLog("Reporte actualizado " . $valida_token["user_id"], "<info>");
            successResponse(true, 'Reporte actualizado con exito', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar reporte " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al actualizar reporte', $this);
        }
    }

    function obtener_reportes()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $reportes = $this->reporte->obtener_reportes($valida_token["user_id"], $valida_token["business_id"]);
        if ($reportes) {
            $this->general_mdl->writeLog("Listado de reportes del usuario " . $valida_token["user_id"], "<info>");
            successResponse($reportes, 'Listado de reportes', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener listado de reportes del usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al obtener listado de reportes', $this);
        }
    }

    function obtener_reportes_admin()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $reportes = $this->reporte->obtener_reportes_admin($valida_token["business_id"]);
        if ($reportes) {
            $this->general_mdl->writeLog("Listado de reportes  usuario " . $valida_token["user_id"], "<info>");
            successResponse($reportes, 'Listado de reportes', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener listado de reportes usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al obtener listado de reportes', $this);
        }
    }

    function obtener_categorias()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $categorias = $this->reporte->obtener_categorias($valida_token["business_id"]);

        $this->general_mdl->writeLog("Listado de categorias de reportes usuario " . $valida_token["user_id"], "<info>");
        successResponse($categorias, 'Listado de categorias de reportes', $this);
    }

    function obtener_subcategorias()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_categoria = $this->input->post("id_categoria");
        $subcategorias = $this->reporte->obtener_subcategorias($id_categoria);

        $this->general_mdl->writeLog("Listado de subcategorias de reportes usuario " . $valida_token["user_id"], "<info>");
        successResponse($subcategorias, 'Listado de subcategorias reportes', $this);
    }

    function eliminar_categoria()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_categoria = $this->input->post("id_categoria");
        $result = $this->reporte->eliminar_categoria($id_categoria);
        if ($result) {
            $this->general_mdl->writeLog("Categoria eliminada usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Categoria eliminada reportes', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar categoria usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al eliminar categoria reportes', $this);
        }
    }

    function eliminar_subcategoria()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_subcategoria = $this->input->post("id_subcategoria");
        $result = $this->reporte->eliminar_subcategoria($id_subcategoria);
        if ($result) {
            $this->general_mdl->writeLog("Subcategoria eliminada usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Subcategoria eliminada reportes', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar subcategoria usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al eliminar subcategoria reportes', $this);
        }
    }

    function actualizar_categoria()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->input->post();
        $id_categoria = $data["id"];
        unset($data["id"]);
        $result = $this->reporte->actualizar_categoria($id_categoria, $data);
        if ($result) {
            $this->general_mdl->writeLog("Categoria actualizada usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Categoria actualizada reportes', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar categoria usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al actualizar categoria reportes', $this);
        }
    }

    function actualizar_subcategoria()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->input->post();
        $id_subcategoria = $data["id"];
        unset($data["id"]);
        $result = $this->reporte->actualizar_subcategoria($id_subcategoria, $data);
        if ($result) {
            $this->general_mdl->writeLog("Subcategoria actualizada usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Subcategoria actualizada reportes', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar subcategoria usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al actualizar subcategoria reportes', $this);
        }
    }

    function guardar_categoria()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->input->post();
        $data["business_id"] = $valida_token["business_id"];
        $result = $this->reporte->guardar_categoria($data);
        if ($result) {
            $this->general_mdl->writeLog("Categoria guardada usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Categoria guardada reportes', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar categoria usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al guardar categoria reportes', $this);
        }
    }

    function guardar_subcategoria()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->input->post();
        $result = $this->reporte->guardar_subcategoria($data);
        if ($result) {
            $this->general_mdl->writeLog("Subcategoria guardada usuario " . $valida_token["user_id"], "<info>");
            successResponse($result, 'Subcategoria guardada reportes', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar subcategoria usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('Error al guardar subcategoria reportes', $this);
        }
    }
}
