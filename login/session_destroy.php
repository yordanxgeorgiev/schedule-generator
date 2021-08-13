<?php
	session_start();
	session_destroy();
	header('Location:../initial_page.php');
	exit();
?>