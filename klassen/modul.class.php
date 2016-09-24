<?php
class TModul {
	public $id = 0;
	public $name = "";
	public $istAktiv = false;

	const TABLE_VERSION = 1;
	
	const SQL_SELECT_ALLE = "
		SELECT
			*
		FROM
			modul";
			
	const SQL_INSERT = "
		INSERT INTO
			modul (Name, IstAktiv)
		VALUES
			(:name, 1)";
			
	const SQL_UPDATE = "
		UPDATE
			modul
		SET
			Name = :name
		WHERE
			Id = :id";
			
	const SQL_DELETE = "
		DELETE FROM
			modul
		WHERE
			Id = :id";

	const SQL_CREATE_TABLE = "
		CREATE TABLE IF NOT EXISTS `modul` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`Name` varchar(100) NOT NULL,
			`IstAktiv` tinyint(1) NOT NULL,
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
			TABLE_NAME = 'modul'";
	
	public function create($name, $datenbank) {
	
		$datenbank->queryDirekt(self::SQL_INSERT, Array("name" => $name));	
		$this->id = $datenbank->lastInsertId();
		$this->name = $name;
		$this->istAktiv = true;
		
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
		$sql = "ALTER TABLE modul COMMENT = '1'";
		$datenbank->queryDirekt($sql);
		return self::TABLE_VERSION;
	}
	
	public function update($id, $name, $datenbank) {
	
		$sql = self::SQL_UPDATE;
		$params = Array("name" => $name, "id" => $id);
		$datenbank->queryDirekt($sql, $params);
	
	}
	
	public function destroy($id, $datenbank) {
	
		$sql = "DELETE FROM playlist WHERE ModulId = :id";
		$params = Array("id" => $id);
		$datenbank->queryDirekt($sql, $params);
		
		$sql = self::SQL_DELETE;
		return $datenbank->queryDirekt($sql, $params);
		
	}
	
	public function istInstalliert($name, $datenbank) {
	
		$sql = "SELECT EXISTS(SELECT * FROM modul WHERE Name = :name) AS IstInstalliert";
		$params = Array("name" => $name);
		$result = $datenbank->queryDirektSingle($sql, $params);
		return $result["IstInstalliert"];
		
	}
}

class ModulFactory {
	public function create($record) {
		$result = new TModul();
		
		$result->id = $record["Id"];
		$result->name = $record["Name"];
		$result->istAktiv = $record["IstAktiv"];
		
		return $result;
	}
}
?>