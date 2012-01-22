<?php

class Movies extends CI_Controller
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

  /**
   * Affiche la liste de tous les films sans critère de sélection
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
    $base_url = site_url('movies/page');
    $tpl['title'] = $this->lang->line('list_movies');

    // Récupération des films
    $tpl['movies'] = $this->movies_model->get_all($per_page, intval($this->uri->segment(3)));

    // Total des films pour la pagination
    $total = $this->movies_model->count_all();

    // On charge la vue qui contient le corps de la page
    $tpl['file'] = 'video/movies/index';

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
   * Affiche les informations d'un film
   */
  public function view()
  {
    // Lecture des informations complètes du film
    $movies = $this->movies_model->get(intval($this->uri->segments[2]), TRUE);

    // La fonction précédente retourne un tableau même d'un seul élément
    $movie = $movies[0];

//echo '<pre>'.print_r($movie, TRUE).'</pre>'; die();

		// Si l'utilisateur peut changer les images, on charge les miniatures en les créant le cas échéant
		if ($this->session->userdata('can_change_images'))
		{
			set_time_limit(60000); //fixe un delai maximum d'execution de 600 secondes soit 10 minutes.

			// Pour toutes les affiches
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

			// Pour tous les fonds d'écran
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

//echo '<pre>'.print_r($movie, TRUE).'</pre>'; die();

    $tpl['title'] = $movie->local_title;
    $tpl['movie'] = $movie;

    // On charge la vue qui contient le corps de la page
    $tpl['file'] = 'video/movies/view';

    // On charge la page dans le template
    $this->load->view('includes/template', $tpl);
  }

  /**
   * Contrôle présence $_POST et conversion vers $_GET
   */
	public function pre_search()
	{
		// Si pas de titre alors on affiche la liste des films
		if ($this->input->post('query') == '') redirect('movies');

		redirect('movies/search/'.$this->input->post('query'));
	}

  /**
   * Recherche d'un film
   */
  public function search()
  {
		// Titre recherché dans le dernier segment de l'url
		$query = $this->uri->segments[3];

    $per_page = 6;

    // Adresse de base pour la pagination
    $base_url = site_url('movies/search/'.$query.'/page');
    $tpl['title'] = sprintf($this->lang->line('list_search_movies'), urldecode($query));

    $tpl['movies'] = $this->movies_model->search_by_local_title($query, $per_page, intval($this->uri->segment(5)));

    // Total des films pour la pagination
    $total = $this->movies_model->count_all_by_local_title($query);

		// Si un seul film a été trouvé, on affche sa page
		if ($total == 1)
		{
			redirect('movies/'.$tpl['movies'][0]->id);
		}

    // On charge la vue qui contient le corps de la page
		if ($total == 0)
		{
			$tpl['media'] = 'movies';
			$tpl['include'] = '_no_results';
		}
		else
		{
			$tpl['file'] = 'video/movies/index';
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

  // Film choisi pour mise à jour
  public function ajax_update()
  {
    // Identifiant du film
    $id = intval($this->uri->segments[3]);

    // Récupération des informations de 'base' du film
    $movies = $this->movies_model->get($id, FALSE);

    // On n'a qu'un seul film à traiter !
    $movie = $movies[0];

    // Nom de la classe du scraper
    $scraper = $movie->scraper->class;

    // Chargement de la classe du scraper
    $this->load->library('/scrapers/video/movies/'.ucfirst($movie->scraper->class));

    // Identifiant du film choisi pour mise à jour
    $remote_movie_id = intval($this->input->post('movie_id'));

    // Suppression des données actuelles et peut-être erronées du film
    $this->actors_model->remove_directors_for_movie($id);
    $this->actors_model->remove_writers_for_movie($id);
    $this->actors_model->remove_actors_for_movie($id);
    $this->genres_model->remove_for_movie($id);
    $this->studios_model->remove_for_movie($id);
    $this->countries_model->remove_for_movie($id);

    // Récupère les informations d'un film dans un tableau et mise à jour
    $data = $this->$scraper->get($remote_movie_id, $id);
    $this->movies_model->update($id, $data);

    // Téléchargement et sauvegarde de l'affiche du film précédemment choisi
    $this->xbmc_lib->download($this->$scraper->poster, $movie->poster->filename);

    // Téléchargement et sauvegarde du fond d'écran du film précédemment choisi
    $this->xbmc_lib->download($this->$scraper->backdrop, $movie->backdrop->filename);

  }

  /**
   * Demande à changer les informations d'un film via ajax
   */
  public function ajax_refresh()
  {
    // Appel via ajax ?
    if (IS_AJAX)
    {
      // Identifiant du film
      $id = intval($this->uri->segments[3]);

      // Récupération des informations de 'base' du film
      $movies = $this->movies_model->get($id, FALSE);

      // On n'a qu'un seul film à traiter !
      $movie = $movies[0];

			// Nom de la classe du scraper
			$scraper = $movie->source->scraper;

			// Chargement de la classe du scraper
			$this->load->library('/scrapers/'.$movie->source->media_db.'/'.$movie->source->content.'/'.ucfirst($movie->source->scraper));

      // Titre du film supposé à partir du nom du fichier
      $title = pathinfo($movie->filename, PATHINFO_FILENAME);

      $results = $this->$scraper->search($title);

      $title = count($results);
      $title .= ($title > 1) ? ' '.$this->lang->line('media_movies_found') : ' '.$this->lang->line('media_movie_found');

      // Liste des films trouvés + nombre de résultats
      $json = array('title' => $title, 'results' => $results);

      // Entête pour générer la réponse au format json
      header('Expires: ' . gmdate('r', 0));
      header('Content-type: application/json');
      echo json_encode($json);
      die();
    }
  }

  /**
   * Change les informations d'un film via ajax
   */
  public function ajax_change_data()
  {
    // Appel via ajax ?
    if (IS_AJAX)
    {
      // Champ concerné par la modification
      $field = ($_POST['field'] == 'tagline') ? 'tagline' : 'overview';

      // Nouvelle valeur du champ
      if (get_magic_quotes_gpc() === TRUE)
      {
        $_POST['value'] = stripslashes($_POST['value']);
      }
      $_POST['value'] = mysql_real_escape_string($_POST['value']);

      if ($field == 'overview')
      {
				$this->movies_model->set_overview((int) $this->uri->segments[3], $_POST['value']);
      }

      if ($field == 'tagline')
      {
				$this->movies_model->set_tagline((int) $this->uri->segments[3], $_POST['value']);
      }

			header('Content-type: text/html; charset='.$this->config->item('charset'));
      echo $_POST['value'];
      die();
    }
  }

  /**
   * Attribue un film à une saga via ajax
   */
  public function ajax_add_to_set()
  {
    // Appel via ajax ?
    if (IS_AJAX)
    {
      // Identifiant du film
      $id = (int) $this->uri->segments[3];

      // Identifiant de la saga attribuée à ce film
      $set_id = intval($this->input->post('set_id'));

      // Ajout du film dans la saga
      $this->sets_model->add_movie($id, $set_id);

      $set_name = $this->sets_model->get_name($set_id);

      $link = '<a href="'.site_url('sets/'.$set_id.'/').'">'.$set_name.'</a>';
      $movie_in_set = sprintf($this->lang->line('media_in_set'), $link);

      $json = array ('message' => $this->lang->line('media_movie_added'),
                     'movie_in_set' => $movie_in_set);

      // Entête pour générer la réponse au format json
      header('Expires: ' . gmdate('r', 0));
      header('Content-type: application/json');
      echo json_encode($json);
      die();
    }
  }

  /**
   * Gère la navigation parmi les films en fonction d'un des 4 critères suivants
   * genre / studio / pays / année
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
        $base_url = site_url('movies/genre/'.$id.'/');
        $tpl['title'] = sprintf($this->lang->line('list_movies_genre'), $name);

        // Récupération des films correspondant au critère
        $tpl['movies'] = $this->movies_model->get_all_by_genre($id, $per_page, (int) $this->uri->segment(4));

        // Total des films pour la pagination
        $total = $this->movies_model->count_all_by_genre($id);
        break;

      case 'studio':
        $id = $this->uri->segments[3];

        // Nom du studio
        $name = $this->studios_model->get_by_id($id);

        // Adresse de base pour la pagination
        $base_url = site_url('movies/studio/'.$id.'/');
        $tpl['title'] = sprintf($this->lang->line('list_movies_studio'), $name);

        // Récupération des films correspondant au critère
        $tpl['movies'] = $this->movies_model->get_all_by_studio($id, $per_page, (int) $this->uri->segment(4));

        // Total des films pour la pagination
        $total = $this->movies_model->count_all_by_studio($id);
        break;

      case 'country':
        $id = $this->uri->segments[3];

        // Nom du pays
        $name = $this->countries_model->get_by_id($id);

        // Adresse de base pour la pagination
        $base_url = site_url('movies/country/'.$id.'/');
        $tpl['title'] = sprintf($this->lang->line('list_movies_country'), $name);

        // Récupération des films correspondant au critère
        $tpl['movies'] = $this->movies_model->get_all_by_country($id, $per_page, (int) $this->uri->segment(4));

        // Total des films pour la pagination
        $total = $this->movies_model->count_all_by_country($id);
        break;

      case 'year':
        $year = $this->uri->segments[3];

        // Adresse de base pour la pagination
        $base_url = site_url('movies/year/'.$year.'/');
        $tpl['title'] = sprintf($this->lang->line('list_movies_year'), $year);

        // Récupération des films correspondant au critère
        $tpl['movies'] = $this->movies_model->get_all_by_year($year, $per_page, (int) $this->uri->segment(4));

        // Total des films pour la pagination
        $total = $this->movies_model->count_all_by_year($year);
        break;
    }

    // On charge la vue qui contient le corps de la page
    $tpl['file'] = 'video/movies/index';

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

/* End of file movies.php */
/* Location: ./system/application/controllers/movies.php */
