<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
class Migration_Table_Incidencias extends CI_Migration { 
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
                'comment' => 'Es la referencia de la tabla estatus_incidencia el valor puede ser: 1. Asistencias, 2. Retardos, 3. Faltas, 4. Enfermedad sin justificante, 5. Enfermedad con justificante, 6. Otro'
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
            'comentarios' => [
                'type' => 'TEXT',
                'null' => TRUE,
                'comment' => 'Comentarios dados por el tutor'
            ],
            'tiempo_retardo' => [
                'type' => 'time',
                'null' => TRUE,
                'comment' => 'Es el mes del curso en el que se realizó el reto'
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
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (estatus_id) REFERENCES estatus_incidencia(id)');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (tutor_id) REFERENCES user(id)');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (becario_id) REFERENCES user(id)');
        
        
        $this->dbforge->create_table('incidencias');
    }

    public function down(){
        $this->dbforge->drop_table('incidencias');
    }
}