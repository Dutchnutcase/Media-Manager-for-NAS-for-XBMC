<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sources_model extends CI_model
{
  // Le modèle utilise la base de données 'xbmc'
  private $_db_group_name = 'xbmc';
  private $_CI;

  function __construct()
  {
    parent::__construct();

    $this->_CI =& get_instance();

    // Accès à la base de données 'xbmc_video'
    $this->{$this->_db_group_name} = $this->load->database($this->_db_group_name, TRUE);
  }

  /**
   * Cherche une source dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return object
   */
  function get($id)
  {
    $results = $this->{$this->_db_group_name}->select('*')
                                             ->from('sources')
                                             ->where('id', $id)
                                             ->get()
                                             ->result();
    return $results[0];
  }

  /**
   * Met à jour les informations d'une source dont on précise l'identifiant
   * et un tableau de données
   *
   * @access public
   * @param integer
   * @param array
   * @return void
   */
  function edit($id, $data)
  {
    // On s'assure que le chemin se termine bien par un '/'
    $data['server_path'] .= '/';
    $data['server_path'] = str_replace('//', '/', $data['server_path']);

    $this->{$this->_db_group_name}->where('id', $id)->update('sources', $data);
  }

  /**
   * Ajoute une source dont on précise les informations
   *
   * @access public
   * @param array
   * @return void
   */
  function add($data)
  {
    $this->{$this->_db_group_name}->insert('sources', $data);
  }

  /**
   * Retourne toutes les sources enregistrées dans l'application
   *
   * @access public
   * @param integer
   * @param integer
   * @return array
   */
  function get_all($limit = NULL, $offset = NULL)
  {
    $sources = $this->{$this->_db_group_name}->select('sources.*')
                                             ->from('sources')
                                             ->get()
                                             ->result();

		// Pas de sources ? On récupère celle gérées par XBMC
		if (count($sources) == 0)
		{
			$this->add_from_xbmc();

			$sources = $this->{$this->_db_group_name}->select('sources.*')
																							 ->from('sources')
																							 ->get()
																							 ->result();
		}
		
		// Transforme les paramètres de toutes les sources en objet, si présents
    foreach($sources as $source)
    {
			if ($source->settings != '')
			{
				$source->settings = unserialize($source->settings);
			}
    }

    return $sources;
  }

  /**
   * Compte toutes les sources enregistrées dans l'application
   *
   * @access public
   * @return integer
   */
  function count_all()
  {
    return $this->{$this->_db_group_name}->from('sources')
                                         ->count_all_results();
  }

  /**
   * Ajoute toutes les sources enregistrées dans XBMC
   * 
   * @access public
   * @return void
   */
  function add_from_xbmc()
  {
    $this->_CI->load->model('video/video_paths_model');
    $this->load->model('video/video_paths_model');

    $sources = $this->_CI->video_paths_model->get_sources();

    foreach($sources as $source)
    {
      $settings = '';

      // Si des paramètres existent ont les prend en compte
      if (isset($source->settings)) $settings = serialize($source->settings);

      $data = array('idPath' => $source->id,
                    'client_path' => $source->path,
                    'media_db' => 'video',
                    'content' => $source->content,
                    'scraper' => $source->scraper,
                    'settings' => $settings
                    );

      $this->{$this->_db_group_name}->insert('sources', $data);
    }
  }

}

/* End of file sources_model.php */
/* Location: ./application/models/xbmc/sources_model.php */
