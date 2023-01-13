<?php
class Library extends CI_Controller
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
        $this->load->model('library_mdl', 'library');
        $this->load->model('notification_mdl', 'notification');
        $this->load->model('capacitacion_mdl', 'capacitacion');
        $this->load->model('faqs_mdl', 'faqs');
    }

    /***********************************************************************
     *    Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *           urisancer@gmail.com
     *    Nota: Funcion para guardar una catgoría de elementos de la biblioteca
     ***********************************************************************/
    public function SaveSubcategory()
    {

        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('subcategory', 'category_id'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        //Se procede a guardar el area requerida
        $data = array(
            'business_id' => $valida_token['business_id'],
            'subcategory' => $this->input->post("subcategory"),
            'category_id' => $this->input->post("category_id"),
        );
        if ($this->library->SaveSubcategory($data)) {
            $this->general_mdl->writeLog("Registro de subcategoria de biblioteca usuario " . $valida_token["user_id"], "<info>");
            successResponse('', 'La subcategoria ha sido guardada correctamente.', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar subcategoria de biblioteca usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('La subcategoria no se pudo guardar.', $this);
        }
    }

    /***********************************************************************
     *    Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *           urisancer@gmail.com
     *    Nota: Funcion para editar una catgoría de elementos de la biblioteca
     ***********************************************************************/
    public function EditSubcategory()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id', 'subcategory', 'category_id'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        //Se procede a editar el area requerida
        $data = array(
            'id' => $this->input->post("id"),
            'subcategory' => $this->input->post("subcategory"),
        );
        if ($this->library->EditSubcategory($data)) {
            $this->general_mdl->writeLog("Actualizacion de subcategoria de biblioteca usuario " . $valida_token["user_id"] . " subcategoria " . $data["id"], "<info>");
            successResponse('', 'La subcategoria ha sido guardada correctamente.', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar subcategoria de biblioteca usuario " . $valida_token["user_id"] . " subcategoria " . $data["id"], "<warning>");
            faildResponse('La subcategoria no se pudo guardar.', $this);
        }
    }

    /***********************************************************************
     *    Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *           urisancer@gmail.com
     *    Nota: Funcion para editar una catgoría de elementos de la biblioteca
     ***********************************************************************/
    public function DeleteSubcategory()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        //Validar que no tenga elementos asociados. De lo contrario se termina el proceso, enviando error
        $filters = array(
            'business_id' => $valida_token['business_id'],
            'subcategory_id' => $this->input->post("id"),
        );
        $elementos_asociados = $this->library->ListLibrary($filters);
        if ($elementos_asociados) {
            faildResponse('La subcategoria no se puede eliminar, tiene elementos asociados.', $this);
            return;
        }

        //Se procede a eliminar el area requerida
        $data = array(
            "id" => $this->input->post("id"),
        );
        if ($this->library->DeleteSubcategory($data)) {
            $this->general_mdl->writeLog("Eliminacion de subcategoria biblioteca usuario " . $valida_token["user_id"] . " subcategoria " . $data["id"], "<info>");
            successResponse('', 'La subcategoria ha sido eliminada correctamente.', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar subcategoria biblioteca usuario " . $valida_token["user_id"] . " subcategoria " . $data["id"], "<warning>");
            faildResponse('La subcategoria no se pudo eliminar.', $this);
        }
    }

    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *           mario.martinez.f@hotmail.es
     *    Nota: Funcion para obtener el catalogo de areas de biblioteca
     ***********************************************************************/
    public function ListSubcategory()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $validaPost = $this->general_mdl->validapost(array('category_id'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $areas = $this->library->ListSubcategory($valida_token['business_id'], $this->input->post('category_id'));
        if ($areas) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "mi biblioteca");
            $this->general_mdl->writeLog("Listado de subcategoria biblioteca usuario " . $valida_token["user_id"], "<info>");
            successResponse($areas, 'Listado de subcategorias de biblioteca', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener subcategorias biblioteca usuario " . $valida_token["user_id"], "<warning>");
            successResponse([], "No existen registros", $this);
        }
    }

    /***********************************************************************
     *    Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *           urisancer@gmail.com
     *    Nota: Funcion para guardar una catgoría de elementos de la biblioteca
     ***********************************************************************/
    public function SaveCategory()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('name'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        //Se procede a guardar la categoría requerida
        $data = array(
            'business_id' => $valida_token['business_id'],
            'name' => $this->input->post("name"),
        );
        if ($this->library->SaveCategory($data)) {
            $this->general_mdl->writeLog("Registro de categoria biblioteca usuario " . $valida_token["user_id"], "<info>");
            successResponse('', 'La categoría ha sido guardada correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar categoria biblioteca usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('La categoría no se pudo guardar.', $this);
        }
    }

    /***********************************************************************
     *    Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *           urisancer@gmail.com
     *    Nota: Funcion para editar una catgoría de elementos de la biblioteca
     ***********************************************************************/
    public function EditCategory()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id', 'name'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        //Se procede a editar la categoría requerida
        $data = array(
            'id' => $this->input->post("id"),
            'name' => $this->input->post("name"),
        );
        if ($this->library->EditCategory($data)) {
            $this->general_mdl->writeLog("Actualizacion de categoria biblioteca usuario " . $valida_token["user_id"] . " categoria " . $data["id"], "<info>");
            successResponse('', 'La categoría ha sido guardada correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar categoria biblioteca usuario " . $valida_token["user_id"] . " categoria " . $data["id"], "<warning>");
            faildResponse('La categoría no se pudo guardar.', $this);
        }
    }

    /***********************************************************************
     *    Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *           urisancer@gmail.com
     *    Nota: Funcion para editar una catgoría de elementos de la biblioteca
     ***********************************************************************/
    public function DeleteCategory()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        //Validar que no tenga elementos asociados. De lo contrario se termina el proceso, enviando error
        $filters = array(
            'business_id' => $valida_token['business_id'],
            'category_id' => $this->input->post("id"),
        );
        $elementos_asociados = $this->library->ListLibrary($filters);
        if ($elementos_asociados) {
            faildResponse('La categoría no se puede eliminar, tiene elementos asociados.', $this);
            return;
        }

        //Se procede a eliminar el area requerida
        $data = array(
            "id" => $this->input->post("id"),
        );
        if ($this->library->DeleteCategory($data)) {
            $this->general_mdl->writeLog("Eliminacion de categoria biblioteca usuario " . $valida_token["user_id"] . " categoria " . $data["id"], "<info>");
            successResponse('', 'La categoría ha sido eliminada correctamente.', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar categoria biblioteca usuario " . $valida_token["user_id"] . " categoria " . $data["id"], "<warning>");
            faildResponse('La categoría no se pudo eliminar.', $this);
        }
    }

    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *           mario.martinez.f@hotmail.es
     *    Nota: Funcion para obtener el catalogo de categorias de biblioteca
     ***********************************************************************/
    public function ListCategories()
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
        $es_admin = $this->input->post("es_admin");
        $categories = $this->library->ListCategories($valida_token['business_id'], $es_admin);
        if ($categories) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "mi biblioteca");
            $this->general_mdl->writeLog("Listado de categorias usuario " . $valida_token["user_id"], "<info>");
            successResponse($categories, 'Listado de categorias de biblioteca', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener categorias biblioteca usuario " . $valida_token["user_id"], "<warning>");
            successResponse([], "No existen registros", $this); //faildResponse('No existen registros', $this);
        }
    }

    /**
     * Guardamos un podcast
     */
    public function savePodcast()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('title', 'description'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        $archivos = $this->general_mdl->GuardarArchivos($_FILES, $ruta = 'podcasts', $valida_token['business_id']);
        $image = null;
        $file = null;
        if (!$archivos['success']) {
            //si no fue exitosa, por: 1.-Carpeta de empresa inexistente ó 2.-Limite de espacio superado
            faildResponse($archivos['msg'], $this);
            return;
        }
        if (isset($archivos['success_files']['image'])) {
            $image = $archivos['success_files']['image'];
        }

        if (isset($archivos['success_files']['audio'])) {
            $file = $archivos['success_files']['audio'];
        }

        $data = array(
            'title' => $this->input->post("title"),
            "description" => $this->input->post("description"),
            "etiquetas" => $this->input->post("etiquetas"),
            "type" => $this->input->post("type"),
            "preview" => $image,
            "audio" => $file,
            "duration" => $this->input->post("duration"),
            "business_id" => $valida_token['business_id'],
            "fecha_limite" => $this->input->post("fecha_limite"),
            "capacitacion_obligatoria" => $this->input->post("capacitacion_obligatoria")
        );
        
        $usuarios = json_decode($this->input->post("usuarios"), true);
        $grupos = json_decode($this->input->post("grupos"), true);
        $regiones = json_decode($this->input->post("regiones"));
        $asesores = json_decode($this->input->post("asesores"));
        
        $result = $this->library->savePodcast($data);
        if ($result) {
            /* crear relacion extractos */
            $dataExtracto = array(
                'library_id' => $result,
                'resumen' => $data["title"],
                'etiquetas' => $data["etiquetas"],
                'id_categoria' => $data["category_id"],
                'tipo' => 2
            );
            $this->faqs->actualizar($dataExtracto, $result, 2);
            // $this->library->agregarGruposPodcast($result, $grupos);
            // $this->library->agregarRegionesPodcast($result, $regiones);
            // $this->library->agregarAsesoresPodcast($result, $asesores);
            // $this->library->agregarUsuariosPodcast($result, $usuarios);
            $id_region =  $this->input->post('id_region') == null ? 0 :  $this->input->post('id_region');
            $id_asesor =  $this->input->post('id_asesor') == null ? 0 :  $this->input->post('id_asesor');
            $tokens = $this->notification->ListUserNotification($valida_token['business_id'], 0, $id_region, $id_asesor);
            if ($tokens) {
                $tokens_ = array();
                foreach ($tokens as $index => $value) {
                    array_push($tokens_, $value['token']);
                    /***********************************************************************
                     *    Autor: Mario Adrián Martínez Fernández   Fecha: 09/07/2020
                     *           mario.martinez.f@hotmail.es
                     *    Nota: Guardarmos a los usuarios que se les enviara la notificacion.
                     ***********************************************************************/
                    $data_ = array('title' => 'Podcast', 'notification' => 'Se ha agregado ' . $data['title'] . ' ha podcast', 'user_id' => $value['user_id'], 'service_id' => 13, 'user_create_id' => $valida_token['user_id'], "id_topic" => $result);
                    $this->notification->RegisterNotification($data_);
                }
                /***********************************************************************
                 *    Nota: Se envia notificacion a los multiples tokens
                 ***********************************************************************/
                $this->general_mdl->EnviarNotificacionPush($tokens_, 'Se ha agregado un nuevo elemento a podcast', 'Podcast', 13, true, array("id_topic" => $result));
            }
            $this->general_mdl->writeLog("Registro de nuevo podcast usuario " . $valida_token["user_id"], "<info>");
            successResponse('', 'El podcast ha sido guardado correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar podcast usuario " . $valida_token["user_id"], "<warning>");
            faildResponse("No se guardó", $this);
        }
    }

    /**
     * Eliminamos un podcast
     */
    public function deletePodcast()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        //Se procede a eliminar el area requerida
        $data = array(
            "id" => $this->input->post("id"),
            "active" => $this->input->post("active")
        );
        if ($this->library->deletePodcast($data)) {
            $this->general_mdl->writeLog("Eliminacion de podcast usuario " . $valida_token["user_id"] . " podcast " . $data["id"], "<info>");
            successResponse('', 'El elemento ha sido eliminado correctamente.', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar podcast usuario " . $valida_token["user_id"] . " podcast " . $data["id"], "<warning>");
            faildResponse('El elemento no se pudo eliminar.', $this);
        }
    }

    /***********************************************************************
     *    Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *           urisancer@gmail.com
     *    Nota: Funcion para guardar una catgoría de elementos de la biblioteca
     ***********************************************************************/
    public function SaveElement()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('title', 'text', 'category_id', 'type'), $this->input->post());
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

        //Se procede a guardar el elemento requerido
        $data = array(
            'title' => $this->input->post("title"),
            'text' => $this->input->post("text"),
            'etiquetas' => $this->input->post("etiquetas"),
            'category_id' => $this->input->post("category_id"),
            'subcategory_id' => $this->input->post("subcategory_id"),
            'type' => $this->input->post("type"),
            'file' => $file,
            'image' => $image,
            'link' => $this->input->post("link"),
            'type_video' => $this->input->post("type_video"),
            'video' => $video,
            'question' => $this->input->post('quiz_library'),
            'business_id' => $valida_token['business_id'],
            'fecha_limite' => $this->input->post('fecha_limite'),
            'id_region' => $this->input->post('id_region'),
            'id_asesor' => $this->input->post('id_asesor'),
            'capacitacion_obligatoria' => $this->input->post('capacitacion_obligatoria')
        );
        $usuarios = json_decode($this->input->post("usuarios"), true);
        $grupos = json_decode($this->input->post("grupos"), true);
        $regiones = json_decode($this->input->post("regiones"));
        $asesores = json_decode($this->input->post("asesores"));
        $id_library = $this->library->SaveElement($data);
        if ($id_library) {
             /* crear relacion extractos */
             $dataExtracto = array(
                'library_id' => $id_library,
                'resumen' => $data["title"],
                'etiquetas' => $data["etiquetas"],
                'id_categoria' => $data["category_id"],
                'tipo' => 1
            );
            $this->faqs->actualizar($dataExtracto, $id_library, 1);
            // $this->library->agregarUsuarios($id_library, $usuarios);
            // $this->library->agregarGrupos($id_library, $grupos);
            // $this->library->agregarRegiones($id_library, $regiones);
            // $this->library->agregarAsesores($id_library, $asesores);
            /***********************************************************
             * ************
             *    Nota: Se obtiene los tokens existentes en la BD
             ***********************************************************************/
            $id_region =  $this->input->post('id_region') == null ? 0 :  $this->input->post('id_region');
            $id_asesor =  $this->input->post('id_asesor') == null ? 0 :  $this->input->post('id_asesor');
            $tokens = $this->notification->ListUserNotification($valida_token['business_id'], 0, $id_region, $id_asesor);
            if ($tokens) {
                $tokens_ = array();
                foreach ($tokens as $index => $value) {
                    array_push($tokens_, $value['token']);
                    /***********************************************************************
                     *    Autor: Mario Adrián Martínez Fernández   Fecha: 09/07/2020
                     *           mario.martinez.f@hotmail.es
                     *    Nota: Guardarmos a los usuarios que se les enviara la notificacion.
                     ***********************************************************************/
                    $data_ = array('title' => 'Mi Biblioteca', 'notification' => 'Se ha agregado ' . $data['title'] . ' ha biblioteca', 'user_id' => $value['user_id'], 'service_id' => SERVICE_LIBRARY, 'user_create_id' => $valida_token['user_id'], "id_topic" => $id_library);
                    $this->notification->RegisterNotification($data_);
                }
                /***********************************************************************
                 *    Nota: Se envia notificacion a los multiples tokens
                 ***********************************************************************/
                $this->general_mdl->EnviarNotificacionPush($tokens_, 'Se ha agregado un nuevo elemento a biblioteca', 'Mi Biblioteca', SERVICE_LIBRARY, true, array("id_topic" => $id_library));
            }
            $this->general_mdl->writeLog("Registro de nuevo elemento de biblioteca usuario " . $valida_token["user_id"], "<info>");
            successResponse('', 'El elemento ha sido guardado correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al guardar nuevo elemento biblioteca usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('El elemento no se pudo guardar.', $this);
        }
    }

    public function editPodcast()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");

        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $validaPost = $this->general_mdl->validapost(array('id', 'title', 'description'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        //Se procede a editar la categoría requerida
        $data = array(
            'id' => $this->input->post("id"),
            'title' => $this->input->post("title"),
            'description' => $this->input->post("description"),
            'etiquetas' => $this->input->post("etiquetas"),
            'fecha_limite' => $this->input->post("fecha_limite")
        );
        $regiones = json_decode($this->input->post("regiones"));
        $asesores = json_decode($this->input->post("asesores"));
        if ($this->library->editPodcast($data)) {
            /* editar relacion extracto */
            $dataExtracto = array(
                'library_id' => $this->input->post("id"),
                'resumen' => $data["title"],
                'etiquetas' => $data["etiquetas"]
            );
            $this->faqs->actualizar($dataExtracto, $this->input->post("id"), 2);
            if($regiones){
                $this->library->agregarRegionesPodcast($data["id"], $regiones);
            }
            if($asesores){
                $this->library->agregarAsesoresPodcast($data["id"], $asesores);
            }
            $this->general_mdl->writeLog("Actualizacion de podcast usuario " . $valida_token["user_id"] . " podcast " . $data["id"], "<info>");
            successResponse('', 'El elemento ha sido guardado correctamente', $this);
        } else {
            $this->general_mdl->writeLog("Error al actualizar podcast usuario " . $valida_token["user_id"] . " podcast " . $data["id"], "<warning>");
            faildResponse('El elemento no se pudo guardar.', $this);
        }
    }

    /***********************************************************************
     *    Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *           urisancer@gmail.com
     *    Nota: Funcion para editar una catgoría de elementos de la biblioteca
     ***********************************************************************/
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

    /***********************************************************************
     *    Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *           urisancer@gmail.com
     *    Nota: Funcion para editar una catgoría de elementos de la biblioteca
     ***********************************************************************/
    public function DeleteElement()
    {
        if ($this->input->post() == [])
            $_POST = json_decode(file_get_contents('php://input'), true);
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $validaPost = $this->general_mdl->validapost(array('id'), $this->input->post());
        if (!$validaPost['success']) {
            faildResponse($validaPost['msg'], $this);
            return;
        }

        //Se procede a eliminar el area requerida
        $data = array(
            "id" => $this->input->post("id"),
            "active" => $this->input->post("activo")
        );
        if ($this->library->DeleteElement($data)) {
            $this->general_mdl->writeLog("Eliminacion de elemento biblioteca usuario " . $valida_token["user_id"] . " elemento " . $data["id"], "<info>");
            successResponse('', 'El elemento ha sido eliminado correctamente.', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar elemento biblioteca usuario " . $valida_token["user_id"] . " elemento " . $data["id"], "<warning>");
            faildResponse('El elemento no se pudo eliminar.', $this);
        }
    }

    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *           mario.martinez.f@hotmail.es
     *    Nota: Funcion para obtener llos registros de la biblioteca en base
     *             a la empresa con la que esta ligado el usuario del token
     *             enviado.
     ***********************************************************************/
    public function ListLibrary()
    {

        // if ($this->input->post() == []) {
        //     $_POST = json_decode(file_get_contents('php://input'), true);
        // }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $data = $this->input->post() != null ? $this->input->post() : [];
        $data['business_id'] = $valida_token['business_id'];
        $data['user_id'] = $valida_token['user_id'];
        $data["rol_id"] = $valida_token["rol_id"];
        $data["region_id"] = $valida_token["id_region"];
        $data["asesor_id"] = $valida_token["id_asesor"];
        $library = [];

        $library = $this->library->ListLibrary($data);

        if ($library) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "mi biblioteca");
            $this->general_mdl->writeLog("Listado de elementos de biblioteca usuario " . $valida_token["user_id"], "<info>");
            successResponse($library, 'Listado de biblioteca', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener elementos de biblioteca usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('No existen registros', $this);
        }
    }

    public function GetLibraryById()
    {

        if ($this->input->post() == []) {
            $_POST = json_decode(file_get_contents('php://input'), true);
        }

        $token = $this->input->post("token");
        $id_elemento = $this->input->post("id");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            successResponse(false, 'El usuario no esta registrado', $this);
            return;
        }

        $data = $this->input->post() != null ? $this->input->post() : [];
        $data['business_id'] = $valida_token['business_id'];
        $data['user_id'] = $valida_token['user_id'];
        $data["rol_id"] = $valida_token["rol_id"];
        $data["region_id"] = $valida_token["id_region"];
        $data["asesor_id"] = $valida_token["id_asesor"];
        $data["id"] = $this->input->post("id");
        $library = [];

        $library = $this->library->GetLibraryById($data);

        if ($library) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "mi biblioteca");
            $this->general_mdl->writeLog("Obtener elemento por id usuario " . $valida_token["user_id"], "<info>");
            successResponse($library, 'Elemento de biblioteca', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener elemento por id usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('No existen registros', $this);
        }
    }
    /***********************************************************************
     *    Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
     *           mario.martinez.f@hotmail.es
     *    Nota: Funcion para obtener los catalogos de preguntas que se pueden
     *             asignar a un usuario
     ***********************************************************************/
    public function QuizLibrary()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        // echo json_encode($this->input->post());
        $data = $this->input->post();
        $data['business_id'] = $valida_token['business_id'];
        $quizLibrary = $this->library->QuizLibrary($data);
        if ($quizLibrary) {
            $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "mi biblioteca");
            $this->general_mdl->writeLog("Listado de cuestionarios por elemento biblioteca usuario " . $valida_token["user_id"], "<info>");
            successResponse($quizLibrary, 'Listado de catalogo de preguntas de biblioteca', $this);
        } else {
            $this->general_mdl->writeLog("Error al obtener cuestionarios por elemento biblioteca usuario " . $valida_token["user_id"], "<warning>");
            faildResponse('No existen registros', $this);
        }
    }

    public function SetVisto()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        // echo json_encode($valida_token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = [];
        $data["library_element_id"] = $this->input->post("id");
        $data["numero_clicks"] = $this->input->post("numero_clicks");
        $data['user_id'] = $valida_token['user_id'];
        $result = $this->library->SetVisto($data);
        $this->capacitacion->markCompleted($data["user_id"], $data["library_element_id"], "library_elements_");
        // $this->general_mdl->agregar_recurso_visto($data["user_id"]);
        $this->general_mdl->agregar_uso_usuario($valida_token["user_id"], "mi biblioteca");
        $this->general_mdl->writeLog("Registro de visto de elemento biblioteca usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Se ha establecido como visto el elemento de biblioteca', $this);
    }

    public function eliminarUsuario()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_usuario = $this->input->post("id_usuario");
        $id_library = $this->input->post("id_library");
        $data["numero_clicks"] = $this->input->post("numero_clicks");
        $result = $this->library->eliminarUsuario($id_library, $id_usuario);
        if ($result) {
            $this->general_mdl->writeLog("Eliminacion de usuario de library usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("elearning_id"), "<info>");
            successResponse($result, 'Se elimino el usuario del library', $this);
        } else {
            $this->general_mdl->writeLog("Error al eliminar el usuario de  library usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("elearning_id"), "<warning>");
            faildResponse('Error al eliminar usuario', $this);
        }
    }

    public function agregarUsuario()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $id_usuario = $this->input->post("id_usuario");
        $id_library = $this->input->post("id_library");
        $result = $this->library->agregarUsuario($id_library, $id_usuario);
        if ($result) {
            $this->general_mdl->writeLog("Registro de usuarios en library usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("elearning_id"), "<info>");
            successResponse($result, 'Se agregaron los usuarios al library', $this);
        } else {
            $this->general_mdl->writeLog("Error al agregar usuarios al  library usuario " . $valida_token["user_id"] . " elearning " . $this->input->post("elearning_id"), "<warning>");
            faildResponse('Error al agregar usuarios al library', $this);
        }
    }

    public function CalificarLibrary()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $data = [];
        $data["library_id"] = $this->input->post("id");
        $data["score"] = $this->input->post("score");
        $data['user_id'] = $valida_token['user_id'];
        $result = $this->library->CalificarLibrary($data);
        $this->general_mdl->writeLog("Registro de calificacion de library usuario " . $valida_token["user_id"] . " library " . $data["library_id"], "<info>");
        successResponse($result, 'Se ha establecido la calificación', $this);
    }

    public function obtener_biblioteca_capacitacion()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }
        $business_id = $valida_token["business_id"];
        $result = $this->library->obtener_biblioteca_capacitacion($business_id);
        $this->general_mdl->writeLog("Elementos de biblioteca para capacitaciones obligatorias usuario " . $valida_token["user_id"], "<info>");
        successResponse($result, 'Elementos de biblioteca para capacitaciones obligatorias', $this);
    }
}
