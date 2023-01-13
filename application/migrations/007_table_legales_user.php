<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
class Migration_Table_Legales_Users extends CI_Migration { 
    public function up(){ 
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'comment' => 'Es la referencia de la tabla user'
            ],
            'acepta_aviso' => [
                'type' => 'TINYINT',
                'default' => TRUE,
                'comment' => 'Para saber si el aviso de privacidad fue aceptado'
            ],
            'acepta_terminos' => [
                'type' => 'TINYINT',
                'default' => TRUE,
                'comment' => 'Para saber si los terminos y condiciones fueron aceptados'
            ],
            'fecha_aviso_privacidad' => [
                'type' => 'datetime',
                'null' => TRUE,
                'comment' => 'Es la fecha en que se acepto el aviso de privacidad'
            ],
            'fecha_terminos_condiciones' => [
                'type' => 'datetime',
                'null' => TRUE,
                'comment' => 'Es la fecha en que se acepto los terminos y condiciones'
            ],
        ]);
        $this->dbforge->add_key('id', TRUE);
        
        $this->dbforge->create_table('legales_users');
    }

    public function down(){
        $this->dbforge->drop_table('legales_users');
    }
}