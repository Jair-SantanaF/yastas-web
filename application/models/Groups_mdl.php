<?php
class Groups_mdl extends CI_Model
{
    private $tableGroups = "groups",
        $tableUsersGroups = "users_groups",
        $tableLibraryGroups = "library_groups",
        $tableLibrary = "library_elements_",
        $tableElearningGroups = "elearning_groups",
        $tableElearning = "elearning_modules",
        $tableUser = "user";

    function __construct()
    {
        parent::__construct();
    }

    function valida_roles()
    {
        if ($this->session->userdata("rol_id") == 6) {
            $id_asesor = $this->session->userdata("id_user");
            $this->db->where("u.id_asesor", $id_asesor);
        }
        if ($this->session->userdata("rol_id") == 5) {
            $region = $this->session->userdata("id_region");
            $this->db->where("u.id_region", $region);
        }
    }

    //validacion de segmentar elementos de biblioteca por grupo
    //se aplican validaciones para region y asesor
    function valida_roles_string($id_asesor = null, $id_region = null)
    {

        if ($this->session->userdata("rol_id") == 6) {
            //el asesor siempre tiene estas validaciones porque pertenece solo a un id_asesor y a una region
            $id_asesor = $this->session->userdata("id_user");
            $id_region = $this->session->userdata("id_region");
            return " and ((l.id_region = $id_region and l.id_asesor = $id_asesor) 
            or (l.id_region  = $id_region and l.id_asesor is null))
            or (l.id_region is null and l.id_asesor is null)";
            //primero se validan los elementos que pertenecen tanto a la region como al asesor
            //la segunda validacion es para elementos que pertenecen a la region (se entiende que estan disponibles para todos lo asesores)
            //la ultima opcion es para aquellos elementos que no estan asignados a una region o asesor, es decir estan disponibles para todos
        }
        if ($this->session->userdata("rol_id") == 5) {
            //estas validaciones son opcionales y funcionan mas como filtros en el admin
            //si el usuario selecciona alguna region o asesor en especifico se le manda esa info
            //de manera predeterminada se le manda todo lo que puede ver 
            //es decir lo de todas sus regiones, sin importar si solo es para un asesor
            //cuando el usuario asigne materiales puede asignarlos a un grupo
            //al momento de darlos de alta el elemento ya tiene un asesor y/o region
            //entonces puede asignarlos a un grupo en general pero el archivo solo sera vivible para las regiones o asesores asignados en el alta
            $cadena = "";
            if ($id_region != null) {
                $cadena = " and l.id_region = $id_region ";
                if ($id_asesor != null) {
                    $cadena .= " and l.id_asesor = $id_asesor ";
                }
            } else {
                //en caso de que el id_region sea nulo entonces no ha filtrado ningun elemento
                //de manera predeterminada se le deben mostrar los elementos que
                //-- esten asignados a todas las regiones
                //-- esten asignados a las regiones den gerente (sin importar el asesor)

                //se obtienen las regiones del gerente
                $regiones = $this->general_mdl->obtener_regiones($this->session->userdata("id_user"), 5); //ese 5 se debe pasar a las constantes
                $regiones_ = [];

                //se recorre cada elemento del array para pasar solo el id a un nuevo array
                for ($i = 0; $i < count($regiones); $i++) {
                    array_push($regiones_, $regiones[$i]["id"]);
                }
                $ids = join(",", $regiones_); //se espera una salida como esta "1,3,4,5,6" //dependiendo de los ids de las regiones
                $cadena = " and l.id_region in ($ids) ir l.id_region is null";
                //se valida que el elemento solo pertenezca a alguna region del gerente 
                //o
                //donde el id_region sea nulo es decir no pertenece a ninguna region (o podriamos decir que esta asignada a todas las regiones)
            }
            return $cadena;
            //se regresa la parte de la cadena que completa el where en las consultas donde se llame
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para guardar un grupo nuevo.
     ***********************************************************************/
    function SaveGroup($data)
    {
        if ($this->db->insert($this->tableGroups, $data)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para editar un grupo
     ***********************************************************************/
    function EditGroup($data)
    {
        $key = array('id' => $data["id"]);
        unset($data['id']);
        if ($this->db->update($this->tableGroups, $data, $key)) {
            return true;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para eliminar un grupo
     ***********************************************************************/
    function DeleteGroup($data)
    {
        $key = array('id' => $data["id"]);
        unset($data['id']);
        if ($this->db->update($this->tableGroups, array('active' => 0), $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener el listado de los grupos existentes.
     ***********************************************************************/
    function GroupsRegister($business_id, $id = '')
    {
        $this->db->select('g.id,g.name');
        $this->db->from($this->tableGroups . ' g');
        $this->db->where('g.business_id = ', $business_id);
        $this->db->where('g.active = ', 1);
        if ($id !== '') {
            $this->db->where('g.id = ', $id);
        }
        $groups = $this->db->get()->result_array();
        if (count($groups) > 0) {
            return $groups;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los usuarios registrados en un grupo
     ***********************************************************************/
    function UsersGroups($data)
    {
        $query = "select ug.id, ug.group_id, ug.user_id, concat(u.name, ' ', u.last_name) AS name, number_employee as numero_empleado
        from users_groups as ug force index (ind_user_id, ind_group_id, ind_active)
        join user as u force index (primary, ind_active, ind_business, ind_region_id, ind_asesor_id) on u.id = ug.user_id
        where ug.group_id = " . $data['group_id'] . "
        and ug.active = 1
        and u.active = 1
        and u.business_id = " . $data['business_id'];

        if ($this->session->userdata("rol_id") == 6) {
            $id_asesor = $this->session->userdata("id_user");
            $query .= " and u.id_asesor = $id_asesor";
        }
        if ($this->session->userdata("rol_id") == 5) {
            $region = $this->session->userdata("id_region");
            $query .= " and u.id_region  = $region";
        }
        $result = $this->db->query($query)->result_array();
        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener a los usuarios que aun no pertenecen
     *          al grupo seleccionado
     ***********************************************************************/
    function NoUsersGroups($data)
    {
        $query = "
            select
            u.id,concat(u.name, ' ', u.last_name) AS name, u.number_employee
            from user u force index (primary,ind_business, ind_active, ind_asesor_id, ind_region_id)
            where
            u.id not in (
            select
            user_id
            from users_groups force index (ind_group_id, ind_active)
            where group_id = " . $data['group_id'] . " and active = 1
            )
            and u.business_id = " . $data['business_id'] . " and u.active = 1
        ";
        if ($this->session->userdata("rol_id") == 6) {
            $id_asesor = $this->session->userdata("id_user");
            $query .= " and u.id_asesor = $id_asesor ";
        }
        if ($this->session->userdata("rol_id") == 5) {
            $region = $this->session->userdata("id_region");
            $query .= " and u.id_region = $region ";
        }
        $query = $this->db->query($query)->result_array();
        if (count($query) > 0) {
            return $query;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para guardar un usuario nuevo a un grupo
     ***********************************************************************/
    function SaverUser($data)
    {
        $query = "delete from users_groups where user_id = " . $data["user_id"];
        $this->db->query($query);
        // $this->db->select('*');
        // $this->db->from($this->tableUsersGroups . ' as ug');
        // $this->db->where('ug.group_id =', $data['group_id']);
        // $this->db->where('ug.user_id =', $data['user_id']);
        // $result = $this->db->get()->result_array();
        // if (count($result) > 0) {
        //     $key = array('id' => $result[0]["id"]);
        //     $dataa = array(
        //         'active' => 1
        //     );
        //     if ($this->db->update($this->tableUsersGroups, $dataa, $key)) {
        //         return true;
        //     } else {
        //         return false;
        //     }
        // } else {
        $dataa = array(
            "group_id" => $data["group_id"],
            'user_id' => $data["user_id"],
            "active" => 1
        );
        if ($this->db->insert($this->tableUsersGroups, $dataa)) {
            return true;
        } else {
            return false;
        }
        // }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: funcion para eliminar un usuario de un grupo (Solo se
     *          desactivara, esto para saber si en algun momento pertenecio
     *          a ese grupo)
     ***********************************************************************/
    function DeleteUser($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableUsersGroups, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/6/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los elemento de biblioteca que ya estan
     *          asignados
     ***********************************************************************/
    function LibraryGroups($data)
    {
        $this->db->select('lg.id, lg.group_id, lg.library_id, l.title,l.text, l.type, l.image, l.file, l.link, l.video, l.type_video, l.category_id,c.name as category, s.subcategory');
        $this->db->from($this->tableLibraryGroups . ' as lg');
        $this->db->join($this->tableLibrary . ' as l', 'lg.library_id = l.id');
        $this->db->join("library_category" . ' as c', 'l.category_id = c.id');
        $this->db->join("library_subcategory" . ' as s', 's.id = l.subcategory_id', 'left');
        $this->db->where('lg.group_id =', $data['group_id']);
        $this->db->where('lg.active =', 1);
        $this->db->where('l.active =', 1);
        $result = $this->db->get()->result_array();

        foreach ($result as $index => $value) {
            $this->db->select("g.name");
            $this->db->from("library_groups as lg");
            $this->db->join("groups as g", "g.id = lg.group_id");
            $this->db->where("lg.library_id", $result[$index]["library_id"]);
            $grupos_result = $this->db->get()->result_array();
            $grupos = "";

            for ($i = 0; $i < count($grupos_result); $i++) {
                if ($i != count($grupos_result) - 1)
                    $grupos .= $grupos_result[$i]["name"] . ", ";
                else
                    $grupos .= $grupos_result[$i]["name"];
            }
            $result[$index]["grupos"] = $grupos;

            $result[$index]['video_id'] = '';
            if ($value['image'] !== '') {
                $result[$index]['image'] = base_url('uploads/business_' . $data['business_id'] . '/library/') . $value['image'];
            } else {
                $result[$index]['image'] = base_url('assets/img/img_h_biblio.png');
            }
            switch ($value['type_video']) {
                case 'servidor':
                    $result[$index]['video'] = base_url('uploads/business_' . $data['business_id'] . '/library/') . $value['video'];
                    break;
                case 'youtube':
                    $result[$index]['video_id'] = $value['video'];
                    // $library[$index]['video'] = 'https://youtu.be/' . $value['video'];
                    $result[$index]['video'] = $value['video'];
                    break;
                case 'vimeo':
                    $result[$index]['video_id'] = $value['video'];
                    $result[$index]['video'] = 'https://player.vimeo.com/video/' . $value['video'];
                    break;
                default:
            }
            if ($value['file'] !== '') {
                if (!filter_var($value['file'], FILTER_VALIDATE_URL)) {
                    $result[$index]['file'] = base_url('uploads/business_' . $data['business_id'] . '/library/') . $value['file'];
                }
            }

            $query_capacitaciones = "SELECT 
                l.id,
                l.name
            FROM capacit_detail AS d
                INNER JOIN capacit_list AS l
                    ON (l.id = d.id_capacitacion)
                INNER JOIN capacit_categorias as cc on cc.id = d.catalog
            WHERE cc.`catalog` = 'library_elements_' AND d.id_elemento = " . $result[$index]['library_id'] . ";";

            $resultado = $this->db->query($query_capacitaciones)->result_array();
            if (count($resultado) > 0) {
                $result[$index]['capacitaciones'] = $resultado;
            } else {
                $result[$index]['capacitaciones'] = [];
            }
        }
        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    function PodcastGroups($data)
    {
        $this->db->select('lg.id, lg.group_id, lg.podcast_id, l.title');
        $this->db->from("podcast_groups" . ' as lg');
        $this->db->join("podcast" . ' as l', 'lg.podcast_id = l.id');
        $this->db->where('lg.group_id =', $data['group_id']);
        $this->db->where('lg.active =', 1);
        // $this->db->where('l.active =', 1);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/6/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los elementos de biblioteca que no estan
     *          asignados en un grupo
     ***********************************************************************/
    function NoLibraryGroups($data)
    {
        $query = "
            select
                   l.id,l.title,l.text, l.type, l.image, l.file, l.link, l.video, l.type_video, l.category_id,c.name as category, s.subcategory
            from " . $this->tableLibrary . " as l
            join library_category as c on l.category_id = c.id
            left join library_subcategory as s on s.id = l.subcategory_id
            where
                            l.id not in(
                                select
                                       library_id
                                from " . $this->tableLibraryGroups . "
                                where group_id = " . $data['group_id'] . " and active = 1
                                  )
            and l.business_id = " . $data['business_id'] . " and l.active = 1 and l.capacitacion_obligatoria = 0;
        ";
        // $this->db->select('lg.id, lg.group_id, lg.library_id, l.title,l.text, l.type, l.image, l.file, l.link, l.video, l.type_video, l.category_id,c.name as category, s.subcategory');
        // $this->db->from($this->tableLibraryGroups . ' as lg');
        // $this->db->join($this->tableLibrary . ' as l', 'lg.library_id = l.id');
        // $this->db->join("library_category" . ' as c', 'l.category_id = c.id');
        // $this->db->join("library_subcategory" . ' as s', 's.id = l.subcategory_id', 'left');
        // $this->db->where('lg.group_id =', $data['group_id']);
        // $this->db->where('lg.active =', 1);
        // $this->db->where('l.active =', 1);
        // $result = $this->db->get()->result_array();
        $result = $this->db->query($query)->result_array();
        foreach ($result as $index => $value) {
            $this->db->select("g.name");
            $this->db->from("library_groups as lg");
            $this->db->join("groups as g", "g.id = lg.group_id");
            $this->db->where("lg.library_id", $result[$index]["id"]);
            $grupos_result = $this->db->get()->result_array();
            $grupos = "";

            for ($i = 0; $i < count($grupos_result); $i++) {
                if ($i != count($grupos_result) - 1)
                    $grupos .= $grupos_result[$i]["name"] . ", ";
                else
                    $grupos .= $grupos_result[$i]["name"];
            }
            $result[$index]["grupos"] = $grupos;

            $result[$index]['video_id'] = '';
            if ($value['image'] !== '') {
                $result[$index]['image'] = base_url('uploads/business_' . $data['business_id'] . '/library/') . $value['image'];
            } else {
                $result[$index]['image'] = base_url('assets/img/img_h_biblio.png');
            }
            switch ($value['type_video']) {
                case 'servidor':
                    $result[$index]['video'] = base_url('uploads/business_' . $data['business_id'] . '/library/') . $value['video'];
                    break;
                case 'youtube':
                    $result[$index]['video_id'] = $value['video'];
                    // $library[$index]['video'] = 'https://youtu.be/' . $value['video'];
                    $result[$index]['video'] = $value['video'];
                    break;
                case 'vimeo':
                    $result[$index]['video_id'] = $value['video'];
                    $result[$index]['video'] = 'https://player.vimeo.com/video/' . $value['video'];
                    break;
                default:
            }
            if ($value['file'] !== '') {
                if (!filter_var($value['file'], FILTER_VALIDATE_URL)) {
                    $result[$index]['file'] = base_url('uploads/business_' . $data['business_id'] . '/library/') . $value['file'];
                }
            }

            $query_capacitaciones = "SELECT 
                l.id,
                l.name
            FROM capacit_detail AS d
                INNER JOIN capacit_list AS l
                    ON (l.id = d.id_capacitacion)
                INNER JOIN capacit_categorias as cc on cc.id = d.catalog
            WHERE cc.`catalog` = 'library_elements_' AND d.id_elemento = " . $result[$index]['id'] . ";";

            $resultado = $this->db->query($query_capacitaciones)->result_array();
            if (count($resultado) > 0) {
                $result[$index]['capacitaciones'] = $resultado;
            } else {
                $result[$index]['capacitaciones'] = [];
            }
        }
        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    function NoPodcastGroups($data)
    {
        $query = "
            select
                   l.id,l.title
            from podcast l where
                            l.id not in(
                                select
                                       podcast_id
                                from podcast_groups
                                where group_id = " . $data['group_id'] . " and active = 1
                                  )
            and l.capacitacion_obligatoria = 0
            and l.business_id = " . $data['business_id'] . ";
        ";
        $query = $this->db->query($query)->result_array();
        if (count($query) > 0) {
            return $query;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/6/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para agregar un elemento a biblioteca
     ***********************************************************************/
    function SaveLibrary($data)
    {
        $this->db->select('*');
        $this->db->from($this->tableLibraryGroups . ' as lg');
        $this->db->where('lg.group_id =', $data['group_id']);
        $this->db->where('lg.library_id =', $data['library_id']);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            $key = array('id' => $result[0]["id"]);
            $dataa = array(
                'active' => 1
            );
            if ($this->db->update($this->tableLibraryGroups, $dataa, $key)) {
                return true;
            } else {
                return false;
            }
        } else {
            $dataa = array(
                "group_id" => $data["group_id"],
                'library_id' => $data["library_id"],
                "active" => 1
            );
            if ($this->db->insert($this->tableLibraryGroups, $dataa)) {
                return true;
            } else {
                return false;
            }
        }
    }

    function SavePodcast($data)
    {
        $this->db->select('*');
        $this->db->from("podcast_groups" . ' as lg');
        $this->db->where('lg.group_id =', $data['group_id']);
        $this->db->where('lg.podcast_id =', $data['podcast_id']);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            $key = array('id' => $result[0]["id"]);
            $dataa = array(
                'active' => 1
            );
            if ($this->db->update("podcast_groups", $dataa, $key)) {
                return true;
            } else {
                return false;
            }
        } else {
            $dataa = array(
                "group_id" => $data["group_id"],
                'podcast_id' => $data["podcast_id"],
                "active" => 1
            );
            if ($this->db->insert("podcast_groups", $dataa)) {
                return true;
            } else {
                return false;
            }
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/6/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para eliminar un registro de biblioteca de biblioteca
     ***********************************************************************/
    function DeleteLibrary($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableLibraryGroups, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    function DeletePodcast($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update("podcast_groups", $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/6/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los elemento de elearning que ya estan
     *          asignados
     ***********************************************************************/
    function ElearningGroups($data)
    {
        $this->db->select('eg.id, eg.group_id, eg.elearning_id, e.title');
        $this->db->from($this->tableElearningGroups . ' as eg');
        $this->db->join($this->tableElearning . ' as e', 'eg.elearning_id = e.id');
        $this->db->where('eg.group_id =', $data['group_id']);
        $this->db->where('eg.active =', 1);
        $this->db->where('e.active =', 1);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/6/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los elementos de elearning que no estan
     *          asignados en un grupo
     ***********************************************************************/
    function NoElearningGroups($data)
    {
        $query = "
            select
                   e.id,e.title
            from " . $this->tableElearning . " e where
                            e.id not in(
                                select
                                       elearning_id
                                from " . $this->tableElearningGroups . "
                                where group_id = " . $data['group_id'] . " and active = 1
                                  )
            and e.business_id = " . $data['business_id'] . " and e.active = 1;
        ";
        $query = $this->db->query($query)->result_array();
        if (count($query) > 0) {
            return $query;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/6/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para agregar un elemento a elearning
     ***********************************************************************/
    function SaveElearning($data)
    {
        $this->db->select('*');
        $this->db->from($this->tableElearningGroups . ' as eg');
        $this->db->where('eg.group_id =', $data['group_id']);
        $this->db->where('eg.elearning_id =', $data['elearning_id']);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            $key = array('id' => $result[0]["id"]);
            $dataa = array(
                'active' => 1
            );
            if ($this->db->update($this->tableElearningGroups, $dataa, $key)) {
                return true;
            } else {
                return false;
            }
        } else {
            $dataa = array(
                "group_id" => $data["group_id"],
                'elearning_id' => $data["elearning_id"],
                "active" => 1
            );
            if ($this->db->insert($this->tableElearningGroups, $dataa)) {
                return true;
            } else {
                return false;
            }
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/6/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para eliminar un registro de biblioteca de biblioteca
     ***********************************************************************/
    function DeleteElearning($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableElearningGroups, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    function desasociar_grupo($catalogo, $id_elemento, $id_grupo)
    {
        $campo = $this->obtener_campo($catalogo);
        $query = "delete from $catalogo where $campo = $id_elemento and group_id = $id_grupo";
        return $this->db->query($query);
    }

    function asociar_grupo($catalogo, $id_elemento, $id_grupo)
    {
        $campo = $this->obtener_campo($catalogo);
        $query = "insert into $catalogo ($campo,group_id) values($id_elemento, $id_grupo)";
        return $this->db->query($query);
    }

    function actualizar_grupos_elementos($catalogo, $grupos, $id_elemento)
    {
        $campo = $this->obtener_campo($catalogo);
        for ($i = 0; $i < count($grupos); $i++) {
            $seleccionado = $grupos[$i]["seleccionado"];
            $id_grupo = $grupos[$i]["id"];
            if ($seleccionado) {
                $query = "select * from $catalogo where $campo = $id_elemento and group_id = $id_grupo";
                $result = $this->db->query($query)->result_array();
                if (count($result) > 0) {
                } else {
                    $this->asociar_grupo($catalogo, $id_elemento, $id_grupo);
                }
            } else {
                $this->desasociar_grupo($catalogo, $id_elemento, $id_grupo);
            }
        }
        return true;
    }

    function obtener_campo($catalogo)
    {
        $campo = "";
        if ($catalogo == "library_groups") {
            $campo = "library_id";
        }
        if ($catalogo == "com_groups") {
            $campo = "com_id";
        }
        if ($catalogo == "capacit_groups") {
            $campo = "capacit_id";
        }
        if ($catalogo == "wall_groups") {
            $campo = "wall_id";
        }
        if ($catalogo == "podcast_groups") {
            $campo = "podcast_id";
        }
        if ($catalogo == "quiz_groups") {
            $campo = "quiz_id";
        }
        return $campo;
    }
}
