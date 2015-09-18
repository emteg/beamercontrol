<?php
require_once "../klassen/authentication.class.php";
require_once "../config.php";
require_once "../klassen/datenbank.class.php";
require_once "../klassen/user.class.php";
require_once "../libs/smarty/smarty.class.php";

$datenbank = new Datenbank();

$sql = TUser::SQL_SELECT_ALLE;
$users = $datenbank->queryArray($sql, Array(), new UserFactory());

if (isset($_GET["fehler"])) {
	switch ($_GET["fehler"]) {
		case 0: 
			$nachricht = "Benutzer erfolgreich angelegt";
			$istFehler = false;
			break;
			
		case 1: 
			$nachricht = "Benutzername ist ungültig";
			$istFehler = true;
			break;
			
		case 2: 
			$nachricht = "Passwort ist ungültig";
			$istFehler = true;
			break;
			
		case 3: 
			$nachricht = "Passwörter sind nicht gleich";
			$istFehler = true;
			break;
			
		case 4: 
			$nachricht = "Ein Benutzer mit diesem Namen existiert schon";
			$istFehler = true;
			break;
	}
}

$smarty = new Smarty();
$smarty->assign("rootDir", $config["rootDir"]);
$smarty->setTemplateDir("../seiten/templates/benutzer/");

$smarty->assign("users", $users);

$smarty->assign("hatNachricht", isset($nachricht));

if (isset($nachricht)) {
	$smarty->assign("nachricht", $nachricht);
	$smarty->assign("istFehler", $istFehler);
}

$smarty->display("index.tpl");
?>