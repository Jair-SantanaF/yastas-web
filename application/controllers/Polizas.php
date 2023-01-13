<?php
class Polizas extends CI_Controller
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
        $this->load->model('Polizas_mdl', 'polizas_mdl');
        $this->load->model('General_mdl', 'general_mdl');
    }

    /* obtener usuarios con polizas */
    public function listUserWithPoliza()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->input->post() != null ? $this->input->post() : [];
        $data['business_id'] = $valida_token['business_id'];
        $data['user_id'] = $valida_token['user_id'];
        $polizas = $this->polizas_mdl->listUserWithPoliza($data['business_id']);
        if ($polizas) {
            $this->general_mdl->writeLog("Listado de usuarios con polizas " . $valida_token["user_id"], "<info>");
            successResponse($polizas, 'Listado de biblioteca', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener usuarios con polizas " . $valida_token["user_id"], "<warning>");
            faildResponse('No existen registros', $this);
        }
    }

    /* obtener polizas de usuario id */
    public function getPolizasByUserId()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = $this->input->post() != null ? $this->input->post() : [];
        $user_id = $data['user_id'];
        $business_id = $valida_token['business_id'];
        $polizas = $this->polizas_mdl->getPolizasByUserId($user_id, $business_id);
        if ($polizas) {
            $this->general_mdl->writeLog("Listado de polizas " . $valida_token["user_id"], "<info>");
            successResponse($polizas, 'Listado de biblioteca', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener polizas " . $valida_token["user_id"], "<warning>");
            faildResponse('No existen registros', $this);
        }
    }

    public function guardarPoliza()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        /* validar post */
        $user_id = null;
        //$usuarios = $this->input->post('usuarios');
        $usuarios = json_decode($this->input->post("usuarios"), true);
        //successResponse($usuarios, 'El elemento ha sido guardado correctamente', $this);
        //return;
        if($usuarios){
            foreach($usuarios as $key => $user){
                $user_id = $user['id'];
            }
        }else{
            faildResponse('El elemento no se pudo guardar, se necesita el usuario', $this);
            return;
        }
        $type = $this->input->post("type");
        //Guardar archivos
        $archivos = $this->general_mdl->GuardarArchivos($_FILES, $ruta = 'polizas', $valida_token['business_id']);
        //Si no se subieron los archivos correctamente, terminamos el proceso
        if (!$archivos['success']) {
            //si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
            faildResponse($archivos['msg'], $this);
            return;
        }
        //Guardamos la referencia del los archivos dependiendo el tipo de elemento
        $file = '';
        $video = '';
        if ($type == 'documento' || $type == 'imagen') {
            if (isset($archivos['success_files']['file'])) {
                $file = $archivos['success_files']['file'];
            }
        }
        //Se procede a guardar el elemento requerido
        $data = array(
            'url' => $file,
            'active' => 1,
            'created_at' => date('y-m-d h:i:s'),
        );
        /* relacion poliza */
        $poliza = $this->polizas_mdl->savePoliza($data);
        if ($poliza) {
             /* crear relacion extractos */
             $data = array(
                'poliza_id' => $poliza,
                'user_id' => $user_id,
            );
            $this->polizas_mdl->savePolizaUsuario($data);
            $this->general_mdl->writeLog("Registro nuevo" . $valida_token["user_id"], "<info>");
            successResponse('', 'El elemento ha sido guardado correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar la poliza " . $valida_token["user_id"], "<warning>");
            faildResponse('El elemento no se pudo guardar.', $this);
        }
    }

    public function EditElement()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id', 'title', 'text', 'category_id'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        $type = $this->input->post("type");
        $type_video = $this->input->post("type_video");
        //Guardar archivos
        $archivos = $this->general_mdl->GuardarArchivos($_FILES, $ruta = 'library', $valida_token['business_id']);
        //Si no se subieron los archivos correctamente, terminamos el proceso
        if (!$archivos['success']) {
            //si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
            faildResponse($archivos['msg'], $this);
            return;
        }
        //En cualquier tipo se puede subir la imagen preview, guardamos la referencia
        $image = '';
        if (isset($archivos['success_files']['image'])) {
            $image = $archivos['success_files']['image'];
        }
        //Guardamos la referencia del los archivos dependiendo el tipo de elemento
        $file = '';
        $video = '';
        if ($type == 'documento' || $type == 'imagen') {
            if (isset($archivos['success_files']['file'])) {
                $file = $archivos['success_files']['file'];
            }
        }
        if ($type == 'video') {
            if ($type_video == 'servidor') {
                if (isset($archivos['success_files']['video'])) {
                    $video = $archivos['success_files']['video'];
                }
            } else {
                $video = $this->input->post("video");
            }
        }

        //Se procede a editar la categoría requerida
        $data = array(
            'id' => $this->input->post("id"),
            'title' => $this->input->post("title"),
            'etiquetas' => $this->input->post("etiquetas"),
            'text' => $this->input->post("text"),
            'category_id' => $this->input->post("category_id"),
            'subcategory_id' => $this->input->post("subcategory_id"),
            'image' => $image,
            'type' => $this->input->post("type"),
            'file' => $file,
            'link' => $this->input->post("link"),
            'type_video' => $this->input->post("type_video"),
            'question' => ($this->input->post("quiz_library")) ? $this->input->post("quiz_library") : '',
            'video' => $video,
            'fecha_limite' => $this->input->post("fecha_limite"),
            'id_region' => $this->input->post("id_region"),
            'id_asesor' => $this->input->post("id_asesor")
        );
        $regiones = json_decode($this->input->post("regiones"));
        $asesores = json_decode($this->input->post("asesores"));
        if ($this->library->EditElement($data)) {
            /* editar relacion extracto */
            $dataExtracto = array(
                'library_id' => $this->input->post("id"),
                'resumen' => $data["title"],
                'etiquetas' => $data["etiquetas"],
                'id_categoria' => $data["category_id"],
            );
            $this->faqs->actualizar($dataExtracto, $this->input->post("id"), 1);
           /*  $this->library->agregarRegiones($data["id"], $regiones);
            $this->library->agregarAsesores($data["id"], $asesores); */
            $this->general_mdl->writeLog("Actualizacion de elemento biblioteca usuario " . $valida_token["user_id"] . " elemento " . $data["id"], "<info>");
            successResponse('', 'El elemento ha sido guardado correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar elemento biblioteca usuario " . $valida_token["user_id"] . " elemento " . $data["id"], "<warning>");
            faildResponse('El elemento no se pudo guardar.', $this);
        }
    }

    public function eliminarPoliza()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id = $this->input->post("id");
        if ($this->polizas_mdl->eliminarPoliza($id)) {
            $this->general_mdl->writeLog("Eliminacion de elemento biblioteca usuario " . $valida_token["user_id"] . " elemento " . $id, "<info>");
            successResponse('', 'El elemento ha sido eliminado correctamente.', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar elemento biblioteca usuario " . $valida_token["user_id"] . " elemento " . $id, "<warning>");
            faildResponse('El elemento no se pudo eliminar.', $this);
        }
    }

}
