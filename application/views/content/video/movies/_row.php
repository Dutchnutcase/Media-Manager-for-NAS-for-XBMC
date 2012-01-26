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