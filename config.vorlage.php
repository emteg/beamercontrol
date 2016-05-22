<?php
$config["datenbankBenutzer"] = "user";
$config["datenbankPasswort"] = "password";
$config["datenbankName"] = "infobeamer";

$config["rootDir"] = "beamercontrol/";
$config["beamerModulePfad"] = "C:\\path\\to\\modules\\";
$config["beamerDesignsPfad"] = "C:\\path\\to\\designs\\";
$config["beamerBilderPfad"] = "C:\\path\\to\\images\\";
$config["beamerBilderPfadRelativ"] = "../beamer/img_upload/";

// Wenn false: keine Logins erforderlich.
// Nur beim ersten Setup auf false setzen, um Benutzer anzulegen und zu
// aktivieren.
//$loginErforderlich = false;

// Jede Seite, die Datenbankzugriff haben will, muss diese Datei einbinden. Hier
// wird geprüft, ob auch authentication.class.php eingebunden wurde, in der die
// Variable $session erstellt wird.
if (isset($session)) {
	if ($loginErforderlich && !$session->istAngemeldet()) {
		header("Location: /" . $config["rootDir"] . "login/login.php?target=" . $_SERVER["REQUEST_URI"] . "&msg=2");
		die();
	}
} else {
	die("Keine Sitzung vorhanden!");
}
?>