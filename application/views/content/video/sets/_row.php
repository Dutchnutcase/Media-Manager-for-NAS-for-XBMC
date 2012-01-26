<li>
	<h4><a href="<?php echo site_url('sets/'.$set->id); ?>"><?php echo $set->name; ?></a></h4>
	<div class="left">
		<a href="<?php echo site_url('sets/'.$set->id); ?>"><img class="poster_thumb" src="<?php echo $set->poster->url; ?>" /></a>
	</div>
	<div class="item">
		<p>
			<?php
			if ($set->total > 1)
				echo $set->total.' '.$this->lang->line('media_movies');
			else
				echo $set->total.' '.$this->lang->line('media_movie');
			?>
		</p>
	</div>
</li>