<?php
class TEvent {
	public $id = 0;
	public $titel = "";
	public $beginn = "";
	public $ende = "";
	public $kategorie = "";
	
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
			
	public function create($titel, $beginn, $ende, $kategorie, $datenbank) {
	
		if ($this->kategorieKorrekt($kategorie)) {
			if ($ende == "") {
				$sql = TEvent::SQL_INSERT_KEIN_ENDE;
				$params = Array("titel" => $titel, "beginn" => $beginn,
					"kategorie" => $kategorie);
			} else {
				$sql = TEvent::SQL_INSERT;
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
				$sql = TEvent::SQL_UPDATE_KEIN_ENDE;
			} else {
				$sql = TEvent::SQL_UPDATE;
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
		$datenbank->queryDirekt(TEvent::SQL_DELETE, Array("id" => $id));
	}
	
	private function kategorieKorrekt($kategorie) {
	
		return $kategorie == "Allgemein" || $kategorie == "SdS";
		
	}
}

class EventFactory {
	public function create($record) {
		$result = new TEvent();
		
		$result->id = $record["Id"];
		$result->titel = $record["Titel"];
		$result->beginn = $record["Beginn"];
		$result->ende = $record["Ende"];
		$result->kategorie = $record["Kategorie"];
		
		return $result;
	}
}
?>