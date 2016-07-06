<?php
		
	/* Class representing a generic synset.
	Error coding:		001 missing parameters
						018 no synset found
						


	*/

	// Includes.
	require_once("babelnetrequest.class.php");
	require_once("bingtranslaterequest.class.php");

	
	class Translation {
		
		// Internal service attributes.
		private $word, $source_lang, $dest_lang;

		// Public attributes.
		public $synset_source, $synset_array, $categ_array, $dom_array, $sources_array, $dist_domains, $dist_categs;

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
		public function Translation ($array_id, $source_lang, $dest_lang){
			
			if (is_array($array_id) && $source_lang && $dest_lang){
				$this->source_lang		= $source_lang;
				$this->dest_lang		= $dest_lang;
				$this->synset_source	= $array_id;
				$this->getTranslationArray();
				$this->message			= "Class Translation instanced successfully. [Translation.Translation]";
				$this->errlog			.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status			= TRUE;
			}else{
				$this->message	= "Error code 001: missing parameters (array_id, source_lang, dest_lang). [Translation.Translation]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
			}
		}


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//									PRIVATE METHODS										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Returns an array containing synsets, categories and domains of $this->word.
		private function getTranslationArray(){
			
			$bn = new BabelNetRequest();
			$mt = new BingTranslateRequest();
			$k	= 0;

			foreach ($this->synset_source['id'] as $synonym_id){
				
				$response = $bn->getSynsetByID($synonym_id, $this->dest_lang);

				if ($synonym_id != "void"){

					foreach ($response['senses'] as $row){
						$this->synset_array[$k]['lemma'][]	= str_replace("_", " ", strtolower($row['lemma']));
						$this->synset_array[$k]['source'][] = $row['source'];
					}
				}else{
					//$this->out($this->synset_source['lemma']);
					$this->synset_array[$k]['lemma'] = $mt->translateMany($this->synset_source['lemma'][$k], strtolower($this->source_lang), strtolower($this->dest_lang));
					$this->synset_array[$k]['source'][] = $mt->service_name;
				}

				$this->synset_array[$k]['lemma'] = array_unique($this->synset_array[$k]['lemma']);

				$k++;
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


	} // End class.

?>
