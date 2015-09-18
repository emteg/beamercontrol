<?php
require_once "../klassen/authentication.class.php";
require_once "../config.php";
require_once "../klassen/datenbank.class.php";
require_once "../libs/smarty/smarty.class.php";
require_once "../klassen/modul.class.php";

$datenbank = new Datenbank();

if (isset($_GET["action"])) {
	$action = $_GET["action"];
	
	if (isset($_GET["name"])) {
		$name = $_GET["name"];
	} else if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
		$id = $_GET["id"];
	} else {
		die("Kein oder ungültiges Ziel übergeben :/");
	}
	
	if ($action == "uninstall" && isset($id)) {
		deinstallieren($id, $datenbank);
	} else if ($action == "install" && isset($name)) {
		installieren($name, $datenbank);
	} else if ($action == "delete" && isset($name)) {
		dateienLoeschen($name, $datenbank);
	} else {
		die("Ziel passt nicht zur Aktion :/");
	}
	
} else {
	die("Keine Aktion übergeben :/");
}

function deinstallieren($id, $datenbank) {

	$modul = new TModul();
	if ($modul->destroy($id, $datenbank)) {
		zurueck(11);
	} else {
		zurueck(21);
	}
	
}

function installieren($name, $datenbank) {
	global $config;
	
	if (is_dir($config["beamerModulePfad"] . $name)) {
		$modul = new TModul();
		$modul->create(ucfirst($name), $datenbank);
		zurueck(12);
	} else {
		zurueck(22);
	}
	
}

function dateienLoeschen($name, $datenbank) {
	global $config;
	
	$modul = new TModul();
	
	if (!$modul->istInstalliert($name, $datenbank)) {
		ordnerUndInhalteLoeschen($config["beamerModulePfad"] . $name);
		zurueck(13);
	} else {
		zurueck(23);
	}
}

function ordnerUndInhalteLoeschen($pfad) {
	$it = new RecursiveDirectoryIterator($pfad, 
		RecursiveDirectoryIterator::SKIP_DOTS);
		
	$files = new RecursiveIteratorIterator($it,
		RecursiveIteratorIterator::CHILD_FIRST);
		
	foreach($files as $file) {
		if ($file->getFilename() === '.' || $file->getFilename() === '..') {
			continue;
		}
		if ($file->isDir()){
			rmdir($file->getRealPath());
		} else {
			unlink($file->getRealPath());
		}
	}
	rmdir($pfad);
}

/*
 * 11: Deinstallieren erfolgreich
 * 21. Deinstallieren fehlgeschlagen
 * 12. Installation erfolgreich
 * 22. Fehler bei Installation
 * 13. Löschen erfolgreich
 * 23. Löschen fehlgeschlagen
 */
function zurueck($nachricht) {
	global $config;
	
	header("Location: /" . $config["rootDir"] . "module/index.php?fehler=" . $nachricht);
}
?>