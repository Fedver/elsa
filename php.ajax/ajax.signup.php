<?php

    session_start();

	include("../classi/mysqli.connect.php");
	include("../classi/account.class.php");

	$account = new Account($mysqli);

	//var_dump($mysqli);

	//var_dump($account);


	if ($_REQUEST['email'] && $_REQUEST['pass']){
		
		$account->newUser($_REQUEST['email'], $_REQUEST['pass']);

		echo $account->message;

	}

?>