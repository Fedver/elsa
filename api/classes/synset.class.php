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
		public $synset_source, $synset_array, $categ_array, $dom_array;

		// Output attributes.
		public $message, $errlog, $status;


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
				$this->synset_sounce = NULL;
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
			$response = $bn->getSynsetByWord($this->word, "IT");

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


		// Returns an array containing synsets, categories and domains of $this->word.
		public function getSynsetArray(){
			
			$bn = new BabelNetRequest();

			foreach ($this->synset_source['sid'] as $i => $synonym_id){
				
				$response = $bn->getSynsetByID($synonym_id, $this->dest_lang);

				foreach ($response['senses'] as $row)
					$this->synset_array[$i][] = $row['lemma'];

				foreach ($response['categories'] as $row)
					$this->categ_array[$i][] = $row['category'];

				foreach ($response['domains'] as $key => $value){
					$this->dom_array[$i]['domain'][] = $key;
					$this->dom_array[$i]['weight'][] = $value;
				}

			}

			return array($this->synset_array, $this->categ_array, $this->dom_array);

		}


	} // End class.

?>
