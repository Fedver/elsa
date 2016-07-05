<?php
	
	require_once("api.setup.php");
	
	if (isset($_REQUEST['id']) && isset($_REQUEST['type'])){

		require("classes/parser.class.php");

		$p = new Parser($_REQUEST['header'], chr($_REQUEST['separator']), $_REQUEST['lang'], "EN");

		echo $p->getOutput();

		$test->saveTestResults($_REQUEST['id'], $p->getOutput(), $_SERVER['SERVER_NAME'], $_REQUEST['type']);
		echo "Test salvato con successo!<br>".$test->HTMLizeErrlog();

	}else
		echo "Errore: parametri mancanti. ID: ".$_REQUEST['id'].", type: ".$_REQUEST['type'].".";
?>