<?php 
class DbHandler {
	private $conn;
    // private $token;
	public function __construct($DB_con) {
		$this->conn = $DB_con;
	}
	public function createUser($name, $email, $password, $repassword, $utoken) {
   	    // $error = array();
		$error = '';
		$required_fields = array($name, $email, $password, $repassword);
		$fields = array_map('trim', $required_fields);
		if (in_array(null, $fields)) {
			$error =  'Fields marked with an asterisk are required';
		}
		else if(!$this->valid_token($utoken)){
			$error = "Invalid Token...!!!";
		}
		else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$error = 'Please enter a valid email address !';
		}
		else if(strlen($password) < 6){
			$error = "Password must be atleast 6 characters"; 
		}
		else if($password !== $repassword){
			$error = "Password do n\' t match!!";
		}
		else{
			$name = escape($name);
			$email = escape($email);
			$password_hash = escape($password);
        	// First check if user already existed in db
			if (!$this->isUserExists($name, $email)) {
	            // Generating password hash
				$password_hash = password_hash($password, PASSWORD_DEFAULT, ['cost'=>12]);
	            // insert query
				$stmt = $this->conn->prepare("INSERT INTO users(name, email, password_hash, status) values(:name, :email, :password_hash, 1)");
            	//$stmt->bind_param("ssss", $name, $email, $password_hash);
				$result = $stmt->execute(array(':name' => $name,':email' => $email,':password_hash' => $password_hash));;
            	//$stmt->close();
            	// Check for successful insertion
				if ($result) {
                // User successfully inserted
					return 6;
				} else {
                // Failed to create user
					$error = "Failed to create user";
				}
			} else {
            // User with same email already existed in the db
				$error = "User or Email already taken";
			}
		}	
		return $error;
	}
	public function login($name, $email, $password, $utoken)
	{
		$error = '';
		$required_fields = array($name, $email, $password);
		$fields = array_map('trim', $required_fields);
		if (in_array(null, $fields)) {
			$error =  'Username/email or password required!!!';
		}
		else if(!$this->valid_token($utoken)){
			$error = "Invalid Token...!!!";
		}
		else if(!$this->isUserExists($name, $email)){
			$error = "User not exist, you havn't registered yet!!!";
		}
		else{			
			$name = escape($name);
			$email = escape($email);
			try
			{
	          $stmt = $this->conn->prepare("SELECT * FROM users WHERE name=:name OR email=:mail LIMIT 1");
	          $stmt->execute(array(':name'=>$name, ':mail'=>$email));
	          $userRow=$stmt->fetch(PDO::FETCH_ASSOC);
	          if($stmt->rowCount() > 0)
	          {
	             if(password_verify($password, $userRow['password_hash']))
	             {
	                $_SESSION['user_session'] = $userRow['id'];
	                $_SESSION['user_name'] = $userRow['name'];
	                
	                return true;
	             }
	             else
	             {
	             	$error = "Incorrect credential";
	                
	             }
	          }
	        }
	        catch(PDOException $e)
	        {
	           echo $e->getMessage();
	        }
		}			
		return $error;
	}
	public function is_timeout()
	{
		$logLength = 1800; # time in seconds :: 1800 = 30 minutes 
	    $ctime = strtotime("now"); # Create a time from a string 
	    # If no session time is created, create one 
	    if(!isset($_SESSION['sessionX'])){  
	        # create session time 
	        $_SESSION['sessionX'] = $ctime;  
	    }else{ 
	        # Check if they have exceded the time limit of inactivity 
	        if(((strtotime("now") - $_SESSION['sessionX']) > $logLength) && $this->is_loggedin()){ 
	            return true; 
	        }else{ 
	            # If they have not exceded the time limit of inactivity, keep them logged in 
	            $_SESSION['sessionX'] = $ctime; 
	        } 
	    } 
	}
	public function is_loggedin()
	{
	     if(isset($_SESSION['user_session']))
	      {
	         return true;
	      }
	}
	public function redirect($url)
	{
		header("Location: $url");
	}
	public function logout()
	{
		session_start();
		session_destroy();
		unset($_SESSION['user_session']);
		unset($_SESSION['user_name']);
		return true;
	}
	private function isUserExists($name, $email) {
		$stmt = $this->conn->prepare("SELECT id from users WHERE name = :name OR email=:mail");
		//$stmt->bind_param("s", $email);
		$stmt->execute(array(':name' => $name,':mail' => $email));
		//$stmt->bind_result();
		$num_rows = $stmt->rowCount();
		//$stmt->close();
		return $num_rows > 0;
	}
	public function valid_token($token){
		//if(!isset($_SESSION['token']) || $token != $_SESSION['token'])
		return isset($_SESSION['token']) && $token == $_SESSION['token'];	
	}
	
}