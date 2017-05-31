<?php
/*
  iProject Groep 2
  30-05-2017

  file: user.php
  purpose:
    Functions for user control,
    getLoggedInUser
    isUserLoggedIn
    isUserBeheerder
*/

function getLoggedInUser($db){
  if(isset($_SESSION['email']) && !empty($_SESSION['email'])){
    $dbs = $db->prepare("SELECT TOP(1)
    gebruikersnaam,
    voornaam,
    achternaam,
    adresregel1,
    adresregel2,
    postcode,
    plaatsnaam,
    land,
    geboortedatum,
    emailadres,
    typegebruiker,
    statusID,
    datepart(day,[geboortedatum]) AS geboortedag,
    datepart(month,[geboortedatum]) AS geboortemaand,
    datepart(year,[geboortedatum]) AS geboortejaar,
    biografie,
    bestandsnaam
    FROM Gebruikers WHERE emailadres=?");
    $dbs->execute(array($_SESSION['email']));
    $result = $dbs->fetchAll();

    //var_dump($result);

    if(count($result) == 0)
      return null;

    return $result[0];
  }
  return null;
}

function isUserLoggedIn($db){

  if(isset($_SESSION['email']) && !empty($_SESSION['email'])){

    $data = getLoggedInUser($db);

    if($data == null)
      return false;

    if($data['emailadres'] == $_SESSION['email'] && $data['statusID'] !== '3')
    {
      return true;
    }
    else { // USER IS BLOCKED , logout user

      $_SESSION['email'] = null;
      return false;
    }

  }
  return false;
}

function isUserBeheerder($db){
  if(isset($_SESSION['email']) && !empty($_SESSION['email'])){

    $data = getLoggedInUser($db);

    if($data == null)
      return false;

    if($data['emailadres'] == $_SESSION['email'] && $data['typegebruiker'] == '3')
    {
      return true;
    }
    else { // USER IS NO beheerder \

      //header("Location: index.php"); /* Redirect browser */

      return false;
    }

  }
  return false;
}

?>
