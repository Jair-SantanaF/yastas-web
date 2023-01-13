<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
class Migration_Table_Questions_Answers extends CI_Migration { 
    public function up(){ 
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'answer' => [
                'type' => 'TEXT',
                'comment' => 'Es la respuesta definida a una pregunta'
            ],
            'question_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'comment' => 'Para saber la relación con la pregunta'
            ],
            'correct' => [
                'type' => 'TINYINT',
                'default' => FALSE,
                'null' => TRUE,
                'comment' => 'Para saber si la respuesta es correcta'
            ],
            'active' => [
                'type' => 'TINYINT',
                'default' => TRUE,
                'null' => TRUE,
                'comment' => 'Sirve para saber si es activo o no la respuesta a la pregunta'
            ],
            'image' => [
                'type' => 'TEXT',
                'null' => TRUE,
                'comment' => 'Es el nombre de la imagen que se guarda'
            ],
        ]);
        $this->dbforge->add_key('id', TRUE);
        
        $this->dbforge->create_table('question_answers');
    }

    public function down(){
        $this->dbforge->drop_table('question_answers');
    }
}