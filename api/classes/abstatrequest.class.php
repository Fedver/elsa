<?php
		
	/* Class for send request to ABSTAT and manage responses.
	Error coding:		011 BabelNet connection failed


	*/

	// Includes.
	require_once("httprequest.class.php");

	
	class ABSTATRequest {
		
		// Internal service attributes.
		private $url;
		private $uri_basename;
		private $dataset;
		private $schemas;
		private $query_modes;
		private $http;
		private $synset;
		private $properties;
		private $distinct_words;

		// Public attributes.
		public $service_name;
		public $cardinality;

		// Output attributes.
		public $message, $errlog, $status;


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//										CONSTRUCTOR										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Returns TRUE if is ABSTAT connection is successful, FALSE otherwise.
		public function ABSTATRequest(){

			$this->message = $this->errlog = NULL;
			
			$this->service_name		= "ABSTAT";
			$this->dataset			= "dbpedia-2015-10";
			$this->url				= "http://abstat.cloudapp.net/api/v1/";
			$this->uri_basename		= "http://ld-summaries.org/resource/".$this->dataset;
			$this->http				= new HttpRequest();
			$this->properties		=
			$this->distinct_words	= array();
			$this->setModes();
			$ok = $this->checkConnection();

			if ($ok){
				$this->message	= "Class ABSTATRequest instanced successfully (dataset: ".$this->dataset."). [ABSTATRequest.ABSTATRequest]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= TRUE;
			}else{
				$this->message	= "Error code 011: ABSTAT connection failed. [ABSTATRequest.ABSTATRequest]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}
		}


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//									PRIVATE METHODS										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Hardcode setting of interaction modes with ABTAT and RDF schema definitions of interest.
		private function setModes(){
			
			$this->schemas = array(		0	=> array(	"schema"		=> "foaf:",
														"owlclass"		=> "DTP",
														"uri_base"		=> "/datatype-property/xmlns.com/foaf/0.1/"
													),
										1	=> array(	"schema"		=> "foaf:",
														"owlclass"		=> "OP",
														"uri_base"		=> "/object-property/xmlns.com/foaf/0.1/"
													),
										2	=> array(	"schema"		=> "dbo:",
														"owlclass"		=> "DTP",
														"uri_base"		=> "/datatype-property/dbpedia.org/ontology/"
													),
										3	=> array(	"schema"		=> "dbo:",
														"owlclass"		=> "OP",
														"uri_base"		=> "/object-property/dbpedia.org/ontology/"
													),
										4	=> array(	"schema"		=> "dce:",
														"owlclass"		=> "DTP",
														"uri_base"		=> "/datatype-property/purl.org/dc/elements/1.1/"
													),
									);

			$this->query_modes = array(	"query"			=> "queryWithParams",
										"cardin"		=> "AKPsCardinality",
										"occurr"		=> "resourceOccurrence",
									);
		}


		// Returns the correct ABSTAT mode name stored in $this->query_modes. The parameter is a tag name.
		private function getMode($code){
			return $this->query_modes[$code];
		}


		// Checks ABSTAT connection.
		// Returns TRUE if it's all ok and FALSE otherwise.
		// Also retrieves dataset's current cardinality, stored in $this->cardinality.
		private function checkConnection(){

			$params = array(
							"dataset" => $this->dataset,
						);
			
			$request = $this->url.$this->buildQueryString("cardin", $params);
			$this->http->setURL($request);
			$response = $this->http->send();
			$this->cardinality = $response['results']['bindings'][0]['AKPsCardinality']['value'];

			return $this->http->status;
		}


		// Builds a query string for ABSTAT interaction.
		// Returns the URL-formatted query stirng if successful, FALSE otherwise.
		private function buildQueryString($mode, $array_querystring){

			if (is_array($array_querystring) && count($array_querystring) > 0)
				return $this->getMode($mode)."?".http_build_query($array_querystring);
			else{
				$this->message	= "Error code 019: query string is not an array. [ABSTATRequest.buildQueryString]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}
		}


		// Formats an array as JSON string.
		// Returns a string if successful, FALSE otherwise.
		private function JSONize($array){
			$jsonized = NULL;
			if (is_array($array) && count($array) > 0)	{
				foreach ($array as $element)	$jsonized .= $element.",";
				$jsonized = substr($jsonized, 0, -1);
				return $jsonized;
			}else{
				$this->message	= "Error code 019: query string is not an array [ABSTATRequest.JSONize]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}
		}


		// Converts a word into DBpedia URIs and RDF schemas, applying $this->schemas.
		// Requires a word, returns two arrays: schemas and URIs.
		private function buildProperties($word){
			
			if (!$word){
				$this->message	= "Error code 001: missing parameters (word). ".$word." [ABSTATRequest.buildProperties]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}else{
				$properties = array();
				foreach ($this->schemas as $i => $row){
					$schema[] = $row['schema'];
					$uri[] = $this->uri_basename.$row['uri_base'].$this->wordTransform($word);
				}

				return array($schema, $uri);
			}
		}


		// Applies camelcase to a word. Only one exception: the first character is always lower case. Needed for DBpedia URIs.
		// Returns the word transformed if successful, FALSE otherwise.
		private function wordTransform($word){
			if (!$word){
				$this->message	= "Error code 001: missing parameters (word). ".$word." [ABSTATRequest.wordTransform]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}else{
				$word = ucwords($word);
				$word = str_replace(" ", "", $word);
				$word[0] = strtolower($word[0]);
				return $word;
			}
		}


		private function reverseURLFormat($text){
			
			$text = str_replace("%3A", ":", $text);
			$text = str_replace("%2F", "/", $text);
			$text = str_replace("%2C", ",", $text);
			return $text;

		}


		// Filters every word in a synset, removing annoying characters that are not letters.
		// Returns a filtered synset.
		private function synsetFilter($synset){

			for ($i = 0; $i < count($synset); $i++){
				$synset[$i] = str_replace("(", "", $synset[$i]);
				$synset[$i] = str_replace(")", "", $synset[$i]);
				$synset[$i] = str_replace("_", "", $synset[$i]);
				$synset[$i] = str_replace(",", "", $synset[$i]);
				$synset[$i] = str_replace(".", "", $synset[$i]);
				$synset[$i] = str_replace(";", "", $synset[$i]);
				$synset[$i] = str_replace(":", "", $synset[$i]);
				$synset[$i] = str_replace("+", "", $synset[$i]);
				$synset[$i] = str_replace("*", "", $synset[$i]);

				$synset[$i] = trim($synset[$i]);
			}

			return $synset;
		}


		// Builds a correct output array.
		private function buildOutput(){

			for ($i = 0; $i < count($this->properties['uri']); $i++){
				$this->properties['uri'][$i] = str_replace($this->uri_basename, "", $this->properties['uri'][$i]);

				for ($k = 0; $k < count($this->schemas); $k++)
					if (strpos($this->properties['uri'][$i], $this->schemas[$k]['uri_base']) !== FALSE)
						$this->properties['schema'][$i] = str_replace($this->schemas[$k]['uri_base'], $this->schemas[$k]['owlclass']." ".$this->schemas[$k]['schema'], $this->properties['uri'][$i]);
			
				$this->properties['uri'][$i] = "/".$this->dataset.$this->properties['uri'][$i];
			}
		}


		// Resets $this->properties to a NULL value.
		public function resetProperties(){
			$this->properties = array();
		}


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//									PUBLIC METHODS										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Returns an HTML-compatible version of $this->errlog.
		public function HTMLizeErrlog(){
			return str_replace("\n", "<br />", $this->errlog);
		}


		// Outputs a variable in a preformatted form.
		public function out($var){
			echo "<br><br><pre>";
			print_r($var);
			echo "</pre><br><br>";
		}


		// Outputs $this->properties.
		// If $mode is TRUE, $this->properties will be also reset to a NULL value.
		public function getProperties($mode){
			$returned = $this->properties;
			if ($mode) $this->resetProperties();
			return $returned;
		}

		
		// Performs an ABSTAT query.
		public function query($synset){

			$this->properties = array();
			$this->distinct_words = array();
			
			if (!(is_array($synset) && count($synset) > 0)){
				$this->message	= "Error code 001: missing parameters (synset).".$synset." [ABSTATRequest.query]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}else{

				$this->synset = $this->synsetFilter($synset);

				foreach ($this->synset as $k => $word){

					if (!in_array($word, $this->distinct_words)){

						$this->distinct_words[] = $word;

						list($schema, $property) = $this->buildProperties($word);

						$params = array (
													"dataset"			=> $this->dataset,
													"predicate"			=> $this->JSONize($property),
													"rankingFunction"	=> "pred_frequency",
													"format"			=> "json",
											);
						
						$request = $this->reverseURLFormat($this->url.$this->buildQueryString("query", $params));
						$this->http->setURL($request);
						$response = $this->http->send(TRUE);
						$iterat++;

						foreach ($response['results'] as $result){
							foreach ($result as $element) {
								if (!in_array($element['pred']['value'], $this->properties['uri']))
									$this->properties['uri'][] = $element['pred']['value'];
							}
						}
					}
				}

				$this->buildOutput();
			}
		}

	} // End class.

?>
