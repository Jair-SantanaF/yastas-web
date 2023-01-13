<?php
class Capacitacion_mdl extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function getCapacitacionesAdmin($userId, $id_business)
    {
        $this->poner_fecha_espanol();
        $query = "SELECT '1' as permitir_hacer,l.id, l.name,l.description,l.active,
        coalesce(date_format(l.fecha_limite,'%d %M %Y'),'') as fecha_limite,
        coalesce(date_format(l.fecha_programada,'%d %M %Y'),'') as fecha_programada,
        concat('" . base_url("uploads/business_$id_business/capacitaciones/") . "',l.image) as image
        FROM capacit_list AS l force index (primary, ind_active)
        where l.business_id = ?
        group by l.id
        order by active desc, id desc
                ";
        $resultado = $this->db->query($query, array($id_business))->result_array();
        if (count($resultado) === 0) {
            return false;
        }

        for ($i = 0; $i < count($resultado); $i++) {
            $resultado[$i]["grupos"] = $this->obtener_grupos($resultado[$i]["id"]);
            $resultado[$i]["usuarios"] = $this->obtener_usuarios($resultado[$i]["id"]);
        }

        return $resultado;
    }

    function obtener_grupos($id_capacitacion)
    {
        $query = "select g.id, g.name from groups as g
                  join capacit_groups as cg on cg.group_id = g.id
                  where cg.capacit_id = ?";
        return $this->db->query($query, array($id_capacitacion))->result_array();
    }

    function obtener_usuarios($id_capacitacion)
    {
        $query = "select u.id, concat(u.name, ' ', u.last_name) as name
                 from user as u
                 join capacit_users as cu on cu.id_user = u.id
                 where cu.id_list = ?";
        return $this->db->query($query, array($id_capacitacion))->result_array();
    }

    function poner_fecha_espanol()
    {
        $query = "set lc_time_names = 'es_MX';";
        $this->db->query($query);
    }

    function getCapacitaciones($userId, $id_business, $fecha_inicio = null, $fecha_actual = null)
    {
        $query = "select * from users_groups where active  = 1 and user_id = ?";
        $grupos = $this->db->query($query, array($userId))->result_array();
        $grupos_ = [];
        for ($i = 0; $i < count($grupos); $i++) {
            array_push($grupos_, $grupos[$i]["group_id"]);
        }
        $this->poner_fecha_espanol();
        $ids_completas = $this->ids_completos_obligatorias($userId, $id_business);
        $where = "";
        if ($ids_completas != "") {
            $where = "and l.id not in ($ids_completas)";
        }
        $fecha = date("Y-m-d");
        $fecha_alta_cliente = $this->obtener_fecha_alta($userId);
        $query = "SELECT '1' as permitir_hacer,l.id, l.name,l.description, coalesce(date_format(l.fecha_limite,'%d %M %Y'),'') as fecha_limite,
                concat('" . base_url("uploads/business_$id_business/capacitaciones/") . "',l.image) as image, tipo
                FROM capacit_list AS l force index (primary, ind_active, ind_fecha_lim, ind_fecha_prog, ind_tipo) 
                LEFT JOIN capacit_users AS cu force index (ind_id_list, ind_id_user) ON (l.id = cu.id_list)
                LEFT JOIN `user` AS u force index (primary) ON (u.id = cu.id_user)
                LEFT JOIN `capacit_groups` as `qg` force index (ind_capacit_id, ind_group_id) ON `qg`.`capacit_id` = `l`.`id`
                WHERE (cu.id_user = ? or qg.group_id in (" . join(",", $grupos_) . "))
                    and l.active = 1
                    and (now() <= l.fecha_limite or l.fecha_limite is null or fecha_limite = '0000-00-00 00:00:00')
                    ".$where."";
        
        /* fecha alta cliente */
        if($fecha_alta_cliente){
            $query .= " and (('".$fecha_alta_cliente."' >= l.fecha_programada and l.fecha_programada <= '".$fecha."') or (l.fecha_programada is null or l.fecha_programada = '0000-00-00 00:00:00'))";
        }else if($fecha_inicio && $fecha_actual){
            /* rango fechas */
            $query .= " AND l.fecha_programada BETWEEN  '".$fecha_inicio.' 00:00:00'."' AND '".$fecha_actual.' 23:59:59'."'";
        }else{
            $query .= " and (l.fecha_programada is null or l.fecha_programada = '0000-00-00 00:00:00')";
        }
        $query .= "group by l.id order by tipo desc";
        $resultado = $this->db->query($query, array($userId))->result_array();
        if (count($resultado) === 0) {
            return [];
        }
        return $resultado;
    }

    function obtener_fecha_alta($user_id)
    {
        $query = "select fecha_alta_cliente from user where id = ?";
        return $this->db->query($query, array($user_id))->result_array()[0]["fecha_alta_cliente"];
    }

    function ids_completos_obligatorias($usuario, $business_id)
    {
        $query = "
        select cl.id,cl.puntos, cl.name,count(*) as num_elementos, cl.tipo, cl.id_insignia
        from capacit_list as cl force index (PRIMARY)
        join capacit_detail as cd force index (ind_id_cap) on cd.id_capacitacion = cl.id
        join capacit_users as cu force index (ind_id_list, ind_id_user) on cu.id_list = cl.id
        where cu.id_user = ?
        group by cl.id
        ";
        $capacitaciones_totales = $this->db->query($query, array($usuario))->result_array();


        $completas = [];

        $query = "select cl.id,cl.puntos, cl.name,count(*) as num_elementos, cl.tipo, cl.id_insignia
        from capacit_list as cl force index (primary)
        join capacit_detail as cd force index (ind_id_cap) on cd.id_capacitacion = cl.id
        join capacit_groups as cu force index (ind_group_id, ind_capacit_id) on cu.capacit_id = cl.id
        join users_groups as ug force index (ind_group_id, ind_user_id) on ug.group_id = cu.group_id
        join user as u force index (primary) on u.id = ug.user_id
        where u.id = ?
        group by cl.id";
        $capacitaciones_totales_grupos = $this->db->query($query, array($usuario))->result_array();

        $capacitaciones_totales = array_merge($capacitaciones_totales, $capacitaciones_totales_grupos);

        for ($i = 0; $i < count($capacitaciones_totales); $i++) {
            $id = $capacitaciones_totales[$i]["id"];
            // echo json_encode($id);
            $nombre = $capacitaciones_totales[$i]["name"];
            $puntos = $capacitaciones_totales[$i]["puntos"];
            $this->db->select("id_capacitacion,count(*) as completas");
            $this->db->from("capacit_completed");
            $this->db->where("id_usuario", $usuario);
            $this->db->where("id_capacitacion", $id);
            //$this->db->where("visto", 0);//se debe habilitar para que no salga siempre
            $this->db->group_by("id_capacitacion");
            $cap = $this->db->get()->result_array();
            // echo json_encode($cap);
            if (count($cap) > 0) {
                // echo json_encode($cap);
                if (($cap[0]["completas"] == $capacitaciones_totales[$i]["num_elementos"])) {
                    if ($capacitaciones_totales[$i]["tipo"] == 1) {
                        $array = $this->getDetail($capacitaciones_totales[$i]["id"], $usuario, $business_id);
                        // echo json_encode($array);
                        $quiz_id = end(end($array["elementos"])["items"])["id"];

                        $calificacion = $this->question_mdl->comprobar_calificacion_puntos($quiz_id, $usuario, true);
                        if ($calificacion >= 80)
                            array_push($completas,  $id);
                    } else {
                        array_push($completas,  $id);
                    }
                }
            }
        }
        return join(",", $completas);
    }

    function save($datos, $business_id)
    {
        $data = array(
            'name' => $datos["nombre"],
            'description' => $datos["descripcion"],
            'image' => $datos["name_image"],
            'fecha_limite' => $datos["fecha_limite"],
            'fecha_programada' => $datos["fecha_programada"],
            'active' => 1,
            "business_id" => $business_id,
            "tipo" => $datos["tipo"]
        );
        $this->db->insert('capacit_list', $data);

        $id_capacitacion = $this->db->insert_id();

        $insignia = array(
            "titulo" => $datos["nombre"],
            "descripcion" => $datos["nombre"],
            "imagen" => "img_diploma.png",
            "imagen_inactivo" => "img_diploma_gris.png",
            "business_id" => $business_id
        );

        $this->db->insert("insignias", $insignia);
        $id_insignia = $this->db->insert_id();

        // echo json_encode($id_insignia);
        $this->db->set("id_insignia", $id_insignia);
        $this->db->where("id", $id_capacitacion);
        $this->db->update("capacit_list");

        // if (isset($datos["usuarios"])) {
        //     $datos["usuarios"] = json_decode($datos["usuarios"], true);
        //     if (is_array($datos["usuarios"]))
        //         for ($i = 0; $i < count($datos["usuarios"]); $i++) {
        //             $id_usuario = $datos["usuarios"][$i]["id"];
        //             $this->agregarUsuario_($id_usuario, $id_capacitacion);
        //         }
        // }
        // if (isset($datos["grupos"])) {
        //     $datos["grupos"] = json_decode($datos["grupos"], true);
        //     if (is_array($datos["grupos"]))
        //         $this->agregarGrupos($datos["grupos"], $id_capacitacion);
        // }

        $datos["elementos_capacitacion"] = json_decode($datos["elementos_capacitacion"], true);

        for ($i = 0; $i < count($datos["elementos_capacitacion"]); $i++) {
            $id_elemento = $datos["elementos_capacitacion"][$i]["id"];
            $id_catalogo = $datos["elementos_capacitacion"][$i]["id_catalogo"];
            $orden = $datos["elementos_capacitacion"][$i]["order"];
            $this->agregarElemento_($id_capacitacion, $id_elemento, $id_catalogo, $orden);
        }



        return $id_capacitacion;
    }

    function actualizarCapacitacion($datos)
    {
        $this->db->set('name', $datos["name"]);
        $this->db->set('description', $datos["description"]);
        $this->db->set('image', $datos["image_name"]);
        $this->db->set('fecha_limite', $datos["fecha_limite"]);
        $this->db->set("fecha_programada", $datos["fecha_programada"]);
        $this->db->where('id', $datos["id"]);
        $result = $this->db->update('capacit_list');
        $elementos = json_decode($datos["elementos_capacitacion"], true);

        $this->actualizarOrden($elementos, $datos["id"]);
        return $result;
    }

    function getCapacitacionByID($id_capacitacion, $id_business)
    {
        $query = "select id, name, description, image, active, date, puntos, business_id,
        date_format(fecha_limite,'%Y-%m-%d') as fecha_limite, id_insignia, tipo,
        date_format(fecha_programada,'%Y-%m-%d') as fecha_programada
          from capacit_list where id = ?";
        $capacitacion = $this->db->query($query, array($id_capacitacion))->result_array()[0];
        $capacitacion["image_name"] = $capacitacion["image"];
        $capacitacion["image"] = base_url("uploads/business_" . $id_business . "/capacitaciones/") . $capacitacion["image"];

        $capacitacion["usuarios"] = $this->obtenerUsuarios($id_capacitacion);
        $capacitacion["grupos"] = $this->obtenerGrupos($id_capacitacion);
        $capacitacion["elementos"] = $this->obtenerElementos($id_capacitacion, $id_business);


        return $capacitacion;
    }

    function getDetailAdmin($capacit_id, $usuario_id, $id_business)
    {
        return $this->getDetail($capacit_id, $usuario_id, $id_business);
    }

    function getDetail($capacit_id, $usuario_id, $id_business)
    {
        $query = "SELECT
                  d.id, d.id_capacitacion,d.id_elemento, cc.catalog as catalog,
                  l.name AS capacitacion,d.order, l.tipo,
                  (SELECT 'true' FROM capacit_completed AS c WHERE c.id_elemento = d.id_elemento
                  AND c.id_usuario = ? and c.id_capacitacion = ? limit 1) AS ejecutado
                  FROM
                  capacit_detail AS d
                  LEFT JOIN capacit_list AS l
                  ON (l.id = d.id_capacitacion)
                  join capacit_categorias as cc on cc.id = d.catalog
                  WHERE
                  d.id_capacitacion = ?
                  order by d.order;";

        $resultado = $this->db->query($query, array($usuario_id, $capacit_id, $capacit_id))->result_array();

        $this->db->select("id,name,tipo,description,if(tipo = 2,1,0) as presencial,concat('" . base_url("uploads/business_" . $id_business . "/capacitaciones/") . "',image) as image");
        $this->db->from("capacit_list");
        $this->db->where("id", $capacit_id);
        $capacitacion = $this->db->get()->result_array()[0];

        $capacitacion["elementos"] = [];

        $query = "select * from capacit_categorias";

        $tipo_elementos = $this->db->query($query)->result_array();

        if (count($resultado) === 0) {
            return false;
        } else {
            foreach ($resultado as $key => $value) {
                $sub_query1 = "(SELECT 'true' FROM capacit_completed AS c WHERE c.id_elemento = le.id AND c.id_usuario = " . $usuario_id . " AND c.catalog = '" . $value["catalog"] . "' and c.id_capacitacion = $capacit_id  limit 1) as ejecutado";
                $sub_query2 = "(SELECT c.fecha FROM capacit_completed AS c WHERE c.id_elemento = le.id AND c.id_usuario = " . $usuario_id . " AND c.catalog = '" . $value["catalog"] . "' and c.id_capacitacion = $capacit_id order by c.fecha desc limit 1) as fecha_ejecutado";
                $sub_query3 = "(SELECT c.actualizado FROM capacit_completed AS c WHERE c.id_elemento = le.id AND c.id_usuario = " . $usuario_id . " AND c.catalog = '" . $value["catalog"] . "'  order by c.fecha desc limit 1) as actualizado";
                $_query = "SELECT le.*," . $sub_query1 . "," . $sub_query2 . "," . $sub_query3 . "
                 FROM " . $value['catalog'] . " as le WHERE id = " . $value['id_elemento'];
                if (count($this->db->query("show COLUMNS FROM " . $value["catalog"] . " LIKE 'active';")->result_array()) > 0) {
                    $_query .= " and le.active = 1";
                }
                $_resultado = $this->db->query($_query)->result_array();
                // echo json_encode($this->db->last_query());
                if (count($_resultado) > 0) {
                    $fecha_hoy = strtotime(date("Y-m-d"));
                    $fecha_mas_meses = strtotime(date("Y-m-d", strtotime($_resultado[0]["fecha_ejecutado"] . "+ 3 month")));
                    if ($fecha_hoy > $fecha_mas_meses || $_resultado[0]["actualizado"] == 1) {
                        // $_resultado[0]["ejecutado"] = true;
                        $_resultado[0]["actualizado"] = true;
                    }

                    if ($_resultado[0]["ejecutado"] == null) {
                        $_resultado[0]["ejecutado"]  = false;
                    }
                    if ($_resultado[0]["actualizado"] == 0) {
                        $_resultado[0]["actualizado"] = false;
                    }

                    $ejecutado = (isset($_resultado[0]["ejecutado"]) && $_resultado[0]["ejecutado"] == true ) ? $_resultado[0]["ejecutado"] : false;

                    if ($value['catalog'] == 'podcast') {
                        if ($_resultado[0]['preview'] !== '') {
                            $_resultado[0]['preview'] = base_url('uploads/business_' . $id_business . '/podcasts/') . $_resultado[0]['preview'];
                        }
                        $_resultado[0]['audio'] = base_url('uploads/business_' . $id_business . '/podcasts/') . $_resultado[0]['audio'];
                    }

                    if ($value['catalog'] == 'library_elements_') {
                        if ($_resultado[0]['image'] !== '') {
                            $_resultado[0]['image'] = base_url('uploads/business_' . $id_business . '/library/') . $_resultado[0]['image'];
                        }
                        switch ($_resultado[0]['type_video']) {
                            case 'servidor':
                                $_resultado[0]['video'] = base_url('uploads/business_' . $id_business . '/library/') . $_resultado[0]['video'];
                                break;
                            case 'youtube':
                                $_resultado[0]['video_id'] = $_resultado[0]['video'];
                                if (!strpos($_resultado[0]["video"], "youtube"))
                                    $_resultado[0]['video'] = 'https://youtu.be/' . $_resultado[0]['video'];
                                // $_resultado[0]['video'] = $_resultado[0]['video'];
                                break;
                            case 'vimeo':
                                $_resultado[0]['video_id'] = $_resultado[0]['video'];
                                $_resultado[0]['video'] = 'https://player.vimeo.com/video/' . $_resultado[0]['video'];
                                break;
                            default:
                        }
                        if (isset($_resultado[0]["file"]) && $_resultado[0]['file'] !== '') {
                            if (!filter_var($_resultado[0]['file'], FILTER_VALIDATE_URL)) {
                                $_resultado[0]['file'] = base_url('uploads/business_' . $id_business . '/library/') . $_resultado[0]['file'];
                            }
                        }
                        // $_resultado[0]['file'] = base_url('uploads/business_' . $id_business . '/library/') . $_resultado[0]['file'];
                    }


                    if (count($_resultado) !== 0) {
                        $indice = array_search($value['catalog'], array_column($tipo_elementos, 'catalog'));
                        $clave = $tipo_elementos[$indice]["label"];
                        $imagen = base_url('uploads/business_' . $id_business . '/categorias_capacitaciones/') . $tipo_elementos[$indice]["imagen"];
                        $descripcion = $tipo_elementos[$indice]["descripcion"];
                        $indice = array_search($clave, array_column($capacitacion["elementos"], 'categoria'));
                        if ($indice === false) {
                            array_push($capacitacion["elementos"], ["categoria" => $clave, "imagen" => $imagen, "descripcion" => $descripcion, "items" => []]);
                            $indice = count($capacitacion["elementos"]) - 1;
                        }
                        if ($value['catalog'] === 'com_topics') {
                            $q = "SELECT u.id, u.name, u.last_name, u.email, u.profile_photo FROM com_users_topics AS cut INNER JOIN user AS u ON (u.id = cut.id_user) WHERE cut.id_topic = " . $_resultado[0]["id"];
                            $users = $this->db->query($q)->result_array();
                            $_resultado[0]["users"] = $users;
                        }
                        if ($value['catalog'] === 'question_quiz') {
                            $_resultado[0] = [
                                "id" => $_resultado[0]["id"],
                                "name" => $_resultado[0]["name"]
                            ];
                        }
                        if ($value['catalog'] === 'games') {
                            $_resultado[0] = [
                                "id" => $_resultado[0]["id"],
                                "name" => $_resultado[0]["name"],
                            ];
                        }
                        // if ($value['catalog'] === 'game_roulette_quiz') {
                        //     $_resultado[0] = [
                        //         "id" => $_resultado[0]["id"],
                        //         "name" => $_resultado[0]["name"],
                        //     ];
                        // }
                        // if ($value['catalog'] === 'game_snake_stairs_active_game') {
                        //     $_resultado[0] = [
                        //         "id" => $_resultado[0]["id"],
                        //         "name" => $_resultado[0]["game_name"],
                        //     ];
                        // }
                        // if ($value['catalog'] === 'profiler_quiz') {
                        //     $_resultado[0] = [
                        //         "id" => $_resultado[0]["id"],
                        //         "name" => $_resultado[0]["history"],
                        //     ];
                        // }
                        if (!isset($_resultado[0]["ejecutado"])) {
                            $_resultado[0]["ejecutado"] = $ejecutado;
                        }
                        // if () {
                        //     if ($ejecutado == true)
                        //         $_resultado[0]["permitir_ver"] = 1;
                        //     else
                        //         $_resultado[0]["permitir_ver"] = 0;
                        // }
                        $_resultado[0]["catalog"] = $value['catalog'];

                        if ($capacitacion["tipo"] == 1) {
                            $_resultado[0]["permitir_ver"] = 0;
                        } else {
                            $_resultado[0]["permitir_ver"] = 1;
                        }

                        array_push($capacitacion["elementos"][$indice]["items"], $_resultado[0]);
                        if ($indice == 0 && $capacitacion["tipo"] == 1) {
                            $longitud = count($capacitacion["elementos"][$indice]["items"]);
                            // echo json_encode($longitud);
                            if ($longitud >= 2) {
                                // echo json_encode("entra");
                                if ($capacitacion["elementos"][$indice]["items"][$longitud - 2]["ejecutado"]) {
                                    $capacitacion["elementos"][$indice]["items"][$longitud - 1]["permitir_ver"] = 1;
                                }
                            }
                        } else if ($capacitacion["tipo"] == 1) {
                            $longitud = count($capacitacion["elementos"][$indice]["items"]);
                            if ($longitud > 0)
                                if ($longitud == 1) {
                                    $longitud_ant = count($capacitacion["elementos"][$indice - 1]["items"]);
                                    if ($capacitacion["elementos"][$indice - 1]["items"][$longitud_ant - 1]["ejecutado"] == true) {
                                        $capacitacion["elementos"][$indice]["items"][0]["permitir_ver"] = 1;
                                    }
                                } else {
                                    if ($capacitacion["elementos"][$indice]["items"][$longitud - 2]["ejecutado"] == true) {
                                        $capacitacion["elementos"][$indice]["items"][$longitud - 1]["permitir_ver"] = 1;
                                    }
                                }
                        }
                    }
                }
            }
        }
        if ($capacitacion["tipo"] == 1) {
            $capacitacion["elementos"][0]["items"][0]["permitir_ver"] = 1;
        }
        return $capacitacion;
    }

    public function asistenciaCursoPresencial($id_user, $id_capacit, $latitud, $longitud, $domicilio){
		/* checar asistencia */
		$query = "SELECT * FROM asistencia_capacitacion WHERE id_capacit = {$id_capacit} AND id_user = {$id_user}";
		$result = $this->db->query($query)->result_array();
		if(count($result) > 0){
			return false;
		}
		/* insertar */
		$fecha = date("Y-m-d h:i:s");
		$query = "INSERT INTO asistencia_capacitacion (direccion, latitud, longitud, id_user, id_capacit, date) 
		VALUES ('{$domicilio}', '{$latitud}', '{$longitud}', {$id_user}, {$id_capacit}, '{$fecha}');";
		return $this->db->query($query);
	}

    function markCompleted($usuario, $elemento, $catalogo)
    {
        $capacitacion_obligatoria = 0;
        // && $catalogo != "question_quiz"//quite esta validacion porque no entraba cuando es cuestionario 
        if ($catalogo != "com_topics" && $catalogo != "question_quiz") {
            $query = "select cl.id, cl.tipo from capacit_list as cl
        join capacit_detail as cd on cd.id_capacitacion = cl.id
        join capacit_categorias as cc on cc.id = cd.catalog
        where cd.id_elemento = ? and cc.catalog = ?";
            // where cd.id_elemento = " . $elemento . " and cd.catalog = '" . $catalogo . "'";

            $capacitaciones = $this->db->query($query, array($elemento, $catalogo))->result_array();
            if (count($capacitaciones))
                $capacitacion_obligatoria = $capacitaciones[0]["tipo"];
            for ($i = 0; $i < count($capacitaciones); $i++) {
                $id_capacitacion = $capacitaciones[$i]["id"];
                $query = "insert into capacit_completed (id_usuario, id_elemento, id_capacitacion,catalog)
            values (?,?,?,?) 
            ON DUPLICATE KEY UPDATE `fecha` = now(), `actualizado` = 0";
                $any = $this->db->query($query, array($usuario, $elemento, $id_capacitacion, $catalogo));
                $this->general_mdl->agregar_recurso_visto($usuario);
            }
        }
        // else if ($catalogo == "question_quiz") {
        //     $query = "select cl.id, cl.tipo from capacit_list as cl
        //     join capacit_detail as cd on cd.id_capacitacion = cl.id
        //     join capacit_categorias as cc on cc.id = cd.catalog
        //     where cd.id_elemento = " . $elemento . " and cc.catalog = '" . $catalogo . "'";
        //     // where cd.id_elemento = " . $elemento . " and cd.catalog = '" . $catalogo . "'";

        //     $capacitaciones = $this->db->query($query)->result_array();
        //     if (count($capacitaciones))
        //         $capacitacion_obligatoria = $capacitaciones[0]["tipo"];
        // }
        // $this->obtenerCapacitacionesCompletas($usuario);
        return ["capacitacion" => $capacitacion_obligatoria];
    }

    function obtenerCapacitacionesCompletas($usuario, $business_id = null, $bandera = null)
    {
        $this->db->select("cl.id,cl.puntos, cl.name,count(*) as num_elementos, cl.tipo, cl.id_insignia");
        $this->db->from("capacit_list as cl");
        $this->db->join("capacit_detail as cd", "cd.id_capacitacion = cl.id");
        $this->db->join("capacit_users as cu", "cu.id_list = cl.id");
        $this->db->where("cu.id_user", $usuario);
        $this->db->group_by("cl.id");
        $capacitaciones_totales = $this->db->get()->result_array();    
        $completas = [];

        $this->db->select("cl.id,cl.puntos, cl.name,count(*) as num_elementos, cl.tipo, cl.id_insignia");
        $this->db->from("capacit_list as cl");
        $this->db->join("capacit_detail as cd", "cd.id_capacitacion = cl.id");
        $this->db->join("capacit_groups as cu", "cu.capacit_id = cl.id");
        $this->db->join("users_groups as ug", "ug.group_id = cu.group_id");
        $this->db->join("user as u", "u.id = ug.user_id");
        $this->db->where("u.id", $usuario);
        $this->db->group_by("cl.id");
        $capacitaciones_totales_grupos = $this->db->get()->result_array();
    
        $capacitaciones_totales = array_merge($capacitaciones_totales, $capacitaciones_totales_grupos);
       
        /* definir numero de elementos de las capacitaciones */
        if(count($capacitaciones_totales) > 0){
            foreach($capacitaciones_totales as $k => $obj){
                /* obtener lista de detalles (elementos) de capacitacion */
                $query = "  SELECT cd.* FROM capacit_detail cd
                            JOIN library_elements_ le
                                ON le.id = cd.id_elemento
                                AND cd.catalog = 1
                                AND le.active = 1
                            WHERE id_capacitacion = {$obj["id"]}";
                $capacitacion_detalles_libreria = $this->db->query($query)->result_array();
                $query = "  SELECT cd.* FROM capacit_detail cd
                            JOIN question_quiz qq
                                ON qq.id = cd.id_elemento
                                AND cd.catalog <> 1
                                AND qq.active = 1 
                            WHERE id_capacitacion = {$obj["id"]}";
                $capacitacion_detalles_quiz = $this->db->query($query)->result_array();
                $capacitacion_detalles = array_merge($capacitacion_detalles_libreria, $capacitacion_detalles_quiz);
                $capacitaciones_totales[$k]["num_elementos"] = $numero_elementos = count($capacitacion_detalles);
            }
        }

        $bandera_vistos = false;

        for ($i = 0; $i < count($capacitaciones_totales); $i++) {            
            $id = $capacitaciones_totales[$i]["id"];
            $nombre = $capacitaciones_totales[$i]["name"];
            $puntos = $capacitaciones_totales[$i]["puntos"];
            $this->db->select("id_capacitacion,count(*) as completas");
            $this->db->from("capacit_completed");
            $this->db->where("id_usuario", $usuario);
            $this->db->where("id_capacitacion", $id);

            $this->db->where("visto", 0); //se debe habilitar para que no salga siempre
            $this->db->group_by("id_capacitacion");
            $cap = $this->db->get()->result_array();
            if (count($cap) > 0) {
                if (($cap[0]["completas"] == $capacitaciones_totales[$i]["num_elementos"])) {
                    if ($capacitaciones_totales[$i]["tipo"] == 0) {
                        $imagen = base_url() . "assets/img/" . $puntos . "_puntos.gif";
                        array_push($completas, ["id" => $id, "puntos" => $puntos, "nombre" => $nombre, "imagen" => $imagen, "id_capacitacion" => $cap[0]["id_capacitacion"], "obligatoria" => 0]);
                    } else {
                        $array = $this->getDetail($capacitaciones_totales[$i]["id"], $usuario, $business_id);

                        $quiz_id = end(end($array["elementos"])["items"])["id"];

                        $calificacion = $this->question_mdl->comprobar_calificacion_puntos($quiz_id, $usuario, true);
                        if ($calificacion >= 80)
                            array_push($completas, ["id" => $id, "nombre" => $nombre, "mensaje" => "¡Felicidades has Aprobado!", "id_capacitacion" => $cap[0]["id_capacitacion"], "calificacion" => $calificacion, "imagen" => base_url() . "assets/img/img_mano_paloma.png", "id_insignia" => $capacitaciones_totales[$i]["id_insignia"], "obligatoria" => 1]);
                        else {
                            $bandera_vistos = true;
                            //$this->eliminar_completos_obligatoria($id, $usuario);
                            array_push($completas, ["id" => $id, "nombre" => $nombre, "mensaje" => "Vuelve a ver los videos y responde de nuevo el Cuestionario.", "id_capacitacion" => $cap[0]["id_capacitacion"], "calificacion" => $calificacion, "imagen" => base_url() . "assets/img/img_mano_tache.png", "id_insignia" => 0, "obligatoria" => 1]);
                        }
                    }
                    $this->general_mdl->ModificarScoreUsuario($usuario, $puntos);
                }
            }
        }

        for ($i = 0; $i < count($completas); $i++) {
            $id_capacitacion = $completas[$i]["id"];
            $this->db->set("visto", 1);
            $this->db->where("id_capacitacion", $id_capacitacion);
            $this->db->where("id_usuario", $usuario);
            $this->db->update("capacit_completed");
        }

        if (count($completas) > 0) {
            for ($i = 0; $i < count($completas); $i++) {
                $this->db->select("id_insignia");
                $this->db->from("capacit_list");
                $this->db->where("id", $completas[$i]["id_capacitacion"]);
                $id_insignia = $this->db->get()->result_array();
                if (count($id_insignia) > 0) {
                    $this->general_mdl->asignarInsignia($id_insignia[0]["id_insignia"], $usuario);
                }
            }
        }
        // if (count($completas) >= 2) {
        //     $this->general_mdl->asignarInsignia(5, $usuario);
        // }
        // if (count($completas) >= 5) {
        //     $this->general_mdl->asignarInsignia(6, $usuario);
        // }

        if ($bandera_vistos == true) {
            $this->eliminar_completos_obligatoria($id, $usuario);
        }

        return $completas;
    }

    function obtener_capacitaciones_empezadas($usuario)
    {
        $query = "select * from capacit_completed as cc
        join capacit_list as cl on cl.id = cc.id_capacitacion
        where cc.id_usuario = ? and cl.tipo = 1";
        return $this->db->query($query, array($usuario))->result_array();
    }

    function eliminar_completos_obligatoria($id_capacitacion, $user_id)
    {
        //no se borran los completos pero se eliminan los vistos de las capacitaciones
        $query = "update capacit_completed set visto = 0 where id_capacitacion = ? and id_usuario = ?";
        // $query = "delete from capacit_completed where id_capacitacion = $id_capacitacion and id_usuario = $user_id";
        $this->db->query($query, array($id_capacitacion, $user_id));
        $query = "delete from capacit_completed where id_capacitacion = ? and id_usuario = ? and catalog = 'question_quiz'";
        $this->db->query($query, array($id_capacitacion, $user_id));
    }

    function deleteCapacitacion($data)
    {
        $query = "select active from capacit_list where id = ?";
        $result = $this->db->query($query, array($data["id"]))->result_array()[0]["active"];

        $set = "";
        if ($result == 1) {
            $set = " active = 0";
        } else {
            $set = "active = 1";
        }
        $query = "update capacit_list set $set where id = ?";
        return $this->db->query($query, array($data["id"]));
        // $key = array(
        //     'id' => $data["id"],
        // );
        // if ($this->db->delete('capacit_list', $key)) {
        //     $key = array(
        //         'id_capacitacion' => $data["id"],
        //     );
        //     $this->db->delete('capacit_detail', $key);
        //     $this->db->delete('capacit_completed', $key);
        //     $key = array(
        //         'id_list' => $data["id"],
        //     );
        //     $this->db->delete('capacit_users', $key);
        //     return true;
        // } else {
        //     return false;
        // }
    }

    function obtenerUsuarios($id_capacitacion)
    {
        $query = "select u.* from user as u
                  join capacit_users as cu on cu.id_user = u.id
                  where cu.id_list = ?;";
        $usuarios = $this->db->query($query, array($id_capacitacion))->result_array();
        return $usuarios;
    }

    function obtenerGrupos($id_capacitacion)
    {
        $query = "select g.* from groups as g
                  join capacit_groups as cg on cg.group_id = g.id
                  where cg.capacit_id = ?;";
        $grupos = $this->db->query($query, array($id_capacitacion))->result_array();
        return $grupos;
    }

    function obtenerElementos($id_capacitacion, $id_business)
    {
        $array = [];
        $query = "select cd.id_elemento,cc.catalog,cc.label,cd.order,cd.catalog as id_catalog from capacit_detail as cd
                  join capacit_categorias as cc on cc.id = cd.catalog
                  where cd.id_capacitacion = ?;";
        $resultados = $this->db->query($query, array($id_capacitacion))->result_array();

        for ($i = 0; $i < count($resultados); $i++) {

            $query = "select * from " . $resultados[$i]["catalog"] . " where id = " . $resultados[$i]["id_elemento"];
            if (count($this->db->query("show COLUMNS FROM " . $resultados[$i]["catalog"] . " LIKE 'active';")->result_array()) > 0) {
                $query .= " and active = 1";
            }
            $resultadoquery = $this->db->query($query)->result_array();

            if (isset($resultadoquery[0]))
                $elemento = $resultadoquery[0];
            else
                $elemento = [];
            $elemento["label"] = $resultados[$i]["label"];
            $elemento["catalog"] = $resultados[$i]["catalog"];
            if ($elemento['catalog'] == 'podcast') {
                if ($elemento['preview'] !== '') {
                    $elemento['preview'] = base_url('uploads/business_' . $id_business . '/podcasts/') . $elemento['preview'];
                }
                $elemento['audio'] = base_url('uploads/business_' . $id_business . '/podcasts/') . $elemento['audio'];
            }

            if ($elemento['catalog'] == 'library_elements_') {
                if (isset($elemento['image']) && $elemento['image'] !== '') {
                    $elemento['image'] = base_url('uploads/business_' . $id_business . '/library/') . $elemento['image'];
                }
                switch (isset($elemento["type_video"]) && $elemento['type_video']) {
                    case 'servidor':
                        $elemento['video'] = base_url('uploads/business_' . $id_business . '/library/') . $elemento['video'];
                        break;
                    case 'youtube':
                        $elemento['video_id'] = $elemento['video'];
                        if (!strpos($elemento["video"], "youtube"))
                            $elemento['video'] = 'https://youtu.be/' . $elemento['video'];
                        // $library[$index]['video'] = $value['video'];
                        break;
                    case 'vimeo':
                        $elemento['video_id'] = $elemento['video'];
                        $elemento['video'] = 'https://player.vimeo.com/video/' . $elemento['video'];
                        break;
                    default:
                }
                if (isset($elemento["file"]) && $elemento['file'] !== '') {
                    if (!filter_var($elemento['file'], FILTER_VALIDATE_URL)) {
                        $elemento['file'] = base_url('uploads/business_' . $id_business . '/library/') . $elemento['file'];
                    }
                }
                // $elemento['file'] = base_url('uploads/business_' . $id_business . '/library/') . $elemento['file'];

            }
            $elemento["order"] = $resultados[$i]["order"];
            $elemento["id_catalog"] = $resultados[$i]["id_catalog"];
            array_push($array, $elemento);
        }
        return $array;
    }

    function agregarUsuario_($id_usuario, $id_capacitacion)
    {
        $data = array(
            'id_user' => $id_usuario,
            'id_list' => $id_capacitacion,
            'date' => date("Y-m-d H:i:s")
        );
        return $this->db->insert('capacit_users', $data);
    }

    function agregarUsuario($id_usuario, $id_capacitacion)
    {
        if ($this->agregarUsuario_($id_usuario, $id_capacitacion)) {
            return $this->obtenerUsuarios($id_capacitacion);
        } else {
            return [];
        }
    }

    function eliminarUsuario($id_usuario, $id_capacitacion)
    {
        $key = array(
            'id_user' => $id_usuario,
            'id_list' => $id_capacitacion
        );
        if ($this->db->delete('capacit_users', $key)) {
            return $this->obtenerUsuarios($id_capacitacion);
        } else {
            return [];
        }
    }

    function agregarElemento_($id_capacitacion, $id_elemento, $catalog, $orden = 0)
    {
        $data = array(
            'id_capacitacion' => $id_capacitacion,
            'id_elemento' => $id_elemento,
            'catalog' => $catalog,
            'order' => $orden
        );
        return $this->db->insert('capacit_detail', $data);
    }

    function agregarElemento($datos, $id_business)
    {
        if ($this->agregarElemento_($datos["id_capacitacion"], $datos["elemento"]["id"], $datos["elemento"]["id_catalogo"])) {
            return $this->obtenerElementos($datos["id_capacitacion"], $id_business);
        } else {
            return [];
        }
    }

    function eliminarElemento($datos, $id_business)
    {
        $key = array(
            'id_elemento' => $datos["id_elemento"],
            'id_capacitacion' => $datos["id_capacitacion"],
            'catalog' => $datos["id_catalogo"]
        );
        if ($this->db->delete('capacit_detail', $key)) {
            return $this->obtenerElementos($datos["id_capacitacion"], $id_business);
        } else {
            return [];
        }
    }

    function eliminarElementoAll($id_elemento, $id_catalogo)
    {
        $this->db->where("id_elemento", $id_elemento);
        $this->db->where("catalog", $id_catalogo);
        return $this->db->delete('capacit_detail');
    }

    function actualizarOrden($elementos, $id_capacitacion)
    {
        for ($i = 0; $i < count($elementos); $i++) {
            $this->db->set("order", $elementos[$i]["order"]);
            $this->db->where("id_capacitacion", $id_capacitacion);
            $this->db->where("id_elemento", $elementos[$i]["id"]);
            $this->db->where("catalog", $elementos[$i]["id_catalog"]);
            $this->db->update("capacit_detail");
        }
        return true;
    }

    function agregarGrupos($capacit_id, $grupos)
    {

        for ($i = 0; $i < count($grupos); $i++) {
            $grupo = [];
            $grupo["group_id"] = $grupos[$i]["id"];
            $grupo["capacit_id"] = $capacit_id;
            $this->agregarGrupo($grupo);
        }
    }

    function agregarGrupo($grupo)
    {
        return $this->db->insert("capacit_groups", $grupo);
    }

    function eliminarGrupo($group_id, $quiz_id)
    {
        $this->db->where("group_id", $group_id);
        $this->db->where("capacit_id", $quiz_id);
        return $this->db->delete("capacit_groups");
    }

    function agregarUsuarios($id_capacitacion, $usuarios)
    {
        for ($i = 0; $i < count($usuarios); $i++) {
            $data = [];
            $data["id_user"] = $usuarios[$i]; //asi para yastas angular, en otras versiones como cemex se busca el id
            $data["id_list"] = $id_capacitacion;
            $data['date'] = date("Y-m-d H:i:s");
            $this->db->insert("capacit_users", $data);
        }
    }

    function obtener_capacitaciones_obligatorias()
    {
        $query = "select id, name from capacit_list where tipo = 1";
        return $this->db->query($query)->result_array();
    }

    public function obtener_visto($id, $user_id)
    {        
        $this->db->select("id");
        $this->db->from("capacit_usage");
        $this->db->where("capacit_id", $id);
        $this->db->where("user_id", $user_id);
        $visto = $this->db->get()->result_array();
        return count($visto) > 0 ? 1 : 0;
    }

    public function SetVisto($data)
    {
        $dataa = array(
            "capacit_id" => $data["capacit_id"],
            "user_id" => $data["user_id"],
        );
        $query = "SELECT * FROM capacit_usage WHERE capacit_id = '".$data["capacit_id"]."' AND user_id = '".$data["user_id"]."'";
        $result = $this->db->query($query)->result_array();
        if(count($result) > 0){
            /* actualizar */
            $veces_visto = $result[0]["veces_visto"] + 1;
            $this->db->set('veces_visto', $veces_visto);
            $this->db->where('id', $result[0]["id"]);
            return $this->db->update('capacit_usage');
        }else{
            /* insertar */
            return $this->db->insert("capacit_usage", $dataa);
        }
    }
}
