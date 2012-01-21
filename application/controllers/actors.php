<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Actors extends CI_Controller
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
    $this->load->model('video/sets_model');
    $this->load->model('video/studios_model');
    $this->load->model('video/tvshows_model');
  }

  /**
   * Affiche la liste de toutes les personnalités
   */
  function index()
  {
    // Si l'utilisateur est un administrateur
    if ($this->session->userdata('is_admin'))
    {
			// L'utilisateur n'est pas dans l'adminitration
      $this->session->set_userdata(array('in_admin' => FALSE));
		}

    $per_page = 8;

    // Adresse de base pour la pagination
    $base_url = site_url('actors/page');
    $tpl['title'] = $this->lang->line('list_actors');

    $tpl['actors'] = $this->actors_model->get_all($per_page, intval($this->uri->segment(3)));

    // Total des personnalités pour la pagination
    $total = $this->actors_model->count_all();

    // On charge la vue qui contient le corps de la page
    $tpl['file'] = 'video/actors/index';

    // Paramètrage de la pagination
    // Le 5ème segment contient le numéro de la page
    $config['base_url'] = $base_url;
    $config['total_rows'] = $total;
    $config['per_page'] = $per_page;
    $config['uri_segment'] = '3';

    $config['first_link'] = $this->lang->line('pagination_first_link');
    $config['prev_link'] = $this->lang->line('pagination_prev_link');
    $config['next_link'] = $this->lang->line('pagination_next_link');
    $config['last_link'] = $this->lang->line('pagination_last_link');
    $config['num_links'] = 5;

    $this->my_pagination->initialize($config);

    // On charge la page dans le template
    $this->load->view('includes/template', $tpl);
  }

  /**
   * Affiche les informations d'une personnalité
   */
  public function view()
  {
    // Si l'utilisateur est un administrateur
    if ($this->session->userdata('is_admin'))
    {
			// L'utilisateur n'est pas dans l'adminitration
      $this->session->set_userdata(array('in_admin' => FALSE));
		}

    // Lecture des informations complètes de la personnalité
    $actor = $this->actors_model->get_by_id(intval($this->uri->segments[2]));

    // Valeurs par défaut
    $tpl['movies_written'] = '';
    $tpl['movies_directed'] = '';
    $tpl['episodes_written'] = '';
    $tpl['episodes_directed'] = '';
    $tpl['movies_played'] = '';
    $tpl['tvshows_written'] = '';
    $tpl['tvshows_directed'] = '';
    $tpl['tvshows_played'] = '';

    if ($this->movies_model->count_all_by_writer($actor->id) > 0)
    {
      ob_start();
      $this->ajax_get_movies_written();
      $tpl['movies_written'] = ob_get_contents();
      ob_end_clean();
    }

    if ($this->movies_model->count_all_by_director($actor->id) > 0)
    {
      ob_start();
      $this->ajax_get_movies_directed();
      $tpl['movies_directed'] = ob_get_contents();
      ob_end_clean();
    }

    if ($this->episodes_model->count_all_by_writer($actor->id) > 0)
    {
      ob_start();
      $this->ajax_get_episodes_written();
      $tpl['episodes_written'] = ob_get_contents();
      ob_end_clean();
    }

    if ($this->episodes_model->count_all_by_director($actor->id) > 0)
    {
      ob_start();
      $this->ajax_get_episodes_directed();
      $tpl['episodes_directed'] = ob_get_contents();
      ob_end_clean();
    }

    if ($this->movies_model->count_all_by_actor($actor->id) > 0)
    {
      ob_start();
      $this->ajax_get_movies_played();
      $tpl['movies_played'] = ob_get_contents();
      ob_end_clean();
    }

    if ($this->tvshows_model->count_all_by_actor($actor->id) > 0)
    {
      ob_start();
      $this->ajax_get_tvshows_played();
      $tpl['tvshows_played'] = ob_get_contents();
      ob_end_clean();
    }

    $tpl['title'] = $actor->name;
    $tpl['actor'] = $actor;

    // On charge la vue qui contient le corps de la page
    $tpl['file'] = 'video/actors/view';

    // On charge la page dans le template
    $this->load->view('includes/template', $tpl);
  }

  /**
   * Contrôle présence $_POST et conversion vers $_GET
   */
	public function pre_search()
	{
		// Si pas de nom alors on affiche la liste des personnalités
		if ($this->input->post('query') == '') redirect('actors');
		
		redirect('actors/search/'.$this->input->post('query'));
	}

  /**
   * Recherche d'une personnalité
   */
  public function search()
  {
		// Nom recherché dans le dernier segment de l'url
		$query = $this->uri->segments[3];

    $per_page = 8;

    // Adresse de base pour la pagination
    $base_url = site_url('actors/search/'.$query.'/page');
    $tpl['title'] = sprintf($this->lang->line('list_search_actors'), urldecode($query));

    $tpl['actors'] = $this->actors_model->search_by_name($query, $per_page, intval($this->uri->segment(5)));

    // Total des personnalités pour la pagination
    $total = $this->actors_model->count_all_by_name($query);

		// Si une seule personnalité a été trouvée, on affche sa page
		if ($total == 1)
		{
			redirect('actors/'.$tpl['actors'][0]->id);
		}

    // On charge la vue qui contient le corps de la page
		if ($total == 0)
		{
			$tpl['media'] = 'actors';
			$tpl['include'] = '_no_results';
		}
		else
		{
			$tpl['file'] = 'video/actors/index';
		}

    // Paramètrage de la pagination
    // Le 3ème segment contient le numéro de la page
    $config['base_url'] = $base_url;
    $config['total_rows'] = $total;
    $config['per_page'] = $per_page;
    $config['uri_segment'] = '5';

    $config['first_link'] = $this->lang->line('pagination_first_link');
    $config['prev_link'] = $this->lang->line('pagination_prev_link');
    $config['next_link'] = $this->lang->line('pagination_next_link');
    $config['last_link'] = $this->lang->line('pagination_last_link');
    $config['num_links'] = 5;

    $this->my_pagination->initialize($config);

    // On charge la page dans le template
    $this->load->view('includes/template', $tpl);
	}
	
  function ajax_get_movies_written()
  {
    $per_page = 4;

    // Lecture des informations complètes de la personnalité
    $actor = $this->actors_model->get_by_id(intval($this->uri->segments[2]));

    // Adresse de base pour la pagination
    $base_url = site_url('movies_written/'.$actor->id.'/page');

    $data['title'] = sprintf($this->lang->line('list_movies_has_written'), $actor->name);

    // Récupération des films
    $data['movies'] = $this->movies_model->get_all_by_writer($actor->id, $per_page, intval($this->uri->segment(4)));

    // Paramètrage de la pagination
    // Le 4ème segment contient le numéro de la page
    $config['base_url'] = $base_url;
    $config['total_rows'] = $this->movies_model->count_all_by_writer($actor->id);
    $config['per_page'] = $per_page;
    $config['uri_segment'] = '4';

    $config['first_link'] = $this->lang->line('pagination_first_link');
    $config['prev_link'] = $this->lang->line('pagination_prev_link');
    $config['next_link'] = $this->lang->line('pagination_next_link');
    $config['last_link'] = $this->lang->line('pagination_last_link');
    $config['num_links'] = 2;

    $this->my_pagination->initialize($config);

    // On charge la page dans le template
    $this->load->view('content/video/movies/_rows', $data);
  }

  function ajax_get_movies_directed()
  {
    $per_page = 4;

    // Lecture des informations complètes de la personnalité
    $actor = $this->actors_model->get_by_id(intval($this->uri->segments[2]));

    // Adresse de base pour la pagination
    $base_url = site_url('movies_directed/'.$actor->id.'/page');

    $data['title'] = sprintf($this->lang->line('list_movies_has_directed'), $actor->name);

    // Récupération des films
    $data['movies'] = $this->movies_model->get_all_by_director($actor->id, $per_page, intval($this->uri->segment(4)));

    // Paramètrage de la pagination
    // Le 4ème segment contient le numéro de la page
    $config['base_url'] = $base_url;
    $config['total_rows'] = $this->movies_model->count_all_by_director($actor->id);
    $config['per_page'] = $per_page;
    $config['uri_segment'] = '4';

    $config['first_link'] = $this->lang->line('pagination_first_link');
    $config['prev_link'] = $this->lang->line('pagination_prev_link');
    $config['next_link'] = $this->lang->line('pagination_next_link');
    $config['last_link'] = $this->lang->line('pagination_last_link');
    $config['num_links'] = 2;

    $this->my_pagination->initialize($config);

    // On charge la page dans le template
    $this->load->view('content/video/movies/_rows', $data);
  }







  function ajax_get_episodes_written()
  {
    $per_page = 4;

    // Lecture des informations complètes de la personnalité
    $actor = $this->actors_model->get_by_id(intval($this->uri->segments[2]));

    // Adresse de base pour la pagination
    $base_url = site_url('episodes_written/'.$actor->id.'/page');

    $data['title'] = sprintf($this->lang->line('list_episodes_has_written'), $actor->name);

		// Pour afficher le nom de la série pour chaque épisode
		$data['tvshow_name'] = TRUE;

    // Récupération des épisodes
    $data['episodes'] = $this->episodes_model->get_all_by_writer($actor->id, $per_page, intval($this->uri->segment(4)));

    // Paramètrage de la pagination
    // Le 4ème segment contient le numéro de la page
    $config['base_url'] = $base_url;
    $config['total_rows'] = $this->episodes_model->count_all_by_writer($actor->id);
    $config['per_page'] = $per_page;
    $config['uri_segment'] = '4';

    $config['first_link'] = $this->lang->line('pagination_first_link');
    $config['prev_link'] = $this->lang->line('pagination_prev_link');
    $config['next_link'] = $this->lang->line('pagination_next_link');
    $config['last_link'] = $this->lang->line('pagination_last_link');
    $config['num_links'] = 2;

    $this->my_pagination->initialize($config);

    // On charge la page dans le template
    $this->load->view('content/video/episodes/_rows', $data);
  }


  function ajax_get_episodes_directed()
  {
    $per_page = 4;

    // Lecture des informations complètes de la personnalité
    $actor = $this->actors_model->get_by_id(intval($this->uri->segments[2]));

    // Adresse de base pour la pagination
    $base_url = site_url('episodes_directed/'.$actor->id.'/page');

    $data['title'] = sprintf($this->lang->line('list_episodes_has_directed'), $actor->name);

		// Pour afficher le nom de la série pour chaque épisode
		$data['tvshow_name'] = TRUE;

    // Récupération des épisodes
    $data['episodes'] = $this->episodes_model->get_all_by_director($actor->id, $per_page, intval($this->uri->segment(4)));

    // Paramètrage de la pagination
    // Le 4ème segment contient le numéro de la page
    $config['base_url'] = $base_url;
    $config['total_rows'] = $this->episodes_model->count_all_by_director($actor->id);
    $config['per_page'] = $per_page;
    $config['uri_segment'] = '4';

    $config['first_link'] = $this->lang->line('pagination_first_link');
    $config['prev_link'] = $this->lang->line('pagination_prev_link');
    $config['next_link'] = $this->lang->line('pagination_next_link');
    $config['last_link'] = $this->lang->line('pagination_last_link');
    $config['num_links'] = 2;

    $this->my_pagination->initialize($config);

    // On charge la page dans le template
    $this->load->view('content/video/episodes/_rows', $data);
  }







  function ajax_get_movies_played()
  {
    $per_page = 4;

    // Lecture des informations complètes de la personnalité
    $actor = $this->actors_model->get_by_id(intval($this->uri->segments[2]));

    // Adresse de base pour la pagination
    $base_url = site_url('movies_played/'.$actor->id.'/page');

    $data['title'] = sprintf($this->lang->line('list_movies_has_played'), $actor->name);

    // Récupération des films
    $data['movies'] = $this->movies_model->get_all_by_actor($actor->id, $per_page, intval($this->uri->segment(4)));

    // Paramètrage de la pagination
    // Le 4ème segment contient le numéro de la page
    $config['base_url'] = $base_url;
    $config['total_rows'] = $this->movies_model->count_all_by_actor($actor->id);
    $config['per_page'] = $per_page;
    $config['uri_segment'] = '4';

    $config['first_link'] = $this->lang->line('pagination_first_link');
    $config['prev_link'] = $this->lang->line('pagination_prev_link');
    $config['next_link'] = $this->lang->line('pagination_next_link');
    $config['last_link'] = $this->lang->line('pagination_last_link');
    $config['num_links'] = 2;

    $this->my_pagination->initialize($config);

    // On charge la page dans le template
    $this->load->view('content/video/movies/_rows', $data);
  }

