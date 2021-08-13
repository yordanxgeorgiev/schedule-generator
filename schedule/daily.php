 <?php
	require '../templates/start.php';
 ?>
 
 <?php
	session_start();
	$tenant = isset($_SESSION['tenant'])? $_SESSION['tenant'] : 'unknown';
	require "../db_config.php";
	$connection = new PDO("mysql:host=$host;dbname=$db_name;", $db_username, $db_password);
	$date = $_POST['datepicker'];
	$formatted_date = implode("-",array_reverse(explode("-", $date)));
	
	$sql = "";
	if($tenant == 'unknown')
	{
		$sql = "SELECT $shown_columns
                FROM presentations
                WHERE date = '$date'";
	}
	else
	{
		$sql = "SELECT $shown_columns
                FROM presentations
                WHERE date = '$date' AND tenant = '$tenant'";	
	}
	
	
	$statement = $connection->prepare($sql);
	$statement->execute();
	$result = $statement->fetchAll();
 ?>
 
 <?php
	// remove duplicates to use foreach
	$cap = count($result[0])/2;
	for($i = 0; $i < count($result); $i++)
	{
		for($j = 0; $j < $cap; $j++)
		{	
			unset($result[$i][$j]);
		}
	}
	
	echo "<table>"; 
	echo "<caption>Presentations schedule (".$formatted_date.")</caption>";
	echo "<th>No</th>";
	foreach(explode(",",$shown_columns) as $column)
	{
		echo "<th>" . $column . "</th>";
	}
	$counter = 1;
	foreach($result as $row)
	{		
		echo "<tr>";
		echo "<td>".$counter."</td>";
		foreach($row as $value)
		{
			if($value == "") { $value = "---";}
			echo "<td>" . $value . "</td>";				
		}
		echo "</tr>";
		$counter++;
	}
	echo "</table>";
	
	array_unshift($result , explode(",",$shown_columns));
	$export_data = serialize($result);
	include '../export/export_form.php';
 ?>

 <?php require '../templates/end.php' ?>