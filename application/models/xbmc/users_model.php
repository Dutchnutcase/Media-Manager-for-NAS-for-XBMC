<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends CI_model
{
  // Le modèle utilise la base de données 'xbmc'
  private $_db_group_name = 'xbmc';
  private $_CI;

  function __construct()
  {
    parent::__construct();

    $this->_CI =& get_instance();

    // Accès à la base de données 'xbmc'
    $this->{$this->_db_group_name} = $this->load->database($this->_db_group_name, TRUE);
	}
	
  /**
   * Vérifie si le nom passé en paramètre correspond déjà à un utilisateur
   * enregistré dans l'application
   *
   * @access public
   * @param string
   * @return bool
   */
  function check_username($username)
  {
    $count = $this->{$this->_db_group_name}->from('users')
                                           ->where('username', $username)
                                           ->count_all_results();

    return ($count != 0);
  }

  /**
   * Ajoute un utilisateur dont on précise le nom et le mot de passe
   *
   * Retourne l'identifiant du nouveel utlisateur
   *
   * @access public
   * @param string
   * @param string
   * @return integer
   */
  function add($username, $password)
  {
    // Cryptage du mot de passe
    $password = md5(md5($this->config->item('encryption_key')).md5($password));

    $data = array('username' => $username,
                  'password' => $password,
                  'can_change_infos' => '0',
                  'can_change_images' => '0',
                  'can_download_video' => '0',
                  'can_download_music' => '0',
                  'is_active' => '1',
                  'is_admin' => '0'
                  );

    $this->{$this->_db_group_name}->insert('users', $data);

    // Identifiant de l'uilisateur ajouté
    return $this->{$this->_db_group_name}->insert_id();
  }

  /**
   * Cherche un utilisateur dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return object
   */
  function get($id)
  {
    $results = $this->{$this->_db_group_name}->select('*')
                                             ->from('users')
                                             ->where('id', $id)
                                             ->get()
                                             ->result();
    return $results[0];
  }

  /**
   * Met à jour les informations d'un utilisateur dont on précise l'identifiant
   * et un tableau de données
   *
   * @access public
   * @param integer
   * @param array
   * @return void
   */
  function edit($id, $data)
  {
    // Cryptage du mot de passe si présent
    if (isset($data['password']))
        $data['password'] = md5(md5($this->config->item('encryption_key')).md5($data['password']));

    $this->{$this->_db_group_name}->where('id', $id)->update('users', $data);
  }

  /**
   * Supprime un utilisateur dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @return void
   */
  function delete($id)
  {
    $this->{$this->_db_group_name}->where('id', $id)->delete('users');
  }

  /**
   * Retourne tous les utilisateurs enregistrés dans l'application
   *
   * @access public
   * @param integer
   * @param integer
   * @return array
   */
  function get_all($limit = NULL, $offset = NULL)
  {
    return $this->{$this->_db_group_name}->select('*')
                                         ->from('users')
                                         ->limit($limit, $offset)
                                         ->get()
                                         ->result();
  }

  /**
   * Compte tous les utilisateurs enregistrés dans l'application
   *
   * @access public
   * @return integer
   */
  function count_all()
  {
    return $this->{$this->_db_group_name}->from('users')
                                         ->count_all_results();
  }

}

/* End of file users_model.php */
/* Location: ./application/models/xbmc/users_model.php */
