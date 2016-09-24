<?php
require_once "./klassen/authentication.class.php";
require_once "./config.php";
require_once "./klassen/datenbank.class.php";
require_once "./klassen/einstellung.class.php";

$datenbank = new Datenbank();

if (isset($_POST["anzeigedauer"]) && is_numeric($_POST["anzeigedauer"]) &&
	$_POST["anzeigedauer"] > 0) {
	
	$einstellung = new TEinstellung();
	$einstellung->set("ModulAnzeigeDauerSekunden", $_POST["anzeigedauer"], $datenbank);
}

header("Location: /" . $config["rootDir"]);
?>