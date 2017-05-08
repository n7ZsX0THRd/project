<?php
  include('../php/database.php');
  pdo_connect();
  echo delete_user('A_usernameB',$db);
?>
