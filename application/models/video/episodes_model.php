<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Episodes_model extends CI_model
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
   * Retourne l'identifiant d'un épisode d'une série tv dont on précise :
   * L'identifiant de la série tv
   * Le numéro de la saison :
   *  -1 pour tous les épisodes de la série
   *   0 pour tous les épisodes hors-saison de la série
   * Le numéro de l'épisode si précisé, on recherche réellement 1 seul épisode
   *
   * Retourne 0 si non trouvé
   *
   * @access public
   * @param integer
   * @param integer
   * @param integer
   * @return array
   */
  function get_ids($tvshow_id, $season_number, $episode_number = 0)
  {
    // Uniquement un seul épisode ?
    if ($episode_number != 0)
    {
      $results = $this->{$this->_db_group_name}->select('idEpisode as episode_id')
                                               ->from('episodeview')
                                               ->where('idShow', $tvshow_id)
                                               ->where('c12', $season_number)
                                               ->where('c13', $episode_number)
                                               ->get()
                                               ->result();
    }
    else
    {
      // Tous les épisodes de la série ?
      if ($season_number == -1)
      {
        $results = $this->{$this->_db_group_name}->select('idEpisode as episode_id')
                                                 ->from('episodeview')
                                                 ->where('idShow', $tvshow_id)
                                                 ->get()
                                                 ->result();
      }
      else
      {
        // Tous les épisodes d'une saison d'une série
        $results = $this->{$this->_db_group_name}->select('idEpisode as episode_id')
                                                 ->from('episodeview')
                                                 ->where('idShow', $tvshow_id)
                                                 ->where('c12', $season_number)
                                                 ->get()
                                                 ->result();
      }
    }

    foreach($results as $result)
    {
      $ids[] = $result->episode_id;
    }

    // Pas de résultat, alors on retourne un tableau vide
    if (!isset($ids)) $ids = array();

    // Retourne le(s) identifiant(s) trouvé(s)
    return $ids;
  }

  /**
   * Retourne un épisode d'une série tv dont on précise l'identifiant
   * On précise l'identifiant de l'épisode ou un tableau d'identifiant
   *
   * @access public
   * @param integer
   * @return object
   */
  function get($episode_id)
  {
    // Liste et noms des champs à récupérer
    $fields[] = 'idEpisode';
    $fields[] = 'idFile';
    $fields[] = 'strPath';
    $fields[] = 'strFileName';
    $fields[] = 'lastPlayed';
    $fields[] = 'c00 as local_title';
    $fields[] = 'c01 as overview';
    $fields[] = 'c03 as rating';
    $fields[] = 'c05 as first_aired';
    $fields[] = 'c06 as poster_url';
    $fields[] = 'c09 as runtime';
    $fields[] = 'idShow';
    $fields[] = 'strTitle';
    $fields[] = 'mpaa';
    $fields[] = 'c12 as season_number';
    $fields[] = 'c13 as episode_number';
    $fields = implode(', ', $fields);

    // Est-ce un tableau d'identifiants ?
    if (is_array($episode_id))
    {
      $results = $this->{$this->_db_group_name}->select($fields)
                                               ->from('episodeview')
                                               ->order_by('idShow', 'ASC')
                                               ->order_by('season_number', 'ASC')
                                               ->order_by('episode_number', 'ASC')
                                               ->where_in('idEpisode', $episode_id)
                                               ->get()
                                               ->result();
    }
    else
    {
      $results = $this->{$this->_db_group_name}->select($fields)
                                               ->from('episodeview')
                                               ->where('idEpisode', $episode_id)
                                               ->get()
                                               ->result();
    }

    // Si le ou les épisode(s) est/sont dans la base de données
    if (isset($results[0]->idEpisode))
    {
      foreach($results as $result)
      {
        $episode = new stdClass();

        // Identifiant
        $episode->id = $result->idEpisode;

        // Chemin et scraper correspondant
        $episode->path = $result->strPath;
        $episode->source = $this->_CI->videoinfoscanner->get_source($episode->path);

        // Fichier
        $episode->file_id = $result->idFile;
        $episode->filename = $result->strFileName;

        // Episode vu ou pas
        $episode->seen = !is_null($result->lastPlayed);

        // Données diverses
        $episode->local_title = $result->local_title;

        if ($result->overview != '')
            $episode->overview = $result->overview;
        else
            $episode->overview = $this->lang->line('media_no_overview');

        if ($result->rating != '')
            $episode->rating = $result->rating;
        else
            $episode->rating = $this->lang->line('media_no_rating');

        if ($result->first_aired != '')
        {
          $episode->first_aired = date($this->lang->line('media_first_aired_format'), strtotime($result->first_aired));
        }
        else
        {
          $episode->first_aired = $this->lang->line('media_no_first_aired');
        }

        if ($result->runtime != '')
            $episode->runtime = gmstrftime($this->lang->line('media_runtime_format'), $result->runtime * 60);
        else
            $episode->runtime = $this->lang->line('media_no_runtime');

        $episode->poster_url = $result->poster_url;
        $episode->tvshow_id = $result->idShow;
        $episode->tvshow_name = $result->strTitle;
        $episode->season_number = $result->season_number;
        $episode->episode_number = $result->episode_number;

        $episode->poster = $this->xbmc->get_episode_poster($episode);

        $episode->writers = $this->_CI->actors_model->get_writers_for_episode($episode->id);
        $episode->directors = $this->_CI->actors_model->get_directors_for_episode($episode->id);
        $episode->actors = $this->_CI->actors_model->get_actors_for_episode($episode->id);

        // Ajoût de l'épisode dans un tableau pour retour
        $episodes[] = $episode;
      }
    }
    else
    {
      $episodes = array();
    }

    // On retourne le(s) film(s) trouvé(s) ou NULL
    return $episodes;
  }

  /**
   * Retourne un épisode d'une série tv dont on précise l'identifiant
   * On précise le numéro de la saison
   * -1 permet de compter tous les épisodes de toutes les saisons
   *
   * @access public
   * @param integer
   * @param integer
   * @param integer
   * @param integer
   * @return object
   */
  function get_all_by_season($tvshow_id, $season_id, $limit = NULL, $offset = NULL)
  {
    // Toutes les saisons ?
    if ($season_id == -1)
    {
      $results = $this->{$this->_db_group_name}->select('idEpisode')
                                               ->from('episodeview')
                                               ->where('idShow', $tvshow_id)
                                               ->limit($limit, $offset)
                                               ->get()
                                               ->result();
    }
    else
    {
      $results = $this->{$this->_db_group_name}->select('idEpisode')
                                               ->from('episodeview')
                                               ->where('idShow', $tvshow_id)
                                               ->where('c12', $season_id)
                                               ->limit($limit, $offset)
                                               ->get()
                                               ->result();
    }

    // Si le ou les épisode(s) est/sont dans la base de données
    if (isset($results[0]->idEpisode))
    {
      foreach($results as $result)
      {
        $episode_ids[] = $result->idEpisode;
      }
    }
    else
    {
      $episodes = array();
    }

    // On retourne le(s) film(s) trouvé(s) ou NULL
    return $this->get($episode_ids);
  }

  /**
   * Retourne le nombre total d'épisodes d'une saison d'une série TV
   * -1 perrmet de compter tous les épisodes de toutes les saisons
   *
   * @access public
   * @param integer
   * @param integer
   * @return integer
   */
  function count_all_by_season($tvshow_id, $season_id)
  {
    if ($season_id == -1)
    {
      $count = $this->{$this->_db_group_name}->from('episodeview')
                                             ->where('idShow', $tvshow_id)
                                             ->count_all_results();
    }
    else
    {
      $count = $this->{$this->_db_group_name}->from('episodeview')
                                             ->where('idShow', $tvshow_id)
                                             ->where('c12', $season_id)
                                             ->count_all_results();
    }

    return $count;
  }

  /**
   * Retourne tous les épisodes pour lesquels la personne dont on précise
   * l'identifiant est scénariste
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_all_by_writer($id, $limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('writerlinkepisode.idEpisode')
                                             ->from('writerlinkepisode')
                                             ->where('writerlinkepisode.idWriter', $id)
                                             ->limit($limit, $offset)
                                             ->get()
                                             ->result();

    foreach($results as $result)
    {
      $idsEpisode[] = $result->idEpisode;
    }

    // Pas de résultat, alors on retourne NULL
    if (!isset($idsEpisode)) return NULL;

    return $this->get($idsEpisode);
  }

  /**
   * Compte tous les épisodes pour lesquels la personne dont on précise
   * l'identifiant est scénariste
   *
   * @access public
   * @param integer
   * @return integer
   */
  function count_all_by_writer($id)
  {
    return $this->{$this->_db_group_name}->from('writerlinkepisode')
                                         ->where('writerlinkepisode.idWriter', $id)
                                         ->count_all_results();
  }

  /**
   * Retourne tous les épisodes pour lesquels la personne dont on précise
   * l'identifiant est réalisateur
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_all_by_director($id, $limit = NULL, $offset = NULL)
  {

    $results = $this->{$this->_db_group_name}->select('directorlinkepisode.idEpisode')
                                             ->from('directorlinkepisode')
                                             ->where('directorlinkepisode.idDirector', $id)
                                             ->limit($limit, $offset)
                                             ->get()
                                             ->result();

    foreach($results as $result)
    {
      $idsEpisode[] = $result->idEpisode;
    }

    // Pas de résultat, alors on retourne NULL
    if (!isset($idsEpisode)) return NULL;

    // Un seul résultat ?
    if (count($idsEpisode) == 1)
    {
      return $this->get($idsEpisode[0]);
    }
    else
    {
      return $this->get($idsEpisode);
    }
  }

  /**
   * Compte tous les épisodes pour lesquels la personne dont on précise
   * l'identifiant est réalisateur
   *
   * @access public
   * @param integer
   * @return integer
   */
  function count_all_by_director($id)
  {
    return $this->{$this->_db_group_name}->from('directorlinkepisode')
                                         ->where('directorlinkepisode.idDirector', $id)
                                         ->count_all_results();
  }
}

/* End of file episodes_model.php */
/* Location: ./application/models/video/episodes_model.php */
