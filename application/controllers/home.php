<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller
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
    // Si l'utilisateur est un administrateur
    if ($this->session->userdata('is_admin'))
    {
			// L'utilisateur n'est pas dans l'adminitration
      $this->session->set_userdata(array('in_admin' => FALSE));
		}

    // Définition des données variables du template
    $tpl['title'] = $this->lang->line('menu_home');

    // On charge la vue qui contient le corps de la page
    $tpl['file'] = 'home/index';

    // Derniers films analysés et ajoutés
    $tpl['last_movies'] = $this->movies_model->get_last();

    // Dernières séries analysées et ajoutées
    $tpl['last_tvshows'] = $this->tvshows_model->get_last();

    // Derniers épisodes analysés et ajoutés
    $tpl['last_episodes'] = $this->episodes_model->get_last();

//echo '<pre>'.print_r($tpl['last_episodes'], TRUE).'</pre>'; die();

    // On charge la page dans le template
    $this->load->view('includes/template', $tpl);
  }

}

/* End of file home.php */
/* Location: ./application/controllers/home.php */
