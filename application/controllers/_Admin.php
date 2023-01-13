<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct(){
        parent::__construct();
        date_default_timezone_set("America/Mexico_City");

		$this->load->model('library_mdl', 'library');
		$this->load->model('question_mdl', 'question');
    }
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández
	 *		   mario.martinez.f@hotmail.es
	 *	Fecha: 23 feb 2018
	 *	Nota: Cargar web admin
	 ***********************************************************************/
	public function index()
	{
        $this->load->view('login.php');
	}
    public function index_admin()
    {
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández
         *		   mario.martinez.f@hotmail.es
         *	Fecha: 01 mar 2018
         *	Nota: Vista para admin.
         ***********************************************************************/
        $idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
        set_translation_language($idioma);
        $this->load->view('admin/admin.php');
    }
    /*
    public function usuarios(){
        $idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
        set_translation_language($idioma);
        $this->load->view('admin/usuarios.php');
    }*/
    public function roles(){
        $idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
        set_translation_language($idioma);
        $this->load->view('admin/roles.php');
    }
    public function modulos(){
        $idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
        set_translation_language($idioma);
        $this->load->view('admin/modulos.php');
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 19 mar 2018
     *	Nota: Funcion para cargar el contenedor en caso de obtener las
     *          credenciales.
     ***********************************************************************/
    public function inicio(){
            $this->load->view('contenedor.php',array('view_'=>'admin/index_admin'));
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández
     *		   mario.martinez.f@hotmail.es
     *	Fecha: 19 mar 2018
     *	Nota: Funcion para validar login
     ***********************************************************************/
    public function login(){
        $usuario = $this->input->post("email");
        $password = $this->input->post("password");
        $respuesta = $this->login_admin_mdl->ValidaUsuario($usuario,$password);
        if(count($respuesta) > 0){
            successResponse(array(),'Inicio sesión',$this);
            $this->session->set_userdata(array(
            	'nombre'=>$respuesta[0]['name'],
				'foto'=>$respuesta[0]['profile_photo'],
				'id_user'=>$respuesta[0]['id'],
				'rol_id'=>$respuesta[0]['rol_id'],
				'empresa_id'=>$respuesta[0]['business_id'])
			);
        }else{
            faildResponse('El usuario y/o password son incorrectos',$this);
        }
    }
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández
	 *		   mario.martinez.f@hotmail.es
	 *	Fecha: 13/06/2019
	 *	Nota: funcion para cerrar la sesion
	 ***********************************************************************/
	public function CerrarSesion(){
		$this->output->set_header('Last-Modified:'.gmdate('D, d M Y H:i:s').'GMT');
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Cache-Control: post-check=0, pre-check=0',false);
		$this->output->set_header('Pragma: no-cache');
		$this->session->sess_destroy();
		header("Location:".base_url().'admin');
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 27/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener la vista de api de preguntas
	 ***********************************************************************/
	public function ApiPreguntas(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view(
			'admin/preguntas.php',
			array(
				'configuration'=>$this->question->ConfigurationQuestions($valida_token['business_id']),
				'categories'=> $this->question->ListCategories(),
				'jobs'=>$this->jobs_model->fetchAllByBusinessId($valida_token['business_id']),
				'types_question'=> $this->question->listTypesQuestion(),
			)
		);
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 12/21/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener la vista para las respuesta de las
	 * 			preguntas
	 ***********************************************************************/
	public function ApiRespuestas(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view(
			'admin/preguntas_respuestas.php',
			array(
				'quiz'=> $this->question->ListQuiz(array('business_id'=>$valida_token['business_id'])),
			)
		);
	}
	public function Biblioteca(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$categories = $this->library->ListCategories($valida_token['business_id']);
		$areas = $this->library->ListSubcategory($valida_token['business_id']);
		$this->load->view('admin/biblioteca.php', array("categorias"=>$categories, "areas"=>$areas));
	}
	public function Feedback(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/feedback.php');
	}
	public function Usuarios(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/usuarios.php');
	}
	public function JuegoRuleta(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/juego_ruleta.php');
	}
	public function JuegoRuletaResultados(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/juego_ruleta_resultados.php');
	}

	public function JuegoPerfilador(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/juego_perfilador.php');
	}
	public function JuegoPerfiladorResultados(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/juego_perfilador_resultados.php');
	}


	public function JuegoProductos(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/juego_productos.php');
	}

	public function JuegoProductosResultados(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/juego_productos_resultados.php');
	}

	public function Muro(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/muro.php');
	}

	public function Eventos(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/eventos.php');
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 07/07/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para cargar vista de notificaciones
	 ***********************************************************************/
	public function Notificaciones(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$this->load->view('admin/notificaciones.php');
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 29/09/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener la vista para elearning
	 ***********************************************************************/
	public function Elearning(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/elearning.php', array());
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 04/10/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para pbtener la vista de ranking
	 ***********************************************************************/
	public function Ranking(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$where = " WHERE u.business_id = " . $valida_token['business_id'];
		$this->load->view('admin/ranking.php', array('detail'=>$this->user_model->ranking($where)));
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener la vista de generacion de reportes
	 ***********************************************************************/
	public function ElearningReporte(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/elearning_reporte.php', array());
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/5/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener la vista de creacion y asignacion de grupos
	 * 			a usuarios.
	 ***********************************************************************/
	public function GruposUsuarios(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/grupos_usuarios.php', array());
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/6/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener la vista de grupos de biblioteca
	 ***********************************************************************/
	public function BibliotecaGrupos(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/grupos_biblioteca.php', array());
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/6/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener la vista de grupos de elearning
	 ***********************************************************************/
	public function ElearningGrupos(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/grupos_elearning.php', array());
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/6/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para obtener a los usuarios pendientes de registro
	 * 			por medio de la invitacion.
	 ***********************************************************************/
	public function Invitados(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/usuarios_invitados.php', array());
	}
	/***********************************************************************
	 *	Autor: Mario Adrián Martínez Fernández   Fecha: 08/11/2020
	 *		   mario.martinez.f@hotmail.es
	 *	Nota: Funcion para cargar la vista de numeros de empleados
	 ***********************************************************************/
	public function NumerosEmpleados(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/usuarios_numeros_empleados.php', array());
	}

	/***********************************************************************
	 *    Autor: Uriel Sánchez Cervantes   Fecha: 11/12/2020
	 *           urisancer@gmail.com
	 *    Nota: Módulo de servicios solicitados por el cliente
	 ***********************************************************************/
	public function ServiciosSolicitados(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/servicios_solicitados.php');
	}

	/***********************************************************************
	 *    Autor: Josue Carrasco   Fecha: 15/12/2020
	 *           josue.carrasco.ramirez@gmail.com
	 *    Nota: Funcion para cargar la vista donde se van a guardar
	 *    los puestos de empleados
	 ***********************************************************************/

	public function UsuariosPuestos()
	{
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/usuarios_puestos.php');
	}

	/***********************************************************************
	 *    Autor: Uriel Sánchez Cervantes   Fecha: 11/12/2020
	 *           urisancer@gmail.com
	 *    Nota: Módulo de servicios solicitados por el cliente
	 ***********************************************************************/
	public function Companias(){
		$idioma = ( $this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$valida_token = $this->general_mdl->UsuarioDetalleToken(null);
		if(!$valida_token){
			faildResponse( $this->lang->line('token_error_msg') ,$this);
			return;
		}
		$this->load->view('admin/companias.php');
	}
}
