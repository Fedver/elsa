<?php

	// Connection file. Needs to be included in every page requiring a DB connection.
    $mysqli = new mysqli("eu-cdbr-azure-north-e.cloudapp.net","bdc5cd9bbccfff","b47cdd29", "elsa");

	if ($mysqli->connect_error) {
		die('Connect Error (' . $mysqli->connect_errno . ') '
		. $mysqli->connect_error);
	}

?>