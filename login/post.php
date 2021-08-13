<?php 

	session_start();
	
	if($_POST) {
        $user = isset($_POST["login_name"]) ? triminput($_POST["login_name"]) : "";
        $pass = isset($_POST["login_password"]) ? trimInput($_POST["login_password"]) : "";
		$submit_type = isset($_POST["button_login"]) ? "login" : "register";
		
		checkInput($user, $pass);
		
		if(strcmp($submit_type, "login")==0) login($user, $pass);
		else register($user, $pass);
		
	}
	
	// Function for login
	function login($user, $pass)
	{
		require "../db_config.php";
		$connection = new PDO("mysql:host=$host;dbname=$db_name;", $db_username, $db_password);
		$tenant = $_POST['tenant'];
		$sql = "SELECT * FROM users WHERE username = '$user'";
        $statement = $connection->prepare($sql);
        $statement->execute();

		// Get the db user with the same username
        $db_user = $statement->fetch(PDO::FETCH_ASSOC);
		if(empty($db_user))
		{
			$_SESSION["error"] = "Incorrect username!";
			header('location:login.php'); 
			exit();
		}
		$correct_pass = password_verify($pass, $db_user['password']);
		
		if($correct_pass)
		{
			$_SESSION['username'] = $user;
			$_SESSION['tenant'] = $tenant;
			header('Location:../initial_page.php'); 
			exit();
		}
		else
		{
			$_SESSION["error"] = "Incorrect password!";
			header('location:login.php'); 
			exit();
		}		
	}
	
	// Function for registration 
	function register($user, $pass)
	{
		require "../db_config.php";
		$connection = new PDO("mysql:host=$host;dbname=$db_name;", $db_username, $db_password);
		
		$sql = "SELECT * FROM users WHERE username='$user'";
		$res = $connection->query($sql);
		
		if($res->rowCount()>0) {
				$_SESSION["error"] = "Username already taken!";
				header('location:login.php'); 
				exit();
		}
		else{
			// hashing the password
			$tenant = $_POST['tenant'];
			$hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
			$query = "INSERT INTO users (username, password,tenant) 
				  VALUES ('$user', '$hashed_pass', '$tenant')";
			$connection->query($query);
			$query = "INSERT INTO personal_schedule (username, tenant) 
				  VALUES ('$user', '$tenant')";
			$connection->query($query);
			$_SESSION['username'] = $user;
			$_SESSION['tenant'] = $tenant;
			header('Location:../initial_page.php'); 
			exit();
		}
	}
	
	// Trim unwanted characters from input
	function trimInput($input) {
        $input = trim($input);
        $input = htmlspecialchars($input);
        $input = stripslashes($input);

        return $input;
    }
	
	// Check username and password (empty & length) and redirect to login page if error
	function checkInput($username, $password)
	{
		if (!$username || !$password) {
			$_SESSION["error"] = "Username or Password field can't be empty!";
			header('location:login.php'); 
			exit();
		}
		else if(strlen($username) > 20 || strlen($password) > 20)
		{
			$_SESSION["error"] = "Username and Password can't be over 20 characters!";
			header('location:login.php'); 
			exit();
		}		
	}
?> 