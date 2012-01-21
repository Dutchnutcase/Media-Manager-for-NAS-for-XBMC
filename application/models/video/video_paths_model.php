<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Video_paths_model extends CI_model
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

  /**
   * Retourne les informations d'un chemin dont on précise l'identifiant
   * Retourne le chemin trouvé ou 0 si non trouvé
   *
   * @access public
   * @param integer
   * @return object or NULL
   */
  function get($idPath)
  {
    $results = $this->{$this->_db_group_name}->select('*')
                                             ->from('path')
                                             ->where('idPath', $idPath)
                                             ->get()
                                             ->result();

    // Si le chemin est dans la base de données
    if (isset($results[0]->idPath))
    {
      $result = $results[0];
    }
    else
    {
      $result = NULL;
    }

    return $result;
  }

  /**
   * Retourne tous les chemins de la base de données 'xbmc_video'
   * servant de sources sous la forme d'un objet
   *
   * @access public
   * @return object
   */
  function get_sources()
  {
    $results = $this->{$this->_db_group_name}->select('*')
                                             ->from('path')
                                             ->where('strScraper != ', '')
                                             ->get()
                                             ->result();

    $sources = array();

    foreach($results as $result)
    {
      $result->strScraper = str_replace('metadata.', '', $result->strScraper);
      $result->strScraper = str_replace('.', '_', $result->strScraper);

      $path = new stdClass();
      $path->id = $result->idPath;
      $path->path = $result->strPath;
      $path->content = $result->strContent;
      $path->scraper = $result->strScraper;

      // Présence de paramètres ?
      if ($result->strSettings != '')
      {
        $xml = simplexml_load_string($result->strSettings);

        $settings = new stdClass();

        foreach($xml as $node)
        {
          $id = $node->attributes()->id;
          $v = (string) $node->attributes()->value;

          // Besoin de transformer des chaînes en booléens ?
          if (($v == 'true') || ($v == 'false'))
          {
            if ($v == 'true') $value = TRUE;
            if ($v == 'false') $value = FALSE;
          }
          else
              $value = $v;

          $settings->$id = $value;
        }
        $settings->user_folder_name = ($result->useFolderNames == 1) ? TRUE : FALSE;
        $path->settings = $settings;
      }

      $sources[] = $path;
    }

    return $sources;
  }

}

/* End of file paths_model.php */
/* Location: ./application/models/video/video_paths_model.php */
