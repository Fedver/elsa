<?php
	
	ini_set('max_execution_time', 1000);

	require_once("classes/msghandle.class.php");
	$msg = new Msghandle();
	
	if ($_REQUEST['mode'] == "translate"){

		require("classes/partial.class.php");

		$p = new Partial($_REQUEST['header'], chr($_REQUEST['separator']), strtolower($_REQUEST['lang']), "en");

	}

?>
