<?php
class TPlaylist {
	public $playlist = Array();
	public $library = Array();
	public $playlistGeladen = false;
	public $libraryGeladen = false;

	const SQL_AKTUELLE_PLAYLIST = "
		SELECT 
			modul.Id, 
			modul.Name, 
			playlist.Id AS playlistId, 
			playlist.Nummer 
		FROM 
			modul 
		JOIN 
			playlist 
		ON 
			playlist.ModulId = modul.Id 
		ORDER BY playlist.nummer ASC";
		
	const SQL_ALLE_MODULE = "
		SELECT 
			* 
		FROM 
			modul 
		WHERE IstAktiv";
		
	const SQL_UPDATE_NUMMER = "
		UPDATE
			playlist
		SET
			nummer = :nummer
		WHERE
			id = :id";
			
	const SQL_DELETE = "
		DELETE FROM
			playlist
		WHERE
			id = :id";
			
	const SQL_INSERT = "
		INSERT INTO
			playlist (ModulId, Nummer)
		VALUES (:modulid, :nummer)";
		
		
	public function ladePlaylist($datenbank) {
	
		$this->playlist = $datenbank->queryDirektArray(TPlaylist::SQL_AKTUELLE_PLAYLIST);
		$this->playlistGeladen = true;
		return $this->playlist;
		
	}
	
	public function ladeLibrary($datenbank) {
	
		$this->library = $datenbank->queryDirektArray(TPlaylist::SQL_ALLE_MODULE);
		$this->libraryGeladen = true;
		return $this->library;
		
	}
	
	public function nachOben($index, $datenbank) {
	
		if ($this->playlistGeladen) {
			if ($index > 0) {
				
				$this->playlist[$index]["Nummer"] = $this->playlist[$index]["Nummer"] - 1; 
				$this->playlist[$index - 1]["Nummer"] = $this->playlist[$index - 1]["Nummer"] + 1;			
				$this->mehrereUpdaten(Array($index, $index - 1), $datenbank);
				
			} else {
				echo "Das Modul ist schon an erster Stelle in der Playlist.";
			}
		} else {
			echo "Playlist kann nur mit geladener Playlist geändert werden.";
		}
		
	}
	
	public function nachUnten($index, $datenbank) {
	
		if ($this->playlistGeladen) {
			if ($index < count($this->playlist) - 1) {
			
				$this->playlist[$index]["Nummer"] = $this->playlist[$index]["Nummer"] + 1; 
				$this->playlist[$index + 1]["Nummer"] = $this->playlist[$index + 1]["Nummer"] - 1;
				$this->mehrereUpdaten(Array($index, $index + 1), $datenbank);
				
			} else {
				echo "Das Modul ist schon an letzter Stelle in der Playlist.";
			}
		} else {
			echo "Playlist kann nur mit geladener Playlist geändert werden.";
		}
		
	}
	
	public function loeschen($id, $datenbank) {
	
		if ($this->playlistGeladen) {
		
			$datenbank->queryDirekt(TPlaylist::SQL_DELETE, Array("id" => $id));
			foreach ($this->playlist as $key => $item) {
				if ($item["playlistId"] == $id) {
					unset($this->playlist[$key]);
					break;
				}
			}
			$this->neuNummerieren($datenbank);
			
		} else {
			echo "Playlist kann nur mit geladener Playlist geändert werden.";
		}
		
	}
	
	public function hinzufuegen($id, $datenbank) {
	
		if ($this->playlistGeladen) {
		
			$params = Array("modulid" => $id, "nummer" => count($this->playlist) + 1);
			$datenbank->queryDirekt(TPlaylist::SQL_INSERT, $params);
		
		} else {
			echo "Playlist kann nur mit geladener Playlist geändert werden.";
		}
		
	}
	
	private function mehrereUpdaten($indizes, $datenbank) {
	
		$sql = TPlaylist::SQL_UPDATE_NUMMER;
		foreach ($indizes as $index) {
		
			$params = Array("nummer" => $this->playlist[$index]["Nummer"],
				"id" => $this->playlist[$index]["playlistId"]);
			$datenbank->queryDirekt($sql, $params);
		
		}
		
	}
	
	private function neuNummerieren($datenbank) {
		
		$sql = TPlaylist::SQL_UPDATE_NUMMER;
		$i = 1;
		foreach ($this->playlist as $item) {
			$item["Nummer"] = $i;
			
			$params = Array("nummer" => $i, "id" => $item["Id"]);
			$datenbank->queryDirekt($sql, $params);
			
			$i++;
		}
		
		
	}
}
?>