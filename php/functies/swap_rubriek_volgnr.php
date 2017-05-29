<?php  // swap the volgnr's  of two rubrieken
include ('../../php/database.php');
pdo_connect();


if (!empty($_POST))
    {


    $volgnr_A=htmlspecialchars($_POST["volgnr_A"]);
    $volgnr_B=htmlspecialchars($_POST["volgnr_B"]);
    $rubriek_nummer_A=htmlspecialchars($_POST["rubriek_nummer_A"]);
    $rubriek_nummer_B=htmlspecialchars($_POST["rubriek_nummer_B"]);
    
    global $db;
            
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

// go back to where you came from
$previous = $_SERVER['HTTP_REFERER'];
header("Location: ".$previous);

?>