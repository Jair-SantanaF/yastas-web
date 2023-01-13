<?php
class Events_mdl extends CI_Model
{
    private $tableEvents = 'events',
        $tableMembers = 'events_members',
        $tableUser = 'user';

    function __construct()
    {
        parent::__construct();
    }
    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para guardar un nuevo evento
     ***********************************************************************/
    function SaveEventAdmin($data)
    {
        $dataa = array(
            "business_id" => $data["business_id"],
            "user_id" => $data["user_id"],
            'description' => $data["description"],
            'note' => $data["note"],
            'date' => $data["date"],
            'time_start' => $data["time_start"],
            'time_end' => $data["time_end"],
            "active" => 1
        );

        if ($this->db->insert($this->tableEvents, $dataa)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para editar un evento
     ***********************************************************************/
    function EditEvent($data)
    {
        $members = (isset($data['members'])) ? $data['members'] : '';
        if (isset($data['members'])) {
            unset($data['members']);
        }
        $key = array('id' => $data["id"]);

        $dataa = array(
            'description' => $data["description"],
            'note' => $data["note"],
            'date' => $data["date"],
            'time_start' => $data["time_start"],
            'time_end' => $data["time_end"]
        );

        if ($this->db->update($this->tableEvents, $dataa, $key)) {
            if ($members !== '') {
                $this->db->delete('events_members', array('event_id' => $data['id']));
                $members = explode(',', $members);
                foreach ($members as $index => $value) {
                    $this->db->insert('events_members', array('user_id' => $value, 'event_id' => $data["id"]));
                }
            } else {
                $this->db->delete('events_members', array('event_id' => $data['id']));
            }
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar un evento
     ***********************************************************************/
    function DeleteEvent($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableEvents, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/05/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para optener los eventos
     ***********************************************************************/
    function Events($business_id, $user_id, $rol_id, $date)
    {
        $this->db->select('e.*,concat(u.name," ",u.last_name) as usuario');
        $this->db->from($this->tableEvents . ' as e');
        $this->db->join("user as u", "u.id = e.user_id");
        if ($date !== '') {
            $this->db->where('date = date("' . $date . '")');
        }
        $this->db->where("e.business_id", $business_id);
        $this->db->where("e.active", 1);
        // $result = $this->db->get_where($this->tableEvents, array('business_id' => $business_id, 'active' => 1))->result_array();
        $result = $this->db->get()->result_array();
        
        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para guardar un miembro
     ***********************************************************************/
    function SaveMember($data)
    {
        $this->db->select('*');
        $this->db->from($this->tableMembers . ' as m');
        $this->db->where('m.event_id =', $data['event_id']);
        $this->db->where('m.user_id =', $data['user_id']);
        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            $key = array('id' => $result[0]["id"]);
            $dataa = array(
                'active' => 1
            );
            if ($this->db->update($this->tableMembers, $dataa, $key)) {
                return true;
            } else {
                return false;
            }
        } else {
            $dataa = array(
                "event_id" => $data["event_id"],
                'user_id' => $data["user_id"],
                "active" => 1
            );
            if ($this->db->insert($this->tableMembers, $dataa)) {
                return true;
            } else {
                return false;
            }
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar un miembro
     ***********************************************************************/
    function DeleteMember($data)
    {
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if ($this->db->update($this->tableMembers, $dataa, $key)) {
            return true;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para listar los miembros de un evento
     ***********************************************************************/
    function Members($data)
    {
        $this->db->select(
            '
            m.id, m.event_id, m.user_id, concat(u.name, " ", u.last_name) AS name, u.profile_photo'
        );
        $this->db->from($this->tableEvents . ' as e');
        $this->db->join($this->tableMembers . ' as m', 'e.id = m.event_id');
        $this->db->join($this->tableUser . ' as u', 'm.user_id = u.id');
        $this->db->where('e.business_id =', $data['business_id']);
        $this->db->where('m.event_id =', $data['event_id']);
        $this->db->where('m.active =', 1);

        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 28/07/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para listar los usuarios disponibles para convertirse en miembros de un evento
     ***********************************************************************/
    function NoMembers($data)
    {
        $query = "
            SELECT u.id, concat(u.name, ' ', u.last_name) AS name
            FROM
            (SELECT * FROM user u WHERE u.business_id = " . $data['business_id'] . ") u
            LEFT JOIN
            (SELECT * FROM events_members WHERE event_id = " . $data['event_id'] . " and active = 1 ) em ON u.id = em.user_id
            WHERE em.user_id IS NULL
        ";

        $result = $this->db->query($query)->result_array();
        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 17/06/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para guardar los eventos
     ***********************************************************************/
    function SaveEvent($data)
    {
        $members = (isset($data['members'])) ? $data['members'] : '';
        unset($data['token']);
        if (isset($data['members'])) {
            unset($data['members']);
        }
        if ($this->db->insert('events', $data)) {
            $id = $this->db->insert_id();
            if ($members !== '') {
                $members = explode(',', $members);
                foreach ($members as $index => $value) {
                    $this->db->insert('events_members', array('user_id' => $value, 'event_id' => $id));
                }
            }
            return $id;
        }
        return false;
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 17/06/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener los eventos registrados
     ***********************************************************************/
    function ListEvents($data)
    {
        $query = "
            select 
                e.id,
                e.date,
                e.time_start, 
                e.time_end, 
                e.user_id, 
                e.note, 
                e.description 
            from events e
                left join events_members em on e.id = em.event_id and em.active = 1
                left join user u on u.id = em.user_id 
            WHERE e.date = '" . $data['date'] . "' 
            and e.business_id = " . $data['business_id'] . "
            and ( e.user_id = " . $data['user_id'] . " or u.id = " . $data['user_id'] . ")
            and e.active = 1
            group by e.id";
        $query = $this->db->query($query)->result_array();
        if (count($query) > 0) {
            foreach ($query as $index => $value) {
                /***********************************************************************
                 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/13/2020
                 *		   mario.martinez.f@hotmail.es
                 *	Nota: Recorremos el arreglo para obtener si es propietario del
                 *          evento para poder editar o eliminar un evento
                 ***********************************************************************/
                if ($value['user_id'] == $data['user_id']) {
                    $query[$index]['owner'] = 1;
                } else {
                    $query[$index]['owner'] = 0;
                }
                $members = "
                    select em.id, user_id, concat(u.name,' ',u.last_name) as name,u.profile_photo 
                    from events_members em 
                    join user u on em.user_id = u.id 
                    where 
                        em.event_id = " . $value['id'] . " 
                        and em.active = 1";
                $members = $this->db->query($members)->result_array();
                if (count($members) > 0) {
                    $query[$index]['members'] = $members;
                } else {
                    $query[$index]['members'] = array();
                }
            }
            return $query;
        } else {
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 22/06/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: funcion para obtener los las fechas que tienen evenetos
     *          registrados.
     ***********************************************************************/
    function listDateEvents($business_id, $user_id, $rol_id)
    {
        $validacion = '';
        if ($rol_id == ROL_ADMINISTRADOR_EMPRESA) {
            $validacion .= " business_id = " . $business_id . " and";
        }
        if ($rol_id == ROL_INTEGRANTE) {
            $validacion .= " ( e.user_id = " . $user_id . " or em.user_id = " . $user_id . ") and business_id = " . $business_id . " and";
        }
        $query = "select date from events e left join events_members em on e.id = em.event_id and em.active = 1 WHERE " . $validacion . " e.active = 1 group by date";

        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 12/15/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Se agrega una validacion para obtener los eventos dependiendo
         *          de la sesion.
         ***********************************************************************/
        $query = $this->db->query($query)->result_array();
        return $query;
    }
}
