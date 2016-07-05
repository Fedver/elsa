<?php
	
	ini_set('max_execution_time', 1000);

	require_once("classes/msghandle.class.php");
	require_once("classes/mysqli.connect.php");
	require_once("classes/test.class.php");

	$msg = new Msghandle();
	$test = new Test($mysqli, "save");

	$show_notices = TRUE;

?>
