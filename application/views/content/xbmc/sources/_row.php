<tr id="<?php echo $value->id; ?>" class="<?php echo ($key % 2 == 0) ? 'odd' : 'even'; ?>">
  <td><?php echo $value->client_path; ?></td>
  <td><?php echo $value->server_path; ?></td>
  <td><?php echo $value->content; ?></td>
  <td><?php echo $value->scraper; ?></td>
  <td class="last">
    <?php
    $data['tabindex'] = $value->id;
    $this->load->view('includes/buttons/edit-source', $data);
    ?>
  </td>
</tr>