<?php

	class Shoppingcart_model extends CI_Model{

		private $tableName = "user_shopping_cart",
            $table_purchase = "services_purchase_preview";

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
							s.price,
                            s.image
					  FROM user_shopping_cart sh
					  LEFT JOIN services s ON s.id = sh.service_id
					  LEFT JOIN categories c ON c.id = s.category_id
					  WHERE sh.user_id = $user_id";
			return $this->db->query( $query )->result_array();
		}
		/***********************************************************************
		 *	Autor: Mario Adrián Martínez Fernández   Fecha: 23/06/2020
		 *		   mario.martinez.f@hotmail.es
		 *	Nota: Funcion para validar que no agreguen al carro de compras
         *          un item ya agregado
		 ***********************************************************************/
        function validateItemShopping($user_id,$service_id){
            return (count($this->db->get_where($this->tableName, array("service_id" => $service_id, "user_id"=>$user_id))->result_array()) > 0)?true:false;
        }
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/10/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Funcion para pasar los registros comprados a una tabla temporal
         *          para hacer la compra.
         ***********************************************************************/
        function ServicesPurchase($user_id,$business_id){
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/10/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Obtenemos los serivicios que tienen el carro el usuario
             ***********************************************************************/
            $services_purchase = $this->db->get_where($this->tableName, array("user_id"=>$user_id))->result_array();
            foreach ($services_purchase as $index => $value){
                /***********************************************************************
                 *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/10/2020
                 *		   mario.martinez.f@hotmail.es
                 *	Nota: Validamos que no se encuentre en la tabla previamente para
                 *          evitar registros duplicados
                 ***********************************************************************/
                $validate = $this->db->get_where($this->table_purchase, array('user_id'=>$value['user_id'],'service_id'=>$value['service_id'],'business_id'=>$business_id))->result_array();
                if(count($validate) === 0){
                    /***********************************************************************
                     *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/10/2020
                     *		   mario.martinez.f@hotmail.es
                     *	Nota: Recorremos el arreglo de lo que se encuentra en el carrito de
                     *          comparas, lo recorremos e insertamos los registros en una
                     *          tabla temporal para que el carrito se libere
                     ***********************************************************************/
                    $this->db->insert($this->table_purchase,
                        array(
                            'user_id'=>$value['user_id'],
                            'service_id'=>$value['service_id'],
                            'business_id'=>$business_id
                        )
                    );
                }

            }
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/10/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Una vez que terminamos de recorrer el proces eliminamos lo
             *      que se encuentra en el carrito de compras pendiente.
             ***********************************************************************/
            $this->db->delete($this->tableName,array('user_id'=>$user_id));
        }
	}

