<?php
/*
  iProject Groep 2
  30-05-2017

  file: rubrieken_overzicht.php
  purpose:
  Show rubrieken
*/
session_start();

include_once ('php/database.php');
include_once ('php/user.php');
pdo_connect();
// database and user functions
// connect to database

// Function to get neighbor item in array
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

// Check if user is administrator
$beheerder = false;
if(isUserBeheerder($db)) {
    $beheerder = true;
} else {
    $beheerder = false;
}

// Check if user requested post
if($_SERVER['REQUEST_METHOD'] == 'POST'){

  // check if user is owner
  if($beheerder)
  {
      // check if key isset and new name isset and not empty
      if(isset($_POST['key']) && isset($_POST['new_name']) && !empty($_POST['new_name'])){

        $data = $db->prepare("UPDATE Rubriek
                                SET rubrieknaam = ?
                                WHERE rubrieknummer = ?;");
         // Query to update rubrieknaam

        try{
          $data->execute(array($_POST['new_name'],$_POST['key']));
          $_SESSION['warning']['changed'] = true; //Changed succesfull show notifcation
          //echo 'SUCCES';
        }
        catch(Exception $e){
          $_SESSION['warning']['error'] = $_POST['key']; // Something went wrong, show warning
          //echo 'FAILED';
        }
      }
      else {
        $_SESSION['warning']['error'] = $_POST['key']; // Something went wrong, show warning
      }
  }
}

global $db;
$data = $db->query("SELECT *
                    FROM Rubriek
                    WHERE parentRubriek = -1 or parentRubriek IN (
                                                    SELECT rubrieknummer
                                                    FROM Rubriek
                                                    WHERE parentRubriek = -1 )
                    ORDER BY parentRubriek ASC, volgnr ASC, rubrieknaam ASC");
// Select Rubrieken from Rubriek database
// with child rubrieks

$result = $data->fetchAll();
$count=count($result);
//print_r($result);
$rubrieken = [];
// Create array of rubrieken
for ($row = 0; $row < $count; $row++) {
    //echo "<p><b>Row number $row</b></p>";
    if ($result[$row]['parentRubriek']==-1){ //hoofdrubriek
        $nummer = $result[$row]['rubrieknummer'];
        $rubrieken[$nummer][] = $result[$row];
        //echo "<li>".$result[$row]['rubrieknaam']."</li>";
    } else { //subruriek
        $parentNummer = $result[$row]['parentRubriek'];
        $rubriekNummer = $result[$row]['rubrieknummer'];
        $rubriekStatus = $result[$row]['inactief'];

        $rubriekNaam = $result[$row]['rubrieknaam'];
        $volgnr = $result[$row]['volgnr'];

        $rubriek_gegevens['volgnr']= $volgnr;
        $rubriek_gegevens['rubrieknaam'] = $rubriekNaam;
        $rubriek_gegevens['status'] = $rubriekStatus;

        $rubrieken[$parentNummer][$rubriekNummer] = $rubriek_gegevens;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php
          include 'php/includes/default_header.php';
          // Include default head
        ?>
        <title>Rubrieken overzicht - Eenmaal Andermaal</title>
        <link href="css/rubriek_overzicht.css" rel="stylesheet">

  </head>

  <body>

    <?php
      include 'php/includes/header.php';
      //include navigation
    ?>
        <main class="container">


            <?php
            // If this session isset, rubriek name is succesfully changed
            if(isset($_SESSION['warning']['changed']) && $_SESSION['warning']['changed'] === true){
            ?>
              <p class="bg-success notifcation-fix" style="padding:5px;">De naam van de rubriek is succesvol gewijzigd.</p>
            <?php
            }
            ?>
                <nav aria-label="Page navigation example">
                    <ul id="sticky" class="pagination">
                    <?php
                        // Loop over all chars from alphabet
                        // Check if char has rubrieks, otherwise block this char in navigation row
                        foreach(range('A','Z') as $char) {
                            $nRubrieken = 0;
                            foreach($rubrieken as $rubriek){
                                $firstChar =substr ($rubriek[0]['rubrieknaam'] , 0 , 1 );
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
                // Loop over all chars from alphabet
                // Show all rubrieks 
                foreach(range('A','Z') as $char) {
                    $nRubrieken = 0;
                    foreach($rubrieken as $rubriek){
                        $firstChar =substr ($rubriek[0]['rubrieknaam'] , 0 , 1 );
                        if ($char==$firstChar){
                            $nRubrieken ++;
                            if($nRubrieken==1){
                                echo '<section  class="row rubriek_column">
                                  <a class="anchor" id="'.$char.'"></a>';
                                echo '<h1 class="rubriek_char">'.$char.'</h1>';
                            }
                            if($beheerder) {
                                echo '<article class="col-md-6">';
                            }
                            else {
                                echo '<article class="col-md-4">';
                            }
                            echo '<h2>';
                            if($beheerder) {
                                ?>
                                    <div class="btn-group" style="display:inline-block;">
                                      <button type="button" class="btn btn-danger btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Acties
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                      </button>
                                      <ul class="dropdown-menu">
                                        <li>
                                            <form action="php/functies/change_rubriek_status.php" method="POST">
                                              <input type="hidden" name="rubriek_nummer" value="<?php echo $rubriek[0]['rubrieknummer']; ?>" >
                                              <input type="hidden" name="rubriek_status" value="<?php echo $rubriek[0]['inactief']; ?>" >
                                                <button type="submit" style="color: #333;background-color: #fff;width:100%;">
                                                  <?php
                                                  if ((bool)$rubriek[0]['inactief']){ ?>
                                                       <span class="glyphicon glyphicon-eye-open"></span>
                                                       <span>Deblokkeer</span>
                                                 <?php }else{ ?>
                                                       <span class="glyphicon glyphicon-eye-close"></span>
                                                       <span>Blokkeer</span>
                                                <?php } ?>
                                              </button>
                                          </form>
                                        </li>
                                        <li><button data-toggle="modal" data-target="#name_<?php echo $rubriek[0]['rubrieknummer']; ?>" style="color: #333;background-color: #fff;width:100%;">Wijzig rubrieknaam</button></li>
                                        <!-- Modal -->
                                      </ul>
                                    </div>
                                    <div class="modal fade" id="name_<?php echo $rubriek[0]['rubrieknummer']; ?>" tabindex="-1" role="dialog" aria-labelledby="name_<?php echo $rubriek[0]['rubrieknummer']; ?>" style="z-index:9000;">
                                    <div class="modal-dialog modal-sm" role="document">
                                      <div class="modal-content">
                                        <form method="POST" action="">
                                          <input type="hidden" name="key" value="<?php echo $rubriek[0]['rubrieknummer']; ?>">
                                          <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">Rubrieknaam wijzigen</h4>
                                          </div>
                                          <div class="modal-body">
                                            <?php
                                            if(isset($_SESSION['warning']['error']) && $_SESSION['warning']['error'] == $key){
                                            ?>
                                              <p class="bg-danger notifcation-fix" style="padding:5px;">De opgegeven rubrieknaam is ongeldig</p>
                                            <?php
                                            }
                                            ?>
                                            <div class="input-group" style="width:100%;" >
                                              <input class="form-control" type="text" name="new_name" placeholder="Rubrieknaam" value="<?php echo $rubriek[0]['rubrieknaam'];?>" aria-describedby="sizing-addon2">
                                            </div>
                                          </div>
                                          <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
                                            <button type="submit" class="btn btn-orange">Wijzigen</button>
                                          </div>
                                        </form>
                                      </div>
                                    </div>
                                    </div>
                                <?php

                            }


                            $beheerderBlockedStyle = '';

                            if($beheerder){
                              if(((bool)$rubriek[0]['inactief']) == true){
                                $beheerderBlockedStyle = 'style="color:red;"';
                              }
                            }
                            echo '<span '.$beheerderBlockedStyle.'>'.$rubriek[0]['rubrieknaam'].'</span></h2>';
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

                                        $rubriek_status = $subRubriek['status'];
                                        ?>
                                        <div class="btn-group" style="display:inline-block;">
                                          <?php
                                            if($subRubriek['volgnr'] != 1) {
                                              ?>
                                              <form class="btn-group" action="php/functies/swap_rubriek_volgnr.php" method="POST">
                                                      <input type="hidden" name="volgnr_A" value="<?php echo $volgnr_current ?>" >
                                                      <input type="hidden" name="volgnr_B" value="<?php echo $volgnr_before ?>" >
                                                      <input type="hidden" name="rubriek_nummer_A" value="<?php echo $rubriek_nummer_current ?>" >
                                                      <input type="hidden" name="rubriek_nummer_B" value="<?php echo $rubriek_nummer_before ?>" >
                                                      <button type="submit" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-chevron-up"></span></button>
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
                                                  <button type="submit" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-chevron-down"></span></button>
                                                </form>
                                              <?php
                                            }
                                          ?>
                                          <button type="button" class="btn btn-danger btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Acties
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                          </button>
                                          <ul class="dropdown-menu">
                                            <li>
                                                <form action="php/functies/change_rubriek_status.php" method="POST">
                                                  <input type="hidden" name="rubriek_nummer" value="<?php echo $rubriek_nummer_current ?>" >
                                                  <input type="hidden" name="rubriek_status" value="<?php echo $rubriek_status ?>" >
                                                    <button type="submit" style="color: #333;background-color: #fff;width:100%;">
                                                      <?php if ((bool)$rubriek_status){ ?>
                                                           <span class="glyphicon glyphicon-eye-open"></span>
                                                           <span>Deblokkeer</span>
                                                     <?php }else{ ?>
                                                           <span class="glyphicon glyphicon-eye-close"></span>
                                                           <span>Blokkeer</span>
                                                    <?php } ?>
                                                  </button>
                                              </form>
                                            </li>
                                            <li><button data-toggle="modal" data-target="#name_<?php echo $key; ?>" style="color: #333;background-color: #fff;width:100%;">Wijzig rubrieknaam</button></li>
                                            <!-- Modal -->
                                          </ul>
                                        </div>
                                        <div class="modal fade" id="name_<?php echo $key; ?>" tabindex="-1" role="dialog" aria-labelledby="name_<?php echo $key; ?>" style="z-index:9000;">
                                        <div class="modal-dialog modal-sm" role="document">
                                          <div class="modal-content">
                                            <form method="POST" action="">
                                              <input type="hidden" name="key" value="<?php echo $key; ?>">
                                              <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Rubrieknaam wijzigen</h4>
                                              </div>
                                              <div class="modal-body">
                                                <?php
                                                if(isset($_SESSION['warning']['error']) && $_SESSION['warning']['error'] == $key){
                                                ?>
                                                  <p class="bg-danger notifcation-fix" style="padding:5px;">De opgegeven rubrieknaam is ongeldig</p>
                                                <?php
                                                }
                                                ?>
                                                <div class="input-group" style="width:100%;" >
                                                  <input class="form-control" type="text" name="new_name" placeholder="Rubrieknaam" value="<?php echo $subRubriek['rubrieknaam'];?>" aria-describedby="sizing-addon2">
                                                </div>
                                              </div>
                                              <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
                                                <button type="submit" class="btn btn-orange">Wijzigen</button>
                                              </div>
                                            </form>
                                          </div>
                                        </div>
                                        </div>
                                        <?php
                                    }
                                    $beheerderBlockedStyle = '';

                                    if($beheerder){
                                      if(((bool)$rubriek_status) == true){
                                        $beheerderBlockedStyle = 'style="color:red;"';
                                      }
                                    }

                                    echo '<a href="rubriek.php?rubriek='.$key.'"  '.$beheerderBlockedStyle.'>'.$subRubriek['rubrieknaam'].'</a>';

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
      <?php
      if(isset($_SESSION['warning']['error'])){
        ?>
        $('#name_<?php echo $_SESSION['warning']['error'];?>').modal('show');
        <?php
      }
      ?>
    });
    </script>
  </body>
</html>
<?php
$_SESSION['warning'] = null;
?>
