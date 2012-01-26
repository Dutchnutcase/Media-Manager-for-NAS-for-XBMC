<div id="tvshows-index">
  <div class="block">
    <div class="content">
			<h2 class="title"><?php echo $title; ?></h2>
			<div class="inner">
				<?php
				if (count($tvshows) > 0)
				{
					$data['tvshows'] = $tvshows;
					$this->load->view('content/video/tvshows/_rows', $data);
				}
				else
				{
					echo '<h4>'.$this->lang->line('list_no_tvshow').'</h4>';
				}
				?>
				<hr class="clear" />
			</div><!-- inner -->
			<div id="actions-bar" class="actions-bar wat-cf">
				<?php echo $this->my_pagination->create_links(); ?>
			</div><!-- actions-bar -->
    </div><!-- content -->
  </div><!-- block -->
</div><!-- media-list -->