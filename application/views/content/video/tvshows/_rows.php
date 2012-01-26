<ul class="list">
	<?php
	foreach($tvshows as $tvshow)
	{
		$links = array();
		foreach($tvshow->genres as $key => $value)
		{
			$links[] = '<a href="'.site_url('tvshows/genre/'.$value->id.'/').'">'.$value->name.'</a>';
			if ($key >= 1) break;
		}
		$genres = implode(', ', $links);
		if ($genres == '') $genres = $this->lang->line('media_no_genre');

		$links = array();
		foreach($tvshow->studios as $key => $value)
		{
			$links[] = '<a href="'.site_url('tvshows/studio/'.$value->id.'/').'">'.$value->name.'</a>';
			if ($key >= 1) break;
		}
		$studios = implode(', ', $links);
		if ($studios == '') $studios = $this->lang->line('media_no_studio');

		$data['tvshow'] = $tvshow;
		$data['genres'] = $genres;
		$data['studios'] = $studios;

		// Poster ou banner pour les vignettes ?
		if ($tvshow->source->settings->posters != '')
		{
			$this->load->view('content/video/tvshows/_poster_row', $data);
		}
		else
		{
			$this->load->view('content/video/tvshows/_banner_row', $data);
		}
	}
	?>
</ul>