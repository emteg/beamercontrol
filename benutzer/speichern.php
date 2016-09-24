<?php
require_once "../klassen/authentication.class.php";
require_once "../config.php";
require_once "../klassen/datenbank.class.php";
require_once "../klassen/user.class.php";
require_once '../functions/passwordHash.function.php';

$datenbank = new Datenbank();
$name = "";
$passwort = "";


if ($_GET["action"] == "add") {
	hinzufuegen($datenbank);
} else {
	die("Keine oder ungültige Aktion angegeben :/");
}

function hinzufuegen($datenbank) {
	global $name;
	global $passwort;

	if (!$fehler = parameterFehlerhaft($datenbank)) {
		benutzerErstellen($name, $passwort, $datenbank);
		zurueck();
	} else {
		zurueckMitFehler($fehler);
	}
}

function benutzerErstellen($name, $passwort, $datenbank) {
	$sql = TUser::SQL_INSERT_INTO;
	$parameters = Array("name" => $name, 
		"passwort" => $passwort, "istAktiviert" => true);
	$datenbank->queryDirekt($sql, $parameters);
}

function zurueck() {
	header("Location: ./index.php?fehler=0");
}

function zurueckMitFehler($fehler) {
	header("Location: ./index.php?fehler=" . $fehler);
}

function parameterFehlerhaft($datenbank) {
	global $name;
	global $passwort;
	
	try {
		TUser::validiereName($_POST["bcUsername"]);
		$name = $_POST["bcUsername"];
	} catch (Exception $e) {
		return 1;
	}

	try {
		TUser::validierePasswort($_POST["bcPassword"]);
		$passwort = create_hash($_POST["bcPassword"]);
	} catch (Exception $e) {
		return 2;
	}

	if ($_POST["bcPassword"] != $_POST["bcPassword2"]) {
		return 3;
	}
	
	return benutzerExistiert($datenbank);
}

function benutzerExistiert($datenbank) {
	global $name;
	
	$sql = TUser::SQL_SELECT_BY_NAME;
	$parameters = Array("name" => $name);
	$user = $datenbank->querySingle($sql, $parameters, new UserFactory());

	if ($user) {
		return 4;
	} else {
		return 0;
	}
}
?>