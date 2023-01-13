<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Client extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('library_mdl', 'library');
		$this->load->model('admin_mdl', "admin");
        $this->load->model('posts_mdl', 'post');
	}

	public function index()
	{
		$this->load->view('client/login');
	}

	public function inicio()
	{
		$this->load->view('contenedor.php', array('view_' => 'client/home'));
	}

	public function showTest()
	{
		$this->load->view('client/quiz/menu_test.php');
	}

	public function showTraining()
	{
		$this->load->view('client/training/menu_training.php');
	}

	public function showLibrary()
	{
		$this->load->view('client/library/menu_library.php');
	}

	public function home()
	{
		$idioma = ($this->input->post("idioma") ? $this->input->post("idioma") : 'es_ES');
		set_translation_language($idioma);
		$this->load->view('client/home.php');
	}

	public function showSignUp()
	{
		$this->load->view('client/signup.php');
	}

	public function showQuestionsPage()
	{
		$this->load->view('client/quiz/questions.php');
	}

	public function showTrainingPage()
	{
		$this->load->view('client/training/training.php');
	}

	public function showVideoPage()
	{
		$this->load->view('client/library/type_video.php');
	}

	public function showImagePage()
	{
		$this->load->view('client/library/type_image.php');
	}

	public function showPdfPage()
	{
		$this->load->view('client/library/type_pdf.php');
	}

	public function getNews()
	{
		$business_id = $this->input->post('business_id');
		$user_id = $this->input->post('user_id');
		$news = $this->post->obtener_noticia($business_id, $user_id);
		return $this->output->set_content_type('application/json')->set_output(json_encode($news));
	}

	public function showNewsPage()
	{
		$this->load->view('client/news/news.php');
	}
	
}