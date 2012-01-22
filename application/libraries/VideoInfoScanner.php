<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class VideoInfoScanner
{
  private $_CI;

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

    log_message('debug', "VideoInfoScanner Class Initialized");

    // Chargement des modèles de la base de données 'xbmc'
    $this->_CI->load->model('xbmc/sources_model');

    // Sources de la base de données 'xbmc_video'
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
   * Retourne les informations relatives à un dossier
   *
   * @access public
   * @param string
   * @param string
   * @return object
   */
  function get_source($path)
  {
    $the_source = new stdClass();

    foreach($this->sources as $source)
    {
      if (strrpos($path, $source->client_path) !== FALSE)
      {
        $the_source = $source;
        break;
      }
    }

    return $the_source;
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
  }

  /**
   * Ecrit le fichier de configuration pour le gestionnaire de média
   * Analyse et traite un fichier 'advancedsettings.xml' préalablement chargé
   * sur le serveur
   *
   * @access public
   * @return object
   */
  public function make_library_config()
  {
    // Pas de limite de temps pour le script
    set_time_limit(0);

		// Valeur de retour par défaut
		$return = NULL;

		if (isset($_FILES['advancedsettings']))
		{
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

				$filename = APPPATH.'/config/library.php';

				$handle = fopen($filename, 'w');
				fwrite($handle, $library_config);
				fclose($handle);

				// Etablie la configuration pour le gestionnaire de média
				$this->_init_config();

				// Gestion de la base de données
				if (isset($xml->videodatabase) && isset($xml->musicdatabase))
				{
					// En espérant qu'il n'y ait qu"une seul serveur, port, utilisateur et mot de passe
					$data = new stdClass();

					// Adresse IP remplacée par localhost si celle fourni est identique à celle du serveur
					$data->host = ($_SERVER['SERVER_ADDR'] != $xml->videodatabase->host) ? (string) $xml->videodatabase->host : 'localhost';
					$data->port = (string) $xml->videodatabase->port;
					$data->user = (string) $xml->videodatabase->user;
					$data->pass = (string) $xml->videodatabase->pass;

					// Nom de la base de données 'video' fourni ?
					if (isset($xml->videodatabase->name))
					{
						$data->video = (string) $xml->videodatabase->name;
					}
					else
					{
						$data->video = 'MyVideos58';
					}

					// Nom de la base de données 'music' fourni ?
					if (isset($xml->musicdatabase->name))
					{
						$data->music = (string) $xml->musicdatabase->name;
					}
					else
					{
						$data->music = 'MyMusic18';
					}

					// Pour retour
					$return = $data;
				}
			}
		}

		return $return;
  }

  /* Return object
  stdClass Object
  (
      [year] =>
      [title] => 1492 Christophe Colomb
      [idPath] => 1
      [path] => smb://NAS/films/
      [file] => 1492 Christophe Colomb.avi
  )
  */
  public function get_potential_movies_to_update($source)
  {
    $potential_movies = array();

    // Utilisation des noms de dossiers comme titre de films ?
    if ($source->settings->user_folder_name)
    {
      echo 'par dossier';
    }
    else
    {
      // Chargement des modèles de la base de données 'xbmc_video'
      $this->_CI->load->model('video/files_model');

      // Liste des fichiers correspondants au dossier
      $files_in_db = $this->_CI->files_model->get_all_by_path_id($source->idPath);

      // Tri des fichiers nécessaires pour gérer les films en plusieurs parties
      $handle = opendir($source->server_path);
      while (false !== ($file = readdir($handle)))
      {
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        // On ne conserve que les fichiers correspondants à des vidéos
        if (in_array($extension, $this->video_extensions))
        {
          $files[] = $file;
        }
      }
      closedir($handle);
      sort($files);

      // Par défaut, on n'est pas en train d'empiler les parties d'un film
      $stacking = FALSE;
      $stack = array();

      // Tant que le tableau de fichiers n'est pas vide.
      while(!empty($files))
      {
        // On prend un fichier que l'on va traiter
        $file = array_shift($files);

        foreach($files_in_db as $file_in_db)
        {
          // Le fichier fait-il partie d'un empilement ou est déjà dans la base ?
          if (strpos($file_in_db, $file) !== false)
          {
            // Oui donc on repart dans le while et on prend un autre fichier
            continue 2;
          }
        }

        // Si le fichier n'est pas présent dans la base alors on le traite
        $potential_movie = new stdClass();

        // Valeur par défaut
        $potential_movie->nfo = '';
        $potential_movie->poster = '';
        $potential_movie->backdrop = '';

        $potential_movie->source = $source;

        // Titre basé sur le nom du fichier
        $potential_title = pathinfo($file, PATHINFO_FILENAME);

        // Si on n'est pas en train d'empiler les parties d'un film
        if (!$stacking)
        {
          // Masque pour identifier des films en plusieurs fichiers
          foreach($this->movie_stacking as $movie_stacking)
          {
            // Film en plusieurs parties ?
            if (preg_match('#'.$movie_stacking.'#i', $file, $matches))
            {
              // On cherche les autres parties
              $others = $matches[1].'*'.$matches[4];
              $parts = glob($source->server_path.$others);

              // Plusieurs parties de film ? Ce n'est donc pas une erreur
              if (count($parts) > 1)
              {
/*
$matches
Array
(
[0] => Avatar-cd1.avi
[1] => Avatar
[2] => -cd1
[3] =>
[4] => .avi
)
*/
                // On empile les parties d'un film
                $stacking = TRUE;

                // Titre basé sur le nom du fichier de la première partie
                $potential_title = $matches[1];

                // Présence d'un fichier 'Avatar-cd1.nfo' ?
                if (file_exists($source->server_path.$matches[1].$matches[2].'.nfo'))
                {
                  $potential_movie->nfo = $matches[1].$matches[2].'.nfo';
                }

                // Présence d'un fichier 'Avatar.nfo' ?
                if (file_exists($source->server_path.$matches[1].'.nfo'))
                {
                  $potential_movie->nfo = $matches[1].'.nfo';
                }

                // Présence d'un fichier 'Avatar-cd1.tbn' ?
                if (file_exists($source->server_path.$matches[1].$matches[2].'.tbn'))
                {
                  $potential_movie->poster = $matches[1].$matches[2].'.tbn';
                }

                // Présence d'un fichier 'Avatar.tbn' ?
                if (file_exists($source->server_path.$matches[1].'.tbn'))
                {
                  $potential_movie->poster = $matches[1].'.tbn';
                }

                // Suppression du chemin dans les fichiers de parties
                foreach($parts as $key => $value)
                {
                  $parts[$key] = str_replace($source->server_path, '', $value);
                }
              }
              else
              {
                // C'est une erreur, on n'empile pas les parties d'un film
                $stacking = FALSE;
              }

              break;
            }
          }

          // Pas de parties de film
          if (!$stacking)
          {
            // Présence d'un fichier '$potential_title.nfo' ?
            if (file_exists($source->server_path.$potential_title.'.nfo'))
            {
              $potential_movie->nfo = $potential_title.'.nfo';
            }

            // Présence d'un fichier '$potential_title.tbn' ?
            if (file_exists($source->server_path.$potential_title.'.tbn'))
            {
              $potential_movie->poster = $potential_title.'.tbn';
            }
          }
        }

        // On nettoie le nom du fichier
        foreach($this->clean_strings as $clean_string)
        {
          $potential_title = preg_replace('/'.$clean_string.'/i', '', $potential_title);
        }

        // On cherche une date éventuelle
        preg_match('#'.$this->clean_date_time.'#', $potential_title, $matches);

        $potential_movie->year = '';
        if (isset($matches[2]))
        {
          $potential_movie->year = $matches[2];

          // On nettoie le nom du fichier pour en retirer la date
          $potential_title = $matches[1];

          // Cas de 1984 ou 2012
          if ($potential_title == '') $potential_movie->title = $matches[2];
        }

        // Dernière modification sur le nom de fichier et on a un titre
        $potential_movie->title = trim(str_replace('.', ' ', $potential_title));

        // On vérifie si une année est entre paranthèses
        if (preg_match("#(.*)\((19[0-9][0-9]|20[0-1][0-9])\)#", $potential_movie->title, $matches))
        {
          $potential_movie->title = $matches[1];
          $potential_movie->year = $matches[2];
        }

        $potential_movie->filename = pathinfo($file, PATHINFO_BASENAME);

        if (!$stacking)
        {
          // On ajoute cette entrée à la liste des films potentiels
          $potential_movies[] = $potential_movie;
        }
        else
        {
          // On ajoute cette entrée à la liste des parties empilées
          $stacks[] = $potential_movie;

          // Si ce fichier est une partie de film
          if (in_array($file, $parts))
          {
            // On retire cette partie
            $part = array_shift($parts);
          }

          // Plus de partie à traiter ?
          if (empty($parts))
          {
            $stacking = FALSE;

            $potential_movie = $stacks[0];

            $stack_filenames = array();
            foreach($stacks as $stack)
            {
              $stack_filenames[] = $stack->source->client_path.$stack->filename;
            }

            $potential_movie->filename = 'stack://'.implode(' , ', $stack_filenames);

            // Pour le prochain empilement de parties
            unset($stacks);

            // On ajoute cette entrée à la liste des films potentiels
            $potential_movies[] = $potential_movie;
          }
        }
      }
    }

    return $potential_movies;
  }

  /**
   * Ajoute ou met à jour un film
   *
   * Si $entry->movie['idMovie'] n'est pas défini ajoute le film sinon met à jour
   *
   * @access public
   * @param object
   * @return void
   */
  public function add_or_update_movie($entry)
  {

//echo '<pre>'.print_r($entry, true).'</pre>';
//die();

    // Pas d'identifiant de film alors on ajoute ce film
    if (!isset($entry->data->movie['idMovie']))
    {
      // Préparation des données
      $data = array('idPath' => $entry->source->idPath, 'strFilename' => $entry->filename);

      // Prise en compte du nombre et date du dernier visionnage
      if (($entry->data->playcount != 0) && ($entry->data->lastplayed != ''))
      {
        $data['playcount'] = $entry->data->playcount;
        $data['lastplayed'] = $entry->data->lastplayed;
      }

      // Ajoût du fichier
      $entry->data->movie['idFile'] = $this->_CI->files_model->add($data);

      // Ajoût du film
      $movie_id = $this->_CI->movies_model->add($entry->data->movie);
    }
    else
    {
      // Le film est mis à jour
      $movie_id = $entry->data->movie['idMovie'];

      // Suppression au préalable des scénaristes de ce film
      $this->_CI->actors_model->remove_writers_for_movie($movie_id);

      // Suppression au préalable des réalisateurs de ce film
      $this->_CI->actors_model->remove_directors_for_movie($movie_id);

      // Suppression au préalable des acteurs de ce film
      $this->_CI->actors_model->remove_actors_for_movie($movie_id);

      // Suppression au préalable des genres de ce film
      $this->_CI->genres_model->remove_for_movie($movie_id);

      // Suppression au préalable des studios de ce film
      $this->_CI->studios_model->remove_for_movie($movie_id);

      // Suppression au préalable des pays de ce film
      $this->_CI->countries_model->remove_for_movie($movie_id);
    }

    // Insertion des scénaristes pour ce film
    if ($entry->data->writers != '')
    {
      foreach($entry->data->writers as $person)
      {
        // Recherche de la personne
        $writer = $this->_CI->actors_model->get_by_name($person->name);

        // Personne absente, alors on l'ajoute dans la base de données
        if ($writer == NULL)
        {
          $id = $this->_CI->actors_model->add($person->name);

          // Téléchargement de la photo si disponible et non présente
          if ($person->profile != '')
          {
            $photo = $this->_CI->xbmc_lib->get_person_photo($person->name);

            // Fichier non présent sur le disque ?
            if (!file_exists($photo->filename))
            {
              $this->_CI->xbmc_lib->download($person->profile, $photo->filename);
            }
          }
        }
        else
          $id = $writer->id;

        // Ajoût de cette personne comme scénariste pour ce film
        $this->_CI->actors_model->set_writers_for_movie($id, $movie_id);
      }
    }

    // Insertion des réalisateurs pour ce film
    if ($entry->data->directors != '')
    {
      foreach($entry->data->directors as $person)
      {
        // Recherche de la personne
        $director = $this->_CI->actors_model->get_by_name($person->name);

        // Personne absente, alors on l'ajoute dans la base de données
        if ($director == NULL)
        {
          $id = $this->_CI->actors_model->add($person->name);

          // Téléchargement de la photo si disponible et non présente
          if ($person->profile != '')
          {
            $photo = $this->_CI->xbmc_lib->get_person_photo($person->name);

            // Fichier non présent sur le disque ?
            if (!file_exists($photo->filename))
            {
              $this->_CI->xbmc_lib->download($person->profile, $photo->filename);
            }
          }
        }
        else
          $id = $director->id;

        // Ajoût de cette personne comme réalisateur pour ce film
        $this->_CI->actors_model->set_directors_for_movie($id, $movie_id);
      }
    }

    // Insertion des acteurs pour ce film
    if ($entry->data->actors != '')
    {
      foreach($entry->data->actors as $person)
      {
        // Recherche de la personne
        $actor = $this->_CI->actors_model->get_by_name($person->name);

        // Personne absente, alors on l'ajoute dans la base de données
        if ($actor == NULL)
        {
          $id = $this->_CI->actors_model->add($person->name);

          // Téléchargement de la photo si disponible et non présente
          if ($person->profile != '')
          {
            $photo = $this->_CI->xbmc_lib->get_person_photo($person->name);

            // Fichier non présent sur le disque ?
            if (!file_exists($photo->filename))
            {
              $this->_CI->xbmc_lib->download($person->profile, $photo->filename);
            }
          }
        }
        else
          $id = $actor->id;

        // Ajoût de cette personne comme acteur pour ce film
        $this->_CI->actors_model->set_actors_for_movie($id, $person->character, $movie_id);
      }
    }

    // Insertion des genres pour ce film
    if ($entry->data->genres != '')
    {
      foreach($entry->data->genres as $name)
      {
        // Recherche du genre
        $id = $this->_CI->genres_model->get_by_name($name);

        // Genre absent, alors on l'ajoute dans la base de données
        if ($id == 0)
        {
          $id = $this->_CI->genres_model->add($name);
        }

        // Ajoût de ce genre pour ce film
        $this->_CI->genres_model->set_for_movie($id, $movie_id);
      }
    }

    // Insertion des studios pour ce film
    if ($entry->data->studios != '')
    {
      foreach($entry->data->studios as $name)
      {
        // Recherche du studio
        $id = $this->_CI->studios_model->get_by_name($name);

        // Studio absent, alors on l'ajoute dans la base de données
        if ($id == 0)
        {
          $id = $this->_CI->studios_model->add($name);
        }

        // Ajoût de ce studio pour ce film
        $this->_CI->studios_model->set_for_movie($id, $movie_id);
      }
    }

    // Insertion des pays pour ce film
    if ($entry->data->countries != '')
    {
      foreach($entry->data->countries as $name)
      {
        // Recherche du pays
        $id = $this->_CI->countries_model->get_by_name($name);

        // Pays absent, alors on l'ajoute dans la base de données
        if ($id == 0)
        {
          $id = $this->_CI->countries_model->add($name);
        }

        // Ajoût de ce pays pour ce film
        $this->_CI->countries_model->set_for_movie($id, $movie_id);
      }
    }

    // Pour avoir l'emplacement de l'affiche
    $poster = $this->_CI->xbmc_lib->get_movie_poster($entry);

    // Affiche locale du film ?
    if ($entry->poster != '')
    {
      // Copie de l'affiche locale du film
      copy($entry->source->server_path.$entry->poster, $poster->filename);
    }
    else
    {
      // Téléchargement de l'affiche distante du film
      $this->_CI->xbmc_lib->download($entry->data->poster, $poster->filename);
    }

    // Prise en compte des fanarts dans la configuration du scraper ?
    if ($entry->data->backdrop != '')
    {
      // Pour avoir l'emplacement du fond d'écran
      $backdrop = $this->_CI->xbmc_lib->get_movie_backdrop($entry);
      $this->_CI->xbmc_lib->download($entry->data->backdrop, $backdrop->filename);
    }
  }

  public function parse_movie_nfo($filename)
  {
    $new_entry = new stdClass();
    $movie = array();

    // Valeurs par défaut
    $posters = '';
    $backdrops = '';
    $new_entry->playcount = 0;
    $new_entry->lastplayed = '';
    $new_entry->poster = '';

    $xml = simplexml_load_file($filename);

    foreach($xml->children() as $child)
    {
      $property = $child->getName();

      switch($property)
      {
        // Champs ignorés
        case 'epbookmark':
        case 'premiered':
        case 'status':
        case 'code':
        case 'aired':
          break;

        case 'title':
          $movie['c00'] = (string) $child[0];
          break;

        case 'plot':
          $movie['c01'] = (string) $child[0];
          break;

        case 'outline':
          $movie['c02'] = (string) $child[0];
          break;

        case 'tagline':
          $movie['c03'] = (string) $child[0];
          break;

        case 'votes':
          $movie['c04'] = (string) $child[0];
          break;

        case 'rating':
          $movie['c05'] = (string) $child[0];
          break;

        case 'year':
          $movie['c07'] = (string) $child[0];
          break;

        case 'id':
          $movie['c09'] = (string) $child[0];
          break;

        case 'sorttitle':
          $movie['c10'] = (string) $child[0];
          break;

        case 'runtime':
          $movie['c11'] = (string) $child[0];
          $movie['c11'] = trim(strtolower(str_replace('min', '', $movie['c11'])));
          break;

        case 'mpaa':
          $movie['c12'] = (string) $child[0];
          break;

        case 'top250':
          $movie['c13'] = (string) $child[0];
          break;

        case 'originaltitle':
          $movie['c16'] = (string) $child[0];
          break;

        case 'trailer':
          $movie['c19'] = (string) $child[0];
          break;

        case 'credits':
          if ((string) $child[0] != '')
          {
            $person = new stdClass();
            $person->name = (string) $child[0];

            $writers[] = $person;
          }
          break;

        case 'director':
          if ((string) $child[0] != '')
          {
            $person = new stdClass();
            $person->name = (string) $child[0];

            $directors[] = $person;
          }
          break;

        case 'actor':
          if ((string) $child->name != '')
          {
            $person = new stdClass();
            $person->name = (string) $child->name;
            $person->character = '';
            $person->profile = '';
            if (isset($child->role)) $person->character = (string) $child->role;
            if (isset($child->thumb)) $person->profile = (string) $child->thumb;

            $actors[] = $person;
          }
          break;

        case 'genre':
          if ((string) $child[0] != '')
          {
            $genres[] = (string) $child[0];
          }
          break;

        case 'studio':
          if ((string) $child[0] != '')
          {
            $studios[] = (string) $child[0];
          }
          break;

        case 'country':
          if ((string) $child[0] != '')
          {
            $countries[] = (string) $child[0];
          }
          break;

        case 'set':
          if ((string) $child[0] != '')
          {
            $new_entry->set = (string) $child[0];
          }
          break;

        case 'playcount':
          if (((string) $child[0] != '') && ((string) $child[0] != '0'))
          {
            $new_entry->playcount = (string) $child[0];
          }
          break;

        case 'lastplayed':
          if (((string) $child[0] != '') && ((string) $child[0] != 'false'))
          {
            $new_entry->lastplayed = (string) $child[0];
          }
          break;

        case 'thumb':
          if (isset($child['preview']))
          {
            $posters .= '<thumb preview="'.(string) $child['preview'].'">'.(string) $child[0].'</thumb>';
          }

          // Cas d'une seule url et pas de preview dans la table 'movie'
          if (isset($child[0]))
          {
            $new_entry->poster = (string) $child[0];
          }
          break;
      }

      if (isset($xml->fanart))
      {
        $backdrops .= '<fanart';
        foreach($xml->fanart->attributes() as $key => $value)
        {
          $backdrops .= ' '.$key.'="'.$value.'"';
        }
        $backdrops .= '>';

        foreach($xml->fanart->thumb as $thumb)
        {
          $backdrops .= '<thumb';
          foreach($thumb->attributes() as $key => $value)
          {
            $backdrops .= ' '.$key.'="'.$value.'"';
          }
          $backdrops .= '>';
          $backdrops .= (string) $thumb[0];
          $backdrops .= '</thumb>';
        }

        $backdrops .= '</fanart>';
      }

      if (isset($xml->fileinfo))
      {
        if (isset($xml->fileinfo->streamdetails))
        {
          $streamdetails = new stdClass();
          if (isset($xml->fileinfo->streamdetails->video))
          {
            foreach($xml->fileinfo->streamdetails->video as $stream)
            {
              $video = new stdClass();

              if (isset($stream->codec)) $video->codec = (string) $stream->codec;
              if (isset($stream->aspect)) $video->aspect = (string) $stream->aspect;
              if (isset($stream->width)) $video->width = (string) $stream->width;
              if (isset($stream->height)) $video->height = (string) $stream->height;

              $streamdetails->videos = $video;
            }
          }

          if (isset($xml->fileinfo->streamdetails->audio))
          {
            foreach($xml->fileinfo->streamdetails->audio as $stream)
            {
              $audio = new stdClass();

              if (isset($stream->codec)) $audio->codec = (string) $stream->codec;
              if (isset($stream->language)) $audio->language = (string) $stream->language;
              if (isset($stream->channels)) $audio->channels = (string) $stream->channels;

              $streamdetails->audios = $audio;
            }
          }

          if (isset($xml->fileinfo->streamdetails->subtitle))
          {
            foreach($xml->fileinfo->streamdetails->subtitle as $stream)
            {
              $subtitle = new stdClass();

              if (isset($stream->language)) $subtitle->language = (string) $stream->language;

              $streamdetails->subtitles = $subtitle;
            }
          }

          $new_entry->streamdetails = $streamdetails;
        }
      }
    }

    if (isset($writers))
    {
      // Pour récupération de la liste de noms
      foreach($writers as $person)
        $a_writers[] = $person->name;

      $new_entry->writers = $writers;
      $movie['c06'] = implode(' / ', $a_writers);
    }

    if (isset($directors))
    {
      // Pour récupération de la liste de noms
      foreach($directors as $person)
        $a_directors[] = $person->name;

      $new_entry->directors = $directors;
      $movie['c15'] = implode(' / ', $a_directors);
    }

    if (isset($genres))
    {
      $new_entry->genres = $genres;
      $movie['c14'] = implode(' / ', $genres);
    }

    if (isset($studios))
    {
      $new_entry->studios = $studios;
      $movie['c18'] = implode(' / ', $studios);
    }

    if (isset($countries))
    {
      $new_entry->countries = $countries;
      $movie['c21'] = implode(' / ', $countries);
    }

    $movie['c08'] = $posters;
    $movie['c20'] = $backdrops;

    $new_entry->movie = $movie;
    $new_entry->actors = $actors;

    return $new_entry;
  }

}

/* End of file VideoInfoScanner.php */
/* Location: ./application/libraries/VideoInfoScanner.php */
