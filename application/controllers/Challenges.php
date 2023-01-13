<?php
class Challenges extends CI_Controller
{
    public $defaultLang = 'es';

    public function __construct(){
        parent::__construct();
        date_default_timezone_set("America/Mexico_City");

        $this->lang->load('message', 'en');
        $this->load->model("challenges_mdl", "challenges");
    }


    // obtener todos los estatus de los retos
    public function getEstatusRetos(){
        if(empty($this->input->post("token"))) {
            return faildResponse("No se ha ingresado el token del usuario", $this);
        }

        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $estatus = $this->challenges->getEstatusReto();

        if(!empty($estatus)){
            $this->general_mdl->writeLog("Lista de estatus", "<info>");
            return successResponse($estatus, 'Lista de estatus', $this);

        }else if(empty($estatus)){
            $this->general_mdl->writeLog("Lista de estatus", "<info>");
            return successResponse($estatus, 'Lista de estatus', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener el listado de estatus", "<warning>");
            return faildResponse("Error al obtener el listado de estatus", $this);
        }
    }

    // obtener los retos cargados
    public function getRetosCargados(){
        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        if($valida_token["rol_id"] == 2){
            $retos = $this->challenges->getRetosCargados(null, $valida_token["business_id"], true);
        }else{
            $retos = $this->challenges->getRetosCargados($valida_token["user_id"], $valida_token["business_id"], false);
        }

        if(count($retos) > 0){
            $this->general_mdl->writeLog("Listado de retos cargados", "<info>");
            return successResponse($retos, 'Listado de retos cargados', $this);

        }else if(count($retos) == 0){
            $this->general_mdl->writeLog("Listado de retos cargados", "<info>");
            return successResponse($retos, 'Listado de retos cargados', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de retos cargados", "<warning>");
            return faildResponse("Error al obtener listado de retos cargados", $this);
        }
    }


    // obtener los retos realizados
    public function getRetosRealizados(){
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        if($valida_token["rol_id"] == 2){
            $retosRealizados = $this->challenges->getRetosRealizados(null, $valida_token["business_id"], true);
        }else{
            $retosRealizados = $this->challenges->getRetosRealizados($valida_token["user_id"], $valida_token["business_id"], false);
        }

        if(count($retosRealizados) > 0){
            $this->general_mdl->writeLog("Listado de todos los retos realizados", "<info>");
            return successResponse($retosRealizados, 'Listado de todos los retos realizados', $this);

        }else if(count($retosRealizados) == 0){
            $this->general_mdl->writeLog("Listado de todos los retos realizados", "<info>");
            return successResponse($retosRealizados, 'Listado de todos los retos realizados', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de retos realizados", "<warning>");
            return faildResponse("Error al obtener listado de retos realizados", $this);
        }
    }


    // obtener las regiones
    public function getRegiones(){
        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $regiones = $this->challenges->getRegiones($valida_token["business_id"]);

        if(count($regiones) > 0){
            $this->general_mdl->writeLog("Listado de regiones pertenecientes a la empresa", "<info>");
            return successResponse($regiones, 'Listado de regiones pertenecientes a la empresa', $this);

        }else if(count($regiones) == 0){
            $this->general_mdl->writeLog("Listado de regiones pertenecientes a la empresa", "<info>");
            return successResponse($regiones, 'Listado de regiones pertenecientes a la empresa', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de regiones", "<warning>");
            return faildResponse("Error al obtener listado de regiones", $this);
        }
    }


    // obtener los tutores
    public function getTutores(){
        if(empty($this->input->post("region_id"))) {
            return faildResponse("No se ha ingresado el id de la region", $this);
        }

        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $tutores = $this->challenges->getTutores($this->input->post("region_id"), $valida_token["business_id"]);

        if(count($tutores) > 0){
            $this->general_mdl->writeLog("Listado de tutores", "<info>");
            return successResponse($tutores, 'Listado de tutores', $this);

        }else if(count($tutores) == 0){
            $this->general_mdl->writeLog("Listado de tutores", "<info>");
            return successResponse($tutores, 'Listado de tutores', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de tutores", "<warning>");
            return faildResponse("Error al obtener listado de tutores", $this);
        }
    }


    // obtener los becarios
    public function getBecarios(){
        if(empty($this->input->post("tutor_id"))) {
            return faildResponse("No se ha ingresado el id del tutor", $this);
        }

        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $becarios = $this->challenges->getBecarios($this->input->post("tutor_id"), $valida_token["business_id"], false);

        
        if(!empty($becarios)){
            $this->general_mdl->writeLog("Lista de becarios asignados al tutor", "<info>");
            return successResponse($becarios, 'Lista de becarios asignados al tutor', $this);

        }else if(empty($becarios)){
            $this->general_mdl->writeLog("Lista de becarios asignados al tutor", "<info>");
            return successResponse($becarios, 'Lista de becarios asignados al tutor', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener el listado de becarios asignados al tutor", "<warning>");
            return faildResponse("Error al obtener el listado de becarios asignados al tutor", $this);
        }
    }


    // crear reto
    public function crearReto(){
        if($this->input->post() == []) {
            return validationResponse("No se ha ingresado dato alguno", $this);
        }

        if(empty($this->input->post("tutor_id"))) {
            return validationResponse("No se ha seleccionado un tutor", $this);
        }

        if(empty($this->input->post("becarios"))) {
            return validationResponse("No se ha seleccionado un becario", $this);
        }

        if(empty($this->input->post("reto"))) {
            return validationResponse("No se ha ingresado el nombre del reto", $this);
        }

        if(empty($this->input->post("detalles"))) {
            return validationResponse("No se ha ingresado los detalles del reto", $this);
        }

        if(empty($this->input->post("mes"))) {
            return validationResponse("No se ha seleccionado el mes en curso", $this);
        }

        
        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        foreach ($this->input->post("becarios") as $key => $value) {
            $this->challenges->crearReto($valida_token["business_id"], 1, $this->input->post("tutor_id"), $value, $this->input->post("reto"), $this->input->post("detalles"), $this->input->post("mes"));
        }
    
        if($this->db->affected_rows() > 0){
            $this->general_mdl->writeLog("Reto creado exitosamente", "<info>");
            return successResponse([], 'Reto creado exitosamente', $this);

        }else{
            $this->general_mdl->writeLog("Error al crear el reto", "<warning>");
            return faildResponse("Error al crear el reto", $this);
        }
    }


    // obtener un reto por el id
    public function getReto(){
        if($this->input->post() == []) {
            return validationResponse("No se ha ingresado dato alguno", $this);
        }

        if(empty($this->input->post("reto_id"))) {
            return validationResponse("No se ha ingresado el id del reto", $this);
        }

        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $reto = $this->challenges->getReto($this->input->post("reto_id"));

        if(!empty($reto)){
            $this->general_mdl->writeLog("Detalle del reto", "<info>");
            return successResponse($reto, 'Detalle del reto', $this);

        }else if(empty($reto)){
            $this->general_mdl->writeLog("Detalle del reto", "<info>");
            return successResponse($reto, 'Detalle del reto', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener el detalle del reto", "<warning>");
            return faildResponse("Error al obtener el detalle del reto", $this);
        }
    }


    // actualizar datos del reto
    public function actualizarReto(){
        if($this->input->post() == []) {
            return validationResponse("No se ha ingresado dato alguno", $this);
        }

        if(empty($this->input->post("reto_id"))) {
            return validationResponse("No se ha ingresado el id del reto", $this);
        }

        if(empty($this->input->post("reto"))) {
            return validationResponse("No se ha ingresado el nombre del reto", $this);
        }

        if(empty($this->input->post("detalles"))) {
            return validationResponse("No se ha ingresado los detalles del reto", $this);
        }

        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $reto = $this->challenges->updateReto($this->input->post("reto_id"), $this->input->post("reto"), $this->input->post("detalles"));

        if($reto["success"]){
            $this->general_mdl->writeLog("Datos del reto actualizados exitosamente", "<info>");
            return successResponse([], 'Datos del reto actualizados exitosamente', $this);

        }else{
            $this->general_mdl->writeLog("El reto no se pudo actualizar", "<info>");
            return faildResponse([], 'El reto no se pudo actualizar', $this);
        }
    }


    // eliminar reto
    public function eliminarReto(){
        if($this->input->post() == []) {
            return validationResponse("No se ha ingresado dato alguno", $this);
        }

        if(empty($this->input->post("reto_id"))) {
            return validationResponse("No se ha ingresado el id del reto", $this);
        }

        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $reto = $this->challenges->deleteReto($this->input->post("reto_id"));

        if($reto["success"]){
            $this->general_mdl->writeLog("Reto eliminado exitosamente", "<info>");
            return successResponse([], 'Reto eliminado exitosamente', $this);

        }else{
            $this->general_mdl->writeLog("El reto no se pudo eliminar", "<info>");
            return faildResponse([], 'El reto no se pudo eliminar', $this);
        }
    }


    // obtener los retos cargados
    public function getRetosCargadosFiltro(){
        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        if($valida_token["rol_id"] == 2){
            $retos = $this->challenges->getRetosCargadosFiltro(null, $valida_token["business_id"], $this->input->post("filtro"), true);
        }else{
            $retos = $this->challenges->getRetosCargadosFiltro($valida_token["user_id"], $valida_token["business_id"], $this->input->post("filtro"), false);
        }

        if(count($retos) > 0){
            $this->general_mdl->writeLog("Listado de retos cargados", "<info>");
            return successResponse($retos, 'Listado de retos cargados', $this);

        }else if(count($retos) == 0){
            $this->general_mdl->writeLog("Listado de retos cargados", "<info>");
            return successResponse($retos, 'Listado de retos cargados', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de retos cargados", "<warning>");
            return faildResponse("Error al obtener listado de retos cargados", $this);
        }
    }


    // obtener los retos realizados
    public function getRetosRealizadosFiltro(){
        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        if($valida_token["rol_id"] == 2){
            $retosRealizados = $this->challenges->getRetosRealizadosFiltro(null, $valida_token["business_id"], $this->input->post("filtro"), true);
        }else{
            $retosRealizados = $this->challenges->getRetosRealizadosFiltro($valida_token["user_id"], $valida_token["business_id"], $this->input->post("filtro"), false);
        }
        
        if(count($retosRealizados) > 0){
            $this->general_mdl->writeLog("Listado de todos los retos realizados", "<info>");
            return successResponse($retosRealizados, 'Listado de todos los retos realizados', $this);

        }else if(count($retosRealizados) == 0){
            $this->general_mdl->writeLog("Listado de todos los retos realizados", "<info>");
            return successResponse($retosRealizados, 'Listado de todos los retos realizados', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de retos realizados", "<warning>");
            return faildResponse("Error al obtener listado de retos realizados", $this);
        }
    }

    // descargar los retos en un csv
    public function descargarCsv(){
        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        if($valida_token["rol_id"] == 2){
            $retosCsv = $this->challenges->getRetosCsv(null, $valida_token["business_id"], true);
        }else{
            $retosCsv = $this->challenges->getRetosCsv($valida_token["user_id"], $valida_token["business_id"], false);
        }

        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header("Content-Disposition: attachment; filename=retos_" . date('y_m_d') . ".csv");
		header('Last-Modified: ' . date('D M j G:i:s T Y'));
        echo "\xEF\xBB\xBF"; // UTF-8 BOM este sirve para los acentos y algunos caracteres especiales
		$outs = fopen("php://output", "w");

        $list[] = ["reto", "detalles", "tutor", "becario", "feedback", "estatus", "fecha_creacion"];
        foreach ($retosCsv as $key => $value) {
            $dateTime = DateTime::createFromFormat('Y-m-d h:i:s', $value->created_at);
            $formatDate = $dateTime->format('d/m/Y');

            array_push($list, [
                $value->reto_nombre,
                $value->reto_detalles,
                $value->tutor_nombre,
                $value->becario_nombre,
                $value->reto_feedback,
                $value->reto_estatus_nombre,
                $formatDate
            ]);
        }

		foreach($list as $rows) {
			fputcsv($outs, $rows);
		}
		
        fclose($outs);
    }


    // crear reto a todos los becarios y tutores
    public function crearRetoTodos(){
        if($this->input->post() == []) {
            return validationResponse("No se ha ingresado dato alguno", $this);
        }

        if(empty($this->input->post("reto"))) {
            return validationResponse("No se ha ingresado el nombre del reto", $this);
        }

        if(empty($this->input->post("detalles"))) {
            return validationResponse("No se ha ingresado los detalles del reto", $this);
        }

        if(empty($this->input->post("mes"))) {
            return validationResponse("No se ha seleccionado el mes en curso", $this);
        }

        if(empty($this->input->post("region"))) {
            return validationResponse("No se ha seleccionado una región", $this);
        }
  
        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        // cuando no se selecciona todas las regiones
        if($this->input->post("region") != 'todos'){
            $tutores = $this->challenges->getTutores($this->input->post("region"), $valida_token["business_id"]);
            if(!count($tutores)){
                return validationResponse("No se han encontrado tutores en la región seleccionada", $this);
            }

            foreach ($tutores as $tutor) {
                $becarios = $this->challenges->getBecarios($tutor->id, $valida_token["business_id"], false);
                if(!count($becarios)){
                    return validationResponse("No se han encontrado tutores en la región seleccionada", $this);
                }

                foreach ($becarios as $becario) {
                    $this->challenges->crearReto($valida_token["business_id"], 1, $tutor->id, $becario->becario_id, $this->input->post("reto"), $this->input->post("detalles"), $this->input->post("mes"));
                }
            }
        }

        // cuando se selecciona todas las regiones
        if($this->input->post("region") == 'todos'){
            $regiones = $this->challenges->getRegiones($valida_token["business_id"]);
            if(!count($regiones)){
                return validationResponse("No se han encontrado regiones", $this);
            }

            foreach ($regiones as $region) {
                $tutores = $this->challenges->getTutores($region->id, $valida_token["business_id"]);
                if(!count($tutores)){
                    return validationResponse("No se han encontrado tutores en la región seleccionada", $this);
                }
    
                foreach ($tutores as $tutor) {
                    $becarios = $this->challenges->getBecarios($tutor->id, $valida_token["business_id"], false);
                    if(!count($becarios)){
                        return validationResponse("No se han encontrado tutores en la región seleccionada", $this);
                    }
    
                    foreach ($becarios as $becario) {
                        $this->challenges->crearReto($valida_token["business_id"], 1, $tutor->id, $becario->becario_id, $this->input->post("reto"), $this->input->post("detalles"), $this->input->post("mes"));
                    }
                }
            }
        }

    
        if($this->db->affected_rows() > 0){
            $this->general_mdl->writeLog("Reto creado exitosamente", "<info>");
            return successResponse([], 'Reto creado exitosamente', $this);

        }else{
            $this->general_mdl->writeLog("Error al crear el reto", "<warning>");
            return faildResponse("Error al crear el reto", $this);
        }
    }
}
