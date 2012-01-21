<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Video_files_model extends CI_model
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
   * Ajoute un fichier dont on précise des informations dans un tableau
   *
   * @access public
   * @param array
   * @param string
   * @return integer
   */
  function add($data)
  {
    $this->{$this->_db_group_name}->insert('files', $data);

    // Identifiant du fichier ajouté
    return $this->{$this->_db_group_name}->insert_id();
  }

  /**
   * Retourne tous les fichiers de la base de données 'xbmc_video'
   * sous la forme d'un tableau
   *
   * @access public
   * @return array
   */
  function get_all()
  {
    $files = array();

    $results = $this->{$this->_db_group_name}->select('path.strPath, files.strFilename')
                                             ->from('files')
                                             ->join('path', 'path.idPath = files.idPath')
                                             ->get()
                                             ->result();

    // Mise en forme des résultats pour ne garder que le chemin complet
    // De la forme /chemin_complet/fichier.ext
    foreach($results as $result)
      $files[] = $result->strPath.$result->strFilename;

    return $files;
  }

  /**
   * Retourne tous les fichiers de la base de données 'xbmc_video'
   * situés dans le dossier dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return array
   */
  function get_all_by_path_id($idPath)
  {
    $files = array();
    $results = $this->{$this->_db_group_name}->select('files.strFilename')
                                             ->from('files')
                                             ->where('files.idPath', $idPath)
                                             ->get()
                                             ->result();

    if (isset($results))
    {
      foreach($results as $result)
        $files[] = $result->strFilename;
    }

    return $files;
  }

}

/* End of file files_model.php */
/* Location: ./application/models/video/files_model.php */
