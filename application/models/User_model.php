<?php
class User_model extends CI_Model
{

    private $tableName = "user",
        $tableInvitation = 'invitation',
        $tableConfiguration = 'configuration',
        $tableNumbersEmployees = 'numbers_employees',
        $tableJobs = 'jobs',
        $tableCountries = 'countries',
        $tableStates = 'states',
        $tableActivites = 'activities',
        $tablePriorityCrop = 'priority_crop',
        $tableGroups = 'groups',
        $tableUsersGroups = 'users_groups',
        $tableAreas = 'areas';

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
        if ($id_rol == 3) {
            $query .= " and u.id_asesor = " . $id_asesor;
        }
        return $query;
    }

    function __construct()
    {
        parent::__construct();
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para editar un usuario
     ***********************************************************************/
    function EditUser($data)
    {
        $key = array('id' => $data["id"]);
        if (isset($data["password"]) && $data["password"] != "") {
            $password = $data["password"];
            $query = "UPDATE user SET password = AES_ENCRYPT('" . $password . "', '" . KEY_AES . "') WHERE id = " . $data["id"];
            $this->db->query($query);
        } else {
            unset($data['id']);
        }
        unset($data["created_at"]);
        unset($data["password"]);
        if ($this->db->update($this->tableName, $data, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar un invitado
     ***********************************************************************/
    function DeleteUser($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableName, $dataa, $key)) {
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/10/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Obtenemos el email del usuario registrado para poder identificar
             *          su invitacion
             ***********************************************************************/
            $number_employee = $this->db->get_where($this->tableName, $key)->result_array();
            $number_employee = $number_employee[0]['number_employee'];
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/10/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Una vez que el usuario se elimina tambien se elimina su
             *          invitacion.
             ***********************************************************************/
            $this->db->update($this->tableInvitation, $dataa, array('number_employee' => $number_employee));
            return true;
        } else {
            return false;
        }
    }

    //usuarios de la empresa registrados y sin registrar
    //se usa para asignar materiales, por eso no importa si aun no se registran
    //
    function UserListAll($params)
    {
        // u.*,
        $this->db->select('u.id,concat("w",u.number_employee) as number_employee,concat(u.name," ",u.last_name) as name');
        $this->db->from($this->tableName . " AS u");
        $this->db->join("invitation as i", "i.number_employee = u.number_employee", "left");
        $this->db->where('u.active', 1);
        // $this->db->where('u.password !=', 123);
        // $this->db->where("u.es_prueba", 0);

        if ($this->session->userdata("rol_id") == 8 || $params["asesores"] == true)
            $this->db->where("u.es_prueba", 2);
        else
            $this->db->where("u.es_prueba", 0);

        // $this->db->where("(i.status = 1 or i.status is null)", null, false);

        if (isset($params['business_id']) && $params['business_id'] != '')
            $this->db->where('u.business_id = ', $params['business_id']);

        if (($this->session->userdata("rol_id") == 6) && $params["asesores"] != true) {
            $id_asesor = $this->session->userdata("id_user");
            if ($id_asesor == null) {
                $id_asesor = $params["user_id"];
            }
            $this->db->where("u.id_asesor", $id_asesor);
            $this->db->or_where("u.id", $id_asesor);
        }

        if ($this->session->userdata("rol_id") == 5 && $params["asesores"] != true) {
            $region = $this->session->userdata("id_region");
            if ($region == null) {
                $query = "select id_region from user where id = " . $params["id_user"];
                $result = $this->db->query($query)->result_array()[0]["id_region"];
                $region = $result;
            }
            $this->db->where("u.id_region", $region);
        }
        $this->db->group_by("u.id");
        $this->db->order_by("u.id desc");
        $users = $this->db->get()->result_array();

        if (count($users) > 0) {
            return $users;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para obtener los usuarios
     ***********************************************************************/
    function UserList($params)
    {
        $query = "
        select concat('w',u.number_employee) as number_employee,u.id,u.job_id,
        date_format(u.created_at,'%d-%m-%Y') as fecha_registro,ifnull(j.job_name,'Sin puesto') as job_name,  
        CONCAT(u.name, ' ', u.last_name) AS full_name,u.register_no_invitation,u.name, u.last_name,u.email,
        u.phone, u.score,u.id_comercio,date_format(u.fecha_alta_cliente,'%Y-%m-%d') as fecha_alta_cliente
        from user as u force index (PRIMARY, ind_es_prueba, ind_numb_emp, ind_business, ind_active)
        left join jobs as j force index (primary) on u.job_id = j.id 
        join invitation as i force index (PRIMARY, ind_numb_emp,ind_status) on i.number_employee = u.number_employee
        and u.active = 1 
        and u.password != 123
        and (i.status = 1  or i.status is null)
        ";

        if ($this->session->userdata("rol_id") == 8)
            $query .= " and u.es_prueba = 2";
        else
            $query .= " and u.es_prueba = 0";

        if (isset($params['business_id']) && $params['business_id'] != '')
            $query .= " and u.business_id = " . $params["business_id"];
        if (isset($params['job_id']) && $params['job_id'] != '')
            $query .= " and u.job = " . $params["job_id"];
        if (isset($params['user_id']) && $params["user_id"] != '') {
            $query .= " and u.id = " . $params["user_id"];
        }
        if (isset($params['group_id']) && $params["group_id"] != '') {
            $query .= " and ug.group_id = " . $params["group_id"];
        }

        if ($this->session->userdata("rol_id") == 6 || (isset($params["rol_id"]) && $params["rol_id"] == 6)) {
            $id_asesor = $this->session->userdata("id_user") != null ? $this->session->userdata("id_user") : $params["rol_id"];
            if ($id_asesor == null) {
                $id_asesor = $params["user_id"];
            }
            $query .= " and (u.id_asesor = $id_asesor or u.id = $id_asesor)";
        }

        if ($this->session->userdata("rol_id") == 5) {
            $region = $this->session->userdata("id_region");
            if ($region == null) {
                $query = "select id_region from user where id = " . $params["id_user"];
                $result = $this->db->query($query)->result_array()[0]["id_region"];
                $region = $result;
            }
            $query .= " and u.id_region = $region";
        }

        if ($this->session->userdata("rol_id") == 3) {
            $id_asesor = ""; //$this->session->userdata("id_asesor");
            if ($region == null) {
                $query = "select id_asesor from user where id = " . $params["id_user"];
                $result = $this->db->query($query)->result_array()[0]["id_asesor"];
                $id_asesor = $result;
            }
            $query .= " and u.id_asesor = $id_asesor";
        }
        $query .= " group by u.id";
        $query .= " order by u.id desc";
        $users = $this->db->query($query)->result_array();

        for ($i = 0; $i < count($users); $i++) {
            if (isset($users[$i])) {
                $id_usuario = $users[$i]["id"];
                $query = "select g.name
                from groups as g force index (primary)
                join users_groups as ug force index (ind_group_id, ind_user_id) on ug.group_id = g.id
                where ug.user_id = $id_usuario";
                $grupos = $this->db->query($query)->result_array();
                $users[$i]["grupos"] = "";
                for ($j = 0; $j < count($grupos); $j++) {
                    $users[$i]["grupos"] .= $grupos[$j]["name"] . ", ";
                }
            }
        }

        if (count($users) > 0) {
            return $users;
        } else {
            return false;
        }
    }

    function UserListCsv($params)
    {
        // u.*,
        $this->db->select('u.id,concat("w",u.number_employee) as number_employee,u.name, u.last_name,date_format(u.created_at,"%d-%m-%Y") as fecha_registro,u.email,ifnull(j.job_name,"Sin puesto") as job_name, u.phone , u.score');
        $this->db->from($this->tableName . " AS u");
        $this->db->join('jobs AS j', 'u.job_id = j.id', 'left');
        $this->db->join("invitation as i", "i.number_employee = u.number_employee", "left");
        $this->db->where('u.active = ', 1);
        $this->db->where("(i.status = 1 or i.status is null)", null, false);
        $this->db->where("u.password !=", 123);

        if ($this->session->userdata("rol_id") == 8)
            $this->db->where("u.es_prueba", 2);
        else
            $this->db->where("u.es_prueba", 0);

        if (isset($params['business_id']) && $params['business_id'] != '')
            $this->db->where('u.business_id = ', $params['business_id']);
        if (isset($params['job_id']) && $params['job_id'] != '')
            $this->db->where('u.job_id = ', $params['job_id']);
        if (isset($params['user_id']) && $params["user_id"] != '') {
            $this->db->where('u.id != ', $params['user_id']);
        }
        if (isset($params['group_id']) && $params["group_id"] != '') {
            $this->db->where("ug.group_id", $params["group_id"]);
        }
        $this->db->group_by("u.id");
        $this->db->order_by("u.id desc");
        $users = $this->db->get()->result_array();
        // echo json_encode($users);
        // echo json_encode($this->db->last_query());
        // echo json_encode(count($users));
        for ($i = 0; $i < count($users); $i++) {
            if (isset($users[$i])) {
                $id_usuario = $users[$i]["id"];
                $users[$i]["number_employee"] = "# " .  substr($users[$i]["number_employee"], 1);
                $this->db->select("g.name");
                $this->db->from("groups as g");
                $this->db->join("users_groups as ug", "ug.group_id = g.id");
                $this->db->where("ug.user_id", $id_usuario);
                $grupos = $this->db->get()->result_array();
                $users[$i]["grupos"] = "";
                for ($j = 0; $j < count($grupos); $j++) {
                    $users[$i]["grupos"] .= $grupos[$j]["name"] . ", ";
                }
                unset($users[$i]["id"]);
            }
        }

        if (count($users) > 0) {
            return $users;
        } else {
            return false;
        }
    }


    function ObtenerUsuariosAdmin($business_id)
    {
        $this->db->select('u.id,u.number_employee,date_format(u.created_at,"%d-%m-%Y") as fecha_registro, u.*, CONCAT(u.name, " ", u.last_name) AS full_name,u.register_no_invitation');
        $this->db->from($this->tableName . " AS u");
        // $this->db->join('jobs AS j', 'u.job_id = j.id', 'left');
        // $this->db->join('users_groups as ug', 'u.id = ug.user_id', 'left');
        $this->db->where('u.active = ', 1);
        $this->db->where('u.rol_id', 1);

        $this->db->where('u.business_id = ', $business_id);
        // if (isset($params['job_id']) && $params['job_id'] != '')
        //     $this->db->where('u.job_id = ', $params['job_id']);
        // if (isset($params['user_id']) && $params["user_id"] != '') {
        //     $this->db->where('u.id != ', $params['user_id']);
        // }
        // if (isset($params['group_id']) && $params["group_id"] != '') {
        //     $this->db->where("ug.group_id", $params["group_id"]);
        // }
        $users = $this->db->get()->result_array();
        // echo json_encode(count($users));
        // for ($i = 0; $i < count($users); $i++) {
        //     if (isset($users[$i])) {
        //         $id_usuario = $users[$i]["id"];
        //         $this->db->select("g.name");
        //         $this->db->from("groups as g");
        //         $this->db->join("users_groups as ug", "ug.group_id = g.id");
        //         $this->db->where("ug.user_id", $id_usuario);
        //         $grupos = $this->db->get()->result_array();
        //         $users[$i]["grupos"] = "";
        //         for ($j = 0; $j < count($grupos); $j++) {
        //             $users[$i]["grupos"] .= $grupos[$j]["name"] . ", ";
        //         }
        //     }
        // }

        if (count($users) > 0) {
            return $users;
        } else {
            return false;
        }
    }

    function crear_admin($datos)
    {
        $query = "insert into user (name, last_name, email, password,rol_id,business_id) values ('" . $datos['name'] . "','" . $datos["last_name"] . "','" . $datos["email"] . "',aes_encrypt('" . $datos["password"] . "','" . KEY_AES . "'),1," . $datos["business_id"] . ")";
        $this->db->query($query);
        return true;
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para obtener los puestos
     ***********************************************************************/
    function JobList($params)
    {
        $this->db->select('j.*');
        $this->db->from("jobs j");
        $this->db->where('j.active = ', 1);
        $this->db->where('j.business_id = ', $params['business_id']);

        $users = $this->db->get()->result_array();
        if (count($users) > 0) {
            return $users;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar un invitado
     ***********************************************************************/
    function DeleteInvited($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );
        $query = "select number_employee from invitation where id =" . $data["id"];
        $number_employee = $this->db->query($query)->result_array($query)[0]["number_employee"];
        if ($this->db->update($this->tableInvitation, $dataa, $key)) {
            $this->db->update("user", $dataa, array("number_employee" => $number_employee));
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para obtener los invitados
     ***********************************************************************/
    function InvitedList($business_id, $rol_id = null, $id_asesor = null, $id_region = null, $tipo = null)
    {
        $query = "
        select i.id, i.email,i.business_id,i.status, i.active, i.group_id, i.name, i.last_name,
        concat('# ',i.number_employee) as number_employee, u.id_asesor,coalesce(a.number_employee,'') as asesor,
        coalesce(r.nombre,'') as region, u.id_region,date_format(u.fecha_alta_cliente,'%Y-%m-%d') as fecha_alta_cliente,
        u.phone, u.id_comercio, u.job_id
        from invitation as i force index (primary, ind_numb_emp, ind_status)
        join user as u force index (primary, ind_numb_emp, ind_es_prueba, ind_region_id, ind_asesor_id) on u.number_employee = i.number_employee
        left join user as a force index (primary) on a.id = u.id_asesor
        left join regiones as r force index (primary) on r.id = u.id_region
        where i.active = 1 and i.status = 0 and i.business_id = $business_id
        ";

        if($tipo != null){
            $query .= " and i.tipo = $tipo ";
            $query .= " and i.id_creador = $id_asesor ";
        }

        if ($this->session->userdata("rol_id") == 8)
            $query .= " and u.es_prueba = 2";
        else
            $query .= " and u.es_prueba = 0";
        //validacion para obtener solo las invitaciones que corresponden a la region o al asesor
        if ($this->session->userdata("rol_id") == 6 || $rol_id == 6) {
            $id_asesor = $this->session->userdata("id_user") != null ? $this->session->userdata("id_user") : $id_asesor;
            $query .= " and u.id_asesor = $id_asesor";
            
        }
        if ($this->session->userdata("rol_id") == 5 || $rol_id == 5) {
            $region = $this->session->userdata("id_region");
            if($id_region != null){
                $region = $id_region;
            }
            $query .= " and u.id_region = $region";
        }
        $query .= " order by i.id desc";
        
        $users = $this->db->query($query)->result_array();

        if (count($users) > 0) {
            return $users;
        } else {
            return false;
        }
    }

    public function puntos_por_usuario($business_id)
    {
        $query = "
            select concat(name,' ', last_name) as name, concat('#', number_employee) as numero_empleado, score
            from user
            where business_id = $business_id and active = 1 and password != '123'
        ";
        //esta validacion debe quedar reutilizable
        if ($this->session->userdata("rol_id") == 8)
            $query .= " and es_prueba = 2 ";
        else
            $query .= " and es_prueba = 0 ";
        $result = $this->db->query($query)->result_array();
        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    public function insert($entity)
    {
        if ($this->db->insert($this->tableName, $entity)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    public function update($id, $entity)
    {
        unset($entity["created_at"]);
        if ($this->db->update($this->tableName, $entity, array("id" => $id))) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        if ($this->db->delete($this->tableName, array('id' => $id))) {
            return true;
        } else {
            return false;
        }
    }

    public function fetchAll($id_usuario = null)
    {
        return $this->db->get($this->tableName)->result_array();
    }

    public function fetchAllById($id)
    {
        return $this->db->get_where($this->tableName, array("id" => $id))->result_array();
    }

    public function mailExists($email)
    {
        $reuslt = $this->db->get_where($this->tableName, array("email" => $email))->result_array();

        if (empty($reuslt)) {
            return false;
        } else {
            return true;
        }
    }

    function ValidarInvitacionDuplicada($email, $number_employee, $id = null)
    {
        // ) and id != '" . $id . "'
        $query = "select id from invitation where number_employee = '" . $number_employee . "'";
        if ($id != null) {
            $query .= " and id != " . $id;
        }
        $validacion = $this->db->query($query)->result_array();
        if (count($validacion) > 0) {
            return false;
        }
        return true;
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 15/05/2020
     *	Nota: Funcion para validar si la cuenta de correo enviada se
     *          encuentra como invitado.
     ***********************************************************************/
    function ValidateInvitation($email, $number_employee)
    {
        // $validate = "select i.*,b.business_name from invitation i join business b on i.business_id = b.id where i.email = '$email' and i.last_name = '$last_name' and i.number_employee = '$number_employee' and i.active = 1";
        $validate = "select i.*,b.business_name from invitation i join business b on i.business_id = b.id where i.number_employee = '$number_employee'"; // and i.active = 1
        $validate = $this->db->query($validate)->result_array();
        // echo json_encode($validate);
        // if (count($validate) > 0) {
        //     if ($validate[0]['status'] == 1) {
        //         $validate = 'in_use';
        //     } else {
        //         $validate = 'existe';
        //     }
        //     // $validate = $validate[0];
        // } else {
        //     $validate = false;
        // }
        // echo json_encode($validate);

        // if ($validate) {
        //     /***********************************************************************
        //      *	Autor: Mario Adrián Martínez Fernández   Fecha: 21/09/2020
        //      *		   mario.martinez.f@hotmail.es
        //      *	Nota: Obtenemos el plan que actualmente tiene contratado
        //      ***********************************************************************/
        //     $this->db->select('id, name, value');
        //     $this->db->from($this->tableConfiguration);
        //     $this->db->where('name =', 'plan');
        //     $this->db->where('business_id =', $validate['business_id']);
        //     $plan = $this->db->get()->result_array();
        //     $plan_id = $plan[0]['value'];
        //     if ($plan_id != PAQUETE_SIN_RESTICCIONES) {
        //         $this->db->select('*');
        //         $this->db->from('plans');
        //         $this->db->where('id =', $plan_id);
        //         $plan_detalle = $this->db->get()->result_array();
        //         $max_user = $plan_detalle[0]['num_users'];
        //         /***********************************************************************
        //          *	Autor: Mario Adrián Martínez Fernández   Fecha: 21/09/2020
        //          *		   mario.martinez.f@hotmail.es
        //          *	Nota: Validamos que la cuenta del usuario tiene capacidad para enviar
        //          *          invitacion a as usuarios.
        //          ***********************************************************************/
        //         $this->db->from($this->tableName);
        //         $this->db->where('business_id', $validate['business_id']);
        //         $total_registros = $this->db->count_all_results();
        //         if ((int)$total_registros >= (int)$max_user) {
        //             return 'plan_limit';
        //         }
        //     }
        //     return $validate;
        // } else {
        if (count($validate) > 0)
            return $validate[0];
        else
            return false;
        // }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 15/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para registrar una invitacion enviada.
     ***********************************************************************/
    function SaveInvitation($entity)
    {
        $iArray = $entity;
        unset($iArray['region_id']);
        unset($iArray['id_asesor']);
        unset($iArray['rol_id']);
        unset($iArray['fecha_alta_cliente']);
        unset($iArray['job_id']);
        unset($iArray['id_comercio']);
        unset($iArray["phone"]);
        $id = $this->GuardarUsuarioPrevio($entity);


        if ($id) {
            $this->db->insert($this->tableInvitation, $iArray);
            return $id;
        } else {
            return false;
        }
    }

    function eliminarElementosArray($valor, $arr)
    {
        foreach (array_keys($arr, $valor) as $key) {
            unset($arr[$key]);
        }
        return $arr;
    }

    //funcion para insertar el usuario en la tabla user
    //esto para que se le puedan asignar contenidos desde el admin
    // aunque el usuario aun no este registrado aun (solo tenga la invitacion)
    // de esta forma cuando se registra e inicia sesion no ve su aplicacion vacia

    function GuardarUsuarioPrevio($invitacion)
    {
        $data = [];
        $data["password"] = "123"; //debe ser siempre este valor para la password porque hay una validacion en el registro, si no esta asi se considera que ya se dio de alta previamente
        // 'email' => $member_mail, 'business_id' => $valida_token['business_id'],
        // 'group_id' => $group_id, 'name' => $nombre, 'last_name' => $apellido, 'number_employee' => $number_employee)
        $data["name"] = $invitacion["name"];
        $data["last_name"] = $invitacion["last_name"];
        $data["email"] = $invitacion["email"];
        $data["id_region"] = $invitacion["region_id"];
        $data["id_asesor"] = $invitacion["id_asesor"];
        if (isset($invitacion["rol_id"]))
            $data["rol_id"] = $invitacion["rol_id"];
        $data["business_id"] = $invitacion["business_id"];
        $data["number_employee"] = $invitacion["number_employee"];
        if (isset($invitacion["fecha_alta_cliente"]))
            $data["fecha_alta_cliente"] = $invitacion["fecha_alta_cliente"];
        $data["job_id"] = $invitacion["job_id"];
        $data["phone"] = $invitacion["phone"] == null ? '' : $invitacion["phone"];
        $data["id_comercio"] = $invitacion["id_comercio"];
        //este campo de job_id es necesario porque no tiene un valor por defecto 
        //lo ideal es agregarle uno desde bd
        //en el momento en que se hace esta funcion hay pruebas con cliente por eso no se modifica el campo para evitar problemas inesperados
        //lo mismo va para el campo de phone, ahi se manda un espacio en blanco, si el usuario no sobrescribe no hay problema porque en su perfil no veria nada
        $this->db->insert("user", $data);
        $id_usuario = $this->db->insert_id();
        //una vez creado se debe agregar a su grupo, si es que tiene uno ya asignado desde la invitacion
        if (isset($invitacion["group_id"]) && $invitacion["group_id"] != "") {
            $data = [];
            $data["group_id"] = $invitacion["group_id"];
            $data["user_id"] = $id_usuario;
            $this->db->insert("users_groups", $data);
        }
        return $id_usuario;
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 15/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para confirmar que la invitacion ha sido registrado
     *          el usuario con el correo al que se le envio la invitacion.
     ***********************************************************************/
    function ConfirmInvitation($email)
    {
        return $this->db->update($this->tableInvitation, array('status' => 1), array('number_employee' => $email));
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 05/06/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: funcion para obtner los usuarios de la empresa de la sesion
     ***********************************************************************/
    function ListUsersBusiness($business_id, $search = '', $user_id = 0)
    {
        $this->db->select('id,name,last_name,profile_photo');
        $this->db->from($this->tableName . " as u");
        $this->db->where('business_id= ', $business_id);
        $this->db->where('active=', 1);
        $this->db->where('es_prueba =', 0);
        $this->db->where('id !=', $user_id);
        $this->validacion_roles($user_id);
        if ($search !== '') {
            $this->db->like('name', $search);
            $this->db->or_like('last_name', $search);
        }
        $list = $this->db->get()->result_array();
        if (count($list) > 0) {
            return $list;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *  Autor: Rodolfo Terrones Ruiz   Fecha: 23/06/2020
     *  Nota: Obtenemos el ranking de usuarios con la posibilidad de filtrarlos
     *      por empresa y puesto
     ***********************************************************************/
    function ranking($where, $user_id = null)
    {
        $query = "select * from user where id = $user_id";
        $user = $this->db->query($query)->result_array()[0];
        $region = $user["id_region"];
        $asesor = $user["id_asesor"]; //el asesor lo dejo por si se necesita, la instruccion fue segmentar por region y puesto
        $where_ = "";
        // if ($user["rol_id"] == 5) {
        //     $query = "select * from regiones_gerentes where id_gerente = $user_id";
        //     $regiones = $this->db->query($query)->result_array();
        //     for ($i = 0; $i < count($regiones); $i++) {
        //         if ($i == 0)
        //             $where_ .= ' and (id_region = ' . $regiones[$i]["id_region"];
        //         else if ($i == count($regiones) - 1)
        //             $where_ .= ' or id_region = ' . $regiones[$i]["id_region"] . ")";
        //         else
        //             $where_ .= ' or id_region = ' . $regiones[$i]["id_region"];
        //     }
        // } else {

        //     if ($region != null && $region != '') {
        //         $where_ .= " and id_region = " . $region;
        //     }
        // }
        $query = "SELECT 
							u.id, 
						    u.name, 
						    u.last_name, 
						    u.email, 
						    u.score , 
						    u.profile_photo, 
						    u.business_id,
						    b.business_name,
						    u.job_id,
						    ifnull(j.job_name,'Sin puesto') as job_name
						FROM user u
						LEFT JOIN business b ON b.id = u.business_id
						LEFT JOIN jobs j ON j.id = u.job_id
						 $where and u.es_prueba = 0 and u.active = 1 and u.password != '123' $where_
						ORDER by score DESC limit 10";

        return $this->db->query($query)->result_array();
    }

     /***********************************************************************
     *  Autor: Francisco Javier Avalos Prado   Fecha: 30/09/2022
     *
     *  Nota: Funcion para obtener puntos para el cuestionario "especial" de primera pregunta
     ***********************************************************************/
    function rankingCuestionarioPrimeraPregunta()
    {
        /* obtener bussines yastas */
        $business = $this->db->query("SELECT * FROM `business` WHERE business_name = 'Yastas';")->result_array();
        if(count($business) > 0){
            $id_business = $business[0]["id"];
            /* obtener cuestionarios de tipo first_question_is_correct de business_id */
            $cuestionario = $this->db->query("SELECT * FROM question_quiz WHERE business_id = '".$id_business."' AND first_question_is_correct = 1;")->result_array();
            $preguntasArray = [];
            $respuestasUsuarioArray = [];
            if(count($cuestionario) > 0){
                for ($i = 0; $i < count($cuestionario); $i++){
                    /* obtener preguntas del cuestionario */
                    $preguntas = $this->db->query("SELECT * FROM questions where quiz_id = '".$cuestionario[$i]["id"]."';")->result_array();
                    for ($x = 0; $x < count($preguntas); $x++){
                        /* buscar usuario que respondio */
                        $respuesta_usuario = $this->db->query("SELECT * FROM question_answer_users where question_id = '".$preguntas[$x]["id"]."';")->result_array();
                        /* guardar preguntas */
                        //array_push($preguntasArray, $preguntas[$x]);
                        if(count($respuesta_usuario) > 0){
                            for ($r = 0; $r < count($respuesta_usuario); $r++){
                                /* si la respuesta es correcta */
                                if($respuesta_usuario[$r]["correcto"] != 0){
                                    $jsonRespuesta = array(
                                        'id' => $respuesta_usuario[$r]["user_id"],
                                        'score' => $preguntas[$x]["points"]
                                    );
                                    array_push($respuestasUsuarioArray, $jsonRespuesta);
                                }else{
                                    
                                }
                            }
                            
                            /* usuario */
                            //$usuario = 
                            /* puntaje */
                        }
                    }
                }
                /* recorrer respuestas usuario para mapear el nuevo resultado similar a getranking */
                $result = [];
                for ($i = 0; $i < count($respuestasUsuarioArray); $i++){
                    /* comprobar que el usaurio ya fue evaluado */
                    if(count($result) > 0){
                        for ($u = 0; $u < count($result); $u++){
                            if($result[$u]["id"] == $respuestasUsuarioArray[$i]["id"]){
                                $result[$u]["score"] = $result[$u]["score"] + $respuestasUsuarioArray[$i]["score"];
                            }else{
                                $usuario = $this->db->query("SELECT * FROM user where id = '".$respuestasUsuarioArray[$i]["id"]."';")->result_array();
                                if(count($usuario) > 0){
                                    /* business */
                                    $business = $this->db->query("SELECT * FROM `business` WHERE id = '".$usuario[0]["business_id"]."';")->result_array();
                                    if(count($business) > 0){
                                        $business = $business[0]['business_name'];
                                    }else{
                                        $business = null;
                                    }
                                    /* job */
                                    $job = $this->db->query("SELECT * FROM `jobs` WHERE id = '".$usuario[0]["job_id"]."';")->result_array();
                                    if(count($job) > 0){
                                        $job = $job[0]['job_name'];
                                    }else{
                                        $job = null;
                                    }
                                    $jsonRespuesta = [
                                        'id' => $usuario[0]["id"],
                                        'name' => $usuario[0]["name"],
                                        'last_name' => $usuario[0]["last_name"],
                                        'email' => $usuario[0]["email"],
                                        'score' => $respuestasUsuarioArray[$i]["score"],
                                        'profile_photo' => $usuario[0]["profile_photo"],
                                        'business_id' => $usuario[0]["business_id"],
                                        'business_name' => $business,
                                        'job_id' => $usuario[0]["job_id"],
                                        'job_name' => $job,
                                        //'position' => $usuario[0]["position"],
                                        'gemas' => [],
                                        'moustro' => [],
                                        //'nivel' => $usuario[0]["nivel"],
                                    ];
                                    array_push($result, $jsonRespuesta);
                                }
                            }
                        }
                    }else{
                        $usuario = $this->db->query("SELECT * FROM user where id = '".$respuestasUsuarioArray[$i]["id"]."';")->result_array();
                                if(count($usuario) > 0){
                                    /* business */
                                    $business = $this->db->query("SELECT * FROM `business` WHERE id = '".$usuario[0]["business_id"]."';")->result_array();
                                    if(count($business) > 0){
                                        $business = $business[0]['business_name'];
                                    }else{
                                        $business = null;
                                    }
                                    /* job */
                                    $job = $this->db->query("SELECT * FROM `jobs` WHERE id = '".$usuario[0]["job_id"]."';")->result_array();
                                    if(count($job) > 0){
                                        $job = $job[0]['job_name'];
                                    }else{
                                        $job = null;
                                    }
                                    $jsonRespuesta = [
                                        'id' => $usuario[0]["id"],
                                        'name' => $usuario[0]["name"],
                                        'last_name' => $usuario[0]["last_name"],
                                        'email' => $usuario[0]["email"],
                                        'score' => $respuestasUsuarioArray[$i]["score"],
                                        'profile_photo' => $usuario[0]["profile_photo"],
                                        'business_id' => $usuario[0]["business_id"],
                                        'business_name' => $business,
                                        'job_id' => $usuario[0]["job_id"],
                                        'job_name' => $job,
                                        //'position' => $usuario[0]["position"],
                                        'gemas' => [],
                                        'moustro' => [],
                                        //'nivel' => $usuario[0]["nivel"],
                                    ];
                                    array_push($result, $jsonRespuesta);
                                }
                    }
                }
                return $result;
                return $respuestasUsuarioArray;
                /* obtener usuarios que han contestado preguntas */

                return $preguntasArray;
                //return $cuestionariosId;
                /* buscar respuestas para obtener puntaje de usuarios que respondieron */
            }
        }
        /* obtener cuestionario del grupo yastas */
        $pregunta = $this->db->query("SELECT * FROM questions where quiz_id = '".$result[$i]['id']."';")->result_array(); /* preguntas cuestionario */
        $pregunta = $this->db->query("SELECT * FROM questions where quiz_id = '".$result[$i]['id']."';")->result_array(); /* preguntas cuestionario */
        $result = 'aa';
        successResponse($result, 'Listado de ranking', $this);
        return;
    }

    function ranking_admin($where)
    {
        //se define una variable sql para realizar sumatorias dentro de la consulta
        $query = "SET @row := 0;";
        $this->db->query($query);
        //en la consulta la variable definida anteriormente se obtiene
        //con su valor + 1
        //se le asigna ese valor de modo que incrementa en 1
        $query = "
                    SELECT @row := @row + 1 as puesto,
							u.id, 
						    u.name, 
						    u.last_name, 
						    u.email, 
						    u.score , 
						    u.profile_photo, 
						    u.business_id,
						    b.business_name,
						    u.job_id,
						    ifnull(j.job_name,'Sin puesto') as job_name
						FROM user u
						LEFT JOIN business b ON b.id = u.business_id
						LEFT JOIN jobs j ON j.id = u.job_id
						 $where 
						ORDER by score DESC
                        ";

        return $this->db->query($query)->result_array();
    }

    /***********************************************************************
     *  Autor: Josue Ali Carrasco Ramirez   Fecha: 16/12/2020
     *         josue.carrasco.ramirez@gmail.com
     *  Nota: Funcion para guardar los nuevos puestos
     ***********************************************************************/
    function SaveJobs($entity)
    {
        if ($this->db->insert($this->tableJobs, $entity)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    /***********************************************************************
     *  Autor: Josue Ali Carrasco Ramirez   Fecha: 16/12/2020
     *         josue.carrasco.ramirez@gmail.com
     *  Nota: Funcion para modificar el nombre del puesto
     ***********************************************************************/
    function EditJobs($id, $data)
    {
        $key = array('id' => $id);
        if ($this->db->update($this->tableJobs, $data, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *  Autor: Josue Ali Carrasco Ramirez   Fecha: 16/12/2020
     *         josue.carrasco.ramirez@gmail.com
     *  Nota: Funcion para modificar el nombre del puesto
     ***********************************************************************/
    function DeleteJobs($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableJobs, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    function ValidateJobId($id)
    {
        $this->db->select('*');
        $this->db->from($this->tableJobs);
        $this->db->where('id', $id);
        $this->db->where('active', 1);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }


    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 17/08/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Validamos que el numero de empleado coincida con el email
     *          registrado
     ***********************************************************************/
    function ValidationNumberEmployee($data)
    {
        $this->db->select('id, number, email, job_id, group_id');
        $this->db->from($this->tableNumbersEmployees);
        $this->db->where('email =', $data['email']);
        $this->db->where('number =', $data['number_employee']);
        $this->db->where('estatus =', 0);
        $validation = $this->db->get()->result_array();
        if (count($validation) > 0) {
            return $validation[0];
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 17/08/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para actualizar el numero de empleado
     ***********************************************************************/
    function UpdateEmployee($id, $data)
    {
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Validamos que el numero de empleado no sea el mismo
         ***********************************************************************/
        if (isset($data['number'])) {
            $number_old = $this->db->get_where($this->tableNumbersEmployees, array('id' => $id))->result_array();
            if ($number_old[0]['number'] !== $data['number']) {
                if ($this->ValidateNumberEmployee($data['number'])) {
                    return 'number_in_use';
                }
            }
        }
        if ($this->db->update($this->tableNumbersEmployees, $data, array("id" => $id))) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/8/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para editar una invitacion.
     ***********************************************************************/
    function EditInvited($data)
    {
        $data["number_employee"] = trim($data["number_employee"]);
        $key = array('id' => $data["id"]);
        $dataa = [];
        $dataa['email'] = $data['email'];
        $dataa["name"] = $data["name"];
        $dataa["last_name"] = $data["last_name"];
        $dataa["number_employee"] = $data["number_employee"];
        $query = "select number_employee from invitation where id =  " . $data["id"];
        $number_employee = $this->db->query($query)->result_array()[0]["number_employee"];
        unset($data['member_mail']);
        unset($data['id']);
        unset($data["nombre"]);
        unset($data["apellido"]);
        //primero se obtiene el numero de empleado original
        //se necesita para saber que registro de la tabla usuarios modificar
        if ($data["rol_id"] == "undefined") {
            $data["rol_id"] = 3;
        }
        //se obtiene el id del registro en user en base al numero de empleado
        $query = "select id from user where number_employee =" . $number_employee;
        $id = $this->db->query($query)->result_array()[0]["id"];
        if ($this->db->update($this->tableInvitation, $dataa, $key)) {
            unset($data['group_id']);
            $this->db->update("user", $data, array("id" => $id));
            return true;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/8/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para guardar un numero de empleado nuevo
     ***********************************************************************/
    function SaveEmployee($entity)
    {
        if ($this->db->insert($this->tableNumbersEmployees, $entity)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/8/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener la lista de numeros de empleados
     *          registrados
     ***********************************************************************/
    function NumberEmployeeList($business_id)
    {
        $this->db->select('n.*,g.name as name_group,ifnull(j.job_name,"") as job_name');
        $this->db->from($this->tableNumbersEmployees . " AS n");
        $this->db->join($this->tableGroups . " AS g", "n.group_id = g.id");
        $this->db->join($this->tableJobs . " AS j", "j.id = n.job_id", 'left');
        $this->db->where('n.active = ', 1);
        $this->db->where('n.estatus = ', 0);
        $this->db->where('n.business_id = ', $business_id);

        $users = $this->db->get()->result_array();
        if (count($users) > 0) {
            return $users;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/8/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para eliminar un numero de empleado registrado
     ***********************************************************************/
    function DeleteNumbreEmployee($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableNumbersEmployees, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para validar si un numero de empleado existe
     ***********************************************************************/
    function ValidateNumberEmployee($number)
    {
        $this->db->select('*');
        $this->db->from($this->tableNumbersEmployees);
        $this->db->where('number', $number);
        $this->db->where('active', 1);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para aprobar a un usuario que no contaba con invitacion
     ***********************************************************************/
    function AcceptInvitation($data)
    {
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Actualizamos el estatus para que se pueda iniciar sesion.
         ***********************************************************************/
        $this->db->update($this->tableName, array('register_no_invitation' => 0), array('id' => $data['id']));
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Registramos el usuario a un grupo en especifico
         ***********************************************************************/
        return $this->db->insert($this->tableUsersGroups, array('user_id' => $data['id'], 'group_id' => $data['group_id']));
    }

    function ObtenerToken($user_id)
    {
        $this->db->select("token");
        $this->db->from("devices");
        $this->db->where("id_user", $user_id);
        $result = $this->db->get()->result_array();
        if(count($result)){
            return $result[0]["token"];
        }
        return false;
    }

    function ValidaNumeroEmpleado($numero_empleado)
    {
        $this->db->select("*");
        $this->db->from("invitation");
        $this->db->where("number_employee", $numero_empleado);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            if ($numero_empleado != '' && $numero_empleado != null && strlen($numero_empleado) >= 6)
                return true;
            return false;
        } else {
            return false;
        }
    }

    function validarEmail($email)
    {
        $this->db->select("*");
        $this->db->from("invitation");
        $this->db->where("email", $email);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function ValidaAsesorGerente($asesor)
    {
        if ($asesor == "" || $asesor == null) {
            $asesor_id = $this->session->userdata("id_user");
        } else {
            //consultamos la id del nombre
            $query  = $this->db->select('id')
                ->from('user')
                ->where("number_employee ", $asesor)
                ->where("rol_id", 6) //id de gerente, pasar a constants
                ->get();
            $res = $query->row_array();
            $idasesor = $res["id"];

            $asesor_id = $idasesor;
        }
        return $asesor_id;
    }

    function ValidaNumeroDeRegiones($region)
    {

        $reg = explode(",", $region);

        if (count($reg) == 1) {
            $this->db->select('*');
            $this->db->from('regiones');
            $this->db->where('nombre', $region);
            $result = $this->db->get()->result_array();

            if (count($result) > 0) {
                $result = $result[0]['id'];
            } else {
                return array($result, 1, false);
            }
            //se obtiene la id, 1=> es por que solo es una region, manda true
            return array($result, 1, true);
        } else {
            $reg_count = count($reg);
            $idInicial = 0;
            $idRegionesarray = array();
            for ($i = 0; $i < $reg_count; $i++) {
                $query  = $this->db->select('id')
                    ->from('regiones')
                    ->where('nombre', trim($reg[$i]))
                    ->get();
                $res = $query->row_array();
                $id = $res["id"];
                if ($i == 0) {
                    $idInicial = $id;
                }
                array_push($idRegionesarray, $id);
                //$this->db->insert('regiones_gerentes', array('id_region' => $id, 'id_gerente' => $this->session->userdata('id_user')));
            }
            //regresa el primer id, 2=> es mas de 1 Region, manda true y manda las id's de todas las regiones
            return array($idInicial, 2, true, $idRegionesarray);
        }
    }

    function SaveRegionesGerente($regiones, $idGerente)
    {

        $reg = explode(',', $regiones);
        if (count($reg) == 1) {
            $idGerente = $reg[0];
        } else {
            for ($i = 0; $i < count($reg); $i++) {
                $this->db->insert('regiones_gerentes', array('id_region' => trim($reg[$i]), 'id_gerente' => $idGerente));
            }
        }
    }

    function ValidaRolId($rol_id)
    {
        if ($rol_id == "") {
            return 3;
        } else {
            return false;
        }
    }

    function guardar_invitacion($name, $last_name, $email, $number_employee, $grupo_id, $business_id)
    {
        //guardar tambien el business_id
        $data = [];
        $data["name"] = $name;
        $data["last_name"] = $last_name;
        $data["email"] = $email;
        $data["number_employee"] = $number_employee;
        $data["group_id"] = $grupo_id;
        $data["business_id"] = $business_id;
        return $this->db->insert("invitation", $data);
    }

    function obtenerGrupoId($nombre_grupo)
    {
        $this->db->select("id");
        $this->db->from("groups");
        $this->db->where("name", $nombre_grupo);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            return $result[0]["id"];
        } else {
            return "";
        }
    }

    function getUsersInGroup($user_id)
    {
        $this->db->select("g.group_id");
        $this->db->from("users_groups as g");
        $this->db->join("user as u", "u.id = g.user_id");
        $this->db->where("u.id", $user_id);
        $this->db->where("g.active", 1);
        $result = $this->db->get()->result_array();
        $grupos = [];
        for ($i = 0; $i < count($result); $i++) {
            array_push($grupos, $result[$i]["group_id"]);
        }
        $this->db->select("u.id");
        $this->db->from("user as u");
        $this->db->join("users_groups as g", "g.user_id = u.id");
        $this->db->where_in("g.group_id", $grupos);
        $result = $this->db->get()->result_array();
        $users = [];
        for ($i = 0; $i < count($result); $i++) {
            array_push($users, $result[$i]["id"]);
        }
        return $users;
    }

    function getTokensByGroups($users)
    {
        $this->db->select('d.token,u.id as user_id');
        $this->db->from('user u');
        $this->db->join('devices d', 'u.id = d.id_user');
        $this->db->where_in("u.id", $users);
        $list = $this->db->get()->result_array();
        return (count($list) > 0) ? $list : false;
    }

    function getTokensUsuarios($users)
    {
        $this->db->select('d.token');
        $this->db->from('user u');
        $this->db->join('devices d', 'u.id = d.id_user');
        $this->db->where_in("u.id", $users);
        $list = $this->db->get()->result_array();
        $list_ = [];
        for ($i = 0; $i < count($list); $i++) {
            array_push($list_, $list[$i]["token"]);
        }
        return (count($list_) > 0) ? $list_ : false;
    }

    function guardar_mensaje($datos)
    {
        return $this->db->insert("mensajes_contacto", $datos);
    }

    function obtenerMensajesContacto($business_id)
    {
        $this->db->select("concat(u.name, ' ', u.last_name) as name,concat('#', u.number_employee) as number_employee,mc.mensaje,u.email, mc.id as id_mensaje");
        $this->db->from("mensajes_contacto as mc");
        $this->db->join("user as u", "u.id = mc.user_id");
        $this->db->where("u.business_id", $business_id);
        $this->db->where("mc.activo", 1);
        $this->db->order_by("mc.id desc");
        if ($this->session->userdata("rol_id") == 6) {
            $id_asesor = $this->session->userdata("id_user");
            $this->db->where("u.id_asesor", $id_asesor);
        }
        if ($this->session->userdata("rol_id") == 5) {
            $region = $this->session->userdata("id_region");
            $this->db->where("u.id_region", $region);
        }
        return $this->db->get()->result_array();
    }

    function eliminarMensaje($id_mensaje)
    {
        $this->db->set("activo", 0);
        $this->db->where("id", $id_mensaje);
        return $this->db->update("mensajes_contacto");
    }

    function obtener_regiones_gerente($id_gerente)
    {
        $query = "select id_region from regiones_gerentes where id_gerente = $id_gerente";
        $result = $this->db->query($query)->result_array();
        $regiones = [];
        for ($i = 0; $i < count($result); $i++) {
            array_push($regiones, $result[$i]["id_region"]);
        }
        return $regiones;
    }

    function obtener_region($user_id)
    {
        // $this->db->select("id_region");
        // $this->db->from("user");
        // $this->db->where("id", $user_id);
        $query = "select id_region from user where id = $user_id";
        return $this->db->query($query)->result_array()[0]["id_region"];
    }

    function obtener_asesor($user_id)
    {
        // $this->db->select("id_asesor");
        // $this->db->from("user");
        // $this->db->where("id", $user_id);
        $query = "select id_asesor from user where id = $user_id";
        return $this->db->query($query)->result_array()[0]["id_asesor"];
    }

    function obtener_rol($user_id)
    {
        // $this->db->select("rol_id");
        // $this->db->from("user");
        // $this->db->where("id", $user_id);
        $query = "select rol_id from user where id = $user_id";
        return $this->db->query($query)->result_array()[0]["rol_id"];
    }

    public function obtener_imagenes_puntos($puntos)
    {
        $arr = [];
        // || $i == 350 || $i == 375 || $i == 400 || $i == 450
        //     || $i == 475 || $i == 500 || $i == 550 || $i == 575 || $i == 600 || $i == 700 || $i == 800 || $i == 900
        //     || $i == 1000 || $i == 1500

        // || $i == 350 || $i == 375 || $i == 400 || $i == 450
        //     || $i == 475 || $i == 500 || $i == 550 || $i == 575 || $i == 600 || $i == 700 || $i == 800 || $i == 900
        //     || $i == 1000 || $i == 1500
        for ($i = 1; $i < $puntos; $i++) {
            if (
                $i == 25 || $i == 50 || $i == 75 || $i == 100 || $i == 150 || $i == 175
                || $i == 200 || $i == 250 || $i == 275 || $i == 300
            ) {
                array_push($arr, array("imagen" => BASE_URL . "assets/img/img_" . $i . "_pts_color.png", "nombre" => $i . " PTS."));
            }
        }
        for ($i = $puntos; $i < 1500; $i++) {
            if (
                $i == 25 || $i == 50 || $i == 75 || $i == 100 || $i == 150 || $i == 175
                || $i == 200 || $i == 250 || $i == 275 || $i == 300
            ) {
                array_push($arr, array("imagen" => BASE_URL . "assets/img/img_" . $i . "_pts_bn.png", "nombre" => $i . " PTS."));
            }
        }
        return $arr;
    }

    function desasociar_usuario($catalogo, $id_elemento, $id_usuario)
    {
        $campo = $this->obtener_campo($catalogo);
        $campo_user = $this->obtener_campo_user($catalogo);
        $query = "delete from $catalogo where $campo = $id_elemento and $campo_user = $id_usuario";
        return $this->db->query($query);
    }

    function asociar_usuario($catalogo, $id_elemento, $id_usuario)
    {
        $campo = $this->obtener_campo($catalogo);
        $campo_user = $this->obtener_campo_user($catalogo);
        $query = "insert into $catalogo ($campo,$campo_user) values($id_elemento, $id_usuario)";
        return $this->db->query($query);
    }

    function actualizar_grupos_elementos($catalogo, $usuarios, $id_elemento)
    {
        $campo = $this->obtener_campo($catalogo);
        $campo_user = $this->obtener_campo_user($catalogo);
        for ($i = 0; $i < count($usuarios); $i++) {
            $seleccionado = $usuarios[$i]["seleccionado"];
            $id_usuario = $usuarios[$i]["id"];
            if ($seleccionado) {
                $query = "select * from $catalogo where $campo = $id_elemento and $campo_user = $id_usuario";
                $result = $this->db->query($query)->result_array();
                if (count($result) > 0) {
                } else {
                    $this->asociar_usuario($catalogo, $id_elemento, $id_usuario);
                }
            } else {
                $this->desasociar_usuario($catalogo, $id_elemento, $id_usuario);
            }
        }
        return true;
    }

    function obtener_campo($catalogo)
    {
        $campo = "";
        if ($catalogo == "library_users") {
            $campo = "library_id";
        }
        if ($catalogo == "com_users_topics") {
            $campo = "id_topic";
        }
        if ($catalogo == "capacit_users") {
            $campo = "id_list";
        }
        if ($catalogo == "wall_users") {
            $campo = "wall_id";
        }
        if ($catalogo == "podcast_users") {
            $campo = "podcast_id";
        }
        if ($catalogo == "quiz_users") {
            $campo = "quiz_id";
        }
        return $campo;
    }

    function obtener_campo_user($catalogo)
    {
        $campo = "";
        if ($catalogo == "library_users") {
            $campo = "user_id";
        }
        if ($catalogo == "com_users_topics") {
            $campo = "id_user";
        }
        if ($catalogo == "capacit_users") {
            $campo = "id_user";
        }
        if ($catalogo == "wall_users") {
            $campo = "user_id";
        }
        if ($catalogo == "podcast_users") {
            $campo = "user_id";
        }
        if ($catalogo == "quiz_users") {
            $campo = "user_id";
        }
        return $campo;
    }

    function obtener_usuarios_de_asesores($id_asesor)
    {
        $query = "
            select id, concat(name,' ',last_name) as name, concat('w',number_employee) as numero_empleado
            from user where id_asesor = $id_asesor
        ";
        return $this->db->query($query)->result_array();
    }

    function obtener_gemas($user_id)
    {
        $url = base_url() . "assets/img/";
        $query = "
            select ug.cantidad, concat('$url',g.imagen) as imagen from users_gemas as ug
            join gemas as g on g.id = ug.gema_id
            where user_id = $user_id
        ";
        return $this->db->query($query)->result_array();
    }

    function obtener_monstruo($user_id)
    {
        $url = base_url() . "assets/img/";
        $query = "
            select m.nivel, concat('$url',m.imagen) as imagen
            from users_monstruos as um
            join monstruos as m on m.id = um.monstruo_id
            where user_id = $user_id
            order by monstruo_id desc
            limit 1
        ";
        return $this->db->query($query)->result_array();
    }

    function asignar_usuarios_a_asesores($data)
    {
        $dataa = array(
            "id_asesor" => $data["id_asesor"]
        );
        $this->db->where("id", $data["user_id"]);
        if ($this->db->update("user", $dataa)) {
            return true;
        } else {
            return false;
        }
    }

    function obtener_usuarios_asignables($id_asesor)
    {
        $id_region = $this->obtener_region($id_asesor);
        $query = "select u.id, concat(u.name,' ', u.last_name) as name, concat('#',u.number_employee) as numero_empleado
         from user as u
          where (id_asesor != $id_asesor or id_asesor is null) and active = 1 and id_region = $id_region and es_prueba = 0 and rol_id = 3";
        return $this->db->query($query)->result_array();
    }

    function eliminar_usuario_de_asesor($id_usuario)
    {
        $id_region = $this->obtener_region($id_usuario);
        $id_asesor = $this->obtener_id_de_asesor_prueba($id_region);
        $query = "
            update user set id_asesor = $id_asesor where id = $id_usuario
        ";
        $this->db->query($query);
    }

    function obtener_id_de_asesor_prueba($id_region)
    {
        //se busca el asesor con un numero de empleado generico pero se le suma la region
        //porque asi se definio
        $query = "select id from user where number_employee = 777444$id_region";
        return $this->db->query($query)->result_array()[0]["id"];
    }

    function set_video_visto($user_id)
    {
        $query = "update user set video_visto = 1 where id = $user_id";
        return $this->db->query($query);
    }

    function obtener_info_registro($numero_empleado)
    {
        $query = "select name, last_name, email, phone, job_id from user where number_employee = '$numero_empleado'";
        return $this->db->query($query)->result_array();
    }

    function valida_puesto($nombre_puesto, $business_id)
    {
        $query = "select id from jobs where job_name = '$nombre_puesto' and business_id = $business_id;";
        $result = $this->db->query($query)->result_array();
        if (count($result) > 0)
            return $result[0]["id"];
        else
            return 0;
    }

    function set_instrucciones_vistas($user_id)
    {
        $data = [];
        $data["user_id"] = $user_id;
        if (!$this->get_video_visto($user_id))
            return $this->db->insert("video_visto", $data);
        return false;
    }

    function get_video_visto($user_id)
    {
        $query = "select * from video_visto where user_id = $user_id";
        $result = $this->db->query($query)->result_array();
        if (count($result) > 0) {
            return true;
        }
        return false;
    }

    function obtener_fecha_cap($id_insignia, $id_usuario)
    {
        $query = "select date_format(cc.fecha,'%d/%m/%Y') as fecha from capacit_list as cl
        join capacit_completed as cc on cc.id_capacitacion = cl.id
        where id_insignia = ? and cc.id_usuario = ?
        order by cc.id desc
        limit 1";
        return $this->db->query($query, array($id_insignia, $id_usuario))->result_array()[0]["fecha"];
    }
}
