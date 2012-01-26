<h2><?php echo $title; ?></h2>
<div class="inner">
  <ul class="list">
    <?php
    foreach($episodes as $episode)
    {
			if (!isset($tvshow_name)) $tvshow_name = '';
			$links = array();
			foreach($episode->writers as $key => $value)
			{
				$links[] = '<a href="'.site_url('actors/'.$value->id).'">'.$value->name.'</a>';
				if ($key >= 1) break;
			}
			$writers = implode(', ', $links);
			if ($writers == '') $writers = $this->lang->line('media_no_writer');

			$links = array();
			foreach($episode->directors as $key => $value)
			{
				$links[] = '<a href="'.site_url('actors/'.$value->id).'">'.$value->name.'</a>';
				if ($key >= 1) break;
			}
			$directors = implode(', ', $links);
			if ($directors == '') $directors = $this->lang->line('media_no_director');

			$episode_number = str_replace('%s', sprintf("%02s", $episode->season_number), $this->lang->line('media_number_format'));
			$episode_number = str_replace('%e', sprintf("%02s", $episode->episode_number), $episode_number);


			$data['episode'] = $episode;
			$data['writers'] = $writers;
			$data['directors'] = $directors;
			$data['tvshow_name'] = $tvshow_name;
			$data['episode_number'] = $episode_number;

			$this->load->view('content/video/episodes/_row', $data);
		}
		?>
  </ul>
  <hr class="clear" />
</div><!-- inner -->
<div id="actions-bar" class="actions-bar wat-cf">
  <?php echo $this->my_pagination->create_links(); ?>
</div><!-- actions-bar -->