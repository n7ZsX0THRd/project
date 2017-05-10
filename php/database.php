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

  } catch (PDOException $e) {
      $to = 'casper.plate@hotmail.com';
                $subject = "PDOexception eenmaalandermaal";
                $message= '
                '.$e.'
                ';
                $headers = 'From: noreply@iproject2.icasites.nl' . "\r\n";
                mail($to, $subject, $message, $headers);
      return 0;
  }
}

function update_user($data,$db){
  try {
      $dbs = $db->prepare(" UPDATE Gebruikers SET
      voornaam=?,
      achternaam =?,
      adresregel1=?,
      postcode=?,
      plaatsnaam=?,
      geboortedatum=?,
      emailadres=?,
      biografie=?,

      WHERE gebruikersnaam = ?");

      $dbs->execute(array($data['p_firstname'],$data['p_lastname'],$data['p_adres'],
      $data['p_zipcode'],$data['p_city'],$data['p_birthday'].'-'.$data['p_birthmonth'].'-'.$data['p_birthyear'],
      $data['p_email'],$data['p_biografie'],$data['p_username']));

      $dbs = $db->prepare(" UPDATE Gebruikerstelefoon SET
      p_tel =?,

      WHERE gebruikersnaam = ?"
      $dbs->execute(array($data['p_tel'],$data['p_username']));
      
      return true;
  } catch (PDOException $e) {
      return $e;
  }
}
?>
