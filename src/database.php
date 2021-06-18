<?php

$mysql_hostname = "localhost";
$mysql_username = "wcss_peerC";
$mysql_password = "7s9girx2dJi9YKT3";
$mysql_database = "wcss_peerC";
$dsn = "mysql:host=".$mysql_hostname.";dbname=".$mysql_database;

$debug = false;
try
{
	$pdo= new PDO($dsn, $mysql_username,$mysql_password, array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}
catch (PDOException $e)
{
	echo 'PDO error: could not connect to DB, error: '.$e;
}
