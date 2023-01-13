<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
class Migration_Table_retos extends CI_Migration { 
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
            'estatus_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'comment' => 'Es la referencia de la tabla estatus_reto el valor puede ser: 1. Reto sin cumplir, 2. Reto cumplido, 3. Feedback'
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
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => '120',
                'comment' => 'Nombre del reto'
            ],
            'detalles' => [
                'type' => 'TEXT',
                'null' => TRUE,
                'comment' => 'Descripción o detalles del reto'
            ],
            'mes_curso' => [
                'type' => 'TEXT',
                'comment' => 'Es el mes del curso en el que se realizó el reto'
            ],
            'comentarios' => [
                'type' => 'TEXT',
                'null' => TRUE,
                'comment' => 'Comentario que proporciona el becario al reto o al tutor en general'
            ],
            'feedback' => [
                'type' => 'TEXT',
                'null' => TRUE,
                'comment' => 'Feedback que proporciona el tutor al becario'
            ],
            'completed_tutor_at' => [
                'type' => 'DATE',
                'null' => TRUE,
                'comment' => 'Fecha en que el reto fue completado por el tutor'
            ],
            'completed_becario_at' => [
                'type' => 'DATE',
                'null' => TRUE,
                'comment' => 'Fecha en que el reto fue completado por el becario'
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
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (estatus_id) REFERENCES estatus_reto(id)');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (tutor_id) REFERENCES user(id)');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (becario_id) REFERENCES user(id)');
        
        $this->dbforge->create_table('retos');
    }

    public function down(){
        $this->dbforge->drop_table('retos');
    }
}