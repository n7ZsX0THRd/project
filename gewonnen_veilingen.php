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

$data = $db->query("SELECT Voorwerp.titel , Bod.bodbedrag, Bod.gebruiker, Gebruikers.emailadres AS koperMail, Gebruikers.gebruikersnaam as koper, (select emailadres from Gebruikers where gebruikersnaam=Voorwerp.verkoper) AS verkoperMail
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
    $verkoperMail  = "$row[verkoperMail]";
    $verkoperSubject = "Veiling ". $veiling ." is beëindigd";
    $koperSubject = "Veiling ". $veiling ."  heeft u gewonnen!";
    $verkoperContent = "Beste verkoper, <br> Uw veiling is succesvol beëindigd. Het is verkocht aan: ".$koper.". Voor € ".$bedrag." ! <br> Groeten van de beste veilingsite :)";
    $koperContent = "Beste ".$koper.", <br> U heeft succesvol de veiling: " . $veiling . ". Voor &euro; ".$bedrag." gewonnen! <br> Groeten van de beste veilingsite :)";

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
