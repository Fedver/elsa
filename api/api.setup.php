<?php
	
	ini_set('max_execution_time', 2000);

	require_once("classes/msghandle.class.php");
	require_once("classes/mysqli.connect.php");
	require_once("classes/account.class.php");
	require_once("classes/test.class.php");

	$msg = new Msghandle();
	$acc = new Account($mysqli, $_REQUEST['key']);
	if ($acc->status) {
		$test = new Test($mysqli, "save");
		$show_notices = FALSE;
	}else{
		die($acc->HTMLizeErrlog());
	}

?>
