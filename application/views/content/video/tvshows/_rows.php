<h2 class="title"><?php echo $title; ?></h2>
<div class="inner">
  <ul class="list">
    <?php foreach($tvshows as $tvshow): ?>
    <?php
    $links = array();
    foreach($tvshow->genres as $key => $value)
    {
      $links[] = '<a href="'.site_url('tvshows/genre/'.$value->id.'/').'">'.$value->name.'</a>';
      if ($key >= 1) break;
    }
    $genres = implode(', ', $links);
    if ($genres == '') $genres = $this->lang->line('media_no_genre');

    $links = array();
    foreach($tvshow->studios as $key => $value)
    {
      $links[] = '<a href="'.site_url('tvshows/studio/'.$value->id.'/').'">'.$value->name.'</a>';
      if ($key >= 1) break;
    }
    $studios = implode(', ', $links);
    if ($studios == '') $studios = $this->lang->line('media_no_studio');
    
    // Poster ou banner pour les vignettes ?
    $poster_class = ($tvshow->source->settings->posters == 1) ? 'poster' : 'banner';
    ?>
    <li>
      <p><a href="<?php echo site_url('tvshows/'.$tvshow->id); ?>"><img class="<?php echo $poster_class; ?>_thumb" src="<?php echo $tvshow->poster->url; ?>" /></a></p>
      <h4><a href="<?php echo site_url('tvshows/'.$tvshow->id); ?>"><?php echo $tvshow->title; ?></a></h4>
			<?php echo $this->lang->line('media_genres'); ?> <?php echo $genres; ?><br />
			<?php echo $this->lang->line('media_studios'); ?> <?php echo $studios; ?><br />
			<?php echo $this->lang->line('media_year'); ?> <?php echo $tvshow->year; ?><br />
    </li>
    <?php endforeach; ?>
  </ul>
  <hr class="clear" />
</div><!-- inner -->
<div id="actions-bar" class="actions-bar wat-cf">
  <?php echo $this->my_pagination->create_links(); ?>
</div><!-- actions-bar -->
