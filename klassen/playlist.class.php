<?php
class TPlaylist {
	public $playlist = Array();
	public $library = Array();
	public $playlistGeladen = false;
	public $libraryGeladen = false;

	const TABLE_VERSION = 1;

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

	const SQL_CREATE_TABLE = "
		CREATE TABLE IF NOT EXISTS `playlist` (
			`Id` int(11) NOT NULL AUTO_INCREMENT,
			`ModulId` int(11) NOT NULL,
			`Nummer` int(11) NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='".self::TABLE_VERSION."'";
	const SQL_TABLE_EXISTS = "
		SELECT
			*
		FROM
			information_schema.tables
		WHERE
			TABLE_SCHEMA = :table_schema AND
			TABLE_NAME = 'playlist'";

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
		$sql = "ALTER TABLE playlist COMMENT = '1'";
		$datenbank->queryDirekt($sql);
		return self::TABLE_VERSION;
	}		
		
	public function ladePlaylist($datenbank) {
	
		$this->playlist = $datenbank->queryDirektArray(self::SQL_AKTUELLE_PLAYLIST);
		$this->playlistGeladen = true;
		return $this->playlist;
		
	}
	
	public function ladeLibrary($datenbank) {
	
		$this->library = $datenbank->queryDirektArray(self::SQL_ALLE_MODULE);
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
		
			$datenbank->queryDirekt(self::SQL_DELETE, Array("id" => $id));
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
			$datenbank->queryDirekt(self::SQL_INSERT, $params);
		
		} else {
			echo "Playlist kann nur mit geladener Playlist geändert werden.";
		}
		
	}
	
	private function mehrereUpdaten($indizes, $datenbank) {
	
		$sql = self::SQL_UPDATE_NUMMER;
		foreach ($indizes as $index) {
		
			$params = Array("nummer" => $this->playlist[$index]["Nummer"],
				"id" => $this->playlist[$index]["playlistId"]);
			$datenbank->queryDirekt($sql, $params);
		
		}
		
	}
	
	private function neuNummerieren($datenbank) {
		
		$sql = self::SQL_UPDATE_NUMMER;
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