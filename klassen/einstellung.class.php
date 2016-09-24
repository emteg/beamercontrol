<?php
class TEinstellung {
	public $name = "";
	public $wert = "";

	const TABLE_VERSION = 1;

	const SQL_SELECT_ALLE = "
		SELECT
			*
		FROM
			einstellungen";
	const SQL_SELECT = "
		SELECT
			Wert
		FROM
			einstellungen
		WHERE
			Name = :name";
	const SQL_INSERT = "
		INSERT INTO
			einstellungen (Name, Wert)
		VALUES
			(:name, :wert)";
	const SQL_UPDATE = "
		UPDATE
			einstellungen
		SET
			Wert = :wert
		WHERE
			Name = :name";
	const SQL_DELETE = "
		DELETE FROM
			einstellungen
		WHERE
			Name = :name";
	const SQL_INSERT_OR_UPDATE = "
		INSERT INTO
			einstellungen (Name, Wert)
		VALUES (:name, :wert)
		ON DUPLICATE KEY UPDATE Wert = VALUES(Wert)";
	const SQL_CREATE_TABLE = "
		CREATE TABLE IF NOT EXISTS `einstellungen` (
			`Name` varchar(100) NOT NULL,
			`Wert` varchar(200) NOT NULL,
			PRIMARY KEY (`Name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='".self::TABLE_VERSION."'";
	const SQL_TABLE_EXISTS = "
		SELECT
			*
		FROM
			information_schema.tables
		WHERE
			TABLE_SCHEMA = :table_schema AND
			TABLE_NAME = 'einstellungen'";

	public function create($name, $wert, $datenbank) {
		try {
			$sql = self::SQL_INSERT;
			$params = Array("name" => $name, "wert" => $wert);
			$datenbank->queryDirekt($sql, $params);

			$this->name = $name;
			$this->wert = $wert;

			return true;

		} catch (Exception $e) {

			return false;

		}
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
		$sql = "ALTER TABLE einstellungen COMMENT = '1'";
		$datenbank->queryDirekt($sql);
		return self::TABLE_VERSION;
	}

	public function read($name, $datenbank) {

		$sql = self::SQL_SELECT;
		$params = Array("name" => $name);
		$record = $datenbank->queryDirektSingle($sql, $params);

		if (isset($record["Wert"])) {

			$this->name = $name;
			$this->wert = $record["Wert"];

			return $this->wert;

		} else {

			$this->name = "";
			$this->wert = "";

			return false;

		}

	}

	public function set($name, $wert, $datenbank) {

		$sql = self::SQL_INSERT_OR_UPDATE;
		$params = Array("name" => $name, "wert" => $wert);

		$this->name = $name;
		$this->wert = $wert;

		return $datenbank->queryDirekt($sql, $params);

	}

	public function update($name, $wert, $datenbank) {

		$sql = self::SQL_UPDATE;
		$params = Array("name" => $name, "wert" => $wert);
		return $datenbank->queryDirekt($sql, $params);

	}

	public function destroy($name, $datenbank) {

		$sql = self::SQL_DELETE;
		$params = Array("name" => $name);
		return $datenbank->queryDirekt($sql, $params);

	}
}

class EinstellungFactory {
	public function create($record) {
		$result = new TEinstellung();

		$result->name = $record["Name"];
		$result->wert = $record["Wert"];

		return $result;
	}
}
?>
