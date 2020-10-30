<?php
if(getenv("CLEARDB_DATABASE_URL") != null) {
	$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

	$server = $url["host"];
	$username = $url["user"];
	$password = $url["pass"];
	$db = substr($url["path"], 1);
} else {
	$server = "us-cdbr-iron-east-02.cleardb.net";
	$username = "bdff93e251b1e8";
	$password = "e9441c6d";
	$db = "heroku_b19b2563ad60541";
}


$dsn = "mysql:host=".$server."; dbname=".$db."; charset:utf16_unicode_ci";
//$dsn = "mysql:host=us-cdbr-iron-east-02.cleardb.net; dbname=heroku_b19b2563ad60541; charset:utf16_unicode_ci";
//$username = "bdff93e251b1e8";
//$password = "e9441c6d";

try {
	$db = new PDO($dsn, $username, $password);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
	$error_message = $e->getMessage();
	echo $error_message;
	exit();
}
?>