/*
  function ajax_get_tvshows_written()
  {
    $per_page = 4;

    // Lecture des informations complètes de la personnalité
    $actor = $this->actors_model->get_by_id(intval($this->uri->segments[2]));

    // Adresse de base pour la pagination
    $base_url = site_url('tvshows_written/'.$actor->id.'/page');

    $data['title'] = sprintf($this->lang->line('list_tvshows_has_written'), $actor->name);

    // Récupération des films
    $data['tvshows'] = $this->tvshows_model->get_all_by_writer($actor->id, $per_page, intval($this->uri->segment(4)));

    // Paramètrage de la pagination
    // Le 4ème segment contient le numéro de la page
    $config['base_url'] = $base_url;
    $config['total_rows'] = $this->tvshows_model->count_all_by_writer($actor->id);
    $config['per_page'] = $per_page;
    $config['uri_segment'] = '4';

    $config['first_link'] = $this->lang->line('pagination_first_link');
    $config['prev_link'] = $this->lang->line('pagination_prev_link');
    $config['next_link'] = $this->lang->line('pagination_next_link');
    $config['last_link'] = $this->lang->line('pagination_last_link');
    $config['num_links'] = 2;

    $this->my_pagination->initialize($config);

    // On charge la page dans le template
    $this->load->view('content/video/tvshows/_rows', $data);
  }
*/

  function ajax_get_tvshows_directed()
  {
    $per_page = 4;

    // Lecture des informations complètes de la personnalité
    $actor = $this->actors_model->get_by_id(intval($this->uri->segments[2]));

    // Adresse de base pour la pagination
    $base_url = site_url('tvshows_directed/'.$actor->id.'/page');

    $data['title'] = sprintf($this->lang->line('list_tvshows_has_directed'), $actor->name);

    // Récupération des films
    $data['tvshows'] = $this->tvshows_model->get_all_by_director($actor->id, $per_page, intval($this->uri->segment(4)));

    // Paramètrage de la pagination
    // Le 4ème segment contient le numéro de la page
    $config['base_url'] = $base_url;
    $config['total_rows'] = $this->tvshows_model->count_all_by_director($actor->id);
    $config['per_page'] = $per_page;
    $config['uri_segment'] = '4';

    $config['first_link'] = $this->lang->line('pagination_first_link');
    $config['prev_link'] = $this->lang->line('pagination_prev_link');
    $config['next_link'] = $this->lang->line('pagination_next_link');
    $config['last_link'] = $this->lang->line('pagination_last_link');
    $config['num_links'] = 2;

    $this->my_pagination->initialize($config);

    // On charge la page dans le template
    $this->load->view('content/video/tvshows/_rows', $data);
  }

  function ajax_get_tvshows_played()
  {
    $per_page = 4;

    // Lecture des informations complètes de la personnalité
    $actor = $this->actors_model->get_by_id(intval($this->uri->segments[2]));

    // Adresse de base pour la pagination
    $base_url = site_url('tvshows_played/'.$actor->id.'/page');

    $data['title'] = sprintf($this->lang->line('list_tvshows_has_played'), $actor->name);

    // Récupération des séries TV
    $data['tvshows'] = $this->tvshows_model->get_all_by_actor($actor->id, $per_page, intval($this->uri->segment(4)));

    // Paramètrage de la pagination
    // Le 4ème segment contient le numéro de la page
    $config['base_url'] = $base_url;
    $config['total_rows'] = $this->tvshows_model->count_all_by_actor($actor->id);
    $config['per_page'] = $per_page;
    $config['uri_segment'] = '4';

    $config['first_link'] = $this->lang->line('pagination_first_link');
    $config['prev_link'] = $this->lang->line('pagination_prev_link');
    $config['next_link'] = $this->lang->line('pagination_next_link');
    $config['last_link'] = $this->lang->line('pagination_last_link');
    $config['num_links'] = 2;

    $this->my_pagination->initialize($config);

    // On charge la page dans le template
    $this->load->view('content/video/tvshows/_rows', $data);
  }

}

/* End of file people.php */
/* Location: ./system/application/controllers/people.php */
