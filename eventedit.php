<?php
require_once "./klassen/authentication.class.php";
require_once "./config.php";
require_once "./klassen/datenbank.class.php";
require_once "./klassen/event.class.php";

$datenbank = new Datenbank();
$event = new TEvent();

//die(var_dump($_POST));

if (!isset($_GET["action"])) {
	die("Keine Aktion :/");
}

switch ($_GET["action"]) {		
	case "delete":
		loeschen($event, $datenbank);
		break;
		
	case "add":
		hinzufuegen($event, $datenbank);
		break;
}

function loeschen($event, $datenbank) {

	if (loeschenVariablenVorhanden()) {
	
		$event->destroy($_GET["id"], $datenbank);
		zurueck();
		
	} else {
		echo "Nicht alle oder ungültige Daten übertragen :/";
	}
}

function hinzufuegen($event, $datenbank) {
    if (hinzufuegenVariablenVorhanden() && hinzufuegenVariablenGueltig()) {
		if ($_POST["ende"] != "") {
			$event->create($_POST["titel"], 
				date("Y-m-d H:i:s", strtotime($_POST["beginn"])), 
				date("Y-m-d H:i:s", strtotime($_POST["ende"])), 
				$_POST["kategorie"], $datenbank);
		} else {
			$event->create($_POST["titel"], 
				date("Y-m-d H:i:s", strtotime($_POST["beginn"])), 
				$_POST["ende"], $_POST["kategorie"], $datenbank);
		}
		zurueck();
	} else {
		echo "Nicht alle oder ungültige Daten übertragen :/";
	}
}

function zurueck() {
	global $config;
    header("Location: /" . $config["rootDir"]);
}

function hinzufuegenVariablenVorhanden() {
	return isset($_POST["titel"]) && isset($_POST["beginn"]) &&
		isset($_POST["kategorie"]) && isset($_POST["ende"]);
}

function hinzufuegenVariablenGueltig() {
	if ($_POST["titel"] == "") {
		echo "Der Titel des Events darf nicht leer sein :/";
		return false;
	}
	
	if (strtotime($_POST["beginn"]) === false) {
		echo "Kein oder ungültiger Beginn des Events :/";
		return false;
	}
	
	if ($_POST["ende"] != "" && strtotime($_POST["beginn"]) === false) {
		echo "Ungültiges Ende des Events :/";
		return false;
	}
	
	if (!($_POST["kategorie"] == "Allgemein" || $_POST["kategorie"] == "SdS")) {
		echo "Keine oder ungütlige Kategorie :/";
		return false;
	}
	
	return true;
}

function loeschenVariablenVorhanden() {
	return isset($_GET["id"]) && is_numeric($_GET["id"]);
}
?>