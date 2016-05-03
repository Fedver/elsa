<?php

    session_start();

	include("../classes/mysqli.connect.php");
	include("../classes/registration.class.php");

	$account = new Account($mysqli);

	if ($_REQUEST['email'] && $_REQUEST['pass']){
		
		$account->newUser($_REQUEST['email'], $_REQUEST['pass']);

		echo $account->message;

	}

?>