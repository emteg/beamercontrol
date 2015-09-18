<?php
require_once "./klassen/authentication.class.php";
require_once "./config.php";
require_once "./klassen/datenbank.class.php";
require_once "./klassen/playlist.class.php";

$datenbank = new Datenbank();
$playlist = new TPlaylist();
$playlist->ladePlaylist($datenbank);

if (!isset($_GET["action"])) {
	die("Keine Aktion :/");
}

switch ($_GET["action"]) {
	case "up":
		nachOben($playlist, $datenbank);
		break;
		
	case "down":
		nachUnten($playlist, $datenbank);
		break;
		
	case "delete":
		loeschen($playlist, $datenbank);
		break;
		
	case "add":
		hinzufuegen($playlist, $datenbank);
		break;
}

function nachOben($playlist, $datenbank) {
	
	if (isset($_GET["index"]) && is_numeric($_GET["index"])) {
		$playlist->nachOben($_GET["index"], $datenbank);
		zurueck();
	} else {
		echo "Kein oder ungültiger Index :/";
	}
}

function nachUnten($playlist, $datenbank) {
	
	if (isset($_GET["index"]) && is_numeric($_GET["index"])) {
		$playlist->nachUnten($_GET["index"], $datenbank);
		zurueck();
	} else {
		echo "Kein oder ungültiger Index :/";
	}
}

function loeschen($playlist, $datenbank) {
	
	if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
		$playlist->loeschen($_GET["id"], $datenbank);
		zurueck();
	} else {
		echo "Keine oder ungültige Id :/";
	}
}

function hinzufuegen($playlist, $datenbank) {
	
	
	if (isset($_POST["modulId"]) && is_numeric($_POST["modulId"])) {
		$playlist->hinzufuegen($_POST["modulId"], $datenbank);
		zurueck();
	} else {
		echo "Keine oder ungültige Id :/";
	}
	
}

function zurueck() {
	global $config;
	header("Location: /" . $config["rootDir"]);
}
?>