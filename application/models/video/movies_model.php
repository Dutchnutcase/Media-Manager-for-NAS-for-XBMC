<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Movies_model extends CI_model
{
  // Le modèle utilise la base de données 'xbmc_video'
  private $_db_group_name = 'video';
  private $_CI;

  function __construct()
  {
    parent::__construct();

    $this->_CI =& get_instance();

    // Accès à la base de données 'xbmc_video'
    $this->{$this->_db_group_name} = $this->load->database($this->_db_group_name, TRUE);
  }

  /**
   * Ajoute un film dont on précise les données
   *
   * @access public
   * @param array
   * @return integer
   */
  function add($data)
  {
    $this->{$this->_db_group_name}->insert('movie', $data);

    // Identifiant du film ajouté
    return $this->{$this->_db_group_name}->insert_id();
  }

  /**
   * Retourne un film dont on précise l'identifiant ou un tableau d'identifiants
   *
   * Permet d'avoir plus de données si $for_view vaut TRUE
   *
   * @access public
   * @param integer or array
   * @param boolean
   * @return array
   */
  function get($idMovie, $for_view = FALSE)
  {
    // Liste et noms des champs à récupérer
    $fields[] = 'movie.*';
    $fields[] = 'files.idFile';
    $fields[] = 'files.strFilename';
    $fields[] = 'files.lastPlayed';
    $fields[] = 'path.idPath';
    $fields[] = 'path.strPath';
    $fields[] = 'path.strScraper as strScraper';
    $fields = implode(', ', $fields);

    // Est-ce un tableau d'identifiants ?
    if (is_array($idMovie))
    {
      $results = $this->{$this->_db_group_name}->select($fields)
                                               ->from('movie')
                                               ->join('files', 'files.idFile = movie.idFile')
                                               ->join('path', 'path.idPath = files.idPath')
                                               ->order_by('movie.c10', 'ASC')
                                               ->where_in('movie.idMovie', $idMovie)
                                               ->order_by('movie.c00', 'asc')
                                               ->get()
                                               ->result();
    }
    else
    {
      $results = $this->{$this->_db_group_name}->select($fields)
                                               ->from('movie')
                                               ->join('files', 'files.idFile = movie.idFile')
                                               ->join('path', 'path.idPath = files.idPath')
                                               ->where('movie.idMovie', $idMovie)
                                               ->get()
                                               ->result();
    }

    // Si au moins un film est dans la base de données
    if (count($results) > 0)
    {
      foreach($results as $result)
      {
        $movie = new stdClass();

        // Identifiant
        $movie->id = $result->idMovie;

        // Chemin et scraper correspondant
        $movie->path_id = $result->idPath;
        $movie->path = $result->strPath;
        $movie->source = $this->_CI->videoinfoscanner->get_source($movie->path);

        // Fichier
        $movie->file_id = $result->idFile;
        $movie->filename = $result->strFilename;

        $movie->poster = $this->xbmc->get_movie_poster($movie);
        $movie->backdrop = $this->xbmc->get_movie_backdrop($movie);

        // Données diverses avec mise en forme
        $movie->local_title = $result->c00;

        if ($result->c16 != '')
            $movie->original_title = $result->c16;
        else
            $movie->original_title = $this->lang->line('media_original_title');

        if ($result->c07 != '')
            $movie->year = '<a href="'.site_url('movies/year/'.$result->c07.'/').'">'.$result->c07.'</a>';
        else
            $movie->year = $this->lang->line('media_no_year');

        if ($result->c11 != '')
            $movie->runtime = gmstrftime($this->lang->line('media_runtime_format'), $result->c11 * 60);
        else
            $movie->runtime = $this->lang->line('media_no_runtime');

        // Consulation de la page détaillée d'un film ?
        if ($for_view)
        {
          // Nom de la classe du scraper
          $scraper = $movie->source->scraper;

          // Chargement de la classe du scraper
          $this->load->library('/scrapers/'.$movie->source->media_db.'/'.$movie->source->content.'/'.ucfirst($movie->source->scraper));

          // Classe pour lister toutes les images que fourni le scraper
          $images = new stdClass();
          $images = $this->$scraper->get_posters($result);
          $images->backdrops = $this->$scraper->get_backdrops($result);
          $movie->images = $images;

          if ($result->c09 != '')
          {
            $movie->external_link = $this->$scraper->get_external_link($result);
            $movie->external_link = '<a href="'.$movie->external_link.'" target="_blank" >'.$movie->external_link.'</a>';
          }
          else
          {
            $movie->external_link = $this->lang->line('media_no_external_link');
          }

          // Film vu ou pas
          $movie->seen = !is_null($result->lastPlayed);

          if ($result->c03 != '')
              $movie->tagline = $result->c03;
          else
              $movie->tagline = $this->lang->line('media_no_tagline');

          if ($result->c01 != '')
              $movie->overview = $result->c01;
          else
              $movie->overview = $this->lang->line('media_no_overview');

          if ($result->c05 != '')
              $movie->rating = $result->c05;
          else
              $movie->rating = $this->lang->line('media_no_rating');

          if ($result->c04 != '')
              $movie->votes = $result->c04;
          else
              $movie->votes = $this->lang->line('media_no_vote');

          if ($result->c12 != '')
              $movie->mpaa = $result->c12;
          else
              $movie->mpaa = $this->lang->line('media_no_mpaa');

          // Nom de la saga si présent
          $sets = $this->{$this->_db_group_name}->select('*')
                                                ->from('movie')
                                                ->join('setlinkmovie', 'setlinkmovie.idMovie = movie.idMovie')
                                                ->join('sets', 'sets.idSet = setlinkmovie.idSet')
                                                ->where('movie.idMovie', $result->idMovie)
                                                ->get()
                                                ->result();

          // Le film fait partie d'une saga
          if (isset($sets[0]->strSet))
          {
            $movie->set_id = $sets[0]->idSet;
            $movie->set_name = $sets[0]->strSet;
            $movie->set_order = $result->c10;
          }
          else
            $movie->set_id = 0;

          $movie->writers = $this->_CI->actors_model->get_writers_for_movie($movie->id);
          $movie->directors = $this->_CI->actors_model->get_directors_for_movie($movie->id);
          $movie->actors = $this->_CI->actors_model->get_actors_for_movie($movie->id);
          $movie->genres = $this->_CI->genres_model->get_for_movie($movie->id);
          $movie->studios = $this->_CI->studios_model->get_for_movie($movie->id);
          $movie->countries = $this->_CI->countries_model->get_for_movie($movie->id);
        }
        else
        {
          $movie->writers = $this->_CI->actors_model->get_writers_for_movie($movie->id, 1);
          $movie->directors = $this->_CI->actors_model->get_directors_for_movie($movie->id, 1);
          $movie->actors = $this->_CI->actors_model->get_actors_for_movie($movie->id, 3);
          $movie->genres = $this->_CI->genres_model->get_for_movie($movie->id, 2);
          $movie->studios = $this->_CI->studios_model->get_for_movie($movie->id, 2);
          $movie->countries = $this->_CI->countries_model->get_for_movie($movie->id, 2);
        }

        // Ajoût du film dans un tableau pour retour
        $movies[] = $movie;
      }
    }
    else
    {
      $movies = NULL;
    }

    // On retourne le(s) film(s) trouvé(s) ou NULL
    return $movies;
  }

  /**
   * Retourne quelques informations d'un film dont on précise l'identifiant
   *
   * Permet d'avoir le titre localisé, l'affiche, le fond d'écran
   * 
   * Si $can_change_images vaut TRUE permet d'avoir aussi toutes les affiaches et tous les fonds d'écran
   *
   * @access public
   * @param array
   * @param boolean
   * @return array
   */
  function get_for_set($idMovie, $can_change_images = FALSE)
  {
    // Liste et noms des champs à récupérer
    $fields[] = 'movie.idMovie';
    $fields[] = 'movie.c00';
    $fields[] = 'movie.c08';
    $fields[] = 'movie.c10';
    $fields[] = 'movie.c20';
    $fields[] = 'files.idFile';
    $fields[] = 'files.strFilename';
    $fields[] = 'files.lastPlayed';
    $fields[] = 'path.idPath';
    $fields[] = 'path.strPath';
    $fields[] = 'path.strScraper as strScraper';
    $fields = implode(', ', $fields);

		$results = $this->{$this->_db_group_name}->select($fields)
																						 ->from('movie')
																						 ->join('files', 'files.idFile = movie.idFile')
																						 ->join('path', 'path.idPath = files.idPath')
																						 ->order_by('movie.c10', 'ASC')
																						 ->where_in('movie.idMovie', $idMovie)
																						 ->order_by('movie.c00', 'asc')
																						 ->get()
																						 ->result();

		// Pour tous les films de la saga
		foreach($results as $result)
		{
			$movie = new stdClass();

			// Identifiant
			$movie->id = $result->idMovie;
			$movie->local_title = $result->c00;
			$movie->set_order = $result->c10;

			// Chemin et scraper correspondant
			$movie->path_id = $result->idPath;
			$movie->path = $result->strPath;
			$movie->source = $this->_CI->videoinfoscanner->get_source($movie->path);

			// Fichier
			$movie->file_id = $result->idFile;
			$movie->filename = $result->strFilename;

			$movie->poster = $this->xbmc->get_movie_poster($movie);
			$movie->backdrop = $this->xbmc->get_movie_backdrop($movie);

			// L'utilisateur peut-il changer les images ?
			if ($can_change_images)
			{
				// Nom de la classe du scraper
				$scraper = $movie->source->scraper;

				// Chargement de la classe du scraper
				$this->load->library('/scrapers/video/movies/'.ucfirst($movie->source->scraper));

				// Classe pour lister toutes les images que fourni le scraper
				$images = new stdClass();
				$images = $this->$scraper->get_posters($result);
				$images->backdrops = $this->$scraper->get_backdrops($result);
				$movie->images = $images;
			}

			// Suppression de données inutiles
			unset($movie->path_id);
			unset($movie->path);
			unset($movie->source);
			unset($movie->file_id);
			unset($movie->filename);
			
			$movies[] = $movie;
		}
		
    // On retourne les films trouvés
    return $movies;
  }

  /**
   * Met à jour les informations d'un film dont on précise l'identifiant
   * et les données
   *
   * @access public
   * @param integer
   * @param array
   * @return void
   */
  function update($idMovie, $data)
  {
    $this->{$this->_db_group_name}->where('idMovie', $idMovie)->update('movie', $data);
  }

  /**
   * Fixe la phrase d'accroche d'un film dont on précise l'idenfiant
   *
   * @access public
   * @param integer
   * @param string
   * @return void
   */
  function set_tagline($idMovie, $value)
  {
    $data = array('c03' => $value);
    $this->update($idMovie, $data);
  }

  /**
   * Fixe le résumé d'un film dont on précise l'idenfiant
   *
   * @access public
   * @param integer
   * @param string
   * @return void
   */
  function set_overview($idMovie, $value)
  {
    $data = array('c01' => $value);
    $this->update($idMovie, $data);
  }

  /**
   * Fixe l'ordre d'un film dans une saga de films
   *
   * @access public
   * @param integer
   * @param integer
   * @return void
   */
  function set_order_in_set($idMovie, $value)
  {
    $data = array('c10' => $value);
    $this->update($idMovie, $data);
  }

  /**
   * Retourne tous les films pour lesquels la personne dont on précise
   * l'identifiant est scénariste
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_all_by_writer($id, $limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('writerlinkmovie.idMovie')
                                             ->from('writerlinkmovie')
                                             ->where('writerlinkmovie.idWriter', $id)
                                             ->limit($limit, $offset)
                                             ->get()
                                             ->result();

    foreach($results as $result)
    {
      $idsMovie[] = $result->idMovie;
    }

    // Pas de résultat, alors on retourne NULL
    if (!isset($idsMovie)) return NULL;

    return $this->get($idsMovie);
  }

  /**
   * Compte tous les films pour lesquels la personne dont on précise
   * l'identifiant est scénariste
   *
   * @access public
   * @param integer
   * @return integer
   */
  function count_all_by_writer($id)
  {
    return $this->{$this->_db_group_name}->from('writerlinkmovie')
                                         ->where('writerlinkmovie.idWriter', $id)
                                         ->count_all_results();
  }

  /**
   * Retourne tous les films pour lesquels la personne dont on précise
   * l'identifiant est réalisateur
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_all_by_director($id, $limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('directorlinkmovie.idMovie')
                                             ->from('directorlinkmovie')
                                             ->where('directorlinkmovie.idDirector', $id)
                                             ->limit($limit, $offset)
                                             ->get()
                                             ->result();
    foreach($results as $result)
    {
      $idsMovie[] = $result->idMovie;
    }

    // Pas de résultat, alors on retourne NULL
    if (!isset($idsMovie)) return NULL;

    // Un seul résultat ?
    if (count($idsMovie) == 1)
    {
      return $this->get($idsMovie[0]);
    }
    else
    {
      return $this->get($idsMovie);
    }
  }

  /**
   * Compte tous les films pour lesquels la personne dont on précise
   * l'identifiant est réalisateur
   *
   * @access public
   * @param integer
   * @return integer
   */
  function count_all_by_director($id)
  {
    return $this->{$this->_db_group_name}->from('directorlinkmovie')
                                         ->where('directorlinkmovie.idDirector', $id)
                                         ->count_all_results();
  }

  /**
   * Retourne tous les films pour lesquels la personne dont on précise
   * l'identifiant est acteur
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_all_by_actor($id, $limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('actorlinkmovie.idMovie')
                                             ->from('actorlinkmovie')
                                             ->where('actorlinkmovie.idActor', $id)
                                             ->limit($limit, $offset)
                                             ->get()
                                             ->result();

    foreach($results as $result)
    {
      $idsMovie[] = $result->idMovie;
    }

    // Pas de résultat, alors on retourne NULL
    if (!isset($idsMovie)) return NULL;

    // Un seul résultat ?
    if (count($idsMovie) == 1)
    {
      return $this->get($idsMovie[0]);
    }
    else
    {
      return $this->get($idsMovie);
    }
  }

  /**
   * Compte tous les films pour lesquels la personne dont on précise
   * l'identifiant est acteur
   *
   * @access public
   * @param integer
   * @return integer
   */
  function count_all_by_actor($id)
  {
    return $this->{$this->_db_group_name}->from('actorlinkmovie')
                                         ->where('actorlinkmovie.idActor', $id)
                                         ->count_all_results();
  }

  /**
   * Retourne tous les films dont on précise l'année
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_all_by_year($year, $limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('movie.idMovie')
                                             ->from('movie')
                                             ->where('movie.c07', $year)
                                             ->limit($limit, $offset)
                                             ->get()
                                             ->result();

    // Si au moins un film est dans la base de données
    if (isset($results[0]->idMovie))
    {
      foreach($results as $result)
      {
        $idsMovie[] = $result->idMovie;
      }

      // On récupère enfin les films
      $movies = $this->get($idsMovie);
    }
    else
    {
      $movies = NULL;
    }

    // On retourne le(s) film(s) trouvé(s) ou NULL
    return $movies;
  }

  /**
   * Retourne le total des films dont on précise l'année
   *
   * @access public
   * @param integer
   * @return integer
   */
  function count_all_by_year($year)
  {
    return $this->{$this->_db_group_name}->from('movie')
                                         ->where('movie.c07', $year)
                                         ->count_all_results();
  }

  /**
   * Cherche un film dont on précise une partie du titre localisé
   * Retourne un objet représentant le ou les films(s) trouvé(s) ou NULL si non trouvé
   *
   * @access public
   * @param string
   * @param integer
   * @param integer
   * @return integer
   */
  function search_by_local_title($local_title, $limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('movie.idMovie')
																						 ->from('movie')
																						 ->like('movie.c00', urldecode($local_title))
																						 ->limit($limit, $offset)
																						 ->get()
                                             ->result();

    // Si au moins un film est dans la base de données
    if (isset($results[0]->idMovie))
    {
      foreach($results as $result)
      {
        $idsMovie[] = $result->idMovie;
      }

      // On récupère enfin les films
      $movies = $this->get($idsMovie);
    }
    else
    {
      $movies = NULL;
    }

    // On retourne le(s) film(s) trouvé(s) ou NULL
    return $movies;
  }

  /**
   * Retourne le nombre total de films contenant une partie d'un titre localisé
   *
   * @access public
   * @param string
   * @return integer
   */
  function count_all_by_local_title($local_title)
  {
    return $this->{$this->_db_group_name}->from('movie')
																				 ->like('movie.c00', urldecode($local_title))
																				 ->count_all_results();
  }

  /**
   * Retourne tous les films dont on précise l'identifiant du genre
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_all_by_genre($id, $limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('genrelinkmovie.idMovie as id')
                                             ->from('genrelinkmovie')
                                             ->where('genrelinkmovie.idGenre', $id)
                                             ->limit($limit, $offset)
                                             ->get()
                                             ->result();

    // Si au moins un film est dans la base de données
    if (isset($results[0]->id))
    {
      foreach($results as $result)
      {
        $idsMovie[] = $result->id;
      }

      // On récupère enfin les films
      $movies = $this->get($idsMovie);
    }
    else
    {
      $movies = NULL;
    }

    // On retourne le(s) film(s) trouvé(s) ou NULL
    return $movies;
  }

  /**
   * Retourne le total des films dont on précise l'identifiant du genre
   *
   * @access public
   * @param integer
   * @return integer
   */
  function count_all_by_genre($id)
  {
    return $this->{$this->_db_group_name}->from('genrelinkmovie')
                                         ->where('genrelinkmovie.idGenre', $id)
                                         ->count_all_results();
  }

  /**
   * Retourne tous les films dont on précise l'identifiant du studio
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_all_by_studio($id, $limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('studiolinkmovie.idMovie as id')
                                             ->from('studiolinkmovie')
                                             ->where('studiolinkmovie.idstudio', $id)
                                             ->limit($limit, $offset)
                                             ->get()
                                             ->result();

    // Si au moins un film est dans la base de données
    if (isset($results[0]->id))
    {
      foreach($results as $result)
      {
        $idsMovie[] = $result->id;
      }

      // On récupère enfin les films
      $movies = $this->get($idsMovie);
    }
    else
    {
      $movies = NULL;
    }

    // On retourne le(s) film(s) trouvé(s) ou NULL
    return $movies;
  }

  /**
   * Retourne le total des films dont on précise l'identifiant du studio
   *
   * @access public
   * @param integer
   * @return integer
   */
  function count_all_by_studio($id)
  {
    return $this->{$this->_db_group_name}->from('studiolinkmovie')
                                         ->where('studiolinkmovie.idstudio', $id)
                                         ->count_all_results();
  }

  /**
   * Retourne tous les films dont on précise l'identifiant du pays
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_all_by_country($id, $limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('countrylinkmovie.idMovie as id')
                                             ->from('countrylinkmovie')
                                             ->where('countrylinkmovie.idcountry', $id)
                                             ->limit($limit, $offset)
                                             ->get()
                                             ->result();

    // Si au moins un film est dans la base de données
    if (isset($results[0]->id))
    {
      foreach($results as $result)
      {
        $idsMovie[] = $result->id;
      }

      // On récupère enfin les films
      $movies = $this->get($idsMovie);
    }
    else
    {
      $movies = NULL;
    }

    // On retourne le(s) film(s) trouvé(s) ou NULL
    return $movies;
  }

  /**
   * Retourne le total des films dont on précise l'identifiant du pays
   *
   * @access public
   * @param integer
   * @return integer
   */
  function count_all_by_country($id)
  {
    return $this->{$this->_db_group_name}->from('countrylinkmovie')
                                         ->where('countrylinkmovie.idcountry', $id)
                                         ->count_all_results();
  }

  /**
   * Retourne les identifiants de tous les films figurant dans une saga
   *
   * @access private
   * @return array
   */
  private function _get_all_id_in_set()
  {
    $results = $this->{$this->_db_group_name}->select('movie.idMovie as id')
                                             ->from('movie')
                                             ->join('setlinkmovie', 'setlinkmovie.idMovie = movie.idMovie')
                                             ->get()
                                             ->result();

    // Si au moins un film est dans la saga
    if (isset($results[0]->id))
    {
      foreach($results as $result)
      {
        $idsMovie[] = $result->id;
      }
    }
    else
    {
      $idsMovie = NULL;
    }

    // On retourne l'identifiant du/des film(s) trouvé(s) ou NULL
    return $idsMovie;
  }

  /**
   * Retourne tous les films
   *
   * @access public
   * @return array
   */
  function get_all($limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('movie.idMovie as id, movie.c00 as title')
                                             ->from('movie')
                                             ->limit($limit, $offset)
                                             ->order_by('title', 'asc')
                                             ->get()
                                             ->result();

    // Si au moins un film est dans la base de données
    if (isset($results[0]->id))
    {
      // Récupération des identifiants de tous les films
      foreach($results as $result)
      {
        $idsMovie[] = $result->id;
      }

      // On récupère enfin les films
      $movies = $this->movies_model->get($idsMovie);
    }
    else
    {
      $movies = NULL;
    }

    // On retourne le(s) film(s) trouvé(s) ou NULL
    return $movies;
  }

  /**
   * Retourne le nombre total des films
   *
   * @access public
   * @return integer
   */
  function count_all()
  {
    return $this->{$this->_db_group_name}->from('movie')
                                         ->count_all_results();
  }

  /**
   * Retourne 'max' derniers films
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_last($max = 3)
  {
    $results = $this->{$this->_db_group_name}->select('idMovie as id')
                                             ->from('movie')
                                             ->order_by('idMovie DESC')
                                             ->limit($max)
                                             ->get()
                                             ->result();

    // Récupération des identifiants des films
    foreach($results as $result)
    {
      $idsMovie[] = $result->id;
    }

    // On retourne les films trouvés
    return $this->get($idsMovie, FALSE);
  }

}

/* End of file movies_model.php */
/* Location: ./application/models/video/movies_model.php */
