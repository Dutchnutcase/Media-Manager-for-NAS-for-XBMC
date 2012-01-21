<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Images extends CI_Controller
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
   * Change l'image d'un film, une série TV, une saga ou une personne
   * après sélection de cette image
   * Gère les affiches, bannières, photos et fond d'écran
   */
	public function ajax_change_image()
	{

    // Appel via ajax ?
    if (IS_AJAX)
    {

			// Contrôleur_idenfiant dans le champs 'media_id'
			list($media, $id) = explode('_', $this->input->post('media_id'));

			// Type d'image (poster, banner, photo, backdrop)
			$type = $_POST['type'];
			
			// Url de l'image à télécherger
			$image_url = $_POST['image_url'];

			// Nom et chemin de l'image à changer sur le serveur
			// Les variables 'poster_filename', 'banner_filename', 'backdrop_filename' sont définies
			// en javascript selon le type de 'media' consulté
			$image_filename = $_POST[$type.'_filename'];

//echo '<pre>'.print_r($_POST, true).'</pre>'; die();

			// Chemin d'une éventuelle image générée automatiquement par xbmc pour la supprimer
			$file_filename = substr($image_filename, strrpos($image_filename, "/")+1);
			$file_auto = str_replace($file_filename, 'auto-'.$file_filename, $image_filename);
			if (file_exists($file_auto)) unlink($file_auto);

			// Téléchargement de l'image et sauvegarde au bon endroit
			$this->xbmc->download($image_url, $image_filename);
			sleep(1);

			switch($type)
			{
				case 'poster';
					$thumbnail_filename = $this->xbmc->images_cache_dir.'xbmc/Video/media/p_'.str_replace('.tbn', '.jpg', substr($image_filename, strrpos($image_filename, '/')+1));

					// Mise à jour de la miniature
					$this->xbmc->create_image($image_filename, $thumbnail_filename, $this->xbmc->poster_size, TRUE);
					break;

				case 'banner';
					$thumbnail_filename = $this->xbmc->images_cache_dir.'xbmc/Video/media/b_'.str_replace('.tbn', '.jpg', substr($image_filename, strrpos($image_filename, '/')+1));

					// Mise à jour de la miniature
					$this->xbmc->create_image($image_filename, $thumbnail_filename, $this->xbmc->banner_size, TRUE);
					break;

				case 'backdrop';
					$thumbnail_filename = $this->xbmc->images_cache_dir.'xbmc/Video/Fanart/b_'.str_replace('.tbn', '.jpg', substr($image_filename, strrpos($image_filename, '/')+1));

					// Mise à jour de la miniature
					$this->xbmc->create_image($image_filename, $thumbnail_filename, $this->xbmc->backdrop_size, TRUE);
					break;
			}

			// Url de l'image miniature sur le site
			$url = str_replace($this->xbmc->images_cache_dir, $this->xbmc->images_cache_url, $thumbnail_filename).'?'.time();

//echo $url; die();

			switch($media)
			{
				case 'movies':
					$json = array('success' => '1',
												'url' => $url,
												'message' => $this->lang->line('msg_'.$type.'_movie_updated')
											 );
					break;

				case 'sets':
					$json = array('success' => '1',
												'url' => $url,
												'message' => $this->lang->line('msg_'.$type.'_set_updated')
											 );
					break;
					
				case 'tvshows':
					$json = array('success' => '1',
												'url' => $url,
												'message' => $this->lang->line('msg_'.$type.'_tvshow_updated')
											 );
					break;
			}

			$json = json_encode($json);

			header('Content-type: application/json');
			echo $json;
			die();
		}
	}

  /**
   * Ajoute une nouvelle image à un film, une série TV, une saga ou une personne
   * Gère les affiches, bannières, photos et fond d'écran
   */
	public function ajax_upload_image()
	{
		$type = $this->input->post('type');
		$image_filename = $this->input->post('image_filename');

		// Chemin d'une éventuelle image générée automatiquement pour suppression
		$file_filename = substr($image_filename, strrpos($image_filename, "/")+1);
		$file_auto = str_replace($file_filename, 'auto-'.$file_filename, $image_filename);
		if (file_exists($file_auto)) unlink($file_auto);

		// Traitement de l'iamge téléchargée
		// Prévoir gestion des erreurs
		 $this->_upload($image_filename);

		// Url de l'image pour mise à jour
		$url = str_replace($this->config->item('xbmc_thumbnails_dir'), base_url().'assets/images/', $image_filename);
		$url = str_replace('\\', '/', $url);

    echo '<script type="text/javascript">
    <!--
			var data = {
				success: "1",
        type: "'.$type.'",
        url: "'.$url.'",
        message: "Toto fait du vélo."
			};

      window.top.window.UploadImageEnd(data);
    //-->
    </script>';
	}

	private function _upload($target)
	{
		$extensions = array('png', 'jpg', 'jpeg');

		// Fichier correctement uploadé
		if (!isset($_FILES['image']) OR $_FILES['image']['error'] > 0) return FALSE;

		// Extension
		$ext = substr(strrchr($_FILES['image']['name'],'.'),1);
		if ($extensions !== FALSE AND !in_array($ext, $extensions)) return FALSE;

		// Déplacement
		return move_uploaded_file($_FILES['image']['tmp_name'], $target);
	}

}

/* End of file xbmc.php */
/* Location: ./application/controllers/xbmc.php */
