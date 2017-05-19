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
        $dbs = $db->prepare("SELECT emailadres FROM Gebruikers WHERE gebruikersnaam = ? ");
        $dbs->execute(array($gebruikersnaam));
        $result = $dbs->fetchAll()[0];

        $to = $result[0];
        $subject = 'Je account is geblokkeerd';
        $headers = "From: " .'noreply@iproject2.icasites.nl'. "\r\n";
        $headers .= "Content-Type: text/html;\r\n";
        $message = '
        <html>
        <body style="margin: 0; padding: 0;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td style="padding: 10px 0 20px 0;">
                        <table align="center" cellpadding="0" cellspacing="0" width="600" style="border: 1px solid #cccccc;">
                            <tr>
                                <td align="center" bgcolor="#5484a4" style="padding: 30px 0 20px 0;">
                                    <h1 style="font-family: '.'Varela Round'.', sans-serif; color:#FFFFFF;">Eenmaal Andermaal</h1>
                                    <img src="http://iproject2.icasites.nl/images/logo.png" alt="Eenmaal Andermaal" width="300" height="230" style="display: block;"/>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" bgcolor="#FFFFFF" style="padding: 40px 30px 40px 30px;">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: '.'Varela Round'.', sans-serif;">
                                        <tr>
                                            <td style="color:#023042">
                                                Beste '.$gebruikersnaam.',
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 20px 0 0 0; color:#023042">
                                                <p>Je account dat gekoppeld is met het emailadres '.$result[0].' is geblokkeerd!
                                                Ben je het hier niet mee eens, neem contact met ons op!</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 10px 0 20px 0; color:#023042">
                                                <p>Met vriendelijke groeten,</p>
                                                <p>Team EenmaalAndermaal</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" bgcolor="#5484a4" style="padding: 20px 30px 20px 30px;">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: '.'Varela Round'.', sans-serif; color:#DFDFDF;">
                                        <tr>
                                            <td width="166" valign="top">
                                                <h3>Start hier</h3>
                                                <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl">Home</a></p>
                                                <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl/login.php">Inloggen</a></p>
                                            </td>
                                            <td style="font-size: 0; line-height: 0;" width="21">
                                                &nbsp;
                                            </td>
                                            <td width="166" valign="top">
                                                <h3>Over ons</h3>
                                                <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl">Bedrijfsinformatie</a></p>
                                                <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl/pdf/voorwaarden.pdf">Algemene voorwaarden</a></p>
                                            </td>
                                            <td style="font-size: 0; line-height: 0;" width="21">
                                                &nbsp;
                                            </td>
                                            <td width="166" valign="top">
                                                <h3>Support</h3>
                                                <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl">Veelgestelde vragen</a></p>
                                                <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl">Contact</a></p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';

        mail($to,$subject,$message,$headers);
        return true;
    } catch (PDOException $e) {
        //echo "Could not block user, ".$e->getMessage();
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
            $subject = 'Je account is gedeblokkeerd';
            $headers = "From: " .'noreply@iproject2.icasites.nl'. "\r\n";
            $headers .= "Content-Type: text/html;\r\n";
            $message = '
            <html>
            <body style="margin: 0; padding: 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td style="padding: 10px 0 20px 0;">
                            <table align="center" cellpadding="0" cellspacing="0" width="600" style="border: 1px solid #cccccc;">
                                <tr>
                                    <td align="center" bgcolor="#5484a4" style="padding: 30px 0 20px 0;">
                                        <h1 style="font-family: '.'Varela Round'.', sans-serif; color:#FFFFFF;">Eenmaal Andermaal</h1>
                                        <img src="http://iproject2.icasites.nl/images/logo.png" alt="Eenmaal Andermaal" width="300" height="230" style="display: block;"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" bgcolor="#FFFFFF" style="padding: 40px 30px 40px 30px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: '.'Varela Round'.', sans-serif;">
                                            <tr>
                                                <td style="color:#023042">
                                                    Beste '.$gebruikersnaam.',
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 20px 0 0 0; color:#023042">
                                                    Je account is gedeblokkeerd.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 20px 0 0 0; color:#023042">
                                                    Om je account weer te kunnen gebruiken moet je deze opnieuw activeren door op onderstaande link te klikken.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 20px 0 0 0; color:#023042">
                                                    <p>Het account met het volgende e-mailadres is gedeblokkeerd:</p>
                                                    <p>E-mailadres: '.$result[0].'</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 0 0 0; color:#023042">
                                                    <p>Klik op deze link om je account te activeren:</p>
                                                    <p>http://iproject2.icasites.nl/verify.php?gebruikersnaam='.$gebruikersnaam.'&code='.$code.' </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 0 20px 0; color:#023042">
                                                    <p>Met vriendelijke groeten,</p>
                                                    <p>Team EenmaalAndermaal</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" bgcolor="#5484a4" style="padding: 20px 30px 20px 30px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: '.'Varela Round'.', sans-serif; color:#DFDFDF;">
                                            <tr>
                                                <td width="166" valign="top">
                                                    <h3>Start hier</h3>
                                                    <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl">Home</a></p>
                                                    <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl/login.php">Inloggen</a></p>
                                                </td>
                                                <td style="font-size: 0; line-height: 0;" width="21">
                                                    &nbsp;
                                                </td>
                                                <td width="166" valign="top">
                                                    <h3>Over ons</h3>
                                                    <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl">Bedrijfsinformatie</a></p>
                                                    <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl/pdf/voorwaarden.pdf">Algemene voorwaarden</a></p>
                                                </td>
                                                <td style="font-size: 0; line-height: 0;" width="21">
                                                    &nbsp;
                                                </td>
                                                <td width="166" valign="top">
                                                    <h3>Support</h3>
                                                    <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl">Veelgestelde vragen</a></p>
                                                    <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl">Contact</a></p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>';

            mail($to,$subject,$message,$headers);
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
      //var_dump($e);
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
      $to = 'otisvdm@hotmail.com';
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
      biografie=?,
      land=?

      WHERE gebruikersnaam = ?;

      UPDATE Gebruikerstelefoon SET
      telefoonnummer =?

      WHERE gebruikersnaam = ?");

      $dbs->execute(array($data['p_firstname'],$data['p_lastname'],$data['p_adres'],
      $data['p_zipcode'],$data['p_city'],$data['p_birthmonth'].'-'.$data['p_birthday'].'-'.$data['p_birthyear'],
      $data['p_email'],htmlspecialchars($data['p_biografie']),$data['p_land'],$data['p_username'],$data['p_tel'],$data['p_username']));


      return true;
  } catch (PDOException $e) {
      //var_dump($e);
      return false;
  }
}

