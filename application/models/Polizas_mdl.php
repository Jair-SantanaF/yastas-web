<?php
class Polizas_mdl extends CI_Model
{
    private $tablePoliza = "polizas";
    private $tablePolizaUser = "polizas_usuario";
    private $tableUser = "user";

    public function __construct()
    {
        parent::__construct();
    }

    public function listUserWithPoliza($business_id){
        $query = "SELECT u.id, CONCAT(u.name, u.last_name) as nombre FROM {$this->tablePolizaUser} pu
        JOIN {$this->tableUser} u 
            ON u.id = pu.user_id
        LEFT JOIN {$this->tablePoliza} p
            ON p.id = pu.poliza_id
            AND p.active = 1
        WHERE u.business_id = '".$business_id."' AND p.active = 1
        GROUP BY u.id";
        $result = $this->db->query($query)->result_array();
        if(count($result) > 0 ){
            foreach($result as $i => $r){
                $result[$i]["polizas"] = $this->getPolizasByUserId($r['id'], $business_id);
            }
        }
        return $result;
    }

    public function getPolizasByUserId($user_id, $business_id){
        $query = "SELECT p.* FROM {$this->tablePoliza} p
        JOIN {$this->tablePolizaUser} pu 
            ON pu.poliza_id = p.id AND pu.user_id = '".$user_id."'
        WHERE p.active = 1";
        $result = $this->db->query($query)->result_array();
        if(count($result) > 0 ){
            foreach($result as $i => $r){
                $result[$i]["url"] = $this->definir_archivo($r["url"], $business_id);
            }
        }
        return $result;
    }

    public function savePoliza($data)
    {
        $this->db->insert($this->tablePoliza, $data);
        return $this->db->insert_id();
    }

    public function savePolizaUsuario($data)
    {
        $this->db->insert($this->tablePolizaUser, $data);
    }

    function eliminarPoliza($id)
    {
        $id = array('id' => $id);
        $data = array(
            'active' => 0,
        );
        return $this->db->update($this->tablePoliza, $data, $id);
    }

    function definir_archivo($file, $business_id)
    {
        $ruta = '';
        if ($file !== '') {
            if (!filter_var($file, FILTER_VALIDATE_URL)) {
                $ruta = base_url('uploads/business_' . $business_id . '/polizas/') . $file;
            }
        }
        return $ruta;
    }
}
