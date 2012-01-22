<div id="movies-index">
  <div class="block">
    <div class="content">
			<?php
				$data['title'] = $this->lang->line('list_last_movies');
				$data['movies'] = $last_movies;
				$this->load->view('content/video/movies/_rows', $data);
			?>
    </div><!-- content -->
  </div><!-- block -->
</div><!-- movies-index -->

<div id="tvshows-index">
  <div class="block">
    <div class="content">
			<?php
				$data['title'] = $this->lang->line('list_last_tvshows');
				$data['tvshows'] = $last_tvshows;
				$this->load->view('content/video/tvshows/_rows', $data);
			?>
    </div><!-- content -->
  </div><!-- block -->
</div><!-- media-list -->

<div id="episodes-index">
  <div class="block">
    <div class="content">
			<?php
				$data['title'] = $this->lang->line('list_last_episodes');

				// Même vide, cette variable permet d'affiche le nom de la série pour chaque épisode
				$data['tvshow_name'] = '';

				$data['episodes'] = $last_episodes;
				$this->load->view('content/video/episodes/_rows', $data);
			?>
    </div><!-- content -->
  </div><!-- block -->
</div><!-- episodes-index -->