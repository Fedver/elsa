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

	
	class Test {
		
		// Internal service attributes.
		private $conn;

		// Public attributes.
		public $user_id;
		public $user_email;

		// Output attributes.
		public $message, $errlog, $status;


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//										CONSTRUCTOR										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Requires mysqli resource.
		// Returns TRUE if is successful, FALSE otherwise.
		public function Test($mysqli, $type){

			$this->message = $this->errlog = NULL;
			
			if (!isset($mysqli) || !$mysqli){
				$this->message	= "Error code 000: connection resource is not set. [Test.Test]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}
			elseif (!isset($type) || !$type){
				$this->message	= "Error code 000: connection resource is not set. [Test.Test]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}else{
				$this->conn = $mysqli;
				$this->message	= "Class Registration instanced successfully. [Test.Test]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= TRUE;
			}
		}


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//									PRIVATE METHODS										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		private function parseResult($result_json){
			
			
			$result = json_decode(stripslashes($result_json), TRUE);
			//$this->out($result);

			$parsed = NULL;

			foreach ($result as $element){
				
				foreach ($element['predicate'] as $property){
					$parsed .= implode("|", $property['properties']);
					$parsed .= "|";
				}

				$parsed = substr($parsed, 0, -1);
				$parsed .= ";";
			}

			$parsed = substr($parsed, 0, -1);
			$parsed = str_replace("DTP ", "", $parsed);
			$parsed = str_replace("OP ", "", $parsed);
			return $parsed;
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


		public function getAllTests(){

			$array_result = array(
								"id"  => array(),
								"header"  => array(),
								"mapping"  => array(),
								"lingua"  => array(),
								"titolo"  => array()
							);
			
			$sql = "SELECT id, headers, gs_mapping, lang, descr FROM gold_standards";
			$stmt = $this->conn->prepare($sql);
			if (!$stmt) {
				$this->message	= "Error code 002: statement is not valid. [Test.getAllTests]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
			}else{
				$stmt->bind_result($id, $headers, $mapping, $lang, $descr);
				$stmt->execute();
				while ($stmt->fetch()) {
							$array_result['id'][] 	 = $id;
							$array_result['header'][] 	 = $headers;
							$array_result['mapping'][] 	 = $mapping;
							$array_result['lingua'][] 	 = $lang;
							$array_result['titolo'][] 	 = $descr;
				}	
				$stmt->close();
				
				return $array_result;
			}

		}
		
		
		public function saveTestResults($header_id, $api_mapping, $where, $type){
			
			$sql = "INSERT INTO test VALUES (NULL, ?, ?, ?, NOW(), ?)";
			$stmt = $this->conn->prepare($sql);
			if (!$stmt) {
				$this->message	= "Error code 002: statement is not valid. ".$this->conn->error.$sql." [Test.saveTestResults]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
			}else{
				$stmt->bind_param("isss", $this->conn->escape_string($header_id), $this->conn->escape_string($api_mapping), $this->conn->escape_string($where), $this->conn->escape_string($type));
				$stmt->execute();

				if ($this->conn->affected_rows > 0){
					$this->message	= "Test successful! [Test.saveTestResults]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= TRUE;
					$stmt->close();
					return TRUE;
				}else{
					$this->message	= "Error code 004: no rows affected. ".$this->conn->error.$sql." [Test.saveTestResults]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
					$stmt->close();
					return FALSE;
				}
			}
		}


		public function getAllTestsDone(){
			
			$array_result = array(
								"id"  => array(),
								"header"  => array(),
								"mapping"  => array(),
								"lingua"  => array(),
								"titolo"  => array(),
								"result"	=> array(),
								"date"	=> array(),
								"where"	=> array(),
								"type"	=> array(),
							);
			
			$sql = "SELECT gs.id, gs.headers, gs.gs_mapping, gs.lang, gs.descr, t.api_mapping, t.date, t.where, t.type FROM gold_standards AS gs
					INNER JOIN test as t on gs.id = t.id_header
					ORDER BY t.date DESC";
			$stmt = $this->conn->prepare($sql);
			if (!$stmt) {
				$this->message	= "Error code 002: statement is not valid. [Test.getAllTests]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
			}else{
				$stmt->bind_result($id, $headers, $mapping, $lang, $descr, $result, $date, $where, $type);
				$stmt->execute();
				while ($stmt->fetch()) {
							$array_result['id'][] 	 = $id;
							$array_result['header'][] 	 = $headers;
							$array_result['mapping'][] 	 = $mapping;
							$array_result['lingua'][] 	 = $lang;
							$array_result['titolo'][] 	 = $descr;
							$array_result['result'][] 	 = $this->parseResult($result);
							$array_result['date'][] 	 = $date;
							$array_result['where'][] 	 = $where;
							$array_result['type'][] 	 = $type;
				}	
				$stmt->close();
				
				return $array_result;
			}

		}

	} // End class.

?>