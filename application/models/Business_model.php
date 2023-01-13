<?php

class Business_model extends CI_Model{

    private $tableName = "business";

    function __construct(){
        parent::__construct();
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 22/12/2020
     *		   urisancer@gmail.com
     *	Nota: Funcion para obtener las empresas
     ***********************************************************************/
    function BusinessList($params){
        $this->db->select('b.*, p.id AS plan_id, p.name as plan_name, p.num_users, p.sections, p.space as plan_space');
        $this->db->from("business as b");
        $this->db->join('configuration as c','b.id = c.business_id');
        $this->db->join('plans as p','c.value = p.id AND c.name = "plan"');
        $this->db->where("active", 1);

        if(isset($params['business_id']) && $params['business_id'] != ''){
            $this->db->where("b.id", $params['business_id']);
        }

        $business = $this->db->get()->result_array();
        if(count($business) > 0){
            //Obtener el espacio utilizado y numero de usuarios por cada empresa
            foreach ($business as $index => $value){
                $this->db->select('u.*');
                $this->db->from("user u");
                $this->db->where("business_id", $value['id']);
                $this->db->where("active", 1);
                $users = $this->db->get()->result_array();
                $business[$index]['users'] = count($users);

                //Espacio en disco
                $f = getcwd().'/uploads/business_'.$value['id'];
                $path = realpath($f);
                $used_space = 0;
                if($path){
                    $used_space = $this->dirSize($f);
                    $format_used_space = $this->formatBytes($used_space, 2);
                    $business[$index]['used_space'] = $used_space;
                    $business[$index]['formatted_used_space'] = $format_used_space['size'].$format_used_space['prefix'];
                }else{
                    $business[$index]['used_space'] = 0;
                    $business[$index]['formatted_used_space'] = "0B";
                }

                if ($business[$index]['plan_id'] == 5){
                    $business[$index]['formatted_plan_space'] = "0B";
                    $business[$index]['percentage_used_space'] = 0;
                }else{
                    $format_plan_space = $this->formatBytes($business[$index]['plan_space'], 2);
                    $business[$index]['formatted_plan_space'] = $format_plan_space['size'].$format_plan_space['prefix'];

                    $percentage = ($used_space * 100) / $business[$index]['plan_space'];
                    if($percentage>100){ $percentage = 100;}
                    $business[$index]['percentage_used_space'] = $percentage;
                }
            }
            return $business;
        }else{
            return false;
        }
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 12/22/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para obtener el espacio
     ***********************************************************************/
    function dirSize($directory) {
        $size = 0;
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file){
            $size+=$file->getSize();
        }
        return $size;
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 12/22/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para convertir los bytes a KB,MB,GB,TB
     ***********************************************************************/
    function formatBytes($size, $precision = 0)
    {
        if($size == 0){return array("prefix"=>"B", "size"=>"0");}

        $base = log($size, 1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');

        return array('prefix'=>$suffixes[floor($base)],'size'=>round(pow(1024, $base - floor($base)), $precision));
    }


    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 07/01/2021
     *		   urisancer@gmail.com
     *	Nota: Funcion para guardar una empresa y su plan asociado
     ***********************************************************************/
    function SaveBusiness($entity){
        $business = array(
            "business_name" => $entity["business_name"],
            "active" => 1
        );
        if( $this->db->insert($this->tableName, $business) ){
            $business_id = $this->db->insert_id();
            $configuration = array(
                "business_id" => $business_id,
                "name" => "plan",
                "value" => $entity["plan_id"]
            );
            $this->db->insert("configuration", $configuration);

            $carpeta_nueva = "./uploads/business_".$business_id;
            $carpeta_default = "./uploads/default";
            $this->copiar($carpeta_default, $carpeta_nueva);

            return $business_id;
        }else{
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 07/01/2021
     *		   urisancer@gmail.com
     *	Nota: Funcion para editar una empresa
     ***********************************************************************/
    function EditBusiness($id, $params){
        $key = array('id' => $id);
        $business = array('business_name' => $params["business_name"]);
        if($this->db->update($this->tableName, $business, $key)){
            $where = array(
                "business_id" => $id,
                "name" => "plan"
            );
            $configuration = array(
                "value" => $params["plan_id"]
            );
            if($this->db->update("configuration", $configuration, $where)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 07/01/2021
     *		   urisancer@gmail.com
     *	Nota: Funcion para eliminar una empresa
     ***********************************************************************/
    function DeleteBusiness($data){
        $key = array('id' => $data["id"]);
        $dataa = array(
            'active' => 0
        );

        if($this->db->update($this->tableName, $dataa, $key)){
            return true;
        }else{
            return false;
        }
    }

    /***********************************************************************
     *	Autor: Uriel Sánchez Cervantes   Fecha: 07/01/2021
     *		   urisancer@gmail.com
     *	Nota: Funcion para obtener el listado de planes disponibles
     ***********************************************************************/
    function PlansList(){
        $this->db->select('p.*');
        $this->db->from("plans as p");
        $plans = $this->db->get()->result_array();
        if(count($plans) > 0){
            foreach ($plans as $index => $value){
                $formatted_space = $this->formatBytes($plans[$index]['space'], 2);
                $plans[$index]['formtted_space'] = $formatted_space['size'].$formatted_space['prefix'];
            }
        }
        return $plans;
    }

    /***********************************************************************
     *  Autor: Uriel Sánchez Cervantes   Fecha: 22/12/2020
     *		   urisancer@gmail.com
     *  Nota: Funcion para copiar carpetas y archivos principales para una empresa
     ***********************************************************************/
    function copiar($fuente, $destino){
        if(is_dir($fuente)){
            $dir=opendir($fuente);
            while($archivo=readdir($dir)){
                if($archivo!="." && $archivo!=".."){
                    if(is_dir($fuente."/".$archivo)){
                        if(!is_dir($destino."/".$archivo)){
                            mkdir($destino."/".$archivo, 0777, true);
                        }
                        $this->copiar($fuente."/".$archivo, $destino."/".$archivo);
                    }else{
                        copy($fuente."/".$archivo, $destino."/".$archivo);
                    }
                }
            }
            closedir($dir);
        }else{
            copy($fuente, $destino);
        }
    }
}
