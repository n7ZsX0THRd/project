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

function delete_user($gebruikersnaam) {
    try {
        $db = $pdo->prepare(" DELETE FROM Gebruikers WHERE gebruikersnaam = ? ");
        $db->execute(array($gebruikersnaam));
    } catch (PDOException $e) {
        echo "Could not delete user, ".$e->getMessage();
    }
}
function create_user($data){
  /*
  'r_username' => 'Gebruikersnaam',
  'r_firstname' => 'Voornaam',
  'r_lastname' => 'Achternaam',
  'r_birthday' => 'Geboortdag',
  'r_birthyear' => 'Geboortejaar',
  'r_birthmonth' => 'Geboortemaand',
  'r_street_name' => 'Straat',
  'r_street_nr' => 'Huisnummer',
  'r_zipcode' => 'Postcode',
  'r_phonenumber' => 'Telefoonnummer',
  'r_email' => 'Email',
  'r_email_confirm' => 'Email bevestiging',
  'r_password' => 'Wachtwoord',
  'r_password_confirm' => 'Wachtwoord bevestiging',
  'r_secret_question' => 'Geheime vraag',
  'r_secret_question_answer' => 'Geheime vraag antwoord',
  'r_terms_of_use' => 'Gebruikersvoorwaarden'
  */
  try {
      $db = $pdo->prepare(" INSERT INTO Gebruikers
      VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
      $db->execute($data);
  } catch (PDOException $e) {
      echo "Could not insert user, ".$e->getMessage();
  }
}
?>
