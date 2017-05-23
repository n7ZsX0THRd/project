<? 
    // Insert a rubriek into the db
    if (!empty($_POST)){
        if(!empty($_POST['rubriek_naam'])){

            $rubriek_naam=$_POST['rubriek_naam'];

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


            $data = $db->prepare("  INSERT INTO Rubriek (rubrieknaam, parentRubriek, volgnr)
                                    VALUES (?, ?, ?);  
                                                                                            ");
            $data->execute(array($rubriek_naam, $parent_rubriek, $volgnr));
        }
    }

?>