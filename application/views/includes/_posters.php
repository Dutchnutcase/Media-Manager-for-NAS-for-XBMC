<?php
foreach ($posters as $poster)
{
  $image_properties = array(
            'src' => $poster->url,
            'class' => 'poster_thumb',
            'alt' => $poster->real_url,
            'rel' => $this->uri->segment(1).'_'.$this->uri->segment(2) // 'movies_xx', 'tvshows_xx', 'sets_xx' ...
  );
  echo img($image_properties);
}
?>
