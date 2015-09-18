<?php
class TEinstellung {
	public $name = "";
	public $wert = "";
	
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
			
	public function create($name, $wert, $datenbank) {
		try {
		
			$sql = TEinstellung::SQL_INSERT;
			$params = Array("name" => $name, "wert" => $wert);
			$datenbank->queryDirekt($sql, $params);
			
			$this->name = $name;
			$this->wert = $wert;
			
			return true;
			
		} catch (Exception $e) {
		
			return false;
			
		}
	}
	
	public function read($name, $datenbank) {
	
		$sql = TEinstellung::SQL_SELECT;
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
	
	public function update($name, $wert, $datenbank) {
	
		$sql = TEinstellung::SQL_UPDATE;
		$params = Array("name" => $name, "wert" => $wert);
		return $datenbank->queryDirekt($sql, $params);
		
	}
	
	public function destroy($name, $datenbank) {
	
		$sql = TEinstellung::SQL_DELETE;
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