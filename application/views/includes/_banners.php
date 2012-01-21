<?php
foreach ($banners as $banner)
{
  $image_properties = array(
            'src' => $banner->url,
            'class' => 'banner_thumb',
            'alt' => $banner->real_url,
            'rel' => $this->uri->segment(1).'_'.$this->uri->segment(2) // 'movies_xx', 'tvshows_xx', 'sets_xx' ...
  );
  echo img($image_properties);
}
?>
