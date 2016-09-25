<?php
require_once "./klassen/authentication.class.php";
require_once "./config.php";
require_once "./klassen/datenbank.class.php";
require_once "./klassen/bildseite.class.php";

$datenbank = new Datenbank();
$bildseite = new TBildseite();

if (!isset($_GET["action"])) {
	die("Keine Aktion :/");
}

switch ($_GET["action"]) {
	case "add":
		hinzufuegen($bildseite, $datenbank);
		break;
		
	case "delete":
		loeschen($bildseite, $datenbank);
		break;
}


function hinzufuegen($bildseite, $datenbank) {

	if (hinzufuegenVariablenVorhanden()) {
	
		if ($extension = getExtension()) {
	
			// ZeigenAb wurde angegeben
			if (strtotime($_POST["zeigenAb"]) !== false) {
			
				// ZeigenBis wurde angegeben
				if (strtotime($_POST["zeigenBis"]) !== false) {
				
					$bildseite->create($datenbank, $extension, $_POST["beschriftung"], 
						$_POST["layout"], date("Y-m-d H:i:s", strtotime($_POST["zeigenAb"])), 
						date("Y-m-d H:i:s", strtotime($_POST["zeigenBis"])));
					
				// ZeigenBis wurde nicht angegeben
				} else {
				
					$bildseite->create($datenbank, $extension, $_POST["beschriftung"], 
						$_POST["layout"], date("Y-m-d H:i:s", strtotime($_POST["zeigenAb"])));
					
				}
			
			// ZeigenAb wurde nicht angegeben
			} else {
			
				$bildseite->create($datenbank, $extension, $_POST["beschriftung"], 
					$_POST["layout"]);
			}
			
			dateiVerschieben($bildseite->id, $extension);
			zurueck();
		
		} else {
			echo "Datei wurde nicht erfolgreich hochgeladen :/";
		}
			
	
	} else {
		echo "Nicht alle oder ungültige Daten übertragen :/";
	}
	
}


function loeschen($bildseite, $datenbank) {

	if (loeschenVariablenVorhanden()) {
	
		if ($bildseite->read($_GET["id"], $datenbank)) {
			$bildseite->destroy($bildseite->id, $datenbank);
		}
		zurueck();
		
	} else {
		echo "Nicht alle oder ungültige Daten übertragen :/";
	}
	
}


function zurueck() {
	global $config;
	header("Location: /" . $config["rootDir"]);
}


function hinzufuegenVariablenVorhanden() {
	return isset($_POST["zeigenAb"]) && isset($_POST["zeigenBis"]) && 
		isset($_POST["beschriftung"]) && isset($_POST["layout"]);
}


function loeschenVariablenVorhanden() {
	return isset($_GET["id"]) && is_numeric($_GET["id"]);
}


function getExtension() {
	if (isset($_FILES['datei']) && $_FILES['datei']['error'] == UPLOAD_ERR_OK) {
		$s = explode('.', $_FILES['datei']['name']);
		
		if (count($s) == 2) {
			return $s[1];
		} else {
			return false;
		}
	} else {
		handleUploadError($_FILES['datei']['error']);
		return false;
	}
}

function handleUploadError($error) {
	echo "Upload Error " . $error . ": ";
	switch ($error) {
		case 1:
			echo "UPLOAD_ERR_INI_SIZE.<br/>";
			echo "The uploaded file exceeds the upload_max_filesize directive in php.ini.<br/>";
			echo "Current setting: " . ini_get("upload_max_filesize") . ".<br/>";
			break;
		case 2:
			echo "UPLOAD_ERR_FORM_SIZE.<br/>";
			echo "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.<br/>";
			break;
		case 3:
			echo "UPLOAD_ERR_PARTIAL.<br/>";
			echo "The uploaded file was only partially uploaded.<br/>";
			break;
		case 4:
			echo "UPLOAD_ERR_NO_FILE.<br/>";
			echo "No file was uploaded.<br/>";
			break;
		case 6:
			echo "UPLOAD_ERR_NO_TMP_DIR.<br/>";
			echo "Missing a temporary folder.<br/>";
			break;
		case 7:
			echo "UPLOAD_ERR_CANT_WRITE.<br/>";
			echo "Failed to write file to disk.<br/>";
			break;
		case 8:
			echo "UPLOAD_ERR_EXTENSION.<br/>";
			echo "A PHP extension stopped the file upload.<br/>";
			break;
	}
}

function dateiVerschieben($id, $extension) {
	global $config;
	
	$quelle = $_FILES['datei']['tmp_name'];
	$ziel = $config["beamerBilderPfad"] . $id . "." . $extension;
	
	if (!is_dir($config["beamerBilderPfad"])) {
		mkdir($config["beamerBilderPfad"]);
	}
	
	rename($quelle, $ziel);
}
?>
