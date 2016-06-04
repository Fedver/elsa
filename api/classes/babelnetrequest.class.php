<?php
		
	/* Class for send request to BabelNet and manage responses.
	Works with BabelNet 3.6 and lower. Higher versions compatibility is not guaranteed.
	Error coding:		011 BabelNet connection failed


	*/

	// Includes.
	require_once("httprequest.class.php");

	
	class BabelNetRequest {
		
		// Internal service attributes.
		private $url;
		private $api_key;
		private $key_string;
		private $http;
		private $query_modes;

		// Public attributes.
		public $bn_version;
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
		public function BabelNetRequest(){

			global $msg;

			$this->message = $this->errlog = NULL;
			
			$this->api_key			= "0323bb0e-3eab-4923-85bc-36f974868f88";
			$this->key_string		= "key=".$this->api_key;
			$this->service_name		= "BabelNet";
			$this->url				= "https://babelnet.io/v3/";
			$this->http				= new HttpRequest();
			$this->setModes();
			$ok = $this->checkConnection();
			
			if ($ok){
				$msg->log("999", __METHOD__, "BabelNet version: ".$this->bn_version);
				$this->message	= "Class BabelNetRequest instanced successfully. BabelNet version: ".$this->bn_version." [BabelNetRequest.BabelNetRequest]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= TRUE;
			}else{
				$msg->log("012", __METHOD__);
				$this->message	= "Error code 011: BabelNet connection failed. [BabelNetRequest.BabelNetRequest]";
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


		// Hardcode setting of interaction modes with BabelNet.
		private function setModes(){
			
			$this->query_modes = array(	"version"			=> "getVersion",
										"synsid_by_word"	=> "getSynsetIds",
										"synset_by_id"		=> "getSynset",
										"sense_by_word"		=> "getSenses",
										"synsid_by_resid"	=> "getSynsetIdsFromResourceID",
										"edge_by_synsid"	=> "getEdges"
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
			
			$request = $this->url.$this->getMode("version")."?".$this->key_string;
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
				$msg->log("002", __METHOD__, "word, source_lang");
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
				$msg->log("002", __METHOD__, "id");
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
				$msg->log("002", __METHOD__, "word");
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
