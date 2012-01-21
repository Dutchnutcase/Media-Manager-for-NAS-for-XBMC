<tr id="<?php echo $value->id; ?>" class="<?php echo ($key % 2 == 0) ? 'odd' : 'even'; ?>">
  <td><?php echo $value->username; ?></td>
  <td>
    <?php
    if ($value->can_change_images)
        echo img(array(
                    'src' => base_url().'assets/gui/tick.png',
                    'title' => $this->lang->line('users_can_change_images')
                    ));
    else
        echo img(array(
                    'src' => base_url().'assets/gui/cross.png',
                    'title' => $this->lang->line('users_not_can_change_images')
                    ));
    ?>
  </td>
  <td>
    <?php
    if ($value->can_change_infos)
        echo img(array(
                    'src' => base_url().'assets/gui/tick.png',
                    'title' => $this->lang->line('users_can_change_infos')
                    ));
    else
        echo img(array(
                    'src' => base_url().'assets/gui/cross.png',
                    'title' => $this->lang->line('users_not_can_change_infos')
                    ));
    ?>
  </td>
  <td>
    <?php
    if ($value->can_download_video)
        echo img(array(
                    'src' => base_url().'assets/gui/tick.png',
                    'title' => $this->lang->line('users_can_download_video')
                    ));
    else
        echo img(array(
                    'src' => base_url().'assets/gui/cross.png',
                    'title' => $this->lang->line('users_not_can_download_video')
                    ));
    ?>
  </td>
  <td>
    <?php
    if ($value->can_download_music)
        echo img(array(
                    'src' => base_url().'assets/gui/tick.png',
                    'title' => $this->lang->line('users_can_download_music')
                    ));
    else
        echo img(array(
                    'src' => base_url().'assets/gui/cross.png',
                    'title' => $this->lang->line('users_not_can_download_music')
                    ));
    ?>
  </td>
  <td>
    <?php
    if ($value->is_active)
        echo img(array(
                    'src' => base_url().'assets/gui/tick.png',
                    'title' => $this->lang->line('users_is_active')
                    ));
    else
        echo img(array(
                    'src' => base_url().'assets/gui/cross.png',
                    'title' => $this->lang->line('users_not_is_active')
                    ));
    ?>
  </td>
  <td>
    <?php
    if ($value->is_admin)
        echo img(array(
                    'src' => base_url().'assets/gui/tick.png',
                    'title' => $this->lang->line('users_is_admin')
                    ));
    else
        echo img(array(
                    'src' => base_url().'assets/gui/cross.png',
                    'title' => $this->lang->line('users_not_is_admin')
                    ));
    ?>
  </td>
  <td class="last">
    <?php
    $data['tabindex'] = $value->id;
    $this->load->view('includes/buttons/edit-user', $data);
    $this->load->view('includes/buttons/delete-user', $data);
    ?>
  </td>
</tr>
