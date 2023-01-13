<?php
class Service_model extends CI_Model
{

    private $tablePurchaseService = "services_purchase_preview",
        $tableServices = "services",
        $tableCategories = "categories",
        $tableBusiness = "business",
        $tableHiredServices = "hired_services";

    function __construct()
    {
        parent::__construct();
    }

    /***********************************************************************
     *    Autor: Uriel Sánchez Cervantes   Fecha: 11/12/2020
     *           urisancer@gmail.com
     *    Nota:
     ***********************************************************************/
    function ApprovePurchaseService($data)
    {

        $servicio = $this->PurchaseServicesList($data);

        if($servicio){
            $servicio = $servicio[0];
            $this->DeletePurchaseService($data);

            $hired_service = array(
                "business_id" => $servicio["business_id"],
                "services_id" => $servicio["service_id"],
                "view" => 1
            );

            if($this->db->insert($this->tableHiredServices, $hired_service)){
                return true;
            }else{
                return false;
            }
        }
    }

    /***********************************************************************
     *    Autor: Uriel Sánchez Cervantes   Fecha: 11/12/2020
     *           urisancer@gmail.com
     *    Nota:
     ***********************************************************************/
    public function DeletePurchaseService($data){
        if($this->db->delete($this->tablePurchaseService, array('id' => $data["id"]))){
            return true;
        }else{
            return false;
        }
    }

    /***********************************************************************
     *    Autor: Uriel Sánchez Cervantes   Fecha: 11/12/2020
     *           urisancer@gmail.com
     *    Nota:
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
        $this->db->join($this->tableServices.' AS s', 'spp.service_id = s.id');
        $this->db->join($this->tableBusiness.' AS b', 'spp.business_id = b.id');
        $this->db->join($this->tableCategories.' AS c', 's.category_id = c.id');

        if(isset($params['id'])){
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

}


