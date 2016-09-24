<?php
class TBildseite {
	public $id = 0;
	public $extension = "";
	public $beschriftung = "";
	public $layout = "";
	public $zeigenAb = "";
	public $zeigenBis = "";

	const TABLE_VERSION = 1;
	
	const SQL_SELECT = "
		SELECT
			*
		FROM
			bildseite
		WHERE
			Id = :id";
	const SQL_SELECT_ALLE = "
		SELECT
			*
		FROM
			bildseite";
	const SQL_SELECT_AKTUELLE = "
		SELECT 
			* 
		FROM 
			`bildseite` 
		WHERE 
			ZeigenAb <= NOW() AND 
			(ZeigenBis IS NULL OR ZeigenBis >= NOW())";
	const SQL_SELECT_GEPLANTE = "
		SELECT
			*
		FROM
			bildseite
		WHERE
			zeigenAb > NOW()
		ORDER BY
			zeigenAb ASC";
	
	const SQL_INSERT = "
		INSERT INTO
			bildseite (Extension, Beschriftung, Layout, ZeigenAb, ZeigenBis)
		VALUES
			(:extension, :beschriftung, :layout, :zeigenAb, :zeigenBis)";
			
	const SQL_UPDATE = "
		UPDATE
			bildseite
		SET
			Extension = :extension,
			Beschrfitung = :beschriftung,
			Layout = :layout,
			ZeigenAb = :zeigenAb,
			ZeigenBis = :zeigenBis
		WHERE
			Id = :id";
	const SQL_DELETE = "
		DELETE FROM
			bildseite
		WHERE
			Id = :id";

	const SQL_CREATE_TABLE = "
		CREATE TABLE IF NOT EXISTS `bildseite` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`Extension` varchar(10) NOT NULL,
			`Beschriftung` text NOT NULL,
			`Layout` enum('Zweispaltig','Mittig') NOT NULL,
			`ZeigenAb` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`ZeigenBis` timestamp NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='".self::TABLE_VERSION."'";

	const SQL_TABLE_EXISTS = "
		SELECT
			*
		FROM
			information_schema.tables
		WHERE
			TABLE_SCHEMA = :table_schema AND
			TABLE_NAME = 'bildseite'";

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
		$sql = "ALTER TABLE bildseite COMMENT = '1'";
		$datenbank->queryDirekt($sql);
		return self::TABLE_VERSION;
	}
			
	public function create($datenbank, $extension, $beschriftung, $layout, $zeigenAb = "", $zeigenBis = "") {
	
		$spalten = Array("Extension", "Beschriftung", "Layout");
		$variablen = Array(":extension", ":beschriftung", ":layout");
		$params["extension"] = $extension;
		$params["beschriftung"] = $beschriftung;
		$params["layout"] = $layout;
		
		if ($zeigenAb != "") {
			$spalten[] = "ZeigenAb";
			$variablen[] = ":zeigenAb";
			$params["zeigenAb"] = $zeigenAb;
		}
		
		if ($zeigenBis != "") {
			$spalten[] = "ZeigenBis";
			$variablen[] = ":zeigenBis";
			$params["zeigenBis"] = $zeigenBis;
		}
		
		$sql = "INSERT INTO bildseite (" . implode(",", $spalten) . ") ";
		$sql .= "VALUES (" . implode(",", $variablen) . ")";
		$datenbank->queryDirekt($sql, $params);
		
		$this->id = $datenbank->lastInsertId();
		
		return $this->id;
	}
	
	public function read($id, $datenbank) {

		$sql = self::SQL_SELECT;
		$params["id"] = $id;
		
		$result = $datenbank->queryDirektSingle($sql, $params);
		$this->applyRecord($result);
		
		return $result;
		
	}
	
	public function update($id, $datenbank) {
	
	}
	
	public function destroy($id, $datenbank) {
	
		global $config;
		try {
			unlink($config["beamerBilderPfad"] . $id . "." . $this->getExtension($id, $datenbank));
		} catch (Exception $e) {
			
		}
		
		$datenbank->queryDirekt(self::SQL_DELETE, Array("id" => $id));	
		
	}
	
	public function applyRecord($record) {
	
		$this->id = $record["Id"];
		$this->extension = $record["Extension"];
		$this->beschriftung = $record["Beschriftung"];
		$this->layout = $record["Layout"];
		$this->zeigenAb = $record["ZeigenAb"];
		$this->zeigenBis = $record["ZeigenBis"];
		
	}
	
	private function getExtension($id, $datenbank) {
		$sql = "SELECT extension FROM bildseite WHERE Id = :id";
		$params = Array("id" => $id);
		$result = $datenbank->queryDirektSingle($sql, $params);
		return $result["extension"];
	}
}

class BildseiteFactory {
	public function create($record) {
		$result = new TBildseite();
		
		$result->id = $record["Id"];
		$result->extension = $record["Extension"];
		$result->beschriftung = $record["Beschriftung"];
		$result->layout = $record["Layout"];
		$result->zeigenAb = $record["ZeigenAb"];
		$result->zeigenBis = $record["ZeigenBis"];
		
		return $result;
	}
}
?>