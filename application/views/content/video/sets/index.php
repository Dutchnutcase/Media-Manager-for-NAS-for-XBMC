<?php
//echo '<pre>'.print_r($sets, true).'</pre>';
?>
<?php if ($this->session->userdata('can_change_infos')): ?>
<script src="<?php echo base_url(); ?>assets/scripts/sets.js" language="javascript" type="text/javascript"></script>
<?php endif; ?>
<div id="sets-index">
  <div class="block">
    <div class="content">
      <h2><?php echo $title; ?></h2>
      <div class="inner">
        <ul class="list">
          <?php foreach($sets as $set): ?>
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
          <?php endforeach; ?>
        </ul>
        <hr class="clear" />
				<div id="actions-bar" class="actions-bar wat-cf">
					<div class="actions">
						<?php
						if ($this->session->userdata('can_change_infos'))
								$this->load->view('includes/buttons/add-set');
						?>
					</div>
					<?php echo $this->my_pagination->create_links(); ?>
				</div><!-- actions-bar -->
      </div><!-- inner -->
      <div id="add-set-form" class="inner">
        <?php $this->load->view('content/video/sets/_add-form'); ?>
      </div><!-- inner -->
    </div><!-- content -->
  </div><!-- block -->
</div><!-- sets-list -->
