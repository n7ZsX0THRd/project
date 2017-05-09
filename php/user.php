<?php

function isUserLoggedIn(){

  if(isset($_SESSION['email']) && !empty($_SESSION['email'])){
    return true;
  }
  return false;
}
function getLoggedInUser($db){
  if(isUserLoggedIn())
  {
    $dbs = $db->prepare("SELECT TOP(1) gebruikersnaam,voornaam,achternaam,adresregel1,postcode,plaatsnaam,land,geboortedatum,emailadres,typegebruiker,statusID FROM Gebruikers WHERE emailadres=?");
    $dbs->execute(array($_SESSION['email']));
    $result = $dbs->fetchAll();
    return $result[0];
  }
  return null;
}

?>
