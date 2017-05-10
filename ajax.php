<?php
  include('php/database.php');
  pdo_connect();

  if($_POST['action'][0] == 'block') {
    if((block_user($_POST['action'][1]))==1) {
      echo 'Gebruiker geblokkeerd';
    }
  }
  if($_POST['action'][0] == 'unBlock') {
    if((block_user($_POST['action'][1]))==1) {
      echo 'Gebruiker gedeblokkeerd';
    }
  }
?>
