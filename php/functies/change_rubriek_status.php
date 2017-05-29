<?php //Sets a rubriek who's active to inactive and vice versa
include ('../../php/database.php');
pdo_connect();


if (!empty($_POST))
    {
    $rubriek_status=htmlspecialchars($_POST["rubriek_status"]);
    $rubriek_nummer=htmlspecialchars($_POST["rubriek_nummer"]);
    echo $rubriek_status; 
    global $db;

    (bool)$rubriek_status = !(bool)$rubriek_status; // true to false and false to true

    echo $rubriek_nummer; 

    $data = $db->prepare("  UPDATE Rubriek
                            SET inactief = ?
                            WHERE rubrieknummer = ?;  
                                                                                    ");
    $data->execute(array($rubriek_status, $rubriek_nummer));

}

// go back to where you came from
$previous = $_SERVER['HTTP_REFERER'];
header("Location: ".$previous);

?>