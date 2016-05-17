<?php
		
	/* Class deputated to parse a header list.
	Error coding:		001 missing parameters
						017 header is an array


	*/

	// Includes.
	//require_once("httprequest.class.php");

	
	class Parser {
		
		// Internal service attributes.
		private $header_string, $separator, $delimiter, $header_array;

		// Public attributes.
		public $header_token;

		// Output attributes.
		public $message, $errlog, $status;


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//										CONSTRUCTOR										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		public function Parser($header, $separator, $delimiter){
			
			if ($header && $separator && $delimiter){
				if (is_array($header)){
					$this->message	= "Error code 017:header is an array. [Parser.Parser]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
				}else{
					$this->header_string	= $header;
					$this->separator		= $separator;
					$this->delimiter		= $delimiter;
					$this->header_array		= 
					$this->header_token		= array();
					$this->message			= "Class Parser instanced successfully. [Parser.Parser]";
					$this->errlog			.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status			= TRUE;
					$this->headerToArray();
					$this->token();
				}
			}else{
				$this->message	= "Error code 001: missing parameters (header, separator, delimiter). [Parser.Parser]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
			}
		}


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//									PRIVATE METHODS										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		private function headerToArray(){
			
			$exp_delimiter = explode($this->delimiter, $this->header_string);

			foreach ($exp_delimiter as $delimited){
				
				$exp_separator = explode($this->separator, $delimited);

				foreach ($exp_separator as $field) if ($field) $this->header_array[] = $field;

			}

		}


		private function token(){

			foreach ($this->header_array as $key => $value){
				
				$this->header_token['header'][$key]	= $this->processCN($value);
				$this->header_token['token'][$key]	= $value != $this->header_token['header'][$key] ? "CN" : "WN";

			}

		}


		private function processCN($value){

			$terms	= explode(" ", $value);
			$output = NULL;

			foreach ($terms as $term){

				$output .= " ";
			
				for ($i = 0; $i < strlen($term); $i++){
				
					if (ord($term[$i]) >= 65 && ord($term[$i]) <= 90)
						$output .= " ";
					$output .= $term[$i];

				}
			}

			return trim($output);

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


	} // End class.

?>
