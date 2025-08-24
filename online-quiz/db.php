<?php
$host = "localhost";
$user = "root";
$pass = "root123";
$db = "quiz_db";  // database you created

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
