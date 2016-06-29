<?php
	
	ini_set('max_execution_time', 1000);

	require_once("classes/msghandle.class.php");
	$msg = new Msghandle();
	
	if ($_REQUEST['mode'] == "compute"){

		require("classes/parser.class.php");

		$p = new Parser($_REQUEST['header'], chr($_REQUEST['separator']), $_REQUEST['lang'], "EN");

	}

?>