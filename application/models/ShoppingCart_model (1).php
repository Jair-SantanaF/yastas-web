<?php 

	class ShoppingCart_model extends CI_Model{
		
		private $tableName = "user_shopping_cart";

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

		public function delete($id , $user_id){
	        if($this->db->delete($this->tableName, array('id' => $id , 'user_id' => $user_id))){
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


		public function getMyShoppingCart( $user_id )
		{
			$query = "SELECT 
							sh.id,
							s.service_name,
							s.description,
							c.category_name,
							sh.quantity,
							sh.discount,
							s.price
					  FROM user_shopping_cart sh
					  LEFT JOIN services s ON s.id = sh.service_id
					  LEFT JOIN categories c ON c.id = s.category_id
					  WHERE sh.user_id = $user_id";
			return $this->db->query( $query )->result_array();
		}
	}

?>

