<?php

    session_start();

	include("../classes/mysqli.connect.php");
	include("../classes/registration.class.php");

	$account = new Account($mysqli);

	if ($_REQUEST['email'] && $_REQUEST['pass']){
		
		$ok = $account->doLogin($_REQUEST['email'], $_REQUEST['pass']);

		if ($ok){
			$_SESSION['user_id'] = $account->user_id;
			$_SESSION['user_email'] = $account->user_email;
		}

		echo $account->message;

	}

?>