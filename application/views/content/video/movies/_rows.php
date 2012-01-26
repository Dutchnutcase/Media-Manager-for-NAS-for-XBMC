<ul class="list">
	<?php
	foreach($movies as $movie)
	{
		$links = array();
		if (is_array($movie->writers))
		{
			foreach($movie->writers as $key => $value)
			{
				$links[] = '<a href="'.site_url('actors/'.$value->id).'">'.$value->name.'</a>';
			}
		}
		$writers = implode(', ', $links);
		if ($writers == '') $writers = $this->lang->line('media_no_writer');

		$links = array();
		if (is_array($movie->directors))
		{
			foreach($movie->directors as $key => $value)
				{
					$links[] = '<a href="'.site_url('actors/'.$value->id).'">'.$value->name.'</a>';
				}
		}
		$directors = implode(', ', $links);
		if ($directors == '') $directors = $this->lang->line('media_no_director');

		$links = array();
		if (is_array($movie->actors))
		{
			foreach($movie->actors as $key => $value)
			{
				$links[] = '<a href="'.site_url('actors/'.$value->id).'">'.$value->name.'</a>';
			}
		}
		$actors = implode(', ', $links);
		if ($actors == '') $actors = $this->lang->line('media_no_actor');

		$links = array();
		if (is_array($movie->genres))
		{
			foreach($movie->genres as $key => $value)
			{
				$links[] = '<a href="'.site_url('movies/genre/'.$value->id.'/').'">'.$value->name.'</a>';
			}
		}
		$genres = implode(', ', $links);
		if ($genres == '') $genres = $this->lang->line('media_no_genre');

		$links = array();
		if (is_array($movie->studios))
		{
			foreach($movie->studios as $key => $value)
			{
				$links[] = '<a href="'.site_url('movies/studio/'.$value->id.'/').'">'.$value->name.'</a>';
			}
		}
		$studios = implode(', ', $links);
		if ($studios == '') $studios = $this->lang->line('media_no_studio');

		$links = array();
		if (is_array($movie->countries))
		{
			foreach($movie->countries as $key => $value)
			{
				$links[] = '<a href="'.site_url('movies/country/'.$value->id.'/').'">'.$value->name.'</a>';
			}
		}
		$countries = implode(', ', $links);
		if ($countries == '') $countries = $this->lang->line('media_no_country');

		$data['movie'] = $movie;
		$data['writers'] = $writers;
		$data['directors'] = $directors;
		$data['actors'] = $actors;
		$data['genres'] = $genres;
		$data['studios'] = $studios;
		$data['countries'] = $countries;

		$this->load->view('content/video/movies/_row', $data);
	}
	?>
</ul>