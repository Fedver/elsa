<?php
		
	/* Class for registration management.
	Error coding:		000 connection resource is not set
						001 missing parameters
						002 statement is not valid
						003 email address already taken
						004 no rows affected
						005 password not found
						006 user data not found
						007 wrong username
						008 wrong password
						009 URL format is not valid
						010 no URL specified
						011 BabelNet connection failed
						012 method is not valid
						013 parameters are not valid
						014 conflictual settings
						015 required attribute not found
						016 Microsoft Translator token not valid
						017 header is an array
						018 no synset found
						019 query string is not an array


	*/

	
	class Msghandle {
		
		// Internal service attributes.
		private $msgcode;
		private $showclassnames;

		// Output attributes.
		public $message;
		public $msglog;
		public $asciilog;
		public $status;


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//										CONSTRUCTOR										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Requires mysqli resource.
		// Returns TRUE if is successful, FALSE otherwise.
		public function Msghandle(){

			$this->message = $this->msglog = $this->asciilog = $this->status = NULL;
			$this->showclassnames = TRUE;

			$this->msgcode = array(
										"000" => array("message" => "Message code not found", "status" => "Error"),
										"001" => array("message" => "Connection resource is not set", "status" => "Error"),
										"002" => array("message" => "Missing parameters", "status" => "Error"),
										"003" => array("message" => "Statement is not valid", "status" => "Error"),
										"004" => array("message" => "Email address already taken", "status" => "Error"),
										"005" => array("message" => "No row affected", "status" => "Error"),
										"006" => array("message" => "Password not found", "status" => "Error"),
										"007" => array("message" => "User data not found", "status" => "Error"),
										"008" => array("message" => "Username doesn't exist", "status" => "Error"),
										"009" => array("message" => "Wrong password", "status" => "Error"),
										"010" => array("message" => "URL format is not valid", "status" => "Error"),
										"011" => array("message" => "No URL specified", "status" => "Error"),
										"012" => array("message" => "BabelNet connection failed", "status" => "Error"),
										"013" => array("message" => "Method is not valid", "status" => "Error"),
										"014" => array("message" => "Parameters are not valid", "status" => "Error"),
										"015" => array("message" => "Conflictual settings", "status" => "Error"),
										"016" => array("message" => "Required attribute not found", "status" => "Error"),
										"017" => array("message" => "Microsoft Translator auth token not valid", "status" => "Error"),
										"018" => array("message" => "A parameter is an array", "status" => "Error"),
										"019" => array("message" => "A parameter is not an array", "status" => "Error"),

										"998" => array("message" => "Operation successful", "status" => "Notice"),
										"999" => array("message" => "Class instanced successfully", "status" => "Notice"),
									);
			
			$this->log("999", __METHOD__);
		}


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//									PRIVATE METHODS										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		private function getMsg($code){
			$msg = $this->msgcode[$code]['message'];
			$status = $this->msgcode[$code]['status'];
			if ($msg && $status !== NULL) return array($msg, $status);
			else return array($this->msgcode['000']['message'], $this->msgcode['000']['status']);
		}


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//									PUBLIC METHODS										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		public function out($var){
			echo "<br><br><pre>";
			print_r($var);
			echo "</pre><br><br>";
		}


		public function log($code, $location=NULL, $param_string=NULL){

			list($msg, $status) = $this->getMsg($code);
			
			$this->message = $status." ".$code.": ".$msg;
			if ($param_string) $this->message .= " (".$param_string.")";
			$this->message .= ".";
			if ($this->showclassnames && $location) $this->message .= " [".$location."]";

			$this->msglog	.= "[".date("d-m-o H:i:s")."] ".$this->message."<br />";
			$this->asciilog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
			$this->status	= $status == "Notice" || $status == "Warning" ? TRUE : FALSE;

		}


		public function logCustom($environment, $code, $msgtext, $status, $location=NULL, $param_string=NULL){
			
			$this->message = $environment." ".strtolower($status)." ".$code.": ".$msgtext;
			if ($param_string) $this->message .= " (".$param_string.")";
			$this->message .= ".";
			if ($this->showclassnames && $location) $this->message .= " [".$location."]";

			$this->msglog	.= "[".date("d-m-o H:i:s")."] ".$this->message."<br />";
			$this->asciilog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
			$this->status	= $status == "Notice" || $status == "Warning" ? TRUE : FALSE;

		}
		

	} // End class.

?>