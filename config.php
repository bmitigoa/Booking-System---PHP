<?php
// Database configuration file

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'labbooking';


$conn = new mysqli($host, $user, $password, $database);


if ($conn->connect_error) {
die('Connection failed: ' . $conn->connect_error);
}
?>