<?php
require_once "../klassen/authentication.class.php";
$loginErforderlich = false;
require_once "../config.php";

$session->abmelden();

header("Location: " . $_SERVER["HTTP_REFERER"]);
?>