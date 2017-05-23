<? 
    // Update a rubriek  in db
    if (!empty($_POST)){
        if(!empty($_POST['rubriek_volgnr']) &&  !empty($_POST['rubriek_nummer']) ){

            $rubriek_naam=$_POST['rubriek_volgnr'];
            $rubriek_nummer=$_POST['rubriek_nummer'];

            $data = $db->prepare("  UPDATE Rubriek
                                    SET  volgnr = ?
                                    WHERE rubrieknummer = ?;  
                                                                                            ");
            $data->execute(array($rubriek_volgnr, $rubriek_nummer));
        }
    }

?>