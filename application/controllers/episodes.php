<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Episodes extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    // Chargement des modèles de la base de données 'xbmc_video'
    $this->load->model('video/actors_model');
    $this->load->model('video/episodes_model');
    $this->load->model('video/video_files_model');
    $this->load->model('video/video_paths_model');
    $this->load->model('video/tvshows_model');
  }

  public function view()
  {
    // Si l'utilisateur est un administrateur
    if ($this->session->userdata('is_admin'))
    {
			// L'utilisateur n'est pas dans l'adminitration
      $this->session->set_userdata(array('in_admin' => FALSE));
		}

    // Identifiant de l'épisode correspondant à :
    // identifiant série (2)/numéro saison (3)/numéro d'épisode (4)
    $episode_ids = $this->episodes_model->get_ids(intval($this->uri->segments[2]),
                                                  intval($this->uri->segments[3]),
                                                  intval($this->uri->segments[4]));

    // La fonction précédente retourne un tableau même d'un seul élément
    $episode_id = $episode_ids[0];

    // Lecture des informations complètes de l'épisode
    $episodes = $this->episodes_model->get($episode_id);

    // La fonction précédente retourne un tableau même d'un seul élément
    $episode = $episodes[0];

    $tpl['title'] = $episode->local_title;
    $tpl['episode'] = $episode;

    // On charge la vue qui contient le corps de la page
    $tpl['file'] = 'video/episodes/view';

    // On charge la page dans le template
    $this->load->view('includes/template', $tpl);
  }

}

/* End of file episodes.php */
/* Location: ./application/controllers/episodes.php */
