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

function block_user($gebruikersnaam,$db) {
    try {
        $dbs = $db->prepare(" UPDATE Gebruikers SET statusID = '3' WHERE gebruikersnaam = ? ");
        $dbs->execute(array($gebruikersnaam));
        return true;
    } catch (PDOException $e) {
        echo "Could not block user, ".$e->getMessage();
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
function create_verification_for_user($data,$db){
  try {
      $dbs = $db->prepare("SELECT COUNT(gebruikersnaam) as count FROM Activatiecodes WHERE gebruikersnaam=?");
      $dbs->execute(array($data['gebruikersnaam']));
      $count = $dbs->fetchAll()[0]['count'];

      if($count == 0){
        $dbs = $db->prepare("INSERT INTO Activatiecodes (gebruikersnaam,activatiecode) VALUES (?,?)");
        $dbs->execute(array($data['gebruikersnaam'],$data['verificatiecode']));

        return $data['verificatiecode'];
      }

      return 0;
  } catch (PDOException $e) {
      return 0;
  }
}
function update_user($data,$db){
  try {
      $dbs = $db->prepare(" UPDATE Gebruikers SET (gebruikersnaam,voornaam,achternaam,adresregel1,postcode,plaatsnaam,geboortedatum,emailadres,wachtwoord,vraag,antwoordtekst) WHERE emailadres=$email
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
?>
