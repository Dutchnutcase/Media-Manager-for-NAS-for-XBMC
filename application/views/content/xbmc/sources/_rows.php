<h2><?php echo $title; ?></h2>
<div class="inner">
  <table id="sources-list" class="table">
    <tr>
      <th><?php echo $this->lang->line('sources_client_path'); ?></th>
      <th><?php echo $this->lang->line('sources_server_path'); ?></th>
      <th><?php echo $this->lang->line('sources_content'); ?></th>
      <th><?php echo $this->lang->line('sources_scraper'); ?></th>
      <th class="last"><?php echo $this->lang->line('sources_actions'); ?></th>
    </tr>
    <?php
    foreach($sources as $key => $value)
    {
      $data['key'] = $key;
      $data['value'] = $value;
      $this->load->view('content/xbmc/sources/_row', $data);
    }
    ?>
  </table>
  <hr class="clear" />
	<div id="actions-bar" class="actions-bar wat-cf">
		<?php echo $this->my_pagination->create_links(); ?>
	</div><!-- actions-bar -->
</div><!-- inner -->
