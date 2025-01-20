<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		
			extract($_POST);		
			$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".md5($password)."' ");
			if($qry->num_rows > 0){
				foreach ($qry->fetch_array() as $key => $value) {
					if($key != 'passwors' && !is_numeric($key))
						$_SESSION['login_'.$key] = $value;
				}
				if($_SESSION['login_type'] != 1){
					foreach ($_SESSION as $key => $value) {
						unset($_SESSION[$key]);
					}
					return 2 ;
					exit;
				}
					return 1;
			}else{
				return 3;
			}
	}
	function login2() {
		extract($_POST);
		if (isset($email)) {
			$username = $email;
		}
	
		// Prepare the SQL query to prevent SQL injection
		$stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$qry = $stmt->get_result();
	
		if ($qry->num_rows > 0) {
			$user = $qry->fetch_assoc();
	
			// Verify the password using password_verify
			if (password_verify($password, $user['password'])) {
				foreach ($user as $key => $value) {
					if ($key != 'password' && !is_numeric($key)) {
						$_SESSION['login_' . $key] = $value;
					}
				}
				
				// Load alumnus bio if needed
				if ($_SESSION['login_alumnus_id'] > 0) {
					$bio = $this->db->query("SELECT * FROM alumnus_bio WHERE id = " . $_SESSION['login_alumnus_id']);
					if ($bio->num_rows > 0) {
						foreach ($bio->fetch_assoc() as $key => $value) {
							if ($key != 'password' && !is_numeric($key)) {
								$_SESSION['bio'][$key] = $value;
							}
						}
					}
				}
				
				// Check if bio status is verified
				if ($_SESSION['bio']['status'] != 1) {
					foreach ($_SESSION as $key => $value) {
						unset($_SESSION[$key]);
					}
					return 2; // Account not verified
				}
				return 1; // Successful login
			} else {
				return 3; // Incorrect password
			}
		} else {
			return 3; // Username not found
		}
	}
	
	function login3() {
		extract($_POST);
		if (isset($email)) {
			$username = $email;
		}
	
		// Prepare the SQL query to prevent SQL injection
		$stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$qry = $stmt->get_result();
	
		if ($qry->num_rows > 0) {
			$user = $qry->fetch_assoc();
	
			// Verify the password using password_verify
			if (password_verify($password, $user['password'])) {
				foreach ($user as $key => $value) {
					if ($key != 'password' && !is_numeric($key)) {
						$_SESSION['login_' . $key] = $value;
					}
				}
	
				// Load student bio if necessary
				if ($_SESSION['login_student_id'] > 0) {
					$bio = $this->db->query("SELECT * FROM student_bio WHERE id = " . $_SESSION['login_student_id']);
					if ($bio->num_rows > 0) {
						foreach ($bio->fetch_assoc() as $key => $value) {
							if ($key != 'password' && !is_numeric($key)) {
								$_SESSION['bio'][$key] = $value;
							}
						}
					}
				}
	
				// Check if bio status is verified
				if ($_SESSION['bio']['status'] != 1) {
					foreach ($_SESSION as $key => $value) {
						unset($_SESSION[$key]);
					}
					return 2; // Account not verified
				}
				return 1; // Successful login
			} else {
				return 3; // Incorrect password
			}
		} else {
			return 3; // Username not found
		}
	}
	
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function logout2(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}
	function logout3(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}


	function save_user() {
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
	
		// Use password_hash if a new password is provided
		if (!empty($password)) {
			$hashed_password = password_hash($password, PASSWORD_DEFAULT);
			$data .= ", password = '$hashed_password' ";
		}
		$data .= ", type = '$type' ";
		if ($type == 1) {
			$establishment_id = 0;
		}
		$data .= ", establishment_id = '$establishment_id' ";
		// Check if username is already in use by another user
		$chk = $this->db->query("SELECT * FROM users WHERE username = '$username' AND id != '$id'")->num_rows;
		if ($chk > 0) {
			return 2; // Username already exists
			exit;
		}
		// Insert or update user information
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO users SET " . $data);
		} else {
			$save = $this->db->query("UPDATE users SET " . $data . " WHERE id = " . $id);
		}
		if ($save) {
			return 1; // Operation successful
		}
	}
	
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
	}
	function alumni_signup() {
		extract($_POST);
		$data = " name = '" . $firstname . ' ' . $lastname . "' ";
		$data .= ", username = '$email' ";
	
		// Use password_hash to securely hash the password
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);
		$data .= ", password = '$hashed_password' ";
	
		// Check if the email (username) already exists
		$chk = $this->db->query("SELECT * FROM users WHERE username = '$email'")->num_rows;
		if ($chk > 0) {
			return 2; // Email already exists
			exit;
		}
	
		// Save user to the `users` table
		$save = $this->db->query("INSERT INTO users SET " . $data);
		if ($save) {
			$uid = $this->db->insert_id;
			$data = '';
	
			// Prepare data for the `alumnus_bio` table
			foreach ($_POST as $k => $v) {
				if ($k == 'password') {
					continue;
				}
				if (empty($data) && !is_numeric($k)) {
					$data = " $k = '$v' ";
				} else {
					$data .= ", $k = '$v' ";
				}
			}
	
			// Handle avatar upload
			if ($_FILES['img']['tmp_name'] != '') {
				$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
				$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
				$data .= ", avatar = '$fname' ";
			}
	
			// Save data to the `alumnus_bio` table
			$save_alumni = $this->db->query("INSERT INTO alumnus_bio SET $data");
			if ($save_alumni) {
				$aid = $this->db->insert_id;
				$this->db->query("UPDATE users SET alumnus_id = $aid WHERE id = $uid ");
	
				// Automatically log in the user after signup
				$login = $this->login2();
				if ($login) {
					return 1; // Signup and login successful
				}
			}
		}
	}
	
	function student_signup() {
		extract($_POST);
		$data = " name = '" . $firstname . ' ' . $lastname . "' ";
		$data .= ", username = '$email' ";
	
		// Use password_hash for secure password storage
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);
		$data .= ", password = '$hashed_password' ";
	
		// Check if the email (username) already exists
		$chk = $this->db->query("SELECT * FROM users WHERE username = '$email'")->num_rows;
		if ($chk > 0) {
			return 2; // Email already exists
			exit;
		}
	
		// Save user to the `users` table
		$save = $this->db->query("INSERT INTO users SET " . $data);
		if ($save) {
			$uid = $this->db->insert_id;
			$data = '';
	
			// Prepare data for the `student_bio` table
			foreach ($_POST as $k => $v) {
				if ($k == 'password') {
					continue;
				}
				if (empty($data) && !is_numeric($k)) {
					$data = " $k = '$v' ";
				} else {
					$data .= ", $k = '$v' ";
				}
			}
	
			// Handle avatar upload
			if ($_FILES['img']['tmp_name'] != '') {
				$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
				$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
				$data .= ", avatar = '$fname' ";
			}
	
			// Save data to the `student_bio` table
			$save_student = $this->db->query("INSERT INTO student_bio SET $data");
			if ($save_student) {
				$aid = $this->db->insert_id;
				$this->db->query("UPDATE users SET student_id = $aid WHERE id = $uid ");
	
				// Automatically log in the user after signup
				$login = $this->login3();
				if ($login) {
					return 1; // Signup and login successful
				}
			}
		}
	}
	
	function update_account() {
		extract($_POST);
	
		// Update user account details
		$data = " name = '" . $firstname . ' ' . $lastname . "' ";
		$data .= ", username = '$email' ";
		// Update password if provided
		if (!empty($password)) {
			$hashed_password = password_hash($password, PASSWORD_DEFAULT);
			$data .= ", password = '$hashed_password' ";
		}
		// Check for duplicate email excluding current user
		$chk = $this->db->query("SELECT COUNT(*) as count FROM users WHERE username = '$email' AND id != '{$_SESSION['login_id']}'")->fetch_assoc();
		if ($chk['count'] > 0) {
			return 2; // Email already exists
			exit;
		}
	
		// Update `users` table
		$save = $this->db->query("UPDATE users SET $data WHERE id = '{$_SESSION['login_id']}'");
		if ($save) {
			$data = '';
	
			// Update additional details
			foreach ($_POST as $k => $v) {
				if ($k == 'password') continue;
				if (empty($data) && !is_numeric($k)) {
					$data = " $k = '$v' ";
				} else {
					$data .= ", $k = '$v' ";
				}
			}
	
			// Handle avatar upload
			if ($_FILES['img']['tmp_name'] != '') {
				$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
				$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
				$data .= ", avatar = '$fname' ";
			}
	
			// Check if user is alumnus or student
			if (isset($_SESSION['login_alumnus_id']) && $_SESSION['login_alumnus_id'] > 0) {
				// Update alumnus data
				$save_alumni = $this->db->query("UPDATE alumnus_bio SET $data WHERE id = '{$_SESSION['bio']['id']}'");
				if ($save_alumni) {
					foreach ($_SESSION as $key => $value) unset($_SESSION[$key]);
					$login = $this->login2();
					if ($login) return 1; // Success for alumnus
				}
			} elseif (isset($_SESSION['login_student_id']) && $_SESSION['login_student_id'] > 0) {
				// Update student data
				$save_student = $this->db->query("UPDATE student_bio SET $data WHERE id = '{$_SESSION['bio']['id']}'");
				if ($save_student) {
					foreach ($_SESSION as $key => $value) unset($_SESSION[$key]);
					$login = $this->login3();
					if ($login) return 1; // Success for student
				}
			}
		}
	
		return 3; // Default failure
	}	
	function save_settings(){
		extract($_POST);
		$data = " name = '".str_replace("'","&#x2019;",$name)."' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
					$data .= ", cover_img = '$fname' ";

		}
		
		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set ".$data);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set ".$data);
		}
		if($save){
		$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['settings'][$key] = $value;
		}

			return 1;
				}
	}

	
	function save_course(){
		extract($_POST);
		$data = " course = '$course' ";
			if(empty($id)){
				$save = $this->db->query("INSERT INTO courses set $data");
			}else{
				$save = $this->db->query("UPDATE courses set $data where id = $id");
			}
		if($save)
			return 1;
	}
	function delete_course(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM courses where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function update_alumni_acc(){
		extract($_POST);
		$update = $this->db->query("UPDATE alumnus_bio set status = $status where id = $id");
		if($update)
			return 1;
	}
	function update_student_acc(){
		extract($_POST);
		$update = $this->db->query("UPDATE student_bio set status = $status where id = $id");
		if($update)
			return 1;
	}
	function save_gallery(){
		extract($_POST);
		$img = array();
  		$fpath = 'assets/uploads/gallery';
		$files= is_dir($fpath) ? scandir($fpath) : array();
		foreach($files as $val){
			if(!in_array($val, array('.','..'))){
				$n = explode('_',$val);
				$img[$n[0]] = $val;
			}
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO gallery set about = '$about' ");
			if($save){
				$id = $this->db->insert_id;
				$folder = "assets/uploads/gallery/";
				$file = explode('.',$_FILES['img']['name']);
				$file = end($file);
				if(is_file($folder.$id.'/_img'.'.'.$file))
					unlink($folder.$id.'/_img'.'.'.$file);
				if(isset($img[$id]))
						unlink($folder.$img[$id]);
				if($_FILES['img']['tmp_name'] != ''){
					$fname = $id.'_img'.'.'.$file;
					$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/gallery/'. $fname);
				}
			}
		}else{
			$save = $this->db->query("UPDATE gallery set about = '$about' where id=".$id);
			if($save){
				if($_FILES['img']['tmp_name'] != ''){
					$folder = "assets/uploads/gallery/";
					$file = explode('.',$_FILES['img']['name']);
					$file = end($file);
					if(is_file($folder.$id.'/_img'.'.'.$file))
						unlink($folder.$id.'/_img'.'.'.$file);
					if(isset($img[$id]))
						unlink($folder.$img[$id]);
					$fname = $id.'_img'.'.'.$file;
					$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/gallery/'. $fname);
				}
			}
		}
		if($save)
			return 1;
	}
	function delete_gallery(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM gallery where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_career(){
		extract($_POST);
		$data = " company = '$company' ";
		$data .= ", job_title = '$title' ";
		$data .= ", location = '$location' ";
		$data .= ", description = '".htmlentities(str_replace("'","&#x2019;",$description))."' ";

		if(empty($id)){
			// echo "INSERT INTO careers set ".$data;
		$data .= ", user_id = '{$_SESSION['login_id']}' ";
			$save = $this->db->query("INSERT INTO careers set ".$data);
		}else{
			$save = $this->db->query("UPDATE careers set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_career(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM careers where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_forum(){
		extract($_POST);
		$data = " title = '$title' ";
		$data .= ", description = '".htmlentities(str_replace("'","&#x2019;",$description))."' ";

		if(empty($id)){
		$data .= ", user_id = '{$_SESSION['login_id']}' ";
			$save = $this->db->query("INSERT INTO forum_topics set ".$data);
		}else{
			$save = $this->db->query("UPDATE forum_topics set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_forum(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM forum_topics where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_comment(){
		extract($_POST);
		$data = " comment = '".htmlentities(str_replace("'","&#x2019;",$comment))."' ";

		if(empty($id)){
			$data .= ", topic_id = '$topic_id' ";
			$data .= ", user_id = '{$_SESSION['login_id']}' ";
			$save = $this->db->query("INSERT INTO forum_comments set ".$data);
		}else{
			$save = $this->db->query("UPDATE forum_comments set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_comment(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM forum_comments where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_event(){
		extract($_POST);
		$data = " title = '$title' ";
		$data .= ", schedule = '$schedule' ";
		$data .= ", content = '".htmlentities(str_replace("'","&#x2019;",$content))."' ";
		if($_FILES['banner']['tmp_name'] != ''){
						$_FILES['banner']['name'] = str_replace(array("(",")"," "), '', $_FILES['banner']['name']);
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['banner']['name'];
						$move = move_uploaded_file($_FILES['banner']['tmp_name'],'assets/uploads/'. $fname);
					$data .= ", banner = '$fname' ";

		}
		if(empty($id)){

			$save = $this->db->query("INSERT INTO events set ".$data);
		}else{
			$save = $this->db->query("UPDATE events set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_event(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM events where id = ".$id);
		if($delete){
			return 1;
		}
	}
	
	function participate(){
		extract($_POST);
		$data = " event_id = '$event_id' ";
		$data .= ", user_id = '{$_SESSION['login_id']}' ";
		$commit = $this->db->query("INSERT INTO event_commits set $data ");
		if($commit)
			return 1;

	}
}