<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sets_model extends CI_model
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
   * Ajoute une saga dont on précise le nom
   *
   * @access public
   * @param string
   * @return integer
   */
  function add($strSet)
  {
    $data = array('strSet' => $strSet);
    $this->{$this->_db_group_name}->insert('sets', $data);

    // Identifiant de la saga ajoutée
    return $this->{$this->_db_group_name}->insert_id();
  }

  /**
   * Ajoute un film dont on précise l'identifiant à une saga dont on précise
   * l'identifiant
   *
   * @access public
   * @param interger
   * @param interger
   * @return void
   */
  function add_movie($idMovie, $idSet)
  {
    $data = array('idSet' => $idSet, 'idMovie' => $idMovie);
    $this->{$this->_db_group_name}->insert('setlinkmovie', $data);
  }

  /**
   * Retire un film dont on précise l'identifiant d'une saga
   *
   * @access public
   * @param interger
   * @return void
   */
  function remove_movie($idMovie)
  {
    $data = array('idMovie' => $idMovie);
    $this->{$this->_db_group_name}->delete('setlinkmovie', $data);
  }

  /**
   * Supprime complètement une saga dont on précise l'identifiant
   *
   * @access public
   * @param interger
   * @return void
   */
  function delete($idSet)
  {
		// Recherche des films liés à eette saga
    $results = $this->{$this->_db_group_name}->select('setlinkmovie.idMovie')
                                             ->from('setlinkmovie')
                                             ->where('setlinkmovie.idSet', $idSet)
                                             ->get()
                                             ->result();

    // Si au moins un film est dans la saga
    if (isset($results[0]->idMovie))
    {
      $this->load->model('video/movies_model');

			// Supression de l'ordre dans la saga pour tous les films de cette saga
      foreach($results as $result)
      {
        $data = array('c10' => '');
        $this->movies_model->update($result->idMovie, $data);
      }
		}

		$poster = $this->xbmc_lib->get_set_poster($idSet);
		$backdrop = $this->xbmc_lib->get_set_backdrop($idSet);

		// Suppression de l'affiche de cette saga + miniature
		if ($poster->url != base_url().'css/assets/gui/DefaultVideoPoster.png')
		{
			unlink($poster->filename);
			unlink(str_replace($this->xbmc_lib->images_cache_url,  $this->xbmc_lib->images_cache_dir, $poster->url));
		}

		// Suppression du fond d'écran de cette saga + miniature
		if ($poster->url != base_url().'css/assets/gui/DefaultVideoBackdrop.png')
		{
			unlink($backdrop->filename);
			unlink(str_replace($this->xbmc_lib->images_cache_url,  $this->xbmc_lib->images_cache_dir, $backdrop->url));
		}

		// Suppresion des films liés à cette saga
    $data = array('idSet' => $idSet);
    $this->{$this->_db_group_name}->delete('setlinkmovie', $data);

		// Suppresion de cette saga
    $data = array('idSet' => $idSet);
    $this->{$this->_db_group_name}->delete('sets', $data);
  }

  /**
   * Retourne le nom d'une saga dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return string
   */
  function get_name($idSet)
  {
    $results = $this->{$this->_db_group_name}->select('strSet as name')
                                             ->from('sets')
                                             ->where('idSet', $idSet)
                                             ->get()
                                             ->result();

    // Si la saga est dans la base de données
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
   * Retourne les informations complètes d'une saga dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return object
   */
  function get($idSet)
  {
    $results = $this->{$this->_db_group_name}->select('movie.idMovie')
                                             ->from('movie')
                                             ->join('setlinkmovie', 'setlinkmovie.idMovie = movie.idMovie')
                                             ->where('setlinkmovie.idSet', $idSet)
                                             ->get()
                                             ->result();

    $set = new stdClass();
    $set->id = $idSet;
    $set->name = $this->get_name($idSet);
    $set->total = 0;
		$set->poster = $this->xbmc_lib->get_set_poster($idSet);
		$set->backdrop = $this->xbmc_lib->get_set_backdrop($idSet);

    // Si au moins un film est dans la saga
    if (isset($results[0]->idMovie))
    {
      $this->load->model('video/movies_model');

      foreach($results as $result)
      {
        $idsMovie[] = $result->idMovie;
      }

			// Nombre total de films dans la saga
			$set->total = count($results);

      // On récupère enfin les films de la saga
      $set->movies = $this->movies_model->get_for_set($idsMovie, $this->session->userdata('can_change_images'));
    }

    // On retourne la saga avec le(s) film(s) trouvé(s)
    return $set;
  }

  /**
   * Cherche une saga dont on précise une partie du nom
   * Retourne un objet représentant la ou les saga(s) trouvée(s) ou NULL si non trouvé
   *
   * @access public
   * @param string
   * @param integer
   * @param integer
   * @return integer
   */
  function search_by_name($name, $limit = NULL, $offset = NULL)
  {
    $results = $this->{$this->_db_group_name}->select('idSet as id')
																						 ->from('sets')
																						 ->like('strSet', urldecode($name))
																						 ->limit($limit, $offset)
																						 ->get()
                                             ->result();

    // Si au moins une saga est trouvée dans la base de données
    foreach($results as $result)
    {
      $sets[] = $this->get($result->id);
    }

		if (isset($sets))
				return $sets;
		else
				return NULL;
  }

  /**
   * Retourne le nombre total de sagas contenant une partie d'un nom
   *
   * @access public
   * @param string
   * @return integer
   */
  function count_all_by_name($name)
  {
    return $this->{$this->_db_group_name}->from('sets')
																				 ->like('strSet', urldecode($name))
																				 ->count_all_results();
  }

  function get_all($limit = NULL, $offset = NULL)
  {

    // Récupère toutes les sagas sans compter le nombre de films attribués
    $results = $this->{$this->_db_group_name}->select('sets.idSet as id, sets.strSet as name')
                                             ->from('sets')
                                             ->limit($limit, $offset)
                                             ->get()
                                             ->result();
		// Valeur par défaut
		$sets = array();

    foreach($results as $value)
    {
      $value->total = 0;

			$set = new stdClass();
			$set->id = $value->id;
			$set->name = $value->name;
			$set->total = $value->total;

      $sets[$value->id] = $set;
      $sets[$value->id]->poster = $this->xbmc_lib->get_set_poster($set->id);
      $sets[$value->id]->backdrop = $this->xbmc_lib->get_set_backdrop($set->id);
    }

    // Récupère toutes les sagas en comptant le nombre de films attribués
    $results = $this->{$this->_db_group_name}->select('sets.idSet as id, sets.strSet as name, COUNT(1) AS total')
                                             ->from('sets')
                                             ->join('setlinkmovie', 'setlinkmovie.idSet = sets.idSet')
                                             ->group_by('sets.idSet')
                                             ->get()
                                             ->result();

    foreach($results as $key => $value)
    {
			$set = new stdClass();
			$set->id = $value->id;
			$set->name = $value->name;
			$set->total = $value->total;

      $sets[$value->id] = $set;
      $sets[$value->id]->poster = $this->xbmc_lib->get_set_poster($set->id);
      $sets[$value->id]->backdrop = $this->xbmc_lib->get_set_backdrop($set->id);
    }

    // On retourne les sagas trouvées ou un tableau vide
    return $sets;
  }

  /**
   * Cherche une saga dont on précise le nom
   * Retourne l'identifiant de la saga trouvé ou 0 si non trouvée
   *
   * @access public
   * @param string
   * @return integer
   */
  function search($strSet)
  {
    $results = $this->{$this->_db_group_name}->select('idSet as id')
                                             ->from('sets')
                                             ->where('strSet', $strSet)
                                             ->get()
                                             ->result();

    // Si la saga est dans la base de données
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
   * Retourne 'max' dernières sagas de films
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_last($max = 4)
  {
    $results = $this->{$this->_db_group_name}->select('idSet as id')
                                             ->from('sets')
                                             ->order_by('idSet DESC')
                                             ->limit($max)
                                             ->get()
                                             ->result();

    // Récupération des identifiants des sagas de films
    foreach($results as $result)
    {
      $sets[] = $this->get($result->id);
    }

    // On retourne les sagas de films trouvées
    return $sets;
  }

  /**
   * Retourne le nombre total de de sagas de films
   *
   * @access public
   * @return integer
   */
  function count_all()
  {
    return $this->{$this->_db_group_name}->from('sets')
                                         ->count_all_results();
  }

}

/* End of file sets_model.php */
/* Location: ./application/models/video/sets_model.php */
