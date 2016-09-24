<?php
ini_set("display_errors", "on");

$config_vorlage_file_exists = file_exists("../config.vorlage.php");
$config_file_exists = file_exists("../config.php");

echo "php-info-beamer Setup<br/><br/>";

$no_redirect = true;
include "../klassen/authentication.class.php";
include "../klassen/einstellung.class.php";
include "../klassen/user.class.php";
include "../klassen/modul.class.php";
include "../klassen/event.class.php";
include "../klassen/bildseite.class.php";
include "../klassen/textseite.class.php";
include "../klassen/playlist.class.php";
include "../config.php";

function test() {
	echo "Checking configuration...<br/>";
	test_config_file();

	echo "Checking database...<br/>";
	test_database();
}

function test_config_file() {
	global $config_vorlage_file_exists;
	global $config_file_exists;
	if (!$config_file_exists) {
		echo "File not found: config.php.<br/>";
		echo "Trying to copy from config.vorlage.php...<br/>";
		if (!$config_vorlage_file_exists) {
			die("ERROR: 'config.php' and 'config.vorlage.php' do not exist.");
		} else {
			$config_file_created = copy("../config.vorlage.php", "../config.php");
			if (!$config_file_created) {
				die("ERROR: failed to copy 'config.vorlage.php' to 'config.php'. Please check permisions.");
			} else {
				die("SUCCESS: Config file created. Please reload this page.");
			}
		}
	} else {
		echo "SUCCESS: configuration file exists.<br/>";
	}
}

function test_database() {
	global $config;

	echo "Trying to connect to database...<br/>";
	echo "User name: " . $config["datenbankBenutzer"] . "<br/>";
	echo "Password: " . $config["datenbankPasswort"] . "<br/>";

	include "../klassen/datenbank.class.php";

	$datenbank = new Datenbank(false);

	if ($datenbank->lastError != null) {
		echo "ERROR: failed to connect to database.<br/>";
		die($datenbank->lastError->getMessage());
	}

	echo "SUCCESS: connected to database.<br/>";

	echo "Trying to select database " . $config["datenbankName"] . "...<br/>";
	$datenbank->useDatabase($config["datenbankName"]);

	if ($datenbank->lastError != null) {
		echo "Database does not exist. Trying to create database...<br/>";
		$created = $datenbank->createDatabase($config["datenbankName"]);

		if (!$created) {
			echo "ERROR: failed to create database.<br/>";
			die($datenbank->lastError->getMessage());
		}

		echo "SUCCESS: database created.<br/>";
		$datenbank->useDatabase($config["datenbankName"]);
	} else {
		echo "SUCCESS: database selected.<br/>";
	}

	echo "Checking database structure...<br/>";
	test_database_tables($datenbank);

}

function test_database_tables($datenbank) {
	global $config;

	echo "Checking table Einstellung...<br/>";
	test_table($datenbank, new TEinstellung());

	echo "Checking table User...<br/>";
	test_table($datenbank, new TUser(0, "", "", 0));

	echo "Checking table Modul...<br/>";
	test_table($datenbank, new TModul());

	echo "Checking table Event...<br/>";
	test_table($datenbank, new TEvent());

	echo "Checking table Bildseite...<br/>";
	test_table($datenbank, new TBildseite());

	echo "Checking table Textseite...<br/>";
	test_table($datenbank, new TTextseite());

	echo "Checking table Playlist...<br/>";
	test_table($datenbank, new TPlaylist());
}

function test_table($datenbank, $table) {
	global $config;

	$ok = $table->setupTable($datenbank, $config);
	if ($ok) {
		echo "OK, table at version " . $ok . "<br/>";
	} else {
		echo "ERROR: setup or migration failed<br/>";
		die($datenbank->lastError->getMessage());
	}

}

function test_filesystem() {
	return false;
}

test();
?>
