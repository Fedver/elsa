<?php
		
	/* Class deputated to parse a header list.
	Error coding:		001 missing parameters
						017 header is an array


	*/

	// Includes.
	require_once("bingtranslaterequest.class.php");
	require_once("abstatrequest.class.php");
	require_once("synset.class.php");

	
	class Partial {
		
		// Internal service attributes.
		private $header_string;
		private $separator;
		private $header_array;
		private $header_token;
		private $domains;
		private $categs;
		private $synset;
		private $weight;
		private $table_categs;
		private $table_domains;
		private $source_lang;
		private $dest_lang;
		private $translation;
		private $property;
		private $output;

		// Output attributes.
		public $message, $errlog, $status;

		// Parameters and configuration attributes.
		public $categ_k		= 0.6;
		public $domain_k	= 0.4;
		public $threshold_k	= 0.1;


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//										CONSTRUCTOR										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Requires the header, a separator character and a delimiter character.
		public function Partial($header, $separator, $source_lang, $dest_lang){

			global $msg;
			
			if ($header && $separator && $dest_lang && $source_lang){
				if (is_array($header)){
					$msg->log("018", __METHOD__, "header");
					$this->message	= "Error code 017:header is an array. [Partial.Partial]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
				}else{
					$this->header_string	= $header;
					$this->separator		= $separator;
					$this->source_lang		= $source_lang;
					$this->dest_lang		= $dest_lang;
					$this->header_array		= 
					$this->header_token		= array();
					$msg->log("998", __METHOD__);
					$this->message			= "Class Parser instanced successfully. [Partial.Partial]";
					$this->errlog			.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status			= TRUE;
					$this->headerToArray();
					$this->translate();
					$this->retrieveMapping();
					$this->buildOutput();

					$this->out($this->output);
				}
			}else{
				$msg->log("002", __METHOD__, "header, separator, source_lang, dest_lang");
				$this->message	= "Error code 001: missing parameters (header, separator, source_lang, dest_lang). [Partial.Partial]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
			}
		}


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//									PRIVATE METHODS										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Transforms a header in form of string into an array of headers.
		private function headerToArray(){

			$harray = explode($this->separator, $this->header_string);
			foreach ($harray as $field) if ($field) $this->header_array['lemma'][] = $this->processCN($field);
			
			/*$exp_delimiter = explode($this->delimiter, $this->header_string);

			foreach ($exp_delimiter as $delimited){
				
				$exp_separator = explode($this->separator, $delimited);

				foreach ($exp_separator as $field) if ($field) $this->header_array[] = $field;

			}*/
		}


		// Processes a compound name, separating camel case words.
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

			return trim(strtolower($output));

		}



		// Retrieves the synset in destination language.
		private function translate(){

			$mt = new BingTranslateRequest();

			foreach ($this->header_array['lemma'] as $i => $word){

				$translations = $mt->translateMany($word, $this->source_lang, $this->dest_lang);

				$this->header_array['translation'][$i] = $translations;

			}
		}


		// Performs ABSTAT queries and retrieves DBpedia mappings.
		private function retrieveMapping(){
			
			$abstat = new ABSTATRequest();

			foreach ($this->header_array['translation'] as $i => $array_lemmas){

				$predicates		= array();
					
				$abstat->query($array_lemmas);

				$abstatprop = $abstat->getProperties(TRUE);

				if ($abstatprop['schema'] || is_array($abstataprop['schema'])){
					$predicates = array_merge($predicates, $abstatprop['schema']);
					$predicates = array_unique($predicates);
					$this->header_array['properties'][$i] = $predicates;
				}
			}

			$this->out($this->header_array);
		}


		private function buildOutput(){
			
			foreach ($this->header_array['lemma'] as $i => $row){

					$this->output[$i]['header'] = $row;
					$this->output[$i]['properties'] = $this->header_array['properties'][$i];

				}

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

	} // End class.

?>
