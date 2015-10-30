<?php
require_once "../klassen/authentication.class.php";
require_once "../config.php";
require_once "../klassen/datenbank.class.php";
require_once "../libs/smarty/Smarty.class.php";
require_once "../klassen/modul.class.php";

$datenbank = new Datenbank();

$dbModule = $datenbank->queryArray(TModul::SQL_SELECT_ALLE, Array(), 
	new ModulFactory());
	
$modulDateien = modulDateienSuchen($config["beamerModulePfad"]);

$module = modulArrayErzeugen($dbModule, $modulDateien);

seiteAnzeigen($module);


function modulDateienSuchen($pfad) {

	$result = Array();
	$ordner = scandir($pfad);
	
	foreach ($ordner as $aktuell) {
		if (istModulOrdner($aktuell)) {
			$result[$aktuell] = modulDateienVorhanden($pfad . $aktuell);
		}
	}
	
	return $result;

}


function seiteAnzeigen($module) {
	global $config;

	$smarty = new Smarty();
	$smarty->setTemplateDir("../seiten/templates/module/");
	$smarty->assign("rootDir", $config["rootDir"]);

	$smarty->assign("module", $module);
	
	if (isset($_GET["fehler"]) && is_numeric($_GET["fehler"])) {
	
		$smarty->assign("hatNachricht", true);
		
		if ($_GET["fehler"] < 20) {
			$smarty->assign("istFehler", false);
		} else {
			$smarty->assign("istFehler", true);
		}
		
		$fehler = "Unbekannte Nachricht";
		
		switch ($_GET["fehler"]) {
			case "11":
				$fehler = "Modul erfolgreich deinstalliert";
				break;
			case "12":
				$fehler = "Modul erfolgreich installiert";
				break;
			case "13":
				$fehler = "Modul erfolgreich gelöscht";
				break;
			case "21":
				$fehler = "Modul konnte nicht deinstalliert werden";
				break;
			case "22":
				$fehler = "Modul konnte nicht installiert werden";
				break;
			case "23":
				$fehler = "Modul konnte nicht gelöscht werden";
				break;
		}
		$smarty->assign("fehler", $fehler);
		
	} else {
	
		$smarty->assign("hatNachricht", false);
		
	}

	$smarty->display("index.tpl");
}


function istModulOrdner($ordnerName) {

	if ($ordnerName != '.' && $ordnerName != '..') {
		$info = pathinfo($ordnerName);
		
		return !isset($info["extension"]);
	}
	
	return false;
	
}


function modulDateienVorhanden($pfad) {

	$dateien = scandir($pfad);
	$result = Array();

	foreach ($dateien as $datei) {
		if ($datei != '.' && $datei != '..') {
			$result[] = $datei;
		}
	}
	
	return $result;
	
}


function modulArrayErzeugen($dbModule, $modulDateien) {
	
	$result = Array();
	
	// Module, die in der Datenbank eingetragen sind
	foreach ($dbModule as $dbModul) {
		
		$record["name"] = $dbModul->name;
		$record["id"] = $dbModul->id;
		$record["inDatenbank"] = true;
		
		foreach ($modulDateien as $name => $datei) {
			if (strcasecmp($name, $dbModul->name) == 0) {
				if (isset($datei[0])) {
					$record["template"] = $datei[0];
				}
				if (isset($datei[1])) {
					$record["modul"] = $datei[1];
				}
				if (isset($datei[0]) && isset($datei[1])) {
					$record["istBereit"] = true;
				} else {
					$record["istBereit"] = false;
				}
				unset($modulDateien[$name]);
				break;
			}
		}
		
		$result[] = $record;
	}
	
	// Module, die nicht in der Datenbank eingetragen sind
	if (count($modulDateien) > 0) {
		foreach ($modulDateien as $name => $datei) {
			$record["name"] = $name;
			$record["id"] = 0;
			$record["inDatenbank"] = false;
			if (isset($datei[0])) {
				$record["template"] = $datei[0];
			}
			if (isset($datei[1])) {
				$record["modul"] = $datei[1];
			}
			if (isset($datei[0]) && isset($datei[1])) {
				$record["istBereit"] = true;
			} else {
				$record["istBereit"] = false;
			}
			$result[] = $record;
		}
	}
	
	return $result;
}
?>
