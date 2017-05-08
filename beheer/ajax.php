<?php
  include('../php/database.php');
  pdo_connect();

  if($_POST['action'][0] == 'delete') {
    echo delete_user($_POST['action'][1],$db);
  }
?>
