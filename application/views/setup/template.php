<!-- Structure de la page -->
<?php
if (!isset($data)) $data = array();
$this->load->view('setup/header');

if (isset($file)) $this->load->view($file, $data);

$this->load->view('setup/footer');
?>
