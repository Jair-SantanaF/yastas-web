<?php
class Teams extends CI_Controller
{
    public $defaultLang = 'es';

    public function __construct(){
        parent::__construct();
        date_default_timezone_set("America/Mexico_City");

        $this->lang->load('message', 'en');
        $this->load->model("teams_mdl", "teams");
    }

    // obtener los tutores asociados a un asesor
    public function getTutoresAsesores(){
        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $tutores = $this->teams->getTutoresAsesores($valida_token["business_id"]);

        if(count($tutores) > 0){
            $this->general_mdl->writeLog("Listado de tutores asociados a un asesor", "<info>");
            return successResponse($tutores, 'Listado de tutores asociados a un asesor', $this);

        }else if(count($tutores) == 0){
            $this->general_mdl->writeLog("Listado de tutores asociados a un asesor", "<info>");
            return successResponse($tutores, 'Listado de tutores asociados a un asesor', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de tutores asociados a un asesor", "<warning>");
            return faildResponse("Error al obtener listado de tutores asociados a un asesor", $this);
        }
    }


    // obtener los becarios asociados a un tutor
    public function getBecariosTutores(){
        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $tutores = $this->teams->getBecariosTutores($valida_token["business_id"]);

        if(count($tutores) > 0){
            $this->general_mdl->writeLog("Listado de becario asociados a un tutor", "<info>");
            return successResponse($tutores, 'Listado de becario asociados a un tutor', $this);

        }else if(count($tutores) == 0){
            $this->general_mdl->writeLog("Listado de becario asociados a un tutor", "<info>");
            return successResponse($tutores, 'Listado de becario asociados a un tutor', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de becario asociados a un tutor", "<warning>");
            return faildResponse("Error al obtener listado de becario asociados a un tutor", $this);
        }
    }


    // obtener el detalle para editar los datos del becario
    public function getBecario(){
        if($this->input->post() == []) {
            return validationResponse("No se ha ingresado dato alguno", $this);
        }

        if(empty($this->input->post("becario_id"))) {
            return validationResponse("No se ha ingresado el id del becario", $this);
        }

        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $becario = $this->teams->getBecario($this->input->post("becario_id"), $valida_token["business_id"]);

        if(!empty($becario)){
            $this->general_mdl->writeLog("Detalle del becario", "<info>");
            return successResponse($becario, 'Detalle del becario', $this);

        }else if(empty($becario)){
            $this->general_mdl->writeLog("Detalle del becario", "<info>");
            return successResponse($becario, 'Detalle del becario', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener el detalle del becario", "<warning>");
            return faildResponse("Error al obtener el detalle del becario", $this);
        }
    }


    // actualizar datos del becario
    public function actualizarDatosBecario(){
        if($this->input->post() == []) {
            return validationResponse("No se ha ingresado dato alguno", $this);
        }

        if(empty($this->input->post("becario_id"))) {
            return validationResponse("No se ha ingresado el id del becario", $this);
        }

        if(empty($this->input->post("region_id"))) {
            return validationResponse("No se ha ingresado el id de la región", $this);
        }

        if(empty($this->input->post("tutor_id"))) {
            return validationResponse("No se ha ingresado el id del tutor", $this);
        }

        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $this->teams->updateRegionBecario($this->input->post("becario_id"), $this->input->post("region_id"));
        $becario = $this->teams->updateTutorBecario($this->input->post("becario_id"), $this->input->post("tutor_id"));
        
        if($becario["success"]){
            $this->general_mdl->writeLog("Datos actualizados exitosamente", "<info>");
            return successResponse([], 'Datos actualizados exitosamente', $this);

        }else{
            $this->general_mdl->writeLog("La información no se pudo actualizar", "<info>");
            return faildResponse([], 'La información no se pudo actualizar', $this);
        }
    }


    // eliminar asociación de un becario con un tutor
    public function deleteAsociacionBecarioTutor(){
        if($this->input->post() == []) {
            return validationResponse("No se ha ingresado dato alguno", $this);
        }

        if(empty($this->input->post("becario_id"))) {
            return validationResponse("No se ha ingresado el id del becario", $this);
        }

        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $responseModel = $this->teams->deleteAsociacionBecarioTutor($this->input->post("becario_id"));

        if($responseModel["success"]){
            $this->general_mdl->writeLog("Asociación de becario a un tutor eliminado exitosamente", "<info>");
            return successResponse([], 'Asociación de becario a un tutor eliminado exitosamente', $this);

        }else{
            $this->general_mdl->writeLog("La asociación de becario a un tutor no se pudo eliminar", "<info>");
            return faildResponse([], 'La asociación de becario a un tutor no se pudo eliminar', $this);
        }
    }


    // obtener el detalle para editar los datos del tutor
    public function getTutor(){
        if($this->input->post() == []) {
            return validationResponse("No se ha ingresado dato alguno", $this);
        }

        if(empty($this->input->post("tutor_id"))) {
            return validationResponse("No se ha ingresado el id del tutor", $this);
        }

        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $tutor = $this->teams->getTutor($this->input->post("tutor_id"), $valida_token["business_id"]);

        if(!empty($tutor)){
            $this->general_mdl->writeLog("Detalle del tutor", "<info>");
            return successResponse($tutor, 'Detalle del tutor', $this);

        }else if(empty($tutor)){
            $this->general_mdl->writeLog("Detalle del tutor", "<info>");
            return successResponse($tutor, 'Detalle del tutor', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener el detalle del tutor", "<warning>");
            return faildResponse("Error al obtener el detalle del tutor", $this);
        }
    }


    // obtener lista de asesores
    public function getAsesores(){
        if(empty($this->input->post("region_id"))) {
            return validationResponse("No se ha ingresado el id de la región", $this);
        }

        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $asesores = $this->teams->getAsesores($this->input->post("region_id"), $valida_token["business_id"]);

        if(count($asesores) > 0){
            $this->general_mdl->writeLog("Listado de asesores pertenecientes a la empresa", "<info>");
            return successResponse($asesores, 'Listado de asesores pertenecientes a la empresa', $this);

        }else if(count($asesores) == 0){
            $this->general_mdl->writeLog("Listado de asesores pertenecientes a la empresa", "<info>");
            return successResponse($asesores, 'Listado de asesores pertenecientes a la empresa', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de asesores", "<warning>");
            return faildResponse("Error al obtener listado de asesores", $this);
        }
    }


    // actualizar datos del tutor
    public function updateDatosTutor(){
        if($this->input->post() == []) {
            return validationResponse("No se ha ingresado dato alguno", $this);
        }

        if(empty($this->input->post("tutor_id"))) {
            return validationResponse("No se ha ingresado el id del tutor", $this);
        }

        if(empty($this->input->post("region_id"))) {
            return validationResponse("No se ha ingresado el id de la región", $this);
        }

        if(empty($this->input->post("asesor_id"))) {
            return validationResponse("No se ha ingresado el id del asesor", $this);
        }

        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $this->teams->updateRegion($this->input->post("tutor_id"), $this->input->post("region_id"));
        $tutor = $this->teams->updateAsesorTutor($this->input->post("tutor_id"), $this->input->post("asesor_id"));
        
        if($tutor["success"]){
            $this->general_mdl->writeLog("Datos actualizados exitosamente", "<info>");
            return successResponse([], 'Datos actualizados exitosamente', $this);

        }else{
            $this->general_mdl->writeLog("La información no se pudo actualizar", "<info>");
            return faildResponse([], 'La información no se pudo actualizar', $this);
        }
    }


    // eliminar asociación de un tutor con un asesor
    public function deleteAsociacionTutorAsesor(){
        if($this->input->post() == []) {
            return validationResponse("No se ha ingresado dato alguno", $this);
        }

        if(empty($this->input->post("tutor_id"))) {
            return validationResponse("No se ha ingresado el id del tutor", $this);
        }

        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $responseModel = $this->teams->deleteAsociacionTutorAsesor($this->input->post("tutor_id"));

        if($responseModel["success"]){
            $this->general_mdl->writeLog("Asociación de tutor a un asesor eliminado exitosamente", "<info>");
            return successResponse([], 'Asociación de tutor a un asesor eliminado exitosamente', $this);

        }else{
            $this->general_mdl->writeLog("La asociación de tutor a un asesor no se pudo eliminar", "<info>");
            return faildResponse([], 'La asociación de tutor a un asesor no se pudo eliminar', $this);
        }
    }


    // obtener los tutores de una region
    public function getTutoresRegion(){
        if($this->input->post() == []) {
            return validationResponse("No se ha ingresado dato alguno", $this);
        }

        if(empty($this->input->post("region_id"))) {
            return validationResponse("No se ha ingresado el id de la region", $this);
        }

        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $tutores = $this->teams->getTutoresRegion($this->input->post("region_id"), $valida_token["business_id"]);

        if(count($tutores) > 0){
            $this->general_mdl->writeLog("Listado de tutores de una región", "<info>");
            return successResponse($tutores, 'Listado de tutores de una región', $this);

        }else if(count($tutores) == 0){
            $this->general_mdl->writeLog("Listado de tutores de una región", "<info>");
            return successResponse($tutores, 'Listado de tutores de una región', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de tutores de una región", "<warning>");
            return faildResponse("Error al obtener listado de tutores de una región", $this);
        }
    }


    // obtener los becarios de una region
    public function getBecariosRegion(){
        if($this->input->post() == []) {
            return validationResponse("No se ha ingresado dato alguno", $this);
        }

        if(empty($this->input->post("region_id"))) {
            return validationResponse("No se ha ingresado el id de la region", $this);
        }

        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        $becarios = $this->teams->getBecariosRegion($this->input->post("region_id"), $valida_token["business_id"]);

        if(count($becarios) > 0){
            $this->general_mdl->writeLog("Listado de becarios de una región", "<info>");
            return successResponse($becarios, 'Listado de becarios de una región', $this);

        }else if(count($becarios) == 0){
            $this->general_mdl->writeLog("Listado de becarios de una región", "<info>");
            return successResponse($becarios, 'Listado de becarios de una región', $this);

        }else{
            $this->general_mdl->writeLog("Error al obtener listado de becarios de una región", "<warning>");
            return faildResponse("Error al obtener listado de becarios de una región", $this);
        }
    }


    // actualizar datos del tutor
    public function saveTeam(){
        if($this->input->post() == []) {
            return validationResponse("No se ha ingresado dato alguno", $this);
        }

        if(empty($this->input->post("tipo_id"))) {
            return validationResponse("No se ha ingresado el id del tipo de usuario", $this);
        }

        // return var_dump($this->input->post());

        if($this->input->post("tipo_id") === "1"){
            if(empty($this->input->post("asesor_id"))) {
                return validationResponse("No se ha ingresado el id del asesor", $this);
            }

            if(empty($this->input->post("tutores"))) {
                return validationResponse("No se ha ingresado el arreglo de tutores", $this);
            }
        }

        if($this->input->post("tipo_id") === "2"){
            if(empty($this->input->post("tutor_id"))) {
                return validationResponse("No se ha ingresado el id del tutor", $this);
            }

            if(empty($this->input->post("becarios"))) {
                return validationResponse("No se ha ingresado el arreglo de becarios", $this);
            }
        }

        $valida_token = $this->general_mdl->UsuarioDetalleToken(null);

        if(!$valida_token) {
            return faildResponse($this->lang->line('token_error_msg'), $this);
        }

        if($this->input->post("tipo_id") == 1){
            $responseModel = $this->teams->saveAsesorTutor($valida_token['business_id'], $this->input->post("asesor_id"), $this->input->post("tutores"));
        }

        if($this->input->post("tipo_id") == 2){
            $responseModel = $this->teams->saveTutorBecario($valida_token['business_id'], $this->input->post("tutor_id"), $this->input->post("becarios"));
        }

        if($responseModel["success"]){
            $this->general_mdl->writeLog("Datos guardados correctamente", "<info>");
            return successResponse([], 'Datos guardados correctamente', $this);

        }else{
            $this->general_mdl->writeLog("La información no se pudo guardar", "<info>");
            return faildResponse([], 'La información no se pudo guardar', $this);
        }
    }
}
