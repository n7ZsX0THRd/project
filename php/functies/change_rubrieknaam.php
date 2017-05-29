<? // sets a new name for the given rubriek (number)
if (!empty($_POST))
    {
    $rubriek_naam=htmlspecialchars($_POST["rubriek_naam"]);
    $$rubriek_nummer=htmlspecialchars($_POST["rubriek_nummer"]);
    
    global $db;

    $data = $db->prepare("  UPDATE Rubriek
                            SET rubrieknaam = ?
                            WHERE rubrieknummer = ?;   
                                                    ");
    $data->execute(array($rubriek_naam, $rubriek_nummer));
}

?>