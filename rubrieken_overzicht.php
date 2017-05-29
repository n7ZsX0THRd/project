<?php
session_start();

include ('php/database.php');
include ('php/user.php');
pdo_connect();

$beheerder = false;
if(isUserBeheerder($db)) {
    $beheerder = true;
} else {
    $beheerder = false;
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php include 'php/includes/default_header.php'; ?>
        <title>Rubrieken overzicht - Eenmaal Andermaal</title>
        <link href="css/rubriek_overzicht.css" rel="stylesheet">
        
  </head>

  <body>

    <?php include 'php/includes/header.php' ?>
        <main class="container">
            <?php
            global $db;
            $data = $db->query("SELECT *
                                FROM Rubriek
                                WHERE parentRubriek = -1 or parentRubriek IN (
                                                                SELECT rubrieknummer
                                                                FROM Rubriek
                                                                WHERE parentRubriek = -1 )
                                ORDER BY parentRubriek ASC, volgnr ASC, rubrieknaam ASC");
                                /*
                while ($row = $data->fetch()){
                    echo "$row[rubrieknaam]</br>";
                }*/

                $result = $data->fetchAll();
                $count=count($result);
                //print_r($result);
                $rubrieken = [];
                for ($row = 0; $row < $count; $row++) {
                    //echo "<p><b>Row number $row</b></p>";
                    if ($result[$row]['parentRubriek']==-1){ //hoofdrubriek
                        $nummer = $result[$row]['rubrieknummer'];
                        $rubrieken[$nummer][] = $result[$row]['rubrieknaam'];
                        //echo "<li>".$result[$row]['rubrieknaam']."</li>";
                    } else { //subruriek
                        $parentNummer = $result[$row]['parentRubriek'];
                        $rubriekNummer = $result[$row]['rubrieknummer'];
                        
                        $rubriekNaam = $result[$row]['rubrieknaam'];
                        $volgnr = $result[$row]['volgnr'];
                        
                        $rubriek_gegevens['volgnr']= $volgnr;
                        $rubriek_gegevens['rubrieknaam'] = $rubriekNaam;
                        
                        $rubrieken[$parentNummer][$rubriekNummer] = $rubriek_gegevens;
                    }
                }

                ?>
                <nav aria-label="Page navigation example">
                    <ul id="sticky" class="pagination">
                    <?php
                        foreach(range('A','Z') as $char) {
                            $nRubrieken = 0;
                            foreach($rubrieken as $rubriek){
                                $firstChar =substr ($rubriek[0] , 0 , 1 );
                                if ($char==$firstChar){
                                    $nRubrieken ++;
                                    if($nRubrieken==1){
                                        echo '<li class="page-item"><a class="page-link" href="#'.$char.'">'.$char.'</a></li>';
                                    }
                                }
                            }
                            if($nRubrieken==0){
                                echo '<li class="page-item"><a class="page-nolink">'.$char.'</a></li>';
                            }
                        }

                    ?>
                    </ul>
                </nav>
                <?php
                foreach(range('A','Z') as $char) {
                    $nRubrieken = 0;
                    foreach($rubrieken as $rubriek){
                        $firstChar =substr ($rubriek[0] , 0 , 1 );
                        if ($char==$firstChar){
                            $nRubrieken ++;
                            if($nRubrieken==1){
                                echo '<section  class="row rubriek_column">
                                  <a class="anchor" id="'.$char.'"></a>';
                                echo '<h1 class="rubriek_char">'.$char.'</h1>';
                            }
                            echo '<article class="col-md-4">';
                            echo '<h2>'; 
                            if($beheerder) {
                                ?>
               
                                    <form class="btn-group" action="php/functies/change_rubrieknaam.php" method="POST">
                                        <input type="hidden" name="rubriek_nummer" value="<?php $rubriek_nummer ?>" >
                                        <input type="hidden" name="rubriek_naam" value="<?php $rubriek_naam ?>"  style="display: none">
                                        <button type="submit" class="btn btn-secondary glyphicon glyphicon-edit"></button>
                                    </form>
                                    <form class="btn-group inline" action="php/functies/change_rubriek_status.php" method="POST">
                                        <input type="hidden" name="rubriek_nummer" value="<?php $rubriek_nummer ?>" >
                                        <input type="hidden" name="rubriek_status" value="<?php $rubriek_status ?>" >
                                        <button type="submit" class="btn btn-secondary glyphicon glyphicon-ban-circle"></button>
                                    </form>
                              
                                <?php
                            }
                            echo ''.$rubriek[0].'</h2>';
                            echo '<ul class="list-unstyled">';
                            foreach($rubriek as $key => $subRubriek){
                                if (!$key==0){
                                    echo '<li>';
                                    if($beheerder) {
                                      if($subRubriek['volgnr'] != 1) {
                                          ?> 
                                          
                                          <form class="btn-group" action="php/functies/swap_rubriek_volgnr.php" method="POST">
                                                <input type="hidden" name="volgnr_A" value="<?php $volgnr_A ?>" >
                                                <input type="hidden" name="volgnr_B" value="<?php $volgnr_B ?>" >
                                                <input type="hidden" name="rubriek_nummer_A" value="<?php $rubriek_nummer_A ?>" >
                                                <input type="hidden" name="rubriek_nummer_B" value="<?php $rubriek_nummer_B ?>" >
                                                <button type="submit" class="btn btn-secondary glyphicon glyphicon-chevron-up"></button>
                                            </form>
                                        <?php
                                      }
                                      if((count($rubriek) - 1) != $subRubriek['volgnr']){
                                          $volgnr_A = $subRubriek['volgnr'];
                                          $volgnr_B = ($subRubriek['volgnr'] + 1);
                                          $rubriek_nummer_A=$key;
                                          
                                          global $db;
                                          $data = $db->prepare("SELECT rubrieknummer
                                  FROM Rubriek 
                                  WHERE volgnr = ? AND parentRubriek = (SELECT parentRubriek 
                                                                        FROM Rubriek 
                                                                        WHERE rubrieknummer = ?)");
                                            $data->execute(array($volgnr_B, $rubriek_nummer_A));
                                            $results= $data->fetchAll();
                                            $rubriek_nummer_B= $results[0]['rubrieknummer'];
                                          
                                          
        
                                        ?> 
                                            <form class="btn-group" action="php/functies/swap_rubriek_volgnr.php" method="POST">
                                                <input type="hidden" name="volgnr_A" value="<?php $volgnr_A ?>" >
                                                <input type="hidden" name="volgnr_B" value="<?php $volgnr_B ?>" >
                                                <input type="hidden" name="rubriek_nummer_A" value="<?php $rubriek_nummer_A ?>" >
                                                <input type="hidden" name="rubriek_nummer_B" value="<?php $rubriek_nummer_B ?>" >
                                                <button type="submit" class="btn btn-secondary glyphicon glyphicon-chevron-down"></button>
                                            </form>
                                        <?php
                                      }
                                        ?> 
                                            <form class="btn-group" action="php/functies/change_rubrieknaam.php" method="POST">
                                                <input type="hidden" name="rubriek_nummer" value="<?php $rubriek_nummer ?>" >
                                                <input type="hidden" name="rubriek_naam" value="<?php $rubriek_naam ?>"  >
                                                <button type="submit" class="btn btn-secondary glyphicon glyphicon-edit"></button>
                                            </form>
                                            <form class="btn-group inline" action="php/functies/change_rubriek_status.php" method="POST">
                                                <input type="hidden" name="rubriek_nummer" value="<?php $rubriek_nummer ?>" >
                                                <input type="hidden" name="rubriek_status" value="<?php $rubriek_status ?>" >
                                                <button type="submit" class="btn btn-secondary glyphicon glyphicon-ban-circle"></button>
                                            </form>                            
                                        <?php
                                    }
                                    echo '<a href="rubriek.php?rubriek='.$key.'">'.$subRubriek['rubrieknaam'].'</a>';
                                    echo '</li>';
                                }
                            }
                            echo '</ul>';
                            echo '</article>';
                        }
                    }
                    if ($nRubrieken!=0){
                        echo'<div class="col-md-12">';
                        echo '<hr>';
                        echo '</div>';
                        echo '</section>';
                    }
                }

            ?>
        </main>


    <?php include 'php/includes/footer.php' ?>


    <script src="js/jquery.drilldown.min.js"></script>
    <script>
      $('.drilldown').drilldown();
    </script>
    <script src="js/jquery.sticky.js"></script>
    <script>
    $(document).ready(function(){
      $("#sticky").sticky({topSpacing:70});
    });
    </script>
  </body>
</html>
