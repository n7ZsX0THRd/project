<?php
/*
  iProject Groep 2
  07-06-2017

  file: gewonnnen_veilingen.php
  purpose:
    Send mail to all auction winners and auction owners
*/
session_start();

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

$_SESSION['menu']['sub'] = 'bp';
// Set session for sidebar, this will make sure the title = 'Beheerpanel' is highlighted
include_once ('php/database.php');
include_once ('php/user.php');
include_once ('php/mail.php');
pdo_connect();
// Include database, and include user functions.
// Connect to database

// If user is not loggedIn AND administrator redirect to homepage
if(isUserBeheerder($db) == false){
  header("Location: index.php");
}

$data = $db->query("SELECT Voorwerp.titel , Voorwerp.voorwerpnummer, Voorwerp.verkoper, Bod.bodbedrag, Bod.gebruiker, Gebruikers.emailadres AS koperMail, Gebruikers.gebruikersnaam as koper, (select emailadres from Gebruikers where gebruikersnaam=Voorwerp.verkoper) AS verkoperMail
                        FROM Voorwerp
                        INNER JOIN Bod
                        ON Voorwerp.voorwerpnummer = Bod.voorwerpnummer
                        INNER JOIN Gebruikers
                            ON Gebruikers.gebruikersnaam= Bod.gebruiker
                        WHERE veilinggesloten=1 and gemaild = 0 and bodbedrag = (SELECT MAX(bodbedrag) FROM Bod
                                WHERE  Voorwerp.voorwerpnummer=voorwerpnummer );");

while ($row = $data->fetch()){
    $koper= "$row[koper]";
    $koperMail = "$row[koperMail]";
    $veiling = "$row[titel]";
    $bedrag = "$row[bodbedrag]";
	$verkoper= "$row[verkoper]";
    $verkoperMail  = "$row[verkoperMail]";
	$nummer = "$row[voorwerpnummer]";
    $verkoperSubject = "Veiling ". $veiling ." is beëindigd";
    $koperSubject = "Veiling ". $veiling ."  heeft u gewonnen!";
	$verkoperContent = '
	                          <tr>
                              <td align="center" bgcolor="#FFFFFF" style="padding: 40px 30px 40px 30px;">
                                  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: '.'Varela Round'.', sans-serif;">
                                      <tr>
                                          <td style="color:#023042">
                                              Beste '.$verkoper.',
                                          </td>
                                      </tr>
                                      <tr>
                                          <td style="padding: 20px 0 0 0; color:#023042">
                                              <p>Uw veiling <a href="http://iproject2.icasites.nl/veiling.php?voorwerpnummer=.'$nummer.'" target="_top">'.$voorwerp.'</a> is succesvol beëindigd en verkocht aan: ".$koper."</p>
                                              <p>De veiling is verkocht voor: '.$bedrag.'</p>
                                              <p>U kunt de koper benaderen via het volgende emailadres: <a href="mailto:'.$koperMail.'" target="_top">'.$koperMail.'</a></p>
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
                          </tr> ';
    $koperContent = '
	                          <tr>
                              <td align="center" bgcolor="#FFFFFF" style="padding: 40px 30px 40px 30px;">
                                  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: '.'Varela Round'.', sans-serif;">
                                      <tr>
                                          <td style="color:#023042">
                                              Beste '.$koper.',
                                          </td>
                                      </tr>
                                      <tr>
                                          <td style="padding: 20px 0 0 0; color:#023042">
                                              <p>U heeft de veiling: <a href="http://iproject2.icasites.nl/veiling.php?voorwerpnummer=.'$nummer.'" target="_top">'.$voorwerp.' gewonnen voor '.$bedrag.'</p>
                                              <p>U kunt de verkoper benaderen via het volgende emailadres: <a href="mailto:'.$verkoperMail.'" target="_top">'.$verkoperMail.'</a></p>
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
                          </tr> ';
    sendMail($koperMail,$koperSubject,$koperContent);
    if (!endsWith($verkoperMail, 'mail.mail')){
        sendMail($verkoperMail,$verkoperSubject,$verkoperContent);
    }
}

$data = $db->query("    UPDATE Voorwerp
                        SET gemaild = 1
                        WHERE veilinggesloten=1
                        ");

header("Location: gebruikers.php?mail");

?>
