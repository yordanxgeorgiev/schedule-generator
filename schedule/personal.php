<?php require '../templates/start.php' ?>
 
<?php
	session_start();
    $user = isset($_SESSION['username']) ? $_SESSION['username'] : "unknown";
	$tenant = $_SESSION['tenant'];
	
    require "../db_config.php";
	
	$connection = new PDO("mysql:host=$host;dbname=$db_name;", $db_username, $db_password);
	
	if($_POST) {
        $presentationTopic = $_POST["selectPresentation"];
		$choice = isset($_POST["choice"])? $_POST["choice"] : "unchosen";
		if($choice != "unchosen")
		{
			// Get presentationId from presentationTopic
			$sql = "SELECT presentationId
				FROM presentations
				WHERE '$presentationTopic' = topic AND tenant = '$tenant'";
			$statement = $connection->prepare($sql);
			$statement->execute();
			$presentationId = $statement->fetchAll(); // $presentationId[0][0] is the id
			$presentationId = $presentationId[0][0];
			// Add presentationId to chosen list
			$sql = "INSERT IGNORE INTO personal_schedule(username, tenant, presentationId, choice)
					VALUES('$user', '$tenant', '$presentationId', '$choice')";			
			$statement = $connection->prepare($sql);
			$statement->execute();
		}
			
    }		
?>

<?php
	$sql = "SELECT topic
					FROM presentations
					WHERE tenant = '$tenant'";
					
	$statement = $connection->prepare($sql);
	$statement->execute();
	$result = $statement->fetchAll();
	
	echo '<h2 style="margin-left:40px;"> Personal schedule of the presentations: </h2>';
	
	$query = $connection->query($sql);
	
	echo '<form action="personal.php" method="post" name = "post" style="margin-left:40px; font-size:20px;">';
	echo 'Select presentation: &emsp;';
	echo '<select name="selectPresentation">';	
		while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			echo '<option value="'.$row['topic'].'">'.$row['topic'].'</option>';
		}
		
		echo '
				<input type="radio" name="choice" style="margin-left:40px;"
				value="mustGo"> Must go
				<input type="radio" name="choice"
				value="wantToGo"> Want to go

				<input type="submit" value="Add" style="margin-left:40px;" />
			 </form>';
			  
	echo '</select>';
?>

<?php
	// Create table with chosen presentations
	$sql = "SELECT $shown_columns, choice
				FROM personal_schedule AS a 
				LEFT JOIN presentations AS b 
				ON a.presentationId = b.presentationId
			";
	$statement = $connection->prepare($sql);
	$statement->execute();
	$result = $statement->fetchAll();

	if(!empty($result))
	{
		// remove duplicates to use foreach
		$cap = count($result[0])/2;
		for($i = 0; $i < count($result); $i++)
		{
			for($j = 0; $j < $cap; $j++)
			{	
				unset($result[$i][$j]);
			}
		}
		
		$counter = 1;
		echo "<table>";
		
		echo "<caption style=\"margin-top:20px;\">Personal schedule</caption>";
		echo "<th>No</th>";
		
		foreach(explode(",",$shown_columns) as $column)
		{
			echo "<th>" . $column . "</th>";
		}
		echo "<th>" . 'choice' . "</th>";
		
		foreach($result as $row)
		{	
			$choice = $row['choice'];
		
			echo "<tr>";
					echo "<td>".$counter."</td>";
			
					foreach($row as $value)
					{
						if($value == "") { $value = "---";}
						echo "<td>" . $value . "</td>";
					}
					echo "/<tr>";
					$counter++;
		}
		
		echo "</table>"; 
		$shown_columns .= ",choice";
		array_unshift($result , explode(",",$shown_columns));
		$export_data = serialize($result);
		include '../export/export_form.php';
		}
		
	
	/*if(array_key_exists('mustGo',$result[0]) || array_key_exists('wantToGo',$result[0]))
	{
		$mustGoIds = $result[0]['mustGo'];
		$wantToGoIds = $result[0]['wantToGo'];
		
		$mustGoPieces = explode(",", $mustGoIds);
		$wantToGoPieces = explode(",", $wantToGoIds);
		
		$mustGoInts = array_map('intval', $mustGoPieces);
		$wantToGoInts = array_map('intval', $wantToGoPieces);
		$counter = 1;
		echo "<table>"; 
		
		$sql = "";
	
		foreach($mustGoInts as $value)
		{
			$sql .= "SELECT $shown_columns, 'Must Go' as Choice
					FROM presentations
					WHERE presentationId = $value
					UNION ";
		}
		foreach($wantToGoInts as $value)
		{
			$sql .= "SELECT $shown_columns, 'Want to go' as Choice
					FROM presentations
					WHERE presentationId = $value
					UNION ";
		}
		$shown_columns .= ',CHOICE';
		$sql = substr($sql, 0, -6)."ORDER BY date;";
		$statement = $connection->prepare($sql);
		$statement->execute();
		$result = $statement->fetchAll();
		
		if(!empty($result))
		{
			// remove duplicates to use foreach
			$cap = count($result[0])/2;
			for($i = 0; $i < count($result); $i++)
			{
				for($j = 0; $j < $cap; $j++)
				{	
					unset($result[$i][$j]);
				}
			}
			
			echo "<caption style=\"margin-top:20px;\">Personal schedule</caption>";
			echo "<th>No</th>";
			
			foreach(explode(",",$shown_columns) as $column)
			{
				echo "<th>" . $column . "</th>";
			}
			$counter = 1;
			foreach ($result as $row) 
			{
				echo "<tr>";
				echo "<td>".$counter."</td>";
		
				foreach($row as $value)
				{
					if($value == "") { $value = "---";}
					echo "<td>" . $value . "</td>";
				}
				echo "/<tr>";
				$counter++;
			}
			echo "</table>"; 		
			
			array_unshift($result , explode(",",$shown_columns));
			$export_data = serialize($result);
			include '../export/export_form.php';;
		}			
	}*/
