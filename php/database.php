<?php
/*
  iProject Groep 2
  30-05-2017

  file: database.php
  purpose:
    Database functions
*/
include_once ('mail.php');

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
        $dbs = $db->prepare(" UPDATE Gebruikers
								SET statusID = '3'
								WHERE gebruikersnaam = ?;
							DELETE FROM Activatiecodes
								WHERE gebruikersnaam=?
								");
        $dbs->execute(array($gebruikersnaam,$gebruikersnaam,$gebruikersnaam));
        $dbs = $db->prepare("SELECT emailadres FROM Gebruikers WHERE gebruikersnaam = ? ");
        $dbs->execute(array($gebruikersnaam));
        $result = $dbs->fetchAll()[0];
        // Block user Query

        $to = $result[0];
        $subject = 'Je account is geblokkeerd';
        $message = '
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
        </tr>';
        sendMail($to,$subject,$message);
		//SendMail to user
		DisableAllAuctions ($gebruikersnaam);
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
        $dbs->execute(array($gebruikersnaam, $gebruikersnaam));
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
            $message = '
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
                                <p><a href="http://iproject2.icasites.nl/verify.php?gebruikersnaam='.$gebruikersnaam.'&code='.$code.'">http://iproject2.icasites.nl/verify.php?gebruikersnaam='.$gebruikersnaam.'&code='.$code.'</a></p>
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
            </tr>';
            sendMail($to,$subject,$message);
            //Sendmail to user
        }
        return true;
    } catch (PDOException $e) {
        //$to = 'guusbouw@hotmail.com';
        //$subject = "Je account is gedeblokkeerd";
        //$message= $e;
        //$headers = 'From: noreply@iproject2.icasites.nl' . "\r\n";
        //mail($to, $subject, $message, $headers);
        return false;
    }
}
function create_user($data,$db){ //db is global!!
    global $db;
  try {
      $dbs = $db->prepare("INSERT INTO Gebruikers (gebruikersnaam,voornaam,achternaam,adresregel1,adresregel2,postcode,plaatsnaam,geboortedatum,emailadres,wachtwoord,vraag,antwoordtekst)
      VALUES (?,?,?,?,?,?,?,?,?,?,?,?); INSERT INTO Gebruikerstelefoon (gebruikersnaam,telefoonnummer) VALUES (?,?)");

      $dbs->execute(array(
          htmlspecialchars($data['r_username']),
          htmlspecialchars($data['r_firstname']),
          htmlspecialchars($data['r_lastname']),
          htmlspecialchars($data['r_street_name']).' '.htmlspecialchars($data['r_street_nr']).' '.htmlspecialchars($data['r_street_addition']),
          ($data['adresregel2'] !== null) ? htmlspecialchars($data['r_adressregel2']) : null ,
          htmlspecialchars($data['r_zipcode']),
          htmlspecialchars($data['r_city']),
          htmlspecialchars($data['r_birthmonth']).'-'.htmlspecialchars($data['r_birthday']).'-'.htmlspecialchars($data['r_birthyear']),
          htmlspecialchars($data['r_email']),
          password_hash($data['r_password'], PASSWORD_DEFAULT),
          $data['r_secret_question'],
          password_hash($data['r_secret_question_answer'], PASSWORD_DEFAULT),
          htmlspecialchars($data['r_username']),
          htmlspecialchars($data['r_phonenumber'])
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
      /*$to = 'otisvdm@hotmail.com';
                $subject = "PDOexception eenmaalandermaal";
                $message= '
                '.$e.'
                ';
                $headers = 'From: noreply@iproject2.icasites.nl' . "\r\n";
                mail($to, $subject, $message, $headers);*/
      return 0;
  }
}


function update_user($data,$db){  //db is global!!

  try {
      $dbs = $db->prepare(" UPDATE Gebruikers SET
      voornaam=?,
      achternaam =?,
      adresregel1=?,
      adresregel2=?,
      postcode=?,
      plaatsnaam=?,
      geboortedatum=?,
      biografie=?,
      land=?

      WHERE gebruikersnaam = ?");

      $dbs->execute(array($data['p_firstname'],$data['p_lastname'],$data['p_adres'],$data['p_adres2'],
      $data['p_zipcode'],$data['p_city'],$data['p_birthmonth'].'-'.$data['p_birthday'].'-'.$data['p_birthyear'],
      htmlspecialchars($data['p_biografie']),$data['p_land'],$data['p_username']));

      try{
        $dbs = $db->prepare("DELETE FROM Gebruikerstelefoon WHERE gebruikersnaam = ?");
        $dbs->execute(array($data['p_username']));

        foreach($data['p_phonenumbers'] as $value){
          $dbs = $db->prepare("INSERT INTO Gebruikerstelefoon(gebruikersnaam,telefoonnummer) VALUES(?,?)");
          $dbs->execute(array($data['p_username'],$value));
        }

        return true;
      }catch(PDOException $e){
        return false;
      }

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
    $data2=array('gebruikersnaam' => $data['p_username'],'verificatiecode' => $code, 'email' => $_SESSION['email']);

    $result  = $dbs->fetchAll();

    if(count($result)==0) {
      if(create_verification_for_user($data2,$db)) {
        $dbs = $db->prepare(" UPDATE Activatiecodes SET
        emailadres=? WHERE gebruikersnaam = ?");
        $dbs->execute(array($data["confirmmail"], $data['p_username']));
        return true;
      }
    }else{
      if(update_verification_for_user($data2,$db)) {
        $dbs = $db->prepare(" UPDATE Activatiecodes SET
        emailadres=? WHERE gebruikersnaam = ?");
        $dbs->execute(array($data["confirmmail"], $data['p_username']));
        return true;
      }
    }
  } catch (PDOException $e) {
      return false;
  }
}

function unique_mail($mail,$db) {
  $dbs = $db->prepare(" SELECT gebruikersnaam FROM gebruikers WHERE emailadres = ?;SELECT gebruikersnaam FROM Activatiecodes WHERE emailadres = ? ");
  $dbs->execute(array($mail,$mail));
  $result  = $dbs->fetchAll();
  if(count($result) == 0) {
    return true;
  }
  else {
    return false;
  }
}


function change_rubrieknaam($rubriek_naam, $rubriek_nummer){
            global $db;

            $data = $db->prepare("  UPDATE Rubriek
                                    SET rubrieknaam = ?
                                    WHERE rubrieknummer = ?;
                                                            ");
            $data->execute(array($rubriek_naam, $rubriek_nummer));
}

function change_rubriek_status($rubriek_status, $rubriek_nummer){
            global $db;

            $rubriek_status != $rubriek_status; // true to false and false to true

            $data = $db->prepare("  UPDATE Rubriek
                                    SET inactief = ?
                                    WHERE rubrieknummer = ?;
                                                                                            ");
            $data->execute(array($rubriek_status, $rubriek_nummer));
}

function swap_rubriek_volgnr($volgnr_A, $volgnr_B, $rubriek_nummer_A, $rubriek_nummer_B){

            $data = $db->prepare("  UPDATE Rubriek
                                    SET  volgnr = ?
                                    WHERE rubrieknummer = ?;
                                                            ");
            $data->execute(array($volgnr_B, $rubriek_nummer_A));

            $data = $db->prepare("  UPDATE Rubriek
                                    SET  volgnr = ?
                                    WHERE rubrieknummer = ?;
                                                            ");
            $data->execute(array($volgnr_A, $rubriek_nummer_B));
}
function create_auction($data,$db){  //db is global!!
  //var_dump($data);
  try {
      $allowedTags = '<br><p><h1><h2><h3><h4><h5><h6><ul><li><ol><span><b><i><strong><small><mark><em><ins><sub><sup><del>';

      $dbs = $db->prepare("EXEC dbo.InsertVeiling
	@titel = ?,
	@beschrijving = ?,
	@startprijs = ?,
	@looptijd = ?,
	@verzendkosten = ?,
	@verzendinstructie = ?,
	@betalingswijze = ?,
	@betalingsinstructie = ?,
	@postcode = ?,
	@plaatsnaam = ?,
	@land = ?,
	@verkoper = ?,
	@rubriekList = ?,
	@foto1 = ?,
	@foto2 = ?,
	@foto3 = ?,
	@foto4 = ?");
      $dbs->execute(array(
          htmlspecialchars($data['vt_title']),
          strip_tags($data['vt_description'],$allowedTags),
          (float)$data['vt_startPrice'],
          $data['vt_auctionTime'],
          (float)$data['vt_send'],
          htmlspecialchars($data['vt_sendInstructions']),
          (int)$data['vt_payment'],
          htmlspecialchars($data['vt_paymentInstruction']),
          htmlspecialchars($data['vt_zipcode']),
          htmlspecialchars($data['vt_city']),
          $data['vt_country'],
          $data['vt_seller'],
          implode(",",$data['vt_rubrieken']),
          $data['vt_images'][0],
          $data['vt_images'][1],
          $data['vt_images'][2],
          $data['vt_images'][3]
        ));
      $count = $dbs->fetchAll()[0]['count'];

  } catch (PDOException $e) {
      return $e;
  }
}
function DisableAllAuctions ($gebruikersnaam) {
	try {
		$dbs = $db->prepare("UPDATE Voorwerp
								SET inactief = 1
                  WHERE verkoper = ? AND veilinggesloten = 0
							SELECT voorwerpnummer
								FROM Voorwerp
								WHERE verkoper = ? AND veilinggesloten = 0");
        $dbs->execute(array($gebruikersnaam, $gebruikersnaam));
        $result = $dbs->fetchAll();
		foreach($result as $voorwerpnummer) {
			AuctionDisabledBiddersMail ($voorwerpnummer['voorwerpnummer']);
		}
	}
	catch (PDOException $e) {
      //var_dump($e);
      return false;
  }

}
function AuctionDisabledBiddersMail ($voorwerpnummer) {
	try {
		$dbs = $db->prepare("SELECT DISTINCT G.voornaam, G.emailadres, V.titel
								FROM Voorwerp AS V
								INNER JOIN Bod AS B ON
								V.voorwerpnummer=B.voorwerpnummer
								INNER JOIN Gebruikers AS G ON
								B.gebruiker=G.gebruikersnaam
								WHERE V.voorwerpnummer = ? AND v.veilinggesloten = 0");
        $dbs->execute(array($voorwerpnummer));
        $result = $dbs->fetchAll();
        // Block user Query
		$i = 0;
		while ($i < count($result)) {
			$voornaam = $result[$i][0];
			$emailadres = $result[$i][1];
			$titel = $result[$i][2];
			echo $voornaam;
			echo $emailadres;
			$i++;
			$to = $emailadres;
			$subject = 'Veiling gestopt';
			$message = '
			<tr>
				<td align="center" bgcolor="#FFFFFF" style="padding: 40px 30px 40px 30px;">
					<table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: '.'Varela Round'.', sans-serif;">
						<tr>
							<td style="color:#023042">
								Beste '.$voornaam.',
							</td>
						</tr>
						<tr>
							<td style="padding: 20px 0 0 0; color:#023042">
								<p>De veiling met de titel:'.$titel.' is beëindigd door EenmaalAndermaal.
								Hierdoor is geen helaas geen winnaar.</p>
								<p> Wij hopen u via de email voldoende op de hoogte te hebben gesteld!</p>
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
			</tr>';
			sendMail($to,$subject,$message);
			return true;
		}
	}
		catch (PDOException $e) {
      //var_dump($e);
      return false;
  }
}

?>
