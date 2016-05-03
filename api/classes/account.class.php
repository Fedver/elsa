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
		public $user_id, $user_email, $user_key;

		// Output attributes.
		public $message, $errlog, $status;


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//										CONSTRUCTOR										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Requires mysqli resource.
		// Returns TRUE if is successful, FALSE otherwise.
		public function Account($mysqli, $user_id){

			$this->message = $this->errlog = NULL;
			
			if (!isset($mysqli) || !$mysqli){
				$this->message	= "Error code 000: connection resource is not set. [Account.Account]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}elseif(!isset($user_id) || !$user_id){
				$this->message	= "Error code 001: missing parameters (user_id). [Account.Account]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}else{
				$this->conn = $mysqli;
				$this->api_key = array();
				$ok = $this->getUserData();
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

			$sql = "SELECT email, key FROM users WHERE id LIKE ? LIMIT 1";
			$stmt = $this->conn->prepare($sql);
			if (!$stmt) {
				$this->message	= "Error code 002: statement is not valid. [Account.getUserData]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
			}else{
				$stmt->bind_param("i", $this->conn->escape_string($this->user_id));
				$stmt->bind_result($username, $key);
				$stmt->execute();
				$stmt->fetch();
				$stmt->close();
				
				if ($id && $username){
					$this->user_email	= $username;
					$this->user_key		= $key;
					$this->message		= "User data retrieved successfully. [Account.getUserData]";
					$this->errlog		.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status		= TRUE;
					return TRUE;
				}else{
					$this->message	= "Error code 006: user data not found. [Account.getUserData]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
					return FALSE;
				}
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


	} // End class.

?>
