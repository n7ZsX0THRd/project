<?php
require_once('../database.php');

  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET)) {
      if(!empty($_GET['gebruikersnaam'])){
        $gebruikersnaam = htmlspecialchars($_GET['gebruikersnaam']);
      }
      else {
        $gebruikersnaam = '';
      }
    }
  }
  delete_user($gebruikersnaam);
?>
