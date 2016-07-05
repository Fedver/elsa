<?php

	// Connection file. Needs to be included in every page requiring a DB connection.
	/*$host = "eu-cdbr-azure-north-e.cloudapp.net";
	$user = "bdc5cd9bbccfff";
	$pwd = "b47cdd29";*/
	$host = "localhost";
	$user = "elsaU9sjn";
	$pwd = "-M279i]y/*fS";
	$dbname = "elsa";
	$mysqli = new mysqli($host,$user,$pwd, $dbname);

	if ($mysqli->connect_error) {
		die('Connect Error (' . $mysqli->connect_errno . ') '
		. $mysqli->connect_error);
	}

?>