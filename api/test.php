<?php

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
		<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post" style="margin: 0 auto; width: 100%; height: 500px; line-height: 500px; text-align: center;">
			<input type="text" id="test" name="test" placeholder="Inserire parola da tradurre" style="width: 700px; height: 40px; font-size: large;" />
			<input type="button" value="Traduci" style="width: 100px; height: 40px; font-size: large;" onclick="translate();"/>
		</form>
		<span style="font-size: large; margin-left: 50px;">Esito:</span><br /><br />
		<div style="font-size: large; margin-left: 50px;" id="target">
		Qui viene visualizzato l'esito
		</div>
		<div id="spinner_target"></div>
    </body>
</html>
