<div style="display:none">
  <div id="sets-list">
  </div>
</div>
<div style="display:none">
  <div id="refresh-list" style="width:695px;height:420px;overflow:auto;" >
  </div>
</div>
<script type="text/javascript">
<!--
movie_id = <?php echo $movie->id; ?>;
poster_filename = '<?php echo $movie->poster->filename; ?>';
backdrop_filename = '<?php echo $movie->backdrop->filename; ?>';
//-->
</script>
<script src="<?php echo base_url(); ?>assets/scripts/tabs.js" language="javascript" type="text/javascript"></script>

<?php if ($this->session->userdata('can_change_infos')): ?>
<script src="<?php echo base_url(); ?>assets/scripts/movies_infos.js" language="javascript" type="text/javascript"></script>
<?php endif; ?>

<?php if ($this->session->userdata('can_change_images')): ?>
<script src="<?php echo base_url(); ?>assets/scripts/images.js" language="javascript" type="text/javascript"></script>
<?php endif; ?>

<?php
if ($this->session->userdata('can_change_images'))
{
	// Formulaire pour ajoût d'une affiche
	$data['image_filename'] = $movie->poster->filename;
	$data['type'] = 'poster';
	$this->load->view('includes/_add_image_form', $data);

	// Formulaire pour ajoût d'un fond d'écran
	$data['image_filename'] = $movie->backdrop->filename;
	$data['type'] = 'backdrop';
	$this->load->view('includes/_add_image_form', $data);
}
?>

<div style="display: none;">
  <div id="posters-list">
  <?php
  if ($this->session->userdata('can_change_images'))
  {
    $data['posters'] = $movie->images->posters;
    $this->load->view('includes/_posters', $data);
  }
  ?>
  </div>
</div>
<div style="display: none;">
  <div id="backdrops-list">
  <?php
  if ($this->session->userdata('can_change_images'))
  {
    $data['backdrops'] = $movie->images->backdrops;
    $this->load->view('includes/_backdrops', $data);
  }
  ?>
  </div>
</div>

