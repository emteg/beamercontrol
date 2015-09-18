{extends file="../layout.tpl"}
{block name=body}
		<div class="box">
			<h2>Module</h2>
			<div class="boxContentWide">
{if $hatNachricht}
	{if $istFehler}
				<p class="fehler">Fehler: {$fehler}.</p>
	{else}
				<p class="erfolg">{$fehler}.</p>
	{/if}
{/if}
				<p>Die folgenden Module sind im System vorhanden:</p><br/>
				<table>
					<tr>
						<th>Name</th><th>Status</th><th>Aktionen</th>
					</tr>
{foreach $module as $modul}
					<tr>
						<td>{$modul["name"]}</td>
{if $modul["inDatenbank"] && $modul["istBereit"]}
						<td>Installiert</td><td><a href="modul.php?action=uninstall&id={$modul['id']}">Deinstallieren</a></td>
{else if $modul["inDatenbank"]}
						<td>Installiert & Fehlerhaft</td><td><a href="modul.php?action=uninstall&id={$modul['id']}">Deinstallieren</a></td>
{else if $modul["istBereit"]}
						<td>Nicht installiert</td><td><a href="modul.php?action=install&name={$modul['name']}">Installieren</a></td>
{else}
						<td>Nicht installiert & Fehlerhaft</td><td><a href="modul.php?action=delete&name={$modul['name']}">Dateien löschen</a></td>
{/if}
					</tr>
{foreachelse}
					<tr colspan="3">
						<td>- Keine Module vorhanden -</td>
					</tr>
{/foreach}
				</table>
				
			</div>
        </div>
{/block}