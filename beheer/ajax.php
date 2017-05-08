<?php
  include('../php/database.php');
  pdo_connect();

  if($_POST['action'][0] == 'delete') {
    if((delete_user($_POST['action'][1],$db))==1) {
      echo 'Gebruiker verwijderd';
    }
    else {
      echo 'Gebruiker NIET verwijderd';
    }
  }
?>
