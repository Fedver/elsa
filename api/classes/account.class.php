<?php
	
	class Account{

		/* Class for user account management.
		Error coding:		000 connection resource is not set
							001 missing parameters
							002 statement is not valid
							006 user data not found


		*/
		
		// Internal service attributes.
		private $conn;
		private $api_key;

		// Public attributes.
		public $user_id, $user_email, $user_key, $user_headers;

		// Output attributes.
		public $message, $errlog, $status;


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//										CONSTRUCTOR										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Requires mysqli resource.
		// Returns TRUE if is successful, FALSE otherwise.
		public function Account($mysqli, $key){

			$this->message = $this->errlog = NULL;
			
			if (!isset($mysqli) || !$mysqli){
				$this->message	= "Error code 000: connection resource is not set. [Account.Account]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}elseif(!isset($key) || !$key){
				$this->message	= "Error code 001: missing parameters (key). [Account.Account]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}else{
				$this->conn = $mysqli;
				$this->user_key = $key;
				$ok = $this->verifyKey();
				if ($ok) $ok *= $this->getUserData();
				if ($ok){
					$this->message	= "Class Account instanced successfully. [Account.Account]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= TRUE;
				}else{
					return FALSE;
				}
			}
		}


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//									PRIVATE METHODS										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Retrieve email address and encryption key of a specified user and sets them as attributes.
		// Returns TRUE if data is found, FALSE otherwise.
		private function getUserData(){

			$sql = "SELECT u.id, u.email, u.headers FROM users AS u WHERE u.key LIKE ?";
			$stmt = $this->conn->prepare($sql);
			if (!$stmt) {
				$this->message	= "Error code 002: statement is not valid. [Account.getUserData]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
			}else{
				$stmt->bind_param("s", $this->conn->escape_string($this->user_key));
				$stmt->bind_result($id, $email, $headers);
				$stmt->execute();
				$stmt->fetch();
				$stmt->close();
				
				if ($id && $email){
					$this->user_email	= $email;
					$this->user_id		= $id;
					$this->user_headers = $headers;
					$this->message		= "User data retrieved successfully. [Account.getUserData]";
					$this->errlog		.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status		= TRUE;
					return TRUE;
				}else{
					$this->message	= "Error code 006: user data not found. a".$id.$email.$headers.$this->conn->errno." [Account.getUserData]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
					return FALSE;
				}
			}
		}


		private function verifyKey(){

				$sql = "SELECT u.key FROM users AS u WHERE u.key LIKE ? LIMIT 1";
				$stmt = $this->conn->prepare($sql);
				if (!$stmt) {
					$this->message	= "Error code 002: statement is not valid. [Registration.verifyKey]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
				}else{
					$stmt->bind_param("s", $this->conn->escape_string($this->user_key));
					$stmt->bind_result($value);
					$stmt->execute();
					$stmt->fetch();
					$stmt->close();

					if ($value == $this->user_key) return TRUE;
					else return FALSE;
				}

		}


		private function generateAPIKey(){
			
			$api_key = sha1($this->user_id.$this->user_email.$this->user_key.mt_rand().time());
			return $api_key;

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


		public function addHeaders($num){

			if (is_numeric($num)){

				$sql = "UPDATE users SET headers = headers + ? WHERE id = ?";
				$stmt = $this->conn->prepare($sql);
				if (!$stmt) {
					$this->message	= "Error code 002: statement is not valid. [Registration.addHeaders]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
				}else{
					$stmt->bind_param("ii", $this->conn->escape_string($num), $this->user_id);
					$stmt->execute();
					$stmt->close();

					if ($this->conn->affected_rows > 0){
						$this->message	= "Header count update successful [Account.addHeaders]";
						$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
						$this->status	= TRUE;
						$this->user_headers += $num;
						return TRUE;
					}else{
						$this->message	= "Error code 00X: header count not updated correctly [Account.addHeaders]";
						$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
						$this->status	= FALSE;
						return FALSE;
					}
				}
			}else{
				$this->message	= "Error code 00X: incorrect value (num). [Account.addHeaders]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}
		}


	} // End class.

?>
