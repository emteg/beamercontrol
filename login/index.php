<?php
require_once "../klassen/authentication.class.php";
$loginErforderlich = false;
require_once "../config.php";
require_once "../klassen/datenbank.class.php";
require_once "../klassen/user.class.php";
require_once '../functions/passwordHash.function.php';

$datenbank = new Datenbank();

if (!isset($_GET["target"])) {
	$target = '/' . $config["rootDir"];
} else {
	$target = $_GET["target"];
}

$name = $_POST["bcUsername"];
$passwort = $_POST["bcPassword"];

try {
	TUser::validiereName($name);
	TUser::validierePasswort($passwort);

	$sql = TUser::SQL_SELECT_BY_NAME;
	$params = Array("name" => $name);
	$user = $datenbank->querySingle($sql, $params, new UserFactory());

	if ($user && validate_password($passwort, $user->passwort) && $user->istAktiviert) {
		$session->anmelden($user->id, $user->name);
		header("Location: " . $target);
	} else if ($user && !$user->istAktiviert) {
		header("Location: /" . $config["rootDir"] . "login/login.php?target=" . $target . "&msg=3");
	} else {
		header("Location: /" . $config["rootDir"] . "login/login.php?target=" . $target . "&msg=1");
	}
} catch (Exception $e) {
	header("Location: /" . $config["rootDir"] . "login/login.php?target=" . $target . "&msg=1");
}
?>