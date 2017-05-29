<?php
session_start();

include ('php/database.php');
include ('php/user.php');
pdo_connect();


function array_neighbor($arr, $key)
{
   $keys = array_keys($arr);
   $keyIndexes = array_flip($keys);
 
   $return = array();
   if (isset($keys[$keyIndexes[$key]-1])) {
       $return[] = $keys[$keyIndexes[$key]-1];
   }
   else {
       $return[] = $keys[sizeof($keys)-1];
   }
  
   if (isset($keys[$keyIndexes[$key]+1])) {
       $return[] = $keys[$keyIndexes[$key]+1];
   }
   else {
       $return[] = $keys[0];
   }
  
   return $return;
}


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

                                        $neigbour_keys=array_neighbor($rubriek, $key);
                                        //echo var_dump($neigbour_keys);
                                        $volgnr_before = ($subRubriek['volgnr'] - 1);
                                        $volgnr_current = $subRubriek['volgnr'];
                                        $volgnr_after = ($subRubriek['volgnr'] + 1);
                                        $rubriek_nummer_before=$neigbour_keys[0];
                                        $rubriek_nummer_current=$key;
                                        $rubriek_nummer_after=$neigbour_keys[1];
                                        
                                        //echo $rubriek_nummer_before.'<br>';
                                       // echo $rubriek_nummer_current.'<br>';
                                        //echo $rubriek_nummer_after.'<br>';

                                      if($subRubriek['volgnr'] != 1) {
                                          

                                          ?> 
                                        <form class="btn-group" action="php/functies/swap_rubriek_volgnr.php" method="POST">
                                                <input type="hidden" name="volgnr_A" value="<?php echo $volgnr_current ?>" >
                                                <input type="hidden" name="volgnr_B" value="<?php echo $volgnr_before ?>" >
                                                <input type="hidden" name="rubriek_nummer_A" value="<?php echo $rubriek_nummer_current ?>" >
                                                <input type="hidden" name="rubriek_nummer_B" value="<?php echo $rubriek_nummer_before ?>" >
                                                <button type="submit" class="btn btn-secondary glyphicon glyphicon-chevron-up"></button>
                                        </form>
                                        <?php
                                      }
                                      if((count($rubriek) - 1) != $subRubriek['volgnr']){
                                          
                                          
        
                                        ?> 
                                            <form class="btn-group" action='php/functies/swap_rubriek_volgnr.php' method="POST">
                                                <input type="hidden" name="volgnr_A" value="<?php echo $volgnr_current ?>" >
                                                <input type="hidden" name="volgnr_B" value="<?php echo $volgnr_after ?>" >
                                                <input type="hidden" name="rubriek_nummer_A" value="<?php echo $rubriek_nummer_current ?>" >
                                                <input type="hidden" name="rubriek_nummer_B" value="<?php echo $rubriek_nummer_after ?>" >
                                                <button type="submit" class="btn btn-secondary glyphicon glyphicon-chevron-down"></button>
                                            </form>
                                        <?php
                                      }
                                        ?> 
                                            <form class="btn-group" action="php/functies/change_rubrieknaam.php" method="POST">
                                                <input type="hidden" name="rubriek_nummer" value="<?php echo $rubriek_nummer ?>" >
                                                <input type="hidden" name="rubriek_naam" value="<?php echo $rubriek_naam ?>"  >
                                                <button type="submit" class="btn btn-secondary glyphicon glyphicon-edit"></button>
                                            </form>
                                            <form class="btn-group inline" action="php/functies/change_rubriek_status.php" method="POST">
                                                <input type="hidden" name="rubriek_nummer" value="<?php echo $rubriek_nummer ?>" >
                                                <input type="hidden" name="rubriek_status" value="<?php echo $rubriek_status ?>" >
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
