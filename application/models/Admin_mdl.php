<?php
class Admin_mdl extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 23 mar 2018
     *	Nota: Se obtienen los modulos por rol del usuario de la sesion
     ***********************************************************************/

    public function obtener_modulos_por_rol_angular($rol)
    {
        $query = "
        select m.id,m.nombre, m.enlace_angular from role_permissions as r 
        join modules as m on m.id = r.id_modulo
        join hired_services as h on (h.services_id = m.service_id or m.service_id = 0) and
        h.business_id = " . $this->session->userdata('empresa_id') . "
        where m.id_padre = 0 and r.estatus = 1  and r.id_rol = $rol 
        group by m.id
        order by m.order
        ";
        $modulos = $this->db->query($query)->result_array();
        for ($i = 0; $i < count($modulos); $i++) {
            $id = $modulos[$i]["id"];
            $query = "select m.id, m.nombre, m.enlace_angular from modules as m
                      join role_permissions as r on r.id_modulo = m.id
                      where m.id_padre = $id and r.estatus = 1 and r.id_rol = $rol and m.nombre != 'segmentar archivos'";
            $modulos[$i]["submodulos"] = $this->db->query($query)->result_array();
            for ($j = 0; $j < count($modulos[$i]["submodulos"]); $j++) {
                $id = $modulos[$i]["submodulos"][$j]["id"];
                $query = "select m.id, m.nombre, m.enlace_angular from modules as m 
                          join role_permissions as r on r.id_modulo = m.id
                          where m.id_padre = $id and r.estatus = 1 and r.id_rol = $rol";
                $modulos[$i]["submodulos"][$j]["submodulos"] = $this->db->query($query)->result_array();
            }
        }
        return $modulos;
    }

    public function ObtenerModulosPorRol($rol = null, $id_padre = 0, $id_modulo = '')
    {
        $query = "select m.* from role_permissions p
              JOIN modules m ON p.id_modulo = m.id
            where p.id_rol = $rol and p.estatus = 1 and m.id_padre = $id_padre
            order by m.`order`
         ";
        if ($id_modulo !== '') {
            $query .= ' and m.id = ' . $id_modulo;
        }
        $resultado = $this->db->query($query)->result_array();
        $validate__ = 'nuup';
        if ($validate__ !== 'nuup') {
            foreach ($resultado as $index => $value) {
                if ($value['id'] == 28 && $this->session->userdata('empresa_id') == EMPRESA_INTERNOS) {
                    unset($resultado[$index]);
                    continue;
                }
                if ($value['id'] == 29 && $this->session->userdata('empresa_id') == EMPRESA_EXTERNOS) {
                    unset($resultado[$index]);
                    continue;
                }
                if ($value['service_id'] != 0) {
                    $query = "
                    select 
                           id
                    from hired_services 
                    where business_id =" . $this->session->userdata('empresa_id') . "
                        and services_id = " . $value['service_id'];
                    $query = $this->db->query($query)->result_array();
                    if (count($query) === 0) {
                        unset($resultado[$index]);
                    }
                }
            }
        } else {
            foreach ($resultado as $index => $value) {
                /*if($value['id'] == 28){
                    unset($resultado[$index]);
                    continue;
                }*/
                if ($value['id'] == 29) {
                    unset($resultado[$index]);
                    continue;
                }
                if ($value['service_id'] != 0) {
                    $query = "
                    select 
                           id
                    from hired_services 
                    where business_id =" . $this->session->userdata('empresa_id') . "
                        and services_id = " . $value['service_id'];
                    $query = $this->db->query($query)->result_array();
                    if (count($query) === 0) {
                        unset($resultado[$index]);
                    }
                }
            }
        }
        return $resultado;
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 23 mar 2018
     *	Nota: Funcion para crear Arbol de menu con sus respectivos submenus
     ***********************************************************************/
    public function CrearArbolMenu($rol)
    {
        return $this->ValidaDropDown($rol);
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 23 mar 2018
     *	Nota: Funcion principal para generar el arbol de los menus.
     ***********************************************************************/
    function ValidaDropDown($rol, $id_padre = 0)
    {
        $KCO = &get_instance();
        $KCO->load->model('admin_mdl');
        $html = '';
        $result = $this->ObtenerModulosPorRol($rol, $id_padre);
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández
         *		   mario.martinez.f@hotmail.es
         *	Fecha: 23 mar 2018
         *	Nota: Se obtiene los padres principales del arbol del menu
         ***********************************************************************/
        foreach ($result as $index => $value) {

            if ($value['vista'] === null) {
                $html_cierre_drop = '</li>';
                $html .= '<li>' .
                    '<a href="#submenu' . $index . '" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><img class="pr-1 pb-1" src="' . base_url() . 'assets/img/' . $value['icono'] . '">' . $value['nombre'] . '</a>' .
                    '<ul class="collapse list-unstyled" id="submenu' . $index . '">' .
                    $this->DetalleDropDown($rol, $value['id']) .
                    '</ul>';
                $html .= $html_cierre_drop;
            } else {
                $html .= '<li>' .
                    '<a onclick="cargarHtml(\'' . $value['vista'] . '\')" class="nav-link" href="javascript:void(0)"><img class="pr-1 pb-1" src="' . base_url() . 'assets/img/' . $value['icono'] . '">' . $value['nombre'] . '</a>' .
                    '</li>';
            }
        }
        return $html;
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 23 mar 2018
     *	Nota: Funcion para obtener el detalle en caso de que la vista se
     *          encuentre en null, si la vista se encuentra en null indica
     *          que tiene submenus.
     ***********************************************************************/
    function DetalleDropDown($rol, $id_padre, $finalizar = false, $id_modulo = '')
    {
        $KCO = &get_instance();
        $KCO->load->model('admin_mdl');
        $html = '';
        $result = $this->ObtenerModulosPorRol($rol, $id_padre, $id_modulo);
        $tab_index = '';

        if ($finalizar === false) {
            $tab_index = 'tabindex="-1"';
        }
        foreach ($result as $index => $value) {
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández
             *		   mario.martinez.f@hotmail.es
             *	Fecha: 23 mar 2018
             *	Nota: Si el detalle tiene una vista de nuevo null, se crea su
             *          submenu y para ellos se manda a llamar la funcion
             *          de Detalle DropDownFinal
             ***********************************************************************/
            if ($value['vista'] === null) {
                //print_r($value);exit;
                $html_cierre_drop = '</li>';
                $html .= '<li>' .
                    '<a style="background:transparent !important;" href="#submenu' . $value['id'] . '" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><img class="pr-1 pb-1" src="' . base_url() . 'assets/img/' . $value['icono'] . '">' . $value['nombre'] . '</a>' .
                    '<ul class="collapse list-unstyled" id="submenu' . $value['id'] . '">' .
                    $this->DetalleDropDown($rol, $value['id']) .
                    '</ul>';
                $html .= $html_cierre_drop;
                /*$html.='<li>'.
                    '<a '.$tab_index.' href="javascript:void(0)">'.$value['nombre'].'</a>'.
                    '<ul class="collapse list-unstyled" id="submenu_2'.$index.'">'.
                    $this->DetalleDropDownFinal($rol,$value['id']).
                    '</ul>'.
                    '</li>';*/
            } else {
                $html .= '<li>';
                $html .= '<a onclick="cargarHtml(\'' . $value['vista'] . '\')" class="dropdown-item" tabindex="-1" href="javascript:void(0)">' . $value['nombre'] . '</a>';
                $html .= '</li>';
            }
        }
        return $html;
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 23 mar 2018
     *	Nota: Funcion para obtener en dado caso el final del menu.
     ***********************************************************************/
    function DetalleDropDownFinal($rol, $id_padre)
    {

        $KCO = &get_instance();
        $KCO->load->model('admin_mdl');
        $html = '';
        $result = $this->ObtenerModulosPorRol($rol, $id_padre);
        foreach ($result as $index => $value) {
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández
             *		   mario.martinez.f@hotmail.es
             *	Fecha: 23 mar 2018
             *	Nota: Si la vista vuelve a ser null, se crea el detalle del menu
             *          para ello se manda de nuevo a la Funcion de DetalleDropDown
             *          pero se manda un paramtri nuevo que es id del modulo a crear.
             ***********************************************************************/
            if ($value['vista'] === null) {
                $html .= $this->DetalleDropDown($rol, $id_padre, true, $value['id']);
            } else {
                $html .= '<li><a onclick="cargarHtml(\'' . $value['vista'] . '\')"  tabindex="-1" href="javascript:void(0)">' . $value['nombre'] . '</a></li>';
            }
        };
        return $html;
    }

    function ObtenerTerminos($business_id)
    {
        $this->db->select("terminos");
        $this->db->from("legales");
        $this->db->where("business_id", $business_id);
        return $this->db->get()->result_array();
    }

    function ObtenerAvisoPrivacidad($business_id)
    {
        $this->db->select("aviso_privacidad");
        $this->db->from("legales");
        $this->db->where("business_id", $business_id);
        return $this->db->get()->result_array();
    }

    function AceptarAviso($user_id)
    {
        $this->db->select("id");
        $this->db->from("user");
        $this->db->where("id", $user_id);
        $this->db->or_where("number_employee", $user_id);
        $user_id = $this->db->get()->result_array()[0]["id"];
        //se valida el id solo para yastas, se cambio la forma en que se aceptan los terminos y condiciones
        $this->db->select("*");
        $this->db->from("legales_users");
        $this->db->where("user_id", $user_id);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            $this->db->set("aceptacion_aviso", 1);
            $this->db->set("fecha_aviso_privacidad", date('Y-m-d H:i:s'));

            $this->db->where("user_id", $user_id);
            return $this->db->update("legales_users");
        } else {
            $data = [];
            $data["user_id"] = $user_id;
            $data["aceptacion_aviso"] = 1;
            $data["fecha_aviso_privacidad"] = date('Y-m-d H:i:s');
            return $this->db->insert("legales_users", $data);
        }
    }

    function AceptarTerminos($user_id)
    {
        $this->db->select("id");
        $this->db->from("user");
        $this->db->where("id", $user_id);
        $this->db->or_where("number_employee", $user_id);
        $user_id = $this->db->get()->result_array()[0]["id"];
        //se valida el id solo para yastas, se cambio la forma en que se aceptan los terminos y condiciones
        $this->db->select("*");
        $this->db->from("legales_users");
        $this->db->where("user_id", $user_id);
        $result = $this->db->get()->result_array();

        if (count($result) > 0) {
            $this->db->set("aceptacion_terminos", 1);
            $this->db->set("fecha_terminos_condiciones", date('Y-m-d H:i:s'));

            $this->db->where("user_id", $user_id);
            return $this->db->update("legales_users");
        } else {
            $data = [];
            $data["user_id"] = $user_id;
            $data["aceptacion_terminos"] = 1;
            $data["fecha_terminos_condiciones"] = date('Y-m-d H:i:s');
            return $this->db->insert("legales_users", $data);
        }
    }

    function AvisoAceptado($user_id)
    {
        $this->db->select("*");
        $this->db->from("legales_users");
        $this->db->where("user_id", $user_id);
        $this->db->where("aceptacion_aviso", 1);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            return 1;
        }
        return 0;
    }

    function TerminosAceptados($user_id)
    {
        $this->db->select("*");
        $this->db->from("legales_users");
        $this->db->where("user_id", $user_id);
        $this->db->where("aceptacion_terminos", 1);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            return 1;
        }
        return 0;
    }

    function set_logout($user_id)
    {
        $this->db->set("fecha_logout", date('Y-m-d H:i:s'));
        $this->db->where("id_user", $user_id);
        $this->db->where("fecha_logout is NULL", NULL, false);
        $this->db->order_by("id", "DESC");
        $this->db->limit(1);
        return $this->db->update("historial_sesiones");
    }

    function validar_ids_comercio_empleado($numero_empleado, $id_comercio)
    {
        $query = "select * from user where id_comercio = $id_comercio and number_employee = '$numero_empleado' and id_comercio != 0";
        $result = $this->db->query($query)->result_array();
        if (count($result) > 0)
            return true;
        return false;
    }

    function actualizar_password($password, $numero_empleado)
    {
        $query = "UPDATE user set password = aes_encrypt('$password','" . KEY_AES . "') WHERE number_employee = '$numero_empleado' ";
        return $this->db->query($query);
    }
}
