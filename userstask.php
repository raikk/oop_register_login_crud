<?php
class Usertask {
	private $conn;
    // private $token;
	public function __construct($DB_task) {
		$this->conn = $DB_task;
	}
	public function inserstTask($task, $user_id, $image, $token){
		// $error = array();
		$error = '';
		$required_fields = array($task, $user_id);
		$fields = array_map('trim', $required_fields);
		if (in_array(null, $fields)) {
			$error =  'Fields marked with an asterisk are required';
		}
		else if(!$this->valid_token($token)){
			$error = "Invalid Token...!!!";
		}
		else if(empty($image['image']['name']))
		{
		 $error = "Error no file selected"; 
		}
		else{			
			if(!empty($image['image']['name'])){
			
			$files = $image['image'];
			$uploaded = array();
			$failed = array();
			$allowed = array('png', 'jpg');
				$file_tmp = $files['tmp_name'];
				$file_size = $files['size'];
				$file_error = $files['error'];
				$file_ext = explode('.', $files['name']);
				$file_ext = strtolower(end($file_ext));
				if(in_array($file_ext, $allowed)){
					if($file_error === 0){
						if($file_size <= 2097152){
							$file_name_new = uniqid('', true) . '.' . $file_ext;
							$file_destination = 'uploads/'.$file_name_new;
							if(move_uploaded_file($file_tmp, $file_destination)){
									//$uploaded = $file_destination;
									$task = escape($task);
									$user_id = escape($user_id);
									//$image = escape($image);
									$stmt = $this->conn->prepare("INSERT INTO tasks(task, user_id, image) values(:task, :user_id, :image)");
					            	//$stmt->bind_param("ssss", $name, $email, $password_hash);
									$result = $stmt->execute(array(':task' => $task,':user_id' => $user_id,':image' => $file_name_new));
					            	//$stmt->close();
					            	// Check for successful insertion
									if ($result) {
					                // User successfully inserted
										return 6;
									} else {
					                // Failed to create user
									$error = "Failed to create task...";
									}
							}else{
								$error = $files['name']." failed to upload.";
							}
						}else{
							$error = $files['name']." is too large.";
						}
					}else{
						$error = $files['name']." errored with code {$file_error}.";
					}
				}else{
					$error = $files['name']." file extension '{$file_ext}' is not allowed.";
				}
				}
		}
		return $error;		
		
	}
	public function valid_token($token){
		//if(!isset($_SESSION['token']) || $token != $_SESSION['token'])
		return isset($_SESSION['token']) && $token == $_SESSION['token'];	
	}
	
	public function updateTask($task, $id, $user_id, $image, $token){
		// $error = array();
		$error = '';
		$required_fields = array($task, $user_id);
		$fields = array_map('trim', $required_fields);
		if (in_array(null, $fields)) {
			$error =  'Fields marked with an asterisk are required';
		}
		else if(!$this->valid_token($token)){
			$error = "Invalid Token...!!!";
		}
		else{			
			if(!empty($image['image']['name'])){
			$st = $this->conn->prepare("SELECT image FROM tasks WHERE id=:id");
			$st->execute(array(':id' => $id));
			$iresult = $st->fetch();
			$ires = unlink('uploads/'.$iresult['image']);
				
			
			$files = $image['image'];
			$uploaded = array();
			$failed = array();
			$allowed = array('png', 'jpg');
				$file_tmp = $files['tmp_name'];
				$file_size = $files['size'];
				$file_error = $files['error'];
				$file_ext = explode('.', $files['name']);
				$file_ext = strtolower(end($file_ext));
				if(in_array($file_ext, $allowed)){
					if($file_error === 0){
						if($file_size <= 2097152){
							$file_name_new = uniqid('', true) . '.' . $file_ext;
							$file_destination = 'uploads/'.$file_name_new;
							if(move_uploaded_file($file_tmp, $file_destination)){
									//$uploaded = $file_destination;
									$task = escape($task);
									$user_id = escape($user_id);
					            	
									$stmt = $this->conn->prepare('UPDATE tasks SET task=:task, image=:image WHERE id=:id AND user_id=:user_id');
									$result = $stmt->execute(array(':task' => $task,':user_id' => $user_id,':image' => $file_name_new, ':id' => $id));
               
					            	// Check for successful insertion
									if ($result) {
					                // User successfully inserted
										return 6;
									} else {
					                // Failed to create user
									$error = "Failed to create task...";
									}
							}else{
								$error = $files['name']." failed to upload.";
							}
						}else{
							$error = $files['name']." is too large.";
						}
					}else{
						$error = $files['name']." errored with code {$file_error}.";
					}
				}else{
					$error = $files['name']." file extension '{$file_ext}' is not allowed.";
				}
				}
				else{
					//user are not updating image
					$task = escape($task);
					$user_id = escape($user_id);
	            	
					$stmt = $this->conn->prepare('UPDATE tasks SET task=:task WHERE id=:id AND user_id=:user_id');
					$result = $stmt->execute(array(':task' => $task,':user_id' => $user_id, ':id' => $id));
	            	// Check for successful insertion
					if ($result) {
	                // User successfully inserted
						return 6;
					} else {
	                // Failed to create user
					$error = "Failed to create task...";
					}
				}
		}
		return $error;		
	}
	public function deleteTask($id, $user_id){
		$error = '';
		$del = $this->conn->prepare("SELECT image FROM tasks WHERE id=:id AND user_id = :user_id");
		$del->execute(array(':id'=>$id, ':user_id' => $user_id));
		$result = $del->fetch();
		$res = unlink('uploads/'.$result['image']);
		
		if($res){
			$sql = "DELETE FROM tasks WHERE id =  :id AND user_id = :user_id";
			$stmt = $this->conn->prepare($sql);
			$result = $stmt->execute(array(':id'=> $id,':user_id' => $user_id));
    		// Check for successful insertion
			if ($result) {
        	// User successfully inserted
			return true;
			} else {
        	// Failed to create user
			$error = "Failed to delete task...";
			}
		}
		else{
			$error = "No such file";
		}
		
		return $error;		
	}
	public function getUpdateDetails($id, $user_id){
		$result = $this->conn->prepare("SELECT * FROM tasks WHERE id=:id AND user_id=:user_id");
		$result->execute(array(':id'=>$id, ':user_id'=>$user_id));
		$res = $result->fetch(PDO::FETCH_ASSOC);
		return $res;
	}
	public function showTask($user_id, $page, $perPage){
		$records = array();
		//Positioning
		$start = ($page > 1) ? ($page*$perPage) - $perPage : 0;
		//Query
		$result = $this->conn->prepare("
			SELECT SQL_CALC_FOUND_ROWS id, task, image
			FROM tasks WHERE user_id=:user_id
			LIMIT {$start}, {$perPage}
			");
		$result->execute(array(':user_id' => $user_id));
		$result = $result->fetchAll(PDO::FETCH_ASSOC);
		//Pages
		$total = $this->conn->query("SELECT FOUND_ROWS() as total")->fetch()['total'];
		$pages = ceil($total / $perPage);
		$result['pages'] = $pages;
		$result['total'] = $total;
		
      	if($result){
      		return $result;
      	}
      	else{
      		return 0;
      	}
		
	}
}
?>