?>

<?php
		
	/*session_start();
    $user = isset($_SESSION['username']) ? $_SESSION['username'] : "unknown";
	
    require "../db_config.php";
	
	$connection = new PDO("mysql:host=$host;dbname=$db_name;", $db_username, $db_password);
	
	if($_POST) {
        $presentationTopic = $_POST["selectPresentation"];
		$choice = isset($_POST["choice"])? $_POST["choice"] : "unchosen";
		
		if($choice != "unchosen")
		{
			// Get presentationId from presentationTopic
			$sql = "SELECT presentationId
				FROM presentations
				WHERE '$presentationTopic' = topic";
			$statement = $connection->prepare($sql);
			$statement->execute();
			$presentationId = $statement->fetchAll(); // $presentationId[0][0] is the id
			
			// Add presentationId to chosen list
			$choiceColumn;
			if($choice == 'mustGo') { $choiceColumn = 'mustGo';}
			else if($choice == 'wantToGo') { $choiceColumn = 'wantToGo';}
			
			$sql = "SELECT $choiceColumn
					FROM personal
					WHERE username = '$user'";
			$statement = $connection->prepare($sql);
			$statement->execute();
			$result = $statement->fetchAll();
			$chosenIds = "";
			
			if(!empty($result))
			{
				$chosenIds = $result[0][0];
			}			
			if($chosenIds == "")
			{
				$chosenIds = $presentationId[0][0];
			}
			else
			{
				$chosenPieces = explode(",", $chosenIds);
				if(!in_array($presentationId[0][0], $chosenPieces))
				{
					$chosenIds = $chosenIds. "," .$presentationId[0][0];
				}				
			}
			
			$sql = "UPDATE personal
					SET $choiceColumn = '$chosenIds'
					WHERE username = '$user'";
					
			$result = $connection->query($sql);			
		}
			
    }		
?>

<?php

	$sql = "SELECT topic
                FROM presentations";
				
	$statement = $connection->prepare($sql);
	$statement->execute();
	$result = $statement->fetchAll();
	
    echo '<h2 style="margin-left:40px;"> Personal schedule of the presentations: </h2>';
    
    $query = $connection->query($sql);
    
	echo '<form action="personal.php" method="post" name = "post" style="margin-left:40px; font-size:20px;">';
	echo 'Select presentation: &emsp;';
	echo '<select name="selectPresentation">';	
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="'.$row['topic'].'">'.$row['topic'].'</option>';
        }
        
		echo '
                <input type="radio" name="choice" style="margin-left:40px;"
                value="mustGo"> Must go
                <input type="radio" name="choice"
                value="wantToGo"> Want to go

                <input type="submit" value="Add" style="margin-left:40px;" />
             </form>';
              
    echo '</select>';
?>

<?php
	// Create table with chosen presentations
	$sql = "SELECT *
			FROM personal
			WHERE username ='$user'";
	$statement = $connection->prepare($sql);
	$statement->execute();
	$result = $statement->fetchAll();
	
	if(array_key_exists('mustGo',$result[0]) || array_key_exists('wantToGo',$result[0]))
	{
		$mustGoIds = $result[0]['mustGo'];
		$wantToGoIds = $result[0]['wantToGo'];
		
		$mustGoPieces = explode(",", $mustGoIds);
		$wantToGoPieces = explode(",", $wantToGoIds);
		
		$mustGoInts = array_map('intval', $mustGoPieces);
		$wantToGoInts = array_map('intval', $wantToGoPieces);
		$counter = 1;
		echo "<table>"; 
		
		$sql = "";
	
		foreach($mustGoInts as $value)
		{
			$sql .= "SELECT $shown_columns, 'Must Go' as Choice
					FROM presentations
					WHERE presentationId = $value
					UNION ";
		}
		foreach($wantToGoInts as $value)
		{
			$sql .= "SELECT $shown_columns, 'Want to go' as Choice
					FROM presentations
					WHERE presentationId = $value
					UNION ";
		}
		$shown_columns .= ',CHOICE';
		$sql = substr($sql, 0, -6)."ORDER BY date;";
		$statement = $connection->prepare($sql);
		$statement->execute();
		$result = $statement->fetchAll();
		
		if(!empty($result))
		{
			// remove duplicates to use foreach
			$cap = count($result[0])/2;
			for($i = 0; $i < count($result); $i++)
			{
				for($j = 0; $j < $cap; $j++)
				{	
					unset($result[$i][$j]);
				}
			}
			
			echo "<caption style=\"margin-top:20px;\">Personal schedule</caption>";
			echo "<th>No</th>";
			
			foreach(explode(",",$shown_columns) as $column)
			{
				echo "<th>" . $column . "</th>";
			}
			$counter = 1;
			foreach ($result as $row) 
			{
				echo "<tr>";
				echo "<td>".$counter."</td>";
		
				foreach($row as $value)
				{
					if($value == "") { $value = "---";}
					echo "<td>" . $value . "</td>";
				}
				echo "/<tr>";
				$counter++;
			}
			echo "</table>"; 		
			
			array_unshift($result , explode(",",$shown_columns));
			$export_data = serialize($result);
			include '../export/export_form.php';;
		}			
	}*/
?>
 
 <?php require '../templates/end.php' ?>