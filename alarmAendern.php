<?php
require_once "./klassen/authentication.class.php";
require_once "./config.php";
require_once "./klassen/datenbank.class.php";
require_once "./klassen/einstellung.class.php";

$datenbank = new Datenbank();

if (isset($_POST["alarmText"])) {
  
  if (isset($_POST["alarmAnzeigen"]) && $_POST["alarmAnzeigen"] === "on") {
    $alarmAnzeigen = "true";
  } else {
    $alarmAnzeigen = "false";
  }
  $alarmText = nl2br($_POST["alarmText"]);
  
  $einstellung = new TEinstellung();
  $einstellung->set("alarmAnzeigen", $alarmAnzeigen, $datenbank);
  $einstellung->set("alarmText", $alarmText, $datenbank);
}

header("Location: /" . $config["rootDir"]);
?>