<?php
class Com_mdl extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function validacion_roles($user_id = 0)
    {

        if ($user_id != 0) {
            $id_region = $this->user_model->obtener_region($user_id);
            $id_asesor = $this->user_model->obtener_asesor($user_id);
            $id_rol = $this->user_model->obtener_rol($user_id);
        } else {
            $id_region = $this->session->userdata("id_region");
            $id_asesor = $this->session->userdata("id_user");
            $id_rol = $this->session->userdata("rol_id");
        }
        if ($id_rol == 6) {
            $this->db->where("u.id_asesor", $id_asesor);
        }
        if ($id_rol == 5) {
            $this->db->where("u.id_region", $id_region);
        }
        if ($id_rol == 3) {
            $this->db->where("u.id_asesor", $id_asesor);
        }
    }

    public function validacion_roles_string($user_id = 0)
    {
        if ($user_id != 0) {
            $id_region = $this->user_model->obtener_region($user_id);
            $id_asesor = $this->user_model->obtener_asesor($user_id);
            $id_rol = $this->user_model->obtener_rol($user_id);

            if ($id_rol == 6) {
                $id_asesor = $user_id;
            }
        } else {
            $id_region = $this->session->userdata("id_region");
            $id_asesor = $this->session->userdata("id_user");
            $id_rol = $this->session->userdata("rol_id");
        }
        $query = "";
        if ($id_rol == 6) {
            $query .= " and u.id_asesor = " . $id_asesor;
        }
        if ($id_rol == 5) {
            $query .= " and u.id_region = " . $id_region . " ";
        }
        if ($id_rol == 3 && $id_asesor != null) {
            $query .= " and u.id_asesor = " . $id_asesor;
        }
        return $query;
    }

    public function getTopicsAdmin($filtro, $business_id, $id_region = null, $id_asesor = null)
    {
        $where = "";
        if ($id_region != null)
            $where = " or ct.id_region = $id_region ";
        if ($id_asesor != null) {
            $where = " or (ct.id_region = '$id_region' and (ct.id_asesor = '$id_asesor' or ct.id_asesor is null)) ";
        }

        if (!isset($filtro) || $filtro == "todo" || $filtro == null) {
            $query = "SELECT ct.*
            FROM com_topics AS ct force index (ind_id_user, ind_active, ind_fecha, ind_region)
            join user as u force index (primary, ind_business) on u.id = ct.id_user
            WHERE date_format(date,'%Y-%m-%d') < '2025-06-28' and
             u.business_id =  '" . $business_id . "' and (ct.id_region is null " . $where . ") order by ct.active desc, ct.name asc";
        }
        if (isset($filtro) && $filtro == "activo") {
            $query = "
            SELECT ct.*
            FROM com_topics AS ct force index (ind_id_user, ind_active, ind_fecha, ind_region)
            join user as u force index (primary, ind_business) on u.id = ct.id_user
            WHERE date_format(date,'%Y-%m-%d') < '2025-06-28' and
             ct.active = 1 and u.business_id =  '" . $business_id . "' and (ct.id_region is null " . $where . ") order by ct.active desc, ct.name asc";
        }
        if (isset($filtro) && $filtro == "noactivo") {
            $query = "
            SELECT ct.*
            FROM com_topics AS ct force index (ind_id_user, ind_active, ind_fecha, ind_region)
            join user as u force index (primary, ind_business) on u.id = ct.id_user
            WHERE 
            date_format(date,'%Y-%m-%d') < '2025-06-28' and
            ct.active = 0 and u.business_id =  '" . $business_id . "'
            and (ct.id_region is null " . $where . ") order by ct.active desc, ct.name asc;";
        }

        //nuevas consultas esto debe reemplazar las anteriores pero primero se deben validar que 
        //las comunidades que ya existen tengan sus regiones y asesores asignadas en las tablas de relaciones
        // echo json_encode($query);
        $resultado = $this->db->query($query)->result_array();

        $query = "select ct.*
        from com_topics as ct force index (ind_id_user, ind_active, ind_fecha, ind_region)
        join com_regiones as cr force index(ind_com_id, ind_id_region) on cr.com_id = ct.id
        join regiones as r force index (primary) on cr.id_region = r.id
        join com_asesores  as ca force index (ind_com_id) on ca.com_id = ct.id
        WHERE date_format(date,'%Y-%m-%d') > '2025-06-28' 
                  ";

        if ($this->session->userdata("rol_id") == 2) {
            $query .= " and r.business_id = $business_id ";
        } else if ($this->session->userdata("rol_id") == 6) {

            $query .= " and ca.id_asesor = '" . $_SESSION["id_user"] . "'";
        } else if ($this->session->userdata("rol_id") == 5) {
            $regiones = $this->user_model->obtener_regiones_gerente($_SESSION["id_user"]);
            if (count($regiones) > 0)
                $query .= " and cr.id_region in (" . join(",", $regiones) . ")";
            else
                $query .= "and cr.id_region = 0";
        }
        if (isset($filtro) && $filtro == "noactivo") {
            $query .= " and ct.active = 0";
        } else if (isset($filtro) && $filtro == "activo") {
            $query .= " and ct.active = 1";
        }
        $query .= " group by ct.id order by ct.active desc, ct.name asc";

        $resultado = array_merge($resultado, $this->db->query($query)->result_array());

        for ($i = 0; $i < count($resultado); $i++) {
            //$this->obtener_numero_usuarios($resultado[$i]["id"], $this->session->userdata("id_user"));
            $users_by_topic = $this->com->getUsersByTopic($resultado[$i]['id'], $this->session->userdata("id_user"));
            if ($users_by_topic != false)
                $resultado[$i]["user_count"] = count($users_by_topic);
            else
                $resultado[$i]["user_count"] = 0;
            $grupos = $this->com->obtenerGrupos($resultado[$i]["id"]);
            $resultado[$i]['usuarios'] = $users_by_topic;
            $resultado[$i]["grupos"] = $grupos;
            $resultado[$i]["regiones"] = $this->obtener_regiones($resultado[$i]["id"]);
            $resultado[$i]["asesores"] = $this->obtener_asesores($resultado[$i]["id"]);
        }
        if (count($resultado) === 0) {
            return false;
        }
        return $resultado;
    }

    function obtener_regiones($id_topic)
    {
        $query = "select r.* from regiones as r
        join com_regiones as lr on lr.id_region = r.id
        where lr.com_id = " . $id_topic;
        $regiones = $this->db->query($query)->result_array();
        return $regiones;
    }

    function obtener_asesores($id_topic)
    {
        $query = "select concat(r.name, ' ', r.last_name) as name,r.id from user as r
        join com_asesores as lr on lr.id_asesor = r.id
        where lr.com_id = " . $id_topic;
        return $this->db->query($query)->result_array();
    }

    public function getTopics($userId, $id_region = null, $id_asesor = null, $business_id = null, $fecha_inicio = null, $fecha_actual = null)
    {

        $query = "select * from users_groups where active  = 1 and user_id = " . $userId;
        $grupos = $this->db->query($query)->result_array();
        $grupos_ = [];
        for ($i = 0; $i < count($grupos); $i++) {
            array_push($grupos_, $grupos[$i]["group_id"]);
        }
        $where = "";
        if ($id_region != null)
            $where = " or ct.id_region = $id_region ";
        if ($id_asesor != null) {
            $where = " or (ct.id_region = $id_region and (ct.id_asesor = $id_asesor or ct.id_asesor is null)) ";
        }
        $query = "SELECT
            ct.*
        FROM
            com_users_topics AS cut
            LEFT JOIN com_topics AS ct ON (cut.id_topic = ct.id)
            left join com_groups as cg on cg.com_id = ct.id
        WHERE ct.active = 1 AND (cut.id_user = ? or cg.group_id in (" . join(",", $grupos_) . "))
        and date_format(ct.date,'%Y-%m-%d') < '2025-06-28'
        AND (ct.id_region is null $where)";
        /* rango fechas */
        if($fecha_inicio && $fecha_actual){
            $query .= " AND ct.date BETWEEN  '".$fecha_inicio.' 00:00:00'."' AND '".$fecha_actual.' 23:59:59'."'";
        }
        $query .= "
        group by ct.id
        order by id desc";
        // $this->db->join("quiz_groups as qg", "qg.quiz_id = qq.id","left");
        $resultado = $this->db->query($query, array($userId))->result_array();
        $rol = $this->user_model->obtener_rol($userId);
        $query = "select ct.*
                FROM
                com_users_topics AS cut
                LEFT JOIN com_topics AS ct ON (cut.id_topic = ct.id)
                  join com_regiones as cr on cr.com_id = ct.id
                  join regiones as r on cr.id_region = r.id
                  join com_asesores as ca on ca.com_id = ct.id
                  left join com_groups as cg on cg.com_id = ct.id
                  WHERE date_format(ct.date,'%Y-%m-%d') > '2025-06-28' 
                  and ct.active = 1 AND (cut.id_user = '$userId' or cg.group_id in (" . join(",", $grupos_) . "))
                  ";
        if ($rol == 2) {
            $query .= " and r.business_id = '$business_id' ";
        } else if ($rol == 6) {
            $query .= " and ca.id_asesor = '" . $userId . "'";
        } else if ($rol == 5) {
            $regiones = $this->user_model->obtener_regiones_gerente($userId);
            if (count($regiones) > 0)
                $query .= " and cr.id_region in (" . join(",", $regiones) . ")";
            else
                $query .= "and cr.id_region = 0";
        }
        $query .= " group by ct.id
        order by id desc";
        array_merge($resultado, $this->db->query($query)->result_array());

        for ($i = 0; $i < count($resultado); $i++) {
            $resultado[$i]["user_count"] = $this->obtener_numero_usuarios($resultado[$i]["id"], $userId);
        }
        if (count($resultado) === 0) {
            return false;
        }
        return $resultado;
    }

    public function obtener_numero_usuarios($id_topic, $user_id)
    {
        $query = "
        select count(*) as num from com_groups as cg force index (ind_group_id, ind_com_id) 
        join users_groups as ug force index (ind_group_id, ind_user_id) on ug.group_id = cg.group_id
        join user as u force index (primary, ind_active, ind_es_prueba, ind_password) on u.id = ug.user_id and u.id not in 
        (SELECT cut2.id_user FROM com_users_topics AS cut2 where cut2.id_topic =  ?)
        WHERE cg.com_id = ? and u.password != '123' 
        AND u.password <> '' AND u.password <> 'Ab12345@'
        and u.active = 1 and u.es_prueba = 0 " . $this->validacion_roles_string($user_id) . "
        ";
        $result = $this->db->query($query, array($id_topic, $id_topic))->result_array();
        
        $cant_usuarios = 0;
        if (count($result) > 0)
            $cant_usuarios = $result[0]["num"];
        $query = "select count(*) as num from com_users_topics as cut force index (ind_id_user, ind_id_topic)
        join user as u on u.id = cut.id_user
        WHERE u.active = 1 and u.password != '123' 
        AND u.password <> '' AND u.password <> 'Ab12345@'  
        and u.active = 1 and u.es_prueba = 0 and cut.id_topic = ? " . $this->validacion_roles_string($user_id);
        $result = $this->db->query($query, array($id_topic))->result_array();
        if (count($result) > 0)
            $cant_usuarios += $result[0]["num"];
        return $cant_usuarios;
    }

    public function getTopicById($id_topic, $userId)
    {
        $query = "SELECT
            ct.*,
            (SELECT COUNT(*) FROM com_users_topics AS cut2 WHERE cut2.id_topic = ct.id " . $this->validacion_roles_string($userId) . ") AS user_count
        FROM
            com_users_topics AS cut
            LEFT JOIN com_topics AS ct ON (cut.id_topic = ct.id)
        WHERE ct.active = 1 and ct.id = ?";

        $resultado = $this->db->query($query, array($id_topic))->result_array();
        if (count($resultado) === 0) {
            return false;
        }
        return $resultado;
    }

    public function getUsersByTopic($topic_id, $user_id)
    {
        // $query = "SELECT u.id, u.name, u.last_name, u.email, u.profile_photo FROM com_users_topics AS cut INNER JOIN user AS u ON (u.id = cut.id_user) WHERE cut.id_topic = $topic_id";
        $query = "
        (SELECT u.id, '' as name, '' as last_name, '' as email, u.profile_photo FROM com_users_topics AS cut2
        join user as u on u.id = cut2.id_user
        where cut2.id_topic = ? and u.active = 1 and u.password != '123' and u.es_prueba = 0 " . $this->validacion_roles_string($user_id) . ") 
        union
        (select u.id, '' as name, '' as last_name, '' as email, u.profile_photo  from com_groups as cg 
        join users_groups as ug on ug.group_id = cg.group_id
        join user as u on u.id = ug.user_id and u.id not in (SELECT cut2.id_user FROM com_users_topics AS cut2 where cut2.id_topic = ?)
        WHERE cg.com_id = ? and u.password != '123' and u.active = 1 and u.es_prueba = 0 " . $this->validacion_roles_string($user_id) . "
        )
        ";
        $resultado = $this->db->query($query, array($topic_id, $topic_id, $topic_id))->result_array();
        
        if (count($resultado) === 0) {
            return [];
        }
        return $resultado;
    }

    public function createTopic($topic)
    {
        unset($topic['token']);
        return array(
            "result" => $this->db->insert('com_topics', $topic),
            "id" => $this->db->insert_id(),
        );
    }

    public function editTopic($data)
    {
        $this->db->set('name', $data["name"]);
        $this->db->set('id_region', $data["id_region"]);
        $this->db->set('id_asesor', $data["id_asesor"]);
        $this->db->where('id', $data["id"]);
        return $this->db->update('com_topics');
    }

    public function deleteTopic($data)
    {
        $key = array(
            'id' => $data["id"],
        );
        $this->db->set("active", $data["active"]);
        $this->db->where("id", $data["id"]);
        return $this->db->update("com_topics");
        // if ($this->db->delete('com_topics', $key)) {
        //     $key = array(
        //         'id_topic' => $data["id"],
        //     );
        //     $this->db->delete('com_users_topics', $key);
        //     return true;
        // } else {
        //     return false;
        // }
    }

    public function subscribeToTopic($data)
    {
        if ($this->insert_valido($data)) {
            $subscription = array(
                'id_user' => $data['id_user'],
                'id_topic' => $data['id_topic'],
                'date' => date('Y-m-d H:i:s'),
            );
            return $this->db->insert('com_users_topics', $subscription);
        } else {
            return false;
        }
    }

    public function insert_valido($data)
    {
        $this->db->select("*");
        $this->db->from("com_users_topics");
        $this->db->where("id_user", $data["id_user"]);
        $this->db->where("id_topic", $data["id_topic"]);
        $result = $this->db->get()->result_array();
        return count($result) > 0 ? false : true;
    }

    public function unsubscribeToTopic($data)
    {
        if ($this->db->delete('com_users_topics', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function saveMessage($data)
    {  
        $query = "INSERT INTO com_messages (id_user, id_topic, message, date)
        VALUES ('".$data['id_user']."', '".$data['id_topic']."', HEX(AES_ENCRYPT('{$data['message']}','" . KEY_AES . "')), '".$data['date']."')";
        return $this->db->query($query);
    }

    public function establecerCapacitacionCompleta($id_usuario, $id_topic)
    {
        $query = "select cl.id from capacit_list as cl
        join capacit_detail as cd on cd.id_capacitacion = cl.id
        join capacit_categorias as cc on cc.id = cd.catalog
        where cd.id_elemento = ? and cc.catalog = 'com_topics'";
        // where cd.id_elemento = " . $elemento . " and cd.catalog = '" . $catalogo . "'";

        $capacitaciones = $this->db->query($query,array($id_topic))->result_array();

        for ($i = 0; $i < count($capacitaciones); $i++) {
            $id_capacitacion = $capacitaciones[$i]["id"];
            $query = "insert into capacit_completed (id_usuario, id_elemento, id_capacitacion,catalog)
            values (?,?,?,'com_topics') ON DUPLICATE KEY UPDATE `fecha` = now(), `actualizado` = 0";
            $any = $this->db->query($query, array($id_usuario, $id_topic, $id_capacitacion));
            $this->general_mdl->agregar_recurso_visto($id_usuario);
        }
    }


    public function getMessages($data)
    {
        $query = "SELECT u.name, u.profile_photo, m.id, m.id_user, m.id_topic, m.date,
        case when AES_DECRYPT(UNHEX(m.message),'" . KEY_AES . "') IS NULL or AES_DECRYPT(UNHEX(m.message),'" . KEY_AES . "') = ''
            then m.message
            else AES_DECRYPT(UNHEX(m.message),'" . KEY_AES . "')
        end as message,
        count(cml.id) as numero_likes
        FROM com_messages AS m 
        INNER JOIN user AS u ON (u.id = m.id_user)
        left join com_message_like as cml on cml.message_id = m.id
        WHERE m.id_topic = ?
        group by m.id
        order by id ";
        $resultado = $this->db->query($query,array($data["id_topic"]))->result_array();
        if (count($resultado) > 0) {
            for ($i = 0; $i < count($resultado); $i++) {
                $query = "select * from com_message_like where user_id = ? and message_id = ?" ;
                $result = $this->db->query($query, array($data["id_user"], $resultado[$i]["id"]))->result_array();
                if ($result) {
                    $resultado[$i]["validate_like"] = 1;
                } else {
                    $resultado[$i]["validate_like"] = 0;
                }
            }
            return $resultado;
        }
        return false;
    }

    public function getNumeroMensajes($user_id, $topic_id)
    {
        $query = "
        select count(*) numero_mensajes from com_messages
        where id_user = ?
        and id_topic  = ?
        group by id_user, id_topic
        ";
        $resultado = $this->db->query($query, array($user_id, $topic_id))->result_array();
        if (count($resultado) > 0) {
            return $resultado[0]["numero_mensajes"];
        } else {
            return 0;
        }
    }

    function agregarGrupos($grupos, $quiz_id)
    {
        for ($i = 0; $i < count($grupos); $i++) {
            $grupo = [];
            $grupo["group_id"] = $grupos[$i];
            $grupo["com_id"] = $quiz_id;
            $this->agregarGrupo($grupo);
        }
    }

    function agregarGrupo($grupo)
    {
        return $this->db->insert("com_groups", $grupo);
    }

    function eliminarGrupo($group_id, $quiz_id)
    {
        $this->db->where("group_id", $group_id);
        $this->db->where("com_id", $quiz_id);
        return $this->db->delete("com_groups");
    }

    function obtenerGrupos($id_com)
    {
        $query = "select g.* from groups as g
                  join com_groups as cg on cg.group_id = g.id
                  where cg.com_id = ? ORDER BY g.active DESC;";
        $grupos = $this->db->query($query, array($id_com))->result_array();
        return $grupos;
    }

    function SaveLikeMessage($data)
    {
        $where = array("user_id" => $data["user_id"], "message_id" => $data["message_id"]);
        $validacion = $this->db->get_where("com_message_like", $where)->result_array();
        if (count($validacion) == 0) {
            if ($this->db->insert("com_message_like", $data)) {
                $this->general_mdl->ModificarScoreUsuario($data["user_id"], 1);
                return true;
            } else {
                return false;
            }
        } else {
            if ($this->db->delete("com_message_like", $data)) {
                $this->general_mdl->ModificarScoreUsuario($data["user_id"], -1);
                return true;
            } else {
                return false;
            }
        }
    }

    function obtener_comunidades_capacitacion($business_id)
    {
        $query = "select * from com_topics as ct
        join user as u on u.id = ct.user_id 
        where u.business_id = ? and ct.capacitacion_obligatoria = 1";
        return $this->db->query($query, array($business_id))->result_array();
    }

    function agregar_regiones($id, $regiones)
    {
        $this->db->where("com_id", $id);
        $this->db->delete("com_regiones");
        for ($i = 0; $i < count($regiones); $i++) {
            $data = [];
            $data["com_id"] = $id;
            $data["id_region"] = $regiones[$i];
            $this->db->insert("com_regiones", $data);
        }
    }

    function agregar_asesores($id, $asesores)
    {
        $this->db->where("com_id", $id);
        $this->db->delete("com_asesores");
        for ($i = 0; $i < count($asesores); $i++) {
            $data = [];
            $data["com_id"] = $id;
            $data["id_asesor"] = $asesores[$i];
            $this->db->insert("com_asesores", $data);
        }
    }

    function obtener_visto($id, $user_id)
    {        
        $this->db->select("id");
        $this->db->from("com_usage");
        $this->db->where("com_id", $id);
        $this->db->where("user_id", $user_id);
        $visto = $this->db->get()->result_array();
        return count($visto) > 0 ? 1 : 0;
    }

    public function SetVisto($data)
    {
        $dataa = array(
            "com_id" => $data["com_id"],
            "user_id" => $data["user_id"],
        );
        $query = "SELECT * FROM com_usage WHERE com_id = '".$data["com_id"]."' AND user_id = '".$data["user_id"]."'";
        $result = $this->db->query($query)->result_array();
        if(count($result) > 0){
            /* actualizar */
            $veces_visto = $result[0]["veces_visto"] + 1;
            $this->db->set('veces_visto', $veces_visto);
            $this->db->where('id', $result[0]["id"]);
            return $this->db->update('com_usage');
        }else{
            /* insertar */
            return $this->db->insert("com_usage", $dataa);
        }
    }
}
