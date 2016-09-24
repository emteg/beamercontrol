<?php
require_once "./klassen/authentication.class.php";
require_once "./config.php";
require_once "./klassen/datenbank.class.php";
require_once "./klassen/einstellung.class.php";

$datenbank = new Datenbank();

if (isset($_POST["name"])) {
	$einstellung = new TEinstellung();
	$einstellung->set("event", $_POST["name"], $datenbank);
}

if (isset($_POST["datum"])) {
	$einstellung = new TEinstellung();
	$einstellung->set("eventDate", $_POST["datum"], $datenbank);
}

if (isset($_POST["design"])) {
	$einstellung = new TEinstellung();
	$einstellung->set("design", $_POST["design"], $datenbank);
}

header("Location: /" . $config["rootDir"]);
?>