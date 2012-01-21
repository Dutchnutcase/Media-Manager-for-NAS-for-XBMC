<h2><?php echo $title; ?></h2>
<div class="inner">
  <ul class="list">
    <?php foreach($movies as $movie): ?>
    <?php
    $links = array();
    if (is_array($movie->writers))
    {
      foreach($movie->writers as $key => $value)
      {
        $links[] = '<a href="'.site_url('actors/'.$value->id).'">'.$value->name.'</a>';
      }
    }
    $writers = implode(', ', $links);
    if ($writers == '') $writers = $this->lang->line('media_no_writer');

    $links = array();
    if (is_array($movie->directors))
    {
      foreach($movie->directors as $key => $value)
        {
          $links[] = '<a href="'.site_url('actors/'.$value->id).'">'.$value->name.'</a>';
        }
    }
    $directors = implode(', ', $links);
    if ($directors == '') $directors = $this->lang->line('media_no_director');

    $links = array();
    if (is_array($movie->actors))
    {
      foreach($movie->actors as $key => $value)
      {
        $links[] = '<a href="'.site_url('actors/'.$value->id).'">'.$value->name.'</a>';
      }
    }
    $actors = implode(', ', $links);
    if ($actors == '') $actors = $this->lang->line('media_no_actor');

    $links = array();
    if (is_array($movie->genres))
    {
      foreach($movie->genres as $key => $value)
      {
        $links[] = '<a href="'.site_url('movies/genre/'.$value->id.'/').'">'.$value->name.'</a>';
      }
    }
    $genres = implode(', ', $links);
    if ($genres == '') $genres = $this->lang->line('media_no_genre');

    $links = array();
    if (is_array($movie->studios))
    {
      foreach($movie->studios as $key => $value)
      {
        $links[] = '<a href="'.site_url('movies/studio/'.$value->id.'/').'">'.$value->name.'</a>';
      }
    }
    $studios = implode(', ', $links);
    if ($studios == '') $studios = $this->lang->line('media_no_studio');

    $links = array();
    if (is_array($movie->countries))
    {
      foreach($movie->countries as $key => $value)
      {
        $links[] = '<a href="'.site_url('movies/country/'.$value->id.'/').'">'.$value->name.'</a>';
      }
    }
    $countries = implode(', ', $links);
    if ($countries == '') $countries = $this->lang->line('media_no_country');
    ?>
    <li>
			<h4><a href="<?php echo site_url('movies/'.$movie->id); ?>"><?php echo $movie->local_title; ?></a></h4>
      <div class="left">
				<a href="<?php echo site_url('movies/'.$movie->id); ?>"><img class="poster_thumb" src="<?php echo $movie->poster->url; ?>" /></a>
      </div>
      <div class="item">
      <p>
        <?php echo $this->lang->line('media_directed_by'); ?> <?php echo $directors; ?><br />
        <?php echo $this->lang->line('media_written_by'); ?> <?php echo $writers; ?><br />
        <?php echo $this->lang->line('media_genres'); ?> <?php echo $genres; ?><br />
        <?php echo $this->lang->line('media_studios'); ?> <?php echo $studios; ?><br />
        <?php echo $this->lang->line('media_countries'); ?> <?php echo $countries; ?><br />
        <?php echo $this->lang->line('media_actors'); ?> <?php echo $actors; ?><br />
        <?php echo $this->lang->line('media_year'); ?> <?php echo $movie->year; ?><br />
        <?php echo $this->lang->line('media_runtime'); ?> <?php echo $movie->runtime; ?>
      </p>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>
  <hr class="clear" />
</div><!-- inner -->
<div id="actions-bar" class="actions-bar wat-cf">
	<?php echo $this->my_pagination->create_links(); ?>
</div><!-- actions-bar -->
