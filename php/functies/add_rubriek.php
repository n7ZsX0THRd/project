<?php
/*
  iProject Groep 2
  30-05-2017

  file: add_rubriek.php
  purpose:
  POST function to add rubriek
*/
// add a new rubriek to a parent rubriek,
//if there's no other sub rubriek in the parent rubriek it will make a new rubriek overig
include_once ('../../php/database.php');
pdo_connect();
// Include database and with database

if (!empty($_POST))
    {
    $rubriek_naam=htmlspecialchars($_POST["rubriek_naam"]);
    $rubriek_parentNummer=htmlspecialchars($_POST["rubriek_parentNummer"]);

    global $db;

    $data = $db->prepare("  SELECT TOP 1 volgnr
                            FROM Rubriek WHERE parentRubriek = ?
                            ORDER BY volgnr DESC");
                                $data->execute(array($rubriek_parentNummer));
                                $result=$data->fetchAll();

    if($data->rowCount() > 0)
    {
        // row exists. add a new sub rubriek
        $volgnr = ($result[0]['volgnr'] + 1); // add it to the bottom
        $data = $db->prepare("  INSERT INTO Rubriek (rubrieknaam, parentRubriek, volgnr)
                                VALUES (?,  ?, ?);");
                                $data->execute(array($rubriek_naam, $rubriek_parentNummer, $volgnr));

    } else {
        // there are no sub rubbriek
        // So make a overig rubriek and and insert all the auctions in to that
        $data = $db->prepare("  INSERT INTO Rubriek (rubrieknaam, parentRubriek, volgnr)
                                VALUES (?,  ?, 1)
                                VALUES ('Overige veiligen',  ?, 2);");
                                $data->execute(array($rubriek_naam, $rubriek_parentNummer, $rubriek_parentNummer));

        $data = $db->prepare("  SELECT TOP 1 rubrieknummer
                                FROM Rubriek
                                WHERE rubrieknaam='Overig' AND parentRubriek =  179198 AND volgnr = 0;");
                                $data->execute(array());
        $result=$data->fetchAll();
        $rubriek_nummer_overig=$result[0]['rubrieknummer'];

        $data = $db->prepare("  UPDATE VoorwerpInRubriek
                                SET rubrieknummer =  179203
                                WHERE rubrieknummer = 4352;");
                                $data->execute(array($rubriek_naam, $rubriek_parentNummer, $rubriek_parentNummer));
        $result=$data->fetchAll();
    }
}

// go back to where you came from
$previous = $_SERVER['HTTP_REFERER'];
header("Location:".$previous.'"');


?>
