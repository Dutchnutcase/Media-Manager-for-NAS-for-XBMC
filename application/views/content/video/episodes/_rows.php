<?php
//echo '<pre>'.print_r($episodes[0], true).'</pre>';
?>
<h2><?php echo $title; ?></h2>
<div class="inner">
  <ul class="list">
    <?php foreach($episodes as $episode): ?>
    <?php
    $links = array();
    foreach($episode->writers as $key => $value)
    {
      $links[] = '<a href="'.site_url('actors/'.$value->id).'">'.$value->name.'</a>';
      if ($key >= 1) break;
    }
    $writers = implode(', ', $links);
    if ($writers == '') $writers = $this->lang->line('media_no_writer');

    $links = array();
    foreach($episode->directors as $key => $value)
    {
      $links[] = '<a href="'.site_url('actors/'.$value->id).'">'.$value->name.'</a>';
      if ($key >= 1) break;
    }
    $directors = implode(', ', $links);
    if ($directors == '') $directors = $this->lang->line('media_no_director');

    $episode_number = str_replace('%s', sprintf("%02s", $episode->season_number), $this->lang->line('media_number_format'));
    $episode_number = str_replace('%e', sprintf("%02s", $episode->episode_number), $episode_number);

    ?>
    <li>
			<?php	if (isset($tvshow_name)) echo '<h2>'.$episode->tvshow_name.'</h2>'; ?>
      <div class="left">
      <a href="<?php echo site_url('episodes/'.$episode->tvshow_id.'/'.$episode->season_number.'/'.$episode->episode_number); ?>"><img class="episode_thumb" src="<?php echo $episode->poster->url; ?>" /></a>
      </div>
      <div class="item">
      <h4><a href="<?php echo site_url('episodes/'.$episode->tvshow_id.'/'.$episode->season_number.'/'.$episode->episode_number); ?>"><?php echo $episode_number.$episode->local_title; ?></a></h4>
      <p>
				<?php echo $this->lang->line('media_directed_by'); ?> <?php echo $directors; ?><br />
				<?php echo $this->lang->line('media_written_by'); ?> <?php echo $writers; ?><br />
				<?php echo $this->lang->line('media_first_aired'); ?> <?php echo $episode->first_aired; ?><br />
				<?php echo $this->lang->line('media_runtime'); ?> <?php echo $episode->runtime; ?>
      </p>
      </div>
      <div class="overview">
				<p><?php echo $this->lang->line('media_overview'); ?></p>
				<p id="overview" class="overview_editable"><?php echo $episode->overview; ?></p>
			</div>
    </li>
    <?php endforeach; ?>
  </ul>
  <hr class="clear" />
</div><!-- inner -->
<div id="actions-bar" class="actions-bar wat-cf">
  <?php echo $this->my_pagination->create_links(); ?>
</div><!-- actions-bar -->
