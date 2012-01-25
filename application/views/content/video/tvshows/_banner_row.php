<li>
	<p><a href="<?php echo site_url('tvshows/'.$tvshow->id); ?>"><img class="banner_thumb" src="<?php echo $tvshow->poster->url; ?>" /></a></p>
	<h4><a href="<?php echo site_url('tvshows/'.$tvshow->id); ?>"><?php echo $tvshow->title; ?></a></h4>
	<?php echo $this->lang->line('media_genres'); ?> <?php echo $genres; ?><br />
	<?php echo $this->lang->line('media_studios'); ?> <?php echo $studios; ?><br />
	<?php echo $this->lang->line('media_year'); ?> <?php echo $tvshow->year; ?><br />
</li>
