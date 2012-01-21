<?php
//echo '<pre>'.print_r($tvshow, true).'</pre>';
?>
<script type="text/javascript">
<!--
tvshow_id = <?php echo $tvshow->id; ?>;
poster_filename = '<?php echo $tvshow->poster->filename; ?>';

// ** Attention ** l'objet tvshow a un poster défini même si il est au format 'bannière' ** Attention **
// Doit quand même être défini pour la gestion du changement de poster
banner_filename = '<?php echo $tvshow->poster->filename; ?>';
backdrop_filename = '<?php echo $tvshow->backdrop->filename; ?>';
//-->
</script>
<script src="<?php echo base_url(); ?>assets/scripts/tabs.js" language="javascript" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/scripts/ajax_pagination.js" language="javascript" type="text/javascript"></script>

<?php if ($this->session->userdata('can_change_infos')): ?>
<script src="<?php echo base_url(); ?>assets/scripts/tvshows_infos.js" language="javascript" type="text/javascript"></script>
<?php endif; ?>

<?php if ($this->session->userdata('can_change_images')): ?>
<script src="<?php echo base_url(); ?>assets/scripts/images.js" language="javascript" type="text/javascript"></script>
<?php endif; ?>

<?php
if ($this->session->userdata('can_change_images'))
{
		// Formulaire pour ajoût d'une affiche
		$data['image_filename'] = $tvshow->poster->filename;
		$data['type'] = 'poster';
		$this->load->view('includes/_add_image_form', $data);

		// Formulaire pour ajoût d'un fond d'écran
		$data['image_filename'] = $tvshow->backdrop->filename;
		$data['type'] = 'backdrop';
		$this->load->view('includes/_add_image_form', $data);
}
?>

<div style="display: none;">
  <div id="posters-list">
  <?php
  if ($this->session->userdata('can_change_images'))
  {
    $data['posters'] = $tvshow->images->posters;
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
    $data['backdrops'] = $tvshow->images->backdrops;
    $this->load->view('includes/_backdrops', $data);
  }
  ?>
  </div>
</div>
<div style="display: none;">
  <div id="banners-list">
  <?php
  if ($this->session->userdata('can_change_images'))
  {
    $data['banners'] = $tvshow->images->banners;
    $this->load->view('includes/_banners', $data);
  }
  ?>
  </div>
</div>

<div id="tvshow-view">
  <div class="block">
    <div class="secondary-navigation">
      <ul class="wat-cf">
        <li class="active first"><a href="#block-infos"><?php echo $this->lang->line('navigation_infos'); ?></a></li>
        <li><a href="#block-casting"><?php echo $this->lang->line('navigation_casting'); ?></a></li>
        <?php foreach($seasons as $season): ?>
        <?php
        $season_name = sprintf($this->lang->line('navigation_season'), $season->id);
        if ($season->id == 0) $season_name = $this->lang->line('navigation_special_season');
        ?>
        <li><a id="season-<?php echo $season->id; ?>" href="#block-season-<?php echo $season->id; ?>"><?php echo $season_name; ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="content" id="block-infos">
      <h2><?php echo $tvshow->title; ?></h2>
      <div class="inner">
        <?php
        $links = array();
        foreach($tvshow->genres as $genre)
          $links[] = '<a href="'.site_url('tvshows/genre/'.$genre->id.'/').'">'.$genre->name.'</a>';

        $genres = implode(', ', $links);
        if ($genres == '') $genres = $this->lang->line('media_no_genre');

        $links = array();
        foreach($tvshow->studios as $studio)
          $links[] = '<a href="'.site_url('tvshows/studio/'.$studio->id.'/').'">'.$studio->name.'</a>';

        $studios = implode(', ', $links);
        if ($studios == '') $studios = $this->lang->line('media_no_studio');

        ?>
        <p><?php echo $this->lang->line('media_genres'); ?> <?php echo $genres; ?></p>
        <p><?php echo $this->lang->line('media_studios'); ?> <?php echo $studios; ?></p>
        <p><?php echo $this->lang->line('media_total_seasons'); ?> <?php echo $tvshow->total_seasons; ?></p>
        <p><?php echo $this->lang->line('media_total_episodes'); ?> <?php echo $tvshow->total_episodes; ?></p>
        <p><?php echo $this->lang->line('media_external_link'); ?> <?php echo $tvshow->external_link; ?></p>
        <p><?php echo $this->lang->line('media_mpaa'); ?> <?php echo $tvshow->mpaa; ?></p>
        <p><?php echo $this->lang->line('media_first_aired'); ?> <?php echo $tvshow->first_aired; ?></p>
        <p><?php echo $this->lang->line('media_year'); ?> <?php echo $tvshow->year; ?></p>
        <p><?php echo $this->lang->line('media_rating'); ?> <?php echo $tvshow->rating; ?></p>
        <p><?php echo $this->lang->line('media_overview'); ?></p>
        <p id="overview" class="overview_editable"><?php echo $tvshow->overview; ?></p>
				<div id="actions-bar" class="actions-bar wat-cf">
					<div class="actions">
						<?php
						if ($this->session->userdata('can_change_infos'))
								$this->load->view('includes/buttons/refresh');
						?>
					</div>
				</div><!-- actions-bar -->
      </div><!-- inner -->
    </div><!-- content -->
    <div class="content" id="block-casting" style="display: none;">
      <h2><?php echo $this->lang->line('navigation_casting'); ?></h2>
      <div class="inner">
        <ul>
          <?php foreach($tvshow->actors as $actor): ?>
          <li><a title="<?php echo $actor->name; ?>" href="<?php echo site_url('actors/'.$actor->id); ?>"><img src="<?php echo $actor->photo->url; ?>" /></a>
            <h3><?php echo $actor->name; ?></h3>
            <p><?php echo $this->lang->line('media_role'); ?> <?php echo $actor->role; ?></p>
          </li>
          <?php endforeach; ?>
        </ul>
        <hr class="clear" />
      </div><!-- inner -->
    </div><!-- content -->

    <?php foreach($seasons as $season): ?>
      <div class="content" id="block-season-<?php echo $season->id; ?>" style="display: none;">
      </div><!-- season -->
    <?php endforeach; ?>

  </div><!-- block -->
</div><!-- end main -->

<div id="sidebar">
  <div class="block notice">
    <h4><?php echo $this->lang->line('media_'.$tvshow->poster->type); ?></h4>
		<p>
			<img id="<?php echo $tvshow->poster->type; ?>_image" src="<?php echo $tvshow->poster->url; ?>" alt="<?php echo $this->lang->line('title_'.$tvshow->poster->type.'_choice'); ?>" />
		</p>
		<?php if ($this->session->userdata('can_change_images')): ?>
			<div id="poster-actions-bar" class="actions-bar wat-cf">
				<div class="actions">
					<?php
						$data['type'] = $tvshow->poster->type;
						$this->load->view('includes/buttons/add_image', $data);
						$this->load->view('includes/buttons/delete_image', $data);
					?>
				</div>
			</div><!-- actions-bar -->
		<?php endif; ?>
  </div>
  <div class="block notice">
    <h4><?php echo $this->lang->line('media_backdrop'); ?></h4>
		<p>
			<img id="backdrop_image" src="<?php echo $tvshow->backdrop->url; ?>" alt="<?php echo $this->lang->line('title_backdrop_choice'); ?>" />
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
  </div>
</div><!-- end sidebar -->
