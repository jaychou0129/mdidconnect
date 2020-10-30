<?php
	session_start();
	session_unset();
	//echo "<script>alert('You have successfully signed out!'); window.location='index.php';</script>"
	header("Location: index.php");
?>