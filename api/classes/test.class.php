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
		private $conn, $init_time, $elab_time;

		// Public attributes.
		public $full, $partial;


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
            $this->full['precision']    =
            $this->full['recall']       =
            $this->full['count']        =
            $this->partial['precision'] =
            $this->partial['recall']    =
            $this->partial['count']     = 0;
			
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
				$this->message	= "Class Test instanced successfully. [Test.Test]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= TRUE;
			}
		}


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//									PRIVATE METHODS										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		private function parseResult($result_json, $result_type){
			
			
			$result = json_decode(stripslashes($result_json), TRUE);

			$parsed = NULL;

            switch ($result_type) {
                case "full":        foreach ($result as $element) {

                                        foreach ($element['predicate'] as $property) {
                                            $parsed .= implode("|", $property['properties']);
                                            $parsed .= "|";
                                        }

                                        $parsed = substr($parsed, 0, -1);
                                        $parsed .= ";";
                                    }

                                    $parsed = substr($parsed, 0, -1);
                                    $parsed = str_replace("DTP ", "", $parsed);
                                    $parsed = str_replace("OP ", "", $parsed);
                                    break;

                case "partial":     foreach ($result as $element) {

                                        $parsed .= implode("|", $element['properties']);
                                        $parsed .= ";";
                                    }

                                    $parsed = substr($parsed, 0, -1);
                                    $parsed = str_replace("DTP ", "", $parsed);
                                    $parsed = str_replace("OP ", "", $parsed);
                                    break;
            }

            while (strstr($parsed, "||") != FALSE) $parsed = str_replace("||", "|", $parsed);
            while (strstr($parsed, "|;") != FALSE) $parsed = str_replace("|;", ";", $parsed);
            while (strstr($parsed, ";|") != FALSE) $parsed = str_replace(";|", ";", $parsed);
			return $parsed;
		}


		private function FMeasure($precision, $recall){

		    return (2 * $precision * $recall) / ($precision + $recall);

        }


        private function parseWeightCSV($csv, $response){

            $csv_output = $res_output = array();
            $headers = explode(";", $csv);
            $results = explode(";", $response);

            for ($i = 0; $i < count($headers); $i++){

                $properties = explode("|", $headers[$i]);

                for ($k = 0; $k < count($properties); $k++){

                    $property = substr($properties[$k], 0, -2);
                    $csv_output[$i][$k]['property'] = $property;
                    $csv_output[$i][$k]['weight'] = substr($properties[$k], -1);

                }
            }

            for ($i = 0; $i < count($results); $i++){

                $properties = explode("|", $results[$i]);

                for ($k = 0; $k < count($properties); $k++){

                    $res_output[$i][$k]['property'] = $properties[$k];

                    for ($j = 0; $j < count($properties); $j++)
                        if ($csv_output[$i][$j]['property'] == $res_output[$i][$k]['property']) $res_output[$i][$k]['weight'] = $csv_output[$i][$j]['weight'];

                    if (!$res_output[$i][$k]['weight']) $res_output[$i][$k]['weight'] = 0;
                }
            }

            return array($csv_output, $res_output);

        }


        private function DCG($rel_prop){

            $dcg = array();

            for ($i = 0; $i < count($rel_prop); $i++){

                $dcg[$i] = $rel_prop[$i][0]['weight'];

                for ($k = 1; $k < count($rel_prop[$i]); $k++)
                    $dcg[$i] += $rel_prop[$i][$k]['weight'] / log(($k+1), 2);
            }

            return $dcg;

        }


        private function arrayMean($array){
            return (array_sum($array) /count($array));
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

                $array_result = array_filter($array_result);
				
				return $array_result;
			}

		}
		
		
		public function saveTestResults($header_id, $api_mapping, $where, $type){

			$sql = "INSERT INTO test VALUES (NULL, ?, ?, ?, NOW(), ?, ?)";
			$stmt = $this->conn->prepare($sql);
			if (!$stmt) {
				$this->message	= "Error code 002: statement is not valid. ".$this->conn->error.$sql." [Test.saveTestResults]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
			}else{
				$stmt->bind_param("isssi", $this->conn->escape_string($header_id), $this->conn->escape_string($api_mapping), $this->conn->escape_string($where), $this->conn->escape_string($type), $this->elab_time);
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
                                "elab_time" => array(),
							);
			
			$sql = "SELECT gs.id, gs.headers, gs.gs_mapping, gs.lang, gs.descr, t.api_mapping, t.date, t.where, t.type, t.elab_time FROM gold_standards AS gs
					INNER JOIN test as t on gs.id = t.id_header
					ORDER BY t.date DESC";
			$stmt = $this->conn->prepare($sql);
			if (!$stmt) {
				$this->message	= "Error code 002: statement is not valid. [Test.getAllTestsDone]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
			}else{
				$stmt->bind_result($id, $headers, $mapping, $lang, $descr, $result, $date, $where, $type, $elab_time);
				$stmt->execute();
				while ($stmt->fetch()) {
							$array_result['id'][] 	 = $id;
							$array_result['header'][] 	 = $headers;
							$array_result['mapping'][] 	 = $mapping;
							$array_result['lingua'][] 	 = $lang;
							$array_result['titolo'][] 	 = $descr;
							$array_result['result'][] 	 = $this->parseResult($result, $type);
							$array_result['date'][] 	 = $date;
							$array_result['where'][] 	 = $where;
							$array_result['type'][] 	 = $type;
                            $array_result['elab_time'][] = $elab_time;
				}	
				$stmt->close();
				
				return $array_result;
			}
		}


		public function calcIndicators($mapping, $results, $type){

		    $map_noweights = $this->removeWeights($mapping);

            $count_mapping = 0;
            $array_mapping = explode(";", $map_noweights);
            for ($i = 0; $i < count($array_mapping); $i++){
                if ($array_mapping[$i]) {
                    $array_mapping[$i] = array_filter(explode("|", $array_mapping[$i]));
                    $count_mapping += count($array_mapping[$i]);
                }
            }

            $count_results = 0;
            $array_results = explode(";", $results);
            for ($i = 0; $i < count($array_results); $i++){
                if ($array_results[$i]) {
                    $array_results[$i] = array_filter(explode("|", $array_results[$i]));
                    $count_results += count($array_results[$i]);
                }
            }

            $count_found = 0;
            for ($i = 0; $i < count($array_mapping); $i++){
                for ($k = 0; $k < count($array_mapping[$i]); $k++) {
                    if (in_array($array_mapping[$i][$k], $array_results[$i])) {
                        $count_found += 1;
                    }
                }
            }

            $precision = $count_found / $count_results;
            $recall = $count_found / $count_mapping;


            list($map_arr, $res_arr) = $this->parseWeightCSV($mapping, $results);

            $dcg = $this->arrayMean($this->DCG($res_arr));
            $idcg = $this->arrayMean($this->DCG($map_arr));

            $ndcg = $dcg / $idcg;

            switch ($type) {
                case "full":        $this->full['precision'] += $precision;
                                    $this->full['recall'] += $recall;
                                    $this->full['count'] ++;
                                    $this->full['ndcg'] += $ndcg;
                                    break;

                case "partial":     $this->partial['precision'] += $precision;
                                    $this->partial['recall'] += $recall;
                                    $this->partial['count'] ++;
                                    $this->partial['ndcg'] += $ndcg;
                                    break;
            }

            $fmeasure = $this->FMeasure($precision, $recall);

            return array($precision, $recall, $fmeasure, $ndcg);

        }


        public function getFinalEvaluation($type){

            switch ($type) {
                case "full":        $precision = $this->full['precision'] /= $this->full['count'];
                                    $recall = $this->full['recall'] /= $this->full['count'];
                                    $ndcg = $this->full['ndcg'] /= $this->full['count'];
                                    break;

                case "partial":     $precision = $this->partial['precision'] /= $this->partial['count'];
                                    $recall = $this->partial['recall'] /= $this->partial['count'];
                                    $ndcg = $this->partial['ndcg'] /= $this->partial['count'];
                                    break;
            }

            $fmeasure = $this->FMeasure($precision, $recall);

            return array($precision, $recall, $fmeasure, $ndcg);
        }


        public function removeWeights($csv){

            $headers = explode(";", $csv);

            for ($i = 0; $i < count($headers); $i++){

                $properties = explode("|", $headers[$i]);

                for ($k = 0; $k < count($properties); $k++)
                    $properties[$k] = substr($properties[$k], 0, -2);

                $headers[$i] = implode("|", $properties);
            }

            return implode(";", $headers);
        }


        public function setInitialTime(){

            $this->init_time = time();

        }


        public function calcTime(){

            $this->elab_time = time() - $this->init_time;

        }

	} // End class.

?>