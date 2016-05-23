<?php
		
	/* Class deputated to parse a header list.
	Error coding:		001 missing parameters
						017 header is an array


	*/

	// Includes.
	require_once("synset.class.php");

	
	class Parser {
		
		// Internal service attributes.
		private $header_string, $separator, $delimiter, $header_array, $header_token, $domains, $categs, $synset;

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
				
				$this->header_token['token'][$key]	= $synset->status ? "WN" : "CN";
				$this->header_token['header'][$key]	= $this->header_token['token'][$key] == "CN" ? $this->processCN($value) : $value;
				

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

			return trim(strtolower($output));

		}


		// Retrieves a synset array, a categories array and a domains array.
		private function getSynsets(){

			$divide = function (&$num, $index, $tot) {
				$num = $num / $tot;
			};
			
			foreach ($this->header_token['header'] as $key => $term){
				
				$this->synset[$key] = new Synset($term, "IT", "IT");

				if ($this->synset[$key]->status){
					$this->synset[$key]->getSynsetArray();
				}
			}

			foreach ($this->synset as $i => $syns){

				$all_categories = array();
				$all_domains	= array();

				echo "<b>Header".($i+1)."</b>";
				echo "<table border='1' cellpadding='5'><tr><th>#</th><th>Synset</th><th>Categorie</th><th>Domini</th><th>Sources</th></tr>";
				
				foreach ($syns->synset_array as $k => $syns_arr){
					
					echo "<tr><td>".($k+1)."</td>";
					echo "<td>{".implode(", ", $syns_arr['lemma'])."}</td>";
					echo "<td>".implode(", ", $syns_arr['category'])."</td>";
					echo "<td>";
					for ($j = 0; $j < count($syns_arr['domain']); $j++)
						echo $syns_arr['domain'][$j].": ".$syns_arr['weight'][$j]."<br>";
					echo "</td>";
					echo "<td>".implode(", ", $syns_arr['source'])."</td>";
					echo "</tr>";

					for ($j = 0; $j < count($syns_arr['category']); $j++) if ($syns_arr['category'][$j]) $all_categories[] = $syns_arr['category'][$j];
					for ($j = 0; $j < count($syns_arr['domain']); $j++) if ($syns_arr['domain'][$j]) $all_domains[] = $syns_arr['domain'][$j];

				}

				echo "</table>";

				$dist_categs[$i]['name'] = array_unique($all_categories);
				foreach ($dist_categs[$i]['name'] as $k => $row) $dist_categs[$i]['weight'][$k] = 0;
				$this->categs[$i] = array_combine($dist_categs[$i]['name'], $dist_categs[$i]['weight']);
				foreach ($all_categories as $category) if ($this->categs[$i][$category] !== NULL) $this->categs[$i][$category]++;
				array_walk($this->categs[$i], $divide, count($all_categories));

				echo "Categorie (".count($all_categories).")<br>";
				foreach ($this->categs[$i] as $key => $value) echo $key.": ".$value." (".($value * count($all_categories))." su ".count($all_categories).")<br>";
				echo "<br><br>";

				$dist_domains[$i]['name'] = array_unique($all_domains);
				foreach ($dist_domains[$i]['name'] as $k => $row) $dist_domains[$i]['weight'][$k] = 0;
				$this->domains[$i] = array_combine($dist_domains[$i]['name'], $dist_domains[$i]['weight']);
				foreach ($all_domains as $domain) if ($this->domains[$i][$domain] !== NULL) $this->domains[$i][$domain]++;
				array_walk($this->domains[$i], $divide, count($all_domains));
				
				echo "Domini (".count($all_domains).")<br>";
				foreach ($this->domains[$i] as $key => $value) echo $key.": ".$value." (".($value * count($all_domains))." su ".count($all_domains).")<br>";
				echo "<br><br>";

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


		public function getSynset(){
			return $this->synset;
		}


		public function getDomains(){
			return $this->domains;
		}


		public function getCategs(){
			return $this->categs;
		}


	} // End class.

?>
