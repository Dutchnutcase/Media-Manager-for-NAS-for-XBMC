<?php
//echo '<pre>'.print_r($set, true).'</pre>';
?>
<script type="text/javascript">
<!--
set_id = <?php echo $set->id; ?>;
poster_filename = '<?php echo $set->poster->filename; ?>';
backdrop_filename = '<?php echo $set->backdrop->filename; ?>';
//-->
</script>

<?php if ($this->session->userdata('can_change_infos')): ?>
<script src="<?php echo base_url(); ?>assets/scripts/sets.js" language="javascript" type="text/javascript"></script>
<?php endif; ?>

<?php if ($this->session->userdata('can_change_images')): ?>
<script src="<?php echo base_url(); ?>assets/scripts/images.js" language="javascript" type="text/javascript"></script>
<?php endif; ?>

<?php
if ($this->session->userdata('can_change_images'))
{
		// Formulaire pour ajoût d'une affiche
		$data['image_filename'] = $set->poster->filename;
		$data['type'] = 'poster';
		$this->load->view('includes/_add_image_form', $data);

		// Formulaire pour ajoût d'un fond d'écran
		$data['image_filename'] = $set->backdrop->filename;
		$data['type'] = 'backdrop';
		$this->load->view('includes/_add_image_form', $data);
}
?>

<div style="display: none;">
  <div id="posters-list">
  <?php
  if ($this->session->userdata('can_change_images'))
  {
		foreach($set->movies as $movie)
		{
			$data['posters'] = $movie->images->posters;
			$this->load->view('includes/_posters', $data);
		}
  }
  ?>
  </div>
</div>
<div style="display: none;">
  <div id="backdrops-list">
  <?php
  if ($this->session->userdata('can_change_images'))
  {
		foreach($set->movies as $movie)
		{
			$data['backdrops'] = $movie->images->backdrops;
			$this->load->view('includes/_backdrops', $data);
		}
  }
  ?>
  </div>
</div>

<div id="set-view">
  <div class="block">
    <div class="content">
			<h2><?php echo $set->name; ?></h2>
			<div class="inner">
				<ul class="list" id="movies_list">
					<?php foreach($set->movies as $movie): ?>
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
					<?php endforeach; ?>
				</ul>
        <hr class="clear" />
				<div id="actions-bar" class="actions-bar wat-cf">
					<div class="actions">
						<form action="<?php echo base_url(); ?>sets/delete/<?php echo $set->id; ?>" method="post">
							<?php
							if ($this->session->userdata('can_change_infos'))
							{
								$data['media'] = 'set';
								$this->load->view('includes/buttons/delete_media', $data);
							}
							?>
						</form>
					</div>
				</div><!-- actions-bar -->
			</div><!-- inner -->
    </div><!-- content -->
  </div><!-- block -->
</div><!-- set-vew -->

<div id="sidebar">
	<div class="block notice">
    <h4><?php echo $this->lang->line('media_poster'); ?></h4>
    <p>
      <img id="poster_image" src="<?php echo $set->poster->url; ?>" alt="<?php echo $this->lang->line('title_poster_choice'); ?>" />
    </p>
		<?php if ($this->session->userdata('can_change_images')): ?>
			<div id="poster-actions-bar" class="actions-bar wat-cf">
				<div class="actions">
					<?php
						$data['type'] = 'poster';
						$this->load->view('includes/buttons/add_image', $data);
						$this->load->view('includes/buttons/delete_image', $data);
					?>
				</div>
			</div><!-- actions-bar -->
		<?php endif; ?>
  </div><!-- end block -->
  <div class="block notice">
    <h4><?php echo $this->lang->line('media_backdrop'); ?></h4>
    <p>
      <img id="backdrop_image" src="<?php echo $set->backdrop->url; ?>" alt="<?php echo $this->lang->line('title_backdrop_choice'); ?>" />
    </p>
		<?php if ($this->session->userdata('can_change_images')): ?>
			<div id="backdrop-actions-bar" class="actions-bar wat-cf">
				<div class="actions">
					<?php
						$data['type'] = 'backdrop';
						$this->load->view('includes/buttons/add_image', $data);
						$this->load->view('includes/buttons/delete_image', $data);
					?>
				</div>
			</div><!-- actions-bar -->
		<?php endif; ?>
  </div><!-- end block -->
</div><!-- end sidebar -->
