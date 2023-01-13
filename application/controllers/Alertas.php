<?php
class Alertas extends CI_Controller
{
    public $defaultLang = 'es';

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("America/Mexico_City");

        $headers = $this->input->request_headers();
        // if (isset($headers['lenguage'])) {
        $this->lang->load('message', 'es');
        $this->defaultLang = 'es';
        // } else {
        //     $this->lang->load('message', 'en');
        //     $this->defaultLang = 'en';
        // }
        $this->load->model('alertas_mdl', 'alertas');
    }

    public function ObtenerAlertas()
    {
        $token = $this->input->post("token");
        $valida_token = $this->general_mdl->UsuarioDetalleToken($token);
        if (!$valida_token) {
            faildResponse($this->lang->line('token_error_msg'), $this);
            return;
        }

        $id_sindicato = $valida_token["business_id"];
        $publicaciones = $this->alertas->ObtenerAlertas($id_sindicato);
        successResponse($publicaciones, 'Publicaciones con palabras de alerta', $this);
    }
}
