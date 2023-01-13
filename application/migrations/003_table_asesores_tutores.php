<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
class Migration_Table_Asesores_Tutores extends CI_Migration { 
    public function up(){ 
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'empresa_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'comment' => 'Es la referencia de la tabla business'
            ],
            'asesor_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'comment' => 'Es la referencia de la tabla user'
            ],
            'tutor_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'comment' => 'Es la referencia de la tabla user'
            ],
            'created_at' => [
                'type' => 'timestamp'
            ],
            'updated_at' => [
                'type' => 'timestamp'
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (empresa_id) REFERENCES business(id)');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (asesor_id) REFERENCES user(id)');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (tutor_id) REFERENCES user(id)');

        $this->dbforge->create_table('asesores_tutores');
    }

    public function down(){
        $this->dbforge->drop_table('asesores_tutores');
    }
}