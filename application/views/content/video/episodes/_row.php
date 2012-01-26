<li>
	<?php	if ($tvshow_name != '') echo '<h2>'.$episode->tvshow_name.'</h2>'; ?>
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