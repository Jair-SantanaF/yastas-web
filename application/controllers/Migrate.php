<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
class Migrate extends CI_Controller { 

    function __construct(){
        parent::__construct();
        $this->load->library('migration');
    }

    function current(){
        if ($this->migration->current()) {
            echo "Migraciones cargadas";
            
        } else {
            log_message('error', $this->migration->error_string());
            echo $this->migration->error_string();
        }
    }

    function rollback(){
        $this->dbforge->drop_table('retos');
        $this->dbforge->drop_table('estatus_reto');
        $this->dbforge->drop_table('asesores_tutores');
        $this->dbforge->drop_table('tutores_becarios');
        $this->dbforge->drop_table('estatus_incidencia');
        $this->dbforge->drop_table('incidencias');
        $this->dbforge->drop_table('legales_users');
        
        echo "Migraciones eliminadas";
    }
}