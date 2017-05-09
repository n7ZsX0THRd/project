<?php

//Making a connection with the database
function pdo_connect() {
    $hostname = "mssql2.iproject.icasites.nl,1433"; //(local) for your local db
    $dbname = "iproject2"; //database name
    $username = "iproject2"; //your username
    $pw = "PHd1LgMs"; //your password

    global $db;
    $db = new PDO ("sqlsrv:Server=$hostname;Database=$dbname;ConnectionPooling=0","$username","$pw");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

function delete_user($gebruikersnaam,$db) {
    try {
        $dbs = $db->prepare(" DELETE FROM Gebruikers WHERE gebruikersnaam = ? ");
        $dbs->execute(array($gebruikersnaam));
        return true;
    } catch (PDOException $e) {
        echo "Could not delete user, ".$e->getMessage();
        return false;
    }
}
function create_user($data,$db){
  try {
      $dbs = $db->prepare(" INSERT INTO Gebruikers (gebruikersnaam,voornaam,achternaam,adresregel1,postcode,plaatsnaam,geboortedatum,emailadres,wachtwoord,vraag,antwoordtekst)
      VALUES (?,?,?,?,?,?,?,?,?,?,?)");
      $dbs->execute(array(
          $data['r_username'],
          $data['r_firstname'],
          $data['r_lastname'],
          $data['r_street_name'].' '.$data['r_street_nr'].' '.$data['r_street_addition'],
          $data['r_zipcode'],
          $data['r_city'],
          $data['r_birthday'].'-'.$data['r_birthmonth'].'-'.$data['r_birthyear'],
          $data['r_email'],
          password_hash($data['r_password'], PASSWORD_DEFAULT),
          $data['r_secret_question'],
          $data['r_secret_question_answer']
        )
      );
      return true;
  } catch (PDOException $e) {
      return $e;
  }
}
function update_user($data,$db){
  try {
      $dbs = $db->prepare(" UPDATE Gebruikers SET gebruikersnaam =,voornaam,achternaam,adresregel1,postcode,plaatsnaam,geboortedatum,emailadres,wachtwoord,vraag,antwoordtekst) WHERE emailadres=$email
      VALUES (?,?,?,?,?,?,?,?,?,?,?)");
      $dbs->execute(array(
          $data['p_username'],
          $data['p_firstname'],
          $data['p_lastname'],
          $data['p_street_name'].' '.$data['p_street_nr'].' '.$data['p_street_addition'],
          $data['p_zipcode'],
          $data['p_city'],
          $data['p_birthday'].'-'.$data['p_birthmonth'].'-'.$data['p_birthyear'],
          $data['p_email'],
          password_hash($data['r_password'], PASSWORD_DEFAULT),
        )
      );
      return true;
  } catch (PDOException $e) {
      return $e;
  }
}
?>
