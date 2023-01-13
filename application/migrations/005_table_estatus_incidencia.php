<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
class Migration_Table_Estatus_Incidencia extends CI_Migration { 
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
            ],
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => '120',
                'comment' => 'Nombre del estatus de la incidencia'
            ],
            'padre' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'null' => TRUE,
                'comment' => 'Aqui se pone el id del estatus padre al que pertenece'
            ],
            'activo' => [
                'type' => 'TINYINT',
                'default' => TRUE,
                'comment' => 'Para saber si el registro esta activo o no'
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
        $this->dbforge->create_table('estatus_incidencia');
    }

    public function down(){
        $this->dbforge->drop_table('estatus_incidencia', TRUE);
    }
}