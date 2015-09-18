<?php
class TBildseite {
	public $id = 0;
	public $extension = "";
	public $beschriftung = "";
	public $layout = "";
	public $zeigenAb = "";
	public $zeigenBis = "";
	
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

		$sql = TBildseite::SQL_SELECT;
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
		
		$datenbank->queryDirekt(TBildseite::SQL_DELETE, Array("id" => $id));	
		
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