function update_wachtwoord($data,$db){
  try {
      $dbs = $db->prepare(" UPDATE Gebruikers SET
      wachtwoord=?

      WHERE gebruikersnaam = ?");

      $dbs->execute(array(password_hash($data['confirmpass'], PASSWORD_DEFAULT),$data['p_username']));

      return true;
  } catch (PDOException $e) {
      //var_dump($e);
      return false;
  }
}

function update_emailadres($data,$code,$db){


  try {
    $dbs = $db->prepare(" SELECT gebruikersnaam FROM Activatiecodes WHERE gebruikersnaam = ? ");
    $dbs->execute(array($data['p_username']));
    $data2=array('gebruikersnaam' => $data['p_username'],'verificatiecode' => $code, 'email' => $data["confirmmail"]);

    $result  = $dbs->fetchAll();

    PRINT "gein";
    if(count($result)==0) {
      create_verification_for_user($data2,$db);
      echo "if";
    }else{
      echo "else";
      update_verification_for_user($data2,$db);
    }
      $dbs = $db->prepare(" UPDATE Activatiecodes SET
      emailadres=? WHERE gebruikersnaam = ?");
      $dbs->execute(array($data["confirmmail"], $data['p_username']));
      return true;
  } catch (PDOException $e) {
      return false;
  }
}

function send_message($data) {
  //header("Location: google.com");
  echo "lol";

}
?>
