<?php
require_once "../klassen/authentication.class.php";
$config["loginErforderlich"] = false;
require_once "../config.php";
require_once "../libs/smarty/Smarty.class.php";

if (isset($_GET["target"])) {
	$target = $_GET["target"];
} else {
	$target = $config["rootDir"];
}

$messsage = "Sie müssen sich anmelden, um auf diese Seite zugreifen zu dürfen.";

if (isset($_GET["msg"])) {
	if ($_GET["msg"] == "1") {
		$messsage = "Benutzername oder Passwort falsch.";
	} else if ($_GET["msg"] == "3") {
		$message = "Der Account ist zur Zeit deaktiviert.";
	}
}

$smarty = new Smarty();
$smarty->assign("rootDir", $config["rootDir"]);
$smarty->setTemplateDir("../seiten/templates/login");

$smarty->assign("message", $messsage);
$smarty->assign("target", $target);

$smarty->display("login.tpl");
?>