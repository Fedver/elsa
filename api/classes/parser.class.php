<?php
		
	/* Class deputated to parse a header list.
	Error coding:		001 missing parameters
						017 header is an array


	*/

	// Includes.
	require_once("synset.class.php");
	require_once("translation.class.php");
	require_once("abstatrequest.class.php");

	
	class Parser {
		
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
		public $message, $errlog, $status, $showprogress = FALSE;

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
		public function Parser($header, $separator, $source_lang, $dest_lang){

			global $msg;
			
			if ($header && $separator && $dest_lang && $source_lang){
				if (is_array($header)){
					$msg->log("018", __METHOD__, "header");
					$this->message	= "Error code 017:header is an array. [Parser.Parser]";
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
					$this->message			= "Class Parser instanced successfully. [Parser.Parser]";
					$this->errlog			.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status			= TRUE;
					$this->headerToArray();
					$this->tokenizate();
					$this->buildSynsets();
					$this->calculateWeightByTable();
					$this->translate();
					$this->retrieveMapping();
					$this->buildOutput();

					//$this->out($this->output);
				}
			}else{
				$msg->log("002", __METHOD__, "header, separator, source_lang, dest_lang");
				$this->message	= "Error code 001: missing parameters (header, separator, source_lang, dest_lang). [Parser.Parser]";
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
			foreach ($harray as $field) if ($field) $this->header_array[] = trim($field);
			
			/*$exp_delimiter = explode($this->delimiter, $this->header_string);

			foreach ($exp_delimiter as $delimited){
				
				$exp_separator = explode($this->separator, $delimited);

				foreach ($exp_separator as $field) if ($field) $this->header_array[] = $field;

			}*/
		}


		// Tokenization of an array of headers. Processes a compound name and set every header as wordnet entry (WN) or compund name (CN).
		private function tokenizate(){

			foreach ($this->header_array as $key => $value){

				$synset = new Synset($value, $this->source_lang, $this->source_lang);
				
				$this->header_token['token'][$key]	= $synset->status ? "WN" : "CN";
				$this->header_token['header'][$key]	= $this->header_token['token'][$key] == "CN" ? $this->processCN($value) : strtolower($value);

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
		private function buildSynsets(){

			$divide = function (&$num, $index, $tot) {
				$num = $num / $tot;
			};

			$table_categories = $table_domains = array();
			
			foreach ($this->header_token['header'] as $key => $term){
				
				$this->synset[$key] = new Synset($term, $this->source_lang, $this->source_lang);

				if ($this->synset[$key]->status || count($this->synset[$key]->synset_source) > 0){
					$this->synset[$key]->getSynsetArray();
				}
			}

			foreach ($this->synset as $i => $syns){

				$header_categories = $header_domains = array();

				if ($this->showprogress) echo "<b>".($i+1).". ".$this->header_token['header'][$i]."</b>";
				if ($this->showprogress) echo "<table border='1' cellpadding='5'><tr><th>#</th><th>Synset</th><th>Categorie</th><th>Domini</th><th>Sources</th></tr>";
				
				foreach ($syns->synset_array as $k => $syns_arr){
					
					if ($this->showprogress) echo "<tr><td>".($k+1)."</td>";
					if ($this->showprogress) echo "<td>{".implode(", ", $syns_arr['lemma'])."}</td>";
					if ($this->showprogress) echo "<td>".implode(", ", $syns_arr['category'])."</td>";
					if ($this->showprogress) if ($this->showprogress) echo "<td>".implode(", ", $syns_arr['domain'])."</td>";
					if ($this->showprogress) echo "<td>".implode(", ", $syns_arr['source'])."</td>";
					if ($this->showprogress) echo "</tr>";

					for ($j = 0; $j < count($syns_arr['category']); $j++) if ($syns_arr['category'][$j]) $header_categories[] = $syns_arr['category'][$j];
					for ($j = 0; $j < count($syns_arr['domain']); $j++) if ($syns_arr['domain'][$j]) $header_domains[] = $syns_arr['domain'][$j];

				} 

				if ($this->showprogress) echo "</table>";

				$dist_categs[$i]['name'] = array_unique($header_categories);
				foreach ($dist_categs[$i]['name'] as $k => $row) $dist_categs[$i]['weight'][$k] = 0;
				$this->categs[$i] = array_combine($dist_categs[$i]['name'], $dist_categs[$i]['weight']);
				foreach ($header_categories as $category) if ($this->categs[$i][$category] !== NULL) $this->categs[$i][$category]++;
				array_walk($this->categs[$i], $divide, count($header_categories));

				if ($this->showprogress) echo "Categorie (".count($header_categories).")<br>";
				foreach ($this->categs[$i] as $key => $value) if ($this->showprogress) echo $key.": ".$value." (".($value * count($header_categories))." su ".count($header_categories).")<br>";
				if ($this->showprogress) echo "<br><br>";

				$dist_domains[$i]['name'] = array_unique($header_domains);
				foreach ($dist_domains[$i]['name'] as $k => $row) $dist_domains[$i]['weight'][$k] = 0;
				$this->domains[$i] = array_combine($dist_domains[$i]['name'], $dist_domains[$i]['weight']);
				foreach ($header_domains as $domain) if ($this->domains[$i][$domain] !== NULL) $this->domains[$i][$domain]++;
				array_walk($this->domains[$i], $divide, count($header_domains));
				
				if ($this->showprogress) echo "Domini (".count($header_domains).")<br>";
				foreach ($this->domains[$i] as $key => $value) if ($this->showprogress) echo $key.": ".$value." (".($value * count($header_domains))." su ".count($header_domains).")<br>";
				if ($this->showprogress) echo "<br><br>";

				$table_categories	= array_merge($table_categories, $header_categories);
				$table_domains		= array_merge($table_domains, $header_domains);

			}

			$dist_categs		=
			$dist_domains		= NULL;
			$table_categories	= array_filter($table_categories);
			$table_domains		= array_filter($table_domains);

			$dist_categs['name'] = array_unique($table_categories);
			foreach ($dist_categs['name'] as $k => $row) $dist_categs['weight'][$k] = 0;
			$this->table_categs = array_combine($dist_categs['name'], $dist_categs['weight']);
			foreach ($table_categories as $category) if ($this->table_categs[$category] !== NULL) $this->table_categs[$category]++;
			array_walk($this->table_categs, $divide, count($table_categories));

			if ($this->showprogress) echo "Categorie generali (".count($table_categories).")<br>";
			foreach ($this->table_categs as $key => $value) if ($this->showprogress) echo $key.": ".$value." (".($value * count($table_categories))." su ".count($table_categories).")<br>";
			if ($this->showprogress) echo "<br><br>";

			$dist_domains['name'] = array_unique($table_domains);
			foreach ($dist_domains['name'] as $k => $row) $dist_domains['weight'][$k] = 0;
			$this->table_domains = array_combine($dist_domains['name'], $dist_domains['weight']);
			foreach ($table_domains as $domain) if ($this->table_domains[$domain] !== NULL) $this->table_domains[$domain]++;
			array_walk($this->table_domains, $divide, count($table_domains));

			if ($this->showprogress) echo "Domini generali (".count($table_domains).")<br>";
			foreach ($this->table_domains as $key => $value) if ($this->showprogress) echo $key.": ".$value." (".($value * count($table_domains))." su ".count($table_domains).")<br>";
			if ($this->showprogress) echo "<br><br>";
		}


		// Calculates the weight of every synset based on current header's categories and domains.
		private function calculateWeightBySynset(){
			
			for ($i = 0; $i < count($this->synset); $i++){

				if ($this->showprogress) echo "<b>".($i+1).". ".$this->header_token['header'][$i]."</b>";
				if ($this->showprogress) echo "<table border='1' cellpadding='5'><tr><th>#</th><th>Synset</th><th>Peso categ</th><th>Peso domini</th><th>Peso tot</th></tr>";
				
				for ($k = 0; $k < count($this->synset[$i]->synset_array); $k++){

					$w_categ = $w_dom = array();

					if ($this->showprogress) echo "<tr><td>".($k+1)."</td>";
					if ($this->showprogress) echo "<td>{".implode(", ", $this->synset[$i]->synset_array[$k]['lemma'])."}</td>";

					foreach ($this->synset[$i]->synset_array[$k]['category'] as $key => $category) {
						$w_categ[] = count($this->synset[$i]->synset_array[$k]['category'] > 0) ? $this->categs[$i][$category] : -1;
					}

					foreach ($this->synset[$i]->synset_array[$k]['domain'] as $domain) {
						$w_dom[] = count($this->synset[$i]->synset_array[$k]['domain'] > 0) ? $this->domains[$i][$domain] : -1;
					}

					$categ_weight	= max($w_categ);
					$domain_weight	= max($w_dom);

					if ($this->showprogress) echo "<td>".$categ_weight."</td>";
					if ($this->showprogress) echo "<td>".$domain_weight."</td>";

					$this->synset[$i]->synset_array[$k]['weight'] = ($categ_weight*$this->categ_k + $domain_weight*$this->domain_k) / 2;
					if (!$this->synset[$i]->synset_array[$k]['weight']) $this->synset[$i]->synset_array[$k]['weight'] = "?";

					if ($this->showprogress) echo "<td>".$this->synset[$i]->synset_array[$k]['weight']."</td></tr>";

				}

				if ($this->showprogress) echo "</table>";

			}
		}


		// Calculates the weight of every synset based on the entire headerset's categories and domains.
		private function calculateWeightByTable(){

			$sort = function($a, $b) {
					return $a['weight'] < $b['weight'];
			};
			
			for ($i = 0; $i < count($this->synset); $i++){

				$threshold = 0;

				if ($this->showprogress) echo "<b>".($i+1).". ".$this->header_token['header'][$i]."</b>";
				if ($this->showprogress) echo "<table border='1' cellpadding='5'><tr><th>#</th><th>Synset</th><th>Peso categ</th><th>Peso domini</th><th>Peso tot</th></tr>";
				
				for ($k = 0; $k < count($this->synset[$i]->synset_array); $k++){

					$w_categ = $w_dom = array();

					foreach ($this->synset[$i]->synset_array[$k]['category'] as $key => $category) {
						$w_categ[] = count($this->synset[$i]->synset_array[$k]['category'] > 0) ? $this->table_categs[$category] : -1;
					}

					foreach ($this->synset[$i]->synset_array[$k]['domain'] as $domain) {
						$w_dom[] = count($this->synset[$i]->synset_array[$k]['domain'] > 0) ? $this->table_domains[$domain] : -1;
					}

					$categ_weight	= max($w_categ);
					$domain_weight	= max($w_dom);

					$this->synset[$i]->synset_array[$k]['weight'] = ($categ_weight*$this->categ_k + $domain_weight*$this->domain_k) / 2;
					if (!$this->synset[$i]->synset_array[$k]['weight']) $this->synset[$i]->synset_array[$k]['weight'] = 0;

					$threshold = $this->synset[$i]->synset_array[$k]['weight'] > $threshold ? $this->synset[$i]->synset_array[$k]['weight'] : $threshold;

					if ($this->showprogress) echo "<tr><td>".($k+1)."</td>";
					if ($this->showprogress) echo "<td>{".implode(", ", $this->synset[$i]->synset_array[$k]['lemma'])."}</td>";
					if ($this->showprogress) echo "<td>".$categ_weight."</td>";
					if ($this->showprogress) echo "<td>".$domain_weight."</td>";
					if ($this->showprogress) echo "<td>".$this->synset[$i]->synset_array[$k]['weight']."</td></tr>";

				}

				if ($this->showprogress) echo "</table>";

				$threshold *= $this->threshold_k;

				if ($this->showprogress) echo "Vengono filtrati pesi minori stretti di ".$threshold."<br>";

				foreach ($this->synset[$i]->synset_array as $key => $value) if ($value['weight'] < $threshold) unset($this->synset[$i]->synset_array[$key]);

				usort($this->synset[$i]->synset_array, $sort);
			}
		}


		// Retrieves the synset in destination language.
		private function translate(){

			foreach ($this->synset as $i => $syns){

				/*echo "syns";
				$this->out($syns);*/

				$all_ids = array();

				foreach ($syns->synset_array as $syns_arr) {

					//$this->out($syns_arr);
					$all_ids['lemma'][] = $syns_arr['lemma'];
					$all_ids['id'][] = $syns_arr['id'];
				}

				$this->translation[$i] = new Translation($all_ids, $this->source_lang, $this->dest_lang);
				//$this->translation[$i]->HTMLizeErrlog();
				//$this->out($this->translation[$i]->synset_array);
				//$k = 0;
			}
		}


		// Performs ABSTAT queries and retrieves DBpedia mappings.
		private function retrieveMapping(){
			
			$abstat = new ABSTATRequest();

			foreach ($this->translation as $i => $trans){

				foreach ($trans->synset_array as $k => $syns_arr){

					$array_lemmas	= array();
					$predicates		= array();

					foreach ($syns_arr['lemma'] as $lemma)
						$array_lemmas[] = $lemma;
					
					$abstat->query($array_lemmas);

					$abstatprop = $abstat->getProperties(TRUE);

					if ($abstatprop['schema'] || is_array($abstataprop['schema'])){
						$predicates = array_merge($predicates, $abstatprop['schema']);
						$predicates = array_unique($predicates);
						$this->property[$i][$k] = $predicates;
						//$this->out($this->property);
						
					}
				}
				/*echo "alla fine";
				$this->out($predicates);*/

				//$this->property[$i][$k] = $predicates;

				//$this->out($this->property);

				$k = 0;

				if ($this->showprogress) echo "<b>".($i+1).". ".$this->header_token['header'][$i]."</b>";
				if ($this->showprogress) echo "<table border='1' cellpadding='5'><tr><th>#</th><th>Synset source</th><th>Synset dest</th><th>Properties</th><th>Peso</th></tr>";

				foreach ($this->synset[$i]->synset_array as $key => $value){

					if ($this->showprogress) echo "<tr><td>".($key+1)."</td>";
					if ($this->showprogress) echo "<td>{".utf8_encode(implode(", ", $value['lemma']))."}</td>";
					if ($this->showprogress) echo "<td>{".utf8_encode(implode(", ", $this->translation[$i]->synset_array[$k]['lemma']))."}</td>";
					if ($this->showprogress) echo "<td>{".utf8_encode(implode(", ", $this->property[$i][$k]))."}</td>";
					if ($this->showprogress) echo "<td>".$value['weight']."</td></tr>";

					$k++;

				}

				if ($this->showprogress) echo "</table>";
			}
		}


		// Builds a well formatted output.
		private function buildOutput(){
			
			for ($i = 0; $i < count($this->translation); $i++){

				$this->output[$i]['header'] = $this->header_token['header'][$i];

				$properties = array();

				for ($k = 0; $k < count($this->translation[$i]->synset_array); $k++){

					$addweight = FALSE;
					
					foreach ($this->property[$i][$k] as $property){
						
						if (!in_array($property, $properties)) {
							$properties[] = $property;
							$this->output[$i]['predicate'][$k]['properties'][] = $property;
							$addweight = TRUE;
						}else	$addweight = FALSE;
					}

					//$properties = array_merge($properties, $this->property[$i][$k]);
					
					//$this->output[$i][$k]['predicates'] = implode(", ", $this->property[$i][$k]);
					if ($addweight) $this->output[$i]['predicate'][$k]['weight'] = $this->synset[$i]->synset_array[$k]['weight'] ? $this->synset[$i]->synset_array[$k]['weight'] : "?";

				}
			}

			if ($this->showprogress) echo "<hr>";
			if ($this->showprogress) $this->out($this->output);
			$this->output = json_encode($this->output);
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


		public function getOutput(){
			return $this->output;
		}

	} // End class.

?>
