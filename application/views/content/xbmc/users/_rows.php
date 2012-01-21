<h2><?php echo $title; ?></h2>
<div class="inner">
  <table id="users-list" class="table">
    <tr>
      <th><?php echo $this->lang->line('users_names'); ?></th>
      <th><?php echo $this->lang->line('users_images'); ?></th>
      <th><?php echo $this->lang->line('users_infos'); ?></th>
      <th>
        <img src="<?php echo base_url(); ?>assets/gui/download.png" />&nbsp;<?php echo $this->lang->line('users_videos'); ?>
      </th>
      <th>
        <img src="<?php echo base_url(); ?>assets/gui/download.png" />&nbsp;<?php echo $this->lang->line('users_musics'); ?>
      </th>
      <th><?php echo $this->lang->line('users_accounts'); ?></th>
      <th><?php echo $this->lang->line('users_settings'); ?></th>
      <th class="last"><?php echo $this->lang->line('users_actions'); ?></th>
    </tr>
    <?php
    foreach($users as $key => $value)
    {
      $data['key'] = $key;
      $data['value'] = $value;
      $this->load->view('content/xbmc/users/_row', $data);
    }
    ?>
  </table>
  <hr class="clear" />
	<div id="user_add" class="inner" style="display:none;">
		<?php $this->load->view('content/xbmc/users/_add_form'); ?>
	</div><!-- inner -->
	<div id="actions-bar" class="actions-bar wat-cf">
		<div class="actions">
			<?php $this->load->view('includes/buttons/add-user'); ?>
		</div>
		<?php echo $this->my_pagination->create_links(); ?>
	</div><!-- actions-bar -->
</div><!-- inner -->
