<script type="text/javascript">
<!--
tvshow_id = <?php echo $episode->tvshow_id; ?>;
//-->
</script>
<script src="<?php echo base_url(); ?>assets/scripts/tabs.js" language="javascript" type="text/javascript"></script>

<?php if ($this->session->userdata('can_change_infos')): ?>
<script src="<?php echo base_url(); ?>assets/scripts/episodes_infos.js" language="javascript" type="text/javascript"></script>
<?php endif; ?>

<div id="main">
  <div class="block">
    <div class="secondary-navigation">
      <ul class="wat-cf">
        <li class="active first"><a href="#block-infos"><?php echo $this->lang->line('navigation_infos'); ?></a></li>
        <li><a href="#block-casting"><?php echo $this->lang->line('navigation_guest_star'); ?></a></li>
      </ul>
    </div>
    <div class="content" id="block-infos">
      <h2><?php echo $episode->local_title; ?></h2>
      <div class="inner">
        <?php
        $links = array();
        foreach($episode->directors as $director)
          $links[] = '<a href="'.site_url('actors/'.$director->id).'">'.$director->name.'</a>';

        $directors = implode(', ', $links);
        if ($directors == '') $directors = $this->lang->line('media_no_director');

        $links = array();
        foreach($episode->writers as $writer)
          $links[] = '<a href="'.site_url('actors/'.$writer->id).'">'.$writer->name.'</a>';

        $writers = implode(', ', $links);
        if ($writers == '') $writers = $this->lang->line('media_no_writer');
        ?>
        <p><?php echo $this->lang->line('media_directed_by'); ?> <?php echo $directors; ?></p>
        <p><?php echo $this->lang->line('media_written_by'); ?> <?php echo $writers; ?></p>
        <p><?php echo $this->lang->line('media_tvshow'); ?> <a href="<?php echo site_url('tvshows/'.$episode->tvshow_id); ?>"><?php echo $episode->tvshow_name; ?></a></p>
        <p><?php echo $this->lang->line('media_season_number'); ?> <?php echo $episode->season_number; ?></p>
        <p><?php echo $this->lang->line('media_episode_number'); ?> <?php echo $episode->episode_number; ?></p>
        <p><?php echo $this->lang->line('media_first_aired'); ?> <?php echo $episode->first_aired; ?></p>
        <p><?php echo $this->lang->line('media_runtime'); ?> <?php echo $episode->runtime; ?></p>
        <p><?php echo $this->lang->line('media_rating'); ?> <?php echo $episode->rating; ?></p>
        <p><?php echo $this->lang->line('media_overview'); ?></p>
        <p id="overview" class="overview_editable"><?php echo $episode->overview; ?></p>
				<div id="actions-bar" class="actions-bar wat-cf">
					<div class="actions">
						<?php
						if ($this->session->userdata('can_change_infos'))
								$this->load->view('includes/buttons/refresh');

						if ($this->session->userdata('can_download_video'))
								$this->load->view('includes/buttons/download');
						?>
					</div>
				</div><!-- actions-bar -->
      </div><!-- inner -->
    </div><!-- content -->
    <div class="content" id="block-casting" style="display: none;">
      <h2><?php echo $this->lang->line('navigation_guest_star'); ?></h2>
      <div class="inner">
        <ul>
          <?php foreach($episode->actors as $actor): ?>
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
    <h4><?php echo $this->lang->line('media_photo'); ?></h4>
    <p>
      <img src="<?php echo $episode->poster->url; ?>" />
    </p>
  </div>
</div><!-- end sidebar -->