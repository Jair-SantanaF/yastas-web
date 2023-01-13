<?php 


	class Comments_model extends CI_Model{
		
		private $tableName = "wall_comments";

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

		public function getCommentsByPost($post_id)
		{
			$query = "SELECT 
							w.* ,
							concat( u.name , ' ' , u.last_name) as 'nombre_creador',
							u.profile_photo
						FROM wall_comments w
						LEFT JOIN user u ON u.id = w.user_id
					WHERE w.post_id = $post_id";
			return $this->db->query( $query )->result_array();
		}
		
	}


