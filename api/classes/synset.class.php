<?php
		
	/* Class representing a generic synset.
	Error coding:		001 missing parameters
						018 no synset found
						


	*/

	// Includes.
	require_once("babelnetrequest.class.php");

	
	class Synset {
		
		// Internal service attributes.
		private $word, $source_lang, $dest_lang;

		// Public attributes.
		public $synset_source, $synset_array, $categ_array, $dom_array, $sources_array;

		// Output attributes.
		public $message, $errlog, $status;

		// Parameters and configuration attributes.
		public $filter_results	= TRUE;
		public $filter			= array("CONCEPT");


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//										CONSTRUCTOR										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Requires a single word which will be the base of the synset, a source language and a destination language.
		public function Synset($word, $source_lang, $dest_lang){
			
			if ($word && $source_lang && $dest_lang){
				$this->word				= $word;
				$this->source_lang		= $source_lang;
				$this->dest_lang		= $dest_lang;
				$this->synset_source	= NULL;
				$this->getSynset();
				if (is_array($this->synset_source)){
					$this->message			= "Class Synset (".$word.") instanced successfully. [Synset.Synset]";
					$this->errlog			.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status			= TRUE;
				}else{
					$this->message			= "Error code 018: synset not found (".$word."). [Synset.Synset]";
					$this->errlog			.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status			= FALSE;
				}
			}else{
				$this->message	= "Error code 001: missing parameters (word, source_lang, dest_lang). [Synset.Synset]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
			}
		}


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//									PRIVATE METHODS										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Verifies the presence of the word stored in $this->word (see construct) in the wordnet.
		// Returns void and populates the $this->synset_source array, which contains synsets IDs in the wordnet.
		private function getSynset(){
			
			$bn = new BabelNetRequest();
			$response = $bn->getSynsetByWord($this->word, $this->source_lang);

			foreach ($response as $row){
				
				$this->synset_source['sid'][] = $row['id'];
				$this->synset_source['source'][] = $row['source'];

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


		public function out($var){
			echo "<br><br><pre>";
			print_r($var);
			echo "</pre><br><br>";
		}


		// Returns an array containing synsets, categories and domains of $this->word.
		public function getSynsetArray(){
			
			$bn = new BabelNetRequest();
			$k	= 0;

			foreach ($this->synset_source['sid'] as $synonym_id){
				
				$response = $bn->getSynsetByID($synonym_id, $this->dest_lang);

				if ($this->filter_results && in_array($response['synsetType'], $this->filter)){

					foreach ($response['senses'] as $row){
						$this->synset_array[$k]['lemma'][]	= str_replace("_", " ", strtolower($row['lemma']));
						$this->synset_array[$k]['source'][] = $row['source'];
						$this->synset_array[$k]['id']		= $row['synsetID']['id'];
					}

					$this->synset_array[$k]['lemma'] = array_unique($this->synset_array[$k]['lemma']);

					foreach ($response['categories'] as $row)
						$this->synset_array[$k]['category'][] = str_replace("_", " ", strtolower($row['category']));

					foreach ($response['domains'] as $key => $value){
						$this->synset_array[$k]['domain'][] = str_replace("_", " ", strtolower($key));
						$this->synset_array[$k]['weight'][] = $value;
					}

					$k++;
				}
			}
		}


	} // End class.

?>
