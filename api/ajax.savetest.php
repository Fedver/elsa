<?php
	
	require_once("classes/mysqli.connect.php");
	require_once("classes/test.class.php");

	$test = new Test($mysqli, "save");

	if ($_REQUEST['id'] && $_REQUEST['mapping'] && $_REQUEST['type']){
		
		$test->saveTestResults($_REQUEST['id'], $_REQUEST['mapping'], $_SERVER['SERVER_NAME'], $_REQUEST['type']);
		echo "Test salvato con successo!<br>".$test->HTMLizeErrlog();

	}else{
		
		echo "Errore: parametri mancanti. ID: ".$_REQUEST['id'].", mapping: ".$_REQUEST['mapping'].", type: ".$_REQUEST['type'].".";

	}

?>