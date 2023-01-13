<?php
class Notification_mdl extends CI_Model
{
    public  $table = 'notification';
    function __construct()
    {
        parent::__construct();
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/07/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Función
     ***********************************************************************/
    function RegisterNotification($insert)
    {
        return $this->db->insert($this->table, $insert);
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/07/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota:
     ***********************************************************************/
    function ListNotifications($user_id, $validate = false)
    {
        $this->db->select('n.id,n.title,n.notification,u.profile_photo, concat(u.name," ",u.last_name) as name,n.service_id, n.view,n.id_topic');
        $this->db->from($this->table . ' as n');
        $this->db->join('user u', 'u.id = n.user_create_id');
        $this->db->where('user_id =', $user_id);
        // if ($validate) {
        //     $this->db->where('view = ', 0);
        // }
        $this->db->order_by('view', 'asc');
        $this->db->order_by('id', 'desc');
        $list = $this->db->get()->result_array();
        // echo json_encode($this->db->last_query());
        for ($i = 0; $i < count($list); $i++) {
            $id = $list[$i]["id"];
            $this->db->update($this->table, array('view' => 1), array('id' => $id));
        }
        return (count($list) > 0) ? $list : false;
    }

    function comprobarNotificaciones($user_id)
    {
        $this->db->select('n.id,n.title,n.notification,u.profile_photo, concat(u.name," ",u.last_name) as name,n.service_id, n.view');
        $this->db->from($this->table . ' n');
        $this->db->join('user u', 'u.id = n.user_create_id');
        $this->db->where('user_id =', $user_id);

        $this->db->where('view = ', 0);

        $this->db->order_by('view', 'asc');
        $this->db->order_by('id', 'desc');
        $list = $this->db->get()->result_array();
        return (count($list) > 0) ? $list : false;
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 09/07/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para actualizar que ya ha sido vista esa notificacion
     ***********************************************************************/
    function NotificationView($id)
    {
        return $this->db->update($this->table, array('view' => 1), array('id' => $id));
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 09/07/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los usuarios en base a la empresa para
     *          enviar una notificacion.
     ***********************************************************************/
    function ListUserNotificationRecertificacion(){
        $query = "select d.token, u.id as user_id
        from user as u
        join devices as d on d.id_user = u.id
        join users_groups as ug on ug.user_id = u.id
        where u.business_id = 18
        and u.id in (
        select id_user from capacit_users where id_list = 56
        )
        ";
        $list = $this->db->query($query)->result_array();
        return (count($list) > 0) ? $list : false;
    }

    function ListUserNotification($business_id, $user_id = null, $id_region_ = null, $id_asesor_ = null, $regiones = null, $asesores = null, $grupos = null)
    {

        $this->db->select('d.token,u.id as user_id');
        $this->db->from('user as u');
        $this->db->join('devices d', 'd.id_user = u.id');
        $this->db->join("users_groups as ug", "ug.user_id = u.id");
        $this->db->where('u.business_id =', $business_id);
        // if ($this->session->userdata('rol_id') != ROL_ADMINISTRADOR_PRINCIPAL) {
        //     $this->db->where('u.business_id =', $business_id);
        //     if ($user_id !== 0) {
        //         $this->db->where('u.id =', $user_id);
        //     }
        // }

        if ($this->session->userdata("rol_id") == 6) {
            // $id_asesor = $this->session->userdata("id_user");
            $id_asesor = $id_asesor_;
            if ($id_asesor == null) {
                $id_asesor = $user_id;
            }
            if ($id_region_ == null) {
                $id_region = $this->session->userdata("id_region");
            }
            $this->db->where("u.id_region", $id_region);
            $this->db->where("u.id_asesor", $id_asesor);
            $this->db->or_where("u.id",$id_asesor);
        }

        if($asesores != null){
            $this->db->where_in("u.id_region", $regiones);
        }

        if($asesores != null){
            $this->db->where_in("u.id_asesor", $asesores);
        }

        if($grupos != null){
            $this->db->where_in("ug.group_id", $grupos);
        }

        $this->db->or_where("u.id",$user_id);

        if ($this->session->userdata("rol_id") == 5) {
            if ($id_asesor_ == 0 || $id_asesor_ == null) {
                $this->db->where_in("u.id_region", $id_region_);
            } else {
                $this->db->where_in("u.id_region", $id_region_);
                $this->db->where_in("u.id_asesor", $id_asesor_);
                $this->db->or_where("u.id",$id_asesor);
            }


            // if ($id_region_ == null) {
            //     //validacion solo para biblioteca
            //     if ($id_region_ == null) {
            //         $query = "select id_region from user where id = " . $user_id;
            //         $result = $this->db->query($query)->result_array()[0]["id_region"];
            //         $region = $result;
            //     }
            // }
            // if ($id_asesor_ != 0 && $id_asesor_ != null) { //validacion solo para biblioteca
            //     $this->db->where("u.id_region", $region);
            // }
            // if ($id_region_ != 0 && $id_asesor_ != null) { //validacion para biblioteca
            //     $region = $id_region_;
            // }
            // //si las validaciones para bibliteca vienen en cero no mandar el where
            // $this->db->where("u.id_region", $region);
        }

        if ($this->session->userdata("rol_id") == 3) {
            $id_asesor = ""; //$this->session->userdata("id_asesor");
            if ($id_region_ == null) {
                $query = "select id_asesor from user where id = " . $user_id;
                $result = $this->db->query($query)->result_array()[0]["id_asesor"];
                $id_asesor = $result;
            }
            $this->db->where("u.id_asesor", $id_asesor);
        }

        $list = $this->db->get()->result_array();
        // echo json_encode($this->db->last_query());
        return (count($list) > 0) ? $list : false;
    }

    function obtenerTokensComunidad($id_topic, $id_user)
    {
        $usuarios = $this->obtenerUsuariosEnComunidad($id_topic, $id_user);

        if (count($usuarios) == 0) {
            return false;
        }
        $this->db->select('d.token,u.id as user_id');
        $this->db->from('user u');
        $this->db->join('devices d', 'u.id = d.id_user');
        $this->db->where_in("u.id", $usuarios);
        $list = $this->db->get()->result_array();
        return (count($list) > 0) ? $list : false;
    }

    function obtenerTokensRuletaRetos($usuarios)
    {
        $this->db->select('d.token,u.id as user_id');
        $this->db->from('user u');
        $this->db->join('devices d', 'u.id = d.id_user');
        $this->db->where_in("u.id", $usuarios);
        $usuarios_join = join(",", $usuarios);
        $this->db->order_by("field(u.id,$usuarios_join)");
        $list = $this->db->get()->result_array();
        return (count($list) > 0) ? $list : false;
    }

    function obtenerUsuariosEnComunidad($id_topic, $id_user)
    {
        $query = "(SELECT u.id FROM com_users_topics AS cut2
        join user as u on u.id = cut2.id_user
        where cut2.id_topic =" . $id_topic . " and u.active = 1 and u.password != '123' and u.id != " . $id_user . ") 
        union
        (select u.id  from com_groups as cg 
        join users_groups as ug on ug.group_id = cg.group_id
        join user as u on u.id = ug.user_id and u.id not in (SELECT cut2.id_user FROM com_users_topics AS cut2 where cut2.id_topic = " . $id_topic . ")
        WHERE cg.com_id = " . $id_topic . " and u.password != '123' and u.active = 1 and u.id != " . $id_user . "
        )";

        $ids = $this->db->query($query)->result_array();
        $usuarios = [];
        for ($i = 0; $i < count($ids); $i++) {
            array_push($usuarios, $ids[$i]["id"]);
        }
        return $usuarios;
    }

    function eliminarNotificaciones()
    {
        $this->db->where("view", 1);
        return $this->db->delete("notification");
    }

    function obtener_notificaciones($business_id)
    {
        $query = "select n.notification,coalesce(s.service_name,'General') as service_name,date_format(n.fecha,'%d-%m-%Y') as fecha,g.name from notification as n
        join users_groups as ug on ug.user_id = n.user_id
        join groups as g on g.id = ug.group_id
        join user as u on ug.user_id = u.id
        left join services as s on s.id = n.service_id and s.service_name != 'Chat'
        where u.business_id = $business_id
        group by notification, g.id
        order by n.fecha desc";
        return $this->db->query($query)->result_array();
    }
}
