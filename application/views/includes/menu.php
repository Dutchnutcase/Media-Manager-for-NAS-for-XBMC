<?php
// Liste des menus pour le site et l'administration
$menus = $this->config->item('menus');

// Si un utilisateur est connecté
if ($this->session->userdata('user_id'))
{
	// Si un administrateur est dans l'administration
	if ($this->session->userdata('in_admin'))
	{
		// On sélectionne le menu de l'administration
		$entries = $menus['admin'];

		$menu_entries = array();

		$menu_entry = new stdClass();
		$menu_entry->title = $this->lang->line('menu_dashboard');
		$menu_entry->link = 'admin';
		$menu_entry->class = '';

		// Un ou des segment(s) dans l'url, alors ce n'est pas l'accueil
		if (count($this->uri->segments) == 1)
		{
			$menu_entry->class = ' class="active"';
		}
		$menu_entries[] = $menu_entry;

		foreach($entries as $entry)
		{
			$menu_entry = new stdClass();
			$menu_entry->title = $this->lang->line('menu_'.$entry);
			$menu_entry->link = 'admin/'.$entry;
			$menu_entry->class = '';

			if (count($this->uri->segments) > 1)
			{
				if ($this->uri->segments[2] == $entry)
				{
					$menu_entry->class = ' class="active"';
				}
			}
			$menu_entries[] = $menu_entry;
		}
	}
	else
	{
		$entries = $menus['site'];

		$menu_entries = array();

		$menu_entry = new stdClass();
		$menu_entry->title = $this->lang->line('menu_home');
		$menu_entry->link = '';
		$menu_entry->class = '';

		if (count($this->uri->segments) == 0)
		{
			$menu_entry->class = ' class="active"';
		}
		$menu_entries[] = $menu_entry;
		
		foreach($entries as $entry)
		{
			$menu_entry = new stdClass();
			$menu_entry->title = $this->lang->line('menu_'.$entry);
			$menu_entry->link = $entry;
			$menu_entry->class = '';

			// Un ou des segment(s) dans l'url, alors ce n'est pas l'accueil
			if (count($this->uri->segments) > 0)
			{
				if ($this->uri->segments[1] == $entry)
				{
					$menu_entry->class = ' class="active"';
				}
			}
			$menu_entries[] = $menu_entry;
		}
	}
}
else
{
	$entries = $menus['site'];

	$menu_entries = array();

	$menu_entry = new stdClass();
	$menu_entry->title = $this->lang->line('menu_home');
	$menu_entry->link = '';
	$menu_entry->class = '';

	if (count($this->uri->segments) == 0)
	{
		$menu_entry->class = ' class="active"';
	}
	$menu_entries[] = $menu_entry;

	foreach($entries as $entry)
	{
		$menu_entry = new stdClass();
		$menu_entry->title = $this->lang->line('menu_'.$entry);
		$menu_entry->link = $entry;
		$menu_entry->class = '';

		// Un ou des segment(s) dans l'url, alors ce n'est pas l'accueil
		if (count($this->uri->segments) > 0)
		{
			if ($this->uri->segments[1] == $entry)
			{
				$menu_entry->class = ' class="active"';
			}
		}
		$menu_entries[] = $menu_entry;
	}
}
?>
<div id="main-navigation">
	<ul class="wat-cf">
		<?php
			foreach($menu_entries as $menu_entry)
			{
				echo '<li'.$menu_entry->class.'><a href="'.site_url($menu_entry->link).'">'.$menu_entry->title.'</a></li>';
			}

			// Un seul segment dans l'uri ?
			if (count($this->uri->segments) >= 1)
			{
				// Si le contrôleur figure dans les menus du site, on rajoute la boîte de recherche
				if (in_array($this->uri->segments[1], $menus['site']))
						$this->load->view('includes/_search');
			}
		?>
	</ul>
</div>
