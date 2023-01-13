<?php
class Incidencias extends CI_Controller
{
    public $defaultLang = 'es';

    public function __construct(){
        parent::__construct();
        date_default_timezone_set("America/Mexico_City");

        $this->lang->load('message', 'en');
        $this->load->model("incidencias_mdl", "incidencias");
    }


    public function getIncidencias(){
        // return print_r($this->input->post());
        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $region_id = null;
        $fecha_inicio = null;
        $fecha_fin = null;

        if(!empty($this->input->post('region_id')) && $this->input->post('region_id') != 'null'){
            $region_id = $this->input->post('region_id');
        }

        if(!empty($this->input->post('fecha_inicio'))){
            $fecha_inicio = $this->input->post('fecha_inicio');
        }

        if(!empty($this->input->post('fecha_fin'))){
            $fecha_fin = $this->input->post('fecha_fin');
        }

        if($valida_token["rol_id"] == 2){
            $incidencias = $this->incidencias->getIncidenciasWeb($valida_token["business_id"], null, $region_id, $fecha_inicio, $fecha_fin, true);
        }else{
            $incidencias = $this->incidencias->getIncidenciasWeb($valida_token["business_id"], $valida_token["user_id"], $region_id, $fecha_inicio, $fecha_fin, false);
        }
        
        if(count($incidencias) > 0){
            $this->general_mdl->writeLog("Listado de incidencias", "<info>");
            return successResponse($incidencias, 'Listado de incidencias', $this);

        }else if(count($incidencias) == 0){
            $this->general_mdl->writeLog("Listado de incidencias", "<info>");
            return successResponse($incidencias, 'Listado de incidencias', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de incidencias", "<warning>");
            return faildResponse("Error al obtener listado de incidencias", $this);
        }
    }

    // descargar los retos en un csv
    public function descargarCsv(){
        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $region_id = null;
        $fecha_inicio = null;
        $fecha_fin = null;

        if($valida_token["rol_id"] == 2){
            $incidencias = $this->incidencias->getIncidenciasWeb($valida_token["business_id"], null, $region_id, $fecha_inicio, $fecha_fin, true);
        }else{
            $incidencias = $this->incidencias->getIncidenciasWeb($valida_token["business_id"], $valida_token["user_id"], $region_id, $fecha_inicio, $fecha_fin, false);
        }

        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header("Content-Disposition: attachment; filename=retos_" . date('y_m_d') . ".csv");
        header('Last-Modified: ' . date('D M j G:i:s T Y'));
        echo "\xEF\xBB\xBF"; // UTF-8 BOM este sirve para los acentos y algunos caracteres especiales
        $outs = fopen("php://output", "w");

        $list[] = ["numero_empleado_becario", "nombre_becario", "numero_empleado_becario", "nombre_tutor", "comentarios", "tiempo_retardo", "asistencias", "retardos", "faltas", "enfermedad_con_justificante_", "enfermedad_sin_justificante", "fecha_creacion"];
        foreach ($incidencias as $key => $value) {

            array_push($list, [
                $value->becario_numero_empleado,
                $value->becario_nombre,
                $value->tutor_numero_empleado,
                $value->tutor_nombre,
                $value->comentarios,
                $value->tiempo_retardo,
                $value->asistencias,
                $value->retardos,
                $value->faltas,
                $value->enfermedad_con_justificante,
                $value->enfermedad_sin_justificante,
                $value->created_at
            ]);
        }



        foreach($list as $rows) {
            fputcsv($outs, $rows);
        }
        
        fclose($outs);
    }
}
