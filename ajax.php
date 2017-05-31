<?php
/*
  iProject Groep 2
  30-05-2017

  file: ajax.php
  purpose:
  Functions for Administrator to block or deblock an user.
*/
  include_once 'php/database.php';
  include_once 'php/user.php';
  pdo_connect();
  // Include database, and include user functions.
  // Connect to database

  if(isUserLoggedIn($db) == false || isUserBeheerder($db) == false){
    header("Location: index.php");
  }
  // If user not logged in or typegebruiker not equal to Administrator redirect to homepage.

  // Post action block
  if($_POST['action'][0] == 'block') {
    if((block_user($_POST['action'][1]))==1) {
      echo 'Gebruiker geblokkeerd';
    }
  }
  // Post action unblock
  if($_POST['action'][0] == 'unBlock') {
    if((unblock_user($_POST['action'][1]))==1) {
      echo 'Gebruiker gedeblokkeerd';
    }
  }
?>
