<?php
	
	include("classes/babelnetrequest.class.php");
	include("classes/bingtranslaterequest.class.php");

	$bn = new BabelNetRequest();
	$mt = new BingTranslateRequest();

	$string = $mt->translate($_REQUEST['word'], "it", "en");
	//$string = $mt->detect($_REQUEST['word']);

	echo $string;
	echo "<br>";
	//$string = $bn->getSenseByWord($_REQUEST['word'], "IT", "EN");
	
	/*foreach($string as $result) {
		echo "	Lemma ".$result['language'].": ".$result['lemma']."<br/>
				Source: ".$result['source']."<br/>
				ID: ".$result['synsetID']['id']."<br/>
			";

		$string2 = $bn->getSynsetByID($result['synsetID']['id']);

		echo "Lemma ".$string2['senses'][0]['language'].": ";

		//var_dump($string2['senses'][0]['lemma']);
		$lemmas = NULL;

		foreach($string2['senses'] as $result2) {
			$lemmas .= $result2['lemma'].", ";
		}
		echo $lemmas."<br />";

		foreach($string2['glosses'] as $result2) {
			echo "	Gloss: ".$result2['gloss']."<br/>";
		}
		echo "<br>";
	}*/

	//echo var_export($string);

	//echo $bn->HTMLizeErrlog();

	//echo $mt->HTMLizeErrlog();

?>