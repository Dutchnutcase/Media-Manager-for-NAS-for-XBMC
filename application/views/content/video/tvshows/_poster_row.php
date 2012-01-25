<li>
	<h4><a href="<?php echo site_url('tvshows/'.$tvshow->id); ?>"><?php echo $tvshow->title; ?></a></h4>
	<div class="left">
		<a href="<?php echo site_url('tvshows/'.$tvshow->id); ?>"><img class="poster_thumb" src="<?php echo $tvshow->poster->url; ?>" /></a>
	</div>
	<div class="item">
	<p>
		<?php echo $this->lang->line('media_genres'); ?> <?php echo $genres; ?><br />
		<?php echo $this->lang->line('media_studios'); ?> <?php echo $studios; ?><br />
		<?php echo $this->lang->line('media_year'); ?> <?php echo $tvshow->year; ?><br />
	</p>
	</div>
</li>