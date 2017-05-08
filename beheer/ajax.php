<?php
  include('../php/database.php');
  pdo_connect();
  echo var_dump($_POST);
  if($_POST['action'] == 'delete') {
    echo delete_user($_POST['data'],$db);
  }
?>
