<?php

class Sets extends CI_Controller
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
   * Affiche la liste de toutes les sagas de films
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
    $base_url = site_url('sets/page');
    $tpl['title'] = $this->lang->line('list_sets');

    // Récupération des sagas
    $tpl['sets'] = $this->sets_model->get_all($per_page, intval($this->uri->segment(3)));

    // Total des sagas pour la pagination
    $total = $this->sets_model->count_all();

    // On charge la vue qui contient le corps de la page
    $tpl['file'] = 'video/sets/index';

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

    $this->my_pagination->initialize($config);

    // On charge la page dans le template
    $this->load->view('includes/template', $tpl);
  }

  /**
   * Affiche les informations d'une saga de films
   */
  public function view()
  {
    // Si l'utilisateur est un administrateur
    if ($this->session->userdata('is_admin'))
    {
			// L'utilisateur n'est pas dans l'adminitration
      $this->session->set_userdata(array('in_admin' => FALSE));
		}

    // Lecture des informations complètes de la saga
    $set = $this->sets_model->get((int) $this->uri->segments[2]);

//echo '<pre>'.print_r($set, TRUE).'</pre>'; die();

		// Si l'utilisateur peut changer les images, on charge les miniatures en les créant le cas échéant
		if ($this->session->userdata('can_change_images'))
		{
			set_time_limit(60000); //fixe un delai maximum d'execution de 600 secondes soit 10 minutes.

			// Pour tous les films de cette saga
			foreach($set->movies as $movie)
			{
				// Pour toutes les affiches de ce film
				foreach($movie->images->posters as $poster)
				{
					// Téléchargement de l'affiche via un fichier temporaire effacé ensuite si miniature absente
					if (!file_exists($poster->filename))
					{
						$temp_filename = $this->xbmc_lib->images_cache_dir.'temp';
						$this->xbmc_lib->download($poster->real_url, $temp_filename);
						$this->xbmc_lib->create_image($temp_filename, $poster->filename, $this->xbmc_lib->poster_size);
						unlink($temp_filename);
						sleep(2); // Attente de 2 secondes pour soulager le serveur
					}
				}
			}

			// Pour tous les films de cette saga
			foreach($set->movies as $movie)
			{
				// Pour tous les fonds d'écran de ce film
				foreach($movie->images->backdrops as $backdrop)
				{
					// Téléchargement de l'affiche via un fichier temporaire effacé ensuite si miniature absente
					if (!file_exists($backdrop->filename))
					{
						$temp_filename = $this->xbmc_lib->images_cache_dir.'temp';
						$this->xbmc_lib->download($backdrop->real_url, $temp_filename);
						$this->xbmc_lib->create_image($temp_filename, $backdrop->filename, $this->xbmc_lib->backdrop_size);
						unlink($temp_filename);
						sleep(3); // Attente de 3 secondes pour soulager le serveur
					}
				}
			}
		}

    $tpl['title'] = $set->name;
    $tpl['set'] = $set;

    // On charge la vue qui contient le corps de la page
    $tpl['file'] = 'video/sets/view';

    // On charge la page dans le template
    $this->load->view('includes/template', $tpl);
  }

  /**
   * Contrôle présence $_POST et conversion vers $_GET
   */
	public function pre_search()
	{
		// Si pas de nom alors on affiche la liste des sagas
		if ($this->input->post('query') == '') redirect('sets');

		redirect('sets/search/'.$this->input->post('query'));
	}

  /**
   * Recherche d'une saga
   */
  public function search()
  {
		// Nom recherché dans le dernier segment de l'url
		$query = $this->uri->segments[3];

    $per_page = 6;

    // Adresse de base pour la pagination
    $base_url = site_url('sets/search/'.$query.'/page');
    $tpl['title'] = sprintf($this->lang->line('list_search_sets'), urldecode($query));

    $tpl['sets'] = $this->sets_model->search_by_name($query, $per_page, intval($this->uri->segment(5)));

    // Total des sagas pour la pagination
    $total = $this->sets_model->count_all_by_name($query);

		// Si une seule saga a été trouvée, on affche sa page
		if ($total == 1)
		{
			redirect('sets/'.$tpl['sets'][0]->id);
		}

    // On charge la vue qui contient le corps de la page
		if ($total == 0)
		{
			$tpl['media'] = 'sets';
			$tpl['include'] = '_no_results';
		}
		else
		{
			$tpl['file'] = 'video/sets/index';
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

  public function ajax_delete_poster()
  {
    // Appel via ajax ?
    if (IS_AJAX)
    {
      // Identifiant de la saga
      $set_id = (int) $this->uri->segments[3];

      $json = array ('message' => $this->lang->line('msg_set_poster_deleted'));

      // Entête pour générer la réponse au format json
      header('Expires: ' . gmdate('r', 0));
      header('Content-type: application/json');
      echo json_encode($json);
      die();
    }
  }

  public function ajax_change_order()
  {
    // Appel via ajax ?
    if (IS_AJAX)
    {
      // Liste des identifiants de films dans l'ordre
      $order = $this->input->post('order');

      /* Mise à jour de l'ordre des films dans la saga pour chaque identifiant
       * de film de la liste
       */
      foreach($order as $key => $value)
      {
        $this->movies_model->set_order_in_set(intval($value), sprintf('%02d', intval($key)+1));
      }

      $json = array ('message' => $this->lang->line('msg_set_order_updated'));

      // Entête pour générer la réponse au format json
      header('Expires: ' . gmdate('r', 0));
      header('Content-type: application/json');
      echo json_encode($json);
      die();
    }
  }

  public function ajax_get_list()
  {
    // Appel via ajax ?
    if (IS_AJAX)
    {
      $sets = $this->sets_model->get_all();

      $json = array();
      foreach($sets as $set)
      {
        $set->total .= ($set->total > 1) ? ' '.$this->lang->line('commmons_movies') : ' '.$this->lang->line('commmons_movie');
        $json[] = $set;
      }

      // Entête pour générer la réponse au format json
      header('Expires: ' . gmdate('r', 0));
      header('Content-type: application/json');
      echo json_encode($json);
      die();
    }
  }

  function ajax_get_sets()
  {
    $per_page = 5;

    // Adresse de base pour la pagination
    $base_url = site_url('sets/page');
    $data['title'] = $this->lang->line('list_sets');

    // Récupération des utilisateurs
    $data['sets'] = $this->sets_model->get_all($per_page, intval($this->uri->segment(3)));

    // Total des utilisateurs pour la pagination
    $total = $this->sets_model->count_all();

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
    $this->load->view('content/video/sets/_rows', $data);
  }

  function ajax_add()
  {
    // Appel via ajax ?
    if (IS_AJAX)
    {
      $name = $this->input->post('name');

      // Vérifie que la saga n'existe pas déjà
      if ($this->sets_model->search($name) != 0)
      {
        $json = array('message' => $this->lang->line('msg_new_set_exists'));
      }
      else
      {
        // Préparation des données pour retour
        $set->id = $this->sets_model->add($name);
        $set->name = $name;

        $data['value'] = $set;

        $json = array('success' => '1',
                      'set' => $this->load->view('content/video/sets/_row', $data, TRUE),
                      'message' => $this->lang->line('msg_new_set_added')
                     );
      }

      $json = json_encode($json);

      header('Content-type: application/json');
      echo $json;
      die();
    }
  }

	/*
	 * Efface une saga, les images et les informations qui lui sont rattachées
	*/
  function delete()
  {
		// L'utilisateur peut changer les informations ?
    if ($this->session->userdata('can_change_infos'))
    {
			// On vient d'une autre page ?
			if (isset($_SERVER['HTTP_REFERER']))
			{
				// L'utilisateur vient de la page de consultation de la saga dont l'identifiant est fournie dans l'url ?
				if ($_SERVER['HTTP_REFERER'] == base_url().'sets/'.$this->uri->segments[3])
				{
					// Suppression des informations complètes de la saga
					$this->sets_model->delete((int) $this->uri->segments[3]);

					$this->session->set_flashdata('result', $this->lang->line('msg_set_deleted'));
				}
			}
		}
		else
		{
			// L'utilisateur ne peut pas faire ça
			$this->session->set_flashdata('result', $this->lang->line('error_cant_do'));
		}

		// On renvoit l'utilisateur sur la page des sagas
		redirect('sets', 'refresh');
  }

}

/* End of file sets.php */
/* Location: ./system/application/controllers/sets.php */
