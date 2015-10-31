<?php
require_once "./klassen/authentication.class.php";
require_once "./config.php";
require_once "./klassen/datenbank.class.php";
require_once "./klassen/einstellung.class.php";

$datenbank = new Datenbank();

if (isset($_POST["titel"])) {
	
	$einstellung = new TEinstellung();
	$einstellung->update("eventTitel", $_POST["titel"], $datenbank);
}

header("Location: /" . $config["rootDir"]);
?>