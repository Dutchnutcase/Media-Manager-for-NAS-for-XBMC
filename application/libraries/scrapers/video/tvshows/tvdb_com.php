<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tvdb_com
{
  private $_CI;
  private $_content;
  private $_api_url = 'http://www.thetvdb.com/api/';
  private $_site_url = 'http://www.thetvdb.com/';
  private $_api_key;
  private $_lang;

  private $_images_cache_dir;
  private $_images_cache_url;

  function __construct()
  {
    $this->_CI =& get_instance();
    $this->_CI->load->config('xbmc');
    $this->_CI->load->config('scrapers/video/tvdb_com');
    $this->_api_key = $this->_CI->config->item('api_key');
    $this->_lang = $this->_CI->config->item('lang');

    $this->_images_cache_dir = FCPATH.'assets/images_cache/scrapers/video/tvshows/tvdb_com/';
    $this->_images_cache_url = $this->_CI->config->item('base_url').'assets/images_cache/scrapers/video/tvshows/tvdb_com/';
  }

  /**
   * Traite et retourne les affiches d'une série TV à partir du champ
   * issu de la base 'xbmc_video'
   *
   * On supprime l'url du site pour déjouer une mesure anti hotlink
   *
   * Retourne un objet avec :
   * posters => tableau des affiches au format 'normal'
   * banners => tableau des affiches au format 'bannières'
   * seasons => tableau des affiches de saison au format 'normal'
   *
   * @access public
   * @param object
   * @return object
   */
  public function get_posters($data)
  {
    $xml = simplexml_load_string('<root>'.$data->c06.'</root>');

    $banners = array();
    $posters = array();
    $seasons = array();

    foreach ($xml as $value)
    {
      if ($value->attributes()->count() != 0)
      {
        // Affiche de saison ?
        if ($value[0]->attributes()->type == 'season')
        {
          $season = (string) $value[0]->attributes()->season;
          if (!isset($seasons[$season])) $seasons[$season] = array();

					$url = (string) $value[0];
					$season_poster = new stdClass();

					// Identifiant unique de l'image
					$id = substr($url, strrpos($url, '/')+1);
					
					$season_poster->real_url = $url;
					$season_poster->url = $this->_images_cache_url.'media/s_'.$id;
					$season_poster->filename = $this->_images_cache_dir.'media/s_'.$id;

          $seasons[$season][] = $season_poster;
        }
      }
      else
      {
        // Affiche au format 'poster' ou au format 'bannière' ?
        if (stripos($value[0], 'posters') !== FALSE)
        {
					$url = (string) $value[0];
					$poster = new stdClass();

					// Identifiant unique de l'image
					$id = substr($url, strrpos($url, '/')+1);
					
					$poster->real_url = $url;
					$poster->url = $this->_images_cache_url.'media/p_'.$id;
					$poster->filename = $this->_images_cache_dir.'media/p_'.$id;

          $posters[] = $poster;
        }
        else
        {
					$url = (string) $value[0];
					$banner = new stdClass();

					// Identifiant unique de l'image
					$id = substr($url, strrpos($url, '/')+1);
					
					$banner->real_url = $url;
					$banner->url = $this->_images_cache_url.'media/b_'.$id;
					$banner->filename = $this->_images_cache_dir.'media/b_'.$id;

          $banners[] = $banner;
        }
      }
    }

		// Suppression des affiches de la saison '-' car ceux sont les posters
		unset($seasons[-1]);

    // Tri par ordre croissant de numéro de saison (-1 pour toutes les saisons)
    ksort($seasons);

    // On retourne une classe
    $images = new stdClass();
    $images->posters = $posters;
    $images->banners = $banners;
    $images->seasons = $seasons;

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
    $xml = simplexml_load_string('<root>'.$data->c11.'</root>');

    $backdrops = array();
    foreach ($xml->fanart->thumb as $value)
    {
      $url = 'http://thetvdb.com/banners/'.(string) $value;
      $backdrop = new stdClass();
      
      // Identifiant unique de l'image
      $id = substr($url, strrpos($url, '/')+1);
      
      $backdrop->real_url = $url;
      $backdrop->url = $this->_images_cache_url.'Fanart/b_'.$id;
      $backdrop->filename = $this->_images_cache_dir.'Fanart/b_'.$id;
            
      $backdrops[] = $backdrop;
    }

    return $backdrops;
  }

  /**
   * Retrouve la véritable url d'une image depuis un site distant
   *
   * @access public
   * @param string
   * @param string
   * @return string
   */
  public function get_image_url($fake_url)
  {
    $url = $this->_thumbnails_url;
    $url .= str_replace('tvdb_com/', '', $fake_url);

    return $url;
  }

/*
            [3] => SimpleXMLElement Object
                (
                    [id] => 52781
                    [BannerPath] => fanart/original/75930-10.jpg
                    [BannerType] => fanart
                    [BannerType2] => 1280x720
                    [Colors] => SimpleXMLElement Object
                        (
                        )

                    [Language] => en
                    [Rating] => 8.0000
                    [RatingCount] => 1
                    [SeriesName] => false
                    [ThumbnailPath] => _cache/fanart/original/75930-10.jpg
                    [VignettePath] => fanart/vignette/75930-10.jpg
                )
*/

  /**
   * Récupère le lien vers une série TV à partir de son identifiant sur le site
   * distant
   *
   * @access public
   * @param object
   * @return string
   */
  public function get_external_link($result)
  {
    return $this->_site_url.'?tab=series&id='.$result->c12.'&lid='.$this->_lang;
  }

}

/* End of file tvdb_com.php */
/* Location: ./application/libraries/scrapers/video/tvshows/tvdb_com.php */
