<?php
class TUser {
	public $id = 0;
	public $name = "";
	public $passwort = "";

	const TABLE_VERSION = 1;
	
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
	const SQL_CREATE_TABLE = "
		CREATE TABLE IF NOT EXISTS `user` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(50) NOT NULL,
			`passwort` varchar(100) NOT NULL,
			`istAktiviert` tinyint(1) NOT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `name` (`name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='".self::TABLE_VERSION."'";
	const SQL_TABLE_EXISTS = "
		SELECT
			*
		FROM
			information_schema.tables
		WHERE
			TABLE_SCHEMA = :table_schema AND
			TABLE_NAME = 'user'";
	
	function __construct($id, $name, $passwort, $istAktiviert) {
		$this->id = $id;
		$this->name = $name;
		$this->passwort = $passwort;
		$this->istAktiviert = $istAktiviert;
	}

	public function setupTable($datenbank, $config) {
		$version = $this->getTableVersion($datenbank, $config);
		if ($version >= 0) {
			if ($version < self::TABLE_VERSION) {
				return $this->migrateTable($datenbank, $config, $version);
			}
			return $version;
		} else {
			$sql = self::SQL_CREATE_TABLE;
			$datenbank->queryDirekt($sql);
			return self::TABLE_VERSION;
		}
	}

	public function getTableVersion($datenbank, $config) {
		$sql = self::SQL_TABLE_EXISTS;
		$params = Array("table_schema" => $config["datenbankName"]);
		$result = $datenbank->queryDirektSingle($sql, $params);
		if ($result) {
			$version = $result["TABLE_COMMENT"];
			if ($version = "") {
				$version = 0;
			} else {
				$version = (int)$version;
			}
			return $version;
		}
		return -1;
	}

	private function migrateTable($datenbank, $config, $version) {
		if ($version == 0 && self::TABLE_VERSION == 1) {
			return $this->migrate0to1($datenbank, $config);
		} else {
			die("Migration from version " . $version . " to " . 
				self::TABLE_VERSION . " does not exist :/");
		}
	}

	private function migrate0to1($datenbank, $config) {
		$sql = "ALTER TABLE user COMMENT = '1'";
		$datenbank->queryDirekt($sql);
		return self::TABLE_VERSION;
	}
	
	public static function validiereId($id) {
		if (is_numeric($id) && $id > 0) {
			return true;
		} else {
			throw new Exception("Ungültige Id: " . $id);
		}
	}
	
	public static function validiereName($name) {
		if (strlen($name) >= self::MIN_LAENGE_NAME && 
		    strlen($name) <= self::MAX_LAENGE_NAME) {
			return true;
		} else {
			throw new Exception("Ungültiger Name: " . $name);
		}
	}
	
	public static function validierePasswort($passwort) {
		if (strlen($passwort) >= self::MIN_LAENGE_PASSWORT && 
		    strlen($passwort) <= self::MAX_LAENGE_PASSWORT) {
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