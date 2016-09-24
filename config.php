<?php
// Create a copy of the configuration file.
function copy_config() {
	$source = dirname(__FILE__) . "/config.values.php";
	$dest = dirname(__FILE__) . "/current.config.values.php";
	copy($source, $dest);
}

// Overwrite the configuration file with the current configuration values.
function write_config() {
	global $config;

	$filename = dirname(__FILE__) . "/config.values.php";
	$file = fopen($filename, "w");

	$txt = "<?php\n";
	fwrite($file, $txt);

	foreach ($config as $key => $value) {
		$txt = '$config["' . $key . '"] = "' . $value . '";' . "\n";
		fwrite($file, $txt);
	}

	$txt = "?>";
	fwrite($file, $txt);

	fclose($file);
}

// Create a copy of the configuration file and include it.
// This allows to overwrite the configuration file through php.
copy_config();
include dirname(__FILE__) . "/current.config.values.php";

// Jede Seite, die Datenbankzugriff haben will, muss diese Datei einbinden. Hier
// wird geprüft, ob auch authentication.class.php eingebunden wurde, in der die
// Variable $session erstellt wird.
if (!isset($no_redirect)) {
	if (isset($session)) {
		if ($config["loginErforderlich"] && !$session->istAngemeldet()) {
			header("Location: /" . $config["rootDir"] . "login/login.php?target=" . $_SERVER["REQUEST_URI"] . "&msg=2");
			die();
		}
	} else {
		die("Keine Sitzung vorhanden!");
	}
}
?>
