<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Countries_model extends CI_model
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
   * Ajoute un pays dont on précise le nom
   *
   * @access public
   * @param string
   * @return integer
   */
  function add($name)
  {
    $data = array('strCountry' => $name);
    $this->{$this->_db_group_name}->insert('country', $data);

    // Identifiant du pays ajouté
    return $this->{$this->_db_group_name}->insert_id();
  }

  /**
   * Récupère les pays d'un film dont on précise l'identifiant
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
      $results = $this->{$this->_db_group_name}->select('country.idCountry as id, country.strCountry as name')
                                               ->from('countrylinkmovie')
                                               ->join('country', 'country.idCountry = countrylinkmovie.idCountry')
                                               ->where('countrylinkmovie.idMovie', $movie_id)
                                               ->get()
                                               ->result();
    }
    else
    {
      $results = $this->{$this->_db_group_name}->select('country.idCountry as id, country.strCountry as name')
                                               ->from('countrylinkmovie')
                                               ->join('country', 'country.idCountry = countrylinkmovie.idCountry')
                                               ->where('countrylinkmovie.idMovie', $movie_id)
                                               ->limit($limit)
                                               ->get()
                                               ->result();
    }

    return $results;
  }

  /**
   * Supprime les pays pour un film dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return void
   */
  function remove_for_movie($movie_id)
  {
    $this->{$this->_db_group_name}->where('idMovie', $movie_id)
                                  ->delete('countrylinkmovie');
  }

  /**
   * Fixe les pays d'un film dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @param integer
   * @return void
   */
  function set_for_movie($country_id, $movie_id)
  {
    $data = array('idCountry' => $country_id, 'idMovie' => $movie_id);
    $this->{$this->_db_group_name}->insert('countrylinkmovie', $data);
  }

  /**
   * Cherche un pays dont on précise le nom
   * Retourne l'identifiant du public trouvé ou 0 si non trouvé
   *
   * @access public
   * @param string
   * @return integer
   */
  function get_by_name($name)
  {
    $results = $this->{$this->_db_group_name}->select('idCountry as id')
                                             ->from('country')
                                             ->where('strCountry', $name)
                                             ->get()
                                             ->result();

    // Si le pays est dans la base de données
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
   * Cherche un pays dont on précise l'identifiant
   * Retourne le nom du pays trouvé ou '' si non trouvé
   *
   * @access public
   * @param integer
   * @return string
   */
  function get_by_id($id)
  {
    $results = $this->{$this->_db_group_name}->select('strCountry as name')
                                             ->from('country')
                                             ->where('idCountry', $id)
                                             ->get()
                                             ->result();

    // Si le pays est dans la base de données
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
   * Retourne tous les pays
   *
   * @access public
   * @return array
   */
  function get_all()
  {
    $results = $this->{$this->_db_group_name}->select('idCountry as id, strCountry as name')
                                             ->from('country')
                                             ->get()
                                             ->result();

    return $results;
  }

}

/* End of file countries_model.php */
/* Location: ./application/models/video/countries_model.php */