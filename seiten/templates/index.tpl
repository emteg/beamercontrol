{extends file="./layout.tpl"}
{block name=script}
		<script>
		function collapseBox(boxId, sender) {
			box = document.getElementById(boxId);
			if (box.style.display == "block") {
				box.style.display = "none";
				img = sender.children[0];
				img.src = "./down.png"
				document.cookie = "boxStatus_" + boxId + "=collapse; path=/{$rootDir}";
			} else {
				box.style.display = "block";
				img = sender.children[0];
				img.src = "./up.png"
				document.cookie = "boxStatus_" + boxId + "=show; path=/{$rootDir}";
			}
		}
		
		function closeBox(boxId, sender) {
			var box = document.getElementById(boxId);
			var p = box.parentNode;
			p.style.display = "none";
			document.cookie = "boxStatus_" + boxId + "=hide; path=/{$rootDir}";
		}
		</script>
{/block}
{block name=body}
		<div class="box"{if $boxStatusPlaylist == "hide"} style="display: none"{/if}>
            <h2>Playlist
                <span class="boxViewButtons">
                    <a onclick="collapseBox('playlist', this)">
                        <img src="./up.png">
                    </a>
                    <a onclick="closeBox('playlist', this)">
                        <img src="./delete.png">
                    </a>
                </span>
            </h2>
            <span class="boxContent" id="playlist"{if $boxStatusPlaylist == "collapse"} style="display: none"{/if}>
                <p>Diese Module werden nacheinander angezeigt:</p>
                <ol class="playlist">
{foreach key=k item=modul from=$module name=playlist}
                    <li>
                        <span class="playlistModulName">{$modul["Name"]}</span>
                        <a href="./playlistedit.php?action=up&index={$k}" class="playlistButton"{if $k==0} style="visibility: hidden"{/if}>
                            <img src="./up.png">
                        </a>
                        <a href="./playlistedit.php?action=down&index={$k}" class="playlistButton"{if $smarty.foreach.playlist.last} style="visibility: hidden"{/if}>
                            <img src="./down.png">
                        </a>
                        <a href="./playlistedit.php?action=delete&id={$modul["playlistId"]}" class="playlistButton">
                            <img src="./delete.png">
                        </a>
				    </li>
{foreachelse}
				    - Die Playlist ist leer -
{/foreach}
                </ol>
                <form method="post" action="./playlistedit.php?action=add">
                    <h3>Modul hinzufügen</h3>
                    <select name="modulId">
{foreach $alleModule as $modul}
					   <option value="{$modul["id"]}">{$modul["Name"]}</option>
{/foreach}
                    </select>
                    <input type="submit" value="Hinzufügen"/>
                </form>
                <form method="post" action="./anzeigedauerAendern.php">
                    <h3>Anzeigedauer jedes Moduls (in Sekunden)</h3>
                    <input class="number" type="number" name="anzeigedauer" value="{$modulAnzeigeDauer}" min="1"/>
                    <input type="submit" value="Festlegen"/>
                </form>
                <form method="post" action="./eventAendern.php">
                    <h3>Grundeinstellungen</h3>
                    Veranstaltungsname:<br/>
                    <input type="text" name="name" value="{$event}" placeholder="Name der Veranstaltung"/>
                    Veranstaltungszeitraum:<br/>
                    <input type="text" name="datum" value="{$eventDate}" placeholder="Veranstaltungszeitraum"/><br/>
                    Design:<br/>
                    <select name="design">
{foreach $designs as $design}                    
                        <option value="{$design}" {if $design==$aktDesign}selected{/if}>{$design}</option>
{/foreach}
                    </select>
                    <input type="submit" value="Festlegen"/>
                </form>
                <form method="post" action="./alarmAendern.php">
                    <h3>Sofortmeldung anzeigen</h3>
                    <textarea name="alarmText" placeholder="Diese Meldung auf allen Beamern anzeigen" style="width: 20em">{str_replace("<br />", "\n", $alarmText)}</textarea>
                    <input type="checkbox" name="alarmAnzeigen" {if $alarmAnzeigen === "true"}checked{/if}/> Meldung anzeigen
                    <input type="submit" value="Festlegen"/>
                </form>
            </span>
		</div>
            
            
		<div class="box"{if $boxStatusTextseiten == "hide"} style="display: none"{/if}>
            <h2>Textseiten
                <span class="boxViewButtons">
                    <a onclick="collapseBox('textseiten', this)">
                        <img src="./up.png">
                    </a>
                    <a onclick="closeBox('textseiten', this)">
                        <img src="./delete.png">
                    </a>
                </span>
            </h2>
            <span class="boxContentWide" id="textseiten"{if $boxStatusTextseiten == "collapse"} style="display: none"{/if}>  
                <p>Bei jedem Aufruf eines Textseiten-Moduls wird eine andere der folgenden Textseiten nacheinander aufgerufen:</p>
                <ul class="textseiten">
{foreach $aktuelleTextseiten as $seite}
                    <li>
                        <span class="textseiteInhalt">{nl2br(trim($seite->inhalt))}</span>
                        <span class="textseiteAb">{date("D H:i", strtotime($seite->zeigenAb))}</span>
                        <span class="textseiteBis">
{if !is_null($seite->zeigenBis)}
					   {date("D H:i", strtotime($seite->zeigenBis))}
{else}
					   unbegrenzt
{/if}
                        </span>
                        <a href="./textseiteedit.php?action=delete&id={$seite->id}" class="textseiteButton"><img src="./delete.png"></a>
                    </li>
{foreachelse}
				    <li>- Zur Zeit werden keine Textseiten angezeigt -</li>
{/foreach}			
                </ul>
                <h3>Geplante Textseiten</h3>
                <p>Diese Textseiten werden erst ab der eingestellten Zeit angezeigt:</p>
                <ul class="textseiten">
{foreach $geplanteTextseiten as $seite}
                    <li>
                        <span class="textseiteInhalt">{nl2br(trim($seite->inhalt))}</span>
                        <span class="textseiteAb">{date("D H:i", strtotime($seite->zeigenAb))}</span>
                        <span class="textseiteBis">
{if !is_null($seite->zeigenBis)}
					   {date("D H:i", strtotime($seite->zeigenBis))}
{else}
					   unbegrenzt
{/if}
                        </span>
                        <a href="./textseiteedit.php?action=delete&id={$seite->id}" class="textseiteButton"><img src="./delete.png"></a>
                    </li>
{foreachelse}
				    <li>- Zur Zeit keine Textseiten geplant -</li>
{/foreach}			
                </ul>
                <form method="post" action="./textseiteedit.php?action=add">
                    <h3>Textseite hinzufügen</h3>
                    <textarea name="inhalt" placeholder="Anzuzeigender Text"></textarea>
                    <p>Hier kann die Anzeige der Textseite zeitlich geplant werden. Wenn man nur eine Uhrzeit eingibt, wird das aktuelle Datum verwendet.</p>
                    <span class="formCaption">Zeigen ab:</span>
                    <input type="datetime-local" name="zeigenAb" placeholder="JJJJ-MM-TT HH:MM"/> Leer: ab sofort<br/>
                    <span class="formCaption">Zeigen bis:</span>
                    <input type="datetime-local" name="zeigenBis" placeholder="JJJJ-MM-TT HH:MM"/> Leer: unbegrenzt
                    <input type="submit" value="Hinzufügen"/>
                </form>
            </span>
		</div>
		
		
		<div class="box"{if $boxStatusBildseiten == "hide"} style="display: none"{/if}>
            <h2>Bildseiten
                <span class="boxViewButtons">
                    <a onclick="collapseBox('bildseiten', this)">
                        <img src="./up.png">
                    </a>
                    <a onclick="closeBox('bildseiten', this)">
                        <img src="./delete.png">
                    </a>
                </span>
            </h2>
            <span class="boxContentWide" id="bildseiten"{if $boxStatusBildseiten == "collapse"} style="display: none"{/if}>  
                <p>Bei jedem Aufruf eines Bildseiten-Moduls wird eine andere der folgenden Bildseiten nacheinander aufgerufen:</p>
                <ul class="textseiten">
{foreach $aktuelleBildseiten as $seite}
                    <li>
						<div>
                        <span class="textseiteInhalt">{$seite->layout}</span>
                        <span class="textseiteAb">{date("D H:i", strtotime($seite->zeigenAb))}</span>
						<span class="textseiteBis">
{if !is_null($seite->zeigenBis)}
					   {date("D H:i", strtotime($seite->zeigenBis))}
{else}
					   unbegrenzt
{/if}
                        </span>
                        <a href="./bildseiteedit.php?action=delete&id={$seite->id}" class="textseiteButton"><img src="./delete.png"></a>
						</div>
						<img src="{$beamerBilderPfadRelativ}{$seite->id}.{$seite->extension}" class="bildseiteBild">
						{nl2br(trim($seite->beschriftung))}
						<div style="clear: both"></div>
                    </li>
{foreachelse}
				    <li>- Zur Zeit werden keine Bildseiten angezeigt -</li>
{/foreach}			
                </ul>
                <h3>Geplante Bildseiten</h3>
                <p>Diese Bildseiten werden erst ab der eingestellten Zeit angezeigt:</p>
                <ul class="textseiten">
{foreach $geplanteBildseiten as $seite}
                    <li>
						<div>
                        <span class="textseiteInhalt">{$seite->layout}</span>
                        <span class="textseiteAb">{date("D H:i", strtotime($seite->zeigenAb))}</span>
						<span class="textseiteBis">
{if !is_null($seite->zeigenBis)}
					   {date("D H:i", strtotime($seite->zeigenBis))}
{else}
					   unbegrenzt
{/if}
                        </span>
                        <a href="./bildseiteedit.php?action=delete&id={$seite->id}" class="textseiteButton"><img src="./delete.png"></a>
						</div>
						<img src="{$beamerBilderPfadRelativ}{$seite->id}.{$seite->extension}" class="bildseiteBild">
						{nl2br(trim($seite->beschriftung))}
						<div style="clear: both"></div>
                    </li>
{foreachelse}
				    <li>- Zur Zeit keine Bildseiten geplant -</li>
{/foreach}			
                </ul>
                <form enctype="multipart/form-data" method="post" action="./bildseiteedit.php?action=add">
                    <h3>Bildseite hinzufügen</h3>
					<span class="formCaption">Bild:</span>
					<input type="file" name="datei">
                    <textarea name="beschriftung" placeholder="Anzuzeigender Text"></textarea>
                    <span class="formCaption">Layout:</span>
					<select name="layout">
						<option value="Zweispaltig">Zweispaltig: Bild links, Text rechts</option>
						<option value="Mittig">Großes Bild mit Bildunterschrift</option>
					</select>
					<p>Hier kann die Anzeige der Textseite zeitlich geplant werden. Wenn man nur eine Uhrzeit eingibt, wird das aktuelle Datum verwendet.</p>
                    <span class="formCaption">Zeigen ab:</span>
                    <input type="datetime-local" name="zeigenAb" placeholder="JJJJ-MM-TT HH:MM"/> Leer: ab sofort<br/>
                    <span class="formCaption">Zeigen bis:</span>
                    <input type="datetime-local" name="zeigenBis" placeholder="JJJJ-MM-TT HH:MM"/> Leer: unbegrenzt
                    <input type="submit" value="Hinzufügen"/>
                </form>
            </span>
		</div>


		<div class="box"{if $boxStatusEvents == "hide"} style="display: none"{/if}>
            <h2>Zeitplan
                <span class="boxViewButtons">
                    <a onclick="collapseBox('events', this)">
                        <img src="./up.png">
                    </a>
                    <a onclick="closeBox('events', this)">
                        <img src="./delete.png">
                    </a>
                </span>
            </h2>
            <span class="boxContentWide" id="events"{if $boxStatusEvents == "collapse"} style="display: none"{/if}>
                <ul class="events">
{foreach $zeitplan as $event}
                    <li>[{$event->kategorie}] {$event->titel}
                        <span class="textseiteAb">{date("D H:i", strtotime($event->beginn))}</span>
                        <span class="textseiteBis">
{if !is_null($event->ende)}
					       {date("D H:i", strtotime($event->ende))}
{else}
                            &nbsp;
{/if}
                        </span>
                        <a href="./eventedit.php?action=delete&id={$event->id}" class="textseiteButton"><img src="./delete.png"></a>
                        <!--<a href="./event/index.php?id={$event->id}" class="textseiteButton"><img src="./edit.png"></a>-->
                    </li>        
{foreachelse}
                    <li>- Zur Zeit keine Events geplant -</li>              
{/foreach}
                </ul>  
                <form method="post" action="./eventedit.php?action=add">
                    <h3>Ereignis hinzufügen</h3>
                    <span class="formCaption">Titel:</span>
                    <input type="text" name="titel" placeholder="Titel des Ereignisses"/>
                    <p>Wenn man nur Uhrzeiten eingibt, wird das aktuelle Datum verwendet:</p>
                    <span class="formCaption">Beginn:</span>
                    <input type="datetime-local" name="beginn" placeholder="JJJJ-MM-TT HH:MM"/><br/>
                    <span class="formCaption">Ende:</span>
                    <input type="datetime-local" name="ende" placeholder="JJJJ-MM-TT HH:MM"/> (optional)<br/>
                    <span class="formCaption">Kategorie:</span>
                    <select name="kategorie">
                        <option>Allgemein</option>
                        <option value="SdS">Spiel der Stunde</option>
                    </select>
                    <input type="submit" value="Hinzufügen"/>
                </form>
            </span>
        </div>
{/block}
