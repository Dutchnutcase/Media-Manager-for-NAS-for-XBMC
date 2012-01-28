<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tvshows_model extends CI_model
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
   * Retourne une série tv dont on précise l'identifiant ou un tableau
   * d'identifiants
   *
   * Permet d'avoir moins de données si $full vaut FALSE
   *
   * @access public
   * @param integer or array
   * @param boolean

   * @return array
   */
  function get($idShow, $for_view = FALSE)
  {
    // Liste et noms des champs à récupérer
    $fields[] = 'tvshow.idShow';
    $fields[] = 'path.idPath';
    $fields[] = 'path.strPath';
    $fields[] = 'tvshow.c00 as title';
    $fields[] = 'tvshow.c01 as overview';
    $fields[] = 'tvshow.c04 as rating';
    $fields[] = 'tvshow.c05 as first_aired';
    $fields[] = 'tvshow.c06';
    $fields[] = 'tvshow.c11';
    $fields[] = 'tvshow.c12';
    $fields[] = 'tvshow.c13 as mpaa';
    $fields[] = 'if(min( episode.c12) = 0, max( episode.c12)+1, max( episode.c12)) as total_seasons';
    $fields[] = 'count( 1 ) as total_episodes';
    $fields = implode(', ', $fields);

    // Est-ce un tableau d'identifiants ?
    if (is_array($idShow))
    {
      $results = $this->{$this->_db_group_name}->select($fields)
                                               ->from('episode')
                                               ->join('tvshowlinkepisode', 'tvshowlinkepisode.idEpisode = episode.idEpisode')
                                               ->join('tvshow', 'tvshow.idShow = tvshowlinkepisode.idShow')
                                               ->join('tvshowlinkpath', 'tvshowlinkpath.idShow = tvshow.idShow')
                                               ->join('path', 'path.idPath = tvshowlinkpath.idPath')
                                               ->group_by('tvshow.idShow')
                                               ->where_in('tvshow.idShow', $idShow)
                                               ->order_by('tvshow.c00', 'asc')
                                               ->get()
                                               ->result();
    }
    else
    {
      $results = $this->{$this->_db_group_name}->select($fields)
                                               ->from('episode')
                                               ->join('tvshowlinkepisode', 'tvshowlinkepisode.idEpisode = episode.idEpisode')
                                               ->join('tvshow', 'tvshow.idShow = tvshowlinkepisode.idShow')
                                               ->join('tvshowlinkpath', 'tvshowlinkpath.idShow = tvshow.idShow')
                                               ->join('path', 'path.idPath = tvshowlinkpath.idPath')
                                               ->group_by('tvshow.idShow')
                                               ->where('tvshow.idShow', $idShow)
                                               ->get()
                                               ->result();
    }

    // Si au moins une série est dans la base de données
    if (isset($results[0]->idShow))
    {
      foreach($results as $result)
      {
        $tvshow = new stdClass();

        // Identifiant
        $tvshow->id = $result->idShow;

        // Chemin et scraper correspondant
        $tvshow->path_id = $result->idPath;
        $tvshow->path = $result->strPath;
        $tvshow->source = $this->_CI->videoinfoscanner->get_source($tvshow->path);

//echo '<pre>'.print_r($tvshow->source, TRUE).'</pre>';
//die();

        // Valeurs actuelle de l'affiche et du fond d'écran
        $tvshow->poster = $this->xbmc_lib->get_tvshow_poster($tvshow);
        $tvshow->backdrop = $this->xbmc_lib->get_tvshow_backdrop($tvshow);

//echo '<pre>'.print_r($tvshow, TRUE).'</pre>';
//die();

				$tvshow->title = $result->title;

				if ($result->first_aired != '')
				{
					$tvshow->year = date('Y', strtotime($result->first_aired));
					$tvshow->year = '<a href="'.site_url('tvshows/year/'.$tvshow->year.'/').'">'.$tvshow->year.'</a>';
					$tvshow->first_aired = date($this->lang->line('media_first_aired_format'), strtotime($result->first_aired));
				}
				else
				{
					$tvshow->year = $this->lang->line('media_no_year');
					$tvshow->first_aired = $this->lang->line('media_no_first_aired');
				}

        // Données complètes ?
        if ($for_view)
        {
          // Nom de la classe du scraper
          $scraper = $tvshow->source->scraper;

          // Chargement de la classe du scraper
          $this->load->library('/scrapers/video/tvshows/'.ucfirst($scraper));

          // Classe pour lister toutes les images que fourni le scraper
          $images = new stdClass();
          $images = $this->$scraper->get_posters($result);
          $images->backdrops = $this->$scraper->get_backdrops($result);
          $tvshow->images = $images;

          if ($result->c12 != '')
          {
            $tvshow->external_link = $this->$scraper->get_external_link($result);
            $tvshow->external_link = '<a href="'.$tvshow->external_link.'" target="_blank" >'.$tvshow->external_link.'</a>';
          }
          else
          {
            $tvshow->external_link = $this->lang->line('media_no_external_link');
          }

          // Données diverses
          if ($result->overview != '')
              $tvshow->overview = $result->overview;
          else
              $tvshow->overview = $this->lang->line('media_no_overview');

          if ($result->rating != '')
              $tvshow->rating = $result->rating;
          else
              $tvshow->rating = $this->lang->line('media_no_rating');

          if ($result->mpaa != '')
              $tvshow->mpaa = $result->mpaa;
          else
              $tvshow->mpaa = $this->lang->line('media_no_mpaa');

          $tvshow->total_seasons = $result->total_seasons;
          $tvshow->total_episodes = $result->total_episodes;

          $tvshow->actors = $this->_CI->actors_model->get_actors_for_tvshow($tvshow->id);

          $tvshow->genres = $this->_CI->genres_model->get_for_tvshow($tvshow->id);
          $tvshow->studios = $this->_CI->studios_model->get_for_tvshow($tvshow->id);

          // Recherche des saisons de cette série TV
          $tvshow_seasons = $this->{$this->_db_group_name}->select("cast(episode.c12 as unsigned) as idSeason, count(1) as total_episodes")
                                                          ->from('episode')
                                                          ->join('tvshowlinkepisode', 'tvshowlinkepisode.idEpisode = episode.idEpisode')
                                                          ->where('tvshowlinkepisode.idShow', $tvshow->id)
                                                          ->group_by('idSeason')
                                                          ->get()
                                                          ->result();

          // Si il y a des saisons
          if (is_array($tvshow_seasons))
          {
            // Si il y au moins 2 saisons, on cherche l'affiche pour toutes les saisons
            if (count($tvshow_seasons) >= 2)
            {
              $season = new stdClass();

              $season->idSeason = -1;
              $season->total_episodes = $tvshow->total_episodes;
              $season->poster = $this->xbmc_lib->get_tvshow_poster($tvshow, -1);
              $seasons[-1] = $season;
            }

            foreach($tvshow_seasons as $tvshow_season)
            {
              $season = new stdClass();

              $season->idSeason = $tvshow_season->idSeason;
              $season->total_episodes = $tvshow_season->total_episodes;
              $season->poster = $this->xbmc_lib->get_tvshow_poster($tvshow, $tvshow_season->idSeason);
              $seasons[$tvshow_season->idSeason] = $season;
            }
          }

          // Informations sur les saisons de cette série tv
          $tvshow->seasons = $seasons;
        }
        else
        {
          $tvshow->genres = $this->_CI->genres_model->get_for_tvshow($tvshow->id, 2);
          $tvshow->studios = $this->_CI->studios_model->get_for_tvshow($tvshow->id, 2);
        }

        // Ajoût de la série dans un tableau pour retour
        $tvshows[] = $tvshow;
      }
    }
    else
    {
      $tvshows = NULL;
    }

//echo '<pre>'.print_r($tvshow, TRUE).'</pre>'; die();

    // On retourne la ou le(s) série(s) trouvée(s) ou NULL
    return $tvshows;
  }

  /**
   * Cherche une série TV dont on précise une partie du titre localisé
   * Retourne un objet représentant la ou les série(s) TV trouvé(s) ou NULL si non trouvé
   *
   * @access public
   * @param string
   * @param integer
   * @param integer
   * @return integer
   */
  function search_by_local_title($local_title, $limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('tvshow.idShow')
																						 ->from('tvshow')
																						 ->like('tvshow.c00', urldecode($local_title))
																						 ->limit($limit, $offset)
																						 ->get()
                                             ->result();

    // Si au moins une série TV est dans la base de données
    if (isset($results[0]->idShow))
    {
      foreach($results as $result)
      {
        $idsTvshow[] = $result->idShow;
      }

      // On récupère enfin les films
      $tvshows = $this->get($idsTvshow);
    }
    else
    {
      $tvshows = NULL;
    }

    // On retourne la ou le(s) série(s) TV trouvée(s) ou NULL
    return $tvshows;
  }

  /**
   * Retourne le nombre total de séries TV contenant une partie d'un titre localisé
   *
   * @access public
   * @param string
   * @return integer
   */
  function count_all_by_local_title($local_title)
  {
    return $this->{$this->_db_group_name}->from('tvshow')
																				 ->like('tvshow.c00', urldecode($local_title))
																				 ->count_all_results();
  }

  /**
   * Retourne 'max' séries tv au hasard
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_random($max = 5)
  {
    $results = $this->{$this->_db_group_name}->select('idShow')
                                             ->from('tvshow')
                                             ->order_by('RAND()')
                                             ->limit($max)
                                             ->get()
                                             ->result();

    // Récupération des identifiants des séries tv
    foreach($results as $result)
    {
      $idsTvshow[] = $result->idShow;
    }

    // On retourne les séries tv trouvées
    return $this->get($idsTvshow);
  }

  /**
   * Retourne 'max' dernières séries tv
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_last($max = 3)
  {
    $results = $this->{$this->_db_group_name}->select('idShow as id')
                                             ->from('tvshow')
                                             ->order_by('idShow DESC')
                                             ->limit($max)
                                             ->get()
                                             ->result();
		// Valeur par défaut
		$ids = array();

    // Récupération des identifiants des séries tv
    foreach($results as $result)
    {
      $ids[] = $result->id;
    }

    // On retourne les séries tv trouvées ou un tableau vide
    if (count($ids) > 0)
				return $this->get($ids, FALSE);
		else
				return $ids;
  }

  /**
   * Retourne toutes les séries pour lesquels la personne dont on précise
   * l'identifiant est acteur
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_all_by_actor($id, $limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('actorlinktvshow.idShow')
                                             ->from('actorlinktvshow')
                                             ->where('actorlinktvshow.idActor', $id)
                                             ->limit($limit, $offset)
                                             ->get()
                                             ->result();

    foreach($results as $result)
    {
      $idsTvshow[] = $result->idShow;
    }

    // Pas de résultat, alors on retourne NULL
    if (!isset($idsTvshow)) return NULL;

    // Un seul résultat ?
    if (count($idsTvshow) == 1)
    {
      return $this->get($idsTvshow[0]);
    }
    else
    {
      return $this->get($idsTvshow);
    }
  }

  /**
   * Compte toutes les séries pour lesquels la personne dont on précise
   * l'identifiant est acteur
   *
   * @access public
   * @param integer
   * @return integer
   */
  function count_all_by_actor($id)
  {
    return $this->{$this->_db_group_name}->from('actorlinktvshow')
                                         ->where('actorlinktvshow.idActor', $id)
                                         ->count_all_results();
  }



  /**
   * Retourne toutes les séries TV dont on précise l'année
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_all_by_year($year, $limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('tvshow.idShow')
                                             ->from('tvshow')
                                             ->like('tvshow.c05', $year)
                                             ->limit($limit, $offset)
                                             ->get()
                                             ->result();

    // Si au moins un film est dans la base de données
    if (isset($results[0]->idShow))
    {
      foreach($results as $result)
      {
        $idsShow[] = $result->idShow;
      }

      // On récupère enfin les films
      $tvshows = $this->get($idsShow);
    }
    else
    {
      $tvshows = NULL;
    }

    // On retourne le(s) film(s) trouvé(s) ou NULL
    return $tvshows;
  }

  /**
   * Retourne le total des séries TV dont on précise l'année
   *
   * @access public
   * @param integer
   * @return integer
   */
  function count_all_by_year($year)
  {
    return $this->{$this->_db_group_name}->from('tvshow')
                                         ->like('tvshow.c05', $year)
                                         ->count_all_results();
  }

  /**
   * Retourne toutes les séries TV dont on précise l'identifiant du genre
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_all_by_genre($id, $limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('genrelinktvshow.idShow as id')
                                             ->from('genrelinktvshow')
                                             ->where('genrelinktvshow.idGenre', $id)
                                             ->limit($limit, $offset)
                                             ->get()
                                             ->result();

    // Si au moins un film est dans la base de données
    if (isset($results[0]->id))
    {
      foreach($results as $result)
      {
        $idsShow[] = $result->id;
      }

      // On récupère enfin les films
      $tvshows = $this->get($idsShow);
    }
    else
    {
      $tvshows = NULL;
    }

    // On retourne le(s) film(s) trouvé(s) ou NULL
    return $tvshows;
  }

  /**
   * Retourne le total des séries TV dont on précise l'identifiant du genre
   *
   * @access public
   * @param integer
   * @return integer
   */
  function count_all_by_genre($id)
  {
    return $this->{$this->_db_group_name}->from('genrelinktvshow')
                                         ->where('genrelinktvshow.idGenre', $id)
                                         ->count_all_results();
  }

  /**
   * Retourne toutes les séries TV dont on précise l'identifiant du studio
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_all_by_studio($id, $limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('studiolinktvshow.idShow as id')
                                             ->from('studiolinktvshow')
                                             ->where('studiolinktvshow.idstudio', $id)
                                             ->limit($limit, $offset)
                                             ->get()
                                             ->result();

    // Si au moins un film est dans la base de données
    if (isset($results[0]->id))
    {
      foreach($results as $result)
      {
        $idsShow[] = $result->id;
      }

      // On récupère enfin les films
      $tvshows = $this->get($idsShow);
    }
    else
    {
      $tvshows = NULL;
    }

    // On retourne le(s) film(s) trouvé(s) ou NULL
    return $tvshows;
  }

  /**
   * Retourne le total des séries TV dont on précise l'identifiant du studio
   *
   * @access public
   * @param integer
   * @return integer
   */
  function count_all_by_studio($id)
  {
    return $this->{$this->_db_group_name}->from('studiolinktvshow')
                                         ->where('studiolinktvshow.idstudio', $id)
                                         ->count_all_results();
  }

  /**
   * Retourne toutes les séries TV
   *
   * @access public
   * @return array
   */
  function get_all($limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('tvshow.idShow as id, tvshow.c00 as title')
                                             ->from('tvshow')
                                             ->limit($limit, $offset)
                                             ->order_by('title', 'asc')
                                             ->get()
                                             ->result();

    // Si au moins une série TV est dans la base de données
    if (isset($results[0]->id))
    {
      // Récupération des identifiants de toutess les séries TV
      foreach($results as $result)
      {
        $idsTvshow[] = $result->id;
      }

      // On récupère enfin les séries TV
      $Tvshows = $this->tvshows_model->get($idsTvshow);
    }
    else
    {
      $Tvshows = NULL;
    }

    // On retourne la ou les série(s) TV trouvée(s) ou NULL
    return $Tvshows;
  }

  /**
   * Retourne le nombre total des séries TV
   *
   * @access public
   * @return integer
   */
  function count_all()
  {
    return $this->{$this->_db_group_name}->from('tvshow')
                                         ->count_all_results();
  }

  /**
   * Télécharge toutes les images d'une série TV si non déjà téléchargées en vue de la sélection
   * de l'affiche, de la bannière et/ou du fond d'écran
   *
   * @access public
   * @param object
   * @return void
   */
  function prepare_to_display($tvshow)
  {
		set_time_limit(120000); //fixe un delai maximum d'execution de 1200 secondes soit 20 minutes.

		// Affiche ou bannière ?
		if ($tvshow->poster->type == 'poster')
		{
			// Pour toutes les affiches
			foreach($tvshow->images->posters as $poster)
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
		else
		{
			// Pour toutes les bannières
			foreach($tvshow->images->banners as $banner)
			{
				// Téléchargement de l'affiche via un fichier temporaire effacé ensuite si miniature absente
				if (!file_exists($banner->filename))
				{
					$temp_filename = $this->xbmc_lib->images_cache_dir.'temp';
					$this->xbmc_lib->download($banner->real_url, $temp_filename);
					$this->xbmc_lib->create_image($temp_filename, $banner->filename, $this->xbmc_lib->banner_size);
					unlink($temp_filename);
					sleep(2); // Attente de 2 secondes pour soulager le serveur
				}
			}
		}

		// Pour tous les fonds d'écran
		foreach($tvshow->images->backdrops as $backdrop)
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

  /**
   * Met à jour les informations d'une série TV dont on précise l'identifiant
   * et les données
   *
   * @access public
   * @param integer
   * @param array
   * @return void
   */
  function update($idShow, $data)
  {
    $this->{$this->_db_group_name}->where('idShow', $idShow)->update('tvshow', $data);
  }

}

/* End of file tvshows_model.php */
/* Location: ./application/models/video/tvshows_model.php */
