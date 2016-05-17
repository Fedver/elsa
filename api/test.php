<?php
	
$selectvalues = array(
						"Nome;Cognome;Soprannome;Lingua;DataDiNascita;LuogoDiNascita;Apparizione",
						"Nome;\"Cognome\";Soprannome;Lingua;DataDiNascita;LuogoDiNascita;Apparizione"

					);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>ELSA test</title>
		<script src='../js/jquery.min.js'></script>
		<script src='js/test.js'></script>
		<script src='../js/spin.js'></script>
		<script src='../js/jquery.spin.js'></script>
    </head>
    <body>
		<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post" style="margin: 0 auto; width: 100%; height: 300px; margin-top: 200px; text-align: center;">
			<input type="text" id="test" name="test" placeholder="Inserire parola da tradurre" style="width: 700px; height: 40px; font-size: large;" />
			<input type="button" value="Traduci" style="width: 100px; height: 40px; font-size: large;" onclick="translate();"/>
			<br /><br /><br />
			<select id="header" name="header" style="width: 700px; height: 40px; font-size: large;">
				<?php
						for ($i = 0; $i < count($selectvalues); $i++){
							echo "<option value='".$selectvalues[$i]."'>".$selectvalues[$i]."</option>";
						}

				?>
			</select><br />
			<input type="text" id="separator" name="separator" placeholder="Inserire separatore" style="width: 200px; height: 40px; font-size: large;" value=";"/><br />
			<input type="text" id="delimiter" name="delimiter" placeholder="Inserire delimitatore" style="width: 200px; height: 40px; font-size: large;" value='"' /><br />
			<input type="button" value="Computa" style="width: 100px; height: 40px; font-size: large;" onclick="compute();"/>
		</form>
		<span style="font-size: large; margin-left: 50px;">Esito:</span><br /><br />
		<div style="font-size: large; margin-left: 50px;" id="target">
		Qui viene visualizzato l'esito
		</div>
		<div id="spinner_target"></div>
    </body>
</html>
