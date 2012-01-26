<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Xbmc_Lib
{
  private $_CI;
  private $_base_url;
  private $_thumbnails_dir;
  private $_thumbnails_url;

  public $images_cache_dir;
  public $images_cache_url;

  public $poster_size;
  public $banner_size;
  public $backdrop_size;
  public $photo_size;
  public $episode_size;

  // Pour déterminer les chemins des posters de saison
  private $_poster_all_seasons;
  private $_poster_special_season;
  private $_poster_season;

  public $user_agent;

  public $sources = array();
  public $scrapers = array();

  public $video_extensions = array();
  public $movie_stacking = array();
  public $clean_date_time = '';
  public $clean_strings = array();
  public $tvshow_matching = array();
  public $tv_multipart_matching = '';
  public $trailer_matching = array();
  public $exclude_from_scan = array();
  public $exclude_tvshows_from_scan = array();
  public $fanart = array();

  public function __construct()
  {
    // Instance de CodeIgniter pour accéder à sa configuration
    $this->_CI =& get_instance();

    $this->_base_url = $this->_CI->config->item('base_url');
    $this->_thumbnails_dir = $this->_CI->config->item('thumbnails_dir');
    $this->_thumbnails_url = base_url().'assets/images/';

    $this->images_cache_dir = FCPATH.'assets/images_cache/';
    $this->images_cache_url = base_url().'assets/images_cache/';

    $this->poster_size = $this->_CI->config->item('poster_size');
    $this->banner_size = $this->_CI->config->item('banner_size');
    $this->backdrop_size = $this->_CI->config->item('backdrop_size');
    $this->photo_size = $this->_CI->config->item('photo_size');
    $this->episode_size = $this->_CI->config->item('episode_size');

    // user agent du navigateur de l'utilisateur navigant sur le site
    $this->user_agent = $this->_CI->agent->agent_string();

    // user agent simulé si utilisation en ligne de commandes
    if (!$this->_CI->agent->is_browser()) $this->user_agent = $this->_CI->config->item('xbmc_fake_user_agent');

    // Pour déterminer les chemins des posters de saison
    $this->_poster_all_seasons = $this->_CI->lang->line('xbmc_poster_all_seasons');
    $this->_poster_special_season = $this->_CI->lang->line('xbmc_poster_special_season');
    $this->_poster_season = $this->_CI->lang->line('xbmc_poster_season');

    log_message('debug', "MY_xbmc Class Initialized");

    // Chargement des modèles de la base de données 'xbmc_video'
    $this->_CI->load->model('xbmc/sources_model');

    // Sources de la base de données 'xbmc'
    $this->sources = $this->_CI->sources_model->get_all();

    // Récupère les noms des différents scrapers présents
    $this->scrapers = $this->_get_scrapers();

    // Etablie la configuration pour le gestionnaire de média
    $this->_init_config();
  }

  /**
   * Récupère les noms des différents scrapers présents
   *
   * @access private
   * @param void
   * @return array
   */
  private function _get_scrapers()
  {
    $scrapers_folder = pathinfo(__FILE__, PATHINFO_DIRNAME).'/scrapers/';
    $matches = array();
    $folders = array(rtrim($scrapers_folder, DIRECTORY_SEPARATOR));

    while($folder = array_shift($folders)) {
        $matches = array_merge($matches, glob($folder.DIRECTORY_SEPARATOR.'*.php'));
        $moreFolders = glob($folder.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);
        $folders = array_merge($folders, $moreFolders);
    }
    foreach($matches as $match)
    {
      $type = str_replace($scrapers_folder, '', pathinfo($match, PATHINFO_DIRNAME));
      $type = str_replace(DIRECTORY_SEPARATOR, '', $type);
      $scrapers[pathinfo($match, PATHINFO_FILENAME)] = $type;
    }

    return $scrapers;
  }

  /**
   * Cherche le scraper d'un dossier ou d'un éventuel dossier parent
   * en précisant le type de contenu concerné
   *
   * @access public
   * @param string
   * @param string
   * @return string
   */
  public function get_scraper($path)
  {
    $scraper = new stdClass();

    foreach($this->sources as $source)
    {
      if (strrpos($path, $source->client_path) !== FALSE)
      {
        $scraper->class = $source->scraper;
        $scraper->settings = $source->settings;
        break;
      }
    }

    return $scraper;
  }

  /**
   * Génére une image à partir de $source, la sauvegarde dans $destination
   * en précisant la taille $size
   *
   * On peut demander à recréer la miniature si $update vaut TRUE
   *
   * @access public
   * @param string
   * @param string
   * @param array
   * @param bool
   * @return void
   */
	public function create_image($source, $destination, $size, $update = FALSE)
	{
		// Force la mise à jour de l'image de destination si on a modifié l'image source entre temps via XBMC
		if (file_exists($destination))
				if (filemtime($source) > filemtime($destination))
						$update = TRUE;

		// Fichier absent sur le disque ou mise à jour ?
		if (!file_exists($destination) || $update)
		{
			list($width, $height, $type) = getimagesize($source);

			// jpg ?
			if ($type == IMAGETYPE_JPEG) $src = imagecreatefromjpeg($source);

			// png ?
			if ($type == IMAGETYPE_PNG) $src = imagecreatefrompng($source);

			$image = imagecreatetruecolor($size['width'], $size['height']);
			imagecopyresampled($image, $src, 0, 0, 0, 0, $size['width'], $size['height'], $width, $height);

			imagejpeg($image, $destination);

			// Libération de la mémoire
			imagedestroy($image);
		}
	}

  /**
   * Etablie la configuration pour le gestionnaire de média
   * Complète la configuration par défaut par celle établie lors du traitement
   * d'un éventuel fichier 'advancedsettings.xml' préalablement chargé sur le
   * serveur
   *
   * @access private
   * @return void
   */
  private function _init_config()
  {
    // Inclusion des paramètres par défaut
    include(APPPATH.'config/default'.EXT);

    // Inclusion des paramètres fournis par l'utilisateur
    if (is_file(APPPATH.'config/library'.EXT))
    {
      include(APPPATH.'config/library'.EXT);
    }

    //  Valeurs par défaut
    $this->video_extensions = $video_extensions;
    $this->movie_stacking = $movie_stacking;
    $this->clean_date_time = $clean_date_time;
    $this->clean_strings = $clean_strings;
    $this->tvshow_matching = $tvshow_matching;
    $this->tv_multipart_matching = $tv_multipart_matching;
    $this->trailer_matching = $trailer_matching;
    $this->exclude_from_scan = $exclude_from_scan;
    $this->exclude_tvshows_from_scan = $exclude_tvshows_from_scan;
    $this->fanart = $fanart;

    // Extensions additionnelles pour les fichiers vidéo ?
    if (isset($video_extensions_added))
    {
      $this->video_extensions = array_merge($this->video_extensions, $video_extensions_added);
      unset($video_extensions_added);
    }

    // Extensions à ne pas prendre en compte pour les fichiers vidéo ?
    if (isset($video_extensions_removed))
    {
      $this->video_extensions = array_diff($this->video_extensions, $video_extensions_removed);
      unset($video_extensions_removed);
    }

    // Tableau à mettre avant ?
    if (isset($movie_stacking_before))
    {
      $a = array_merge($movie_stacking_before, $this->movie_stacking);
      $this->movie_stacking = $a;
      unset($a);
      unset($movie_stacking_before);
    }

    // Tableau à mettre après ?
    if (isset($movie_stacking_after))
    {
      $a = array_merge($this->movie_stacking, $movie_stacking_after);
      $this->movie_stacking = $a;
      unset($a);
      unset($movie_stacking_after);
    }

    // Tableau à mettre avant ?
    if (isset($clean_strings_before))
    {
      $a = array_merge($clean_strings_before, $this->clean_strings);
      $this->clean_strings = $a;
      unset($a);
      unset($clean_strings_before);
    }

    // Tableau à mettre après ?
    if (isset($clean_strings_after))
    {
      $a = array_merge($this->clean_strings, $clean_strings_after);
      $this->clean_strings = $a;
      unset($a);
      unset($clean_strings_after);
    }

    // Tableau à mettre avant ?
    if (isset($tvshow_matching_before))
    {
      $a = array_merge($tvshow_matching_before, $this->tvshow_matching);
      $this->tvshow_matching = $a;
      unset($a);
      unset($tvshow_matching_before);
    }

    // Tableau à mettre après ?
    if (isset($tvshow_matching_after))
    {
      $a = array_merge($this->tvshow_matching, $tvshow_matching_after);
      $this->tvshow_matching = $a;
      unset($a);
      unset($tvshow_matching_after);
    }

    // Tableau à mettre avant ?
    if (isset($exclude_from_scan_before))
    {
      $a = array_merge($exclude_from_scan_before, $this->exclude_from_scan);
      $this->exclude_from_scan = $a;
      unset($a);
      unset($exclude_from_scan_before);
    }

    // Tableau à mettre après ?
    if (isset($exclude_from_scan_after))
    {
      $a = array_merge($this->exclude_from_scan, $exclude_from_scan_after);
      $this->exclude_from_scan = $a;
      unset($a);
      unset($exclude_from_scan_after);
    }

    // Tableau à mettre avant ?
    if (isset($exclude_tvshows_from_scan_before))
    {
      $a = array_merge($exclude_tvshows_from_scan_before, $this->exclude_tvshows_from_scan);
      $this->exclude_tvshows_from_scan = $a;
      unset($a);
      unset($exclude_tvshows_from_scan_before);
    }

    // Tableau à mettre après ?
    if (isset($exclude_tvshows_from_scan_after))
    {
      $a = array_merge($this->exclude_tvshows_from_scan, $exclude_tvshows_from_scan_after);
      $this->exclude_tvshows_from_scan = $a;
      unset($a);
      unset($exclude_tvshows_from_scan_after);
    }

    // Noms additionnels pour les fanart ?
    if (isset($fanart_added))
    {
      $this->fanart = array_merge($this->fanart, $fanart_added);
      unset($fanart_added);
    }

    // Noms à ne pas prendre en compte pour les fanart ?
    if (isset($fanart_removed))
    {
      $this->fanart = array_diff($this->fanart, $fanart_removed);
      unset($fanart_removed);
    }

    unset($video_extensions);
    unset($movie_stacking);
    unset($clean_date_time);
    unset($clean_strings);
    unset($tvshow_matching);
    unset($tv_multipart_matching);;
    unset($trailer_matching);
    unset($exclude_from_scan);
    unset($exclude_tvshows_from_scan);
    unset($fanart);

//    echo '<pre>'.print_r($this, true).'</pre>';
//    die();
  }

  /**
   * Ecrit le fichier de configuration pour le gestionnaire de média
   * Analyse et traite un fichier 'advancedsettings.xml' préalablement chargé
   * sur le serveur
   *
   * @access public
   * @return void
   */
  public function make_library_config()
  {
    // Pas de limite de temps pour le script
    set_time_limit(0);

    if (is_uploaded_file($_FILES['advancedsettings']['tmp_name']))
    {
      $xml = simplexml_load_file($_FILES['advancedsettings']['tmp_name']);

      $library_config = "<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');";

      $video_extensions_added = array();
      $video_extensions_removed = array();

      if (isset($xml->videoextensions))
      {
        if (isset($xml->videoextensions->add))
        {
          $to_add = explode('|', $xml->videoextensions->add);
          foreach($to_add as $added)
          {
            $video_extensions_added[] = $added;
          }
        }

        if (isset($xml->videoextensions->remove))
        {
          $to_remove = explode('|', $xml->videoextensions->remove);
          foreach($to_remove as $removed)
          {
            $video_extensions_removed[] = $removed;
          }
        }
      }

      $library_config .= "\n\n// Video file extensions\n// Do NOT change";
      if (count($video_extensions_added) > 0)
      {
        foreach($video_extensions_added as $extension)
        {
          $library_config .= "\n".'$video_extensions_added[] = "'.str_replace('.', '', $extension).'";';
        }
      }
      else
      {
        $library_config .= "\n".'$video_extensions_added = array();';
      }

      if (count($video_extensions_removed) > 0)
      {
        foreach($video_extensions_removed as $extension)
        {
          $library_config .= "\n".'$video_extensions_removed[] = "'.str_replace('.', '', $extension).'";';
        }
      }
      else
      {
        $library_config .= "\n".'$video_extensions_removed = array();';
      }

      $movie_stacking_before = array();
      $movie_stacking_after = array();

      foreach($xml->moviestacking as $movie_stacking)
      {
        if (isset($movie_stacking['action']))
        {
          if ($movie_stacking['action'] == 'append')
          {
            foreach($movie_stacking->regexp as $regexp)
            {
              $movie_stacking_after[] = (string) $regexp;
            }
          }

          if ($movie_stacking['action'] == 'prepend')
          {
            foreach($movie_stacking->regexp as $regexp)
            {
              $movie_stacking_before[] = (string) $regexp;
            }
          }
        }

        if (isset($movie_stacking['append']))
        {
          if ($movie_stacking['append'] == 'yes')
          {
            foreach($movie_stacking->regexp as $regexp)
            {
              $movie_stacking_after[] = (string) $regexp;
            }
          }
        }

      }

      $library_config .= "\n\n// Movie stacking\n// Do NOT change";
      if (count($movie_stacking_before) > 0)
      {
        foreach($movie_stacking_before as $movie_stacking)
        {
          $library_config .= "\n".'$movie_stacking_before[] = "'.$movie_stacking.'";';
        }
      }
      else
      {
        $library_config .= "\n".'$movie_stacking_before = array();';
      }

      if (count($movie_stacking_after) > 0)
      {
        foreach($movie_stacking_after as $movie_stacking)
        {
          $library_config .= "\n".'$movie_stacking_after[] = "'.$movie_stacking.'";';
        }
      }
      else
      {
        $library_config .= "\n".'$movie_stacking_after = array();';
      }

      foreach($xml->video as $video)
      {
        if (isset($video->cleandate))
        {
          $library_config .= "\n\n// Clean date\n// Do NOT change";
          $library_config .= "\n".'$clean_date_time = "'.(string) $video->cleandate.'";';
        }
      }

      $clean_strings_before = array();
      $clean_strings_after = array();

      foreach($xml->video as $video)
      {
        foreach($video->cleanstrings as $clean_string)
        {
          if (isset($clean_string['action']))
          {
            if ($clean_string['action'] == 'append')
            {
              foreach($clean_string->regexp as $regexp)
              {
                $clean_strings_after[] = (string) $regexp;
              }
            }

            if ($clean_string['action'] == 'prepend')
            {
              foreach($clean_string->regexp as $regexp)
              {
                $clean_strings_before[] = (string) $regexp;
              }
            }
          }

          if (isset($clean_string['append']))
          {
            if ($clean_string['append'] == 'yes')
            {
              foreach($clean_string->regexp as $regexp)
              {
                $clean_strings_after[] = (string) $regexp;
              }
            }
          }

        }
      }

      $library_config .= "\n\n// Clean strings\n// Do NOT change";
      if (count($clean_strings_before) > 0)
      {
        foreach($clean_strings_before as $clean_string)
        {
          $library_config .= "\n".'$clean_strings_before[] = "'.$clean_string.'";';
        }
      }
      else
      {
        $library_config .= "\n".'$clean_strings_before = array();';
      }

      if (count($clean_strings_after) > 0)
      {
        foreach($clean_strings_after as $clean_string)
        {
          $library_config .= "\n".'$clean_strings_after[] = "'.$clean_string.'";';
        }
      }
      else
      {
        $library_config .= "\n".'$clean_strings_after = array();';
      }

      $tvshow_matching_before = array();
      $tvshow_matching_after = array();

      foreach($xml->tvshowmatching as $tvshow_matching)
      {
        if (isset($tvshow_matching['action']))
        {
          if ($tvshow_matching['action'] == 'append')
          {
            foreach($tvshow_matching->regexp as $regexp)
            {
              $tvshow_matching_after[] = (string) $regexp;
            }
          }

          if ($tvshow_matching['action'] == 'prepend')
          {
            foreach($tvshow_matching->regexp as $regexp)
            {
              $tvshow_matching_before[] = (string) $regexp;
            }
          }
        }

        if (isset($tvshow_matching['append']))
        {
          if ($tvshow_matching['append'] == 'yes')
          {
            foreach($tvshow_matching->regexp as $regexp)
            {
              $tvshow_matching_after[] = (string) $regexp;
            }
          }
        }

      }

      $library_config .= "\n\n// Tvshow matching\n// Do NOT change";
      if (count($tvshow_matching_before) > 0)
      {
        foreach($tvshow_matching_before as $tvshow_matching)
        {
          $library_config .= "\n".'$tvshow_matching_before[] = "'.$tvshow_matching.'";';
        }
      }
      else
      {
        $library_config .= "\n".'$tvshow_matching_before = array();';
      }

      if (count($tvshow_matching_after) > 0)
      {
        foreach($tvshow_matching_after as $tvshow_matching)
        {
          $library_config .= "\n".'$tvshow_matching_after[] = "'.$tvshow_matching.'";';
        }
      }
      else
      {
        $library_config .= "\n".'$tvshow_matching_after = array();';
      }

      if (isset($xml->tvmultipartmatching))
      {
        $library_config .= "\n\n// Tv multipart matching\n// Do NOT change";
        $library_config .= "\n".'$tv_multipart_matching = "'.(string) $xml->tvmultipartmatching.'";';
      }

      $exclude_from_scan_before = array();
      $exclude_from_scan_after = array();

      foreach($xml->video as $video)
      {
        foreach($video->excludefromscan as $exclude_from_scan)
        {
          if (isset($exclude_from_scan['action']))
          {
            if ($exclude_from_scan['action'] == 'append')
            {
              foreach($exclude_from_scan->regexp as $regexp)
              {
                $exclude_from_scan_after[] = (string) $regexp;
              }
            }

            if ($exclude_from_scan['action'] == 'prepend')
            {
              foreach($exclude_from_scan->regexp as $regexp)
              {
                $exclude_from_scan_before[] = (string) $regexp;
              }
            }
          }

          if (isset($exclude_from_scan['append']))
          {
            if ($exclude_from_scan['append'] == 'yes')
            {
              foreach($exclude_from_scan->regexp as $regexp)
              {
                $exclude_from_scan_after[] = (string) $regexp;
              }
            }
          }

        }
      }

      $library_config .= "\n\n// Exclude from scan\n// Do NOT change";
      if (count($exclude_from_scan_before) > 0)
      {
        foreach($exclude_from_scan_before as $exclude_from_scan)
        {
          $library_config .= "\n".'$exclude_from_scan_before[] = "'.$exclude_from_scan.'";';
        }
      }
      else
      {
        $library_config .= "\n".'$exclude_from_scan_before = array();';
      }

      if (count($exclude_from_scan_after) > 0)
      {
        foreach($exclude_from_scan_after as $exclude_from_scan)
        {
          $library_config .= "\n".'$exclude_from_scan_after[] = "'.$exclude_from_scan.'";';
        }
      }
      else
      {
        $library_config .= "\n".'$exclude_from_scan_after = array();';
      }

      $exclude_tvshows_from_scan_before = array();
      $exclude_tvshows_from_scan_after = array();

      foreach($xml->video as $video)
      {
        foreach($video->excludetvshowsfromscan as $exclude_tvshows_from_scan)
        {
          if (isset($exclude_tvshows_from_scan['action']))
          {
            if ($exclude_tvshows_from_scan['action'] == 'append')
            {
              foreach($exclude_tvshows_from_scan->regexp as $regexp)
              {
                $exclude_tvshows_from_scan_after[] = (string) $regexp;
              }
            }

            if ($exclude_tvshows_from_scan['action'] == 'prepend')
            {
              foreach($exclude_tvshows_from_scan->regexp as $regexp)
              {
                $exclude_tvshows_from_scan_before[] = (string) $regexp;
              }
            }
          }

          if (isset($exclude_tvshows_from_scan['append']))
          {
            if ($exclude_tvshows_from_scan['append'] == 'yes')
            {
              foreach($exclude_tvshows_from_scan->regexp as $regexp)
              {
                $exclude_tvshows_from_scan_after[] = (string) $regexp;
              }
            }
          }

        }
      }

      $library_config .= "\n\n// Exclude tvshows from scan\n// Do NOT change";
      if (count($exclude_tvshows_from_scan_before) > 0)
      {
        foreach($exclude_tvshows_from_scan_before as $exclude_tvshows_from_scan)
        {
          $library_config .= "\n".'$exclude_tvshows_from_scan_before[] = "'.$exclude_tvshows_from_scan.'";';
        }
      }
      else
      {
        $library_config .= "\n".'$exclude_tvshows_from_scan_before = array();';
      }

      if (count($exclude_tvshows_from_scan_after) > 0)
      {
        foreach($exclude_tvshows_from_scan_after as $exclude_tvshows_from_scan)
        {
          $library_config .= "\n".'$exclude_tvshows_from_scan_after[] = "'.$exclude_tvshows_from_scan.'";';
        }
      }
      else
      {
        $library_config .= "\n".'$exclude_tvshows_from_scan_after = array();';
      }

      $fanart_added = array();
      $fanart_removed = array();

      if (isset($xml->fanart))
      {
        if (isset($xml->fanart->add))
        {
          $to_add = explode('|', $xml->fanart->add);
          foreach($to_add as $added)
          {
            $fanart_added[] = $added;
          }
        }

        if (isset($xml->fanart->remove))
        {
          $to_remove = explode('|', $xml->fanart->remove);
          foreach($to_remove as $removed)
          {
            $fanart_removed[] = $removed;
          }
        }
      }

      $library_config .= "\n\n// Fanart\n// Do NOT change";
      if (count($fanart_added) > 0)
      {
        foreach($fanart_added as $fanart)
        {
          $library_config .= "\n".'$fanart_added[] = "'.$fanart.'";';
        }
      }
      else
      {
        $library_config .= "\n".'$fanart_added = array();';
      }

      if (count($fanart_removed) > 0)
      {
        foreach($fanart_removed as $fanart)
        {
          $library_config .= "\n".'$fanart_removed[] = "'.$fanart.'";';
        }
      }
      else
      {
        $library_config .= "\n".'$fanart_removed = array();';
      }

      $library_config .= "\n\n/* End of file library.php */\n";
      $library_config .= "/* Location: ./application/config/library.php */";

      $filename = realpath(str_replace('libraries','config', dirname(__FILE__))).'/library.php';

      $handle = fopen($filename, 'w');
      fwrite($handle, $library_config);
      fclose($handle);

      // Etablie la configuration pour le gestionnaire de média
      $this->_init_config();
    }
  }

  /**
   * Retourne l'affiche d'un film pour un objet 'movie'
   *
   * @access public
   * @param object
   * @return object
   */
  public function get_movie_poster($movie)
  {
    // Utilisation des dossiers comme titre de film ?
    if ($movie->source->settings->user_folder_name)
    {
      $file_path = $movie->source->client_path;
    }
    else
    {
      $file_path = $movie->path.$movie->filename;
      $file_path = str_replace($movie->source->server_path, $movie->source->client_path, $file_path);

      // Cas des empilements de parties de film
      if (strpos($movie->filename, 'stack://') !== false)
      {
        $file_path = str_replace('stack://', '', $movie->filename);
        $file_path = explode(' , ', $file_path);
        $file_path = $file_path[0];
      }
    }

    $poster = new stdClass();

    // Calcul du hashage du fichier
    $thumbnail = $this->_get_hash($file_path);

    // Emplacement présumé du fichier pour xbmc
    $image_filename = $this->_thumbnails_dir.'Video/'.substr($thumbnail, 0, 1).'/'.$thumbnail.'.tbn';

		// Emplacement de la miniature correspondante
		$thumbnail_filename = $this->images_cache_dir.'xbmc/Video/media/p_'.$thumbnail.'.jpg';

		// Emplacement du fichier
		$poster->filename = $image_filename;

		// Fichier présent sur le disque ?
		if (file_exists($image_filename))
		{
			// Création de la miniature de l'affiche
			$this->create_image($image_filename, $thumbnail_filename, $this->poster_size);
			$poster->url = $this->images_cache_url.'xbmc/Video/media/p_'.$thumbnail.'.jpg';
		}
		else
		{
			$poster->url = base_url().'assets/gui/DefaultVideoPoster.png';
		}

    return $poster;
  }

  /**
   * Retourne le fond d'écran d'un film pour un objet 'movie'
   *
   * @access public
   * @param object
   * @return object
   */
  public function get_movie_backdrop($movie)
  {
    // Utilisation des dossiers comme titre de film ?
    if ($movie->source->settings->user_folder_name)
    {
      $file_path = $movie->filename;
    }
    else
    {
      $file_path = $movie->path.$movie->filename;
      $file_path = str_replace($movie->source->server_path, $movie->source->client_path, $file_path);

      // Cas des empilements de parties de film
      if (strpos($movie->filename, 'stack://') !== false)
          $file_path = $movie->filename;
    }

    $backdrop = new stdClass();

    // Calcul du hashage du fichier
    $thumbnail = $this->_get_hash($file_path);

    // Emplacement présumé du fichier pour xbmc
    $image_filename = $this->_thumbnails_dir.'Video/Fanart/'.$thumbnail.'.tbn';

		// Emplacement de la miniature correspondante
		$thumbnail_filename = $this->images_cache_dir.'xbmc/Video/Fanart/b_'.$thumbnail.'.jpg';

		// Emplacement du fichier
		$backdrop->filename = $image_filename;

    // Fichier présent sur le disque ?
		if (file_exists($image_filename))
    {
			// Création de la miniature du fond d'écran
			$this->create_image($image_filename, $thumbnail_filename, $this->backdrop_size);
      $backdrop->url = $this->images_cache_url.'xbmc/Video/Fanart/b_'.$thumbnail.'.jpg';
    }
    else
    {
      $backdrop->url = base_url().'assets/gui/DefaultVideoBackdrop.png';
    }

    return $backdrop;
  }

  /**
   * Retourne l'affiche d'une saga de films dont on précise l'idenfitiant
   *
   * @access public
   * @param integer
   * @return object
   */
  public function get_set_poster($set_id)
  {
		$file_path = 'videodb://1/7/'.intval($set_id).'/';

    $poster = new stdClass();

    // Calcul du hashage du fichier
    $thumbnail = $this->_get_hash($file_path);

    // Emplacement présumé du fichier pour xbmc
    $image_filename = $this->_thumbnails_dir.'Video/'.substr($thumbnail, 0, 1).'/'.$thumbnail.'.tbn';

		// Emplacement de la miniature correspondante
		$thumbnail_filename = $this->images_cache_dir.'xbmc/Video/media/p_'.$thumbnail.'.jpg';

		// Emplacement du fichier
		$poster->filename = $image_filename;

		// Fichier présent sur le disque ?
		if (file_exists($image_filename))
		{
			// Création de la miniature de l'affiche
			$this->create_image($image_filename, $thumbnail_filename, $this->poster_size);
			$poster->url = $this->images_cache_url.'xbmc/Video/media/p_'.$thumbnail.'.jpg';
    }
    else
    {
      $poster->url = base_url().'assets/gui/DefaultVideoPoster.png';
    }

    return $poster;
  }

  /**
   * Retourne le fond d'écran d'une saga de films dont on précise l'idenfitiant
   *
   * @access public
   * @param integer
   * @return object
   */
  public function get_set_backdrop($set_id)
  {
		$file_path = 'videodb://1/7/'.intval($set_id).'/';

    $backdrop = new stdClass();

    // Calcul du hashage du fichier
    $thumbnail = $this->_get_hash($file_path);

    // Emplacement présumé du fichier pour xbmc
    $image_filename = $this->_thumbnails_dir.'Video/Fanart/'.$thumbnail.'.tbn';

		// Emplacement de la miniature correspondante
		$thumbnail_filename = $this->images_cache_dir.'xbmc/Video/Fanart/b_'.$thumbnail.'.jpg';

		// Emplacement du fichier
		$backdrop->filename = $image_filename;

    // Fichier présent sur le disque ?
		if (file_exists($image_filename))
    {
			// Création de la miniature du fond d'écran
			$this->create_image($image_filename, $thumbnail_filename, $this->backdrop_size);
      $backdrop->url = $this->images_cache_url.'xbmc/Video/Fanart/b_'.$thumbnail.'.jpg';
    }
    else
    {
      $backdrop->url = base_url().'assets/gui/DefaultVideoBackdrop.png';
    }

    return $backdrop;
  }

  /**
   * Retourne l'affiche d'un clip pour un fichier
   *
   * @access public
   * @param string
   * @return object
   */
  public function get_musicvideo_poster($file_path)
  {
    // Même fonction que pour les films mais sait-on jamais...
    return $this->get_movie_poster($file_path);
  }

  /**
   * Retourne le fond d'écran d'un clip pour un fichier
   *
   * @access public
   * @param string
   * @return object
   */
  public function get_musicvideo_backdrop($file_path)
  {
    // Même fonction que pour les films mais sait-on jamais...
    return $this->get_movie_backdrop($file_path);
  }

  /**
   * Retourne la photo d'une personne
   *
   * @access public
   * @param string
   * @return object
   */
  function get_actor_photo($people_name)
  {
    $photo = new stdClass();

    // Calcul du hashage du fichier
    $thumbnail = $this->_get_hash('actor'.strtolower($people_name));

    // Emplacement présumé du fichier pour xbmc
    $image_filename = $this->_thumbnails_dir.'Video/'.substr($thumbnail, 0, 1).'/'.$thumbnail.'.tbn';

		// Emplacement de la miniature correspondante
		$thumbnail_filename = $this->images_cache_dir.'xbmc/Video/media/a_'.$thumbnail.'.jpg';

		// Emplacement du fichier
		$photo->filename = $image_filename;

		// Fichier présent sur le disque ?
		if (file_exists($image_filename))
		{
			// Création de la miniature de l'affiche
			$this->create_image($image_filename, $thumbnail_filename, $this->photo_size);
			$photo->url = $this->images_cache_url.'xbmc/Video/media/a_'.$thumbnail.'.jpg';
    }
    else
    {
      $photo->url = base_url().'assets/gui/DefaultActorPhoto.png';
    }

    return $photo;
  }

  /**
   * Retourne l'affiche principale d'une série TV
   *
   * Retourne l'affiche d'une saison si $idSeason est non nul
   *
   * @access public
   * @param object
   * @return object
   */
  public function get_tvshow_poster($tvshow, $idSeason = NULL)
  {
//    echo '<pre>'.print_r($tvshow, true).'</pre>';

    $poster = new stdClass();

		// Affiche d'une saison particulière ?
		if (!is_null($idSeason))
		{
			// * Toutes les saisons
			if (intval($idSeason) == -1)
					$path = 'season'.$tvshow->path.$this->_poster_all_seasons;

			// Spéciales
			if (intval($idSeason) == 0)
					$path = 'season'.$tvshow->path.$this->_poster_special_season;

			// Saison $idSeason
			if (intval($idSeason) > 0)
					$path = 'season'.$tvshow->path.$this->_poster_season.' '.$idSeason;

			// Pour l'affiche d'une saison
			$type = 'poster';
		}
		else
		{
			$type = ($tvshow->source->settings->posters != '') ? 'poster' : 'banner';

			// Affiche principale si $idSeason est NULL
			$path = $tvshow->path;
		}

    // Calcul du hashage du fichier
    $thumbnail = $this->_get_hash($path);

    // Emplacement partiel présumé du fichier
    $filename = 'Video/'.substr($thumbnail, 0, 1).'/'.$thumbnail.'.tbn';

    // Emplacement présumé du fichier pour xbmc
    $image_filename = $this->_thumbnails_dir.'Video/'.substr($thumbnail, 0, 1).'/'.$thumbnail.'.tbn';

    // Fichier présent sur le disque, on détermine le type d'iamge
    if (file_exists($image_filename))
    {
			// Prendre en compte le cas où même si le scraper télécharge des bannières, l'affiche est un poster
      list($width, $height) = getimagesize($image_filename);

      if ($width < $height)
          $type = 'poster';
      else
          $type = 'banner';
    }

		// Emplacement de la miniature correspondante (préfixée p_ ou b_ suivant poster ou banner)
		$thumbnail_filename = $this->images_cache_dir.'xbmc/Video/media/'.substr($type, 0, 1).'_'.$thumbnail.'.jpg';

		// Emplacement du fichier
		$poster->filename = $image_filename;

		// Fichier présent sur le disque ?
		if (file_exists($image_filename))
		{

			if ($type == 'poster')
			{
				// Création de la miniature de l'affiche
				$this->create_image($image_filename, $thumbnail_filename, $this->poster_size);
			}
			else
			{
				// Création de la miniature de la bannière
				$this->create_image($image_filename, $thumbnail_filename, $this->banner_size);
			}

			$poster->url = $this->images_cache_url.'xbmc/Video/media/'.substr($type, 0, 1).'_'.$thumbnail.'.jpg';
			$poster->type = $type;
		}
    else
    {
      $poster->url = '';
    }

    return $poster;
  }

  /**
   * Retourne le fond d'écran d'une série tv pour un chemin
   *
   * @access public
   * @param object
   * @return object
   */
  public function get_tvshow_backdrop($tvshow)
  {
    $backdrop = new stdClass();

    // Calcul du hashage du fichier
    $thumbnail = $this->_get_hash($tvshow->path);

    // Emplacement présumé du fichier pour xbmc
    $image_filename = $this->_thumbnails_dir.'Video/Fanart/'.$thumbnail.'.tbn';

		// Emplacement de la miniature correspondante
		$thumbnail_filename = $this->images_cache_dir.'xbmc/Video/Fanart/b_'.$thumbnail.'.jpg';

		// Emplacement du fichier
		$backdrop->filename = $image_filename;

    // Fichier présent sur le disque ?
		if (file_exists($image_filename))
    {
			// Création de la miniature du fond d'écran
			$this->create_image($image_filename, $thumbnail_filename, $this->backdrop_size);
      $backdrop->url = $this->images_cache_url.'xbmc/Video/Fanart/b_'.$thumbnail.'.jpg';
    }
    else
    {
      $backdrop->url = '';
    }

    return $backdrop;
  }

  /**
   * Retourne l'affiche d'une saison d'une série tv pour un chemin
   *
   * @access public
   * @param string
   * @param string
   * @return object
   */
  public function get_season_poster($idSeason, $path)
  {
    // * Toutes les saisons
    if (intval($idSeason) == -1)
        $path = 'season'.$path.$this->_poster_all_seasons;

    // Spéciales
    if (intval($idSeason) == 0)
        $path = 'season'.$path.$this->_poster_special_season;

    // Saison $idSeason
    if (intval($idSeason) > 0)
        $path = 'season'.$path.$this->_poster_season.' '.$idSeason;

    return $this->get_tvshow_poster($path);
  }

  /**
   * Retourne l'image d'un épisode d'une série tv pour un fichier
   *
   * @access public
   * @param object
   * @return object
   */
  public function get_episode_poster($episode)
  {
		$file_path = $episode->path.$episode->filename;
		$file_path = str_replace($episode->source->server_path, $episode->source->client_path, $file_path);

    $poster = new stdClass();

    // Calcul du hashage du fichier
    $thumbnail = $this->_get_hash($file_path);

    // Emplacement présumé du fichier pour xbmc
    $image_filename = $this->_thumbnails_dir.'Video/'.substr($thumbnail, 0, 1).'/'.$thumbnail.'.tbn';

		// Emplacement de la miniature correspondante
		$thumbnail_filename = $this->images_cache_dir.'xbmc/Video/media/e_'.$thumbnail.'.jpg';

		// Emplacement du fichier
		$poster->filename = $image_filename;

		// Fichier présent sur le disque ?
		if (file_exists($image_filename))
		{
			// Création de la miniature de l'affiche
			$this->create_image($image_filename, $thumbnail_filename, $this->episode_size);
			$poster->url = $this->images_cache_url.'xbmc/Video/media/e_'.$thumbnail.'.jpg';
		}
		else
		{
			$poster->url = base_url().'assets/gui/DefaultVideoPoster.png';
		}

    return $poster;
  }

  /**
   * Calcule le hash pour un fichier selon l'algorithme utilisé par XBMC
   *
   * @access private
   * @param string
   * @return string
   */
  private function _get_hash($file_path)
  {
    $chars = strtolower($file_path);
    $crc = 0xffffffff;

    for ($ptr = 0; $ptr < strlen($chars); $ptr++)
    {
      $chr = ord($chars[$ptr]);
      $crc ^= $chr << 24;

      for ((int) $i = 0; $i < 8; $i++)
      {
        if ($crc & 0x80000000)
        {
          $crc = ($crc << 1) ^ 0x04C11DB7;
        }
        else
        {
          $crc <<= 1;
        }
      }
    }

    // Système d'exploitation en 64 bits ?
    if (strpos(php_uname('m'), '_64') !== false)
    {

			//Formatting the output in a 8 character hex
			if ($crc>=0)
			{
				$hash = sprintf("%16s",sprintf("%x",sprintf("%u",$crc)));
			}
			else
			{
				$source = sprintf('%b', $crc);

				$hash = "";
				while ($source <> "")
				{
					$digit = substr($source, -4);
					$hash = dechex(bindec($digit)) . $hash;
					$source = substr($source, 0, -4);
				}
			}
			$hash = substr($hash, 8);
    }
    else
    {
			//Formatting the output in a 8 character hex
			if ($crc>=0)
			{
				$hash = sprintf("%08s",sprintf("%x",sprintf("%u",$crc)));
			}
			else
			{
				$source = sprintf('%b', $crc);

				$hash = "";
				while ($source <> "")
				{
					$digit = substr($source, -4);
					$hash = dechex(bindec($digit)) . $hash;
					$source = substr($source, 0, -4);
				}
			}
    }

    return $hash;
  }

  /**
   * Coupe un texte pour n'en retourner que les x premiers caractères
   *
   * @access public
   * @param string
   * @param integer
   * @return string
   */
  public function cut_text($text, $max_lenght = 350)
  {

    if (strlen($text) > $max_lenght)
    {
      $text = substr($text, 0, $max_lenght);
      $last_space = strrpos($text, " ");
      $text = substr($text, 0, $last_space)."...";
    }

    return $text;
  }

  /**
   * Télécharge et stocke un fichier distant en se faisant passer pour un navigateur
   * Retourne le contenu de l'url pointée si on ne spécifie pas de nom de fichier
   *
   * @access public
   * @param string
   * @param string
   * @return void or string
   */
  public function download($url, $filename = NULL)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);

    $result = curl_exec($ch);
    $headers = curl_getinfo($ch);

    $error_number = curl_errno($ch);
    $error_message = curl_error($ch);

    curl_close($ch);

    // Téléchargement si nom de fichier spécifié sinon retour du contenu pointé
    if (isset($filename))
    {
      $f = fopen($filename,'wb');
      fwrite($f, $result, strlen($result));
      fclose($f);
    }
    else
    {
      return $result;
    }
  }
}

/* End of file Xbmc.php */
/* Location: ./application/libraries/Xbmc.php */
