<? 
    // Update a rubriek  in db
    if (!empty($_POST)){
        if(!empty($_POST['rubriek_naam']) &&  !empty($_POST['rubriek_nummer']) ){

            $rubriek_naam=$_POST['rubriek_naam'];
            $rubriek_nummer=$_POST['rubriek_nummer'];

            if(!empty($_POST['parent_rubriek'])){
                $parent_rubriek=$_POST['parent_rubriek'];
            } else {
                $parent_rubriek = -1;
            }

            if(!empty($_POST['volgnr'])){
                $volgnr=$_POST['volgnr'];
            } else {
                $volgnr = 0;
            }


            $data = $db->prepare("  UPDATE Rubriek
                                    SET rubrieknaam = ?, parentRubriek = ?, volgnr = ?
                                    WHERE rubrieknummer = ?;  
                                                                                            ");
            $data->execute(array($rubriek_naam, $parent_rubriek, $volgnr, $rubriek_nummer));
        }
    }

?>