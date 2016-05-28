<?php
		
	/* Class for send request to ABSTAT and manage responses.
	Error coding:		011 BabelNet connection failed


	*/

	// Includes.
	require_once("httprequest.class.php");

	
	class ABSTATRequest {
		
		// Internal service attributes.
		private $url, $uri_basename, $dataset, $schemas, $query_modes;

		// Public attributes.
		public $service_name;

		// Output attributes.
		public $message, $errlog, $status;


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//										CONSTRUCTOR										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Requires mysqli resource.
		// Returns TRUE if is successful, FALSE otherwise.
		public function ABSTATRequest(){

			$this->message = $this->errlog = NULL;
			
			$this->service_name		= "ABSTAT";
			$this->dataset			= "dbpedia-2015-10";
			$this->url				= "http://abstat.cloudapp.net/api/v1/";
			$this->uri_basename		= "http://ld-summaries.org/resource/".$this->dataset;
			$this->http				= new HttpRequest();
			$this->setModes();
			$ok = $this->checkConnection();

			if ($ok){
				$this->message	= "Class ABSTATRequest instanced successfully. [ABSTATRequest.ABSTATRequest]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= TRUE;
			}else{
				$this->message	= "Error code 011: BabelNet connection failed. [ABSTATRequest.ABSTATRequest]";
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


		// Hardcode setting of interaction modes with ABTAT.
		private function setModes(){
			
			$this->schemas = array(		0	=> array(	"schema"		=> "foaf:",
														"owlclass"		=> "datatype-property",
														"uri_base"		=> "/datatype-property/xmlns.com/foaf/0.1/"
													),
										1	=> array(	"schema"		=> "foaf:",
														"owlclass"		=> "object-property",
														"uri_base"		=> "/object-property/xmlns.com/foaf/0.1/"
													),
										2	=> array(	"schema"		=> "dbo:",
														"owlclass"		=> "datatype-property",
														"uri_base"		=> "/datatype-property/dbpedia.org/ontology/"
													),
										3	=> array(	"schema"		=> "dbo:",
														"owlclass"		=> "object-property",
														"uri_base"		=> "/object-property/dbpedia.org/ontology/"
													),
										4	=> array(	"schema"		=> "dce:",
														"owlclass"		=> "datatype-property",
														"uri_base"		=> "/datatype-property/purl.org/dc/elements/1.1/"
													),
									);

			$this->query_modes = array(	"query"			=> "queryWithParams",
										"cardin"		=> "AKPsCardinality",
										"occurr"		=> "resourceOccurrence",
									);
		}


		// Returns the correct BabelNet mode name stored in $this->query_modes. The parameter is a tag name.
		private function getMode($code){
			return $this->query_modes[$code];
		}


		// Checks the connection with BabelNet.
		// Returns TRUE if it's all ok and FALSE otherwise.
		// Also retrieves BabelNet current version, stored in $this->bn_version.
		private function checkConnection(){
			
			$request = $this->url.$this->getMode("cardin")."?".$this->key_string;
			$this->http->setURL($request);
			$response = $this->http->send();
			//echo $this->http->HTMLizeErrlog();
			$this->bn_version = str_replace("_", ".", $response["version"]);

			return $this->http->status;
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
		
		
		// Requests the sense of a given word.
		// Returns the response.
		public function getSenseByWord($word, $source_lang, $dest_lang=NULL){

			if (!$word || !$source_lang){
				$this->message	= "Error code 001: missing parameters (word or source language). [BabelNetRequest.getSenseByWord]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}else{

				if (!$dest_lang) $dest_lang = $source_lang;

				$params = array (
											"word"				=> $word,
											"lang"				=> $source_lang,
											"filterLangs"		=> $dest_lang,
											"key"				=> $this->api_key,
											"source"			=> "WIKI",

									);
				$request = $this->url.$this->getMode("sense_by_word")."?".http_build_query($params);
				//echo $request."<br>";
				$this->http->setURL($request);
				$response = $this->http->send(TRUE);

				return $response;
			}
		}


		// Requests information of a synset given its ID.
		// Returns the response.
		public function getSynsetByID($id, $dest_lang=NULL){

			if (!$id){
				$this->message	= "Error code 001: missing parameters (id). [BabelNetRequest.getSynsetByID]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}else{

				$params = array (
											"id"				=> $id,
											"filterLangs"		=> $dest_lang,
											"key"				=> $this->api_key
									);
				$request = $this->url.$this->getMode("synset_by_id")."?".http_build_query($params);
				//echo $request."<br>";
				$this->http->setURL($request);
				$response = $this->http->send(TRUE);

				return $response;
			}
		}


		// Requests all the synset IDs of a given word.
		// Returns the response.
		public function getSynsetByWord($word, $source_lang){

			if (!$word){
				$this->message	= "Error code 001: missing parameters (word). [BabelNetRequest.getSynsetByWord]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}else{

				$params = array (
											"word"				=> $word,
											"langs"				=> $source_lang,
											"key"				=> $this->api_key
									);
				$request = $this->url.$this->getMode("synsid_by_word")."?".http_build_query($params);
				//echo $request."<br>";
				$this->http->setURL($request);
				$response = $this->http->send(TRUE);

				return $response;
			}
		}


	} // End class.

?>
