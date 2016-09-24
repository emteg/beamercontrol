{extends file="../layout.tpl"}
{block name=body}
		<div class="box">
			<h2>Benutzer</h2>
			<div class="boxContent">
		{if $hatNachricht}
			{if $istFehler}
				<p class="fehler">Fehler: {$nachricht}.</p>
			{else}
				<p class="erfolg">{$nachricht}!</p>
			{/if}
		{/if}
				<ul class="textseiten">
{foreach $users as $user}
					<li>{$user->name}
						<a href="./edit.php?id={$user->id}" class="benutzerButton">
							<img src="../edit.png"/>
						</a>
					</li>
{foreachelse}
					<li>- Keine Benutzer vorhanden -</li>
{/foreach}
				</ul>
				<form method="post" action="speichern.php?action=add">
					<h3>Benutzer hinzufügen</h3>
					<input type="text" name="bcUsername" placeholder="Benutzername"/>
					<input type="password" name="bcPassword" placeholder="Passwort"/>
					<input type="password" name="bcPassword2" placeholder="Passwort wiederholen"/>
					<input type="submit" value="Erstellen"/>
				</form>
			</div>
        </div>
{/block}