<?php
	require '../db_config.php';
	session_start();
	$user = $_SESSION['username'];
	$date = $_POST['date'];
	$presentation_attributes = "start,end,topic,names,ids,major,description";
	
	$tenant = $_SESSION['tenant'];
	
	if($_POST['spreadsheet_link'] != "")
	{
		if($_POST['spreadsheet_page'] == "" || $_POST['spreadsheet_range'] == "")
		{
			echo "Error: spreadsheet fields can't be empty.";
			echo "Redirecting to home page...";
			header("Refresh:5; URL=../initial_page.php");
		}
		
		$link = $_POST['spreadsheet_link'];
		$page = $_POST['spreadsheet_page'];
		$range = $_POST['spreadsheet_range'];
		
		$url = $link.'/gviz/tq?tqx=out:csv&range='.$range.'&sheet='.$page.'.';
		date_default_timezone_set('Europe/Sofia');
		$time = date('Y-m-d-h-i-s-a', time());
		$target_file = "imported/spreadsheet_".$time.".csv";
		
		file_put_contents($target_file, file_get_contents($url));
		
		echo "The file has been imported.";
			
		saveCSV("csv", $target_file, $date, $tenant, $presentation_attributes);			
			
		echo "Redirecting to home page...";
		header("Refresh:5; URL=../initial_page.php"); 
		exit();
	}
	else
	{
		$target_dir = "imported/";
		$target_file = $target_dir . basename($_FILES["fileToImport"]["name"]);
		$importOk = 1;
		$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		
		
		// Check if file already exists
		if (file_exists($target_file)) {
			  echo "Error: file already exists.";
			  echo "<br>";
			  $importOk = 0;
		}
		
		// Allow certain file formats
		if($fileType != "csv") {
			  echo "Error: only 'csv' files are allowed.";
			  echo "<br>";
			  $importOk = 0;
		}
		
		// Check for errors and import
		if ($importOk == 0) {
			echo "<br>";
			echo "Your file was not imported. Redirecting to home page...";
			header("Refresh:5; URL=../initial_page.php"); 
			exit();
		}	 
		else {
			if (move_uploaded_file($_FILES["fileToImport"]["tmp_name"], $target_file)) {
				echo "The file ". htmlspecialchars( basename( $_FILES["fileToImport"]["name"])). " has been imported.";
				
				saveCSV($fileType, $target_file, $date, $tenant, $presentation_attributes);			
				
				echo "Redirecting to home page...";
				header("Refresh:5; URL=../initial_page.php"); 
				exit();
			} else {
				echo "Sorry, there was an error importing your file.";
				echo "Redirecting to home page...";
				header("Refresh:5; URL=../initial_page.php"); 
				exit();
			}
		}
	}
	
	
	function saveCSV($fileType, $taget_file, $date, $tenant, $presentation_attributes)
	{	
		require '../db_config.php';
		$connection = new PDO("mysql:host=$host;dbname=$db_name;", $db_username, $db_password);
		$order = $_POST["order"];
		if($order != ""){				
			$order = explode(",",$_POST['order']);
			$order = array_map('intval', $order);
		}
		else{
			$order = range(1, count(explode(",",$presentation_attributes)));
		}	
		
		$order = array_map('substractOne', $order);
			
		$handle = fopen($taget_file, "r");
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$import_header="INSERT IGNORE into presentations(presentationID, tenant, date, $presentation_attributes)values(DEFAULT, '$tenant','$date','";
			
			foreach($order as $value)
			{
				if($value <= -1)
				{
					$import_header .= "','";
				}
				else
				{
					if(array_key_exists($value, $data))
					{
						$import_header .= $data[$value] . "','";
					}
					else
					{
						$import_header .= "','";
					}
				}				
			}
			$sql_import = substr($import_header, 0, -2) . ");";
			$statement = $connection->prepare($sql_import);
			$statement->execute();
		}
	}
	
	function substractOne($n)
	{
		return ($n-1);
	}	
?>