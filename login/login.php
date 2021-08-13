<?php session_start(); ?>

<?php 
		require "../templates/start.php"; 
		require "../db_config.php";
		$connection = new PDO("mysql:host=$host;dbname=$db_name;", $db_username, $db_password);
		
		echo '
			<div style="margin-left:80px; margin-top:20px;">
			<h2>Login/Register</h2>
			
			<form method="POST" action="post.php" style="margin-top:30px;">

				<label for="login_name">Username:</label>
				<input id="login_name" name="login_name" type="text" />
				<br>
				<br>
				<label for="login_password">Password:</label>
				<input id="login_password" name="login_password" type="password" />
				<br><br>
				<br>
				Course:&emsp;';
				
		$sql = "SELECT tenant
			FROM users";	

		$statement = $connection->prepare($sql);
		$statement->execute();
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		echo '<select name="tenant">';	
		foreach ($result as $row) {
			echo '<option value="'.$row['tenant'].'">'.$row['tenant'].'</option>';
		}
		echo '
				<br><br>
				<br>
				<input type="submit" name="button_login" value="Sign in" style="margin-left:45px; font-size:18px;"/>
				<input type="submit" name="button_register" value="Register" style="margin-left:20px; font-size:18px;"//>';
				
		if(isset($_SESSION["error"])){
			$error = $_SESSION["error"];
			echo "<span>$error</span>";
		}
		echo '
			</form>
			</div>;';
require "../templates/end.php"; ?>