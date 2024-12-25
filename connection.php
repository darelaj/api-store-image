<?php

$servername = "h4o0k4cogw4swg8w48040c4c";
$username = "mysql";
$password = "yap0MoMP1Ue8ctLkc8H2gANFCei7PqVNhFK0mJPwvIOu1OcWDVr12NtrFyqBlzYN";
$dbname = "default";

$conn = new mysqli($servername, $username, $password, $dbname);

if (!$conn) {
  echo "Connection Failed";
}

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

?>