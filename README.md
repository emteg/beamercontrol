beamercontrol is das Backend zum Anzeigen von Slide Shows im Browser von php-info-beamer (https://github.com/emteg/php-info-beamer). Die Slide Show kann im Backend erstellt und konfiguriert werden. Um zum Backend zu gelangen im Browser /beamercontrol aufrufen.

Nach dem ersten Auschecken muss das Backend zunächst konfiguriert werden:
- Datenbank erstellen
- Datenstruktur aus setup.sql importieren
- config.vorlage.php kopieren und als config.php abspeichern
- config.php öffnen und Benutzername, Passwortund den Namen der SQL-Datenbank eintragen
- Pfade zu den Modulen und dem Bilderordner eintragen
- Datei speichern.
- Im Browser /beamercontrol öffnen (es ist keine Anmeldung erfoderlich)
- Im Punkt "Benutzer" mindestens einen Benutzer anlegen
- config.php wieder öffnen und die Zeile $loginErforderlich = false; auskommentieren und speichern.
- Im Backend anmelden (jetzt mit Passwort) und unter dem Punkt Module die zu verwendenen Module installieren.
