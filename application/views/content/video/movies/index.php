<div id="movies-index">
  <div class="block">
    <div class="content">
			<?php
				$data['title'] = $title;
				$data['movies'] = $movies;
				$this->load->view('content/video/movies/_rows', $data);
			?>
    </div><!-- content -->
  </div><!-- block -->
</div><!-- movies-index -->
