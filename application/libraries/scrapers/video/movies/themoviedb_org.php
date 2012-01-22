<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Themoviedb_org
{
  private $_CI;
  private $_movie;
  private $_api_url = 'http://api.themoviedb.org/2.1/';
  private $_site_url = 'http://www.themoviedb.org/';
  private $_api_key;
  private $_lang;

  private $_images_cache_dir;
  private $_images_cache_url;

  function __construct()
  {
    $this->_CI =& get_instance();
    $this->_CI->load->config('xbmc');
    $this->_CI->load->config('scrapers/video/themoviedb_org');
    $this->_api_key = $this->_CI->config->item('api_key');
    $this->_lang = $this->_CI->config->item('lang');

    $this->_images_cache_dir = FCPATH.'assets/images_cache/scrapers/video/movies/themoviedb_org/';
    $this->_images_cache_url = $this->_CI->config->item('base_url').'assets/images_cache/scrapers/video/movies/themoviedb_org/';
  }

  /**
   * Traite et retourne les affiches d'un film à partir du champ
   * issu de la base 'xbmc_video'
   *
   * Retourne un objet avec :
   * posters => tableau des affiches au format 'normal'
   *
   * @access public
   * @param object
   * return object
   */
  public function get_posters($data)
  {
    $xml = simplexml_load_string('<root>'.$data->c08.'</root>');

    foreach ($xml->thumb as $value)
    {
      $url = (string) $value[0];
      $poster = new stdClass();

      // Identifiant unique de l'image et changement éventuel en jpg
      $id = str_replace('.png', '.jpg', substr($url, strrpos($url, '/')+1));

      $poster->real_url = $url;
      $poster->url = $this->_images_cache_url.'media/p_'.$id;
      $poster->filename = $this->_images_cache_dir.'media/p_'.$id;

      $posters[] = $poster;
    }

    // On retourne une classe
    $images = new stdClass();
    $images->posters = $posters;

    return $images;
  }

  /**
   * Traite et retourne les fonds d'écran d'une série TV à partir du champ
   * issu de la base 'xbmc_video'
   *
   * On supprime l'url du site pour déjouer une mesure anti hotlink
   *
   * @access public
   * @param object
   * @return array
   */
  public function get_backdrops($data)
  {
    $xml = simplexml_load_string('<root>'.$data->c20.'</root>');

    $backdrops = array();
    foreach ($xml->fanart->thumb as $value)
    {
      $url = (string) $value;
      $backdrop = new stdClass();

      // Identifiant unique de l'image et changement éventuel en jpg
      $id = str_replace('.png', '.jpg', substr($url, strrpos($url, '/')+1));

      $backdrop->real_url = $url;
      $backdrop->url = $this->_images_cache_url.'Fanart/b_'.$id;
      $backdrop->filename = $this->_images_cache_dir.'Fanart/b_'.$id;

      $backdrops[] = $backdrop;
		}

    return $backdrops;
  }

  /**
   * Récupère les informations d'un film à partir de son identifiant sur le site
   * distant pour le film local dont on précise l'identifiant
   *
   * @access public
   * @param integer
   * @param integer
   * @return array
   */
  public function get($remote_movie_id, $local_movie_id)
  {
    // Utilisation de la classe Xbmc avec user agent spécifique ou pas
    $response = $this->_CI->xbmc_lib->download($this->_api_url.'Movie.getInfo/'.$this->_lang.'/json/'.$this->_api_key.'/'.$remote_movie_id);

    $response = json_decode($response);
    $remote_movie = $response[0];

    // Tableau pour mise à jour directe dans la base de données
    $movie = array();

    // Gestion du 'casting' + ajout dans la base de données si absent
    if (is_array($remote_movie->cast))
    {
      // Pour toutes les personnes du 'casting'
      foreach($remote_movie->cast as $person)
      {
        if (($person->job == 'Screenplay') || ($person->job == 'Author'))
        {
          // Recherche de la personne
          $id = $this->_CI->actors_model->get_by_name($person->name);

          // Personne absente, alors on l'ajoute dans la base de données
          if ($id == 0)
          {
            $id = $this->_CI->actors_model->add($person->name);
          }
          $writers[id] = $person->name;
        }

        if ($person->job == 'Director')
        {
          // Recherche de la personne
          $id = $this->_CI->actors_model->get_by_name($person->name);

          // Personne absente, alors on l'ajoute dans la base de données
          if ($id == 0)
          {
            $id = $this->_CI->actors_model->add($person->name);
          }
          $directors[$id] = $person->name;
        }

        if ($person->job == 'Actor')
        {
          // Recherche de la personne
          $id = $this->_CI->actors_model->get_by_name($person->name);

          // Personne absente, alors on l'ajoute dans la base de données
          if ($id == 0)
          {
            $id = $this->_CI->actors_model->add($person->name);
          }

          $actors[$id] = array('name' => $person->name,
                               'character' => ($person->character != '') ? $person->character : $this->_CI->lang->line('media_no_role'),
                               'profile' => str_replace('-thumb', '-original', $person->profile));
        }
      }

      if (isset($writers))
      {
        $this->_CI->actors_model->set_writers_for_movie($writers, $local_movie_id);
        $movie['c06'] = implode(' / ', $writers);
      }

      if (isset($directors))
      {
        $this->_CI->actors_model->set_directors_for_movie($directors, $local_movie_id);
        $movie['c15'] = implode(' / ', $directors);
      }

      if (isset($actors))
      {
        $this->_CI->actors_model->set_actors_for_movie($actors, $local_movie_id);
      }
    }

    // Gestion des genres + ajout dans la base de données si absent
    if (is_array($remote_movie->genres))
    {
      foreach($remote_movie->genres as $genre)
      {
        // Recherche du genre
        $id = $this->_CI->genres_model->get_by_name($genre->name);

        // Genre absent, alors on l'ajoute dans la base de données
        if ($id == 0)
        {
          $id = $this->_CI->genres_model->add($genre->name);
        }

        $genres[$id] = $genre->name;
      }

      if (isset($genres))
      {
        $this->_CI->genres_model->set_for_movie($genres, $local_movie_id);
        $movie['c14'] = implode(' / ', $genres);
      }
    }

    // Gestion des studios + ajout dans la base de données si absent
    if (is_array($remote_movie->studios))
    {
      foreach($remote_movie->studios as $studio)
      {
        // Recherche du studio
        $id = $this->_CI->studios_model->get_by_name($studio->name);

        // studio absent, alors on l'ajoute dans la base de données
        if ($id == 0)
        {
          $id = $this->_CI->studios_model->add($studio->name);
        }

        $studios[$id] = $studio->name;
      }

      if (isset($studios))
      {
        $this->_CI->studios_model->set_for_movie($studios, $local_movie_id);
        $movie['c18'] = implode(' / ', $studios);
      }
    }

    // Gestion des pays + ajout dans la base de données si absent
    if (is_array($remote_movie->countries))
    {
      foreach($remote_movie->countries as $country)
      {
        // Recherche du pays
        $id = $this->_CI->countries_model->get_by_name($country->name);

        // studio absent, alors on l'ajoute dans la base de données
        if ($id == 0)
        {
          $id = $this->_CI->countries_model->add($country->name);
        }

        $countries[$id] = $country->name;
      }

      if (isset($countries))
      {
        $this->_CI->countries_model->set_for_movie($countries, $local_movie_id);
        $movie['c21'] = implode(' / ', $countries);
      }
    }

    $movie['c00'] = isset($remote_movie->name) ? $remote_movie->name : '';
    $movie['c01'] = isset($remote_movie->overview) ? $remote_movie->overview : '';
    $movie['c03'] = isset($remote_movie->tagline) ? $remote_movie->tagline : '';
    $movie['c04'] = isset($remote_movie->votes) ? $remote_movie->votes : '';
    $movie['c05'] = isset($remote_movie->rating) ? sprintf("%01.5f", $remote_movie->rating) : '';
    $movie['c07'] = isset($remote_movie->released) ? substr($remote_movie->released, 0, 4) : '';

    // Gestion des affiches
    $images = array();
    foreach($remote_movie->posters as $image)
    {
      $images[$image->image->id][$image->image->size] = $image->image->url;
    }

    $this->poster = '';
    $movie['c08'] = '';
    foreach($images as $image)
    {
      $movie['c08'] .= '<thumb preview="';
      $movie['c08'] .= $image['mid'];
      $movie['c08'] .= '">';
      $movie['c08'] .= $image['original'];
      $movie['c08'] .= '</thumb>';

      // On conserve la première affiche proposée
      if ($this->poster == '') $this->poster = $image['original'];
    }
    unset($images);

    $movie['c09'] = isset($remote_movie->imdb_id) ? $remote_movie->imdb_id : '';
    $movie['c11'] = isset($remote_movie->runtime) ? $remote_movie->runtime : '';
    $movie['c12'] = isset($remote_movie->certification) ? $movie['c12'] = 'Rated '.$remote_movie->certification : '';

    $movie['c13'] = '0';

    $movie['c16'] = isset($remote_movie->original_name) ? $remote_movie->original_name : '';

    // Gestion des fonds d'écran
    $images = array();
    foreach($remote_movie->backdrops as $image)
    {
      $images[$image->image->id][$image->image->size] = $image->image->url;
    }

    $this->backdrop = '';
    $movie['c20'] = '<fanart>';
    foreach($images as $image)
    {
      $movie['c20'] .= '<thumb preview="';
      $movie['c20'] .= $image['poster'];
      $movie['c20'] .= '">';
      $movie['c20'] .= $image['original'];
      $movie['c20'] .= '</thumb>';

      // On conserve le premier fond d'écran proposé
      if ($this->backdrop == '') $this->backdrop = $image['original'];
    }
    $movie['c20'] .= '</fanart>';
    unset($images);

    // Informations du film prêtes à être mises à jour ou ajoutée
    return $movie;
  }

  public function search($title)
  {
    // Utilisation de la classe Xbmc avec user agent spécifique ou pas
    $response = $this->_CI->xbmc_lib->download($this->_api_url.'Movie.search/'.$this->_lang.'/json/'.$this->_api_key.'/'.urlencode($title));

    $movies = json_decode($response);

    $results = array();

    // Y a t'il au moins un résultat ?
    if ($movies[0] != 'Nothing found.')
    {
      foreach($movies as $movie)
      {
        // On ne traite que les films
        if ($movie->movie_type == 'movie')
        {
          $poster = '';

          // Recherche d'une affiche pour présentation
          foreach($movie->posters as $image)
          {
            if ($image->image->size == 'cover')
            {
              $poster = $image->image->url;
              break;
            }
          }

          $result = new stdClass();
          $result->id = $movie->id;
          $result->title = $movie->name;
          $result->poster = $poster;

          $results[] = $result;
        }
      }
    }

    // On retourne les films trouvés
    return $results;
  }

  /**
   * Récupère le lien vers un film à partir de son identifiant sur le site
   * distant
   *
   * @access public
   * @param array
   * @return string
   */
  public function get_external_link($data)
  {
    // Utilisation de la classe Xbmc avec user agent spécifique ou pas
    $response = $this->_CI->xbmc_lib->download($this->_api_url.'Movie.imdbLookup/'.$this->_lang.'/json/'.$this->_api_key.'/'.$data->c09);

    $response = json_decode($response);
    $movie = $response[0];

    return $this->_site_url.'movie/'.$movie->id;
  }

  /**
   * Récupère les informations d'un film à partir de son identifiant sur le site
   * distant pour préparer l'ajout ou la mise à jour dans la base de données
   *
   * @access public
   * @param integer
   * @return object
   */
  public function prepare_entry($movie_id)
  {
    // Utilisation de la classe Xbmc avec user agent spécifique ou pas
    $response = $this->_CI->xbmc_lib->download($this->_api_url.'Movie.getInfo/'.$this->_lang.'/json/'.$this->_api_key.'/'.$movie_id);

    $response = json_decode($response);
    $remote_movie = $response[0];

    // Nouvelle entrée dans la base de données
    $new_entry = new stdClass();

    // Valeurs par défaut
    $new_entry->playcount = 0;
    $new_entry->lastplayed = '';
    $new_entry->movie = '';
    $new_entry->writers = '';
    $new_entry->directors = '';
    $new_entry->actors = '';
    $new_entry->genres = '';
    $new_entry->studios = '';
    $new_entry->countries = '';
    $new_entry->poster = '';
    $new_entry->backdrop = '';

    // Tableau pour mise à jour directe dans la base de données
    $movie = array();

    // Gestion du 'casting'
    if (is_array($remote_movie->cast))
    {
      // Pour toutes les personnes du 'casting'
      foreach($remote_movie->cast as $person)
      {
        if (($person->job == 'Screenplay') || ($person->job == 'Author') || ($person->job == 'Writer'))
        {
          $writer = new stdClass();
          $writer->name = $person->name;
          $writer->profile = str_replace('-thumb', '-original', $person->profile);

          $writers[] = $writer;
        }

        if ($person->job == 'Director')
        {
          $director = new stdClass();
          $director->name = $person->name;
          $director->profile = str_replace('-thumb', '-original', $person->profile);

          $directors[] = $director;
        }

        if ($person->job == 'Actor')
        {
          $actor = new stdClass();
          $actor->name = $person->name;
          $actor->character = ($person->character != '') ? $person->character : $this->_CI->lang->line('media_no_role');
          $actor->profile = str_replace('-thumb', '-original', $person->profile);

          $actors[] = $actor;
        }
      }

      if (isset($writers))
      {
        $new_entry->writers = $writers;

        // Pour récupération de la liste de noms
        foreach($writers as $person)
          $a_writers[] = $person->name;

        $movie['c06'] = implode(' / ', $a_writers);
      }

      if (isset($directors))
      {
        $new_entry->directors = $directors;

        // Pour récupération de la liste de noms
        foreach($directors as $person)
          $a_directors[] = $person->name;

        $movie['c15'] = implode(' / ', $a_directors);
      }

      if (isset($actors))
      {
        $new_entry->actors = $actors;
      }
    }

    // Gestion des genres
    if (is_array($remote_movie->genres))
    {
      foreach($remote_movie->genres as $genre)
      {
        $genres[] = $genre->name;
      }

      if (isset($genres))
      {
        $new_entry->genres = $genres;
        $movie['c14'] = implode(' / ', $genres);
      }
    }

    // Gestion des studios
    if (is_array($remote_movie->studios))
    {
      foreach($remote_movie->studios as $studio)
      {
        $studios[] = $studio->name;
      }

      if (isset($studios))
      {
        $new_entry->studios = $studios;
        $movie['c18'] = implode(' / ', $studios);
      }
    }

    // Gestion des pays
    if (is_array($remote_movie->countries))
    {
      foreach($remote_movie->countries as $country)
      {
        $countries[] = $country->name;
      }

      if (isset($countries))
      {
        $new_entry->countries = $countries;
        $movie['c21'] = implode(' / ', $countries);
      }
    }

    $movie['c00'] = isset($remote_movie->name) ? $remote_movie->name : '';
    $movie['c01'] = isset($remote_movie->overview) ? $remote_movie->overview : '';
    $movie['c03'] = isset($remote_movie->tagline) ? $remote_movie->tagline : '';
    $movie['c04'] = isset($remote_movie->votes) ? $remote_movie->votes : '';
    $movie['c05'] = isset($remote_movie->rating) ? sprintf("%01.5f", $remote_movie->rating) : '';
    $movie['c07'] = isset($remote_movie->released) ? substr($remote_movie->released, 0, 4) : '';

    // A finir
    $movie['c19'] =  $remote_movie->trailer;

    // Gestion des affiches
    $images = array();
    foreach($remote_movie->posters as $image)
    {
      $images[$image->image->id][$image->image->size] = $image->image->url;
    }

    $movie['c08'] = '';
    foreach($images as $image)
    {
      $movie['c08'] .= '<thumb preview="';
      $movie['c08'] .= $image['mid'];
      $movie['c08'] .= '">';
      $movie['c08'] .= $image['original'];
      $movie['c08'] .= '</thumb>';

      // On conserve la première affiche proposée
      if ($new_entry->poster == '') $new_entry->poster = $image['original'];
    }
    unset($images);

    $movie['c09'] = isset($remote_movie->imdb_id) ? $remote_movie->imdb_id : '';
    $movie['c11'] = isset($remote_movie->runtime) ? $remote_movie->runtime : '';
    $movie['c12'] = isset($remote_movie->certification) ? $movie['c12'] = 'Rated '.$remote_movie->certification : '';

    $movie['c13'] = '0';

    $movie['c16'] = isset($remote_movie->original_name) ? $remote_movie->original_name : '';

    // Gestion des fonds d'écran
    $images = array();
    foreach($remote_movie->backdrops as $image)
    {
      $images[$image->image->id][$image->image->size] = $image->image->url;
    }

    $movie['c20'] = '<fanart>';
    foreach($images as $image)
    {
      $movie['c20'] .= '<thumb preview="';
      $movie['c20'] .= $image['poster'];
      $movie['c20'] .= '">';
      $movie['c20'] .= $image['original'];
      $movie['c20'] .= '</thumb>';

      // On conserve le premier fond d'écran proposé
      if ($new_entry->backdrop == '') $new_entry->backdrop = $image['original'];
    }
    $movie['c20'] .= '</fanart>';
    unset($images);

    $new_entry->movie = $movie;

    // Informations du film prêtes à être mises à jour ou ajoutée
    return $new_entry;
  }

  public function update($source)
  {

/*

stdClass Object
(
    [id] => 1
    [idPath] => 1
    [client_path] => smb://NAS/films/
    [server_path] => /media/raid/Films
    [content] => movies
    [scraper] => themoviedb_org
    [settings] => O:8:"stdClass":6:{s:8:"TrailerQ";s:2:"No";s:6:"fanart";b:1;s:10:"imdbrating";b:0;s:8:"language";s:2:"fr";s:7:"trailer";b:0;s:16:"user_folder_name";b:0;}
)
*
stdClass Object
(
    [TrailerQ] => No
    [fanart] => 1
    [imdbrating] =>
    [language] => fr
    [trailer] =>
    [user_folder_name] =>
)

*/
//    echo '<pre>'.print_r($this->_CI->xbmc_lib, true).'</pre>';

    $this->_lang = $source->settings->language;

    // Utilisation des noms de dossiers comme titre de films ?
    if ($source->settings->user_folder_name)
    {
      $to_add = $this->update_by_folder_name($source);
    }
    else
    {
      $potential_movies = $this->_CI->videoinfoscanner->get_potential_movies_to_update($source);

      foreach($potential_movies as $potential_movie)
      {
        $entry = new stdClass();
        $entry = $potential_movie;

        $title = $potential_movie->title;

        if ($potential_movie->year != '')
            $title .= '+'.$potential_movie->year;

        // Présence d'un fichier nfo prioritaire ?
        if ($entry->nfo != '')
        {
          $entry->data = $this->_CI->videoinfoscanner->parse_movie_nfo($entry->source->server_path.$entry->nfo);
        }
        else
        {
          // Recherche du film
          $movies = $this->search($title, TRUE);

          if (count($movies) == 0)
          {
            echo '<br />not found';
          }

          if (count($movies) == 1)
          {
            $entry->data = $this->prepare_entry($movies[0]->id);
          }

          if (count($movies) > 1)
          {
            // Pour toutes les réponses
            foreach($movies as $movie)
            {
              // Titres en minuscules strictement idenditiques ?
              if (strtolower($potential_movie->title) == strtolower($movie->title))
              {
                $entry->data = $this->prepare_entry($movie->id);
                break;
              }

            }
          }


        }

        // Si une correspondance avec un film a été trouvée
        if (isset($entry->data))
        {
          // Pas de prise en compte des fanarts dans la configuration du scraper ?
          if (!$source->settings->fanart)
              $entry->data->backdrop = '';

          // Ajoût du film et de toutes ses informations dans la base de données
          $this->_CI->videoinfoscanner->add_or_update_movie($entry);

          echo "Ajoût de '$title' dans la base de données.\n";
          die();
        }

      }

//      echo '<pre>'.print_r($potential_movies, true).'</pre>';

    }

    die();
  }

  // Return objet (path/file/title
  public function update_by_folder_name($source)
  {
  }

}

/* End of file themoviedb_org.php */
/* Location: ./application/libraries/scrapers/video/movies/themoviedb_org.php */
