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

function block_user($gebruikersnaam) {
    global $db;
    try {
        $dbs = $db->prepare(" UPDATE Gebruikers SET statusID = '3' WHERE gebruikersnaam = ? ");
        $dbs->execute(array($gebruikersnaam));
        return true;
    } catch (PDOException $e) {
        echo "Could not block user, ".$e->getMessage();
        return false;
    }
}
function unBlock_user($gebruikersnaam) {
    global $db;
    try {
        $dbs = $db->prepare(" UPDATE Gebruikers SET statusID = '1' WHERE gebruikersnaam = ?");
        $dbs->execute(array($gebruikersnaam));
        $dbs = $db->prepare("SELECT emailadres FROM Gebruikers WHERE gebruikersnaam = ? ");
        $dbs->execute(array($gebruikersnaam));
        $result = $dbs->fetchAll()[0];
        $random = rand(100000,999999);
        $dbs = $db->prepare("SELECT COUNT(gebruikersnaam) as count FROM Activatiecodes WHERE gebruikersnaam=?");
        $dbs->execute(array($gebruikersnaam));
        $count = $dbs->fetchAll()[0];
        $code = 0;
        if($count[0] == 0){
            $code = create_verification_for_user(array('gebruikersnaam' => $gebruikersnaam,'verificatiecode' => $random), $db);
        }else {
            $code = update_verification_for_user(array('email' => $result[0],'verificatiecode' => $random), $db);
        }
        if($code != 0) {
            $to = $result[0];
            $subject = "Je account is gedeblokkeerd";
            $message= '
                      Beste '.$gebruikersnaam.',
                      Je account is gedeblokkeerd. 
                      Om je account weer te kunnen gebruiken moet je deze opnieuw activeren door op onderstaande link te klikken.
                      --------------------
                      Het account met het volgende e-mailadres is gedeblokkeerd:
                      E-mailadres: '.$result[0].'
                                            
                      Nieuwe activatiecode: '.$code.'
                      --------------------
                      Klik op deze link om je account te activeren:
                      http://iproject2.icasites.nl/verify.php?gebruikersnaam='.$gebruikersnaam.'&code='.$code.'
                      '; //Bovenstaand bericht is de email die gebruikers ontvangen.
            $headers = 'From: noreply@iproject2.icasites.nl' . "\r\n";
            mail($to, $subject, $message, $headers);
        }
        return true;
    } catch (PDOException $e) {
        $to = 'guusbouw@hotmail.com';
        $subject = "Je account is gedeblokkeerd";
        $message= $e;
        $headers = 'From: noreply@iproject2.icasites.nl' . "\r\n";
        mail($to, $subject, $message, $headers);
        return false;
    }
}
function create_user($data,$db){ //db is global!!
    global $db;
  try {
      $dbs = $db->prepare("INSERT INTO Gebruikers (gebruikersnaam,voornaam,achternaam,adresregel1,postcode,plaatsnaam,geboortedatum,emailadres,wachtwoord,vraag,antwoordtekst)
      VALUES (?,?,?,?,?,?,?,?,?,?,?); INSERT INTO Gebruikerstelefoon (gebruikersnaam,telefoonnummer) VALUES (?,?)");

      $dbs->execute(array(
          $data['r_username'],
          $data['r_firstname'],
          $data['r_lastname'],
          $data['r_street_name'].' '.$data['r_street_nr'].' '.$data['r_street_addition'],
          $data['r_zipcode'],
          $data['r_city'],
          $data['r_birthmonth'].'-'.$data['r_birthday'].'-'.$data['r_birthyear'],
          $data['r_email'],
          password_hash($data['r_password'], PASSWORD_DEFAULT),
          $data['r_secret_question'],
          $data['r_secret_question_answer'],
          $data['r_username'],
          $data['r_phonenumber']
        )
      );
      return true;
  } catch (PDOException $e) {
      var_dump($e);
      return false;
  }
}
function create_verification_for_user($data,$db){  //db is global!!
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

function update_verification_for_user($data,$db){
  try {
      $dbs = $db->prepare("SELECT COUNT(Activatiecodes.gebruikersnaam) as count FROM Gebruikers INNER JOIN Activatiecodes ON Gebruikers.gebruikersnaam= Activatiecodes.gebruikersnaam WHERE Gebruikers.emailadres=?");
      $dbs->execute(array($data['email']));
      $count = $dbs->fetchAll()[0]['count'];

      if($count == 1){
        $dbs = $db->prepare("UPDATE Activatiecodes SET activatiecode =?, startdatum = GETDATE()
WHERE gebruikersnaam = (SELECT Gebruikers.gebruikersnaam
						FROM Gebruikers
						INNER JOIN Activatiecodes
						ON Gebruikers.gebruikersnaam= Activatiecodes.gebruikersnaam
						WHERE Gebruikers.emailadres = ?)");
        $dbs->execute(array($data['verificatiecode'], $data['email']));

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


function update_user($data,$db){  //db is global!!

  try {
      $dbs = $db->prepare(" UPDATE Gebruikers SET
      voornaam=?,
      achternaam =?,
      adresregel1=?,
      postcode=?,
      plaatsnaam=?,
      geboortedatum=?,
      emailadres=?,
      biografie=?

      WHERE gebruikersnaam = ?;

      UPDATE Gebruikerstelefoon SET
      telefoonnummer =?

      WHERE gebruikersnaam = ?");

      $dbs->execute(array($data['p_firstname'],$data['p_lastname'],$data['p_adres'],
      $data['p_zipcode'],$data['p_city'],$data['p_birthmonth'].'-'.$data['p_birthday'].'-'.$data['p_birthyear'],
      $data['p_email'],$data['p_biografie'],$data['p_username'],$data['p_tel'],$data['p_username']));


      return true;
  } catch (PDOException $e) {
      return $e;
  }
}

function update_wachtwoord($data,$db){
  try {
      $dbs = $db->prepare(" UPDATE Gebruikers SET
      wachtwoord=?,

      WHERE gebruikersnaam = ?");

      $dbs->execute(array($data['confirmpass']));

      return true;
  } catch (PDOException $e) {
      return $e;
  }
}
function send_message($data) {
  header("Location: google.com");
  echo "lol";

}
?>
