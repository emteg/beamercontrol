<?php
ini_set("display_errors", "on");

echo "php-info-beamer Setup<br/><br/>";
echo "This script checks if all settings are present and correct.<br/>";

echo "Checking configuration...<br/>";
$config_template_exists = file_exists("../config.template.php");
$config_values_exists = file_exists("../config.values.php");

if (!$config_values_exists) {
	if ($config_template_exists) {
		$source = "../config.template.php";
		$dest = "../config.values.php";
		copy($source, $dest);
		echo "SUCCESS: config file created from template.<br/>";
	} else {
		die("ERROR: config template file does not exist.<br/>");
	}
} else {
	echo "SUCCESS: config file exists.<br/>";
}

include "../klassen/authentication.class.php";
$config["loginErforderlich"] = false;
include "../config.php";

include "../klassen/einstellung.class.php";
include "../klassen/user.class.php";
include "../klassen/modul.class.php";
include "../klassen/event.class.php";
include "../klassen/bildseite.class.php";
include "../klassen/textseite.class.php";
include "../klassen/playlist.class.php";

function test() {
	echo "Checking database...<br/>";
	test_database();
	echo "SUCCESS: Database is ready.<br/>";	

	echo "Checking files...<br/>";
	test_files();

	echo "SUCCESS: php-info-beamer is ready.";
}

function test_config_file() {
	global $config_template_exists;

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

	echo "Checking default values...<br/>";
	test_defaults($datenbank);
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

function test_defaults($datenbank) {
	global $config;

	echo "Checking existance of important default values...<br/>";
	$e = new TEinstellung();
	if ($e->read("ModulAnzeigeDauerSekunden", $datenbank)) {
		echo "SUCCESS: value ModulAnzeigeDauerSekunden is set.<br/>";
	} else {
		$e->set("ModulAnzeigeDauerSekunden", "20", $datenbank);
		echo "SUCCESS: value ModulAnzeigeDauerSekunden has been set to 20.<br/>";
	}

	include "../config.values.php";

	echo "Checking for user accounts for beamercontrol...<br/>";
	$sql = TUser::SQL_SELECT_COUNT;
	$userCount = $datenbank->queryDirektSingle($sql);
	if ($userCount) {
		$userCount = (int) $userCount["COUNT(*)"];
		if ($userCount == 0) {
			echo "WARNING: no user accounts found.<br/>";
			if ($config["loginErforderlich"]) {
				echo "ERROR: beamercontrol currently requires a user account to login.<br/>";
				echo "To disable authentication please edit 'config.values.php':<br/>";
				echo 'Change value of $config["loginErforderlich"] to "0",<br/>';
				die("then open /beamercontrol in your browser and create a user account.<br/>");
			} else {
				echo "beamercontrol does currently not require login.<br/>";
				die("To create a user account, please open /beamercontrol in your browser.<br/>");
			}
		} else {
			echo "SUCCESS: " . $userCount . " user accounts present.<br/>";
			if ($config["loginErforderlich"]) {
				echo "SUCCESS: login is required.<br/>";
			} else {
				echo "WARNING: beamercontrol does currently not require login.<br/>";
				echo "To enable authentication please edit 'config.values.php':<br/>";
				echo 'Change value of $config["loginErforderlich"] to "1",<br/>';
				die("then open /beamercontrol in your browser and login.<br/>");
			}
		}
	}
}

function test_files() {
	global $config;
	
	echo "Checking path to modules...<br/>";
	if (file_exists($config["beamerModulePfad"])) {
		echo "SUCCESS: modules path is valid.<br/>";
	} else {
		echo "ERROR: directory does not exist: " . $config["beamerModulePfad"] . "<br/>";
		echo "Please edit config.values.php and adjust the value of 'beamerModulePfad'<br/>";
		die("Please enter an absolute path to /beamer/module.");
	}

	// designs
	echo "Checking path to designs...<br/>";
	if (file_exists($config["beamerDesignsPfad"])) {
		echo "SUCCESS: designs path is valid.<br/>";
	} else {
		echo "ERROR: directory does not exist: " . $config["beamerDesignsPfad"] . "<br/>";
		echo "Please edit config.values.php and adjust the value of 'beamerDesignsPfad'<br/>";
		die("Please enter an absolute path to /beamer/designs.");
	}

	// img_upload
	echo "Checking picture upload path...<br/>";
	if (file_exists($config["beamerBilderPfad"])) {
		echo "SUCCESS: picture upload path is valid.<br/>";
	} else {
		echo "WARNING: directory does not exist: " . $config["beamerBilderPfad"] . "<br/>";
		if (mkdir($config["beamerBilderPfad"])) {
			echo "SUCCESS: picture upload path has been created.<br/>";
		} else {
			echo "ERROR: picture upload directory could not be created in '" . $config["beamerBilderPfad"] . "'<br/>";
			echo "Please edit config.values.php and adjust the value of 'beamerBilderPfad'<br/>";
			die("and/or create a directory and enter an absolute path to it in the configuration.");
		}
		
	}

	// write beamer config
	echo "Checking beamer config...<br/>";
	if (file_exists($config["beamerDirAbs"] . "config.php")) {
		echo "Config file exists.<br/>";
	} else {
		echo "Config file does not exist.<br/>";
		$source = $config["beamerDirAbs"] . "config.vorlage.php";
		$dest = $config["beamerDirAbs"] . "config.php";
		if (copy($source, $dest)) {
			echo "SUCCESS: config file created from template.<br/>";
			echo "Notice: you may have to edit /beamer/config.php if you do not use the default paths.<br/>";
		} else {
			die("ERROR: failed to copy config file.");
		}
	}
}

test();
?>