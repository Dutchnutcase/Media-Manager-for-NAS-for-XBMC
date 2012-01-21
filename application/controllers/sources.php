<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sources extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->load->model('xbmc/sources_model');
  }

  function index()
  {

    // Si l'utilisateur est un administrateur, on traite sinon on sort
    if ($this->session->userdata('is_admin'))
    {
      $tpl['title'] = $this->lang->line('list_sources');

      // On charge la vue qui contient le corps de la page
      $tpl['file'] = 'xbmc/sources/index';

      ob_start();
      $this->ajax_get_sources();
      $tpl['sources_list'] = ob_get_contents();
      ob_end_clean();

      $this->load->view('includes/template', $tpl);
    }
    else
      redirect('/', 'refresh');
  }

  /**
   * Affiche les informations d'une source via ajax
   */
  public function ajax_edit()
  {
    // Appel via ajax ?
    if (IS_AJAX)
    {
    // Lecture des informations complètes de la source
    $source = $this->sources_model->get((int) $this->uri->segments[3]);

    $tpl['source'] = $source;

    // On charge la page dans le template
    $this->load->view('content/xbmc/sources/_edit_form', $tpl);
    }
  }

  function ajax_save()
  {
    // Appel via ajax ?
    if (IS_AJAX)
    {
      $data = array();
      $data['server_path'] = $this->input->post('server_path');

      // Mise à jour de la source et redirection
      $this->sources_model->edit(intval($this->uri->segments[3]), $data);

      $json = array('success' => '1',
                    'message' => $this->lang->line('msg_source_updated')
                   );

      $json = json_encode($json);

      header('Content-type: application/json');
      echo $json;
    }
  }

  function ajax_get_sources()
  {
    $per_page = 5;

    // Adresse de base pour la pagination
    $base_url = site_url('sources/page');
    $data['title'] = $this->lang->line('list_sources');

    // Récupération des utilisateurs
    $data['sources'] = $this->sources_model->get_all($per_page, intval($this->uri->segment(3)));

    // Total des utilisateurs pour la pagination
    $total = $this->sources_model->count_all();

    // Paramètrage de la pagination
    // Le 3ème segment contient le numéro de la page
    $config['base_url'] = $base_url;
    $config['total_rows'] = $total;
    $config['per_page'] = $per_page;
    $config['uri_segment'] = '3';

    $config['first_link'] = $this->lang->line('pagination_first_link');
    $config['prev_link'] = $this->lang->line('pagination_prev_link');
    $config['next_link'] = $this->lang->line('pagination_next_link');
    $config['last_link'] = $this->lang->line('pagination_last_link');
    $config['num_links'] = 5;

    $this->my_pagination->initialize($config);

    // On charge la page dans le template
    $this->load->view('content/xbmc/sources/_rows', $data);
  }

}

/* End of file sources.php */
/* Location: ./application/controllers/sources.php */
