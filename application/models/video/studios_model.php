<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Studios_model extends CI_model
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
   * Ajoute un studio dont on précise le nom
   *
   * Retourne l'identifiant du nouveau studio
   *
   * @access public
   * @param string
   * @return integer
   */
  function add($name)
  {
    $data = array('strStudio' => $name);
    $this->{$this->_db_group_name}->insert('studio', $data);

    // Identifiant du studio ajouté
    return $this->{$this->_db_group_name}->insert_id();
  }

  /**
   * Récupère les studios d'un film dont on précise l'identifiant
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
    // Pas de limite ?
    if ($limit == 0)
    {
      $results = $this->{$this->_db_group_name}->select('studio.idStudio as id, studio.strStudio as name')
                                               ->from('studiolinkmovie')
                                               ->join('studio', 'studio.idStudio = studiolinkmovie.idStudio')
                                               ->where('studiolinkmovie.idMovie', $movie_id)
                                               ->get()
                                               ->result();
    }
    else
    {
      $results = $this->{$this->_db_group_name}->select('studio.idStudio as id, studio.strStudio as name')
                                               ->from('studiolinkmovie')
                                               ->join('studio', 'studio.idStudio = studiolinkmovie.idStudio')
                                               ->where('studiolinkmovie.idMovie', $movie_id)
                                               ->limit($limit)
                                               ->get()
                                               ->result();
    }

    return $results;
  }

  /**
   * Supprime les studios pour un film dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return void
   */
  function remove_for_movie($movie_id)
  {
    $this->{$this->_db_group_name}->where('idMovie', $movie_id)
                                  ->delete('studiolinkmovie');
  }

  /**
   * Fixe les studios d'un film dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @param integer
   * @return void
   */
  function set_for_movie($studio_id, $movie_id)
  {
    $data = array('idStudio' => $studio_id, 'idMovie' => $movie_id);
    $this->{$this->_db_group_name}->insert('studiolinkmovie', $data);
  }

  /**
   * Récupère les studios d'une série tv dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_for_tvshow($idShow)
  {
    $studios = array();

    $results = $this->{$this->_db_group_name}->select('studio.idStudio as id, studio.strStudio as name')
                                             ->from('studiolinktvshow')
                                             ->join('studio', 'studio.idStudio = studiolinktvshow.idStudio')
                                             ->where('studiolinktvshow.idShow', $idShow)
                                             ->get()
                                             ->result();

    return $results;
  }

  /**
   * Fixe les studios d'une série tv dont on précise l'identifiant
   *
   * @access public
   * @param array
   * @param integer
   * @return void
   */
  function set_for_tvshow($studios, $idShow)
  {
    // Suppression au préalable des studios de cette série tv
    $this->{$this->_db_group_name}->delete('studiolinktvshow', array('idShow' => $idShow));

    foreach($studios as $key => $value)
    {
      $data = array('idStudio' => $key, 'idShow' => $idShow);
      $this->{$this->_db_group_name}->insert('studiolinktvshow', $data);
    }
  }

  /**
   * Cherche un studio dont on précise le nom
   * Retourne l'identifiant du studio trouvé ou 0 si non trouvé
   *
   * @access public
   * @param string
   * @return integer
   */
  function get_by_name($name)
  {
    $results = $this->{$this->_db_group_name}->select('idStudio as id')
                                             ->from('studio')
                                             ->where('strStudio', $name)
                                             ->get()
                                             ->result();

    // Si le studio est dans la base de données
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
   * Cherche un studio dont on précise l'identifiant
   * Retourne le nom du studio trouvé ou '' si non trouvé
   *
   * @access public
   * @param integer
   * @return string
   */
  function get_by_id($id)
  {
    $results = $this->{$this->_db_group_name}->select('strStudio as name')
                                             ->from('studio')
                                             ->where('idStudio', $id)
                                             ->get()
                                             ->result();

    // Si le studio est dans la base de données
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
   * Retourne tous les studios
   *
   * @access public
   * @return array
   */
  function get_all()
  {
    $results = $this->{$this->_db_group_name}->select('idStudio as id, strStudio as name')
                                             ->from('studio')
                                             ->get()
                                             ->result();

    return $results;
  }

}

/* End of file studios_model.php */
/* Location: ./application/models/video/studios_model.php */