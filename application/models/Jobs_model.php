<?php 

	class Jobs_model extends CI_Model{
		
		private $tableName = "jobs";

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

		public function fetchAll()
		{
			return $this->db->get($this->tableName)->result_array();
		}

		public function fetchAllById( $id )
		{
			return $this->db->get_where($this->tableName, array("id" => $id))->result_array();
		}

		public function fetchAllByBusinessId( $id )
		{
			return $this->db->get_where($this->tableName, array("business_id" => $id))->result_array();
		}


		public function findJobName( $job_name , $business_id  )
		{
			$result = $this->db->get_where($this->tableName, array("job_name" => $job_name, "business_id" => $business_id ))->result_array();

			if (!empty($result)) {
				return $result[0]['id'];
			}else{
				// $this->db->insert($this->tableName, array("job_name" => $job_name, "business_id" => $business_id));
				// return $this->db->insert_id();
				return false;
			}
		}
	}


