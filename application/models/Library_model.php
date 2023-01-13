<?php 


	class Library_model extends CI_Model{
		
		private $tableName = "library";

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

		public function fetchAllByMediaType( $media_type )
		{
			if($media_type != ""){
				return $this->db->get_where($this->tableName, array("media_type" => $media_type))->result_array();
			}else{
				return $this->db->get($this->tableName)->result_array();
			}
		}
		
	}


