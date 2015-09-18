<?php
/**
 * session.class.php
 * 
 * Stellt ein Login-System zur Verfügung. Erzeugt die Variablen 
 * $session = new Session() und
 * $loginErforderlich = true.
 * 
 * Das vorhandensein dieser Variablen wird von config.inc.php vorausgesetzt,
 * daher muss diese Datei auf jeder Seite eingebunden werden.
 * 
 * Um eine Seite darzustellen, für die man nicht eingeloggt sein muss (z.B. die
 * Login-Seite), muss nach dem Einbinden der Session-Klasse und vor dem
 * Einbinden von config.inc.php die Variable $loginErforderlich auf false gesetzt
 * werden.
 */
session_save_path("D:\\Programme\\xampp\\htdocs\\c4\\tmp");
session_start();

class Session {	
	public $gueltigBis = 0;
	
	const TIMEOUT_MINUTEN = 60;
	
	private function validiereGueltigBis() {
		$jetzt = time();
		return isset($_SESSION["gueltigBis"]) && is_numeric($_SESSION["gueltigBis"]) &&
			   $_SESSION["gueltigBis"] >= $jetzt;
	}
	
	private function validiereUserId() {
		return isset($_SESSION["userId"]) && is_numeric($_SESSION["userId"]) &&
		       $_SESSION["userId"] > 0;	
	}
	
	private function gueltigkeitVerlaengern() {
		return time() + 60 * Session::TIMEOUT_MINUTEN;
	}
	
	public function istAngemeldet() {
		if (isset($_SESSION["angemeldet"]) && $_SESSION["angemeldet"] &&
		  $this->validiereGueltigBis() && $this->validiereUserId()) {
			
			$_SESSION["gueltigBis"] = $this->gueltigkeitVerlaengern();
			
			$this->gueltigBis = $_SESSION["gueltigBis"];
			
			return true;
		}
		
		return false;
	}
	
	public function anmelden($userId, $userName) {
		$_SESSION["angemeldet"] = true;
		$_SESSION["gueltigBis"] = $this->gueltigkeitVerlaengern();
		$_SESSION["userId"] = $userId;
		$_SESSION["userName"] = $userName;
	}
	
	public function abmelden() {
		session_destroy();
	}
	
	public function getUserId() {
		return $_SESSION["userId"];
	}
	
	public function getUserName() {
		return $_SESSION["userName"];
	}
}

$session = new Session();
$loginErforderlich = true;
?>