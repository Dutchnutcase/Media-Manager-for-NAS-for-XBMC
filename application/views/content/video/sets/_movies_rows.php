<ul class="list" id="movies_list">
	<?php
	foreach($set->movies as $movie)
	{
		$data['movie'] = $movie;
		$this->load->view('content/video/sets/_movies_row', $data);
	}
	?>
</ul>