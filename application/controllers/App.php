<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App extends CI_Controller {
	public $defaultLang = 'es';

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("America/Mexico_City");

		$headers = $this->input->request_headers();
		if (isset($headers['lenguage'])) {
			$this->lang->load('message', 'es');
			$this->defaultLang = 'es';
		} else {
			$this->lang->load('message', 'en');
			$this->defaultLang = 'en';
		}

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
     *	Nota: Cargar web app
     ***********************************************************************/
    public function index()
    {
		if(!$this->session->userdata('session_web')) {
			/***********************************************************************
			 *    Autor: Mario Adrián Martínez Fernández
			 *           mario.martinez.f@hotmail.es
			 *    Fecha: 01 mar 2018
			 *    Nota: Vista de prueba para cargar idiomas
			 ***********************************************************************/
			//$idioma = ($this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
			//set_translation_language($idioma);

			$image_properties = array(
				'src' => base_url().'assets/img/logo_negro.png',
				'alt' => 'NUUP',
				'class' => 'logo img-fluid',
				'title' => 'NUUP'
			);

			$b_elearning = FALSE;

			//Carga de vista separando el header y el footer
			$this->load->helper(array('form', 'html'));
			$title = "Iniciar Sesión";
			$this->load->view('app/app_header', array('title' => $title, 'logo' => '', 'user_img' => '', 'user_img_nav' => '', 'session' => FALSE, 'b_elearning' => $b_elearning, 'services'=>FALSE));
			$this->load->view('app/app', array('logo' => $image_properties));
			$this->load->view('app/app_footer');
		}else{
			redirect('home');
		}
    }

    public function RecoverPassword(){

        $image_properties = array(
            'src' => base_url().'assets/img/logo_negro.png',
            'alt' => 'NUUP',
            'class' => 'logo img-fluid',
            'title' => 'NUUP'
        );

        $b_elearning=FALSE;

    	$title="Recuperar Contraseña";
        $this->load->helper(array('form', 'html'));
    	$this->load->view('app/app_header', array('title'=>$title, 'logo'=>'', 'user_img'=>'', 'user_img_nav'=>'', 'session'=>FALSE, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
    	$this->load->view('app/recoverpassword.php', array('logo'=>$image_properties));
    	$this->load->view('app/app_footer');

    }

    public function Register(){

        $image_properties = array(
            'src' => base_url().'assets/img/logo_negro.png',
            'alt' => 'NUUP',
            'class' => 'logo img-fluid',
            'title' => 'NUUP'
        );

        $b_elearning=FALSE;

        $title="Registro";
        $this->load->helper(array('form', 'html'));
    	$this->load->view('app/app_header', array('title'=>$title, 'logo'=>'', 'user_img'=>'', 'user_img_nav'=>'', 'session'=>FALSE, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
    	$this->load->view('app/register', array('logo'=>$image_properties));
    	$this->load->view('app/app_footer');

    }

    public function RegisterIntern()
    {

        $image_properties = array(
            'src'   => 'assets/img/logo_agro.png',
            'alt'   => 'Universidad AGRO',
            'class' => 'logo',
            'title' => 'Universidad AGRO'
        );
        $array_pais=array(
            ''=>'Selecciona país...'
        );

        $array_estados=array(
            ''=>'Selecciona un país primero'
        );

        $array_area=array(
            ''=>'Area'
        );

        $array_rol=array(
            ''=>'Rol'
        );

        $b_elearning=FALSE;

        $title="Registro Interno";
        $this->load->helper(array('form', 'html'));
    	$this->load->view('app/app_header', array('title'=>$title, 'logo'=>'', 'user_img'=>'', 'user_img_nav'=>'', 'session'=>FALSE,'b_elearning'=>$b_elearning, 'services'=>FALSE));
    	$this->load->view('app/registerintern',array('rol'=>$array_rol,'area'=>$array_area, 'pais'=>$array_pais,'estados'=>$array_estados, 'logo'=>$image_properties));
    	$this->load->view('app/app_footer');
    }

    public function RegisterExtern()
    {

        $image_properties = array(
            'src'   => 'assets/img/logo_agro.png',
            'alt'   => 'Universidad AGRO',
            'class' => 'logo',
            'title' => 'Universidad AGRO'
        );

		$array_pais=array(
			''=>'Selecciona país...'
		);

		$array_estados=array(
			''=>'Selecciona un país primero'
		);

		$array_actividad=array(
			''=>'Seleccionar actividad...'
		);

        $array_cultivo=array(
            ''=>'Selecciona un país primero'
        );

        $b_elearning=FALSE;

        $title="Registro Externo";
        $this->load->helper(array('form', 'html'));
    	$this->load->view('app/app_header', array('title'=>$title, 'logo'=>'', 'user_img'=>'', 'user_img_nav'=>'', 'session'=>FALSE, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
    	$this->load->view('app/registerextern', array('pais'=>$array_pais,'estados'=>$array_estados, 'actividad'=>$array_actividad, 'cultivo'=>$array_cultivo, 'logo'=>$image_properties));
    	$this->load->view('app/app_footer');
    }

    public function Home()
    {
        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
			$params = array(
				"business_id" => $this->session->userdata('business_id')
			);
			$services_hired = $this->services_model->HiredServices($params);
            $title="Inicio";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/home', array('services'=>$services_hired));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }
    }

    public function Profile()
    {
        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $session=$this->session->userdata('session_web');

            $title="Perfil";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$session, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/profile', array());
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

    public function Contact()
    {
        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $session=$this->session->userdata('session_web');

            $title="Contacto";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$session, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/contact', array());
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }
    }

    public function Elearning()
    {
        if($this->session->userdata('session_web'))
        {
            $array_categoria=array(
                ''=>'Selecciona categoría...'
            );

            $array_subcategoria=array(
                ''=>'Selecciona una categoría primero'
            );

            $image_properties = array(
                'src'   => 'assets/img/logo_white.png',
                'alt'   => 'Universidad AGRO',
                'class' => 'd-inline-block align-top',
                'title' => 'Universidad AGRO',
                'width' => '141',
                'height' => '39'
            );

            $b_elearning=TRUE;
            $session=$this->session->userdata('session_web');

            $title="Elearning";
            $this->load->helper(array('html', 'form'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>'', 'user_img_nav'=>'', 'session'=>$session, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/elearning', array('categoria'=>$array_categoria, 'subcategoria'=>$array_subcategoria));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

    public function library()
    {
        if($this->session->userdata('session_web'))
        {
            $array_categoria=array(
                '-'=>'Categoria',
                'cat1'=>'Categoria 1',
                'cat2'=>'Categoria 2',
                'cat3'=>'Categoria 3',
                'cat4'=>'Categoria 4',
                'cat5'=>'Categoria 5',
                'cat6'=>'Categoria 6',
                'cat7'=>'Categoria 7'
            );

            $array_subcategoria=array(
                '-'=>'Subcateogira',
                'subcat1'=>'Subcategoria 1',
                'subcat2'=>'Subcategoria 2',
                'subcat3'=>'Subcategoria 3',
                'subcat4'=>'Subcategoria 4',
                'subcat5'=>'Subcategoria 5',
                'subcat6'=>'Subcategoria 6',
                'subcat7'=>'Subcategoria 7'
            );

            $image_properties = array(
                'src'   => 'assets/img/logo_blanco.png',
                'alt'   => 'NUUP',
                'class' => 'd-inline-block align-top',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $b_elearning=TRUE;
            $session=$this->session->userdata('session_web');

            $title="Biblioteca";
            $this->load->helper(array('html', 'form'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>'', 'user_img_nav'=>'', 'session'=>$session, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/library', array('categoria'=>$array_categoria, 'subcategoria'=>$array_subcategoria));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }
    }

    public function Video($id_elemento)
    {
		if($this->session->userdata('session_web'))
        {
			$image_properties = array(
				'src'   => 'assets/img/logo_blanco.png',
				'alt'   => 'NUUP',
				'class' => 'd-inline-block align-top',
				'title' => 'NUUP',
				'width' => '141',
				'height' => '39'
			);

            $b_elearning=TRUE;
            $session=$this->session->userdata('session_web');

            $title="Video";
            $this->load->helper(array('html', 'form'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>'', 'user_img_nav'=>'', 'session'=>$session, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/video', array('categoria'=>'', 'subcategoria'=>'', 'id_elemento'=>$id_elemento));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }
    }

    public function Pdf($id_elemento)
    {
    	if($this->session->userdata('session_web'))
        {

			$image_properties = array(
				'src'   => 'assets/img/logo_blanco.png',
				'alt'   => 'NUUP',
				'class' => 'd-inline-block align-top',
				'title' => 'NUUP',
				'width' => '141',
				'height' => '39'
			);

            $b_elearning=TRUE;
            $session=$this->session->userdata('session_web');

            $title="PDF";
            $this->load->helper(array('html', 'form'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>'', 'user_img_nav'=>'', 'session'=>$session, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/pdf', array('categoria'=>'', 'subcategoria'=>'', 'id_elemento'=>$id_elemento));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }
    }

	public function Imagen($id_elemento)
	{
		if($this->session->userdata('session_web'))
		{

			$image_properties = array(
				'src'   => 'assets/img/logo_blanco.png',
				'alt'   => 'NUUP',
				'class' => 'd-inline-block align-top',
				'title' => 'NUUP',
				'width' => '141',
				'height' => '39'
			);

			$b_elearning=TRUE;
            $session=$this->session->userdata('session_web');

			$title="Imagen";
			$this->load->helper(array('html', 'form'));
			$this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>'', 'user_img_nav'=>'', 'session'=>$session, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
			$this->load->view('app/imagen', array('categoria'=>'', 'subcategoria'=>'', 'id_elemento'=>$id_elemento));
			$this->load->view('app/app_footer');
		}
		else
		{
			redirect('app','refresh');
		}
	}

    public function Newsletter($session=TRUE)
    {
        if($this->session->userdata('session_web'))
        {

            $image_properties = array(
                'src'   => 'assets/img/logo_blanco.png',
                'alt'   => 'NUUP',
                'class' => 'd-inline-block align-top',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $b_elearning=TRUE;
            $session=$this->session->userdata('session_web');

            $title="Muro";
            $this->load->helper(array('html', 'form'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>'', 'user_img_nav'=>'', 'session'=>$session, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/newsletter', array('categoria'=>'', 'subcategoria'=>''));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }
    }

    public function ranking()
    {
        if($this->session->userdata('session_web'))
        {

            $image_properties = array(
                'src'   => 'assets/img/logo_blanco.png',
                'alt'   => 'NUUP',
                'class' => 'd-inline-block align-top',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $b_elearning=TRUE;
            $session=$this->session->userdata('session_web');

            $title="Ranking";
            $this->load->helper(array('html', 'form'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>'', 'user_img_nav'=>'', 'session'=>$session, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/ranking', array('categoria'=>'', 'subcategoria'=>''));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }
    }

    public function Agenda()
    {
        if($this->session->userdata('session_web'))
        {

            $image_properties = array(
                'src'   => 'assets/img/logo_blanco.png',
                'alt'   => 'NUUP',
                'class' => 'd-inline-block align-top',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $b_elearning=TRUE;
            $session=$this->session->userdata('session_web');

            $title="Agenda + Actividades";
            $this->load->helper(array('html', 'form'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>'', 'user_img_nav'=>'', 'session'=>$session, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/agenda', array('categoria'=>'', 'subcategoria'=>''));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }
    }

    public function New_event()
    {
        if($this->session->userdata('session_web'))
        {

            $image_properties = array(
                'src'   => 'assets/img/logo_white.png',
                'alt'   => 'Universidad AGRO',
                'class' => 'd-inline-block align-top',
                'title' => 'Universidad AGRO',
                'width' => '141',
                'height' => '39'
            );

            $b_elearning=TRUE;
            $session=$this->session->userdata('session_web');

            $title="Nuevo Evento";
            $this->load->helper(array('html', 'form'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>'', 'user_img_nav'=>'', 'session'=>$session, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/new_event', array('categoria'=>'', 'subcategoria'=>''));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }
    }

    public function Games()
    {
        $session=$this->session->userdata('session_web');
        if($this->session->userdata('session_web'))
        {

            $image_properties = array(
                'src'   => 'assets/img/logo_blanco.png',
                'alt'   => 'NUUP',
                'class' => 'd-inline-block align-top',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $b_elearning=TRUE;
            $session=$this->session->userdata('session_web');

            $title="Juegos";
            $this->load->helper(array('html', 'form'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>'', 'user_img_nav'=>'', 'session'=>$session, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/games', array('categoria'=>'', 'subcategoria'=>''));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }
    }

    public function Roulette()
    {
        if($this->session->userdata('session_web'))
        {

            $image_properties = array(
                'src'   => 'assets/img/logo_blanco.png',
                'alt'   => 'NUUP',
                'class' => 'd-inline-block align-top',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $b_elearning=TRUE;
            $session=$this->session->userdata('session_web');

            $title="Juego Ruleta";
            $this->load->helper(array('html', 'form'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>'', 'user_img_nav'=>'', 'session'=>$session, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/roulette', array('categoria'=>'', 'subcategoria'=>''));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }
    }

    public function Roulette_quest()
    {
        if($this->session->userdata('session_web'))
        {

            $image_properties = array(
                'src'   => 'assets/img/logo_blanco.png',
                'alt'   => 'NUUP',
                'class' => 'd-inline-block align-top',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $b_elearning=TRUE;
            $session=$this->session->userdata('session_web');

            $title="Pregunta";
            $this->load->helper(array('html', 'form'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>'', 'user_img_nav'=>'', 'session'=>$session, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/roulette_quest', array('categoria'=>'', 'subcategoria'=>''));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }
    }

	public function Cuestionarios()
	{
		if($this->session->userdata('session_web'))
		{
			$image_properties = array(
                'src'   => 'assets/img/logo_blanco.png',
                'alt'   => 'NUUP',
                'class' => 'd-inline-block align-top',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

			$image_user = array(
				'src'   => 'assets/img/persona_1.png',
				'alt'   => 'user',
				'class' => 'card-img-top rounded-circle',
				'title' => 'user'
			);

			$mini_image_user = array(
				'src'   => 'assets/img/cw2-18.png',
				'alt'   => 'user',
				'class' => 'float-right',
				'title' => 'user',
				'width' => ''
			);

			$b_elearning=TRUE;

			$title="Cuestionarios";
			$this->load->helper(array('html','form'));
			$this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>FALSE));
			$this->load->view('app/cuestionarios', array());
			$this->load->view('app/app_footer');
		}
		else
		{
			redirect('app','refresh');
		}
	}
	public function Cuestionario($quiz_id)
	{
		if($this->session->userdata('session_web'))
		{
			$image_properties = array(
                'src'   => 'assets/img/logo_blanco.png',
                'alt'   => 'NUUP',
                'class' => 'd-inline-block align-top',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

			$image_user = array(
				'src'   => 'assets/img/persona_1.png',
				'alt'   => 'user',
				'class' => 'card-img-top rounded-circle',
				'title' => 'user'
			);

			$mini_image_user = array(
				'src'   => 'assets/img/cw2-18.png',
				'alt'   => 'user',
				'class' => 'float-right',
				'title' => 'user',
				'width' => ''
			);

			$b_elearning=TRUE;

			$title="Cuestionario";
			$this->load->helper(array('html'));
			$this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>FALSE));
			$this->load->view('app/cuestionario', array("quiz_id"=>$quiz_id));
			$this->load->view('app/app_footer');
		}
		else
		{
			redirect('app','refresh');
		}
	}

    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 11/10/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Funcion para cerrar la sesion actual
     ***********************************************************************/
	public function CloseSession(){
		$this->output->set_header('Last-Modified:'.gmdate('D, d M Y H:i:s').'GMT');
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Cache-Control: post-check=0, pre-check=0',false);
		$this->output->set_header('Pragma: no-cache');
		$this->session->sess_destroy();
		header("Location:".base_url().'app');
	}

    public function Notification(){

        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
			$params = array(
				"business_id" => $this->session->userdata('business_id')
			);
            $services_hired = $this->services_model->HiredServices($params);
            $title="Notificacion";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/notification', array('services'=>$services_hired));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

    public function Pricing()
    {
        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
			$params = array(
				"business_id" => $this->session->userdata('business_id')
			);
            $services_hired = $this->services_model->HiredServices($params);
            $title="Precios";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>TRUE));
            $this->load->view('app/pricing', array());
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }
    }

    public function Basic_pack()
    {
        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
			$params = array(
				"business_id" => $this->session->userdata('business_id')
			);
            $services_hired = $this->services_model->HiredServices($params);
            $title="Paquete Basico";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>TRUE));
            $this->load->view('app/basic_pack', array());
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }
    }

    public function middle_pack()
    {
        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
			$params = array(
				"business_id" => $this->session->userdata('business_id')
			);
            $services_hired = $this->services_model->HiredServices($params);
            $title="Paquete intermedio";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>TRUE));
            $this->load->view('app/middle_pack', array());
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }
    }

    public function advance_pack()
    {
        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $services_hired = $this->services_model->HiredServices($this->session->userdata('business_id'));
            $title="Paquete avanzado";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>TRUE));
            $this->load->view('app/advance_pack', array());
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }
    }

    public function Services(){

        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $services_hired = $this->services_model->HiredServices($this->session->userdata('business_id'));
            $title="Servicios";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>TRUE));
            $this->load->view('app/services', array('services'=>$services_hired));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

    public function Cart(){

        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $services_hired = $this->services_model->HiredServices($this->session->userdata('business_id'));
            $title="Carrito";
            $this->load->helper(array('html','form'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>TRUE));
            $this->load->view('app/cart', array('services'=>$services_hired));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

    public function Team(){

        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $services_hired = $this->services_model->HiredServices($this->session->userdata('business_id'));
            $title="Equipo";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/team', array('services'=>$services_hired));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

    public function Feedback(){

        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $services_hired = $this->services_model->HiredServices($this->session->userdata('business_id'));
            $title="Feedback";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/feedback', array('services'=>$services_hired));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

    public function AddNote(){

        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $services_hired = $this->services_model->HiredServices($this->session->userdata('business_id'));
            $title="Nueva nota";
            $this->load->helper(array('html', 'form'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/add_note', array('services'=>$services_hired));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

    public function Search(){

        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $services_hired = $this->services_model->HiredServices($this->session->userdata('business_id'));
            $title="Buscar";
            $this->load->helper(array('html', 'form'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/search', array('services'=>$services_hired));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

    public function GiveFeedback(){

        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $services_hired = $this->services_model->HiredServices($this->session->userdata('business_id'));
            $title="Dar Feedback";
            $this->load->helper(array('html', 'form'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/give_feedback', array('services'=>$services_hired));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

    public function FeedbackPost($feedback_id){

        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $services_hired = $this->services_model->HiredServices($this->session->userdata('business_id'));
            $title="Post";
            $this->load->helper(array('html', 'form'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>FALSE));
            $this->load->view('app/feedback_post', array("feedback_id"=>$feedback_id));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

    public function ServAgenda(){

        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $services_hired = $this->services_model->HiredServices($this->session->userdata('business_id'));
            $title="Servicios";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>TRUE));
            $this->load->view('app/serv_agenda', array('services'=>$services_hired));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

    public function ServFeedback(){

        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $services_hired = $this->services_model->HiredServices($this->session->userdata('business_id'));
            $title="Servicios";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>TRUE));
            $this->load->view('app/serv_feedback', array('services'=>$services_hired));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

    public function ServWall(){

        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $services_hired = $this->services_model->HiredServices($this->session->userdata('business_id'));
            $title="Servicios";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>TRUE));
            $this->load->view('app/serv_wall', array('services'=>$services_hired));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

    public function ServCuest(){

        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $services_hired = $this->services_model->HiredServices($this->session->userdata('business_id'));
            $title="Servicios";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>TRUE));
            $this->load->view('app/serv_cuest', array('services'=>$services_hired));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

    public function ServGames(){

        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $services_hired = $this->services_model->HiredServices($this->session->userdata('business_id'));
            $title="Servicios";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>TRUE));
            $this->load->view('app/serv_games', array('services'=>$services_hired));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

    public function ServRank(){

        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $services_hired = $this->services_model->HiredServices($this->session->userdata('business_id'));
            $title="Servicios";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>TRUE));
            $this->load->view('app/serv_rank', array('services'=>$services_hired));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

    public function ServLibrary(){

        if($this->session->userdata('session_web'))
        {
            $image_properties = array(
                'src'   => 'assets/img/logo_negro.png',
                'alt'   => 'NUUP',
                'class' => 'img-fluid',
                'title' => 'NUUP',
                'width' => '141',
                'height' => '39'
            );

            $image_user = array(
                'src'   => 'assets/img/persona_1.png',
                'alt'   => 'user',
                'class' => 'card-img-top rounded-circle',
                'title' => 'user'
            );

            $mini_image_user = array(
                'src'   => $this->session->userdata('profile_photo'),
                'alt'   => 'user',
                'class' => 'float-right rounded-circle',
                'title' => 'user',
                'width' => '40',
                'height' => '40'
            );

            $b_elearning=FALSE;
            $services_hired = $this->services_model->HiredServices($this->session->userdata('business_id'));
            $title="Servicios";
            $this->load->helper(array('html'));
            $this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>$image_user, 'user_img_nav'=>$mini_image_user, 'session'=>$this->session->userdata('session_web'), 'b_elearning'=>$b_elearning, 'services'=>TRUE));
            $this->load->view('app/serv_library', array('services'=>$services_hired));
            $this->load->view('app/app_footer');
        }
        else
        {
            redirect('app','refresh');
        }

    }

	public function ProfilerQuiz()
	{
		if($this->session->userdata('session_web'))
		{

			$image_properties = array(
				'src'   => 'assets/img/logo_blanco.png',
				'alt'   => 'NUUP',
				'class' => 'd-inline-block align-top',
				'title' => 'NUUP',
				'width' => '141',
				'height' => '39'
			);

			$b_elearning=TRUE;
			$session=$this->session->userdata('session_web');

			$title="Juego Ruleta";
			$this->load->helper(array('html', 'form'));
			$this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>'', 'user_img_nav'=>'', 'session'=>$session, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
			$this->load->view('app/profiler', array('categoria'=>'', 'subcategoria'=>''));
			$this->load->view('app/app_footer');
		}
		else
		{
			redirect('app','refresh');
		}
	}
	public function ProductQuiz()
	{
		if($this->session->userdata('session_web'))
		{

			$image_properties = array(
				'src'   => 'assets/img/logo_blanco.png',
				'alt'   => 'NUUP',
				'class' => 'd-inline-block align-top',
				'title' => 'NUUP',
				'width' => '141',
				'height' => '39'
			);

			$b_elearning=TRUE;
			$session=$this->session->userdata('session_web');

			$title="Juego Ruleta";
			$this->load->helper(array('html', 'form'));
			$this->load->view('app/app_header', array('title'=>$title, 'logo'=>$image_properties, 'user_img'=>'', 'user_img_nav'=>'', 'session'=>$session, 'b_elearning'=>$b_elearning, 'services'=>FALSE));
			$this->load->view('app/products', array('categoria'=>'', 'subcategoria'=>''));
			$this->load->view('app/app_footer');
		}
		else
		{
			redirect('app','refresh');
		}
	}
}
