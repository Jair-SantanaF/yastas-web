<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
class Migration_Table_Tutores_Becarios extends CI_Migration { 
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
            'tutor_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'comment' => 'Es la referencia de la tabla user'
            ],
            'becario_id' => [
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
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (tutor_id) REFERENCES user(id)');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (becario_id) REFERENCES user(id)');

        $this->dbforge->create_table('tutores_becarios');
    }

    public function down(){
        $this->dbforge->drop_table('tutores_becarios');
    }
}