<?php

class Services_model extends CI_Model
{

    private $tablePurchaseService = "services_purchase_preview",
        $tableServices = "services",
        $tableCategories = "categories",
        $tableBusiness = "business",
        $tableHiredServices = "hired_services";

    function __construct()
    {
        parent::__construct();
        $this->load->model('Library_mdl', 'library_mdl');
        $this->load->model('Podcast_mdl', 'podcast_mdl');
        $this->load->model('Capacitacion_mdl', 'capacitacion_mdl');
        $this->load->model('question_mdl', 'questions_mdl');        
        $this->load->model('Com_mdl', 'com_mdl');
        $this->load->model('Chat_mdl', 'chat_mdl');
    }

    public function insert($entity)
    {
        if ($this->db->insert($this->tableServices, $entity)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    public function update($id, $entity)
    {
        if ($this->db->update($this->tableServices, $entity, array("id" => $id))) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        if ($this->db->delete($this->tableServices, array('id' => $id))) {
            return true;
        } else {
            return false;
        }
    }

    public function fetchAll($business_id)
    {
        $query = "select * from $this->tableServices where id not in(select services_id from $this->tableHiredServices where business_id = $business_id)";
        $query = $this->db->query($query)->result_array();
        $url = base_url('uploads/default/services/');
        if (count($query) > 0) {
            foreach ($query as $index => $value) {
                $query[$index]['image'] = $url . $value['image'];
            }
            return $query;
        } else {
            return false;
        }
    }

    public function fetchAllById($id)
    {
        return $this->db->get_where($this->tableServices, array("id" => $id))->result_array();
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 18/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: funcion para obtner los servicios contratados por la empresa
     ***********************************************************************/
    function HiredServices($params)
    {
        $query = "
                select 
                    hs.id,                
                       b.id as business_id,
                       s.id as service_id,
                       s.service_name,
                       s.description,
                       s.image,
                       s.image_en as new_img,
                       s.category_id,
                       c.category_name, 
                       hs.view                       
                from hired_services hs 
                    join services s on hs.services_id = s.id 
                    join business b on hs.business_id = b.id
                    join categories c on s.category_id = c.id  
                where hs.services_id != 16 and hs.services_id != 17
            ";
        //el where de arriba evita que aparezcan el servicio de ambiente laboral en el home de la app
        // 
        // where true  
        if (isset($params["business_id"]) && $params["business_id"] != "") {
            $query .= ' AND b.id = ' . $params["business_id"];
        }

        $query .= " ORDER BY `order` ASC";

        $url = base_url('uploads/services/services_hired/');
        $query = $this->db->query($query)->result_array();
        if (count($query) > 0) {
            if ($params["business_id"] == 18) {
                $indice = 5000;
            }
            foreach ($query as $index => $value) {
                if ($params["business_id"] == 28) {
                    if ($query[$index]["service_name"] == "Capacitacion") {
                        $query[$index]["image"] = $url . "img_Capacitacion_txt_cemex.png";
                        $query[$index]["new_img"] = $url . "img_Capacitacion_txt_cemex.png";
                    }
                    if ($query[$index]["service_name"] == "Biblioteca") {
                        $query[$index]["image"] = $url . "img_biblioteca_txt_cemex.png";
                        $query[$index]["new_img"] = $url . "img_biblioteca_txt_cemex.png";
                    }
                    if ($query[$index]["service_name"] == "Elearning") {
                        $query[$index]["image"] = $url . "img_elearnings_txt_cemex.png";
                        $query[$index]["new_img"] = $url . "img_elearnings_txt_cemex.png";
                    }
                    if ($query[$index]["service_name"] == "Preguntas") {
                        $query[$index]["image"] = $url . "img_cuestionarios_txt_cemex.png";
                        $query[$index]["new_img"] = $url . "img_cuestionarios_txt_cemex.png";
                    }
                    if ($query[$index]["service_name"] == "Comunidad de aprendizaje") {
                        $query[$index]["image"] = $url . "img_comunidades_txt_cemex.png";
                        $query[$index]["new_img"] = $url . "img_comunidades_txt_cemex.png";
                    }
                    if ($query[$index]["service_name"] == "Juegos") {
                        $query[$index]["image"] = $url . "img_juegos_txt_cemex.png";
                        $query[$index]["new_img"] = $url . "img_juegos_txt_cemex.png";
                    }
                    if ($query[$index]["service_name"] == "Muro") {
                        $query[$index]["image"] = $url . "img_newsfeed_txt_cemex.png";
                        $query[$index]["new_img"] = $url . "img_newsfeed_txt_cemex.png";
                    }
                    if ($query[$index]["service_name"] == "Chat") {
                        $query[$index]["image"] = $url . "img_chat_txt_cemex.png";
                        $query[$index]["new_img"] = $url . "img_chat_txt_cemex.png";
                    }
                } else if ($params["business_id"] == 18) {
                    if ($query[$index]["service_name"] == "Capacitacion") {
                        //$indice = $index;
                        $query[$index]["image"] = $url . "btn_aprendizaje_yastas.png";
                        $query[$index]["new_img"] = $url . "btn_aprendizaje_yastas.png";
                        $query[$index]["nuevoContenido"] = $this->isNuevoContenido("Capacitacion", $params["user_id"], $params["business_id"]);
                    }
                    if ($query[$index]["service_name"] == "Biblioteca") {
                        $query[$index]["image"] = $url . "btn_biblioteca_yastas.png";
                        $query[$index]["new_img"] = $url . "btn_biblioteca_yastas.png";
                        $query[$index]["nuevoContenido"] = $this->isNuevoContenido("Biblioteca", $params["user_id"], $params["business_id"]);
                    }
                    if ($query[$index]["service_name"] == "Elearning") {
                        $query[$index]["image"] = $url . "btn_aprendo_en_linea_yastas.png";
                        $query[$index]["new_img"] = $url . "btn_aprendo_en_linea_yastas.png";
                        $query[$index]["nuevoContenido"] = 0;
                    }
                    if ($query[$index]["service_name"] == "Preguntas") {
                        //$indice = $index;
                        $query[$index]["image"] = $url . "btn_cuestionarios_yastas.png";
                        $query[$index]["new_img"] = $url . "btn_cuestionarios_yastas.png";
                        $query[$index]["nuevoContenido"] = $this->isNuevoContenido("Preguntas", $params["user_id"], $params["business_id"]);
                    }
                    if ($query[$index]["service_name"] == "Comunidad de aprendizaje") {
                        $query[$index]["image"] = $url . "btn_aprendiendo_juntos_yastas.png";
                        $query[$index]["new_img"] = $url . "btn_aprendiendo_juntos_yastas.png";
                        $query[$index]["nuevoContenido"] = $this->isNuevoContenido("Comunidad de aprendizaje", $params["user_id"], $params["business_id"]);
                    }
                    if ($query[$index]["service_name"] == "Juegos") {
                        $query[$index]["image"] = $url . "btn_a_jugar_yastas.png";
                        $query[$index]["new_img"] = $url . "btn_a_jugar_yastas.png";
                        $query[$index]["nuevoContenido"] = 0;
                    }
                    if ($query[$index]["service_name"] == "Muro") {
                        $query[$index]["image"] = $url . "btn_yastas_aldia_yastas.png";
                        $query[$index]["new_img"] = $url . "btn_yastas_aldia_yastas.png";
                        $query[$index]["nuevoContenido"] = 0;
                    }
                    if ($query[$index]["service_name"] == "Podcast") {
                        $query[$index]["image"] = $url . "img_podcast.png";
                        $query[$index]["new_img"] = $url . "img_podcast.png";
                        $query[$index]["nuevoContenido"] = $this->isNuevoContenido("Podcast", $params["user_id"], $params["business_id"]);
                    }
                    if ($query[$index]["service_name"] == "Ranking") {
                        $query[$index]["image"] = $url . "img_ranking.png";
                        $query[$index]["new_img"] = $url . "img_ranking.png";
                        $query[$index]["nuevoContenido"] = 0;
                    }
                    if ($query[$index]["service_name"] == "Q&A") {
                        $query[$index]["image"] = $url . "img_home_btn_Q&A.png";
                        $query[$index]["new_img"] = $url . "img_home_btn_Q&A.png";
                        $query[$index]["nuevoContenido"] = 0;
                    }
                    if ($query[$index]["service_name"] == "Chat") {
                        $query[$index]["image"] = $url . "btn_chat_txt_eng.png";
                        $query[$index]["new_img"] = $url . "btn_chat_txt_eng.png";
                        $query[$index]["nuevoContenido"] = $this->isNuevoContenido("Chat", $params["user_id"], $params["business_id"]);
                    }
                } else if ($params["business_id"] == 83) {
                    if ($query[$index]["service_name"] == "Capacitacion") {
                        $query[$index]["image"] = $url . "btn_cursos_bimbo.png";
                        $query[$index]["new_img"] = $url . "btn_cursos_bimbo.png";
                        $query[$index]["nuevoContenido"] = $this->isNuevoContenido("Capacitacion", $params["user_id"], $params["business_id"]);
                    }
                    if ($query[$index]["service_name"] == "Biblioteca") {
                        $query[$index]["image"] = $url . "btn_biblioteca_bimbo.png";
                        $query[$index]["new_img"] = $url . "btn_biblioteca_bimbo.png";
                        $query[$index]["nuevoContenido"] = $this->isNuevoContenido("Biblioteca", $params["user_id"], $params["business_id"]);
                    }
                    if ($query[$index]["service_name"] == "Preguntas") {
                        $query[$index]["image"] = $url . "btn_evaluaciones_bimbo.png";
                        $query[$index]["new_img"] = $url . "btn_evaluaciones_bimbo.png";
                        $query[$index]["nuevoContenido"] = $this->isNuevoContenido("Preguntas", $params["user_id"], $params["business_id"]);
                    }
                    if ($query[$index]["service_name"] == "Comunidad de aprendizaje") {
                        $query[$index]["image"] = $url . "btn_comunidad_aprendizaje_bimbo.png";
                        $query[$index]["new_img"] = $url . "btn_comunidad_aprendizaje_bimbo.png";
                        $query[$index]["nuevoContenido"] = $this->isNuevoContenido("Comunidad de aprendizaje", $params["user_id"], $params["business_id"]);
                    }
                    if ($query[$index]["service_name"] == "Juegos") {
                        $query[$index]["image"] = $url . "btn_juegos_bimbo.png";
                        $query[$index]["new_img"] = $url . "btn_juegos_bimbo.png";
                        $query[$index]["nuevoContenido"] = 0;
                    }
                    if ($query[$index]["service_name"] == "Muro") {
                        $query[$index]["image"] = $url . "btn_muro_bimbo.png";
                        $query[$index]["new_img"] = $url . "btn_muro_bimbo.png";
                        $query[$index]["nuevoContenido"] = 0;
                    }
                    if ($query[$index]["service_name"] == "Podcast") {
                        $query[$index]["image"] = $url . "btn_podcast_bimbo.png";
                        $query[$index]["new_img"] = $url . "btn_podcast_bimbo.png";
                        $query[$index]["nuevoContenido"] = $this->isNuevoContenido("Podcast", $params["user_id"], $params["business_id"]);
                    }
                    if ($query[$index]["service_name"] == "Ranking") {
                        $query[$index]["image"] = $url . "btn_ranking_bimbo.png";
                        $query[$index]["new_img"] = $url . "btn_ranking_bimbo.png";
                        $query[$index]["nuevoContenido"] = 0;
                    }
                    if ($query[$index]["service_name"] == "Chat") {
                        $query[$index]["image"] = $url . "btn_chat_bimbo.png";
                        $query[$index]["new_img"] = $url . "btn_chat_bimbo.png";
                        $query[$index]["nuevoContenido"] = $this->isNuevoContenido("Chat", $params["user_id"], $params["business_id"]);
                    }
                    if ($query[$index]["service_name"] == "Retos") {
                        $query[$index]["image"] = $url . "btn_retos_bimbo.png";
                        $query[$index]["new_img"] = $url . "btn_retos_bimbo.png";
                        $query[$index]["nuevoContenido"] = 0;
                    }
                } else {
                    $query[$index]['image'] = $url . $value['image'];
                    $query[$index]['new_img'] = $url . $value['new_img'];
                }
            }
            //se elimina preguntas del menu de yastas pero solo en la app
            //en el admin se manda a llamar este metodo en capacitaciones
            //para saber si tiene contratado el servicio y puede agregar
            //elementos a la capacitacion
            if ($params["business_id"] == 18 && !isset($_SESSION['id_user']))
                unset($query[$indice]);
            return $query;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *    Autor: Uriel Sánchez Cervantes   Fecha: 11/12/2020
     *           urisancer@gmail.com
     *    Nota: Eliminar registro de servicio solicitado
     ***********************************************************************/
    public function DeleteHiredService($data)
    {
        if ($this->db->delete($this->tableHiredServices, array('id' => $data["id"]))) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 10/22/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para ocultar o mostrar los servicios
     ***********************************************************************/
    function ServicesHideShow($data)
    {
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 10/22/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Obtenemos la configuracion actual para definir si se activa
         *          o se desactiva
         ***********************************************************************/
        $this->db->select('view, id');
        $this->db->from($this->tableHiredServices);
        $this->db->where(array('business_id' => $data['business_id'], 'services_id' => $data['services_id']));
        $validate = $this->db->get()->result_array();
        $view = intval($validate[0]['view']);
        if ($view === 1) {
            $view = 0;
        } else {
            $view = 1;
        }
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 10/22/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Actualizamos el estatus del view para que se muestre o se
         *          oculte el servicio
         ***********************************************************************/
        return $this->db->update($this->tableHiredServices, array('view' => $view), array('id' => $validate[0]['id']));
    }

    /***********************************************************************
     *    Autor: Uriel Sánchez Cervantes   Fecha: 11/12/2020
     *           urisancer@gmail.com
     *    Nota: Aprobar servicio solicitado
     ***********************************************************************/
    function ApprovePurchaseService($data)
    {

        $servicio = $this->PurchaseServicesList($data);

        if ($servicio) {
            $servicio = $servicio[0];
            $this->DeletePurchaseService($data);

            $hired_service = array(
                "business_id" => $servicio["business_id"],
                "services_id" => $servicio["service_id"],
                "view" => 1
            );

            if ($this->db->insert($this->tableHiredServices, $hired_service)) {
                return true;
            } else {
                return false;
            }
        }
    }

    /***********************************************************************
     *    Autor: Uriel Sánchez Cervantes   Fecha: 11/12/2020
     *           urisancer@gmail.com
     *    Nota: Eliminar registro de servicio solicitado
     ***********************************************************************/
    public function DeletePurchaseService($data)
    {
        if ($this->db->delete($this->tablePurchaseService, array('id' => $data["id"]))) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *    Autor: Uriel Sánchez Cervantes   Fecha: 11/12/2020
     *           urisancer@gmail.com
     *    Nota: Listado de servicios solicitados
     ***********************************************************************/
    function PurchaseServicesList($params)
    {
        $this->db->select('
            spp.id,
            spp.service_id, spp.user_id, spp.business_id,
            s.service_name, s.category_id, s.description,
            b.business_name,
            c.category_name
        ');
        $this->db->from($this->tablePurchaseService . " AS spp");
        $this->db->join($this->tableServices . ' AS s', 'spp.service_id = s.id');
        $this->db->join($this->tableBusiness . ' AS b', 'spp.business_id = b.id');
        $this->db->join($this->tableCategories . ' AS c', 's.category_id = c.id');

        if (isset($params['id'])) {
            $this->db->where('spp.id = ', $params['id']);
        }
        //$this->db->where('spp.business_id = ', $business_id);

        $users = $this->db->get()->result_array();
        if (count($users) > 0) {
            return $users;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *    Autor: Francisco Javier Avalos Prado   Fecha: 08/10/2022
     *           fjavpradev@gmail.com
     *    Nota: Función para saber cuando hay un nuevo contenido por rango de fecha
     *    Parametros: Nombre de sección, usuario_id, business_id
     ***********************************************************************/
    public function isNuevoContenido($seccion, $usuario_id, $business_id)
    {
        $rango_fecha = $this->rangoFechaElementoNuevo();
        if($seccion == "Podcast"){
            $elementos = $this->podcast_mdl->obtenerNuevoContenidoPorRangoFecha($usuario_id, $business_id, $rango_fecha["fecha_inicio"], $rango_fecha["fecha_actual"]);
            return $this->numeroElementosNuevosNoVistos($seccion, $elementos, $usuario_id);
        }else if($seccion == "Biblioteca"){
            $elementos = $this->library_mdl->list_library_app(array('user_id' => $usuario_id, 'business_id' => $business_id, 'fecha_inicio' => $rango_fecha["fecha_inicio"], 'fecha_actual' => $rango_fecha["fecha_actual"], 'asesor_id' => null, 'region_id' => null));
            /* el resultado no viene en arreglo */
            $elementos ? $elementos : $elementos = [];
            return $this->numeroElementosNuevosNoVistos($seccion, $elementos, $usuario_id);
        }else if($seccion == "Capacitacion"){
            $elementos = $this->capacitacion_mdl->getCapacitaciones($usuario_id, $business_id, $rango_fecha["fecha_inicio"], $rango_fecha["fecha_actual"]);
            return $this->numeroElementosNuevosNoVistos($seccion, $elementos, $usuario_id);
        }else if($seccion == "Preguntas"){
            $elementos = $this->questions_mdl->ListQuiz(array("user_id" => $usuario_id, "business_id" => $business_id, "fecha_inicio" => $rango_fecha["fecha_inicio"], "fecha_actual" => $rango_fecha["fecha_actual"]));
            /* el resultado no viene en arreglo */
            $elementos ? $elementos : $elementos = [];
            return $this->numeroElementosNuevosNoVistos($seccion, $elementos, $usuario_id);
        }else if($seccion == "Comunidad de aprendizaje"){
            $elementos = $this->com_mdl->getTopics($usuario_id, null, null, $business_id, $rango_fecha["fecha_inicio"], $rango_fecha["fecha_actual"]);
            /* el resultado no viene en arreglo */
            $elementos ? $elementos : $elementos = [];
            return $this->numeroElementosNuevosNoVistos($seccion, $elementos, $usuario_id);
        }else if($seccion == "Chat"){
            $elementos = $this->chat_mdl->tieneMensajesNuevos($usuario_id);
            /* el resultado no viene en arreglo */
            return $elementos ? $elementos = 1 : $elementos = 0;
        }
        return false;
    }

    /***********************************************************************
     *    Autor: Francisco Javier Avalos Prado   Fecha: 08/10/2022
     *           fjavpradev@gmail.com
     *    Nota: Función para el numero de elementos vistos por el usuario
     ***********************************************************************/
    public function numeroElementosNuevosNoVistos($seccion, $elementos, $usuario_id)
    {
        $element_view = 0;
        if(count($elementos) > 0){
            $test_elemento = 0;
            foreach($elementos as $elemento){
                if($seccion == "Podcast"){
                    $test_elemento = $this->podcast_mdl->valida_visto(array("podcast_id" => $elemento["id"], "user_id" => $usuario_id));
                    /* la respuesta da un arreglo */
                    count($test_elemento) > 0 ? $test_elemento = 1 : $test_elemento = 0;
                }else if($seccion == "Biblioteca"){
                    $test_elemento = $this->library_mdl->obtener_visto($elemento["id"], $usuario_id);
                }else if($seccion == "Capacitacion"){
                    $test_elemento = $this->capacitacion_mdl->obtener_visto($elemento["id"], $usuario_id);
                }else if($seccion == "Preguntas"){
                    $test_elemento = $this->questions_mdl->obtener_visto($elemento["id"], $usuario_id);
                }else if($seccion == "Comunidad de aprendizaje"){
                    $test_elemento = $this->com_mdl->obtener_visto($elemento["id"], $usuario_id);
                }
                /* sumar elemento visto */
                if($test_elemento <= 0){
                    $element_view = $element_view + 1;
                }else{
                    $element_view > 0 ? $element_view = $element_view - 1 : $element_view;
                }
            }
        }
        return $element_view;
    }

    /***********************************************************************
     *    Autor: Francisco Javier Avalos Prado   Fecha: 08/10/2022
     *           fjavpradev@gmail.com
     *    Nota: Función para obtener rango de fechas por inicio de semana
     ***********************************************************************/
    public function rangoFechaElementoNuevo()
    {
        $fecha_actual = date('Y-m-d');
        $dia_semana = date('N');
        if($dia_semana == 1){
            $fecha_inicio = $fecha_actual;
        }else{
            $dia_semana = $dia_semana - 1;
            $fecha_inicio = date("Y-m-d",strtotime($fecha_actual."- {$dia_semana} days"));
        }
        return array(
            "fecha_actual" => $fecha_actual,
            "fecha_inicio" => $fecha_inicio
        );
    }
}