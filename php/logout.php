<?php
/*
  iProject Groep 2
  30-05-2017

  file: logout.php
  purpose:
  Logout
*/

session_start();

$_SESSION = array();// Unset session variables

session_destroy(); // Destroy the sessions

header ("Location: ../index.php"); // Redirect to homepage

?>
