<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Beamer Control</title>
		<link rel="stylesheet" type="text/css" href="/{$rootDir}style.css">
		<script>
		function showBox(boxId) {
			try {
				var box = document.getElementById(boxId);
				var p = box.parentNode;
				p.style.display = "inline";
			} finally {
				document.cookie = "boxStatus_" + boxId + "=show; path=/{$rootDir}";
			}
		}
		</script>
{block name=script}
{/block}
{block name=menu}
	</head>
	<body>
		<h1>Beamer Control</h1>
		<div class="box">
			<h2>Menü</h2>
			<ul class="navigation">
				<li><a href="/{$rootDir}" onclick="showBox('playlist')">Playlist</a></li>
				<li><a href="/{$rootDir}" onclick="showBox('textseiten')">Textseiten</a></li>
				<li><a href="/{$rootDir}" onclick="showBox('bildseiten')">Bildseiten</a></li>
				<li><a href="/{$rootDir}" onclick="showBox('events')">Zeitplan</a></li>
        <li><a href="/{$rootDir}module/">Module</a></li>
				<li><a href="/{$rootDir}benutzer/">Benutzer</a></li>
				<li><a href="/{$rootDir}logout/">Abmelden</a></li>
			</ul>
		</div>
		
{/block}
{block name=body}
{/block}	
	</body>
</html>