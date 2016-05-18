<?php
		
	/* Class deputated to parse a header list.
	Error coding:		001 missing parameters
						017 header is an array


	*/

	// Includes.
	require_once("synset.class.php");

	
	class Parser {
		
		// Internal service attributes.
		private $header_string, $separator, $delimiter, $header_array, $header_token;

		// Public attributes.
		public $a;

		// Output attributes.
		public $message, $errlog, $status;


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//										CONSTRUCTOR										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Requires the header, a separator character and a delimiter character.
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
					$this->tokenizate();
					$this->getSynsets();
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


		// Transforms a header in form of string into an array of headers.
		private function headerToArray(){
			
			$exp_delimiter = explode($this->delimiter, $this->header_string);

			foreach ($exp_delimiter as $delimited){
				
				$exp_separator = explode($this->separator, $delimited);

				foreach ($exp_separator as $field) if ($field) $this->header_array[] = $field;

			}
		}


		// Tokenization of an array of headers. Processes a compound name and set every header as wordnet entry (WN) or compund name (CN).
		private function tokenizate(){

			foreach ($this->header_array as $key => $value){

				$synset = new Synset($value, "IT", "IT");
				
				$this->header_token['header'][$key]	= $this->processCN($value);
				$this->header_token['token'][$key]	= $synset->status ? "WN" : "CN";

			}
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

			return trim($output);

		}


		// Retrieves a synset array, a categories array and a domains array.
		private function getSynsets(){
			
			foreach ($this->header_token['header'] as $term){
				
				$synset = new Synset($term, "IT", "IT");
				if ($synset->status){
					
					list($synset_array, $categ_array, $dom_array) = $synset->getSynsetArray();

					echo "<b>".$term."</b><br><br>";
					echo "<table border='1' cellpadding='5'><tr><th>#</th><th>Synset</th><th>Categorie</th><th>Domini</th></tr>";

					for ($k = 0; $k < count($synset_array); $k++){

						echo "<tr><td>".($k+1)."</td>";

						//echo "<b>Synset ".$k.":</b><br>{";
						$synset_array[$k] = array_unique($synset_array[$k]);
						echo "<td>{".implode(", ", $synset_array[$k])."}</td>";
						/*for ($i = 0; $i < count($synset_array[$k]); $i++)
							echo $synset_array[$k][$i].",";*/
						//echo "}<br><br>";

						//echo "<b>Categorie:</b><br>{";
						$categ_array[$k] = array_unique($categ_array[$k]);
						echo "<td>{".implode(", ", $categ_array[$k])."}</td>";
						/*for ($i = 0; $i < count($categ_array[$k]); $i++)
							echo $categ_array[$k][$i]."<br>";*/
						//echo "}<br><br>";

						//echo "<b>Domini:</b><br>";
						echo "<td>";
						for ($i = 0; $i < count($dom_array[$k]['domain']); $i++)
							echo $dom_array[$k]['domain'][$i].": ".$dom_array[$k]['weight'][$i]."<br>";
						echo "</td></tr>";
					}

					echo "</table><br><hr>";
				}
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


		public function getHeaderArray(){
			return $this->header_array;
		}


		public function getTokenArray(){
			return $this->header_token;
		}


	} // End class.

?>
