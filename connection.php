<?php

$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn) {
  echo "Connection Success";
} else {
  echo "Connection Failed";
}

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

?>