<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Genres_model extends CI_model
{
  // Le modèle utilise la base de données 'xbmc_video'
  private $_db_group_name = 'video';

  function __construct()
  {
    parent::__construct();

    // Accès à la base de données 'xbmc_video'
    $this->{$this->_db_group_name} = $this->load->database($this->_db_group_name, TRUE);
  }

  /**
   * Ajoute un genre dont on précise le nom
   *
   * @access public
   * @param string
   * @return integer
   */
  function add($name)
  {
    $data = array('strGenre' => $name);
    $this->{$this->_db_group_name}->insert('genre', $data);

    // Identifiant du genre ajouté
    return $this->{$this->_db_group_name}->insert_id();
  }

  /**
   * Récupère les genres d'un film dont on précise l'identifiant
   *
   * Limite les résultats à $limit si différent précisé
   *
   * @access public
   * @param integer
   * @param integer
   * @return array
   */
  function get_for_movie($movie_id, $limit = 0)
  {
    $genres = array();

    // Pas de limite ?
    if ($limit == 0)
    {
      $results = $this->{$this->_db_group_name}->select('genre.idGenre as id, genre.strGenre as name')
                                               ->from('genrelinkmovie')
                                               ->join('genre', 'genre.idGenre = genrelinkmovie.idGenre')
                                               ->where('genrelinkmovie.idMovie', $movie_id)
                                               ->get()
                                               ->result();
    }
    else
    {
      $results = $this->{$this->_db_group_name}->select('genre.idGenre as id, genre.strGenre as name')
                                               ->from('genrelinkmovie')
                                               ->join('genre', 'genre.idGenre = genrelinkmovie.idGenre')
                                               ->where('genrelinkmovie.idMovie', $movie_id)
                                               ->limit($limit)
                                               ->get()
                                               ->result();
    }

    return $results;
  }

  /**
   * Supprime les genres pour un film dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return void
   */
  function remove_for_movie($movie_id)
  {
    $this->{$this->_db_group_name}->where('idMovie', $movie_id)
                                  ->delete('genrelinkmovie');
  }

  /**
   * Fixe les genres d'un film dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @param integer
   * @return void
   */
  function set_for_movie($genre_id, $movie_id)
  {
    $data = array('idGenre' => $genre_id, 'idMovie' => $movie_id);
    $this->{$this->_db_group_name}->insert('genrelinkmovie', $data);
  }

  /**
   * Récupère les genres d'une série tv dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_for_tvshow($idShow)
  {
    $genres = array();

    $results = $this->{$this->_db_group_name}->select('genre.idGenre as id, genre.strGenre as name')
                                             ->from('genrelinktvshow')
                                             ->join('genre', 'genre.idGenre = genrelinktvshow.idGenre')
                                             ->where('genrelinktvshow.idShow', $idShow)
                                             ->get()
                                             ->result();

    return $results;
  }

  /**
   * Fixe les genres d'une série tv dont on précise l'identifiant
   *
   * @access public
   * @param array
   * @param integer
   * @return void
   */
  function set_for_tvshow($genres, $idShow)
  {
    // Suppression au préalable des genres de cette série tv
    $this->{$this->_db_group_name}->delete('genrelinktvshow', array('idShow' => $idShow));

    foreach($genres as $key => $value)
    {
      $data = array('idGenre' => $key, 'idShow' => $idShow);
      $this->{$this->_db_group_name}->insert('genrelinktvshow', $data);
    }
  }

  /**
   * Cherche un genre dont on précise le nom
   * Retourne l'identifiant du genre trouvé ou 0 si non trouvé
   *
   * @access public
   * @param string
   * @return integer
   */
  function get_by_name($name)
  {
    $results = $this->{$this->_db_group_name}->select('idGenre as id')
                                             ->from('genre')
                                             ->where('strGenre', $name)
                                             ->get()
                                             ->result();

    // Si le genre est dans la base de données
    if (isset($results[0]->id))
    {
      $result = $results[0]->id;
    }
    else
    {
      $result = 0;
    }

    return $result;
  }

  /**
   * Cherche un genre dont on précise l'identifiant
   * Retourne le nom du genre trouvé ou '' si non trouvé
   *
   * @access public
   * @param integer
   * @return string
   */
  function get_by_id($id)
  {
    $results = $this->{$this->_db_group_name}->select('strGenre as name')
                                             ->from('genre')
                                             ->where('idGenre', $id)
                                             ->get()
                                             ->result();

    // Si le genre est dans la base de données
    if (isset($results[0]->name))
    {
      $result = $results[0]->name;
    }
    else
    {
      $result = '';
    }

    return $result;
  }

  /**
   * Retourne tous les genres
   *
   * @access public
   * @return array
   */
  function get_all()
  {
    $results = $this->{$this->_db_group_name}->select('idGenre as id, strGenre as name')
                                             ->from('genre')
                                             ->get()
                                             ->result();

    return $results;
  }

}

/* End of file genres_model.php */
/* Location: ./application/models/video/genres_model.php */