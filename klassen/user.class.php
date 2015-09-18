<?php
class TUser {
	public $id = 0;
	public $name = "";
	public $passwort = "";
	
	const MIN_LAENGE_NAME = 3;
	const MIN_LAENGE_PASSWORT = 3;
	const MAX_LAENGE_NAME = 50;
	const MAX_LAENGE_PASSWORT = 50;
	
	const SQL_SELECT_BY_ID = 'SELECT * FROM user WHERE id = :id';
	const SQL_SELECT_BY_NAME = 'SELECT * FROM user WHERE name = :name';
	const SQL_SELECT_ALLE = 'SELECT * FROM user';
	const SQL_SELECT_COUNT = 'SELECT COUNT(*) FROM user';
	const SQL_INSERT_INTO = '
		INSERT INTO 
			`user` (name, passwort, istAktiviert) 
		VALUES 
			(:name, :passwort, :istAktiviert)';
	const SQL_UPDATE = '
		UPDATE
			user
		SET
			passwort = :passwort,
			istAktiviert = :istAktiviert
		WHERE
			id = :id';
	const SQL_DELETE = '
		DELETE FROM
			`user`
		WHERE
			id = :id';
	
	function __construct($id, $name, $passwort, $istAktiviert) {
		$this->id = $id;
		$this->name = $name;
		$this->passwort = $passwort;
		$this->istAktiviert = $istAktiviert;
	}
	
	public static function validiereId($id) {
		if (is_numeric($id) && $id > 0) {
			return true;
		} else {
			throw new Exception("Ungültige Id: " . $id);
		}
	}
	
	public static function validiereName($name) {
		if (strlen($name) >= TUser::MIN_LAENGE_NAME && 
		    strlen($name) <= TUser::MAX_LAENGE_NAME) {
			return true;
		} else {
			throw new Exception("Ungültiger Name: " . $name);
		}
	}
	
	public static function validierePasswort($passwort) {
		if (strlen($passwort) >= TUser::MIN_LAENGE_PASSWORT && 
		    strlen($passwort) <= TUser::MAX_LAENGE_PASSWORT) {
			return true;
		} else {
			throw new Exception("Ungültiges Passwort.");
		}
	}
}

class UserFactory {
	function create($record) {
		return new TUser($record["id"], $record["name"], 
			$record["passwort"], $record["istAktiviert"]);
	}
}
?>