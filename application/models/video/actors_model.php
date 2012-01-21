<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Actors_model extends CI_model
{
  // Le modèle utilise la base de données 'xbmc_video'
  private $_db_group_name = 'video';

  // Si le rôle n'est pas précisé
  private $_no_role = 'Non précisé';

  function __construct()
  {
    parent::__construct();

    // Accès à la base de données 'xbmc_video'
    $this->{$this->_db_group_name} = $this->load->database($this->_db_group_name, TRUE);
  }

  /**
   * Ajoute un acteur dont on précise le nom
   *
   * @access public
   * @param string
   * @return integer
   */
  function add($strActor)
  {
    $data = array('strActor' => $strActor);
    $this->{$this->_db_group_name}->insert('actors', $data);

    // Identifiant du actors ajouté
    return $this->{$this->_db_group_name}->insert_id();
  }

  /**
   * Récupère toutes les séries tv pour lesquels la personne dont on précise
   * l'identifiant est réalisateur
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_tvshows_directed($idDirector)
  {
    $results = $this->{$this->_db_group_name}->select('directorlinktvshow.idShow')
                                             ->from('directorlinktvshow')
                                             ->where('directorlinktvshow.idDirector', $idDirector)
                                             ->get()
                                             ->result();
    foreach($results as $result)
    {
      $idsShow[] = $result->idShow;
    }

    // Pas de résultat, alors on retourne NULL
    if (!isset($idsShow)) return NULL;

    // Un seul résultat ?
    if (count($idsShow) == 1)
    {
      return $this->tvshows_model->get($idsShow[0]);
    }
    else
    {
      return $this->tvshows_model->get($idsShow);
    }
  }

  /**
   * Récupère toutes les séries tv pour lesquels la personne dont on précise
   * l'identifiant est acteur
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_tvshows_played($idActor)
  {
    $results = $this->{$this->_db_group_name}->select('actorlinktvshow.idShow')
                                             ->from('actorlinktvshow')
                                             ->where('actorlinktvshow.idActor', $idActor)
                                             ->get()
                                             ->result();
    foreach($results as $result)
    {
      $idsShow[] = $result->idShow;
    }

    // Pas de résultat, alors on retourne NULL
    if (!isset($idsShow)) return NULL;

    // Un seul résultat ?
    if (count($idsShow) == 1)
    {
      return $this->tvshows_model->get($idsShow[0]);
    }
    else
    {
      return $this->tvshows_model->get($idsShow);
    }
  }

  /**
   * Récupère tous les épisodes d'une série tv pour lesquels la personne dont on
   * précise l'identifiant est scénariste
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_episodes_writed($idWriter)
  {
    $results = $this->{$this->_db_group_name}->select('writerlinkepisode.idEpisode')
                                             ->from('writerlinkepisode')
                                             ->where('writerlinkepisode.idWriter', $idWriter)
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
      return $this->episodes_model->get($idsEpisode[0]);
    }
    else
    {
      return $this->episodes_model->get($idsEpisode);
    }
  }

  /**
   * Récupère tous les épisodes d'une série tv pour lesquels la personne dont on
   * précise l'identifiant est réalisateur
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_episodes_directed($idDirector)
  {
    $results = $this->{$this->_db_group_name}->select('directorlinkepisode.idEpisode')
                                             ->from('directorlinkepisode')
                                             ->where('directorlinkepisode.idDirector', $idDirector)
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
      return $this->episodes_model->get($idsEpisode[0]);
    }
    else
    {
      return $this->episodes_model->get($idsEpisode);
    }
  }

  /**
   * Récupère tous les épisodes d'une série tv pour lesquels la personne dont on
   * précise l'identifiant est acteur
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_episodes_played($idActor)
  {
    $results = $this->{$this->_db_group_name}->select('actorlinkepisode.idEpisode')
                                             ->from('actorlinkepisode')
                                             ->where('actorlinkepisode.idActor', $idActor)
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
      return $this->episodes_model->get($idsEpisode[0]);
    }
    else
    {
      return $this->episodes_model->get($idsEpisode);
    }
  }

  /**
   * Récupère tous les scénaristes d'un film dont on précise l'identifiant
   *
   * Limite les résultats à $limit si différent précisé
   *
   * @access public
   * @param integer
   * @param integer
   * @return array
   */
  function get_writers_for_movie($idMovie, $limit = 0)
  {
    // Pas de limite ?
    if ($limit == 0)
    {
      $results = $this->{$this->_db_group_name}->select('writerlinkmovie.idWriter as id, actors.strActor as name')
                                               ->from('writerlinkmovie')
                                               ->join('actors', 'actors.idActor = writerlinkmovie.idWriter')
                                               ->where('writerlinkmovie.idMovie', $idMovie)
                                               ->get()
                                               ->result();
    }
    else
    {
      $results = $this->{$this->_db_group_name}->select('writerlinkmovie.idWriter as id, actors.strActor as name')
                                               ->from('writerlinkmovie')
                                               ->join('actors', 'actors.idActor = writerlinkmovie.idWriter')
                                               ->where('writerlinkmovie.idMovie', $idMovie)
                                               ->limit($limit)
                                               ->get()
                                               ->result();
    }

    // Mise en forme des résultats
    foreach($results as $key => $value)
      $results[$key]->photo = $this->xbmc->get_actor_photo($results[$key]->name);

    return $results;
  }

  /**
   * Supprime les scénaristes pour un film dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return void
   */
  function remove_writers_for_movie($movie_id)
  {
    $this->{$this->_db_group_name}->where('idMovie', $movie_id)
                                  ->delete('writerlinkmovie');
  }

  /**
   * Fixe les scénaristes d'un film dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @param integer
   * @return void
   */
  function set_writers_for_movie($writer_id, $movie_id)
  {
    $data = array('idWriter' => $writer_id, 'idMovie' => $movie_id);
    $this->{$this->_db_group_name}->insert('writerlinkmovie', $data);
  }

  /**
   * Récupère les réalisateurs d'un film dont on précise l'identifiant
   *
   * Limite les résultats à $limit si différent précisé
   *
   * @access public
   * @param integer
   * @param integer
   * @return array
   */
  function get_directors_for_movie($idMovie, $limit = 0)
  {
    // Pas de limite ?
    if ($limit == 0)
    {
      $results = $this->{$this->_db_group_name}->select('directorlinkmovie.idDirector as id, actors.strActor as name')
                                               ->from('directorlinkmovie')
                                               ->join('actors', 'actors.idActor = directorlinkmovie.idDirector')
                                               ->where('directorlinkmovie.idMovie', $idMovie)
                                               ->get()
                                               ->result();
    }
    else
    {
      $results = $this->{$this->_db_group_name}->select('directorlinkmovie.idDirector as id, actors.strActor as name')
                                               ->from('directorlinkmovie')
                                               ->join('actors', 'actors.idActor = directorlinkmovie.idDirector')
                                               ->where('directorlinkmovie.idMovie', $idMovie)
                                               ->limit($limit)
                                               ->get()
                                               ->result();
    }

    // Mise en forme des résultats
    foreach($results as $key => $value)
      $results[$key]->photo = $this->xbmc->get_actor_photo($results[$key]->name);

    return $results;
  }

  /**
   * Supprime les réalisateurs pour un film dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return void
   */
  function remove_directors_for_movie($movie_id)
  {
    $this->{$this->_db_group_name}->where('idMovie', $movie_id)
                                  ->delete('directorlinkmovie');
  }

  /**
   * Fixe les réalisateurs d'un film dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @param integer
   * @return void
   */
  function set_directors_for_movie($director_id, $movie_id)
  {
    $data = array('idDirector' => $director_id, 'idMovie' => $movie_id);
    $this->{$this->_db_group_name}->insert('directorlinkmovie', $data);
  }

  /**
   * Récupère les acteurs d'un film dont on précise l'identifiant
   *
   * Limite les résultats à $limit si différent précisé
   *
   * @access public
   * @param integer
   * @param integer
   * @return array
   */
  function get_actors_for_movie($idMovie, $limit = 0)
  {
    // Pas de limite ?
    if ($limit == 0)
    {
      $results = $this->{$this->_db_group_name}->select('actorlinkmovie.idActor as id, actors.strActor as name, actorlinkmovie.strRole as role')
                                               ->from('actorlinkmovie')
                                               ->join('actors', 'actors.idActor = actorlinkmovie.idActor')
                                               ->where('actorlinkmovie.idMovie', $idMovie)
                                               ->get()
                                               ->result();
    }
    else
    {
      $results = $this->{$this->_db_group_name}->select('actorlinkmovie.idActor as id, actors.strActor as name, actorlinkmovie.strRole as role')
                                               ->from('actorlinkmovie')
                                               ->join('actors', 'actors.idActor = actorlinkmovie.idActor')
                                               ->where('actorlinkmovie.idMovie', $idMovie)
                                               ->limit($limit)
                                               ->get()
                                               ->result();
    }

    // Mise en forme des résultats
    foreach($results as $key => $value)
    {
      if ($results[$key]->role == '') $results[$key]->role = $this->_no_role;

      $results[$key]->photo = $this->xbmc->get_actor_photo($results[$key]->name);
    }

    return $results;
  }

  /**
   * Supprime les actors pour un film dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return void
   */
  function remove_actors_for_movie($movie_id)
  {
    $this->{$this->_db_group_name}->where('idMovie', $movie_id)
                                  ->delete('actorlinkmovie');
  }

  /**
   * Fixe les acteurs d'un film dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @param string
   * @param integer
   * @return void
   */
  function set_actors_for_movie($actor_id, $role, $movie_id)
  {
    $data = array('idActor' => $actor_id, 'idMovie' => $movie_id, 'strRole' => $role);
    $this->{$this->_db_group_name}->insert('actorlinkmovie', $data);
  }

  /**
   * Récupère les scénaristes d'un épisode d'une série tv dont on précise
   * l'identifiant
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_writers_for_episode($idEpisode)
  {
    $writers = array();

    $results = $this->{$this->_db_group_name}->select('writerlinkepisode.idWriter as id, actors.strActor as name')
                                             ->from('writerlinkepisode')
                                             ->join('actors', 'actors.idActor = writerlinkepisode.idWriter')
                                             ->where('writerlinkepisode.idEpisode', $idEpisode)
                                             ->get()
                                             ->result();

    // Mise en forme des résultats
    foreach($results as $key => $value)
      $results[$key]->photo = $this->xbmc->get_actor_photo($results[$key]->name);

    return $results;
  }

  /**
   * Fixe les scénaristes d'un épisode d'une série tv dont on précise l'identifiant
   *
   * @access public
   * @param array
   * @param integer
   * @return void
   */
  function set_writers_for_episode($writers, $idEpisode)
  {
    // Suppression au préalable des scénaristes de cet épisode
    $this->{$this->_db_group_name}->delete('writerlinkepisode', array('idEpisode' => $idEpisode));

    foreach($writers as $key => $value)
    {
      $data = array('idWriter' => $key, 'idEpisode' => $idEpisode);
      $this->{$this->_db_group_name}->insert('writerlinkepisode', $data);
    }
  }

  /**
   * Récupère les réalisateurs d'une série tv dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_directors_for_tvshow($idShow)
  {
    $directors = array();

    $results = $this->{$this->_db_group_name}->select('directorlinktvshow.idDirector as id, actors.strActor as name')
                                             ->from('directorlinktvshow')
                                             ->join('actors', 'actors.idActor = directorlinktvshow.idDirector')
                                             ->where('directorlinktvshow.idShow', $idShow)
                                             ->get()
                                             ->result();

    // Mise en forme des résultats
    foreach($results as $key => $value)
      $results[$key]->photo = $this->xbmc->get_actor_photo($results[$key]->name);

    return $results;
  }

  /**
   * Fixe les réalisateurs d'une série tv dont on précise l'identifiant
   *
   * @access public
   * @param array
   * @param integer
   * @return void
   */
  function set_directors_for_tvshow($directors, $idShow)
  {
    // Suppression au préalable des réalisateurs de cette série tv
    $this->{$this->_db_group_name}->delete('directorlinktvshow', array('idShow' => $idShow));

    foreach($directors as $key => $value)
    {
      $data = array('idDirector' => $key, 'idShow' => $idShow);
      $this->{$this->_db_group_name}->insert('directorlinktvshow', $data);
    }
  }

  /**
   * Récupère les réalisateurs d'un épisode d'une série tv dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_directors_for_episode($idEpisode)
  {
    $directors = array();

    $results = $this->{$this->_db_group_name}->select('directorlinkepisode.idDirector as id, actors.strActor as name')
                                             ->from('directorlinkepisode')
                                             ->join('actors', 'actors.idActor = directorlinkepisode.idDirector')
                                             ->where('directorlinkepisode.idEpisode', $idEpisode)
                                             ->get()
                                             ->result();

    // Mise en forme des résultats
    foreach($results as $key => $value)
      $results[$key]->photo = $this->xbmc->get_actor_photo($results[$key]->name);

    return $results;
  }

  /**
   * Fixe les réalisateurs d'un épisode d'une série tv dont on précise l'identifiant
   *
   * @access public
   * @param array
   * @param integer
   * @return void
   */
  function set_directors_for_episode($directors, $idEpisode)
  {
    // Suppression au préalable des réalisateurs de cet épisode
    $this->{$this->_db_group_name}->delete('directorlinkepisode', array('idEpisode' => $idEpisode));

    foreach($directors as $key => $value)
    {
      $data = array('idDirector' => $key, 'idEpisode' => $idEpisode);
      $this->{$this->_db_group_name}->insert('directorlinkepisode', $data);
    }
  }

  /**
   * Récupère les acteurs d'une série tv dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_actors_for_tvshow($idShow)
  {
    $actors = array();

    $results = $this->{$this->_db_group_name}->select('actorlinktvshow.idActor as id, actors.strActor as name, actorlinktvshow.strRole as role')
                                             ->from('actorlinktvshow')
                                             ->join('actors', 'actors.idActor = actorlinktvshow.idActor')
                                             ->where('actorlinktvshow.idShow', $idShow)
                                             ->get()
                                             ->result();

    // Mise en forme des résultats
    foreach($results as $key => $value)
    {
      if ($results[$key]->role == '') $results[$key]->role = $this->_no_role;

      $results[$key]->photo = $this->xbmc->get_actor_photo($results[$key]->name);
    }

    return $results;
  }

  /**
   * Fixe les acteurs d'une série tv dont on précise l'identifiant
   *
   * @access public
   * @param array
   * @param integer
   * @return void
   */
  function set_actors_for_tvshow($actors, $idShow)
  {
    // Suppression au préalable des acteurs de cette série tv
    $this->{$this->_db_group_name}->delete('actorlinktvshow', array('idShow' => $idShow));

    foreach($actors as $key => $value)
    {
      $data = array('idActor' => $key, 'idShow' => $idShow, 'strRole' => $value->role);
      $this->{$this->_db_group_name}->insert('actorlinktvshow', $data);
    }
  }

  /**
   * Récupère les acteurs d'un épisode d'une série tv dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_actors_for_episode($idEpisode)
  {
    $actors = array();

    $results = $this->{$this->_db_group_name}->select('actorlinkepisode.idActor as id, actors.strActor as name, actorlinkepisode.strRole as role')
                                             ->from('actorlinkepisode')
                                             ->join('actors', 'actors.idActor = actorlinkepisode.idActor')
                                             ->where('actorlinkepisode.idEpisode', $idEpisode)
                                             ->get()
                                             ->result();

    // Mise en forme des résultats
    foreach($results as $key => $value)
    {
      if ($results[$key]->role == '') $results[$key]->role = $this->_no_role;

      $results[$key]->photo = $this->xbmc->get_actor_photo($results[$key]->name);
    }

    return $results;
  }

  /**
   * Fixe les acteurs d'un épisode d'une série tv dont on précise l'identifiant
   *
   * @access public
   * @param array
   * @param integer
   * @return void
   */
  function set_actors_for_episode($actors, $idEpisode)
  {
    // Suppression au préalable des acteurs de cet épisode
    $this->{$this->_db_group_name}->delete('actorlinkepisode', array('idEpisode' => $idEpisode));

    foreach($actors as $key => $value)
    {
      $data = array('idActor' => $key, 'idEpisode' => $idEpisode, 'strRole' => $value->role);
      $this->{$this->_db_group_name}->insert('actorlinkepisode', $data);
    }
  }

  /**
   * Cherche un acteur dont on précise une partie du nom
   * Retourne un objet représentant le ou les acteur(s) trouvé(s) ou NULL si non trouvé
   *
   * @access public
   * @param string
   * @param integer
   * @param integer
   * @return integer
   */
  function search_by_name($name, $limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('idActor as id, strActor	as name')
																						 ->from('actors')
																						 ->like('strActor', urldecode($name))
																						 ->limit($limit, $offset)
																						 ->get()
                                             ->result();

    // Si au moins un acteur est trouvé dans la base de données
    foreach($results as $result)
    {
      $actor = new stdClass();

      $actor->id = $result->id;
      $actor->name = $result->name;
      $actor->photo = $this->xbmc->get_actor_photo($actor->name);

      $actors[] = $actor;
    }

		if (isset($actors))
				return $actors;
		else
				return NULL;
  }

  /**
   * Retourne le nombre total de personnalités contenant une partie d'un nom
   *
   * @access public
   * @param string
   * @return integer
   */
  function count_all_by_name($name)
  {
    return $this->{$this->_db_group_name}->from('actors')
																				 ->like('strActor', urldecode($name))
																				 ->count_all_results();
  }

  /**
   * Cherche un acteur dont on précise l'identifiant
   * Retourne un objet représentant l'acteur trouvé ou NULL si non trouvé
   *
   * @access public
   * @param integer
   * @return object
   */
  function get_by_id($id)
  {
    $results = $this->{$this->_db_group_name}->select('strActor as name')
                                             ->from('actors')
                                             ->where('idActor', $id)
                                             ->get()
                                             ->result();

    // Si l'acteur est dans la base de données
    if (isset($results[0]->name))
    {
      $actor = new stdClass();

      $actor->id = $id;
      $actor->name = $results[0]->name;
      $actor->photo = $this->xbmc->get_actor_photo($results[0]->name);

      $result = $actor;
    }
    else
    {
      $result = NULL;
    }

    return $result;
  }

  /**
   * Retourne tous les acteurs
   *
   * @access public
   * @return array
   */
  function get_all($limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('idActor as id, strActor as name')
                                             ->from('actors')
                                             ->limit($limit, $offset)
                                             ->order_by('name', 'asc')
                                             ->get()
                                             ->result();

    // Pour tous les acteurs dans la base de données
    foreach($results as $result)
    {
      $actor = new stdClass();

      $actor->id = $result->id;
      $actor->name = $result->name;
      $actor->photo = $this->xbmc->get_actor_photo($result->name);

      $actors[] = $actor;
    }

    return $actors;
  }

  /**
   * Retourne le nombre total de personnalités
   *
   * @access public
   * @return integer
   */
  function count_all()
  {
    return $this->{$this->_db_group_name}->from('actors')->count_all_results();
  }

}

/* End of file actors_model.php */
/* Location: ./application/models/video/actors_model.php */
