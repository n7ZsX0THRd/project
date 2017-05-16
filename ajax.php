<?php
  include 'php/database.php';
  pdo_connect();
  include 'php/user.php';

  if(isUserBeheerder($db) == false){
    header("Location: index.php");
  }

  if($_POST['action'][0] == 'block') {
    if((block_user($_POST['action'][1]))==1) {
      echo 'Gebruiker geblokkeerd';
    }
  }
  if($_POST['action'][0] == 'unBlock') {
    if((unblock_user($_POST['action'][1]))==1) {
      echo 'Gebruiker gedeblokkeerd';
    }
  }
?>
