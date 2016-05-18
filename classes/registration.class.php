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


	*/

	
	class Registration {
		
		// Internal service attributes.
		private $conn;

		// Public attributes.
		public $user_id, $user_email;

		// Output attributes.
		public $message, $errlog, $status;


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//										CONSTRUCTOR										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Requires mysqli resource.
		// Returns TRUE if is successful, FALSE otherwise.
		public function Registration($mysqli, $user_id=-1){

			$this->message = $this->errlog = NULL;
			
			if (!isset($mysqli) || !$mysqli){
				$this->message	= "Error code 000: connection resource is not set. [Registration.Registration]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}else{
				if ($user_id != -1) $this->user_id = $user_id;
				$this->conn = $mysqli;
				$this->message	= "Class Registration instanced successfully. [Registration.Registration]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= TRUE;
			}
		}


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//									PRIVATE METHODS										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Retrieve an user's hashed password.
		// Returns a string containing the password, FALSE if doesn't find the requested user_id.
		private function getUserPassword($email=""){
			
			$email = $email != "" ? $email : $this->user_email;

			if ($email){

				$sql = "SELECT passwd FROM users WHERE email = ? LIMIT 1";
				$stmt = $this->conn->prepare($sql);
				if (!$stmt) {
					$this->message	= "Error code 002: statement is not valid. [Registration.verifyEmail]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
				}else{
					$stmt->bind_param("i", $this->conn->escape_string($email));
					$stmt->bind_result($value);
					$stmt->execute();
					$stmt->fetch();
					$stmt->close();
				
					if ($value) return $value;
					else{
						$this->message	= "Error code 005: password not found. [Registration.getUserPassword]";
						$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
						$this->status	= FALSE;
						return FALSE;
					}
				}
			}else{
				$this->message	= "Error code 001: missing parameters (user_id). [Registration.getUserPassword]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}
		}
		

		// Retrieve id and email address of a specified user and sets them as attributes. Used as final check during registration.
		// Returns TRUE if the address is free, FALSE otherwise.
		private function getUserData($email){

			$sql = "SELECT id, email FROM users WHERE email LIKE ? LIMIT 1";
			$stmt = $this->conn->prepare($sql);
			if (!$stmt) {
				$this->message	= "Error code 002: statement is not valid. [Registration.getUserData]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
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
					$this->message	= "Error code 006: user data not found. [Registration.getUserData]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
					return FALSE;
				}
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

		// Verify if an email address is not already taken.
		// Returns TRUE if the address is free, FALSE otherwise.
		public function verifyEmail($email){
			
			if ($email){

				$sql = "SELECT email FROM users WHERE email LIKE ? LIMIT 1";
				$stmt = $this->conn->prepare($sql);
				if (!$stmt) {
					$this->message	= "Error code 002: statement is not valid. [Registration.verifyEmail]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
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
				$this->message	= "Error code 001: missing parameters (email).[Registration.verifyEmail]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
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
						$this->message	= "Error code 002: statement is not valid. [Registration.newUser]";
						$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
						$this->status	= FALSE;
					}else{
						$stmt->bind_param("sss", $this->conn->escape_string($email), $this->conn->escape_string($hash_password), $this->conn->escape_string($hash_key));
						$stmt->execute();

						if ($this->conn->affected_rows > 0){
							$this->message	= "You are successfully signed up! [Registration.newUser]";
							$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
							$this->status	= TRUE;
							$stmt->close();
							return TRUE;
						}else{
							$this->message	= "Error code 004: no rows affected. [Registration.newUser]";
							$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
							$this->status	= FALSE;
							$stmt->close();
							return FALSE;
						}
					}
				}else{
					$this->message	= "Error code 003: email address already taken. [Registration.newUser]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
					return FALSE;
				}
			}else{
				$this->message	= "Error code 001: missing parameters (email and/or password). [Registration.newUser]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
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
							$this->message	= "Login successful! [Registration.doLogin]";
							$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
							$this->status	= TRUE;
							return TRUE;
						}
					}else{
						$this->message	= "Error code 008: wrong password. [Registration.doLogin]";
						$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
						$this->status	= FALSE;
						return FALSE;
					}
				}else{
					$this->message	= "Error code 007: wrong username. [Registration.doLogin]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
					return FALSE;
				}
			}else{
				$this->message	= "Error code 001: missing parameters (email and/or password). [Registration.doLogin]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}
		}

	} // End class.

?>