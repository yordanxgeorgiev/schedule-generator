<?php
	// Create database, tables and test rows
	
	require "db_config.php";
	
	try {	
	$connection = new PDO("mysql:host=$host;", $db_username, $db_password);
	// Create database
	$sql_create_db = "DROP DATABASE IF EXISTS $db_name;
						SET @db_name = '$db_name';" 
						. file_get_contents("data/create_database.sql");
	$statement = $connection->prepare($sql_create_db);
	$statement->execute();
	
	//Create table for the users	
	$sql_table_users = "USE $db_name;"
						. file_get_contents("data/create_table_users.sql");
	$statement = $connection->prepare($sql_table_users);
	$statement->execute();		

	// Create table for presentations
	$sql_table_presentations= "USE $db_name;"
								. file_get_contents("data/create_table_presentations.sql");	
	$statement = $connection->prepare($sql_table_presentations);
	$statement->execute();	
	
	// Create table for personal schedule
    $sql_table_personal_schedule = "USE $db_name;"
						. file_get_contents("data/create_table_personal_schedule.sql");
	$statement = $connection->prepare($sql_table_personal_schedule);
	$statement->execute();					
	
	// Create admin user
	$pass1 = password_hash('123', PASSWORD_DEFAULT);
	$sql_insert_admin1 = "INSERT IGNORE INTO users(username, password, tenant)
	VALUES ('admin_web2020kn', '$pass1', 'web2020kn')";
	$statement = $connection->prepare($sql_insert_admin1);
	$statement->execute();	
	
	// insert sample presentations
	$sql = "INSERT IGNORE INTO presentations(presentationId,tenant,date,start, end, topic, names)
			VALUES(DEFAULT, 'web2020kn', '2020-12-28', '08:00', '08:30', 'Tema 1', 'Name 1'),
					(DEFAULT, 'web2020si', '2020-12-29', '08:30', '09:00', 'Tema 2', 'Name 2'),
					(DEFAULT, 'web2020kn', '2020-12-29', '08:00', '08:30', 'Tema 3', 'Name 3'),
					(DEFAULT, 'web2020kn', '2020-12-28', '08:30', '09:00', 'Tema 4', 'Name 4')";
    $statement = $connection->prepare($sql);
	$statement->execute();
	
    echo "Database and tables created successfully.";
	echo "Redirecting to home page...";
	header("Refresh:3; URL=initial_page.php"); 
	exit();
} catch(PDOException $error) {
    echo $sql . "<br>" . $error->getMessage();
}
?>