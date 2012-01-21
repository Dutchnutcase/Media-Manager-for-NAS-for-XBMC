<?php
foreach ($backdrops as $backdrop)
{
  $image_properties = array(
            'src' => $backdrop->url,
            'class' => 'backdrop_thumb',
            'alt' => $backdrop->real_url,
            'rel' => $this->uri->segment(1).'_'.$this->uri->segment(2) // 'movies_xx', 'tvshows_xx', 'sets_xx' ...
  );
  echo img($image_properties);
}
?>
