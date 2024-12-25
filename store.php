<?php

include_once 'connection.php';

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
  $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];

  if ($_REQUEST['REQUEST_METHOD'] === 'GET') {
    
  }
}

?>