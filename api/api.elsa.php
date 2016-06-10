<?php
	
	ini_set('max_execution_time', 1000);

	require_once("classes/msghandle.class.php");
	$msg = new Msghandle();
	
	if ($_REQUEST['mode'] == "translate"){
	
		
		require("classes/babelnetrequest.class.php");
		require("classes/bingtranslaterequest.class.php");

		$bn = new BabelNetRequest();
		$mt = new BingTranslateRequest();

		$string = $mt->translateSingle($_REQUEST['word'], "it", "en");
		echo $msg->msglog;

		echo $string;
		echo "<br>";
	}elseif ($_REQUEST['mode'] == "compute"){

		require("classes/parser.class.php");

		$p = new Parser($_REQUEST['header'], chr($_REQUEST['separator']), $_REQUEST['lang'], "EN");

	}

?>