<?php
require_once "./klassen/authentication.class.php";
require_once "./config.php";
require_once "./klassen/datenbank.class.php";
require_once "./libs/smarty/Smarty.class.php";
require_once "./klassen/playlist.class.php";
require_once "./klassen/textseite.class.php";
require_once "./klassen/event.class.php";
require_once "./klassen/einstellung.class.php";
require_once "./klassen/bildseite.class.php";

$datenbank = new Datenbank();

// Playlist
$playlist = new TPlaylist();
$module = $playlist->ladePlaylist($datenbank);
$alleModule = $playlist->ladeLibrary($datenbank);


// Textseiten
$aktuelleTextseiten = $datenbank->queryArray(
	TTextseite::SQL_SELECT_AKTUELLE, Array(), new TextseiteFactory());	
$geplanteTextseiten = $datenbank->queryArray(
	TTextseite::SQL_SELECT_GEPLANTE, Array(), new TextseiteFactory());

	
// Bildseiten
$aktuelleBildseiten = $datenbank->queryArray(
	TBildseite::SQL_SELECT_AKTUELLE, Array(), new BildseiteFactory());
$geplanteBildseiten = $datenbank->queryArray(
	TBildseite::SQL_SELECT_GEPLANTE, Array(), new BildseiteFactory());
	
// Zeitplan
$events = $datenbank->queryArray(
    TEvent::SQL_SELECT_ANSTEHENDE, Array(), new EventFactory());
	
	
// Modul-Anzeigedauer
$einstellung = new TEinstellung();
$modulAnzeigeDauer = $einstellung->read("ModulAnzeigeDauerSekunden", $datenbank);
$eventTitel = $einstellung->read("eventTitel", $datenbank);


// Boxen
if (isset($_COOKIE["boxStatus_playlist"])) {
	$boxStatusPlaylist = $_COOKIE["boxStatus_playlist"];
} else {
	$boxStatusPlaylist = "show";
}
if (isset($_COOKIE["boxStatus_textseiten"])) {
	$boxStatusTextseiten = $_COOKIE["boxStatus_textseiten"];
} else {
	$boxStatusTextseiten = "hide";
}
if (isset($_COOKIE["boxStatus_bildseiten"])) {
	$boxStatusBildseiten = $_COOKIE["boxStatus_bildseiten"];
} else {
	$boxStatusBildseiten = "hide";
}
if (isset($_COOKIE["boxStatus_events"])) {
	$boxStatusEvents = $_COOKIE["boxStatus_events"];
} else {
	$boxStatusEvents = "hide";
}


$smarty = new Smarty();
$smarty->setTemplateDir("./seiten/templates/");
$smarty->assign("rootDir", $config["rootDir"]);
$smarty->assign("beamerBilderPfadRelativ", $config["beamerBilderPfadRelativ"]);

$smarty->assign("module", $module);
$smarty->assign("alleModule", $alleModule);

$smarty->assign("aktuelleTextseiten", $aktuelleTextseiten);
$smarty->assign("geplanteTextseiten", $geplanteTextseiten);

$smarty->assign("aktuelleBildseiten", $aktuelleBildseiten);
$smarty->assign("geplanteBildseiten", $geplanteBildseiten);

$smarty->assign("zeitplan", $events);

$smarty->assign("modulAnzeigeDauer", $modulAnzeigeDauer);
$smarty->assign("eventTitel", $eventTitel);

$smarty->assign("boxStatusPlaylist", $boxStatusPlaylist);
$smarty->assign("boxStatusTextseiten", $boxStatusTextseiten);
$smarty->assign("boxStatusBildseiten", $boxStatusBildseiten);
$smarty->assign("boxStatusEvents", $boxStatusEvents);

$smarty->display("index.tpl");

?>
