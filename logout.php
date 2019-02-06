<?php

require_once("config.php");
#On détruit la session
$_SESSION = array();
session_destroy();
#On redirige vers la page de connexion
header('Location: admin.php');
exit;

?>