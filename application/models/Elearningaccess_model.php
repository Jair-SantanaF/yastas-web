<?php

	class Elearningaccess_model extends CI_Model{

		private $tableName = "elearning_access_log";

		function __construct(){
			parent::__construct();
		}

		public function insert( $entity )
		{
			if( $this->db->insert($this->tableName, $entity) ){
				return $this->db->insert_id();
			}else{
				return false;
			}
		}

		public function update( $id , $entity )
		{
			if($this->db->update($this->tableName , $entity , array("id" => $id))){
				return true;
			}else{
				return false;
			}
		}

		public function delete($id){
	        if($this->db->delete($this->tableName, array('id' => $id))){
	            return true;
	        }else{
	            return false;
	        }
	    }

	    public function fetchAll(){
	    	return $this->db->get($this->tableName)->result_array();
	    }

		public function fetchAllById( $id )
		{
			return $this->db->get_where($this->tableName, array("id" => $id))->result_array();
		}

        public function countAcccess( $user_id , $module_id )
        {
            $query = "SELECT IFNULL(COUNT(*) , 0) as access FROM elearning_access_log WHERE user_id ='".$user_id."' AND modules_id = ". $module_id ." AND type = 1";
            return $this->db->query( $query )->result_array();

        }
	}

