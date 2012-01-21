<script language="javascript" type="text/javascript">
actor_id = <?php echo $actor->id; ?>;
</script>

<script src="<?php echo base_url(); ?>assets/scripts/tabs.js" language="javascript" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/scripts/ajax_pagination.js" language="javascript" type="text/javascript"></script>

<?php if ($this->session->userdata('can_change_images')): ?>
<script src="<?php echo base_url(); ?>assets/scripts/images.js" language="javascript" type="text/javascript"></script>
<?php endif; ?>

<?php
if ($this->session->userdata('can_change_images'))
{
		// Formulaire pour ajoût d'une photo
		$data['image_filename'] = $actor->photo->filename;
		$data['type'] = 'photo';
		$this->load->view('includes/_add_image_form', $data);
}
?>

<div id="main">
  <div class="block">
    <div class="secondary-navigation">
      <ul class="wat-cf">

        <?php if ($movies_written != ''): ?>
          <li><a href="#block-writer-movies"><?php echo $this->lang->line('navigation_writer_movies'); ?></a></li>
        <?php endif; ?>

        <?php if ($movies_directed != ''): ?>
          <li><a href="#block-director-movies"><?php echo $this->lang->line('navigation_director_movies'); ?></a></li>
        <?php endif; ?>

        <?php if ($movies_played != ''): ?>
          <li><a href="#block-actor-movies"><?php echo $this->lang->line('navigation_actor_movies'); ?></a></li>
        <?php endif; ?>

        <?php if ($tvshows_played != ''): ?>
          <li><a href="#block-actor-tvshows"><?php echo $this->lang->line('navigation_actor_tvshows'); ?></a></li>
        <?php endif; ?>

        <?php if ($episodes_written != ''): ?>
          <li><a href="#block-writer-episodes"><?php echo $this->lang->line('navigation_writer_episodes'); ?></a></li>
        <?php endif; ?>

        <?php if ($episodes_directed != ''): ?>
          <li><a href="#block-director-episodes"><?php echo $this->lang->line('navigation_director_episodes'); ?></a></li>
        <?php endif; ?>

      </ul>
    </div>

    <div class="content" id="block-writer-movies">
      <?php echo $movies_written; ?>
    </div><!-- content -->

    <div class="content" id="block-director-movies">
      <?php echo $movies_directed; ?>
    </div><!-- content -->

    <div class="content" id="block-actor-movies">
      <?php echo $movies_played; ?>
    </div><!-- content -->

    <div class="content" id="block-actor-tvshows">
      <?php echo $tvshows_played; ?>
    </div><!-- content -->

    <div class="content" id="block-writer-episodes">
      <?php echo $episodes_written; ?>
    </div><!-- content -->

    <div class="content" id="block-director-episodes">
      <?php echo $episodes_directed; ?>
    </div><!-- content -->

  </div><!-- block -->
</div><!-- end main -->

<div id="sidebar">
	<div class="block notice">
    <h4><?php echo $this->lang->line('media_photo'); ?></h4>
    <p>
      <img id="photo_image" src="<?php echo $actor->photo->url; ?>" alt="<?php echo $this->lang->line('title_phto_choice'); ?>" />
    </p>
		<?php if ($this->session->userdata('can_change_images')): ?>
			<div id="actions-bar" class="actions-bar wat-cf">
				<div class="actions">
					<?php
						$data['type'] = 'photo';
						$this->load->view('includes/buttons/add_image', $data);
						$this->load->view('includes/buttons/delete_image', $data);
					?>
				</div>
			</div><!-- actions-bar -->
		<?php endif; ?>
  </div><!-- end block -->
</div><!-- end sidebar -->
