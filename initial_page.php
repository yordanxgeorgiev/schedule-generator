<?php 
	require "./db_config.php";
	$connection = new PDO("mysql:host=$host;dbname=$db_name;", $db_username, $db_password);
	session_start();
	$tenant = isset($_SESSION['tenant'])? $_SESSION['tenant'] : 'unknown';
	
	$sql = "";
	if($tenant == 'unknown')
	{
		$sql = "SELECT DISTINCT date
			FROM presentations";
	}
	else
	{
		$sql = "SELECT DISTINCT date
			FROM presentations WHERE tenant = '$tenant' ";
	}
	
	$statement = $connection->prepare($sql);
	$statement->execute();
	
	$dates = "";
	$dates_rev="";
	while ($row = $statement->fetch(PDO::FETCH_ASSOC))
    {
		$split = explode("-", $row['date']);
		$r_split = array_reverse($split);
		$formatted = implode("/", $r_split);
		$temp = $r_split[0];
		$r_split[0] = $r_split[1];
		$r_split[1] = $temp;
		$formatted_rev = implode("/", $r_split);
        $dates.= $formatted . ',';
		$dates_rev.=$formatted_rev . ',';
    }
	$dates = substr($dates, 0, -1);
	$dates_rev = substr($dates_rev, 0, -1);
	
	echo '
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset="utf-8">
				<title>Schedule Generator</title>		
				
				<link rel="stylesheet" type="text/css" href="./css/style.css" >
				<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" >
				<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
				<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
				
				<script>
					$(document).ready(function() {
						var dates =\''; 
				echo $dates;
				echo '\'; var dates_rev=\'';
				echo $dates_rev;
				echo '\';				
						var SelectedDates = dates.split(",");
						SelectedDates = SelectedDates.map(
							function(x){
								return "\'".concat(x).concat("\'");
							}
						);
						
						var SelectedDates_rev = dates_rev.split(",");
						SelectedDates_rev = SelectedDates_rev.map(
							function(x){
								return "\'".concat(x).concat("\'");
							}
						);

						
						var i;
						for(i = 0; i < SelectedDates.length; i++)
						{
							var date = new Date(SelectedDates[i]);
							SelectedDates[date] = date.toString();
							if(SelectedDates[date] == "Invalid Date")
							{
								var rev_date = new Date(SelectedDates_rev[i]);
								SelectedDates[rev_date] = rev_date.toString();
								SelectedDates[date] = rev_date.toString();
							}
						}
						
						$(\'#datepicker\').datepicker({
							
							dateFormat: "yy-mm-dd",
							
							beforeShowDay: function(date) {
								var Highlight = SelectedDates[date];
								if (Highlight) {								
								return [true, \'highlighted\', Highlight];
								} else {
									return [false, \'\', \'\'];
								}
							},
							onSelect: function(date)
								{
								
								}
						});
					});		
				
				</script>
			</head>
		<body style="margin-left:50px;">	
			<h1 style="margin-left:8px;">Schedule Generator</h1>
			<hr style="margin-left:-42px;">
			';
?>

<?php

$user = isset($_SESSION['username']) ? $_SESSION['username'] : "unknown";

if($user === "unknown")
{
	echo "<br><br>";
	echo "<a href=\"login\login.php\"><strong>Login</strong></a>";
	echo " to see personal schedule.";
}
else
{
	echo "<br><br>";
	echo 'Hello, <strong><i>'.$user.'</i></strong>!';
	echo ' Your personal schedule is <a href="schedule\personal.php"><strong>here</strong></a>.';
	
	echo "<a href=\"login\session_destroy.php\" class=\"button\", style = \"margin-left:800px;\"><strong>Log out</strong></a>";
}

echo '
		<form action="./schedule/daily.php" method="post"; style="margin-top:10px;""> 
				<br><br>
				Full day schedule (yy-mm-dd):&emsp;
				<input type="text" id="datepicker" name="datepicker" value="pick a date" size=8 onfocus="this.value=\'\'"/>
				<input type="submit" name="button_picked_date" value="Show schedule" />
			</form>
	';
	
if($user == "admin_web2020kn")
	{
		echo '<br><br>
			<a href = "import/import_form.php" class="button">Import data</a>
		';
	}

include "templates/end.php"; 
?>