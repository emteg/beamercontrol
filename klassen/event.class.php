<?php
class TEvent {
	public $id = 0;
	public $titel = "";
	public $beginn = "";
	public $ende = "";
	public $kategorie = "";

	const TABLE_VERSION = 1;
	
	const SQL_SELECT_ALLE = "
		SELECT
			*
		FROM
			event
		ORDER BY
			Beginn ASC";
	const SQL_SELECT_ANSTEHENDE = "
		SELECT
			*
		FROM 
			`event` 
		WHERE 
			Beginn >= NOW() OR 
			(Beginn <= NOW() AND Ende >= NOW())
		ORDER BY
			Beginn ASC";
			
	const SQL_INSERT = "
		INSERT INTO
			event (Titel, Beginn, Ende, Kategorie)
		VALUES
			(:titel, :beginn, :ende, :kategorie)";
	const SQL_INSERT_KEIN_ENDE = "
		INSERT INTO
			event (Titel, Beginn, Kategorie)
		VALUES
			(:titel, :beginn, :kategorie)";
			
	const SQL_UPDATE = "
		UPDATE
			event
		SET
			Titel = :titel,
			Beginn = :beginn,
			Ende = :ende,
			Kategorie = :kategorie
		WHERE
			Id = :id";
	const SQL_UPDATE_KEIN_ENDE = "
		UPDATE
			event
		SET
			Titel = :titel,
			Beginn = :beginn,
			Ende = NULL,
			Kategorie = :kategorie
		WHERE
			Id = :id";
			
	const SQL_DELETE = "
		DELETE FROM
			event
		WHERE
			Id = :id";

	const SQL_CREATE_TABLE = "
		CREATE TABLE IF NOT EXISTS `event` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`Titel` varchar(100) NOT NULL,
			`Beginn` datetime NOT NULL,
			`Ende` datetime DEFAULT NULL,
			`Kategorie` set('Allgemein','SdS') NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='".self::TABLE_VERSION."'";
	const SQL_TABLE_EXISTS = "
		SELECT
			*
		FROM
			information_schema.tables
		WHERE
			TABLE_SCHEMA = :table_schema AND
			TABLE_NAME = 'event'";
			
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
		$sql = "ALTER TABLE event COMMENT = '1'";
		$datenbank->queryDirekt($sql);
		return self::TABLE_VERSION;
	}

	public function create($titel, $beginn, $ende, $kategorie, $datenbank) {
	
		if ($this->kategorieKorrekt($kategorie)) {
			if ($ende == "") {
				$sql = self::SQL_INSERT_KEIN_ENDE;
				$params = Array("titel" => $titel, "beginn" => $beginn,
					"kategorie" => $kategorie);
			} else {
				$sql = self::SQL_INSERT;
				$params = Array("titel" => $titel, "beginn" => $beginn, 
				"ende" => $ende, "kategorie" => $kategorie);
			}
			
			$datenbank->queryDirekt($sql, $params);
			
			$this->id = $datenbank->lastInsertId();
			$this->titel = $titel;
			$this->beginn = $beginn;
			$this->ende = $ende;
			$this->kategorie = $kategorie;
		} else {
			echo "Ungültige Kategorie: " . $kategorie;
		}
		
	}
	
	public function update($id, $titel, $beginn, $ende, $kategorie, $datenbank) {
	
		if ($this->kategorieKorrekt($kategorie)) {
			if ($ende == "") {
				$sql = self::SQL_UPDATE_KEIN_ENDE;
			} else {
				$sql = self::SQL_UPDATE;
			}
			$params = Array("id" => $id, "titel" => $title, "beginn" => $beginn, 
				"ende" => $ende, "kategorie" => $kategorie);
			$datenbank->queryDirekt($sql, $params);
			
			$this->id = $id;
			$this->titel = $titel;
			$this->beginn = $beginn;
			$this->ende = $ende;
			$this->kategorie = $kategorie;
		} else {
			echo "Ungültige Kategorie: " . $kategorie;
		}
		
	}
	
	public function destroy($id, $datenbank) {
		$datenbank->queryDirekt(self::SQL_DELETE, Array("id" => $id));
	}
	
	private function kategorieKorrekt($kategorie) {
	
		return $kategorie == "Allgemein" || $kategorie == "SdS";
		
	}
}

class EventFactory {
	public function create($record) {
		$result = new TEvent();
		
		$result->id = $record["id"];
		$result->titel = $record["Titel"];
		$result->beginn = $record["Beginn"];
		$result->ende = $record["Ende"];
		$result->kategorie = $record["Kategorie"];
		
		return $result;
	}
}
?>
