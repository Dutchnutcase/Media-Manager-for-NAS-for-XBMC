<?php

class Files extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    // Chargement des modèles de la base de données 'xbmc_video'
    $this->load->model('video/actors_model');
    $this->load->model('video/countries_model');
    $this->load->model('video/episodes_model');
    $this->load->model('video/video_files_model');
    $this->load->model('video/genres_model');
    $this->load->model('video/movies_model');
    $this->load->model('video/video_paths_model');
    $this->load->model('xbmc/sources_model');
    $this->load->model('video/sets_model');
    $this->load->model('video/studios_model');
    $this->load->model('video/tvshows_model');
  }

  /**
   * Demande à télécharger un film ou un épisode
   */
  public function ajax_download()
  {
		// L'utilisateur connecté peut télécharger des vidéos ?
		if ($this->session->userdata('can_download_video'))
		{
      // Champ concerné par la modification
      list($type, $id) = explode('_', $this->uri->segments[3]);

			switch($type)
			{
				case 'movie':
					$files = $this->movies_model->get($id, FALSE);
					$filename = $files[0]->filename;
					$path = str_replace($files[0]->source->client_path, $files[0]->source->server_path, $files[0]->path);
					break;

				case 'episode':
					$files = $this->episodes_model->get($id, FALSE);
					$filename = $files[0]->filename;
					$path = str_replace($files[0]->source->client_path, $files[0]->source->server_path, $files[0]->path);
					break;
			}

			// Chargement de la focntion 'force_download'
			$this->load->helper('MY_download');

			// Force le téléchargement du fichier
			force_download($filename, $path.$filename);
		}

		// Sortie avec téléchargement de fichier ou pas
		die();
  }

}

/* End of file files.php */
/* Location: ./system/application/controllers/files.php */
