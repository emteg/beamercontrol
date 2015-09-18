<?php
class Datenbank {
	private $benutzer = "";
	private $passwort = "";
	private $datenbank = "";
	private $verbindung;
	private $istVerbunden = false;
	private $istBereit = false;
	public $lastError = null;
	
	function __construct($datenbankAuswaehlen = true, $datenbank = "") {
		global $config;
		
		$this->benutzer = $config["datenbankBenutzer"];
		$this->passwort = $config["datenbankPasswort"];
		
		if ($datenbank == "") {
			$this->datenbank = $config["datenbankName"];
		} else {
			$this->datenbank = $datenbank;
		}

		try {
			if ($datenbankAuswaehlen) {
				$this->verbindung = new PDO('mysql:host=localhost;dbname=' . $this->datenbank,
					$this->benutzer, $this->passwort);
				$this->istVerbunden = true;
				$this->istBereit = true;
			} else {
				$this->verbindung = new PDO('mysql:host=localhost;dbname=mysql', $this->benutzer, 
					$this->passwort);
				$this->istVerbunden = true;
			}
		  $this->verbindung->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			$this->lastError = $e;
		}
	}
	
	public function useDatabase($datenbank) {
		if ($this->istVerbunden) {
			try {
				$this->verbindung->query("use " . $datenbank);
				$this->datenbank = $datenbank;
				$this->istBereit = true;
				return true;
			} catch (PDOException $e) {
				$this->lastError = $e;
				$this->istBereit = false;
				return false;
			}
		} else {
			return false;
		}
	}
	
	public function createDatabase($datenbank) {
		if ($this->istVerbunden) {
			try {
				$this->verbindung->query("create database " . $datenbank);
				$this->datenbank = $datenbank;
				$this->istBereit = true;
			} catch (PDOException $e) {
				$this->lastError = $e;
				$this->istBereit = false;
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Holt ein einzelnes Ergebnis aus der Datenbank und weißt es mit der
	 * Factory Pattern einem Objekt zu.
	 *
	 * Beispiel:
	 * $sql = SELECT * FROM `Fahrzeug` WHERE id = :id
	 * $parameters = array('id' => $id);
	 * $factory = new FahrzeugFactory();
	 *
	 * @param $sql Parameterisierter SQL String
	 * @param $parameters Array mit den zu SQL gehörenden Parametern
	 * @param $factory Factory, die verwendet werden soll.
	 *
	 * @result false, wenn ein Fehler aufgetreten ist
	 * @result Von $factory erzeugtes Objekt.
	 */
	public function querySingle($sql, $parameters, $factory) {
		$statement = $this->verbindung->prepare($sql);
		$statement->execute($parameters);
		if ($record = $statement->fetch(PDO::FETCH_ASSOC)) {
			return $factory->create($record);
		} else {
			return false;
		}
	}
	
	public function queryArray($sql, $parameters, $factory) {
		$statement = $this->verbindung->prepare($sql);		
		$statement->execute($parameters);
		
		$result = array();
		
		while($record = $statement->fetch(PDO::FETCH_ASSOC)) {
        $result[] = $factory->create($record);
    }
		
		return $result;
	}
	
	public function queryDirekt($sql, $parameters = Array()) {
		if (count($parameters) == 0) {
			return $this->verbindung->query($sql);
		} else {
			$statement = $this->verbindung->prepare($sql);
			return $statement->execute($parameters);
		}
	}
	
	public function queryDirektSingle($sql, $parameters = Array()) {
		if (count($parameters) == 0) {
			$res = $this->verbindung->query($sql);
			return $res->fetch(PDO::FETCH_ASSOC);
		} else {
			$statement = $this->verbindung->prepare($sql);
			$statement->execute($parameters);
			return $statement->fetch(PDO::FETCH_ASSOC);
		}
	}
	
	public function queryDirektArray($sql) {
		$result = $this->verbindung->query($sql);
		
		$res = Array();
		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$res[] = $row;
		}
		
		return $res;
	}
	
	public function lastInsertId() {
		return $this->verbindung->lastInsertId();
	}
	
	public function istVerbunden() {
		return $this->istVerbunden;
	}
	
	public function istBereit() {
		return $this->istBereit;
	}
	
	public function name() {
		return $this->datenbank;
	}
}
?>