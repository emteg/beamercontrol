<?php
class TModul {
	public $id = 0;
	public $name = "";
	public $istAktiv = false;
	
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
	
	public function create($name, $datenbank) {
	
		$datenbank->queryDirekt(TModul::SQL_INSERT, Array("name" => $name));	
		$this->id = $datenbank->lastInsertId();
		$this->name = $name;
		$this->istAktiv = true;
		
	}
	
	public function update($id, $name, $datenbank) {
	
		$sql = TModul::SQL_UPDATE;
		$params = Array("name" => $name, "id" => $id);
		$datenbank->queryDirekt($sql, $params);
	
	}
	
	public function destroy($id, $datenbank) {
	
		$sql = "DELETE FROM playlist WHERE ModulId = :id";
		$params = Array("id" => $id);
		$datenbank->queryDirekt($sql, $params);
		
		$sql = TModul::SQL_DELETE;
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