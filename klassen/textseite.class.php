<?php
class TTextseite {
	public $id = 0;
	public $inhalt = "";
	public $istAktiv = false;
	public $zeigenAb = "";
	public $zeigenBis = "";
	
	const SQL_SELECT = "
		SELECT
			*
		FROM
			textseite
		WHERE
			Id = :id";
	const SQL_SELECT_NEXT = "
		SELECT 
			* 
		FROM 
			`textseite`
		WHERE 
			Id > :id AND
			IstAktiv AND 
			ZeigenAb < NOW() AND 
			(ZeigenBis IS NULL OR ZeigenBis > NOW())
		LIMIT 1

		UNION

		SELECT 
			* 
		FROM 
			`textseite` 
		WHERE 
			IstAktiv AND 
			ZeigenAb < NOW() AND 
			(ZeigenBis IS NULL OR ZeigenBis > NOW()) 
		LIMIT 1";
	const SQL_SELECT_ALLE = "
		SELECT
			*
		FROM
			textseite";
	const SQL_SELECT_AKTUELLE = "
		SELECT 
			* 
		FROM 
			`textseite` 
		WHERE 
			IstAktiv AND 
			ZeigenAb <= NOW() AND 
			(ZeigenBis IS NULL OR ZeigenBis >= NOW())";
	const SQL_SELECT_GEPLANTE = "
		SELECT
			*
		FROM
			textseite
		WHERE
			IstAktiv AND zeigenAb > NOW()
		ORDER BY
			zeigenAb ASC";
	
	const SQL_INSERT = "
		INSERT INTO
			textseite (Inhalt, ZeigenAb, ZeigenBis)
		VALUES
			(:inhalt, :zeigenAb, :zeigenBis)";
			
	const SQL_UPDATE = "
		UPDATE
			textseite
		SET
			Inhalt = :inhalt,
			ZeigenAb = :zeigenAb,
			ZeigenBis = :zeigenBis,
			IstAktiv = :istAktiv
		WHERE
			Id = :id";
	const SQL_AKTIVIEREN = "
		UPDATE
			textseite
		SET
			IstAktiv = 1
		WHERE
			Id = :id";
	const SQL_DEAKTIVIEREN = "
		UPDATE
			textseite
		SET
			IstAktiv = 0
		WHERE
			Id = :id";
	const SQL_DELETE = "
		DELETE FROM
			textseite
		WHERE
			Id = :id";
			
	public function create($datenbank, $inhalt, $zeigenAb = "", $zeigenBis = "") {
	
		$spalten[] = "Inhalt";
		$variablen[] = ":inhalt";
		$params["inhalt"] = $inhalt;
		
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
		
		$sql = "INSERT INTO textseite (" . implode(",", $spalten) . ") ";
		$sql .= "VALUES (" . implode(",", $variablen) . ")";
		$datenbank->queryDirekt($sql, $params);
		
		$this->id = $datenbank->lastInsertId();
		
		return $this->id;
	}
	
	public function read($id, $datenbank) {

		$sql = TTextseite::SQL_SELECT;
		$params["id"] = $id;
		
		$result = $datenbank->queryDirektArray($sql, $params);
		$this->applyRecord($result);
		
		return $result;
		
	}
	
	public function readNext($lastId, $datenbank) {
		
		$sql = TTextseite::SQL_SELECT_NEXT;
		$params["id"] = $lastId;
		
		$result = $datenbank->queryDirektArray($sql, $params);
		$this->applyRecord($result);
		
		return $result;
		
	}
	
	public function update($id, $datenbank) {
	
	}
	
	public function destroy($id, $datenbank) {
		$datenbank->queryDirekt(TTextseite::SQL_DELETE, Array("id" => $id));
	}
	
	public function applyRecord($record) {
	
		$this->id = $record["Id"];
		$this->inhalt = $record["Inhalt"];
		$this->zeigenAb = $record["ZeigenAb"];
		$this->zeigenBis = $record["ZeigenBis"];
		$this->istAktiv = $record["IstAktiv"];
		
	}
}

class TextseiteFactory {
	public function create($record) {
		$result = new TTextseite();
		
		$result->id = $record["Id"];
		$result->inhalt = $record["Inhalt"];
		$result->zeigenAb = $record["ZeigenAb"];
		$result->zeigenBis = $record["ZeigenBis"];
		$result->istAktiv = $record["IstAktiv"];
		
		return $result;
	}
}
?>