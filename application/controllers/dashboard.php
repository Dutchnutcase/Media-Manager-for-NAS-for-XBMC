<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    // Chargement des modèles de la base de données 'xbmc_video'
    $this->load->model('video/actors_model');
    $this->load->model('video/countries_model');
    $this->load->model('video/episodes_model');
    $this->load->model('video/video_files_model');
    $this->load->model('video/genres_model');
    $this->load->model('video/movies_model');
    $this->load->model('video/video_paths_model');
    $this->load->model('xbmc/sources_model');
    $this->load->model('video/sets_model');
    $this->load->model('video/studios_model');
    $this->load->model('video/tvshows_model');
  }

  public function index()
  {
    // Si l'utilisateur n'est pas un administrateur, on sort
    if (!$this->session->userdata('is_admin'))
    {
			// L'utilisateur n'est pas dans l'adminitration
      $this->session->set_userdata(array('in_admin' => FALSE));
			redirect('/', 'refresh');
		}

		// L'utilisateur est dans l'adminitration
		$this->session->set_userdata(array('in_admin' => TRUE));

    // Définition des données variables du template
    $tpl['title'] = $this->lang->line('menu_dashboard');

    // On charge la vue qui contient le corps de la page
    $tpl['file'] = 'admin/index';

    // Séries au hasard
//    $tpl['random_tvshows'] = $this->tvshows_model->get_random();

    // Dernières séries analysées et ajoutées
//    $tpl['last_tvshows'] = $this->tvshows_model->get_last();

    // Derniers films analysés et ajoutés
//    $tpl['last_movies'] = $this->movies_model->get_last();

    // Dernières sagas analysées et ajoutées
//    $tpl['last_sets'] = $this->sets_model->get_last();

    // On charge la page dans le template
    $this->load->view('includes/template', $tpl);
  }

}

/* End of file home.php */
/* Location: ./application/controllers/home.php */
