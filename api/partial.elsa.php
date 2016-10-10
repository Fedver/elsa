<?php
	
	require_once("api.setup.php");
	
	if (isset($_REQUEST['id']) && isset($_REQUEST['type'])){

		require("classes/partial.class.php");

        $test->setInitialTime();
        $p = new Partial($_REQUEST['header'], chr($_REQUEST['separator']), strtolower($_REQUEST['lang']), "en");
        $test->calcTime();

		echo $p->getOutput();

		$test->saveTestResults($_REQUEST['id'], $p->getOutput(), $_SERVER['SERVER_NAME'], $_REQUEST['type']);
		if ($show_notices) echo "Test salvato con successo!<br>".$test->HTMLizeErrlog();

        $acc->addHeaders($p->header_count);

	}else
		echo "Errore: parametri mancanti. ID: ".$_REQUEST['id'].", type: ".$_REQUEST['type'].".";
?>