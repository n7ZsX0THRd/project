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
                            echo'<h2><button class="glyphicon glyphicon-edit"></button><button class="glyphicon glyphicon-ban-circle"></button>'.$rubriek[0].'</h2>';
                            echo '<ul>';
                            foreach($rubriek as $key => $subRubriek){
                                if (!$key==0){
                                    echo '<li>';
                                    if($beheerder) {
                                      if($subRubriek['volgnr'] != 1) {
                                          ?> <button class="glyphicon glyphicon-chevron-up"  onclick="document.write(' ')"></button> <?
                                      }
                                      if((count($rubriek) - 1) != $subRubriek['volgnr']){
                                          ?> <button class="glyphicon glyphicon-chevron-down" onclick="document.write('<?php swap_rubriek_volgnr($subRubriek['volgnr'], $subRubriek['volgnr']+1, $subRubriek['rubrieknummer'], next($subRubriek['rubrieknummer'])) ?>')"></button> <?php
                                      }
                                        ?> <button class="glyphicon glyphicon-edit">
                                            </button><button class="glyphicon glyphicon-ban-circle" onclick="document.write('<?php functie() ?>')"></button> 
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
