<?php	if ($this->session->userdata('can_change_infos')) : ?>
	<li id="<?php echo $movie->id; ?>"><span><img src="<?php echo base_url(); ?>assets/gui/sort.png" title="<?php echo $this->lang->line('title_set_change_order'); ?>" /></span>
<?php else:	?>
	<li>
<?php endif; ?>
		<h4><a href="<?php echo site_url('movies/'.$movie->id); ?>"><?php echo $movie->local_title; ?></a></h4>
		<div class="item">
			<a href="<?php echo site_url('movies/'.$movie->id); ?>"><img class="poster_thumb" src="<?php echo $movie->poster->url; ?>" /></a>
		</div>
	</li>