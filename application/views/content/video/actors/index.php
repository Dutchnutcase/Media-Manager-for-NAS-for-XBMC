<div id="actors-index">
  <div class="block">
    <div class="content">
      <h2 class="title"><?php echo $title; ?></h2>
      <div class="inner">
				<?php
				if (count($actors) > 0)
				{
					$data['actors'] = $actors;
					$this->load->view('content/video/actors/_rows', $data);
				}
				else
				{
					echo '<h4>'.$this->lang->line('list_no_actor').'</h4>';
				}
				?>
        <hr class="clear" />
      </div><!-- inner -->
      <div id="actions-bar" class="actions-bar wat-cf">
        <?php echo $this->my_pagination->create_links(); ?>
      </div><!-- actions-bar -->
    </div><!-- content -->
  </div><!-- block -->
</div><!-- actors-index -->