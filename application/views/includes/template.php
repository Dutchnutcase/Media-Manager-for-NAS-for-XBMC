<!-- Structure de la page -->
<?php
if (!isset($data)) $data = array();
$this->load->view('includes/header');

// Formulaire de connexion si l'utilisateur n'est pas connecté
if (!$this->session->userdata('user_id')) $this->load->view('includes/login');

if (isset($include)) $this->load->view('includes/'.$include, $data);

if (isset($file)) $this->load->view('content/'.$file, $data);

$this->load->view('includes/footer');
?>
