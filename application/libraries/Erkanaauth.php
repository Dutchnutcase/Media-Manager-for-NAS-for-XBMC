<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Erkanaauth
{
  // La librairie utilise la base de données 'xbmc'
  private $_db_group_name = 'xbmc';
  private $_CI;

  function Erkanaauth() {
    $this->_CI =& get_instance();
    log_message('debug', 'Authorization class initialized.');

    // Accès à la base de données 'xbmc'
    $this->_CI->{$this->_db_group_name} = $this->_CI->load->database($this->_db_group_name, TRUE);

    $this->_CI->load->library('session');
  }

  /**
   * Attempt to login using the given condition
   *
   * Accepts an associative array as input, containing login condition
   * Example: $this->auth->try_login(array('email'=>$email, 'password'=>dohash($password)))
   *
   * @access  public
   * @param array login conditions
   * @return  boolean
   */
  function try_login($condition = array())
  {
    $results = $this->_CI->{$this->_db_group_name}->select('*')
                    ->from('users')
                    ->where($condition)
                    ->get()
                    ->result();

    if (count($results) != 1)
    {
      return FALSE;
    }
    else
    {
      // On ne conserve que le premier élément du tableau
      $result = $results[0];
      $data = array();

      // Mise en forme des informations de l'utilisateur
      $data['user_id'] = $result->id;
      unset($result->id);
      unset($result->password);
      $result->can_change_images = ($result->can_change_images == 1) ? TRUE : FALSE;
      $result->can_change_infos = ($result->can_change_infos == 1) ? TRUE : FALSE;
      $result->can_download_video = ($result->can_download_video == 1) ? TRUE : FALSE;
      $result->can_download_music = ($result->can_download_music == 1) ? TRUE : FALSE;
      $result->is_active = ($result->is_active == 1) ? TRUE : FALSE;
      $result->is_admin = ($result->is_admin == 1) ? TRUE : FALSE;

      foreach($result as $key => $value)
      {
        $data[$key] = $value;
      }
      
      // L'utilisateur vient de se connecter, il est sur le site et par sur l'administration
      $data['in_admin'] = FALSE;

      $this->_CI->session->set_userdata($data);
      return TRUE;
    }
  }


  /**
   * Attempt to login using session stored information
   *
   * Example: $this->auth->try_session_login()
   *
   * @access  public
   * @return  boolean
   */
  function try_session_login() {
    if ($this->_CI->session->userdata('user_id')) {
      $query = $this->_CI->{$this->_db_group_name}->query('SELECT COUNT(*) AS total FROM users WHERE id = ' . $this->_CI->session->userdata('user_id'));
      $row = $query->row();
      if ($row->total != 1) {
        // Bad session - kill it
        $this->logout();
        return FALSE;
      } else {
        return TRUE;
      }
    } else {
      return FALSE;
    }
  }


  /**
   * Logs a user out
   *
   * Example: $this->erkanaauth->logout()
   *
   * @access  public
   * @return  void
   */
   function logout()
   {
     $array_items = array('user_id' => '',
                          'username' => '',
                          'is_admin' => '',
                          'can_change_infos' => '',
                          'can_change_images' => '',
                          'can_download_video' => '',
                          'can_download_music' => '',
                          'in_admin' => FALSE
                         );

     $this->_CI->session->unset_userdata($array_items);
   }

 }

 ?>
