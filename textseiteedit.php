<?php
require_once "./klassen/authentication.class.php";
require_once "./config.php";
require_once "./klassen/datenbank.class.php";
require_once "./klassen/textseite.class.php";

$datenbank = new Datenbank();
$textseite = new TTextseite();

if (!isset($_GET["action"])) {
	die("Keine Aktion :/");
}

switch ($_GET["action"]) {
	case "add":
		hinzufuegen($textseite, $datenbank);
		break;
		
	case "delete":
		loeschen($textseite, $datenbank);
		break;
}

function hinzufuegen($textseite, $datenbank) {

	if (hinzufuegenVariablenVorhanden()) {
	
		if ($_POST["inhalt"] != "") {
		
			if (strtotime($_POST["zeigenAb"]) !== false) {
				if (strtotime($_POST["zeigenBis"]) !== false) {
				
					$textseite->create($datenbank, $_POST["inhalt"], 
						date("Y-m-d H:i:s", strtotime($_POST["zeigenAb"])), 
						date("Y-m-d H:i:s", strtotime($_POST["zeigenBis"])));
					
				} else {
				
					$textseite->create($datenbank, $_POST["inhalt"], 
						date("Y-m-d H:i:s", strtotime($_POST["zeigenAb"])));
					
				}
			} else {
			
				$textseite->create($datenbank, $_POST["inhalt"]);
			}
			
			zurueck();
			
		} else {
			echo "Inhalt der Textseite darf nicht leer sein :/";
		}
	
	} else {
		echo "Nicht alle oder ungültige Daten übertragen :/";
	}
	
}

function loeschen($textseite, $datenbank) {

	if (loeschenVariablenVorhanden()) {
	
		$textseite->destroy($_GET["id"], $datenbank);
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
	return isset($_POST["inhalt"]) && isset($_POST["zeigenAb"]) && 
		isset($_POST["zeigenBis"]);
}

function loeschenVariablenVorhanden() {
	return isset($_GET["id"]) && is_numeric($_GET["id"]);
}
?>
