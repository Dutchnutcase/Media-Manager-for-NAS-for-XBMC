<?php

class Tvshows extends CI_Controller
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
   * Affiche la liste de toutes les séries TV sans critère de sélection
   */
  function index()
  {
    // Si l'utilisateur est un administrateur
    if ($this->session->userdata('is_admin'))
    {
			// L'utilisateur n'est pas dans l'adminitration
      $this->session->set_userdata(array('in_admin' => FALSE));
		}

    $per_page = 6;

    // Adresse de base pour la pagination
    $base_url = site_url('tvshows/page');
    $tpl['title'] = $this->lang->line('list_tvshows');

    // Récupération des séries TV
    $tpl['tvshows'] = $this->tvshows_model->get_all($per_page, intval($this->uri->segment(3)));

    // Total des séries TV pour la pagination
    $total = $this->tvshows_model->count_all();

    // On charge la vue qui contient le corps de la page
    $tpl['file'] = 'video/tvshows/index';

    // Paramètrage de la pagination
    // Le 3ème segment contient le numéro de la page
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
   * Affiche les informations d'une série TV
   */
  public function view()
  {
    // Si l'utilisateur est un administrateur
    if ($this->session->userdata('is_admin'))
    {
			// L'utilisateur n'est pas dans l'adminitration
      $this->session->set_userdata(array('in_admin' => FALSE));
		}

    // Lecture des informations complètes de la série TV
    $tvshows = $this->tvshows_model->get(intval($this->uri->segments[2]), TRUE);

    // La fonction précédente retourne un tableau même d'un seul élément
    $tvshow = $tvshows[0];

//    echo '<pre>'.print_r($tvshow, true).'</pre>'; die();

		// Si l'utilisateur peut changer les images, on charge les miniatures en les créant le cas échéant
		if ($this->session->userdata('can_change_images'))
		{
			$this->tvshows_model->prepare_to_display($tvshow);
		}

    $tpl['title'] = $tvshow->title;
    $tpl['tvshow'] = $tvshow;

    $seasons = array();
    foreach($tvshow->seasons as $key => $value)
    {
			if ($key != -1)
			{
      $season = new stdClass();
      $season->id = $key;
      $seasons[] = $season;
			}
    }

    $tpl['seasons'] = $seasons;

    // On charge la vue qui contient le corps de la page
    $tpl['file'] = 'video/tvshows/view';

    // On charge la page dans le template
    $this->load->view('includes/template', $tpl);
  }

  /**
   * Contrôle présence $_POST et conversion vers $_GET
   */
	public function pre_search()
	{
		// Si pas de titre alors on affiche la liste des séries TV
		if ($this->input->post('query') == '') redirect('tvshows');

		redirect('tvshows/search/'.$this->input->post('query'));
	}

  /**
   * Recherche d'une série TV
   */
  public function search()
  {
		// Titre recherché dans le dernier segment de l'url
		$query = $this->uri->segments[3];

    $per_page = 6;

    // Adresse de base pour la pagination
    $base_url = site_url('tvshows/search/'.$query.'/page');
    $tpl['title'] = sprintf($this->lang->line('list_search_tvshows'), urldecode($query));

    $tpl['tvshows'] = $this->tvshows_model->search_by_local_title($query, $per_page, intval($this->uri->segment(5)));

    // Total des séries TV pour la pagination
    $total = $this->tvshows_model->count_all_by_local_title($query);

		// Si une seule série TV a été trouvée, on affche sa page
		if ($total == 1)
		{
			redirect('tvshows/'.$tpl['tvshows'][0]->id);
		}

    // On charge la vue qui contient le corps de la page
		if ($total == 0)
		{
			$tpl['media'] = 'tvshows';
			$tpl['include'] = '_no_results';
		}
		else
		{
			$tpl['file'] = 'video/tvshows/index';
		}

    // Paramètrage de la pagination
    // Le 5ème segment contient le numéro de la page
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

  function ajax_get_episodes_by_season($season_id = NULL)
  {
    $per_page = 5;

    $tvshow_id = intval($this->uri->segments[2]);

    // $season_id n'est pas NULL lors de l'affichage des informations d'une série
    // Ensuite, la pagination transmet $season_id via l'url
    if (is_null($season_id)) $season_id = intval($this->uri->segments[3]);

    // Adresse de base pour la pagination
    $base_url = site_url('episodes_by_season/'.$tvshow_id.'/'.$season_id.'/page');

    $data['title'] = sprintf($this->lang->line('media_list_season'), $season_id);
    if ($season_id == -1) $data['title'] = $this->lang->line('media_list_all_season');
    if ($season_id == 0) $data['title'] = $this->lang->line('media_list_special_season');

    // Récupération des épisodes
    $data['episodes'] = $this->episodes_model->get_all_by_season($tvshow_id, $season_id, $per_page, intval($this->uri->segment(5)));

    // Paramètrage de la pagination
    // Le 5ème segment contient le numéro de la page
    $config['base_url'] = $base_url;
    $config['total_rows'] = $this->episodes_model->count_all_by_season($tvshow_id, $season_id);
    $config['per_page'] = $per_page;
    $config['uri_segment'] = '5';

    $config['first_link'] = $this->lang->line('pagination_first_link');
    $config['prev_link'] = $this->lang->line('pagination_prev_link');
    $config['next_link'] = $this->lang->line('pagination_next_link');
    $config['last_link'] = $this->lang->line('pagination_last_link');
    $config['num_links'] = 2;

    $this->my_pagination->initialize($config);

    // On charge la page dans le template
    $this->load->view('content/video/episodes/_rows', $data);
  }

  /**
   * Gère la navigation parmi les séries TV en fonction d'un des 3 critères suivants
   * genre / studio  / année
   */
  public function by_criteria()
  {
    // Critère de sélection
    $criteria = $this->uri->segments[2];

    $per_page = 12;

    switch ($criteria)
    {
      case 'genre':
        $id = $this->uri->segments[3];

        // Nom du genre
        $name = $this->genres_model->get_by_id($id);

        // Adresse de base pour la pagination
        $base_url = site_url('tvshows/genre/'.$id.'/');
        $tpl['title'] = sprintf($this->lang->line('list_tvshows_genre'), $name);

        // Récupération des films correspondant au critère
        $tpl['tvshows'] = $this->tvshows_model->get_all_by_genre($id, $per_page, (int) $this->uri->segment(4));

        // Total des films pour la pagination
        $total = $this->tvshows_model->count_all_by_genre($id);
        break;

      case 'studio':
        $id = $this->uri->segments[3];

        // Nom du studio
        $name = $this->studios_model->get_by_id($id);

        // Adresse de base pour la pagination
        $base_url = site_url('tvshows/studio/'.$id.'/');
        $tpl['title'] = sprintf($this->lang->line('list_tvshows_studio'), $name);

        // Récupération des films correspondant au critère
        $tpl['tvshows'] = $this->tvshows_model->get_all_by_studio($id, $per_page, (int) $this->uri->segment(4));

        // Total des films pour la pagination
        $total = $this->tvshows_model->count_all_by_studio($id);
        break;

      case 'year':
        $year = $this->uri->segments[3];

        // Adresse de base pour la pagination
        $base_url = site_url('tvshows/year/'.$year.'/');
        $tpl['title'] = sprintf($this->lang->line('list_tvshows_year'), $year);

        // Récupération des films correspondant au critère
        $tpl['tvshows'] = $this->tvshows_model->get_all_by_year($year, $per_page, (int) $this->uri->segment(4));

        // Total des films pour la pagination
        $total = $this->tvshows_model->count_all_by_year($year);
        break;
    }

    // On charge la vue qui contient le corps de la page
    $tpl['file'] = 'video/tvshows/index';

    // Paramètrage de la pagination
    // Le 4ème segment contient le numéro de la page
    $config['base_url'] = $base_url;
    $config['total_rows'] = $total;
    $config['per_page'] = $per_page;
    $config['uri_segment'] = '4';

    $config['first_link'] = $this->lang->line('pagination_first_link');
    $config['prev_link'] = $this->lang->line('pagination_prev_link');
    $config['next_link'] = $this->lang->line('pagination_next_link');
    $config['last_link'] = $this->lang->line('pagination_last_link');
    $config['num_links'] = 5;

    $this->my_pagination->initialize($config);

    // On charge la page dans le template
    $this->load->view('includes/template', $tpl);
  }

}

/* End of file tvshows.php */
/* Location: ./system/application/controllers/tvshows.php */
