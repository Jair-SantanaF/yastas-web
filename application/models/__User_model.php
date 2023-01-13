<?php


	class User_model extends CI_Model{

		private $tableName = "user",
                $tableInvitation = 'invitation';

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

		public function mailExists( $email )
		{
			$reuslt = $this->db->get_where($this->tableName, array("email" => $email))->result_array();

			if (empty($reuslt)) {
				return false;
			}else{
				return true;
			}
		}
		/***********************************************************************
		 *	Autor: Mario Adrián Martínez Fernández
		 *		   mario.martinez.f@hotmail.es
		 *	Fecha: 15/05/2020
		 *	Nota: Funcion para validar si la cuenta de correo enviada se
         *          encuentra como invitado.
		 ***********************************************************************/
        function ValidateInvitation($email){
            $validate = "select i.*,b.business_name from invitation i join business b on i.business_id = b.id where i.email = '$email'";
            $validate = $this->db->query($validate)->result_array();
            if(count($validate)>0){
                if($validate[0]['status'] == 1){
                    return 'in_use';
                }
                return $validate[0];
            }else{
                return false;
            }
        }
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 15/05/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Funcion para registrar una invitacion enviada.
         ***********************************************************************/
        function SaveInvitation($entity){
            if( $this->db->insert($this->tableInvitation, $entity) ){
                return $this->db->insert_id();
            }else{
                return false;
            }
        }
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 15/05/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Funcion para confirmar que la invitacion ha sido registrado
         *          el usuario con el correo al que se le envio la invitacion.
         ***********************************************************************/
        function ConfirmInvitation($email){
            return $this->db->update($this->tableInvitation, array('status'=>1),array('email'=>$email));
        }
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 05/06/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: funcion para obtner los usuarios de la empresa de la sesion
         ***********************************************************************/
        function ListUsersBusiness($business_id){
            $this->db->select('id,name,last_name,profile_photo');
            $this->db->from($this->tableName);
            $this->db->where('business_id= ', $business_id);
            $list = $this->db->get()->result_array();
            if(count($list) > 0){
                return $list;
            }else{
                return false;
            }
        }
	}


