{extends file="../layout.tpl"}
{block name=menu}

{/block}
{block name=body}
		<div style="display: table; position: absolute; height: 100%; width: 100%; margin: -8px;">
			<div style="display: table-cell; vertical-align: middle">
				<div class="centerBox">
					<h2>Anmeldung</h2>
					<div class="boxContent">
						<p>{$message}</p>
						<form method="post" action="./index.php">
							<input type="text" name="bcUsername" placeholder="Benutzername" autofocus/>
							<input type="password" name="bcPassword" placeholder="Passwort"/>
							<input type="submit" value="Anmelden"/>
						</form>
					</div>
				</div>
			</div>
		</div>
{/block}