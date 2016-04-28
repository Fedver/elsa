<?php
		
	/* Class for account management.
	Error coding:		000 connection resource is not set
						001 missing parameters
						002 statement is not valid
						003 email address already taken
						004 no rows affected
						005 password not found
						006 user data not found
						007 wrong username
						008 wrong password


	*/

	
	class Account {
		
		// Internal service attributes.
		private $conn;

		// Public attributes.
		public $user_id, $user_email;

		// Output attributes.
		public $message, $errlog;


		// Constructor. Needs mysqli resource.
		public function Account($mysqli, $user_id=-1){

			$this->message = $this->errlog = NULL;
			
			if (!isset($mysqli)){
				$this->message = "Error code 000: connection resource is not set. [Account.Account]";
				$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				return FALSE;
			}else{
				if ($user_id != -1) $this->user_id = $user_id;
				$this->conn = $mysqli;
				$this->message = "Class Account instanced successfully.";
				$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				return TRUE;
			}

		}


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//									PRIVATE METHODS										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////

		private function setAPIKey(){
			


		}


		private function getUserPassword($email=""){
			
			$email = $email != "" ? $email : $this->user_email;

			if ($email){

				$sql = "SELECT passwd FROM users WHERE email = ? LIMIT 1";
				$stmt = $this->conn->prepare($sql);
				if (!$stmt) {
					$this->message	= "Error code 002: statement is not valid. [Account.verifyEmail]";
					$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				}else{
					$stmt->bind_param("i", $this->conn->escape_string($email));
					$stmt->bind_result($value);
					$stmt->execute();
					$stmt->fetch();
					$stmt->close();
				
					if ($value) return $value;
					else{
						$this->message = "Error code 005: password not found. [Account.getUserPassword]";
						$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
						return FALSE;
					}
				}
			}else{
				$this->message = "Error code 001: missing parameters (user_id). [Account.getUserPassword]";
				$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				return FALSE;
			}
		}
		

		// Retrieve id and email address of a specified user and sets they as attributes.
		// Returns TRUE if the address is free, FALSE otherwise.
		private function getUserData($email){

			$sql = "SELECT id, email FROM users WHERE email LIKE ? LIMIT 1";
			$stmt = $this->conn->prepare($sql);
			if (!$stmt) {
				$this->message	= "Error code 002: statement is not valid. [Account.getUserData]";
				$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
			}else{
				$stmt->bind_param("s", $this->conn->escape_string($email));
				$stmt->bind_result($id, $username);
				$stmt->execute();
				$stmt->fetch();
				$stmt->close();
				
				if ($id && $username){
					$this->user_id = $id;
					$this->user_email = $username;
					return TRUE;
				}else{
					$this->message = "Error code 006: user data not found. [Account.getUserData]";
					$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					return FALSE;
				}
				
			}
		}


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//									PUBLIC METHODS										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////

		// Verify if an email address is not already taken.
		// Returns TRUE if the address is free, FALSE otherwise.
		public function verifyEmail($email){
			
			if ($email){

				$sql = "SELECT email FROM users WHERE email LIKE ? LIMIT 1";
				$stmt = $this->conn->prepare($sql);
				if (!$stmt) {
					$this->message	= "Error code 002: statement is not valid. [Account.verifyEmail]";
					$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				}else{
					$stmt->bind_param("s", $this->conn->escape_string($email));
					$stmt->bind_result($value);
					$stmt->execute();
					$stmt->fetch();
					$stmt->close();
				
					if ($value) return FALSE;
					else return TRUE;
				}
			}else{
				$this->message = "Error code 001: missing parameters (email).[Account.verifyEmail]";
				$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				return FALSE;
			}
		}


		// Register a new user.
		// Returns TRUE if the registration is successful, FALSE otherwise.
		public function newUser($email, $pass){
			
			if ($email && $pass){
				
				if ($this->verifyEmail($email)){

					$hash_password = sha1($pass);
					$hash_key = sha1($hash_password.$email.time().mt_rand());
					
					$sql = "INSERT INTO users VALUES (NULL, ?, ?, NOW(), ?)";
					$stmt = $this->conn->prepare($sql);
					if (!$stmt) {
						$this->message	= "Error code 002: statement is not valid. [Account.newUser]";
						$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					}else{
						$stmt->bind_param("sss", $this->conn->escape_string($email), $this->conn->escape_string($hash_password), $this->conn->escape_string($hash_key));
						$stmt->execute();

						if ($this->conn->affected_rows > 0){
							$this->message = "You are successfully signed up!";
							$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
							$stmt->close();
							return TRUE;
						}else{
							$this->message = "Error code 004: no rows affected. [Account.newUser]".$stmt->error;
							$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
							$stmt->close();
							return FALSE;
						}
					}
				}else{
					$this->message = "Error code 003: email address already taken. [Account.newUser]";
					$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					return FALSE;
				}

			}else{
				$this->message = "Error code 001: missing parameters (email and/or password). [Account.newUser]";
				$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				return FALSE;
			}
		}


		// Try to login an user.
		// Return TRUE if login is successful, FALSE otherwise.
		public function doLogin($email, $pass){
			
			if ($email && $pass){

				if (!$this->verifyEmail($email)){
					
					$hash_password = $this->getUserPassword($email);
				
					if ($hash_password == sha1($pass)){
						$ok = $this->getUserData($email);
						if ($ok){
							$this->message = "Login successful!";
							$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
							return TRUE;
						}
					}else{
						$this->message = "Error code 008: wrong password.";
						$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
						return FALSE;
					}
				}else{
					$this->message = "Error code 007: wrong username.";
					$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					return FALSE;
				}
			}else{
				$this->message = "Error code 001: missing parameters (email and/or password).";
				$this->errlog .= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				return FALSE;
			}

		}

	} // End class.

?>