<div id="main">
  <div class="block">
    <div class="secondary-navigation">
      <ul class="wat-cf">
        <li class="active first"><a href="#block-infos"><?php echo $this->lang->line('navigation_infos'); ?></a></li>
        <li><a href="#block-casting"><?php echo $this->lang->line('navigation_casting'); ?></a></li>
      </ul>
    </div>
    <div class="content" id="block-infos">
      <h2><?php echo $movie->local_title; ?></h2>
      <div class="inner">
        <?php
        $links = array();
        foreach($movie->directors as $director)
          $links[] = '<a href="'.site_url('actors/'.$director->id).'">'.$director->name.'</a>';

        $directors = implode(', ', $links);
        if ($directors == '') $directors = $this->lang->line('media_no_director');

        $links = array();
        foreach($movie->writers as $writer)
          $links[] = '<a href="'.site_url('actors/'.$writer->id).'">'.$writer->name.'</a>';

        $writers = implode(', ', $links);
        if ($writers == '') $writers = $this->lang->line('media_no_writer');

        $links = array();
        foreach($movie->genres as $genre)
          $links[] = '<a href="'.site_url('movies/genre/'.$genre->id.'/').'">'.$genre->name.'</a>';

        $genres = implode(', ', $links);
        if ($genres == '') $genres = $this->lang->line('media_no_genre');

        $links = array();
        foreach($movie->studios as $studio)
          $links[] = '<a href="'.site_url('movies/studio/'.$studio->id.'/').'">'.$studio->name.'</a>';

        $studios = implode(', ', $links);
        if ($studios == '') $studios = $this->lang->line('media_no_studio');

        $links = array();
        foreach($movie->countries as $country)
          $links[] = '<a href="'.site_url('movies/country/'.$country->id.'/').'">'.$country->name.'</a>';

        $countries = implode(', ', $links);
        if ($countries == '') $countries = $this->lang->line('media_no_country');

        // Film n'appartenant pas à une saga par défaut
        $movie_in_set = '';

        if (isset($movie->set_order))
        {
          $link = '<a href="'.site_url('sets/'.$movie->set_id.'/').'">'.$movie->set_name.'</a>';
          $movie_in_set = '<p id="movie_in_set">'.sprintf($this->lang->line('media_in_set'), $link).'</p>';
        }
        else
        {
          $movie_in_set = '<p id="movie_in_set" style="display:none"></p>';
        }

				// Film vue ou pas ?
				if ($movie->seen != '')
						$seen = 'vue';
				else
						$seen = $this->lang->line('media_never_seen');

        ?>
        <p><?php echo $this->lang->line('media_original_title'); ?> <?php echo $movie->original_title; ?></p>
        <?php echo $movie_in_set; ?>
        <p id="directors"><?php echo $this->lang->line('media_directed_by'); ?> <?php echo $directors; ?></p>
        <p id="writers"><?php echo $this->lang->line('media_written_by'); ?> <?php echo $writers; ?></p>
        <p id="genres"><?php echo $this->lang->line('media_genres'); ?> <?php echo $genres; ?></p>
        <p id="studios"><?php echo $this->lang->line('media_studios'); ?> <?php echo $studios; ?></p>
        <p id="countries"><?php echo $this->lang->line('media_countries'); ?> <?php echo $countries; ?></p>
        <p><?php echo $this->lang->line('media_external_link'); ?> <?php echo $movie->external_link; ?></p>
        <p id="mpaa"><?php echo $this->lang->line('media_mpaa'); ?> <?php echo $movie->mpaa; ?></p>
        <p id="year"><?php echo $this->lang->line('media_year'); ?> <?php echo $movie->year; ?></p>
        <p id="runtime"><?php echo $this->lang->line('media_runtime'); ?> <?php echo $movie->runtime; ?></p>
        <p id="seen"><?php echo $this->lang->line('media_seen'); ?> <?php echo $seen; ?></p>
        <p><?php echo $this->lang->line('media_rating'); ?> <?php echo $movie->rating; ?></p>
        <p><?php echo $this->lang->line('media_votes'); ?> <?php echo $movie->votes; ?></p>
        <p><?php echo $this->lang->line('media_path'); ?> <?php echo $movie->path.$movie->filename; ?></p>
        <p><?php echo $this->lang->line('media_tagline'); ?></p>
        <p id="tagline" class="tagline_editable"><?php echo $movie->tagline; ?></p>
        <p><?php echo $this->lang->line('media_overview'); ?></p>
        <p id="overview" class="overview_editable"><?php echo $movie->overview; ?></p>
				<div id="actions-bar" class="actions-bar wat-cf">
					<div class="actions">
						<?php
						if ($this->session->userdata('can_change_infos'))
								$this->load->view('includes/buttons/refresh');

						if ($this->session->userdata('can_change_infos'))
						{
							// Film dans une saga ?
							if ($movie->set_id != 0)
									$this->load->view('includes/buttons/remove_from_set');
							else
									$this->load->view('includes/buttons/add_to_set');
						}

						if ($this->session->userdata('can_download_video'))
								$this->load->view('includes/buttons/download');
						?>
					</div>
				</div><!-- actions-bar -->
      </div><!-- inner -->
    </div><!-- content -->
    <div class="content" id="block-casting" style="display: none;">
      <h2><?php echo $this->lang->line('navigation_casting'); ?></h2>
      <div class="inner">
          <ul>
            <?php foreach($movie->actors as $actor): ?>
            <li><a title="<?php echo $actor->name; ?>" href="<?php echo site_url('actors/'.$actor->id); ?>"><img src="<?php echo $actor->photo->url; ?>" /></a>
              <h3><?php echo $actor->name; ?></h3>
              <p><?php echo $this->lang->line('media_role'); ?> <?php echo $actor->role; ?></p>
            </li>
            <?php endforeach; ?>
          </ul>
          <hr class="clear" />
      </div><!-- inner -->
    </div><!-- content -->
  </div><!-- block -->
</div><!-- end main -->

<div id="sidebar">
	<div class="block notice">
    <h4><?php echo $this->lang->line('media_poster'); ?></h4>
    <p>
      <img id="poster_image" src="<?php echo $movie->poster->url; ?>" alt="<?php echo $this->lang->line('title_poster_choice'); ?>" />
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
      <img id="backdrop_image" src="<?php echo $movie->backdrop->url; ?>" alt="<?php echo $this->lang->line('title_backdrop_choice'); ?>" />
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
