<?php
session_start();

// Supprimer toutes les variables de session
$_SESSION = [];

// Détruire la session
session_destroy();

// Rediriger vers la page de login
header("Location: ../../projet_stage/Frontend/login.php");
exit;
?>
