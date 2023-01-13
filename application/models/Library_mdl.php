<?php
class Library_mdl extends CI_Model
{
    private $tableLibraryCategory = "library_category",
        $tableSubcategory = "library_subcategory",
        $tableLibraryElements = "library_elements_",
        $tableLibraryUsage = "library_usage",
        $tableQuizQuestions = "question_quiz",
        $tableLibraryGroups = "library_groups",
        $tablePodcasts = "podcast";

    public function __construct()
    {
        parent::__construct();
    }

    public function SaveCategory($data)
    {
        $dataa = array(
            "name" => $data["name"],
            "active" => 1,
            "business_id" => $data["business_id"],
        );
        return $this->db->insert($this->tableLibraryCategory, $dataa);
    }

    public function EditCategory($data)
    {
        $key = array('id' => $data["id"]);

        $dataa = array(
            "name" => $data["name"],
        );

        return $this->db->update($this->tableLibraryCategory, $dataa, $key);
    }

    public function DeleteCategory($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0,
        );

        return $this->db->update($this->tableLibraryCategory, $dataa, $key);
    }

    public function ListCategories($business_id, $es_admin = false)
    {
        $this->db->select('lc.id, lc.name');
        $this->db->from($this->tableLibraryCategory . " as lc");
        $this->db->where('lc.business_id = ', $business_id);
        $this->db->where('lc.active = ', 1);
        $this->db->group_by("lc.id");
        $this->db->order_by('lc.order', 'ASC');

        $categories = $this->db->get()->result_array();
        if (count($categories) > 0) {
            if (($business_id == 13 || $business_id == 18) && !$es_admin)
                array_push($categories, ["id" => 1000000, "name" => "Mejor calificados"]); //solo debe salir en la app
            return $categories;
        } else {
            return false;
        }
    }

    public function SaveSubcategory($data)
    {
        $data['active'] = 1;
        return $this->db->insert($this->tableSubcategory, $data);
    }

    public function EditSubcategory($data)
    {
        $key = array('id' => $data["id"]);
        unset($data['id']);
        return $this->db->update($this->tableSubcategory, $data, $key);
    }

    public function DeleteSubcategory($data)
    {
        $key = array('id' => $data["id"]);
        unset($data['id']);
        return $this->db->update($this->tableSubcategory, array('active' => 0), $key);
    }

    public function ListSubcategory($business_id, $category_id = '')
    {
        $this->db->select('s.id, s.subcategory, s.category_id, a.name as category');
        $this->db->from($this->tableSubcategory . ' s');
        $this->db->join($this->tableLibraryCategory . ' a', 's.category_id = a.id');
        $this->db->where('s.business_id = ', $business_id);
        if ($category_id !== '') {
            $this->db->where('s.category_id = ', $category_id);
        }
        $this->db->where('s.active = ', 1);
        $this->db->group_by("s.id");
        $this->db->order_by('s.order', 'ASC');
        $categories = $this->db->get()->result_array();
        if (count($categories) > 0) {
            return $categories;
        } else {
            return false;
        }
    }

    public function savePodcast($data)
    {
        $dataa = array(
            "title" => $data["title"],
            "description" => $data["description"],
            "etiquetas" => $data["etiquetas"],
            "preview" => $data["preview"],
            "type" => $data["type"],
            "audio" => $data["audio"],
            "duration" => $data["duration"],
            "business_id" => $data["business_id"],
            "date" => date("Y-m-d H:i:s"),
            "fecha_limite" => $data["fecha_limite"],
            "capacitacion_obligatoria" => $data["capacitacion_obligatoria"]
        );

        if ($this->db->insert($this->tablePodcasts, $dataa)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    public function SaveElement($data)
    {
        $dataa = array(
            'title' => $data["title"],
            'text' => $data["text"],
            'etiquetas' => $data["etiquetas"],
            'category_id' => $data["category_id"],
            'subcategory_id' => $data["subcategory_id"],
            'type' => $data["type"],
            'link' => $data["link"],
            'image' => $data["image"],
            'type_video' => $data["type_video"],
            'question' => $data["question"],
            "business_id" => $data["business_id"],
            "active" => 1,
            "date" => date("Y-m-d H:i:s"),
            "fecha_limite" => $data["fecha_limite"],
            "id_region" => $data["id_region"],
            "id_asesor" => $data["id_asesor"],
            "capacitacion_obligatoria" => $data["capacitacion_obligatoria"]
        );

        if ($data['file'] != '') {
            $dataa['file'] = $data['file'];
        }

        if ($data['video'] != '') {
            $dataa['video'] = $data['video'];
        }

        if ($this->db->insert($this->tableLibraryElements, $dataa)) {
            $id_library = $this->db->insert_id();
            // $this->db->update($this->tableQuizQuestions, array('connection_id' => $id_library), array('id' => $data['question']));
            return $id_library;
        } else {
            return false;
        }
    }

    public function editPodcast($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'title' => $data["title"],
            'description' => $data["description"],
            'etiquetas' => $data["etiquetas"],
            'fecha_limite' => $data["fecha_limite"]
        );
        if ($this->db->update($this->tablePodcasts, $dataa, $key)) {
            $this->actualizar_en_capacitacion($data["id"], "podcast");
            return true;
        } else {
            return false;
        }
    }

    function actualizar_en_capacitacion($id_elemento, $catalogo)
    {
        $key = array('id_elemento' => $id_elemento, 'catalog' => $catalogo);
        $dataa = array(
            'actualizado' => 1
        );
        $this->db->update("capacit_completed", $dataa, $key);
    }

    public function EditElement($data)
    {
        $key = array('id' => $data["id"]);

        $dataa = array(
            'title' => $data["title"],
            'text' => $data["text"],
            'etiquetas' => $data["etiquetas"],
            'category_id' => $data["category_id"],
            'subcategory_id' => $data["subcategory_id"],
            'type' => $data["type"],
            'file' => $data["file"],
            'link' => $data["link"],
            'type_video' => $data["type_video"],
            'question' => $data["question"], //,
            'video' => $data["video"],
            "fecha_limite" => $data["fecha_limite"],
            "id_region" => $data["id_region"],
            "id_asesor" => $data["id_asesor"]
        );
        if ($data["type"] == "video") {
            $dataa["file"] = "";
            $dataa["link"] = "";
        } else {
            unset($dataa["file"]);
            unset($dataa["link"]);
        }
        if ($data["type"] == "documento") {
            $dataa["link"] = "";
            $dataa["video"] = "";
        } else {
            unset($dataa["video"]);
            unset($dataa["link"]);
        }
        if ($data["type"] == "imagen") {
            $dataa["video"] = "";
            $dataa["link"] = "";
        } else {
            unset($dataa["video"]);
            unset($dataa["link"]);
        }
        if ($data["type"] == "link") {
            $dataa["file"] = "";
            $dataa["video"] = "";
        } else {
            unset($dataa["file"]);
            unset($dataa["video"]);
        }
        if ($data["image"] !== '') {
            $dataa["image"] = $data["image"];
        } else {
            unset($dataa["image"]);
        }

        if ($data['file'] != '') {
            $dataa['file'] = $data['file'];
        } else {
            unset($dataa["file"]);
        }

        if ($data['video'] != '') {
            $dataa['video'] = $data['video'];
        } else {
            unset($dataa["video"]);
        }

        if ($this->db->update($this->tableLibraryElements, $dataa, $key)) {
            // $this->db->update($this->tableQuizQuestions, array('connection_id' => $data["id"]), array('id' => $data['question']));
            $this->actualizar_en_capacitacion($data["id"], "library_elements_");
            return true;
        } else {
            return false;
        }
    }

    public function deletePodcast($data)
    {
        $this->db->set("active", $data["active"]);
        $this->db->where("id", $data["id"]);
        return $this->db->update("podcast");
    }

    public function DeleteElement($data)
    {
        $key = array('id' => $data["id"]);
        return $this->db->update($this->tableLibraryElements, array('active' => $data["active"]), $key);
    }

    function poner_fecha_español()
    {
        $query = "set lc_time_names = 'es_MX';";
        $this->db->query($query);
    }

    public function ListLibrary($data)
    {
        if (isset($_SESSION['id_user'])) {
            return $this->list_library_admin($data);
        } else {
            return $this->list_library_app($data);
        }
    }

    function list_library_app($data)
    {
        $region_id = $data["region_id"];
        $asesor_id = $data["asesor_id"];
        $group = $this->obtener_grupos_usuario($data);


        $this->poner_fecha_español();

        $library = [];

        $this->db->select('
                    e.id, e.title,e.text, e.type, e.image, e.file, 
                    e.link, e.video, e.type_video, e.category_id, e.etiquetas,
                    ifnull(e.subcategory_id,0) as subcategory_id,
                    c.name as name_category,
                    ifnull(s.subcategory,"") as subcategory,
                    if(e.question="",0,e.question) as question,
                    coalesce(date_format(e.fecha_limite,"%d %M %Y"),"") as fecha_limite,
                    coalesce(date_format(e.date,"%d %M %Y"),"") as fecha_alta,
                    if(avg(ls.score) is null,0,avg(ls.score)) as promedio
                ');
        $this->db->from($this->tableLibraryElements . ' as e');
        $this->db->join($this->tableLibraryCategory . ' as c', 'e.category_id = c.id');
        $this->db->join($this->tableSubcategory . ' as s', 's.id = e.subcategory_id', 'left');
        $this->db->join($this->tableLibraryGroups . ' lg', 'e.id = lg.library_id ', "left");
        $this->db->join("library_users as lu", "lu.library_id = e.id", "left");
        $this->db->join('library_score ls', "ls.library_id = e.id", "left");
        if (!isset($data["ids_faqs"]))
            $this->db->where("(lu.user_id = " . $data['user_id'] . " or lg.group_id in (  $group  ))", null, false);
        $this->db->where('e.active =', 1);
        $this->db->where('e.business_id = ', $data['business_id']);
        $this->db->where("e.capacitacion_obligatoria", 0);
        $this->db->where('(now() <= e.fecha_limite or e.fecha_limite is null or e.fecha_limite = "0000-00-00 00:00:00" )', null, false);

        if (!isset($data["ids_faqs"])) {
            if ($region_id != null && $asesor_id == null)
                $this->db->where("(id_region is null or id_region = $region_id)", null, false);
            else {
                $this->db->where("(id_region is null)", null, false);
            }
            if ($region_id !== null && $asesor_id != null)
                $this->db->where("(id_region is null or (id_region = $region_id and id_asesor = $asesor_id) or (id_region = $region_id and id_asesor is null))", null, false);
        }
        if (isset($data['category_id']) && $data['category_id'] !== '' && $data["category_id"] != 1000000) {
            $this->db->where('e.category_id = ', $data['category_id']);
        }
        if (isset($data['subcategory_id']) && $data['subcategory_id'] !== '') {
            $this->db->where('e.subcategory_id = ', $data['subcategory_id']);
        }
        if (isset($data['id']) && $data['id'] !== '') {
            $this->db->where('e.id = ', $data['id']);
        }

        if (isset($data["ids_faqs"])) {
            $this->db->where_in("e.id", $data["ids_faqs"]);
        }

        $this->db->group_by("e.id");
        if (!isset($data["ids_faqs"])) {
            //$this->db->order_by("e.id", "desc");
        } else {
            //$this->db->order_by("field(e.id," . $data["ids_faqs_str"] . ")");
        }
        /* rango fechas */
        if(isset($data["fecha_inicio"]) && isset($data["fecha_actual"])){
            $this->db->where('e.date >=', $data["fecha_inicio"]." 00:00:00");
            $this->db->where('e.date <=', $data["fecha_actual"]." 23:59:59");
        }

        if (isset($data['category_id']) && $data['category_id'] !== '' && $data['category_id'] == 1000000) {
            $this->db->having("promedio >= 4", null);
            $this->db->limit(10);
        }
        /* fecha ordenada */
        $this->db->order_by("e.date", "DESC");
        $library = $this->db->get()->result_array();
        if (count($library) > 0) {
            foreach ($library as $index => $value) {
                $library[$index]["calificacion"] = $this->obtener_calificacion($library[$index]["id"], $data["user_id"]);
                if (isset($data['category_id']) && $data['category_id'] !== '' && $data['category_id'] == 1000000) {
                    $library[$index]['category_id'] = 1000000;
                    $library[$index]["name_category"] = "Mejor calificados";
                    $library[$index]['subcategory_id'] = 0;
                    $library[$index]['subcategory'] = "";
                }
                $library[$index]['image'] = $this->definir_imagen($value["image"], $data["business_id"]);
                $_video = $this->definir_video($value["video"], $value["type_video"], $data["business_id"]);
                if (isset($_video["video_id"]))
                    $library[$index]['video_id'] = $_video["video_id"];
                if (isset($_video["video"]))
                    $library[$index]['video'] = $_video["video"];
                $library[$index]["file"] = $this->definir_archivo($value["file"], $data["business_id"]);
                $library[$index]["capacitaciones"] = $this->obtener_capacitaciones($library[$index]["id"]);
                $library[$index]["setVisto"] = $this->obtener_visto($library[$index]["id"], $data["user_id"]);
                $library[$index]["retos"] = $this->obtener_retos($library[$index]["id"]);
                $library[$index]["retos"] = $this->obtener_preguntas_retos($library[$index]["retos"]);
                $library[$index]["question"] = $this->validar_quiz_contestado($library[$index]["id"], $data["user_id"]);
            }
            return $library;
        } else {
            return false;
        }
    }

    function validar_quiz_contestado($question, $user_id)
    {
        if ($question) { //agrego esta validacion porque al parecer no todos los elementos tienen cuestionarios
            $result = $this->validar_contestadas($question, $user_id);
            if (count($result) > 0) {
                if ($result[0]["numero_contestadas"] > 0) {
                    $question = 0;
                }
            }
        }
        return $question;
    }

    function obtener_preguntas_retos($retos)
    {
        for ($p = 0; $p < count($retos); $p++) {
            $query = "select * from game_retos_questions as gr 
                where gr.id_reto = ?";
            $retos[$p]["question"] = $this->db->query($query, array($retos[$p]["id"]))->result_array()[0];
            $query = "select * from game_retos_question_answers as gr
                where gr.question_id = ?";
            $retos[$p]["question"]["answers"] = $this->db->query($query, array($retos[$p]["question"]["id"]))->result_array();
        }
        return $retos;
    }

    function list_library_admin($data)
    {
        $region_id = $_SESSION['id_region'];
        $id_user = $_SESSION['id_user'];
        $rol_id = $_SESSION['rol_id'];

        $query = '
        select e.id, e.title, e.text, e.type, e.image, e.file, e.link, e.video, e.type_video, e.category_id,e.active, e.etiquetas,
        ifnull(e.subcategory_id,0) as subcategory_id, c.name as name_category,
        coalesce(date_format(e.fecha_limite,"%Y-%m-%d"),"") as fecha_limite,
        ifnull(s.subcategory,"") as subcategory, ifnull(e.question,if(e.question="",0,e.question)) as question,
        if(avg(ls.score) is null,0,avg(ls.score)) as promedio, if(ls.score is null, 0, ls.score) as calificacion
        from library_elements_ as e force index (primary, ind_category, ind_subcategory, ind_active)
        join library_category as c force index (primary) on e.category_id = c.id
        left join library_subcategory as s force index (primary) on s.id = e.subcategory_id
        left join library_score as ls force index (ind_library_id, ind_user_id) on ls.library_id = e.id and ls.user_id = ?
        where e.business_id = ?
        ';

        if ($rol_id == 6)
            $query .= " and (e.id_region is null or e.id_region = '$region_id')";
        if ($rol_id == 5)
            $query .= " and (e.id_region is null or (e.id_region = '$region_id' and e.id_asesor = '$id_user') or (e.id_region = '$region_id' and e.id_asesor is null))";
        if (isset($data['category_id']) && $data['category_id'] !== '') {
            $query .= " and e.category_id = '" . $data['category_id'] . "'";
        }
        if (isset($data['subcategory_id']) && $data['subcategory_id'] !== '') {
            $query .= " and e.subcategory_id = '" . $data['subcategory_id'] . "'";
        }
        if (isset($data['id']) && $data['id'] !== '') {
            $query .= " and e.id = '" . $data['id'] . "'";
        }

        if (isset($data["filtro"]) && $data['filtro'] == "activo") {
            $query .= " and e.active = 1";
        }
        if (isset($data["filtro"]) && $data['filtro'] == "noactivo") {
            $query .= " and e.active = 0";
        }

        $query .= "group by e.id
        order by e.active desc, e.title asc";

        $library = $this->db->query($query, array($data['user_id'], $data['business_id']))->result_array();

        if (count($library) > 0) {
            foreach ($library as $index => $value) {
                $result = $this->obtener_grupos_library($library[$index]["id"]);
                $library[$index]["usuarios"] = $this->obtener_usuarios_library($library[$index]["id"]);
                $library[$index]["grupos_string"] = $this->concatenar_grupos($result);
                $library[$index]["grupos"] = $this->concatenar_grupos($result); //$result;//temporal en lo que se pasa a angular appy
                $library[$index]["regiones"] = $this->obtener_regiones_library($library[$index]["id"]);
                $library[$index]["asesores"] = $this->obtener_asesores_library($library[$index]["id"]);
                $library[$index]['video_id'] = '';
                $library[$index]['image'] = $this->definir_imagen($value["image"], $data["business_id"]);
                $_video = $this->definir_video($value["video"], $value["type_video"], $data["business_id"]);
                if (isset($_video["video_id"]))
                    $library[$index]['video_id'] = $_video["video_id"];
                if (isset($_video["video"]))
                    $library[$index]['video'] = $_video["video"];
                $library[$index]["file"] = $this->definir_archivo($value["file"], $data["business_id"]);
                $library[$index]['capacitaciones'] = $this->obtener_capacitaciones($library[$index]["id"]);
                $extracto = $this->obtener_extracto($library[$index]["id"]);
                $library[$index]["resumen"] = $extracto["resumen"];
                //$library[$index]["etiquetas"] = $extracto["etiquetas"];
                $library[$index]["id_categoria"] = $extracto["id_categoria"];
            }
            return $library;
        } else {
            return false;
        }
    }

    function obtener_extracto($library_id)
    {
        $query = "select resumen, etiquetas, id_categoria
        from extractos where library_id = ?";
        $result = $this->db->query($query, array($library_id))->result_array();
        if (count($result) > 0) {
            return $result[0];
        }
        return array("resumen" => "", "etiquetas" => "", "id_categoria" => 0);
    }

    function definir_imagen($imagen, $business_id)
    {
        $ruta = "";
        if ($imagen !== '') {
            $ruta = base_url('uploads/business_' . $business_id . '/library/') . $imagen;
        } else {
            $ruta = base_url('assets/img/img_h_biblio.png');
        }
        return $ruta;
    }

    function definir_archivo($file, $business_id)
    {
        $ruta = '';
        if ($file !== '') {
            if (!filter_var($file, FILTER_VALIDATE_URL)) {
                $ruta = base_url('uploads/business_' . $business_id . '/library/') . $file;
            }
        }
        return $ruta;
    }

    function definir_video($video, $tipo, $business_id)
    {
        $data = [];
        switch ($tipo) {
            case 'servidor':
                $data['video'] = base_url('uploads/business_' . $business_id . '/library/') . $video;
                break;
            case 'youtube':
                $data['video_id'] = $video;
                if (!strpos($video, "youtube"))
                    $data['video'] = $video;
                break;
            case 'vimeo':
                $data['video_id'] = $video;
                $data['video'] = 'https://player.vimeo.com/video/' . $video;
                break;
            default:
        }
        return $data;
    }

    function obtener_grupos_library($library_id)
    {
        $this->db->select("g.name, g.id");
        $this->db->from("library_groups as lg");
        $this->db->join("groups as g", "g.id = lg.group_id");
        $this->db->where("lg.library_id", $library_id);
        return $this->db->get()->result_array();
    }

    function concatenar_grupos($result)
    {
        $grupos = "";
        for ($i = 0; $i < count($result); $i++) {
            if ($i != count($result) - 1)
                $grupos .= $result[$i]["name"] . ", ";
            else
                $grupos .= $result[$i]["name"];
        }
        return $grupos;
    }

    function obtener_usuarios_library($library_id)
    {
        $this->db->select("u.id, u.name, u.last_name");
        $this->db->from("user as u");
        $this->db->join("library_users as lu", "lu.user_id = u.id");
        $this->db->where("lu.library_id", $library_id);
        return $this->db->get()->result_array();
    }

    function obtener_asesores_library($library_id)
    {
        $query = "select concat(a.name, ' ', a.last_name) as name,a.id from user as a
        join library_asesores as lr on lr.id_asesor = a.id
        where lr.library_id = ? ";
        return $this->db->query($query, array($library_id))->result_array();
    }

    function obtener_regiones_library($library_id)
    {
        $query = "select r.* from regiones as r
                join library_regiones as lr on lr.id_region = r.id
                where lr.library_id = ?";
        return $this->db->query($query, array($library_id))->result_array();
    }

    function obtener_grupos_usuario($data)
    {
        $query = "select GROUP_CONCAT(ug.group_id) as grupos
        from users_groups as ug
        join groups as g on ug.group_id = g.id
        where ug.user_id = ?
        and g.active = 1 and ug.active = 1";
        $grupos = $this->db->query($query, array($data["user_id"]))->result_array();
        if (count($grupos) > 0) {
            return $grupos[0]["grupos"];
        }
        return "";
    }

    function obtener_calificacion($id, $user_id)
    {
        $query = "select score from library_score
        where library_id = " . $id . " and user_id = " . $user_id;
        $res = $this->db->query($query)->result_array();

        if (count($res) > 0)
            return $res[0]["score"];
        else {
            return 0;
        }
    }

    function obtener_capacitaciones($id)
    {
        $query = "SELECT l.id, l.name
                  FROM capacit_detail AS d
                  JOIN capacit_list AS l ON l.id = d.id_capacitacion
                  JOIN capacit_categorias as cc on cc.id = d.catalog
                  WHERE cc.`catalog` = 'library_elements_' AND d.id_elemento = ?;";

        return $this->db->query($query, array($id))->result_array();
    }

    function obtener_visto($id, $user_id)
    {
        $this->db->select("id");
        $this->db->from("library_usage");
        $this->db->where("library_element_id", $id);
        $this->db->where("user_id", $user_id);
        $visto = $this->db->get()->result_array();
        return count($visto) > 0 ? 1 : 0;
    }

    function obtener_retos($id)
    {
        $query = "select gr.* from game_retos_library as grl
        join game_retos as gr on gr.id = grl.id_reto
         where id_library = ?";
        return $this->db->query($query, array($id))->result_array();
    }

    function validar_contestadas($id, $user_id)
    {
        $query = "select
        qq.id, qq.name,count(q.id) as numero_preguntas, count(qau.id) as numero_contestadas
        from question_quiz as qq
        join questions as q on q.quiz_id = qq.id
        left join question_answer_users as qau on qau.question_id = q.id and user_id = ? and qau.id_elemento = ?
        where quiz_id = ?
        group by qq.id";
        return $this->db->query($query, array($user_id, $id, $id))->result_array();
    }

    public function GetLibraryById($data)
    {
        return $this->list_library_app($data);
    }

    public function QuizLibrary($data)
    {
        $this->db->select('id, name, connection_id');
        $this->db->from($this->tableQuizQuestions);
        $this->db->where('business_id =', $data['business_id']);
        $this->db->where('active =', 1);
        $this->db->where('category_id =', QUIZ_CATEGORY_LIBRARY);
        return $this->db->get()->result_array();
    }

    public function SetVisto($data)
    {
        $dataa = array(
            "veces_visto" => 1,
            "library_element_id" => $data["library_element_id"],
            "user_id" => $data["user_id"],
            "numero_clicks" => $data["numero_clicks"]
        );
        $this->general_mdl->ModificarScoreUsuario($data["user_id"], 1);
        return $this->db->insert($this->tableLibraryUsage, $dataa);
    }

    function agregarUsuarios($id_library, $usuarios)
    {
        for ($i = 0; $i < count($usuarios); $i++) {
            $this->agregarUsuario($id_library, $usuarios[$i]);
        }
    }

    function agregarGrupos($id_library, $grupos)
    {
        for ($i = 0; $i < count($grupos); $i++) {
            $data = [];
            $data["group_id"] = $grupos[$i];
            $data["library_id"] = $id_library;
            $this->db->insert("library_groups", $data);
        }
    }

    function eliminarUsuario($id_elemento, $id_usuario)
    {
        $this->db->where("user_id", $id_usuario);
        $this->db->where("library_id", $id_elemento);
        return $this->db->delete("library_users");
    }

    function agregarUsuario($id_elemento, $id_usuario)
    {
        $data = [];
        $data["user_id"] = $id_usuario;
        $data["library_id"] = $id_elemento;
        return $this->db->insert("library_users", $data);
    }

    function agregarUsuariosPodcast($id_podcast, $usuarios)
    {
        if (is_array($usuarios))
            for ($i = 0; $i < count($usuarios); $i++) {
                $this->agregarUsuarioPodcast($id_podcast, $usuarios[$i]);
            }
    }

    function agregarGruposPodcast($id_library, $grupos)
    {
        for ($i = 0; $i < count($grupos); $i++) {
            $data = [];
            $data["group_id"] = $grupos[$i];
            $data["podcast_id"] = $id_library;
            $this->db->insert("podcast_groups", $data);
        }
    }

    function eliminarUsuarioPodcast($id_podcast, $id_usuario)
    {
        $this->db->where("user_id", $id_usuario);
        $this->db->where("podcast_id", $id_podcast);
        return $this->db->delete("podcast_users");
    }

    function agregarUsuarioPodcast($id_podcast, $id_usuario)
    {
        $data = [];
        $data["user_id"] = $id_usuario;
        $data["podcast_id"] = $id_podcast;
        return $this->db->insert("podcast_users", $data);
    }

    public function CalificarLibrary($data)
    {
        $result = $this->validar_calificado($data);
        if ($result) {
            return $this->actualizar_calificacion($data["score"], $result[0]["id"]);
        } else {
            $this->general_mdl->ModificarScoreUsuario($data["user_id"], 1);
            return $this->insertar_calificacion($data);
        }
    }

    function validar_calificado($data)
    {
        $this->db->select("id");
        $this->db->from("library_score");
        $this->db->where('library_id', $data["library_id"]);
        $this->db->where('user_id', $data["user_id"]);
        return $this->db->get()->result_array();
    }

    function actualizar_calificacion($score, $id)
    {
        $this->db->set('score', $score);
        $this->db->where('id', $id);
        return $this->db->update("library_score");
    }

    function insertar_calificacion($data)
    {
        $dataa = array(
            "score" => $data["score"],
            "library_id" => $data["library_id"],
            "user_id" => $data["user_id"],
        );
        return $this->db->insert("library_score", $dataa);
    }

    public function obtener_biblioteca_capacitacion($business_id)
    {
        $query = "select * from library_elements_
        where business_id = ? and capacitacion_obligatoria = 1";
        return $this->db->query($query, array($business_id))->result_array();
    }

    public function agregarRegiones($library_id, $regiones)
    {
        $this->db->where("library_id", $library_id);
        $this->db->delete("library_regiones");
        for ($i = 0; $i < count($regiones); $i++) {
            $data = [];
            $data["library_id"] = $library_id;
            $data["id_region"] = $regiones[$i];
            $this->db->insert("library_regiones", $data);
        }
        return true;
    }

    public function agregarAsesores($library_id, $asesores)
    {
        $this->db->where("library_id", $library_id);
        $this->db->delete("library_asesores");
        for ($i = 0; $i < count($asesores); $i++) {
            $data = [];
            $data["library_id"] = $library_id;
            $data["id_asesor"] = $asesores[$i];
            $this->db->insert("library_asesores", $data);
        }
        return true;
    }

    public function agregarRegionesPodcast($library_id, $regiones)
    {
        $this->db->where("podcast_id", $library_id);
        $this->db->delete("podcast_regiones");
        if($regiones){
            for ($i = 0; $i < count($regiones); $i++) {
                $data = [];
                $data["podcast_id"] = $library_id;
                $data["id_region"] = $regiones[$i];
                $this->db->insert("podcast_regiones", $data);
            }
        }
        return true;
    }

    public function agregarAsesoresPodcast($library_id, $asesores)
    {
        $this->db->where("podcast_id", $library_id);
        $this->db->delete("podcast_asesores");
        for ($i = 0; $i < count($asesores); $i++) {
            $data = [];
            $data["podcast_id"] = $library_id;
            $data["id_asesor"] = $asesores[$i];
            $this->db->insert("podcast_asesores", $data);
        }
        return true;
    }

    public function sumar_compartido($id_topic, $user_id)
    {
        $data = [];
        $data["user_id"] = $user_id;
        $data["element_id"] = $id_topic;
        $this->db->insert("library_shared", $data);
        return true;
    }
}
