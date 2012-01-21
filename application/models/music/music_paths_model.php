<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Music_paths_model extends CI_model
{
  // Le modèle utilise la base de données 'music'
  private $_db_group_name = 'music';

  function __construct()
  {
    parent::__construct();

    // Accès à la base de données 'xbmc_video'
    $this->{$this->_db_group_name} = $this->load->database($this->_db_group_name, TRUE);
  }

  /**
   * Ajoute un chemin dont on précise le nom
   *
   * @access public
   * @param string
   * @return integer
   */
  function add($strPath)
  {
    $data = array('strPath' => $strPath);
    $this->{$this->_db_group_name}->insert('path', $data);

    // Identifiant du chemin ajouté
    return $this->{$this->_db_group_name}->insert_id();
  }

  /**
   * Cherche un chemin dont on précise le nom
   * Retourne l'identifiant du chemin trouvé ou 0 si non trouvé
   *
   * @access public
   * @param string
   * @return integer
   */
  function search($strPath)
  {
    $results = $this->{$this->_db_group_name}->select('idPath')
                                             ->from('path')
                                             ->where('strPath', $strPath)
                                             ->get()
                                             ->result();

    // Si le chemin est dans la base de données
    if (isset($results[0]->idPath))
    {
      $result = $results[0]->idPath;
    }
    else
    {
      $result = 0;
    }

    return $result;
  }

}

/* End of file paths_model.php */
/* Location: ./application/models/music/music_paths_model.php */
