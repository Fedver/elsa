<?php

    session_start();

	if ($_SESSION['user_id'] && $_SESSION['user_email']){
		
		unset($_SESSION['user_id']);
		unset($_SESSION['user_email']);

		sleep(2);

	}

?>